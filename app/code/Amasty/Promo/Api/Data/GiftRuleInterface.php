<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_Promo
 */


namespace Amasty\Promo\Api\Data;

use Magento\Framework\Api\ExtensibleDataInterface;

interface GiftRuleInterface extends ExtensibleDataInterface
{
    const RULE_NAME = 'ampromo_rule';
    const EXTENSION_CODE = self::RULE_NAME;

    const IMAGE_FIELDS = [self::AFTER_PRODUCT_BANNER_IMAGE, self::LABEL_IMAGE, self::TOP_BANNER_IMAGE];

    /**#@+
     * Sales Rule Simple Action values
     */
    const SAME_PRODUCT = 'ampromo_product'; //Auto add the same product
    const PER_PRODUCT = 'ampromo_items'; //Auto add promo items with products
    const WHOLE_CART = 'ampromo_cart'; //Auto add promo items for the whole cart
    const SPENT = 'ampromo_spent'; //Auto add promo items for every $X spent
    const EACHN = 'ampromo_eachn'; //Add gift with each N-th product in the cart
    /**#@-*/

    /**#@+
     * Constants defined for keys of data array
     */
    const ENTITY_ID = 'entity_id';
    const SALESRULE_ID = 'salesrule_id';
    const SKU = 'sku';
    const TYPE = 'type';
    const TOP_BANNER_IMAGE = 'top_banner_image';
    const TOP_BANNER_ALT = 'top_banner_alt';
    const TOP_BANNER_ON_HOVER_TEXT = 'top_banner_on_hover_text';
    const TOP_BANNER_LINK = 'top_banner_link';
    const TOP_BANNER_SHOW_GIFT_IMAGES = 'top_banner_show_gift_images';
    const TOP_BANNER_DESCRIPTION = 'top_banner_description';
    const AFTER_PRODUCT_BANNER_IMAGE = 'after_product_banner_image';
    const AFTER_PRODUCT_BANNER_ALT = 'after_product_banner_alt';
    const AFTER_PRODUCT_BANNER_ON_HOVER_TEXT = 'after_product_banner_on_hover_text';
    const AFTER_PRODUCT_BANNER_LINK = 'after_product_banner_link';
    const AFTER_PRODUCT_BANNER_SHOW_GIFT_IMAGES = 'after_product_banner_show_gift_images';
    const AFTER_PRODUCT_BANNER_DESCRIPTION = 'after_product_banner_description';
    const LABEL_IMAGE = 'label_image';
    const LABEL_IMAGE_ALT = 'label_image_alt';
    const ITEMS_DISCOUNT = 'items_discount';
    const MINIMAL_ITEMS_PRICE = 'minimal_items_price';
    const APPLY_TAX = 'apply_tax';
    const APPLY_SHIPPING = 'apply_shipping';
    /**#@-*/

    /**
     * @return string
     */
    public function getSku();

    /**
     * @param string $sku
     *
     * @return \Amasty\Promo\Api\Data\GiftRuleInterface
     */
    public function setSku($sku);

    /**
     * @return int
     */
    public function getType();

    /**
     * @param int $type
     *
     * @return \Amasty\Promo\Api\Data\GiftRuleInterface
     */
    public function setType($type);

    /**
     * @return string|null
     */
    public function getTopBannerImage();

    /**
     * @param string|null $topBannerImage
     *
     * @return \Amasty\Promo\Api\Data\GiftRuleInterface
     */
    public function setTopBannerImage($topBannerImage);

    /**
     * @return string|null
     */
    public function getTopBannerAlt();

    /**
     * @param string|null $topBannerAlt
     *
     * @return \Amasty\Promo\Api\Data\GiftRuleInterface
     */
    public function setTopBannerAlt($topBannerAlt);

    /**
     * @return string|null
     */
    public function getTopBannerOnHoverText();

    /**
     * @param string|null $topBannerOnHoverText
     *
     * @return \Amasty\Promo\Api\Data\GiftRuleInterface
     */
    public function setTopBannerOnHoverText($topBannerOnHoverText);

    /**
     * @return string|null
     */
    public function getTopBannerLink();

    /**
     * @param string|null $topBannerLink
     *
     * @return \Amasty\Promo\Api\Data\GiftRuleInterface
     */
    public function setTopBannerLink($topBannerLink);

    /**
     * @return int
     */
    public function getTopBannerShowGiftImages();

    /**
     * @param int $topBannerShowGiftImages
     *
     * @return \Amasty\Promo\Api\Data\GiftRuleInterface
     */
    public function setTopBannerShowGiftImages($topBannerShowGiftImages);

    /**
     * @return string|null
     */
    public function getTopBannerDescription();

    /**
     * @param string|null $topBannerDescription
     *
     * @return \Amasty\Promo\Api\Data\GiftRuleInterface
     */
    public function setTopBannerDescription($topBannerDescription);

    /**
     * @return string|null
     */
    public function getAfterProductBannerImage();

    /**
     * @param string|null $afterProductBannerImage
     *
     * @return \Amasty\Promo\Api\Data\GiftRuleInterface
     */
    public function setAfterProductBannerImage($afterProductBannerImage);

    /**
     * @return string|null
     */
    public function getAfterProductBannerAlt();

    /**
     * @param string|null $afterProductBannerAlt
     *
     * @return \Amasty\Promo\Api\Data\GiftRuleInterface
     */
    public function setAfterProductBannerAlt($afterProductBannerAlt);

    /**
     * @return string|null
     */
    public function getAfterProductBannerOnHoverText();

    /**
     * @param string|null $afterProductBannerOnHoverText
     *
     * @return \Amasty\Promo\Api\Data\GiftRuleInterface
     */
    public function setAfterProductBannerOnHoverText($afterProductBannerOnHoverText);

    /**
     * @return string|null
     */
    public function getAfterProductBannerLink();

    /**
     * @param string|null $afterProductBannerLink
     *
     * @return \Amasty\Promo\Api\Data\GiftRuleInterface
     */
    public function setAfterProductBannerLink($afterProductBannerLink);

    /**
     * @return int
     */
    public function getAfterProductBannerShowGiftImages();

    /**
     * @param int $afterProductBannerShowGiftImages
     *
     * @return \Amasty\Promo\Api\Data\GiftRuleInterface
     */
    public function setAfterProductBannerShowGiftImages($afterProductBannerShowGiftImages);

    /**
     * @return string|null
     */
    public function getAfterProductBannerDescription();

    /**
     * @param string|null $afterProductBannerDescription
     *
     * @return \Amasty\Promo\Api\Data\GiftRuleInterface
     */
    public function setAfterProductBannerDescription($afterProductBannerDescription);

    /**
     * @return string|null
     */
    public function getLabelImage();

    /**
     * @param string|null $labelImage
     *
     * @return \Amasty\Promo\Api\Data\GiftRuleInterface
     */
    public function setLabelImage($labelImage);

    /**
     * @return string|null
     */
    public function getLabelImageAlt();

    /**
     * @param string|null $labelImageAlt
     *
     * @return \Amasty\Promo\Api\Data\GiftRuleInterface
     */
    public function setLabelImageAlt($labelImageAlt);

    /**
     * @return string|null
     */
    public function getItemsDiscount();

    /**
     * @param string|null $itemsDiscount
     *
     * @return \Amasty\Promo\Api\Data\GiftRuleInterface
     */
    public function setItemsDiscount($itemsDiscount);

    /**
     * @return float|null
     */
    public function getMinimalItemsPrice();

    /**
     * @param float|null $minimalItemsPrice
     *
     * @return \Amasty\Promo\Api\Data\GiftRuleInterface
     */
    public function setMinimalItemsPrice($minimalItemsPrice);

    /**
     * @return int
     */
    public function getApplyTax();

    /**
     * @param int $applyTax
     *
     * @return \Amasty\Promo\Api\Data\GiftRuleInterface
     */
    public function setApplyTax($applyTax);

    /**
     * @return int
     */
    public function getApplyShipping();

    /**
     * @param int $applyShipping
     *
     * @return \Amasty\Promo\Api\Data\GiftRuleInterface
     */
    public function setApplyShipping($applyShipping);
}
