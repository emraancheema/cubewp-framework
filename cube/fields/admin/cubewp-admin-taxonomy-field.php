<?php
/**
 * CubeWp admin taxonomy field
 *
 * @version 1.0
 * @package cubewp/cube/fields/admin
 */

if ( ! defined('ABSPATH')) {
	exit;
}

/**
 * CubeWp_Admin_Taxonomy_Field
 */
class CubeWp_Admin_Taxonomy_Field extends CubeWp_Admin {

	public function __construct() {
		add_filter('cubewp/admin/post/taxonomy/field', array($this, 'render_taxonomy_field'), 10, 2);

		add_filter('cubewp/admin/taxonomies/taxonomy/customfield', array($this, 'render_taxonomy_custom_field'), 10, 2);
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
		$args = apply_filters('cubewp/admin/field/parametrs', $args);

		if ($args['appearance'] == 'select') {
			$args['type'] = 'dropdown';
		} else if ($args['appearance'] == 'multi_select') {
			$args['type']     = 'dropdown';
			$args['multiple'] = 1;
		} else {
			$args['type'] = $args['appearance'];
		}

		if (isset($args['auto_complete']) && ! empty($args['auto_complete']) && $args['type'] == 'dropdown') {
			$args['select2_ui'] = true;
			$options = array();
			if ( ! empty($args['value']) && is_array($args['value'])) {
				foreach ($args['value'] as $term_id) {
					if(!empty($term_id)){
						$term      = get_term_by('term_id', $term_id, $args['filter_taxonomy'], true);
						$options[$term_id] = esc_html($term->name);
					}
				}
			} else if ( ! empty($args['value']) && ! is_array($args['value'])) {
				$term      = get_term_by('term_id', $args['value'], $args['filter_taxonomy']);
				$options[$args['value']] = esc_html($term->name);
			}
			$args['options']     = $options;
			$args['class']       = $args['class'] . ' cubewp-remote-options ';
			$extra_attrs = isset($args['extra_attrs']) ? $args['extra_attrs'] : '';
            $args['extra_attrs'] = $extra_attrs . ' data-dropdown-type="taxonomy" data-dropdown-values="' . $args['filter_taxonomy'] . '" ';
		} else {
			$args['options'] = cwp_get_categories_by_taxonomy($args['filter_taxonomy']);
		}

		return apply_filters("cubewp/admin/post/{$args['type']}/field", '', $args);
	}

	/**
	 * Method render_taxonomy_custom_field
	 *
	 * @param string $output
	 * @param array  $FieldData
	 *
	 * @return string html
	 * @since  1.0.0
	 */
	public function render_taxonomy_custom_field($output = '', $FieldData = array()) {

		$taxonomies_list = get_taxonomies(['public' => true], 'objects');
		foreach ($taxonomies_list as $taxonomy) {
			$output  .= '<li class="pull-left">';
			$checked = '';
			if (isset($FieldData['value']) && in_array($taxonomy->name, explode(',', $FieldData['value']))) {
				$checked = 'checked="checked"';
			}

			if ($taxonomy->name == 'category' || $taxonomy->name == 'post_tag' || $taxonomy->name == 'post_format') {
				$taxonomy_label = sprintf(__('%s (WP Core)'), $taxonomy->label);
			} else {
				$taxonomy_label = $taxonomy->label;
			}

			$output .= '<input type="checkbox" id="' . $taxonomy->name . '" name="' . $FieldData['name'] . '" value="' . $taxonomy->name . '" ' . $checked . '>';
			$output .= '<label for="' . $taxonomy->name . '">' . esc_html($taxonomy_label) . '</label><br>';
			$output .= '</li>';
		}

		return $output;
	}

}

new CubeWp_Admin_Taxonomy_Field();