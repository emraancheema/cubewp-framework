<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class CubeWp_Terms_Widget extends WP_Widget {

	private static $baseName = self::class;
	private static $instance = array();

	public function __construct() {
		parent::__construct(
			self::$baseName,
			esc_html__( "CubeWP Terms Widget", "cubewp-framework" ),
			array(
				'description' => esc_html__( "A list of your site's terms.", "cubewp-framework" ),
			)
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
		echo self::cwp_widget_get_terms();
		echo cubewp_core_data($after_widget);
	}

    private static function cwp_widget_get_terms() {
	    $args   = self::cwp_widget_get_terms_args();
        $output = null;
	    $terms  = get_terms($args);
        if (!empty($terms) && is_array($terms)) {
	        $output .= '<ul>';
            foreach ($terms as $term) {
	            $output .= '<li><a href="' . get_term_link($term) . '">' . $term->name . '</a></li>';
            }
	        $output .= '</ul>';
        } else {
	        $output .= esc_html__( "No Posts Found", "cubewp-framework" );
        }

        return $output;
    }

    private static function cwp_widget_get_terms_args() {
	    $instance    = self::$instance;
	    $Taxonomy    = apply_filters('cwp_post_widget_Taxonomy', $instance['Taxonomy']);
	    $termsNumber = apply_filters('cwp_post_widget_termsNumber', $instance['termsNumber']);
	    $hideEmpty   = apply_filters('cwp_post_widget_hideEmpty', $instance['hideEmpty']);
	    $orderBy     = apply_filters('cwp_post_widget_orderBy', $instance['orderBy']);
	    $termsOrder  = apply_filters('cwp_post_widget_termsOrder', $instance['termsOrder']);
        if ($hideEmpty == 'false') {
	        $hideEmpty = false;
        }else {
	        $hideEmpty = true;
        }
	    $args = array(
		    'taxonomy' => $Taxonomy,
		    'number' => !empty( $termsNumber ) ? $termsNumber : get_option( 'posts_per_page' ),
		    'orderby' => $orderBy,
		    'order' => $termsOrder,
		    'hide_empty' => $hideEmpty
        );
	    return $args;
    }

	public function form( $instance ) {
		$title = !empty( $instance['title'] ) ? $instance['title'] : '';
		$Taxonomy = !empty( $instance['Taxonomy'] ) ? $instance['Taxonomy'] : '';
		$termsNumber = !empty( $instance['termsNumber'] ) ? $instance['termsNumber'] : 0;
		$hideEmpty = !empty( $instance['hideEmpty'] ) ? $instance['hideEmpty'] : '';
		$orderBy = !empty( $instance['orderBy'] ) ? $instance['orderBy'] : '';
		$termsOrder = !empty( $instance['termsOrder'] ) ? $instance['termsOrder'] : '';
		?>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_html_e( 'Title', 'cubewp-framework' ); ?></label>
            <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
        </p>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'Taxonomy' ) ); ?>"><?php esc_html_e( 'Select Taxonomy', 'cubewp-framework' ); ?></label>
            <select id="<?php echo esc_attr( $this->get_field_id( 'Taxonomy' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'Taxonomy' ) ); ?>" class="widefat">
                <?php echo self::cwp_widget_get_taxonomies_options( $Taxonomy ); ?>
            </select>
        </p>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'termsNumber' ) ); ?>"><?php esc_html_e( 'Number Of Terms Per Page', 'cubewp-framework' ); ?></label>
            <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'termsNumber' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'termsNumber' ) ); ?>" type="number" min="0" value="<?php echo esc_attr( $termsNumber ); ?>">
        </p>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'hideEmpty' ) ); ?>"><?php esc_html_e( 'Hide Empty Terms', 'cubewp-framework' ); ?></label>
            <select id="<?php echo esc_attr( $this->get_field_id( 'hideEmpty' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'hideEmpty' ) ); ?>" class="widefat">
                <option <?php selected( $hideEmpty, "false" ) ?> value="false"><?php esc_html_e("Don't Hide", "cubewp-framework"); ?></option>
                <option <?php selected( $hideEmpty, "true" ) ?> value="true"><?php esc_html_e("Hide", "cubewp-framework"); ?></option>
            </select>
        </p>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'orderBy' ) ); ?>"><?php esc_html_e( 'Terms Order By', 'cubewp-framework' ); ?></label>
            <select id="<?php echo esc_attr( $this->get_field_id( 'orderBy' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'orderBy' ) ); ?>" class="widefat">
                <option <?php selected( $orderBy, "name" ) ?> value="name"><?php esc_html_e("Terms Name", "cubewp-framework"); ?></option>
                <option <?php selected( $orderBy, "count" ) ?> value="count"><?php esc_html_e("Posts Count", "cubewp-framework"); ?></option>
            </select>
        </p>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'termsOrder' ) ); ?>"><?php esc_html_e( 'Terms Order By', 'cubewp-framework' ); ?></label>
            <select id="<?php echo esc_attr( $this->get_field_id( 'termsOrder' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'termsOrder' ) ); ?>" class="widefat">
                <option <?php selected( $termsOrder, "DESC" ) ?> value="DESC"><?php esc_html_e("Descending Order", "cubewp-framework"); ?></option>
                <option <?php selected( $termsOrder, "ASC" ) ?> value="ASC"><?php esc_html_e("Ascending Order", "cubewp-framework"); ?></option>
            </select>
        </p>
		<?php
	}

	private static function cwp_widget_get_taxonomies_options( $selected ) {
		$Taxonomies = cwp_taxonomies();
		unset($Taxonomies['post_format']);
		$output = null;
		$output .= '<option ' . selected( $selected, "" ) . ' value="">' . esc_html__( "Select Taxonomy", "cubewp-framework" ) . '</option>';
		if (!empty($Taxonomies) && is_array($Taxonomies)) {
			foreach ($Taxonomies as $Taxonomy) {
				$output .= '<option ' . selected($selected, $Taxonomy, false) . ' value="' . $Taxonomy . '">' . $Taxonomy . '</option>';
			}
		}

		return $output;
	}

	public function update( $new_instance, $old_instance ) {
		$instance = array();

		$instance['title'] = (!empty($new_instance['title'])) ? strip_tags($new_instance['title']) : '';
		$instance['Taxonomy'] = (!empty($new_instance['Taxonomy'])) ? strip_tags($new_instance['Taxonomy']) : '';
		$instance['termsNumber'] = (!empty($new_instance['termsNumber'])) ? strip_tags($new_instance['termsNumber']) : '';
		$instance['hideEmpty'] = (!empty($new_instance['hideEmpty'])) ? strip_tags($new_instance['hideEmpty']) : '';
		$instance['orderBy'] = (!empty($new_instance['orderBy'])) ? strip_tags($new_instance['orderBy']) : '';
		$instance['termsOrder'] = (!empty($new_instance['termsOrder'])) ? strip_tags($new_instance['termsOrder']) : '';

		return $instance;
	}
}