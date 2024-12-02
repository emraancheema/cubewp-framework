<?php
/**
 * CubeWp single page trait contains all type of field's HTML for single page.
 *
 * @version 1.0
 * @package cubewp/cube/classes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

trait CubeWp_Single_Page_Trait {
	
	/**
	 * Method field_taxonomy
	 *
	 * @param array $args field data
	 *
	 * @return string html
	 * @since  1.0.0
	 */
	public static function field_taxonomy($args = array()) {
		$args['field_size'] = isset($args['field_size']) ? $args['field_size'] : '';
		$args['not_formatted_value'] = $args['value'];
		$args['value'] = cwp_handle_data_format( $args );
		$output = null;
		if (isset($args['value']) && ! empty($args['value']) && is_array($args['value'])) {
			$output = '<div class="cwp-cpt-single-category-container cwp-cpt-single-field-container '.esc_attr($args['container_class']).' '.esc_attr($args['field_size']).'">';
			$output .= '<h4>' . $args['label'] . '</h4>';
			$output .= '<ul class="cwp-single-category-widget-inner ' . $args['class'] . '">';
				foreach ($args['value'] as $terms) {
					$output .= '<li>
	                    <a href="' . get_term_link($terms) . '">
	                        <p>' . $terms->name . '</p>
	                    </a>
	                </li>';
				}
				$output .= '</div>';
			$output .= '</ul>';
		}

		return apply_filters('cubewp/singlecpt/field/taxonomy', $output, $args);
	}
	
	/**
	 * Method field_text
	 *
	 * @param array $args field data
	 *
	 * @return string html
	 * @since  1.0.0
	 */
	public static function field_text($args = array()) {
		$args['field_size'] = isset($args['field_size']) ? $args['field_size'] : '';
		$args['container_class'] = isset($args['container_class']) ? $args['container_class'] : '';
		$output = null;
		if(!empty($args['value'])){
			$output = '<div class="cwp-cpt-single-text-container cwp-cpt-single-field-container ' . esc_attr($args['container_class']) . ' '.esc_attr($args['field_size']).'">
				<h4>' . $args['label'] . '</h4>
				<div class="cwp-cpt-single-text ' . $args['class'] . '"><p>' . esc_html($args['value']) . '</p></div>
			</div>';
		}

		return apply_filters('cubewp/singlecpt/field/text', $output, $args);
	}
	
	/**
	 * Method field_number
	 *
	* @param array $args field data
	 *
	 * @return string html
	 * @since  1.0.0
	 */
	public static function field_number($args = array()) {
		$args['field_size'] = isset($args['field_size']) ? $args['field_size'] : '';
		$args['container_class'] = isset($args['container_class']) ? $args['container_class'] : '';
		$output = null;
		if(!empty($args['value'])){
			$output = '<div class="cwp-cpt-single-number-container cwp-cpt-single-field-container ' . esc_attr($args['container_class']) . ' '.esc_attr($args['field_size']).'">
				<h4>' . $args['label'] . '</h4>
				<div class="cwp-cpt-single-number ' . $args['class'] . '">
					' . esc_html($args['value']) . '
				</div>
			</div>';
		}

		return apply_filters('cubewp/singlecpt/field/number', $output, $args);
	}
	
	/**
	 * Method field_email
	 *
	 * @param array $args field data
	 *
	 * @return string html
	 * @since  1.0.0
	 */
	public static function field_email($args = array()) {
		$args['field_size'] = isset($args['field_size']) ? $args['field_size'] : '';
		$args['container_class'] = isset($args['container_class']) ? $args['container_class'] : '';
		$output = null;
		if(!empty($args['value'])){
			$output = '<div class="cwp-cpt-single-email-container cwp-cpt-single-field-container ' . esc_attr($args['container_class']) . ' '.esc_attr($args['field_size']).'">
				<h4>' . $args['label'] . '</h4>
				<div class="cwp-cpt-single-email ' . $args['class'] . '">
					<a href="mailto:' . esc_html($args['value']) . '">' . esc_html($args['value']) . '</a>
				</div>
			</div>';
		}

		return apply_filters('cubewp/singlecpt/field/email', $output, $args);
	}
	
	/**
	 * Method field_url
	 *
	* @param array $args field data
	 *
	 * @return string html
	 * @since  1.0.0
	 */
	public static function field_url($args = array()) {
		$args['container_class'] = isset( $args['container_class'] ) ? $args['container_class'] : '';
		$rel_attr = '';
		$field_name = isset( $args['name'] ) && ! empty( $args['name'] ) ? $args['name'] : '';
		if ( ! empty( $field_name ) ) {
			$field = get_field_options( $field_name );
			$rel = isset( $field['rel_attr'] ) && ! empty( $field['rel_attr'] ) ? $field['rel_attr'] : '';
			if ( ! empty( $rel ) && $rel != 'do-follow' ) {
				$rel_attr = 'rel="' . esc_attr( $rel ) . '"';
			}
		}
		$args['field_size'] = isset($args['field_size']) ? $args['field_size'] : '';
		$output = null;
		if(!empty($args['value'])){
			$output = '<div class="cwp-cpt-single-url-container cwp-cpt-single-field-container ' . esc_attr( $args['container_class'] ) . ' '.esc_attr($args['field_size']).'">
				<h4>' . $args['label'] . '</h4>
				<div class="cwp-cpt-single-url ' . $args['class'] . '">
					<a ' . $rel_attr . ' href="' . esc_url($args['value']) . '">' . esc_url($args['value']) . '</a>
				</div>
			</div>';
		}

		return apply_filters('cubewp/singlecpt/field/url', $output, $args);
	}
	
	/**
	 * Method field_password
	 *
	 * @param array $args field data
	 *
	 * @return string html
	 * @since  1.0.0
	 */
	public static function field_password($args = array()) {
		$args['field_size'] = isset($args['field_size']) ? $args['field_size'] : '';
		$args['container_class'] = isset($args['container_class']) ? $args['container_class'] : '';
		$output = null;
		if(!empty($args['value'])){
			$output = '<div class="cwp-cpt-single-password-container cwp-cpt-single-field-container ' . esc_attr($args['container_class']) . ' '.esc_attr($args['field_size']).'">
				<h4>' . $args['label'] . '</h4>
				<div class="cwp-cpt-single-password ' . $args['class'] . '">
					<input type="password" value="' . esc_html($args['value']) . '" readonly disabled>
				</div>
			</div>';
		}

		return apply_filters('cubewp/singlecpt/field/password', $output, $args);
	}
	
	/**
	 * Method field_textarea
	 *
	 * @param array $args field data
	 *
	 * @return string html
	 * @since  1.0.0
	 */
	public static function field_textarea($args = array()) {
		$args['field_size'] = isset($args['field_size']) ? $args['field_size'] : '';
		$args['container_class'] = isset($args['container_class']) ? $args['container_class'] : '';
		$output = null;
		if(!empty($args['value'])){
			$output = '<div class="cwp-cpt-single-textarea-container cwp-cpt-single-field-container ' . esc_attr($args['container_class']) . ' '.esc_attr($args['field_size']).'">
				<h4>' . $args['label'] . '</h4>
				<div class="cwp-cpt-single-textarea ' . $args['class'] . '">
					<p>' . wp_kses_post($args['value']) . '</p>
				</div>
			</div>';
		}

		return apply_filters('cubewp/singlecpt/field/textarea', $output, $args);
	}
	
	/**
	 * Method field_wysiwyg_editor
	 *
	 * @param array $args field data
	 *
	 * @return string html
	 * @since  1.0.0
	 */
	public static function field_wysiwyg_editor($args = array()) {
		$args['field_size'] = isset($args['field_size']) ? $args['field_size'] : '';
		$args['container_class'] = isset($args['container_class']) ? $args['container_class'] : '';
		$output = null;
		if(!empty($args['value'])){
			$output = '<div class="cwp-cpt-single-wysiwyg_editor-container cwp-cpt-single-field-container ' . esc_attr($args['container_class']) . ' '.esc_attr($args['field_size']).'">
				<h4>' . $args['label'] . '</h4>
				<div class="cwp-cpt-single-wysiwyg_editor ' . $args['class'] . '">
					' . wp_kses_post($args['value']) . '
				</div>
			</div>';
		}

		return apply_filters('cubewp/singlecpt/field/wysiwyg_editor', $output, $args);
	}
	
	/**
	 * Method field_oembed
	 *
	 * @param array $args field data
	 *
	 * @return string html
	 * @since  1.0.0
	 */
	public static function field_oembed($args = array()) {
		$args['field_size'] = isset($args['field_size']) ? $args['field_size'] : '';
		$args['container_class'] = isset($args['container_class']) ? $args['container_class'] : '';
		$output = null;
        if(!empty(wp_oembed_get($args['value']))){
			$output = '<div class="cwp-cpt-single-oembed-container cwp-cpt-single-field-container ' . esc_attr($args['container_class']) . ' '.esc_attr($args['field_size']).'">
	            <h4>' . $args['label'] . '</h4>
	            <div class="cwp-cpt-single-oembed ' . $args['class'] . '">
		            ' . wp_oembed_get($args['value']) . '
	            </div>
	        </div>';
        }

		return apply_filters('cubewp/singlecpt/field/oembed', $output, $args);
	}
	
	/**
	 * Method field_gallery
	 *
	 * @param array $args field data
	 *
	 * @return string html
	 * @since  1.0.0
	 */
	public static function field_gallery($args = array()) {
		wp_enqueue_script( 'cubewp-pretty-photo' );
		wp_enqueue_style( 'cubewp-pretty-photo' );

		$args['field_size'] = isset($args['field_size']) ? $args['field_size'] : '';
		$args['container_class'] = isset($args['container_class']) ? $args['container_class'] : '';
		$output = null;
		$args['not_formatted_value'] = $args['value'];
		$args['value'] = cwp_handle_data_format( $args );
		if (is_array($args['value']) && ! empty( $args['value'] )) {
			$gallery_id = $args['id'] ?? wp_rand();
			$output .= '<div class="cwp-cpt-single-gallery-container cwp-cpt-single-field-container ' . esc_attr($args['container_class']) . ' '.esc_attr($args['field_size']).'">
                <h4>' . $args['label'] . '</h4>
                <div class="cwp-cpt-single-gallery ' . $args['class'] . '">';
				foreach ($args['value'] as $galleryItemID) {
					$galleryItemID = cwp_get_attachment_id( $galleryItemID );
					$galleryItemURL     = wp_get_attachment_url($galleryItemID);
					$galleryItemCaption = wp_get_attachment_caption($galleryItemID);
					if (empty($galleryItemCaption)) {
						$galleryItemCaption = esc_html__('Gallery Image', 'cubewp-framework');
					}
					$output .= '<a href="' . esc_url($galleryItemURL) . '" rel="prettyPhoto[' . $gallery_id . ']" title="" class="cwp-cpt-single-gallery-item">';
					$output .= '<img src="' . esc_url($galleryItemURL) . '" alt="' . esc_attr($galleryItemCaption) . '">';
					$output .= '</a>';
				}
				$output .= '</div>
            </div>';
		}

		return apply_filters('cubewp/singlecpt/field/gallery', $output, $args);
	}
	
	/**
	 * Method field_file
	 *
	 * @param array $args field data
	 *
	 * @return string html
	 * @since  1.0.0
	 */
	public static function field_file($args = array()) {
        $args['field_size'] = isset($args['field_size']) ? $args['field_size'] : '';
		$args['container_class'] = isset($args['container_class']) ? $args['container_class'] : '';
		$output = null;
		$args['value'] = cwp_get_attachment_id( $args['value'] );
		$fileItemURL = wp_get_attachment_url($args['value']);
        if(!empty($fileItemURL)){
		$output      = '<div class="cwp-cpt-single-file-container cwp-cpt-single-field-container ' . esc_attr($args['container_class']) . ' '.esc_attr($args['field_size']).'">
            <h4>' . $args['label'] . '</h4>
            <div class="cwp-cpt-single-file ' . $args['class'] . '">
                <a href="' . esc_url($fileItemURL) . '" download>' . esc_html__('Download File', 'cubewp-framework') . '</a>
            </div>
        </div>';
        }else{
            $output='';
        }

		return apply_filters('cubewp/singlecpt/field/file', $output, $args);
	}
	
	/**
	 * Method field_switch
	 *
	 * @param array $args field data
	 *
	 * @return string html
	 * @since  1.0.0
	 */
	public static function field_switch($args = array()) {
		$args['field_size'] = isset($args['field_size']) ? $args['field_size'] : '';
		$args['container_class'] = isset($args['container_class']) ? $args['container_class'] : '';
		$output = null;
		if(!empty($args['value'])){
			$args['value'] = esc_html__( $args['value'], 'cubewp-framework' );
			$output = '<div class="cwp-cpt-single-switch-container cwp-cpt-single-field-container ' . esc_attr($args['container_class']) . ' '.esc_attr($args['field_size']).'">
				<h4>' . $args['label'] . '</h4>
				<div class="cwp-cpt-single-switch ' . $args['class'] . '"><p>' . esc_html($args['value']) . '</p></div>
			</div>';
		}

		return apply_filters('cubewp/singlecpt/field/switch', $output, $args);
	}
	
	/**
	 * Method field_dropdown
	 *
	 * @param array $args field data
	 *
	 * @return string html
	 * @since  1.0.0
	 */
	public static function field_dropdown($args = array()) {
		$args['field_size'] = isset($args['field_size']) ? $args['field_size'] : '';
		$args['container_class'] = isset($args['container_class']) ? $args['container_class'] : '';
		$output = null;
		$args['not_formatted_value'] = $args['value'];
		$args['value'] = cwp_handle_data_format( $args );
		if(!empty($args['value'])){
			$output = '<div class="cwp-cpt-single-switch-dropdown cwp-cpt-single-field-container ' . esc_attr($args['container_class']) . ' '.esc_attr($args['field_size']).'">
				<h4>' . $args['label'] . '</h4>
				<ul class="' . $args['class'] . '">';
					if (is_array($args['value'])) {
						foreach ($args['value'] as $dropdownValue):
							$output .= '<li>' . esc_html($dropdownValue) . '</li>';
						endforeach;
					} else {
						$output .= '<li>' . esc_html($args['value']) . '</li>';
					}
				$output .= '</ul>
			</div>';
		}

		return apply_filters('cubewp/singlecpt/field/dropdown', $output, $args);
	}
	
	/**
	 * Method field_checkbox
	 *
	 * @param array $args field data
	 *
	 * @return string html
	 * @since  1.0.0
	 */
	public static function field_checkbox($args = array()) {
		
		$args['field_size'] = isset($args['field_size']) ? $args['field_size'] : '';
		$args['container_class'] = isset($args['container_class']) ? $args['container_class'] : '';
		$output = null;
		$args['not_formatted_value'] = $args['value'];
		$args['value'] = cwp_handle_data_format( $args );
        if (!empty($args['value'])){
		$output = '<div class="cwp-cpt-single-switch-checkbox cwp-cpt-single-field-container ' . esc_attr($args['container_class']) . ' '.esc_attr($args['field_size']).'">
            <h4>' . $args['label'] . '</h4>';
			$output .= '<ul class="' . $args['class'] . '">';
			if (is_array($args['value'])) {
				foreach ($args['value'] as $checkbox):
					$output .= '<li>' . esc_html($checkbox) . '</li>';
				endforeach;
			} else {
				$output .= '<li>' . esc_html($args['value']) . '</li>';
			}
			$output .= '</ul>';
		$output .= '</div>';
        }else{
            return '';
        }

		return apply_filters('cubewp/singlecpt/field/checkbox', $output, $args);
	}
	
	/**
	 * Method field_radio
	 *
	 * @param array $args field data
	 *
	 * @return string html
	 * @since  1.0.0
	 */
	public static function field_radio($args = array()) {
		$args['field_size'] = isset($args['field_size']) ? $args['field_size'] : '';
		$args['container_class'] = isset($args['container_class']) ? $args['container_class'] : '';
		$output = null;
		if(!empty($args['value'])){
			$output = '<div class="cwp-cpt-single-switch-radio cwp-cpt-single-field-container ' . esc_attr($args['container_class']) . ' '.esc_attr($args['field_size']).'">
				<h4>' . $args['label'] . '</h4>
				<ul class="' . $args['class'] . '">
					<li>' . esc_html($args['value']) . '</li>
				</ul>
			</div>';
		}

		return apply_filters('cubewp/singlecpt/field/radio', $output, $args);
	}

		/**
	 * Method field_business_hours
	 *
	 * @param array $args field data
	 *
	 * @return string html
	 * @since  1.0.0
	 */
	public static function field_business_hours($args = array()) {
		$args['field_size'] = isset($args['field_size']) ? $args['field_size'] : '';
		$args['container_class'] = isset($args['container_class']) ? $args['container_class'] : '';
        $output = null;
        if (!empty($args['value']) && is_array($args['value'])) {
			$status = cwp_business_hours_status($args['value']);
            $output = '<div class="cwp-cpt-single-business_hours cwp-cpt-single-field-container' . esc_attr($args['container_class']) . ' ' . esc_attr($args['field_size']) . '">
				<h4>' . $args['label'] . '</h4>
				<p class="cwp-business-status '.$status.'">'.$status.'</p>
				<div class="cwp-business-hours ' . $args['class'] . '">';
			$time_format = get_option('time_format');
			foreach ($args['value'] as $day => $times) {
				$output .= '<div class="cwp-cpt-single-field-container">';
				$output .= '<h6>' . ucfirst($day) . '</h6>';
				if(!is_array($times) && is_string($times) && $times == '24-hours-open'){
					$output .= '<p> '. __('24 Hours Open','cubewp-framework') .'</p>';
				}else{
					$openTimes = $times['open'];
					$closeTimes = $times['close'];
					$output .= '<div class="cwp-single-day-timings">';
					for ($i = 0; $i < count($openTimes); $i++) {
						$otime = date_i18n($time_format, strtotime($openTimes[$i]));
						$ctime = date_i18n($time_format, strtotime($closeTimes[$i]));
						$output .= '<p>' . $otime . ' ~ ' . $ctime . '</p>';
					}
					$output .= '</div>';
				}
				
				
				$output .= '</div>';
			}
            $output .= '</div>
			</div>';
        }
		return apply_filters('cubewp/singlecpt/field/business_hour', $output, $args);
	}
	
	/**
	 * Method field_google_address
	 *
	 * @param array $args field data
	 *
	 * @return string html
	 * @since  1.0.0
	 */
	public static function field_google_address($args = array()) {
		$args['field_size'] = isset($args['field_size']) ? $args['field_size'] : '';
		$args['container_class'] = isset($args['container_class']) ? $args['container_class'] : '';
		$output = null;
		if (is_array($args['value']) && (isset($args['value']['address']) && isset($args['value']['lat']) && isset($args['value']['lng'])) && !empty($args['value']['lat']) && !empty($args['value']['lng']) ) {
			CubeWp_Enqueue::enqueue_style('cwp-map-cluster');
			CubeWp_Enqueue::enqueue_style('cwp-leaflet-css');

			CubeWp_Enqueue::enqueue_script('cubewp-leaflet');
			CubeWp_Enqueue::enqueue_script('cubewp-leaflet-cluster');
			CubeWp_Enqueue::enqueue_script('cubewp-map');

			$address = $args['value']['address'];
			$lat     = $args['value']['lat'];
			$lng     = $args['value']['lng'];
			$pin     = is_single() ? apply_filters( 'cubewp/search_result/map/pin', '', get_the_ID() ) : '';
			$output  .= '<div class="cwp-cpt-single-google_address cwp-cpt-single-field-container '.esc_attr($args['container_class']).' '.esc_attr($args['field_size']).'">
                <h4>' . $args['label'] . '</h4>
                <div class="cwp-single-loc ' . $args['class'] . '">
                    <div class="cpt-single-map" data-latitude="' . $lat . '" data-longitude="' . $lng . '" data-pinicon="' . $pin . '" style="height: 300px;width: 100%;"></div>
                    <div class="cwp-map-address">
                        <p>
                            <span id="cpt-single" class="address">' . $address . '</span>
                        </p>
                        <a href="https://www.google.com/maps?daddr=' . esc_attr($lat) . ',' . esc_attr($lng) . '" target="_blank" >
                            ' . esc_html__("Get Directions", "cubewp-framework") . '
                        </a>
                    </div>
                </div>
            </div>';
		}

		return apply_filters('cubewp/singlecpt/field/google_address', $output, $args);
	}
	
	/**
	 * Method field_date_picker
	 *
	* @param array $args field data
	 *
	 * @return string html
	 * @since  1.0.0
	 */
	public static function field_date_picker($args = array()) {
		$args['field_size'] = isset($args['field_size']) ? $args['field_size'] : '';
		$args['container_class'] = isset($args['container_class']) ? $args['container_class'] : '';
		$output = null;
		if(!empty($args['value'])){
			$output = '<div class="cwp-cpt-single-date_picker cwp-cpt-single-field-container '.esc_attr($args['container_class']).' '.esc_attr($args['field_size']).'">
				<h4>' . $args['label'] . '</h4>
				<div class="cwp-cpt-single-date_picker ' . $args['class'] . '">
					<p>' . date_i18n(get_option('date_format'), $args['value']) . '</p>
				</div>
			</div>';
		}

		return apply_filters('cubewp/singlecpt/field/date_picker', $output, $args);
	}
	
	/**
	 * Method field_date_time_picker
	 *
	* @param array $args field data
	 *
	 * @return string html
	 * @since  1.0.0
	 */
	public static function field_date_time_picker($args = array()) {
		$args['field_size'] = isset($args['field_size']) ? $args['field_size'] : '';
		$args['container_class'] = isset($args['container_class']) ? $args['container_class'] : '';
		$output = null;
		if(!empty($args['value'])){
			$output = '<div class="cwp-cpt-single-date_time_picker cwp-cpt-single-field-container '.esc_attr($args['container_class']).' '.esc_attr($args['field_size']).'">
				<h4>' . $args['label'] . '</h4>
				<div class="cwp-cpt-single-date_time_picker ' . $args['class'] . '">
					<p>' . date_i18n(get_option('date_format') . ' ' . get_option('time_format'), $args['value']) . '</p>
				</div>
			</div>';
		}

		return apply_filters('cubewp/singlecpt/field/date_time_picker', $output, $args);
	}
	
	/**
	 * Method field_time_picker
	 *
	 * @param array $args field data
	 *
	 * @return string html
	 * @since  1.0.0
	 */
	public static function field_time_picker($args = array()) {
		$args['field_size'] = isset($args['field_size']) ? $args['field_size'] : '';
		$args['container_class'] = isset($args['container_class']) ? $args['container_class'] : '';
		$output = null;
		if(!empty($args['value'])){
			$output = '<div class="cwp-cpt-single-time_picker cwp-cpt-single-field-container '.esc_attr($args['container_class']).' '.esc_attr($args['field_size']).'">
				<h4>' . $args['label'] . '</h4>
				<div class="cwp-cpt-single-time_picker ' . $args['class'] . '">
					<p>' . date_i18n(get_option('time_format'), strtotime($args['value'])) . '</p>
				</div>
			</div>';
		}

		return apply_filters('cubewp/singlecpt/field/time_picker', $output, $args);
	}
	
	/**
	 * Method field_repeating_field
	 *
	 * @param array $args field data
	 *
	 * @return string html
	 * @since  1.0.0
	 */
	public static function field_repeating_field($args = array()) {
		$args['field_size'] = isset($args['field_size']) ? $args['field_size'] : '';
		$args['container_class'] = isset($args['container_class']) ? $args['container_class'] : '';
        $values = $args['value'];
		$output = '';
        if (is_array($values) && isset($values) && !empty($values)) {
			$output .= '<div class="cwp-cpt-single-repeating-field cwp-cpt-single-field-container '.esc_attr($args['container_class']).' '.esc_attr($args['field_size']).'">
            	<h4>' . $args['label'] . '</h4>';
				for ($i = 0; $i < count($values); $i ++) {
					$output .= '<div class="cwp-cpt-single-repeating-field-inner">';
					foreach ($values[$i] as $k => $value) {
						$field_type = $value['type'];
						$options = get_field_options( $k );
						$options['value'] = $value['value'];
						$options['label'] = isset( $value['label'] ) ? $value['label'] : $options['label'];
						$value = $options;
						$value['class'] = isset($value['class']) ? $value['class'] : '';
						$value['container_class'] = isset($value['container_class']) ? $value['container_class'] : '';
						$value = apply_filters( 'cubewp/custom/cube/field/options', $value );
						if (method_exists(__CLASS__, 'field_' . $field_type)) {
							$output .= call_user_func( array( __CLASS__, 'field_' . $field_type ), $value);
						} else {
							$output .= '<p style="color: #ff0000">' . sprintf(esc_html__("Invalid Field Type: %s", "cubewp-framework"), $field_type) . '</p>';
						}
					}
					$output .= '</div>';
				}
			
	        $output .= '</div>';
        }

		return apply_filters('cubewp/singlecpt/field/repeating_field', $output, $args);
	}
	
	/**
	 * Method field_terms
	 *
	 * @param array $args field data
	 *
	 * @return string html
	 * @since  1.0.0
	 */
	public static function field_terms($args = array()) {
		$args['field_size'] = isset($args['field_size']) ? $args['field_size'] : '';
		$args['container_class'] = isset($args['container_class']) ? $args['container_class'] : '';
		$output = null;
		$args['not_formatted_value'] = $args['value'];
		$args['value'] = cwp_handle_data_format( $args );
		if ((!is_array($args['value']) && isset($args['value']) && !empty($args['value'])) || (is_array($args['value']) && isset($args['value'][0]) && $args['value'][0] != '')) {
			$output = '<div class="cwp-cpt-single-category-container cwp-cpt-single-field-container '.esc_attr($args['container_class']).' '.esc_attr($args['field_size']).'">';
			$output .= '<h4>' . $args['label'] . '</h4>';
			$output .= '<ul class="cwp-single-category-widget-inner ' . $args['class'] . '">';
				if (is_array($args['value'])) {
					foreach ($args['value'] as $terms) {
						$terms = get_term($terms);
						if ( ! empty($terms) && !is_wp_error( $terms )) {
							$output .= '<li>
			                    <a href="' . get_term_link( $terms ) . '">
			                        <p>' . $terms->name . '</p>
			                    </a>
			                </li>';
						}
					}
				}else {
					$terms = get_term($args['value']);
					if ( ! empty($terms)) {
						$output .= '<li>
		                    <a href="' . get_term_link($terms) . '">
		                        <p>' . $terms->name . '</p>
		                    </a>
	                </li>';
					}
				}
				$output .= '</div>';
			$output .= '</ul>';
		}

		return apply_filters('cubewp/singlecpt/field/terms', $output, $args);
	}

	/**
	 * Method field_user
	 *
	 * @param array $args field data
	 *
	 * @return string html
	 * @since  1.0.0
	 */
	public static function field_user($args = array()) {
		$args['field_size'] = isset($args['field_size']) ? $args['field_size'] : '';
		$args['container_class'] = isset($args['container_class']) ? $args['container_class'] : '';
		$output = null;
		$args['not_formatted_value'] = $args['value'];
		$args['value'] = cwp_handle_data_format( $args );
		$value = '';
		if (isset($args['value']) && ! empty($args['value'])) {
			$value = $args['value'];
		}
		if (is_array($value)) {
			$value = array_filter( $value, 'ucfirst' );
		}

		if ( ! empty($value)) {
			$output .= '<div class="cwp-cpt-single-user-container cwp-cpt-single-field-container '.esc_attr($args['container_class']).' '.esc_attr($args['field_size']).'">
				<h4>' . $args['label'] . '</h4>';
                $output .= '<div class="cwp-row cwp-user-row ' . $args['class'] . '">';
				if (is_array($value)) {
					foreach ($value as $user_id) {
						$output .= '<div class="cwp-col-md-6 cwp-user-col">';
	                    $output .= get_user_details($user_id);
		                $output .= '</div>';
					}
				} else {
					$user_data = get_userdata($value);
					if (!empty($user_data) && is_object($user_data)) {
						$output .= '<div class="cwp-col-md-6">';
	                    $output .= get_user_details($value);
		                $output .= '</div>';
					}
				}
				$output .= '</div>
            </div>';
		}

		return apply_filters('cubewp/singlecpt/field/user', $output, $args);
	}
	
	/**
	 * Method field_post
	 *
	* @param array $args field data
	 *
	 * @return string html
	 * @since  1.0.0
	 */
	public static function field_post($args = array()) {
		$args['field_size'] = isset($args['field_size']) ? $args['field_size'] : '';
		$args['container_class'] = isset($args['container_class']) ? $args['container_class'] : '';
		$output = null;
		$args['not_formatted_value'] = $args['value'];
		$args['value'] = cwp_handle_data_format( $args );
		$value = '';
		if (isset($args['value']) && ! empty($args['value'])) {
			$value = $args['value'];
		}
		if (is_array($value)) {
			$value = array_filter( $value, 'ucfirst' );
		}
		if ( ! empty($value) && $value != 'N/A') {
			$output .= '<div class="cwp-cpt-single-post-container cwp-cpt-single-field-container '.esc_attr($args['container_class']).' '.esc_attr($args['field_size']).'">
				<h4>' . $args['label'] . '</h4>';
                $output .= '<div class="cwp-row cwp-post-row ' . $args['class'] . '">';
				if (is_array($value)) {
					foreach ($value as $post_id) {
						$output .= CubeWp_frontend_grid_HTML($post_id);
					}
				}else{
					$output .= CubeWp_frontend_grid_HTML($value);
				}
				$output .= '</div>
            </div>';
		}

		return apply_filters('cubewp/singlecpt/field/post', $output, $args);
	}
	
	/**
	 * Method field_image
	 *
	 * @param array $args field data
	 *
	 * @return string html
	 * @since  1.0.0
	 */
	public static function field_image($args = array()) {
		wp_enqueue_script( 'cubewp-pretty-photo' );
		wp_enqueue_style( 'cubewp-pretty-photo' );
		$args['value'] = cwp_get_attachment_id( $args['value'] );
		$args['field_size'] = isset($args['field_size']) ? $args['field_size'] : '';
		$args['container_class'] = isset($args['container_class']) ? $args['container_class'] : '';
		$output = null;
        $imageURL     = wp_get_attachment_url($args['value']);
		if (isset($args['value']) && !empty ($imageURL)) {
			$gallery_id = $args['id'] ?? wp_rand();
			$output .= '<div class="cwp-cpt-single-image-container cwp-cpt-single-field-container ' . esc_attr($args['container_class']) . ' '.esc_attr($args['field_size']).'">
                <h4>' . $args['label'] . '</h4>
                <div class="cwp-cpt-single-image ' . $args['class'] . '">';
				$imageCaption = wp_get_attachment_caption($args['value']);
				if (empty($imageCaption)) {
					$imageCaption = esc_html__('Image', 'cubewp-framework');
				}
				$output .= '<a href="' . esc_url($imageURL) . '" rel="prettyPhoto[' . $gallery_id . ']" title="" class="cwp-cpt-single-image-item">';
				$output .= '<img src="' . esc_url($imageURL) . '" alt="' . esc_attr($imageCaption) . '">';
				$output .= '</a>';
				$output .= '</div>
            </div>';
		}

		return apply_filters('cubewp/singlecpt/field/image', $output, $args);
	}

   /**
    * Method field_color
    *
    * @param array $args field data
    *
    * @return string html
    * @since  1.0.0
    */
	public static function field_color($args = array()) {
		$args['field_size'] = isset($args['field_size']) ? $args['field_size'] : '';
		$args['container_class'] = isset($args['container_class']) ? $args['container_class'] : '';
		$output = null;
		if(!empty($args['value'])){
			$output = '<div class="cwp-cpt-single-color-container cwp-cpt-single-field-container ' . esc_attr($args['container_class']) . ' '.esc_attr($args['field_size']).'">
				<h4>' . $args['label'] . '</h4>
				<div class="cwp-cpt-single-text ' . $args['class'] . '">
					<p style="color: ' . $args['value'] . ';">' . esc_html($args['value']) . '</p>
				</div>
			</div>';
		}

		return apply_filters('cubewp/singlecpt/field/color', $output, $args);
	}
 
	/**
	 * Method field_range
	 *
	 * @param array $args field data
	 *
	 * @return string html
	 * @since  1.0.0
	 */
	public static function field_range($args = array()) {
		$args['field_size'] = isset($args['field_size']) ? $args['field_size'] : '';
		$args['container_class'] = isset($args['container_class']) ? $args['container_class'] : '';
		$output = null;
		if(!empty($args['value'])){
			$output = '<div class="cwp-cpt-single-range-container cwp-cpt-single-field-container ' . esc_attr($args['container_class']) . ' '.esc_attr($args['field_size']).'">
				<h4>' . $args['label'] . '</h4>
				<div class="cwp-cpt-single-text ' . $args['class'] . '">
					<p>' . esc_html($args['value']) . '</p>
				</div>
		    </div>';
		}

		return apply_filters('cubewp/singlecpt/field/range', $output, $args);
	}
        
    /**
     * Method get_post_share_button
     *
     * @param int $post_id [explicite description]
     *
     * @return void
	 * @since  1.0.0
     */
	public static function get_post_share_button($post_id = '') {
        global $cwpOptions;
		$site_url   = get_site_url();
		$site_title = get_bloginfo();
		if ($post_id != 0) {
			$site_url   = get_post_permalink($post_id);
			$site_title = get_the_title($post_id);
		}
		$site_title     = str_replace(' ', '%20', $site_title);
		$post_thumbnail = get_the_post_thumbnail_url($post_id);;
		$twitterURL     = 'https://twitter.com/intent/tweet?text=' . esc_attr($site_title) . '&amp;url=' . esc_url($site_url) . '';
		$facebookURL    = 'https://www.facebook.com/sharer/sharer.php?u=' . esc_url($site_url);
		$pinterest      = 'https://pinterest.com/pin/create/button/?url=' . esc_url($site_url) . '&media=' . esc_attr($post_thumbnail) . '&description=' . esc_attr($site_title);
		$linkedin       = 'http://www.linkedin.com/shareArticle?mini=true&url=' . esc_url($site_url);
		$reddit         = 'https://www.reddit.com/login?dest=https%3A%2F%2Fwww.reddit.com%2Fsubmit%3Ftitle%3D' . esc_attr($site_title) . '%26url%3D' . esc_url($site_url);
	    $output         = '';
		$output         .= '<div class="cwp-single-share-btn cwp-single-widget">
            <span class="cwp-main">
                 <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-share-fill" viewBox="0 0 16 16">
                  <path d="M11 2.5a2.5 2.5 0 1 1 .603 1.628l-6.718 3.12a2.499 2.499 0 0 1 0 1.504l6.718 3.12a2.5 2.5 0 1 1-.488.876l-6.718-3.12a2.5 2.5 0 1 1 0-3.256l6.718-3.12A2.5 2.5 0 0 1 11 2.5z"/>
                </svg>
                <span class="cwp-share-text">' . esc_html__("Share", "cubewp-framework") . '</span>
            </span>
            <div class="cwp-share-modal" style="display:none;">
                <ul class="cwp-share-options">';
                    if($cwpOptions['twitter_share']=='1'){
	                    $output .= '<li style="background-color: #4099FF;">
                            '. self::get_twitter_svg(esc_url($twitterURL)) .'
                        </li>';
                    }
                    if($cwpOptions['facebook_share']=='1'){
	                    $output .= '<li style="background-color: #3b5998;">
                            '. self::get_facebook_svg(esc_url($facebookURL)) .'
                        </li>';
                    }
                    if($cwpOptions['pinterest_share']=='1'){
	                    $output .= '<li style="background-color: #C92228;">
                            '. self::get_pinterest_svg(esc_url($pinterest)) .'
                        </li>';
                    }
                    if($cwpOptions['linkedin_share']=='1'){
	                    $output .= '<li style="background-color: #0077B5;">
                            '. self::get_linkedIn_svg(esc_url($linkedin)) .'
                        </li>';
                    }
                    if($cwpOptions['reddit_share']=='1'){
	                    $output .= '<li style="background-color: #fe6239;">
                            '. self::get_reddit_svg(esc_url($reddit)) .'
                        </li>';
                    }
	            $output .= '</ul>
            </div>
        </div>';

	    echo apply_filters('cubewp/singlecpt/share_post', $output, $post_id);
    }
    
    /**
     * Method get_twitter_svg
     *
     * @param string $twitter_url [explicite description]
     *
     * @return string html
	 * @since  1.0.0
     */
    public static function get_twitter_svg($twitter_url = '') {
        return '<a href="'.$twitter_url.'" class="" target="_blank">
	        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-twitter" viewBox="0 0 16 16">
	          <path d="M5.026 15c6.038 0 9.341-5.003 9.341-9.334 0-.14 0-.282-.006-.422A6.685 6.685 0 0 0 16 3.542a6.658 6.658 0 0 1-1.889.518 3.301 3.301 0 0 0 1.447-1.817 6.533 6.533 0 0 1-2.087.793A3.286 3.286 0 0 0 7.875 6.03a9.325 9.325 0 0 1-6.767-3.429 3.289 3.289 0 0 0 1.018 4.382A3.323 3.323 0 0 1 .64 6.575v.045a3.288 3.288 0 0 0 2.632 3.218 3.203 3.203 0 0 1-.865.115 3.23 3.23 0 0 1-.614-.057 3.283 3.283 0 0 0 3.067 2.277A6.588 6.588 0 0 1 .78 13.58a6.32 6.32 0 0 1-.78-.045A9.344 9.344 0 0 0 5.026 15z"/>
	        </svg>
	    </a>';
    }
        
    /**
     * Method get_facebook_svg
     *
    * @param string $facebook_url [explicite description]
     *
     * @return string html
	 * @since  1.0.0
     */
    public static function get_facebook_svg($facebook_url = '') {
        return '<a href="'.$facebook_url.'" class="" target="_blank">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-facebook" viewBox="0 0 16 16">
              <path d="M16 8.049c0-4.446-3.582-8.05-8-8.05C3.58 0-.002 3.603-.002 8.05c0 4.017 2.926 7.347 6.75 7.951v-5.625h-2.03V8.05H6.75V6.275c0-2.017 1.195-3.131 3.022-3.131.876 0 1.791.157 1.791.157v1.98h-1.009c-.993 0-1.303.621-1.303 1.258v1.51h2.218l-.354 2.326H9.25V16c3.824-.604 6.75-3.934 6.75-7.951z"/>
            </svg>
        </a>';
    }

	/**
     * Method get_pinterest_svg
     *
     * @param string $pinterest_url [explicite description]
     *
     * @return string html
	 * @since  1.0.0
     */
    public static function get_pinterest_svg($pinterest_url = '') {
        return '<a href="'.$pinterest_url.'" class="" target="_blank">
	        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-pinterest" viewBox="0 0 16 16">
	          <path d="M8 0a8 8 0 0 0-2.915 15.452c-.07-.633-.134-1.606.027-2.297.146-.625.938-3.977.938-3.977s-.239-.479-.239-1.187c0-1.113.645-1.943 1.448-1.943.682 0 1.012.512 1.012 1.127 0 .686-.437 1.712-.663 2.663-.188.796.4 1.446 1.185 1.446 1.422 0 2.515-1.5 2.515-3.664 0-1.915-1.377-3.254-3.342-3.254-2.276 0-3.612 1.707-3.612 3.471 0 .688.265 1.425.595 1.826a.24.24 0 0 1 .056.23c-.061.252-.196.796-.222.907-.035.146-.116.177-.268.107-1-.465-1.624-1.926-1.624-3.1 0-2.523 1.834-4.84 5.286-4.84 2.775 0 4.932 1.977 4.932 4.62 0 2.757-1.739 4.976-4.151 4.976-.811 0-1.573-.421-1.834-.919l-.498 1.902c-.181.695-.669 1.566-.995 2.097A8 8 0 1 0 8 0z"/>
	        </svg>
	    </a>';
    }

	/**
     * Method get_linkedIn_svg
     *
     * @param string $linkedIn_url [explicite description]
     *
     * @return string html
	 * @since  1.0.0
     */
    public static function get_linkedIn_svg($linkedIn_url = '') {
        return '<a href="'.$linkedIn_url.'" class="" target="_blank">
	        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-linkedin" viewBox="0 0 16 16">
	          <path d="M0 1.146C0 .513.526 0 1.175 0h13.65C15.474 0 16 .513 16 1.146v13.708c0 .633-.526 1.146-1.175 1.146H1.175C.526 16 0 15.487 0 14.854V1.146zm4.943 12.248V6.169H2.542v7.225h2.401zm-1.2-8.212c.837 0 1.358-.554 1.358-1.248-.015-.709-.52-1.248-1.342-1.248-.822 0-1.359.54-1.359 1.248 0 .694.521 1.248 1.327 1.248h.016zm4.908 8.212V9.359c0-.216.016-.432.08-.586.173-.431.568-.878 1.232-.878.869 0 1.216.662 1.216 1.634v3.865h2.401V9.25c0-2.22-1.184-3.252-2.764-3.252-1.274 0-1.845.7-2.165 1.193v.025h-.016a5.54 5.54 0 0 1 .016-.025V6.169h-2.4c.03.678 0 7.225 0 7.225h2.4z"/>
	        </svg>
	    </a>';
    }

    /**
     * Method get_reddit_svg
     *
     * @param string $reddit_url [explicite description]
     *
     * @return string html
	 * @since  1.0.0
     */
    public static function get_reddit_svg($reddit_url = '') {
        return '<a href="'.$reddit_url.'" class="" target="_blank">
			<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-reddit" viewBox="0 0 16 16">
				<path d="M6.167 8a.831.831 0 0 0-.83.83c0 .459.372.84.83.831a.831.831 0 0 0 0-1.661zm1.843 3.647c.315 0 1.403-.038 1.976-.611a.232.232 0 0 0 0-.306.213.213 0 0 0-.306 0c-.353.363-1.126.487-1.67.487-.545 0-1.308-.124-1.671-.487a.213.213 0 0 0-.306 0 .213.213 0 0 0 0 .306c.564.563 1.652.61 1.977.61zm.992-2.807c0 .458.373.83.831.83.458 0 .83-.381.83-.83a.831.831 0 0 0-1.66 0z"/>
				<path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.828-1.165c-.315 0-.602.124-.812.325-.801-.573-1.9-.945-3.121-.993l.534-2.501 1.738.372a.83.83 0 1 0 .83-.869.83.83 0 0 0-.744.468l-1.938-.41a.203.203 0 0 0-.153.028.186.186 0 0 0-.086.134l-.592 2.788c-1.24.038-2.358.41-3.17.992-.21-.2-.496-.324-.81-.324a1.163 1.163 0 0 0-.478 2.224c-.02.115-.029.23-.029.353 0 1.795 2.091 3.256 4.669 3.256 2.577 0 4.668-1.451 4.668-3.256 0-.114-.01-.238-.029-.353.401-.181.688-.592.688-1.069 0-.65-.525-1.165-1.165-1.165z"/>
			</svg>
		</a>';
    }
}