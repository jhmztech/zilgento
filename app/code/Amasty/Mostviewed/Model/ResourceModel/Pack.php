<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_Mostviewed
 */


namespace Amasty\Mostviewed\Model\ResourceModel;

use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Pack extends AbstractDb
{
    const PACK_PRODUCT_TABLE = 'amasty_mostviewed_pack_product';

    private $savedData = [];

    /**
     * Model Initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('amasty_mostviewed_pack', 'pack_id');
    }

    protected function _afterLoad(\Magento\Framework\Model\AbstractModel $object)
    {
        if ($object->getId()) {
            $products = $this->getParentIdsByPack($object->getId());
            if ($products) {
                $object->setData('parent_ids', $products);
            }
        }

        return parent::_afterLoad($object);
    }

    public function getParentIdsByPack($packId)
    {
        $select = $this->getConnection()->select()->from(
            $this->getTable(self::PACK_PRODUCT_TABLE),
            ['product_id']
        )->where('pack_id=?', $packId);

        return $this->getConnection()->fetchCol($select, []);
    }

    /**
     * @param int $productId
     * @param int $storeId
     *
     * @return array
     */
    public function getIdsByProductAndStore($productId, $storeId)
    {
        $select = $this->getConnection()->select()->from(
            $this->getTable(self::PACK_PRODUCT_TABLE),
            ['pack_id']
        )
            ->where('product_id=?', $productId)
            ->where('store_id IN(?)', [0, $storeId]);

        return $this->getConnection()->fetchCol($select, []);
    }

    /**
     * @param int $productId
     * @param int $storeId
     *
     * @return array
     */
    public function getIdsByChildProductAndStore($productId, $storeId)
    {
        $select = $this->getConnection()->select()->from(
            $this->getMainTable(),
            ['pack_id']
        )
            ->where('CONCAT(\',\', product_ids, \',\') LIKE ?', '%,' . $productId . ',%')
            ->where('store_id IN (?)', [0, $storeId]);

        return $this->getConnection()->fetchCol($select, []);
    }

    /**
     * @param AbstractModel $object
     *
     * @return $this
     */
    protected function _beforeSave(AbstractModel $object)
    {
        $this->savedData = $object->getData();

        return parent::_beforeSave($object);
    }

    /**
     * @param AbstractModel $object
     *
     * @return $this
     */
    protected function _afterSave(AbstractModel $object)
    {
        $this->deletePackAdditional($object);
        if (isset($this->savedData['parent_product_ids'])
            && isset($this->savedData['store_id'])
            && $this->savedData['parent_product_ids']
        ) {
            $this->savePackProductData(
                [
                    'parent_product_ids' => $this->savedData['parent_product_ids'],
                    'store_id'           => $this->savedData['store_id'],
                    'pack_id'            => $object->getId()
                ]
            );
        }

        return parent::_afterSave($object);
    }

    /**
     * @param AbstractModel $object
     *
     * @return $this
     */
    protected function _afterDelete(AbstractModel $object)
    {
        $this->deletePackAdditional($object);

        return parent::_afterDelete($object);
    }

    /**
     * @param array $data
     */
    private function savePackProductData($data)
    {
        $insertData = [];
        foreach ($data['parent_product_ids'] as $parentProductId) {
            $insertData[] = [
                'store_id'   => $data['store_id'],
                'pack_id'    => $data['pack_id'],
                'product_id' => $parentProductId,
            ];
        }
        if ($insertData) {
            $this->getConnection()->insertOnDuplicate(
                $this->getTable(self::PACK_PRODUCT_TABLE),
                $insertData
            );
        }
    }

    /**
     * @param AbstractModel $object
     */
    private function deletePackAdditional(AbstractModel $object)
    {
        if ($object->getPackId()) {
            $this->getConnection()->delete(
                $this->getTable(self::PACK_PRODUCT_TABLE),
                ['pack_id=?' => $object->getPackId()]
            );
        }
    }
}
