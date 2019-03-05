<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_Mostviewed
 */


namespace Amasty\Mostviewed\Model\Group;

use Amasty\Mostviewed\Controller\Adminhtml\Product\Group\Edit;
use Amasty\Mostviewed\Model\Group;
use Amasty\Mostviewed\Model\Layout\Updater;
use Magento\Framework\App\Request\DataPersistorInterface;
use Amasty\Mostviewed\Model\ResourceModel\Group\CollectionFactory;
use Magento\Ui\DataProvider\AbstractDataProvider;

class DataProvider extends AbstractDataProvider
{
    private $loadedData;

    /**
     * @var DataPersistorInterface
     */
    private $dataPersistor;

    /**
     * @var \Magento\Framework\UrlInterface
     */
    private $urlBuilder;

    /**
     * @var \Magento\Framework\Registry
     */
    private $coreRegistry;

    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    private $request;

    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        DataPersistorInterface $dataPersistor,
        \Magento\Framework\UrlInterface $urlBuilder,
        CollectionFactory $collectionFactory,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\App\RequestInterface $request,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
        $this->collection = $collectionFactory->create();
        $this->dataPersistor = $dataPersistor;
        $this->urlBuilder = $urlBuilder;
        $this->coreRegistry = $coreRegistry;
        $this->request = $request;
    }

    /**
     * @return array
     */
    public function getData()
    {
        if (isset($this->loadedData)) {
            return $this->loadedData;
        }

        $currentGroup = $this->coreRegistry->registry(Edit::CURRENT_GROUP);
        if ($currentGroup && $currentGroup->getGroupId()) {
            $this->loadedData[$currentGroup->getGroupId()] = $currentGroup->getData();
        } else {
            $items = $this->collection->getItems();
            /** @var Group $rule */
            foreach ($items as $rule) {
                $this->loadedData[$rule->getGroupId()] = $rule->getData();
            }
        }

        $data = $this->dataPersistor->get(Group::PERSISTENT_NAME);
        if (!empty($data)) {
            /** @var Group $rule */
            $rule = $this->collection->getNewEmptyItem();
            $rule->setData($data);
            $this->loadedData[$rule->getId()] = $rule->getData();
            $this->dataPersistor->clear(Group::PERSISTENT_NAME);
        }

        $this->generateEmbeddingContent();

        return $this->loadedData;
    }

    /**
     * @return array
     */
    public function getMeta()
    {
        if ($currentGroup = $this->coreRegistry->registry(Edit::CURRENT_GROUP)) {
            $meta = [
                'where_to_display_product' => [
                    'children' => [
                        'products_grid' => [
                            'arguments' => [
                                'data' => [
                                    'config' => [
                                        'group_id' => $currentGroup->getGroupId()
                                    ]
                                ]
                            ]
                        ]
                    ]
                ],
                'what_to_display_product'  => [
                    'children' => [
                        'products_grid' => [
                            'arguments' => [
                                'data' => [
                                    'config' => [
                                        'group_id' => $currentGroup->getGroupId()
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ];
        } else {
            $meta = parent::getMeta();
        }

        return $meta;
    }

    private function generateEmbeddingContent()
    {
        if ($this->loadedData) {
            foreach ($this->loadedData as $groupId => $loadedDatum) {
                if ($groupId) {
                    $this->loadedData[$groupId]['block_embedding'] = '<referenceContainer name="content">
                    <block class="Amasty\Mostviewed\Block\Widget\Related"
                           template="' . Updater::CONTENT_TEMPLATE . '" 
                           name="amasty.mostvieved.products.' . $groupId . '">
                        <arguments>
                            <argument name="group_id" xsi:type="string">' . $groupId . '</argument>
                        </arguments>
                    </block>
                    </referenceContainer>';
                }
            }
        }
    }
}
