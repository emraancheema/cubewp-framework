<?php
/**
 * CubeWp admin number field 
 *
 * @version 1.0
 * @package cubewp/cube/fields/frontend
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * CubeWp_Frontend_Number_Field
 */
class CubeWp_Frontend_Number_Field extends CubeWp_Frontend {
    
    public function __construct( ) {
        add_filter('cubewp/frontend/number/field', array($this, 'render_number_field'), 10, 2);
        
        add_filter('cubewp/user/registration/number/field', array($this, 'render_number_field'), 10, 2);
        add_filter('cubewp/user/profile/number/field', array($this, 'render_number_field'), 10, 2);
        
        add_filter('cubewp/search_filters/number/field', array($this, 'render_search_filters_number_field'), 10, 2);
        add_filter('cubewp/frontend/search/number/field', array($this, 'render_search_number_field'), 10, 2);
    }
        
    /**
     * Method render_number_field
     *
     * @param string $output
     * @param array $args
     *
     * @return string html
     * @since  1.0.0
     */
    public function render_number_field( $output = '', $args = array() ) {
        $output = apply_filters("cubewp/frontend/text/field", $output, $args);
        return $output;
    }
        
    /**
     * Method render_search_filters_number_field
     *
     * @param string $output
     * @param array $args
     *
     * @return string html
     * @since  1.0.0
     */
    public function render_search_filters_number_field( $output = '', $args = array() ){
        $args = apply_filters( 'cubewp/frontend/field/parametrs', $args );
        $minval = $maxval = '';
        if(isset($_GET['min-'.$args['name']])){
            $minval = sanitize_text_field($_GET['min-'.$args['name']]);
        }
        if(isset($_GET['max-'.$args['name']])){
            $maxval = sanitize_text_field($_GET['max-'.$args['name']]);
        }
        $output         = self::cwp_frontend_search_field_container($args);
            $output .= self::cwp_frontend_search_field_label($args);
            $output .= '<div class="cwp-range-number-fields">'."\n";
                $output .= '<div class="cwp-range-number-field">'."\n";
                    $output .= '<input type="number" class="form-control '. $args['class'].'" id="min-'. esc_attr($args['name']) .'" placeholder="'.esc_html__( 'Min', 'cubewp-framework' ).'" name="min-'. esc_attr($args['name']) .'" value="'.$minval.'">'."\n";
                $output .= '</div>'."\n";
                $output .= '<span class="cwp-range-number-field-seprator"> - </span>'."\n";						
                $output .= '<div class="cwp-range-number-field">'."\n";
                    $output .= '<input type="number" class="form-control '. $args['class'].'" id="max-'. esc_attr($args['name']) .'" placeholder="'.esc_html__( 'Max', 'cubewp-framework' ).'" name="max-'. esc_attr($args['name']) .'" value="'.$maxval.'">'."\n";
                $output .= '<input type="hidden" name="'. esc_attr($args['name']) .'" value="0">'."\n";
                $output .= '</div>'."\n";						
            $output .= '</div>'."\n";
        $output .= '</div>';
        
        $output = apply_filters("cubewp/search_filters/{$args['name']}/field", $output, $args);
        
        return $output;
    }
        
    /**
     * Method render_search_number_field
     *
     * @param string $output
     * @param array $args
     *
     * @return string html
     * @since  1.0.0
     */
    public function render_search_number_field( $output = '', $args = array() ){
        $args = apply_filters( 'cubewp/frontend/field/parametrs', $args );

        $label = '';
        if(isset($args['label']) && !empty($args['label'])){
            $label = $args['label'];
        }
        
        $output = self::cwp_frontend_search_field_container($args);
            $output .= self::cwp_frontend_search_field_label($args);
            $output .= '<div class="cwp-range-number-fields">';
                $output .= '<div class="cwp-range-number-field">';
                    $output .= '<input type="number" class="form-control '. $args['class'].'" id="min-'. esc_attr($args['name']) .'" placeholder="'.esc_html__( 'Min ', 'cubewp-framework' ).$label.'" name="min-'. esc_attr($args['name']) .'">';
                $output .= '</div>';
                $output .= '<span class="cwp-range-number-field-seprator"> - </span>'."\n";					
                $output .= '<div class="cwp-range-number-field">';
                    $output .= '<input type="number" class="form-control '. $args['class'].'" id="max-'. esc_attr($args['name']) .'"placeholder="'.esc_html__( 'Max ', 'cubewp-framework' ).$label.'"  name="max-'. esc_attr($args['name']) .'">';
                $output .= '</div>';						
            $output .= '</div>';
        $output .= '</div>';
        
        $output = apply_filters("cubewp/frontend/search/{$args['name']}/field", $output, $args);
        
        return $output;
    }
    
}
new CubeWp_Frontend_Number_Field();