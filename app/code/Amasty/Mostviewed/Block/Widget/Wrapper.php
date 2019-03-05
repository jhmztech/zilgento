<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_Mostviewed
 */


namespace Amasty\Mostviewed\Block\Widget;

class Wrapper extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Magento\Framework\Module\Manager
     */
    private $moduleManager;

    public function __construct(
        \Magento\Framework\Module\Manager $moduleManager,
        \Magento\Framework\View\Element\Template\Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->moduleManager = $moduleManager;
    }

    /**
     * @inheritdoc
     */
    public function toHtml()
    {
        if ($this->moduleManager->isEnabled('Amasty_Mostviewed')) {
            $relateds = $this->getLayout()->createBlock(
                Related::class
            )->setData(
                'position',
                $this->getPosition()
            )->setTemplate($this->getTemplate());

            return $relateds->toHtml();
        }

        return '';
    }
}
