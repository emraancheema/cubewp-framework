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
	}

	public static function cubewp_posts($output, array $parameters) {
		ob_start();
		echo self::cubewp_posts_output($parameters);

		return ob_get_clean();
	}

	public static function cubewp_posts_output($parameters) {
		$args = array(
			'post_type'      => $parameters['posttype'],
			'posts_per_page' => $parameters['posts_per_page'],
//			'orderby'        => $parameters['orderby'],
			'order'          => $parameters['order'],
		);
        $show_boosted_posts = '';
        if (class_exists('CubeWp_Booster_Load')) {
            $show_boosted_posts = $parameters['show_boosted_posts'];
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
		$column_per_row = $parameters['column_per_row'];
		$col_class = 'cwp-col-12 cwp-col-md-6';
		if ($column_per_row == '0') {
			$col_class = 'cwp-col-12 cwp-col-md-auto';
		}
		if ($column_per_row == '1') {
			$col_class = 'cwp-col-12';
		}
		if ($column_per_row == '2') {
			$col_class = 'cwp-col-12 cwp-col-md-6';
		}
		if ($column_per_row == '3') {
			$col_class = 'cwp-col-12 cwp-col-md-6 cwp-col-lg-4';
		}
		if ($column_per_row == '4') {
			$col_class = 'cwp-col-12 cwp-col-md-6 cwp-col-lg-3';
		}
		if ($column_per_row == '6') {
			$col_class = 'cwp-col-12 cwp-col-md-6 cwp-col-lg-2';
		}
		$layout = $parameters['layout'];
		$row_class = 'grid-view';
		if ($layout == 'list') {
			$col_class = 'cwp-col-12';
            $row_class = 'list-view';
		}
		$query = new CubeWp_Query($args);
		$posts = $query->cubewp_post_query();
		ob_start();
		if ($posts->have_posts()) {
			?>
            <div class="cwp-row <?php esc_attr_e($row_class); ?>">
				<?php
				if($show_boosted_posts == 'yes'){
                if(class_exists('CubeWp_Booster_Load')){
                    while ($posts->have_posts()): $posts->the_post();
					if (function_exists('is_boosted')) {
						if (is_boosted(get_the_ID())) {
							echo CubeWp_frontend_grid_HTML(get_the_ID(), $col_class);
						}
					}
                    endwhile;
                }
            }else{
                while ($posts->have_posts()): $posts->the_post();
                    echo CubeWp_frontend_grid_HTML(get_the_ID(), $col_class);
				endwhile;
            }
				?>
            </div>
			<?php
		}

		return ob_get_clean();
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
}