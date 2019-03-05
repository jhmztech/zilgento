<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_Mostviewed
 */


namespace Amasty\Mostviewed\Plugin\Mui;

use Amasty\Mostviewed\Controller\Adminhtml\Product\Mui\Render as RenderController;
use Amasty\Mostviewed\Model\ResourceModel\RuleIndex;
use Magento\Framework\Exception\NoSuchEntityException;

class Render
{
    /**
     * @var \Amasty\Mostviewed\Model\Repository\GroupRepository
     */
    private $groupRepository;

    /**
     * @var \Magento\Framework\Registry
     */
    private $registry;

    public function __construct(
        \Amasty\Mostviewed\Model\Repository\GroupRepository $groupRepository,
        \Magento\Framework\Registry $registry
    ) {
        $this->groupRepository = $groupRepository;
        $this->registry = $registry;
    }

    /**
     * @param RenderController $renderController
     */
    public function beforeExecute(RenderController $renderController)
    {
        $request = $renderController->getRequest();
        if ($conditions = $request->getParam('rule', null)) {
            $relation = $request->getParam('relation') . '_show';
            try {
                /** @var \Amasty\Mostviewed\Model\Group $group */
                $group = $this->groupRepository->getById($request->getParam('group_id'));
            } catch (NoSuchEntityException $entityException) {
                $group = $this->groupRepository->getNew();
                $group->setStores('0');
            }
            $group->setRelation($relation);
            $group->setShowForOutOfStock($request->getParam('for_out_of_stock', 0));
            switch ($relation) {
                case RuleIndex::WHAT_SHOW:
                    $group->loadPost($conditions);
                    break;
                case RuleIndex::WHERE_SHOW:
                    $group->loadPost($conditions);
                    break;
            }
            $this->registry->register(
                \Amasty\Mostviewed\Ui\DataProvider\Product\ProductDataProvider::PRODUCTS_KEY,
                $group->getMatchingProductIdsByGroup()
            );
        }
    }
}
