<?php
/**
 * Page for the actual import step.
 */


$args = array(
	'action' => 'familab-wxr-import'
);
$url = add_query_arg( urlencode_deep( $args ), admin_url( 'admin-ajax.php' ) );

$script_data = array(
	'count' => array(
	    'package' => ($fetch_attachments ? 1: 0),
		'posts' => $data->post_count,
		'media' => $data->media_count,
		'comments' => $data->comment_count,
		'terms' => $data->term_count,
		'categories' => $data->category_count,
        'widget' => $data->widget_count,
        'setting' => $data->setting_count,
	),
	'url' => $url,
	'strings' => array(
		'complete' => __( 'Import complete!', 'familabcore' ),
	),
);

// neither IE10-11 nor Edge understand EventSource, so enqueue a polyfill
wp_enqueue_script( 'eventsource-polyfill', plugins_url( 'assets/js/eventsource-polyfill.js', FAMILAB_CORE_PLUGIN_DIR.'/familabcore/'), array(), '20160909', true );

// DataTables allows the log messages to sorted/paginated
$suffix = defined ('SCRIPT_DEBUG') && SCRIPT_DEBUG ? '' : '.min' ;
wp_enqueue_script( 'jquery.dataTables', plugins_url( "assets/3rd-party/jquery.dataTables/jquery.dataTables$suffix.js", FAMILAB_CORE_PLUGIN_DIR.'/familabcore/'), array( 'jquery' ), '20160909', true );
wp_enqueue_style( 'jquery.dataTables', plugins_url( 'assets/3rd-party/jquery.dataTables/jquery.dataTables.css', FAMILAB_CORE_PLUGIN_DIR.'/familabcore/'), array(), '20160909' );

$url = plugins_url( 'assets/js/import.js', FAMILAB_CORE_PLUGIN_DIR.'/familabcore/');
wp_enqueue_script( 'wxr-importer-import', $url, array( 'jquery' ), '20160909', true );
wp_localize_script( 'wxr-importer-import', 'wxrImportData', $script_data );

wp_enqueue_style( 'wxr-importer-import', plugins_url( 'assets/css/import.css', FAMILAB_CORE_PLUGIN_DIR.'/familabcore/'), array(), '20160909' );

?>
<div class="welcome-panel">
	<div class="welcome-panel-content">
		<h2><?php esc_html_e( 'Importing', 'familabcore' ) ?></h2>
		<div id="import-status-message" class="notice notice-info"><?php esc_html_e( 'Now importing.', 'familabcore' ) ?></div>
		<table class="import-status">
			<thead>
				<tr>
					<th><?php esc_html_e( 'Import Summary', 'familabcore' ) ?></th>
					<th><?php esc_html_e( 'Completed', 'familabcore' ) ?></th>
					<th><?php esc_html_e( 'Progress', 'familabcore' ) ?></th>
				</tr>
			</thead>
			<tbody>
			<?php if (isset($script_data['count']['package']) && isset($data->package_count)): ?>
                <tr>
                    <td>
                        <span class="dashicons dashicons-media-archive"></span>
						<?php
						echo esc_html( sprintf(
							_n( '%d Package', '%d Package', $data->package_count, 'familabcore' ),
							$data->package_count
						));
						?>
                    </td>
                    <td>
                        <span id="completed-package" class="completed">0/0</span>
                    </td>
                    <td>
                        <div class="cssProgress">
                            <div class="progress1">
                                <div id="progressbar-package" class="cssProgress-bar cssProgress-active"  style="width: 0%;">
                                    <span id="progress-package" class="cssProgress-label">0%</span>
                                </div>
                            </div>
                        </div>
                    </td>
                </tr>
			<?php endif; ?>
				<tr>
					<td>
						<span class="dashicons dashicons-admin-post"></span>
						<?php
						echo esc_html( sprintf(
							_n( '%d post (including CPTs)', '%d posts (including CPTs)', $data->post_count, 'familabcore' ),
							$data->post_count
						));
						?>
					</td>
					<td>
						<span id="completed-posts" class="completed">0/0</span>
					</td>
					<td>
                        <div class="cssProgress">
                            <div class="progress1">
                                <div id="progressbar-posts" class="cssProgress-bar cssProgress-active"  style="width: 0%;">
                                    <span id="progress-posts" class="cssProgress-label">0%</span>
                                </div>
                            </div>
                        </div>
					</td>
				</tr>
				<tr>
					<td>
						<span class="dashicons dashicons-admin-media"></span>
						<?php
						echo esc_html( sprintf(
							_n( '%d media item', '%d media items', $data->media_count, 'familabcore' ),
							$data->media_count
						));
						?>
					</td>
					<td>
						<span id="completed-media" class="completed">0/0</span>
					</td>
					<td>
                        <div class="cssProgress">
                            <div class="progress1">
                                <div id="progressbar-media" class="cssProgress-bar cssProgress-active"  style="width: 0%;">
                                    <span id="progress-media" class="cssProgress-label">0%</span>
                                </div>
                            </div>
                        </div>
					</td>
				</tr>

                <tr>
                    <td>
                        <span class="dashicons dashicons-admin-comments"></span>
                        <?php
                        echo esc_html( sprintf(
                            _n( '%d comment', '%d comments', $data->comment_count, 'familabcore' ),
                            $data->comment_count
                        ));
                        ?>
                    </td>
                    <td>
                        <span id="completed-comments" class="completed">0/0</span>
                    </td>
                    <td>
                        <div class="cssProgress">
                            <div class="progress1">
                                <div id="progressbar-comments" class="cssProgress-bar cssProgress-active"  style="width: 0%;">
                                    <span id="progress-comments" class="cssProgress-label">0%</span>
                                </div>
                            </div>
                        </div>
                    </td>
                </tr>
				<tr>
					<td>
						<span class="dashicons dashicons-category"></span>
						<?php
						echo esc_html( sprintf(
							_n( '%d term', '%d terms', $data->term_count, 'familabcore' ),
							$data->term_count
						));
						?>
					</td>
					<td>
						<span id="completed-terms" class="completed">0/0</span>
					</td>
					<td>
                        <div class="cssProgress">
                            <div class="progress1">
                                <div id="progressbar-terms" class="cssProgress-bar cssProgress-active"  style="width: 0%;">
                                    <span id="progress-terms" class="cssProgress-label">0%</span>
                                </div>
                            </div>
                        </div>
					</td>
				</tr>
				<tr>
					<td>
						<span class="dashicons dashicons-admin-category"></span>
						<?php
						echo esc_html( sprintf(
							_n( '%d category', '%d categories', $data->category_count, 'familabcore' ),
							$data->category_count
						));
						?>
					</td>
					<td>
						<span id="completed-categories" class="completed">0/0</span>
					</td>
					<td>
                        <div class="cssProgress">
                            <div class="progress1">
                                <div id="progressbar-categories" class="cssProgress-bar cssProgress-active"  style="width: 0%;">
                                    <span id="progress-categories" class="cssProgress-label">0%</span>
                                </div>
                            </div>
                        </div>
					</td>
				</tr>
                <tr>
                    <td>
                        <span class="dashicons dashicons-archive"></span>
                        <?php
                        echo esc_html( sprintf(
	                        _n( '%d Widget', '%d Widgets', $data->widget_count, 'familabcore' ),
	                        $data->widget_count
                        ));
                        ?>
                    </td>
                    <td>
                        <span id="completed-widget" class="completed">0/0</span>
                    </td>
                    <td>
                        <div class="cssProgress">
                            <div class="progress1">
                                <div id="progressbar-widget" class="cssProgress-bar cssProgress-active"  style="width: 0%;">
                                    <span id="progress-widget" class="cssProgress-label">0%</span>
                                </div>
                            </div>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>
                        <span class="dashicons dashicons-admin-generic"></span>
                        <?php
                        echo esc_html( sprintf(
                            _n( '%d Setting', '%d Settings', $data->setting_count, 'familabcore' ),
                            $data->setting_count
                        ));
                        ?>
                    </td>
                    <td>
                        <span id="completed-setting" class="completed">0/0</span>
                    </td>
                    <td>
                        <div class="cssProgress">
                            <div class="progress1">
                                <div id="progressbar-setting" class="cssProgress-bar cssProgress-active"  style="width: 0%;">
                                    <span id="progress-setting" class="cssProgress-label">0%</span>
                                </div>
                            </div>
                        </div>
                    </td>
                </tr>
			</tbody>
		</table>
		<div class="import-status-indicator">
            <div class="cssProgress">
                <div class="progress1">
                    <div id="progressbar-total" class="cssProgress-bar cssProgress-active"  style="width: 0%;">
                        <span id="progress-total" class="cssProgress-label">0%</span>
                    </div>
                </div>
            </div>
			<div class="status">
				<span id="completed-total" class="completed">0/0</span>
			</div>
		</div>
	</div>
</div>

<table id="import-log" class="widefat">
	<thead>
		<tr>
			<th class='type'><?php esc_html_e( 'Type', 'familabcore' ) ?></th>
			<th><?php esc_html_e( 'Message', 'familabcore' ) ?></th>
		</tr>
	</thead>
	<tbody>
	</tbody>
</table>