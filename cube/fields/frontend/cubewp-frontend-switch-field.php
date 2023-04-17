<?php
/**
 * CubeWp admin repeating field 
 *
 * @version 1.0
 * @package cubewp/cube/fields/frontend
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * CubeWp_Frontend_Switch_Field
 */
class CubeWp_Frontend_Switch_Field extends CubeWp_Frontend {
    
    public function __construct( ) {
        add_filter('cubewp/frontend/switch/field', array($this, 'render_switch_field'), 10, 2);
        
        add_filter('cubewp/user/registration/switch/field', array($this, 'render_switch_field'), 10, 2);
        add_filter('cubewp/user/profile/switch/field', array($this, 'render_switch_field'), 10, 2);
        add_filter('cubewp/search_filters/switch/field', array($this, 'render_search_switch_field'), 10, 2);
        add_filter('cubewp/frontend/search/switch/field', array($this, 'render_search_switch_field'), 10, 2);
    }
        
    /**
     * Method render_switch_field
     *
     * @param string $output
     * @param array $args
     *
     * @return string html
     * @since  1.0.0
     */
    public function render_switch_field( $output = '', $args = array() ) {
		$args     = apply_filters( 'cubewp/frontend/field/parametrs', $args );
		$required = self::cwp_frontend_field_required( $args['required'] );
		$required = ! empty( $required['class'] ) ? $required['class'] : '';
		$checked = '';
		if ( isset( $args['value'] ) && $args['value'] == 'Yes' ) {
			$checked = 'checked="checked"';
		} else {
			$args['value'] = '';
		}
		$output = self::cwp_frontend_post_field_container( $args );
		$output .= '<div class="cwp-switch-container">';
		$output .= self::cwp_frontend_field_label( $args );
		$output .= '<label for="' . esc_attr( $args['id'] ) . '" class="cwp-field-switch-container cwp-switch">';
		$input_attrs = array(
			'name'  => ! empty( $args['custom_name'] ) ? $args['custom_name'] : $args['name'],
			'value' => $args['value'],
		);
		$output      .= cwp_render_hidden_input( $input_attrs );
		$input_attrs = array(
			'type'        => 'checkbox',
			'id'          => esc_attr( $args['id'] ),
			'name'        => '',
			'value'       => 1,
			'class'       => 'cwp-switch-field ' . $args['class'] . ' ' . $required,
			'extra_attrs' => $checked
		);
		$output      .= cwp_render_text_input( $input_attrs );
		$output .= '<span class="cwp-switch-slider"></span>
					<span class="cwp-switch-text-no">' . esc_html__( "No", "cubewp-framework" ) . '</span>
					<span class="cwp-switch-text-yes">' . esc_html__( "Yes", "cubewp-framework" ) . '</span>';
		$output .= '</label>';
		$output .= '</div>';
		$output .= '</div>';

		return apply_filters( "cubewp/frontend/{$args['name']}/field", $output, $args );
	}

	/**
     * Method render_switch_field
     *
     * @param string $output
     * @param array $args
     *
     * @return string html
     * @since  1.0.0
     */
    public function render_search_switch_field( $output = '', $args = array() ) {
		$args     = apply_filters( 'cubewp/frontend/field/parametrs', $args );
		$required = self::cwp_frontend_field_required( $args['required'] );
		$required = ! empty( $required['class'] ) ? $required['class'] : '';
		$checked = '';
		if ( isset( $args['value'] ) && $args['value'] == 'Yes' ) {
			$checked = 'checked="checked"';
		} else {
			$args['value'] = '';
		}
		$output = self::cwp_frontend_post_field_container( $args );
		$output .= '<div class="cwp-switch-container">';
		$output .= self::cwp_frontend_search_field_label( $args );
		$output .= '<label for="' . esc_attr( $args['id'] ) . '" class="cwp-field-switch-container cwp-switch">';
		$input_attrs = array(
			'name'  => ! empty( $args['custom_name'] ) ? $args['custom_name'] : $args['name'],
			'value' => $args['value'],
		);
		$output      .= cwp_render_hidden_input( $input_attrs );
		$input_attrs = array(
			'type'        => 'checkbox',
			'id'          => esc_attr( $args['id'] ),
			'name'        => '',
			'value'       => 1,
			'class'       => 'cwp-switch-field ' . $args['class'] . ' ' . $required,
			'extra_attrs' => $checked
		);
		$output      .= cwp_render_text_input( $input_attrs );
		$output .= '<span class="cwp-switch-slider"></span>
					<span class="cwp-switch-text-no">' . esc_html__( "No", "cubewp-framework" ) . '</span>
					<span class="cwp-switch-text-yes">' . esc_html__( "Yes", "cubewp-framework" ) . '</span>';
		$output .= '</label>';
		$output .= '</div>';
		$output .= '</div>';

		return apply_filters( "cubewp/frontend/{$args['name']}/field", $output, $args );
	}
    
}
new CubeWp_Frontend_Switch_Field();