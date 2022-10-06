<?php
/**
 * CubeWp admin color field 
 *
 * @version 1.0
 * @package cubewp/cube/fields/admin
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * CubeWp_Admin_Color_Field
 */
class CubeWp_Admin_Color_Field extends CubeWp_Admin {
    
    public function __construct( ) {
        add_filter('cubewp/admin/post/color/field', array($this, 'render_color_field'), 10, 2);
    }
        
    /**
     * Method render_color_field
     *
     * @param string $output 
     * @param array $args 
     *
     * @return string html
     * @since  1.0.0
     */
    public function render_color_field( $output = '', $args = array() ) {

        $output = apply_filters("cubewp/admin/post/text/field", $output, $args);
        return $output;
        
    }
    
}
new CubeWp_Admin_Color_Field();