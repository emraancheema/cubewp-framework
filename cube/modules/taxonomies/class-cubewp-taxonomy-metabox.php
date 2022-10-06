<?php
/**
 * Creates the submenu item for the plugin.
 *
 * @package Custom_Admin_Settings
 * Creates the submenu item for the plugin.
 *
 * Registers a new menu item under 'Tools' and uses the dependency passed into
 * the constructor in order to display the page corresponding to this menu item.
 *
 * @package CubeWp_Taxonomy_Metabox
 */
class CubeWp_Taxonomy_Metabox {
  
    
    public static function cwp_show_taxonomy_metaboxes( $taxonomy ){
        
        $tax_custom_fields = CWP()->get_custom_fields( 'taxonomy' );
        
        $term_id = 0;
        $tax_name = $taxonomy;
        if( is_object($taxonomy) ){
            $tax_name = $taxonomy->taxonomy;
            $term_id  = $taxonomy->term_id;
        }
        if(isset($tax_custom_fields[$tax_name]) && !empty($tax_custom_fields[$tax_name])){
            
            $output = '';
            foreach($tax_custom_fields[$tax_name] as $field){ 
                $value = isset($field['default_value']) ? $field['default_value'] : '';
                if( $term_id > 0 ){
                    $value = get_term_meta( $term_id, $field['slug'], true );
                }
                $field['label']        =  $field['name'];
                $field['value']        =  $value;
                $field['custom_name']  =  'cwp_term_meta[' . $field['slug'] . ']';
                $field['wrap']         =  true;
                $field['class']        =  $field['type'] == 'color' ? 'color-field': '';
                $output .= apply_filters( "cubewp/admin/post/{$field['type']}/field", '', $field );
            }
            echo cubewp_core_data($output);
        }
    }
    
    public static function cwp_save_taxonomy_custom_fields( $term_id = 0 ){
        if(isset($_POST['cwp_term_meta'])) {
            $POST_DATA = CubeWp_Sanitize_Fields_Array($_POST['cwp_term_meta'],'taxonomy');
            foreach($POST_DATA as $key => $val ){
                update_term_meta( $term_id, $key, $val );
            }
        }
    }
    
}
