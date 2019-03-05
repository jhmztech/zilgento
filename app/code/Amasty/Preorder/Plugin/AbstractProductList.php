<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_Preorder
 */


namespace Amasty\Preorder\Plugin;

use Amasty\Preorder\Block\Product\ListProduct\Preorder;
use \Magento\CatalogWidget\Block\Product\ProductsList as WidgetProductList;
use \Magento\Catalog\Block\Product\ListProduct as CatalogProductList;

abstract class AbstractProductList
{
    /**
     * @var \Amasty\Preorder\Helper\Data
     */
    private $helper;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    private $scopeConfig;

    public function __construct(
        \Amasty\Preorder\Helper\Data $helper,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    ) {
        $this->helper = $helper;
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * @param CatalogProductList | WidgetProductList $subject
     * @param string $resultHtml
     *
     * @return string
     */
    public function afterToHtml($subject, $resultHtml)
    {
        if ($this->helper->preordersEnabled() && !$this->isSkipAdvanedsearchPopup($subject)) {
            $productCollection = $subject->getLoadedProductCollection();
            if ($productCollection == null) {
                $productCollection = $subject->getProductCollection();
            }
            $preOrderHtml = $subject->getLayout()
                ->createBlock(Preorder::class)
                ->setProductCollection($productCollection)
                ->toHtml();

            $resultHtml .= $preOrderHtml;
        }

        return $resultHtml;
    }

    /**
     * @param CatalogProductList | WidgetProductList $subject
     * @return bool
     */
    private function isSkipAdvanedsearchPopup($subject)
    {
        $result = $subject instanceof \Amasty\Xsearch\Block\Search\Product;

        /*  preorder is not working with advanced search popup anyway.
        This assertion prevents elastic response slowing down for popup. Uncomment after implementing compatibility */
//        $result = $result && !$this->scopeConfig->getValue('amasty_xsearch/product/add_to_cart');
        return $result;
    }
}
