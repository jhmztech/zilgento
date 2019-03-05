<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_Mostviewed
 */


namespace Amasty\Mostviewed\Model\ResourceModel\Product;

class Collection extends \Magento\Catalog\Model\ResourceModel\Product\Collection
{
    /**
     * @return $this
     */
    public function updateTotalRecords()
    {
        $this->_totalRecords = count($this->getItems());

        return $this;
    }
}
