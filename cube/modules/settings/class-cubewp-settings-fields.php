<?php
if (!defined('ABSPATH'))
    exit;

class CubeWp_Settings_Fields {
    
    public function __construct( $parent = null ) {
        
        add_filter('cubewp/settings/heading/field', array($this, 'field_heading'), 10, 2);
        add_filter('cubewp/settings/desc/field', array($this, 'field_description'), 10, 2);
        add_filter('cubewp/settings/info/field', array($this, 'info_field'), 10, 2);
        add_filter('cubewp/settings/text/field', array($this, 'text_field'), 10, 2);
        add_filter('cubewp/settings/textarea/field', array($this, 'textarea_field'), 10, 2);
        add_filter('cubewp/settings/editor/field', array($this, 'editor_field'), 10, 2);
        add_filter('cubewp/settings/ace_editor/field', array($this, 'ace_editor_field'), 10, 2);
        add_filter('cubewp/settings/switch/field', array($this, 'switch_field'), 10, 2);
        add_filter('cubewp/settings/select/field', array($this, 'dropdown_field'), 10, 2);
        add_filter('cubewp/settings/color/field', array($this, 'color_field'), 10, 2);
        add_filter('cubewp/settings/media/field', array($this, 'media_field'), 10, 2);
        add_filter('cubewp/settings/image_select/field', array($this, 'image_select_field'), 10, 2);
        add_filter('cubewp/settings/typography/field', array($this, 'typography_field'), 10, 2);
        
        add_filter('cubewp/settings/pages/options', array($this, 'pages_options'), 10, 2);
        add_filter('cubewp/settings/posts/options', array($this, 'posts_options'), 10, 2);
        add_filter('cubewp/settings/terms/options', array($this, 'terms_options'), 10, 2);
        
        add_filter('cubewp/settings/google_fonts/options', array($this, 'google_fonts_options'), 10, 1);
        add_filter('cubewp/settings/font_styles/options', array($this, 'font_styles_options'), 10, 2);
        add_filter('cubewp/settings/font_subsets/options', array($this, 'font_subsets_options'), 10, 2);
        add_filter('cubewp/settings/pages/field', array($this, 'pages'), 10, 2);
        add_filter('cubewp/settings/submit_edit_page/field', array($this, 'submit_edit_page'), 10, 2);
        
    }
    public function submit_edit_page( $output = '', $args = array() ) {
        $args = $this->default_input_parameters( $args );
        $fieldID = $args['id'];
        $output = apply_filters( "cubewp/settings/heading/field", '', $args );
        $postTypes = CWP_all_post_types('settings');
        $output .= '<td>';
        foreach ($postTypes as $postType => $postTypeLabel) {
            $args['id'] = $fieldID . '[' . $postType . ']';
            $args['options'] = apply_filters( "cubewp/settings/pages/options", $args['options'], $args );
            $value = isset($args['value'][$postType]) ? $args['value'][$postType] : '';
            $output .= '<fieldset id="cwp-' . esc_attr( $args['id'] ) . '" class="cwp-field-container cwp-' . esc_attr( $args['type'] ) . '-container" data-id="' . esc_attr( $args['id'] ) . '" data-type="' . esc_attr( $args['type'] ) . '" style="margin-bottom: 10px;">';
            $field_args = array(
                'id'          => $args['id'],
                'name'        => $args['id'],
                'placeholder' => $args['placeholder'] == '' ? esc_html__( 'Select Option', "cubewp-framework" ) : '',
                'class'       => $args['class'],
                'value'       => $value,
                'options'     => $args['options'],
                'extra_attrs' => $args['extra_attrs'],
            );
            $field_args['class'] = $field_args['class'] . ' cwp-single-select';
            $output .= cwp_render_dropdown_input( $field_args );
            $args['desc'] = sprintf(__( 'Select The Page Used For %s Submission (Page must include the %s Submission Shortcode)', 'cubewp-framework' ), $postTypeLabel, $postTypeLabel);
            $output .= apply_filters( "cubewp/settings/desc/field", '', $args );
            $output .= '</fieldset>';
        }
        $output .= '</td>';
        return $output;
    }
    
    public function pages( $output = '', $args = array() ) {
        
        $args = $this->default_input_parameters( $args );
        $output = apply_filters( "cubewp/settings/heading/field", '', $args );
        $output .= '<td>';
            $args['options'] = apply_filters( "cubewp/settings/pages/options", $args['options'], $args );
            $output .= '<fieldset id="cwp-' . esc_attr( $args['id'] ) . '" class="cwp-field-container cwp-' . esc_attr( $args['type'] ) . '-container" data-id="' . esc_attr( $args['id'] ) . '" data-type="' . esc_attr( $args['type'] ) . '" style="margin-bottom: 10px;">';
            $field_args = array(
                'id'          => $args['id'],
                'name'        => $args['id'],
                'placeholder' => $args['placeholder'] == '' ? esc_html__( 'Select Option', "cubewp-framework" ) : '',
                'class'       => $args['class'],
                'value'       => $args['value'],
                'options'     => $args['options'],
                'extra_attrs' => $args['extra_attrs'],
            );
            $output .= cwp_render_dropdown_input( $field_args );
            $output .= apply_filters( "cubewp/settings/desc/field", '', $args );
            $output .= '</fieldset>';
        $output .= '</td>';
        return $output;
    }
    
    function default_input_parameters( $args = array() ){

        $default = array(
            'type'              =>    '',
            'id'                =>    '',
            'class'             =>    '',
            'name'              =>    '',
            'value'             =>    '',
            'placeholder'       =>    '',
            'title'             =>    '',
            'sub_title'         =>    '',
            'desc'              =>    '',
            'options'           =>    '',
            'rows'              =>    '',
            'notice'            =>    '',
            'style'             =>    '',
            'extra_attrs'       =>    '',
        );
        return wp_parse_args($args, $default);

    }

    public function field_heading( $output = '', $args = array() ){
        
        $args   =  $this->default_input_parameters( $args );
        
        $output = '<th scope="row">
            <div class="cwp_field_th">';
                $output .= esc_html($args['title']);
                if(isset($field['sub_title']) && !empty($args['sub_title'])){
                    $output .= '<span class="description">'. $args['sub_title'] .'</span>';
                }
            $output .= '</div>
        </th>';
            
        return $output;
    }
    
    public function field_description( $output = '', $args = array() ){
        $args = $this->default_input_parameters( $args );
        if(isset($args['desc']) && !empty($args['desc'])){
            return '<div class="cwp-field-desc">'. $args['desc'] .'</div>';
        }
        return '';
    }
    
    public function info_field( $output = '', $args = array() ){
        $args   =  $this->default_input_parameters( $args );
        
        if(isset($args['notice']) && $args['notice'] == true){
            $class = 'cwp-notice-field';
        }else{
            $class = 'cwp-info-field';
        }
        
        $output    = '</tr></tbody></table>';
        $output   .= '<div id="'. esc_attr($args['id']) .'-info" class="cwp-info-holder cwp-'. esc_attr( $args['style'] ) .' '. esc_attr($class).' ">';
            $output   .= '<p class="cwp-info-desc">';
                if(isset($args['title']) && !empty($args['title'])){
                    $output   .= '<b>'. $args['title'] .'</b><br>';
                }
                if(isset($args['desc']) && !empty($args['desc'])){
                    $output   .= $args['desc'];
                }
            $output   .= '</p>';
        $output   .= '</div>';
       
        $output   .= '<table class="form-table mt-0">';
        $output   .= '<tbody>';
        $output   .= '<tr>';
        
        return $output;
    }


    public function text_field( $output = '', $args = array() ){
        
        $args   =  $this->default_input_parameters( $args );
        
        $output = apply_filters("cubewp/settings/heading/field", '', $args);
        
        $output   .= '<td>';
            $output   .= '<fieldset id="cwp-'. esc_attr($args['id']).'" class="cwp-field-container cwp-'. esc_attr($args['type']).'-container" data-id="'. esc_attr($args['id']).'" data-type="'. esc_attr($args['type']).'">';
                
                $field_args = array(
                    'type'              =>  $args['type'],
                    'id'                =>  $args['id'],
                    'name'              =>  $args['id'],
                    'placeholder'       =>  $args['placeholder'],
                    'class'             =>  $args['class'],
                    'value'             =>  $args['value'],
                    'extra_attrs'       =>  $args['extra_attrs'],
                );
                $output .= cwp_render_text_input( $field_args );
                $output .= apply_filters("cubewp/settings/desc/field", '', $args);
                
            $output   .= '</fieldset>';
        $output .= '</td>';
        
        return $output;
    }
    
    public function textarea_field( $output = '', $args = array() ){
        
        $args   =  $this->default_input_parameters( $args );
        
        $output = apply_filters("cubewp/settings/heading/field", '', $args);
        
        $output .= '<td>';
            $output   .= '<fieldset id="cwp-'. esc_attr($args['id']).'" class="cwp-field-container cwp-'. esc_attr($args['type']).'-container" data-id="'. esc_attr($args['id']).'" data-type="'. esc_attr($args['type']).'">';
            
                $field_args = array(
                    'id'                =>  $args['id'],
                    'name'              =>  $args['id'],
                    'placeholder'       =>  $args['placeholder'],
                    'class'             =>  $args['class'],
                    'value'             =>  $args['value'],
                    'extra_attrs'       =>  $args['extra_attrs'],
                    'row'               =>  $args['row'],
                );
                $output .= cwp_render_textarea_input( $field_args );
                $output .= apply_filters("cubewp/settings/desc/field", '', $args);
            
            $output   .= '</fieldset>';
        $output .= '</td>';
        
        return $output;
    }
    
    public function editor_field( $output = '', $args = array() ){
        
        $args   =  $this->default_input_parameters( $args );
        
        $output = apply_filters("cubewp/settings/heading/field", '', $args);
        
        $output .= '<td>';
            $output   .= '<fieldset id="cwp-'. esc_attr($args['id']).'" class="cwp-field-container cwp-'. esc_attr($args['type']).'-container" data-id="'. esc_attr($args['id']).'" data-type="'. esc_attr($args['type']).'">';
            
                $defaults = array(
                    'textarea_name' => esc_attr( $args['id'] ),
                    'editor_class'  => esc_attr( $args['class'] ),
                    'textarea_rows' => 10,
                    'teeny'         => true,
                );
                $field_args = wp_parse_args( $args['args'], $defaults );

                ob_start();
                    wp_editor( $args['value'], $args['id'], $field_args );
                    $output .= ob_get_contents();
                ob_end_clean();

                $output .= apply_filters("cubewp/settings/desc/field", '', $args);
                
            $output .= '</fieldset>';
        $output .= '</td>';
        
        return $output;
    }
    
    public function ace_editor_field( $output = '', $args = array() ){
        
        $args   =  $this->default_input_parameters( $args );
        
        $output = apply_filters("cubewp/settings/heading/field", '', $args);
        
        $output .= '<td>';
            
            $params = array(
                'minLines' => 10,
                'maxLines' => 30,
            );
            if ( isset($args['args']) && !empty($args['args']) && is_array($args['args']) ) {
                $params = wp_parse_args( $args['args'], $params );
            }
            
            if(!isset($args['mode'])){
                $args['mode'] = 'javascript';
            }
            if(!isset($args['theme'])){
                $args['theme'] = 'monokai';
            }
            
            $output .= '<fieldset id="cwp-'. esc_attr($args['id']) .'" class="cwp-field-container cwp-container-'. esc_attr($args['id']) .'" data-id="'. esc_attr($args['mode']) .'_editor" data-type="'. esc_attr($args['id']) .'">';
                $output .= '<div class="ace-wrapper">';
                    $field_args = array(
                        'name'              =>  '',
                        'class'             =>  'localize_data',
                        'value'             =>  esc_html( wp_json_encode( $params ) ),
                    );
                    $output .= cwp_render_hidden_input( $field_args );

                    $field_args = array(
                        'id'                =>  $args['id'],
                        'name'              =>  $args['id'],
                        'placeholder'       =>  $args['placeholder'],
                        'class'             =>  'ace-editor hide '. $args['class'],
                        'value'             =>  $args['value'],
                        'extra_attrs'       =>  $args['extra_attrs']. ' data-editor="'. esc_attr($args['id']) .'-editor" data-mode="'. esc_attr($args['mode']) .'" data-theme="'. esc_attr($args['theme']) .'"',
                        'rows'              =>  $args['rows'],
                    );
                    $output .= cwp_render_textarea_input( $field_args );

                    $output .= '<pre id="'. esc_attr($args['id']) .'-editor" class="ace-editor-area">'. esc_html($args['value']) .'</pre>';

                $output .= '</div>';
                $output .= apply_filters("cubewp/settings/desc/field", '', $args);
            $output .= '</fieldset>';
            
        $output .= '</td>';
        
        return $output;
    }
    
    public function switch_field( $output = '', $args = array() ){
        
        $args   =  $this->default_input_parameters( $args );
        
        $output = apply_filters("cubewp/settings/heading/field", '', $args);
        $output .= '<td>';
        
            $cb_enabled  = '';
            $cb_disabled = '';

            if ( 1 === (int) $args['value'] ) {
                $cb_enabled = ' selected';
            } else {
                $cb_disabled = ' selected';
            }
            
            $args['on']  = isset( $args['on'] )  ? $args['on']  : esc_html__('On', 'cubewp-framework');
            $args['off'] = isset( $args['off'] ) ? $args['off'] : esc_html__('Off', 'cubewp-framework');
        
            $output   .= '<fieldset id="cwp-'. esc_attr($args['id']).'" class="cwp-field-container cwp-'. esc_attr($args['type']).'-container" data-id="'. esc_attr($args['id']).'" data-type="'. esc_attr($args['type']).'">';
                $output .= '<div class="cwp-switch-options">';
                    $output .= '<label class="cb-enable'. esc_attr( $cb_enabled ) .'" data-id="'. esc_attr( $args['id'] ) .'"><span>'. esc_html( $args['on'] ) .'</span></label>';
                    $output .= '<label class="cb-disable'. esc_attr( $cb_disabled ) .'" data-id="'. esc_attr( $args['id'] ) .'"><span>'. esc_html( $args['off'] ) .'</span></label>';
                    
                    $field_args = array(
                        'id'                =>  $args['id'],
                        'name'              =>  $args['id'],
                        'class'             =>  'checkbox checkbox-input '. $args['class'],
                        'value'             =>  $args['value'],
                    );
                    $output .= cwp_render_hidden_input( $field_args );
                $output .= '</div>';
            $output .= '</fieldset>';
        
        $output .= apply_filters("cubewp/settings/desc/field", '', $args);
            
        $output .= '</td>';
        
        return $output;
        
    }
    
    public function dropdown_field( $output = '', $args = array() ){
        
        $args   =  $this->default_input_parameters( $args );
        
        $output = apply_filters("cubewp/settings/heading/field", '', $args);
        
        if(isset($args['data']) && !empty($args['data'])){
            $args['options'] = apply_filters("cubewp/settings/{$args['data']}/options", $args['options'], $args);
            $args['options'] = apply_filters("cubewp/settings/{$args['data']}/options", $args['options'], $args);
            $args['options'] = apply_filters("cubewp/settings/{$args['data']}/options", $args['options'], $args);
        }
        
        $output .= '<td>';
            $output   .= '<fieldset id="cwp-'. esc_attr($args['id']).'" class="cwp-field-container cwp-'. esc_attr($args['type']).'-container" data-id="'. esc_attr($args['id']).'" data-type="'. esc_attr($args['type']).'">';
            
                $field_args = array(
                    'id'                =>  $args['id'],
                    'name'              =>  $args['id'],
                    'placeholder'       =>  $args['placeholder'] == '' ? esc_html__('Select Option', "cubewp-framework") : '',
                    'class'             =>  $args['class'],
                    'value'             =>  $args['value'],
                    'options'           =>  $args['options'],
                    'extra_attrs'       =>  $args['extra_attrs'],
                );

                if(isset($args['multi']) && $args['multi'] == true){
                    $field_args['extra_attrs'] = ' multiple '. $field_args['extra_attrs'];
                    $field_args['class']       = $field_args['class']. ' cwp-multi-select';
                    $field_args['name']        = $field_args['name'].'[]';
                }else{
                    $field_args['class']       = $field_args['class']. ' cwp-single-select';
                }

                $output .= cwp_render_dropdown_input( $field_args );
                $output .= apply_filters("cubewp/settings/desc/field", '', $args);
            
            $output .= '</fieldset>';
        $output .= '</td>';
        
        return $output;
    }
    
    public function color_field( $output = '', $args = array() ){
        
        $args   =  $this->default_input_parameters( $args );
        
        $output = apply_filters("cubewp/settings/heading/field", '', $args);
        
        $output .= '<td>';
            $output   .= '<fieldset id="cwp-'. esc_attr($args['id']).'" class="cwp-field-container cwp-'. esc_attr($args['type']).'-container" data-id="'. esc_attr($args['id']).'" data-type="'. esc_attr($args['type']).'">';
            
                $field_args = array(
                    'type'              =>  'text',
                    'id'                =>  $args['id'],
                    'name'              =>  $args['id'],
                    'placeholder'       =>  $args['placeholder'],
                    'class'             =>  'cwp-color-field ', $args['class'],
                    'value'             =>  $args['value'],
                    'extra_attrs'       =>  $args['extra_attrs'],
                );
                $output .= cwp_render_text_input( $field_args );
                $output .= apply_filters("cubewp/settings/desc/field", '', $args);
                
            $output .= '</fieldset>';
        $output .= '</td>';
        
        return $output;
    }
    
    public function media_field( $output = '', $args = array() ){
        
        $args   =  $this->default_input_parameters( $args );
        
        $output = apply_filters("cubewp/settings/heading/field", '', $args);
        
        $output .= '<td>';
            $output   .= '<fieldset id="cwp-'. esc_attr($args['id']).'" class="cwp-field-container cwp-'. esc_attr($args['type']).'-container" data-id="'. esc_attr($args['id']).'" data-type="'. esc_attr($args['type']).'">';
            
                $image_thumb = '';
                if(isset($args['value'])) {
                    $image_thumb = wp_get_attachment_image_src($args['value'], array('300','169'));
                    if($image_thumb){
                        $image_thumb = $image_thumb[0];
                    }
                }
                $output .= '<a href="' . $image_thumb . '" target="_blank"><img id="' . $args['id'] . '_preview" class="image_preview" alt="image" src="' . $image_thumb . '" /></a>' . "\n";
                $output .= '<input id="' . $args['id'] . '_button" data-multiple="false" type="button" data-uploader_title="' . __('Upload', 'cubewp-framework') . '" data-uploader_button_text="' . __('Use image', 'cubewp-framework') . '" class="image_upload_button button" value="' . __('Upload', 'plugin_textdomain') . '" />' . "\n";
                $output .= '<input id="' . $args['id'] . '_delete" type="button" class="image_delete_button button" value="' . __('Remove', 'cubewp-framework') . '" />' . "\n";
                $output .= '<input id="' . $args['id'] . '" class="image_data_field" type="hidden" name="' . $args['id'] . '" value="' . $args['value'] . '"/><br/>' . "\n";

                $output .= apply_filters("cubewp/settings/desc/field", '', $args);
                
            $output .= '</fieldset>';
        $output .= '</td>';
        
        return $output;
    }
    
    public function image_select_field( $output = '', $args = array() ){
        
        $args   =  $this->default_input_parameters( $args );
        
        $output = apply_filters("cubewp/settings/heading/field", '', $args);
        
        $output .= '<td>';
            $output .= '<fieldset id="cwp-'. esc_attr($args['id']) .'" class="cwp-field-container cwp-image_select-container" data-id="'. esc_attr($args['id']) .'" data-type="image_select">';
                $output .= '<ul class="cwp-image-select">';
                    $counter = 0;
                    foreach($args['options'] as $key => $data){
                        $counter++;
                        
                        $selected = '';
                        $checked  = '';
                        if($args['value'] == $key){
                            $selected = ' cwp-image-select-selected ';
                            $checked = ' checked="checked"';
                        }
                        
                        $output .= '<li class="cwp-image-select">
                            <label class="cwp-image-select'. $selected . esc_attr($args['id'].'_'.$counter) .'" for="'. esc_attr($args['id'].'_'.$counter) .'">';
                                $field_args = array(
                                    'type'              =>  'radio',
                                    'id'                =>  $args['id'].'_'.$counter,
                                    'name'              =>  $args['id'],
                                    'placeholder'       =>  $args['placeholder'],
                                    'class'             =>  'no-update ',
                                    'value'             =>  esc_attr($key),
                                    'extra_attrs'       =>  $checked,
                                );
                                $output .= cwp_render_text_input( $field_args );
                                $output .= '<img src="'. esc_url($data['img']) .'" title="'. esc_attr($data['alt']) .'" alt="'. esc_attr($data['alt']) .'">
                            </label>
                        </li>';
                    }
                $output .= '</ul>';
            $output .= '</fieldset>';
            $output .= apply_filters("cubewp/settings/desc/field", '', $args);
        $output .= '</td>';
        
        return $output;
    }
    
    public function typography_field( $output = '', $args = array() ){
        $args   =  $this->default_input_parameters( $args );
        
        $output = apply_filters("cubewp/settings/heading/field", '', $args);
        
        
        $font_family            = isset($args['value']['font-family']) ? $args['value']['font-family'] : '';
        $font_weight            = isset($args['value']['font-weight']) ? $args['value']['font-weight'] : '';
        $subsets                = isset($args['value']['subsets']) ? $args['value']['subsets'] : '';
        $google_fonts_options   = apply_filters("cubewp/settings/google_fonts/options", '');
        $font_styles_options    = apply_filters("cubewp/settings/font_styles/options", '', $font_family);
        $font_subsets_options   = apply_filters("cubewp/settings/font_subsets/options", '', $font_family);
        
        $output .= '<td>';
            $output   .= '<fieldset id="cwp-'. esc_attr($args['id']).'" class="cwp-field-container cwp-'. esc_attr($args['type']).'-container" data-id="'. esc_attr($args['id']).'" data-type="'. esc_attr($args['type']).'">';
                $output .= '<div class="cwp-typography-container">';
        
                    $output .= '<div class="select_wrapper typography-family">';
                        $output .= '<label for="'. $args['id']. '-family">'.esc_html__("Font Family", "cubewp-framework") .'</label>';
                        $field_args = array(
                            'id'                =>  $args['id'].'-family',
                            'name'              =>  $args['id'].'[font-family]',
                            'placeholder'       =>  $args['placeholder'],
                            'class'             =>  $args['class']. 'cwp-typography-family',
                            'value'             =>  $font_family,
                            'options'           =>  $google_fonts_options,
                            'extra_attrs'       =>  $args['extra_attrs'],
                        );
                        $output .= cwp_render_dropdown_input( $field_args );
                        $output .= apply_filters("cubewp/settings/desc/field", '', $args);
                    $output .= '</div>';

                    $output .= '<div class="select_wrapper typography-style">';
                        $output .= '<label for="'. $args['id']. '-font-style">'.esc_html__("Font Weight & Style", "cubewp-framework") .'</label>';
                        $field_args = array(
                            'id'                =>  $args['id'].'-font-style',
                            'name'              =>  $args['id'].'[font-weight]',
                            'placeholder'       =>  $args['placeholder'],
                            'class'             =>  $args['class'],
                            'value'             =>  $font_weight,
                            'options'           =>  $font_styles_options,
                            'extra_attrs'       =>  $args['extra_attrs'] . 'data-val="'. $font_weight .'"',
                        );
                        $output .= cwp_render_dropdown_input( $field_args );
                        $output .= apply_filters("cubewp/settings/desc/field", '', $args);
                    $output .= '</div>';

                    $output .= '<div class="select_wrapper typography-subset">';
                        $output .= '<label for="'. $args['id']. '-subset">'.esc_html__("Font Subsets", "cubewp-framework") .'</label>';
                        $field_args = array(
                            'id'                =>  $args['id'].'-subset',
                            'name'              =>  $args['id'].'[subsets]',
                            'placeholder'       =>  $args['placeholder'],
                            'class'             =>  $args['class'],
                            'value'             =>  $subsets,
                            'options'           =>  $font_subsets_options,
                            'extra_attrs'       =>  $args['extra_attrs'] . 'data-val="'. $subsets .'"',
                        );
                        $output .= cwp_render_dropdown_input( $field_args );
                        $output .= apply_filters("cubewp/settings/desc/field", '', $args);
                    $output .= '</div>';

                    $output .= '<div class="input_wrapper font-size">';
                        $output .= '<label for="'. $args['id']. '-font-size">'.esc_html__("Font Size (px)", "cubewp-framework") .'</label>';
                        $field_args = array(
                            'id'                =>  $args['id'].'-font-size',
                            'name'              =>  $args['id'].'[font-size]',
                            'placeholder'       =>  $args['placeholder'],
                            'class'             =>  $args['class'],
                            'value'             =>  isset($args['value']['font-size']) ? $args['value']['font-size'] : '',
                        );
                        $output .= cwp_render_text_input( $field_args );
                        $output .= apply_filters("cubewp/settings/desc/field", '', $args);
                    $output .= '</div>';
                    
                    $output .= '<div class="input_wrapper line-height">';
                        $output .= '<label for="'. $args['id']. '-line-height">'.esc_html__("Line Height (px)", "cubewp-framework") .'</label>';
                        $field_args = array(
                            'id'                =>  $args['id'].'-line-height',
                            'name'              =>  $args['id'].'[line-height]',
                            'placeholder'       =>  $args['placeholder'],
                            'class'             =>  $args['class'],
                            'value'             =>  isset($args['value']['line-height']) ? $args['value']['line-height'] : '',
                        );
                        $output .= cwp_render_text_input( $field_args );
                        $output .= apply_filters("cubewp/settings/desc/field", '', $args);
                    $output .= '</div>';
                    
                    $output .= '<div class="input_wrapper font-color">';
                        $output .= '<label for="'. $args['id']. '-line-height">'.esc_html__("Font Color", "cubewp-framework") .'</label>';
                        $field_args = array(
                            'id'                =>  $args['id'].'-font-color',
                            'name'              =>  $args['id'].'[color]',
                            'placeholder'       =>  $args['placeholder'],
                            'class'             =>  'cwp-color-field ', $args['class'],
                            'value'             =>  isset($args['value']['color']) ? $args['value']['color'] : '',
                        );
                        $output .= cwp_render_text_input( $field_args );
                        $output .= apply_filters("cubewp/settings/desc/field", '', $args);
                    $output .= '</div>';
                
                $output .= '</div>';
            $output .= '</fieldset>';
        $output .= '</td>';
        
        return $output;
    }


    public function pages_options( $output = '', $args = array() ){
        if(isset($args['args']) && !empty($args['args'])){
            $query_args = $args['args'];
        }else{
            $query_args = array( 'post_type' => 'page', 'posts_per_page' => -1  );
        }
        
        $query_args = array( 'post_type' => 'page', 'posts_per_page' => -1  );
        $query_args['fields']      = 'ids';
        $query_args['post_status'] = 'publish';
        $pages = get_posts( $query_args );
        
        $options = array();
        if(isset($pages) && !empty($pages)){
            foreach($pages as $page){
                $options[$page] = esc_html(get_the_title($page));
            }
        }
        wp_reset_postdata();
        return $options;
    }
    
    public function posts_options( $output = '', $args = array() ){
        if(isset($args) && !empty($args)){
            $query_args = $args['args'];
        }else{
            $query_args = array( 'post_type' => 'post', 'posts_per_page' => -1  );
        }
        $query_args['fields']      = 'ids';
        $query_args['post_status'] = 'publish';
        $pages = get_posts( $query_args );
        
        $options = array();
        if(isset($pages) && !empty($pages)){
            foreach($pages as $page){
                $options[$page] = esc_html(get_the_title($page));
            }
        }
        wp_reset_postdata();
        return $options;
    }
    
    public function terms_options( $output = '', $args = array() ){
     
        if( !isset($args['args']['taxonomies']) ) {
            return array();
        }
        $taxonomy = $args['args']['taxonomies'];
   
        unset($args['args']['taxonomies']);
        $terms    = get_terms( $taxonomy, $args['args'] );
        
        $options = array();
        if(isset($terms) && !empty($terms) && !isset($terms->errors)){
            foreach($terms as $term){
                $options[$term->term_id] = esc_html($term->name);
            }
        }
        return $options;
    }
    
    public function google_fonts(){
		$file = CWP_PLUGIN_PATH . 'cube/functions/settings/googlefonts.php';
		if ( file_exists( $file ) ) {
			return (array) require $file;
		}
		
		return array();
    }
    
    public function google_fonts_options(){
        $google_fonts = apply_filters("cubewp/settings/google_fonts", self::google_fonts());
        
        $options = array();
        if(isset($google_fonts) && !empty($google_fonts)){
            foreach($google_fonts as $key => $google_font){
                $options[$key] = $key;
            }
        }
        
        return $options;
    }
    
    public function font_styles_options( $output = '', $font_family = '' ){
        $google_fonts = apply_filters("cubewp/settings/google_fonts", array());
        
        $options = array();
        if(isset($google_fonts[$font_family]['variants']) && !empty($google_fonts[$font_family]['variants'])){
            foreach($google_fonts[$font_family]['variants'] as $variant){
                $options[$variant['id']] = $variant['name'];
            }
        }
        return $options;
    }
    
    public function font_subsets_options( $output = '', $font_family = '' ){
        $google_fonts = apply_filters("cubewp/settings/google_fonts", array());
        
        $options = array();
        if(isset($google_fonts[$font_family]['subsets']) && !empty($google_fonts[$font_family]['subsets'])){
            foreach($google_fonts[$font_family]['subsets'] as $subset){
                $options[$subset['id']] = $subset['name'];
            }
        }
        return $options;
    }
    
}