<?php
/**
 * CubeWp admin textarea field 
 *
 * @version 1.0
 * @package cubewp/cube/fields/admin
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class CubeWp_Admin_Textarea_Field extends CubeWp_Admin {
    
    public function __construct( ) {
        add_filter('cubewp/admin/post/textarea/field', array($this, 'render_textarea_field'), 10, 2);
        add_filter('cubewp/admin/dashboard/textarea/field', array($this, 'render_textarea_field'), 10, 2);
        
        add_filter('cubewp/admin/textarea/customfield', array($this, 'render_textarea_custom_field'), 10, 2);
        add_filter('cubewp/admin/taxonomies/textarea/customfield', array($this, 'render_textarea_custom_field'), 10, 2);
    }
        
    /**
     * Method render_textarea_field
     *
     * @param string $output
     * @param array $args
     *
     * @return string html
     * @since  1.0.0
     */
    public function render_textarea_field( $output = '', $args = array() ) {

        $args   =  apply_filters( 'cubewp/admin/field/parametrs', $args );
        
        $output = $this->cwp_field_wrap_start($args);
            $input_attrs = array( 
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

            $output .= cwp_render_textarea_input( $input_attrs );

        $output .= $this->cwp_field_wrap_end($args);
        
        $output =  apply_filters("cubewp/admin/{$args['name']}/field", $output, $args);
        
        return $output;
    }
        
    /**
     * Method render_textarea_custom_field
     *
     * @param string $output
     * @param array $args
     *
     * @return string html
     * @since  1.0.0
     */
    public function render_textarea_custom_field( $output = '', $args = array() ) {

        $args   =  apply_filters( 'cubewp/admin/field/parametrs', $args );
        
        $output = self::cwp_tr_start($args);
        $tooltip = isset($args['tooltip']) && !empty($args['tooltip']) ? $args['tooltip'] : '';        
        $required = isset($args['required']) && !empty($args['required']) ? $args['required'] : '';        
        $output .= self::cwp_td_start().self::cwp_label( $args['id'], $args['label'], $required, $tooltip ).self::cwp_td_end();
        $input_attrs = array( 
            'id'           => $args['id'],
            'class'        => $args['class'],
            'name'         => !empty($args['custom_name']) ? $args['custom_name'] : $args['name'],
            'value'        => $args['value'],
            'placeholder'  => $args['placeholder'],
            'rows'              => '5',
        );
        $extra_attrs = '';
        if(isset($args['required']) && $args['required'] == 1){
            $extra_attrs .= ' required';
        }
        if(isset($args['validation_msg']) && $args['validation_msg'] != ''){
            $extra_attrs .= ' data-validation_msg="'. $args['validation_msg'] .'"';
        }
        $input_attrs['extra_attrs'] = $extra_attrs;
        
        $output .= self::cwp_td_start();

        $output .= cwp_render_textarea_input( $input_attrs );

        $output .= $this->cwp_field_wrap_end($args);

        return $output;
    }
    
}
new CubeWp_Admin_Textarea_Field();