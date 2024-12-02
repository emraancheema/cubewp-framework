<?php
/**
 * Post type's search fields Shortcodes.
 *
 * @package cubewp/cube/includes/shortcodes
 * @version 1.0
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * CubeWp_Frontend_Search_Fields
 */
class CubeWp_Frontend_Search_Fields {
    
    public $type;
    public $form_container_class;
    public $form_class;
    public $custom_fields;
    public $form_id;
	public $search_fields;
    
    public function __construct() {
        add_shortcode('cwpSearch', array($this, 'cwp_search'));
        add_filter('cubewp/frontend/search/button/field', array($this, 'render_search_button'), 10, 2);
    }

    /**
     * Method cwp_search
     * @param array $params shortcodes parameteres
     * 
     * @return string html
     * @since  1.0.0
     */
    public function cwp_search( $params = array(), $content = null ) {
        // default parameters
        extract(shortcode_atts(array(
                'type'                  => 'post',
                'form_container_class'  => '',
                'form_class'            => '',
                'form_id'               => '',
            ), $params)
        );

        $this->custom_fields      =  CWP()->get_custom_fields( 'post_types' );
        $cwp_search_fields        =   CWP()->get_form('search_fields');
        $this->search_fields      =  isset($cwp_search_fields[$type]['fields']) ? $cwp_search_fields[$type]['fields'] : array();
        
        if(empty($this->search_fields)){
            return cwp_alert_ui('Sorry! Search form is empty.');
        }

        $this->form_container_class     =  isset($cwp_search_fields[$type]['form']['form_container_class']) ? $cwp_search_fields[$type]['form']['form_container_class']   : '';
        $this->form_class               =  isset($cwp_search_fields[$type]['form']['form_class'])           ? 'cwp-search-form '.$cwp_search_fields[$type]['form']['form_class'] : 'cwp-search-form';
        $this->form_id                  =  isset($cwp_search_fields[$type]['form']['form_id'])              ? $cwp_search_fields[$type]['form']['form_id']                : 'cwp-search-'.$type;

        $this->type = $type;

        wp_enqueue_style( 'frontend-fields' );
        wp_enqueue_script( 'cwp-search' );
        wp_enqueue_script('cwp-frontend-fields');

        return $this->cwp_search_form($params);
    }
        
    /**
     * Method cwp_search_form
     *
     * @param array $params [explicite description]
     *
     * @return string html
     * @since  1.0.0
     */
    public function cwp_search_form( $params = array() ) {
        
        $output = '<div class="cwp-frontend-search-form '. esc_attr($this->form_container_class) .'">
            <form method="GET" id="'. esc_attr($this->form_id) .'" class="'. esc_attr($this->form_class) .'" action="'.esc_url(home_url('/')).'" class="cwp-search-form">
                <input type="hidden" name="post_type" value="'. esc_attr($this->type) .'">
                <input type="hidden" name="s" value="">';
        
                $output .= $this->cwp_search_form_fields();
   
                $output .= '</form>
        </div>';
        
        $output = apply_filters('cubewp/frontend/search/form', $output, $params, $this->search_fields);
        
        return $output;
    }
        
    /**
     * Method cwp_search_form_fields
     *
     * @return string html
     * @since  1.0.0
     */
    public function cwp_search_form_fields( ) {

        $only_fields = [];
        $output ='<div class="search-form-fields">';
            foreach($this->search_fields as $name){
                $fieldOptions = $name;
                if(isset($label) && $label != ''){
                    $fieldOptions['label'] = $label;
                }
                if($fieldOptions['type'] == 'google_address' ){
                    $fieldOptions['custom_name_lat'] =   $fieldOptions['name'].'_lat';
                    $fieldOptions['custom_name_lng'] =   $fieldOptions['name'].'_lng';
                    $fieldOptions['custom_name_range'] =   $fieldOptions['name'].'_range';
                }
                if($fieldOptions['type'] == 'taxonomy'){
                    $fieldOptions['appearance'] = $fieldOptions['display_ui'];
                }
                $fieldOptions['form_type'] = 'search';

                if( isset($this->custom_fields[$name['name']]) && !empty($this->custom_fields[$name['name']]) ){
                    $fieldOptions = wp_parse_args($fieldOptions, $this->custom_fields[$name['name']]);
                }
                $only_fields[]= $fieldOptions;
                $output .=  apply_filters("cubewp/frontend/search/{$fieldOptions['type']}/field", '', $fieldOptions);
            }
            
            if(wp_is_serving_rest_request()){
                return $only_fields;
            }

        $output .= '</div>';
        
        return $output;
    }
        
    /**
     * Method render_search_button
     *
     * @param string $output 
     * @param array $args
     *
     * @return string html
     * @since  1.0.0
     */
    public function render_search_button($output = '', $args = array()) {

        $args    =  apply_filters( 'cubewp/frontend/field/parametrs', $args );
        
        $output  = CubeWp_Frontend::cwp_frontend_post_field_container($args);
        
        $output .= '<button type="submit" class="cwp-submit-search '.$args['class'].'">'.$args['label'].'</button>';
        
        $output .= '</div>';

        $output = apply_filters("cubewp/frontend/search/{$args['name']}/field", $output, $args);

        return $output;
    }
    
    public static function init() {
        $CubeClass = __CLASS__;
        new $CubeClass;
    }
}