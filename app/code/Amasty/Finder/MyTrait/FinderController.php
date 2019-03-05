<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_Finder
 */

/**
 * Copyright © 2015 Amasty. All rights reserved.
 */

namespace Amasty\Finder\MyTrait;

trait FinderController
{
    /** @return \Amasty\Finder\Model\Finder */
    protected function _initFinder()
    {
        $finderId = $this->getRequest()->getParam('id');

        if ($finderId) {
            $finder = $this->finderRepository->getById($finderId);
            if (!$finder->getId()) {
                $this->messageManager->addError(__('This item no longer exists.'));
                $this->_redirect('amasty_finder/*');
                return;
            }
        }

        $this->coreRegistry->register('current_amasty_finder_finder', $finder);

        return $finder;
    }
}
