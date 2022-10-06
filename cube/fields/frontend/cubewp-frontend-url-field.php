<?php
/**
 * CubeWp admin url field 
 *
 * @version 1.0
 * @package cubewp/cube/fields/frontend
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * CubeWp_Frontend_URL_Field
 */
class CubeWp_Frontend_URL_Field extends CubeWp_Frontend {
    
    public function __construct( ) {
        add_filter('cubewp/frontend/url/field', array($this, 'render_url_field'), 10, 2);
        
        add_filter('cubewp/user/registration/url/field', array($this, 'render_url_field'), 10, 2);
        add_filter('cubewp/user/profile/url/field', array($this, 'render_url_field'), 10, 2);
    }
        
    /**
     * Method render_url_field
     *
     * @param string $output
     * @param array $args
     *
     * @return string html
     * @since  1.0.0
     */
    public function render_url_field( $output = '', $args = array() ) {
        
        $output = apply_filters("cubewp/frontend/text/field", $output, $args);
        return $output;
        
    }
    
}
new CubeWp_Frontend_URL_Field();