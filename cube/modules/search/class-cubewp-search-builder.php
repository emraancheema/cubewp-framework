<?php
/**
 * Post type's search and filters creator for frontend.
 *
 * @package cubewp/cube/includes/modules/search
 * @version 1.0
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * CubeWp_Search_Builder
 */
class CubeWp_Search_Builder extends CubeWp_Form_Builder {
    
    use CubeWp_Builder_Ui;
    protected static $FORM_TYPE = '';
    protected static $Wraper_class = '';
    protected static $Form_title = '';

    public function __construct() {
        add_action( "cubewp_admin_search_filters", array( $this, "create_search_filters" ) );
        add_action( "cubewp_admin_search_fields", array( $this, "create_search_fields" ) );
        
        add_filter("cubewp/builder/search_fields/default/fields",array($this,'default_search_fields'),10,2);
        add_filter("cubewp/builder/search_fields/taxonomies/fields",array($this,'taxonomy_fields'),9,2);

        add_filter("cubewp/builder/search_filters/default/fields",array($this,'default_search_filters'),10,2);
        add_filter("cubewp/builder/search_filters/taxonomies/fields",array($this,'taxonomy_fields'),9,2);
    }
    
    /**
     * Method create_search_fields
     *
     * @return string html
     * @since  1.0.0
     */
    public function create_search_fields() {
        self::$FORM_TYPE = 'search_fields';
        self::$Wraper_class = 'cwp-search-filters';
        self::$Form_title = esc_html__('Search Form Builder','cubewp-framework');
        self::Builder_Ui();
    }
        
    /**
     * Method create_search_filters
     *
     * @return string html
     * @since  1.0.0
     */
    public function create_search_filters() {
        self::$FORM_TYPE = 'search_filters';
        self::$Wraper_class = 'cwp-search-filters';
        self::$Form_title = esc_html__('Search Filter Builder','cubewp-framework');
        self::Builder_Ui();
    }
    
        
    /**
     * Method default_wp_fields
     *
     * @param string $empty
     * @param string $key
     *
     * @return string html
     * @since  1.0.0
     */
    public function default_search_fields($empty,$key) {
        $wp_default_fields   =  cubewp_search_default_fields('search_fields');
        $args = array(
            'section_title'        =>  esc_html__("WordPress Default Fields", "cubewp-framework"),
            'section_description'  =>  '',
            'section_class'        =>  '',
            'open_close_class'     =>  'open',
            'form_relation'        =>  $key,
            'form_type'            =>  'search_fields',
            'fields'               =>  $wp_default_fields,
        );
        return $this->cwpform_form_section( $args );
    }

    /**
     * Method default_wp_fields
     *
     * @param string $empty
     * @param string $key
     *
     * @return string html
     * @since  1.0.0
     */
    public function default_search_filters($empty,$key) {
        $wp_default_fields   =  cubewp_search_default_fields('search_filters');
        $args = array(
            'section_title'        =>  esc_html__("WordPress Default Fields", "cubewp-framework"),
            'section_description'  =>  '',
            'section_class'        =>  '',
            'open_close_class'     =>  'open',
            'form_relation'        =>  $key,
            'form_type'            =>  'search_filters',
            'fields'               =>  $wp_default_fields,
        );
        return $this->cwpform_form_section( $args );
    }
        
    /**
     * Method taxonomy_fields
     *
     * @param string $empty
     * @param string $key
     *
     * @return string html
     * @since  1.0.0
     */
    public function taxonomy_fields($empty,$key) {
        $taxonomies   = get_object_taxonomies($key, 'objects');
        if(!empty($taxonomies) && count($taxonomies)>0){
            if(isset($taxonomies) && !empty($taxonomies)){
                $taxonomy_fields = array();
                foreach($taxonomies as $taxonomy){
                    $taxonomy_fields[$taxonomy->name] = array(
                        'label'         =>  $taxonomy->label,
                        'name'          =>  $taxonomy->name,
                        'type'          =>  'taxonomy',
                        'display_ui'    =>  'checkbox',
                    );
                }
                $args = array(
                    'section_title'        =>  esc_html__("Taxonomies", "cubewp-framework"),
                    'section_description'  =>  '',
                    'section_class'        =>  '',
                    'open_close_class'     =>  'close',
                    'form_relation'        =>  $key,
                    'form_type'            =>  self::$FORM_TYPE,
                    'fields'               =>  $taxonomy_fields,
                );
                return $this->cwpform_form_section( $args );
            }
        }
    }
        
    /**
     * Method default_ui_section_right
     *
     * @param string $empty
     * @param string $key
     * @param array $data
     * @return string html
     * @since  1.0.0
     */
    public function default_ui_section_right($empty,$key,$data) {
        $form_options  =  CWP()->get_form(self::$FORM_TYPE);
        $args = array(
            'section_title'        =>  esc_html__("Active Fields", "cubewp-framework"),
            'section_description'  =>  '',
            'section_class'        =>  '',
            'open_close_class'     =>  'open',
            'form_relation'        =>  $key,
            'form_type'            =>  self::$FORM_TYPE,
            'fields'               =>  isset($form_options[$key]['fields']) ? $form_options[$key]['fields'] : array(),
        );
        return $this->cwpform_form_section( $args );
    }
        
    /**
     * Method setting_tab
     *
     * @param string $empty
     * @param string $key
     *
     * @return string html
     * @since  1.0.0
     */
    public function setting_tab($empty,$key) {
        $form_options  =  CWP()->get_form(self::$FORM_TYPE);
        $form_fields = isset($form_options[$key]['form']) ? $form_options[$key]['form'] : array();
        return $this->cwpform_form_setting_fields( $form_fields, self::$FORM_TYPE,$key );
    }
    
        
    /**
     * Method Builder_Ui
     *
     * @return string html
     * @since  1.0.0
     */
    public function Builder_Ui() {
        add_filter('cubewp/builder/default/right/section',array($this,'default_ui_section_right'),9,4);
        add_filter('cubewp/builder/right/settings',array($this,'setting_tab'),9,2);
        $types = CWP_all_post_types();
        $args = array(
            'form_type'         => self::$FORM_TYPE,
            'wrapper_class'     => self::$Wraper_class,
            'page_title'        => self::$Form_title,
            'switcher_types'    => $types,
            'switcher_title'    => esc_html__('Select Post Type','cubewp-framework'),
        );
        echo self::CubeWp_Form_Builder($args);
        
    }
    
    public static function init() {
        $CubeClass = __CLASS__;
        new $CubeClass;
    }
    
}