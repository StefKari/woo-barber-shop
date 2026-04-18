<?php

namespace ADP\BaseVersion\Includes\Compatibility;

use ADP\BaseVersion\Includes\Context;
use ADP\BaseVersion\Includes\Core\Rule\Internationalization\RuleTranslator;
use ADP\BaseVersion\Includes\Core\Rule\Rule;

defined('ABSPATH') or exit;

/**
 * Plugin Name: WooCommerce Price Based on Country
 * Author: Oscar Gare
 *
 * @see https://wordpress.org/plugins/woocommerce-product-price-based-on-countries/
 */
class PriceBasedOnCountryCmp
{
    /**
     * @var Context
     */
    protected $context;

    /**
     * @param null $deprecated
     */
    public function __construct($deprecated = null)
    {
        $this->context = adp_context();
    }

    public function withContext(Context $context)
    {
        $this->context = $context;
    }

    /**
     * @return bool
     */
    public function isActive()
    {
        return defined("WCPBC_PLUGIN_FILE");
    }

    /**
     * @param Rule $rule
     *
     * @return Rule
     */
    public function changeRuleCurrency($rule): Rule
    {
        if ( ! function_exists("wcpbc_get_zone_by_country")) {
            return $rule;
        }

        if ( ! ($zone = wcpbc_get_zone_by_country())) {
            return $rule;
        }

        if ($rate = $zone->get_real_exchange_rate()) {
            $rule = RuleTranslator::setCurrency($rule, $rate);
        }

        return $rule;
    }
}
