<?php

namespace ADP\BaseVersion\Includes\Compatibility;

use ADP\BaseVersion\Includes\Context;

defined('ABSPATH') or exit;

/**
 * Plugin Name: YITH WooCommerce Gift Cards
 * Author: YITH
 *
 * @see https://wordpress.org/plugins/yith-woocommerce-gift-cards/
 */
class YithGiftCardsCmp
{
    /**
     * @var Context
     */
    private $context;

    /**
     * @var \YITH_YWGC_Cart_Checkout
     */
    private $cartCheckout;

    /**
     * Active totals passes, tracked separately for each WC_Cart instance.
     *
     * @var array<string|int, int>
     */
    private $totalsCalculationDepth = array();

    public function __construct($deprecated = null)
    {
        $this->context = adp_context();
    }

    public function withContext(Context $context)
    {
        $this->context = $context;
    }

    public function applyCompatibility()
    {
        if ( ! $this->isActive()) {
            return;
        }

        add_filter('wdp_calculate_totals_hook_priority', function ($priority) {
            return $priority - 1;
        });

        $instance = function_exists('YITH_YWGC_Cart_Checkout') ? YITH_YWGC_Cart_Checkout() : \YITH_YWGC_Cart_Checkout::get_instance();
        if (false === ($priority = has_action('woocommerce_after_calculate_totals',
                [$instance, 'apply_gift_cards_discount']))) {
            return;
        }
        remove_action('woocommerce_after_calculate_totals', [$instance, 'apply_gift_cards_discount'], $priority);

        $this->cartCheckout = $instance;
        add_action('woocommerce_after_calculate_totals', [$this, 'beginTotalsCalculation'], PHP_INT_MIN);
        add_action('woocommerce_after_calculate_totals', [$this, 'applyGiftCardsDiscount'], PHP_INT_MAX);

        add_filter('adp_get_original_product_from_cart', function($product, $wcCartItem) {
            if ($product instanceof \WC_Product_Gift_Card) {
                $productExt = new \ADP\BaseVersion\Includes\ProductExtensions\ProductExtension($product);
                $cartItemData = $wcCartItem->getCartItemData();

                $price = $cartItemData['ywgc_amount'] ?? $product->get_price();

                $productExt->setCustomPrice($price);
                $product->set_price($price);
            }
            return $product;
        }, 10, 2);
    }

    /**
     * @param \WC_Cart $cart
     *
     * @return string|int
     */
    private function getCartKey($cart)
    {
        if (\PHP_VERSION_ID < 80600) {
            //phpcs:ignore PHPCompatibility.FunctionUse.RemovedFunctions
            return spl_object_hash($cart);
        }

        return spl_object_id($cart);
    }

    /**
     * WooCommerce Tax can dispatch this action again before its outer pass ends.
     * Tracking the cart object also allows independent nested carts to finish.
     *
     * @param \WC_Cart $cart
     */
    public function beginTotalsCalculation($cart)
    {
        if ( ! $cart instanceof \WC_Cart) {
            return;
        }

        $key = $this->getCartKey($cart);
        $this->totalsCalculationDepth[$key] = ($this->totalsCalculationDepth[$key] ?? 0) + 1;
    }

    /**
     * Apply credit once at the end of the outermost totals pass for this cart.
     * YITH resets the applied amounts and deducts from the current grand total.
     * On an ADP cache hit, an inner deduction would otherwise survive into the
     * outer pass, where YITH would deduct again or replace the credit with zero.
     *
     * @param \WC_Cart $cart
     */
    public function applyGiftCardsDiscount($cart)
    {
        if ( ! $cart instanceof \WC_Cart) {
            return;
        }

        $key = $this->getCartKey($cart);
        $depth = ($this->totalsCalculationDepth[$key] ?? 1) - 1;

        if ($depth > 0) {
            $this->totalsCalculationDepth[$key] = $depth;
            return;
        }

        unset($this->totalsCalculationDepth[$key]);
        $this->cartCheckout->apply_gift_cards_discount($cart);
    }

    public function isActive()
    {
        return defined('YITH_YWGC_PLUGIN_NAME');
    }
}
