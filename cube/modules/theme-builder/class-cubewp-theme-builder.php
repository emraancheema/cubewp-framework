<?php

/**
 * CubeWp Theme builder for display dynamic templates
 *
 * @version 1.0.0
 * @package cubewp/cube/mobules/theme builder
 */

if (! defined('ABSPATH')) {
    exit;
}


/**
 * CubeWp_Theme_Builder
 */
class CubeWp_Theme_Builder
{

    public function __construct()
    {
        add_action('cubewp_theme_builder', array($this, 'display_cubewp_tb_admin_page'));
        add_filter('cubewp/theme_builder/blocks', array($this, 'hooks_from_settings'));
    }

    /**
     * Method display_cubewp_tb_admin_page
     *
     * @return void
     */
    function hooks_from_settings($return)
    {
        global $cwpOptions;
        $hooks = isset($cwpOptions['cwp_tb_hooks']) ? $cwpOptions['cwp_tb_hooks'] : '';
        if (!empty($hooks)) {
            foreach ($hooks as $hook) {
                $return[$hook] = $hook;
            }
        }
        return $return;
    }

    /**
     * Method display_cubewp_tb_admin_page
     *
     * @return void
     */
    function display_cubewp_tb_admin_page()
    {
        // Create an instance of our custom list table class
        $theme_builders_list_table = new CubeWp_Theme_Builder_Table();
        $theme_builders_list_table->prepare_items();

        // Display the admin page
?>
        <div class="wrap">
            <h1 class="wp-heading-inline"><?php echo esc_html__('CubeWP Theme Builder', 'cubewp-framework'); ?></h1>
            <?php
            if (!cubewp_check_if_elementor_active()) {
                echo '<div class="notice notice-error is-dismissible">
                    <p>
                        <strong><span style="display: block; margin: 0.5em 0.5em 0 0; clear: both;">
                            CubeWP Theme Builder requires the Elementor plugin to be installed.
                        </strong>
                    </p>
               </div>';
            } else {
                echo '<a href="#" class="ctb-add-new-template page-title-action">' . esc_html__('Add New Template', 'cubewp-framework') . '</a>';
            }
            $search_type = isset($_GET['cwp-template-type']) && !empty($_GET['cwp-template-type']) ? $_GET['cwp-template-type'] : 'activated';
            $tabs = [
                'all' => esc_html__("All", 'cubewp-framework'),
                'activated' => esc_html__("Activated", 'cubewp-framework'),
                'deactivated' => esc_html__("Deactivated", 'cubewp-framework'),
                'header' => esc_html__("Header", 'cubewp-framework'),
                'header' => esc_html__("Header", 'cubewp-framework'),
                'footer' => esc_html__("Footer", 'cubewp-framework'),
                'single' => esc_html__("Single", 'cubewp-framework'),
                'archive' => esc_html__("Archive", 'cubewp-framework'),
                'block' => esc_html__("Hooks", 'cubewp-framework'),
                'shop' => esc_html__("Shop", 'cubewp-framework'),
                'mega-menu' => esc_html__("Mega Menu", 'cubewp-framework'),
                '404' => esc_html__("404", 'cubewp-framework'),
            ];
            if (!class_exists('WooCommerce')) {
                unset($tabs['shop']);
            }
            if (!$theme_builders_list_table->check_if_post_available_by_status('inactive')) {
                unset($tabs['deactivated']);
            }
            if (!$theme_builders_list_table->check_if_post_available_by_status('publish')) {
                unset($tabs['activated']);
            }
            ?>

            <hr class="wp-header-end">
            <div class="wrap cwp-post-type-title flex-none margin-minus-20">
                <div class="cwp-post-type-title-nav">
                    <h1 class="wp-heading-inline"><?php esc_html_e("Theme Builder templates", 'cubewp-framework'); ?></h1>
                    <nav class="nav-tab-wrapper wp-clearfix">
                        <?php
                        foreach ($tabs as $key => $tab) {
                            $active_tab = !empty($search_type) && $search_type == $key  ? 'nav-tab-active' : '';
                            echo ' <a class="nav-tab ' . $active_tab . '" href="?page=cubewp-theme-builder&cwp-template-type=' . $key . '">' . $tab . '</a>';
                        }
                        ?>
                    </nav>
                </div>
            </div>
            <!-- Display the list table -->
            <form method="post">
                <?php
                $theme_builders_list_table->display();
                ?>
            </form>
        </div>
        <?php
    }

    /**
     * Method add_custom_popup_form
     *
     * @return void
     */
    public static function add_custom_popup_form()
    {
        if (CWP()->is_admin_screen('cubewp_theme_builder')) {
            wp_enqueue_script('jquery-ui-dialog');
            wp_enqueue_style('wp-jquery-ui-dialog');
        ?>
            <div id="ctb-add-template-dialog" title="<?php esc_attr_e('Add New Template', 'cubewp-framework'); ?>" style="display:none;">
                <form id="add-template-form">
                    <div class="cubewp-elements-builder cubewp-theme-builder-popup">
                        <div class="page-content">
                            <h1 class="heading"><?php esc_html_e('Templates Help You', 'cubewp-framework'); ?> <br><span><?php esc_html_e('Work Efficiently', 'cubewp-framework'); ?></span></h1>
                            <p class="paragraph">
                                <?php esc_html_e('CubeWP theme builder is a powerful tool in WordPress that empowers users to design and customize nearly every aspect of their website without needing to write code. Instead of relying on pre-built templates, a theme builder allows you to create unique layouts for various parts of your website, such as the header, footer, single post pages, archive pages, and even error pages (like 404 pages)', 'cubewp-framework'); ?>
                            </p>
                        </div>
                        <div class="cubewp-main-form">
                            <h2 class="heading">Choose Template Type</h2>
                            <div class="cubewp-form-fileds">
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
                                        echo '<option value="block">Hooks</option>';
                                    }
                                    if (class_exists('WooCommerce')) {
                                        echo '<option value="shop">Shop Page</option>';
                                    }
                                    ?>
                                    <option value="mega-menu">Mega Menu</option>
                                    <option value="404">Error 404</option>
                                    <?php
                                    $custom_options = apply_filters('cubewp/theme_builder/options/register', array());
                                    if (!empty($custom_options) && is_array($custom_options)) {
                                        foreach ($custom_options as $key => $label) {
                                            // Ensure the label is a string for safe output
                                            if (is_string($label)) {
                                                echo '<option value="' . esc_attr($key) . '">' . esc_html($label) . '</option>';
                                            }
                                        }
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="cubewp-form-fileds">
                                <label for="template_name">Name your template</label>
                                <input type="text" name="template_name" id="template_name" placeholder="Enter Template Name..." required>
                            </div>
                            <div class="cubewp-form-fileds">
                                <label for="template_location">Display On</label>
                                <select name="template_location" id="template_location" required>
                                </select>
                            </div>
                            <div class="form-fileds-buttons">
                                <button type="button" name="submit" value="save" class="button button-primary cwp-save-template"><?php esc_html_e('Save', 'cubewp-framework'); ?></button>
                                <button type="button" name="submit" value="save-edit" class="button button-primary cwp-save-template"><?php esc_html_e('Save & Edit', 'cubewp-framework'); ?></button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
<?php
        }
    }


    /**
     * Method cubewp_theme_builder_template
     *
     * @return void
     */
    public static function cubewp_theme_builder_template()
    {
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
            wp_update_post(array(
                'ID' => $post_id,
                'post_title' => $template_name,
            ));
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
                if ($existing_post_id != $post_id && $template_type != 'mega-menu') {
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

                if ($template_type == 'single') {
                    $post_type_slug = CubeWp_Theme_Builder_Table::get_post_type_slug($template_location);
                    $tb_demo_id = CubeWp_Theme_Builder_Table::get_first_post_id_by_post_type($post_type_slug);
                    $response['redirect'] = add_query_arg(['action' => 'elementor', 'tb_demo_id' => $tb_demo_id], $response['redirect']);
                }
            }
            wp_send_json_success($response);
        } else {
            wp_send_json_error(['message' => 'There was an error saving the template.']);
        }
    }


    /**
     * Method get_current_template_post_id
     *
     * @param $type $type 
     *
     * @return void
     */
    public static function get_current_template_post_id($type = '')
    {
        if ($type == '') return false;

        global $post;

        $template_post_id = false;

        if ($type == 'block') {
            return self::get_template_post_ids_by_location($type);
        }

        if ($type == '404' && is_404()) {
            return self::get_template_post_id_by_location('all', $type);
        }

        if ($type == 'archive' && is_post_type_archive('product')) {
            return self::get_template_post_id_by_location('all', 'shop');
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

            if (!$template_post_id) {
                // Default to archive_all
                $template_post_id = self::get_template_post_id_by_location('archive_all', $type);
            }
        }
        if (!$template_post_id) {
            // Default to Entire Site
            $template_post_id = self::get_template_post_id_by_location('entire_site', $type);
        }
        return $template_post_id;
    }

    /**
     * Method get_template_post_id_by_location
     *
     * @param $location $location 
     * @param $type $type 
     *
     * @return void
     */
    public static function get_template_post_id_by_location($location, $type)
    {
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

    /**
     * Method get_template_post_ids_by_location
     *
     * @param $type $type 
     *
     * @return void
     */
    public static function get_template_post_ids_by_location($type)
    {
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

    /**
     * Method is_cubewp_theme_builder_active
     *
     * @param $type $type 
     *
     * @return void
     */
    public static function is_cubewp_theme_builder_active($type = '')
    {
        if (empty($type)) return false;

        if (self::get_current_template_post_id($type)) {
            return true;
        }
    }

    /**
     * Method do_cubewp_theme_builder
     *
     * @param $template $template 
     * @param $static_template_id $static_template_id 
     * @param $return $return 
     *
     * @return void
     */
    public static function do_cubewp_theme_builder($template = '', $static_template_id = 0, $return = false)
    {
        if (empty($template)) return;

        $template_id = $static_template_id > 0 ? $static_template_id : self::get_current_template_post_id($template);

        if (!empty($template_id) && !is_array($template_id)) {
            $elementor_frontend_builder = new Elementor\Frontend();
            $elementor_frontend_builder->init();

            if ($return == true) {
                return $elementor_frontend_builder->get_builder_content_for_display($template_id, true);
            } else {
                echo $elementor_frontend_builder->get_builder_content_for_display($template_id, true);
            }
        }
    }

    /**
     * Method cubewp_set_custom_template
     *
     * @return void
     */
    public static function cubewp_set_custom_template()
    {
        if (is_singular('cubewp-tb')) {
            $template = '';
            $page_template = CUBEWP_FILES . 'templates/cubewp-template-single.php';
            if (file_exists($page_template)) {
                $template = $page_template;
            }
            return $template;
        }
    }

    /**
     * Method mega_menu_options
     *
     * @return void
     */
    public static function cwp_elementor_builder_options($template_type = '')
    {
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
                $options[$existing_post_id] = get_the_title($existing_post_id);
            }
        }
        return $options;
    }



    public static function init()
    {
        $CubeClass = __CLASS__;
        new $CubeClass;
    }
}
