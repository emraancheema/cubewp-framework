<?php
/**
 * CubeWp admin email field 
 *
 * @version 1.0
 * @package cubewp/cube/fields/frontend
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * CubeWp_Frontend_Email_Field
 */
class CubeWp_Frontend_Email_Field extends CubeWp_Frontend {
    
    public function __construct( ) {
        add_filter('cubewp/frontend/email/field', array($this, 'render_email_field'), 10, 2);
        
        add_filter('cubewp/user/registration/email/field', array($this, 'render_email_field'), 10, 2);
        add_filter('cubewp/user/profile/email/field', array($this, 'render_email_field'), 10, 2);
    }
        
    /**
     * Method render_email_field
     *
     * @param string $output
     * @param array $args
     *
     * @return string html
     * @since  1.0.0
     */
    public function render_email_field( $output = '', $args = array() ) {
        
        $output = apply_filters("cubewp/frontend/text/field", $output, $args);
        return $output;
        
    }
    
}
new CubeWp_Frontend_Email_Field();