<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_Preorder
 */


namespace Amasty\Preorder\Plugin\Framework\Webapi;

use \Magento\Checkout\Model\Session as CheckoutSession;

class ServiceOutputProcessor
{
    /**
     * @var CheckoutSession
     */
    private $checkoutSession;

    /**
     * @var \Amasty\Preorder\Helper\Data
     */
    private $helper;

    /**
     * ServiceOutputProcessor constructor.
     * @param \Amasty\Preorder\Helper\Data $helper
     * @param CheckoutSession $checkoutSession
     */
    public function __construct(
        \Amasty\Preorder\Helper\Data $helper,
        CheckoutSession $checkoutSession
    ) {
        $this->checkoutSession = $checkoutSession;
        $this->helper = $helper;
    }

    /**
     * @param \Magento\Framework\Webapi\ServiceOutputProcessor $subject
     * @param $result
     * @return array
     */
    public function afterProcess(\Magento\Framework\Webapi\ServiceOutputProcessor $subject, $result)
    {
        if (is_array($result)
            && isset($result['totals']['items'])
            && is_array($result['totals']['items'])
        ) {
            /** @var \Magento\Quote\Model\Quote  */
            $quote = $this->checkoutSession->getQuote();

            foreach ($result['totals']['items'] as $index => $item) {
                if (isset($item['item_id'])) {
                    $quiteItem =  $quote->getItemById($item['item_id']);
                    $note = $this->helper->getQuoteItemPreorderNote($quiteItem);
                    if ($note) {
                        $result['totals']['items'][$index]['preorder_note'] = $note;
                    }
                }
            }
        }

        return $result;
    }
}
