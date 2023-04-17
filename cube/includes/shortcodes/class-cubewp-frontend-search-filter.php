<?php
/**
 * Post type's search filters Shortcodes.
 *
 * @package cubewp/cube/includes/shortcodes
 * @version 1.0
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * CubeWp_Frontend_Search_Filter
 */
class CubeWp_Frontend_Search_Filter {
    
    private $custom_fields;
    private $wp_default_fields;
    private $taxonomies;
    private static $sorting;
    private static $form_container_class;
    private static $form_class;
    private static $form_id;
        
    
    public function __construct() {
        add_shortcode('cwpFilters', array($this, 'cwp_filter_callback'));
        add_shortcode('cwpFilterFields', array($this, 'cwp_filter_fields'));
        add_shortcode('cwpFilterResultCount', array($this, 'cwp_filter_result_count'));
        add_shortcode('cwpFilterSorting', array($this, 'cwp_filter_sorting'));
        add_shortcode('cwpFilterListSwitcher', array($this, 'cwp_filter_list_switcher'));
        add_shortcode('cwpFilterResults', array($this, 'cwp_filter_results'));
        add_shortcode('cwpFilterMap', array($this, 'cwp_filter_map'));
        add_filter('cubewp/frontend/sorting/filter', array($this, 'cwp_sorting_data'),10,1);
    }
    
    public static function init() {
        $CubeClass = __CLASS__;
        new $CubeClass;
    }
    
    /**
     * Method cwp_filter_callback
     * @param array $params shortcodes parameteres
     * 
     * @return string html
     * @since  1.0.0
     */
    public function cwp_filter_callback( $params = array(), $content = null ) {
        // default parameters
        extract(shortcode_atts(array(
                'type'                  => '',
                'page_num'              => '1',
                'form_container_class'  => '',
                'form_class'            => '',
                'form_id'               => '',
            ), $params)
        );        
        return self::get_shortcode_filters($type,$page_num);
    }
        
    /**
     * Method get_filters_wrap_start
     *
     * @param string $type 
     * @param int $page_num
     *
     * @return string html
     * @since  1.0.0
     */
    private static function get_filters_wrap_start($type='',$page_num=''){
    ?>
        <div class="cwp-search-filters-wrap <?php echo self::$form_container_class; ?>">
        <form name="cwp-search-filters" class="cwp-search-filters <?php echo self::$form_class; ?>" id="<?php echo self::$form_id; ?>" method="post">
        <div class="cwp-reset-search-filters">
        <p><?php esc_html_e('Filters', 'cubewp-framework'); ?></p>
        <a href="javascript:void(0);" class="clear-filters">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-counterclockwise" viewBox="0 0 16 16">
              <path fill-rule="evenodd" d="M8 3a5 5 0 1 1-4.546 2.914.5.5 0 0 0-.908-.417A6 6 0 1 0 8 2v1z"/>
              <path d="M8 4.466V.534a.25.25 0 0 0-.41-.192L5.23 2.308a.25.25 0 0 0 0 .384l2.36 1.966A.25.25 0 0 0 8 4.466z"/>
            </svg>
            <?php esc_html_e('Reset', 'cubewp-framework'); ?>
        </a>
        </div>    
        <div class="cwp-search-filters-fields">
    <?php
       echo self::filter_hidden_fields($type,$page_num);
    }
        
    /**
     * Method get_filters_wrap_end
     *
     * @return string html
     * @since  1.0.0
     */
    private static function get_filters_wrap_end(){
    ?>
        </div>
        </form>
        </div>
    <?php
    }
        
    /**
     * Method filter_hidden_fields
     *
     * @param string $type 
     * @param int $page_num
     *
     * @return string html
     * @since  1.0.0
     */
    private static function filter_hidden_fields($type='',$page_num=''){
        if(empty($type)){
            $type = _get_post_type();
        }
        if(isset($_GET['page_num'])){
            $page_num = sanitize_text_field($_GET['page_num']);
        }else{
            $page_num = '1';
        }
        $output = '<input type="hidden" id="cwp-page-num" name="page_num" value="'.$page_num.'">';
        if(isset($type) && !is_tax()){
            $output .= '<input type="hidden" id="cwp-posttype-name-archive" name="post_type" value="'.$type.'">';
        }
        if(!is_archive()){
            $output .= '<input type="hidden" name="page" value="page">';
        }
        
        
        return $output;
    }
        
    /**
     * Method get_filters_taxonomy
     *
     * @param string $field_name 
     * @param array $search_filter
     *
     * @return array
     * @since  1.0.0
     */
    private static function get_filters_taxonomy( $search_filter = array(), $field_name ='' ){
        if( $search_filter['type'] == 'taxonomy' ){
            $search_filter['value']      = isset($_GET[$field_name]) ? sanitize_text_field($_GET[$field_name]) : '';
            $search_filter['appearance'] = isset($search_filter['display_ui']) ? $search_filter['display_ui'] : '';
            echo apply_filters("cubewp/search_filters/taxonomy/field", '', $search_filter);
        }
    }
        
     /**
     * Method get_filters_fields
     *
     * @param string $field_name 
     * @param array $search_filter
     *
     * @return array
     * @since  1.0.0
     */
    private static function get_filters_fields( $search_filter = array(), $field_name =''){
        
        if( $search_filter['type'] != 'taxonomy' ){
            $fieldOptions            =   get_field_options($field_name);
            $fieldOptions['label']   =   isset($search_filter['label']) ? $search_filter['label'] : $fieldOptions['label'];
            $fieldOptions['name']    =   isset($search_filter['name']) ? $search_filter['name'] : $fieldOptions['name'];
            $fieldOptions['type']    =   isset($search_filter['display_ui']) ? $search_filter['display_ui'] : $fieldOptions['type'];
            $fieldOptions['container_class']    =   isset($search_filter['container_class']) ? $search_filter['container_class'] : $fieldOptions['container_class'];
            $fieldOptions['class']    =   isset($search_filter['class']) ? $search_filter['class'] : $fieldOptions['class'];
            $placeholder   =   isset($search_filter['placeholder']) && !empty($search_filter['placeholder']) ? $search_filter['placeholder'] : '';
            $fieldOptions['placeholder']   =   empty($placeholder) && isset($fieldOptions['placeholder']) ? $fieldOptions['placeholder'] : $placeholder;
            
            $field_type  =   isset($search_filter['display_ui']) ? $search_filter['display_ui'] : '';

            if($fieldOptions['type'] == 'google_address' ){
                $fieldOptions['custom_name_lat'] =   $fieldOptions['name'].'_lat';
                $fieldOptions['custom_name_lng'] =   $fieldOptions['name'].'_lng';
                $fieldOptions['custom_name_range'] =   $fieldOptions['name'].'_range';
                if(isset($_GET[$fieldOptions['name'].'_lat']) && !empty($_GET[$fieldOptions['name'].'_lat'])){
                    $fieldOptions['lat'] = sanitize_text_field($_GET[$fieldOptions['name'].'_lat']);
                }
                if(isset($_GET[$fieldOptions['name'].'_lng']) && !empty($_GET[$fieldOptions['name'].'_lng'])){
                    $fieldOptions['lng'] = sanitize_text_field($_GET[$fieldOptions['name'].'_lng']);
                }
                if(isset($_GET[$fieldOptions['name'].'_range']) && !empty($_GET[$fieldOptions['name'].'_range'])){
                    $fieldOptions['range'] = sanitize_text_field($_GET[$fieldOptions['name'].'_range']);
                }
            }

            if(isset($_GET[$fieldOptions['name']]) && !empty($_GET[$fieldOptions['name']])){
                $fieldOptions['value'] = sanitize_text_field($_GET[$fieldOptions['name']]);                
            }
            echo apply_filters("cubewp/search_filters/{$field_type}/field", '', $fieldOptions);
        }
    }
        
    /**
     * Method get_filters_content
     *
     * @param string $field_name 
     * @param array $search_filter
     *
     * @return array
     * @since  1.0.0
     */
    private static function get_filters_content( $search_filter = array(), $field_name =''){
        if( $search_filter['type'] == 'taxonomy' ){
            self::get_filters_taxonomy($search_filter,$field_name);
        }
        self::get_filters_fields($search_filter,$field_name);
    }
    
    /**
     * Method get_hidden_field_if_tax
     *
     * @return void
     */
    private static function get_hidden_field_if_tax(){
        if(is_tax() && !is_search()){
            if(!is_page()){
                $queried_object = get_queried_object();
                if (is_object($queried_object) && !empty($queried_object) && !is_wp_error($queried_object)) {
                    $slug = $queried_object->slug;
                    $taxonomy = $queried_object->taxonomy;
                    echo '<input class="is_tax" data-current-tax="'.esc_attr($slug).'" type="hidden" name="' .esc_attr($taxonomy). '" value="' . esc_attr($slug) . '">';

                }
            }
        }
    }

    /**
     * Method get_filters_style_scripts
     *
     * @return void
     * @since  1.0.0
     */
    private static function get_filters_style_scripts(){
        
        CubeWp_Enqueue::enqueue_script( 'cwp-search-filters' );
        CubeWp_Enqueue::enqueue_script( 'select2' );
        CubeWp_Enqueue::enqueue_style( 'select2' );
        CubeWp_Enqueue::enqueue_style( 'archive-cpt-styles' );
        CubeWp_Enqueue::enqueue_script( 'jquery-ui-datepicker' );
        CubeWp_Enqueue::enqueue_style( 'frontend-fields' );

        // Archive map script and style.
        CubeWp_Enqueue::enqueue_style( 'cwp-map-cluster' );
        CubeWp_Enqueue::enqueue_style( 'cwp-leaflet-css' );
        CubeWp_Enqueue::enqueue_script( 'cubewp-map' );
        CubeWp_Enqueue::enqueue_script( 'cubewp-leaflet' );
        CubeWp_Enqueue::enqueue_script( 'cubewp-leaflet-cluster' );
        CubeWp_Enqueue::enqueue_script( 'cubewp-leaflet-fullscreen' );
        CubeWp_Enqueue::enqueue_script('cwp-frontend-fields');
    }
        
    /**
     * Method get_filters this function uses to get filters directly without shortcode
     *
     * @param string $type
     * @param int $page_num
     *
     * @return string html
     * @since  1.0.0
     */
    public static function get_filters($type='',$page_num=''){
        
         /* Calling all css and JS files for filters */
         self::get_filters_style_scripts();

        $sorting_fields = array();
        $post_type =   !empty($type) ? $type : _get_post_type();
        $cwp_search_filters = CWP()->get_form('search_filters');
        
        self::$form_container_class = isset($cwp_search_filters[$post_type]['form']['form_container_class']) ? $cwp_search_filters[$post_type]['form']['form_container_class'] : '';
        self::$form_class = isset($cwp_search_filters[$post_type]['form']['form_class']) ? $cwp_search_filters[$post_type]['form']['form_class'] : '';
        self::$form_id = isset($cwp_search_filters[$post_type]['form']['form_id']) ? $cwp_search_filters[$post_type]['form']['form_id'] : '';
        global $cwpOptions;
        $archive_filters = isset($cwpOptions['archive_filters']) ? $cwpOptions['archive_filters'] : '';
        $show_filters = true;
        if (is_archive() && ! $archive_filters) {
            $show_filters = false;
        }

        self::get_filters_wrap_start($post_type,$page_num);
        self::get_hidden_field_if_tax();
        
        if(!empty($cwp_search_filters[$post_type]['fields']) && count($cwp_search_filters[$post_type]['fields'])>0 && $show_filters){
            if(isset($cwp_search_filters[$post_type]['fields']) && !empty($cwp_search_filters[$post_type]['fields'])){
                foreach ($cwp_search_filters[$post_type]['fields'] as $field_name => $search_filter) {
                    if(($search_filter['type'] == 'number' || $search_filter['type'] == 'date_picker') && isset($search_filter['sorting']) && $search_filter['sorting'] == 1){
                        $sorting_fields[$search_filter['label']] = $search_filter['name'];
                    }
                    self::get_filters_content($search_filter,$field_name);
                }
            }

            self::$sorting = $sorting_fields;
        }
        self::get_filters_wrap_end();
        
    }
        
    /**
     * Method get_shortcode_filters this function uses to get filter by shortcode
     *
     * @param string $type
     * @param int $page_num
     *
     * @return string html
     * @since  1.0.0
     */
    public static function get_shortcode_filters($type='',$page_num=''){
        global $cwpOptions;
              /* Calling all css and JS files for filters */
             self::get_filters_style_scripts();
        
        $archive_map = isset($cwpOptions['archive_map']) ? $cwpOptions['archive_map'] : 1;
        $archive_filters = isset($cwpOptions['archive_filters']) ? $cwpOptions['archive_filters'] : 1;
        $archive_sort_filter = isset($cwpOptions['archive_sort_filter']) ? $cwpOptions['archive_sort_filter'] : 1;
        $archive_layout = isset($cwpOptions['archive_layout']) ? $cwpOptions['archive_layout'] : 1;
        $archive_found_text = isset($cwpOptions['archive_found_text']) ? $cwpOptions['archive_found_text'] : 1;
        
        $filter_area_cols = 'cwp-col-md-2';
        if ( ! $archive_filters) {
           $filter_area_cols = 'cwp-hide';
        }
        $content_area_cols = 'cwp-col-md-7';
        if ( ! $archive_filters && $archive_map) {
           $content_area_cols = 'cwp-col-md-9';
        }else if ( ! $archive_map && $archive_filters) {
           $content_area_cols = 'cwp-col-md-10';
        }else if ( ! $archive_map && ! $archive_filters) {
           $content_area_cols = 'cwp-col-md-12';
        }
        $type =   !empty($type) ? $type : _get_post_type();
        ob_start();
        ?>
        <div class="cwp-container cwp-archive-container">
            <div class="cwp-row">
                <div class="<?php esc_attr_e($filter_area_cols); ?> cwp-archive-sidebar-filters-container">
                    <?php echo do_shortcode('[cwpFilterFields type='.$type.']') ?>
                </div>
                <div class="<?php esc_attr_e($content_area_cols); ?> cwp-archive-content-container">
                    <div class="cwp-archive-content-listing">
                        <div class="cwp-breadcrumb-results">
                        <?php if ($archive_sort_filter || $archive_layout || $archive_found_text) { ?>
                                <div class="cwp-filtered-results">
                                    <?php if ($archive_found_text) {echo do_shortcode('[cwpFilterResultCount]'); } ?>
                                    <?php if ($archive_sort_filter) {echo do_shortcode('[cwpFilterSorting]'); } ?>
                                    <?php if ($archive_layout) {echo do_shortcode('[cwpFilterListSwitcher]'); } ?>
                                </div>
                        <?php } ?>
                        </div>
                        <?php echo do_shortcode('[cwpFilterResults]') ?>
                    </div>
                </div>
            <?php if ($archive_map) { ?>
                <div class="cwp-col-md-3 cwp-archive-content-map">
                    <?php echo do_shortcode('[cwpFilterMap]') ?>
                </div>
        <?php } ?>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }
        
    /**
     * Method cwp_filter_fields
     *
     * @param string $content [explicite description]
     * @param array $params [explicite description]
     *
     * @return string html
     * @since  1.0.0
     */
    public static function cwp_filter_fields( $params = array(), $content = null ){
        // default parameters
        extract(shortcode_atts(array(
                'type'                  => '',
                'page_num'              => '1',
            ), $params)
        );
        ob_start();
        ?>
            <div class="cwp-archive-sidebar-filters-container">
                <?php self::get_filters($type,$page_num); ?>
            </div>
        <?php
        return ob_get_clean();
    }
        
    /**
     * Method cwp_filter_result_count
     *
     * @return string html
     * @since  1.0.0
     */
    public static function cwp_filter_result_count(){
        ob_start();
        ?>
            <div class="cwp-filtered-result-count">
                <?php CubeWp_frontend::results_data(); ?>
            </div>
        <?php
        return ob_get_clean();
    }
        
    /**
     * Method cwp_filter_sorting
     *
      * @return string html
     * @since  1.0.0
     */
    public static function cwp_filter_sorting(){
        ob_start();
        ?>
            <div class="cwp-filtered-sorting">
                <?php CubeWp_frontend::sorting_filter(); ?>
            </div>
        <?php
        return ob_get_clean();
    }
        
    /**
     * Method cwp_filter_list_switcher
     *
     * @return string html
     * @since  1.0.0
     */
    public static function cwp_filter_list_switcher(){
        ob_start();
        ?>
            <div class="cwp-filtered-list-switcher">
                <?php CubeWp_frontend::list_switcher(); ?>
            </div>
        <?php
        return ob_get_clean();
    }    
    /**
     * Method cwp_filter_results
     *
     * @return string html
     * @since  1.0.0
     */
    public static function cwp_filter_results(){
        ob_start();
        ?>
            <div class="cwp-archive-container">
                <div class="cwp-search-result-output"></div>
            </div>
        <?php
        return ob_get_clean();
    }
        
    /**
     * Method cwp_filter_map
     *
     * @return string html
     * @since  1.0.0
     */
    public static function cwp_filter_map(){
        ob_start();
        ?>
            <div class="cwp-archive-content-map"></div>
        <?php
        return ob_get_clean();
    }
        
    /**
     * Method cwp_sorting_data
     *
     * @param string $data [explicite description]
     *
     * @return string html
     * @since  1.0.0
     */
    public static function cwp_sorting_data($data=''){
        return self::$sorting;
    }
    
}