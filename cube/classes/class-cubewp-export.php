<?php
/**
 * CubeWp Export.
 *
 * @version 1.0
 * @package cubewp/cube/classes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


/**
 * CubeWp_Export
 */
class CubeWp_Export {

    public function __construct(){
        add_action('cubewp_export', array($this, 'manage_export'));
        add_action('wp_ajax_cwp_export_data', array($this, 'cwp_export_data_callback'));
        add_action( 'wp_ajax_cwp_user_data', array($this, 'cwp_user_fields_data_callback') );
        add_action('wp_ajax_cwp_custom_forms', array($this, 'cwp_custom_forms_data_callback') );
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
        
    /**
     * Method manage_export
     *
     * @return string html
	 * @since  1.0.0
     */
    public function manage_export()
    {
        ?>
        <div id="cubewp-export" class="imp-exp">
            <div class="cubewp-page-header">
                <h2><?php esc_html_e('CubeWP Data Import / Export', 'cubewp-framework'); ?></h2>
                <nav class="nav-tab-wrapper wp-clearfix">
                    <a class="nav-tab" href="?page=cubewp-import"><?php esc_html_e('CubeWP Import', 'cubewp-framework'); ?></a>
                    <a class="nav-tab nav-tab-active" href="?page=cubewp-export"><?php esc_html_e('CubeWP Export', 'cubewp-framework'); ?></a>
                </nav>
            </div>
            <?php $this->cwp_export_all(); ?>
        </div>
        <?php
    }
        
    /**
     * Method export_all
     *
     * @return string html
	 * @since  1.0.0
     */
    public function cwp_export_all()
    {
        ?>
        <form class="export-form" method="post" action="">
            <input type="hidden" name="action" value="cwp_export_data">
            <input type="hidden" name="cwp_export_type" value="all">
            <input type="hidden" name="cwp_export_nonce" value="<?php echo wp_create_nonce( 'cwp_export_data_nonce' ); ?>">
            <div class="cubewp-import-box-container">
                <div class="cubewp-import-box">
                    <div class="cubewp-import-card">
                        <div class="cubewp-import-header">
                            <span class="dashicons dashicons-media-document"></span>
                            <h4><?php esc_html_e('Export Data', 'cubewp-framework'); ?></h4>
                        </div>
                        <div class="cubewp-import-content">
                            <p>Please Choose the Content Type and Settings that you would like to Export.</p>
                            <?php self::cwp_export_options(); ?>
                        </div>
                    </div>
                    <?php self::cwp_export_button(); ?>
                </div>
            </div>
        </form>
        <?php
    }

    private function cwp_export_options()
    {
        ?>
        <div class="cubewp-export-options">
            <div class="cubewp-export-option">
                <input type="checkbox" id="post_types" name="cwp_export_content_type[]"
                       value="post_types" checked="checked">
                <label for="post_types"><?php esc_html_e('CubeWP Custom Post Types', 'cubewp-framework'); ?></label>
            </div>
            <div class="cubewp-export-option">
                <input type="checkbox" id="taxonomies" name="cwp_export_content_type[]"
                       value="taxonomies" checked="checked">
                <label for="taxonomies"><?php esc_html_e('CubeWP Custom Taxonomies', 'cubewp-framework'); ?></label>
            </div>
            <div class="cubewp-export-option">
                <input type="checkbox" id="custom-fields" name="cwp_export_content_type[]"
                       value="custom-fields" checked="checked">
                <label
                    for="custom-fields"><?php esc_html_e('CubeWP Post Type Custom Fields', 'cubewp-framework'); ?></label>
            </div>
            <div class="cubewp-export-option">
                <input type="checkbox" id="tax-custom-fields"
                       name="cwp_export_content_type[]" value="tax-custom-fields"
                       checked="checked">
                <label
                    for="tax-custom-fields"><?php esc_html_e('CubeWP Taxonomy Custom Fields', 'cubewp-framework'); ?></label>
            </div>
            <div class="cubewp-export-option">
                <input type="checkbox" id="user-custom-fields" name="cwp_export_content_type[]"
                       value="user-custom-fields" checked="checked">
                <label
                    for="user-custom-fields"><?php esc_html_e('CubeWP User Custom Fields', 'cubewp-framework'); ?></label>
            </div>
            <?php
            if (class_exists('CubeWp_Forms_Custom')) {
                ?>            
                <div class="cubewp-export-option">
                    <input type="checkbox" id="custom-forms-fields" name="cwp_export_content_type[]"
                        value="custom-forms-fields" checked="checked">
                    <label
                        for="custom-forms-fields"><?php esc_html_e('CubeWP Forms', 'cubewp-framework'); ?></label>
                </div>
                <?php
            }
            ?>
            <div class="cubewp-export-option">
                <input type="checkbox" id="search-forms" name="cwp_export_content_type[]"
                       value="search-forms" checked="checked">
                <label for="search-forms"><?php esc_html_e('CubeWP Search Forms', 'cubewp-framework'); ?></label>
            </div>
            <div class="cubewp-export-option">
                <input type="checkbox" id="filter-forms" name="cwp_export_content_type[]"
                       value="filter-forms" checked="checked">
                <label for="filter-forms"><?php esc_html_e('CubeWP Filter Forms', 'cubewp-framework'); ?></label>
            </div>
            <?php
            if (class_exists('CubeWp_Frontend_Load')) {
                ?>
                <div class="cubewp-export-option">
                    <input type="checkbox" id="post-type-forms" name="cwp_export_content_type[]"
                           value="post-type-forms" checked="checked">
                    <label
                        for="post-type-forms"><?php esc_html_e('CubeWP Post Types Forms', 'cubewp-framework'); ?></label>
                </div>
                <div class="cubewp-export-option">
                    <input type="checkbox" id="user-reg-forms" name="cwp_export_content_type[]"
                           value="user-reg-forms" checked="checked">
                    <label
                        for="user-reg-forms"><?php esc_html_e('CubeWP User Registration Forms', 'cubewp-framework'); ?></label>
                </div>
                <div class="cubewp-export-option">
                    <input type="checkbox" id="user-profile-forms" name="cwp_export_content_type[]"
                           value="user-profile-forms" checked="checked">
                    <label
                        for="user-profile-forms"><?php esc_html_e('CubeWP User Profile Forms', 'cubewp-framework'); ?></label>
                </div>
                <div class="cubewp-export-option">
                    <input type="checkbox" id="single_layout" name="cwp_export_content_type[]"
                           value="single_layout" checked="checked">
                    <label
                        for="single_layout"><?php esc_html_e('CubeWP Single Post layout', 'cubewp-framework'); ?></label>
                </div>
                <div class="cubewp-export-option">
                    <input type="checkbox" id="user_dashboard" name="cwp_export_content_type[]"
                           value="user_dashboard" checked="checked">
                    <label
                        for="user_dashboard"><?php esc_html_e('CubeWP User Dashboard', 'cubewp-framework'); ?></label>
                </div>
                <?php
            }
            ?>
            <div class="cubewp-export-option">
                <input type="checkbox" id="cwp_settings" name="cwp_export_content_type[]"
                       value="cwp_settings" checked="checked">
                <label for="cwp_settings"><?php esc_html_e('CubeWP Settings', 'cubewp-framework'); ?></label>
            </div>
        </div>
        <?php
    }

    private function cwp_export_button(){
        ?>
        <p>
            <button type="button" class="button-primary cwp_export"
                name="cwp_export"><?php esc_html_e('Export', 'cubewp-framework'); ?></button>
                <a href="javascrip:void(0);" class="button cwp_download_content hidden"
                    download><?php esc_html_e('Download file', 'cubewp-framework'); ?></a>
        </p>
        <?php
    }
    public function cwp_user_fields_data_callback(){
        if ( !current_user_can('manage_options') ) {
            wp_send_json( array( 'success' => 'false', 'msg' => esc_html__('You do not have permission to perform this action.', 'cubewp-framework') ) );
            wp_die();
        }
        if ( !isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'cubewp-admin-nonce') ) {
            wp_send_json( array( 'success' => 'false', 'msg' => esc_html__('Invalid nonce. You are not authorized to perform this action.', 'cubewp-framework') ) );
            wp_die();
        }
        if(isset($_POST['export']) && $_POST['export'] == 'success'){
            $buffer = self::cwp_custom_fields_posts('cwp_user_fields');
            $files = self::cwp_file_names();
            if (self::cwp_file_force_contents($files['cwp_user_groups'], $buffer)) {
                $download_now = isset( $_POST['download_now'] ) ? sanitize_text_field( $_POST['download_now'] ) : 'true';
                if ( $download_now != 'false' ) {
                    self::cwp_create_zip_file();
                }
                wp_send_json(array(
                    'success'  => 'true',
                    'msg'      => esc_html__('The file you requested is ready for download. Please click the download file button to download it.', 'cubewp-framework'),
					'file_url' => $files['zip_file'],
                ));
            }else {
                wp_send_json(array(
                    'success' => 'false',
                    'msg'     => esc_html__('Something went wrong. Please try again.', 'cubewp-framework')
                ));
            }
        }
    }

    public function cwp_custom_forms_data_callback(){
        if ( !current_user_can('manage_options') ) {
            wp_send_json( array( 'success' => 'false', 'msg' => esc_html__('You do not have permission to perform this action.', 'cubewp-framework') ) );
            wp_die();
        }
        if ( !isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'cubewp-admin-nonce') ) {
            wp_send_json( array( 'success' => 'false', 'msg' => esc_html__('Invalid nonce. You are not authorized to perform this action.', 'cubewp-framework') ) );
            wp_die();
        }
        if(isset($_POST['export']) && $_POST['export'] == 'success'){
            $buffer = self::cwp_custom_fields_posts('cwp_forms');
            $files = self::cwp_file_names();
            if (self::cwp_file_force_contents($files['cwp_custom_forms'], $buffer)) {
                self::cwp_create_zip_file();
                wp_send_json(array(
                    'success'  => 'true',
                    'msg'      => esc_html__('The file you requested is ready for download. Please click the download file button to download it.', 'cubewp-framework'),
					'file_url' => $files['zip_file'],
                ));
            }else {
                wp_send_json(array(
                    'success' => 'false',
                    'msg'     => esc_html__('Something went wrong. Please try again.', 'cubewp-framework')
                ));
            }
        }
    }
    /**
     * Method cwp_export_data_callback
     *
     * @return Json data to ajax
	 * @since  1.0.0
     */
    public function cwp_export_data_callback() {

        if ( !current_user_can('manage_options') ) {
            wp_send_json( array( 'success' => 'false', 'msg' => esc_html__('You do not have permission to perform this action.', 'cubewp-framework') ) );
            wp_die();
        }
        if ( !isset($_POST['cwp_export_nonce']) || !wp_verify_nonce($_POST['cwp_export_nonce'], 'cwp_export_data_nonce') ) {
            wp_send_json( array( 'success' => 'false', 'msg' => esc_html__('Invalid nonce. You are not authorized to perform this action.', 'cubewp-framework') ) );
            wp_die();
        }

		if (isset($_POST['cwp_export_type']) && $_POST['cwp_export_type'] == 'all') {
			if (empty($_POST['cwp_export_content_type'])) {
				wp_send_json(array(
					'success' => 'false',
					'msg'     => esc_html__('Select at least one content type to proceed with data export.', 'cubewp-framework')
				));
			} else {
				$export_content = array();
				foreach ($_POST['cwp_export_content_type'] as $content_type) {
					switch ($content_type) {
						case 'post_types':
							$export_content['post_types'] = CWP_types();
							break;
						case 'taxonomies':
							$export_content['taxonomies'] = get_option('cwp_custom_taxonomies');
							$export_content['terms'] = cwp_all_terms();
						case 'custom-fields':
							$export_content['custom_fields'] = CWP()->get_custom_fields( 'post_types' );
							break;
						case 'tax-custom-fields':
							$export_content['tax_custom_fields'] = CWP()->get_custom_fields( 'taxonomy' );
							break;
                        case 'user-custom-fields':
							$export_content['user_custom_fields'] = CWP()->get_custom_fields( 'user' );
							break;   
                        case 'custom-forms-fields':
                            $export_content['custom_forms_fields'] = CWP()->get_custom_fields( 'custom_forms' );
                            break;
                        case 'post-type-forms':
							$export_content['post_type_forms'] = CWP()->get_form('post_type');
                            $export_content['loop_builder_forms'] = CWP()->get_form( 'loop_builder' );
							break;
						case 'search-forms':
							$export_content['search_forms'] = CWP()->get_form('search_fields');
							break;
                        case 'filter-forms':
							$export_content['filter_forms'] = CWP()->get_form('search_filters');
							break;
                        case 'user-reg-forms':
							$export_content['user_reg_forms'] = CWP()->get_form('user_register');
							break;
                        case 'user-profile-forms':
							$export_content['user_profile_forms'] = CWP()->get_form('user_profile');
							break;
                        case 'single_layout':
							$export_content['single_layout'] = CWP()->get_form('single_layout');
							break;
                        case 'user_dashboard':
                            $export_content['user_dashboard'] = CWP()->cubewp_options('cwp_userdash');
                            break;
                        case 'cwp_settings':
                            $export_content['cwp_settings'] = CWP()->cubewp_options('cwpOptions');
                            break;
					}
				}
                $buffer = self::cwp_custom_fields_posts('cwp_form_fields');
				$files = self::cwp_file_names();
				if (isset($export_content) && ! empty($export_content) && self::cwp_file_force_contents($files['setup_file'], json_encode($export_content)) && self::cwp_file_force_contents($files['cwp_post_groups'], $buffer)) {
					wp_send_json(array(
						'success'  => 'true'
					));
				} else {
					wp_send_json(array(
						'success' => 'false',
						'msg'     => esc_html__('Something went wrong. Please try again.', 'cubewp-framework')
					));
				}
			}
		}
	}
    
    private function cwp_custom_fields_posts($post_type=''){
        ob_start();
        require_once './includes/export.php';
        export_wp(array('content' => $post_type));
        if (version_compare(PHP_VERSION, '5.3.0', '>=')) {
            header_remove('Content-Description');
            header_remove('Content-Disposition');
        } else {
            header("Content-Description: ");
            header("Content-Disposition: ");
        }
        $buffer = ob_get_contents();
        ob_end_clean();
        return $buffer;
    }

    private function cwp_file_names(){
        $names = array();
        $file_name  = str_replace(array('-', ' ', ':'), array(
            '_',
            '_',
            '_'
        ), 'demo_data_' . current_time('M-d-Y H:s A'));
        $upload_dir = wp_upload_dir();
        $upload_url = $upload_dir['url'];
		if (strpos($upload_url, 'http://') === 0 && is_ssl()) {
			$upload_url = 'https://' . substr($upload_url, 7);
		}
        $names['setup_file']  = $upload_dir['path'] . '/cubewp/export/cwp-setup.json';
        $names['cwp_post_groups']  = $upload_dir['path'] . '/cubewp/export/cwp_post_groups.json';
        $names['cwp_user_groups']  = $upload_dir['path'] . '/cubewp/export/cwp_user_groups.json';
        $names['zip_file']   = $upload_url . '/cubewp/export/' . $file_name . '.zip';
        $names['cwp_custom_forms'] = $upload_dir['path'] . '/cubewp/export/cwp_custom_forms.json';
        $names['file_name'] = $file_name;
        return $names;
    }

    private function cwp_create_zip_file($final = false){

        $files = self::cwp_file_names();
        $zip = new ZipArchive();

        $DelFilePath = $files['file_name'].".zip";
        $upload_dir = wp_upload_dir();
        if(file_exists($upload_dir['path'] . '/cubewp/export/'.$DelFilePath)) {

            unlink ($upload_dir['path'] . '/cubewp/export/'.$DelFilePath); 

        }
        if ($zip->open($upload_dir['path'] . '/cubewp/export/'.$DelFilePath, ZIPARCHIVE::CREATE) != TRUE) {
                die ("Could not open archive");
        }
            $zip->addFile($files['setup_file'],'cwp-setup.json');
            $zip->addFile($files['cwp_post_groups'],'cwp_post_groups.json');
            $zip->addFile($files['cwp_user_groups'],'cwp_user_groups.json');
            $zip->addFile($files['cwp_custom_forms'],'cwp_custom_forms.json');
        // close and save archive

        $zip->close();
        unlink ($files['setup_file']);
        unlink ($files['cwp_post_groups']);
        unlink ($files['cwp_user_groups']);
        unlink ($files['cwp_custom_forms']);

    }
    /**
     * Method cwp_file_force_contents
     *
     * @param string $file_path
     * @param Json $file_content
     * @param bolean $flags
     * @param int $permissions
     *
     * @return Json
	 * @since  1.0.0
     */
    private static function cwp_file_force_contents($file_path, $file_content, $flags = 0, $permissions = 0777) {
		$parts = explode('/', $file_path);
		array_pop($parts);
		$dir = implode('/', $parts);

		if ( ! is_dir($dir)) {
			mkdir($dir, $permissions, true);
		}

		return file_put_contents($file_path, $file_content, $flags);
	}

}