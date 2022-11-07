<?php
/**
 * CubeWp frontend color field
 *
 * @version 1.0
 * @package cubewp/cube/fields/frontend
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * CubeWp_Frontend_Color_Field
 */
class CubeWp_Frontend_Color_Field extends CubeWp_Frontend {
    
    public function __construct( ) {
        add_filter('cubewp/frontend/color/field', array($this, 'render_color_field'), 10, 2);
        add_filter('cubewp/user/registration/color/field', array($this, 'render_color_field'), 10, 2);
        add_filter('cubewp/user/profile/color/field', array($this, 'render_color_field'), 10, 2);
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
	    return apply_filters("cubewp/frontend/text/field", $output, $args);
    }
    
}
new CubeWp_Frontend_Color_Field();