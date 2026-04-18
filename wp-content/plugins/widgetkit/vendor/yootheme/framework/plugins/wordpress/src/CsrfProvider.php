<?php

namespace YOOtheme\Framework\Wordpress;

use YOOtheme\Framework\Csrf\DefaultCsrfProvider;

class CsrfProvider extends DefaultCsrfProvider
{
    /**
     * {@inheritdoc}
     */
    public function generate()
    {
        return wp_create_nonce();
    }
}
