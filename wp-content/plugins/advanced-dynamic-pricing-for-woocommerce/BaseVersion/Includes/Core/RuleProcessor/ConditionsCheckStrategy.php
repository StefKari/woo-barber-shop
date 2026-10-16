<?php

namespace ADP\BaseVersion\Includes\Core\RuleProcessor;

use ADP\BaseVersion\Includes\Core\Cart\Cart;
use ADP\BaseVersion\Includes\Core\Rule\Rule;

defined('ABSPATH') or exit;

class ConditionsCheckStrategy
{
    /**
     * @var Rule
     */
    protected $rule;

    /**
     * @param Rule $rule
     */
    public function __construct($rule)
    {
        $this->rule = $rule;
    }

    /**
     * @param Cart $cart
     *
     * @return bool
     */
    public function check($cart)
    {

        if (adp_context()->getOption('allow_customers_choose_between_discounts')&& $this->rule->isExclusive()) {
            $session_key = WC()->session->get_customer_id();
            $transient_key = 'adp_selected_rules_' . $session_key;
            $selectedRules = get_transient($transient_key) ?: [];
            foreach ($cart->getItems() as $cartItem) {
                $productId = $cartItem->getWcItem()->getProduct()->get_id();
                if (isset($selectedRules[$productId]) && (int)$selectedRules[$productId] != $this->rule->getId()) {
                    return false;
                }
            }
        }

        $conditions = $this->rule->getConditions();

        if (count($conditions) === 0) {
            return true;
        }

        if( apply_filters("adp_apply_rule_custom_conditions_check", false, $conditions, $cart, $this->rule) ) {
            return true;
        }

        $relationship = $this->rule->getConditionsRelationship();
        $result       = false;

        foreach ($conditions as $condition) {
            if ($condition->check($cart)) {
                // check_conditions always true if relationship not 'and' and at least one condition checked
                $result = true;
            } elseif ('and' == $relationship) {
                return false;
            }
        }

        return $result;
    }

    /**
     * @param Cart $cart
     *
     * @return bool
     */
    public function match($cart)
    {
        $conditions = $this->rule->getConditions();

        if (count($conditions) === 0) {
            return true;
        }

        $relationship = $this->rule->getConditionsRelationship();
        $result       = false;

        foreach ($conditions as $condition) {
            if ($condition->match($cart)) {
                // check_conditions always true if relationship not 'and' and at least one condition checked
                $result = true;
            } elseif ('and' == $relationship) {
                return false;
            }
        }

        return $result;
    }
}
