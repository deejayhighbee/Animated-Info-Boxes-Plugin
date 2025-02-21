<?php
/**
 * Plugin Name: Animated Info Boxes
 * Plugin URI:  https://postalcode.pro
 * Description: A plugin that lets you create animated info boxes with gradient/image backgrounds, and display them via shortcode.
 * Version:     1.2
 * Author:      Ayodeji
 * Author URI:  https://ayodeji.co
 * License:     GPL2
 */

// Prevent direct file access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Animated_Info_Boxes {

    public function __construct() {
        add_action( 'init', array( $this, 'register_post_type' ) );
        add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ) );
        add_action( 'save_post', array( $this, 'save_meta' ) );
        add_shortcode( 'animated_info_boxes', array( $this, 'render_shortcode' ) );
    }

    /**
     * Register Custom Post Type: aib_box
     */
    public function register_post_type() {
        $labels = array(
            'name'               => __( 'Info Boxes', 'aib' ),
            'singular_name'      => __( 'Info Box', 'aib' ),
            'add_new'            => __( 'Add New', 'aib' ),
            'add_new_item'       => __( 'Add New Info Box', 'aib' ),
            'edit_item'          => __( 'Edit Info Box', 'aib' ),
            'new_item'           => __( 'New Info Box', 'aib' ),
            'all_items'          => __( 'All Info Boxes', 'aib' ),
            'view_item'          => __( 'View Info Box', 'aib' ),
            'search_items'       => __( 'Search Info Boxes', 'aib' ),
            'not_found'          => __( 'No Info Box found', 'aib' ),
            'not_found_in_trash' => __( 'No Info Box found in Trash', 'aib' ),
            'menu_name'          => __( 'Info Boxes', 'aib' ),
        );
        
        $args = array(
            'labels'             => $labels,
            'public'             => false,
            'show_ui'            => true,
            'show_in_menu'       => true,
            'menu_icon'          => 'dashicons-layout', 
            'capability_type'    => 'post',
            'hierarchical'       => false,
            'supports'           => array( 'title', 'page-attributes' ), // Added 'page-attributes'
        );
        register_post_type( 'aib_box', $args );
    }

    /**
     * Add Meta Boxes for the aib_box post type
     */
    public function add_meta_boxes() {
        add_meta_box(
            'aib_box_meta',
            __( 'Info Box Settings', 'aib' ),
            array( $this, 'box_meta_callback' ),
            'aib_box',
            'normal',
            'high'
        );
    }

    /**
     * Meta Box Callback: Display form fields
     */
    public function box_meta_callback( $post ) {
        wp_nonce_field( basename( __FILE__ ), 'aib_box_nonce' );

        // Retrieve existing values
        $aib_description = get_post_meta( $post->ID, 'aib_description', true );
        $aib_link_text   = get_post_meta( $post->ID, 'aib_link_text', true );
        $aib_link_url    = get_post_meta( $post->ID, 'aib_link_url', true );
        $aib_bg_color    = get_post_meta( $post->ID, 'aib_bg_color', true );
        $aib_bg_gradient = get_post_meta( $post->ID, 'aib_bg_gradient', true );
        $aib_bg_image    = get_post_meta( $post->ID, 'aib_bg_image', true );
        $aib_title_color = get_post_meta( $post->ID, 'aib_title_color', true );
        $aib_desc_color  = get_post_meta( $post->ID, 'aib_desc_color', true );
        ?>
        <!-- Meta Box Fields -->
        <p>
            <label for="aib_description"><?php _e( 'Description', 'aib' ); ?></label><br>
            <textarea style="width:100%;min-height:80px;" id="aib_description" name="aib_description"><?php echo esc_textarea( $aib_description ); ?></textarea>
        </p>
        <p>
            <label for="aib_link_text"><?php _e( 'Link Text', 'aib' ); ?></label><br>
            <input type="text" style="width:100%;" id="aib_link_text" name="aib_link_text" 
                value="<?php echo esc_attr( $aib_link_text ); ?>">
        </p>
        <p>
            <label for="aib_link_url"><?php _e( 'Link URL', 'aib' ); ?></label><br>
            <input type="url" style="width:100%;" id="aib_link_url" name="aib_link_url"
                value="<?php echo esc_attr( $aib_link_url ); ?>">
        </p>
        <hr>
        <p>
            <label for="aib_bg_color"><?php _e( 'Background Color (e.g. #f00)', 'aib' ); ?></label><br>
            <input type="text" style="width:100%;" id="aib_bg_color" name="aib_bg_color"
                value="<?php echo esc_attr( $aib_bg_color ); ?>">
        </p>
        <p>
            <label for="aib_bg_gradient">
                <?php _e( 'Background Gradient (e.g. linear-gradient(rgba(0,0,0,0.4), rgba(0,0,0,0.2)))', 'aib' ); ?>
            </label><br>
            <input type="text" style="width:100%;" id="aib_bg_gradient" name="aib_bg_gradient" 
                value="<?php echo esc_attr( $aib_bg_gradient ); ?>">
            <small><?php _e('Used as top layer if both gradient and image are set.', 'aib'); ?></small>
        </p>
        <p>
            <label for="aib_bg_image"><?php _e( 'Background Image URL', 'aib' ); ?></label><br>
            <input type="url" style="width:100%;" id="aib_bg_image" name="aib_bg_image"
                value="<?php echo esc_attr( $aib_bg_image ); ?>">
            <small><?php _e('Will appear behind the gradient if both are set.', 'aib'); ?></small>
        </p>
        <hr>
        <p>
            <label for="aib_title_color"><?php _e( 'Title Text Color (e.g. #fff)', 'aib' ); ?></label><br>
            <input type="text" style="width:100%;" id="aib_title_color" name="aib_title_color"
                value="<?php echo esc_attr( $aib_title_color ); ?>">
        </p>
        <p>
            <label for="aib_desc_color"><?php _e( 'Description Text Color (e.g. #333)', 'aib' ); ?></label><br>
            <input type="text" style="width:100%;" id="aib_desc_color" name="aib_desc_color"
                value="<?php echo esc_attr( $aib_desc_color ); ?>">
        </p>
        <?php
    }

    /**
     * 4. Save Meta Box Data
     */
    public function save_meta( $post_id ) {
        // Verify nonce
        if ( ! isset( $_POST['aib_box_nonce'] ) ||
             ! wp_verify_nonce( $_POST['aib_box_nonce'], basename( __FILE__ ) ) ) {
            return;
        }
        // Bail if auto-saving
        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
            return;
        }
        // Check permissions
        if ( isset( $_POST['post_type'] ) && 'aib_box' === $_POST['post_type'] ) {
            if ( ! current_user_can( 'edit_post', $post_id ) ) {
                return;
            }
        }

        // Fields to save
        $fields = array(
            'aib_description',
            'aib_link_text',
            'aib_link_url',
            'aib_bg_color',
            'aib_bg_gradient',
            'aib_bg_image',
            'aib_title_color',
            'aib_desc_color',
        );

        foreach ( $fields as $field ) {
            if ( isset( $_POST[ $field ] ) ) {
                // Additional sanitization based on field type
                switch ( $field ) {
                    case 'aib_link_url':
                    case 'aib_bg_image':
                        $sanitized = esc_url_raw( $_POST[ $field ] );
                        break;
                    case 'aib_bg_color':
                    case 'aib_title_color':
                    case 'aib_desc_color':
                        $sanitized = sanitize_hex_color( $_POST[ $field ] );
                        break;
                    default:
                        $sanitized = sanitize_text_field( $_POST[ $field ] );
                }
                update_post_meta( $post_id, $field, $sanitized );
            } else {
                delete_post_meta( $post_id, $field );
            }
        }
    }

    /**
     * 5. Enqueue Front-End CSS & JS
     */
    public function enqueue_scripts() {
        static $assets_loaded = false;

        if ( $assets_loaded ) {
            return;
        }

        $assets_loaded = true;

        // Enqueue CSS
        wp_enqueue_style( 
            'aib-style', 
            plugin_dir_url( __FILE__ ) . 'css/aib-style.css', 
            array(), 
            '1.9' 
        );

        // Enqueue jQuery (already included with WordPress)
        wp_enqueue_script( 'jquery' );

        // Enqueue Custom JS for Animated Info Boxes
        wp_enqueue_script( 
            'aib-script', 
            plugin_dir_url( __FILE__ ) . 'js/aib-script.js', 
            array( 'jquery' ), 
            '1.9', 
            true 
        );
    }

    /**
     * Shortcode: [animated_info_boxes count="6" order="ASC|DESC" ids="1,3,2,4"]
     */
    public function render_shortcode( $atts ) {
        // Enqueue Scripts and Styles only when shortcode is used
        $this->enqueue_scripts();

        $atts = shortcode_atts( array(
            'count' => -1,        // Show all by default
            'order' => 'ASC',     // Order direction: ASC or DESC
            'ids'   => '',        // Comma-separated list of IDs for custom ordering
        ), $atts, 'animated_info_boxes' );

        // If 'ids' attribute is set, use it to fetch posts in that specific order
        if ( ! empty( $atts['ids'] ) ) {
            $ids = array_map( 'intval', explode( ',', $atts['ids'] ) );
            $args = array(
                'post_type'      => 'aib_box',
                'posts_per_page' => count( $ids ),
                'post_status'    => 'publish',
                'post__in'       => $ids,
                'orderby'        => 'post__in', // Preserve the order of IDs
            );
        } else {
            // Default ordering by menu_order
            $args = array(
                'post_type'      => 'aib_box',
                'posts_per_page' => intval( $atts['count'] ),
                'post_status'    => 'publish',
                'orderby'        => 'menu_order',
                'order'          => strtoupper( $atts['order'] ) === 'DESC' ? 'DESC' : 'ASC',
            );
        }

        $query = new WP_Query( $args );

        if ( ! $query->have_posts() ) {
            return '<p>' . __( 'No info boxes found.', 'aib' ) . '</p>';
        }

        ob_start();
        ?>
        <div class="aib-board">
        <?php 
        while ( $query->have_posts() ) : 
            $query->the_post();

            // Get meta
            $description = get_post_meta( get_the_ID(), 'aib_description', true );
            $link_text   = get_post_meta( get_the_ID(), 'aib_link_text', true );
            $link_url    = get_post_meta( get_the_ID(), 'aib_link_url', true );
            $bg_color    = get_post_meta( get_the_ID(), 'aib_bg_color', true );
            $bg_gradient = get_post_meta( get_the_ID(), 'aib_bg_gradient', true );
            $bg_image    = get_post_meta( get_the_ID(), 'aib_bg_image', true );
            $title_color = get_post_meta( get_the_ID(), 'aib_title_color', true );
            $desc_color  = get_post_meta( get_the_ID(), 'aib_desc_color', true );

            // Prepare inline styles
            $cover_style = '';
            if ( $bg_gradient && $bg_image ) {
                // Combine gradient + image in the same background property:
                // The gradient is the TOP layer, the image is the BOTTOM layer
                $cover_style = "
                  background: 
                    $bg_gradient, 
                    url('$bg_image') center center / cover no-repeat;
                  background-blend-mode: overlay;
                ";
            } elseif ( $bg_image ) {
                $cover_style = "
                  background: 
                    url('$bg_image') center center / cover no-repeat;
                ";
            } elseif ( $bg_gradient ) {
                $cover_style = "background: $bg_gradient;";
            } elseif ( $bg_color ) {
                $cover_style = "background-color: $bg_color;";
            }

            // Title color
            $title_style = $title_color ? "color: $title_color;" : '';

            // Description color
            $desc_style  = $desc_color ? "color: $desc_color;" : '';
            ?>
            <section>
                <div class="aib-tile aib-cover" style="<?php echo esc_attr( $cover_style ); ?>">
                    <h4 style="<?php echo esc_attr( $title_style ); ?>"><?php the_title(); ?></h4>
                </div>
                <div class="aib-tile aib-desc" style="<?php echo esc_attr( $desc_style ); ?>">
                    <span><?php echo wp_kses_post( $description ); ?></span>
                    <?php if ( $link_text && $link_url ) : ?>
                        <a href="<?php echo esc_url( $link_url ); ?>">
                            <?php echo esc_html( $link_text ); ?>
                        </a>
                    <?php endif; ?>
                </div>
            </section>
            <?php
        endwhile;
        wp_reset_postdata();
        ?>
        </div> <!-- .aib-board -->
        <?php

        return ob_get_clean();
    }
}

new Animated_Info_Boxes();
