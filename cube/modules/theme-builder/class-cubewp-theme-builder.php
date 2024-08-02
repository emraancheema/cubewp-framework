<?php

/**
 * CubeWp Theme builder for display dynamic templates
 *
 * @version 1.0.0
 * @package cubewp/cube/mobules/theme builder
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


/**
 * CubeWp_Theme_Builder
 */
class CubeWp_Theme_Builder {

    public function __construct() {
        add_action( 'cubewp_theme_builder', array( $this, 'display_cubewp_tb_admin_page' ) );
        add_filter('cubewp/posttypes/new', [$this, 'register_theme_builder_post_type']);
        add_filter('cubewp-submenu', array($this, 'Cubewp_Menu'), 10);
        add_action('admin_enqueue_scripts', [$this, 'enqueue_admin_scripts']);
        add_action('admin_footer', [$this, 'add_custom_popup_form']);
        add_action('wp_ajax_cubewp_theme_builder_template', [$this, 'handle_cubewp_theme_builder_template']);
        add_filter('elementor/documents/register', [$this, 'add_elementor_support']);
    }

    public function register_theme_builder_post_type($defaultCPT) {
        $defaultCPT['cubewp-tb']   = array(
            'label'                  => __('Theme Builder', 'cubewp-framework'),
            'singular'               => 'cwp-tb',
            'icon'                   => '',
            'slug'                   => 'cubewp-tb',
            'description'            => __('Custom post type for theme builder templates', 'cubewp-framework'),
            'supports'               => array('title'),
            'hierarchical'           => false,
            'public'                 => false,
            'show_ui'                => true,
            'menu_position'          => '',
            'show_in_menu'           => false,
            'show_in_nav_menus'      => false,
            'show_in_admin_bar'      => false,
            'can_export'             => true,
            'has_archive'            => false,
            'exclude_from_search'    => true,
            'publicly_queryable'     => true,
            'query_var'              => false,
            'rewrite'                => false,
            'rewrite_slug'           => '',
            'rewrite_withfront'      => false,
            'show_in_rest'           => true,
        );
        return $defaultCPT;
    }

    public function Cubewp_Menu($settings) {
        register_post_status( 'inactive', array(
            'label'                     => _x( 'Inactive ', 'Inactive', 'cubewp-framework' ),
            'public'                    => true,
            'label_count'               => _n_noop( 'Inactive s <span class="count">(%s)</span>', 'Inactive s <span class="count">(%s)</span>', 'cubewp-framework' ),
            'post_type'                 => array( 'cubewp-tb'), 
            'show_in_admin_all_list'    => true,
            'show_in_admin_status_list' => true,
            'show_in_metabox_dropdown'  => true,
            'show_in_inline_dropdown'   => true,
            'dashicon'                  => 'dashicons-businessman',
        ) );
        $settings[]	=	array(
            'id'        => 'cubewp-theme-builder', // Expected to be overridden if dashboard is enabled.
            'parent'    => 'cube_wp_dashboard',
            'title'     => esc_html__('Theme Builder', 'cubewp-framework'),
            'callback'  => 'cubewp-theme-builder',
            'position'     => 5
        );
		
        return $settings;
    }

    public function add_elementor_support($post_types) {
        add_post_type_support('cubewp-tb', 'elementor');
    }

    function display_cubewp_tb_admin_page() {
        // Create an instance of our custom list table class
        $theme_builders_list_table = new CubeWp_Theme_Builder_Table();
        $theme_builders_list_table->prepare_items();
    
        // Display the admin page
        ?>
        <div class="wrap">
            <h1 class="wp-heading-inline"><?php echo esc_html__('CubeWP Theme Builder', 'cubewp-framework'); ?></h1>
            <?php 
            if ( !cubewp_check_if_elementor_active()) {   
               echo '<div class="notice notice-error is-dismissible">
                    <p>
                        <strong><span style="display: block; margin: 0.5em 0.5em 0 0; clear: both;">
                            CubeWP Theme Builder requires the Elementor plugin to be installed.
                        </strong>
                    </p>
               </div>';
            }else{
                echo '<a href="#" class="ctb-add-new-template page-title-action">'.esc_html__('Add New Template', 'cubewp-framework').'</a>';
            }
            ?>
            <hr class="wp-header-end">    
            <!-- Display the list table -->
            <form method="post">
                <?php
                $theme_builders_list_table->display();
                ?>
            </form>
        </div>
        <?php
    }

    public function enqueue_admin_scripts() {
        global $pagenow;

        if(CWP()->is_admin_screen('cubewp_theme_builder')){
            // Enqueue jQuery UI Dialog
            wp_enqueue_script('jquery-ui-dialog');
            wp_enqueue_style('wp-jquery-ui-dialog');
        }
    }

    public function add_custom_popup_form() {
        if(CWP()->is_admin_screen('cubewp_theme_builder')){
        ?>
        <div id="ctb-add-template-dialog" title="<?php esc_attr_e('Add New Template', 'cubewp-framework'); ?>" style="display:none;">
            <form id="add-template-form">       
                <div class="cubewp-elements-builder">
                    <div class="page-content">
                        <h1 class="main-heading">Templates Help You <br><span>Work Efficiently</span></h1>
                        <p class="main-paragraph">Use templates to create the different <br> pieces of your site, and reuse them with <br> one click whenever needed</p>
                    </div>

                    <div class="main-form-content">
                        <form action="#" class="main-form">
                            <h2 class="form-heading">Chosse Template Type</h2>
                            <div class="form-fileds">
                                <label for="template_type">Select the type of template you want to work on</label>
                                <select name="template_type" id="template_type" required>
                                    <option value="">Select Template Type</option>
                                    <option value="header">Header</option>
                                    <option value="footer">Footer</option>
                                    <option value="single">Single</option>
                                    <option value="archive">Archive</option>
                                    <?php
                                    // Check if there are blocks available via PHP
                                    $blocks = apply_filters('cubewp/theme_builder/blocks', array());
                                    if (!empty($blocks)) {
                                        echo '<option value="block">Block</option>';
                                    }
                                    ?>
                                    <option value="mega-menu">Mega Menu</option>
                                    <option value="404">Error 404</option>
                                </select>
                            </div>
                            <div class="form-fileds">
                                <label for="template_name">Name your template</label>
                                <input type="text" name="template_name" id="template_name" placeholder="Enter Template Name..." required>
                            </div>
                            <div class="form-fileds">
                                <label for="template_location">Display On</label>
                                <select name="template_location" id="template_location" required>
                                </select>
                            </div>
                            <div class="form-fileds-buttons">
                                <button type="button" name="submit" value="save" class="button button-primary cwp-save-template"><?php esc_html_e('Save', 'cubewp-framework'); ?></button>
                                <button type="button" name="submit" value="save-edit" class="button button-secondary cwp-save-template"><?php esc_html_e('Save & Edit', 'cubewp-framework'); ?></button>
                            </div>
                        </form>
                    </div>
                </div>
            </form>
        </div>
        <?php
        }
    }


    public function handle_cubewp_theme_builder_template() {
        if (!is_user_logged_in()) {
            wp_send_json_error(['message' => 'User not logged in']);
            return;
        }
    
        // Check if data is set
        if (!isset($_POST['data'])) {
            wp_send_json_error(['message' => 'No data received']);
            return;
        }
    
        // Parse and sanitize form data
        parse_str($_POST['data'], $form_data);
    
        $template_type = sanitize_text_field($form_data['template_type']);
        $template_name = sanitize_text_field($form_data['template_name']);
        $template_location = sanitize_text_field($form_data['template_location']);
    
        // Check if fields are empty
        if (empty($template_type)) {
            wp_send_json_error(['message' => 'Template type is required']);
            return;
        }
    
        if (empty($template_name)) {
            wp_send_json_error(['message' => 'Template name is required']);
            return;
        }
    
        if (empty($template_location)) {
            wp_send_json_error(['message' => 'Template location is required']);
            return;
        }

        $post_id = false;
        // FOR EDITING EXISTING TEMPLATE
        if (isset($form_data['ctb_edit_template_id']) && !empty($form_data['ctb_edit_template_id'])) {
            $post_id = $form_data['ctb_edit_template_id'];
            if ( get_post_status ( $post_id ) == 'inactive' ) {
                wp_update_post(array(
                    'ID' => $post_id,
                    'post_status' => 'publish'
                ));
            }
        }

        // Query for existing posts with the same name and location
        $args = array(
            'post_type' => 'cubewp-tb',
            'post_status' => 'publish',
            'meta_query' => array(
                array(
                    'key' => 'template_type',
                    'value' => $template_type,
                    'compare' => '='
                ),
                array(
                    'key' => 'template_location',
                    'value' => $template_location,
                    'compare' => '='
                )
            ),
            'fields' => 'ids'
        );

        $existing_posts = new WP_Query($args);

        // Change status to 'inactive' for existing posts
        if ($existing_posts->have_posts()) {
            foreach ($existing_posts->posts as $existing_post_id) {
                if($existing_post_id != $post_id && $template_type != 'mega-menu'){
                    wp_update_post(array(
                        'ID' => $existing_post_id,
                        'post_status' => 'inactive'
                    ));
                }
            }
        }

        // FOR CREATING NEW TEMPLATE
        if (!$post_id) {
            $new_post = array(
                'post_title'    => $template_name,
                'post_content'  => '',
                'post_status'   => 'publish',
                'post_type'     => 'cubewp-tb',
            );

            $post_id = wp_insert_post($new_post);
        }

        if ($post_id) {
            update_post_meta($post_id, 'template_type', $template_type);
            update_post_meta($post_id, 'template_location', $template_location);

            $response = '';
            if ($_POST['template_action'] === 'save-edit') {
                $response = ['redirect' => get_edit_post_link($post_id, 'url')];
                $response['redirect'] = add_query_arg(['action' => 'elementor'], $response['redirect']);
            }
            wp_send_json_success($response);
        } else {
            wp_send_json_error(['message' => 'There was an error saving the template.']);
        }
    }


    public static function get_current_template_post_id($type = '') {
        if($type == '') return false;
 
        global $post;

        $template_post_id = false;

        if ($type == 'block') {
            return self::get_template_post_ids_by_location($type);
        }

        if ($type == '404' && is_404()) {
            return self::get_template_post_id_by_location('all', $type);
        }

        if (is_singular() && !is_front_page()) {
            // Single Post Page
            $post_type = get_post_type($post);
            $template_post_id = self::get_template_post_id_by_location('single_' . $post_type, $type);

            // If no specific template found, look for 'single_all'
            if (!$template_post_id) {
                $template_post_id = self::get_template_post_id_by_location('single_all', $type);
            }
        } elseif (is_front_page()) {
            // For Front page
            $template_post_id = self::get_template_post_id_by_location('home', $type); 

        } elseif (is_home()) {
            // For Front page
            $template_post_id = self::get_template_post_id_by_location('blog', $type); 

        } elseif (is_archive()) {

            // Archive Page
            if (is_author()) {
                $template_post_id = self::get_template_post_id_by_location('archive_author', $type);
            } elseif (is_search()) {
                
                $template_post_id = self::get_template_post_id_by_location('archive_search_' . get_post_type(), $type);
                // If no specific template found, look for 'single_all'
                if (!$template_post_id) {
                    $template_post_id = self::get_template_post_id_by_location('archive_search', $type);
                }
            } else {
                $taxonomy = get_queried_object();

                if (!empty($taxonomy->taxonomy)) {

                    $template_post_id = self::get_template_post_id_by_location('archive_' . $taxonomy->taxonomy, $type);
                    
                } elseif (is_post_type_archive()) {
                    
                    $template_post_id = self::get_template_post_id_by_location('archive_' . get_post_type(), $type);
                }
            }

            if(!$template_post_id){
                // Default to archive_all
                $template_post_id = self::get_template_post_id_by_location('archive_all', $type);
            }
        }
        if(!$template_post_id){
            // Default to Entire Site
            $template_post_id = self::get_template_post_id_by_location('entire_site', $type);
        }
        return $template_post_id;
    }
    
    public static function get_template_post_id_by_location($location, $type) {
        $args = array(
            'post_type' => 'cubewp-tb',
            'post_status' => 'publish',
            'meta_query' => array(
                'relation' => 'AND', // Ensure that both conditions are met
                array(
                    'key' => 'template_location',
                    'value' => $location,
                    'compare' => '='
                ),
                array(
                    'key' => 'template_type',
                    'value' => $type,
                    'compare' => '='
                )
            ),
            'fields' => 'ids'
        );
    
        $query = new WP_Query($args);

        if ($query->have_posts()) {
            $post_id = $query->posts[0];
            wp_reset_postdata();
            return $post_id;
            
        }
    
        return false;
    }

    public static function get_template_post_ids_by_location($type) {
        $args = array(
            'post_type' => 'cubewp-tb',
            'post_status' => 'publish',
            'meta_query' => array(
                array(
                    'key' => 'template_type',
                    'value' => $type,
                    'compare' => '='
                )
            ),
            'fields' => 'ids'
        );
    
        $query = new WP_Query($args);
    
        if ($query->have_posts()) {
            $post_id = $query->posts;
            wp_reset_postdata();
            return $post_id;
        }
    
        return false;
    }

    public static function is_cubewp_theme_builder_active($type = '') {
        if(empty($type)) return false;

        if(self::get_current_template_post_id($type)){
            return true;
        }
    }

    public static function do_cubewp_theme_builder($template = '', $static_template_id = 0, $return = false){
        if(empty($template)) return;

        $template_id = $static_template_id > 0 ? $static_template_id : self::get_current_template_post_id($template);

        if(!empty($template_id) && !is_array($template_id)){
            $elementor_frontend_builder = new Elementor\Frontend();
            $elementor_frontend_builder->init();

            if($return == true){
                return $elementor_frontend_builder->get_builder_content_for_display( $template_id, true );
            }else{
                echo $elementor_frontend_builder->get_builder_content_for_display( $template_id, true );
            }
            
        }
    }

    public static function cubewp_set_custom_template() {
        if ( is_singular( 'cubewp-tb' ) ) {
            $template = '';
            $page_template = CUBEWP_FILES . 'templates/cubewp-template-single.php';
            if ( file_exists( $page_template ) ) {
                $template = $page_template;
            }
            return $template;
        }
    }

    public static function mega_menu_options() {
        $args = array(
            'post_type' => 'cubewp-tb',
            'post_status' => 'publish',
            'meta_query' => array(
                array(
                    'key' => 'template_type',
                    'value' => 'mega-menu',
                    'compare' => '='
                ),
                array(
                    'key' => 'template_location',
                    'value' => 'all',
                    'compare' => '='
                )
            ),
            'fields' => 'ids'
        );

        $existing_posts = new WP_Query($args);
        $options = [];
        if ($existing_posts->have_posts()) {
            foreach ($existing_posts->posts as $existing_post_id) {
                $options[$existing_post_id] = get_the_title( $existing_post_id );
            }
        }
        return $options;
    }
    


    public static function init() {
        $CubeClass = __CLASS__;
        new $CubeClass;
    }

}