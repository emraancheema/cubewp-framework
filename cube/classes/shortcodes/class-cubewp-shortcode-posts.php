<?php
defined('ABSPATH') || exit;

/**
 * CubeWP Posts Shortcode.
 *
 * @class CubeWp_Frontend_Posts_Shortcode
 */
class CubeWp_Shortcode_Posts
{

	public function __construct()
	{
		add_shortcode('cubewp_shortcode_posts', array($this, 'cubewp_shortcode_posts_callback'));
		add_filter('cubewp_shortcode_posts_output', array($this, 'cubewp_posts'), 10, 2);
		new CubeWp_Ajax('', 'CubeWp_Shortcode_Posts', 'cubewp_posts_output');
		new CubeWp_Ajax('wp_ajax_nopriv_', 'CubeWp_Shortcode_Posts', 'cubewp_posts_output');
	}

	public static function cubewp_posts($output, array $parameters)
	{
		return self::cubewp_posts_output($parameters);
	}

	public static function cubewp_posts_output($parameters)
	{

		// AJAX CALL
		if (wp_doing_ajax() && isset($_REQUEST['action']) && $_REQUEST['action'] === 'cubewp_posts_output') {
			// Set $parameters to $_POST if it's an AJAX request for 'cubewp_posts_output'
			$parameters = $_POST;
		}

		$cwp_enable_slider = isset($parameters['cwp_enable_slider']) ? $parameters['cwp_enable_slider'] : '';
		$prev_icon = isset($parameters['prev_icon']) ? $parameters['prev_icon'] : 'fas fa-chevron-left';
		$next_icon = isset($parameters['next_icon']) ? $parameters['next_icon'] : 'fas fa-chevron-right';
		$slides_to_show = isset($parameters['slides_to_show']) ? intval($parameters['slides_to_show']) : 3;
		$slides_to_scroll = isset($parameters['slides_to_scroll']) ? intval($parameters['slides_to_scroll']) : 1;
		$slides_to_show_tablet = isset($parameters['slides_to_show_tablet']) ? intval($parameters['slides_to_show_tablet']) : 3;
		$slides_to_show_tablet_portrait = isset($parameters['slides_to_show_tablet_portrait']) ? intval($parameters['slides_to_show_tablet_portrait']) : 2;
		$slides_to_show_mobile = isset($parameters['slides_to_show_mobile']) ? intval($parameters['slides_to_show_mobile']) : 1;
		$slides_to_scroll_tablet = isset($parameters['slides_to_scroll_tablet']) ? intval($parameters['slides_to_scroll_tablet']) : 1;
		$slides_to_scroll_tablet_portrait = isset($parameters['slides_to_scroll_tablet_portrait']) ? intval($parameters['slides_to_scroll_tablet_portrait']) : 1;
		$slides_to_scroll_mobile = isset($parameters['slides_to_scroll_mobile']) ? intval($parameters['slides_to_scroll_mobile']) : 1;
		$autoplay = isset($parameters['autoplay']) && $parameters['autoplay'] ? 'true' : 'false';
		$autoplay_speed = isset($parameters['autoplay_speed']) ? intval($parameters['autoplay_speed']) : 2000;
		$speed = isset($parameters['speed']) ? intval($parameters['speed']) : 500;
		$infinite = isset($parameters['infinite']) && $parameters['infinite'] ? 'true' : 'false';
		$variable_width = isset($parameters['variable_width']) && $parameters['variable_width'] ? 'true' : 'false';
		$custom_arrows = isset($parameters['custom_arrows']) && $parameters['custom_arrows'] ? 'true' : 'false';
		$custom_dots = isset($parameters['custom_dots']) && $parameters['custom_dots'] ? 'true' : 'false';
		$enable_progress_bar = isset($parameters['enable_progress_bar']) && $parameters['enable_progress_bar'] ? 'true' : 'false';

		
		$next_icon_type = isset($parameters['next_icon_type']) && $parameters['next_icon_type'] ? 'true' : 'false';
		$prev_icon_type = isset($parameters['prev_icon_type']) && $parameters['prev_icon_type'] ? 'true' : 'false';

		$args = array(
			'post_type'      => $parameters['post_type'],
			'orderby'        => $parameters['orderby'],
			'order'          => $parameters['order'],
			'page_num'          => 1,
			'meta_query'     => isset($parameters['meta_query']) ? $parameters['meta_query'] : array(),
		);

		if (isset($parameters['number_of_posts'])) {
			$args['posts_per_page'] = $parameters['number_of_posts'];
		}

		if (isset($parameters['posts_per_page'])) {
			$args['posts_per_page'] = $parameters['posts_per_page'];
		}

		if (isset($parameters['page_num'])) {
			$args['page_num'] = $parameters['page_num'];
		}

		$show_boosted_posts = '';
		if (class_exists('CubeWp_Booster_Load')) {
			$show_boosted_posts = $parameters['boosted_only'];
		}
		if (isset($parameters['post__in']) && ! empty($parameters['post__in']) && is_array($parameters['post__in'])) {
			$args['post__in'] = $parameters['post__in'];
		}
		if (isset($parameters['taxonomy']) && ! empty($parameters['taxonomy']) && is_array($parameters['taxonomy'])) {
			foreach ($parameters['taxonomy'] as $taxonomy) {
				if (isset($parameters[$taxonomy . '-terms']) && ! empty($parameters[$taxonomy . '-terms'])) {
					$terms           = $parameters[$taxonomy . '-terms'];
					$terms           = implode(',', $terms);
					$args[$taxonomy] = $terms;
				}
			}
		}

		$layout = $parameters['layout'];
		$row_class = 'grid-view';
		if ($layout == 'list') {
			$col_class = 'cwp-col-12';
			$row_class = 'list-view';
		}
		$query = new CubeWp_Query($args);
		$posts = $query->cubewp_post_query();
		$load_btn = $post_markup = '';
		$slider_class = $cwp_enable_slider === 'cubewp-post-slider' ? 'cubewp-post-slider' : '';
		$container_open = '<div class="cubewp-posts-shortcode cwp-row ' . esc_attr($slider_class) . '"';
		$container_open .= ' data-prev-arrow="' . esc_attr($prev_icon) . '"';
		$container_open .= ' data-next-arrow="' . esc_attr($next_icon) . '"';
		$container_open .= ' data-prev-icon-type="' . esc_attr($prev_icon_type) . '"';
		$container_open .= ' data-next-icon-type="' . esc_attr($next_icon_type) . '"';
		$container_open .= ' data-slides-to-show="' . esc_attr($slides_to_show) . '"';
		$container_open .= ' data-slides-to-scroll="' . esc_attr($slides_to_scroll) . '"';
		$container_open .= ' data-slides-to-show-tablet="' . esc_attr($slides_to_show_tablet) . '"';
		$container_open .= ' data-slides-show-tablet-portrait="' . esc_attr($slides_to_show_tablet_portrait) . '"';
		$container_open .= ' data-slides-to-show-mobile="' . esc_attr($slides_to_show_mobile) . '"';
		$container_open .= ' data-slides-to-scroll-tablet="' . esc_attr($slides_to_scroll_tablet) . '"';
		$container_open .= ' data-slides-scroll-tablet-portrait="' . esc_attr($slides_to_scroll_tablet_portrait) . '"';
		$container_open .= ' data-slides-to-scroll-mobile="' . esc_attr($slides_to_scroll_mobile) . '"';
		$container_open .= ' data-autoplay="' . esc_attr($autoplay) . '"';
		$container_open .= ' data-autoplay-speed="' . esc_attr($autoplay_speed) . '"';
		$container_open .= ' data-speed="' . esc_attr($speed) . '"';
		$container_open .= ' data-infinite="' . esc_attr($infinite) . '"';
		$container_open .= ' data-variable-width="' . esc_attr($variable_width) . '"';
		$container_open .= ' data-custom-arrows="' . esc_attr($custom_arrows) . '"';
		$container_open .= ' data-custom-dots="' . esc_attr($custom_dots) . '"';
		$container_open .= ' data-enable-progress-bar="' . esc_attr($enable_progress_bar) . '"';
		$container_open .= '>';
		$container_close = '</div>';

		if ($posts->have_posts()) {
			CubeWp_Enqueue::enqueue_style('cubewp-slick');
			CubeWp_Enqueue::enqueue_script('cubewp-slick');
			if ($show_boosted_posts == 'yes') {
				if (class_exists('CubeWp_Booster_Load')) {
					while ($posts->have_posts()): $posts->the_post();
						$post_type = get_post_type(get_the_ID());
						$style = isset($parameters['card_style'][$post_type]) ? $parameters['card_style'][$post_type] : '';
						if (function_exists('is_boosted')) {
							if (is_boosted(get_the_ID())) {
								$post_markup .= CubeWp_frontend_grid_HTML(get_the_ID(), '', $style);
							}
						}
					endwhile;
				}
			} else {
				while ($posts->have_posts()): $posts->the_post();
					$post_type = get_post_type(get_the_ID());
					$style = isset($parameters['card_style'][$post_type]) ? $parameters['card_style'][$post_type] : '';
					$post_markup .= CubeWp_frontend_grid_HTML(get_the_ID(), '', $style);
				endwhile;
			}
			if (isset($parameters['load_more']) && $parameters['load_more'] == 'yes') {
				if (isset($parameters['page_num'])) {
					$parameters['page_num'] = $parameters['page_num'] + 1;
				} else {
					$parameters['page_num'] = 2;
				}
				$has_more_posts = $args['page_num'] < $posts->max_num_pages;
				$dataAttributes = json_encode($parameters);
				CubeWp_Enqueue::enqueue_script('cwp-load-more');


				$load_btn .= '<div class="cubewp-load-more-conatiner">
					<button class="cubewp-load-more-button" data-attributes="' . htmlspecialchars($dataAttributes, ENT_QUOTES, 'UTF-8') . '">
						' . esc_html__('Load More', 'cubewp-framework') . '
					</button>
				</div>';
			}
		} else {
			$post_markup = self::cwp_no_result_found();
		}
		wp_reset_query();

		if (wp_doing_ajax() && isset($_REQUEST['action']) && $_REQUEST['action'] === 'cubewp_posts_output') {
			wp_send_json_success(array('content' => $post_markup, 'newAttributes' => $parameters, 'has_more_posts' => $has_more_posts));
		} else {
			return $container_open . $post_markup . $container_close . $load_btn;
		}
	}

	public static function init()
	{
		$CubeWPClass = __CLASS__;
		new $CubeWPClass;
	}

	public function cubewp_shortcode_posts_callback($parameters)
	{
		$title  = isset($parameters['title']) ? $parameters['title'] : '';
		$output = '<div class="cwp-widget-shortcode">';
		if (! empty($title)) {
			$output .= '<h2 class="cwp-widget-shortcode-heading">' . $title . '</h2>';
		}
		$output .= apply_filters('cubewp_shortcode_posts_output', '', $parameters);
		$output .= '</div>';

		return $output;
	}

	private static function cwp_no_result_found()
	{
		return '<div class="cwp-empty-search"><img class="cwp-empty-search-img" src="' . esc_url(CWP_PLUGIN_URI . 'cube/assets/frontend/images/no-result.png') . '" alt=""><h2>' . esc_html__('No Results Found', 'cubewp-framework') . '</h2><p>' . esc_html__('There are no results matching your search.', 'cubewp-framework') . '</p></div>';
	}
}
