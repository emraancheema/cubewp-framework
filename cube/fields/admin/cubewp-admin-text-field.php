<?php
/**
 * CubeWp admin text field 
 *
 * @version 1.0
 * @package cubewp/cube/fields/admin
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * CubeWp_Admin_Text_Field
 */
class CubeWp_Admin_Text_Field extends CubeWp_Admin {
    
    public function __construct( ) {
        add_filter('cubewp/admin/post_type/text/field', array($this, 'render_text_field'), 10, 2);
        add_filter('cubewp/admin/taxonomy/text/field', array($this, 'render_text_field'), 10, 2);
        add_filter('cubewp/admin/dashboard/text/field', array($this, 'render_text_field'), 10, 2);
        
        add_filter('cubewp/admin/group/text/field', array($this, 'render_text_field'), 10, 2);
        
        add_filter('cubewp/admin/post/text/field', array($this, 'render_text_field'), 10, 2);
        
        add_filter('cubewp/admin/text/customfield', array($this, 'render_text_custom_field'), 10, 2);
        add_filter('cubewp/admin/taxonomies/text/customfield', array($this, 'render_text_custom_field'), 10, 2);
    }
        
    /**
     * Method render_text_field
     *
     * @param string $output
     * @param array $args
     *
     * @return string html
     * @since  1.0.0
     */
    public function render_text_field( $output = '', $args = array() ) {

        $args   =  apply_filters( 'cubewp/admin/field/parametrs', $args );

        $output = $this->cwp_field_wrap_start($args);
            $input_attrs = array( 
                'type'         => isset($args['type']) ? $args['type'] : 'text',
                'id'           => $args['id'],
                'class'        => $args['class'],
                'name'         => !empty($args['custom_name']) ? $args['custom_name'] : $args['name'],
                'value'        => $args['value'],
                'placeholder'  => $args['placeholder']
            );

            $extra_attrs = isset($args['extra_attrs']) ? $args['extra_attrs'] : '';
            if(isset($args['required']) && $args['required'] == 1){
                $input_attrs['class'] .= ' required';
                $validation_msg = isset($args['validation_msg']) ? $args['validation_msg'] : '';
                $extra_attrs .= ' data-validation_msg="'. $validation_msg .'"';
            }
            $input_attrs['extra_attrs'] = $extra_attrs;
            if (isset($args['char_limit']) && ! empty($args['char_limit']) && is_numeric($args['char_limit'])) {
                $input_attrs['extra_attrs'] .= ' maxlength="' . $args['char_limit'] . '" ';
            }
            $output .= cwp_render_text_input( $input_attrs );

        $output .= $this->cwp_field_wrap_end($args);
        
        $output =  apply_filters("cubewp/admin/{$args['name']}/field", $output, $args);
        
        return $output;
    }
        
    /**
     * Method render_text_custom_field
     *
     * @param string $output
     * @param array $FieldData
     *
     * @return string html
     * @since  1.0.0
     */
    public function render_text_custom_field($output = '', $FieldData = array()){
        if(isset($FieldData['tr_class'])){
            $output = '<tr class="'.$FieldData['tr_class'].'" '.$FieldData['tr_extra_attr'].' >';
        }else{
            $output = self::cwp_tr_start($FieldData);
        }
        $class = explode(' ', $FieldData['class']);
        $is_switch = false;
        if (in_array('cwp-switch-check', $class)) {
            $is_switch = true;
            $FieldData['class'] .= ' cwp-switch-field ';
        }
        $tooltip = isset($FieldData['tooltip']) && !empty($FieldData['tooltip']) ? $FieldData['tooltip'] : '';        
        $required = isset($FieldData['required']) && !empty($FieldData['required']) ? $FieldData['required'] : '';        
        $output .= self::cwp_td_start().self::cwp_label( $FieldData['id'], $FieldData['label'], $required, $tooltip ).self::cwp_td_end();
        $input_attrs = array( 
            'type'         => isset($FieldData['type']) ? $FieldData['type'] : 'text',
            'id'           => $FieldData['id'],
            'class'        => $FieldData['class'],
            'name'         => !empty($FieldData['custom_name']) ? $FieldData['custom_name'] : $FieldData['name'],
            'value'        => $FieldData['value'],
            'placeholder'  => $FieldData['placeholder'],
            'extra_attrs'  => isset($FieldData['extra_attrs']) && !empty($FieldData['extra_attrs']) ? $FieldData['extra_attrs'] : '',
        );

        $extra_attrs = isset($FieldData['extra_attrs']) ? $FieldData['extra_attrs'] : '';
        if(isset($FieldData['required']) && $FieldData['required'] == 1){
            $input_attrs['class'] .= ' required';
            $validation_msg = isset($FieldData['validation_msg']) ? $FieldData['validation_msg'] : '';
            $extra_attrs .= ' data-validation_msg="'. $validation_msg .'"';
        }
        $input_attrs['extra_attrs'] = $extra_attrs;

        
        if( isset($FieldData['checked']) && $FieldData['checked'] == 1 ){
            $input_attrs['extra_attrs'] = ' checked="checked"';
        }       
        
        if(isset($FieldData['type_input'])){
            $input_attrs['type'] = $FieldData['type_input'];
        }
        if(isset($FieldData['extra_label'])){
            $extraLabel = '<label for="'. $FieldData['id'] .'">'. $FieldData['extra_label'] .'</label>';
        }else{
            $extraLabel = '';
        }
        if(isset($FieldData['extra_input_name'])){
            $extraInput = '<input type="hidden" class="'.$FieldData['extra_input_class'].'" name="'.$FieldData['extra_input_name'].'" value="'. $FieldData['value'] .'" />';
        }else{
            $extraInput = '';
        }
        
        if ("cubewp-locked-field field-name" == $FieldData['class']) {
             $input_attrs["extra_attrs"] = $input_attrs["extra_attrs"] . ' readonly';
             $input = cwp_render_text_input($input_attrs);
             $input = '<div class="cubewp-locked-field">' . $input . '<span class="dashicons dashicons-lock"></span></div>';
        }else {
            $input = cwp_render_text_input($input_attrs);
        }
        if ($is_switch) {
            $extraLabel = '<label class="cwp-switch" for="'. $FieldData['id'] .'">
                  ' . $extraInput . '
                  ' . $input . '
                  <span class="cwp-switch-slider"></span>
                  <span class="cwp-switch-text-no">' . esc_html__("No", "cubewp-framework") . '</span>
                  <span class="cwp-switch-text-yes">' . esc_html__("Yes", "cubewp-framework") . '</span></label>';
            $output .= self::cwp_td_start().$extraLabel.self::cwp_td_end();
         }else {
            $output .= self::cwp_td_start().$extraInput.$input.$extraLabel.self::cwp_td_end();
         }
        $output .= self::cwp_tr_end();
        return $output;
    }
    
}
new CubeWp_Admin_Text_Field();