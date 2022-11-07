<?php
/**
 * CubeWp Custom Fields.
 *
 * @version 1.0
 * @package cubewp/cube/classes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
class CubeWp_Custom_Fields {
    
    const CFP = 'CubeWp_Posttype_Custom_Fields_Display';
    
    const CFPM = 'CubeWp_Metabox';
    
    const CFT = 'CubeWp_Taxonomy_Custom_Fields';
    
    const CFTM = 'CubeWp_Taxonomy_Metabox';
    
    const CFU = 'CubeWp_User_Custom_Fields_UI';
    
    const CFUM = 'CubeWp_User_Meta';

    public function __construct() {
        
        //For display custom fields of post types
        self::PostType_Fields();
        
        //Post type metabox
        self::PostType_Metabox();
        
        //Taxonomy custom fields
        self::Taxonomy_Fields();
        
        //Taxonomy metabox
        self::Taxonomy_Metabox();
        
        //USer custom fields
        self::User_Fields();
        
        //USer custom meta
        self::User_Meta();
        
    }
        
    /**
     * Method init
     *
     * @return void
     * @since  1.0.0
     */
    public static function init() {
        $CubeClass = __CLASS__;
        new $CubeClass;
    }
        
    /**
     * Method PostType_Fields
     *
     * @return void
     * @since  1.0.0
     */
    private function PostType_Fields(){
        
        add_action( 'custom_fields', array( self::CFP, 'cwp_custom_fields_run' ) );
        
        new CubeWp_Ajax( '',
            self::CFP,
            'process_poststype_add_field'
        );
        new CubeWp_Ajax( '',
            self::CFP,
            'cwp_add_custom_sub_field'
        );

        new CubeWp_Ajax( '',
            self::CFP,
            'cwp_duplicate_posttype_custom_field'
        );

        new CubeWp_Ajax( '',
            self::CFP,
            'cwp_get_taxonomies_by_post_types'
        );
    }
        
    /**
     * Method PostType_Metabox
     *
     * @return void
     * @since  1.0.0
     */
    private function PostType_Metabox(){

        add_action( 'add_meta_boxes', array(self::CFPM, 'get_current_meta') );
        add_action( 'save_post', array(self::CFPM, 'save_metaboxes'));

    }
        
    /**
     * Method Taxonomy_Fields
     *
     * @return void
     * @since  1.0.0
     */
    private function Taxonomy_Fields(){
        
        add_action('taxonomy_custom_fields', array(self::CFT, 'manage_taxonomy_custom_fields'));
        
    }
        
    /**
     * Method Taxonomy_Metabox
     *
     * @return void
     * @since  1.0.0
     */
    private function Taxonomy_Metabox(){

        $tax_custom_fields = CWP()->get_custom_fields( 'taxonomy' );
        if(isset($tax_custom_fields) && !empty($tax_custom_fields)){
            foreach($tax_custom_fields as $taxonomy => $fields ){
                add_action( $taxonomy.'_add_form_fields', array(self::CFTM, 'cwp_show_taxonomy_metaboxes'), 10, 2 );
                add_action( $taxonomy.'_edit_form_fields', array(self::CFTM, 'cwp_show_taxonomy_metaboxes'), 10, 2 );
                add_action( 'edit_'.$taxonomy, array(self::CFTM, 'cwp_save_taxonomy_custom_fields'), 10, 2 );
                add_action( 'create_'.$taxonomy, array(self::CFTM, 'cwp_save_taxonomy_custom_fields'), 10, 2 );
            }
        }

    }
        
    /**
     * Method User_Fields
     *
     * @return void
     * @since  1.0.0
     */
    private function User_Fields(){

        add_action('user_custom_fields', array(self::CFU, 'manage_user_fields'), 30);
        
        new CubeWp_Ajax( '',
            self::CFU,
            'cwp_add_user_custom_field'
        );

        new CubeWp_Ajax( '',
            self::CFU,
            'cwp_duplicate_user_custom_field'
        );
        
        new CubeWp_Ajax( '',
            self::CFU,
            'cwp_add_user_custom_sub_field'
        );

    }
        
    /**
     * Method User_Meta
     *
     * @return void
     * @since  1.0.0
     */
    private function User_Meta(){

        add_action('show_user_profile', array(self::CFUM, 'cwp_user_profile_fields'));
        add_action('edit_user_profile', array(self::CFUM, 'cwp_user_profile_fields'));
        add_action('user_new_form', array(self::CFUM, 'cwp_user_profile_fields'));
        
        add_action('user_register', array(self::CFUM, 'cwp_save_user_fields'));
	    add_action('profile_update', array(self::CFUM, 'cwp_save_user_fields'));
    }
    
}