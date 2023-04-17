<?php
/**
 * CubeWp admin repeating field 
 *
 * @version 1.0
 * @package cubewp/cube/fields/frontend
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * CubeWp_Frontend_Repeater_Field
 */
class CubeWp_Frontend_Repeater_Field extends CubeWp_Frontend {
    
    public function __construct( ) {
        add_filter('cubewp/frontend/repeating_field/field', array($this, 'render_repeater_field'), 10, 2);
        
        add_filter('cubewp/user/registration/repeating_field/field', array($this, 'render_user_repeater_field'), 10, 2);
        add_filter('cubewp/user/profile/repeating_field/field', array($this, 'render_profile_repeater_field'), 10, 2);
    }

    /**
     * Method render_user_repeater_field
     *
     * @param string $output
     * @param array $args
     *
     * @return string html
     * @since  1.0.0
     */
    public function render_user_repeater_field( $output = '', $args = array() ) {
        wp_enqueue_script('jquery-ui-sortable');
        wp_enqueue_script('cwp-repeating-fields');
        
        $args    =  apply_filters( 'cubewp/frontend/field/parametrs', $args );
        $required       = self::cwp_frontend_field_required($args['required']);
        $args['class']  = !empty($required['class']) ? $required['class'] : $args['class'];
        $output         = self::cwp_frontend_post_field_container($args);
        $subFields = $args['sub_fields'];
            $output .= self::cwp_frontend_field_label($args);
            $output .= '<div class="cwp-repeating-field-container">
                <div class="cwp-repeating-field-wrapper">
                    <div class="cwp-repeating-single-field-container">
                        <div class="cwp-repeating-single-field-template cwp-repeating-single-field-row">
                            <div class="cwp-repeating-single-field">
                                <div class="cwp-repeating-single-field-actions">
                                    <div class="cwp-repeating-single-field-move">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20"><path class="a" d="M8.273,11.909V10.091H3.727L5.545,8.273l-.909-.909L1,11l3.636,3.636.909-.909L3.727,11.909Zm9.091-4.545-.909.909,1.818,1.818H13.727v1.818h4.545l-1.818,1.818.909.909L21,11ZM11.909,18.273V13.727H10.091v4.545L8.273,16.455l-.909.909L11,21l2.357-2.357,1.28-1.28-.909-.909ZM10.091,3.727V8.273h1.818V3.727l1.818,1.818.909-.909L11,1,7.364,4.636l.909.909Z" transform="translate(-1 -1)"/></svg>
                                    </div>
                                    <div class="cwp-repeating-single-field-collapse">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.646 4.646a.5.5 0 0 1 .708 0l6 6a.5.5 0 0 1-.708.708L8 5.707l-5.646 5.647a.5.5 0 0 1-.708-.708l6-6z"/></svg>
                                    </div>
                                    <div class="cwp-repeating-single-field-remove">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" viewBox="0 0 20 20"><path d="M6.5 1h3a.5.5 0 0 1 .5.5v1H6v-1a.5.5 0 0 1 .5-.5ZM11 2.5v-1A1.5 1.5 0 0 0 9.5 0h-3A1.5 1.5 0 0 0 5 1.5v1H2.506a.58.58 0 0 0-.01 0H1.5a.5.5 0 0 0 0 1h.538l.853 10.66A2 2 0 0 0 4.885 16h6.23a2 2 0 0 0 1.994-1.84l.853-10.66h.538a.5.5 0 0 0 0-1h-.995a.59.59 0 0 0-.01 0H11Zm1.958 1-.846 10.58a1 1 0 0 1-.997.92h-6.23a1 1 0 0 1-.997-.92L3.042 3.5h9.916Zm-7.487 1a.5.5 0 0 1 .528.47l.5 8.5a.5.5 0 0 1-.998.06L5 5.03a.5.5 0 0 1 .47-.53Zm5.058 0a.5.5 0 0 1 .47.53l-.5 8.5a.5.5 0 1 1-.998-.06l.5-8.5a.5.5 0 0 1 .528-.47ZM8 4.5a.5.5 0 0 1 .5.5v8.5a.5.5 0 0 1-1 0V5a.5.5 0 0 1 .5-.5Z"/>
                                        </svg>
                                    </div>
                                </div>
                                <div class="cwp-repeating-single-field-content {{row-count-placeholder}}">';
                                    if (!empty($subFields) && is_array($subFields)) {
                                        foreach ($subFields as $k => $fieldArgs) {
                                            $fieldArgs['custom_name'] =   'cwp_user_register[custom_fields]['. $args['name'] .'][' . $fieldArgs['name'] . '][]';
                                            $fieldArgs['value']       =  isset($fieldArgs['default_value']) ? $fieldArgs['default_value'] : '';
                                            $fieldArgs['id'] = $fieldArgs['id'] . '{{row-count-placeholder}}';
                                            if($fieldArgs['type'] == 'google_address' ){
                                                $fieldArgs['custom_name_lat'] =   'cwp_user_register[custom_fields]['.$args['name'].'][' . $fieldArgs['name'].'_lat' . '][]';
                                                $fieldArgs['custom_name_lng'] =   'cwp_user_register[custom_fields]['.$args['name'].'][' . $fieldArgs['name'].'_lng' . '][]';
                                            }
                                            if($fieldArgs['type'] == 'radio' || $fieldArgs['type'] == 'checkbox' || ($fieldArgs['type'] == 'dropdown' && $fieldArgs['multiple'] == true)){
                                                $fieldArgs['custom_name']    =  'cwp_user_register[custom_fields]['. $args['name'] .'][' . $fieldArgs['name'] . '][{{row-count-placeholder}}]';
                                            }
                                            $fieldArgs = apply_filters("cubewp/frontend/user/repeating_field/args", $fieldArgs);
                                            $output .= apply_filters("cubewp/frontend/{$fieldArgs['type']}/field", '', $fieldArgs);
                                        }
                                    }
                                $output .= '</div>
                            </div>
                        </div>';
                        $output .= '<input type="hidden" name="cwp_user_register[custom_fields][' . $args['name'] . '][]" value="">';
                        if(isset($args['value']) && !empty($args['value'])){
                            for($i = 0; $i < count($args['value']); $i++){
                                $output .= '<div class="cwp-repeating-single-field-row">
                                    <div class="cwp-repeating-single-field">
                                        <div class="cwp-repeating-single-field-actions">
                                            <div class="cwp-repeating-single-field-move ui-sortable-handle">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20"><path class="a" d="M8.273,11.909V10.091H3.727L5.545,8.273l-.909-.909L1,11l3.636,3.636.909-.909L3.727,11.909Zm9.091-4.545-.909.909,1.818,1.818H13.727v1.818h4.545l-1.818,1.818.909.909L21,11ZM11.909,18.273V13.727H10.091v4.545L8.273,16.455l-.909.909L11,21l2.357-2.357,1.28-1.28-.909-.909ZM10.091,3.727V8.273h1.818V3.727l1.818,1.818.909-.909L11,1,7.364,4.636l.909.909Z" transform="translate(-1 -1)"></path></svg>
                                            </div>
                                            <div class="cwp-repeating-single-field-collapse">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.646 4.646a.5.5 0 0 1 .708 0l6 6a.5.5 0 0 1-.708.708L8 5.707l-5.646 5.647a.5.5 0 0 1-.708-.708l6-6z"/></svg>
                                            </div>
                                            <div class="cwp-repeating-single-field-remove">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" viewBox="0 0 20 20"><path d="M6.5 1h3a.5.5 0 0 1 .5.5v1H6v-1a.5.5 0 0 1 .5-.5ZM11 2.5v-1A1.5 1.5 0 0 0 9.5 0h-3A1.5 1.5 0 0 0 5 1.5v1H2.506a.58.58 0 0 0-.01 0H1.5a.5.5 0 0 0 0 1h.538l.853 10.66A2 2 0 0 0 4.885 16h6.23a2 2 0 0 0 1.994-1.84l.853-10.66h.538a.5.5 0 0 0 0-1h-.995a.59.59 0 0 0-.01 0H11Zm1.958 1-.846 10.58a1 1 0 0 1-.997.92h-6.23a1 1 0 0 1-.997-.92L3.042 3.5h9.916Zm-7.487 1a.5.5 0 0 1 .528.47l.5 8.5a.5.5 0 0 1-.998.06L5 5.03a.5.5 0 0 1 .47-.53Zm5.058 0a.5.5 0 0 1 .47.53l-.5 8.5a.5.5 0 1 1-.998-.06l.5-8.5a.5.5 0 0 1 .528-.47ZM8 4.5a.5.5 0 0 1 .5.5v8.5a.5.5 0 0 1-1 0V5a.5.5 0 0 1 .5-.5Z"/>
                                                </svg>
                                            </div>
                                        </div>
                                        <div class="cwp-repeating-single-field-content">';
                                            foreach($args['sub_fields'] as $sub_field){
                                                $sub_field['custom_name']  =  'cwp_user_register[custom_fields]['. $args['name'] .'][' . $sub_field['name'] . '][]';
                                                $sub_field['value']        =  isset($args['value'][$i][$sub_field['name']]) ? $args['value'][$i][$sub_field['name']] : '';
                                                $sub_field['id']           =  'cwp_'.rand(123456789, 1111111111);
                                                if($sub_field['type'] == 'google_address' ){
                                                    $sub_field['custom_name_lat'] =   'cwp_user_register[custom_fields]['.$args['name'].'][' . $sub_field['name'].'_lat' . '][]';
                                                    $sub_field['custom_name_lng'] =   'cwp_user_register[custom_fields]['.$args['name'].'][' . $sub_field['name'].'_lng' . '][]';
                                                    $sub_field['lat'] = $args['value'][$i][$fieldArgs['name'] . '_lat'];
                                                    $sub_field['lng'] = $args['value'][$i][$fieldArgs['name'] . '_lng'];
                                                }
                                                if($sub_field['type'] == 'radio' || $sub_field['type'] == 'checkbox' || ($sub_field['type'] == 'dropdown' && $sub_field['multiple'] == true)){
                                                    $sub_field['custom_name']    =  'cwp_user_register[custom_fields]['. $args['name'] .'][' . $sub_field['name'] . '][{{row-count-placeholder}}]';
                                                }
                                                $sub_field = apply_filters("cubewp/frontend/user/repeating_field/args", $sub_field);
                                                $output .= apply_filters( "cubewp/frontend/{$sub_field['type']}/field", '', $sub_field );
                                            }
                                        $output .= '</div>
                                    </div>
                                </div>';
                            }
                        }

                    $output .= '</div>
                   <button class="cwp-add-new-repeating-field" type="button">
                       ' . esc_html__('Add New', 'cubewp-framework') . '
                    </button>
                </div>
            </div>';

        $output .= '</div>';

        $output = apply_filters("cubewp/frontend/{$args['name']}/field", $output, $args);
        
        return $output;
    }

    /**
     * Method render_repeater_field
     *
     * @param string $output
     * @param array $args
     *
     * @return string html
     * @since  1.0.0
     */
    public function render_repeater_field( $output = '', $args = array() ) {

        wp_enqueue_script('jquery-ui-sortable');
        wp_enqueue_script('cwp-repeating-fields');
        
        $args    =  apply_filters( 'cubewp/frontend/field/parametrs', $args );
        $required       = self::cwp_frontend_field_required($args['required']);
        $args['class']  = !empty($required['class']) ? $required['class'] : $args['class'];
        $output         = self::cwp_frontend_post_field_container($args);
        $subFields = $args['sub_fields'];
            $output .= self::cwp_frontend_field_label($args);
            $output .= '<div class="cwp-repeating-field-container">
                <div class="cwp-repeating-field-wrapper">
                    <div class="cwp-repeating-single-field-container">
                        <div class="cwp-repeating-single-field-template cwp-repeating-single-field-row">
                            <div class="cwp-repeating-single-field">
                                <div class="cwp-repeating-single-field-actions">
                                    <div class="cwp-repeating-single-field-move">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20"><path class="a" d="M8.273,11.909V10.091H3.727L5.545,8.273l-.909-.909L1,11l3.636,3.636.909-.909L3.727,11.909Zm9.091-4.545-.909.909,1.818,1.818H13.727v1.818h4.545l-1.818,1.818.909.909L21,11ZM11.909,18.273V13.727H10.091v4.545L8.273,16.455l-.909.909L11,21l2.357-2.357,1.28-1.28-.909-.909ZM10.091,3.727V8.273h1.818V3.727l1.818,1.818.909-.909L11,1,7.364,4.636l.909.909Z" transform="translate(-1 -1)"/></svg>
                                    </div>
                                    <div class="cwp-repeating-single-field-collapse">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.646 4.646a.5.5 0 0 1 .708 0l6 6a.5.5 0 0 1-.708.708L8 5.707l-5.646 5.647a.5.5 0 0 1-.708-.708l6-6z"/></svg>
                                    </div>
                                    <div class="cwp-repeating-single-field-remove">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" viewBox="0 0 20 20"><path d="M6.5 1h3a.5.5 0 0 1 .5.5v1H6v-1a.5.5 0 0 1 .5-.5ZM11 2.5v-1A1.5 1.5 0 0 0 9.5 0h-3A1.5 1.5 0 0 0 5 1.5v1H2.506a.58.58 0 0 0-.01 0H1.5a.5.5 0 0 0 0 1h.538l.853 10.66A2 2 0 0 0 4.885 16h6.23a2 2 0 0 0 1.994-1.84l.853-10.66h.538a.5.5 0 0 0 0-1h-.995a.59.59 0 0 0-.01 0H11Zm1.958 1-.846 10.58a1 1 0 0 1-.997.92h-6.23a1 1 0 0 1-.997-.92L3.042 3.5h9.916Zm-7.487 1a.5.5 0 0 1 .528.47l.5 8.5a.5.5 0 0 1-.998.06L5 5.03a.5.5 0 0 1 .47-.53Zm5.058 0a.5.5 0 0 1 .47.53l-.5 8.5a.5.5 0 1 1-.998-.06l.5-8.5a.5.5 0 0 1 .528-.47ZM8 4.5a.5.5 0 0 1 .5.5v8.5a.5.5 0 0 1-1 0V5a.5.5 0 0 1 .5-.5Z"/>
                                        </svg>
                                    </div>
                                </div>
                                <div class="cwp-repeating-single-field-content {{row-count-placeholder}}">';
                                    if (!empty($subFields) && is_array($subFields)) {
                                        foreach ($subFields as $k => $fieldArgs) {
                                            $fieldArgs['custom_name'] =   'cwp_user_form[cwp_meta]['. $args['name'] .'][' . $fieldArgs['name'] . '][]';
                                            $fieldArgs['value']       =  isset($fieldArgs['default_value']) ? $fieldArgs['default_value'] : '';
                                            $fieldArgs['id'] = $fieldArgs['id'] . '{{row-count-placeholder}}';
                                            if($fieldArgs['type'] == 'google_address' ){
                                                $fieldArgs['custom_name_lat'] =   'cwp_user_form[cwp_meta]['.$args['name'].'][' . $fieldArgs['name'].'_lat' . '][]';
                                                $fieldArgs['custom_name_lng'] =   'cwp_user_form[cwp_meta]['.$args['name'].'][' . $fieldArgs['name'].'_lng' . '][]';
                                            }
                                            if($fieldArgs['type'] == 'radio' || $fieldArgs['type'] == 'checkbox' || ($fieldArgs['type'] == 'dropdown' && (isset($fieldArgs['multiple']) && $fieldArgs['multiple'] == true))){
                                                $fieldArgs['custom_name']    =  'cwp_user_form[cwp_meta]['. $args['name'] .'][' . $fieldArgs['name'] . '][{{row-count-placeholder}}]';
                                            }
                                            if ($fieldArgs['type'] == 'post' && ($fieldArgs['appearance'] == 'multi_select' || $fieldArgs['appearance'] == 'checkbox')) {
                                                $fieldArgs['custom_name']    =  'cwp_user_form[cwp_meta]['. $args['name'] .'][' . $fieldArgs['name'] . '][{{row-count-placeholder}}]';
                                            }
                                            if ($fieldArgs['type'] == 'gallery') {
                                                $fieldArgs['custom_name']    =  'cwp_user_form[cwp_meta]['. $args['name'] .'][' . $fieldArgs['name'] . '][{{row-count-placeholder}}]';
                                            }
                                            $fieldArgs = apply_filters("cubewp/frontend/post/repeating_field/args", $fieldArgs);
                                            $output .= apply_filters("cubewp/frontend/{$fieldArgs['type']}/field", '', $fieldArgs);
                                        }
                                    }
                                $output .= '</div>
                            </div>
                        </div>';
                        $output .= '<input type="hidden" name="cwp_user_form[cwp_meta][' . $args['name'] . '][]" value="">';
                        if(isset($args['value']) && !empty($args['value'])){
                            for($i = 0; $i < count($args['value']); $i++){
                                $output .= '<div class="cwp-repeating-single-field-row">
                                    <div class="cwp-repeating-single-field">
                                        <div class="cwp-repeating-single-field-actions">
                                            <div class="cwp-repeating-single-field-move ui-sortable-handle">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20"><path class="a" d="M8.273,11.909V10.091H3.727L5.545,8.273l-.909-.909L1,11l3.636,3.636.909-.909L3.727,11.909Zm9.091-4.545-.909.909,1.818,1.818H13.727v1.818h4.545l-1.818,1.818.909.909L21,11ZM11.909,18.273V13.727H10.091v4.545L8.273,16.455l-.909.909L11,21l2.357-2.357,1.28-1.28-.909-.909ZM10.091,3.727V8.273h1.818V3.727l1.818,1.818.909-.909L11,1,7.364,4.636l.909.909Z" transform="translate(-1 -1)"></path></svg>
                                            </div>
                                            <div class="cwp-repeating-single-field-collapse">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.646 4.646a.5.5 0 0 1 .708 0l6 6a.5.5 0 0 1-.708.708L8 5.707l-5.646 5.647a.5.5 0 0 1-.708-.708l6-6z"/></svg>
                                            </div>
                                            <div class="cwp-repeating-single-field-remove">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" viewBox="0 0 20 20"><path d="M6.5 1h3a.5.5 0 0 1 .5.5v1H6v-1a.5.5 0 0 1 .5-.5ZM11 2.5v-1A1.5 1.5 0 0 0 9.5 0h-3A1.5 1.5 0 0 0 5 1.5v1H2.506a.58.58 0 0 0-.01 0H1.5a.5.5 0 0 0 0 1h.538l.853 10.66A2 2 0 0 0 4.885 16h6.23a2 2 0 0 0 1.994-1.84l.853-10.66h.538a.5.5 0 0 0 0-1h-.995a.59.59 0 0 0-.01 0H11Zm1.958 1-.846 10.58a1 1 0 0 1-.997.92h-6.23a1 1 0 0 1-.997-.92L3.042 3.5h9.916Zm-7.487 1a.5.5 0 0 1 .528.47l.5 8.5a.5.5 0 0 1-.998.06L5 5.03a.5.5 0 0 1 .47-.53Zm5.058 0a.5.5 0 0 1 .47.53l-.5 8.5a.5.5 0 1 1-.998-.06l.5-8.5a.5.5 0 0 1 .528-.47ZM8 4.5a.5.5 0 0 1 .5.5v8.5a.5.5 0 0 1-1 0V5a.5.5 0 0 1 .5-.5Z"/>
                                                </svg>
                                            </div>
                                        </div>
                                        <div class="cwp-repeating-single-field-content">';
                                            foreach($args['sub_fields'] as $sub_field){
                                                $sub_field['custom_name']  =  'cwp_user_form[cwp_meta]['. $args['name'] .'][' . $sub_field['name'] . '][]';
                                                $sub_field['value']        =  isset($args['value'][$i][$sub_field['name']]) ? $args['value'][$i][$sub_field['name']] : '';
                                                $sub_field['id']           =  'cwp_'.rand(123456789, 1111111111);
                                                if($sub_field['type'] == 'google_address' ){
                                                    $sub_field['custom_name_lat'] =   'cwp_user_form[cwp_meta]['.$args['name'].'][' . $sub_field['name'].'_lat' . '][]';
                                                    $sub_field['custom_name_lng'] =   'cwp_user_form[cwp_meta]['.$args['name'].'][' . $sub_field['name'].'_lng' . '][]';

                                                    $sub_field['lat'] = isset($args['value'][$i][$fieldArgs['name'] . '_lat']) ? $args['value'][$i][$fieldArgs['name'] . '_lat'] : '';
                                                    $sub_field['lng'] = isset($args['value'][$i][$fieldArgs['name'] . '_lat']) ? $args['value'][$i][$fieldArgs['name'] . '_lng'] : '';
                                                }
                                                if($sub_field['type'] == 'radio' || $sub_field['type'] == 'checkbox' || ($sub_field['type'] == 'dropdown' && (isset($sub_field['multiple']) && $sub_field['multiple'] == true))){
                                                    $sub_field['custom_name']    =  'cwp_user_form[cwp_meta]['. $args['name'] .'][' . $sub_field['name'] . '][{{row-count-placeholder}}]';
                                                }
                                                if ($sub_field['type'] == 'post' && ($sub_field['appearance'] == 'multi_select' || $sub_field['appearance'] == 'checkbox')) {
                                                    $sub_field['custom_name']    =  'cwp_user_form[cwp_meta]['. $args['name'] .'][' . $sub_field['name'] . '][{{row-count-placeholder}}]';
                                                }
                                                if ($sub_field['type'] == 'gallery') {
                                                    $sub_field['custom_name']    =  'cwp_user_form[cwp_meta]['. $args['name'] .'][' . $sub_field['name'] . '][{{row-count-placeholder}}]';
                                                }
                                                $sub_field = apply_filters("cubewp/frontend/post/repeating_field/args", $sub_field);
                                                $output .= apply_filters( "cubewp/frontend/{$sub_field['type']}/field", '', $sub_field );
                                            }
                                        $output .= '</div>
                                    </div>
                                </div>';
                            }
                        }

                    $output .= '</div>
                   <button class="cwp-add-new-repeating-field" type="button">
                       ' . esc_html__('Add New', 'cubewp-framework') . '
                    </button>
                </div>
            </div>';

        $output .= '</div>';

        $output = apply_filters("cubewp/frontend/{$args['name']}/field", $output, $args);
        
        return $output;
        
    }

        /**
     * Method render_repeater_field
     *
     * @param string $output
     * @param array $args
     *
     * @return string html
     * @since  1.0.0
     */
    public function render_profile_repeater_field( $output = '', $args = array() ) {

        wp_enqueue_script('jquery-ui-sortable');
        wp_enqueue_script('cwp-repeating-fields');
        
        $args    =  apply_filters( 'cubewp/frontend/field/parametrs', $args );
        $required       = self::cwp_frontend_field_required($args['required']);
        $args['class']  = !empty($required['class']) ? $required['class'] : $args['class'];
        $output         = self::cwp_frontend_post_field_container($args);
        $subFields = $args['sub_fields'];
            $output .= self::cwp_frontend_field_label($args);
            $output .= '<div class="cwp-repeating-field-container">
                <div class="cwp-repeating-field-wrapper">
                    <div class="cwp-repeating-single-field-container">
                        <div class="cwp-repeating-single-field-template cwp-repeating-single-field-row">
                            <div class="cwp-repeating-single-field">
                                <div class="cwp-repeating-single-field-actions">
                                    <div class="cwp-repeating-single-field-move">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20"><path class="a" d="M8.273,11.909V10.091H3.727L5.545,8.273l-.909-.909L1,11l3.636,3.636.909-.909L3.727,11.909Zm9.091-4.545-.909.909,1.818,1.818H13.727v1.818h4.545l-1.818,1.818.909.909L21,11ZM11.909,18.273V13.727H10.091v4.545L8.273,16.455l-.909.909L11,21l2.357-2.357,1.28-1.28-.909-.909ZM10.091,3.727V8.273h1.818V3.727l1.818,1.818.909-.909L11,1,7.364,4.636l.909.909Z" transform="translate(-1 -1)"/></svg>
                                    </div>
                                    <div class="cwp-repeating-single-field-collapse">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.646 4.646a.5.5 0 0 1 .708 0l6 6a.5.5 0 0 1-.708.708L8 5.707l-5.646 5.647a.5.5 0 0 1-.708-.708l6-6z"/></svg>
                                    </div>
                                    <div class="cwp-repeating-single-field-remove">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" viewBox="0 0 20 20"><path d="M6.5 1h3a.5.5 0 0 1 .5.5v1H6v-1a.5.5 0 0 1 .5-.5ZM11 2.5v-1A1.5 1.5 0 0 0 9.5 0h-3A1.5 1.5 0 0 0 5 1.5v1H2.506a.58.58 0 0 0-.01 0H1.5a.5.5 0 0 0 0 1h.538l.853 10.66A2 2 0 0 0 4.885 16h6.23a2 2 0 0 0 1.994-1.84l.853-10.66h.538a.5.5 0 0 0 0-1h-.995a.59.59 0 0 0-.01 0H11Zm1.958 1-.846 10.58a1 1 0 0 1-.997.92h-6.23a1 1 0 0 1-.997-.92L3.042 3.5h9.916Zm-7.487 1a.5.5 0 0 1 .528.47l.5 8.5a.5.5 0 0 1-.998.06L5 5.03a.5.5 0 0 1 .47-.53Zm5.058 0a.5.5 0 0 1 .47.53l-.5 8.5a.5.5 0 1 1-.998-.06l.5-8.5a.5.5 0 0 1 .528-.47ZM8 4.5a.5.5 0 0 1 .5.5v8.5a.5.5 0 0 1-1 0V5a.5.5 0 0 1 .5-.5Z"/>
                                        </svg>
                                    </div>
                                </div>
                                <div class="cwp-repeating-single-field-content {{row-count-placeholder}}">';
                                    if (!empty($subFields) && is_array($subFields)) {
                                        foreach ($subFields as $k => $fieldArgs) {
                                            $fieldArgs['custom_name'] =   'cwp_user_profile[custom_fields]['. $args['name'] .'][' . $fieldArgs['name'] . '][]';
                                            $fieldArgs['value']       =  isset($fieldArgs['default_value']) ? $fieldArgs['default_value'] : '';
                                            $fieldArgs['id'] = $fieldArgs['id'] . '{{row-count-placeholder}}';
                                            if($fieldArgs['type'] == 'google_address' ){
                                                $fieldArgs['custom_name_lat'] =   'cwp_user_profile[custom_fields]['.$args['name'].'][' . $fieldArgs['name'].'_lat' . '][]';
                                                $fieldArgs['custom_name_lng'] =   'cwp_user_profile[custom_fields]['.$args['name'].'][' . $fieldArgs['name'].'_lng' . '][]';
                                            }
                                            if($fieldArgs['type'] == 'radio' || $fieldArgs['type'] == 'checkbox' || ($fieldArgs['type'] == 'dropdown' && (isset($fieldArgs['multiple']) && $fieldArgs['multiple'] == true))){
                                                $fieldArgs['custom_name']    =  'cwp_user_profile[custom_fields]['. $args['name'] .'][' . $fieldArgs['name'] . '][{{row-count-placeholder}}]';
                                            }
                                            $fieldArgs = apply_filters("cubewp/frontend/profile/repeating_field/args", $fieldArgs);
                                            $output .= apply_filters("cubewp/frontend/{$fieldArgs['type']}/field", '', $fieldArgs);
                                        }
                                    }
                                $output .= '</div>
                            </div>
                        </div>';
                        $output .= '<input type="hidden" name="cwp_user_profile[custom_fields][' . $args['name'] . '][]" value="">';
                        if(isset($args['value']) && !empty($args['value'])){
                            for($i = 0; $i < count($args['value']); $i++){
                                $output .= '<div class="cwp-repeating-single-field-row">
                                    <div class="cwp-repeating-single-field">
                                        <div class="cwp-repeating-single-field-actions">
                                            <div class="cwp-repeating-single-field-move ui-sortable-handle">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20"><path class="a" d="M8.273,11.909V10.091H3.727L5.545,8.273l-.909-.909L1,11l3.636,3.636.909-.909L3.727,11.909Zm9.091-4.545-.909.909,1.818,1.818H13.727v1.818h4.545l-1.818,1.818.909.909L21,11ZM11.909,18.273V13.727H10.091v4.545L8.273,16.455l-.909.909L11,21l2.357-2.357,1.28-1.28-.909-.909ZM10.091,3.727V8.273h1.818V3.727l1.818,1.818.909-.909L11,1,7.364,4.636l.909.909Z" transform="translate(-1 -1)"></path></svg>
                                            </div>
                                            <div class="cwp-repeating-single-field-collapse">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.646 4.646a.5.5 0 0 1 .708 0l6 6a.5.5 0 0 1-.708.708L8 5.707l-5.646 5.647a.5.5 0 0 1-.708-.708l6-6z"/></svg>
                                            </div>
                                            <div class="cwp-repeating-single-field-remove">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" viewBox="0 0 20 20"><path d="M6.5 1h3a.5.5 0 0 1 .5.5v1H6v-1a.5.5 0 0 1 .5-.5ZM11 2.5v-1A1.5 1.5 0 0 0 9.5 0h-3A1.5 1.5 0 0 0 5 1.5v1H2.506a.58.58 0 0 0-.01 0H1.5a.5.5 0 0 0 0 1h.538l.853 10.66A2 2 0 0 0 4.885 16h6.23a2 2 0 0 0 1.994-1.84l.853-10.66h.538a.5.5 0 0 0 0-1h-.995a.59.59 0 0 0-.01 0H11Zm1.958 1-.846 10.58a1 1 0 0 1-.997.92h-6.23a1 1 0 0 1-.997-.92L3.042 3.5h9.916Zm-7.487 1a.5.5 0 0 1 .528.47l.5 8.5a.5.5 0 0 1-.998.06L5 5.03a.5.5 0 0 1 .47-.53Zm5.058 0a.5.5 0 0 1 .47.53l-.5 8.5a.5.5 0 1 1-.998-.06l.5-8.5a.5.5 0 0 1 .528-.47ZM8 4.5a.5.5 0 0 1 .5.5v8.5a.5.5 0 0 1-1 0V5a.5.5 0 0 1 .5-.5Z"/>
                                                </svg>
                                            </div>
                                        </div>
                                        <div class="cwp-repeating-single-field-content">';
                                            foreach($args['sub_fields'] as $sub_field){
                                                $sub_field['custom_name']  =  'cwp_user_profile[custom_fields]['. $args['name'] .'][' . $sub_field['name'] . '][]';
                                                $sub_field['value']        =  isset($args['value'][$i][$sub_field['name']]) ? $args['value'][$i][$sub_field['name']] : '';
                                                $sub_field['id']           =  'cwp_'.rand(123456789, 1111111111);
                                                if($sub_field['type'] == 'google_address' ){
                                                    $sub_field['custom_name_lat'] =   'cwp_user_profile[custom_fields]['.$args['name'].'][' . $sub_field['name'].'_lat' . '][]';
                                                    $sub_field['custom_name_lng'] =   'cwp_user_profile[custom_fields]['.$args['name'].'][' . $sub_field['name'].'_lng' . '][]';
                                                    $sub_field['lat'] = $args['value'][$i][$fieldArgs['name'] . '_lat'];
                                                    $sub_field['lng'] = $args['value'][$i][$fieldArgs['name'] . '_lng'];
                                                }
                                                if($sub_field['type'] == 'radio' || $sub_field['type'] == 'checkbox' || ($sub_field['type'] == 'dropdown' && (isset($sub_field['multiple']) && $sub_field['multiple'] == true))){
                                                    $sub_field['custom_name']    =  'cwp_user_profile[custom_fields]['. $args['name'] .'][' . $sub_field['name'] . '][{{row-count-placeholder}}]';
                                                }
                                                $sub_field = apply_filters("cubewp/frontend/profile/repeating_field/args", $sub_field);
                                                $output .= apply_filters( "cubewp/frontend/{$sub_field['type']}/field", '', $sub_field );
                                            }
                                        $output .= '</div>
                                    </div>
                                </div>';
                            }
                        }

                    $output .= '</div>
                   <button class="cwp-add-new-repeating-field" type="button">
                       ' . esc_html__('Add New', 'cubewp-framework') . '
                    </button>
                </div>
            </div>';

        $output .= '</div>';

        $output = apply_filters("cubewp/frontend/{$args['name']}/field", $output, $args);
        
        return $output;
        
    }
    
}
new CubeWp_Frontend_Repeater_Field();