<?php
defined( 'ABSPATH' ) || exit;

/**
 * CubeWP Taxonomy Terms Shortcode.
 *
 * @class CubeWp_Frontend_Taxonomy_Shortcode
 */
class CubeWp_Shortcode_Taxonomy {
	public function __construct() {
		add_shortcode( 'cubewp_shortcode_taxonomy', array( $this, 'cubewp_shortcode_taxonomy_callback' ) );
		add_filter( 'cubewp_shortcode_taxonomy_output', array( $this, 'cubewp_taxonomy_output' ), 10, 2 );
	}

	public static function cubewp_taxonomy_output( $output, $parameters = array()) {
		if(empty($parameters) || count($parameters) == 0)
		return;

		wp_enqueue_style( 'cwp-taxonomy-shortcode' );
		$taxonomy        = isset($parameters['taxonomy']) ? $parameters['taxonomy'] : '';
		if(empty($taxonomy))
		return;

		$terms_per_page  = $parameters['terms_per_page'];
		$output_style    = $parameters['output_style'];
		$_child_terms    = $parameters['child_terms'];
		$_hide_empty     = $parameters['hide_empty'];
		$icon_media_name = $parameters['icon_media_name'];
		$column_per_row  = $parameters['column_per_row'];
		$terms_box_color = $parameters['terms_box_color'];
		$child_terms     = false;
		$hide_empty      = false;
		$col_class       = '';
		if ( $_child_terms == 'yes' ) {
			$child_terms = true;
		}
		if ( $_hide_empty == 'yes' ) {
			$hide_empty = true;
		}
		if ( $column_per_row == '0' ) {
			$col_class = 'cwp-col-12 cwp-col-md-auto';
		}
		if ( $column_per_row == '1' ) {
			$col_class = 'cwp-col-12';
		}
		if ( $column_per_row == '2' ) {
			$col_class = 'cwp-col-12 cwp-col-md-6';
		}
		if ( $column_per_row == '3' ) {
			$col_class = 'cwp-col-12 cwp-col-md-6 cwp-col-lg-4';
		}
		if ( $column_per_row == '4' ) {
			$col_class = 'cwp-col-12 cwp-col-md-6 cwp-col-lg-3';
		}
		if ( $column_per_row == '6' ) {
			$col_class = 'cwp-col-12 cwp-col-md-6 cwp-col-lg-2';
		}
		$args  = array(
			'taxonomy'   => $taxonomy,
			'hide_empty' => $hide_empty,
			'parent'     => 0,
			'number'     => $terms_per_page,
		);
		$terms = get_terms( $args );
		ob_start();
		if ( ! empty( $terms ) && is_array( $terms ) ) {
			$counter = 0;
			?>
            <div class="cwp-row">
				<?php foreach ( $terms as $term ) {
					$term_id   = $term->term_id;
					$term_name = $term->name;
					if ( $output_style == 'boxed_view' ) {
						$color_count = count( $terms_box_color );
						$icon_media  = get_term_meta( $term_id, $icon_media_name, true );
						$color       = $terms_box_color[ $counter ]['term_box_color'];
						$counter ++;
						if ( $counter >= $color_count ) {
							$counter = 0;
						}
						?>
                        <div class="<?php echo esc_attr( $col_class ); ?>">
                            <div class="cwp-taxonomy-term-box">
                                <div class="cwp-taxonomy-term-box-heading"
                                     style="background-color: <?php echo esc_html( $color ); ?>">
									<?php
									if ( ! is_array( $icon_media ) ) {
										if ( $icon_media != strip_tags( $icon_media ) ) {
										   echo cubewp_core_data( $icon_media );
										} else if ( is_numeric( $icon_media ) ) {
										   $icon_media = wp_get_attachment_url( $icon_media );
										   echo '<img src="' . esc_attr($icon_media) . '" alt="' . esc_attr($term_name) . '">
																		<div class="cwp-taxonomy-term-box-heading-overlay" style="background-color: ' . esc_attr($color) . ';"></div>';
										} else {
										   echo '<i class="' . esc_attr($icon_media) . '" aria-hidden="true"></i>';
										}
									 }
									?>
                                    <a href="<?php echo get_term_link( $term_id ) ?>"><?php echo esc_html( $term_name ); ?></a>
                                </div>
								<?php
								if ( $child_terms ) {
									$term_child_args = array(
										'taxonomy'   => $taxonomy,
										'hide_empty' => $hide_empty,
										'parent'     => $term_id,
									);
									$term_childs     = get_terms( $term_child_args );
									if ( ! empty( $term_childs ) && is_array( $term_childs ) ) {
										?>
                                        <ul class="cwp-taxonomy-term-child-terms">
											<?php
											$child_terms_count = count( $term_childs );
											$term_counter      = 1;
											foreach ( $term_childs as $term_child ) {
												$child_term_id   = $term_child->term_id;
												$child_term_name = $term_child->name;
												if ( $child_terms_count > 5 && 5 == $term_counter ) {
													?>
                                                    <li>
                                                        <a href="#"
                                                           class="cwp-taxonomy-term-child-terms-see-more"
                                                           data-more="<?php esc_html_e( "View More", "cubewp-framework" ); ?>"
                                                           data-less="<?php esc_html_e( "View Less", "cubewp-framework" ); ?>"><?php esc_html_e( "View More", "cubewp-framework" ); ?></a>
                                                    </li>
                                                    <ul class="cwp-taxonomy-term-child-terms-more">
													<?php
												}
												?>
                                                <li>
                                                    <a href="<?php echo get_term_link( $child_term_id ) ?>"><?php echo esc_html( $child_term_name ); ?></a>
                                                </li>
												<?php
												if ( $child_terms_count > 5 && $child_terms_count == $term_counter ) {
													?>
                                                    </ul>
													<?php
												}
												$term_counter ++;
											}
											?>
                                        </ul>
										<?php
									}
								}
								?>
                            </div>
                        </div>
						<?php
					} else if ( $output_style == 'list_view' ) {
						?>
                        <div class="<?php esc_attr_e( $col_class ); ?>">
                            <div class="cwp-taxonomy-term-list">
                                <a href="<?php echo get_term_link( $term_id ) ?>"><?php echo esc_html( $term_name ); ?></a>
								<?php
								if ( $child_terms ) {
									$term_child_args = array(
										'taxonomy'   => $taxonomy,
										'hide_empty' => $hide_empty,
										'parent'     => $term_id,
									);
									$term_childs     = get_terms( $term_child_args );
									if ( ! empty( $term_childs ) && is_array( $term_childs ) ) {
										?>
                                        <ul><?php
										foreach ( $term_childs as $term_child ) {
											$child_term_id   = $term_child->term_id;
											$child_term_name = $term_child->name;
											?>
                                            <li>
                                                <a href="<?php echo get_term_link( $child_term_id ) ?>"><?php echo esc_html( $child_term_name ); ?></a>
                                            </li>
											<?php
										}
										?>
                                        </ul><?php
									}
								}
								?>
                            </div>
                        </div>
						<?php
					}
				} ?>
            </div>
			<?php
		}

		return ob_get_clean();
	}

	public static function init() {
		$CubeWPClass = __CLASS__;
		new $CubeWPClass;
	}

	public function cubewp_shortcode_taxonomy_callback( $parameters ) {
		$title  = isset( $parameters['title'] ) ? $parameters['title'] : '';
		$output = '<div class="cwp-widget-shortcode">';
		if ( ! empty( $title ) ) {
			$output .= '<h2 class="cwp-widget-shortcode-heading">' . $title . '</h2>';
		}
		$output .= apply_filters( 'cubewp_shortcode_taxonomy_output', '', $parameters );
		$output .= '</div>';

		return $output;
	}
}