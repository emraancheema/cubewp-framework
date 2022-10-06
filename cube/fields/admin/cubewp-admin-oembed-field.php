<?php
/**
 * CubeWp admin oembed field 
 *
 * @version 1.0
 * @package cubewp/cube/fields/admin
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * CubeWp_Admin_oEmbed_Field
 */
class CubeWp_Admin_oEmbed_Field extends CubeWp_Admin {
    
    public function __construct( ) {
        add_filter('cubewp/admin/post/oembed/field', array($this, 'render_oembed_field'), 10, 2);
    }
        
    /**
     * Method render_oembed_field
     *
     * @param string $output
     * @param array $args
     *
     * @return string html
     * @since  1.0.0
     */
    public function render_oembed_field( $output = '', $args = array() ) {
        $args   =  apply_filters( 'cubewp/admin/field/parametrs', $args );
        $args['type'] = 'text'; 
        $output = apply_filters("cubewp/admin/post/text/field", $output, $args);
        return $output;
        
    }
    
}
new CubeWp_Admin_oEmbed_Field();