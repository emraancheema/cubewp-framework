<?php
/**
 * CubeWp admin file field 
 *
 * @version 1.0
 * @package cubewp/cube/fields/frontend
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


/**
 * CubeWp_Frontend_File_Field
 */
class CubeWp_Frontend_File_Field extends CubeWp_Frontend {

	public function __construct() {
		add_filter('cubewp/frontend/file/field', array($this, 'render_file_field'), 10, 2);

		add_filter('cubewp/user/registration/file/field', array($this, 'render_file_field'), 10, 2);
		add_filter('cubewp/user/profile/file/field', array($this, 'render_file_field'), 10, 2);
	}
	
	/**
	 * Method render_file_field
	 *
     * @param string $output
     * @param array $args
     *
     * @return string html
     * @since  1.0.0
	 */
	function render_file_field($output = '', $args = array()) {
		$args          = apply_filters('cubewp/frontend/field/parametrs', $args);
		$required      = self::cwp_frontend_field_required($args['required']);
		$required       = !empty($required['class']) ? $required['class'] : '';
		$output        = self::cwp_frontend_post_field_container($args);
		$output        .= self::cwp_frontend_field_label($args);
		if (isset($args["file_types"]) && !empty($args["file_types"])) {
			$accept = 'accept="' . $args["file_types"] . '"';
		}else {
			$accept = 'accept="application/gzip,text/calendar,application/pdf,text/plain,application/zip,application/x-7z-compressed,application/x-zip-compressed,multipart/x-zip,application/x-compressed"';
		}
		$args['extra_attrs'] = !empty($args['extra_attrs']) ? $args['extra_attrs'] : $accept . ' data-error-msg="' . esc_html__("is not acceptable in this field.", "cubewp-framework") . '"';
		if (isset($args["upload_size"]) && !empty($args["upload_size"]) && is_numeric($args["upload_size"])) {
			$args['extra_attrs'] .= ' data-max-upload="' . esc_attr( $args["upload_size"] ) . '"';
		}
		ob_start();
		?>
		<div class="cwp-file-field-container">
			<div class="cwp-file-field">
                <span>
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-upload" viewBox="0 0 16 16">
                        <path d="M.5 9.9a.5.5 0 0 1 .5.5v2.5a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-2.5a.5.5 0 0 1 1 0v2.5a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2v-2.5a.5.5 0 0 1 .5-.5z"/>
                        <path d="M7.646 1.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1-.708.708L8.5 2.707V11.5a.5.5 0 0 1-1 0V2.707L5.354 4.854a.5.5 0 1 1-.708-.708l3-3z"/>
                    </svg>
                </span>
				<p class="cwp-file-field-trigger"><?php esc_html_e("Choose File", "cubewp-framework"); ?></p>
				<?php
				if ( ! isset( $args['value'] ) || empty( $args['value'] ) ) {
					$input_attrs = array(
						'name'  => ! empty($args['custom_name']) ? $args['custom_name'] : $args['name'],
						'value' => '0',
					);
					echo cwp_render_hidden_input($input_attrs);
				}
				$input_attrs = array(
					'type'  => ! empty($args['type']) ? $args['type'] : 'file',
					'id'    => $args['id'],
					'class' => $args['class'].' '.$required,
					'name'  => ! empty($args['custom_name']) ? $args['custom_name'] : $args['name'],
					'value' => '',
					'extra_attrs' => $args['extra_attrs'],
				);
				echo cwp_render_file_input($input_attrs);
				?>
			</div>
			<?php
			$default_file = CWP_PLUGIN_URI . 'cube/assets/frontend/images/default_file.png';
			$args['not_formatted_value'] = $args['value'];
			$args['value'] = cwp_get_attachment_id( $args['value'] );
			if (isset($args['value']) && ! empty($args['value'])) { ?>
				<div class="cwp-file-field-preview" data-default-file="<?php echo esc_url($default_file); ?>" style="display: block;">
					<?php
					$fileurl = $default_file;
					$filename = basename(get_attached_file($args['value']));
					$filetype = pathinfo($filename, PATHINFO_EXTENSION);
					if ($filetype == 'png' || $filetype == 'jpeg' || $filetype == 'gif' || $filetype == 'jpg') {
						$fileurl = wp_get_attachment_url($args['value']);
					}
					?>
					<span>
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-x" viewBox="0 0 16 16">
                            <path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"/>
                        </svg>
                    </span>
					<img src="<?php echo esc_url($fileurl); ?>" alt="<?php echo esc_attr($filename); ?>">
					<p><?php echo basename(get_attached_file($args['value'])); ?></p>
					<?php
					$input_attrs = array(
						'name'  => ! empty($args['custom_name']) ? $args['custom_name'] : $args['name'],
						'class'  => 'cwp-image-preview',
						'value' => $args['value'],
					);
					echo cwp_render_hidden_input($input_attrs);
					?>
				</div>
			<?php } else { ?>
				<div class="cwp-file-field-preview" data-default-file="<?php echo esc_url($default_file); ?>">
                    <span>
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-x" viewBox="0 0 16 16">
                            <path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"/>
                        </svg>
                    </span>
					<img src="" alt="image" />
					<p></p>
				</div>
			<?php } ?>
		</div>
		<?php
		$output .= ob_get_clean();
		$output .= '</div>';

		return apply_filters("cubewp/frontend/{$args['name']}/field", $output, $args);
	}

}

new CubeWp_Frontend_File_Field();