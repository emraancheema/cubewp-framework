<?php
/**
 * CubeWp admin taxonomy field
 *
 * @version 1.0
 * @package cubewp/cube/fields/frontend
 */

if ( ! defined('ABSPATH')) {
	exit;
}

/**
 * CubeWp_Frontend_Taxonomy_Field
 */
class CubeWp_Frontend_Taxonomy_Field extends CubeWp_Frontend {

	public function __construct() {
		add_filter('cubewp/frontend/taxonomy/field', array($this, 'render_taxonomy_field'), 10, 2);

		add_filter('cubewp/user/registration/taxonomy/field', array($this, 'render_taxonomy_field'), 10, 2);
		add_filter('cubewp/user/profile/taxonomy/field', array($this, 'render_taxonomy_field'), 10, 2);

		add_filter('cubewp/search_filters/taxonomy/field', array($this, 'render_search_filters_taxonomy_field'), 10, 2);
		add_filter('cubewp/frontend/search/taxonomy/field', array($this, 'render_search_taxonomy_field'), 10, 2);
	}

	/**
	 * Method render_taxonomy_field
	 *
	 * @param string $output
	 * @param array  $args
	 *
	 * @return string html
	 * @since  1.0.0
	 */
	public function render_taxonomy_field($output = '', $args = array()) {
		$args = apply_filters('cubewp/frontend/field/parametrs', $args);
		$args = self::cubewp_build_taxonomy_field_args($args, "forms");

		return apply_filters("cubewp/frontend/{$args['type']}/taxonomy/field", $output, $args);
	}

	/**
	 * Method render_search_filters_taxonomy_field
	 *
	 * @param string $output
	 * @param array  $args
	 *
	 * @return string html
	 * @since  1.0.0
	 */
	public function render_search_filters_taxonomy_field($output = '', $args = array()) {
		$args = apply_filters('cubewp/frontend/field/parametrs', $args);
		$args = self::cubewp_build_taxonomy_field_args($args, "filters");

		return apply_filters("cubewp/search_filters/{$args['type']}/taxonomy/field", '', $args);
	}

	/**
	 * Method render_search_taxonomy_field
	 *
	 * @param string $output
	 * @param array  $args
	 *
	 * @return string html
	 * @since  1.0.0
	 */
	public function render_search_taxonomy_field($output = '', $args = array()) {
		$args = apply_filters('cubewp/frontend/field/parametrs', $args);
		$args = self::cubewp_build_taxonomy_field_args($args, "search");

		return apply_filters("cubewp/frontend/search/{$args['type']}/taxonomy/field", '', $args);
	}

	private static function cubewp_build_taxonomy_field_args($args, $location) {
		if ($args['appearance'] == 'select') {
			$args['type'] = 'dropdown';
		} else if ($args['appearance'] == 'multi_select') {
			$args['type']  = 'dropdown';
			$args['multi'] = true;
		} else {
			$args['type'] = $args['appearance'];
		}

		if (isset($args['auto_complete']) && ! empty($args['auto_complete']) && $args['type'] == 'dropdown') {
			$args['select2_ui'] = true;
			$options = array();
			if ( ! empty($args['value']) && is_array($args['value'])) {
				foreach ($args['value'] as $term_id) {
					$term      = get_term_by('term_id', $term_id, $args['filter_taxonomy']);
					if ( ! empty( $term ) ) {
						$options[] = array(
							"term_id"   => $term_id,
							"term_name" => esc_html($term->name),
						);
					}
				}
			} else if ( ! empty($args['value']) && ! is_array($args['value'])) {
				$term      = get_term_by('term_id', $args['value'], $args['filter_taxonomy']);
				if ( ! empty( $term ) ) {
					$options[] = array(
						"term_id"   => $args['value'],
						"term_name" => esc_html( $term->name ),
					);
				}
			}
			$args['options'] = $options;
			$args['class']  = $args['class'] . ' cubewp-remote-options ';
			$args['extra_attrs']  = $args['extra_attrs'] . ' data-dropdown-type="taxonomy" data-dropdown-values="' . $args['filter_taxonomy'] . '" ';
		}else {
			if ($location == 'forms') {
				$args['options'] = cwp_get_terms($args['filter_taxonomy']);
			}else {
				$args['options']  =   cwp_get_terms($args['name']);
			}
		}

		if ($location == 'forms' || $location == 'filters') {
			if (empty($args['container_attrs'])) {
				$args['class'] = $args['class'] . ' cwp-taxonomy-field';
			} else {
				$args['class'] = $args['class'] . ' relationship-taxonomy-field';
			}
		}

		return $args;
	}
}

new CubeWp_Frontend_Taxonomy_Field();