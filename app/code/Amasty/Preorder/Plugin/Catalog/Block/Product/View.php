<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_Preorder
 */


namespace Amasty\Preorder\Plugin\Catalog\Block\Product;

use Amasty\Preorder\Helper\Data;
use Magento\Catalog\Block\Product\View as ProductView;

class View
{
    /**
     * @var string
     */
    private $regexp;

    /**
     * @var Data
     */
    private $helper;

    /**
     * @var array
     */
    private $applicableBlocks = [
        'product.info.addtocart.bundle',
        'product.info.addtocart',
        'product.info.addtocart.additional'
    ];

    public function __construct(Data $helper)
    {
        $this->helper = $helper;
        $this->_construct();
    }

    protected function _construct()
    {
        $this->regexp = '@(<button[^>]*title=")[^"]*("[^>]*id="product-addtocart-button">[^>]*<span>)(.*)(</span>.*</button>)@s';
    }

    /**
     * @param ProductView $subject
     * @param string $html
     * @return string
     */
    public function afterToHtml(ProductView $subject, $html)
    {
        if (in_array($subject->getNameInLayout(), $this->applicableBlocks)
            && $this->helper->preordersEnabled()
            && $this->helper->getIsProductPreorder($subject->getProduct())
        ) {
            $note = $this->helper->getProductPreorderCartLabel($subject->getProduct());
            $html = preg_replace(
                $this->regexp,
                '$1 ' . $note . '$2 ' . $note . '$4'
                    . '<div class="original-add-to-cart-text" data-text="$3"></div>',
                $html
            );
        }

        return $html;
    }
}
