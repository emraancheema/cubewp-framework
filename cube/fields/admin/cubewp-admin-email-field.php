<?php
/**
 * CubeWp admin email field 
 *
 * @version 1.0
 * @package cubewp/cube/fields/admin
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * CubeWp_Admin_Email_Field
 */
class CubeWp_Admin_Email_Field extends CubeWp_Admin {
    
    public function __construct( ) {
        add_filter('cubewp/admin/post/email/field', array($this, 'render_email_field'), 10, 2);
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

        $output = apply_filters("cubewp/admin/post/text/field", $output, $args);
        return $output;
        
    }
    
}
new CubeWp_Admin_Email_Field();