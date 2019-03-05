<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_Mostviewed
 */


namespace Amasty\Mostviewed\Block\Widget;

use Amasty\Mostviewed\Api\GroupRepositoryInterface;
use Amasty\Mostviewed\Model\Group;
use Amasty\Mostviewed\Model\ProductProvider;
use Magento\Catalog\Block\Product\AbstractProduct;
use Magento\Catalog\Block\Product\Context;
use Magento\Catalog\Model\Product\Visibility;
use Magento\Catalog\Model\ResourceModel\Product\Collection as ProductCollection;
use Amasty\Mostviewed\Helper\Quote;
use Amasty\Mostviewed\Model\OptionSource\BlockPosition;
use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Exception\NoSuchEntityException;

class Related extends AbstractProduct implements IdentityInterface
{
    const CACHE_TAG = 'amasty_mostviewed';

    /**
     * @var null|ProductCollection
     */
    private $productCollection = null;

    /**
     * @var GroupRepositoryInterface
     */
    private $groupRepository;

    /**
     * @var ProductProvider
     */
    private $productProvider;

    /**
     * @var Visibility
     */
    private $catalogProductVisibility;

    /**
     * @var Quote
     */
    private $quoteHelper;

    public function __construct(
        GroupRepositoryInterface $groupRepository,
        ProductProvider $productProvider,
        Visibility $catalogProductVisibility,
        Quote $quoteHelper,
        Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->groupRepository = $groupRepository;
        $this->productProvider = $productProvider;
        $this->catalogProductVisibility = $catalogProductVisibility;
        $this->quoteHelper = $quoteHelper;
    }

    /**
     * @return ProductCollection
     */
    public function getProductCollection()
    {
        if ($this->productCollection === null) {
            $this->initializeProductCollection();
        }

        return $this->productCollection;
    }

    private function initializeProductCollection()
    {
        $group = false;
        $entity = null;
        if ($this->getGroupId()) { //custom block
            try {
                $group = $this->groupRepository->getById($this->getGroupId());
            } catch (NoSuchEntityException $ex) {
                $group = false;
            }
        }

        if (!$group && $entity = $this->getEntity()) {
            $group = $this->groupRepository->getGroupByIdAndPosition($entity->getId(), $this->getPosition());
        }

        if ($group && $group->getMaxProducts()) {
            $this->setGroupId($group->getId());
            $this->setTitle($group->getBlockTitle());
            $this->setAddToCart($group->getAddToCart());
            $this->setBlockLayout($group->getBlockLayout());

            $this->productCollection = $this->productProvider->getAppliedProducts($group, $entity);
            if ($this->productCollection) {
                $this->productCollection->setPageSize($group->getMaxProducts());

                $product = $this->_coreRegistry->registry('current_product');
                $this->productProvider->prepareCollection($group, $this->productCollection, $product);
            }
        }
    }

    /**
     * @return \Magento\Catalog\Model\Product|\Magento\Catalog\Model\Category
     */
    private function getEntity()
    {
        switch ($this->_request->getFullActionName()) {
            case 'catalog_product_view':
                $entity = $this->_coreRegistry->registry('current_product');
                break;
            case 'catalog_category_view':
                $entity = $this->_coreRegistry->registry('current_category');
                break;
            case 'checkout_cart_index':
                $entity = $this->quoteHelper->getLastAddedProductInCart();
                break;
            default:
                $entity = null;
        }

        return $entity;
    }

    /**
     * @return string
     */
    public function getCssClass()
    {
        $class = 'widget';
        if (in_array(
            $this->getPosition(),
            [BlockPosition::CART_BEFORE_CROSSSEL, BlockPosition::CART_AFTER_CROSSSEL]
        )
        ) {
            $class = 'crosssell';
        }

        return $class;
    }

    /**
     * Return unique ID(s) for each object in system
     *
     * @return string[]
     */
    public function getIdentities()
    {
        return [
            self::CACHE_TAG . '_' . $this->getPosition(),
            Group::CACHE_TAG . '_' . $this->getGroupId()
        ];
    }
}
