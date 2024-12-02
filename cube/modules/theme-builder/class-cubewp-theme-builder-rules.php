<?php

/**
 * CubeWp Theme builder RUles options
 *
 * @version 1.1.16
 * @package cubewp/cube/mobules/theme-builder
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


/**
 * CubeWp_Theme_Builder_Rules
 */
class CubeWp_Theme_Builder_Rules {

    public function __construct() {
        add_action('wp_ajax_get_template_options', [$this, 'get_template_options']);
    }

    /**
     * Method get_public_taxonomies
     *
     * @return array
	 * * @since   1.1.16
     */
	private static function get_public_taxonomies() {
		$core                  = get_taxonomies( [ '_builtin' => true, 'public' => true, 'hierarchical' => true ], 'objects', 'and' );
		$public                = get_taxonomies( [ '_builtin' => false, 'public' => true, 'hierarchical' => true ], 'objects', 'and' );
		$taxonomies = array_merge($core,$public);
		$taxonomies = array_map(function($taxonomy) {
            return array(
                'name' => esc_attr($taxonomy->name),
                'label' => esc_html($taxonomy->label),
            );
        }, $taxonomies);
		return $taxonomies;
	}

    /**
	 * Method get_public_post_types
	 *
	 * @return array
	 * * @since   1.1.16
	 */
	private static function get_public_post_types() {
		$array = [];
		$core                  = get_post_types( [ '_builtin' => true, 'show_in_menu' => true ], 'objects', 'and' );
        $public                = get_post_types( [ '_builtin' => false, 'public' => true, 'show_in_menu' => true ], 'objects', 'and'  );
		$post_types = array_merge($core,$public);
		$post_types = array_map(function($post_type) {
            return array(
                'name' => esc_attr($post_type->name),
                'label' => esc_html($post_type->label),
                'hasArchive' => $post_type->has_archive,
            );
        }, $post_types);
		
		if(isset($post_types['attachment'])){
			unset($post_types['attachment']);
		}
		if(isset($post_types['e-landing-page'])){
			unset($post_types['e-landing-page']);
		}
		if(isset($post_types['elementor_library'])){
			unset($post_types['elementor_library']);
		}
        return $post_types;
	}
    
    /**
     * Method render_single_options
     *
     * @return HTML
     * @since   1.1.16
     */
    public static function render_single_options() {
        $template_options = '';
        $template_options .= '<optgroup label="Single Posts">';
        $template_options .= '<option value="single_all">All Single</option>';
        foreach (self::get_public_post_types() as $post_type) {
            $template_options .= '<option value="single_' . esc_attr($post_type['name']) . '">Single ' . esc_html($post_type['label']) . '</option>';
            //$exclude_options .= '<option value="exclude_single_' . esc_attr($post_type['name']) . '">Exclude Single ' . esc_html($post_type['label']) . '</option>';
        }
        $template_options .= '</optgroup>';
        return $template_options;
    }
    
    /**
     * Method render_archive_options
     *
     * @return html
     * @since   1.1.16
     */
    public static function render_archive_options() {
        $template_options = '';
        $template_options .= '<optgroup label="Archive">';
        $template_options .= '<option value="archive_all">All Archives</option>';
        foreach (self::get_public_taxonomies() as $taxonomy) {
            $template_options .= '<option value="archive_' . esc_attr($taxonomy['name']) . '">Archive ' . esc_html($taxonomy['label']) . '</option>';
            //$exclude_options .= '<option value="exclude_archive_' . esc_attr($taxonomy['name']) . '">Exclude Archive ' . esc_html($taxonomy['label']) . '</option>';
        }
        $template_options .= '<option value="archive_author">Author Archive</option>';
        $template_options .= '<option value="archive_search">Search Results</option>';
        $template_options .= '</optgroup>';

        $template_options .= '<optgroup label="Post Type Archives">';
        foreach (self::get_public_post_types() as $post_type) {
            if ($post_type['hasArchive']) {
                $template_options .= '<option value="archive_' . esc_attr($post_type['name']) . '">Archive ' . esc_html($post_type['label']) . '</option>';
                //$exclude_options .= '<option value="exclude_archive_' . esc_attr($post_type['name']) . '">Exclude Archive ' . esc_html($post_type['label']) . '</option>';
            }
        }
        $template_options .= '</optgroup>';
        $template_options .= '<optgroup label="Search Results">';
        foreach (self::get_public_post_types() as $post_type) {
            if ($post_type['hasArchive']) {
                $template_options .= '<option value="archive_search_' . esc_attr($post_type['name']) . '">' . esc_html($post_type['label']) . '</option>';
            }
        }
        $template_options .= '</optgroup>';
        return $template_options;
    }
    
    /**
     * Method render_block_options
     *
     * @return HTML
     * @since   1.1.16
     */
    public static function render_block_options() {
        $template_options = '';
        $blocks = is_array(apply_filters('cubewp/theme_builder/blocks', array())) ? apply_filters('cubewp/theme_builder/blocks', array()): array();
        foreach ($blocks as $key => $label) {
            $template_options .= '<option value="' . esc_attr($key) . '">' . esc_html($label) . '</option>';
        }
        return $template_options;
    }
    
    /**
     * Method render_default_options
     *
     * @return HTML
     * @since   1.1.16
     */
    public static function render_default_options() {
        $template_options = '';
        $template_options .= '<optgroup label="General">';
        $template_options .= '<option value="entire_site">Entire Site</option>';
        $template_options .= '<option value="single_all">All Single</option>';
        $template_options .= '<option value="archive_all">All Archives</option>';
        $template_options .= '<option value="archive_author">Author Archive</option>';
        $template_options .= '<option value="archive_search">Search Results</option>';
        $template_options .= '<option value="home">Home Page</option>';
        $template_options .= '<option value="blog">Blog Page</option>';
        $template_options .= '</optgroup>';

        $template_options .= '<optgroup label="Single Post">';
        foreach (self::get_public_post_types() as $post_type) {
            $template_options .= '<option value="single_' . esc_attr($post_type['name']) . '">' . esc_html($post_type['label']) . '</option>';
        }
        $template_options .= '</optgroup>';
        $template_options .= '<optgroup label="Archive">';
        // foreach (self::get_public_post_types() as $post_type) {
        //     if ($post_type['hasArchive']) {
        //         $template_options .= '<option value="archive_' . esc_attr($post_type['name']) . '">' . esc_html($post_type['label']) . '</option>';
        //     }
        // }
        foreach (self::get_public_taxonomies() as $taxonomy) {
            $template_options .= '<option value="archive_' . esc_attr($taxonomy['name']) . '">' . esc_html($taxonomy['label']) . '</option>';
        }
        $template_options .= '</optgroup>';

        $template_options .= '<optgroup label="Search Results">';
        foreach (self::get_public_post_types() as $post_type) {
            if ($post_type['hasArchive']) {
                $template_options .= '<option value="archive_search_' . esc_attr($post_type['name']) . '">' . esc_html($post_type['label']) . '</option>';
            }
        }
        $template_options .= '</optgroup>';
        return $template_options;
    }
    
    /**
     * Method get_template_options
     *
     * @return JSON
     * @since   1.1.16
     */
    public static function get_template_options() {
        if (!isset($_POST['template_type'])) {
            wp_send_json_error(['message' => 'Template type not specified']);
        }
    
        $template_type = sanitize_text_field($_POST['template_type']);
        $template_options = '';
        //$exclude_options = '';
    
        switch ($template_type) {
            case 'single':
                $template_options .= self::render_single_options();
                break;
    
            case 'archive':
                $template_options .= self::render_archive_options();
                break;
    
            case 'block':
                $template_options .= self::render_block_options(); 
                break;
    
            case '404':
            case 'mega-menu':
            case 'shop':
                $template_options .= '<option value="all">all</option>';
                break;
    
            default:
                $template_options .= self::render_default_options();
                break;
        }
    
        wp_send_json_success([
            'template_options' => $template_options,
            //'exclude_options' => $exclude_options
        ]);
    }

    public static function init() {
        $CubeClass = __CLASS__;
        new $CubeClass;
    }

}