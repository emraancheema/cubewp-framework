<?php
/**
 * CubeWp admin range field
 *
 * @version 1.0
 * @package cubewp/cube/fields/admin
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * CubeWp_Admin_Range_Field
 */
class CubeWp_Admin_Range_Field extends CubeWp_Admin {
    
    public function __construct( ) {
        add_filter('cubewp/admin/post/range/field', array($this, 'render_range_field'), 10, 2);
    }
        
    /**
     * Method render_range_field
     *
     * @param string $output 
     * @param array $args 
     *
     * @return string html
     * @since  1.0.0
     */
    public function render_range_field( $output = '', $args = array() ) {
	    $args   =  apply_filters( 'cubewp/admin/field/parametrs', $args );
	    $args['extra_attrs'] = isset($args['extra_attrs']) ? $args['extra_attrs'] : '';
	    $args['class'] = isset($args['class']) ? $args['class'] . ' cwp-field-range' : 'cwp-field-range';
		if (isset($args["minimum_value"]) && !empty($args["minimum_value"])) {
			$args['extra_attrs'] .= ' min="' . $args["minimum_value"] . '"';
		}
		if (isset($args["maximum_value"]) && !empty($args["maximum_value"])) {
			$args['extra_attrs'] .= ' max="' . $args["maximum_value"] . '"';
		}
		if (isset($args["steps_count"]) && !empty($args["steps_count"])) {
			$args['extra_attrs'] .= ' step="' . $args["steps_count"] . '"';
		}
	    return apply_filters("cubewp/admin/post/text/field", $output, $args);
    }
    
}
new CubeWp_Admin_Range_Field();