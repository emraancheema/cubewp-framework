<?php
/**
 * CubeWp admin gallary field 
 *
 * @version 1.0
 * @package cubewp/cube/fields/frontend
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * CubeWp_Frontend_Gallery_Field
 */
class CubeWp_Frontend_Gallery_Field extends CubeWp_Frontend {

	public function __construct() {
		add_filter('cubewp/frontend/gallery/field', array($this, 'render_gallery_field'), 10, 2);

		add_filter('cubewp/user/registration/gallery/field', array($this, 'render_gallery_field'), 10, 2);
		add_filter('cubewp/user/profile/gallery/field', array($this, 'render_gallery_field'), 10, 2);
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
	public function render_gallery_field($output = '', $args = array()) {
		$args          = apply_filters('cubewp/frontend/field/parametrs', $args);
		$required      = self::cwp_frontend_field_required($args['required']);
		$required       = !empty($required['class']) ? $required['class'] : '';
		$args['container_attrs'] .= 'data-error-msg="' . esc_html__("is not acceptable in gallery.", "cubewp-framework") . '"';
		$output        = self::cwp_frontend_post_field_container($args);
		$output        .= self::cwp_frontend_field_label($args);
		ob_start();
		?>
		<div class="cwp-gallery-field-container">
			<div class="cwp-gallery-field"
				<?php
				if (isset($args["upload_size"]) && !empty($args["upload_size"]) && is_numeric($args["upload_size"])) {
					echo ' data-max-upload="' . esc_attr( $args["upload_size"] ) . '"';
				}
				if (isset($args["max_upload_files"]) && !empty($args["max_upload_files"]) && is_numeric($args["max_upload_files"])) {
					echo ' data-max-files="' . esc_attr( $args["max_upload_files"] ) . '"';
				}
				?>>
                <span>
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-upload" viewBox="0 0 16 16">
                        <path d="M.5 9.9a.5.5 0 0 1 .5.5v2.5a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-2.5a.5.5 0 0 1 1 0v2.5a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2v-2.5a.5.5 0 0 1 .5-.5z"/>
                        <path d="M7.646 1.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1-.708.708L8.5 2.707V11.5a.5.5 0 0 1-1 0V2.707L5.354 4.854a.5.5 0 1 1-.708-.708l3-3z"/>
                    </svg>
                </span>
				<p class="cwp-gallery-field-trigger"><?php esc_html_e("Choose Images", "cubewp-framework"); ?></p>
				<div class="cwp-gallery-field-inputs">
					<?php
					$rand_id     = rand(123456789, 1111111111);
					$input_attrs = array(
						'name'  => ! empty($args['custom_name']) ? $args['custom_name'] . '[]' : $args['name'],
						'value' => $rand_id,
					);
					echo cwp_render_hidden_input($input_attrs);
					if (isset($args["file_types"]) && !empty($args["file_types"])) {
						$accept = 'accept="' . $args["file_types"] . '"';
					}else {
						$accept = 'accept="image/png,image/jpg,image/jpeg,image/gif"';
					}
					$input_attrs = array(
						'type'        => 'file',
						'id'          => $rand_id,
						'class'       => 'form-control ' . $args['class'].' '.$required,
						'name'        => ! empty($args['custom_name']) ? $args['custom_name'] . '[' . $rand_id . '][]' : $args['name'],
						'value'       => '',
						'extra_attrs' => $accept . ' multiple="multiple"',
					);
					echo cwp_render_file_input($input_attrs);
					?>
				</div>
			</div>
			<div class="cwp-gallery-field-preview">
                <span>
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-x" viewBox="0 0 16 16">
                        <path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"/>
                    </svg>
                </span>
				<img src="" alt="image">
				<p></p>
			</div>
			<?php
			$args['not_formatted_value'] = $args['value'];
			$args['value'] = cwp_handle_data_format( $args );
			if(isset($args['value']) && !empty($args['value']) && is_array($args['value'])){
				foreach($args['value'] as $attachment_id){
					$attachment_id = cwp_get_attachment_id( $attachment_id );
					$filename = basename(get_attached_file($attachment_id));
					$fileurl = wp_get_attachment_url($attachment_id);
					$input_attrs = array(
						'name'         => !empty($args['custom_name']) ? $args['custom_name'].'[]' : $args['name'],
						'value'        => $attachment_id,
					);
					$rand_id     = rand(123456789, 1111111111);
					?>
					<div class="cwp-gallery-field-preview cloned batch-<?php esc_attr_e($rand_id); ?>" data-batch-id="batch-<?php esc_attr_e($rand_id); ?>" style="display: block;">
                        <span>
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-x" viewBox="0 0 16 16">
                                <path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"/>
                            </svg>
                        </span>
						<img src="<?php echo esc_url($fileurl); ?>" alt="image">
						<p><?php echo esc_html($filename); ?></p>
						<?php echo cwp_render_hidden_input( $input_attrs ); ?>
					</div>
					<?php
				}
			}
			?>
		</div>
		<?php
		$output .= ob_get_clean();
		$output .= '</div>';

		return apply_filters("cubewp/frontend/{$args['name']}/field", $output, $args);
	}

}

new CubeWp_Frontend_Gallery_Field();