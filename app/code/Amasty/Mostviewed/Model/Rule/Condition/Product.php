<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_Mostviewed
 */


namespace Amasty\Mostviewed\Model\Rule\Condition;

use Magento\Catalog\Model\Product as ProductModel;
use Magento\Catalog\Model\ResourceModel\Product\Collection;

class Product extends \Magento\CatalogRule\Model\Rule\Condition\Product
{
    /**
     * @param Collection $collection
     * @param ProductModel $product
     * @param int $equals
     */
    public function apply(Collection $collection, ProductModel $product, $equals)
    {
        $applied = false;
        $attributeCode = $this->getAttribute();
        if ($attributeCode == 'category_ids') {
            $attributeValue = $product->getCategoryIds();
            if ($attributeValue) {
                $applied = true;
                $collection->addCategoriesFilter(
                    [
                        $equals ? 'in' : 'nin' => $attributeValue
                    ]
                );
            }
        } else {
            $attributeValue = $product->getData($attributeCode);
            if ($attributeValue === null) {
                $attributeValue = $product->getResource()->getAttributeRawValue(
                    $product->getId(),
                    $attributeCode,
                    $product->getStoreId()
                );
            }

            if ($attributeValue) {
                if ($product->getResource()->getAttribute($attributeCode)->getFrontendInput() == 'multiselect') {
                    $filter = [];
                    foreach (explode(',', $attributeValue) as $val) {
                        $filter[] = [
                            'attribute' => $attributeCode,
                            'finset'    => $val
                        ];
                    }
                    if (!empty($filter)) {
                        $collection->addAttributeToFilter($filter);
                        if (!$equals) {
                            $where = $collection->getSelect()->getPart(\Zend_Db_Select::WHERE);
                            $fInSetCondition = array_pop($where);
                            $fInSetCondition = str_replace('FIND_IN_SET', 'NOT FIND_IN_SET', $fInSetCondition);
                            $fInSetCondition = str_replace(
                                \Zend_Db_Select::SQL_OR,
                                \Zend_Db_Select::SQL_AND,
                                $fInSetCondition
                            );
                            $where[] = $fInSetCondition;
                            $collection->getSelect()->setPart(\Zend_Db_Select::WHERE, $where);
                        }
                        $applied = true;
                    }
                } else {
                    $collection->addAttributeToFilter(
                        $attributeCode,
                        [$equals ? 'eq' : 'neq' => $attributeValue]
                    );
                    $applied = true;
                }
            }
        }

        return $applied;
    }

    /**
     * Default operator input by type map getter
     *
     * @return array
     */
    public function getDefaultOperatorInputByType()
    {
        if (null === $this->_defaultOperatorInputByType) {
            $this->_defaultOperatorInputByType = [
                'string'      => ['=='],
                'numeric'     => ['=='],
                'date'        => ['=='],
                'select'      => ['=='],
                'boolean'     => ['==',],
                'multiselect' => ['=='],
                'grid'        => ['=='],
            ];
            $this->_defaultOperatorInputByType['category'] = ['=='];
            $this->_arrayInputTypes[] = 'category';
            $this->_defaultOperatorInputByType['sku'] = ['=='];
        }

        return $this->_defaultOperatorInputByType;
    }

    /**
     * @return string
     */
    public function getValueElementHtml()
    {
        return __('same as Current Product ') . $this->getAttributeObject()->getDefaultFrontendLabel();
    }

    /**
     * @return array
     */
    public function getAttributeSelectOptions()
    {
        $opt = [
            [
                'value' => '1',
                'label' => __('same as Current Product ') . $this->getAttributeObject()->getDefaultFrontendLabel()
            ]
        ];

        return $opt;
    }

    /**
     * @return array
     */
    public function getValueSelectOptions()
    {
        return $this->getAttributeSelectOptions();
    }

    /**
     * @return string
     */
    public function getValueElementType()
    {
        return 'select';
    }

    /**
     * @return string
     */
    public function getFormName()
    {
        return \Amasty\Mostviewed\Model\Group::FORM_NAME;
    }
}
