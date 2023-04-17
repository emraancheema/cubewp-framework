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
		$is_cubewp_single = (isset($cwpOptions['cubewp_singular']) && ! empty($cwpOptions['cubewp_singular'])) ? $cwpOptions['cubewp_singular'] : 0;
		$is_cubewp_archive = (isset($cwpOptions['cubewp_archive']) && ! empty($cwpOptions['cubewp_archive'])) ? $cwpOptions['cubewp_archive'] : 0;
		$is_cubewp_author = (isset($cwpOptions['show_author_template']) && ! empty($cwpOptions['show_author_template'])) ? $cwpOptions['show_author_template'] : 0;
        add_filter( 'elementor/theme/need_override_location', [ $this, 'elementor_template_include' ], 10, 2 );
		if ($is_cubewp_single) {
			add_filter('single_template', array($this, 'cubewp_single_template'), 49,3);
		}
		if ($is_cubewp_archive) {
			add_filter('archive_template', array($this, 'cubewp_archive_template'), 49,3);
			add_filter('search_template', array($this, 'cubewp_archive_template'), 49,3);
            add_filter('taxonomy_template', array($this, 'cubewp_taxonomy_template'), 49,3);
		}
        if ($is_cubewp_author) {
            add_filter('author_template', array($this, 'cubewp_author_template'), 50,3);
        }
    }

    /**
     * Method elementor_template_include
     *
     * @return bool
     * @since  1.0.5
     */
    public function elementor_template_include( $need_override_location, $location ) {
        if ( 'single' === $location || 'archive' === $location  ) {
			$this->elementor_template = true;
		}
		return $this->elementor_template;
	}

    /**
     * Method theme_single_post_template
     *
     * @return bool
     * @since  1.0.5
     */
    private function theme_single_post_template() {
        global $cwpOptions;
        if (empty($cwpOptions)) {
            $cwpOptions = get_option('cwpOptions');
        }
        $cubewp_ignore_theme_single = (isset($cwpOptions['cubewp_ignore_theme_single']) && ! empty($cwpOptions['cubewp_ignore_theme_single'])) ? $cwpOptions['cubewp_ignore_theme_single'] : 0;
        if ( $cubewp_ignore_theme_single ) {
            return false;
        }
        $post_type = CubeWp_Frontend::$post_type;
        if (file_exists(get_template_directory() . '/single-' . $post_type . '.php')){
            return true;
        }
        return false;
    }

    /**
     * Method theme_archive_template
     *
     * @return bool
     * @since  1.0.5
     */
    private function theme_archive_template() {
        $post_type = CubeWp_Frontend::$post_type;
        if (file_exists(get_template_directory() . '/archive-' . $post_type . '.php')){
             return true;
         }
        return false;
    }

    /**
     * Method theme_taxonomy_template
     *
     * @return bool
     * @since  1.0.5
     */
    private function theme_taxonomy_template() {
        $taxonomy = CubeWp_Frontend::$taxonomy;
        if (file_exists(get_template_directory() . '/taxonomy-' . $taxonomy . '.php')){
             return true;
         }
        return false;
    }

    /**
     * Method cubewp_single_template
     *
     */
    public function cubewp_single_template($template = '',$type = '',$templates = '') {
        if(!$this->elementor_template){
            if(CubeWp_Frontend::is_cubewp_single() && !$this->theme_single_post_template()){
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
        if(!$this->elementor_template){
            if(CubeWp_Frontend::is_cubewp_archive() && !$this->theme_archive_template()){
                return CWP_PLUGIN_PATH . 'cube/templates/archive-cpt.php';
            }
        }
        return $template;
    }

    /**
     * Method init
     *
     */
    public function cubewp_taxonomy_template($template = '',$type = '',$templates = '') {
        if(!$this->elementor_template){
            if(CubeWp_Frontend::is_cubewp_taxonomy() && !$this->theme_taxonomy_template()){
                return CWP_PLUGIN_PATH . 'cube/templates/archive-cpt.php';
            }
        }
        return $template;
    }

    /**
     * Method init
     *
     */
    public function cubewp_author_template($template = '',$type = '',$templates = '') {
        return CWP_PLUGIN_PATH . 'cube/templates/author.php';
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