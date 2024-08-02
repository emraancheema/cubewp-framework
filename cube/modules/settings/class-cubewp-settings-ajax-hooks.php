<?php
if (!defined('ABSPATH'))
    exit;

class CubeWp_Settings_Ajax_Hooks {
    
    public $helper;
    protected $opt_name = 'cwpOption';

    
    public static function cwp_save_options( $reset = '' ) {
        
        if (!is_user_logged_in() || !is_admin() || !current_user_can('manage_options')) {
            $res = json_encode(array(
                'status' => 'error',
                'html'   => esc_html__('Invalid User Session.', 'cubewp-framework'),
            ));
            die($res);
        }
        if (!isset($_POST['cwpNonce']) || !wp_verify_nonce( $_POST['cwpNonce'], 'plugin_settings-options' )) {
            $res = json_encode(array(
                'status' => 'error',
                'html'   => esc_html__('There Is A Problem With Nonce.', 'cubewp-framework'),
            ));
            die($res);
        }

        if (isset($_POST['activeTab']) && !empty($_POST['activeTab'])) {
            // Saving Last Options Tab User Use Via Cookie For 1 Day
            $cookie_name = "cwp-options-lastUsedTab";
            $cookie_value = sanitize_text_field($_POST['activeTab']);
            setcookie($cookie_name, $cookie_value, time() + (86400 * 30), "/");
        }

        $status = 'error';
        $msg = esc_html__('Unexpected Error Occurred.', 'cubewp-framework');
        $settings_helpers = new CubeWp_Settings_Helpers();
        $post_data = wp_unslash( $_POST['cwpOptions'] );
        $values    = $settings_helpers::parse_str( $post_data );

        if(isset($_POST['reset'])){
            $options = require CWP_PLUGIN_PATH . 'cube/functions/settings/cubewp-default-options.php';
            foreach($options as $option){
                if($_POST['reset'] == 'all' || ($_POST['reset'] == 'section' && $_POST['activeTab'] == $option['id'])){
                    foreach($option['fields'] as $field){
                        if(isset($values[$field['id']]) && is_array($values[$field['id']])){
                            foreach($values[$field['id']] as $key => $val){
                                if(isset($values[$field['id']][$key] )){
                                    $values[$field['id']][$key] = isset($field['default'][$key]) ? $field['default'][$key] : '';
                                }
                            }
                        }else{
                            if(isset($field['id'])){
                                $values[$field['id']] = isset($field['default']) ? $field['default'] : '';
                            }
                        }
                    }
                }
            }
        }
      
        if (isset($values) && !empty($values) && is_array($values)) {

            $update = update_option('cwpOptions', $values);
            $status = 'success';
            $msg = esc_html__('Option Saved Successfully.', 'cubewp-framework');
        }
        if ($status != 'success') {
            $class = 'danger';
        }else {
            $class = 'success';
        }
        $html = '
            <div class="alert-'.$class.' mr-4 cwp-options-alert" role="alert">
                '.$msg.'
            </div>';

        $res = json_encode(array(
            'status' => $status,
            'html'   => $html,
            'update'   => $update,
        ));
        CWP()->cwp_get_option();
        if($update == true){
            do_action( 'cubewp/after/settings/saved', 'saved');
        }
        die($res);
    }
    
    public static function cwp_save_default_options( $reset = '', $resetID = 0 ) {
        
        if (!is_user_logged_in() || !is_admin() || !current_user_can('manage_options')) {
            $res = json_encode(array(
                'status' => 'error',
                'html'   => esc_html__('Invalid User Session.', 'cubewp-framework'),
            ));
            die($res);
        }
        
        if($reset == 'all'){
            $options = require CWP_PLUGIN_PATH . 'cube/functions/settings/cubewp-default-options.php';
            foreach($options as $option){
                if($reset == 'all' || ($reset == 'section' && $resetID == $option['id'])){
                    foreach($option['fields'] as $field){
                        if(isset($values[$field['id']]) && is_array($values[$field['id']])){
                            foreach($values[$field['id']] as $key => $val){
                                if(isset($values[$field['id']][$key] )){
                                    $values[$field['id']][$key] = isset($field['default'][$key]) ? $field['default'][$key] : '';
                                }
                            }
                        }else{
                            if(isset($field['id'])){
                                $values[$field['id']] = isset($field['default']) ? $field['default'] : '';
                            }
                        }
                    }
                }
            }
        }
      
        if (isset($values) && !empty($values) && is_array($values)) {
            update_option('cwpOptions', $values);
        }
    }

    public function cwp_get_font_attributes(){
        
        $font_family            = isset($_POST['font_family']) ? sanitize_text_field($_POST['font_family']) : '';
        $font_styles_options    = apply_filters("cubewp/settings/font_styles/options", '', $font_family);
        $font_subsets_options   = apply_filters("cubewp/settings/font_subsets/options", '', $font_family);
        
        $font_styles = '<option value="">'. esc_html("Select Option", "cubewp-framework") .'</option>';
        if(isset($font_styles_options) && !empty($font_styles_options)){
            foreach($font_styles_options as $key => $val){
                $font_styles .= '<option value="'. $key .'">'. $val .'</option>';
            }
        }
        
        $font_subsets = '<option value="">'. esc_html("Select Option", "cubewp-framework") .'</option>';
        if(isset($font_subsets_options) && !empty($font_subsets_options)){
            foreach($font_subsets_options as $key => $val){
                $font_subsets .= '<option value="'. $key .'">'. $val .'</option>';
            }
        }
        
        wp_send_json( array( 'font_styles' => $font_styles, 'font_subsets' => $font_subsets ) );
    }

}