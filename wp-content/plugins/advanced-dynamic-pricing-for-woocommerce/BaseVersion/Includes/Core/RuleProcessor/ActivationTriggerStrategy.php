<?php

namespace ADP\BaseVersion\Includes\Core\RuleProcessor;

use ADP\BaseVersion\Includes\Core\Cart\Cart;
use ADP\BaseVersion\Includes\Core\Rule\Rule;

defined('ABSPATH') or exit;

class ActivationTriggerStrategy
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
    public function canBeAppliedUsingCouponCode($cart)
    {
        if ($this->rule->getActivationCouponCode() === null) {
            return true;
        }

        return in_array($this->rule->getActivationCouponCode(), $cart->getRuleTriggerCoupons(), true);
    }
}
