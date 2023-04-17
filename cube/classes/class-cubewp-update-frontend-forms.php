<?php
/**
 * CubeWp update frontend forms runs whenever we delete or edit any custom field and it
 * update data into all relevant fields based on the changes.
 *
 * @version 1.0
 * @package cubewp/cube/classes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * CubeWp_Update_Frontend_Forms
 */
class CubeWp_Update_Frontend_Forms{
    
    protected static $GROUP_ID = '';
    
    protected static $GROUP_FIELDS = array();
    
    protected static $UPDATED_FIELDS = array();
    
    protected static $TAX_SLUG = '';
    
    protected static $FORM_TYPE = 'post_types';
    
    protected static $GROUP_OPTIONS = '';
    
    public function __construct($args) {
        $defaults = array(
            'group_id'         =>   '',
            'if_user_form'     =>   false,
            'existing_fields'  =>   array(),
            'taxnomoy_slug'    =>   '',
            'group_options'    =>   false,
        );
        $args = wp_parse_args( $args, $defaults );
        
        self::$GROUP_ID       = $args['group_id'];
        self::$UPDATED_FIELDS = $args['existing_fields'];
        self::$TAX_SLUG       = $args['taxnomoy_slug'];
        self::$FORM_TYPE      = !empty($args['form_type']) ? $args['form_type']: 'post_types';
        self::$GROUP_OPTIONS  = $args['group_options'];
        
        $this->cwp_check_fields_if_deleted();
    }
        
    /**
     * Method cwp_check_fields_if_deleted
     *
     * @return void
     * @since  1.0.0
     */
    private function cwp_check_fields_if_deleted(){
        
        if(isset(self::$GROUP_ID) && !empty(self::$GROUP_ID)){

            $group_fields = get_post_meta(self::$GROUP_ID, '_cwp_group_fields', true);
            
            if(isset($group_fields) && !empty($group_fields)){
                
                if(self::$FORM_TYPE == 'post_types'){
                    $group_fields = explode(',',$group_fields);
                    self::$GROUP_FIELDS = $group_fields;
                    self::cwpform_search_fields_check();
                    self::cwpform_filter_fields_check();
                    self::cwpform_posttype_fields_check();
                    self::cwpform_single_layout_fields_check();
                }elseif(self::$FORM_TYPE == 'user'){
                    $group_fields = json_decode($group_fields, true);
                    self::$GROUP_FIELDS = $group_fields;
                    self::cwpform_user_registration_fields_check();
                    self::cwpform_user_profile_fields_check();
                }
                
                if(self::$GROUP_OPTIONS == true){
                    $options = CWP()->get_custom_fields( 'post_types' );
                    if($options){
                        if(!empty($group_fields)){
                            foreach($group_fields as $group_field){
                                unset($options[$group_field]);
                            }
                            CWP()->update_custom_fields( 'post_types', $options );
                        }
                    }
                }
                                                
            }
        }
    }
    
    /**
     * Method cwpform_search_fields_check
     *
     * @return void
     * @since  1.0.0
     */
    private static function cwpform_search_fields_check(){
        
        $Search_options = CWP()->get_form('search_fields');

        if(isset($Search_options) && !empty($Search_options) && count($Search_options)>0){
            foreach($Search_options as $key => $option){
                if(isset($option['fields']) && count($option['fields'])>0){
                    $fields = self::cwpform_update_value($option['fields']);
                    $Search_options[$key]['fields'] = $fields;
                }
            }
            
            CWP()->update_form('search_fields', $Search_options);
        }
    }
    
    /**
     * Method cwpform_filter_fields_check
     *
     * @return void
     * @since  1.0.0
     */
    private static function cwpform_filter_fields_check(){
        
        $filter_options = CWP()->get_form('search_filters');
        if(isset($filter_options) && !empty($filter_options) && count($filter_options)>0){
            foreach($filter_options as $key => $option){
                if(isset($option['fields']) && count($option['fields'])>0){
                    $fields = self::cwpform_update_value($option['fields']);
                    $filter_options[$key]['fields'] = $fields;
                }
            }
            
            CWP()->update_form('search_filters', $filter_options);
        }
    }
    
    /**
     * Method cwpform_posttype_fields_check
     *
     * @return void
     * @since  1.0.0
     */
    private static function cwpform_posttype_fields_check(){
        
        $cwpform_post_types  =  CWP()->get_form('post_type');
        if(isset($cwpform_post_types) && !empty($cwpform_post_types) && count($cwpform_post_types)>0){
            foreach($cwpform_post_types as $key => $option){
                if(isset($option['groups'])){
                    foreach($option['groups'] as $sectionID => $data){
                        if(isset($data['fields']) && count($data['fields'])>0){
                            $fields = self::cwpform_update_value($data['fields']);
                            $cwpform_post_types[$key]['groups'][$sectionID]['fields'] = $fields;
                        }
                    }
                }else{
                    foreach($option as $PlanID => $data){
                        if ( empty( $data['groups'] ) || ! is_array( $data['groups'] ) ) {
                            continue;
                        }
                        foreach($data['groups'] as $sectionID => $sectionData){
                            if(isset($sectionData['fields']) && count($sectionData['fields'])>0){
                                $fields = self::cwpform_update_value($sectionData['fields']);
                                $cwpform_post_types[$key][$PlanID]['groups'][$sectionID]['fields'] = $fields;
                            }
                        }
                    }
                }
            }
            
            CWP()->update_form('post_type', $cwpform_post_types);
        }
    }
        
    /**
     * Method cwpform_single_layout_fields_check
     *
     * @return void
     * @since  1.0.0
     */
    private static function cwpform_single_layout_fields_check(){
        
        $cwp_single_layout   =  CWP()->get_form('single_layout');

        if(isset($cwp_single_layout) && !empty($cwp_single_layout) && count($cwp_single_layout)>0){
            foreach($cwp_single_layout as $key => $option){
                if(isset($option['sidebar'])){
                    foreach($option['sidebar'] as $sectionID => $data){
                        if(isset($data['fields']) && count($data['fields'])>0){
                            $fields = self::cwpform_update_value($data['fields']);
                            $cwp_single_layout[$key]['sidebar'][$sectionID]['fields'] = $fields;
                        }
                    }
                }elseif(isset($option['content'])){
                    foreach($option['content'] as $sectionID => $data){
                        if(isset($data['fields']) && count($data['fields'])>0){
                            $fields = self::cwpform_update_value($data['fields']);
                            $cwp_single_layout[$key]['content'][$sectionID]['fields'] = $fields;
                        }
                    }
                }
            }
            CWP()->update_form('single_layout', $cwp_single_layout);
        }
    }
    
    /**
     * Method cwpform_user_registration_fields_check
     *
     * @return void
     * @since  1.0.0
     */
    private static function cwpform_user_registration_fields_check(){
        $cwpform_user_registration  = CWP()->get_form('user_register');

        if(isset($cwpform_user_registration) && !empty($cwpform_user_registration) && count($cwpform_user_registration)>0){
            foreach($cwpform_user_registration as $key => $option){
                if(isset($option['groups'])){
                    foreach($option['groups'] as $sectionID => $data){
                        if(isset($data['fields']) && count($data['fields'])>0){
                            $fields = self::cwpform_update_value($data['fields']);
                            $cwpform_user_registration[$key]['groups'][$sectionID]['fields'] = $fields;
                        }
                    }
                }
            }
            CWP()->update_form('user_register', $cwpform_user_registration);
        }
    }
    
    /**
     * Method cwpform_user_profile_fields_check
     *
     * @return void
     * @since  1.0.0
     */
    private static function cwpform_user_profile_fields_check(){
        $cwpform_user_profile  =  CWP()->get_form('user_profile');

        if(isset($cwpform_user_profile) && !empty($cwpform_user_profile) && count($cwpform_user_profile)>0){
            foreach($cwpform_user_profile as $key => $option){
                if(isset($option['groups'])){
                    foreach($option['groups'] as $sectionID => $data){
                        if(isset($data['fields']) && count($data['fields'])>0){
                            $fields = self::cwpform_update_value($data['fields']);
                            $cwpform_user_profile[$key]['groups'][$sectionID]['fields'] = $fields;
                        }
                    }
                }
            }
            CWP()->update_form('user_profile', $cwpform_user_profile);
        }
    }
        
    /**
     * Method cwpform_field_ids
     *
     * @param array $fields all fields data
     *
     * @return array
     * @since  1.0.0
     */
    private static function cwpform_field_ids(array $fields ){
        if(!is_array($fields) && count($fields)<0){
            return;
        }
        
        foreach($fields as $key=>$value){
            $field_ids[] =  $key;
        }
        return $field_ids;
    }
        
    /**
     * Method cwpform_update_value
     *
     * @param array $fields all fields data
     *
     * @return array
     * @since  1.0.0
     */
    private static function cwpform_update_value(array $fields ){
        $group_fields = self::$GROUP_FIELDS;
        $updatedFields = self::$UPDATED_FIELDS;
        if(!empty(self::$TAX_SLUG)){
            unset($fields[self::$TAX_SLUG]);
        }else{
            if(isset($group_fields) && !empty($group_fields)){
                foreach($group_fields as $group_field){
                    if(is_array($updatedFields) && count($updatedFields)>0){
                        $Fields_ids = self::cwpform_field_ids($fields);
                        if( (in_array($group_field, $Fields_ids)) ){
                            if(self::$FORM_TYPE == 'post_types'){
                                $field_options = get_field_options($group_field);
                            }elseif(self::$FORM_TYPE == 'user'){
                                $field_options = get_user_field_options($group_field);
                            }
                            if(!empty($field_options)){
                                $new_options = array(
                                    'label'             => $field_options['label'],
                                    'class'             => $field_options['class'],
                                    'container_class'   => $field_options['container_class'],
                                    'name'              => $field_options['name'],
                                    'display_ui'        => $field_options['type'],
                                    'type'              => $field_options['type'],
                                );
                            
                                if(isset($fields[$group_field]['field_size'])){
                                    $new_options['field_size'] = $fields[$group_field]['field_size'];
                                }
                                $fields[$group_field]=$new_options;
                            }
                        }
                        if( (!in_array($group_field, $updatedFields)) ){        
                            unset($fields[$group_field]);
                        }
                    }else{
                        unset($fields[$group_field]);
                    }
                }
            }
        }
        return $fields;
    }
}