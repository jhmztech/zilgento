<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_Finder
 */


namespace Amasty\Finder\Observer;

class LayoutRender implements \Magento\Framework\Event\ObserverInterface
{
    /**
     * @var \Amasty\Finder\Api\FinderRepositoryInterface
     */
    private $finderRepository;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var \Magento\Framework\App\Request\Http
     */
    private $request;

    /**
     * @var \Magento\Catalog\Model\Layer
     */
    private $layer;

    public function __construct(
        \Amasty\Finder\Api\FinderRepositoryInterface $finderRepository,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Catalog\Model\Layer\Resolver $resolver,
        \Magento\Framework\App\Request\Http $request
    ) {
        $this->finderRepository = $finderRepository;
        $this->storeManager = $storeManager;
        $this->layer = $resolver->get();
        $this->request = $request;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     * @return \Magento\Framework\Event\Observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $isHomeOrProduct = strpos($this->request->getFullActionName(), 'cms_') !== false
            || $this->request->getFullActionName() == 'catalog_product_view'
            || strpos($this->request->getFullActionName(), 'checkout_') !== false
            || strpos($this->request->getFullActionName(), 'sales_') !== false
            || strpos($this->request->getFullActionName(), 'shipping_') !== false
            || strpos($this->request->getFullActionName(), 'amblog_') !== false
            || strpos($this->request->getFullActionName(), 'customer_') !== false;

        if ($isHomeOrProduct) {
            return $observer;
        }

        foreach ($this->getFindersOnPage() as $finder) {
            $observer->getLayout()->getUpdate()->addUpdate($finder->getFinderXmlCode());
        }

        return $observer;
    }

    /**
     * @return \Amasty\Finder\Model\ResourceModel\Finder\Collection
     */
    private function getFindersOnPage()
    {
        $currentCategoryId = $this->layer->getCurrentCategory()->getId();

        if (strpos($this->request->getFullActionName(), 'catalogsearch_') !== false) {
            $findersOnPage = $this->finderRepository->getFindersOnSearchPage();
        } elseif ($this->storeManager->getStore()->getRootCategoryId() == $currentCategoryId) {
            $findersOnPage = $this->finderRepository->getFindersOnDefaultCategory();
        } else {
            $findersOnPage = $this->finderRepository->getFindersCategory($currentCategoryId);
        }

        return $findersOnPage;
    }
}
