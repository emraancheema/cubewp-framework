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
  
    
    public static function cwp_show_taxonomy_metaboxes( $taxonomy ) {
        $tax_custom_fields = CWP()->get_custom_fields( 'taxonomy' );
        $term_id  = 0;
        $tax_name = $taxonomy;
        if ( is_object( $taxonomy ) ) {
           $tax_name = $taxonomy->taxonomy;
           $term_id  = $taxonomy->term_id;
        }
        if ( isset( $tax_custom_fields[ $tax_name ] ) && ! empty( $tax_custom_fields[ $tax_name ] ) ) {
           $output = '';
           foreach ( $tax_custom_fields[ $tax_name ] as $field ) {
              $value = isset( $field['default_value'] ) ? $field['default_value'] : '';
              if ( $term_id > 0 ) {
                 $value = get_term_meta( $term_id, $field['slug'], true );
              }
              $field['label']       = $field['name'];
              $field['name'] = $field['slug'];
              $field['value']       = $value;
              $field['id'] = 'cwp_term_meta[' . $field['slug'] . ']';
              $field['custom_name'] = 'cwp_term_meta[' . $field['slug'] . ']';
              $field['wrap']        = true;
              $field['class']       = $field['type'] == 'color' ? 'color-field' : '';
              if ($field['type'] == 'google_address') {
                 $field['custom_name_lat'] = 'cwp_term_meta[' . $field['slug'].'_lat' . ']';
                 $field['custom_name_lng'] = 'cwp_term_meta[' . $field['slug'].'_lng' . ']';
                 $field['lat'] = get_term_meta( $term_id, $field['slug'].'_lat', true );
                 $field['lng'] = get_term_meta( $term_id, $field['slug'].'_lng', true );
              }
              $output .= apply_filters( "cubewp/admin/post/{$field['type']}/field", '', $field );
           }
           echo cubewp_core_data( $output );
        }
     }

   /**
   * Method cubewp_taxonomy_metas
   *
   * @param int $taxonomy
   *
   * @return array
   * @since  1.0.0
   */
   public static function cubewp_taxonomy_metas( $taxonomy = '', $term_id = 0) {
      $tax_custom_fields = CWP()->get_custom_fields( 'taxonomy' );
      $tax_name = $taxonomy;
      if ( isset( $tax_custom_fields[ $tax_name ] ) && ! empty( $tax_custom_fields[ $tax_name ] ) ) {
         $output = '';
         foreach ( $tax_custom_fields[ $tax_name ] as $field ) {
            if ( $term_id > 0 ) {
               $value = get_term_meta( $term_id, $field['slug'], true );
            }
            if ($field['type'] == 'google_address') {
               $value = [];
               $value['address'] = get_term_meta( $term_id, $field['slug'], true );
               $value['lat'] = get_term_meta( $term_id, $field['slug'].'_lat', true );
               $value['lng'] = get_term_meta( $term_id, $field['slug'].'_lng', true );
            }
            $args[$field['slug']] = array(
               'type'                  =>    $field['type'],
               'meta_key'              =>    $field['slug'],
               'meta_value'            =>    $value,
               'label'                 =>    $field['name'],
            );
         }
         return $args;
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
