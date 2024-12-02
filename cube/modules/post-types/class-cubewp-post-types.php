<?php
/**
 * CubeWP post types to create post types.
 *
 * @version 1.0
 * @package cubewp/cube/modules/post-types
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * CubeWp_Post_Types
 */
class CubeWp_Post_Types {
    
    /**
     * construct
     * two hooks, one is to activate this class in submenu page
     * another filter is to create default post types filter .
     * @since 1.0
     * @version 1.0
     */
    public function __construct() {
        add_action( 'cubewp_post_types', array( $this, 'create_new_cpt' ) );
        add_filter('cubewp/posttypes/new', array($this, 'get_postType'));
    }
    
    /**
     * Create new CPT
     * This function is actully display for post types 
     * contains 3 function to show, save and add new CPT in cubewp
     * @since 1.0
     * @version 1.0
     */
    public function create_new_cpt() {
        $this->save_postType();
        $this->cpt_form_display();
        $this->add_new_cpt();
    }
    
    /**
     * Add new CPT
     * This function checks $_GET if page is loaded for new or edit post type then redirects
     * @since 1.0
     * @version 1.0
     */
    private function add_new_cpt() {
        if (isset($_GET['action']) && ('new' == $_GET['action'] || 'edit' == $_GET['action'])) {
            $this->cpt_form_edit();
        }
    }
        
    /**
     * CWP CPT
     * parse arguments to register post types. 
     * make sure all argumanted passses properly
     * @since 1.0
     * @version 1.0
     */
    public static function CWP_cpt() {
        $post_types = array('cwp_form_fields','cwp_user_fields','cwp_settings_fields');
        foreach($post_types as $post_type){
            $defaultCPT[$post_type]   = array(
                'label'                  => $post_type,
                'singular'               => 'field',
                'icon'                   => '',
                'slug'                   => $post_type,
                'description'            => '',
                'supports'               => array('title', 'author', 'custom-fields'),
                'hierarchical'           => false,
                'public'                 => false,
                'show_ui'                => false,
                'menu_position'          => false,
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
        };
        $default_cpt = apply_filters('cubewp/posttypes/new', $defaultCPT);
        foreach ($default_cpt as $single_cpt) {
           
            $labels = array(
                'name'                   => _x($single_cpt['label'], 'Post Type General Name', 'cubewp-framework'),
                'singular_name'          => _x($single_cpt['singular'], 'Post Type Singular Name', 'cubewp-framework'),
                'menu_name'              => sprintf(__('%s', 'cubewp-framework'), $single_cpt['label']),
                'all_items'              => sprintf(__('All %s', 'cubewp-framework'), $single_cpt['label']),
                'view_item'              => sprintf(__('View %s', 'cubewp-framework'), $single_cpt['singular']),
                'add_new_item'           => sprintf(__('Add New %s', 'cubewp-framework'), $single_cpt['singular']),
                'add_new'                => __('Add New', 'cubewp-framework'),
                'edit_item'              => sprintf(__('Edit %s', 'cubewp-framework'), $single_cpt['singular']),
                'update_item'            => sprintf(__('Update %s', 'cubewp-framework'), $single_cpt['singular']),
                'search_items'           => sprintf(__('Search %s', 'cubewp-framework'), $single_cpt['singular']),
                'not_found'              => __('Not Found', 'cubewp-framework'),
                'not_found_in_trash'     => __('Not found in Trash', 'cubewp-framework'),
            );
            
            $args = array(
                'label'                  => sprintf(__('%s', 'cubewp-framework'), $single_cpt['label']),
                'description'            => sprintf(__('%s', 'cubewp-framework'), $single_cpt['description']),
                'labels'                 => $labels,
                'menu_icon'              => $single_cpt['icon'],
                'supports'               => $single_cpt['supports'],
                'hierarchical'           => cwp_boolean_value($single_cpt['hierarchical']),
                'public'                 => cwp_boolean_value($single_cpt['public']),
                'show_ui'                => cwp_boolean_value($single_cpt['show_ui']),
                'show_in_menu'           => is_numeric($single_cpt['show_in_menu']) ? cwp_boolean_value($single_cpt['show_in_menu']) : $single_cpt['show_in_menu'],
                'menu_position'          => $single_cpt['menu_position'],
                'show_in_nav_menus'      => cwp_boolean_value($single_cpt['show_in_nav_menus']),
                'show_in_admin_bar'      => cwp_boolean_value($single_cpt['show_in_admin_bar']),
                'can_export'             => cwp_boolean_value($single_cpt['can_export']),
                'has_archive'            => cwp_boolean_value($single_cpt['has_archive']),
                'exclude_from_search'    => cwp_boolean_value($single_cpt['exclude_from_search']),
                'publicly_queryable'     => cwp_boolean_value($single_cpt['publicly_queryable']),
                'query_var'              => cwp_boolean_value($single_cpt['query_var']),
                'capability_type'        => 'post',
                'show_in_rest'           => cwp_boolean_value($single_cpt['show_in_rest']),
            );
            if ( $single_cpt['rewrite'] == 1 ) {
                $args['rewrite']['slug']       = isset($single_cpt['rewrite_slug']) ? $single_cpt['rewrite_slug'] : $single_cpt['slug'];
                $args['rewrite']['with_front'] = cwp_boolean_value($single_cpt['rewrite_withfront']);
            }
            register_post_type( $single_cpt['slug'], $args );
        }
        register_post_status( 'inactive', array(
            'label'                     => _x( 'Inactive ', 'Inactive', 'cubewp-framework' ),
            'public'                    => true,
            'label_count'               => _n_noop( 'Inactive s <span class="count">(%s)</span>', 'Inactive s <span class="count">(%s)</span>', 'cubewp-framework' ),
            'post_type'                 => array( 'cwp_form_fields','cwp_user_fields','cwp_settings_fields' ), 
            'show_in_admin_all_list'    => true,
            'show_in_admin_status_list' => true,
            'show_in_metabox_dropdown'  => true,
            'show_in_inline_dropdown'   => true,
            'dashicon'                  => 'dashicons-businessman',
        ) );
        
    }
    
    /**
     * Save post types
     * Make sure data is being recieve properly and merging the existing data in option table. 
     * @since 1.0
     * @version 1.0
     */
    private function save_postType() {
        
        if (isset($_POST['cwp']['postType'])) {
            if( ! wp_verify_nonce( $_POST['cwp_post_type_nonce'], basename( __FILE__ ) ) )
                return '';
            
            $CPT_slug = sanitize_text_field($_POST['cwp']['postType']['slug']);
            if(is_numeric($CPT_slug)){
                return '';
            }
            $cpt = array(
                $CPT_slug                     => array(
                    'label'                   => sanitize_text_field($_POST['cwp']['postType']['label']),
                    'singular'                => sanitize_text_field($_POST['cwp']['postType']['singular']),
                    'icon'                    => sanitize_text_field($_POST['cwp']['postType']['icon']),
                    'slug'                    => sanitize_text_field($_POST['cwp']['postType']['slug']),
                    'description'             => sanitize_text_field($_POST['cwp']['postType']['description']),
                    'supports'                => CubeWp_Sanitize_text_Array($_POST['cwp']['postType']['supports']),
                    'hierarchical'            => sanitize_text_field($_POST['cwp']['postType']['hierarchical']),
                    'public'                  => sanitize_text_field($_POST['cwp']['postType']['public']),
                    'show_ui'                 => sanitize_text_field($_POST['cwp']['postType']['show_ui']),
                    'menu_position'           => intval($_POST['cwp']['postType']['menu_position']),
                    'show_in_menu'            => sanitize_text_field($_POST['cwp']['postType']['show_in_menu']),
                    'show_in_nav_menus'       => sanitize_text_field($_POST['cwp']['postType']['show_in_nav_menus']),
                    'show_in_admin_bar'       => sanitize_text_field($_POST['cwp']['postType']['show_in_admin_bar']),
                    'can_export'              => sanitize_text_field($_POST['cwp']['postType']['can_export']),
                    'has_archive'             => sanitize_text_field($_POST['cwp']['postType']['has_archive']),
                    'exclude_from_search'     => sanitize_text_field($_POST['cwp']['postType']['exclude_from_search']),
                    'publicly_queryable'      => sanitize_text_field($_POST['cwp']['postType']['publicly_queryable']),
                    'query_var'               => sanitize_text_field($_POST['cwp']['postType']['query_var']),
                    'rewrite'                 => sanitize_text_field($_POST['cwp']['postType']['rewrite']),
                    'rewrite_slug'            => sanitize_text_field($_POST['cwp']['postType']['rewrite_slug']),
                    'rewrite_withfront'       => sanitize_text_field($_POST['cwp']['postType']['rewrite_withfront']),
                    'show_in_rest'            => sanitize_text_field($_POST['cwp']['postType']['show_in_rest']),
                )
            );

            $get_CustomTypes = CWP_types();
            if ($get_CustomTypes) {
                $dataMerge = array_merge($get_CustomTypes, $cpt);
            } else {
                $dataMerge = $cpt;
            }
            
            update_option('cwp_custom_types', $dataMerge);
            wp_redirect( CubeWp_Submenu::_page_action('cubewp-post-types') );
        }
    }
    
    /**
     * get post types
     * Get all post types from option tabel and this function is passing through the filter to register post type. 
     * @since 1.0
     * @version 1.0
     */
    public function get_postType($default) {
        $get_CustomTypes = CWP_types();
        if (!empty($get_CustomTypes)) {
            $default_cpt = array_merge($default, $get_CustomTypes);
            $update = update_option('cwp_custom_post_types', count($get_CustomTypes));
            if($update){
                flush_rewrite_rules();
            }
            return $default_cpt;
        }
        return $default;
    }
    
    /**
     * get post type by slug
     * Get single post type by its slug. 
     * @since 1.0
     * @version 1.0
     */
    public function get_postTypeBYsLug() {
        $get_CustomTypes = CWP_types();
        if (!empty($get_CustomTypes)) {
            if (isset($_GET['action']) && 'edit' == $_GET['action'] && !empty($_GET['postTypeid'])) {
                $singleCPT = $get_CustomTypes[sanitize_text_field($_GET['postTypeid'])];
                return $singleCPT;
            }
        }
        return;
    }

    
    /**
     * CPT form display
     * Prepared table in post type table class to show post types in wordpress structure. 
     * @since 1.0
     * @version 1.0
     */    
    private function cpt_form_display() {
        if (isset($_GET['action']) && ('new' == $_GET['action'] || 'edit' == $_GET['action'])) {
            return;
        }        
        
        $customFieldsPostTypesTable = new CubeWp_Post_Types_List_Table();   
        
        ?>
        <div class="wrap cwp-post-type-title">
			<h1 class="wp-heading-inline"><?php esc_html_e('Custom Post Types', 'cubewp-framework'); ?></h1>
			<a href="<?php echo CubeWp_Submenu::_page_action('cubewp-post-types','new'); ?>" class="page-title-action">+ <?php esc_html_e('Add New', 'cubewp-framework'); ?></a>
		</div>
		<hr class="wp-header-end">
        <div class="wrap cwp-post-type-wrape">
            <?php $customFieldsPostTypesTable->prepare_items(); ?>
            <form method="post">
                <input type="hidden" name="page" value="cubewp-post-type">
                <?php $customFieldsPostTypesTable->display(); ?>
            </form>
        </div>
        <?php        
    }
    
    
    /**
     * CPT form edit
     * it show form to add or edit post type. 
     * parsing data and then applying filter on each field. 
     * @since 1.0
     * @version 1.0
     */   
    public function cpt_form_edit(){
        
        
        $postType = $this->get_postTypeBYsLug();
        $defaults = array(
            'label'                => '',
            'singular'             => '',
            'icon'                 => 'dashicons-admin-post',
            'slug'                 => '',
            'description'          => '',
            'supports'             => array('title', 'editor', 'thumbnail'),
            'hierarchical'         => false,
            'public'               => true,
            'show_ui'              => true,
            'menu_position'        => 5,
            'show_in_menu'         => true,
            'show_in_nav_menus'    => true,
            'show_in_admin_bar'    => true,
            'can_export'           => true,
            'has_archive'          => true,
            'exclude_from_search'  => false,
            'publicly_queryable'   => true,
            'query_var'            => true,
            'rewrite'              => false,
            'rewrite_slug'         => '',
            'rewrite_withfront'    => true,
            'taxonomies'          => '',
            'show_in_rest'         => true,
        );
        $postType = wp_parse_args($postType, $defaults);
        
        ?>
        <div class="cpt-form wrap cubewp-wrap">
            <form id="post" class="cwpposttype" method="post" action="" enctype="multipart/form-data">				
                <div class="wrap cwp-post-type-title">
                    <?php echo self::_title();	?>	
                    <?php echo self::save_button(); ?>		
                </div>
				<hr class="wp-header-end">
                <input type="hidden" name="cwp_post_type_nonce" value="<?php echo wp_create_nonce( basename( __FILE__ ) ); ?>">
                <div id="poststuff"  class="padding-0">
                    <div id="post-body" class="metabox-holder columns-2">
                    <?php echo self::post_type_side_actions($postType); ?>
                        <div id="postbox-container-2" class="postbox-container postbox-container-top">
                            <?php echo self::post_type_basic_settings($postType); ?>
                            <?php echo self::post_type_options($postType); ?>

                        </div>
                        <div class="clear"></div>
                    </div>
                </div>
            </form>
        </div>
        <?php
    }
    
    /**
     * page title
     * page title split for edit or add post type form. 
     * @since 1.0
     * @version 1.0
     */  
    private static function post_type_options(array $postType) {
        ?>
        <div class="postbox">
            <div class="postbox-header">
                <h2><span><?php esc_html_e('Advanced Settings', 'cubewp-framework'); ?></span></h2>
            </div>
            <div class="inside">
                <div class="main">
                    <table class="form-table">
                        <tbody>
                            <?php
                            echo apply_filters('cubewp/admin/post_type/dropdown/field', '', array(
                                'id'             =>    'hierarchical',
                                'name'           =>    'cwp[postType][hierarchical]',
                                'value'          =>    $postType['hierarchical'],
                                'label'          =>    esc_html__('Hierarchical', 'cubewp-framework'),
                                'options'        =>    array( 0 => 'False', 1 => 'True' ),
                                'description'    =>    esc_html__( '(default: false) Whether or not the post type can have parent-child relationships.', 'cubewp-framework' ),
                            ));

                            echo apply_filters('cubewp/admin/post_type/dropdown/field', '', array(
                                'id'             =>    'public',
                                'name'           =>    'cwp[postType][public]',
                                'value'          =>    $postType['public'],
                                'label'          =>    esc_html__('Public', 'cubewp-framework'),
                                'options'        =>    array( 0 => 'False', 1 => 'True' ),
                                'description'    =>    esc_html__( '(Custom Post Type UI default: true) Whether or not posts of this type should be shown in the admin UI and is publicly queryable.', 'cubewp-framework' )
                            ));

                            echo apply_filters('cubewp/admin/post_type/dropdown/field', '', array(
                                'id'             =>    'show_ui',
                                'name'           =>    'cwp[postType][show_ui]',
                                'value'          =>    $postType['show_ui'],
                                'label'          =>    esc_html__('Show UI', 'cubewp-framework'),
                                'options'        =>    array( 0 => 'False', 1 => 'True' ),
                                'description'    =>    esc_html__( '(default: true) Whether or not to generate a default UI for managing this post type.', 'cubewp-framework' )
                            ));

                            echo apply_filters('cubewp/admin/post_type/dropdown/field', '', array(
                                'id'             =>    'show_in_menu',
                                'name'           =>    'cwp[postType][show_in_menu]',
                                'value'          =>    $postType['show_in_menu'],
                                'label'          =>    esc_html__('Show in Menu', 'cubewp-framework'),
                                'options'        =>    array( 0 => 'False', 1 => 'True' ),
                                'description'    =>    esc_html__( '(default: true) Whether or not to show the post type in the admin menu and where to show that menu.', 'cubewp-framework' )
                            ));

                            echo apply_filters('cubewp/admin/post_type/dropdown/field', '', array(
                                'id'          => 'menu_position',
                                'name'        => 'cwp[postType][menu_position]',
                                'value'       => $postType['menu_position'],
                                'label'       => esc_html__('Menu Position', 'cubewp-framework'),
                                'options'     => array(
                                   '5'   => 'Below Posts',
                                   '10'  => 'Below Media',
                                   '20'  => 'Below Pages',
                                   '25'  => 'Below Comments',
                                   '65'  => 'Below Plugins',
                                   '70'  => 'Below Users',
                                   '75'  => 'Below Tools',
                                   '80'  => 'Below Settings',
                                   '100' => 'Below Settings Separator'
                                ),
                                'description' => esc_html__('The position in the menu order the post type should appear. show_in_menu must be true.', 'cubewp-framework')
                             ));

                            echo apply_filters('cubewp/admin/post_type/dropdown/field', '', array(
                                'id'             =>    'show_in_nav_menus',
                                'name'           =>    'cwp[postType][show_in_nav_menus]',
                                'value'          =>    $postType['show_in_nav_menus'],
                                'label'          =>    esc_html__('Show in Nav Menus', 'cubewp-framework'),
                                'options'        =>    array( 0 => 'False', 1 => 'True' ),
                                'description'    =>    esc_html__( '(Custom Post Type UI default: true) Whether or not this post type is available for selection in navigation menus.', 'cubewp-framework' )
                            ));

                            echo apply_filters('cubewp/admin/post_type/dropdown/field', '', array(
                                'id'             =>    'show_in_admin_bar',
                                'name'           =>    'cwp[postType][show_in_admin_bar]',
                                'value'          =>    $postType['show_in_admin_bar'],
                                'label'          =>    esc_html__('Show In Admin Bar', 'cubewp-framework'),
                                'options'        =>    array( 0 => 'False', 1 => 'True' ),
                                'description'    =>    esc_html__( '(default: true)  Makes this post type available via the admin bar.', 'cubewp-framework' )
                            ));

                            echo apply_filters('cubewp/admin/post_type/dropdown/field', '', array(
                                'id'             =>    'can_export',
                                'name'           =>    'cwp[postType][can_export]',
                                'value'          =>    $postType['can_export'],
                                'label'          =>    esc_html__('Can Export', 'cubewp-framework'),
                                'options'        =>    array( 0 => 'False', 1 => 'True' ),
                                'description'    =>    esc_html__( '(default: true)  Whether to allow this post type to be exported.', 'cubewp-framework' )
                            ));

                            echo apply_filters('cubewp/admin/post_type/dropdown/field', '', array(
                                'id'             =>    'has_archive',
                                'name'           =>    'cwp[postType][has_archive]',
                                'value'          =>    $postType['has_archive'],
                                'label'          =>    esc_html__('Has Archive', 'cubewp-framework'),
                                'options'        =>    array( 0 => 'False', 1 => 'True' ),
                                'description'    =>    esc_html__( '(default: true) Whether or not the post type will have a post type archive URL.', 'cubewp-framework' )
                            ));

                            echo apply_filters('cubewp/admin/post_type/dropdown/field', '', array(
                                'id'             =>    'exclude_from_search',
                                'name'           =>    'cwp[postType][exclude_from_search]',
                                'value'          =>    $postType['exclude_from_search'],
                                'label'          =>    esc_html__('Exclude From Search', 'cubewp-framework'),
                                'options'        =>    array( 0 => 'False', 1 => 'True' ),
                                'description'    =>    esc_html__( '(default: false) Whether or not to exclude posts with this post type from front end search results.', 'cubewp-framework' )
                            ));

                            echo apply_filters('cubewp/admin/post_type/dropdown/field', '', array(
                                'id'             =>    'publicly_queryable',
                                'name'           =>    'cwp[postType][publicly_queryable]',
                                'value'          =>    $postType['publicly_queryable'],
                                'label'          =>    esc_html__('Publicly Queryable', 'cubewp-framework'),
                                'options'        =>    array( 0 => 'False', 1 => 'True' ),
                                'description'    =>    esc_html__( '(default: true) Whether or not queries can be performed on the front end as part of parse_request()', 'cubewp-framework' )
                            ));

                            echo apply_filters('cubewp/admin/post_type/dropdown/field', '', array(
                                'id'             =>    'query_var',
                                'name'           =>    'cwp[postType][query_var]',
                                'value'          =>    $postType['query_var'],
                                'label'          =>    esc_html__('Query Var', 'cubewp-framework'),
                                'options'        =>    array( 0 => 'False', 1 => 'True' ),
                                'description'    =>    esc_html__( '(default: true) Sets the query_var key for this post type.', 'cubewp-framework' )
                            ));

                            echo apply_filters('cubewp/admin/post_type/dropdown/field', '', array(
                                'id'             =>    'rewrite',
                                'name'           =>    'cwp[postType][rewrite]',
                                'value'          =>    $postType['rewrite'],
                                'label'          =>    esc_html__('Rewrite', 'cubewp-framework'),
                                'options'        =>    array( 0 => 'False', 1 => 'True' ),
                                'description'    =>    esc_html__( '(default: false) Whether or not WordPress should use rewrites for this post type.', 'cubewp-framework' )
                            ));

                            echo apply_filters('cubewp/admin/post_type/text/field', '', array(
                                'id'             =>    'rewrite_slug',
                                'name'           =>    'cwp[postType][rewrite_slug]',
                                'placeholder'    =>    '(default: post type slug)',
                                'value'          =>    $postType['rewrite_slug'],
                                'label'          =>    esc_html__('Custom Rewrite Slug', 'cubewp-framework'),
                                'description'    =>    esc_html__( 'Custom post type slug to use instead of the default.', 'cubewp-framework' )
                            ));


                            echo apply_filters('cubewp/admin/post_type/dropdown/field', '', array(
                                'id'             =>    'rewrite_withfront',
                                'name'           =>    'cwp[postType][rewrite_withfront]',
                                'value'          =>    $postType['rewrite_withfront'],
                                'label'          =>    esc_html__('Rewrite With Front', 'cubewp-framework'),
                                'options'        =>    array( 0 => 'False', 1 => 'True' ),
                                'description'    =>    esc_html__( '(default: true) Should the permalink structure be prepended with the front base.', 'cubewp-framework' )
                            ));

                            echo apply_filters('cubewp/admin/post_type/dropdown/field', '', array(
                                'id'             =>    'show_in_rest',
                                'name'           =>    'cwp[postType][show_in_rest]',
                                'value'          =>    $postType['show_in_rest'],
                                'label'          =>    esc_html__('Show in REST API', 'cubewp-framework'),
                                'options'        =>    array( 0 => 'False', 1 => 'True' ),
                                'description'    =>    esc_html__( '(Custom Post Type UI default: true) Whether or not to show this post type data in the WP REST API.', 'cubewp-framework' )
                            ));
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    <?php
    }
        
    /**
     * page title
     * page title split for edit or add post type form. 
     * @since 1.0
     * @version 1.0
     */  
    private static function post_type_basic_settings(array $postType) {
        ?>
        <div class="postbox">
            <div class="postbox-header">
                <h2><span><?php esc_html_e('Basic Settings', 'cubewp-framework'); ?></span></h2>
            </div>
            <div class="inside">
                <div class="main">
                    <table class="form-table cwp-validation">
                        <tbody>
                            <?php

                            echo apply_filters('cubewp/admin/post_type/text/field', '', array(
                                'id'             =>    'post_type_slug',
                                'name'           =>    'cwp[postType][slug]',
                                'value'          =>    $postType['slug'],
                                'class'          =>    'post-type-slug',
                                'placeholder'    =>    esc_html__('Enter post type slug', 'cubewp-framework'),
                                'label'          =>    esc_html__('Post Type Slug', 'cubewp-framework'),
                                'required'       =>    true,
                                'tooltip'        =>    'Give a slug for this post type. Which will be used to get this post type data.',
                                'extra_attrs'    =>    'maxlength=20 '. isset($postType['slug']) && !empty($postType['slug']) ? 'readonly' : '',
                            ));

                            echo apply_filters('cubewp/admin/post_type/text/field', '', array(
                                'id'             =>    'label',
                                'name'           =>    'cwp[postType][label]',
                                'value'          =>    $postType['label'],
                                'placeholder'    =>    esc_html__('Enter post type plural label', 'cubewp-framework'),
                                'label'          =>    esc_html__('Plural Label', 'cubewp-framework'),
                                'tooltip'        =>    'Give a name for this Post Type. Enter Post type name with "s" at the end',
                                'required'       =>    true,
                            ));

                            echo apply_filters('cubewp/admin/post_type/text/field', '', array(
                                'id'             =>    'singular',
                                'name'           =>    'cwp[postType][singular]',
                                'value'          =>    $postType['singular'],
                                'placeholder'    =>    esc_html__('Enter post type singular label', 'cubewp-framework'),
                                'label'          =>    esc_html__('Singular Label', 'cubewp-framework'),
                                'tooltip'        =>    'Give a name for this Post Type. Enter Post type name without "s" at the end',
                                'required'       =>    true,
                            ));

                            echo apply_filters('cubewp/admin/post_type/text/field', '', array(
                                'id'             =>    'description',
                                'name'           =>    'cwp[postType][description]',
                                'value'          =>    $postType['description'],
                                'placeholder'    =>    esc_html__('Write description to identify this post type', 'cubewp-framework'),
                                'label'          =>    esc_html__('Description', 'cubewp-framework'),
                                'tooltip'        =>    'Give a Description for this Post Type. To understand what this post type is for',
                                'required'       =>    false,
                            ));

                            echo apply_filters('cubewp/admin/post_type/text/field', '', array(
                                'id'             =>    'icon',
                                'name'           =>    'cwp[postType][icon]',
                                'value'          =>    $postType['icon'],
                                'placeholder'    =>    esc_html__('Enter post type menu icon', 'cubewp-framework'),
                                'label'          =>    esc_html__('Menu Icon', 'cubewp-framework'),
                                'required'       =>    true,
                                'tooltip'        =>    'Select this post type icon for WordPress menu.',
                                'description'    =>    sprintf(
                                    esc_html__( 'Few quick picks for icon. For more click on more icons. %s', 'cubewp-framework' ),self::dashicons_list()
                                ),
                            ));

                            ?>

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    <?php
    }
        
        
    /**
     * page title
     * page title split for edit or add post type form. 
     * @since 1.0
     * @version 1.0
     */  
    private static function post_type_side_actions(array $postType) {
        ?>
        <div id="postbox-container-1" class="postbox-container">
        <div id="side-sortables" class="meta-box-sortables ui-sortable">
        <div class="postbox">
            <div class="postbox-header">
                <h2 class="hndle"><?php esc_html_e('Options with this Post Type', 'cubewp-framework'); ?></h2>
            </div>
            <div class="inside">
                <div class="main">
                    <table class="form-table">
                    <tbody>
                    <?php
                    $supports = array( 
                        'title'           => esc_html__('Title', 'cubewp-framework'), 
                        'editor'          => esc_html__('Editor', 'cubewp-framework'),
                        'thumbnail'       => esc_html__('Featured Image', 'cubewp-framework'),
                        'author'          => esc_html__('Author', 'cubewp-framework'),
                        'excerpt'         => esc_html__('Excerpt', 'cubewp-framework'),
                        'comments'        => esc_html__('Comments', 'cubewp-framework'),
                    );
                    $html = '<tr>
                        <td class="text-left">
                            <ul class="cwp-checkbox-outer margin-0">';
                                foreach($supports as $key => $val){
                                    $html .= '<li class="pull-left">';

                                        $input_attrs = array(
                                            'type'           =>    'checkbox',
                                            'id'             =>    'support-'. $key,
                                            'class'          =>    'form-checkbox checkbox',
                                            'name'           =>    'cwp[postType][supports][]',
                                            'value'          =>    $key,
                                            'wrap'           =>    false,
                                            'checked'        =>    (isset($postType['supports']) && in_array($key, $postType['supports'])) ? true : false,
                                        );
                                        $extra_attrs = '';
                                        if(isset($postType['supports']) && in_array($key, $postType['supports'])){
                                            $extra_attrs .= ' checked="checked"';
                                        }
                                        $input_attrs['extra_attrs'] = $extra_attrs;
                                        $html .= cwp_render_text_input($input_attrs);
                                        $html .= CubeWp_Admin::cwp_label( 'support-'. $key, $val );

                                    $html .= '</li>';
                                }    
                            $html .= '</ul>
                        </td>
                    </tr>';

                    echo cubewp_core_data($html);
                    ?>
                    </tbody>
                    </table>
                </div>
            </div>
        </div>
        </div>
        </div>
    <?php
    }
    
    /**
     * page title
     * page title split for edit or add post type form. 
     * @since 1.0
     * @version 1.0
     */  
    private static function _title() {
        if (isset($_GET['action']) && ('edit' == $_GET['action'] && !empty($_GET['postTypeid']))) {
            return '<h1>'. esc_html(__('Edit Post Type', 'cubewp-framework')) .'</h1>';
        } else {
            return '<h1>'. esc_html(__('Create New Post Type', 'cubewp-framework')) .'</h1>';

        }
    }
    
    /**
     * Save button
     * Button to save post type form which were created through CPT form edit. 
     * @since 1.0
     * @version 1.0
     */  
    private static function save_button() {
        if (isset($_GET['action']) && ('edit' == $_GET['action'] && !empty($_GET['postTypeid']))) {
            return '<input type="hidden" name="action" value="update_post_type"><input type="submit" class="cwp-save-button button button-primary button-large" name="Save" value="'. esc_html(__('Update', 'cubewp-framework')) .'" />';
        } else {
            return '<input type="hidden" name="action" value="save_post_type"><input type="submit" class="cwp-save-button button button-primary button-large" name="Save" value="'. esc_html(__('Save', 'cubewp-framework')) .'" />';
        }
    }
    
    private static function dashicons_list() {
        
        return '<div class="cwp-selectMenuIcons">
                    <span data-class="dashicons-admin-appearance" class="dashicons dashicons-admin-appearance"></span>
                    <span data-class="dashicons-admin-collapse" class="dashicons dashicons-admin-collapse"></span>
                    <span data-class="dashicons-admin-comments" class="dashicons dashicons-admin-comments"></span>
                    <span data-class="dashicons-admin-generic" class="dashicons dashicons-admin-generic"></span>
                    <span data-class="dashicons-admin-home" class="dashicons dashicons-admin-home"></span>
                    <span data-class="dashicons-admin-links"class="dashicons dashicons-admin-links"></span>
                    <span data-class="dashicons-admin-media" class="dashicons dashicons-admin-media"></span>
                    <span data-class="dashicons-admin-network" class="dashicons dashicons-admin-network"></span>
                    <span data-class="dashicons-admin-page" class="dashicons dashicons-admin-page"></span>
                    <span data-class="dashicons-admin-plugins" class="dashicons dashicons-admin-plugins"></span>
                    <span data-class="dashicons-admin-post" class="dashicons dashicons-admin-post"></span>
                    <a href="https://developer.wordpress.org/resource/dashicons" target="_blank" class="cwp-btnAddMoreIcons">'.esc_html__('More Icons','cubewp-framework').'</a>
                </div>';
    }
    

}
new CubeWp_Post_Types();