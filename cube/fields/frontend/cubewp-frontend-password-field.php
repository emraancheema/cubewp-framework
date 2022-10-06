<?php
/**
 * CubeWp admin password field 
 *
 * @version 1.0
 * @package cubewp/cube/fields/frontend
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * CubeWp_Frontend_Password_Field
 */
class CubeWp_Frontend_Password_Field extends CubeWp_Frontend {
    
    public function __construct( ) {
        add_filter('cubewp/frontend/password/field', array($this, 'render_password_field'), 10, 2);
        
        add_filter('cubewp/user/registration/password/field', array($this, 'render_password_field'), 10, 2);
        add_filter('cubewp/user/profile/password/field', array($this, 'render_password_field'), 10, 2);
    }
        
    /**
     * Method render_password_field
     *
     * @param string $output
     * @param array $args
     *
     * @return string html
     * @since  1.0.0
     */
    public function render_password_field( $output = '', $args = array() ) {
        
        $output = apply_filters("cubewp/frontend/text/field", $output, $args);
        return $output;
        
    }
    
}
new CubeWp_Frontend_Password_Field();