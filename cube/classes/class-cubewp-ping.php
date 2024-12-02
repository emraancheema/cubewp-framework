<?php
/**
 * CubeWp PIng will handle User Preferences.
 *
 * @version 1.0
 * @since 1.1.17
 * @package cubewp/cube/classes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * CubeWp_Ping
 */
class CubeWp_Ping{

    public $route   = 'https://crm.cubewp.com';

    public function __construct() {
        register_activation_hook( CWP_PLUGIN_FILE, [$this,'cubewp_send_activation_data'] );
        register_deactivation_hook( CWP_PLUGIN_FILE, [$this,'cubewp_send_deactivation_data'] );
        add_action('admin_init', array($this, 'cubewp_send_heartbeat_ping'));
    }
    
    /**
     * Method cubewp_send_activation_data
     *
     * @return void
     */
    public function cubewp_send_activation_data() {    
        $site_url = get_site_url();
        $admin_email = get_option('admin_email');
        $plugin_name = 'CubeWP-Framework'; // Replace with your actual plugin name
        $active_theme = wp_get_theme()->get('Name');
        $active_plugins = array();
        // Get all active plugins and retrieve their names
        $all_plugins = get_option('active_plugins');
        foreach ($all_plugins as $plugin) {
            $plugin_data = get_plugin_data(WP_PLUGIN_DIR . '/' . $plugin);
            $active_plugins[] = $plugin_data['Name'];
        }
        // Example data for addons and features
        $addons = $this->get_cubewp_addons(); // Fill this with actual addon data if available
        $data = array(
            'site_url'     => $site_url,
            'admin_email'  => $admin_email,
            'plugin_name'  => $plugin_name,
            'active_theme' => $active_theme,
            'active_plugins' => $active_plugins,
            'version'      => CUBEWP_VERSION, // Update this to the actual version of your plugin
            'action_type'  => 'active',
            'addons'       => $addons,
            'features'     => $this->build_features_array(),
        );
        $response = wp_remote_post($this->route.'/wp-json/cubewp-crm/v1/register_plugin', array(
            'method'    => 'POST',
            'body'      => wp_json_encode($data),
            'headers'   => array('Content-Type' => 'application/json'),
            'timeout'   => 45,
        ));
        if (!is_wp_error($response)) {
            set_transient('cubewp_crm_ping', current_time('timestamp'), 7 * DAY_IN_SECONDS);
            return true;
        } else {
            // Optionally log the error or handle it
            return false;
        }
    }

    public function cubewp_send_heartbeat_ping() {
        $last_activation_sent = get_transient('cubewp_crm_ping');
        if ($last_activation_sent && (current_time('timestamp') - $last_activation_sent) < 7 * DAY_IN_SECONDS) {
            // Don't send a heartbeat if it hasn't been 7 days since the last activation
            return;
        }
        return $this->cubewp_send_activation_data();
    }
        
    /**
     * Method cubewp_send_deactivation_data
     *
     * @return void
     */
    public function cubewp_send_deactivation_data() {
        $site_url = get_site_url();
        $plugin_name = 'CubeWP-Framework'; // Replace with your actual plugin name
        $action_type = 'deactivate';
    
        $data = array(
            'site_url'          => $site_url,
            'plugin_name'       => $plugin_name,
            'action_type'       => $action_type,
        );
    
        $response = wp_remote_post($this->route.'/cubewp-crm/v1/deactivate_plugin', array(
            'method'    => 'POST',
            'body'      => wp_json_encode($data),
            'headers'   => array('Content-Type' => 'application/json'),
            'timeout'   => 45,
        ));
    
        if (!is_wp_error($response)) {
            return true;
        }
    }
    
    /**
     * Method get_cubewp_addons
     *
     * @return void
     */
    public function get_cubewp_addons() {

        if (!function_exists('get_plugins')) {
            require_once ABSPATH . 'wp-admin/includes/plugin.php';
        }
        
        // Get all installed plugins
        $all_plugins = get_plugins();
        $active_plugins = get_option('active_plugins');
        $cubewp_addons = [];
        
        // Loop through plugins and filter for active CubeWP addons
        if (!empty($all_plugins) && is_array($all_plugins)) {
            foreach ($all_plugins as $plugin_path => $plugin_data) {
                // Check if the plugin is active and has 'cubewp-addon' in its path
                if (in_array($plugin_path, $active_plugins) && strpos($plugin_path, 'cubewp-addon') !== false) {
                    // Add active addon name and version to the array
                    $cubewp_addons[] = [
                        'addon_name' => $plugin_data['Name'],
                        'version'    => $plugin_data['Version']
                    ];
                }
            }
        }
                
        return $cubewp_addons;
    }

    public function build_features_array() {
        include_once CWP_PLUGIN_PATH . 'cube/functions/admin-functions.php';
        // Initialize the array with default values
        $features = array(
            'post_type_builder' => false,
            'search_form' => false,
            'search_filter' => false,
            'user_profile' => false,
            'user_registration' => false,
            'user_dashboard' => false,
            'theme_builder' => false,
            'loop_builder' => false,
            'post_types' => false, // This will be set based on the result of CWP()->get_form()
            'taxonomies' => false,
            'custom_fields' => false,
            'user_custom_fields' => false,
            'tax_custom_fields' => false,
            'settings_fields' => false,
        );
    
        // Dynamically set values based on conditions
        $features['post_type_builder'] = CWP()->get_form('post_type') ? true : false;
        $features['search_form'] = CWP()->get_form('search_fields') ? true : false;
        $features['search_filter'] = CWP()->get_form('search_filters') ? true : false;
        $features['user_profile'] = CWP()->get_form( 'user_profile' ) ? true : false;
        $features['user_registration'] = CWP()->get_form('user_register') ? true : false;
        $features['user_dashboard'] = CWP()->cubewp_options('cwp_userdash') ? true : false;
        $features['loop_builder'] = CWP()->get_form( 'loop_builder' ) ? true : false;

        $features['post_types'] = get_option('cwp_custom_types') ? true : false;
        $features['taxonomies'] = get_option('cwp_custom_taxonomies') ? true : false;

        $features['theme_builder'] = $this->is_theme_builder_active() ? true : false;

        $features['custom_fields'] = CWP()->get_custom_fields( 'post_types' ) ? true : false;
        $features['tax_custom_fields'] = CWP()->get_custom_fields( 'taxonomy' ) ? true : false;
        $features['user_custom_fields'] = CWP()->get_custom_fields( 'user' ) ? true : false;
        $features['settings_fields'] = CWP()->cubewp_options('cwpOptions') ? true : false;
        // Add other conditions as needed
    
        return $features;
    }

    function is_theme_builder_active() {
        // Query arguments to check for published posts of the specified post type
        $args = array(
            'post_type'      => 'cubewp-tb',
            'post_status'    => 'publish',
            'posts_per_page' => 1, // We only need to know if there is at least one post
        );
        
        // Execute the query
        $query = new WP_Query($args);
        
        // Check if there are any posts
        $has_posts = $query->have_posts();
        
        // Clean up after the query
        wp_reset_postdata();
        
        return $has_posts; // Returns true or false
    }
    

}