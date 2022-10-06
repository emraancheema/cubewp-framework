<?php
/**
 * CubeWp Admin Functions
 *
 * @version 1.0
 * @package cubewp/cube/functions
 */
if ( ! defined('ABSPATH')) {
	exit;
}

/**
 * Method cwp_get_meta
 *
 * @param string $meta_key
 * @param int    $post_id
 *
 * @return string
 * @since  1.0.0
 */
if ( ! function_exists("cwp_get_meta")) {
	function cwp_get_meta($meta_key = '', $post_id = '') {
		if ($post_id == '' || $post_id == 0) {
			global $post;
			$post_id = isset($post->ID) ? $post->ID : '';
		}

		if ($post_id && $meta_key) {
			return get_post_meta($post_id, $meta_key, true);
		}

		return '';
	}
}

/**
 * Method cwp_get_image_alt
 *
 * @param int $attachment_id
 *
 * @return string
 * @since  1.0.0
 */
if ( ! function_exists("cwp_get_image_alt")) {
	function cwp_get_image_alt($attachment_id = 0) {
		return get_post_meta($attachment_id, '_wp_attachment_image_alt', true);
	}
}

/**
 * Method cwp_breadcrumb
 *
 * @return string html
 * @since  1.0.0
 */
if ( ! function_exists("cwp_breadcrumb")) {
	function cwp_breadcrumb() {
		$output = '';
		if ( ! is_home()) {
			$output .= '<div class="quick-breadcrum cal-margin-bottom-30">';
			$output .= '<ul class="clearfix">';

			$output .= '<li><a href="' . esc_url(get_bloginfo('url')) . '">' . esc_html__("Home", "cubewp-framework") . '</a></li>';
			if (is_single()) {
				$output .= '<li><span>' . esc_html(get_the_title()) . '</span></li>';
			}
			$output .= '</ul>';
			$output .= '</div>';
		}

		return $output;
	}
}

/**
 * Method cwp_post_types
 *
 * @return array
 * @since  1.0.0
 */
if ( ! function_exists("cwp_post_types")) {
	function cwp_post_types() {
		$args = array(
			'public' => true,
		);
		$output   = 'names'; // 'names' or 'objects' (default: 'names')
		$operator = 'and'; // 'and' or 'or' (default: 'and')

		return get_post_types($args, $output, $operator);
	}
}

/**
 * Method cwp_pages_list
 *
 * @return array
 * @since  1.0.0
 */
if ( ! function_exists("cwp_pages_list")) {
	function cwp_pages_list() {
		$pages = get_pages();
		$list = array();
		foreach ($pages as $page) {
			$list[$page->ID] = $page->post_title;
		}

		return $list;
	}
}

/**
 * Get taxonomies by Post Type
 *
 * @param string $type Post Type Name.
 *
 * @return array $taxonomies List of Taxonomies.
 */
if ( ! function_exists("cwp_tax_by_PostType")) {
	function cwp_tax_by_PostType($type = '', $output = '') {
		$args = array(
			'public'      => true,
			'object_type' => array($type)
		);
		if ($output == 'objects') {
			$taxonomies = get_taxonomies($args, 'objects');
		} else {
			$taxonomies = get_taxonomies($args);
		}

		return $taxonomies;
	}
}

/**
 * Get Taxonomies
 *
 * @return array $taxonomies List of Taxonomies.
 */
if ( ! function_exists("cwp_taxonomies")) {
	function cwp_taxonomies() {
		$args = array(
			'public' => true,
		);

		return get_taxonomies($args);
	}
}

/**
 * Get Taxonomies
 *
 * @return array $taxonomies List of Taxonomies.
 */
if ( ! function_exists("cwp_get_taxonomy")) {
	function cwp_get_taxonomy($taxonomy = '') {
		return get_taxonomy($taxonomy);
	}
}

/**
 * Get Terms
 *
 * @return array $terms List of Terms|string Empty.
 */
if ( ! function_exists("cwp_all_terms")) {
	function cwp_all_terms() {
		$terms      = array();
		$post_types = get_option('cwp_custom_types');
		foreach ($post_types as $key => $single) {
			$taxonomies = get_object_taxonomies($key);
			foreach ($taxonomies as $key2 => $single2) {
				$terms[$key] = get_terms(array(
					'taxonomy'   => $single2,
					'hide_empty' => false
				));
			}

		}

		return $terms;
	}
}

/**
 * Get Terms by Taxonomy
 *
 * @return array $terms List of Terms.
 */
if ( ! function_exists("cwp_all_terms_by")) {
	function cwp_all_terms_by($taxonomy = '') {
		return get_terms($taxonomy, array('hide_empty' => false));
	}
}

/**
 * cwp_term_by Terms by
 * @args $by (id or slug) $type (array of comma), $terms (array data or comma seprated data)
 * $single (true if single element, false if multiple )
 *
 * @return array $terms List of Terms.
 */
if ( ! function_exists("cwp_term_by")) {
	function cwp_term_by($by = '', $type = '', $terms = '', $single = false) {
		if ( ! empty($terms)) {
			if ( ! $single) {
				$termArr = $terms;
				if ($type == 'comma') {
					$termArr = explode(',', $terms);
				}
				$termArray = array();
				foreach ($termArr as $term) {
					if ($by == 'name') {
						foreach (cwp_taxonomies() as $taxonomy) {
							$all_terms_by = cwp_all_terms_by($taxonomy);
							foreach ($all_terms_by as $all_terms) {
								if ($term == $all_terms->name) {
									$termArray[] = $all_terms->term_id;
								}
							}
						}
					} else {
						$termObject = get_term($term);
					}
					if ($by == 'id') {
						$termArray[] = $termObject->slug;
					} else if ($by == 'slug') {
						$termArray[] = $termObject->term_id;
					}
				}
				if ($type == 'comma') {
					return implode(',', $termArray);
				}

				return $termArray;
			} else {
				$termArray = array();
				$termObject = get_term($terms);
				if ($by == 'id') {
					$termArray = $termObject->slug;
				} else if ($by == 'slug') {
					$termArray = $termObject->term_id;
				} else if ($by == 'name') {
					foreach (cwp_taxonomies() as $taxonomy) {
						$all_terms_by = cwp_all_terms_by($taxonomy);
						foreach ($all_terms_by as $all_terms) {
							if ($terms == $all_terms->name) {
								$termArray[] = $all_terms->term_id;
							}
						}
					}
				}

				return $termArray;
			}
		}

        return $terms;
	}
}

/**
 * Method cwp_plan_exist_status_by_posttype
 *
 * @param string $posttype
 *
 * @return bool
 * @since  1.0.0
 */
if ( ! function_exists("cwp_plan_exist_status_by_posttype")) {
	function cwp_plan_exist_status_by_posttype($posttype) {
		$found = false;
		$plans = cwp_get_posts('price_plan');
		foreach ($plans as $id => $plan) {
			$post_type = get_post_meta($id, 'plan_post_type', true);
			if ($post_type == $posttype) {
				$found = true;
				break;
			}
		}

		return $found;
	}
}

/**
 * Method cwp_has_shortcode_pages_array
 *
 * @param string $shortcode
 *
 * @return bool
 */
if ( ! function_exists("cwp_has_shortcode_pages_array")) {
	function cwp_has_shortcode_pages_array($shortcode = '') {
		$id        = array();
		$args      = array('post_type' => 'page');
		$the_query = new WP_Query($args);
		if ($the_query->have_posts()) {
			while ($the_query->have_posts()) {
				$the_query->the_post();
				if (strpos(get_the_content(), $shortcode) !== false) {
					$id[get_the_ID()] = get_the_title();
				}
			}
		}

		return $id;
	}
}

/**
 * Method cwp_google_api_key
 *
 * @return string
 * @since  1.0.0
 */
if ( ! function_exists("cwp_google_api_key")) {
	function cwp_google_api_key() {
		global $cwpOptions;
		if (isset($cwpOptions['google_map_api']) && ! empty($cwpOptions['google_map_api'])) {
			$mapAPI = $cwpOptions['google_map_api'];
		} else {
			$mapAPI = 'AIzaSyBpgJk-IxjvPgy602SRzl1x_6RldPY5xak';
		}

		return $mapAPI;
	}
}

/**
 * Method cwp_associated_taxonomies_terms_links
 *
 * @return string html
 * @since  1.0.0
 */
if ( ! function_exists("cwp_associated_taxonomies_terms_links")) {
	function cwp_associated_taxonomies_terms_links() {
		// Get post by post ID.
		if ( ! $post = get_post()) {
			return '';
		}
		// Get post type by post.
		$post_type = $post->post_type;
		// Get post type taxonomies.
		$taxonomies = get_object_taxonomies($post_type, 'objects');
		$out = array();
		foreach ($taxonomies as $taxonomy_slug => $taxonomy) {
			// Get the terms related to post.
			$terms = get_the_terms($post->ID, $taxonomy_slug);
			if ( ! empty($terms)) {
				$out[] = "<ul class='cwp-loop-terms'>";
				foreach ($terms as $term) {
					$out[] = sprintf('<li><a href="%1$s">%2$s</a></li>', esc_url(get_term_link($term->slug, $taxonomy_slug)), esc_html($term->name));
				}
				$out[] = "</ul>";
			}
		}

		return implode('', $out);
	}
}

/**
 * Method is_cubewp_post_saved
 *
 * @param int  $postid [explicite description]
 * @param bool $class  =true $class
 *
 * @return string
 * @since  1.0.0
 */
if ( ! function_exists("is_cubewp_post_saved")) {
	function is_cubewp_post_saved($postid, $class = true) {
		if (is_user_logged_in()) {
			$uid       = get_current_user_id();
			$savePosts = get_user_meta($uid, 'cwp_save_user_post', true);
			if ( ! is_array($savePosts)) {
				$savePosts = (array) $savePosts;
			}
		} else {
			$savePosts = (isset($_COOKIE['CWP_Saved'])) ? explode(',', (string) sanitize_text_field($_COOKIE['CWP_Saved'])) : array();
			$savePosts = array_map('absint', $savePosts); // Clean cookie input, it's user input!
		}
		if ($class) {
			if (in_array($postid, $savePosts)) {
				return 'cwp-saved-post';
			} else {
				return 'cwp-save-post';
			}
		}else {
			if (in_array($postid, $savePosts)) {
				return true;
			} else {
				return false;
			}
        }
	}
}

/**
 * Method get_post_save_button
 *
 * @since  1.0.0
 */
if ( ! function_exists("get_post_save_button")) {
	function get_post_save_button($post_id) {
		$isSaved = '';
		if (class_exists('CubeWp_Saved')) {
			$SavedClass = CubeWp_Saved::is_cubewp_post_saved($post_id, false, true);
		} else {
			$SavedClass = 'cwp-save-post';
		}
		echo '<div class="cwp-single-save-btns cwp-single-widget">
             <span class="cwp-main ' . esc_attr($SavedClass) . '" data-pid="' . esc_attr($post_id) . '">
                 <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                       <path d="m8 2.748-.717-.737C5.6.281 2.514.878 1.4 3.053c-.523 1.023-.641 2.5.314 4.385.92 1.815 2.834 3.989 6.286 6.357 3.452-2.368 5.365-4.542 6.286-6.357.955-1.886.838-3.362.314-4.385C13.486.878 10.4.28 8.717 2.01L8 2.748zM8 15C-7.333 4.868 3.279-3.04 7.824 1.143c.06.055.119.112.176.171a3.12 3.12 0 0 1 .176-.17C12.72-3.042 23.333 4.867 8 15z"/>
                 </svg>
                 <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                       <path fill-rule="evenodd" d="M8 1.314C12.438-3.248 23.534 4.735 8 15-7.534 4.736 3.562-3.248 8 1.314z"/>
                 </svg>
            </span>
        </div>';
	}
}

/**
 * Method CubeWp_frontend_grid_HTML
 *
 * @param int    $post_id
 * @param string $col_class
 *
 * @return string html
 * @since  1.0.0
 */
if ( ! function_exists("CubeWp_frontend_grid_HTML")) {
	function CubeWp_frontend_grid_HTML($post_id, $col_class = 'cwp-col-12 cwp-col-md-6') {
		$thumbnail_url = get_the_post_thumbnail_url($post_id);
		if (empty($thumbnail_url)) {
			$thumbnail_url = CWP_PLUGIN_URI . 'cube/assets/frontend/images/default-fet-image.png';
		}
		$post_content = strip_tags(get_the_content('', '', $post_id));
		if (str_word_count($post_content, 0) > 10) {
			$words        = str_word_count($post_content, 2);
			$pos          = array_keys($words);
			$post_content = substr($post_content, 0, $pos[10]) . '...';
		}
		ob_start();
		?>
        <div <?php post_class($col_class); ?>>
            <div class="cwp-post">
                <div class="cwp-post-thumbnail">
                    <a href="<?php echo get_permalink($post_id); ?>">
                        <img src="<?php echo esc_url($thumbnail_url); ?>"
                             alt="<?php echo get_the_post_thumbnail_caption($post_id); ?>">
                    </a>
                    <div class="cwp-archive-save">
						<?php get_post_save_button($post_id); ?>
                    </div>
                </div>
                <div class="cwp-post-content-container">
                    <div class="cwp-post-content">
                        <h4><a href="<?php echo get_permalink($post_id); ?>"><?php echo get_the_title($post_id); ?></a>
                        </h4>
                        <p><?php echo esc_html($post_content); ?></p>
                    </div>
					<?php
					$post_type  = get_post_type($post_id);
					$taxonomies = get_object_taxonomies($post_type, 'objects');
					$terms_ui   = '';
					if ( ! empty($taxonomies) && is_array($taxonomies) && count($taxonomies) > 0) {
						$counter = 1;
						foreach ($taxonomies as $taxonomy_slug => $taxonomy) {
							$terms = get_the_terms($post_id, $taxonomy_slug);
							if ( ! empty($terms)) {
								foreach ($terms as $term) {
									$terms_ui .= sprintf('<li><a href="%1$s">%2$s</a></li>', esc_url(get_term_link($term->slug, $taxonomy_slug)), esc_html($term->name));
									if ($counter > 4) {
										$terms_ui .= sprintf('<li><a href="%1$s">%2$s</a></li>', esc_url(get_the_permalink()), esc_html("View All", "cubewp-framework"));
										break;
									}
									$counter ++;
								}
							}
						}
					}
					if ( ! empty($terms_ui)) {
						?>
                        <ul class="cwp-post-terms"><?php
						echo cubewp_core_data($terms_ui);
						?></ul><?php
					}
					?>
                </div>
            </div>
        </div>
		<?php

		return ob_get_clean();
	}
}

/**
 * Method get_user_details
 *
 * @param int $user_id
 *
 * @return string html
 * @since  1.0.0
 */
if ( ! function_exists("get_user_details")) {
	function get_user_details($user_id) {
		$user_login = get_the_author_meta("user_login", $user_id);
		$user_email = get_the_author_meta("user_email", $user_id);
		$user_url   = get_the_author_meta("user_url", $user_id);
		ob_start();
		?>
        <div class="cwp-single-widget cwp-admin-widget">
            <div class="cwp-single-author-img">
                <img src="<?php echo get_avatar_url($user_id, ["size" => "52"]) ?>"
                     alt="<?php esc_html__("Post Author", "cubewp") ?>"/>
            </div>
            <div class="cwp-single-author-detail">
                <div class="cwp-single-author-name">
					<?php echo get_the_author_meta("display_name", $user_id) ?>
                </div>
                <ul>
                    <li class="cwp-author-username"><p class="cwp-author-uname"><?php echo esc_html($user_login) ?></p>
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                             class="bi bi-person-fill" viewBox="0 0 16 16">
                            <path d="M3 14s-1 0-1-1 1-4 6-4 6 3 6 4-1 1-1 1H3zm5-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6z"/>
                        </svg>
                    </li>
                    <li>
                        <a href="mailto:<?php echo $user_email ?>">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                                 viewBox="0 0 16 16">
                                <path d="M.05 3.555A2 2 0 0 1 2 2h12a2 2 0 0 1 1.95 1.555L8 8.414.05 3.555ZM0 4.697v7.104l5.803-3.558L0 4.697ZM6.761 8.83l-6.57 4.027A2 2 0 0 0 2 14h12a2 2 0 0 0 1.808-1.144l-6.57-4.027L8 9.586l-1.239-.757Zm3.436-.586L16 11.801V4.697l-5.803 3.546Z"/>
                            </svg>
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo $user_url ?>">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                                 viewBox="0 0 16 16">
                                <path d="M0 8a8 8 0 1 1 16 0A8 8 0 0 1 0 8zm7.5-6.923c-.67.204-1.335.82-1.887 1.855A7.97 7.97 0 0 0 5.145 4H7.5V1.077zM4.09 4a9.267 9.267 0 0 1 .64-1.539 6.7 6.7 0 0 1 .597-.933A7.025 7.025 0 0 0 2.255 4H4.09zm-.582 3.5c.03-.877.138-1.718.312-2.5H1.674a6.958 6.958 0 0 0-.656 2.5h2.49zM4.847 5a12.5 12.5 0 0 0-.338 2.5H7.5V5H4.847zM8.5 5v2.5h2.99a12.495 12.495 0 0 0-.337-2.5H8.5zM4.51 8.5a12.5 12.5 0 0 0 .337 2.5H7.5V8.5H4.51zm3.99 0V11h2.653c.187-.765.306-1.608.338-2.5H8.5zM5.145 12c.138.386.295.744.468 1.068.552 1.035 1.218 1.65 1.887 1.855V12H5.145zm.182 2.472a6.696 6.696 0 0 1-.597-.933A9.268 9.268 0 0 1 4.09 12H2.255a7.024 7.024 0 0 0 3.072 2.472zM3.82 11a13.652 13.652 0 0 1-.312-2.5h-2.49c.062.89.291 1.733.656 2.5H3.82zm6.853 3.472A7.024 7.024 0 0 0 13.745 12H11.91a9.27 9.27 0 0 1-.64 1.539 6.688 6.688 0 0 1-.597.933zM8.5 12v2.923c.67-.204 1.335-.82 1.887-1.855.173-.324.33-.682.468-1.068H8.5zm3.68-1h2.146c.365-.767.594-1.61.656-2.5h-2.49a13.65 13.65 0 0 1-.312 2.5zm2.802-3.5a6.959 6.959 0 0 0-.656-2.5H12.18c.174.782.282 1.623.312 2.5h2.49zM11.27 2.461c.247.464.462.98.64 1.539h1.835a7.024 7.024 0 0 0-3.072-2.472c.218.284.418.598.597.933zM10.855 4a7.966 7.966 0 0 0-.468-1.068C9.835 1.897 9.17 1.282 8.5 1.077V4h2.355z"/>
                            </svg>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
		<?php

		return ob_get_clean();
	}
}

/**
 * Get custom post types
 *
 * @return array $post_types List of Custom Post Types.
 */
if ( ! function_exists("CWP_all_post_types")) {
	function CWP_all_post_types($form = '') {
		$blog['post']     = 'Post';
		$defaultPost      = apply_filters('cubewp/builder/post_types', $blog, $form);
		$cwp_custom_types = CWP_types();
		if (isset($cwp_custom_types) && ! empty($cwp_custom_types)) {
			$types = array();
            foreach ($cwp_custom_types as $k => $v) {
				$types[$k] = $v['label'];
			}
			if ( ! empty($defaultPost) && is_array($defaultPost)) {
				$list = array_merge($defaultPost, $types);
			} else {
				$list = $types;
			}
		} else {
			$list = $defaultPost;
		}

		return $list;
	}
}

/**
 * Method CWP_types
 *
 * @return array
 * @since  1.0.0
 */
if ( ! function_exists("CWP_types")) {
	function CWP_types() {
		$types            = array();
		$cwp_custom_types = get_option('cwp_custom_types');
		if (isset($cwp_custom_types) && ! empty($cwp_custom_types)) {
			$types = $cwp_custom_types;
		}

		return $types;
	}
}

/**
 * Method current_cubewp_page
 *
 * @return string
 * @since  1.0.0
 */
if ( ! function_exists("current_cubewp_page")) {
	function current_cubewp_page() {
		$current_screen = get_current_screen();
		$screen_pieces  = $current_screen->id;
		if (0 === strpos($screen_pieces, 'toplevel_page_')) {
			$callback = str_replace('toplevel_page_', '', strtolower($screen_pieces));
			foreach (CubeWp_Submenu::default_pages() as $page) {
				if ($callback == $page['callback']) {
					return str_replace('-', '_', strtolower($callback));
				}
			}

			return null;
		} else {
			$pos      = strrpos($screen_pieces, "_");
			$callback = substr($screen_pieces, $pos + 1);
			foreach (CubeWp_Submenu::default_pages() as $page) {
				if ($callback == $page['callback']) {
					return str_replace('-', '_', strtolower($callback));
				}
			}

			return null;
		}
	}
}

/**
 * Get post type groups
 *
 * @param string $type Post Type Slug.
 *
 * @return array $allGroups List of Group ID's.
 */
if ( ! function_exists("cwp_get_groups_by_post_type")) {
	function cwp_get_groups_by_post_type($type = '') {
		$args = array(
			'numberposts' => - 1,
			'post_type'   => 'cwp_form_fields',
			'fields'      => 'ids',
			'meta_query'  => array(
				array(
					'key'     => '_cwp_group_types',
					'value'   => $type,
					'compare' => 'LIKE',
				)
			)
		);

		return get_posts($args);
	}
}

/**
 * Get group fields
 *
 * @param int $GroupID Group ID.
 *
 * @return array $fields_of_specific_group List of Fields.
 */
if ( ! function_exists("cwp_get_fields_by_group_id")) {
	function cwp_get_fields_by_group_id($GroupID = 0) {
		if ( ! $GroupID) {
			return;
		}
		$fields_of_specific_group = get_post_meta($GroupID, '_cwp_group_fields', true);

		return explode(",", $fields_of_specific_group);
	}
}

/**
 * Method cubewp_core_data
 *
 * @param array $data
 *
 * @return mixed
 * @since  1.0.0
 */
if ( ! function_exists("cubewp_core_data")) {
	function cubewp_core_data($data = '') {
		if (empty($data)) {
			return;
		}

		return $data;
	}
}

/**
 * Method CubeWp_Sanitize_Custom_Fields
 *
 * @param array  $input
 * @param string $fields_of
 *
 * @return array
 * @since  1.0.0
 */
if ( ! function_exists("CubeWp_Sanitize_Custom_Fields")) {
	function CubeWp_Sanitize_Custom_Fields($input, $fields_of) {
		$sanitize = new CubeWp_Sanitize();
		$return   = array();
		if ($fields_of == 'post_types') {
			$return = $sanitize->sanitize_post_type_custom_fields($input);
		} else if ($fields_of == 'user') {
			$return = $sanitize->sanitize_post_type_custom_fields($input);
		}

		return $return;
	}
}

/**
 * CubeWp_Sanitize_Fields_Array
 *
 * @param array  $input
 * @param string $fields_of
 *
 * @return array
 * @since  1.0.0
 */
if ( ! function_exists("CubeWp_Sanitize_Fields_Array")) {
	function CubeWp_Sanitize_Fields_Array($input, $fields_of) {
		$sanitize = new CubeWp_Sanitize();
		$return   = array();
		if ($fields_of == 'taxonomy') {
			$return = $sanitize->sanitize_taxonomy_meta($input);
		} else if ($fields_of == 'post_types') {
			$return = $sanitize->sanitize_post_type_meta($input, $fields_of);
		} else if ($fields_of == 'user') {
			$return = $sanitize->sanitize_post_type_meta($input, $fields_of);
		}

		return $return;
	}
}

/**
 * CubeWp_Sanitize_Dynamic_Array
 *
 * @param array $input
 *
 * @return array
 * @since  1.0.0
 */
if ( ! function_exists("CubeWp_Sanitize_Dynamic_Array")) {
	function CubeWp_Sanitize_Dynamic_Array($input) {
		$result = array();
		if (is_array($input)) {
			foreach ($input as $key => $in) {
				if (is_array($in)) {
					foreach ($in as $k => $i) {
						if (is_array($i)) {
							$result[$key][$k] = CubeWp_Sanitize_dynamic_array_loop($i);
						} else {
							$result[$key][$k] = wp_unslash(sanitize_text_field($i));
						}
					}
				} else {
					$result[$key] = wp_unslash(sanitize_text_field($in));
				}
			}
		} else {
			$result = wp_unslash(sanitize_text_field($input));
		}

		return $result;
	}
}

/**
 * CubeWp_Sanitize_dynamic_array_loop
 *
 * @param array $input
 *
 * @return array
 * @since  1.0.0
 */
if ( ! function_exists("CubeWp_Sanitize_dynamic_array_loop")) {
	function CubeWp_Sanitize_dynamic_array_loop($input) {
		return CubeWp_Sanitize_Dynamic_Array($input);
	}
}

/**
 * Method CubeWp_Sanitize_text_Array
 *
 * @param array $input
 *
 * @return array
 * @since  1.0.0
 */
if ( ! function_exists("CubeWp_Sanitize_text_Array")) {
	function CubeWp_Sanitize_text_Array($input) {
		$sanitize = new CubeWp_Sanitize();

		return $sanitize->sanitize_text_array($input);
	}
}

/**
 * Method CubeWp_Sanitize_Muli_Array
 *
 * @param array $input
 *
 * @return array
 * @since  1.0.0
 */
if ( ! function_exists("CubeWp_Sanitize_Muli_Array")) {
	function CubeWp_Sanitize_Muli_Array($input) {
		$sanitize = new CubeWp_Sanitize();

		return $sanitize->sanitize_multi_array($input);
	}
}

/**
 * Method cwp_get_opt_hook
 *
 * @param string $type
 *
 * @return string
 * @since  1.0.0
 */
if ( ! function_exists("cwp_get_opt_hook")) {
	function cwp_get_opt_hook($type = '') {
		$opt_name = '';
		switch ($type) {
			case 'post_types':
				$opt_name = CWP()->prefix() . '_custom_fields';
				break;
			case 'taxonomy':
				$opt_name = CWP()->prefix() . '_tax_custom_fields';
				break;
			case 'user':
				$opt_name = CWP()->prefix() . '_user_custom_fields';
				break;
		}

		return $opt_name;
	}
}

/**
 * Get field option
 *
 * @param int $fieldID Field ID.
 *
 * @return array $SingleFieldOptions List of Field Options.
 */
if ( ! function_exists("get_field_options")) {
	function get_field_options($fieldID = 0) {
		if ( ! $fieldID) {
			return;
		}
		$fieldOptions = CWP()->get_custom_fields('post_types');

		return isset($fieldOptions[$fieldID]) ? $fieldOptions[$fieldID] : array();
	}
}

/**
 * Method get_field_value
 *
 * @param string $field
 *
 * @return array/string
 * @since  1.0.0
 */
if ( ! function_exists("get_field_value")) {
	function get_field_value($field = '') {
		if (empty($field)) {
			return;
		}
		if ( ! is_array($field)) {
			$field = get_field_options($field);
		}
		$field_type = isset($field["type"]) ? $field["type"] : "";
		$meta_key   = isset($field["name"]) ? $field["name"] : "";
		if ($field_type == 'taxonomy') {
			$field_type = 'terms';
		}
		global $cubewp_frontend;
		$single = $cubewp_frontend->single();
		if ($field_type == 'repeating_field') {
			return $single->get_single_fields(array($meta_key));
		}
		$value = $single->get_single_meta_value($meta_key, $field_type);
		if ($field_type == 'date_picker') {
			$value = wp_date(get_option('date_format'), $value);
		}
		if ($field_type == 'time_picker') {
			$value = wp_date(get_option('time_format'), $value);
		}
		if ($field_type == 'date_time_picker') {
			$value = wp_date(get_option('date_format') . ' H:i:s', $value);
		}

		return $value;
	}
}

/**
 * Method get_fields_by_type
 *
 * @param array $allowed_types
 *
 * @return array
 * @since  1.0.0
 */
if ( ! function_exists("get_fields_by_type")) {
	function get_fields_by_type(array $allowed_types) {
		$_data     = array();
		$args      = array(
			'numberposts' => - 1,
			'fields'      => 'ids',
			'post_type'   => 'cwp_form_fields'
		);
		$allGroups = get_posts($args);
		if (isset($allGroups) && ! empty($allGroups)) {
			foreach ($allGroups as $group) {
				$postCustomFields = new CubeWp_Posttype_Custom_Fields;
				$group_fields     = $postCustomFields->get_fields_by_group($group);
				foreach ($group_fields as $group_field) {
					$options = get_field_options($group_field);
					if (in_array($options['type'], $allowed_types)) {
						$title               = $options['label'];
						$_data[$group_field] = $title;
					}
				}
			}
		}

		return $_data;
	}
}

/**
 * Get field option
 *
 * @param int $fieldID Field ID.
 *
 * @return array $SingleFieldOptions List of Field Options.
 */
if ( ! function_exists("get_user_field_options")) {
	function get_user_field_options($fieldID = 0) {
		if ( ! $fieldID) {
			return;
		}
		$fieldOptions = CWP()->get_custom_fields('user');

		return isset($fieldOptions[$fieldID]) ? $fieldOptions[$fieldID] : array();
	}
}

/**
 * Method cwp_boolean_value
 *
 * @param string $value
 *
 * @return bool
 * @since  1.0.0
 */
if ( ! function_exists("cwp_boolean_value")) {
	function cwp_boolean_value($value = '') {
		$value = (string) $value;
		if (empty($value) || '0' === $value || 'false' === $value) {
			return false;
		}

		return true;
	}
}

/**
 * cwp_pre
 *
 * @param array $data
 * @param bool  $die
 *
 * @since  1.0.0
 */
if ( ! function_exists("cwp_pre")) {
	function cwp_pre($data = array(), $die = false) {
		echo '<pre>';
		print_r($data);
		echo '</pre>';
		if ($die == true) {
			die();
		}
	}
}

/**
 * cwp_output_buffer
 *
 * @return void
 */
if ( ! function_exists("cwp_output_buffer")) {
	function cwp_output_buffer() {
		ob_start();
	}

	add_action('init', 'cwp_output_buffer');
}

/**
 * Method cwp_get_posts
 *
 * @param array  $post_types
 * @param string $first_option
 *
 * @return array
 * @since  1.0.0
 */
if ( ! function_exists("cwp_get_posts")) {
	function cwp_get_posts($post_types = array(), $first_option = '') {
		$args   = array(
			'post_type'      => array($post_types),
			'post_status'    => 'publish',
			'posts_per_page' => - 1,
			'author'         => get_current_user_id(),
			'fields'         => 'ids'
		);
		$posts  = get_posts($args);
		$output = array();
		if ($first_option) {
			$output[''] = $first_option;
		}
		if (isset($posts) && ! empty($posts)) {
			foreach ($posts as $post) {
				$output[$post] = esc_html(get_the_title($post));
			}
		}

		return $output;
	}
}

/**
 * Method cwp_get_categories_by_taxonomy
 *
 * @param array  $taxonomy
 * @param string $first_option
 *
 * @return array
 * @since  1.0.0
 */
if ( ! function_exists("cwp_get_categories_by_taxonomy")) {
	function cwp_get_categories_by_taxonomy($taxonomy = array(), $first_option = '') {
		$terms  = get_terms(array(
			'taxonomy'   => $taxonomy,
			'hide_empty' => false,
		));
		$output = array();
		if ($first_option) {
			$output[''] = $first_option;
		}
		if (isset($terms) && ! empty($terms)) {
			foreach ($terms as $term) {
				$output[$term->term_id] = esc_html($term->name);
			}
		}

		return $output;
	}
}

/**
 * Method cwp_get_users_by_role
 *
 * @param array  $role
 * @param string $first_option
 *
 * @return array
 * @since  1.0.0
 */
if ( ! function_exists("cwp_get_users_by_role")) {
	function cwp_get_users_by_role($role = array(), $first_option = '') {
		$args   = array(
			'role'    => $role,
			'orderby' => 'display_name',
			'order'   => 'ASC'
		);
		$users  = get_users($args);
		$output = array();
		if ($first_option) {
			$output[''] = $first_option;
		}
		if (isset($users) && ! empty($users)) {
			foreach ($users as $user) {
				$output[$user->ID] = esc_html($user->display_name);
			}
		}

		return $output;
	}
}

/**
 * Method cubewp_get_template_part
 *
 * @param string $slug
 * @param string $name
 *
 * @return string
 * @since  1.0.0
 */
if ( ! function_exists('cubewp_get_template_part')) {
	function cubewp_get_template_part($slug, $name = null) {
		$templates = array();
		if (isset($name)) {
			$templates[] = "{$slug}-{$name}.php";
		}
		$templates[] = "{$slug}.php";

		cubewp_get_template_path($templates, true, false);
	}
}

/**
 * Method cubewp_get_template_path
 *
 * @param array $template_names
 * @param bool  $load
 * @param bool  $require_once
 *
 * @return string
 * @since  1.0.0
 */
if ( ! function_exists('cubewp_get_template_path')) {
	function cubewp_get_template_path($template_names, $load = false, $require_once = true) {
		$located = '';
		foreach ((array) $template_names as $template_name) {
			if ( ! $template_name) {
				continue;
			}
			if (file_exists(CWP_PLUGIN_PATH . $template_name)) {
				$located = CWP_PLUGIN_PATH . $template_name;
				break;
			}
		}
		if ($load && '' != $located) {
			load_template($located, $require_once);
		}

		return $located;
	}
}

/**
 * Method cubewp_extra_features
 *
 *
 * @return class
 * @since  1.0.0
 */
if ( ! function_exists('cubewp_extra_features')) {
	function cubewp_extra_features() {
		$add_ons = CubeWp_Add_On::cubewp_add_ons();
		foreach ($add_ons as $key => $add_on) {
			$id     = $add_on['id'];
			$load   = $add_on['load'];
			$cubewp = CWP()->cubewp_options($id);
			if (isset($cubewp->license) && $cubewp->license == 'valid') {
				if (class_exists($load)) {
					$load::instance();
				}
			}
		}
	}

	add_action('cubewp_loaded', 'cubewp_extra_features', 10);
}

/**
 * Method cwp_get_current_user_roles
 *
 * @return array
 * @since  1.0.0
 */
if ( ! function_exists("cwp_get_current_user_roles")) {
	function cwp_get_current_user_roles() {
		if (is_user_logged_in()) {
			$user  = wp_get_current_user();
			$roles = ( array ) $user->roles;

			return $roles[0];
		} else {
			return array();
		}
	}
}

/**
 * Method cwp_get_user_roles
 *
 * @return array
 * @since  1.0.0
 */
if ( ! function_exists("cwp_get_user_roles")) {
	function cwp_get_user_roles() {
		global $wp_roles;

		return $wp_roles->roles;
	}
}

/**
 * Method cwp_get_user_roles_name
 *
 * @return array
 * @since  1.0.0
 */
if ( ! function_exists("cwp_get_user_roles_name")) {
	function cwp_get_user_roles_name() {
		return wp_roles()->get_names();
	}
}

/**
 * Method cwp_get_groups_by_user_role
 *
 * @param string $user_role
 *
 * @return array
 * @since  1.0.0
 */
if ( ! function_exists("cwp_get_groups_by_user_role")) {
	function cwp_get_groups_by_user_role($user_role = '') {
		$args = array(
			'numberposts' => - 1,
			'post_type'   => 'cwp_user_fields',
			'fields'      => 'ids',
			'meta_query'  => array(
				array(
					'key'     => '_cwp_group_user_roles',
					'value'   => $user_role,
					'compare' => 'LIKE',
				)
			)
		);

		return get_posts($args);
	}
}

/**
 * Method Builder_field_size_to_text
 *
 * @param string $size
 *
 * @return string
 * @since  1.0.0
 */
if ( ! function_exists('Builder_field_size_to_text')) {
	function Builder_field_size_to_text($size = 'size-1-1') {
		switch ($size) {
			case'size-1-4' :
			{
				$size = '1 / 4';
				break;
			}
			case'size-1-3' :
			{
				$size = '1 / 3';
				break;
			}
			case'size-1-2' :
			{
				$size = '1 / 2';
				break;
			}
			case'size-2-3' :
			{
				$size = '2 / 3';
				break;
			}
			case'size-3-4' :
			{
				$size = '3 / 4';
				break;
			}
			case'size-1-1' :
			{
				$size = '1 / 1';
				break;
			}
		}

		return $size;
	}
}

/**
 * Method cubewp_user_default_fields
 *
 * @return array
 * @since  1.0.0
 */
if ( ! function_exists("cubewp_user_default_fields")) {
	function cubewp_user_default_fields() {
		$wp_default_fields = array(
			'user_login'   => array(
				'label'    => __("Username", "cubewp-framework"),
				'name'     => 'user_login',
				'type'     => 'text',
				'required' => 1,
			),
			'user_email'   => array(
				'label'    => __("Email", "cubewp-framework"),
				'name'     => 'user_email',
				'type'     => 'email',
				'required' => 1,
			),
			'user_pass'    => array(
				'label'    => __("Password", "cubewp-framework"),
				'name'     => 'user_pass',
				'type'     => 'password',
				'required' => 1,
			),
			'confirm_pass' => array(
				'label'    => __("Confirm Password", "cubewp-framework"),
				'name'     => 'confirm_pass',
				'type'     => 'password',
				'required' => 1,
			),
			'user_url'     => array(
				'label' => __("Website", "cubewp-framework"),
				'name'  => 'user_url',
				'type'  => 'text',
			),
			'display_name' => array(
				'label' => __("Display Name", "cubewp-framework"),
				'name'  => 'display_name',
				'type'  => 'text',
			),
			'nickname'     => array(
				'label' => __("Nickname", "cubewp-framework"),
				'name'  => 'nickname',
				'type'  => 'text',
			),
			'first_name'   => array(
				'label' => __("First Name", "cubewp-framework"),
				'name'  => 'first_name',
				'type'  => 'text',
			),
			'last_name'    => array(
				'label' => __("Last Name", "cubewp-framework"),
				'name'  => 'last_name',
				'type'  => 'text',
			),
			'description'  => array(
				'label' => __("Biographical Info", "cubewp-framework"),
				'name'  => 'description',
				'type'  => 'textarea',
			),
		);

		return $wp_default_fields;
	}
}

/**
 * Method cubewp_user_login_fields
 *
 * @return array
 * @since  1.0.0
 */
if ( ! function_exists("cubewp_user_login_fields")) {
	function cubewp_user_login_fields() {
		$wp_default_fields = array(
			'username' => array(
				'label'    => __("Username/Email", "cubewp-framework"),
				'name'     => 'user_login',
				'type'     => 'text',
				'required' => 1,
			),
			'password' => array(
				'label'    => __("Password", "cubewp-framework"),
				'name'     => 'user_pass',
				'type'     => 'password',
				'required' => 1,
			),
		);

		return $wp_default_fields;
	}
}

/**
 * Method _get_post_type
 *
 * @param string $type
 *
 * @return string
 * @since  1.0.0
 */
if ( ! function_exists("_get_post_type")) {
	function _get_post_type($type = '') {
		if (empty($type)) {
			if (isset($_GET['post_type']) && $_GET['post_type'] != '') {
				$post_type = sanitize_text_field($_GET['post_type']);
			} else if (isset($_GET['search_type']) && $_GET['search_type'] != '') {
				$post_type = sanitize_text_field($_GET['search_type']);
			} else if (is_tax()) {
				$post_type = get_taxonomy(get_queried_object()->taxonomy)->object_type[0];
			} else {
				$post_type = isset($_GET['post_type']) ? sanitize_text_field($_GET['post_type']) : get_queried_object()->name;
			}

			return $post_type;
		} else {
			return $type;
		}
	}
}

/**
 * Method _get_map_settings
 *
 * @return array
 * @since  1.0.0
 */
if ( ! function_exists("_get_map_settings")) {
	function _get_map_settings() {
		global $cwpOptions;
		$map = array();
		if ($cwpOptions) {
			if (isset($cwpOptions['map_option']) && ! empty($cwpOptions['map_option'])) {
				$map['map_option'] = $cwpOptions['map_option'];
			}
			if (isset($cwpOptions['map_zoom']) && ! empty($cwpOptions['map_zoom'])) {
				$map['map_zoom'] = $cwpOptions['map_zoom'];
			}
			if ($cwpOptions['map_option'] == 'mapbox' && (isset($cwpOptions['mapbox_token']) && ! empty($cwpOptions['mapbox_token']))) {
				$map['mapbox_token'] = $cwpOptions['mapbox_token'];
			}
			if ($cwpOptions['map_option'] == 'mapbox' && (isset($cwpOptions['map_style']) && ! empty($cwpOptions['map_style']))) {
				$map['map_style'] = $cwpOptions['map_style'];
			}
			if (isset($cwpOptions['map_latitude']) && ! empty($cwpOptions['map_latitude'])) {
				$map['map_latitude'] = $cwpOptions['map_latitude'];
			}
			if (isset($cwpOptions['map_longitude']) && ! empty($cwpOptions['map_longitude'])) {
				$map['map_longitude'] = $cwpOptions['map_longitude'];
			}
		}

		return $map;
	}
}

/**
 * Method cwp_custom_mime_types
 *
 * @param array $mimes
 *
 * @return array
 * @since  1.0.0
 */
if ( ! function_exists("cwp_custom_mime_types")) {
	function cwp_custom_mime_types($mimes) {
		$mimes['json'] = 'application/json';

		return $mimes;
	}

	add_filter('upload_mimes', 'cwp_custom_mime_types');
}