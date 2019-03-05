<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_Mostviewed
 */


namespace Amasty\Mostviewed\Model\Rule\Condition;

class ProductFactory extends \Magento\CatalogRule\Model\Rule\Condition\ProductFactory
{
    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        $instanceName = \Amasty\Mostviewed\Model\Rule\Condition\Product::class
    ) {
        $this->_objectManager = $objectManager;
        $this->_instanceName = $instanceName;
    }
}
