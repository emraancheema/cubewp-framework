<?php
/**
 * CubeWP metabox is for post type metaboxes.
 *
 * @version 1.0
 * @package cubewp/cube/modules/post-types
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * CubeWp_Metabox
 */
class CubeWp_Metabox {
        
    /**
     * Method get_groups
     *
     * @return array 
     * @since  1.0.0
     */
    private static function get_groups() {
        
        $args = array(
            'numberposts' => -1,
            'post_type'   => 'cwp_form_fields',
            'meta_key'    => '_cwp_group_order',
            'orderby'     => 'meta_value_num',
            'order'       => 'ASC',
            'post_status' => array('publish','private'),
            'meta_query'  => array(
                'key'       => '_cwp_group_types',
                'value'     => '',
                'compare'   => '!='
            )
        );

        return get_posts( $args );        
	}    
    
    /**
     * Method get_group_metaboxes
     *
     * @return array
     * @since  1.0.0
     */
    private static function get_group_metaboxes() {
        $allGroups = self::get_groups();
        
        if($allGroups){
            $metaboxes = array();
            foreach ( $allGroups as $group ) {
                
                $groupID = $group->ID;
                $groupTitle = $group->post_title;
                $groupName = $group->post_name;
                $fields = get_post_meta( $groupID, '_cwp_group_fields', true );
                $groupType = get_post_meta($groupID, '_cwp_group_types', true);
                
                if($fields){
                    $screen = isset($groupType) && $groupType != '' ? explode(",", $groupType) : '';
                    $metaboxes[$groupName] =  array(
                        'ID' => $groupID,
                        'title' => $groupTitle,
                        'postTypes' => $screen,
                        'location' => 'normal',
                        'priority' => 'low',
                        'fields' => self::get_group_fields($groupID),
                    );
                }
            }
            return $metaboxes;
        }        
    }
        
    /**
     * Method get_group_fields
     *
     * @param int $groupID
     *
     * @return array
     * @since  1.0.0
     */
    public static function get_group_fields($groupID) {
        
        $fields       = get_post_meta( $groupID, '_cwp_group_fields', true );
        $sub_fields   = get_post_meta( $groupID, '_cwp_group_sub_fields', true );
        $sub_fields   = isset($sub_fields) && !empty($sub_fields) ? json_decode($sub_fields, true) : array();
        $fieldOptions = CWP()->get_custom_fields( 'post_types' );
        
        $fieldBox = array();
        if(isset($fields) && !empty($fields)){
            $fields = explode(",",$fields);
            
            foreach($fields as $field){
                if (isset($fieldOptions[$field])) {
                    $SingleFieldOption = $fieldOptions[$field];
                    
                    if(isset($SingleFieldOption['sub_fields']) && !empty($SingleFieldOption)){
                        unset($SingleFieldOption['sub_fields']);
                    }
                    
                    $fieldBox[$SingleFieldOption['name']] = $SingleFieldOption;
                    if(isset($sub_fields[$SingleFieldOption['name']]) && !empty($sub_fields[$SingleFieldOption['name']])){
                        foreach($sub_fields[$SingleFieldOption['name']] as $sub_field){
                            $fieldBox[$SingleFieldOption['name']]['sub_fields'][] = $fieldOptions[$sub_field];
                        }
                    }
                }
            }
        }
        return $fieldBox;
    }
        
    /**
     * Method get_current_meta
     *
     * @return void
     * @since  1.0.0
     */
    public static function get_current_meta() {
        $metaboxes = self::get_group_metaboxes();
        if(isset($metaboxes) && !empty($metaboxes)) {
            foreach ( $metaboxes as $id => $metabox ) {
                add_meta_box( $id, $metabox['title'], array(__CLASS__, 'show_metaboxes'), $metabox['postTypes'], $metabox['location'], $metabox['priority'], $id );
            }
        }
    }
        
    /**
     * Method show_metaboxes
     *
     * @param object $post
     * @param array $args
     *
     * @return string html
     * @since  1.0.0
     */
    public static function show_metaboxes( $post, $args) {
        
        $metaboxes           = self::get_group_metaboxes();
        $fields              = isset($metaboxes[$args['id']]['fields']) ? $metaboxes[$args['id']]['fields'] : array();
        
        $group_id            = isset($metaboxes[$args['id']]['ID']) ? $metaboxes[$args['id']]['ID'] : '';
        $group_terms         = get_post_meta($group_id, '_cwp_group_terms');
        $group_terms         = (isset($group_terms) && !empty($group_terms)) ? explode(',', implode(',', $group_terms)) : $group_terms;
        
        
        $taxonomies = get_object_taxonomies( get_post_type($post->ID),'objects' );
        
        $hidden_field = '';
        if(isset($taxonomies) && !empty($taxonomies)){
            $comma = $_group_terms = $_group_terms_name = '';
            foreach($taxonomies as $single => $objects){
                $terms = get_terms($single, array('hide_empty' => false));
                if(!empty($terms)){
                    foreach($terms as $term){
                        if(isset($group_terms) && is_array($group_terms) && in_array($term->term_id, $group_terms)){
                            $_group_terms .= $comma . $term->term_id;
                            $_group_terms_name .= $comma . $term->name;
                            $comma = ',';
                        }
                    }
                }
            }
        }
        if(!empty($_group_terms)){
            $input_attrs = array(
                'type'         => 'hidden',
                'name'         => 'conditional_terms',
                'id'           => 'conditional_terms',
                'class'        => 'group-terms',
                'value'        => $_group_terms,
                'extra_attrs'  => 'data-term-name="'.$_group_terms_name.'"'
            );
            $hidden_field = cwp_render_hidden_input( $input_attrs );
        }
        
        $output = '<table class="form-table cwp-metaboxes cwp-validation">';
        $output .= $hidden_field;    
        $input_attrs = array(
            'type'         => 'hidden',
            'name'         => 'cwp_meta_box_nonce',
            'value'        => wp_create_nonce( basename( __FILE__ ) ),
        );
        $output .= cwp_render_hidden_input( $input_attrs );

        $output .= '<tbody>';

        foreach ( $fields as $id => $field ) {

            $Old_Value = get_post_meta( $post->ID, $id, true );
            if($Old_Value){
                $value = $Old_Value;
            }else{
                $value = isset($field['default_value']) ? $field['default_value'] : '';
            }
            $field['value']        =  $value;
            $field['custom_name']  =  'cwp_meta[' . $field['name'] . ']';
            $field['wrap']         =  true;

            if($field['type'] == 'google_address' ){
                $field['custom_name_lat'] =   'cwp_meta[' . $field['name'].'_lat' . ']';
                $field['custom_name_lng'] =   'cwp_meta[' . $field['name'].'_lng' . ']';
                $field['lat']      =   get_post_meta( $post->ID, $field['name'].'_lat', true );
                $field['lng']      =   get_post_meta( $post->ID, $field['name'].'_lng', true );
            }

            $field_args = apply_filters( "cubewp/admin/post/{$field['type']}/args", $field);
            $output .= apply_filters( "cubewp/admin/post/{$field['type']}/field", '', $field_args );

        }

        $output .= '</tbody>';
        $output .= '</table>';
        
        echo cubewp_core_data($output);
    }
        
    /**
     * Method save_metaboxes
     *
     * @param int $post_id [explicite description]
     *
     * @return void
     * @since  1.0.0
     */
    public static function save_metaboxes( $post_id ) {
        
        if(isset($_POST['cwp_meta_box_nonce'])){
            
            // verify nonce
            if ( ! wp_verify_nonce( $_POST['cwp_meta_box_nonce'], basename( __FILE__ ) ) )
                return $post_id;

            // check autosave
            if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
                return $post_id;

            // check permissions
            if ( 'page' == $_POST['post_type'] ) {
                if ( ! current_user_can( 'edit_page', $post_id ) )
                    return $post_id;
            } elseif ( ! current_user_can( 'edit_post', $post_id ) ) {
                return $post_id;
            }
            
            if(isset($_POST['cwp_meta'])){
                
                $fields = CubeWp_Sanitize_Fields_Array($_POST['cwp_meta'], 'post_types');

                $fieldOptions = CWP()->get_custom_fields( 'post_types' );

                foreach ( $fields as $key => $value ) {
                    $_key = str_replace('cwp-', '', $key);

                    $singleFieldOptions = isset($fieldOptions[$_key]) ? $fieldOptions[$_key] : array(); 

                    $val = $value;
                    if ( ( isset($singleFieldOptions['type']) && isset($singleFieldOptions['relationship']) && $singleFieldOptions['type'] == 'post' && $singleFieldOptions['relationship'] ) && is_array( $singleFieldOptions ) && count( $singleFieldOptions ) > 0 ) {
                        if ( ! is_array( $val ) ) {
                            $val = array( $val );
                        }
                        if ( ! empty($val) && count($val) > 0) {
                            (new CubeWp_Relationships)->save_relationship( $post_id, $val, $_key, 'PTP' );
                        }
                    }else if ( isset($singleFieldOptions['type']) && isset($singleFieldOptions['relationship']) && ( $singleFieldOptions['type'] == 'user' && $singleFieldOptions['relationship'] ) && is_array( $singleFieldOptions ) && count( $singleFieldOptions ) > 0 ) {
                        if ( ! is_array( $val ) ) {
                            $val = array( $val );
                        }
                        if ( ! empty($val) && count($val) > 0) {
                            (new CubeWp_Relationships)->save_relationship( $post_id, $val, $_key, 'PTU' );
                        }
                    }

                    if(is_array($singleFieldOptions) && count($singleFieldOptions) > 0 && $singleFieldOptions['type'] == 'repeating_field' ){

                        $arr = array();
                        foreach($value as $_key => $_val){
                            foreach($_val as $field_key => $field_val){
                                $subFieldOptions = isset($fieldOptions[$_key]) ? $fieldOptions[$_key] : array();
                                if ( isset( $subFieldOptions['type'] ) && $subFieldOptions['type'] == 'gallery' ) {
                                    if ( ! empty( $field_val ) ) {
                                        $save_format = isset( $subFieldOptions['files_save'] ) && ! empty( $subFieldOptions['files_save'] ) ? $subFieldOptions['files_save'] : 'ids';
                                        $format_separator = isset( $subFieldOptions['files_save_separator'] ) && ! empty( $subFieldOptions['files_save_separator'] ) ? $subFieldOptions['files_save_separator'] : 'array';
                                        if ( $save_format == 'urls' ) {
                                            $_attachment_ids = array();
                                            foreach ( $field_val as $_field_val ) {
                                                $_attachment_ids[] = wp_get_attachment_url( cwp_get_attachment_id( $_field_val ) );
                                            }
                                            $field_val = $_attachment_ids;
                                        }
                                        if ( $format_separator != 'array' ) {
                                            $field_val = implode( $format_separator, $field_val );
                                        }
                                    }
                                    $arr[$field_key][$_key] = $field_val;
                                } else if ( isset( $subFieldOptions['type'] ) && ( $subFieldOptions['type'] == 'file' || $subFieldOptions['type'] == 'image' ) ) {
                                    if ( ! empty( $field_val ) ) {
                                        $save_format = isset( $subFieldOptions['files_save'] ) && ! empty( $subFieldOptions['files_save'] ) ? $subFieldOptions['files_save'] : 'ids';
                                        if ( $save_format == 'urls' ) {
                                            $attachment_url = wp_get_attachment_url( cwp_get_attachment_id( $field_val ) );
                                            $field_val = $attachment_url;
                                        }
                                    }
                                    $arr[$field_key][$_key] = $field_val;
                                } else if ( ( isset( $subFieldOptions['type'] ) && $subFieldOptions['type'] == 'dropdown' && isset($subFieldOptions['multiple']) && $subFieldOptions['multiple'] ) || ( isset( $subFieldOptions['type'] ) && $subFieldOptions['type'] == 'checkbox' ) || ( isset( $subFieldOptions['appearance'] ) && $subFieldOptions['appearance'] == 'multi_select' ) ) {
                                    $format_separator = isset( $subFieldOptions['files_save_separator'] ) && ! empty( $subFieldOptions['files_save_separator'] ) ? $subFieldOptions['files_save_separator'] : 'array';
                                    if ( $format_separator != 'array' ) {
                                        $field_val = implode( $format_separator, $field_val );
                                    }
                                    $arr[$field_key][$_key] = $field_val;
                                } else {
                                    $arr[$field_key][$_key] = $field_val;
                                }
                            }
                        }
                        if(isset($arr) && !empty($arr)){
                            $_arr = array_filter($arr);
                            
                            update_post_meta( $post_id, $key, $_arr );
                        }else{
                            delete_post_meta( $post_id, $key );
                        }
                    }else{
                        $old = get_post_meta( $post_id, $key, true );
                        $new = $fields[$key];
                        if(is_array($singleFieldOptions) && count($singleFieldOptions) > 0 && $singleFieldOptions['type'] == 'gallery' ){
                            $new = array_filter($new);
                            $save_format = isset( $singleFieldOptions['files_save'] ) && ! empty( $singleFieldOptions['files_save'] ) ? $singleFieldOptions['files_save'] : 'ids';
                            $format_separator = isset( $singleFieldOptions['files_save_separator'] ) && ! empty( $singleFieldOptions['files_save_separator'] ) ? $singleFieldOptions['files_save_separator'] : 'array';
                            if ( ! empty( $new ) ) {
                                if ( $save_format == 'urls' ) {
                                    $_attachment_ids = array();
                                    foreach ( $new as $_field_val ) {
                                        $_attachment_ids[] = wp_get_attachment_url( cwp_get_attachment_id( $_field_val ) );
                                    }
                                    $new = $_attachment_ids;
                                }
                                if ( $format_separator != 'array' ) {
                                    $new = implode( $format_separator, $new );
                                }
                            }
                        }else if(is_array($singleFieldOptions) && count($singleFieldOptions) > 0 && ( $singleFieldOptions['type'] == 'file' || $singleFieldOptions['type'] == 'image' ) ){
                            $save_format = isset( $singleFieldOptions['files_save'] ) && ! empty( $singleFieldOptions['files_save'] ) ? $singleFieldOptions['files_save'] : 'ids';
                            if ( $save_format == 'urls' ) {
                                $attachment_url = wp_get_attachment_url( cwp_get_attachment_id( $new ) );
                                $new = $attachment_url;
                            }
                        }
                        else if ( ( isset( $singleFieldOptions['type'] ) && $singleFieldOptions['type'] == 'dropdown' && ( isset( $singleFieldOptions['multiple'] ) && $singleFieldOptions['multiple'] ) ) || ( isset( $singleFieldOptions['type'] ) && $singleFieldOptions['type'] == 'checkbox' ) || ( isset( $singleFieldOptions['appearance'] ) && $singleFieldOptions['appearance'] == 'multi_select' ) ) {
                            $format_separator = isset( $singleFieldOptions['files_save_separator'] ) && ! empty( $singleFieldOptions['files_save_separator'] ) ? $singleFieldOptions['files_save_separator'] : 'array';
                            if ( $format_separator != 'array' ) {
                                $new = implode( $format_separator, $new );
                            }
                        }else if(is_array($singleFieldOptions) && count($singleFieldOptions) > 0 && ($singleFieldOptions['type'] == 'date_picker' || $singleFieldOptions['type'] == 'date_time_picker' || $singleFieldOptions['type'] == 'time_picker' )){
                            $new = strtotime($new);
                        }
                        if ( $new ) {
                            if($new != $old){
                                update_post_meta( $post_id, $key, $new );
                            }
                        }else{
                            delete_post_meta( $post_id, $key );
                        }
                    }
                }
            }
        }
    }
}