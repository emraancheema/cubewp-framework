<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * CubeWp_Loop_Builder
 */
class CubeWp_Loop_Builder {
	
	private static $cubewp_loop_field_prefix = 'loop_';

	public static $cubewp_style_options = array();


	/**
	 * CubeWp_Loop_Builder Constructor.
	 */
	public function __construct() {
		add_action( 'cubewp_loop_builder', array( $this, 'cubewp_loop_builder_ui' ) );
		add_filter( 'cubewp/loop/builder/save', array( $this, 'cubewp_loop_builder_save' ),80,3 );
		self::cubewp_loop_builder_set_switcher_options();
		$post_types       = CWP_all_post_types( 'post_types' );
		if ( ! empty( $post_types ) ) {
			foreach ( $post_types as $post_type => $val ) {
				add_filter( "cubewp/loop/builder/{$post_type}/default_style/markup", [$this, 'cubewp_default_post_card'], 10 );
			}
		}
		new CubeWp_Ajax( '', __CLASS__, 'cubewp_process_post_card_preview' );
	}

	public static function init() {
		$CubeClass = __CLASS__;
		new $CubeClass;
	}

	private static function cubewp_loop_builder_voided_field_types() {
		$voided_fields = array(
		   'gallery',
		   'review_star',
		   'repeating_field',
		   'user',
		   'taxonomy',
		   'post',
		);
	
		return apply_filters( 'cubewp/loop/builder/voided/field/types', $voided_fields );
	}


	/**
	 * Method cubewp_loop_builder_ui
	 *
	 * @return void
	 */
	public function cubewp_loop_builder_ui() {
		self::cubewp_loop_builder_set_switcher_options();
		?>
		<div class="cubewp-content">
			<?php
			self::cubewp_loop_builder_title_bar();
			?>
			<section id="cwpform-builder" class="cwpform-builder cubewp-builder-loop_builder">
				<?php
				self::cubewp_loop_builder_sidebar();
				self::cubewp_loop_builder_content();
				?>
			</section>
		</div>
		<?php
	}

	private static function cubewp_loop_builder_set_switcher_options() {
		global $cwpOptions;
		$cwpOptions = ! empty( $cwpOptions ) ? $cwpOptions : get_option( 'cwpOptions' );

		$post_types       = CWP_all_post_types();

		// Styles coming from CUbeWp settings.
		$custom_styles = isset($cwpOptions['cwp_loop_style']) & !empty($cwpOptions['cwp_loop_style']) ? $cwpOptions['cwp_loop_style'] : [];

		$switcher_options = array();
		if ( ! empty( $post_types ) ) {
			foreach ( $post_types as $post_type => $label ) {
				$switcher_options[ $post_type ]['label']       	= $label;
				$loop_styles                                   	= apply_filters( "cubewp/loop/builder/{$post_type}/styles", array() );
				$loop_styles                                   	= ! empty( $loop_styles ) && is_array( $loop_styles ) ? $loop_styles : array();
				$_loop_styles 									= isset($custom_styles[$post_type]) && !empty($custom_styles[$post_type]) ? self::render_custom_styles($custom_styles[$post_type]) : self::render_custom_styles();
				$loop_styles                                   	= array_merge( $_loop_styles, $loop_styles );
				$switcher_options[ $post_type ]['loop-styles'] 	= $loop_styles;
			}
		}

		self::$cubewp_style_options = $switcher_options;
	}

	private static function render_custom_styles($styles = '') {
		$styles_array = [];
		if(!empty($styles)){
			$styles_array = explode(",", $styles);
		}
		
		//Default STyle for all post types
		$new_array = array(
			'default_style' => esc_html__( 'Basic Style', 'cubewp-frontend' )
		);

		if(is_array($styles_array) && !empty($styles_array)){
			foreach($styles_array as $style){
				$key = str_replace(' ', '_', $style);
				$new_array[$key] = $style;
			}
		}

		return $new_array;
	}

	private static function cubewp_loop_builder_title_bar() {
		?>
		<section id="cubewp-title-bar">
			<h1><?php esc_html_e( 'Post Card Customizer', 'cubewp-frontend' ); ?></h1>
			<div class="shoftcode-area">
				<div class="cwpform-shortcode"></div>
				<button class="button-primary cwpform-get-shortcode">
					<?php esc_html_e( 'Save Changes', 'cubewp-frontend' ); ?>
				</button>
			</div>
		</section>
		<?php
	}

	private static function cubewp_loop_builder_sidebar() {
		$post_types = self::$cubewp_style_options;
		?>
		<div class="cubewp-builder-sidebar">
			<?php
			self::cubewp_loop_builder_switcher_options();
			?>
			<div class="cubewp-builder-sidebar-groups-widgets">
				<?php
				if ( ! empty( $post_types ) && is_array( $post_types ) ) {
					foreach ( $post_types as $post_type => $data ) {
						$loop_styles = $data['loop-styles'] ?? array();
						?>
						<div class="sidebar-type-<?php echo esc_attr( $post_type ); ?> cubewp-tab-switcher-target cubewp-switcher-tab-<?php echo esc_attr( $post_type ); ?>">
							<?php
							if ( ! empty( $loop_styles ) && is_array( $loop_styles ) ) {
								foreach ( $loop_styles as $name => $label ) {
									$id = $post_type . '-' . $name;
									?>
									<div class="cubewp-tab-switcher-target cubewp-switcher-tab-<?php echo esc_attr( $id ); ?>"></div>
									<?php
								}
							}
							self::cubewp_loop_builder_default_fields( $post_type );
							self::cubewp_loop_builder_custom_cubes( $post_type );
							self::cubewp_loop_builder_custom_fields( $post_type );
							?>
						</div>
						<?php
					}
				}
				?>
			</div>
		</div>
		<?php
	}

	private static function cubewp_loop_builder_switcher_options() {
		$post_types = self::$cubewp_style_options;
		if ( ! empty( $post_types ) && is_array( $post_types ) ) {
			$unset_switch_types = apply_filters( 'cubewp/exclude/content/switcher/loop_builder', array() );
			$unset_switch_types = !empty($unset_switch_types) ? $unset_switch_types : array();
			$loop_style_switcher = '';
			?>
			<div class="cubewp-builder-sidebar-option">
				<label for="cubewp-builder-cpt"><?php esc_html_e( 'Select Post Type', 'cubewp-frontend' ); ?></label>
				<select name="cubewp-builder-cpt" id="cubewp-builder-cpt"
				        class="cubewp-tab-switcher cubewp-tab-switcher-trigger-on-load cubewp-tab-switcher-have-child">
					<?php
					foreach ( $post_types as $post_type => $data ) {
						if (in_array($post_type, $unset_switch_types)) {
							continue;
						}
						$label       = $data['label'];
						$loop_styles = $data['loop-styles'] ?? array();
						?>
						<option data-switcher-target="cubewp-switcher-tab-<?php echo esc_attr( $post_type ); ?>"
						        value="<?php echo esc_attr( $post_type ); ?>"><?php echo esc_html( $label ); ?></option>
						<?php
						if ( ! empty( $loop_styles ) && is_array( $loop_styles ) ) {
							ob_start();
							?>
							<div
								class="cubewp-switcher-tab-<?php echo esc_attr( $post_type ); ?> cubewp-tab-switcher-target">
								<div class="cubewp-builder-sidebar-option">
									<label for="cubewp-builder-<?php echo esc_attr( $post_type ); ?>-plan">Select Loop
										Style</label>
									<select name="cubewp-builder-<?php echo esc_attr( $post_type ); ?>-plan"
									        id="cubewp-builder-<?php echo esc_attr( $post_type ); ?>-plan"
									        class="cubewp-tab-switcher">
										<?php
										foreach ( $loop_styles as $loop_style => $_label ) {
											$id = $post_type . '-' . $loop_style;
											?>
											<option
												data-switcher-target="cubewp-switcher-tab-<?php echo esc_attr( $id ); ?>"
												value="<?php echo esc_attr( $loop_style ); ?>"><?php echo esc_html( $_label ); ?></option>
											<?php
										}
										?>
									</select>
								</div>
							</div>
							<?php
							$loop_style_switcher .= ob_get_clean();
						}
					}
					?>
				</select>
			</div>
			<?php
			echo cubewp_core_data( $loop_style_switcher );
		} else {
			?>
			<h4><?php esc_html_e( 'No Post Type Found', 'cubewp-frontend' ); ?></h4>
			<?php
		}
	}

	private static function cubewp_loop_builder_default_fields( $post_type ) {
		$wp_default_fields                = cubewp_post_type_default_fields( $post_type );
		$wp_default_fields['the_excerpt'] = array(
			'label' => __( "Excerpt", "cubewp-frontend" ),
			'name'  => 'the_excerpt',
			'type'  => 'wysiwyg_editor',
		);
		$wp_default_fields['post_link']   = array(
			'label' => __( "Post Link", "cubewp-frontend" ),
			'name'  => 'post_link',
			'type'  => 'url',
		);
		$wp_default_fields['the_date']    = array(
			'label' => __( "Post Date", "cubewp-frontend" ),
			'name'  => 'the_date',
			'type'  => 'date',
		);
		$wp_default_fields['post_class']    = array(
			'label' => __( "Post Class", "cubewp-frontend" ),
			'name'  => 'post_class',
			'type'  => 'class',
			'attributes'  => '{cwp-col-12__cwp-col-md-4}',
		);
		$taxonomies                       = get_object_taxonomies( $post_type, 'objects' );
		if ( ! empty( $wp_default_fields ) ) {
			?>
			<div class="cubewp-builder-section cubewp-expand-container active-expanded">
				<div class="cubewp-builder-section-header">
					<h3><?php esc_html_e( 'WordPress Default Fields', 'cubewp-frontend' ); ?></h3>
					<div class="cubewp-builder-section-actions">
						<span
							class="dashicons dashicons-arrow-down-alt2 cubewp-builder-section-action-expand cubewp-expand-trigger expanded"></span>
					</div>
				</div>
				<div class="cubewp-loop-builder-fields cubewp-expand-target">
					<?php
					foreach ( $wp_default_fields as $field ) {
						$label = $field['label'];
						$name  = $field['name'];
						$attributes  = isset($field['attributes']) && !empty($field['attributes']) ? $field['attributes']: '';
						self::cubewp_get_loop_shortcode_field( $name, $label, $attributes );
					}
					?>
				</div>
			</div>
			<?php
		}
		if ( ! empty( $taxonomies ) && ! is_wp_error( $taxonomies ) ) {
			?>
			<div class="cubewp-builder-section cubewp-expand-container">
				<div class="cubewp-builder-section-header">
					<h3><?php esc_html_e( 'Taxonomies', 'cubewp-frontend' ); ?></h3>
					<div class="cubewp-builder-section-actions">
						<span
							class="dashicons dashicons-arrow-down-alt2 cubewp-builder-section-action-expand cubewp-expand-trigger"></span>
					</div>
				</div>
				<div class="cubewp-loop-builder-fields cubewp-expand-target">
					<?php
					foreach ( $taxonomies as $taxonomy ) {
						$label = $taxonomy->label;
						$name  = $taxonomy->name;
						$link  = $taxonomy->name . '_tax_link';
						self::cubewp_get_loop_shortcode_field( array( $name, $link ), $label );
					}
					?>
				</div>
			</div>
			<?php
		}
		?>
		<div class="cubewp-builder-section cubewp-expand-container">
			<div class="cubewp-builder-section-header">
				<h3><?php esc_html_e( 'Author', 'cubewp-frontend' ); ?></h3>
				<div class="cubewp-builder-section-actions">
					<span
						class="dashicons dashicons-arrow-down-alt2 cubewp-builder-section-action-expand cubewp-expand-trigger"></span>
				</div>
			</div>
			<div class="cubewp-loop-builder-fields cubewp-expand-target">
				<?php
				self::cubewp_get_loop_shortcode_field( 'author_name', esc_html__( 'Author Name', 'cubewp-frontend' ) );
				self::cubewp_get_loop_shortcode_field( 'author_link', esc_html__( 'Author Link', 'cubewp-frontend' ) );
				self::cubewp_get_loop_shortcode_field( 'author_avatar', esc_html__( 'Author Avatar', 'cubewp-frontend' ) );
				?>
			</div>
		</div>
		<div class="cubewp-builder-section cubewp-expand-container">
			<div class="cubewp-builder-section-header">
				<h3><?php esc_html_e( 'CubeWP UI & Custom Tags', 'cubewp-frontend' ); ?></h3>
				<div class="cubewp-builder-section-actions">
					<span
						class="dashicons dashicons-arrow-down-alt2 cubewp-builder-section-action-expand cubewp-expand-trigger"></span>
				</div>
			</div>
			<div class="cubewp-loop-builder-fields cubewp-expand-target">
				<?php
				$cubewp_tags = array(
					'post_save' => esc_html__( 'Add | Remove Save', 'cubewp-frontend' )
				);
				$custom_tags = apply_filters("cubewp/post/cards/{$post_type}/custom/tags", []);
				$all_tags = array_merge($cubewp_tags,$custom_tags);
				foreach($all_tags as $tags => $label){
					self::cubewp_get_loop_shortcode_field( $tags, $label );
				}
				?>
			</div>
		</div>
		<?php
	}

	private static function cubewp_get_loop_shortcode_field( $name, $label, $attributes = '' ) {
		$shortcodes = self::cubewp_get_loop_shortcode( $name, $attributes );
		?>
		<div class="cubewp-loop-builder-field">
			<p class="cubewp-loop-builder-field-label"><?php echo esc_html( $label ); ?></p>
			<?php
			if ( ! empty( $shortcodes ) && is_array( $shortcodes ) ) {
				foreach ( $shortcodes as $shortcode ) {
					$is_marquee = strlen( $shortcode ) > 25;
					?>
					<div class="cubewp-loop-builder-field-shortcode <?php echo $is_marquee ? 'have-marquee' : ''; ?>"
						id="cubewp-loop-field-<?php echo esc_attr( $shortcode ); ?>">
						<span><?php echo esc_html( $shortcode ); ?></span>
					</div>
					<?php
				}
			} else {
				$is_marquee = strlen( $shortcodes ) > 25;
				?>
				<div class="cubewp-loop-builder-field-shortcode <?php echo $is_marquee ? 'have-marquee' : ''; ?>"
				     id="cubewp-loop-field-<?php echo esc_attr( $name ); ?>">
					<span><?php echo esc_html( $shortcodes ); ?></span>
				</div>
				<?php
			}
			?>
		</div>
		<?php
	}

	private static function cubewp_get_loop_shortcode( $name, $attributes = '' ) {
		$attributes = !empty($attributes) ? $attributes : '';
		if ( ! empty( $name ) && is_array( $name ) ) {
			$return = array();
			foreach ( $name as $shortcode ) {
				$return[] = '[' . self::$cubewp_loop_field_prefix . $shortcode . ']';
			}

			return $return;
		} else {
			return '[' . self::$cubewp_loop_field_prefix . $name . $attributes.']';
		}
	}

	private static function cubewp_loop_builder_custom_fields( $post_type ) {
		$groups = cwp_get_groups_by_post_type( $post_type );
		if ( ! empty( $groups ) ) {
			foreach ( $groups as $group_id ) {
				$fields = get_post_meta( $group_id, '_cwp_group_fields', true );
				$terms  = get_post_meta( $group_id, '_cwp_group_terms', true );
				$fields = ! empty( $fields ) ? explode( ',', $fields ) : array();
				$terms  = ! empty( $terms ) ? explode( ',', $terms ) : array();
				if ( empty( $fields ) ) {
					continue;
				}
				?>
				<div class="cubewp-builder-section cubewp-expand-container">
					<div class="cubewp-builder-section-header">
						<h3><?php echo get_the_title( $group_id ); ?></h3>
						<?php
						if ( ! empty( $terms ) ) {
							$separator = '';
							$_terms    = '';
							foreach ( $terms as $term ) {
								$term_data = get_term( $term );
								if ( isset( $term_data->name ) && ! empty( $term_data->name ) ) {
									$_terms    .= $separator . $term_data->name;
									$separator = ',';
								}
							}
							?>
							<div class="cwp-icon-helpTip">
								<span class="dashicons dashicons-editor-help"></span>
								<div class="cwp-ctp-toolTips drop-left">
									<div class="cwp-ctp-toolTip">
										<h4><?php esc_html_e( 'Associated Taxonomies', 'cubewp-frontend' ); ?></h4>
										<p class="cwp-ctp-tipContent"><?php echo esc_html( $_terms ); ?></p>
									</div>
								</div>
							</div>
							<?php
						}
						?>
						<div class="cubewp-builder-section-actions">
							<span
								class="dashicons dashicons-arrow-down-alt2 cubewp-builder-section-action-expand cubewp-expand-trigger"></span>
						</div>
					</div>
					<div class="cubewp-loop-builder-fields cubewp-expand-target">
						<?php
						$voided_fields = self::cubewp_loop_builder_voided_field_types();
						foreach ( $fields as $field ) {
							$field = get_field_options( $field );
							if ( ! isset( $field['name'] ) || in_array( $field['type'], $voided_fields ) ) {
								continue;
							}
							$label = $field['label'];
							$name  = $field['name'];
							self::cubewp_get_loop_shortcode_field( $name, $label );
						}
						?>
					</div>
				</div>
				<?php
			}
		}
	}

	private static function cubewp_loop_builder_custom_cubes( $post_type ) {
		$cubes = apply_filters( 'cubewp/builder/loop_builder/custom/cubes/sections', array(), $post_type );
		if ( ! empty( $cubes ) ) {
		   foreach ( $cubes as $cube ) {
			  $fields = isset( $cube['fields'] ) && ! empty( $cube['fields'] ) ? $cube['fields'] : array();
			  if ( empty( $fields ) ) {
				 continue;
			  }
				   $group_title = isset( $cube['section_title'] ) && ! empty( $cube['section_title'] ) ? $cube['section_title'] : '';
				   ?>
			  <div class="cubewp-builder-section cubewp-expand-container">
				 <div class="cubewp-builder-section-header">
					<h3><?php echo esc_html( $group_title ); ?></h3>
					<div class="cubewp-builder-section-actions">
					   <span class="dashicons dashicons-arrow-down-alt2 cubewp-builder-section-action-expand cubewp-expand-trigger"></span>
					</div>
				 </div>
				 <div class="cubewp-loop-builder-fields cubewp-expand-target">
					<?php
					$voided_fields = self::cubewp_loop_builder_voided_field_types();
					foreach ( $fields as $field ) {
							   if ( ! is_array( $field ) ) {
								$field = get_field_options( $field );
							   }
					   if ( ! isset( $field['name'] ) || in_array( $field['type'], $voided_fields ) ) {
						  continue;
					   }
					   $label = $field['label'];
					   $name  = $field['name'];
					   self::cubewp_get_loop_shortcode_field( $name, $label );
					}
					?>
				 </div>
			  </div>
			  <?php
		   }
		}
	}

	private static function cubewp_loop_builder_content() {
		$post_types = self::$cubewp_style_options;
		?>
		<div class="cubewp-builder-container">
			<div class="cubewp-builder">
				<?php
				if ( ! empty( $post_types ) && is_array( $post_types ) ) {
					foreach ( $post_types as $post_type => $data ) {
						$loop_styles = $data['loop-styles'] ?? array();
						?>
						<div id="type-<?php echo esc_html( $post_type ); ?>"
						     class="cubewp-type-container cubewp-switcher-tab-<?php echo esc_html( $post_type ); ?> cubewp-tab-switcher-target">
							<?php
							if ( ! empty( $loop_styles ) && is_array( $loop_styles ) ) {
								foreach ( $loop_styles as $name => $label ) {
									$id = $post_type . '-' . $name;
									?>
									<div id="plan-<?php echo esc_html( $id ); ?>"
									     class="cubewp-plan-tab cubewp-switcher-tab-<?php echo esc_html( $id ); ?> cubewp-tab-switcher-target"
									     data-id="<?php echo esc_html( $name ); ?>"
									     data-type="<?php echo esc_html( $name ); ?>">
										<?php
										self::cubewp_loop_builder_area( $post_type, $name );
										?>
									</div>
									<?php
								}
							} else {
								self::cubewp_loop_builder_area( $post_type, '' );
							}
							?>
						</div>
						<?php
					}
				}
				?>
			</div>
		</div>
		<?php
	}

	private static function cubewp_loop_builder_area( $post_type, $style ) {
		$form_options         = cubewp_post_card_style_output($post_type, $style);
		
		$loop_container_class = '';
		$loop_is_primary      = '0';
		$loop_layout_html     =  $loop_layout_css  = '';
		if ( !empty( $form_options ) ) {			
			$loop_container_class = $form_options['form']['loop-container-class'] ?? '';
			$loop_is_primary      = $form_options['form']['loop-is-primary'] ?? '0';
			$preview_post_id      = $form_options['form']['preview-postid'] ?? '';
			$preview_post_id      = $form_options['form']['preview-postid'] ?? '';
			if ( isset($form_options[ 'html' ])){
				$loop_layout_html = $form_options[ 'html' ];
			}
			if ( isset($form_options[ 'css' ])){
				$loop_layout_css = $form_options[ 'css' ];
			}
		}
		?>

		<div class="cubewp-builder-container-topbar">
			<button class="button form-settings-form">
				<span class="dashicons dashicons-admin-generic"></span>
				<?php esc_html_e( 'Loop Settings', 'cubewp-frontend' ); ?>
			</button>
		</div>

		<div class="cubewp-builder-area cubewp-post-loop-generator">

			<div class="form-settings">
				<div class="cwpform-settings">
					<div class="cwpform-setting-label">
						<h2><?php esc_html_e( 'Loop Settings', 'cubewp-frontend' ); ?></h2>
					</div>
					<div class="cwpform-setting-fields">
						<div class="cwpform-setting-field" style="display: none;">
							<label><?php esc_html_e( 'Loop Container Classes', 'cubewp-frontend' ); ?></label>
							<?php
							$input_attrs = array(
								'class'       => 'form-field',
								'name'        => 'loop-container-class',
								'value'       => $loop_container_class,
								'extra_attrs' => 'data-name="loop-container-class"',
							);
							echo cwp_render_text_input( $input_attrs );
							?>
						</div>
						<div class="cwpform-setting-field">
							<label><?php esc_html_e( 'Select post for preview', 'cubewp-frontend' ); ?></label>
							<?php
							$args = array(
								'post_type'      => $post_type,
								'posts_per_page' => 10,
								'orderby'        => 'date',
								'order'          => 'DESC',
								'fields'         => 'ids' // Only retrieve post IDs
							);
						
							// Fetch the posts
							$options = get_posts($args);
							$posts = array();
							if(is_array($options) && !empty($options)){
								foreach($options as $option){
									$posts[$option] = get_the_title($option);
								}
							}
							
							$input_attrs = array(
								'class'       => 'form-field preview-postid',
								'name'        => 'preview-postid',
								'value'       => (isset($preview_post_id) && !empty($preview_post_id)) ? $preview_post_id : '',
								'options'     => $posts,
								'extra_attrs' => 'data-name="preview-postid"',
							);
							echo cwp_render_dropdown_input( $input_attrs );
							?>
							<h4><?php esc_html_e( 'Make this Post-Card primary for this Post-Type', 'cubewp-frontend' ); ?></h4> &nbsp;
							<?php
							$input_attrs = array(
								'name'        => 'loop-is-primary',
								'class'       => 'form-field loop-is-primary',
								'value'       => $loop_is_primary,
								'extra_attrs' => 'data-name="loop-is-primary"',
							);
							echo cwp_render_switch_input($input_attrs);
							?>
						</div>
					</div>
				</div>
			</div>
			
			<div class="cubewp-builder-sections">
				<div class="cubewp-builder-section cubewp-expand-container">
					<div class="cubewp-loop-preview"></div>
					<style><?php echo cubewp_core_data( stripslashes( $loop_layout_css ) ); ?></style>
					<div class="cubewp-builder-section-header">
						<h3><?php esc_html_e( 'Edit Post Card Layout', 'cubewp-frontend' ); ?></h3>
						<div class="cubewp-builder-section-actions">
							<span class="dashicons dashicons-arrow-down-alt2 cubewp-builder-section-action-expand cubewp-expand-trigger expanded"></span>
						</div>
					</div>
					<div class="cubewp-builder-section-fields cubewp-expand-target">
						<div class="cubewp-builder-group-widget">
							<input type="hidden" class="field-name" value="loop-builder">
							<div class="cubewp-loop-builder-editor-container clearfix">
								<div class="cubewp-loop-builder-editor"
								     id="<?php echo esc_attr( 'cubewp-loop-builder-' . $post_type . '-' . $style . '-html-editor' ); ?>">
								</div>
								<?php 
								$field_args = array(
									'id'                =>  'cubewp-loop-builder-' . $post_type . '-' . $style . '-html',
									'name'              =>  'loop-layout-html',
									'placeholder'       =>  '',
									'class'             =>  'ace-editor hidden group-field html-builder',
									'value'             =>  cubewp_core_data( $loop_layout_html  ),
									'extra_attrs'       =>  'data-name="loop-layout-html" data-editor="cubewp-loop-builder-' . $post_type . '-' . $style . '-html-editor" data-mode="html" data-theme="monokai"',
									'rows'              =>  50,
								);
								echo cwp_render_textarea_input( $field_args );
								?>
							</div>
						</div>
					</div>
				</div>

				<div class="cubewp-builder-section cubewp-expand-container">
					<div class="cubewp-builder-section-header">
						<h3><?php esc_html_e( 'Layout CSS', 'cubewp-frontend' ); ?></h3>
						<div class="cubewp-builder-section-actions">
							<span class="dashicons dashicons-arrow-down-alt2 cubewp-builder-section-action-expand cubewp-expand-trigger expanded"></span>
						</div>
					</div>
					<div class="cubewp-builder-section-fields cubewp-expand-target">
						<div class="cubewp-builder-group-widget">
							<input type="hidden" class="field-name" value="loop-builder">
							<div class="cubewp-loop-builder-editor-container clearfix">
								<div class="cubewp-loop-builder-editor-css"
								     id="<?php echo esc_attr( 'cubewp-loop-builder-' . $post_type . '-' . $style . '-css-editor' ); ?>">
								</div>
								<?php 
								$field_argss = array(
									'id'                =>  'cubewp-loop-builder-' . $post_type . '-' . $style . '-css',
									'name'              =>  'loop-layout-css',
									'placeholder'       =>  '',
									'class'             =>  'ace-editor hidden group-field css-builder',
									'value'             =>  cubewp_core_data(  $loop_layout_css  ),
									'extra_attrs'       =>  'data-name="loop-layout-css" data-editor="cubewp-loop-builder-' . $post_type . '-' . $style . '-css-editor" data-mode="css" data-theme="monokai"',
									'rows'              =>  50,
								);
								echo cwp_render_textarea_input( $field_argss );
								?>
							</div>
						</div>
					</div>
				</div>
	
				<?php
					do_action( 'cubewp/loop/builder/area', $post_type, $style );
				?>
			</div>
		</div>
		<?php
		self::cubewp_loop_builder_hidden_fields( $post_type );
	}

	protected static function cubewp_loop_builder_hidden_fields( $form_relation ) {
		$hidden_fields = array(
			array(
				'class' => 'form-relation',
				'name'  => 'form_relation',
				'value' => $form_relation,
			),
			array(
				'class' => 'form-type',
				'name'  => 'form_type',
				'value' => 'loop_builder',
			),
		);
		foreach ( $hidden_fields as $field ) {
			echo cwp_render_hidden_input( $field );
		}
	}

	public static function cubewp_process_post_card_preview(){
        if ( ! wp_verify_nonce($_POST['security_nonce'], "cubewp-admin-nonce")) {
			wp_send_json_error(array(
				'msg' => esc_html__('Sorry! Security Verification Failed.', 'cubewp-framework'),
			), 404);
		}
        if(isset($_POST['html']) && !empty($_POST['html'])){

            $postID = isset($_POST['post_id']) && !empty($_POST['post_id']) ? $_POST['post_id'] : 1;
            $loop_layout_html =   $_POST['html'];
            $string = stripslashes( $loop_layout_html );
			$string = cubewp_process_post_card( $string, $postID );

        }else{
            $string = 'There is nothing to display.....';
        }
		
        wp_send_json_success(array( 'html' => $string));
	}

	public static function cubewp_loop_builder_save($content, $form, $post_type) {
		if(is_array($form) && count($form) > 0){
			$file_content = $form;
			foreach($form[$post_type] as $style => $options){
				unset($form[$post_type][$style]['loop-layout-html']);
				unset($form[$post_type][$style]['loop-layout-css']);
				unset($file_content[$post_type][$style]['form']);
				if(!empty($file_content[$post_type][$style]['loop-layout-html'])){
					$file_content[$post_type][$style]['loop-layout-html'] = stripslashes( $file_content[$post_type][$style]['loop-layout-html'] );
					$file_content[$post_type][$style]['loop-layout-css'] = stripslashes( $file_content[$post_type][$style]['loop-layout-css'] );
				}else{
					unset($file_content[$post_type][$style]);
				}
				
			}
			// Define the file path
			$filePath = ensure_cubewp_post_cards_file();

			if (file_exists($filePath)) {
				$existingArray = include $filePath;
				if (!is_array($existingArray)) {
					$existingArray = array();
				}
			} else {
				$existingArray = array();
			}
			
			$combinedArray = array_merge($existingArray, $file_content);

			$combinedArrayCode = '<?php' . PHP_EOL . 'return ' . var_export($combinedArray, true) . ';' . PHP_EOL . '?>';

			if (file_put_contents($filePath, $combinedArrayCode) === false) {
				wp_send_json( array( 'message' => esc_html__( "There is a problem saving html/css", "cubewp-framework" ) ) );
			}else{
				return $form[$post_type];
			}
		}
	}
	
	public static function cubewp_default_post_card($content) {
		return array(
			'html' => <<<HTML
						<div  [loop_post_class{cwp-col-12__cwp-col-md-4}] >
							<div class="cwp-post">
								<div class="cwp-post-thumbnail">
									<a href=" [loop_post_link] ">
										<img src=" [loop_featured_image] " alt="">
									</a>
								
									<div class="cwp-archive-save">
										<div class="cwp-single-save-btns cwp-single-widget">
										[loop_post_save] 
										</div>
									</div>
								</div>
								<div class="cwp-post-content-container">
									<div class="cwp-post-content">
										<h4><a href=" [loop_post_link] "> [loop_the_title] </a>
										</h4>
										[loop_the_content] 
									</div>
									<ul class="cwp-post-terms">
										<li>
											<a href=" [loop_property_type_tax_link] "> [loop_property_type] </a>
										</li>
									</ul>        
								</div>
							</div>
						</div>
						HTML,
			'css' => <<<HTML
					/*----Grid View-----*/
					.cwp-post {
						background: #ffffff;
						border: 1px solid #e0e0e0;
						border-radius: 5px;
						filter: drop-shadow(0 2px 6px rgba(0, 0, 0, 0.102218));
						margin: 10px 0px;
						overflow: hidden;
						transition: 300ms;
					}

					.cwp-post:hover {
						filter: none;
					}

					.cwp-post-thumbnail {
						height: 220px;
						width: 100%;
						position: relative;
					}

					.cwp-post .cwp-post-thumbnail img {
						height: 100%;
						object-fit: cover;
						transition: 300ms;
						width: 100%;
					}

					/*-------List View------*/
					.list-view .cwp-col-12 {
						width: 100% !important;
					}

					.list-view .cwp-post {
						align-items: flex-start;
						display: flex;
						flex-wrap: wrap;
						justify-content: flex-start;
						position: relative;
					}

					.list-view .cwp-post-thumbnail {
						width: 30%;
						min-height: 160px;
						height: 185px;
					}

					.list-view .cwp-post-content-container {
						width: 70%;
					}

					.list-view .cwp-post-content {
						padding: 30px 20px;
					}

					.list-view .cwp-post-content h4 {
						overflow: hidden;
						text-overflow: ellipsis;
						white-space: nowrap;
						width: 100%;
					}

					.list-view ul.cwp-post-terms {
						padding: 20px 20px 0 20px;
					}

					.cwp-promoted-post {
						border: 1px solid #ddbb2a;
						border-radius: 4px;
						color: #ddbb2a;
						cursor: default;
						display: inline-block;
						font-size: 10px;
						line-height: 1;
						padding: 2px 5px;
						position: relative;
						top: -2px;
					}

					.cwp-post-content {
						padding: 15px;
					}

					.cwp-post-content h4 {
						font-size: 20px;
						font-weight: bold;
						line-height: 1.3;
						margin: 0 0 5px 0;
						text-overflow: ellipsis;
						overflow: hidden;
						white-space: nowrap;
					}

					.cwp-post-content p {
						font-size: 14px;
						line-height: 1.3;
						margin: 0 0 0 0;
					}

					.cwp-post-terms {
						align-items: center;
						border-top: 1px solid #e0e0e0;
						display: flex;
						flex-wrap: wrap;
						justify-content: flex-start;
						line-height: 1.5;
						list-style: none;
						margin: 0;
						padding: 15px 15px 10px;
					}

					.cwp-post-terms li {
						margin: 0 5px 5px 0;
					}

					.cwp-post-terms li a {
						display: block;
						font-size: 12px;
						background: #f6f6f6;
						max-width: 100px;
						overflow: hidden;
						text-overflow: ellipsis;
						white-space: nowrap;
						padding: 8px 8px;
						font-weight: 500;
						text-transform: capitalize;
						border-radius: 210px;
						line-height: 10px;
						color: #343A40;
					}

					/*---Save button-------*/
					.cwp-archive-save {
						position: absolute;
						background: rgba(0, 0, 0, 0) linear-gradient(0deg, rgba(0, 0, 0, .9) 8%, rgba(0, 0, 0, 0) 94%) repeat scroll 0 0;
						bottom: 0;
						padding: 15px 14px 5px 14px;
						width: 100%;
					}

					.cwp-archive-save .cwp-single-save-btns.cwp-single-widget {
						float: right;
						color: #fff;
					}

					span.cwp-main.cwp-save-post svg:nth-child(2) {
						display: none;
					}

					span.cwp-main.cwp-saved-post svg:first-child {
						display: none;
					}

					.cwp-single-save-btns.cwp-single-widget span.cwp-main,
					.cwp-single-share-btn.cwp-single-widget span.cwp-main {
						cursor: pointer;
					}
					.cwp-post-boosted {
						padding: 1px 10px;
						position: absolute;
						top: 15px;
						left: 15px;
						background: #FFBB00;
						border-radius: 12px;
						color: #000000;
						font-weight: 500;
						font-size: 13px;
					}
					HTML,
		);
	}
}