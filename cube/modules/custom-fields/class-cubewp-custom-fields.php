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
    
    const PROCESSOR = 'CubeWp_Custom_Fields_Processor';

    const CFP = 'CubeWp_Posttype_Custom_Fields_Display';
    
    const CFPM = 'CubeWp_Metabox';
    
    const CFT = 'CubeWp_Taxonomy_Custom_Fields';
    
    const CFTM = 'CubeWp_Taxonomy_Metabox';
    
    const CFU = 'CubeWp_User_Custom_Fields_UI';
    
    const CFUM = 'CubeWp_User_Meta';

    const CSF = 'CubeWp_Settings_Custom_Fields_Display';

    public function __construct() {

        //For display custom fields of post types
        self::PostType_Fields();
        
        //Post type metabox
        self::PostType_Metabox();
        
        //Taxonomy custom fields
        add_action('taxonomy_custom_fields', array(self::CFT, 'manage_taxonomy_custom_fields'));
        
        //Taxonomy metabox
        self::Taxonomy_Metabox();
        
        //USer custom fields
        add_action('user_custom_fields', array(self::CFU, 'manage_user_fields'), 30);
        
        //USer custom meta
        self::User_Meta();

        //For display custom fields of Settings
        add_action( 'settings_custom_fields', array( self::CSF, 'cwp_custom_fields_run' ) );

        new CubeWp_Ajax( '',
            self::PROCESSOR,
            'process_add_field'
        );
        new CubeWp_Ajax( '',
            self::PROCESSOR,
            'process_sub_field'
        );

        new CubeWp_Ajax( '',
            self::PROCESSOR,
            'process_duplicate_field'
        );
        
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

    /**
     * Method cubewp_user_metas
     *
     * @param int $postid
     *
     * @return array
     * @since  1.0.0
     */
    public static function cubewp_user_metas($userid = 0, $rest_data = false) {
        $user_role = $userid != 0 ? cwp_get_user_roles_by_id($userid) : cwp_get_current_user_roles();
		$args = array();
        $groups = cwp_get_groups_by_user_role($user_role);
        if (is_array($groups)){
            foreach ($groups as $group) {
                $fields = cwp_get_user_fields_by_group_id( $group );
                if (!empty($fields) && is_array($fields)) {
                    foreach ($fields as $field) {
                        $field = get_user_field_options($field);
                        $field_rest_permission = $rest_data == true && isset( $field["rest_api"] ) ? $field["rest_api"] : 0;
                        if($field_rest_permission || $rest_data == false){
                            $field_type = isset( $field["type"] ) ? $field["type"] : "";
                            $meta_key   = isset( $field["name"] ) ? $field["name"] : "";
                            if (empty($field_type) || empty($meta_key)) continue;
                            $label = isset( $field["label"] ) ? $field["label"] : "";
                            $value = self::get_single_meta_value($meta_key,$field_type, $userid);
                            $args[$meta_key] = array(
                                'type'                  =>    $field_type,
                                'meta_key'              =>    $meta_key,
                                'meta_value'            =>    $value,
                                'label'                 =>    $label,
                            );
                        }
                    }
                }
            }
        }
        return $args;
    }

    /**
     * Method get_single_meta_value
     *
     * @param string $meta_key
     * @param string $field_type
     *
     * @return array
     * @since  1.0.0
     */
    public static function get_single_meta_value($meta_key='',$field_type='', $userid = 0){
        $userid = $userid != 0 ? $userid : get_current_user_id();
        if($field_type == 'repeating_field'){
            $values = get_user_meta( $userid, $meta_key, true );
            if (is_array($values)) {
                $value  = self::get_repeating_Fields_value($values);
            }
        }elseif($field_type == 'dropdown' || $field_type == 'radio' || $field_type == 'checkbox'){
            $value = get_user_meta( $userid, $meta_key, true );
            $value = render_multi_value($meta_key, $value, 'user');
        }else{
            $value = get_user_meta( $userid, $meta_key, true );
            if ("google_address" === $field_type) {
                $lat = get_user_meta( $userid, $meta_key. '_lat', true );
                $lng = get_user_meta( $userid, $meta_key. '_lng', true );
                $value = array(
                    'address' => $value,
                    'lat' => $lat,
                    'lng' => $lng
                );
            }
        }
        if(empty($value)){
            return '';
        }
        return $value;
    }
    	
	/**
	 * Method get_repeating_Fields_value
	 *
	 * @param array $values
	 *
	 * @return array
     * @since  1.0.0
	 */
	private static function get_repeating_Fields_value($values) {
        $value = array();
        if(isset($values) && !empty($values)){
            for($i = 0; $i < count($values); $i++){
                
                foreach ($values[$i] as $k=> $subValue) {
                    $options = get_user_field_options($k);
                    if(count($options)>0){
                        $field_type = $options['type'];
                        $field_label = $options['label'];
                        if ("google_address" === $field_type) {
                            $lat = $values[$i][$k. '_lat'];
                            $lng = $values[$i][$k. '_lng'];
                            
                            $subValue = array(
                                'address' => $subValue,
                                'lat' => $lat,
                                'lng' => $lng
                            );
                        }
                        $value[$i][$k] = array(
                            'type'                  =>    $field_type,
                            'container_class'       =>    "",
                            'label'                 =>    $field_label,
                            'value'                 =>    $subValue,
                        );
                    }
                }
            }
            return $value;
        }
    }

}