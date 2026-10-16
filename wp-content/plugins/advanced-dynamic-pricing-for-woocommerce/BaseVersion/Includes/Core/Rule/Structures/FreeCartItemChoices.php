<?php

namespace ADP\BaseVersion\Includes\Core\Rule\Structures;

use ADP\BaseVersion\Includes\Core\Rule\Rule;
use ADP\BaseVersion\Includes\Enums\GiftModeEnum;

defined('ABSPATH') or exit;

class FreeCartItemChoices implements \Serializable
{
    /**
     * @var GiftChoice[]
     */
    protected $choices;

    /**
     * @var float
     */
    protected $requiredQty;

    /**
     * @var bool
     */
    protected $required;

    /**
     * @var array
     */
    protected $attributes;
    const ATTR_TEMP = 'temporary';

    /**
     * @var int|null
     */
    protected $ruleId;

    public function __construct()
    {
        $this->choices     = array();
        $this->requiredQty = floatval(0);
        $this->required    = false;
        $this->attributes  = array();
        $this->ruleId      = null;
    }

    public function __clone()
    {
        $newChoices = array();
        foreach ($this->choices as $newChoice) {
            $newChoices[] = clone $newChoice;
        }
        $this->choices = $newChoices;
    }

    /**
     * @param array<int, GiftChoice> $choices
     */
    public function setChoices($choices)
    {
        if ( ! is_array($choices)) {
            return;
        }

        $this->choices = array();
        foreach ($choices as $choice) {
            if ($choice instanceof GiftChoice) {
                $this->choices[] = $choice;
            }
        }
    }

    /**
     * @return array<int, GiftChoice>
     */
    public function getChoices()
    {
        return $this->choices;
    }


    /**
     * @param float $requiredQty
     */
    public function setRequiredQty($requiredQty)
    {
        if (is_numeric($requiredQty)) {
            $this->requiredQty = floatval($requiredQty);
        }
    }

    /**
     * @return float
     */
    public function getRequiredQty()
    {
        return $this->requiredQty;
    }

    /**
     * @param bool $required
     */
    public function setRequired($required)
    {
        $this->required = boolval($required);
    }

    /**
     * @return bool
     */
    public function isRequired()
    {
        return $this->required;
    }

    /**
     * @param int|null $ruleId
     */
    public function setRuleId($ruleId)
    {
        $this->ruleId = is_numeric($ruleId) ? intval($ruleId) : null;
    }

    /**
     * @return int|null
     */
    public function getRuleId()
    {
        return $this->ruleId;
    }

    /**
     * @param Rule $rule
     * @param int $index
     * @param Gift $gift
     *
     * @return string
     */
    public function generateHash($rule, $index, $gift)
    {
        if ($gift->getMode()->equals(GiftModeEnum::ALLOW_TO_CHOOSE()) || $gift->getMode()->equals(GiftModeEnum::ALLOW_TO_CHOOSE_FROM_PRODUCT_CAT())) {
            $tmpMode = "allow";
        } elseif ($gift->getMode()->equals(GiftModeEnum::REQUIRE_TO_CHOOSE()) || $gift->getMode()->equals(GiftModeEnum::REQUIRE_TO_CHOOSE_FROM_PRODUCT_CAT())) {
            $tmpMode = "required";
        } else {
            $tmpMode = "false";
        }

        $pieces = array(strval($index), strval($tmpMode), serialize($this->getDataForHash()));

        return md5(join("_", $pieces));
    }

    /**
     * @return string|null
     */
    public function serialize()
    {
        return serialize($this->__serialize());
    }

    public function __serialize()
    {
        return array_merge($this->getDataForHash(), [
            'ruleId' => $this->ruleId,
        ]);
    }

    /**
     * @return array
     */
    protected function getDataForHash()
    {
        $choices = $this->choices;
        sort($choices);

        return [
            'choices' => array_map(function ($choice) {
                return $choice->serialize();
            }, $choices),
            'requiredQty' => $this->requiredQty,
            'required'    => $this->required,
            'attributes'  => $this->attributes,
        ];
    }

    /**
     * @param string $data
     */
    public function unserialize($data)
    {
        $data = maybe_unserialize($data);

        $this->__unserialize($data);
    }

    public function __unserialize($data)
    {
        $this->choices = array_map(function( $choice ) {
            $data = maybe_unserialize($choice);
            $obj = new GiftChoice();
            $obj->unserialize($data);
            return $obj;
        }, $data['choices'] ?? []);

        $this->requiredQty = $data['requiredQty'];
        $this->required    = $data['required'];
        $this->attributes  = $data['attributes'];
        $this->ruleId      = $data['ruleId'] ?? null;
    }

    /**
     * @param string $attribute
     *
     * @return bool
     */
    public function hasAttr($attribute)
    {
        return in_array($attribute, $this->attributes);
    }

    public function addAttr(...$attributes)
    {
        $allowedAttrs = array(
            self::ATTR_TEMP,
        );

        foreach ($attributes as $attribute) {
            if (in_array($attribute, $allowedAttrs)) {
                $this->attributes[] = $attribute;
            }
        }
    }

    public function removeAttr(...$attributes)
    {
        foreach ($attributes as $attr) {
            $pos = array_search($attr, $this->attributes);

            if ($pos !== false) {
                unset($this->attributes[$pos]);
            }
        }

        $this->attributes = array_values($this->attributes);
    }

    public function getAttrs()
    {
        return $this->attributes;
    }
}
