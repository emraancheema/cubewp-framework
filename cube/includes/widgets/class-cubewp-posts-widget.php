<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class CubeWp_Posts_Widget extends WP_Widget {

	private static $baseName = self::class;
	private static $instance = array();

	public function __construct() {
		parent::__construct(
			self::$baseName,
			esc_html__( "CubeWP Posts Widget", "cubewp-framework" ),
			array(
				'description' => esc_html__( "A list of your site's post.", "cubewp-framework" ),
			)
		);
		new CubeWp_Ajax( '',
			self::$baseName,
			'cwp_get_terms_by_post_type'
		);
	}

	public function widget( $args, $instance ) {
		extract( $args );
		self::$instance = $instance;
		$before_widget = !isset($before_widget) ? $args['before_widget'] : $before_widget;
		$before_title = !isset($before_title) ? $args['before_title'] : $before_title;
		$after_title = !isset($after_title) ? $args['after_title'] : $after_title;
		$after_widget = !isset($after_widget) ? $args['after_widget'] : $after_widget;
		$title = apply_filters( 'widget_title', $instance['title'] );
		echo cubewp_core_data($before_widget);
		if ( ! empty( $title ) ) {
			echo cubewp_core_data($before_title) . sanitize_text_field($title) . cubewp_core_data($after_title);
		}
		echo self::cwp_widget_get_posts();
		echo cubewp_core_data($after_widget);
	}

	private static function cwp_widget_get_posts() {
		$args   = self::cwp_widget_get_query_args();
		$query  = new WP_Query( $args );
		$output = null;
		if ( $query->have_posts() ) {
			$output .= '<ul>';
			while ( $query->have_posts() ) {
				$query->the_post();
				$output .= '<li><a href="' . get_the_permalink() . '">' . get_the_title() . '</a></li>';
			}
			$output .= '</ul>';
		} else {
			$output .= esc_html__( "No Posts Found", "cubewp-framework" );
		}
		wp_reset_postdata();

		return $output;
	}

	private static function cwp_widget_get_query_args() {
		$instance     = self::$instance;
		$postType     = apply_filters( 'cwp_post_widget_postType', $instance['postType'] );
		$Term         = apply_filters( 'cwp_post_widget_Term', $instance['Term'] );
		$postsPerPage = apply_filters( 'cwp_post_widget_postsPerPage', $instance['postsPerPage'] );
		$orderBy      = apply_filters( 'cwp_post_widget_orderBy', $instance['orderBy'] );
		$Order        = apply_filters( 'cwp_post_widget_Order', $instance['Order'] );
		$args         = array(
			'post_type'      => $postType,
			'posts_per_page' => !empty( $postsPerPage ) ? $postsPerPage : get_option( 'posts_per_page' ),
			'orderby'        => ! empty( $orderBy ) ? $orderBy : 'date',
			'order'          => ! empty( $Order ) ? $Order : 'DESC'
		);
		if ( ! empty( $Term ) ) {
			$termOBJ = get_term( $Term );
			if ( is_object( $termOBJ ) ) {
				$args['tax_query'] = array(
					array(
						'taxonomy' => $termOBJ->taxonomy,
						'field'    => 'slug',
						'terms'    => $termOBJ->slug,
					),
				);
			}
		}

		return $args;
	}

	public function form( $instance ) {
		$title        = !empty($instance['title']) ? $instance['title'] : '';
		$postType     = !empty($instance['postType']) ? $instance['postType'] : '';
		$Term         = !empty($instance['Term']) ? $instance['Term'] : '';
		$postsPerPage = !empty($instance['postsPerPage']) ? $instance['postsPerPage'] : '';
		$orderBy      = !empty($instance['orderBy']) ? $instance['orderBy'] : '';
		$Order        = !empty($instance['Order']) ? $instance['Order'] : '';
		?>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_html_e( 'Title', 'cubewp-framework' ); ?></label>
            <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
        </p>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'postType' ) ); ?>"><?php esc_html_e( 'Select Post Type', 'cubewp-framework' ); ?></label>
            <select id="<?php echo esc_attr( $this->get_field_id( 'postType' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'postType' ) ); ?>" class="widefat cwp-widget-select-posttype">
				<?php echo self::cwp_widget_get_postTypes_options( $postType ); ?>
            </select>
        </p>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'Term' ) ); ?>"><?php esc_html_e( 'Select Term', 'cubewp-framework' ); ?></label>
            <select id="<?php echo esc_attr( $this->get_field_id( 'Term' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'Term' ) ); ?>" class="widefat cwp-widget-select-term">
				<?php echo self::cwp_widget_get_Terms_options( $Term, $postType ); ?>
            </select>
        </p>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'postsPerPage' ) ); ?>"><?php esc_html_e( 'Posts Per Page', 'cubewp-framework' ); ?></label>
            <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'postsPerPage' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'postsPerPage' ) ); ?>" type="number" value="<?php echo esc_attr( $postsPerPage ); ?>">
        </p>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'orderBy' ) ); ?>"><?php esc_html_e( 'Posts Order By', 'cubewp-framework' ); ?></label>
            <select id="<?php echo esc_attr( $this->get_field_id( 'orderBy' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'orderBy' ) ); ?>" class="widefat">
                <option <?php selected( $orderBy, 'date' ) ?> value="date"><?php esc_attr_e( "Date", "cubewp-framework" ); ?></option>
                <option <?php selected( $orderBy, 'title' ) ?> value="title"><?php esc_attr_e( "Title", "cubewp-framework" ); ?></option>
                <option <?php selected( $orderBy, 'rand' ) ?> value="rand"><?php esc_attr_e( "Random", "cubewp-framework" ); ?></option>
            </select>
        </p>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'Order' ) ); ?>"><?php esc_html_e( 'Posts Order', 'cubewp-framework' ); ?></label>
            <select id="<?php echo esc_attr( $this->get_field_id( 'Order' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'Order' ) ); ?>" class="widefat">
                <option <?php selected( $Order, 'DESC' ) ?> value="DESC"><?php esc_attr_e( "Descending Order", "cubewp-framework" ); ?></option>
                <option <?php selected( $Order, 'ASC' ) ?> value="ASC"><?php esc_attr_e( "Ascending Order", "cubewp-framework" ); ?></option>
            </select>
        </p>
		<?php
	}

	private static function cwp_widget_get_postTypes_options( $selected ) {
		$postTypes = cwp_post_types();
        unset($postTypes['page']);
        unset($postTypes['attachment']);
		$output    = null;
		$output    .= '<option ' . selected( $selected, "" ) . ' value="">' . esc_html__( "Select Post Type", "cubewp-framework" ) . '</option>';
		if ( ! empty( $postTypes ) && is_array( $postTypes ) ) {
			foreach ( $postTypes as $post_type ) {
				$output .= '<option ' . selected( $selected, $post_type, false ) . ' value="' . $post_type . '">' . $post_type . '</option>';
			}
		}

		return $output;
	}

	private static function cwp_widget_get_Terms_options( $selected, $postType, $onlyIDS = false ) {
		$Taxonomies = cwp_tax_by_PostType( $postType );
		$ids        = array();
		$output     = null;
		if ( ! empty( $Taxonomies ) && is_array( $Taxonomies ) ) {
			if ( $onlyIDS ) {
				$ids[] = [
					"",
					esc_html__( "Select Term", "cubewp-framework" ),
					selected( $selected, "", false )
				];
			} else {
				$output .= '<option ' . selected( $selected, "", false ) . ' value="">' . esc_html__( "Select Term", "cubewp-framework" ) . '</option>';
			}
			foreach ( $Taxonomies as $taxonomy ) {
				$termsArgs = array(
					"taxonomy"   => $taxonomy,
					"hide_empty" => false,
				);
				$Terms     = get_terms( $termsArgs );
				if ( ! empty( $Terms ) && is_array( $Terms ) ) {
					foreach ( $Terms as $term ) {
						if ( $onlyIDS ) {
							$ids[] = [
								$term->term_id,
								$term->name . ' ( ' . $term->taxonomy . ')',
								selected( $selected, $term->term_id, false )
							];
						} else {
							$output .= '<option ' . selected( $selected, $term->term_id, false ) . ' value="' . $term->term_id . '">' . $term->name . ' ( ' . $term->taxonomy . ')</option>';
						}
					}
				}
			}
		} else {
			if ( $onlyIDS ) {
				$ids[] = [
					"",
					esc_html__( "Select Post Type First", "cubewp-framework" ),
					selected( $selected, "", false )
				];
			} else {
				$output .= '<option ' . selected( $selected, "", false ) . ' value="">' . esc_html__( "Select Post Type First", "cubewp-framework" ) . '</option>';
			}
		}
		if ( $onlyIDS ) {
			return $ids;
		} else {
			return $output;
		}
	}

	public function cwp_get_terms_by_post_type() {
		check_ajax_referer( 'cubewp-admin-nonce', 'nonce' );
		$post_type = sanitize_text_field( $_POST['post_type'] );
		$termsIDS  = self::cwp_widget_get_Terms_options( null, $post_type, true );
		wp_send_json_success( $termsIDS );
	}

	public function update( $new_instance, $old_instance ) {
		$instance = array();

		$instance['title']        = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
		$instance['postType']     = ( ! empty( $new_instance['postType'] ) ) ? $new_instance['postType'] : '';
		$instance['Term']         = ( ! empty( $new_instance['Term'] ) ) ? $new_instance['Term'] : '';
		$instance['postsPerPage'] = ( ! empty( $new_instance['postsPerPage'] ) ) ? $new_instance['postsPerPage'] : '';
		$instance['orderBy']      = ( ! empty( $new_instance['orderBy'] ) ) ? $new_instance['orderBy'] : '';
		$instance['Order']        = ( ! empty( $new_instance['Order'] ) ) ? $new_instance['Order'] : '';

		return $instance;
	}
}