<?php
/**
 * It process the custom fields output and database storage.
 *
 * @version 1.0
 * @package cubewp/cube/classes
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * CubeWp_Custom_Fields_Processor
 */
class CubeWp_Custom_Fields_Processor {
    
    /**
     * Method get_fields
     * @param array $fields
     * @param array $sub_fields 
     * @return array
     * @since  1.0.0
     */
    public static function get_fields($fields = array(), $sub_fields = array()) {
        if (!$fields) {
            return;
        }
        $html = '';
        $counter = 1;
        if(isJson($fields)){
            $fields = json_decode($fields, true);
        }else{
            $fields = explode(",", $fields);
        }
        $fieldOptions = self::get_field_options();
        foreach ($fields as $field) {
            if($field){
                $SingleFieldOption = $fieldOptions[$field];
                $SingleFieldOption['sub_fields'] = $sub_fields;
                $SingleFieldOption['counter'] = $counter;
                $SingleFieldOption['fields_type'] = self::get_field_option_name();
                $counter++;
                $html .= CubeWp_Custom_Fields_Markup::add_new_field($SingleFieldOption);
            }
        }
        return $html;
    }

    protected static function get_duplicate_field($field = '',$fields_type = '') {
        if (!$field) {
            return;
        }
        
        $fieldOptions = CWP()->get_custom_fields( $fields_type );
        $html = '';
        if(!empty($field)){
            $SingleFieldOption = $fieldOptions[$field];
            if(isset($SingleFieldOption['sub_fields'])){
                $SingleFieldOption['sub_fields'] = json_encode(array($SingleFieldOption['name']=>explode(",",$SingleFieldOption['sub_fields'])));
            }
            $SingleFieldOption['label'] = $SingleFieldOption['label'].' - copy';
            $SingleFieldOption['fields_type'] = $fields_type;
            $html .= CubeWp_Custom_Fields_Markup::add_new_field($SingleFieldOption);
        }
        return $html;
    }
        
    /**
     * Method get_sub_fields
     *
     * @param string $parent_field 
     * @param array $sub_fields 
     *
     * @return string html
     * @since  1.0.0
     */
    public static function get_sub_fields($sub_fields = array(), $parent_field = '', $fields_type = '') {
        if (!$sub_fields) {
            return;
        }
        $type = empty($fields_type) ? self::get_field_option_name() : $fields_type;
        $html = '';
        $sub_fields    = json_decode($sub_fields, true);
        $fieldOptions = CWP()->get_custom_fields( $type );
        $counter = 1;
        if(isset($sub_fields[$parent_field]) && !empty($sub_fields[$parent_field])){
            foreach ($sub_fields[$parent_field] as $sub_field) {
                $SingleFieldOption            = $fieldOptions[$sub_field];
                $SingleFieldOption['counter'] = $counter;
                $html .= CubeWp_Custom_Fields_Markup::add_new_sub_field($SingleFieldOption, $parent_field);
                $counter++;
            }
        }
        return $html;
    }

    /**
     * Method process_add_field
     *
     * @return object
     * @since  1.0.0
     */
    public static function process_add_field() {
        $field_options = [];
        check_ajax_referer( 'cubewp_custom_fields_nonce', 'nonce' );
        if( true ){
            /* TO identify the custom fields type, eg: post-types, user, or any other custom fields type
            *  like for cubewp forms custom-form.
            */
            $field_options['fields_type'] = sanitize_text_field($_POST['fields_type']);
            wp_send_json_success(CubeWp_Custom_Fields_Markup::add_new_field($field_options));
        }else{
            wp_send_json_error( array( 'error' => $custom_error ) );
        }
    }
        
    /**
     * Method cwp_add_custom_sub_field
     *
     * @return object
     * @since  1.0.0
     */
    public static function process_sub_field(){
        check_ajax_referer( 'cubewp_custom_fields_nonce', 'nonce' );
        if( true ){
            wp_send_json_success(CubeWp_Custom_Fields_Markup::add_new_sub_field(array(), sanitize_text_field($_POST['parent_field'])));
        }else{
            wp_send_json_error( array( 'error' => $custom_error ) );
        }
    }

    /**
     * Method cwp_duplicate_posttype_custom_field
     *
     * @return object
     * @since  1.0.0
     */
    public static function process_duplicate_field(){
        check_ajax_referer( 'cubewp_custom_fields_nonce', 'nonce' );
        if( true ){
            wp_send_json_success(self::get_duplicate_field($_POST['field_id'], sanitize_text_field($_POST['fields_type'])));
        }else{
            wp_send_json_error( array( 'error' => $custom_error ) );
        }
    }
    
    /**
     * Method get_fields_by_group
     *
     * @param int $GroupID
     *
     * @return array
     * @since  1.0.0
     */
    public function get_fields_by_group($GroupID) {
        if (!$GroupID) {
            return;
        }
        $fields_of_specific_group = get_post_meta($GroupID, '_cwp_group_fields', true);
        $fields_of_specific_group = explode(",", $fields_of_specific_group);
        return $fields_of_specific_group;
    }
        
    /**
     * Method get_sub_fields_by_group
     *
     * @param int $GroupID
     *
     * @return array
     * @since  1.0.0
     */
    public function get_sub_fields_by_group($GroupID) {
        if (!$GroupID) {
            return;
        }
        $sub_fields_of_specific_group = get_post_meta($GroupID, '_cwp_group_sub_fields', true);
        if(isset($sub_fields_of_specific_group) && !empty($sub_fields_of_specific_group)){
            $sub_fields_of_specific_group = json_decode($sub_fields_of_specific_group, true);
        }
        return $sub_fields_of_specific_group;
    }
    
    /**
     * Method get_field_options
     *
     * @return array
     * @since  1.0.0
     */
    public static function get_field_options() {
        $options = CWP()->get_custom_fields( self::get_field_option_name() );
        return $options;
    }

    /**
     * Method get_field_option_name
     *
     * @return array
     * @since  1.0.0
     */
    public static function get_field_option_name() {
        return apply_filters( 'cubewp/custom_fields/type', 'post_types'  );
    }
    
    /**
     * Method add_new_field_btn
     *
     * @return string html
     * @since  1.0.0
     */
    protected static function add_new_field_btn() {
        echo '<a class="button button-primary button-large" href="javascript:void(0);" id="cwp-add-new-field-btn" data-fields_type=' . self::get_field_option_name().'>'. __('Add New Field', 'cubewp-framework') .'</a>';
    }

    /**
     * Method save_group
     *
     * @return void
     * @since  1.0.0
     */
    protected static function save_group() {
        
        if (isset($_POST['cwp']['group'])) {

            $groupID         = sanitize_text_field($_POST['cwp']['group']['id']);
            $groupName       = sanitize_text_field($_POST['cwp']['group']['name']);
            $groupDesc       = wp_strip_all_tags( wp_unslash( $_POST['cwp']['group']['description'] ));
            $groupOrder      = isset($_POST['cwp']['group']['order']) ? sanitize_text_field($_POST['cwp']['group']['order']) : 0;
            $groupTypes      = isset($_POST['cwp']['group']['types']) ? CubeWp_Sanitize_text_Array($_POST['cwp']['group']['types']) : array();
            $groupTerms      = isset($_POST['cwp']['group']['terms']) ? CubeWp_Sanitize_text_Array($_POST['cwp']['group']['terms']) : array();

            if (!empty($groupName)) {
                if (isset($_POST['cwp_save_group'])) {
                    $post_id = wp_insert_post(array(
                        'post_type' => 'cwp_form_fields',
                        'post_title' => $groupName,
                        'post_content' => $groupDesc,
                        'post_status' => 'publish',
                    ));
                } else if (isset($_POST['cwp_edit_group']) && !empty($groupID)) {
                    wp_update_post(array(
                        'ID' => $groupID,
                        'post_title' => $groupName,
                        'post_content' => $groupDesc,
                    ));
                    $post_id = $groupID;
                }
                
                if(isset($groupOrder) && is_numeric($groupOrder) && $groupOrder > 0){
                    update_post_meta($post_id, '_cwp_group_order', $groupOrder);
                }else{
                    update_post_meta($post_id, '_cwp_group_order', 0);
                }
                
                if (!empty($post_id) && !empty($groupTypes)) {
                    $groupTypes = implode(",", $groupTypes);
                    update_post_meta($post_id, '_cwp_group_types', $groupTypes);
                }else{
                    update_post_meta($post_id, '_cwp_group_types', '');
                }
                if (!empty($post_id) && !empty($groupTerms)) {
                    $groupTerms = implode(",", $groupTerms);
                    update_post_meta($post_id, '_cwp_group_terms', $groupTerms);
                }else{
                    delete_post_meta($post_id, '_cwp_group_terms');
                }
            }
            self::save_custom_fields($_POST['cwp'],$post_id,'post_types');
        
            if (!empty($post_id) ) {
                wp_redirect( CubeWp_Submenu::_page_action('custom-fields') );
            }
        }
        
    }

    /**
     * Method save_custom_fields
     *
     * @param array $fields
     * @param int $post_id
     * @param string $form_type
     *
     * @since  1.0.0
     */
    public static function save_custom_fields($POST = array(), $post_id = '', $form_type = ''){
        if (empty($post_id) ) return;

        $field_names  = $sub_field_names = array();
        if (isset($POST['fields'])) {
            $fields = CubeWp_Sanitize_Custom_Fields($POST['fields'],$form_type);
            foreach ($fields as $field) {
                if(!empty( $field ) && isset($field['name'])){
                    $field['group_id'] = $post_id;
                    if(isset($field['options']) && !empty($field['options']) && is_array($field['options'])){
                        $field['options'] = json_encode(array_filter($field['options']));
                    }
                    if(isset($field['default_option']) && !empty($field['default_option'])){
                        $field['default_value'] = $field['default_option'];
                    }
                    if(isset($POST['sub_fields'][$field['name']]) && $field['type'] == 'repeating_field'){
                        $sub_fields_data = CubeWp_Sanitize_Custom_Fields($POST['sub_fields'][$field['name']],$form_type);
                        $sub_fields = array();
                        foreach ($sub_fields_data as $sub_field){
                            $sub_field['group_id'] = $post_id;
                            if(isset($sub_field['options']) && !empty($sub_field['options']) && is_array($sub_field['options'])){
                                $sub_field['options'] = json_encode(array_filter($sub_field['options']));
                            }
                            if(isset($sub_field['default_option']) && !empty($sub_field['default_option'])){
                                $sub_field['default_value'] = $sub_field['default_option'];
                            }
                            
                            $sub_field_names[$field['name']][] = $sub_field['name'];
                            self::set_option($sub_field['name'], $sub_field);
                            $sub_fields[] = $sub_field['name'];
                        }
                        $field['sub_fields'] = implode(',', $sub_fields);
                    }
                    if($field['type'] == 'google_address' && isset($field['map_use']) && $field['map_use'] == '1'){
                        $cwp_map_meta = array();
                        $options = self::get_field_options();
                        $options = $options == '' ? array() : $options;
                        if(!empty($options) && count($options) > 0){
                            $cwp_map_meta = $options['cwp_map_meta'];
                        }

                        $groupTypes = get_post_meta($post_id, '_cwp_group_types', true);
                        if(!empty($groupTypes)){
                            $groupTypes = explode(',',$groupTypes);
                            foreach($groupTypes as $type){
                                $cwp_map_meta[$type] = $field['name'];
                                self::set_option('cwp_map_meta', $cwp_map_meta);
                            }
                        }
                    }
                    $field_names[] = $field['name'];
                    self::set_option($field['name'], $field);
                }
            }
        }
        
        if (!empty($post_id) ) {
            
            $group_fields = get_post_meta($post_id, '_cwp_group_fields', true);
            if(isset($group_fields) && !empty($group_fields)){
                if(isJson($group_fields)){
                    $group_fields_arr = json_decode($group_fields, true);
                }else{
                    $group_fields_arr = explode(",", $group_fields);
                }
                self::delete_options( $group_fields_arr, $field_names);
            }
            if(isset($field_names) && !empty($field_names)){
                if(!empty($form_type)){
                    new CubeWp_Update_Frontend_Forms(array('group_id'=>$post_id,'existing_fields'=>$field_names,'form_type'=>$form_type));
                }
                if($form_type == 'user'){
                    $field_names = json_encode($field_names);
                }else{
                    $field_names = implode(",", $field_names);
                }
                update_post_meta($post_id, '_cwp_group_fields', $field_names);
            }else{
                if(!empty($form_type)){
                    new CubeWp_Update_Frontend_Forms(array('group_id'=>$post_id,'form_type'=>$form_type));
                }
                delete_post_meta($post_id, '_cwp_group_fields');
            }
            
            $group_sub_fields = get_post_meta($post_id, '_cwp_group_sub_fields', true);
            if(isset($group_sub_fields) && !empty($group_sub_fields)){
                $group_sub_fields_arr = json_decode($group_sub_fields, true);
                if(isset($group_sub_fields_arr) && !empty($group_sub_fields_arr)){
                    foreach($group_sub_fields_arr as $sub_field_key => $sub_fields){
                        $sub_field_names_arr = isset($sub_field_names[$sub_field_key]) ? $sub_field_names[$sub_field_key] : array();
                        self::delete_options( $sub_fields, $sub_field_names_arr);
                    }
                }
            }
            
            if (isset($sub_field_names) && !empty($sub_field_names)) {
                update_post_meta($post_id, '_cwp_group_sub_fields', json_encode($sub_field_names) );
            }else{
                delete_post_meta($post_id, '_cwp_group_sub_fields');
            }
        }
    }
    
    /**
     * Method set_option
     *
     * @param string $name
     * @param string/array $val
     *
     * @return array
     * @since  1.0.0
     */
    public static function set_option($name, $val) {
        if ($name) {
            $options = self::get_field_options();
            $options = $options == '' ? array() : $options;
            $options[$name] = $val;
            return CWP()->update_custom_fields( self::get_field_option_name(), $options );
        } else {
            return false;
        }
    }
    
    /**
     * Method delete_options
     *
     * @param string $new_fields
     * @param string/array $group_fields
     *
     * @return void updating fields
     * @since  1.0.0
     */
    private static function delete_options( $group_fields = array(), $new_fields = array()){
        $options = self::get_field_options();
        if(isset($group_fields) && !empty($group_fields)){
            foreach($group_fields as $group_field){
                if( (isset($new_fields) && is_array($new_fields) && !in_array($group_field, $new_fields)) || empty($new_fields) ){
                    unset($options[$group_field]);
                }
            }
        }
        CWP()->update_custom_fields( self::get_field_option_name(), $options );
    }
    
    /**
     * Method get_group
     *
     * @return array
     * @since  1.0.0
     */
    public static function get_group($GroupID = 0) {
        if (isset($_GET['action']) && ('edit' == $_GET['action'] && !empty($_GET['groupid']))) {
            $GroupID = sanitize_text_field($_GET['groupid']);
        }
        if($GroupID == 0) return;

        $GroupData = get_post($GroupID);
        if (is_null($GroupData)) {
            echo "This data does not exists or was deleted";
            exit;
        }
        $group['id']              = $GroupID;
        $group['slug']            = $GroupData->post_name;
        $group['name']            = $GroupData->post_title;
        $group['description']     = $GroupData->post_content;
        $group['fields']          = get_post_meta($GroupID, '_cwp_group_fields', true);
        $group['sub_fields']      = get_post_meta($GroupID, '_cwp_group_sub_fields', true);
        $group['types']           = get_post_meta($GroupID, '_cwp_group_types');
        $group['terms']           = get_post_meta($GroupID, '_cwp_group_terms');
        $group['user_roles']      = get_post_meta($GroupID, '_cwp_group_user_roles', true);
        $group['order']           = get_post_meta($GroupID, '_cwp_group_order', true);
        return apply_filters( 'cubewp/custom-fields/group/data', $group );
    }
    
}