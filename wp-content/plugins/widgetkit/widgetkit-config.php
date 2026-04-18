<div class="wrap">

    <h2>Widgetkit</h2>

    <form name="form"
          action="<?php echo add_query_arg(array('page' => $app['name'] . '-config', 'action' => 'save'), admin_url('options-general.php')); ?>"
          method="post">

        <table class="form-table">
            <tr>
                <th><?php echo $app['translator']->trans('YOOtheme API Key'); ?></th>
                <td>
                    <label for="config-apikey">
                        <input id="config-apikey" class="regular-text" type="text" name="config[apikey]"
                               value="<?php echo $app['config']->get('apikey'); ?>">
                    </label>

                    <p class="description"><?php echo $app['translator']->trans('In order to update commercial extensions set up your API Key which can be found in your %link% account.', array('%link%' => '<a href="http://yootheme.com/account" target="_blank">YOOtheme</a>')); ?></p>
                </td>
            </tr>
        </table>

        <?php wp_nonce_field(); ?>
        <?php submit_button(); ?>
    </form>
</div>