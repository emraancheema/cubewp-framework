<?php
/**
 * CubeWp admin post field
 *
 * @version 1.0
 * @package cubewp/cube/fields/frontend
 */

if ( ! defined('ABSPATH')) {
	exit;
}

/**
 * CubeWp_Frontend_Post_Field
 */
class CubeWp_Frontend_Post_Field extends CubeWp_Frontend {

	public function __construct() {
		add_filter('cubewp/frontend/post/field', array($this, 'render_post_field'), 10, 2);

		add_filter('cubewp/user/registration/post/field', array($this, 'render_post_field'), 10, 2);
		add_filter('cubewp/user/profile/post/field', array($this, 'render_post_field'), 10, 2);
	}

	/**
	 * Method render_post_field
	 *
	 * @param string $output
	 * @param array  $args
	 *
	 * @return string html
	 * @since  1.0.0
	 */
	public function render_post_field($output = '', $args = array()) {
		$args = apply_filters('cubewp/frontend/field/parametrs', $args);
		if ( isset($args['filter_post_types']) && is_singular($args['filter_post_types'])) {
			$args['value'] = get_the_ID();
		}

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
				foreach ($args['value'] as $post_id) {
					$options[$post_id] = esc_html(get_the_title($post_id));
				}
			} else if ( ! empty($args['value']) && ! is_array($args['value'])) {
				$options[$args['value']] = esc_html(get_the_title($args['value']));
			}
			$args['options']     = $options;
			$args['class']       = $args['class'] . ' cubewp-remote-options ';
			if ( isset( $args['current_user_posts'] ) && $args['current_user_posts'] ) {
				$args['extra_attrs'] = $args['extra_attrs'] . ' data-dropdown-type="user-posts" data-dropdown-values="' . $args['filter_post_types'] . '" ';
			}else {
				$args['extra_attrs'] = $args['extra_attrs'] . ' data-dropdown-type="post" data-dropdown-values="' . $args['filter_post_types'] . '" ';
			}
		} else {
			$query_args = array(
				'post_type'      => $args['filter_post_types'],
				'post_status'    => 'publish',
				'posts_per_page' => -1,
				'fields'         => 'ids'
			);
			if ( isset( $args['current_user_posts'] ) && $args['current_user_posts'] ) {
				if ( is_user_logged_in() ) {
					$query_args['author'] = get_current_user_id();
				}else {
					$query_args = array();
				}
			}
			if ( ! empty( $query_args ) ) {
				$posts      = get_posts($query_args);
				$options    = array();
				if (isset($posts) && ! empty($posts)) {
					foreach ($posts as $post_id) {
						$options[$post_id] = esc_html(get_the_title($post_id));
					}
				}
				$args['options'] = $options;
			}
		}

		return apply_filters("cubewp/frontend/{$args['type']}/field", $output, $args);
	}
}

new CubeWp_Frontend_Post_Field();