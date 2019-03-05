<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_Mostviewed
 */


namespace Amasty\Mostviewed\Model;

use Amasty\Mostviewed\Model\OptionSource\ReplaceType;
use Magento\Catalog\Model\Category;
use Magento\Catalog\Model\Product;
use Amasty\Mostviewed\Model\ResourceModel\Product\CollectionFactory;
use Amasty\Mostviewed\Model\ResourceModel\Product\Collection;
use Amasty\Mostviewed\Model\OptionSource\SourceType;
use Magento\Framework\DB\Select;
use Magento\Catalog\Model\Product\Visibility;
use Amasty\Mostviewed\Model\OptionSource\Sortby;

class ProductProvider
{
    const MAX_COLLECTION_SIZE = 1000;

    const QUERY_LIMIT = 1000;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var ResourceModel\RuleIndex
     */
    private $indexResource;

    /**
     * @var CollectionFactory
     */
    private $productCollectionFactory;

    /**
     * @var Repository\GroupRepository
     */
    private $groupRepository;

    /**
     * @var Visibility
     */
    private $catalogProductVisibility;

    /**
     * @var \Magento\Catalog\Model\Config
     */
    private $catalogConfig;

    /**
     * @var \Magento\CatalogInventory\Helper\Stock
     */
    private $stockHelper;

    /**
     * @var \Amasty\Mostviewed\Helper\Config
     */
    private $config;

    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Amasty\Mostviewed\Model\ResourceModel\RuleIndex $indexResource,
        CollectionFactory $productCollectionFactory,
        \Amasty\Mostviewed\Model\Repository\GroupRepository $groupRepository,
        Visibility $catalogProductVisibility,
        \Magento\Catalog\Model\Config $catalogConfig,
        \Magento\CatalogInventory\Helper\Stock $stockHelper,
        \Amasty\Mostviewed\Helper\Config $config
    ) {
        $this->storeManager = $storeManager;
        $this->indexResource = $indexResource;
        $this->productCollectionFactory = $productCollectionFactory;
        $this->groupRepository = $groupRepository;
        $this->catalogProductVisibility = $catalogProductVisibility;
        $this->catalogConfig = $catalogConfig;
        $this->stockHelper = $stockHelper;
        $this->config = $config;
    }

    /**
     * @param Group $group
     * @param $product
     *
     * @return Collection|bool
     */
    public function getAppliedProducts(Group $group, $product)
    {
        /** @var Collection $products */
        $products = $this->productCollectionFactory->create()
            ->setStoreId($this->storeManager->getStore()->getId());
        $productIds = $this->indexResource->getAppliedProducts($group->getGroupId());
        if (empty($productIds)) {
            return false;
        }

        $products->addIdFilter($productIds);

        if ($product instanceof Product) {
            switch ($group->getSourceType()) {
                case SourceType::SOURCE_BOUGHT:
                    $products = $this->applyBoughtTogether($products, $product, $group);
                    break;
                case SourceType::SOURCE_VIEWED:
                    $products = $this->applyViewedTogether($products, $product, $group);
                    break;
            }

            if ($products && $group->getSameAs()) {
                $group->applySameAsConditions($products, $product);
            }
        }

        return $products;
    }

    /**
     * @param string $type
     * @param Product $product
     * @param $collection
     * @param array $excludedProducts
     *
     * @return Collection
     */
    public function modifyCollection(
        $type,
        Product $product,
        $collection,
        $excludedProducts,
        $block
    ) {
        $group = $this->groupRepository->getGroupByIdAndPosition($product->getId(), $type);
        if ($group) {
            $limit = $group->getMaxProducts() ? : self::MAX_COLLECTION_SIZE;

            $shouldAdd = $group->getReplaceType() == ReplaceType::ADD;
            if ($shouldAdd) {
                $appendIds = is_object($collection) ? $collection->getAllIds() : array_keys($collection);
                $excludedProducts = array_merge($excludedProducts, $appendIds);
                $limit -= count($appendIds);
            }

            if ($limit > 0) {
                $appliedCollection = $this->getAppliedProducts($group, $product);
                if ($appliedCollection) {
                    $appliedCollection->setPageSize($limit);

                    if (!empty($excludedProducts)) {
                        $appliedCollection->addIdFilter($excludedProducts, true);
                    }

                    $this->prepareCollection($group, $appliedCollection, $product);
                    $block->setMostviewedProducts(array_keys($appliedCollection->getItems()));
                    if ($shouldAdd) {
                        foreach ($collection as $item) {
                            try {
                                $appliedCollection->addItem($item);
                            } catch (\Exception $ex) {
                                continue;
                            }
                        }
                        $appliedCollection->updateTotalRecords();
                    }

                    if (is_array($collection)) {
                        $collection = $appliedCollection->getItems();
                    } else {
                        $collection = $appliedCollection;
                    }
                    $block->setGroupId($group->getGroupId());
                }
            }
        }

        return $collection;
    }

    /**
     * @param Group $group
     * @param Collection $collection
     * @param null|Product $product
     */
    public function prepareCollection(Group $group, Collection $collection, $product = null)
    {
        $collection->addAttributeToSelect(
            'required_options'
        )->addStoreFilter();

        $collection
            ->addAttributeToSelect($this->catalogConfig->getProductAttributes())
            ->addMinimalPrice()
            ->addFinalPrice()
            ->addTaxPercents()
            ->addUrlRewrite();

        $collection->setVisibility($this->catalogProductVisibility->getVisibleInCatalogIds());

        if (!$group->getShowOutOfStock()) {
            $this->stockHelper->addInStockFilterToCollection($collection);
        }
        $collection->setFlag('has_stock_status_filter', true);

        $this->applySorting($group->getSorting(), $collection);

        if ($product) {
            $collection->addIdFilter($product->getId(), true);
        }

        foreach ($collection as $product) {
            $product->setDoNotUseCategoryId(true);
        }
    }

    /**
     * @param $sorting
     * @param Collection $collection
     */
    private function applySorting($sorting, Collection $collection)
    {
        $dir = Select::SQL_ASC;
        switch ($sorting) {
            case Sortby::NAME:
                $sortAttr = 'name';
                break;
            case Sortby::PRICE_ASC:
                $sortAttr = 'price';
                break;
            case Sortby::PRICE_DESC:
                $sortAttr = 'price';
                $dir = Select::SQL_DESC;
                break;
            case Sortby::NEWEST:
                $sortAttr = 'created_at';
                $dir = Select::SQL_DESC;
                break;
            default:
                $sortAttr = null;
        }
        if ($sortAttr === null) {
            $collection->getSelect()->order('RAND()');
        } else {
            $collection->setOrder($sortAttr, $dir);
        }
    }

    /**
     * @param Collection $collection
     * @param Product $product
     * @param Group $group
     *
     * @return Collection
     */
    private function applyViewedTogether(Collection $collection, Product $product, Group $group)
    {
        $tbl = $collection->getTable('report_viewed_product_index');
        $db = $collection->getConnection();
        $storeId = $this->storeManager->getStore()->getId();
        $productId = $product->getId();

        $period = $this->config->getGatheredPeriod();
        $queryLimit = self::QUERY_LIMIT;

        //TODO code refactoring - move select to resource model

        //get visitors who viewed this product
        $visitors = $db->select()->from(['t2' => $tbl], ['visitor_id'])
            ->where('product_id = ?', $productId)
            ->where('visitor_id IS NOT NULL')
            ->where('store_id = ?', $storeId)
            ->where('TO_DAYS(NOW()) - TO_DAYS(added_at) <= ?', $period)
            ->limit($queryLimit);

        //get customers who viewed this product
        $customers = $db->select()->from(['t2' => $tbl], ['customer_id'])
            ->where('product_id = ?', $productId)
            ->where('customer_id IS NOT NULL')
            ->where('store_id = ?', $storeId)
            ->where('TO_DAYS(NOW()) - TO_DAYS(added_at) <= ?', $period)
            ->limit($queryLimit);

        $visitors = array_unique($db->fetchCol($visitors));
        $customers = array_unique($db->fetchCol($customers));
        $customers = array_diff($customers, $visitors);

        // get related products
        $fields = [
            'id'  => 't.product_id',
            'cnt' => new \Zend_Db_Expr('COUNT(*)'),
        ];
        $productsByVisitor = [];
        if (!empty($visitors)) {
            $productsByVisitor = $db->select()->from(['t' => $tbl], $fields)
                ->where('t.visitor_id IN (?)', $visitors)
                ->where('t.product_id != ?', $productId)
                ->where('store_id = ?', $storeId)
                ->group('t.product_id')
                ->order('cnt DESC')
                ->limit($queryLimit);
            $productsByVisitor = $db->fetchAll($productsByVisitor);
        }

        $productsByCustomer = [];
        if (!empty($customers)) {
            $productsByCustomer = $db->select()->from(['t' => $tbl], $fields)
                ->where('t.customer_id IN (?)', $customers)
                ->where('t.product_id != ?', $productId)
                ->where('store_id = ?', $storeId)
                ->group('t.product_id')
                ->order('cnt DESC')
                ->limit($queryLimit);
            $productsByCustomer = $db->fetchAll($productsByCustomer);
        }
        $data = array_merge($productsByVisitor, $productsByCustomer);

        $views = [];
        $products = [];
        foreach ($data as $key => $row) {
            $views[$key] = $row['cnt'];
            $products[$key] = $row['id'];
        }

        array_multisort($views, SORT_DESC, $products);
        if (!empty($products)) {
            $collection->addIdFilter(array_unique($products));
            $collection->getSelect()->order(
                new \Zend_Db_Expr('FIELD(e.entity_id, ' . implode(',', $products) . ')')
            );
        } else {
            $collection = false;
        }

        return $collection;
    }

    /**
     * @param Collection $collection
     * @param Product $product
     * @param Group $group
     *
     * @return Collection
     */
    private function applyBoughtTogether(Collection $collection, Product $product, Group $group)
    {
        $tbl = $collection->getTable('sales_order_item');
        $db = $collection->getConnection();
        $storeId = $this->storeManager->getStore()->getId();

        $period = $this->config->getGatheredPeriod();
        $queryLimit = self::QUERY_LIMIT;
        $productIds = [];

        $typeInstance = $product->getTypeInstance();
        switch ($product->getTypeId()) {
            case 'grouped':
                $productIds = $typeInstance->getAssociatedProductIds($product);
                break;
            case 'configurable':
                $productIds = $typeInstance->getUsedProductIds($product);
                break;
            case 'bundle':
                $optionsIds = $typeInstance->getOptionsIds($product);
                $selections = $typeInstance->getSelectionsCollection($optionsIds, $product);
                foreach ($selections as $selection) {
                    $productIds[] = $selection->getProductId();
                }
                break;
            default:
                $productIds[] = $product->getId();
        }

        //TODO code refactoring - move select to resource model

        //get customer who bought this product
        $productsByCustomers = [];
        $customers = $db->select()->from(['order_item' => $tbl], [])
            ->join(
                ['order' => $collection->getTable('sales_order')],
                'order_item.order_id = order.entity_id',
                ['customer_id' => 'order.customer_id']
            )
            ->where('order_item.product_id IN(?)', $productIds)
            ->where('order.customer_id IS NOT NULL')
            ->where('order_item.store_id = ?', $storeId)
            ->where('TO_DAYS(NOW()) - TO_DAYS(order.created_at) <= ?', $period)
            ->limit($queryLimit);
        $customers = array_unique($db->fetchCol($customers));

        $productIdField = new \Zend_Db_Expr(
            'IF(configurable.parent_id IS NOT NULL, configurable.parent_id,'
            . ' IF(bundle.parent_product_id IS NOT NULL, bundle.parent_product_id, order_item.product_id))'
        );

        if (!empty($customers)) {
            $productsByCustomers = $db->select()->from(
                ['order_item' => $tbl],
                ['id' => $productIdField, 'cnt' => new \Zend_Db_Expr('COUNT(*)')]
            )
                ->join(
                    ['order' => $collection->getTable('sales_order')],
                    'order_item.order_id = order.entity_id',
                    []
                )
                ->joinLeft(
                    ['configurable' => $collection->getTable('catalog_product_super_link')],
                    'order_item.product_id = configurable.product_id',
                    []
                )
                ->joinLeft(
                    ['bundle' => $collection->getTable('catalog_product_bundle_selection')],
                    'order_item.product_id = bundle.product_id',
                    []
                )
                ->where('order_item.product_id NOT IN(?)', $productIds)
                ->where('order.customer_id IN(?)', $customers)
                ->where('order_item.store_id = ?', $storeId)
                ->group('order_item.product_id')
                ->order('cnt DESC')
                ->limit($queryLimit);

            $this->addOrderStatusFilter($productsByCustomers);
            $productsByCustomers = $db->fetchAll($productsByCustomers);
        }

        /* guest part start */
        $productsByGuests = [];
        $guests = $db->select()->from(['order_item' => $tbl], [])
            ->join(
                ['order' => $collection->getTable('sales_order')],
                'order_item.order_id = order.entity_id',
                ['customer_id' => 'order.customer_email']
            )
            ->where('order_item.product_id IN(?)', $productIds)
            ->where('order.customer_is_guest = 1')
            ->where('order_item.store_id = ?', $storeId)
            ->where('TO_DAYS(NOW()) - TO_DAYS(order.created_at) <= ?', $period)
            ->limit($queryLimit);
        $guests = array_unique($db->fetchCol($guests));

        if (!empty($guests)) {
            $productsByGuests = $db->select()->from(
                ['order_item' => $tbl],
                ['id' => $productIdField, 'cnt' => new \Zend_Db_Expr('COUNT(*)')]
            )
                ->join(
                    ['order' => $collection->getTable('sales_order')],
                    'order_item.order_id = order.entity_id',
                    []
                )
                ->joinLeft(
                    ['configurable' => $collection->getTable('catalog_product_super_link')],
                    'order_item.product_id = configurable.product_id',
                    []
                )
                ->joinLeft(
                    ['bundle' => $collection->getTable('catalog_product_bundle_selection')],
                    'order_item.product_id = bundle.product_id',
                    []
                )
                ->where('order_item.product_id NOT IN(?)', $productIds)
                ->where('order.customer_email IN(?)', $guests)
                ->where('order_item.store_id = ?', $storeId)
                ->group('order_item.product_id')
                ->order('cnt DESC')
                ->limit($queryLimit);

            $this->addOrderStatusFilter($productsByGuests);
            $productsByGuests = $db->fetchAll($productsByGuests);
        }
        /* guest part end */

        $data = array_merge($productsByGuests, $productsByCustomers);
        if (empty($data)) {
            $collection = false;
        } else {
            $views = [];
            $products = [];
            foreach ($data as $key => $row) {
                $views[$key] = $row['cnt'];
                $products[$key] = $row['id'];
            }

            array_multisort($views, SORT_DESC, $products);
            if (!empty($products)) {
                $collection->addIdFilter(array_unique($products));
                $collection->getSelect()->order(
                    new \Zend_Db_Expr('FIELD(e.entity_id, ' . implode(',', $products) . ')')
                );
            }
        }

        return $collection;
    }

    /**
     * @param \Magento\Framework\DB\Select $collection
     *
     * @return \Magento\Framework\DB\Select
     */
    private function addOrderStatusFilter($collection)
    {
        $orderStatus = $this->config->getOrderStatus();
        if ($orderStatus && $orderStatus !== '0') {
            $collection->where('order.status = ?', $orderStatus);
        }

        return $collection;
    }
}
