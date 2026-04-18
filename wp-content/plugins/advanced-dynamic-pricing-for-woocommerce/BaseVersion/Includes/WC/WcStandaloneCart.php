<?php

namespace ADP\BaseVersion\Includes\WC;

defined('ABSPATH') or exit;

class WcStandaloneCart extends \WC_Cart
{
    public function __construct()
    {
        $this->session  = new \WC_Cart_Session($this);
        $this->fees_api = new \WC_Cart_Fees($this);
    }
}
