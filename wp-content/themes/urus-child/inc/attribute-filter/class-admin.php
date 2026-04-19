<?php
defined( 'ABSPATH' ) || exit;

/**
 * Admin: dodavanje slike na termine WooCommerce atributa.
 * Slika se čuva kao term meta (attachment ID).
 */
class Barbers_Attribute_Image_Admin {

    private string $taxonomy;
    private string $meta_key = 'barbers_attribute_image_id';

    public function __construct( string $attribute_slug ) {
        $this->taxonomy = wc_attribute_taxonomy_name( $attribute_slug );
        $this->hooks();
    }

    private function hooks(): void {
        // Forma za dodavanje novog termina
        add_action( "{$this->taxonomy}_add_form_fields",  [ $this, 'add_image_field' ] );
        // Forma za uređivanje termina
        add_action( "{$this->taxonomy}_edit_form_fields", [ $this, 'edit_image_field' ], 10, 2 );
        // Čuvanje pri kreiranju/uređivanju
        add_action( "created_{$this->taxonomy}", [ $this, 'save_image' ] );
        add_action( "edited_{$this->taxonomy}",  [ $this, 'save_image' ] );
        // Kolona sa slikom u listi termina
        add_filter( "manage_edit-{$this->taxonomy}_columns",        [ $this, 'add_image_column' ] );
        add_filter( "manage_{$this->taxonomy}_custom_column",       [ $this, 'render_image_column' ], 10, 3 );
        // Media uploader JS
        add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_scripts' ] );
    }

    public function add_image_field(): void {
        ?>
        <div class="form-field barbers-attr-image-wrap">
            <label><?php esc_html_e( 'Slika atributa', 'urus-child' ); ?></label>
            <div class="barbers-attr-image-preview" style="margin-bottom:8px;"></div>
            <input type="hidden" name="barbers_attribute_image_id" id="barbers_attribute_image_id" value="">
            <button type="button" class="button barbers-upload-image-btn">
                <?php esc_html_e( 'Dodaj sliku', 'urus-child' ); ?>
            </button>
            <button type="button" class="button barbers-remove-image-btn" style="display:none;">
                <?php esc_html_e( 'Ukloni sliku', 'urus-child' ); ?>
            </button>
            <p class="description"><?php esc_html_e( 'Preporučena veličina: 100×100px.', 'urus-child' ); ?></p>
        </div>
        <?php
    }

    public function edit_image_field( \WP_Term $term ): void {
        $image_id  = absint( get_term_meta( $term->term_id, $this->meta_key, true ) );
        $image_url = $image_id ? wp_get_attachment_image_url( $image_id, 'thumbnail' ) : '';
        ?>
        <tr class="form-field barbers-attr-image-wrap">
            <th scope="row"><label><?php esc_html_e( 'Slika atributa', 'urus-child' ); ?></label></th>
            <td>
                <div class="barbers-attr-image-preview" style="margin-bottom:8px;">
                    <?php if ( $image_url ) : ?>
                        <img src="<?php echo esc_url( $image_url ); ?>" style="width:100px;height:100px;object-fit:cover;" alt="">
                    <?php endif; ?>
                </div>
                <input type="hidden" name="barbers_attribute_image_id" id="barbers_attribute_image_id"
                       value="<?php echo esc_attr( $image_id ); ?>">
                <button type="button" class="button barbers-upload-image-btn">
                    <?php esc_html_e( $image_id ? 'Zameni sliku' : 'Dodaj sliku', 'urus-child' ); ?>
                </button>
                <button type="button" class="button barbers-remove-image-btn" <?php echo $image_id ? '' : 'style="display:none;"'; ?>>
                    <?php esc_html_e( 'Ukloni sliku', 'urus-child' ); ?>
                </button>
                <p class="description"><?php esc_html_e( 'Preporučena veličina: 100×100px.', 'urus-child' ); ?></p>
            </td>
        </tr>
        <?php
    }

    public function save_image( int $term_id ): void {
        if ( ! isset( $_POST['barbers_attribute_image_id'] ) ) {
            return;
        }
        $image_id = absint( $_POST['barbers_attribute_image_id'] );
        if ( $image_id ) {
            update_term_meta( $term_id, $this->meta_key, $image_id );
        } else {
            delete_term_meta( $term_id, $this->meta_key );
        }
    }

    public function add_image_column( array $columns ): array {
        $new = [];
        foreach ( $columns as $key => $value ) {
            $new[ $key ] = $value;
            if ( $key === 'cb' ) {
                $new['barbers_image'] = __( 'Slika', 'urus-child' );
            }
        }
        return $new;
    }

    public function render_image_column( string $content, string $column, int $term_id ): string {
        if ( $column !== 'barbers_image' ) {
            return $content;
        }
        $image_id = absint( get_term_meta( $term_id, $this->meta_key, true ) );
        if ( $image_id ) {
            return '<img src="' . esc_url( wp_get_attachment_image_url( $image_id, 'thumbnail' ) ) . '" style="width:50px;height:50px;object-fit:cover;" alt="">';
        }
        return '—';
    }

    public function enqueue_scripts( string $hook ): void {
        if ( ! in_array( $hook, [ 'edit-tags.php', 'term.php' ], true ) ) {
            return;
        }
        if ( ! isset( $_GET['taxonomy'] ) || $_GET['taxonomy'] !== $this->taxonomy ) {
            return;
        }
        wp_enqueue_media();
        wp_enqueue_script(
            'barbers-attr-image-admin',
            get_stylesheet_directory_uri() . '/assets/js/attribute-filter-admin.js',
            [ 'jquery' ],
            '1.0.0',
            true
        );
    }
}