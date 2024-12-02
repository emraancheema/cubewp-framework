<?php
defined('ABSPATH') || exit;

/**
 * CubeWP Posts Shortcode.
 *
 * @class CubeWp_Frontend_Posts_Shortcode
 */
class CubeWp_Shortcode_Posts {
	
	public function __construct() {
		add_shortcode('cubewp_shortcode_posts', array($this, 'cubewp_shortcode_posts_callback'));
		add_filter('cubewp_shortcode_posts_output', array($this, 'cubewp_posts'), 10, 2);
		new CubeWp_Ajax('', 'CubeWp_Shortcode_Posts', 'cubewp_posts_output');
		new CubeWp_Ajax('wp_ajax_nopriv_', 'CubeWp_Shortcode_Posts', 'cubewp_posts_output');
	}

	public static function cubewp_posts($output, array $parameters) {
		return self::cubewp_posts_output($parameters);
	}

	public static function cubewp_posts_output($parameters) {

		// AJAX CALL
		if (wp_doing_ajax() && isset($_REQUEST['action']) && $_REQUEST['action'] === 'cubewp_posts_output') {
			// Set $parameters to $_POST if it's an AJAX request for 'cubewp_posts_output'
			$parameters = $_POST;
		}


		$args = array(
			'post_type'      => $parameters['post_type'],
			'orderby'        => $parameters['orderby'],
			'order'          => $parameters['order'],
			'page_num'          => 1,
			'meta_query'     => isset($parameters['meta_query']) ? $parameters['meta_query'] : array(),
		);

		if(isset($parameters['number_of_posts'])){
			$args['posts_per_page'] = $parameters['number_of_posts'];
		}

		if(isset($parameters['posts_per_page'])){
			$args['posts_per_page'] = $parameters['posts_per_page'];
		}

		if(isset($parameters['page_num'])){	
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
		$conatiner_open = '<div class="cubewp-posts-shortcode cwp-row">';
		$conatiner_close = '</div>';
		
		if ($posts->have_posts()) {

			if($show_boosted_posts == 'yes'){
                if(class_exists('CubeWp_Booster_Load')){
                    while ($posts->have_posts()): $posts->the_post();
					$post_type = get_post_type( get_the_ID() );
					$style = isset($parameters['card_style'][$post_type]) ? $parameters['card_style'][$post_type]: '';
					if (function_exists('is_boosted')) {
						if (is_boosted(get_the_ID())) {
							$post_markup .= CubeWp_frontend_grid_HTML(get_the_ID(), '', $style);
						}
					}
                    endwhile;
                }
            }else{
                while ($posts->have_posts()): $posts->the_post();
					$post_type = get_post_type( get_the_ID() );
					$style = isset($parameters['card_style'][$post_type]) ? $parameters['card_style'][$post_type]: '';
                    $post_markup .= CubeWp_frontend_grid_HTML(get_the_ID(), '', $style);
				endwhile;
            }
			if(isset($parameters['load_more']) && $parameters['load_more'] == 'yes'){
				if(isset($parameters['page_num'])){
					$parameters['page_num'] = $parameters['page_num'] + 1;
				}else{
					$parameters['page_num'] = 2;
				}
				$has_more_posts = $args['page_num'] < $posts->max_num_pages;
				$dataAttributes = json_encode($parameters);
				CubeWp_Enqueue::enqueue_script( 'cwp-load-more' );
				

				$load_btn .= '<div class="cubewp-load-more-conatiner">
					<button class="cubewp-load-more-button" data-attributes="'.htmlspecialchars($dataAttributes, ENT_QUOTES, 'UTF-8').'">
						'.esc_html__('Load More', 'cubewp-framework').'
					</button>
				</div>';
			}
			
		}else{
            $post_markup = self::cwp_no_result_found();
        }
        wp_reset_query();

		if (wp_doing_ajax() && isset($_REQUEST['action']) && $_REQUEST['action'] === 'cubewp_posts_output') {
			wp_send_json_success(array('content' => $post_markup, 'newAttributes' => $parameters, 'has_more_posts' => $has_more_posts));
		} else {
			return $conatiner_open.$post_markup.$conatiner_close.$load_btn;
		}
	}

	public static function init() {
		$CubeWPClass = __CLASS__;
		new $CubeWPClass;
	}

	public function cubewp_shortcode_posts_callback($parameters) {
		$title  = isset( $parameters['title'] ) ? $parameters['title'] : '';
		$output = '<div class="cwp-widget-shortcode">';
		if ( ! empty($title)) {
			$output .= '<h2 class="cwp-widget-shortcode-heading">' . $title . '</h2>';
		}
		$output .= apply_filters('cubewp_shortcode_posts_output', '', $parameters);
		$output .= '</div>';

		return $output;
	}

	private static function cwp_no_result_found(){
        return '<div class="cwp-empty-search"><img class="cwp-empty-search-img" src="'.esc_url(CWP_PLUGIN_URI.'cube/assets/frontend/images/no-result.png').'" alt=""><h2>'.esc_html__('No Results Found','cubewp-framework').'</h2><p>'.esc_html__('There are no results matching your search.','cubewp-framework').'</p></div>';
    }
}