<?php
defined( 'ABSPATH' ) || exit;

use Elementor\Controls_Manager;
use Elementor\Repeater;
use Elementor\Widget_Base;

/**
 * CubeWP Search Posts Widgets.
 *
 * Elementor Widget For Search Posts By CubeWP.
 *
 * @since 1.0.0
 */

 class CubeWp_Elementor_Archive_Posts_Widget extends Widget_Base {

    private static $post_types = array();

    public function get_name() {
        return 'search_posts_widget';
    }

    public function get_title() {
        return __( 'Archive Posts Display', 'elementor' );
    }

    public function get_icon() {
        return 'eicon-archive-posts';
    }

    public function get_categories() {
        return [ 'cubewp' ];
    }

    protected function _register_controls() {
        self::get_post_types();

        
        $this->start_controls_section(
            'section_map',
            [
                'label' => __( 'Search Posts Settings', 'elementor' ),
            ]
        );
        $this->add_post_type_controls();
        

        $this->end_controls_section();
    }

    private static function get_post_types() {
		$post_types = get_post_types(['public' => true], 'objects');
        $options = [];
        foreach ($post_types as $post_type) {
            $options[$post_type->name] = $post_type->label;
        }
		unset( $options['elementor_library'] );
		unset( $options['e-landing-page'] );
		unset( $options['attachment'] );
		unset( $options['page'] );

		self::$post_types = $options;
	}

    private static function get_post_type_name_by_slug($post_type_slug) {
		$post_type_object = get_post_type_object($post_type_slug);
		// Check if the post type object exists and return its label (name)
		if ($post_type_object) {
			return $post_type_object->label;
		}
		return null;
	}

    private function add_post_type_controls() {
		$post_types = self::$post_types;
		if ( is_array( $post_types ) && ! empty( $post_types ) ) {
			$this->add_control( 'posttype', array(
				'type'        => Controls_Manager::SELECT2,
				//'multiple'    => true,
				'label'       => esc_html__( 'Select Post Types', 'cubewp-classifiad' ),
				'options'     => $post_types,
				'default'     => array( 'post' ),
				'label_block' => true,
			) );
			foreach ( $post_types as $slug => $post_type ) {
				$this->add_card_style_controls( $slug );
			}
		}
	}

	private function add_card_style_controls($post_type) {
		if(class_exists('CubeWp_Frontend_Load')){
			if(!empty(cubewp_post_card_styles($post_type))){
				$this->add_control( $post_type.'_card_style', array(
					'type'        => Controls_Manager::SELECT,
					'label'       => esc_html__( 'Card Style for '.self::get_post_type_name_by_slug($post_type), 'cubewp-framework' ),
					'options'     => cubewp_post_card_styles($post_type),
					'default'     => 'default_style',
					'condition'   => array(
						'posttype' => $post_type
					)
				) );
			}
		}
	}

    protected function render() {
        $settings   = $this->get_settings_for_display();
        $type = isset( $settings[ 'posttype' ] ) ? $settings[ 'posttype' ] : '';
        $card_style = isset( $settings[ $type . '_card_style' ] ) ? $settings[ $type . '_card_style' ] : '';
        $page_num = '1';

        CubeWp_Enqueue::enqueue_script( 'cwp-search-filters' );

        echo CubeWp_Frontend_Search_Filter::cwp_filter_results(); 
        echo '<form name="cwp-search-filters" class="cwp-search-filters" method="post">';
            echo CubeWp_Frontend_Search_Filter::filter_hidden_fields($type,$page_num,$card_style);
        echo '</form>';

        //Only to load data while editing in elementor
        if ( cubewp_is_elementor_editing()){
            ?>
            <script>cwp_search_filters_ajax_content();</script>
            <?php
        }
    }
}