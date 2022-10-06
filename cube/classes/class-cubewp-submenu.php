<?php
/**
 * Creates the submenu item for the plugin.
 *
 * Registers a new menu item under 'Tools' and uses the dependency passed into
 * the constructor in order to display the page corresponding to this menu item.
 *
 * @package cubewp/cube/classes
 * @version 1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class CubeWp_Submenu {

    private $DefaultPages = null;

   
    public function __construct() {
        add_action('admin_menu', array($this, 'register_sub_menu'),9);
    }

    public static function init() {
        $CubeClass = __CLASS__;
        new $CubeClass;
    }
    
    /**
     * Method _page_action
     *
     * @param string $is it tells system which page to redirect based on id of below default pages aaray
     * @param string $action this is first action=''
     * @param string $action2 this is 2nd action=''
     * @param string $action3 this is 3rd action=''
     *
     * @return string URL
     * @since  1.0.0
     */
    public static function _page_action($is,$action='',$action2='',$action3='') {
        if(!empty($action)){
            $action = '&action='.$action;
        }
        foreach(self::default_pages() as $page ){
            if($page['id'] == $is){
               return admin_url('admin.php?page='.$page['callback'].$action.$action2.$action3);
            }
        }
	}


    /**
     * Creates the submenu item and calls on the Submenu Page object to render
     * the actual contents of the page.
     * 
     * Method default_pages
     *
     * @return array
     * @since  1.0.0
     */
    public static function default_pages() {
        $settings = array(
            array(
                'id'        => 'dashboard', // Expected to be overridden if dashboard is enabled.
                'title'     => esc_html__('CubeWP', 'cubewp-framework'),
                'icon'      => CWP_PLUGIN_URI .'cube/assets/admin/images/cubewp-admin.svg',
                'callback'  => 'cube_wp_dashboard',
            ),
            array(
                'id'        => 'dashboard-sub', // Expected to be overridden if dashboard is enabled.
                'parent'    => 'cube_wp_dashboard',
                'title'     => esc_html__('Dashboard', 'cubewp-framework'),
                'callback'  => 'cube_wp_dashboard',
            ),
            array(
                'id'        => 'cubewp-post-types', // Expected to be overridden if dashboard is enabled.
                'parent'    => 'cube_wp_dashboard',
                'title'     => esc_html__('Post Types', 'cubewp-framework'),
                'callback'  => 'cubewp-post-types',
            ),
            array(
                'id'        => 'cubewp-taxonomies', // Expected to be overridden if dashboard is enabled.
                'parent'    => 'cube_wp_dashboard',
                'title'     => esc_html__('Taxonomies', 'cubewp-framework'),
                'callback'  => 'cubewp-taxonomies',
            ),
            array(
                'id'        => 'custom-fields', // Expected to be overridden if dashboard is enabled.
                'parent'    => 'cube_wp_dashboard',
                'title'     => esc_html__('Custom Fields', 'cubewp-framework'),
                'callback'  => 'custom-fields',
            ),
            array(
                'id'        => 'taxonomy-custom-fields', // Expected to be overridden if dashboard is enabled.
                'parent'    => 'cube_wp_dashboard',
                'title'     => esc_html__('Taxonomy Custom Fields', 'cubewp-framework'),
                'callback'  => 'taxonomy-custom-fields',
            ),
            array(
                'id'        => 'user-custom-fields', // Expected to be overridden if dashboard is enabled.
                'parent'    => 'cube_wp_dashboard',
                'title'     => esc_html__('User Custom Fields', 'cubewp-framework'),
                'callback'  => 'user-custom-fields',
            ),
            array(
                'id'           => 'cubewp-admin-search-filters', // Expected to be overridden if dashboard is enabled.
                'parent'       => 'cube_wp_dashboard',
                'title'        => esc_html__('Search Filter', 'cubewp-framework'),
                'callback'     => 'cubewp-admin-search-filters',
            ),
            array(
                'id'           => 'cubewp-admin-search-fields', // Expected to be overridden if dashboard is enabled.
                'parent'       => 'cube_wp_dashboard',
                'title'        => esc_html__('Search Forms', 'cubewp-framework'),
                'callback'     => 'cubewp-admin-search-fields',
            ),/* 
            array(
                'id'           =>  'cubewp-user-registration-form',
                'parent'       =>  'cube_wp_dashboard',
                'title'        =>  esc_html__('User SignUp Form', 'cubewp'),
                'callback'     =>  'cubewp-user-registration-form',
            ),
            array(
                'id'           =>  'cubewp-user-profile-form',
                'parent'       =>  'cube_wp_dashboard',
                'title'        =>  esc_html__('User Profile Form', 'cubewp'),
                'callback'     =>  'cubewp-user-profile-form',
            ),
            array(
                'id'           =>  'cubewp-post-types-form',
                'parent'       =>  'cube_wp_dashboard',
                'title'        =>  esc_html__('Post Types Form', 'cubewp'),
                'callback'     =>  'cubewp-post-types-form',
            ),
            array(
                'id'           =>  'cubewp-single-layout',
                'parent'       =>  'cube_wp_dashboard',
                'title'        =>  esc_html__('Single Layout', 'cubewp'),
                'callback'     =>  'cubewp-single-layout',
            ),
            array(
                'id'           =>  'cubewp-user-dashboard',
                'parent'       =>  'cube_wp_dashboard',
                'title'        =>  esc_html__('User Dashboard', 'cubewp'),
                'callback'     =>  'cubewp-user-dashboard',
            ), */
            array(
                'id'        => 'cubewp-settings', // Expected to be overridden if dashboard is enabled.
                'parent'    => 'cube_wp_dashboard',
                'title'     => esc_html__('CubeWP Settings', 'cubewp-framework'),
                'callback'  => 'cubewp-settings',
            ),
            array(
                'id'        => 'cubewp-import', // Expected to be overridden if dashboard is enabled.
                'parent'    => 'cube_wp_dashboard',
                'title'     => esc_html__('CubeWP Import', 'cubewp-framework'),
                'callback'  => 'cubewp-import',
            ),
            array(
                'id'        => 'cubewp-export', // Expected to be overridden if dashboard is enabled.
                'parent'    => 'cube_wp_dashboard',
                'title'     => esc_html__('CubeWP Export', 'cubewp-framework'),
                'callback'  => 'cubewp-export',
            )

        );
        return apply_filters( 'cubewp-submenu', $settings);
    }
    
    /**
     * Method register_sub_menu
     *
     * @return void
     * @since  1.0.0
     */
    public function register_sub_menu() {
        $alloptions = $this->default_pages();

        if (is_array($alloptions)) {
            foreach ($alloptions as $options) {
                
                $defaults = array(
                    'id' => null,
                    'parent' => null,
                    'title' => '',
                    'capability' => 'manage_options',
                    'callback' => '',
                    'icon' => '',
                    'position' => null,
                );
                $options = wp_parse_args($options, $defaults);

                if (is_null($options['parent'])) {
                    add_menu_page( $options['title'], $options['title'], $options['capability'], $options['callback'], array($this, 'submenu_page_callback'), $options['icon'], $options['position']);
                } else {
                    $parent_path = $options['parent'];
                    add_submenu_page( $parent_path, $options['title'], $options['title'], $options['capability'], $options['callback'], array($this, 'submenu_page_callback'),$options['position']);
                }
            }
        }
    }
    
    /**
     * Method submenu_page_callback
     *
     * @return void
     * @since  1.0.0
     */
    public function submenu_page_callback() {
        if(current_cubewp_page()){
            do_action(current_cubewp_page());
        }
    }

}