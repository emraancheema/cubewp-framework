<?php
/**
 * CubeWp frontend range field
 *
 * @version 1.0
 * @package cubewp/cube/fields/frontend
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * CubeWp_Frontend_Range_Field
 */
class CubeWp_Frontend_Range_Field extends CubeWp_Frontend {
    
    public function __construct( ) {
        add_filter('cubewp/frontend/range/field', array($this, 'render_range_field'), 10, 2);
        add_filter('cubewp/user/registration/range/field', array($this, 'render_range_field'), 10, 2);
        add_filter('cubewp/user/profile/range/field', array($this, 'render_range_field'), 10, 2);

        add_filter('cubewp/search_filters/range/field', array($this, 'render_range_field'), 10, 2);
        add_filter('cubewp/frontend/search/range/field', array($this, 'render_range_field'), 10, 2);
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
    public function render_range_field( $output = '', $args = array() ) {
	    $args = apply_filters( 'cubewp/frontend/field/parametrs', $args );
	    $args['extra_attrs'] = isset($args['extra_attrs']) ? $args['extra_attrs'] : '';
	    if (isset($args["minimum_value"]) && !empty($args["minimum_value"])) {
		    $args['extra_attrs'] .= ' min="' . $args["minimum_value"] . '"';
	    }
	    if (isset($args["maximum_value"]) && !empty($args["maximum_value"])) {
		    $args['extra_attrs'] .= ' max="' . $args["maximum_value"] . '"';
	    }
	    if (isset($args["steps_count"]) && !empty($args["steps_count"])) {
		    $args['extra_attrs'] .= ' step="' . $args["steps_count"] . '"';
	    }
        
	    return apply_filters("cubewp/frontend/text/field", $output, $args);
    }
    
}
new CubeWp_Frontend_Range_Field();