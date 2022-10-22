<?php
/**
 * CubeWp admin gallary field 
 *
 * @version 1.0
 * @package cubewp/cube/fields/admin
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * CubeWp_Admin_Gallery_Field
 */
class CubeWp_Admin_Gallery_Field extends CubeWp_Admin {
    
    public function __construct( ) {
        add_filter('cubewp/admin/post/gallery/field', array($this, 'render_gallery_field'), 10, 2);
    }
        
    /**
     * Method render_gallery_field
     *
     * @param string $output
     * @param array $args
     *
     * @return string html
     * @since  1.0.0
     */
    public function render_gallery_field( $output = '', $args = array() ) {

        $args   =  apply_filters( 'cubewp/admin/field/parametrs', $args );

        $output = $this->cwp_field_wrap_start($args);
            
            if(!did_action( 'wp_enqueue_media')) wp_enqueue_media();
        
            $input_name   = !empty($args['custom_name']) ? $args['custom_name'] : 'cwp_meta[' . $args['name'] . ']';
            $attachments  = isset($args['value']) && is_array($args['value']) ? array_filter($args['value']) : $args['value'];

            $attachments_list = '';
            if(isset($attachments) && !empty($attachments)){
                foreach($attachments as $attachment_id){
                    $attachments_list .= '<li class="cwp-gallery-item" data-slug="'. $attachment_id .'">
                        <input type="hidden" name="'. $input_name .'[]" value="'. $attachment_id .'">
                        <div class="thumbnail">
                            <img src="'. wp_get_attachment_url($attachment_id) .'" alt="image">
                        </div>
                        <div class="cwp-gallery-actions">
                            <a class="remove-gallery-item" href="javascript:void(0);"><span class="dashicons dashicons-trash"></span></a>
                        </div>
                    </li>';
                }
            }
            $output .= '<div id="cwp-gallery-'. $args['name'] .'" class="cwp-custom-field cwp-gallery-field" data-id="'. $args['name'] .'">
                <div class="cwp-field">
                    <div class="cwp-gallery">
                        <ul class="cwp-gallery-list">
                            '. $attachments_list .'
                        </ul>
                    </div>
                    <a href="javascript:void(0);" class="button button-primary cwp-gallery-btn">Add Gallery Images</a>
                </div>
            </div>';
        
        $output .= $this->cwp_field_wrap_end($args);

        $output = apply_filters("cubewp/admin/{$args['name']}/field", $output, $args);
        
        return $output;
    }
    
}
new CubeWp_Admin_Gallery_Field();