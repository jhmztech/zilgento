<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_Promo
 */

namespace Amasty\Promo\Model;

use Amasty\Promo\Api\Data\GiftRuleInterface;

class Rule extends \Magento\Framework\Model\AbstractModel implements GiftRuleInterface
{
    const RULE_TYPE_ALL = 0;
    const RULE_TYPE_ONE = 1;

    const NOT_AUTO_FREE_ITEMS = 0;
    const AUTO_FREE_ITEMS = 1;
    const AUTO_FREE_DISCOUNTED_ITEMS = 2;

    /**
     * Set resource model and Id field name
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_init(\Amasty\Promo\Model\ResourceModel\Rule::class);
        $this->setIdFieldName(self::ENTITY_ID);
    }

    /**
     * @inheritdoc
     */
    public function getEntityId()
    {
        return $this->_getData(GiftRuleInterface::ENTITY_ID);
    }

    /**
     * @inheritdoc
     */
    public function setEntityId($entityId)
    {
        $this->setData(GiftRuleInterface::ENTITY_ID, $entityId);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getSalesruleId()
    {
        return $this->_getData(GiftRuleInterface::SALESRULE_ID);
    }

    /**
     * @inheritdoc
     */
    public function setSalesruleId($salesruleId)
    {
        $this->setData(GiftRuleInterface::SALESRULE_ID, $salesruleId);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getSku()
    {
        return $this->_getData(GiftRuleInterface::SKU);
    }

    /**
     * @inheritdoc
     */
    public function setSku($sku)
    {
        $this->setData(GiftRuleInterface::SKU, $sku);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getType()
    {
        return $this->_getData(GiftRuleInterface::TYPE);
    }

    /**
     * @inheritdoc
     */
    public function setType($type)
    {
        $this->setData(GiftRuleInterface::TYPE, $type);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getTopBannerImage()
    {
        return $this->_getData(GiftRuleInterface::TOP_BANNER_IMAGE);
    }

    /**
     * @inheritdoc
     */
    public function setTopBannerImage($topBannerImage)
    {
        $this->setData(GiftRuleInterface::TOP_BANNER_IMAGE, $topBannerImage);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getTopBannerAlt()
    {
        return $this->_getData(GiftRuleInterface::TOP_BANNER_ALT);
    }

    /**
     * @inheritdoc
     */
    public function setTopBannerAlt($topBannerAlt)
    {
        $this->setData(GiftRuleInterface::TOP_BANNER_ALT, $topBannerAlt);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getTopBannerOnHoverText()
    {
        return $this->_getData(GiftRuleInterface::TOP_BANNER_ON_HOVER_TEXT);
    }

    /**
     * @inheritdoc
     */
    public function setTopBannerOnHoverText($topBannerOnHoverText)
    {
        $this->setData(GiftRuleInterface::TOP_BANNER_ON_HOVER_TEXT, $topBannerOnHoverText);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getTopBannerLink()
    {
        return $this->_getData(GiftRuleInterface::TOP_BANNER_LINK);
    }

    /**
     * @inheritdoc
     */
    public function setTopBannerLink($topBannerLink)
    {
        $this->setData(GiftRuleInterface::TOP_BANNER_LINK, $topBannerLink);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getTopBannerShowGiftImages()
    {
        return $this->_getData(GiftRuleInterface::TOP_BANNER_SHOW_GIFT_IMAGES);
    }

    /**
     * @inheritdoc
     */
    public function setTopBannerShowGiftImages($topBannerShowGiftImages)
    {
        $this->setData(GiftRuleInterface::TOP_BANNER_SHOW_GIFT_IMAGES, $topBannerShowGiftImages);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getTopBannerDescription()
    {
        return $this->_getData(GiftRuleInterface::TOP_BANNER_DESCRIPTION);
    }

    /**
     * @inheritdoc
     */
    public function setTopBannerDescription($topBannerDescription)
    {
        $this->setData(GiftRuleInterface::TOP_BANNER_DESCRIPTION, $topBannerDescription);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getAfterProductBannerImage()
    {
        return $this->_getData(GiftRuleInterface::AFTER_PRODUCT_BANNER_IMAGE);
    }

    /**
     * @inheritdoc
     */
    public function setAfterProductBannerImage($afterProductBannerImage)
    {
        $this->setData(GiftRuleInterface::AFTER_PRODUCT_BANNER_IMAGE, $afterProductBannerImage);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getAfterProductBannerAlt()
    {
        return $this->_getData(GiftRuleInterface::AFTER_PRODUCT_BANNER_ALT);
    }

    /**
     * @inheritdoc
     */
    public function setAfterProductBannerAlt($afterProductBannerAlt)
    {
        $this->setData(GiftRuleInterface::AFTER_PRODUCT_BANNER_ALT, $afterProductBannerAlt);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getAfterProductBannerOnHoverText()
    {
        return $this->_getData(GiftRuleInterface::AFTER_PRODUCT_BANNER_ON_HOVER_TEXT);
    }

    /**
     * @inheritdoc
     */
    public function setAfterProductBannerOnHoverText($afterProductBannerOnHoverText)
    {
        $this->setData(GiftRuleInterface::AFTER_PRODUCT_BANNER_ON_HOVER_TEXT, $afterProductBannerOnHoverText);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getAfterProductBannerLink()
    {
        return $this->_getData(GiftRuleInterface::AFTER_PRODUCT_BANNER_LINK);
    }

    /**
     * @inheritdoc
     */
    public function setAfterProductBannerLink($afterProductBannerLink)
    {
        $this->setData(GiftRuleInterface::AFTER_PRODUCT_BANNER_LINK, $afterProductBannerLink);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getAfterProductBannerShowGiftImages()
    {
        return $this->_getData(GiftRuleInterface::AFTER_PRODUCT_BANNER_SHOW_GIFT_IMAGES);
    }

    /**
     * @inheritdoc
     */
    public function setAfterProductBannerShowGiftImages($afterProductBannerShowGiftImages)
    {
        $this->setData(GiftRuleInterface::AFTER_PRODUCT_BANNER_SHOW_GIFT_IMAGES, $afterProductBannerShowGiftImages);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getAfterProductBannerDescription()
    {
        return $this->_getData(GiftRuleInterface::AFTER_PRODUCT_BANNER_DESCRIPTION);
    }

    /**
     * @inheritdoc
     */
    public function setAfterProductBannerDescription($afterProductBannerDescription)
    {
        $this->setData(GiftRuleInterface::AFTER_PRODUCT_BANNER_DESCRIPTION, $afterProductBannerDescription);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getLabelImage()
    {
        return $this->_getData(GiftRuleInterface::LABEL_IMAGE);
    }

    /**
     * @inheritdoc
     */
    public function setLabelImage($labelImage)
    {
        $this->setData(GiftRuleInterface::LABEL_IMAGE, $labelImage);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getLabelImageAlt()
    {
        return $this->_getData(GiftRuleInterface::LABEL_IMAGE_ALT);
    }

    /**
     * @inheritdoc
     */
    public function setLabelImageAlt($labelImageAlt)
    {
        $this->setData(GiftRuleInterface::LABEL_IMAGE_ALT, $labelImageAlt);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getItemsDiscount()
    {
        return $this->_getData(GiftRuleInterface::ITEMS_DISCOUNT);
    }

    /**
     * @inheritdoc
     */
    public function setItemsDiscount($itemsDiscount)
    {
        $this->setData(GiftRuleInterface::ITEMS_DISCOUNT, $itemsDiscount);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getMinimalItemsPrice()
    {
        return $this->_getData(GiftRuleInterface::MINIMAL_ITEMS_PRICE);
    }

    /**
     * @inheritdoc
     */
    public function setMinimalItemsPrice($minimalItemsPrice)
    {
        $this->setData(GiftRuleInterface::MINIMAL_ITEMS_PRICE, $minimalItemsPrice);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getApplyTax()
    {
        return $this->_getData(GiftRuleInterface::APPLY_TAX);
    }

    /**
     * @inheritdoc
     */
    public function setApplyTax($applyTax)
    {
        $this->setData(GiftRuleInterface::APPLY_TAX, $applyTax);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getApplyShipping()
    {
        return $this->_getData(GiftRuleInterface::APPLY_SHIPPING);
    }

    /**
     * @inheritdoc
     */
    public function setApplyShipping($applyShipping)
    {
        $this->setData(GiftRuleInterface::APPLY_SHIPPING, $applyShipping);

        return $this;
    }
}
