<?php
require_once( ABSPATH . 'wp-admin/includes/import.php' );
if ( ! class_exists( 'WP_Importer' ) ) {
    defined( 'WP_LOAD_IMPORTERS' ) || define( 'WP_LOAD_IMPORTERS', true );
    require_once ABSPATH . '/wp-admin/includes/class-wp-importer.php';
}
class Familab_WXR_Importer extends WP_Importer {
    /**
     * Maximum supported WXR version
     */
    const MAX_WXR_VERSION = 1.3;

    /**
     * Regular expression for checking if a post references an attachment
     *
     * Note: This is a quick, weak check just to exclude text-only posts. More
     * vigorous checking is done later to verify.
     */
    const REGEX_HAS_ATTACHMENT_REFS = '!
		(
			# Match anything with an image or attachment class
			class=[\'"].*?\b(wp-image-\d+|attachment-[\w\-]+)\b
		|
			# Match anything that looks like an upload URL
			src=[\'"][^\'"]*(
				[0-9]{4}/[0-9]{2}/[^\'"]+\.(jpg|jpeg|png|gif)
			|
				content/uploads[^\'"]+
			)[\'"]
		)!ix';

    /**
     * The WXR namespaceURI
     *
     * Note that this is version agnositic
     */
    const WXR_NAMESPACE_URI = 'http://wordpress.org/export/';

    /**
     * The Dublin Core namespaceURI
     */
    const DUBLIN_CORE_NAMESPACE_URI = 'http://purl.org/dc/elements/1.1/';

    /**
     * The RSS content module namspaceURI
     */
    const RSS_CONTENT_NAMESPACE_URI = 'http://purl.org/rss/1.0/modules/content/';

    /**
     * Version of WXR we're importing.
     * @var string
     */
    protected $version;
    protected $fetch_attachments = false;

    // information to import from WXR file
    protected $categories = array();
    protected $tags = array();
    protected $base_url = '';
    protected $posts = array();//
    protected $terms = array();//

    // mappings from old information to new
    protected $processed_terms = array();
    protected $processed_posts = array();
    protected $processed_menu_items = array();
    protected $menu_item_orphans = array();
    protected $missing_menu_items = array();
    protected $post_orphans = array();

    // NEW STYLE
    protected $mapping = array();
    protected $requires_remapping = array();
    protected $exists = array();
    protected $user_slug_override = array();

    protected $url_remap = array();
    protected $featured_images = array();

    /**
     * Logger instance.
     *
     * @var WP_Importer_Logger
     */
    protected $logger;

    /**
     * Constructor
     *
     * @param array $options {
     *     @var bool $prefill_existing_posts Should we prefill `post_exists` calls? (True prefills and uses more memory, false checks once per imported post and takes longer. Default is true.)
     *     @var bool $prefill_existing_comments Should we prefill `comment_exists` calls? (True prefills and uses more memory, false checks once per imported comment and takes longer. Default is true.)
     *     @var bool $prefill_existing_terms Should we prefill `term_exists` calls? (True prefills and uses more memory, false checks once per imported term and takes longer. Default is true.)
     *     @var bool $update_attachment_guids Should attachment GUIDs be updated to the new URL? (True updates the GUID, which keeps compatibility with v1, false doesn't update, and allows deduplication and reimporting. Default is false.)
     *     @var bool $fetch_attachments Fetch attachments from the remote server. (True fetches and creates attachment posts, false skips attachments. Default is false.)
     *     @var bool $aggressive_url_search Should we search/replace for URLs aggressively? (True searches all posts' content for old URLs and replaces, false checks for `<img class="wp-image-*">` only. Default is false.)
     *     @var int $default_author User ID to use if author is missing or invalid. (Default is null, which leaves posts unassigned.)
     * }
     */
    public function __construct( $options = array() ) {
        // Initialize some important variables
        $empty_types = array(
            'post'    => array(),
            'comment' => array(),
            'term'    => array(),
            'user'    => array(),
        );

        $this->mapping = $empty_types;
        $this->mapping['user_slug'] = array();
        $this->mapping['term_id'] = array();
        $this->requires_remapping = $empty_types;
        $this->exists = $empty_types;
    }

    public function set_logger( $logger ) {
        $this->logger = $logger;
    }

    /**
     * Get a stream reader for the file.
     *
     * @param string $file Path to the XML file.
     * @return XMLReader|WP_Error Reader instance on success, error otherwise.
     */
    protected function get_reader( $file ) {
        // Avoid loading external entities for security
        $old_value = null;
        if ( function_exists( 'libxml_disable_entity_loader' ) ) {
            // $old_value = libxml_disable_entity_loader( true );
        }

        $reader = new XMLReader();
        $status = $reader->open( $file );

        if ( ! is_null( $old_value ) ) {
            // libxml_disable_entity_loader( $old_value );
        }

        if ( ! $status ) {
            return new WP_Error( 'wxr_importer.cannot_parse', __( 'Could not open the file for parsing', 'familabcore' ) );
        }

        return $reader;
    }
    /**
     * The main controller for the actual import stage.
     *
     * @param string $file Path to the WXR file for importing
     */
    public function get_preliminary_information( $file ) {
        require dirname( __FILE__ ) . '/parsers.php';
        $data = new Familab_WXR_Import_Info();
        $parser = new Familab_WXR_Parser();
        $data_xml = $parser->parse( $file );
        foreach ($data_xml['posts'] as $post){
            if ($post['post_type'] == 'attachment'){
                $data->media_count++;
            }else{
                $data->post_count++;
            }
            if (isset($post['comments']))
                $data->comment_count += count($post['comments']);
        }
        if (isset($data_xml['authors']))
            $data->users = $data_xml['authors'];
        if (isset($data_xml['terms']))
            $data->term_count = count($data_xml['terms']);
        if (isset($data_xml['tags']))
            $data->tag_count = count($data_xml['tags']);
        if (isset($data_xml['categories']))
            $data->category_count = count($data_xml['categories']);
        if (isset($data_xml['base_url']))
            $data->siteurl = $data_xml['base_url'];
        // Let's run the actual importer now, woot
        if (isset($data_xml['version']))
            $data->version = $data_xml['version'];
        $data->package_count  = 1;
        $data->widget_count = $this->get_total_widgets(dirname($file));
        $data->setting_count = 7;
        return array('data_info'=>$data,'data_xml'=>$data_xml);
    }

    /**
     * The main controller for the actual import stage.
     *
     * @param string $file Path to the WXR file for importing
     */
    public function import( $file ) {
        add_filter( 'import_post_meta_key', array( $this, 'is_valid_meta_key' ) );
        add_filter( 'http_request_timeout', array( &$this, 'bump_request_timeout' ) );
        $this->import_start( $file );
        $this->wc_pre_import();

        wp_suspend_cache_invalidation( true );
        $this->process_categories();
        $this->process_tags();
        $this->process_terms();
        $this->process_posts();
        wp_suspend_cache_invalidation( false );

        // update incorrect/missing information in the DB
        $this->backfill_parents();
        $this->backfill_attachment_urls();
        $this->remap_featured_images();
        $this->import_end();
    }

    /**
     * When running the WP XML importer, ensure attributes exist.
     *
     * WordPress import should work - however, it fails to import custom product attribute taxonomies.
     * This code grabs the file before it is imported and ensures the taxonomies are created.
     */

    public function wc_pre_import(){
        if (class_exists( 'WooCommerce' ) ) {
            $terms = apply_filters( 'wp_import_terms', $this->terms );
            if ( empty( $this->terms ) )
                return;
            foreach ( $terms as $term ) {
                if ( strstr( $term['term_taxonomy'], 'pa_' ) ) {
                    if ( ! taxonomy_exists( $term['term_taxonomy'] ) ) {
                        $attribute_name = wc_attribute_taxonomy_slug( $term['term_taxonomy'] );
                        // Create the taxonomy.
                        if ( ! in_array( $attribute_name, wc_get_attribute_taxonomies(), true ) ) {
                            wc_create_attribute(
                                array(
                                    'name'         => $attribute_name,
                                    'slug'         => $attribute_name,
                                    'type'         => 'select',
                                    'order_by'     => 'menu_order',
                                    'has_archives' => false,
                                )
                            );
                        }
                        // Register the taxonomy now so that the import works!
                        register_taxonomy(
                            $term['term_taxonomy'],
                            apply_filters( 'woocommerce_taxonomy_objects_' . $term['term_taxonomy'], array( 'product' ) ),
                            apply_filters(
                                'woocommerce_taxonomy_args_' . $term['term_taxonomy'],
                                array(
                                    'hierarchical' => true,
                                    'show_ui'      => false,
                                    'query_var'    => true,
                                    'rewrite'      => false,
                                )
                            )
                        );
                    }
                }
            }
        }
    }

    /**
     * Create new categories based on import information
     *
     * Doesn't create a new category if its slug already exists
     */
    function process_categories() {
        $this->categories = apply_filters( 'wp_import_categories', $this->categories );
        if ( empty( $this->categories ) )
            return;

        foreach ( $this->categories as $cat ) {
            // if the category already exists leave it alone
            $term_id = term_exists( $cat['category_nicename'], 'category' );
            if ( $term_id ) {
                if ( is_array($term_id) ) $term_id = $term_id['term_id'];
                if ( isset($cat['term_id']) )
                    $this->processed_terms[intval($cat['term_id'])] = (int) $term_id;
                do_action('wxr_importer.process_already_imported.category');
                continue;
            }

            $category_parent = empty( $cat['category_parent'] ) ? 0 : category_exists( $cat['category_parent'] );
            $category_description = isset( $cat['category_description'] ) ? $cat['category_description'] : '';
            $catarr = array(
                'category_nicename' => $cat['category_nicename'],
                'category_parent' => $category_parent,
                'cat_name' => $cat['cat_name'],
                'category_description' => $category_description
            );
            $catarr = wp_slash( $catarr );

            $id = wp_insert_category( $catarr );
            if ( ! is_wp_error( $id ) ) {
                if ( isset($cat['term_id']) )
                    $this->processed_terms[intval($cat['term_id'])] = $id;
                do_action('wxr_importer.processed.category');
            } else {
                $this->logger->error(sprintf( __( 'Failed to import category %s', 'familabcore' ), esc_html($cat['category_nicename']) ));
                $this->logger->error($id->get_error_message());
                do_action('wxr_importer.process_failed.category');
                continue;
            }

            $this->process_termmeta( $cat, $id['term_id'] );
        }

        unset( $this->categories );
    }

    /**
     * Create new post tags based on import information
     *
     * Doesn't create a tag if its slug already exists
     */
    public function process_tags() {
        $this->tags = apply_filters( 'wp_import_tags', $this->tags );
        if ( empty( $this->tags ) )
            return;
        foreach ( $this->tags as $tag ) {
            // if the tag already exists leave it alone
            $term_id = term_exists( $tag['tag_slug'], 'post_tag' );
            if ( $term_id ) {
                if ( is_array($term_id) ) $term_id = $term_id['term_id'];
                if ( isset($tag['term_id']) )
                    $this->processed_terms[intval($tag['term_id'])] = (int) $term_id;
                continue;
            }
            $tag = wp_slash( $tag );
            $tag_desc = isset( $tag['tag_description'] ) ? $tag['tag_description'] : '';
            $tagarr = array( 'slug' => $tag['tag_slug'], 'description' => $tag_desc );
            $id = wp_insert_term( $tag['tag_name'], 'post_tag', $tagarr );
            if ( ! is_wp_error( $id ) ) {
                if ( isset($tag['term_id']) )
                    $this->processed_terms[intval($tag['term_id'])] = $id['term_id'];
            } else {
                error(sprintf( __( 'Failed to import post tag %s', 'familabcore' ), esc_html($tag['tag_name']) ));
                $this->logger->error($id->get_error_message());
                continue;
            }
            $this->process_termmeta( $tag, $id['term_id'] );
        }
        unset( $this->tags );
    }
    /**
     * Create new terms based on import information
     *
     * Doesn't create a term its slug already exists
     */
    public function process_terms() {
        $this->terms = apply_filters( 'wp_import_terms', $this->terms );
        if ( empty( $this->terms ) ){
            $this->logger->error('empty terms');
            return;
        }
        foreach ( $this->terms as $term ) {
            // if the term already exists in the correct taxonomy leave it alone
            $term_id = term_exists( $term['slug'], $term['term_taxonomy'] );
            if ( $term_id ) {
                if ( is_array($term_id) ) $term_id = $term_id['term_id'];
                if ( isset($term['term_id']) )
                    $this->processed_terms[intval($term['term_id'])] = (int) $term_id;
                $this->logger->notice( sprintf(
                    __( 'Term "%s" (%s) already exists', 'familabcore' ),
                    $term['term_name'],
                    $term['term_taxonomy']
                ) );
                do_action( 'wxr_importer.process_already_imported.term', $term );
                continue;
            }
            if ( empty( $term['term_parent'] ) ) {
                $parent = 0;
            } else {
                $parent = term_exists( $term['term_parent'], $term['term_taxonomy'] );
                if ( is_array( $parent ) ) $parent = $parent['term_id'];
            }
            $term = wp_slash( $term );
            $description = isset( $term['term_description'] ) ? $term['term_description'] : '';
            $termarr = array( 'slug' => $term['slug'], 'description' => $description, 'parent' => intval($parent) );
            $id = wp_insert_term( $term['term_name'], $term['term_taxonomy'], $termarr );
            if ( ! is_wp_error( $id ) ) {
                if ( isset($term['term_id']) )
                    $this->processed_terms[intval($term['term_id'])] = $id['term_id'];
                do_action( 'wxr_importer.processed.term' );
            } else {
                $this->logger->error( sprintf( __( 'Failed to import %s %s', 'familabcore' ), esc_html($term['term_taxonomy']), esc_html($term['term_name']) ));
                $this->logger->error( $id->get_error_message() );
                do_action( 'wxr_importer.process_failed.term');
                continue;
            }
            $this->process_termmeta( $term, $id['term_id'] );
        }
        unset( $this->terms );
    }

    /**
     * Add metadata to imported term.
     *
     * @since 0.6.2
     *
     * @param array $term    Term data from WXR import.
     * @param int   $term_id ID of the newly created term.
     */
    protected function process_termmeta( $term, $term_id ) {
        if ( ! isset( $term['termmeta'] ) ) {
            $term['termmeta'] = array();
        }

        /**
         * Filters the metadata attached to an imported term.
         *
         * @since 0.6.2
         *
         * @param array $termmeta Array of term meta.
         * @param int   $term_id  ID of the newly created term.
         * @param array $term     Term data from the WXR import.
         */
        $term['termmeta'] = apply_filters( 'wp_import_term_meta', $term['termmeta'], $term_id, $term );

        if ( empty( $term['termmeta'] ) ) {
            return;
        }

        foreach ( $term['termmeta'] as $meta ) {
            /**
             * Filters the meta key for an imported piece of term meta.
             *
             * @since 0.6.2
             *
             * @param string $meta_key Meta key.
             * @param int    $term_id  ID of the newly created term.
             * @param array  $term     Term data from the WXR import.
             */
            $key = apply_filters( 'import_term_meta_key', $meta['key'], $term_id, $term );
            if ( ! $key ) {
                continue;
            }

            // Export gets meta straight from the DB so could have a serialized string
            $value = maybe_unserialize( $meta['value'] );

            add_term_meta( $term_id, $key, $value );

            /**
             * Fires after term meta is imported.
             *
             * @since 0.6.2
             *
             * @param int    $term_id ID of the newly created term.
             * @param string $key     Meta key.
             * @param mixed  $value   Meta value.
             */
            do_action( 'import_term_meta', $term_id, $key, $value );
        }
    }

    /**
     * Create new posts based on import information
     *
     * Posts marked as having a parent which doesn't exist will become top level items.
     * Doesn't create a new post if: the post type doesn't exist, the given post ID
     * is already noted as imported or a post with the same title and date already exists.
     * Note that new/updated terms, comments and meta are imported for the last of the above.
     */
    public function process_posts() {
        $this->posts = apply_filters( 'wp_import_posts', $this->posts );

        foreach ( $this->posts as $post ) {
            $post = apply_filters( 'wp_import_post_data_raw', $post );

            if ( ! post_type_exists( $post['post_type'] ) ) {
                $this->logger->warning( sprintf(
                    __( 'Failed to import "%s": Invalid post type %s', 'familabcore' ),
                    $post['post_title'],
                    $post['post_type']
                ) );
                do_action( 'wp_import_post_exists', $post );
                do_action( 'wxr_importer.process_invalid.post', $post );
                continue;
            }

            if ( isset( $this->processed_posts[$post['post_id']] ) && ! empty( $post['post_id'] ) ){
                do_action('wxr_importer.process_already_imported.post',$post);
                continue;
            }

            if ( $post['status'] == 'auto-draft' ){
                do_action('wxr_importer.process_skipped.post',$post);
                continue;
            }

            if ( 'nav_menu_item' == $post['post_type'] ) {
                $this->process_menu_item2( $post );
                continue;
            }

            $post_type_object = get_post_type_object( $post['post_type'] );

            $post_exists = post_exists( $post['post_title'], '', $post['post_date'] );

            /**
             * Filter ID of the existing post corresponding to post currently importing.
             *
             * Return 0 to force the post to be imported. Filter the ID to be something else
             * to override which existing post is mapped to the imported post.
             *
             * @see post_exists()
             * @since 0.6.2
             *
             * @param int   $post_exists  Post ID, or 0 if post did not exist.
             * @param array $post         The post array to be inserted.
             */
            $post_exists = apply_filters( 'wp_import_existing_post', $post_exists, $post );

            if ( $post_exists && get_post_type( $post_exists ) == $post['post_type'] ) {
                $this->logger->warning( sprintf(
                    __( '%s "%s" already exists.', 'familabcore' ),
                    $post_type_object->labels->singular_name,
                    $post['post_title']
                ) );
                $comment_post_ID = $post_id = $post_exists;
                $this->processed_posts[ intval( $post['post_id'] ) ] = intval( $post_exists );
            }
            else {
                $post_parent = (int) $post['post_parent'];
                if ( $post_parent ) {
                    // if we already know the parent, map it to the new local ID
                    if ( isset( $this->processed_posts[$post_parent] ) ) {
                        $post_parent = $this->processed_posts[$post_parent];
                        // otherwise record the parent for later
                    } else {
                        $this->post_orphans[intval($post['post_id'])] = $post_parent;
                        $post_parent = 0;
                    }
                }

                // map the post author
                $author = sanitize_user( $post['post_author'], true );
                if ( isset( $this->author_mapping[$author] ) )
                    $author = $this->author_mapping[$author];
                else
                    $author = (int) get_current_user_id();
                $postdata = array(
                    'import_id' => $post['post_id'], 'post_author' => $author, 'post_date' => $post['post_date'],
                    'post_date_gmt' => $post['post_date_gmt'], 'post_content' => $post['post_content'],
                    'post_excerpt' => $post['post_excerpt'], 'post_title' => $post['post_title'],
                    'post_status' => $post['status'], 'post_name' => $post['post_name'],
                    'comment_status' => $post['comment_status'], 'ping_status' => $post['ping_status'],
                    'guid' => $post['guid'], 'post_parent' => $post_parent, 'menu_order' => $post['menu_order'],
                    'post_type' => $post['post_type'], 'post_password' => $post['post_password']
                );

                $original_post_ID = $post['post_id'];
                $postdata = apply_filters( 'wp_import_post_data_processed', $postdata, $post );

                $postdata = wp_slash( $postdata );

                if ( 'attachment' == $postdata['post_type'] ) {

                    $remote_url = ! empty($post['attachment_url']) ? $post['attachment_url'] : $post['guid'];
                    // try to use _wp_attached file for upload folder placement to ensure the same location as the export site
                    // e.g. location is 2003/05/image.jpg but the attachment post_date is 2010/09, see media_handle_upload()
                    $postdata['upload_date'] = $post['post_date'];
                    if ( isset( $post['postmeta'] ) ) {
                        foreach( $post['postmeta'] as $meta ) {
                            if ( $meta['key'] == '_wp_attached_file' ) {
                                $postdata['_wp_attached_file'] = $meta['value'];
                                if ( preg_match( '%^[0-9]{4}/[0-9]{2}%', $meta['value'], $matches ) )
                                    $postdata['upload_date'] = $matches[0];
                                break;
                            }
                        }
                    }
                    $comment_post_ID = $post_id = $this->process_attachment( $postdata, $remote_url );

                } else {
                    $comment_post_ID = $post_id = wp_insert_post( $postdata, true );
                    do_action( 'wp_import_insert_post', $post_id, $original_post_ID, $postdata, $post );
                }

                if ( is_wp_error( $post_id ) ) {
                    $this->logger->error( sprintf(
                        __( 'Failed to import "%s" (%s)', 'familabcore' ),
                        $post['post_title'],
                        $post_type_object->labels->singular_name
                    ) );
                    $this->logger->error( $post_id->get_error_message() );
                    do_action( 'wxr_importer.process_failed.post', $post_id, $post);
                    continue;
                }

                if ( $post['is_sticky'] == 1 )
                    stick_post( $post_id );
            }

            // map pre-import ID to local ID
            $this->processed_posts[intval($post['post_id'])] = (int) $post_id;

            if ( ! isset( $post['terms'] ) )
                $post['terms'] = array();

            $post['terms'] = apply_filters( 'wp_import_post_terms', $post['terms'], $post_id, $post );

            // add categories, tags and other terms
            if ( ! empty( $post['terms'] ) ) {
                $terms_to_set = array();
                foreach ( $post['terms'] as $term ) {
                    // back compat with WXR 1.0 map 'tag' to 'post_tag'
                    $taxonomy = ( 'tag' == $term['domain'] ) ? 'post_tag' : $term['domain'];
                    $term_exists = term_exists( $term['slug'], $taxonomy );
                    $term_id = is_array( $term_exists ) ? $term_exists['term_id'] : $term_exists;
                    if ( ! $term_id ) {
                        $t = wp_insert_term( $term['name'], $taxonomy, array( 'slug' => $term['slug'] ) );
                        if ( ! is_wp_error( $t ) ) {
                            $term_id = $t['term_id'];
                            do_action( 'wp_import_insert_term', $t, $term, $post_id, $post );
                        } else {
                            $this->logger->error( sprintf( __( 'Failed to import %s %s', 'familabcore' ), esc_html($taxonomy), esc_html($term['name']) ));;
                            $this->logger->error($t->get_error_message());
                            do_action( 'wp_import_insert_term_failed', $t, $term, $post_id, $post );
                            do_action( 'wxr_importer.process_failed.term');
                            continue;
                        }
                    }
                    $terms_to_set[$taxonomy][] = intval( $term_id );
                }

                foreach ( $terms_to_set as $tax => $ids ) {
                    $tt_ids = wp_set_post_terms( $post_id, $ids, $tax );
                    do_action( 'wp_import_set_post_terms', $tt_ids, $ids, $tax, $post_id, $post );
                }
                unset( $post['terms'], $terms_to_set );
            }

            if ( ! isset( $post['comments'] ))
                $post['comments'] = array();

            $post['comments'] = apply_filters( 'wp_import_post_comments', $post['comments'], $post_id, $post );

            // add/update comments
            if ( ! empty( $post['comments'] ) ) {
                $num_comments = 0;
                $inserted_comments = array();
                foreach ( $post['comments'] as $comment ) {
                    $comment_id	= $comment['comment_id'];
                    $newcomments[$comment_id]['comment_post_ID']      = $comment_post_ID;
                    $newcomments[$comment_id]['comment_author']       = $comment['comment_author'];
                    $newcomments[$comment_id]['comment_author_email'] = $comment['comment_author_email'];
                    $newcomments[$comment_id]['comment_author_IP']    = $comment['comment_author_IP'];
                    $newcomments[$comment_id]['comment_author_url']   = $comment['comment_author_url'];
                    $newcomments[$comment_id]['comment_date']         = $comment['comment_date'];
                    $newcomments[$comment_id]['comment_date_gmt']     = $comment['comment_date_gmt'];
                    $newcomments[$comment_id]['comment_content']      = $comment['comment_content'];
                    $newcomments[$comment_id]['comment_approved']     = $comment['comment_approved'];
                    $newcomments[$comment_id]['comment_type']         = $comment['comment_type'];
                    $newcomments[$comment_id]['comment_parent'] 	  = $comment['comment_parent'];
                    $newcomments[$comment_id]['commentmeta']          = isset( $comment['commentmeta'] ) ? $comment['commentmeta'] : array();
                    if ( isset( $this->mapping['user'][$comment['comment_user_id']] ) )
                        $newcomments[$comment_id]['user_id'] = $this->mapping['user'][$comment['comment_user_id']];
                }
                ksort( $newcomments );
                foreach ( $newcomments as $key => $comment ) {
                    // if this is a new post we can skip the comment_exists() check
                    if ( ! $post_exists || ! comment_exists( $comment['comment_author'], $comment['comment_date'] ) ) {
                        if ( isset( $inserted_comments[$comment['comment_parent']] ) )
                            $comment['comment_parent'] = $inserted_comments[$comment['comment_parent']];
                        $comment = wp_slash( $comment );
                        $comment = wp_filter_comment( $comment );
                        $inserted_comments[$key] = wp_insert_comment( $comment );
                        do_action( 'wp_import_insert_comment', $inserted_comments[$key], $comment, $comment_post_ID, $post );

                        foreach( $comment['commentmeta'] as $meta ) {
                            $value = maybe_unserialize( $meta['value'] );
                            add_comment_meta( $inserted_comments[$key], $meta['key'], $value );
                        }
                        do_action( 'wxr_importer.processed.comment');
                        $num_comments++;
                    }else{
                        do_action('wxr_importer.process_already_imported.comment');
                    }
                }
                unset( $newcomments, $inserted_comments, $post['comments'] );
            }

            if ( ! isset( $post['postmeta'] ) )
                $post['postmeta'] = array();

            $post['postmeta'] = apply_filters( 'wp_import_post_meta', $post['postmeta'], $post_id, $post );

            // add/update post meta
            if ( ! empty( $post['postmeta'] ) ) {
                foreach ( $post['postmeta'] as $meta ) {
                    $key = apply_filters( 'import_post_meta_key', $meta['key'], $post_id, $post );
                    $value = false;

                    if ( '_edit_last' == $key ) {
                        if ( isset( $this->processed_authors[intval($meta['value'])] ) )
                            $value = $this->processed_authors[intval($meta['value'])];
                        else
                            $key = false;
                    }

                    if ( $key ) {
                        // export gets meta straight from the DB so could have a serialized string
                        if ( ! $value )
                            $value = maybe_unserialize( $meta['value'] );

                        add_post_meta( $post_id, $key, $value );
                        do_action( 'import_post_meta', $post_id, $key, $value );

                        // if the post has a featured image, take note of this in case of remap
                        if ( '_thumbnail_id' == $key )
                            $this->featured_images[$post_id] = (int) $value;
                    }
                }
            }
            /*if ( 'nav_menu_item' == $post['post_type'] ) {
                $this->process_menu_item( $post );
            }*/
            do_action( 'wxr_importer.processed.post', $post_id, $post );
        }

        unset( $this->posts );
    }



    /**
     * Attempt to create a new menu item from import data
     *
     * Fails for draft, orphaned menu items and those without an associated nav_menu
     * or an invalid nav_menu term. If the post type or term object which the menu item
     * represents doesn't exist then the menu item will not be imported (waits until the
     * end of the import to retry again before discarding).
     *
     * @param array $item Menu item details from WXR file
     */
    public function process_menu_item( $item ) {
        // skip draft, orphaned menu items
        if ( 'draft' == $item['status'] )
            return;

        foreach ( $item['postmeta'] as $meta )
            ${$meta['key']} = $meta['value'];

        if ( 'taxonomy' == $_menu_item_type && isset( $this->processed_terms[intval($_menu_item_object_id)] ) ) {
            $_menu_item_object_id = $this->processed_terms[intval($_menu_item_object_id)];
        } else if ( 'post_type' == $_menu_item_type && isset( $this->processed_posts[intval($_menu_item_object_id)] ) ) {
            $_menu_item_object_id = $this->processed_posts[intval($_menu_item_object_id)];
        } else if ( 'custom' != $_menu_item_type ) {
            // associated object is missing or not imported yet, we'll retry later
            $_menu_item_object_id = $item['post_id'];
        }else{
            $this->missing_menu_items[] = $item;
            return;
        }
        if ( ! empty( $_menu_item_object_id ) ) {
            update_post_meta( $item['post_id'], '_menu_item_object_id', wp_slash( $_menu_item_object_id ) );
        } else {
            $this->logger->warning( sprintf(
                __( 'Could not find the menu object for "%s" (post #%d)', 'familabcore' ),
                $item['post_title'],
                $item['post_id']
            ) );
            $this->logger->debug( sprintf(
                __( 'Post %d was imported with object "%d" of type "%s", but could not be found', 'familabcore' ),
                $item['post_id'],
                $_menu_item_object_id,
                $_menu_item_type
            ) );
        }
    }

    function process_menu_item2( $item ) {
        // skip draft, orphaned menu items
        if ( 'draft' == $item['status'] ){
            do_action('wxr_importer.process_skipped.post',$item);
            return;
        }


        $menu_slug = false;
        if ( isset($item['terms']) ) {
            // loop through terms, assume first nav_menu term is correct menu
            foreach ( $item['terms'] as $term ) {
                if ( 'nav_menu' == $term['domain'] ) {
                    $menu_slug = $term['slug'];
                    break;
                }
            }
        }

        // no nav_menu term associated with this menu item
        if ( ! $menu_slug ) {
            $this->logger->debug( __('Menu item skipped due to missing menu slug','familabcore'));
            do_action('wxr_importer.process_skipped.post',$item);
            return;
        }

        $menu_id = term_exists( $menu_slug, 'nav_menu' );
        if ( ! $menu_id ) {
            $this->logger->debug( printf( __( 'Menu item skipped due to invalid menu slug: %s', 'familabcore' ), esc_html( $menu_slug ) ));
            do_action('wxr_importer.process_skipped.post',$item);
            return;
        } else {
            $menu_id = is_array( $menu_id ) ? $menu_id['term_id'] : $menu_id;
        }

        foreach ( $item['postmeta'] as $meta )
            ${$meta['key']} = $meta['value'];

        if ( 'taxonomy' == $_menu_item_type && isset( $this->processed_terms[intval($_menu_item_object_id)] ) ) {
            $_menu_item_object_id = $this->processed_terms[intval($_menu_item_object_id)];
        } else if ( 'post_type' == $_menu_item_type && isset( $this->processed_posts[intval($_menu_item_object_id)] ) ) {
            $_menu_item_object_id = $this->processed_posts[intval($_menu_item_object_id)];
        } else if ( 'custom' != $_menu_item_type ) {
            // associated object is missing or not imported yet, we'll retry later
            $this->missing_menu_items[] = $item;
            do_action('wxr_importer.process_skipped.post',$item);
            return;
        }

        if ( isset( $this->processed_menu_items[intval($_menu_item_menu_item_parent)] ) ) {
            $_menu_item_menu_item_parent = $this->processed_menu_items[intval($_menu_item_menu_item_parent)];
        } else if ( $_menu_item_menu_item_parent ) {
            $this->menu_item_orphans[intval($item['post_id'])] = (int) $_menu_item_menu_item_parent;
            $_menu_item_menu_item_parent = 0;
        }

        // wp_update_nav_menu_item expects CSS classes as a space separated string
        $_menu_item_classes = maybe_unserialize( $_menu_item_classes );
        if ( is_array( $_menu_item_classes ) )
            $_menu_item_classes = implode( ' ', $_menu_item_classes );

        $args = array(
            'menu-item-object-id' => $_menu_item_object_id,
            'menu-item-object' => $_menu_item_object,
            'menu-item-parent-id' => $_menu_item_menu_item_parent,
            'menu-item-position' => intval( $item['menu_order'] ),
            'menu-item-type' => $_menu_item_type,
            'menu-item-title' => $item['post_title'],
            'menu-item-url' => $_menu_item_url,
            'menu-item-description' => $item['post_content'],
            'menu-item-attr-title' => $item['post_excerpt'],
            'menu-item-target' => $_menu_item_target,
            'menu-item-classes' => $_menu_item_classes,
            'menu-item-xfn' => $_menu_item_xfn,
            'menu-item-status' => $item['status']
        );

        $id = wp_update_nav_menu_item( $menu_id, 0, $args );
        if ( $id && ! is_wp_error( $id ) ){
            $this->processed_menu_items[intval($item['post_id'])] = (int) $id;
            do_action( 'wxr_importer.processed.post', $item['post_id'], $item );
        }else{
            do_action( 'wxr_importer.process_failed.post', $item['post_id'], $item);
        }

    }

    protected function backfill_process_menu_item( $item ) {
        foreach ( $item['postmeta'] as $meta )
            ${$meta['key']} = $meta['value'];
        if ( 'taxonomy' == $_menu_item_type && isset( $this->processed_terms[intval($_menu_item_object_id)] ) ) {
            $_menu_item_object_id = $this->processed_terms[intval($_menu_item_object_id)];
        } else if ( 'post_type' == $_menu_item_type && isset( $this->processed_posts[intval($_menu_item_object_id)] ) ) {
            $_menu_item_object_id = $this->processed_posts[intval($_menu_item_object_id)];
        }

        if ( ! empty( $_menu_item_object_id ) ) {
            update_post_meta( $item['post_id'], '_menu_item_object_id', wp_slash( $_menu_item_object_id ) );
        } else {
            $this->logger->warning( sprintf(
                __( 'Could not find the menu object for "%s" (post #%d)', 'familabcore' ),
                $item['post_title'],
                $item['post_id']
            ) );
            $this->logger->debug( sprintf(
                __( 'Post %d was imported with object "%d" of type "%s", but could not be found', 'familabcore' ),
                $item['post_id'],
                $_menu_item_object_id,
                $_menu_item_type
            ) );
        }
    }

    function backfill_process_menu_item2( $item ) {
        // skip draft, orphaned menu items
        if ( 'draft' == $item['status'] )
            return;

        $menu_slug = false;
        if ( isset($item['terms']) ) {
            // loop through terms, assume first nav_menu term is correct menu
            foreach ( $item['terms'] as $term ) {
                if ( 'nav_menu' == $term['domain'] ) {
                    $menu_slug = $term['slug'];
                    break;
                }
            }
        }

        // no nav_menu term associated with this menu item
        if ( ! $menu_slug ) {
            _e( 'Menu item skipped due to missing menu slug', 'wordpress-importer' );
            echo '<br />';
            return;
        }

        $menu_id = term_exists( $menu_slug, 'nav_menu' );
        if ( ! $menu_id ) {
            printf( __( 'Menu item skipped due to invalid menu slug: %s', 'wordpress-importer' ), esc_html( $menu_slug ) );
            echo '<br />';
            return;
        } else {
            $menu_id = is_array( $menu_id ) ? $menu_id['term_id'] : $menu_id;
        }

        foreach ( $item['postmeta'] as $meta )
            ${$meta['key']} = $meta['value'];

        if ( 'taxonomy' == $_menu_item_type && isset( $this->processed_terms[intval($_menu_item_object_id)] ) ) {
            $_menu_item_object_id = $this->processed_terms[intval($_menu_item_object_id)];
        } else if ( 'post_type' == $_menu_item_type && isset( $this->processed_posts[intval($_menu_item_object_id)] ) ) {
            $_menu_item_object_id = $this->processed_posts[intval($_menu_item_object_id)];
        } else if ( 'custom' != $_menu_item_type ) {
            // associated object is missing or not imported yet, we'll retry later
            $this->missing_menu_items[] = $item;
            return;
        }

        if ( isset( $this->processed_menu_items[intval($_menu_item_menu_item_parent)] ) ) {
            $_menu_item_menu_item_parent = $this->processed_menu_items[intval($_menu_item_menu_item_parent)];
        } else if ( $_menu_item_menu_item_parent ) {
            $this->menu_item_orphans[intval($item['post_id'])] = (int) $_menu_item_menu_item_parent;
            $_menu_item_menu_item_parent = 0;
        }

        // wp_update_nav_menu_item expects CSS classes as a space separated string
        $_menu_item_classes = maybe_unserialize( $_menu_item_classes );
        if ( is_array( $_menu_item_classes ) )
            $_menu_item_classes = implode( ' ', $_menu_item_classes );

        $args = array(
            'menu-item-object-id' => $_menu_item_object_id,
            'menu-item-object' => $_menu_item_object,
            'menu-item-parent-id' => $_menu_item_menu_item_parent,
            'menu-item-position' => intval( $item['menu_order'] ),
            'menu-item-type' => $_menu_item_type,
            'menu-item-title' => $item['post_title'],
            'menu-item-url' => $_menu_item_url,
            'menu-item-description' => $item['post_content'],
            'menu-item-attr-title' => $item['post_excerpt'],
            'menu-item-target' => $_menu_item_target,
            'menu-item-classes' => $_menu_item_classes,
            'menu-item-xfn' => $_menu_item_xfn,
            'menu-item-status' => $item['status']
        );

        $id = wp_update_nav_menu_item( $menu_id, 0, $args );
        if ( $id && ! is_wp_error( $id ) )
            $this->processed_menu_items[intval($item['post_id'])] = (int) $id;
    }

    /**
     * Parses the WXR file and prepares us for the task of processing parsed data
     *
     * @param string $file Path to the WXR file for importing
     */
    protected function import_start( $file ) {
        //$import_data = get_option('familab_wxr_import_data');
        $import_setting = get_option('familab_wxr_import_settings');
        $import_data = false;
        if (isset($import_setting['fetch_attachments'])){
            $this->fetch_attachments = $import_setting['fetch_attachments'];
        }
        if (!$import_data || is_wp_error( $import_data )){
            require dirname( __FILE__ ) . '/parsers.php';
            if ( ! is_file( $file ) ) {
                return new WP_Error( 'wxr_importer.file_missing', __( 'The file does not exist, please try again.', 'familabcore' ) );
            }
            $parser = new Familab_WXR_Parser();
            $import_data = $parser->parse( $file );
        }
        update_option('familab_bug_import',$import_data);
        $this->version = $import_data['version'];
        $this->posts = $import_data['posts'];
        $this->terms = $import_data['terms'];
        $this->categories = $import_data['categories'];
        $this->tags = $import_data['tags'];
        $this->base_url = esc_url( $import_data['base_url'] );
        // Suspend bunches of stuff in WP core
        wp_defer_term_counting( true );
        wp_defer_comment_counting( true );
        do_action( 'import_start' );
    }

    /**
     * Performs post-import cleanup of files and the cache
     */
    protected function import_end() {
        // Re-enable stuff in core
        wp_suspend_cache_invalidation( false );
        wp_cache_flush();
        foreach ( get_taxonomies() as $tax ) {
            delete_option( "{$tax}_children" );
            _get_term_hierarchy( $tax );
        }

        wp_defer_term_counting( false );
        wp_defer_comment_counting( false );

        /**
         * Complete the import.
         *
         * Fires after the import process has finished. If you need to update
         * your cache or re-enable processing, do so here.
         */
        $import_setting = get_option('familab_wxr_import_settings');
        if (isset($import_setting['type'])){
            $imported_maps = array(
                $import_setting['type'] => array(
                    'post' => $this->processed_posts,
                )
            );
            update_option('familab_imported_maps',$imported_maps);
        }
        do_action( 'import_end' );
    }

    /**
     * Set the user mapping.
     *
     * @param array $mapping List of map arrays (containing `old_slug`, `old_id`, `new_id`)
     */
    public function set_user_mapping( $mapping = array() ) {
        foreach ( $mapping as $map ) {
            if ( empty( $map['old_slug'] ) || empty( $map['old_id'] ) || empty( $map['new_id'] ) ) {
                $this->logger->warning( __( 'Invalid author mapping', 'familabcore' ) );
                continue;
            }

            $old_slug = $map['old_slug'];
            $old_id   = $map['old_id'];
            $new_id   = $map['new_id'];

            $this->mapping['user'][ $old_id ]        = $new_id;
            $this->mapping['user_slug'][ $old_slug ] = $new_id;
        }
    }

    /**
     * Set the user slug overrides.
     *
     * Allows overriding the slug in the import with a custom/renamed version.
     *
     * @param string[] $overrides Map of old slug to new slug.
     */
    public function set_user_slug_overrides( $overrides ) {
        foreach ( $overrides as $original => $renamed ) {
            $this->user_slug_override[ $original ] = $renamed;
        }
    }

    /**
     * If fetching attachments is enabled then attempt to create a new attachment
     *
     * @param array $post Attachment post details from WXR
     * @param string $url URL to fetch attachment from
     * @return int|WP_Error Post ID on success, WP_Error otherwise
     */
    protected function process_attachment( $post, $url ) {
        if ( ! $this->fetch_attachments )
            return new WP_Error( 'attachment_processing_error',
                __( 'Fetching attachments is not enabled', 'familabcore' ) );

        // if the URL is absolute, but does not contain address, then upload it assuming base_site_url

        if ( preg_match( '|^/[\w\W]+$|', $url ) )
            $url = rtrim( $this->base_url, '/' ) . $url;
        // get attachments in media package. If it is not exist, download remote file
        $att_url_info = pathinfo($url);

        $upload_dir = wp_upload_dir();
        $_urlxc = $upload_dir['basedir'].DS. $att_url_info['basename'];
        if ( file_exists( $_urlxc ) ) {
            $upload = array(
                'file' => $_urlxc,
                'url'  => $upload_dir['baseurl'].DS . $att_url_info['basename'],
            );
        } else {
            $upload = $this->fetch_remote_file( $url, $post );
        }

        if ( is_wp_error( $upload ) )
            return $upload;

        if ( $info = wp_check_filetype( $upload['file'] ) )
            $post['post_mime_type'] = $info['type'];
        else
            return new WP_Error( 'attachment_processing_error', __('Invalid file type', 'familabcore') );

        $post['guid'] = $upload['url'];

        // as per wp-admin/includes/upload.php
        $post_id = wp_insert_attachment( $post, $upload['file'] );
        wp_update_attachment_metadata( $post_id, wp_generate_attachment_metadata( $post_id, $upload['file'] ) );

        // remap resized image URLs, works by stripping the extension and remapping the URL stub.
        if ( preg_match( '!^image/!', $info['type'] ) ) {
            $parts = pathinfo( $url );
            $name = basename( $parts['basename'], ".{$parts['extension']}" ); // PATHINFO_FILENAME in PHP 5.2
            $parts_new = pathinfo( $upload['url'] );
            $name_new = basename( $parts_new['basename'], ".{$parts_new['extension']}" );
            $this->url_remap[$parts['dirname'] . '/' . $name] = $parts_new['dirname'] . '/' . $name_new;
        }
        return $post_id;
    }




    /**
     * Attempt to download a remote file attachment
     *
     * @param string $url URL of item to fetch
     * @param array $post Attachment details
     * @return array|WP_Error Local file location details on success, WP_Error otherwise
     */
    protected function fetch_remote_file( $url, $post ) {
        // extract the file name and extension from the url
        $file_name = basename( $url );

        // get placeholder file in the upload dir with a unique, sanitized filename
        $upload = wp_upload_bits( $file_name, 0, '', $post['upload_date'] );
        if ( $upload['error'] )
            return new WP_Error( 'upload_dir_error', $upload['error'] );

        // fetch the remote url and write it to the placeholder file
        $remote_response = wp_safe_remote_get( $url, array(
            'timeout' => 300,
            'stream' => true,
            'filename' => $upload['file'],
        ) );

        $headers = wp_remote_retrieve_headers( $remote_response );
        // request failed
        if ( ! $headers ) {
            @unlink( $upload['file'] );
            return new WP_Error( 'import_file_error', __('Remote server did not respond', 'familabcore') );
        }

        $remote_response_code = wp_remote_retrieve_response_code( $remote_response );

        // make sure the fetch was successful
        if ( $remote_response_code != '200' ) {
            @unlink( $upload['file'] );
            return new WP_Error( 'import_file_error', sprintf( __('Remote server returned error response %1$d %2$s', 'familabcore'), esc_html($remote_response_code), get_status_header_desc($remote_response_code) ) );
        }

        $filesize = filesize( $upload['file'] );

        if ( isset( $headers['content-length'] ) && $filesize != $headers['content-length'] ) {
            @unlink( $upload['file'] );
            return new WP_Error( 'import_file_error', __('Remote file is incorrect size', 'familabcore') );
        }

        if ( 0 == $filesize ) {
            @unlink( $upload['file'] );
            return new WP_Error( 'import_file_error', __('Zero size file downloaded', 'familabcore') );
        }

        $max_size = (int) $this->max_attachment_size();
        if ( ! empty( $max_size ) && $filesize > $max_size ) {
            @unlink( $upload['file'] );
            return new WP_Error( 'import_file_error', sprintf(__('Remote file is too large, limit is %s', 'familabcore'), size_format($max_size) ) );
        }
        // keep track of the old and new urls so we can substitute them later
        $this->url_remap[$url] = $upload['url'];
        $this->url_remap[$post['guid']] = $upload['url']; // r13735, really needed?
        // keep track of the destination if the remote url is redirected somewhere else
        if ( isset($headers['x-final-location']) && $headers['x-final-location'] != $url )
            $this->url_remap[$headers['x-final-location']] = $upload['url'];
        return $upload;
    }

    /**
     * Attempt to associate posts and menu items with previously missing parents
     *
     * An imported post's parent may not have been imported when it was first created
     * so try again. Similarly for child menu items and menu items which were missing
     * the object (e.g. post) they represent in the menu
     */
    function backfill_parents() {
        global $wpdb;
        // find parents for post orphans
        foreach ( $this->post_orphans as $child_id => $parent_id ) {
            $local_child_id = $local_parent_id = false;
            if ( isset( $this->processed_posts[$child_id] ) )
                $local_child_id = $this->processed_posts[$child_id];
            if ( isset( $this->processed_posts[$parent_id] ) )
                $local_parent_id = $this->processed_posts[$parent_id];

            if ( $local_child_id && $local_parent_id ) {
                $wpdb->update( $wpdb->posts, array( 'post_parent' => $local_parent_id ), array( 'ID' => $local_child_id ), '%d', '%d' );
                clean_post_cache( $local_child_id );
            }
        }

        // all other posts/terms are imported, retry menu items with missing associated object
        $missing_menu_items = $this->missing_menu_items;
        foreach ( $missing_menu_items as $item )
            $this->backfill_process_menu_item2( $item );

        // find parents for menu item orphans
        foreach ( $this->menu_item_orphans as $child_id => $parent_id ) {
            $local_child_id = $local_parent_id = 0;
            if ( isset( $this->processed_menu_items[$child_id] ) )
                $local_child_id = $this->processed_menu_items[$child_id];
            if ( isset( $this->processed_menu_items[$parent_id] ) )
                $local_parent_id = $this->processed_menu_items[$parent_id];

            if ( $local_child_id && $local_parent_id )
                update_post_meta( $local_child_id, '_menu_item_menu_item_parent', (int) $local_parent_id );
        }
    }

    /**
     * Use stored mapping information to update old attachment URLs
     */
    function backfill_attachment_urls() {
        global $wpdb;
        // make sure we do the longest urls first, in case one is a substring of another
        uksort( $this->url_remap, array(&$this, 'cmpr_strlen') );
        $this->logger->error($this->url_remap);
        foreach ( $this->url_remap as $from_url => $to_url ) {
            // remap urls in post_content
            $wpdb->query( $wpdb->prepare("UPDATE {$wpdb->posts} SET post_content = REPLACE(post_content, %s, %s)", $from_url, $to_url) );
            // remap enclosure urls
            $result = $wpdb->query( $wpdb->prepare("UPDATE {$wpdb->postmeta} SET meta_value = REPLACE(meta_value, %s, %s) WHERE meta_key='enclosure'", $from_url, $to_url) );
        }
    }


    /**
     * Update _thumbnail_id meta to new, imported attachment IDs
     */
    public function remap_featured_images() {
        // cycle through posts that have a featured image
        foreach ( $this->featured_images as $post_id => $value ) {
            if ( isset( $this->processed_posts[ $value ] ) ) {
                $new_id = $this->processed_posts[ $value ];

                // only update if there's a difference
                if ( $new_id !== $value ) {
                    update_post_meta( $post_id, '_thumbnail_id', $new_id );
                }
            }
        }
    }

    /**
     * Decide if the given meta key maps to information we will want to import
     *
     * @param string $key The meta key to check
     * @return string|bool The key if we do want to import, false if not
     */
    public function is_valid_meta_key( $key ) {
        // skip attachment metadata since we'll regenerate it from scratch
        // skip _edit_lock as not relevant for import
        if ( in_array( $key, array( '_wp_attached_file', '_wp_attachment_metadata', '_edit_lock' ) ) ) {
            return false;
        }

        return $key;
    }

    /**
     * Decide what the maximum file size for downloaded attachments is.
     * Default is 0 (unlimited), can be filtered via import_attachment_size_limit
     *
     * @return int Maximum attachment file size to import
     */
    protected function max_attachment_size() {
        return apply_filters( 'import_attachment_size_limit', 0 );
    }

    /**
     * Added to http_request_timeout filter to force timeout at 60 seconds during import
     *
     * @access protected
     * @return int 60
     */
    function bump_request_timeout($val) {
        return 60;
    }

    // return the difference in length between two strings
    function cmpr_strlen( $a, $b ) {
        return strlen( $b ) - strlen( $a );
    }

    public function process_package($import_type) {
        $this->download_package($import_type);
        $this->unpackage($import_type);
    }
    /**
     * Download the media package
     *
     * @param $_tmppath
     */
    public function download_package( $import_type, $content = 'media', $tmppath = '' ,$debug = true) {
        $_cpath = ABSPATH . 'wp-content' . DS.'uploads'.DS;
        if ($tmppath == '')
            $_tmppath = $_cpath . FAMILAB_THEME_SLUG . '-' . $import_type  . '-tmp';
        else{
            $_tmppath = $tmppath;
        }
        require_once( ABSPATH . 'wp-admin/includes/file.php' );
        WP_Filesystem();
        $package = null;
        //if ( ! is_dir( $_tmppath ) || self::isEmptyDir($_tmppath)) {
            $url = FAMILAB_API_URL.'/'.$content.'/'.FAMILAB_THEME_SLUG.'/'.$import_type;
            $package = download_url( $url, 18000 );
            if ( ! is_wp_error( $package ) ) {
                if (self::prepare_directory($_tmppath)) {
                    $unzip = unzip_file( $package, $_tmppath );
                    if ( is_wp_error( $unzip ) ) {
                        $err_code = $unzip->get_error_code();
                        if ($debug){
                            $this->logger->warning( sprintf(
                                __( '<strong>ERROR %s:</strong> Could not extract demo %s package. Please contact our support staff.',
                                    'familabcore' ),
                                $err_code,
                                $content
                            ) );
                            $this->logger->error($unzip->get_error_message($err_code) );
                        }else{
                            echo sprintf(
                                __( '<div class="error notice"><p><strong>ERROR %s:</strong> Could not extract demo %s package. Please contact our support staff.</p></div>', 'familabcore' ),
                                $err_code,
                                $content
                            );
                        }
                        if ($content == 'media')
                            do_action( 'wxr_importer.process_failed.package' );
                        return false;
                    }
                    if ($content == 'media')
                        do_action( 'wxr_importer.processed.package' );
                    @unlink( $package );
                    return;
                }
                else {
                    @unlink( $package );
                    if (!$debug){
                        $this->logger->warning(sprintf(
                            __( '<div class="error notice"><p>Could not make %s folder. Please contact our support staff.</p></div>', 'familabcore' ),
                            $_tmppath
                        ));
                    }
                }
            }
            else {
                $err_code = $package->get_error_code();
                if ($debug) {
                    $this->logger->warning( sprintf(
                        __( '<div class="error notice"><p><strong>ERROR %s:</strong> Could not download demo %s package. Please use <a href="%s" target="_blank">this direct link</a> or contact our support staff.',
                            'familabcore' ),
                        $err_code,
                        $content,
                        $url
                    ) );
                    $this->logger->error($package->get_error_message($err_code) );
                }else{
                    echo sprintf(
                        __( '<strong>ERROR %s:</strong> Could not download demo %s package. Please use <a href="%s" target="_blank">this direct link</a> or contact our support staff.</p></div>',
                            'familabcore' ),
                        $err_code,
                        $content,
                        $url
                    );
                }
                if ($content == 'media')
                    do_action( 'wxr_importer.process_failed.package' );
                return false;
            }
        //}
        if ($content == 'media')
            do_action( 'wxr_importer.processed.package' );
    }

    public function download_rev_package($import_type,$tmppath,$name){
        global $wp_filesystem;
        if ($tmppath == ''){
            $upload_dir = wp_upload_dir();
            $_tmppath = "{$upload_dir['basedir']}".DS.FAMILAB_THEME_SLUG.'_import'.DS.$import_type.DS.'sliders'.DS;
        }
        else{
            $_tmppath = $tmppath;
        }
        $url = FAMILAB_API_URL.DS.'slider'.DS.FAMILAB_THEME_SLUG.DS.$name;
        $package = download_url( $url, 18000 );
        if ( ! is_wp_error( $package ) ) {
            if (self::prepare_directory($_tmppath)) {
                $newname = $_tmppath.DS.$name.'.zip';
                if ( ! ( rename($package, $newname) || $wp_filesystem->move($package, $newname, true) ) ){
                    return false;
                }
                return;
            }else{
                @unlink( $package );
                $this->logger->warning(sprintf(
                    __( '<div class="error notice"><p>Could not make %s folder. Please contact our support staff.</p></div>', 'familabcore' ),
                    $_tmppath
                ));
            }

        }else{
            $err_code = $package->get_error_code();
            $this->logger->warning( sprintf(
                __( '<div class="error notice"><p><strong>ERROR %s:</strong> Could not download demo slider package. Please use <a href="%s" target="_blank">this direct link</a> or contact our support staff.',
                    'familabcore' ),
                $err_code,
                $url
            ) );
            $this->logger->error($package->get_error_message($err_code) );
        }
    }
        /**
     * Unpack the media package
     *
     * @param $_cpath
     * @param $_tmppath
     *
     * @return bool
     */

    protected function unpackage($import_type ) {
        $_cpath = ABSPATH . 'wp-content' . DS.'uploads'.DS;
        $upload_dir = wp_upload_dir();
        $_tmppath = $_cpath . FAMILAB_THEME_SLUG . '-' . $import_type  . '-tmp';
        if ( is_dir( $_tmppath ) ) {
            $_new     = $this->list_files( $_tmppath );
            foreach ( $_new as $key => $value ) {
                if ( $value == 4 ) {
                    @mkdir( $upload_dir['basedir']. DS . urldecode( $key ), 0755 );
                } else if ( strpos( $key, '.DS_Store' ) === false ) {
                    if (!file_exists($upload_dir['basedir'] . DS . urldecode( $key ))){
                        @copy( $_tmppath . DS . urldecode( $key ), $upload_dir['basedir']. DS . urldecode( $key ) );
                        @flush();
                        @ob_flush();
                    }
                }
            }
            require_once( ABSPATH . 'wp-admin/includes/file.php' );
            WP_Filesystem();
            global $wp_filesystem;
            $wp_filesystem->rmdir( $_tmppath, true );
        } else {
            $this->logger->warning(
                sprintf( __( '<strong>ERROR %s:</strong> Could not found temporary folder. Please contact our support staff.',
                    'familabcore' ),
                    'temp_dir_not_found' )
            );
        }
    }

    /**
     * Prepare a directory.
     *
     * @param   string  $path  Directory path.
     *
     * @return  mixed
     */
    protected static function prepare_directory( $path ) {

        global $wp_filesystem;

        if ( ! is_dir( $path ) ) {
            $results = explode( '/', str_replace( '\\', '/', $path ) );
            $path    = array();
            while ( count( $results ) ) {
                $path[] = current( $results );
                // Shift paths.
                array_shift( $results );
            }
        }else{
            return $path;
        }

        // Re-build target directory.
        $path = is_array( $path ) ? implode( '/', $path ) : $path;

        if ( !wp_mkdir_p( $path ) ) {
            return false;
        }

        if ( ! is_dir( $path ) ) {
            return false;
        }

        return $path;
    }

    public static function isEmptyDir($dir){
        return (($files = @scandir($dir)) && count($files) <= 2);
    }
    /**
     * List all files in downloaded folder
     *
     * @param      $dir
     * @param null $DF
     *
     * @return array
     */
    protected function list_files( $dir, $DF = null ) {

        if ( $DF == null ) {
            $DF = $dir;
        }

        $stack = array();

        if ( is_dir( $dir ) ) {
            $dh = opendir( $dir );
            while ( false !== ( $file = @readdir( $dh ) ) ) {

                $path = $dir . DS . $file;

                if ( $file == '.DS_Store' ) {
                    unlink( $dir . DS . $file );
                } else if ( is_file( $path ) ) {

                    $stack[ urlencode( str_replace( $DF . DS, '', $path ) ) ] = 1;

                } else if ( is_dir( $path ) && $file != '.' && $file != '..' ) {

                    $stack[ urlencode( str_replace( $DF . DS, '', $path ) ) ] = 4;

                    $stack = $stack + self::list_files( $dir . DS . $file, $DF );
                }
            }

        }

        return $stack;
    }
    /**
     * Import WooCommerce Image sizes
     *
     * @return array
     */
    public function import_woocommerce_image_sizes() {
        if ( ! class_exists( 'WooCommerce' ) ) {
            return;
        }
        $wc = $this->get_data( 'woocommerce' );
        if ( is_array( $wc ) && ! empty( $wc['images'] ) ) {
            if ( version_compare( WC_VERSION, '3.3.0', '<' ) ) {
                update_option( 'shop_catalog_image_size', $wc['images']['catalog'] );
                update_option( 'shop_thumbnail_image_size', $wc['images']['thumbnail'] );
                update_option( 'shop_single_image_size', $wc['images']['single'] );
                global $_wp_additional_image_sizes;
                if ( isset( $_wp_additional_image_sizes ) && count( $_wp_additional_image_sizes ) ) {
                    if ( isset( $_wp_additional_image_sizes['shop_thumbnail'] ) ) {
                        $_wp_additional_image_sizes['shop_thumbnail'] = array(
                            'width'  => $wc['images']['thumbnail']['width'],
                            'height' => $wc['images']['thumbnail']['height'],
                            'crop'   => $wc['images']['thumbnail']['crop'],
                        );
                    }
                    if ( isset( $_wp_additional_image_sizes['shop_catalog'] ) ) {
                        $_wp_additional_image_sizes['shop_catalog'] = array(
                            'width'  => $wc['images']['catalog']['width'],
                            'height' => $wc['images']['catalog']['height'],
                            'crop'   => $wc['images']['catalog']['crop'],
                        );
                    }
                    if ( isset( $_wp_additional_image_sizes['shop_single'] ) ) {
                        $_wp_additional_image_sizes['shop_single'] = array(
                            'width'  => $wc['images']['single']['width'],
                            'height' => $wc['images']['single']['height'],
                            'crop'   => $wc['images']['single']['crop'],
                        );
                    }
                }
            } else {
                update_option( 'woocommerce_single_image_width', $wc['images']['single'] );
                update_option( 'woocommerce_thumbnail_image_width', $wc['images']['thumbnail'] );
                update_option( 'woocommerce_thumbnail_cropping', $wc['images']['cropping'] );
                update_option( 'woocommerce_thumbnail_cropping_custom_width', $wc['images']['cropping_custom_width'] );
                update_option( 'woocommerce_thumbnail_cropping_custom_height',
                    $wc['images']['cropping_custom_height'] );
            }
        }
        $this->logger->info('import_woocommerce_image_sizes');
        do_action('wxr_importer.processed.setting');
    }
    protected function get_total_widgets($dir){
        $widget_data = $this->get_data('widgets',false,$dir,false);
        $widget_data = json_decode($widget_data,true);
        $widget_count = 0;
        if ($widget_data){
            foreach ($widget_data as $side_bar => $widgets){
                if ( 'wp_inactive_widgets' === $side_bar ) {
                    continue;
                }
                foreach ($widgets as $w){
                    $widget_count++;
                }
            }
        }
        return $widget_count;
    }
    /**
     * Read export files
     *
     * @param $type
     *
     * @return mixed
     */
    protected function get_data( $type, $unserialize = true,$dir = '', $delfile = true ) {
        global $import_path;
        if ($dir != '' && is_dir($dir)){
            $import_path = $dir;
        }
        $file = $import_path.DS.FAMILAB_THEME_SLUG.'_'.$type . '.txt';
        if ( ! file_exists( $file ) ) {
            $file = $import_path.DS.$type . '.txt';
            if ( ! file_exists( $file ) ) {
                return false;
            }
        }

        require_once( ABSPATH . 'wp-admin/includes/file.php' );
        WP_Filesystem();
        global $wp_filesystem;
        $file_content = $wp_filesystem->get_contents( $file );
        if ($delfile)
            unlink($file);
        return $unserialize ? @unserialize( $file_content ) : $file_content;
    }
    /**
     * Page Options
     *
     * @return array
     */
    public function import_page_options() {
        $pages = $this->get_data( 'page_options' );
        if ( is_array( $pages ) ) {
            if ( ! empty( $pages['show_on_front'] ) ) {
                update_option( 'show_on_front', $pages['show_on_front'] );
            }
            if ( ! empty( $pages['page_on_front'] ) ) {
                $page = get_page_by_title( $pages['page_on_front'] );
                update_option( 'page_on_front', $page->ID );
            }
            if ( ! empty( $pages['page_for_posts'] ) ) {
                $page = get_page_by_title( $pages['page_for_posts'] );
                update_option( 'page_for_posts', $page->ID );
            }
            // Move Hello World post to trash
            wp_trash_post( 1 );
            // Move Sample Page to trash
            wp_trash_post( 2 );
        }
        $this->logger->info('import_page_options');
        do_action('wxr_importer.processed.setting');
    }
    /**
     * Import widgets
     *
     * @return array
     */
    public function import_widgets() {
        global $wp_registered_sidebars;
        $data = json_decode( $this->get_data( 'widgets', false ) );
        update_option( 'sidebars_widgets', array() );
        if ( empty( $data ) || ! is_object( $data ) ) {
            $this->logger->warning(sprintf( wp_kses( __( '<strong>ERROR %s:</strong> Could not read the widget sample data file. Please contact our support staff.',
                'familabcore' ),
                array(
                    'strong' => array(),
                    'a'      => array(
                        'href'   => array(),
                        'target' => array(),
                    ),
                ) ),
                'widget_files_wrong' ));
            return;
        }
        // Get all available widgets site supports
        $available_widgets = $this->available_widgets();
        //return;
        // Get all existing widget instances
        $widget_instances = array();
        foreach ( $available_widgets as $widget_data ) {
            $widget_instances[ $widget_data['id_base'] ] = get_option( 'widget_' . $widget_data['id_base'] );
        }
        // Get widget logic sample data
        //$wl_options     = $this->read_widget_logic_file( $this->demo );
        foreach ( $data as $sidebar_id => $widgets ) {
// Skip inactive widgets (should not be in export file).
            if ( 'wp_inactive_widgets' === $sidebar_id ) {
                continue;
            }
            // Check if sidebar is available on this site.
            // Otherwise add widgets to inactive, and say so.
            if ( isset( $wp_registered_sidebars[ $sidebar_id ] ) ) {
                $use_sidebar_id = $sidebar_id;
                $sidebar_available    = true;
            } else {
                $use_sidebar_id = 'wp_inactive_widgets'; // add to inactive if sidebar does not exist in theme
                $sidebar_available    = false;
            }
            foreach ( $widgets as $widget_instance_id => $widget ) {
                $fail = false;
                // Get id_base (remove -# from end) and instance ID number
                $id_base = preg_replace( '/-[0-9]+$/', '', $widget_instance_id );
                $instance_id_number = str_replace( $id_base . '-', '', $widget_instance_id );
                // Does site support this widget?
                if ( ! $fail && ! isset( $available_widgets[ $id_base ] ) ) {
                    $fail = true;
                    $this->logger->error(__( 'Site does not support widget '.$id_base, 'widget-importer-exporter' ));
                    do_action('wxr_importer.process_false.widget');
                    //$widget_message_type = 'error';
                    //$widget_message = esc_html__( 'Site does not support widget', 'widget-importer-exporter' ); // Explain why widget not imported.
                }
                $widget = json_decode( json_encode( $widget ), true );
                if ( ! $fail && isset( $widget_instances[ $id_base ] ) ) {
                    // Get existing widgets in this sidebar
                    $sidebars_widgets = get_option( 'sidebars_widgets' );
                    $sidebar_widgets  = isset( $sidebars_widgets[ $use_sidebar_id ] ) ? $sidebars_widgets[ $use_sidebar_id ] : array(); // check Inactive if that's where will go
                    // Loop widgets with ID base
                    $single_widget_instances = ! empty( $widget_instances[ $id_base ] ) ? $widget_instances[ $id_base ] : array();
                    foreach ( $single_widget_instances as $check_id => $check_widget ) {
                        // Is widget in same sidebar and has identical settings?
                        if ( in_array( "$id_base-$check_id", $sidebar_widgets ) && (array) $widget == $check_widget ) {
                            $fail = true;
                            $this->logger->warning(__( 'Widget already exists', 'familabcore' ));
                            break;
                        }
                    }
                }
                if ( ! $fail ) {
                    $single_widget_instances   = get_option( 'widget_' . $id_base ); // all instances for that widget ID base, get fresh every time
                    $single_widget_instances   = ! empty( $single_widget_instances ) ? $single_widget_instances : array( '_multiwidget' => 1 ); // start fresh if have to
                    $single_widget_instances[] = $widget; // add it
                    // Get the key it was given
                    end( $single_widget_instances );
                    $new_instance_id_number = key( $single_widget_instances );
                    // If key is 0, make it 1
                    // When 0, an issue can occur where adding a widget causes data from other widget to load, and the widget doesn't stick (reload wipes it)
                    if ( '0' === strval( $new_instance_id_number ) ) {
                        $new_instance_id_number                             = 1;
                        $single_widget_instances[ $new_instance_id_number ] = $single_widget_instances[0];
                        unset( $single_widget_instances[0] );
                    }
                    // Move _multiwidget to end of array for uniformity
                    if ( isset( $single_widget_instances['_multiwidget'] ) ) {
                        $multiwidget = $single_widget_instances['_multiwidget'];
                        unset( $single_widget_instances['_multiwidget'] );
                        $single_widget_instances['_multiwidget'] = $multiwidget;
                    }
                    // Update option with new widget
                    update_option( 'widget_' . $id_base, $single_widget_instances );
                    do_action('wxr_importer.processed.widget');
                    // Assign widget instance to sidebar
                    $sidebars_widgets                      = get_option( 'sidebars_widgets' ); // which sidebars have which widgets, get fresh every time
                    if ( ! $sidebars_widgets ) {
                        $sidebars_widgets = array();
                    }
                    $new_instance_id                       = $id_base . '-' . $new_instance_id_number; // use ID number from new widget instance
                    $sidebars_widgets[ $use_sidebar_id ][] = $new_instance_id; // add new instance to sidebar
                    update_option( 'sidebars_widgets', $sidebars_widgets ); // save the amended data
                    // After widget import action.
                    $after_widget_import = array(
                        'sidebar'           => $use_sidebar_id,
                        'sidebar_old'       => $sidebar_id,
                        'widget'            => $widget,
                        'widget_type'       => $id_base,
                        'widget_id'         => $new_instance_id,
                        'widget_id_old'     => $widget_instance_id,
                        'widget_id_num'     => $new_instance_id_number,
                        'widget_id_num_old' => $instance_id_number,
                    );
                    do_action( 'wie_after_widget_import', $after_widget_import );
                    // Success message.
                    if ( $sidebar_available ) {
                        $this->logger->info(__( 'Widget Imported', 'familabcore' ));
                    } else {
                        $this->logger->warning(__( 'Widget Imported to Inactive', 'familabcore' ));
                    }
                }
            }
        }
        //do_action('wxr_importer.processed.setting');
    }
    /**
     * Get available widgets in current site
     *
     * @return array
     */
    public function available_widgets() {
        global $wp_registered_widget_controls;
        $widget_controls = $wp_registered_widget_controls;
        $available_widgets = array();
        foreach ( $widget_controls as $widget ) {
            if ( ! empty( $widget['id_base'] ) && ! isset( $available_widgets[ $widget['id_base'] ] ) ) { // no dupes
                $available_widgets[ $widget['id_base'] ]['id_base'] = $widget['id_base'];
                $available_widgets[ $widget['id_base'] ]['name']    = $widget['name'];
            }
        }
        return $available_widgets;
    }
    /**
     * Import menus
     *
     * @return array
     */
    public function import_menus(){
        global $wpdb;
        $terms_table = $wpdb->prefix . "terms";
        $menu_data                = $this->get_data( 'menus' );
        $menu_array               = array();
        if ( ! empty( $menu_data ) ) {
            foreach ( $menu_data as $registered_menu => $menu_slug ) {
                $term_rows = $wpdb->get_results( "SELECT * FROM $terms_table where slug='{$menu_slug}'",
                    ARRAY_A );
                if ( isset( $term_rows[0]['term_id'] ) ) {
                    $term_id_by_slug = $term_rows[0]['term_id'];
                } else {
                    $term_id_by_slug = null;
                }
                $menu_array[ $registered_menu ] = $term_id_by_slug;
            }
            set_theme_mod( 'nav_menu_locations', array_map( 'absint', $menu_array ) );
        }
        $this->remap_menu_link();
        do_action('wxr_importer.processed.setting');
    }
    /**
     * Import WooCommerce pages
     *
     * @return array
     */
    public function import_woocommerce_pages(){
        if ( ! class_exists( 'WooCommerce' ) ) {
            return;
        }
        $woopages = array(
            'woocommerce_shop_page_id'      => 'Shop',
            'woocommerce_cart_page_id'      => 'Cart',
            'woocommerce_checkout_page_id'  => 'Checkout',
            'woocommerce_myaccount_page_id' => 'My Account',
        );
        foreach ( $woopages as $woo_page_name => $woo_page_title ) {
            $woopage = get_page_by_title( $woo_page_title );
            if ( isset( $woopage ) && $woopage->ID ) {
                update_option( $woo_page_name, $woopage->ID );
            }
        }
        $notices = array_diff( get_option( 'woocommerce_admin_notices', array() ),
            array(
                'install',
                'update',
            ) );
        update_option( 'woocommerce_admin_notices', $notices );
        delete_option( '_wc_needs_pages' );
        delete_transient( '_wc_activation_redirect' );
        do_action('wxr_importer.processed.setting');
    }
    /**
     * Import Revolution sliders
     *
     */
    public function import_rev_sliders($import_type,$slider_packages = false, $dir = '') {
        if ( ! class_exists( 'RevSliderAdmin' ) ) {
            do_action('wxr_importer.processed.setting');
            return;
        }
        global $wpdb;
        global $import_path;
        if ($dir != '' && is_dir($dir)){
            $import_path = $dir;
        }
        if (!$slider_packages || empty($slider_packages)){
            if ( get_option( 'familabcore_import_revsliders') == false) {
                //$xuri     = str_replace( '/', '\\/', FAMILAB_CORE_SITE_URI ) . '\\';
                $xuri = parse_url(site_url(),PHP_URL_HOST);
                $templine = '';
                $command_line = '';
                $lines    = $this->get_data( 'rev_sliders', false );
                if ( ! empty( $lines ) ) {
                    $lines = explode( "\n", $lines );
                    foreach ( $lines as $line ) {
                        if ( substr( $line, 0, 2 ) == '--' || $line == '' ) {
                            continue;
                        }
                        if ($command_line == ''){
                            $command_line = $line;
                            //echo $command_line;
                        }elseif (strpos($command_line,'INSERT INTO') !== false){
                            ob_start();
                            $wpdb->query( str_replace( array(
                                'FAMILAB_CORE_SITE_URI',
                                '#__',
                            ),
                                array(
                                    $xuri,
                                    $wpdb->prefix,
                                ),
                                $command_line.rtrim($line, ",") ),
                                false );
                            ob_end_clean();
                        }
                        $templine .= $line;
                        if ( substr( trim( $line ), - 1, 1 ) == ';' ) {
                            if (strpos($command_line,'INSERT INTO') !== false){
                                ob_start();
                                $wpdb->query( str_replace( array(
                                    'FAMILAB_CORE_SITE_URI',
                                    '#__',
                                ),
                                    array(
                                        $xuri,
                                        $wpdb->prefix,
                                    ),
                                    $templine ),
                                    false );
                                ob_end_clean();
                            }
                            $templine = '';
                            $command_line = '';
                        }
                    }
                    update_option('familabcore_import_revsliders',true);
                }
            }
        }else{
            $import_path .= DS.'sliders';
            foreach ($slider_packages as $slider_p){
                $file = $import_path.DS.$slider_p . '.zip';
                if ( ! file_exists( $file ) ) {
                    $this->download_rev_package($import_type,$import_path,$slider_p);
                }
            }
            $rev_files = glob( $import_path .DS. '*.zip' );
            if ( ! empty( $rev_files ) ) {
                foreach ( $rev_files as $rev_file ) {
                    $_FILES['import_file']['error']    = UPLOAD_ERR_OK;
                    $_FILES['import_file']['tmp_name'] = $rev_file;
                    ob_start();
                    $slider = new RevSlider();
                    $slider->importSliderFromPost( true, 'none' );
                    ob_end_clean();
                }
            }
        }
        do_action('wxr_importer.processed.setting');
    }
    public function import_redux_option($redux_key){
        $data = $this->get_data('redux',false);
        $upload_dir = wp_upload_dir();
        $page_option = base64_decode($data);
        $page_option = @unserialize($page_option);
        $option_maps = apply_filters('familab_core_import_maps_required',array());
        if ($option_maps && sizeof($option_maps)>0){
            foreach ($option_maps as $map_type => $map_arr){
                if ($map_arr && sizeof($map_arr)>0){
                    foreach ($map_arr as $t => $k){
                        switch ($map_type){
                            case 'media':
                                if (isset($page_option[$k]['url'])){
                                $old_url = $page_option[$k]['url'];
                                if (stripos($old_url,'uploads') !== false){
                                    $old_url_info = pathinfo($old_url);
                                    $_urlxc = $upload_dir['basedir'].DS. $old_url_info['basename'];
                                    if ( file_exists( $_urlxc ) ) {
                                        $page_option[$k]['url']  = $upload_dir['baseurl'].DS . $old_url_info['basename'];
                                    }
                                }
                            }
                                if (isset($page_option[$k]['thumbnail'])){
                                    $old_tb_url = $page_option[$k]['thumbnail'];
                                    if (stripos($old_tb_url,'uploads') !== false){
                                        $old_tb_url_info = pathinfo($old_url);
                                        $_urlxc = $upload_dir['basedir'].DS. $old_tb_url_info['basename'];
                                        if ( file_exists( $_urlxc ) ) {
                                            $page_option[$k]['url']  = $upload_dir['baseurl'].DS . $old_tb_url_info['basename'];
                                        }
                                    }
                                }
                                break;
                            case 'background':
                                if (isset($page_option[$k]['background-image'])){
                                    $old_bg_url = $page_option[$k]['background-image'];
                                    if (stripos($old_bg_url,'uploads') !== false){
                                        $old_url_info = pathinfo($old_bg_url);
                                        $_urlxc = $upload_dir['basedir'].DS. $old_url_info['basename'];
                                        if ( file_exists( $_urlxc ) ) {
                                            $page_option[$k]['background-image']  = $upload_dir['baseurl'].DS . $old_url_info['basename'];
                                        }
                                    }
                                }
                                if (isset($page_option[$k]['media']) && isset($page_option[$k]['media']['thumbnail'])){
                                    $old_me_url = $page_option[$k]['media']['thumbnail'];
                                    if (stripos($old_me_url,'uploads') !== false){
                                        $old_me_url_info = pathinfo($old_me_url);
                                        $_urlxc = $upload_dir['basedir'].DS. $old_me_url_info['basename'];
                                        if ( file_exists( $_urlxc ) ) {
                                            $page_option[$k]['background-image']  = $upload_dir['baseurl'].DS . $old_me_url_info['basename'];
                                        }
                                    }
                                }
                                break;
                            case 'post':
                                if (!empty($this->processed_posts)){
                                    if (isset($page_option[$k]) && $page_option[$k] && isset($this->processed_posts[$page_option[$k]])){
                                        $page_option[$k] =  $this->processed_posts[$page_option[$k]];
                                    }
                                }
                                break;
                        }
                    }
                }
            }
        }
        update_option($redux_key,$page_option);
        do_action('wxr_importer.processed.setting');
    }
    protected function remap_menu_link(){
        // Do something here!
        $menus =  get_terms( 'nav_menu', array('hide_empty' => false));
        $home_url = parse_url(get_home_url('/'),PHP_URL_HOST);
        if (!empty($menus)) {
            foreach ($menus as $menu) {
                $items = wp_get_nav_menu_items($menu->term_id);
                if (!empty($items)) {
                    foreach ($items as $item) {
                        $_menu_item_type = get_post_meta($item->ID, '_menu_item_type', true);
                        $_menu_item_url = get_post_meta($item->ID, '_menu_item_url', true);
                        if ($_menu_item_type == 'custom' && strpos($_menu_item_url,FAMILAB_THEME_SLUG.'.familab.net')!== false) {
                            $_menu_item_url = str_replace(FAMILAB_THEME_SLUG.'.familab.net', $home_url, $_menu_item_url);
                            update_post_meta($item->ID, '_menu_item_url', $_menu_item_url);
                        }
                    }
                }
            }
        }
    }
    public function import_filter_options(){
        if (class_exists('PrdctfltrInit')){
            $templates = get_option( 'prdctfltr_templates', array() );
            if ( !empty( $templates ) && is_array( $templates ) ) {
                update_option( 'prdctfltr_backup_templates', $templates, 'no' );
            }
            $data_filter = $this->get_data('filter_options');
            if (isset($data_filter['prdctfltr_templates'])){
                foreach( $data_filter['prdctfltr_templates'] as $k1 => $v1 ) {
                    if (isset($data_filter['prdctfltr_wc_template_'.$k1])){
                        update_option( 'prdctfltr_wc_template_' . sanitize_title( $k1 ), $data_filter['prdctfltr_wc_template_'.$k1], 'no' );
                    }
                    $templates[$k1] = array();
                }
            }
            if (isset($data_filter['prdctfltr_wc_default'])){
                update_option( 'prdctfltr_wc_default', $data_filter['prdctfltr_wc_default'], 'no' );
            }
            update_option('wc_settings_prdctfltr_enable_overrides',array(),'no');
            update_option( 'prdctfltr_templates', $templates, 'no' );
            do_action('wxr_importer.processed.setting');
        } elseif (class_exists('XforWC_Product_Filters')) {
            $data_filter = $this->get_data('filter_new_options');
            if (isset($data_filter['_prdctfltr_autoload']) ){
                if (isset($data_filter['_prdctfltr_autoload']['presets']) && is_array($data_filter['_prdctfltr_autoload']['presets'])) {
                    foreach ($data_filter['_prdctfltr_autoload']['presets'] as $_preset){
                        if (isset($_preset['slug'])) {
                            update_option( '_prdctfltr_preset_'.$_preset['slug'], $data_filter['_prdctfltr_preset_'.$_preset['slug']], 'no' );
                        }
                    }
                }
            }
            if (isset($data_filter['_prdctfltr_preset_default'])){
                update_option( '_prdctfltr_preset_default', $data_filter['_prdctfltr_preset_default'], 'no' );
            }
            $this->logger->error('import filter settings');
            do_action('wxr_importer.processed.setting');
        }
    }
}
