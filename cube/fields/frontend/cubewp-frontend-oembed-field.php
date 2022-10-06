<?php
/**
 * CubeWp admin number field 
 *
 * @version 1.0
 * @package cubewp/cube/fields/frontend
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * CubeWp_Frontend_oEmbed_Field
 */
class CubeWp_Frontend_oEmbed_Field extends CubeWp_Frontend {
    
    public function __construct( ) {
        add_filter('cubewp/frontend/oembed/field', array($this, 'render_oembed_field'), 10, 2);
        
        add_filter('cubewp/user/registration/oembed/field', array($this, 'render_oembed_field'), 10, 2);
        add_filter('cubewp/user/profile/oembed/field', array($this, 'render_oembed_field'), 10, 2);
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

        $args   =  apply_filters( 'cubewp/frontend/field/parametrs', $args );
        $args['type'] = 'text'; 
        $output = apply_filters("cubewp/frontend/text/field", $output, $args);
        
        $output = apply_filters("cubewp/frontend/{$args['name']}/field", $output, $args);
        
        return $output;
    }
    
}
new CubeWp_Frontend_oEmbed_Field();