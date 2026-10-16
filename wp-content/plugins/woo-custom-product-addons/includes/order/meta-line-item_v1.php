<?php
if (is_array($meta_data) && count($meta_data)) {
    ?>
    <table>
        <tr>
            <th><?php _e('Options', 'woo-custom-product-addons') ?></th>
            <th><?php _e('Value', 'woo-custom-product-addons') ?></th>

            <th></th>
        </tr>

        <?php
        foreach ($meta_data as $k => $data) {
            if(!is_array($data)){
                continue;
            }
            if (in_array($data['type'], array('checkbox-group', 'select', 'radio-group')) && is_array($data['value'])) {
                $label_printed = false;
                foreach ($data['value'] as $l => $v) {
                    ?>
                    <tr class="item_wcpa">
                        <td class="name">
                            <?php
                            echo $label_printed ? '' : esc_html($data['label']);
                            $label_printed = true;
                            ?>
                        </td>

                        <td class="value" >
                            <div class="view">
                                <?php
                                if (isset($v['i'])) {
                                    echo '<strong>' . esc_html__('Label:', 'woo-custom-product-addons') . '</strong> ' . esc_html($v['label']) . '<br>';
                                    echo '<strong>' . esc_html__('Value:', 'woo-custom-product-addons') . '</strong> ' . esc_html($v['value']);
                                } else {
                                    echo esc_html($v);
                                }
                                ?>

                            </div>
                            <div class="edit" style="display: none;">
                                <?php
                                if (isset($v['i'])) {
                                        ?>
                                <?php echo '<strong>' . esc_html__('Label:', 'woo-custom-product-addons') . '</strong>'; ?>  <input type="text" name="wcpa_meta[value][<?php echo esc_attr($item_id); ?>][<?php echo esc_attr($k); ?>][<?php echo esc_attr($l); ?>][label]"
                                        value="<?php echo esc_attr($v['label']); ?>"> <br>
                                <?php echo '<strong>' . esc_html__('Value:', 'woo-custom-product-addons') . '</strong>'; ?> <input type="text" name="wcpa_meta[value][<?php echo esc_attr($item_id); ?>][<?php echo esc_attr($k); ?>][<?php echo esc_attr($l); ?>][value]"
                                        value="<?php echo esc_attr($v['value']); ?>">
                                        <?php
                                    } else {
                                        ?>
                                <input type="text" name="wcpa_meta[value][<?php echo esc_attr($item_id); ?>][<?php echo esc_attr($k); ?>][<?php echo esc_attr($l); ?>]" value="<?php echo esc_attr($v); ?>">

                            <?php }
                                ?>


                            </div>
                        </td>

                        <td class="wc-order-edit-line-item" width="1%">
                            <div class = "wc-order-edit-line-item-actions edit" style="display: none;">
                                <a class="wcpa_delete-order-item tips" href="#" data-tip="<?php esc_attr_e('Delete item', 'woocommerce'); ?>"></a>
                            </div>
                        </td>
                    </tr>
                    <?php
                }
            } else {
                ?>
                <tr class="item_wcpa">

                    <td class="name">

                        <?php
                        if ($data['type'] == 'hidden' && empty($data['label'])) {
                            echo esc_html($data['label']) . '[hidden]';
                        } else {
                            echo esc_html($data['label']);
                        }
                        ?>
                    </td>
                    <td class="value" >
                        <div class="view">

                            <?php
                            if ($data['type'] == 'color') {
                                echo '<span style = "color:' . esc_attr($data['value']) . ';font-size: 20px;
            padding: 0;
    line-height: 0;">&#9632;</span>' . esc_html($data['value']);
                            } else {
                                echo nl2br(esc_html($data['value']));
                            }
                            ?>
                        </div>

                        <div class="edit" style="display: none;">
                            <?php
                            if ($data['type'] == 'paragraph' || $data['type'] == 'header') {
                                echo wp_kses_post($data['value']);
                                echo '<input type="hidden" 
                                       name="wcpa_meta[value][' . esc_attr($item_id) . '][' . esc_attr($k) . ']" 
                                       value="1">';
                            } else if($data['type'] == 'textarea' ) {
                                ?>
                                <textarea  name="wcpa_meta[value][<?php echo esc_attr($item_id); ?>][<?php echo esc_attr($k); ?>]" ><?php echo esc_textarea($data['value']); ?></textarea>
                                <?php
                            }
                            else {
                                ?>
                                <input type="text" 
                                       name="wcpa_meta[value][<?php echo esc_attr($item_id); ?>][<?php echo esc_attr($k); ?>]" 
                                       value="<?php echo esc_attr($data['value']); ?>">
                                       <?php
                                   }
                                   ?>

                        </div>
                    </td>


                    <td class = "wc-order-edit-line-item" width = "1%">
                        <div class = "wc-order-edit-line-item-actions edit" style="display: none;">
                            <a class="wcpa_delete-order-item tips" href="#" data-tip="<?php esc_attr_e('Delete item', 'woocommerce'); ?>"></a>
                        </div>
                    </td>
                </tr>
                <?php
            }
            ?>


            <?php
        }
        ?>
        <tr>
            <!--   /* dummy field , it will help to iterate through all data for removing last item*/-->
        <input type="hidden" name="wcpa_meta[value][<?php echo esc_attr($item_id); ?>][<?php echo esc_attr($k + 99); ?>]" value="">

        </tr>
    </table>

    <?php
}