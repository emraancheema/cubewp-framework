<?php

/**
 * CubeWp Frontend templates is for display of single post and archive templates
 *
 * @version 1.0.5
 * @package cubewp/cube/classes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use ElementorPro\Modules\ThemeBuilder\Module;
/**
 * CubeWp_frontend
 */
class CubeWp_Frontend_Templates {
    
    private $elementor_template = false;
    
    public function __construct( ) {
        global $cwpOptions;
        if (empty($cwpOptions)) {
            $cwpOptions = get_option('cwpOptions');
        }
        $is_cubewp_author = (isset($cwpOptions['show_author_template']) && ! empty($cwpOptions['show_author_template'])) ? $cwpOptions['show_author_template'] : 0;
        
        if (CWP()->is_request('frontend')) {

            // CubeWP theme builder All single
            if (!is_singular( 'product' ) ) {
                add_filter('single_template', array($this, 'cubewp_single_template'), 99,3);
            }

            // CubeWP theme builder Single Product
            if (is_singular( 'product' )) {
                add_filter('template_include', array($this, 'cubewp_single_template'), 99,3);
            }

            // CubeWP theme builder Archives
            if (!is_post_type_archive('product')) {
                add_filter('archive_template', array($this, 'cubewp_archive_template'), 49,3);
                add_filter('search_template', array($this, 'cubewp_archive_template'), 49,3);
                add_filter('taxonomy_template', array($this, 'cubewp_archive_template'), 49,3);
            }

            // CubeWP theme builder Product
            if (is_post_type_archive('product')) {
                add_filter('template_include', array($this, 'cubewp_archive_template'), 99,3);
            }

            // CubeWP theme builder Author Template
            if ($is_cubewp_author) {
                add_filter('author_template', array($this, 'cubewp_author_template'), 50,3);
            }

            // CubeWP theme builder Header
            if(CubeWp_Theme_Builder::is_cubewp_theme_builder_active('header') || is_singular( 'cubewp-tb' ) ){
                add_action('cubewp/theme_builder/header', function(){
                    CubeWp_Theme_Builder::do_cubewp_theme_builder('header');
                });
                add_action('get_header', array($this, 'cubewp_header_template'), 10);
            }

            //CubeWP theme builder Footer
            if(CubeWp_Theme_Builder::is_cubewp_theme_builder_active('footer') || is_singular( 'cubewp-tb' )){
                add_action('cubewp/theme_builder/footer', function(){
                    CubeWp_Theme_Builder::do_cubewp_theme_builder('footer');
                });
                add_action('get_footer', array($this, 'cubewp_footer_template'), 10);
            }

            //CubeWP theme builder Blocks
            if(CubeWp_Theme_Builder::is_cubewp_theme_builder_active('block')){
                $template_ids = CubeWp_Theme_Builder::get_current_template_post_id('block');
                foreach($template_ids as $template_id){
                    $hook = get_post_meta( $template_id, 'template_location', true );
                    $content_to_echo = CubeWp_Theme_Builder::do_cubewp_theme_builder('block', $template_id, true);
                    add_action( $hook, function() use ( $content_to_echo ) {
                        echo $content_to_echo;
                    });
                }
            }

            //CubeWP theme builder 404
            if(CubeWp_Theme_Builder::is_cubewp_theme_builder_active('404')){
                if( is_404() ){
                    add_action('cubewp/theme_builder/404', function(){
                        CubeWp_Theme_Builder::do_cubewp_theme_builder('404');
                    });
                    add_action('template_redirect', array($this, 'cubewp_404_template'), 10);
                }
            }
        }
    }

    /**
     * Method elementor_archive_template_include
     *
     * @return bool
     * @since  1.0.5
     */
    public function elementor_archive_template_include() {
        if ( ! class_exists( '\Elementor\Plugin' ) || ! class_exists( 'ElementorPro\Modules\ThemeBuilder\Module' ) ) {
            return;
        }

        // Is Archive?
        $locations_manager = Module::instance()->get_conditions_manager()->get_documents_for_location( 'archive' );
        if (!empty($locations_manager)) {
            return !empty($locations_manager);
        }
	}

    /**
     * Method elementor_single_template_include
     *
     * @return bool
     * @since  1.0.5
     */
    public function elementor_single_template_include() {

        if ( ! class_exists( '\Elementor\Plugin' ) || ! class_exists( 'ElementorPro\Modules\ThemeBuilder\Module' ) ) {
            return;
        }

        // IS Single?
        $locations_manager = Module::instance()->get_conditions_manager()->get_documents_for_location( 'single' );
        if (!empty($locations_manager)) {
            return !empty($locations_manager);
        }
	}

    /**
     * Method theme_single_post_template
     *
     * @return bool
     * @since  1.0.5
     */
    private function theme_single_post_template() {
		$return = false;

        if (is_singular()){
            $post_type = get_post_type( );
        }else{
            return false;
        }

        if (file_exists(get_template_directory() . '/single-' . $post_type . '.php')){
            $return = true;
        }

        return apply_filters( "cubewp/{$post_type}/single/template", $return );
    }

    /**
     * Method theme_archive_template
     *
     * @return bool
     * @since  1.0.5
     */
    private function theme_archive_template() {
	    $return = false;
		if (!empty(self::locate_current_archive_template())){
			$return = true;
        }
        if ( is_post_type_archive() ) {
            $post_type = get_query_var( 'post_type' );
            if($post_type){
                $return = apply_filters( "cubewp/{$post_type}/archive/template", $return );
            }
        }
	    return $return;
    }


    public function cubewp_single_template($template = '',$type = '',$templates = '') {
        if ( !$this->elementor_single_template_include() ) {

            // If bricks builder is active and single page tempplate is built with bricks
            if (class_exists('Bricks\Helpers') && method_exists('Bricks\Helpers', 'render_with_bricks')) {
                $post_id = get_the_ID(); // Get the current post/page ID                
                // Now call the method safely
                if (Bricks\Helpers::render_with_bricks( $post_id, 'content') && !CubeWp_Theme_Builder::is_cubewp_theme_builder_active('single')) {
                    // If the method returns true, the post is built with Bricks Builder
                    return $template;
                }
            }

            // If post type created with CubeWP and single page template not created with theme builder
            if (
                !array_key_exists(get_post_type(), CWP_types())
                && !is_singular( 'cubewp-tb' ) 
                && !CubeWp_Theme_Builder::is_cubewp_theme_builder_active('single')
                ) {
                return $template;
            }

            if(CubeWp_Theme_Builder::is_cubewp_theme_builder_active('single') || !$this->theme_single_post_template()){

                if(CubeWp_Theme_Builder::cubewp_set_custom_template()){

                    //Set custom template only for elementor editor
                    return CubeWp_Theme_Builder::cubewp_set_custom_template();
                }
                
                //Set Single template for display of custom single page
                return CWP_PLUGIN_PATH . 'cube/templates/single-cpt.php';
            }
        }
        return $template;
    }

    /**
     * Method init
     *
     */
    public function cubewp_archive_template($template = '',$type = '',$templates = '') {
        if ( !$this->elementor_archive_template_include() ) {

            // If bricks builder is active and Archive page template is built with bricks
            if (class_exists('Bricks\Database') && method_exists('Bricks\Database', 'get_template_data')) {
                // Now call the method safely
                if (
                    (Bricks\Database::get_template_data('archive') || Bricks\Database::get_template_data('search'))
                    && !CubeWp_Theme_Builder::is_cubewp_theme_builder_active('archive')
                    )
                {
                    // If the method returns true, the Archive is built with Bricks Builder
                    return $template;
                }
            }

            $current_term = get_queried_object();
            
            if ($current_term && !is_wp_error($current_term) && isset($current_term->taxonomy)) {
                if (!array_key_exists($current_term->taxonomy, CWP_custom_taxonomies()) && !CubeWp_Theme_Builder::is_cubewp_theme_builder_active('archive')) {
                    return $template;
                }
            }
           
            if(is_post_type_archive( 'product' ) && !CubeWp_Theme_Builder::is_cubewp_theme_builder_active('archive')){
                return $template;
            }elseif(CubeWp_Theme_Builder::is_cubewp_theme_builder_active('archive') || !self::theme_archive_template()){
                return CWP_PLUGIN_PATH . 'cube/templates/archive-cpt.php';
            }
            
        }
        return $template;
    }

    
    public static function cubewp_header_template($name) {
        require CWP_PLUGIN_PATH . 'cube/templates/header.php';

        $templates = [];
        $name = (string) $name;
        if ( '' !== $name ) {
            $templates[] = "header-{$name}.php";
        }
        $templates[] = 'header.php';

        // Avoid running wp_head hooks again
        remove_all_actions( 'wp_head' );
        ob_start();
        // It cause a `require_once` so, in the get_header it self it will not be required again.
        locate_template( $templates, true );
        ob_get_clean();
    }

    public static function cubewp_footer_template($name) {
        require CWP_PLUGIN_PATH . 'cube/templates/footer.php';

        $templates = [];
		$name = (string) $name;
		if ( '' !== $name ) {
			$templates[] = "footer-{$name}.php";
		}

		$templates[] = 'footer.php';

		ob_start();
		// It cause a `require_once` so, in the get_header it self it will not be required again.
		locate_template( $templates, true );
		ob_get_clean();
    }

    public function cubewp_404_template($name) {
        // Set the status header
        status_header(404);

        // Clear any buffers that might have been created
        ob_clean();

        // Load the custom 404 template
        require CWP_PLUGIN_PATH . 'cube/templates/404.php';

        exit;
    }

    /**
     * Method init
     *
     */
    public function cubewp_author_template($template = '',$type = '',$templates = '') {
        return CWP_PLUGIN_PATH . 'cube/templates/author.php';
    }

    public function locate_current_archive_template() {
        if (is_category()) {
            $category = get_queried_object();
            $templates = array(
                'taxonomy-category-' . $category->slug . '.php',
                'taxonomy-category.php',
                'category-slug.php',
                'category-ID.php',
                'category.php',
            );
        } elseif (is_tag()) {
            $tag = get_queried_object();
            $templates = array(
                'taxonomy-post_tag-' . $tag->slug . '.php',
                'taxonomy-post_tag.php'
            );
        } elseif (is_tax()) {
            $taxonomy = get_queried_object();
            $templates = array(
                'taxonomy-' . $taxonomy->taxonomy . '-' . $taxonomy->slug . '.php',
                'taxonomy-' . $taxonomy->taxonomy . '.php'
            );
            $templates = apply_filters( "cubewp/{$taxonomy->taxonomy}/archive/template", $templates );
            
        } elseif (is_post_type_archive()) {
            $post_type = get_post_type();
            $templates = array(
                'archive-' . $post_type . '.php'
            );
        } elseif (is_author()) {
            $author = get_queried_object();
            $templates = array(
                'author-' . $author->user_nicename . '.php',
                'author.php'
            );
        }elseif (is_search()) {
            if ( isset( $_GET['post_type'] ) && !empty( $_GET['post_type'] ) ) {
                // Sanitize the post_type value from the query string
                $post_type = sanitize_text_field( $_GET['post_type'] );
                
                // Define the template array
                $templates = array(
                    'search-' . $post_type . '.php'
                );
            }   
        }

        if(!empty($templates)){
            foreach ($templates as $template) {
                $located = locate_template($template);
                if ($located) {
                    return $located;
                }
            }
        }
        return false;
    }
    
    
        
    /**
     * Method init
     *
     * @return void
     */
    public static function init() {
        $CubeClass = __CLASS__;
        new $CubeClass;
    }
    
    
}