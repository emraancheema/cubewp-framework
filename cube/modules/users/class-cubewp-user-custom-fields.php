<?php
class CubeWp_User_Custom_Fields{
    
    public static function get_group() {
        if (isset($_GET['action']) && ('edit' == $_GET['action'] && !empty($_GET['groupid']))) {
            
            $GroupID    = isset($_GET['groupid']) ? sanitize_text_field($_GET['groupid']) : 0;
            $GroupData  = get_post($GroupID);
            if (isset($GroupData) && !empty($GroupData) && $GroupData->post_type != 'cwp_user_fields') {
                printf(esc_html("post %s does not exists or was deleted", "cubewp-framework"), $GroupID);
                exit;
            }
            $group['id']              = $GroupID;
            $group['fields']          = get_post_meta($GroupID, '_cwp_group_fields', true);
            $group['sub_fields']      = get_post_meta($GroupID, '_cwp_group_sub_fields', true);
            $group['user_roles']      = get_post_meta($GroupID, '_cwp_group_user_roles', true);
            $group['order']           = get_post_meta($GroupID, '_cwp_group_order', true);
            $group['name']            = $GroupData->post_title;
            $group['description']     = $GroupData->post_content;
            return $group;
        }
    }
    
    protected static function get_fields($fields = array(), $sub_fields = array()) {
        if (!$fields) {
            return;
        }
        
        $fields       = json_decode($fields, true);
        $fieldOptions = CWP()->get_custom_fields( 'user' );
        $html = '';
        $counter = 1;
        if(!empty($fields)){
            foreach ($fields as $field) {
                $SingleFieldOption = $fieldOptions[$field];
                $SingleFieldOption['sub_fields'] = $sub_fields;
                $SingleFieldOption['counter'] = $counter;
                $counter++;
                $html .= CubeWp_User_Custom_Fields_UI::add_new_field($SingleFieldOption);
            }
        }
        return $html;
    }

    protected static function get_duplicate_field($field = '') {
        if (!$field) {
            return;
        }
        
        $fieldOptions = CWP()->get_custom_fields( 'user' );
        $html = '';
        if(!empty($field)){
            $SingleFieldOption = $fieldOptions[$field];
            if(isset($SingleFieldOption['sub_fields'])){
                $SingleFieldOption['sub_fields'] = json_encode(array($SingleFieldOption['name']=>explode(",",$SingleFieldOption['sub_fields'])));
            }
            $SingleFieldOption['label'] = $SingleFieldOption['label'].' - copy';
            $html .= CubeWp_User_Custom_Fields_UI::add_new_field($SingleFieldOption);
        }
        return $html;
    }
    
    protected static function get_sub_fields($sub_fields = array(), $parent_field = '') {
        if (!$sub_fields) {
            return;
        }
        
        $sub_fields    = json_decode($sub_fields, true);
        $fieldOptions  = CWP()->get_custom_fields( 'user' );
        
        $html = '';
        if(isset($sub_fields[$parent_field]) && !empty($sub_fields[$parent_field])){
            foreach ($sub_fields[$parent_field] as $sub_field) {
                $SingleFieldOption          = $fieldOptions[$sub_field];
                
                $html .= CubeWp_User_Custom_Fields_UI::add_new_sub_field($SingleFieldOption, $parent_field);
            }
        }
        return $html;
    }
    
    protected static function cwp_form_field_types() {
        
        $field_types                        = array();
        $field_types['text']                = esc_html__('Text', 'cubewp-framework');
        $field_types['number']              = esc_html__('Number', 'cubewp-framework');
        $field_types['email']               = esc_html__('Email', 'cubewp-framework');
        $field_types['url']                 = esc_html__('URL', 'cubewp-framework');
        $field_types['password']            = esc_html__('Password', 'cubewp-framework');
        $field_types['textarea']            = esc_html__('Textarea', 'cubewp-framework');
        $field_types['wysiwyg_editor']      = esc_html__('Wysiwyg Editor', 'cubewp-framework');
        $field_types['oembed']              = esc_html__('oEmbed', 'cubewp-framework');
        $field_types['file']                = esc_html__('File', 'cubewp-framework');
        $field_types['image']               = esc_html__('Image', 'cubewp-framework');
        $field_types['gallery']             = esc_html__('Gallery', 'cubewp-framework');
        $field_types['color']               = esc_html__('Color', 'cubewp-framework');
        $field_types['range']               = esc_html__('Range', 'cubewp-framework');
        $field_types['switch']              = esc_html__('Switch', 'cubewp-framework');
        $field_types['dropdown']            = esc_html__('Dropdown', 'cubewp-framework');
        $field_types['checkbox']            = esc_html__('Checkbox', 'cubewp-framework');
        $field_types['radio']               = esc_html__('Radio Button', 'cubewp-framework');
        $field_types['google_address']      = esc_html__('Google Address', 'cubewp-framework');
        $field_types['date_picker']         = esc_html__('Date Picker', 'cubewp-framework');
        $field_types['date_time_picker']    = esc_html__('Date Time Picker', 'cubewp-framework');
        $field_types['time_picker']         = esc_html__('Time Picker', 'cubewp-framework');
        $field_types['post']                = esc_html__('Post', 'cubewp-framework');
        $field_types['taxonomy']            = esc_html__('Taxonomy', 'cubewp-framework');
        $field_types['repeating_field']     = esc_html__('Repeating Field', 'cubewp-framework');
        
        return apply_filters('cwp_user_custom_field_types', $field_types);
    }
    
    public static function cwp_form_sub_field_types() {
        
        $field_types                   = array();
        $field_types['text']           = esc_html__('Text', 'cubewp-framework');
        $field_types['number']         = esc_html__('Number', 'cubewp-framework');
        $field_types['email']          = esc_html__('Email', 'cubewp-framework');
        $field_types['url']            = esc_html__('URL', 'cubewp-framework');
        $field_types['textarea']       = esc_html__('Textarea', 'cubewp-framework');
        $field_types['file']           = esc_html__('File', 'cubewp-framework');
        $field_types['color']          = esc_html__('Color', 'cubewp-framework');
        $field_types['range']          = esc_html__('Range', 'cubewp-framework');
        $field_types['switch']         = esc_html__('Switch', 'cubewp-framework');
        $field_types['dropdown']       = esc_html__('Dropdown', 'cubewp-framework');
        $field_types['checkbox']       = esc_html__('Checkbox', 'cubewp-framework');
        $field_types['radio']          = esc_html__('Radio Button', 'cubewp-framework');
        $field_types['date_picker']    = esc_html__('Date Picker', 'cubewp-framework');
        $field_types['time_picker']    = esc_html__('Time Picker', 'cubewp-framework');
        $field_types['google_address'] = esc_html__('Google Address', 'cubewp-framework');
        
        return apply_filters('cwp_user_custom_sub_field_types', $field_types);
    }
    
    protected static function _get_user_roles($get_user_roles) {
        $user_roles        = cwp_get_user_roles();
     
        $get_user_roles    = isset($get_user_roles) && !empty($get_user_roles) ? explode(',', $get_user_roles) : array();
     
        $html = '';
        if(isset($user_roles) && !empty($user_roles)){
            foreach($user_roles as $key => $user_role){
                $checked = '';
                if(isset($get_user_roles) && in_array($key, $get_user_roles)){
                    $checked = ' checked="checked"';
                }
                $html .= '<li class="pull-left">';
                $html .= '<input type="checkbox" class="cwp-custom-fields-post-types" name="cwp[group][user_roles][]" placeholder="" '.$checked.' value="'. esc_attr($key) .'">'. esc_html($user_role['name']) .' <br>';
                $html .= '</li>';
            }
        }
        return $html;
    }
    
    public static function save_group() {
        
        if (isset($_POST['cwp']['group'])) {
            $group           = isset($_POST['cwp']['group'])   ? $_POST['cwp']['group']      : array();
            $groupID         = isset($group['id'])             ? sanitize_text_field($group['id'])          : '';
            $groupName       = isset($group['name'])           ? sanitize_text_field($group['name'])        : '';
            $groupDesc       = isset($group['description'])    ? sanitize_text_field($group['description']) : '';
            $groupOrder      = isset($group['order'])          ? sanitize_text_field($group['order'])       : 0;
            $groupUserRoles  = isset($group['user_roles'])     ? CubeWp_Sanitize_text_Array($group['user_roles'])  : array();

            if (!empty($groupName)) {
                if (isset($_POST['cwp_save_group'])) {
                    $post_data          =   array(
                        'post_type'     =>  'cwp_user_fields',
                        'post_title'    =>  $groupName,
                        'post_content'  =>  $groupDesc,
                        'post_status'   =>  'publish',
                    );
                    $post_id = wp_insert_post($post_data);
                } else if (isset($_POST['cwp_edit_group']) && !empty($groupID)) {
                    $post_data         =   array(
                        'ID'           =>  $groupID,
                        'post_title'   =>  $groupName,
                        'post_content' =>  $groupDesc,
                    );
                    wp_update_post($post_data);
                    $post_id = $groupID;
                }
                if(isset($groupOrder) && is_numeric($groupOrder) && $groupOrder > 0){
                    update_post_meta($post_id, '_cwp_group_order', $groupOrder);
                }else{
                    update_post_meta($post_id, '_cwp_group_order', 0);
                }
                if (!empty($post_id) && !empty($groupUserRoles)) {
                    $groupUserRoles = implode(",", $groupUserRoles);
                    update_post_meta($post_id, '_cwp_group_user_roles', $groupUserRoles);
                }else{
                    delete_post_meta($post_id, '_cwp_group_user_roles');
                }
                
            }
        }
        $field_names  = $sub_field_names = array();
        if (isset($_POST['cwp']['fields'])) {
            $fields = CubeWp_Sanitize_Custom_Fields($_POST['cwp']['fields'],'user');
            foreach ($fields as $field) {
                
                if(isset($field['options']) && !empty($field['options']) && is_array($field['options'])){
                    $field['options'] = json_encode(array_filter($field['options']));
                }
                if(isset($field['default_option']) && !empty($field['default_option'])){
                    $field['default_value'] = $field['default_option'];
                }
                
                
                
                if(isset($_POST['cwp']['sub_fields'][$field['name']]) && $field['type'] == 'repeating_field'){
                    $sub_fields_data = CubeWp_Sanitize_Custom_Fields($_POST['cwp']['sub_fields'][$field['name']],'user');
                    $sub_fields = array();
                    foreach ($sub_fields_data as $sub_field){
                        
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
                
                $field_names[] = $field['name'];
                self::set_option($field['name'], $field);
            }
        }
        
        if (!empty($post_id) ) {
            
            $group_fields = get_post_meta($post_id, '_cwp_group_fields', true);
            if(isset($group_fields) && !empty($group_fields)){
                $group_fields_arr = json_decode($group_fields, true);
                self::delete_options( $group_fields_arr, $field_names);
            }
            if(isset($field_names) && !empty($field_names)){
                new CubeWp_Update_Frontend_Forms(array('group_id'=>$post_id,'existing_fields'=>$field_names,'form_type'=>'user'));
                update_post_meta($post_id, '_cwp_group_fields', json_encode($field_names));
            }else{
                new CubeWp_Update_Frontend_Forms(array('group_id'=>$post_id,'form_type'=>'user'));
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

            wp_redirect( CubeWp_Submenu::_page_action('user-custom-fields') );
        }
        
    }

    private static function set_option( $name, $val ) {
        if (isset($name) && !empty($name)) {
            $options        = CWP()->get_custom_fields( 'user' );
            $options        = $options == '' ? array() : $options;
            $options[$name] = $val;
            CWP()->update_custom_fields( 'user', $options);
        }
    }
    
    private static function delete_options( $group_fields = array(), $new_fields = array()){
        $options = CWP()->get_custom_fields( 'user' );
        if(isset($group_fields) && !empty($group_fields)){
            foreach($group_fields as $group_field){
                if( (isset($new_fields) && is_array($new_fields) && !in_array($group_field, $new_fields)) || empty($new_fields) ){
                    unset($options[$group_field]);
                }
            }
        }
        CWP()->update_custom_fields( 'user', $options);
    }
    
}