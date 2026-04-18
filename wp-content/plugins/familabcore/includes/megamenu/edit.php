<?php
if( !class_exists('Familab_Core_Megamenu_Edit')){
    class  Familab_Core_Megamenu_Edit extends Walker_Nav_Menu{

        /**
         * Starts the list before the elements are added.
         *
         * @see Walker_Nav_Menu::start_lvl()
         *
         * @since 3.0.0
         *
         * @param string $output Passed by reference.
         * @param int $depth Depth of menu item. Used for padding.
         * @param array $args Not used.
         */
        public function start_lvl( &$output, $depth = 0, $args = array() ) { }

        /**
         * Ends the list of after the elements are added.
         *
         * @see Walker_Nav_Menu::end_lvl()
         *
         * @since 3.0.0
         *
         * @param string $output Passed by reference.
         * @param int $depth Depth of menu item. Used for padding.
         * @param array $args Not used.
         */
        public function end_lvl( &$output, $depth = 0, $args = array() ) { }

        public function start_el( &$output, $item, $depth = 0, $args = array(), $id = 0 ){
            global $_wp_nav_menu_max_depth;
            $_wp_nav_menu_max_depth = $depth > $_wp_nav_menu_max_depth ? $depth : $_wp_nav_menu_max_depth;
            $menu_id                = $this->get_selected_menu_id();
            ob_start();
            $item_id        = esc_attr( $item->ID );
            $removed_args   = array(
                'action',
                'customlink-tab',
                'edit-menu-item',
                'menu-item',
                'page-tab',
                '_wpnonce',
            );
            $original_title = false;
            if ( 'taxonomy' == $item->type ) {
                $original_title = get_term_field( 'name', $item->object_id, $item->object, 'raw' );
                if ( is_wp_error( $original_title ) )
                    $original_title = false;
            } elseif ( 'post_type' == $item->type ) {
                $original_object = get_post( $item->object_id );
                $original_title  = get_the_title( $original_object->ID );
            } elseif ( 'post_type_archive' == $item->type ) {
                $original_object = get_post_type_object( $item->object );
                if ( $original_object ) {
                    $original_title = $original_object->labels->archives;
                }
            }
            $classes = array(
                'menu-item menu-item-depth-' . $depth,
                'menu-item-' . esc_attr( $item->object ),
                'menu-item-edit-' . ( ( isset( $_GET['edit-menu-item'] ) && $item_id == $_GET['edit-menu-item'] ) ? 'active' : 'inactive' ),
            );
            $title   = $item->title;
            if ( !empty( $item->_invalid ) ) {
                $classes[] = 'menu-item-invalid';
                /* translators: %s: title of menu item which is invalid */
                $title = sprintf( __( '%s (Invalid)','familabcore' ), $item->title );
            } elseif ( isset( $item->post_status ) && 'draft' == $item->post_status ) {
                $classes[] = 'pending';
                /* translators: %s: title of menu item in draft status */
                $title = sprintf( __( '%s (Pending)' ,'familabcore'), $item->title );
            }
            $title        = ( !isset( $item->label ) || '' == $item->label ) ? $title : $item->label;
            $submenu_text = '';
            if ( 0 == $depth )
                $submenu_text = 'style="display: none;"';
            $settings          = get_post_meta( $item_id, '_familab_menu_settings', true );
            $familab_menu_item_id = isset( $settings['menu_content_id'] ) ? $settings['menu_content_id'] : 0;
            if ( $familab_menu_item_id > 0 ) {
                $url = admin_url( 'post.php?post=' . $familab_menu_item_id . '&action=edit&familab_menu_id=' . $menu_id . '&familab_menu_item_id=' . $item_id . '&depth=' . $depth . '' );
            } else {
                $url = '';
            }
            $_familab_megamenu_enabled = get_term_meta( $menu_id, '_familab_megamenu_enabled', true );
            $_familab_megamenu_enabled = ( isset( $_familab_megamenu_enabled ) && is_numeric( $_familab_megamenu_enabled ) ) ? $_familab_megamenu_enabled : 0;
            $menu_icon_type         = isset( $settings['menu_icon_type'] ) ? $settings['menu_icon_type'] : 'font-icon';
            $icon_image_url         = isset( $settings['icon_image_url'] ) ? $settings['icon_image_url'] : '';
            ?>
        <li id="menu-item-<?php echo $item_id; ?>" class="<?php echo implode( ' ', $classes ); ?>">
            <div class="menu-item-bar">
                <div class="menu-item-handle">
                    <span class="item-title">
                        <span class="menu-item-title">
                            <span class="menu-icon">
                                <?php if ( $menu_icon_type == 'font-icon' ): ?>
                                    <?php if ( isset( $settings['menu_icon'] ) && $settings['menu_icon'] != "" ): ?>
                                        <span class="<?php echo esc_attr( $settings['menu_icon'] ); ?>"></span>
                                    <?php endif; ?>
                                <?php endif; ?>
                                <?php if ( $menu_icon_type == 'image' ): ?>
                                    <?php if ( $icon_image_url != "" ): ?>
                                        <img src="<?php echo esc_url( $icon_image_url ); ?>" alt="">
                                    <?php endif; ?>
                                <?php endif; ?>
                            </span>
                            <?php echo esc_html( $title ); ?>
                        </span>
                        <span class="is-submenu" <?php echo $submenu_text; ?>><?php _e( 'sub item','familabcore' ); ?></span>
                        <span id="familabcore-menu-item-settings-<?php echo esc_attr( $item_id ); ?>"
                              url="<?php echo esc_url( $url ); ?>" data-menu_id="<?php echo esc_attr( $menu_id ); ?>"
                              data-depth="<?php echo esc_attr( $depth ); ?>"
                              data-item_id="<?php echo esc_attr( $item_id ); ?>"
                              class="familabcore-menu-settings "> <?php esc_html_e( 'Settings', 'familabcore' ); ?></span>
                    </span>
                    <span class="item-controls">
						<span class="item-type"><?php echo esc_html( $item->type_label ); ?></span>
						<span class="item-order hide-if-js">
							<a href="<?php
                            echo wp_nonce_url(
                                add_query_arg(
                                    array(
                                        'action'    => 'move-up-menu-item',
                                        'menu-item' => $item_id,
                                    ),
                                    remove_query_arg( $removed_args, admin_url( 'nav-menus.php' ) )
                                ),
                                'move-menu_item'
                            );
                            ?>" class="item-move-up" aria-label="<?php esc_attr_e( 'Move up','familabcore' ) ?>">&#8593;</a>
							|
							<a href="<?php
                            echo wp_nonce_url(
                                add_query_arg(
                                    array(
                                        'action'    => 'move-down-menu-item',
                                        'menu-item' => $item_id,
                                    ),
                                    remove_query_arg( $removed_args, admin_url( 'nav-menus.php' ) )
                                ),
                                'move-menu_item'
                            );
                            ?>" class="item-move-down" aria-label="<?php esc_attr_e( 'Move down','familabcore' ) ?>">&#8595;</a>
						</span>
						<a class="item-edit" id="edit-<?php echo $item_id; ?>" href="<?php
                        echo ( isset( $_GET['edit-menu-item'] ) && $item_id == $_GET['edit-menu-item'] ) ? admin_url( 'nav-menus.php' ) : add_query_arg( 'edit-menu-item', $item_id, remove_query_arg( $removed_args, admin_url( 'nav-menus.php#menu-item-settings-' . $item_id ) ) );
                        ?>" aria-label="<?php esc_attr_e( 'Edit menu item','familabcore' ); ?>"><span
                                class="screen-reader-text"><?php _e( 'Edit' ,'familabcore'); ?></span></a>
					</span>

                </div>
                <?php do_action( 'familab_menu_item_settings', $item_id, $title, $depth ); ?>
            </div>

            <div class="menu-item-settings wp-clearfix" id="menu-item-settings-<?php echo $item_id; ?>">
                <?php if ( 'custom' == $item->type ) : ?>
                    <p class="field-url description description-wide">
                        <label for="edit-menu-item-url-<?php echo $item_id; ?>">
                            <?php _e( 'URL' ,'familabcore'); ?><br/>
                            <input type="text" id="edit-menu-item-url-<?php echo $item_id; ?>"
                                   class="widefat code edit-menu-item-url" name="menu-item-url[<?php echo $item_id; ?>]"
                                   value="<?php echo esc_attr( $item->url ); ?>"/>
                        </label>
                    </p>
                <?php endif; ?>
                <p class="description description-wide">
                    <label for="edit-menu-item-title-<?php echo $item_id; ?>">
                        <?php _e( 'Navigation Label','familabcore' ); ?><br/>
                        <input type="text" id="edit-menu-item-title-<?php echo $item_id; ?>"
                               class="widefat edit-menu-item-title" name="menu-item-title[<?php echo $item_id; ?>]"
                               value="<?php echo esc_attr( $item->title ); ?>"/>
                    </label>
                </p>
                <p class="field-title-attribute field-attr-title description description-wide">
                    <label for="edit-menu-item-attr-title-<?php echo $item_id; ?>">
                        <?php _e( 'Title Attribute','familabcore' ); ?><br/>
                        <input type="text" id="edit-menu-item-attr-title-<?php echo $item_id; ?>"
                               class="widefat edit-menu-item-attr-title"
                               name="menu-item-attr-title[<?php echo $item_id; ?>]"
                               value="<?php echo esc_attr( $item->post_excerpt ); ?>"/>
                    </label>
                </p>
                <p class="field-link-target description">
                    <label for="edit-menu-item-target-<?php echo $item_id; ?>">
                        <input type="checkbox" id="edit-menu-item-target-<?php echo $item_id; ?>" value="_blank"
                               name="menu-item-target[<?php echo $item_id; ?>]"<?php checked( $item->target, '_blank' ); ?> />
                        <?php _e( 'Open link in a new tab','familabcore' ); ?>
                    </label>
                </p>
                <p class="field-css-classes description description-thin">
                    <label for="edit-menu-item-classes-<?php echo $item_id; ?>">
                        <?php _e( 'CSS Classes (optional)','familabcore' ); ?><br/>
                        <input type="text" id="edit-menu-item-classes-<?php echo $item_id; ?>"
                               class="widefat code edit-menu-item-classes"
                               name="menu-item-classes[<?php echo $item_id; ?>]"
                               value="<?php echo esc_attr( implode( ' ', $item->classes ) ); ?>"/>
                    </label>
                </p>
                <p class="field-xfn description description-thin">
                    <label for="edit-menu-item-xfn-<?php echo $item_id; ?>">
                        <?php _e( 'Link Relationship (XFN)','familabcore' ); ?><br/>
                        <input type="text" id="edit-menu-item-xfn-<?php echo $item_id; ?>"
                               class="widefat code edit-menu-item-xfn" name="menu-item-xfn[<?php echo $item_id; ?>]"
                               value="<?php echo esc_attr( $item->xfn ); ?>"/>
                    </label>
                </p>
                <p class="field-description description description-wide">
                    <label for="edit-menu-item-description-<?php echo $item_id; ?>">
                        <?php _e( 'Description','familabcore' ); ?><br/>
                        <textarea id="edit-menu-item-description-<?php echo $item_id; ?>"
                                  class="widefat edit-menu-item-description" rows="3" cols="20"
                                  name="menu-item-description[<?php echo $item_id; ?>]"><?php echo esc_html( $item->description ); // textarea_escaped
                            ?></textarea>
                        <span class="description"><?php _e( 'The description will be displayed in the menu if the current theme supports it.','familabcore' ); ?></span>
                    </label>
                </p>

                <fieldset class="field-move hide-if-no-js description description-wide">
                    <span class="field-move-visual-label" aria-hidden="true"><?php _e( 'Move','familabcore' ); ?></span>
                    <button type="button" class="button-link menus-move menus-move-up"
                            data-dir="up"><?php _e( 'Up one' ,'familabcore'); ?></button>
                    <button type="button" class="button-link menus-move menus-move-down"
                            data-dir="down"><?php _e( 'Down one','familabcore' ); ?></button>
                    <button type="button" class="button-link menus-move menus-move-left" data-dir="left"></button>
                    <button type="button" class="button-link menus-move menus-move-right" data-dir="right"></button>
                    <button type="button" class="button-link menus-move menus-move-top"
                            data-dir="top"><?php _e( 'To the top' ,'familabcore'); ?></button>
                </fieldset>

                <div class="menu-item-actions description-wide submitbox">
                    <?php if ( 'custom' != $item->type && $original_title !== false ) : ?>
                        <p class="link-to-original">
                            <?php printf( __( 'Original: %s','familabcore' ), '<a href="' . esc_attr( $item->url ) . '">' . esc_html( $original_title ) . '</a>' ); ?>
                        </p>
                    <?php endif; ?>
                    <a class="item-delete submitdelete deletion" id="delete-<?php echo $item_id; ?>" href="<?php
                    echo wp_nonce_url(
                        add_query_arg(
                            array(
                                'action'    => 'delete-menu-item',
                                'menu-item' => $item_id,
                            ),
                            admin_url( 'nav-menus.php' )
                        ),
                        'delete-menu_item_' . $item_id
                    ); ?>"><?php _e( 'Remove' ,'familabcore'); ?></a> <span class="meta-sep hide-if-no-js"> | </span> <a
                        class="item-cancel submitcancel hide-if-no-js" id="cancel-<?php echo $item_id; ?>"
                        href="<?php echo esc_url( add_query_arg( array( 'edit-menu-item' => $item_id, 'cancel' => time() ), admin_url( 'nav-menus.php' ) ) );
                        ?>#menu-item-settings-<?php echo $item_id; ?>"><?php _e( 'Cancel' ,'familabcore'); ?></a>
                </div>

                <input class="menu-item-data-db-id" type="hidden" name="menu-item-db-id[<?php echo $item_id; ?>]"
                       value="<?php echo $item_id; ?>"/>
                <input class="menu-item-data-object-id" type="hidden"
                       name="menu-item-object-id[<?php echo $item_id; ?>]"
                       value="<?php echo esc_attr( $item->object_id ); ?>"/>
                <input class="menu-item-data-object" type="hidden" name="menu-item-object[<?php echo $item_id; ?>]"
                       value="<?php echo esc_attr( $item->object ); ?>"/>
                <input class="menu-item-data-parent-id" type="hidden"
                       name="menu-item-parent-id[<?php echo $item_id; ?>]"
                       value="<?php echo esc_attr( $item->menu_item_parent ); ?>"/>
                <input class="menu-item-data-position" type="hidden" name="menu-item-position[<?php echo $item_id; ?>]"
                       value="<?php echo esc_attr( $item->menu_order ); ?>"/>
                <input class="menu-item-data-type" type="hidden" name="menu-item-type[<?php echo $item_id; ?>]"
                       value="<?php echo esc_attr( $item->type ); ?>"/>
            </div><!-- .menu-item-settings-->
            <ul class="menu-item-transport"></ul>
            <?php
            $output .= ob_get_clean();
        }

        /**
         * Get the current menu ID.
         *
         * Most of this taken from wp-admin/nav-menus.php (no built in functions to do this)
         *
         * @since 1.0
         * @return int
         */
        public function get_selected_menu_id()
        {
            $nav_menus            = wp_get_nav_menus( array( 'orderby' => 'name' ) );
            $menu_count           = count( $nav_menus );
            $nav_menu_selected_id = isset( $_REQUEST['menu'] ) ? (int)$_REQUEST['menu'] : 0;
            $add_new_screen       = ( isset( $_GET['menu'] ) && 0 == $_GET['menu'] ) ? true : false;
            // If we have one theme location, and zero menus, we take them right into editing their first menu
            $page_count                  = wp_count_posts( 'page' );
            $one_theme_location_no_menus = ( 1 == count( get_registered_nav_menus() ) && !$add_new_screen && empty( $nav_menus ) && !empty( $page_count->publish ) ) ? true : false;
            // Get recently edited nav menu
            $recently_edited = absint( get_user_option( 'nav_menu_recently_edited' ) );
            if ( empty( $recently_edited ) && is_nav_menu( $nav_menu_selected_id ) )
                $recently_edited = $nav_menu_selected_id;
            // Use $recently_edited if none are selected
            if ( empty( $nav_menu_selected_id ) && !isset( $_GET['menu'] ) && is_nav_menu( $recently_edited ) )
                $nav_menu_selected_id = $recently_edited;
            // On deletion of menu, if another menu exists, show it
            if ( !$add_new_screen && 0 < $menu_count && isset( $_GET['action'] ) && 'delete' == $_GET['action'] )
                $nav_menu_selected_id = $nav_menus[0]->term_id;
            // Set $nav_menu_selected_id to 0 if no menus
            if ( $one_theme_location_no_menus ) {
                $nav_menu_selected_id = 0;
            } elseif ( empty( $nav_menu_selected_id ) && !empty( $nav_menus ) && !$add_new_screen ) {
                // if we have no selection yet, and we have menus, set to the first one in the list
                $nav_menu_selected_id = $nav_menus[0]->term_id;
            }

            return $nav_menu_selected_id;
        }
    }
}
