<?php

namespace ADP\BaseVersion\Includes\Compatibility;

use ADP\BaseVersion\Includes\Context;
use ADP\BaseVersion\Includes\ProductExtensions\ProductExtension;
use ADP\BaseVersion\Includes\WC\WcCartItemFacade;
use WC_Cart;
use WC_Product;

defined('ABSPATH') or exit;

/**
 * Plugin Name: Donation For Woocommerce
 * Author: WPExperts
 *
 * @see https://woocommerce.com/products/donation-product-for-woocommerce/
 */
class DonationForWoocommerceCmp
{
    const CART_ITEM_AMOUNT_KEY = 'custom_price';
    const CART_ITEM_FEE_KEY = 'fees_percent';
    const CART_ITEM_FEE_TYPE_KEY = 'fee_type';

    const FEE_TYPE_PERCENTAGE = 'percentage';
    const DONATION_PRICE_HOOK_METHOD = 'wc_donation_alter_price_cart';

    /**
     * @var Context
     */
    protected $context;

    protected static $hookRemoved = false;

    public function __construct()
    {
        $this->context = adp_context();
    }

    public function withContext(Context $context)
    {
        $this->context = $context;
    }

    public function isActive()
    {
        return defined('WC_DONATION_VERSION') && class_exists('WcDonationOrder');
    }

    public function prepareHooks()
    {
        if ( ! $this->isActive()) {
            return;
        }

        add_action('woocommerce_before_calculate_totals', array($this, 'removeDonationPriceHook'), 1);
        add_action('woocommerce_before_calculate_totals', array($this, 'applyDonationPrices'), 99);

        add_filter('adp_product_get_price', array($this, 'filterInitialPrice'), 10, 6);
    }

    public function removeDonationPriceHook()
    {
        if (self::$hookRemoved) {
            return;
        }

        self::$hookRemoved = true;

        global $wp_filter;

        $hook = 'woocommerce_before_calculate_totals';

        if (empty($wp_filter[$hook]) || ! isset($wp_filter[$hook]->callbacks)) {
            return;
        }

        foreach ($wp_filter[$hook]->callbacks as $priority => $callbacks) {
            foreach ($callbacks as $callback) {
                $function = $callback['function'];

                if (is_array($function)
                    && isset($function[1])
                    && self::DONATION_PRICE_HOOK_METHOD === $function[1]
                    && is_object($function[0])
                ) {
                    remove_action($hook, $function, $priority);
                }
            }
        }
    }

    /**
     *
     * @param WC_Cart $wcCart
     */
    public function applyDonationPrices($wcCart)
    {
        if (is_admin() && ! defined('DOING_AJAX')) {
            return;
        }

        if ( ! $wcCart instanceof WC_Cart) {
            return;
        }

        foreach ($wcCart->get_cart() as $wcCartItem) {
            if (empty($wcCartItem[WcCartItemFacade::KEY_PRODUCT]) || ! $wcCartItem[WcCartItemFacade::KEY_PRODUCT] instanceof WC_Product) {
                continue;
            }

            $product = $wcCartItem[WcCartItemFacade::KEY_PRODUCT];
            $price   = $this->calculateItemPrice($wcCartItem, $product);

            if (null !== $price) {
                $product->set_price($price);
            }
        }
    }

    /**
     *
     * @param float|null       $price
     * @param WC_Product       $product
     * @param array            $variation
     * @param float            $qty
     * @param array            $trdPartyData
     * @param WcCartItemFacade $facade
     *
     * @return float|null
     */
    public function filterInitialPrice($price, $product, $variation, $qty, $trdPartyData, $facade)
    {
        if (!$product instanceof WC_Product || ! is_array($trdPartyData)) {
            return $price;
        }

        $donationPrice = $this->calculateItemPrice($trdPartyData, $product);

        return null === $donationPrice ? $price : $donationPrice;
    }

    /**
     *
     * @param array      $itemData
     * @param WC_Product $product
     *
     * @return float|null
     */
    protected function calculateItemPrice($itemData, $product)
    {
        if (!isset($itemData[self::CART_ITEM_AMOUNT_KEY])) {
            return null;
        }

        $amount = floatval($itemData[self::CART_ITEM_AMOUNT_KEY]);
        $fee = 0.0;

        if (!empty($itemData[self::CART_ITEM_FEE_KEY])) {
            $feeType = isset($itemData[self::CART_ITEM_FEE_TYPE_KEY]) ? $itemData[self::CART_ITEM_FEE_TYPE_KEY] : '';

            $fee = self::FEE_TYPE_PERCENTAGE === $feeType
                ? $amount * (floatval($itemData[self::CART_ITEM_FEE_KEY]) / 100)
                : floatval($itemData[self::CART_ITEM_FEE_KEY]);
        }

        return $this->getCatalogPrice($product) + $amount + $fee;
    }

    /**
     *
     * @param WC_Product $product
     *
     * @return float
     */
    protected function getCatalogPrice($product)
    {
        $productExt = new ProductExtension($this->context, $product);

        return floatval($productExt->getProductPriceDependsOnPriceMode());
    }
}
