<?php
/**
 * display fields of custom fields.
 *
 * @version 1.0
 * @package cubewp/cube/modules/post-types
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * CubeWp_Posttype_Custom_Fields
 */
class CubeWp_Posttype_Custom_Fields {
    
    /**
     * Method get_fields
     * @param array $fields
     * @param array $sub_fields 
     * @return array
     * @since  1.0.0
     */
    protected static function get_fields($fields = array(), $sub_fields = array()) {
        if (!$fields) {
            return;
        }
        
        $fields       = explode(",", $fields);
        $fieldOptions = CWP()->get_custom_fields( 'post_types' );

        $html = '';
        $counter = 1;
        foreach ($fields as $field) {
            $SingleFieldOption = $fieldOptions[$field];
            $SingleFieldOption['sub_fields'] = $sub_fields;
            $SingleFieldOption['counter'] = $counter;
            $counter++;
            $html .= CubeWp_Posttype_Custom_Fields_Display::add_new_field($SingleFieldOption);
        }
        return $html;
    }

    protected static function get_duplicate_field($field = '') {
        if (!$field) {
            return;
        }
        
        $fieldOptions = CWP()->get_custom_fields( 'post_types' );
        $html = '';
        if(!empty($field)){
            $SingleFieldOption = $fieldOptions[$field];
            if(isset($SingleFieldOption['sub_fields'])){
                $SingleFieldOption['sub_fields'] = json_encode(array($SingleFieldOption['name']=>explode(",",$SingleFieldOption['sub_fields'])));
            }
            $SingleFieldOption['label'] = $SingleFieldOption['label'].' - copy';
            $html .= CubeWp_Posttype_Custom_Fields_Display::add_new_field($SingleFieldOption);
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
    protected static function get_sub_fields($sub_fields = array(), $parent_field = '') {
        if (!$sub_fields) {
            return;
        }
        
        $sub_fields    = json_decode($sub_fields, true);
        $fieldOptions  = CWP()->get_custom_fields( 'post_types' );
        
        $html = '';
        if(isset($sub_fields[$parent_field]) && !empty($sub_fields[$parent_field])){
            foreach ($sub_fields[$parent_field] as $sub_field) {
                $SingleFieldOption          = $fieldOptions[$sub_field];
                $html .= CubeWp_Posttype_Custom_Fields_Display::add_new_sub_field($SingleFieldOption, $parent_field);
            }
        }
        return $html;
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
     * @param string $fieldID [explicite description]
     *
     * @return array
     * @since  1.0.0
     */
    public function get_field_options($fieldID) {
        if (!$fieldID) {
            return;
        }
        $fieldOptions = CWP()->get_custom_fields( 'post_types' );
        $SingleFieldOptions = isset($fieldOptions[$fieldID]) ? $fieldOptions[$fieldID] : array();
        return $SingleFieldOptions;
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
        }
        
        $field_names  = $sub_field_names = array();
        if (isset($_POST['cwp']['fields'])) {
            $fields = CubeWp_Sanitize_Custom_Fields($_POST['cwp']['fields'],'post_types');

            foreach ($fields as $field) {
                $field['group_id'] = $post_id;
                if(isset($field['options']) && !empty($field['options']) && is_array($field['options'])){
                    $field['options'] = json_encode(array_filter($field['options']));
                }
                if(isset($field['default_option']) && !empty($field['default_option'])){
                    $field['default_value'] = $field['default_option'];
                }
                
                if(isset($_POST['cwp']['sub_fields'][$field['name']]) && $field['type'] == 'repeating_field'){
                    
                    $sub_fields_data = CubeWp_Sanitize_Custom_Fields($_POST['cwp']['sub_fields'][$field['name']],'post_types');
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
                    $mapMeta = $field['name'];
                    if(!empty($groupTypes)){
                        $groupTypes = explode(',',$groupTypes);
                        $mapMeta = array();
                        foreach($groupTypes as $type){
                            $mapMeta[$type] = $field['name'];
                            self::set_option('cwp_map_meta', $mapMeta);
                        }
                    }
                }
                
                $field_names[] = $field['name'];
                self::set_option($field['name'], $field);
            }
        }
        
        if (!empty($post_id)) {
            $group_fields = get_post_meta($post_id, '_cwp_group_fields', true);
            if(isset($group_fields) && !empty($group_fields)){
                $group_fields_arr = explode(',', $group_fields);
                self::delete_options( $group_fields_arr, $field_names);
            }
            
            if(isset($field_names) && !empty($field_names)){
                new CubeWp_Update_Frontend_Forms(array('group_id'=>$post_id,'existing_fields'=>$field_names));
                $field_names = implode(",", $field_names);            
                update_post_meta($post_id, '_cwp_group_fields', $field_names);
            }else{
                new CubeWp_Update_Frontend_Forms(array('group_id'=>$post_id));
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

            if(isset($sub_field_names) && !empty($sub_field_names)){
                update_post_meta($post_id, '_cwp_group_sub_fields', json_encode($sub_field_names) );
            }else{
                delete_post_meta($post_id, '_cwp_group_sub_fields');
            }
            
            wp_redirect( CubeWp_Submenu::_page_action('custom-fields') );
            
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
            $options = CWP()->get_custom_fields( 'post_types' );
            $options = $options == '' ? array() : $options;
            $options[$name] = $val;
            return CWP()->update_custom_fields( 'post_types', $options );
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
        $options = CWP()->get_custom_fields( 'post_types' );
        if(isset($group_fields) && !empty($group_fields)){
            foreach($group_fields as $group_field){
                if( (isset($new_fields) && is_array($new_fields) && !in_array($group_field, $new_fields)) || empty($new_fields) ){
                    unset($options[$group_field]);
                }
            }
        }
        CWP()->update_custom_fields( 'post_types', $options );
    }
    
    /**
     * Method get_group
     *
     * @return array
     * @since  1.0.0
     */
    protected static function get_group() {
        if (isset($_GET['action']) && ('edit' == $_GET['action'] && !empty($_GET['groupid']))) {
            $GroupID = sanitize_text_field($_GET['groupid']);
            $GroupData = get_post($GroupID);
            if (is_null($GroupData)) {
                echo "Group does not exists or was deleted";
                exit;
            }
            $group['id']              = $GroupID;
            $group['fields']          = get_post_meta($GroupID, '_cwp_group_fields', true);
            $group['sub_fields']      = get_post_meta($GroupID, '_cwp_group_sub_fields', true);
            $group['types']           = get_post_meta($GroupID, '_cwp_group_types');
            $group['terms']           = get_post_meta($GroupID, '_cwp_group_terms');
            $group['user_role']       = get_post_meta($GroupID, '_cwp_group_user_role', true);
            $group['order']           = get_post_meta($GroupID, '_cwp_group_order', true);
            $group['name']            = $GroupData->post_title;
            $group['description']     = $GroupData->post_content;
            return $group;
        }
    }
    
}