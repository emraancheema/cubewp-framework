<?php
/**
 * CubeWp admin number field 
 *
 * @version 1.0
 * @package cubewp/cube/fields/admin
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * CubeWp_Admin_Number_Field
 */
class CubeWp_Admin_Number_Field extends CubeWp_Admin {
    
    public function __construct( ) {
        add_filter('cubewp/admin/post/number/field', array($this, 'render_number_field'), 10, 2);
        add_filter('cubewp/admin/number/customfield', array($this, 'render_custom_field_number_field'), 10, 2);
    }
        
    /**
     * Method render_number_field
     *
     * @param string $output
     * @param array $args
     *
     * @return string html
     * @since  1.0.0
     */
    public function render_number_field( $output = '', $args = array() ) {

        $output = apply_filters("cubewp/admin/post/text/field", $output, $args);
        return $output;
        
    }

    /**
     * Method render_number_field
     *
     * @param string $output
     * @param array $args
     *
     * @return string html
     * @since  1.0.0
     */
    public function render_custom_field_number_field( $output = '', $args = array() ) {

        $output = apply_filters("cubewp/admin/text/customfield", $output, $args);
        return $output;
        
    }
    
}
new CubeWp_Admin_Number_Field();