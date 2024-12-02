<?php
/**
 * CubeWp Import to import only cubewp related data.
 *
 * @version 1.0
 * @package cubewp/cube/classes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

session_start();
/**
 * CubeWp_Import
 */
class CubeWp_Import {
    public static $terms = array();
    public function __construct(){
        add_action('cubewp_import', array($this, 'manage_import'));
        add_action('wp_ajax_cwp_import_data', array($this, 'cwp_import_data_callback'));
        add_action('wp_ajax_cwp_import_dummy_data', array($this, 'cwp_import_dummy_data_callback'));
        if(isset($_GET['import']) && $_GET['import'] == 'success') {
            new CubeWp_Admin_Notice("cubewp-import-success", esc_html__('Data Imported Successfully', 'cubewp-framework'), 'success', false);
        }
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
     * Method manage_import
     *
     * @since  1.0.0
     */
    public function manage_import(){
        if(isset($_GET['import']) && $_GET['import'] == 'success' && isset($_SESSION['terms'])){
            $this->cwp_import_terms( cubewp_core_data($_SESSION['terms']) );
            session_destroy();
        }
       ?>
        <div id="cubewp-import" class="imp-exp">
            <div class="cubewp-page-header">
                <h2><?php esc_html_e('CubeWP Data Import / Export', 'cubewp-framework'); ?></h2>
                <nav class="nav-tab-wrapper wp-clearfix">
                    <a class="nav-tab nav-tab-active" href="?page=cubewp-import"><?php esc_html_e('Import', 'cubewp-framework'); ?></a>
                    <a class="nav-tab " href="?page=cubewp-export"><?php esc_html_e('Export', 'cubewp-framework'); ?></a>
                </nav>
            </div>
            <form id="import_form" method="post" action="" enctype="multipart/form-data">
                <input type="hidden" name="action" value="cwp_import_data">
                <input type="hidden" name="cwp_import_nonce" value="<?php echo wp_create_nonce( 'cwp_import_data_nonce' ); ?>">
                <div class="cubewp-import-box-container">
                    <div class="cubewp-import-box">
                        <div class="cubewp-import-card">
                            <div class="cubewp-import-header">
                                <span class="dashicons dashicons-media-document"></span>
                                <h4><?php esc_html_e('Import Data', 'cubewp-framework'); ?></h4>
                            </div>
                            <div class="cubewp-import-content">
                                <p><?php esc_html_e('Upload only zip file containing one or more JSON files exported using CubeWP Export tool.', 'cubewp-framework'); ?></p>
                                <input type="file" name="file" required>
                            </div>
                        </div>
                        <button type="submit" class="button-primary cwp_import" name="cwp_import">
                            <?php esc_html_e('Import', 'cubewp-framework'); ?>
                        </button>
                    </div>
                    <div class="cubewp-import-box">
                        <div class="cubewp-import-card">
                            <div class="cubewp-import-header">
                                <span class="dashicons dashicons-download"></span>
                                <h4><?php esc_html_e('Dummy Data Importer', 'cubewp-framework'); ?></h4>
                            </div>
                            <div class="cubewp-import-content">
                                <div class="cubewp-import-content-dummy-warning">
                                    <span class="dashicons dashicons-warning"></span>
                                    <p><?php esc_html_e('This is a dummy importer tool for testing purposes, including Post Types, Taxonomies, and Custom Fields. No file upload is required.', 'cubewp-framework'); ?></p>
                                </div>
                            </div>
                        </div>
                        <button type="submit" class="button-primary cwp_import_demo" name="cwp_import">
                            <?php esc_html_e('Import', 'cubewp'); ?>
                        </button>
                    </div>
                </div>
            </form>
        </div>
        <?php
    }   
    /**
     * Method rmdir_recursive
     *
     * @param  $dir
     *
     * @return string
     * @since  1.0.0
     */
    public function rmdir_recursive($dir) {
        foreach(scandir($dir) as $file) {
           if ('.' === $file || '..' === $file) continue;
           if (is_dir("$dir/$file")) rmdir_recursive("$dir/$file");
           else unlink("$dir/$file");
       }

       rmdir($dir);
    }    
    /**
     * Method cwp_import_data_callback
     *
     * @return array JSon to ajax
     * @since  1.0.0
     */
    public function cwp_import_data_callback(){
		if ( ! current_user_can( 'manage_options' ) ) {
			// User doesn't have the required capabilities
			wp_send_json( array( 'success' => 'false', 'msg' => esc_html__( "You do not have permission to perform this action.", 'cubewp-framework' ) ) );
		}
        if ( isset( $_FILES["file"]["name"] ) && isset( $_POST['cwp_import_nonce'] ) && wp_verify_nonce( $_POST['cwp_import_nonce'], 'cwp_import_data_nonce' ) ) {
            $import_file = $_FILES;
            $filename = sanitize_file_name($import_file["file"]["name"]);
            $source = $import_file["file"]["tmp_name"];
            $type = sanitize_file_name($import_file["file"]["type"]);

            $name = explode(".", $filename);
            $accepted_types = array('application/zip', 'application/x-zip-compressed', 'multipart/x-zip', 'application/x-compressed');
            foreach($accepted_types as $mime_type) {
                if($mime_type == $type) {
                    $okay = true;
                    break;
                } 
            }

            $continue = strtolower($name[1]) == 'zip' ? true : false;
            if(!$continue) {
                wp_send_json( array( 'success' => 'false', 'msg' => esc_html__("The file you are trying to upload is not a .zip file. Please try again.", 'cubewp-framework')) );
            }

            /* PHP current path */
            $upload_dir = wp_upload_dir();
            $path  = $upload_dir['path'] . '/cubewp/import/';  // absolute path to the directory where zipper.php is in
            if ( ! is_dir($path)) {
                mkdir($path, 0777, true);
            }
            $filenoext = basename ($filename, '.zip');  // absolute path to the directory where zipper.php is in (lowercase)
            $filenoext = basename ($filenoext, '.ZIP');  // absolute path to the directory where zipper.php is in (when uppercase)

            $targetdir = $path . $filenoext; // target directory
            $targetzip = $path . $filename; // target zip file

            /* create directory if not exists', otherwise overwrite */
            /* target directory is same as filename without extension */

            if (is_dir($targetdir))  $this->rmdir_recursive ( $targetdir);

            mkdir($targetdir, 0777);

            /* here it is really happening */

            if(move_uploaded_file($source, $targetzip)) {
                $zip = new ZipArchive();
                $x = $zip->open($targetzip);  // open the zip file to extract
                if ($x === true) {
                    $zip->extractTo($targetdir); // place in the directory with same name  
                    $zip->close();

                    unlink($targetzip);
                }
                $moved = true;
            } else {    
                $moved = false;
                wp_send_json( array( 'success' => 'false', 'msg' => esc_html__("There is something wrong, Maybe your directory permission is an issue.", 'cubewp-framework')) );
            }
            if($moved == true && $targetdir != ''){
                
                $setup_file = $this->cwp_import_files(true);
                if(file_exists($targetdir.$setup_file)){
                    self::cwp_import_cubewp_data($targetdir, $setup_file);
                }

                $content_files = $this->cwp_import_files();
                if(is_array($content_files)){
                    foreach($content_files as $content_file){
                        if(file_exists($targetdir.$content_file)){
                            $message = self::cwp_import_wordpress_content($targetdir, $content_file);
                        }
                    }
                }
                $message = !empty($message) ? $message : esc_html__('Data imported successfull.', 'cubewp-framework');
                $this->rmdir_recursive ( $targetdir);
                wp_send_json( array( 'success' => 'true', 'msg' => $message, 'redirectURL' => admin_url('admin.php?page=cubewp-import&import=success') ) );
            }
            
            wp_die();
        }else {
            wp_send_json( array( 'success' => 'false', 'msg' => esc_html__("Please upload a zip file to import data.", 'cubewp-framework')) );
        }
    }

    /**
     * Method cwp_import_dummy_content
     *
     * @return void
     * @since  1.0.0
     */
    public function cwp_import_dummy_data_callback(){
        if ( !current_user_can('manage_options') ) {
            wp_send_json( array( 'success' => 'false', 'msg' => esc_html__('You do not have permission to perform this action.', 'cubewp-framework') ) );
            wp_die();
        }
        if ( !isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'cubewp-admin-nonce') ) {
            wp_send_json( array( 'success' => 'false', 'msg' => esc_html__('Invalid nonce. You are not authorized to perform this action.', 'cubewp-framework') ) );
            wp_die();
        }
        if(isset($_POST['data_type']) && $_POST['data_type'] == 'dummy'){
            $plugin_targetdir = CWP_PLUGIN_PATH . 'cube/includes/setup/';
            $targetdir = apply_filters( 'cubewp/import/content/path', $plugin_targetdir );
            $content = 'false';
            if(!isset($_POST['content'])){
                $setup_file = $this->cwp_import_files(true);
                if(file_exists($targetdir.$setup_file)){
                    self::cwp_import_cubewp_data($targetdir, $setup_file);
                }

                $content_files = $this->cwp_import_files();
                if(is_array($content_files)){
                    foreach($content_files as $content_file){
                        if(file_exists($targetdir.$content_file)){
                            $message = self::cwp_import_wordpress_content($targetdir, $content_file);
                        }
                    }
                }
                $contents = $this->cwp_import_files(false,true);
                if(file_exists($targetdir.$contents)){
                    $content = 'true';
                }
            }else{
                $contents = $this->cwp_import_files(false,true);
                if(file_exists($targetdir.$contents)){
                    $message = self::cwp_import_wordpress_content($targetdir, $contents);
                }
            }
            do_action('cwp_actions_after_demo_imported');
            $message = !empty($message) ? $message : esc_html__('Dummy data imported successfully.', 'cubewp-framework');
            $redirectURL = apply_filters( 'cubewp/after/import/redirect', admin_url('admin.php?page=cubewp-import&import=success') );
            $success = apply_filters( 'cubewp/after/import/success_message', '' );
            $successMessage = '';
            if(is_array($success) && isset($success['selecter']) && isset($success['message'])){
                $successMessage = $success;
            }
            wp_send_json( array( 'success' => 'true', 'content' => $content, 'success_message' => $successMessage , 'msg' => $message, 'redirectURL' => $redirectURL ) );
            
            wp_die();
        }
    }

    /**
     * Method cwp_import_dummy_content
     *
     * @return void
     * @since  1.0.0
     */
    public function cwp_import_files($setup = false,$content = false){
        if($setup == true){
            return '/cwp-setup.json';
        }else if($content == true){
            return '/content.xml';
        }
        return array(
            '/cwp_user_groups.json',
            '/cwp_post_groups.json',
            '/cwp_custom_forms.json',
        );
    }
   
    /**
     * Method cwp_import_cubewp_data
     *
     * @param $targetdir $targetdir is path of files
     *
     * @return void
     */
    public function cwp_import_cubewp_data($targetdir = '', $file = ''){

        if($targetdir != '' && $file != ''){

            $file = $targetdir . $file;
            $file_content   = file_get_contents( $file );
            $import_content = json_decode($file_content, true);
            
            foreach( $import_content as $content_type => $import_data ){
                switch($content_type){
                    case 'post_types':
                        $this->cwp_import_post_types( $import_data );
                    break;
                    case 'taxonomies':
                        $this->cwp_import_taxonomies( $import_data );
                    break;
                    case 'custom_fields':
                        $this->cwp_import_custom_fields( $import_data );
                    break;
                    case 'tax_custom_fields':
                        $this->cwp_import_taxonomies_custom_fields( $import_data );
                    break;
                    case 'user_custom_fields':
                        $this->cwp_import_user_custom_fields( $import_data );
                    break;
                    case 'terms':
                        $_SESSION['terms'] = $import_data;
                    break;
                    case 'post_type_forms':
                        $this->cwp_import_post_type_forms( $import_data );
                    break;
                    case 'loop_builder_forms':
                        $this->cwp_import_loop_builder_forms( $import_data );
                    break;
                    case 'custom_forms_fields':
                        $this->cwp_import_custom_forms_fields( $import_data );
                    break;
                    case 'search_forms':
                        $this->cwp_import_search_forms( $import_data );
                    break;
                    case 'filter_forms':
                        $this->cwp_import_filter_forms( $import_data );
                    break;
                    case 'user_reg_forms':
                        $this->cwp_import_user_reg_forms( $import_data );
                    break;
                    case 'user_profile_forms':
                        $this->cwp_import_user_profile_forms( $import_data );
                    break;
                    case 'single_layout':
                        $this->cwp_import_single_layout_forms( $import_data );
                    break;
                    case 'user_dashboard':
                        $this->cwp_import_user_dashboard_forms( $import_data );
                    break;
                    case 'cwp_settings':
                        $this->cwp_import_settings( $import_data );
                    break;
                }
            }
            return true;
        }
        return false;
    }

    
    /**
     * Method cwp_import_wordpress_content
     *
     * @param $targetdir $targetdir path of files
     *
     * @return void
     */
    public function cwp_import_wordpress_content($targetdir = '', $file = ''){
        if($targetdir != '' && $file != ''){
            $file = $targetdir . $file;
            if (!defined('WP_LOAD_IMPORTERS')) {
                define('WP_LOAD_IMPORTERS', true);
            }
            require_once ABSPATH . 'wp-admin/includes/import.php';
            $importer_error = false;
            if (!class_exists('WP_Importer')) {
                $class_wp_importer = ABSPATH . 'wp-admin/includes/class-wp-importer.php';
                if (file_exists($class_wp_importer)) {
                    require_once $class_wp_importer;
                } else {
                    $importer_error = true;
                }
            }
            if (!class_exists('WP_Import')) {
                $class_wp_import = CWP_PLUGIN_PATH . 'cube/importer/wordpress-importer.php';
                if (file_exists($class_wp_import)) {
                    require_once $class_wp_import;
                } else {
                    $importer_error = true;
                }
            }
            if ($importer_error) {
                return "Error on import";
            } else {
                if (!is_file($file)) {
                    return "The XML file containing the content is not available or could not be read .. You might want to try to set the file permission to chmod 755.<br/>If this doesn't work please contact to community or email us for more help.";
                } else {
                    ob_start();
                    $wp_import = new WP_Import();
                    $wp_import->fetch_attachments = true;
                    $wp_import->import( $file );
                    ob_end_clean();
                }
            }
        }
    }
    
        
    /**
     * Method cwp_import_post_types
     *
     * @param array $import_data import data
     *
     * @return void
     * @since  1.0.0
     */
    public function cwp_import_post_types( $import_data = array() ){
        
        if(isset($import_data) && !empty($import_data)){
            $cwp_custom_types = CWP_types();
            $cwp_custom_types = $cwp_custom_types == '' ? array() : $cwp_custom_types;
            foreach($import_data as $post_type => $post_type_data){
                if(!isset($cwp_custom_types[$post_type])){
                    $cwp_custom_types[$post_type] = $post_type_data;
                }
            }
            update_option('cwp_custom_types', $cwp_custom_types);
        }
        
    }
        
    /**
     * Method cwp_import_taxonomies
     *
     * @param array $import_data 
     *
     * @return void
     * @since  1.0.0
     */
    public function cwp_import_taxonomies( $import_data = array() ){
        
        if(isset($import_data) && !empty($import_data)){
            $cwp_custom_taxonomies = get_option('cwp_custom_taxonomies');
            $cwp_custom_taxonomies = $cwp_custom_taxonomies == '' ? array() : $cwp_custom_taxonomies;
            foreach($import_data as $taxonomy_name => $taxonomy_data){
                if(!isset($cwp_custom_taxonomies[$taxonomy_name])){
                    $cwp_custom_taxonomies[$taxonomy_name] = $taxonomy_data;
                }
            }
            update_option('cwp_custom_taxonomies', $cwp_custom_taxonomies);
        }
        
    }
        
    /**
     * Method cwp_import_terms
     *
     * @param array $import_data
     *
     * @return void
     * @since  1.0.0
     */
    public function cwp_import_terms( $import_data = array() ){
        
        if(isset($import_data) && !empty($import_data)){
            foreach ( $import_data as $taxonomy=>$allterms) {
                if(!empty($allterms) && is_array($allterms)){
                    foreach ( $allterms as $terms) {
                        if(!empty($terms) && count($terms) > 0){
                            $id = wp_insert_term(
                                $terms['name'],
                                $terms['taxonomy'], 
                                array(
                                    'slug'   => $terms['slug'],
                                    'parent' => $terms['parent'],
                                )
                            );
                        }
                    }
                }
            }
            return $id;
        }
        
    }
        
    /**
     * Method cwp_import_custom_fields
     *
     * @param array $import_data
     *
     * @return void
     * @since  1.0.0
     */
    public function cwp_import_custom_fields( $import_data = array() ){
        
        if(isset($import_data) && !empty($import_data)){
            $cwp_custom_fields = CWP()->get_custom_fields('post_types');
            $cwp_custom_fields = $cwp_custom_fields == '' ? array() : $cwp_custom_fields;
            if(!empty($import_data) && count($import_data) > 0){
                foreach($import_data as $field_slug => $field_options){
                    if(!isset($cwp_custom_fields[$field_slug])){
                        $cwp_custom_fields[$field_slug] = $field_options;
                    }
                }
                CWP()->update_custom_fields('post_types', $cwp_custom_fields);
            }
        }
        
    }
    
    public function cwp_import_taxonomies_custom_fields( $import_data = array() ){
        
        if(isset($import_data) && !empty($import_data)){
            $cwp_tax_custom_fields = CWP()->get_custom_fields('taxonomy');
            $cwp_tax_custom_fields = $cwp_tax_custom_fields == '' ? array() : $cwp_tax_custom_fields;
            if(!empty($import_data) && count($import_data) > 0){
                foreach($import_data as $taxonomy_name => $taxonomy_custom_fields){
                    if(!isset($cwp_tax_custom_fields[$taxonomy_name])){
                        $cwp_tax_custom_fields[$taxonomy_name] = $taxonomy_custom_fields;
                    }
                }
                CWP()->update_custom_fields('taxonomy', $cwp_tax_custom_fields);
            }
        }
        
    }
        
    /**
     * Method cwp_import_user_custom_fields
     *
     * @param array $import_data
     *
     * @return void
     * @since  1.0.0
     */
    public function cwp_import_user_custom_fields( $import_data = array() ){
        
        if(isset($import_data) && !empty($import_data)){
            $cwp_user_custom_fields = CWP()->get_custom_fields('user');
            $cwp_user_custom_fields = $cwp_user_custom_fields == '' ? array() : $cwp_user_custom_fields;
            if(!empty($import_data) && count($import_data) > 0){
                foreach($import_data as $user_role => $user_custom_fields){
                    if(!isset($cwp_user_custom_fields[$user_role])){
                        $cwp_user_custom_fields[$user_role] = $user_custom_fields;
                    }
                }
                CWP()->update_custom_fields('user', $cwp_user_custom_fields);
            }
        }
        
    }
        
    /**
     * Method cwp_import_post_type_forms
     *
     * @param array $import_data 
     *
     * @return void
     * @since  1.0.0
     */
    public function cwp_import_post_type_forms( $import_data = array() ){
        if(isset($import_data) && !empty($import_data)){
            $cwp_post_type_forms = CWP()->get_form('post_type');
            $cwp_post_type_forms = $cwp_post_type_forms == '' ? array() : $cwp_post_type_forms;
            if(!empty($import_data) && count($import_data) > 0){
                foreach($import_data as $post_type => $form_data){
                    if(!isset($cwp_post_type_forms[$post_type])){
                        $cwp_post_type_forms[$post_type] = $form_data;
                    }
                }
                CWP()->update_form('post_type', $cwp_post_type_forms);
            }
        }
    }

    /**
     * Method cwp_import_loop_builder_forms
     *
     * @param array $import_data 
     *
     * @return void
     * @since  1.0.0
     */
    public function cwp_import_loop_builder_forms( $import_data = array() ){
        if(isset($import_data) && !empty($import_data)){
            $cwp_loop_builder_forms = CWP()->get_form('loop_builder');
            $cwp_loop_builder_forms = $cwp_loop_builder_forms == '' ? array() : $cwp_loop_builder_forms;
            if(!empty($import_data) && count($import_data) > 0){
                foreach($import_data as $post_type => $form_data){
                    if(!isset($cwp_loop_builder_forms[$post_type])){
                        $cwp_loop_builder_forms[$post_type] = $form_data;
                    }
                }
                CWP()->update_form('loop_builder', $cwp_loop_builder_forms);
            }
        }
    }

    /**
     * Method cwp_import_custom_forms_fields
     *
     * @param array $import_data
     *
     * @return void
     * @since  1.1.4
     */
    public function cwp_import_custom_forms_fields( $import_data = array() ){
        if(isset($import_data) && !empty($import_data)){
            $cwp_custom_fields = CWP()->get_custom_fields('custom_forms');
            $cwp_custom_fields = $cwp_custom_fields == '' ? array() : $cwp_custom_fields;
            if(!empty($import_data) && count($import_data) > 0){
                foreach($import_data as $field_slug => $field_options){
                    if(!isset($cwp_custom_fields[$field_slug])){
                        $cwp_custom_fields[$field_slug] = $field_options;
                    }
                }
                CWP()->update_custom_fields('custom_forms', $cwp_custom_fields);
            }
        }
    }
        
    /**
     * Method cwp_import_search_forms
     *
     * @param array $import_data
     *
     * @return void
     * @since  1.0.0
     */
    public function cwp_import_search_forms( $import_data = array() ){
        if(isset($import_data) && !empty($import_data)){
            $cwp_search_forms = CWP()->get_form('search_fields');
            $cwp_search_forms = $cwp_search_forms == '' ? array() : $cwp_search_forms;
            if(!empty($import_data) && count($import_data) > 0){
                foreach($import_data as $post_type => $form_data){
                    if(!isset($cwp_search_forms[$post_type])){
                        $cwp_search_forms[$post_type] = $form_data;
                    }
                }
                CWP()->update_form('search_fields', $cwp_search_forms);
            }
        }
    }
        
    /**
     * Method cwp_import_filter_forms
     *
     * @param array $import_data
     *
     * @return void
     * @since  1.0.0
     */
    public function cwp_import_filter_forms( $import_data = array() ){
        if(isset($import_data) && !empty($import_data)){
            $cwp_filter_forms = CWP()->get_form('search_filters');
            $cwp_filter_forms = $cwp_filter_forms == '' ? array() : $cwp_filter_forms;
            if(!empty($import_data) && count($import_data) > 0){
                foreach($import_data as $post_type => $form_data){
                    if(!isset($cwp_filter_forms[$post_type])){
                        $cwp_filter_forms[$post_type] = $form_data;
                    }
                }
                CWP()->update_form('search_filters', $cwp_filter_forms);
            }
        }
    }
        
    /**
     * Method cwp_import_user_reg_forms
     *
     * @param array $import_data
     *
     * @return void
     * @since  1.0.0
     */
    public function cwp_import_user_reg_forms( $import_data = array() ){
        if(isset($import_data) && !empty($import_data)){
            $cwp_user_forms = CWP()->get_form('user_register');
            $cwp_user_forms = $cwp_user_forms == '' ? array() : $cwp_user_forms;
            if(!empty($import_data) && count($import_data) > 0){
                foreach($import_data as $user_role => $form_data){
                    if(!isset($cwp_user_forms[$user_role])){
                        $cwp_user_forms[$user_role] = $form_data;
                    }
                }
                CWP()->update_form('user_register', $cwp_user_forms);
            }
        }
    }
        
    /**
     * Method cwp_import_user_profile_forms
     *
     * @param array $import_data
     *
     * @return void
     * @since  1.0.0
     */
    public function cwp_import_user_profile_forms( $import_data = array() ){
        if(isset($import_data) && !empty($import_data)){
            $cwp_user_forms = CWP()->get_form('user_profile');
            $cwp_user_forms = $cwp_user_forms == '' ? array() : $cwp_user_forms;
            if(!empty($import_data) && count($import_data) > 0){
                foreach($import_data as $user_role => $form_data){
                    if(!isset($cwp_user_forms[$user_role])){
                        $cwp_user_forms[$user_role] = $form_data;
                    }
                }
                CWP()->update_form('user_profile', $cwp_user_forms);
            }
        }
    }
        
    /**
     * Method cwp_import_single_layout_forms
     *
     * @param array $import_data
     *
     * @return void
     * @since  1.0.0
     */
    public function cwp_import_single_layout_forms( $import_data = array() ){
        if(isset($import_data) && !empty($import_data)){
            $cwp_single_layout_forms = CWP()->get_form('single_layout');
            $cwp_single_layout_forms = $cwp_single_layout_forms == '' ? array() : $cwp_single_layout_forms;
            if(!empty($import_data) && count($import_data) > 0){
                foreach($import_data as $user_role => $form_data){
                    if(!isset($cwp_user_forms[$user_role])){
                        $cwp_single_layout_forms[$user_role] = $form_data;
                    }
                }
                CWP()->update_form('single_layout', $cwp_single_layout_forms);
            }
        }
    }

    /**
     * Method cwp_import_user_dashboard_forms
     *
     * @param array $import_data
     *
     * @return void
     * @since  1.0.0
     */
    public function cwp_import_user_dashboard_forms( $import_data = array() ){
        if(isset($import_data) && !empty($import_data)){
            $cwp_userdash = CWP()->cubewp_options('cwp_userdash');
            $cwp_userdash = $cwp_userdash == '' ? array() : $cwp_userdash;
            if(!empty($import_data) && count($import_data) > 0){
                foreach($import_data as $key => $form_data){
                    if(!isset($cwp_userdash[$key])){
                        $cwp_userdash[$key] = $form_data;
                    }
                }
                update_option('cwp_userdash', $cwp_userdash);
            }
        }
    }

    /**
     * Method cwp_import_user_dashboard_forms
     *
     * @param array $import_data
     *
     * @return void
     * @since  1.0.0
     */
    public function cwp_import_settings( $import_data = array() ){
        if(isset($import_data) && !empty($import_data)){
            $cwp_settings = CWP()->cubewp_options('cwp_settings');
            $cwp_settings = $cwp_settings == '' ? array() : $cwp_settings;
            if(!empty($import_data) && count($import_data) > 0){
                foreach($import_data as $key => $form_data){
                    if(!isset($cwp_settings[$key])){
                        $cwp_settings[$key] = $form_data;
                    }
                }
                update_option('cwpOptions', $cwp_settings);
            }
        }
    }

}
session_write_close();