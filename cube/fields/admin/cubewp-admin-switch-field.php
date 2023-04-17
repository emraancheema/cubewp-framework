<?php
/**
 * CubeWp admin Switch field 
 *
 * @version 1.0
 * @package cubewp/cube/fields/admin
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * CubeWp_Admin_Switch_Field
 */
class CubeWp_Admin_Switch_Field extends CubeWp_Admin {
    
    public function __construct( ) {
        add_filter('cubewp/admin/post/switch/field', array($this, 'render_switch_field'), 10, 2);
    }
        
    /**
     * Method render_switch_field
     *
     * @param string $output
     * @param array $args
     *
     * @return string html
     * @since  1.0.0
     */
    public function render_switch_field( $output = '', $args = array() ) {

        $args   =  apply_filters( 'cubewp/admin/field/parametrs', $args );
        
        $output = $this->cwp_field_wrap_start($args);
        
            $checked = '';
            if( isset($args['value']) && $args['value'] == 'Yes' ){
                $checked = 'checked="checked"';
            }else{
                $args['value'] = 'No';
            }

            $output .= '<label class="cwp-switch" for="cwp-meta-'. $args['id'] .'">';
                $input_attrs = array( 
                    'name'         => !empty($args['custom_name']) ? $args['custom_name'] : $args['name'],
                    'value'        => $args['value'],
                );
                $output .= cwp_render_hidden_input( $input_attrs );
                $input_attrs = array( 
                    'type'         => 'checkbox',
                    'id'           => 'cwp-meta-'. $args['id'] .'',
                    'class'        => 'cwp-switch-field switch-field',
                    'name'         => '',
                    'value'        => 1,
                    'extra_attrs'  => $checked
                );
                if(isset($args['class']) && !empty($args['class'])){
                    $input_attrs['class'] .=  ' '.$args['class'];
                }

                $output .= cwp_render_text_input( $input_attrs );
                $output .= '<span class="cwp-switch-slider"></span>
                <span class="cwp-switch-text-no">' . esc_html__("No", "cubewp-framework") . '</span>
                <span class="cwp-switch-text-yes">' . esc_html__("Yes", "cubewp-framework") . '</span>
            </label>';
                
        $output .= $this->cwp_field_wrap_end($args);
        
        $output = apply_filters("cubewp/admin/{$args['name']}/field", $output, $args);
        return $output;
    }
    
}
new CubeWp_Admin_Switch_Field();