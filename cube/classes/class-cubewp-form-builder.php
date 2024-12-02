<?php
/**
 * CubeWp Form Builder is for creating form builder content.
 *
 * @version 1.0
 * @package cubewp/cube/classes
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * CubeWp_Form_Builder
 */
class CubeWp_Form_Builder {
    
    public function __construct() {
        add_action( 'wp_ajax_cwpform_save_shortcode', array( $this, 'cwpform_save_shortcode' ), 9 );
        add_action( 'wp_ajax_cwpform_add_section', array( $this, 'cwpform_add_section' ) );
    }

    /**
     * Method cwpform_save_shortcode
     *
     * @return void
     */
    public static function cwpform_save_shortcode() {
        if ( !current_user_can('manage_options') ) {
            wp_send_json( array( 'success' => 'false', 'msg' => esc_html__('You do not have permission to perform this action.', 'cubewp-framework') ) );
            wp_die();
        }
        $form_relation = isset( $_POST['form_relation'] ) ? sanitize_text_field( $_POST['form_relation'] ) : '';
        $form_type     = isset( $_POST['form_type'] ) ? sanitize_text_field( $_POST['form_type'] ) : '';
        if ( $form_type != '' ) {
            $cwp_forms = CWP()->get_form( $form_type );
            if ( isset( $form_relation ) && ! empty( $form_relation ) ) {
                if ( isset( $_POST['cwpform'] ) && ! empty( $_POST['cwpform'] ) ) {
                    
                    if ( $form_type == 'loop_builder' ) {
                        $cwp_forms[ $form_relation ] = apply_filters( 'cubewp/loop/builder/save', array() , $_POST['cwpform'], $form_relation );
                    }else {
                        $cwp_forms[ $form_relation ] = CubeWp_Sanitize_Dynamic_Array( $_POST['cwpform'][ $form_relation ] );
                    }

                    CWP()->update_form( $form_type, $cwp_forms );
                } else {
                    if ( isset( $cwp_forms[ $form_relation ] ) ) {
                        unset( $cwp_forms[ $form_relation ] );
                    }
                    CWP()->update_form( $form_type, $cwp_forms );
                }
            }
        }
        wp_send_json( array( 'message' => esc_html__( "Saved successfully", "cubewp-framework" ) ) );
    }

    /**
     * Method cwpform_add_section
     *
     * @since  1.0.0
     */
    public function cwpform_add_section() {
        if ( !current_user_can('manage_options') ) {
            wp_send_json( array( 'success' => 'false', 'msg' => esc_html__('You do not have permission to perform this action.', 'cubewp-framework') ) );
            wp_die();
        }
        $section_args = [];
        if(isset($_POST['action'])){
            unset($_POST['action']);
            unset($_POST['section_id']);
        }
        if(isset($_POST['form_relation']) && isset($_POST['form_type'])){
            foreach($_POST as $key => $POST){
                $section_args[$key] = $POST;
            }
        }
        $section_args['open_close_class'] = 'open';
        $section_html = $this->cwpform_form_section($section_args);

        wp_send_json( array( 'section_html' => $section_html ) );
    }

    /**
     * Method cwpform_form_section
     *
     * @param array $args section arguments
     *
     * @return string html
     * @since  1.0.0
     */
    public function cwpform_form_section( $args = array() ) {
        $defaults         = array(
            'section_id'          => rand( 123456789, 111111111 ),
            'section_title'       => '',
            'section_description' => '',
            'section_type'        => '',
            'section_class'       => '',
            'content_switcher'    => '',
            'open_close_class'    => 'close',
            'form_relation'       => '',
            'form_type'           => '',
            'fields'              => '',
            'terms'               => '',
        );
        $section = wp_parse_args( $args, $defaults );
        $associated_terms = '';
        if ( isset( $section['terms'] ) && ! empty( $section['terms'] ) ) {
            $comma  = '';
            $_terms = '';
            foreach ( $section['terms'] as $term ) {
                $term_data = get_term( $term );
                if ( isset( $term_data->name ) && ! empty( $term_data->name ) ) {
                    $_terms .= $comma . $term_data->name;
                    $comma  = ',';
                }
            }
            $associated_terms = '<div class="cwp-icon-helpTip">
				<span class="dashicons dashicons-editor-help"></span>
				<div class="cwp-ctp-toolTips drop-left">
					<div class="cwp-ctp-toolTip">
					<h4>' . __( 'Associated Taxonomies', 'cubewp-framework' ) . '</h4>
					<p class="cwp-ctp-tipContent">' . $_terms . '</p>
				</div>
				</div>
			</div>';
        }

        $output       = '';
        $active_class = " " . esc_attr( $section["section_class"] ) . " ";
        $icon_active  = "";
        if ( isset( $section['open_close_class'] ) && $section['open_close_class'] == "open" ) {
            $active_class = "active-expanded";
            $icon_active  = "expanded";
        }
        $move_section_class    = '';
        $section_action_edit   = '';
        $section_action_delete = '';
        if ( $section['form_type'] != 'search_fields' && $section['form_type'] != 'search_filters' ) {
            $move_section_class    = 'cubewp-builder-section-mover ui-sortable-handle';
            $section_action_delete = '<span class="dashicons dashicons-trash cubewp-builder-section-action-delete color-danger"></span>';
            $section_action_edit   = '<span class="dashicons dashicons-edit cubewp-builder-section-action-edit"></span>';
        }
        $output .= '<div id="group-' . esc_attr( $section["section_id"] ) . '" class="cubewp-builder-section cubewp-expand-container ' . esc_attr( $active_class ) . '">';
        $output .= '<div class="cubewp-builder-section-header">';
        if ( $section['form_type'] != 'search_fields' && $section['form_type'] != 'search_filters' ) {
            $output .= '<div class="' . $move_section_class . '">
			  <svg xmlns="SVG namespace" width="20px" height="20px" viewBox="0 0 320 512" fill="#888">
				 <path d="M40 352c-22.1 0-40 17.9-40 40l0 48c0 22.1 17.9 40 40 40l48 0c22.1 0 40-17.9 40-40l0-48c0-22.1-17.9-40-40-40l-48 0zm192 0c-22.1 0-40 17.9-40 40l0 48c0 22.1 17.9 40 40 40l48 0c22.1 0 40-17.9 40-40l0-48c0-22.1-17.9-40-40-40l-48 0zM40 320l48 0c22.1 0 40-17.9 40-40l0-48c0-22.1-17.9-40-40-40l-48 0c-22.1 0-40 17.9-40 40l0 48c0 22.1 17.9 40 40 40zM232 192c-22.1 0-40 17.9-40 40l0 48c0 22.1 17.9 40 40 40l48 0c22.1 0 40-17.9 40-40l0-48c0-22.1-17.9-40-40-40l-48 0zM40 160l48 0c22.1 0 40-17.9 40-40l0-48c0-22.1-17.9-40-40-40L40 32C17.9 32 0 49.9 0 72l0 48c0 22.1 17.9 40 40 40zM232 32c-22.1 0-40 17.9-40 40l0 48c0 22.1 17.9 40 40 40l48 0c22.1 0 40-17.9 40-40l0-48c0-22.1-17.9-40-40-40l-48 0z">
				 </path>
			  </svg>
		   </div>';
        }
        $output .= '<h3>' . esc_html( $section['section_title'] ) . '</h3>';
        $output .= $associated_terms;
        $output .= '<div class="cubewp-builder-section-actions">';
        $output .= $section_action_delete;
        $output .= $section_action_edit;
        $output .= '<span class="dashicons dashicons-arrow-down-alt2 cubewp-builder-section-action-expand cubewp-expand-trigger ' . esc_attr( $icon_active ) . '"></span>';
        $output .= self::cwpform_section_fields( $section );
        $output .= '</div>';
        $output .= '</div>';
        $output .= '<div class="cubewp-builder-section-fields cubewp-builder-fields-sortable cubewp-expand-target ui-sortable">';
        $output .= self::cwpform_get_section_fields( $section );
        $output .= '</div>';
        $output .= '</div>';

        return $output;
    }

    /**
     * Method cwpform_section_fields
     *
     * @param array $section
     *
     * @return string html
     * @since  1.0.0
     */
    private function cwpform_section_fields( array $section ) {
        $fields = $this->cwpform_section_fields_list($section['form_type']);
        $output = '';
        unset($fields['form_type']);
        unset($fields['form_relation']);
        foreach($fields as $input_attr){
            if(isset($input_attr['name']) && isset($section[$input_attr['name']])){
                $input_attr['label'] = '';
                $input_attr['class'] = 'section-field '.str_replace("_","-",$input_attr['id']);
                $input_attr['type'] = 'hidden';
                $input_attr['value'] = $section[$input_attr['name']];
                $input_attr['extra_attrs'] = 'data-name="'.$input_attr['name'].'"';
                $output .= cwp_render_hidden_input( $input_attr );
            }
        }
        return $output;
    }

    /**
     * Method cwpform_get_section_fields
     *
     * @param array $section section data
     *
     * @return string html
     * @since  1.0.0
     */
    private function cwpform_get_section_fields( array $section ) {
        $output = '';
        if ( isset( $section['fields'] ) && ! empty( $section['fields'] ) ) {
            if ( $section['form_type'] == 'post_type' || $section['form_type'] == 'search_filters' || $section['form_type'] == 'search_fields' || $section['form_type'] == 'single_layout' ) {
                $fieldOptions      = CWP()->get_custom_fields( 'post_types' );
                $wp_default_fields = cubewp_post_type_default_fields( $section['form_relation'] );
            } else {
                $fieldOptions      = CWP()->get_custom_fields( 'user' );
                $wp_default_fields = cubewp_user_default_fields();
            }
            foreach ( $section['fields'] as $field ) {
                if ( ! is_array( $field ) ) {
                    if ( isset( $wp_default_fields[ $field ] ) ) {
                        $field = $wp_default_fields[ $field ];
                    } else if ( isset( $fieldOptions[ $field ] ) ) {
                        if ( $section['form_type'] == 'search_filters' || $section['form_type'] == 'search_fields' ) {
                            if ( isset( $fieldOptions[ $field ]['type'] ) && in_array( $fieldOptions[ $field ]['type'], array(
                                    'text',
                                    'switch',
                                    'google_address',
                                    'radio',
                                    'range',
                                    'checkbox',
                                    'dropdown',
                                    'number',
                                    'date_picker'
                                ) ) ) {
                                $field = $fieldOptions[ $field ];
                            }
                        } else {
                            $field = $fieldOptions[ $field ];
                        }
                    }
                }
                if ( isset( $field ) && is_array( $field ) ) {
                    $field['form_relation'] = $section['form_relation'];
                    $field['form_type']     = $section['form_type'];
                    $field['content_switcher']  = $section['content_switcher'];
                    $output .= $this->cwpform_form_field( $field );
                }
            }
        }

        return $output;
    }

    /**
     * Method cwpform_form_field
     *
     * @param array $args single field argument
     *
     * @return string html
     * @since  1.0.0
     */
    public function cwpform_form_field( array $args ) {
        $defaults                 = array(
            'name'            => '',
            'label'           => '',
            'class'           => '',
            'container_class' => '',
            'type'            => '',
            'display_ui'      => '',
            'form_relation'   => '',
            'form_type'       => '',
        );
        $output     = '';
        $field      = wp_parse_args( $args, $defaults );
        $class      = isset( $field['class'] ) && ! empty( $field['class'] ) ? $field['class'] : '-';
        $type       = isset( $field['type'] ) && ! empty( $field['type'] ) ? $field['type'] : '';
        $field_size = isset( $field['field_size'] ) && ! empty( $field['field_size'] ) ? $field['field_size'] : 'size-1-1';
        $field['filter_taxonomy'] = isset( get_field_options( $field['name'] )['filter_taxonomy'] ) && ! empty( get_field_options( $field['name'] )['filter_taxonomy'] ) ? get_field_options( $field['name'] )['filter_taxonomy'] : '';
        $type_icon  = str_replace( "_", "-", strtolower( $type ) );
        $output     .= '<div id="cwpform-field-' . esc_attr( $field["name"] ) . '" class="cubewp-builder-group-widget cubewp-expand-container ' . esc_attr( $field_size ) . '">';
        $output     .= '<div class="cubewp-builder-group-widget-row-wrapper">';
        $output     .= '<div class="cubewp-builder-group-widget-mover">';
        $output     .= '<svg xmlns="SVG namespace" width="20px" height="20px" viewBox="0 0 320 512" fill="#BFBFBF"><path d="M40 352c-22.1 0-40 17.9-40 40l0 48c0 22.1 17.9 40 40 40l48 0c22.1 0 40-17.9 40-40l0-48c0-22.1-17.9-40-40-40l-48 0zm192 0c-22.1 0-40 17.9-40 40l0 48c0 22.1 17.9 40 40 40l48 0c22.1 0 40-17.9 40-40l0-48c0-22.1-17.9-40-40-40l-48 0zM40 320l48 0c22.1 0 40-17.9 40-40l0-48c0-22.1-17.9-40-40-40l-48 0c-22.1 0-40 17.9-40 40l0 48c0 22.1 17.9 40 40 40zM232 192c-22.1 0-40 17.9-40 40l0 48c0 22.1 17.9 40 40 40l48 0c22.1 0 40-17.9 40-40l0-48c0-22.1-17.9-40-40-40l-48 0zM40 160l48 0c22.1 0 40-17.9 40-40l0-48c0-22.1-17.9-40-40-40L40 32C17.9 32 0 49.9 0 72l0 48c0 22.1 17.9 40 40 40zM232 32c-22.1 0-40 17.9-40 40l0 48c0 22.1 17.9 40 40 40l48 0c22.1 0 40-17.9 40-40l0-48c0-22.1-17.9-40-40-40l-48 0z"/></svg>';
        $output     .= '</div>';
        $type_image = CWP_PLUGIN_URI . "cube/assets/admin/images/fields/" . $type_icon . ".png";
        if ( ! file_exists( CWP_PLUGIN_PATH . "cube/assets/admin/images/fields/" . $type_icon . ".png" ) ) {
            $type_image = CWP_PLUGIN_URI . "cube/assets/admin/images/fields/cube.png";
        }
        $output .= '<img src="' . $type_image . '" alt="' . esc_html__( "Field Type Icon", 'cubewp-framework' ) . '" class="cubewp-builder-group-widget-type-icon">';
        $output .= '<p class="cubewp-builder-group-widget-title" title="' . esc_html( $field["label"] ) . '"><span class="subtitle">' . esc_html__( "LABEL", "cubewp-framework" ) . '</span>' . esc_html( $field["label"] ) . '</p>';
        $output .= '<p class="builder-area-content cubewp-builder-group-widget-type"><span class="subtitle">' . esc_html__( "FIELD TYPE", "cubewp-framework" ) . '</span>' . esc_html( $type ) . '</p>';
        $output .= '<p class="builder-area-content cubewp-builder-group-widget-class"><span class="subtitle">' . esc_html__( "CSS CLASS", "cubewp-framework" ) . '</span>' . esc_html( $class ) . '</p>';
        $output .= '<div class="builder-area-content cubewp-builder-group-widget-actions">';
        $output .= '<span class="dashicons dashicons-trash color-danger builder-area-content cubewp-builder-group-widget-delete"></span>';
        $output .= '<span class="dashicons dashicons-arrow-down-alt2 builder-area-content cubewp-builder-group-widget-expander cubewp-expand-trigger"></span>';
        $output .= '</div>';
        if ( $field['form_type'] != "search_filters" ) {
            $output .= '<div class="builder-area-content cubewp-builder-group-widget-size">';
            $output .= '<div class="size">' . self::cwpform_field_size( $field_size ) . '</div>';
            $output .= '<span class="dashicons dashicons-plus cubewp-builder-group-widget-increase-size"></span>';
            $output .= '<span class="dashicons dashicons-minus cubewp-builder-group-widget-decrease-size"></span>';
            $output .= '</div>';
        }
        $output .= '</div>';
        $output .= '<div class="builder-area-content cubewp-builder-group-widget-settings cubewp-expand-target">';
        $output .= self::cwpform_group_fields( $field );
        if ( $field['form_type'] != "single_layout" ) {
            $output .= self::cwpform_placeholder_field( $field );
        }
        $output .= self::cwpform_ui_field( $field );
        $output .= self::cwpform_type_field( $field );
        $output .= self::cwpform_required_field( $field );
        $output .= self::cwpform_sorting_field( $field );
        $output .= '</div>';
        $output .= '</div>';

        return $output;
    }

    private function cwpform_field_size( $field_size = array() ) {
        $field_unit = '1 / 1';
        if ( "size-1-4" == $field_size ) {
            $field_unit = "1 / 4";
        }
        if ( "size-1-3" == $field_size ) {
            $field_unit = "1 / 3";
        }
        if ( "size-1-2" == $field_size ) {
            $field_unit = "1 / 2";
        }
        if ( "size-2-3" == $field_size ) {
            $field_unit = "2 / 3";
        }
        if ( "size-3-4" == $field_size ) {
            $field_unit = "3 / 4";
        }
        if ( "size-1-1" == $field_size ) {
            $field_unit = "1 / 1";
        }

        return $field_unit;
    }

    /**
     * Method cwpform_group_fields
     *
     * @param array $field single field data
     *
     * @return string html
     * @since  1.0.0
     */
    private function cwpform_group_fields( array $field ) {
        $field_args['label'] = array(
            'class'       => 'group-field field-label',
            'label'       => esc_html__( "Label", "cubewp-framework" ),
            'name'       => 'label',
            'type'       => 'text',
            'value'       => $field['label'],
            'extra_attrs' => 'data-name="label"',
        );
        if ($field['type'] != 'repeating_field' && $field['type'] != 'author' && $field['type'] != 'messages_form') {
            $field_args['class'] = array(
                'class'       => 'group-field field-input-class',
                'label'       => esc_html__( "Input Class", "cubewp-framework" ),
                'name'       => 'class',
                'type'       => 'text',
                'value'       => $field['class'],
                'extra_attrs' => 'data-name="class"',
            );
        }
        $field_args['container_class'] = array(
            'class'       => 'group-field field-container-class',
            'label'       => esc_html__( "Container Class", "cubewp-framework" ),
            'name'       => 'container_class',
            'type'       => 'text',
            'value'       => $field['container_class'],
            'extra_attrs' => 'data-name="container_class"',
        );
        $field_args['name'] = array(
            'class'       => 'group-field field-name',
            'label'       => '',
            'name'       => 'name',
            'type'       => 'hidden',
            'value'       => $field['name'],
            'extra_attrs' => 'data-name="name"',
        );
        $input_attrs = apply_filters( 'cubewp/builder/cubes/fields', $field_args, $field );
        $output = '';
        foreach($input_attrs as $input_attr){
            if($input_attr['type'] == 'hidden'){
                $output .= call_user_func('cwp_render_hidden_input',$input_attr);
            }else{
                $field_type = $input_attr['type'];
                if(isset($input_attr['input_type']) && !empty($input_attr['input_type'])){
                    $input_attr['type'] = $input_attr['input_type'];
                }
                $output .= '<div class="cubewp-builder-group-widget-setting-field">';
                if(isset($input_attr['label']) && !empty($input_attr['label'])){
                    $output .= '<label>' . $input_attr['label'] . '</label>';
                }
                $output .= call_user_func('cwp_render_'.$field_type.'_input',$input_attr);
                $output .= '</div>';
            }
        }
        return $output;
    }

    /**
     * Method cwpform_placeholder_field
     *
     * @param array $field single field data
     *
     * @return string html
     * @since  1.0.0
     */
    private function cwpform_placeholder_field( array $field ) {
        $output = '';
        if ( ( $field['type'] == 'taxonomy' && empty( $field['filter_taxonomy'] ) ) || $field['name'] == 's' || $field['name'] == 'the_title' ) {
            $output      .= '<div class="cubewp-builder-group-widget-setting-field">
                <label>' . esc_html__( "Placeholder", "cubewp-framework" ) . '</label>';
            $input_attrs = array(
                'class'       => 'group-field field-required',
                'name'        => 'placeholder',
                'value'       => isset( $field['placeholder'] ) && ! empty( $field['placeholder'] ) ? $field['placeholder'] : '',
                'extra_attrs' => 'data-name="placeholder"',
            );
            $output      .= cwp_render_text_input( $input_attrs );
            $output      .= '</div>';
        }

        return $output;
    }

    /**
     * Method cwpform_ui_field
     *
     * @param array $field single field data
     *
     * @return string html
     * @since  1.0.0
     */
    private function cwpform_ui_field( array $field ) {
        $output     = '';
        $display_ui = isset( $field['display_ui'] ) && ! empty( $field['display_ui'] ) ? $field['display_ui'] : $field['type'];
        $field_size = isset( $field['field_size'] ) && ! empty( $field['field_size'] ) ? $field['field_size'] : 'size-1-1';
        if ( ( $field['form_type'] == 'search_filters' || $field['form_type'] == 'search_fields' || $field['form_type'] == 'post_type' ) && ( $field['type'] == 'taxonomy' && empty( $field['filter_taxonomy'] ) ) ) {
            $appearance  = isset( $field['appearance'] ) && ! empty( $field['appearance'] ) ? $field['appearance'] : $display_ui;
            $output      .= '<div class="cubewp-builder-group-widget-setting-field">
                <label>' . esc_html__( "Display UI", "cubewp-framework" ) . '</label>';
            $input_attrs = array(
                'class'       => 'group-field field-display_ui',
                'name'        => 'display_ui',
                'value'       => $appearance,
                'options'     => array(
                    'select'       => __( "Dropdown" ),
                    'multi_select' => __( "Multi Dropdown" ),
                    'checkbox'     => __( "checkbox" )
                ),
                'extra_attrs' => 'data-name="display_ui"',
            );
            $output      .= cwp_render_dropdown_input( $input_attrs );
            $output      .= '</div>';
            $output      .= '<div class="cubewp-builder-group-widget-setting-field">
                <label>' . esc_html__( "Select 2 UI", "cubewp-framework" ) . '</label>';
            $input_attrs = array(
                'class'       => 'group-field field-select2_ui',
                'name'        => 'select2_ui',
                'value'       => isset( $field['select2_ui'] ) && ! empty( $field['select2_ui'] ) ? $field['select2_ui'] : '0',
                'options'     => array( '0' => __( "No" ), '1' => __( "Yes" ) ),
                'extra_attrs' => 'data-name="select2_ui"',
            );
            $output      .= cwp_render_dropdown_input( $input_attrs );
            $output      .= '</div>';
        } else {
            $appearance  = ! empty( $field['filter_taxonomy'] ) && ! empty( $field['appearance'] ) ? $field['appearance'] : $display_ui;
            $input_attrs = array(
                'class'       => 'group-field field-display_ui',
                'name'        => 'display_ui',
                'value'       => $appearance,
                'extra_attrs' => 'data-name="display_ui"',
            );
            $output      .= cwp_render_hidden_input( $input_attrs );
        }
        if ( ( $field['form_type'] == 'search_filters' || $field['form_type'] == 'search_fields' || $field['form_type'] == 'post_type' ) && ! empty( $field['filter_taxonomy'] ) ) {
            $output      .= '<div class="cubewp-builder-group-widget-setting-field">
                <label>' . esc_html__( "Select 2 UI", "cubewp-framework" ) . '</label>';
            $input_attrs = array(
                'class'       => 'group-field field-select2_ui',
                'name'        => 'select2_ui',
                'value'       => isset( $field['select2_ui'] ) && ! empty( $field['select2_ui'] ) ? $field['select2_ui'] : '0',
                'options'     => array( '0' => __( "No" ), '1' => __( "Yes" ) ),
                'extra_attrs' => 'data-name="select2_ui"',
            );
            $output      .= cwp_render_dropdown_input( $input_attrs );
            $output      .= '</div>';
        }
        if ( $field['name'] == 'the_title' ) {
            $output      .= '<div class="cubewp-builder-group-widget-setting-field">
                <label>' . esc_html__( "Character Limit", "cubewp-framework" ) . '</label>';
            $input_attrs = array(
                'class'       => 'group-field field-required',
                'name'        => 'char_limit',
                'type'        => 'number',
                'value'       => isset( $field['char_limit'] ) && ! empty( $field['char_limit'] ) ? $field['char_limit'] : '',
                'extra_attrs' => 'data-name="char_limit"',
            );
            $output      .= cwp_render_text_input( $input_attrs );
            $output      .= '</div>';
        }
        $input_attrs = array(
            'class'       => 'group-field field-size',
            'name'        => 'field_size',
            'value'       => $field_size,
            'extra_attrs' => 'data-name="field_size"',
        );
        $output      .= cwp_render_hidden_input( $input_attrs );

        return $output;
    }

    /**
     * Method cwpform_type_field
     *
     * @param array $field single field data
     *
     * @return mixed
     * @since  1.0.0
     */
    private function cwpform_type_field( array $field ) {
        $input_attrs = array(
            'class'       => 'group-field field-type',
            'name'        => 'type',
            'value'       => isset( $field['type'] ) && ! empty( $field['type'] ) ? $field['type'] : '',
            'extra_attrs' => 'data-name="type"',
        );

        return cwp_render_hidden_input( $input_attrs );
    }

    /**
     * Method cwpform_required_field
     *
     * @param array $field single field data
     *
     * @return string html
     * @since  1.0.0
     */
    private function cwpform_required_field( array $field ) {
        $output = '';
        if (
                ( $field['form_type'] == 'post_type' && $field['type'] == 'taxonomy' && empty( $field['filter_taxonomy'] ) ) ||
                ( $field['form_type'] == 'post_type' && $field['name'] == 'the_title' ) ||
                ( $field['form_type'] == 'user' && $field['name'] != 'user_login' && $field['name'] != 'user_email' )
        ) {
            $default_required = 1;
            if ( $field['form_type'] == 'user' ) {
                $default_required = 0;
            }
            $output      .= '<div class="cubewp-builder-group-widget-setting-field">
                <label>' . esc_html__( "Required", "cubewp-framework" ) . '</label>';
            $input_attrs = array(
                'class'       => 'group-field field-required',
                'name'        => 'required',
                'value'       => isset( $field['required'] ) ? $field['required'] : $default_required,
                'options'     => array( '1' => __( "Required" ), '0' => __( "Not required" ) ),
                'extra_attrs' => 'data-name="required"',
            );
            $output      .= cwp_render_dropdown_input( $input_attrs );
            $output      .= '</div>';
        }
        if ($field['form_type'] == 'post_type' || $field['form_type'] == 'user') {
            $output      .= '<div class="cubewp-builder-group-widget-setting-field">
            <label>' . esc_html__( "Required Message", "cubewp-framework" ) . '</label>';
            $input_attrs = array(
            'class'       => 'group-field field-validation_msg',
            'name'        => 'validation_msg',
            'value'       => isset( $field['validation_msg'] ) ? $field['validation_msg'] : '',
            'extra_attrs' => 'data-name="validation_msg"',
            );
            $output      .= cwp_render_text_input( $input_attrs );
            $output      .= '</div>';
        }
        return $output;
    }

    /**
     * Method cwpform_sorting_field
     *
     * @param array $field single field data
     *
     * @return string html
     * @since  1.0.0
     */
    private function cwpform_sorting_field( array $field ) {
        $output = '';
        if ( $field['form_type'] == 'search_filters' && ( $field['type'] == 'number' || $field['type'] == 'date_picker' ) ) {
            $output      .= '<div class="cubewp-builder-group-widget-setting-field">
                <label>' . esc_html__( "Add this field in sorting filter", "cubewp-framework" ) . '</label>';
            $input_attrs = array(
                'class'       => 'group-field field-sorting',
                'name'        => 'sorting',
                'value'       => isset( $field['sorting'] ) && ! empty( $field['sorting'] ) ? $field['sorting'] : '',
                'options'     => array( '1' => __( "Yes" ), '0' => __( "No" ) ),
                'extra_attrs' => 'data-name="sorting"',
            );
            $output      .= cwp_render_dropdown_input( $input_attrs );
            $output      .= '</div>';
        }

        return $output;
    }

    /**
     * Method cwpform_form_setting_fields
     *
     * @param array  $form_fields
     * @param string $form_type
     *
     * @return string html
     * @since  1.0.0
     */
    public function cwpform_form_setting_fields( array $form_fields, string $form_type, $key = '' ) {
        $output = '<div class="cwpform-settings">';
        $output .= '<div class="cwpform-setting-label">';
       
        $output .= '<h2>' . esc_html__( "Form Settings", "cubewp-framework" ) . '</h2>';

        $output .= '</div>';
        $output .= '<div class="cwpform-setting-fields">';

        if ( $form_type != 'single_layout') {
            $output .= self::cubewp_form_builders_settings( $form_fields, $form_type );
        }
        if ( $form_type == 'search_filters' ) {
            $output .= self::cubewp_search_filters_builder_settings( $form_fields );
        }
        if ( $form_type != 'search_filters' && $form_type != 'search_fields' && $form_type != 'single_layout' ) {
            $form_fields['form_type'] = $form_type;
            $form_fields['post_type'] = $key;
            $output .= self::cubewp_form_builder_settings( $form_fields );
        }
        $output .= '</div>';
        $output .= '</div>';

        return $output;
    }

    public static function cubewp_form_builders_settings( $form_fields, $form_type ) {
        $output      = '<div class="cwpform-setting-field">';
        $output      .= '<label>' . esc_html__( "Form Container Class", "cubewp-framework" ) . '</label>';
        $input_attrs = array(
            'class'       => 'form-field',
            'name'        => 'form_container_class',
            'value'       => isset( $form_fields['form_container_class'] ) ? $form_fields['form_container_class'] : '',
            'extra_attrs' => 'data-name="form_container_class"',
        );
        $output      .= cwp_render_text_input( $input_attrs );
        $output      .= '</div>';
        $output      .= '<div class="cwpform-setting-field">';
        $output      .= '<label>' . esc_html__( "Form Class", "cubewp-framework" ) . '</label>';
        $input_attrs = array(
            'class'       => 'form-field',
            'name'        => 'form_class',
            'value'       => isset( $form_fields['form_class'] ) ? $form_fields['form_class'] : '',
            'extra_attrs' => 'data-name="form_class"',
        );
        $output      .= cwp_render_text_input( $input_attrs );
        $output      .= '</div>';
        $output      .= '<div class="cwpform-setting-field">';
        $output      .= '<label>' . esc_html__( "Form ID", "cubewp-framework" ) . '</label>';
        $input_attrs = array(
            'class'       => 'form-field',
            'name'        => 'form_id',
            'value'       => isset( $form_fields['form_id'] ) ? $form_fields['form_id'] : '',
            'extra_attrs' => 'data-name="form_id"',
        );
        $output      .= cwp_render_text_input( $input_attrs );
        $output      .= '</div>';
        global $cwpOptions;
        if ( isset( $cwpOptions['recaptcha'] ) && $cwpOptions['recaptcha'] == '1' ) {
            if ( $form_type != 'search_filters' && $form_type != 'search_fields' ) {
                $output      .= '<div class="cwpform-setting-field">';
                $output      .= '<label>' . esc_html__( "ReCaptcha", "cubewp-framework" ) . '</label>';
                $options     = array(
                    "disabled" => esc_html__( "Disabled", "cubewp-framework" ),
                    "enabled"  => esc_html__( "Enabled", "cubewp-framework" )
                );
                $input_attrs = array(
                    'class'       => 'form-field',
                    'name'        => 'form_recaptcha',
                    'value'       => isset( $form_fields['form_recaptcha'] ) ? $form_fields['form_recaptcha'] : 'disable',
                    'options'     => $options,
                    'extra_attrs' => 'data-name="form_recaptcha"',
                );
                $output      .= cwp_render_dropdown_input( $input_attrs );
                $output      .= '</div>';
            }
        }

        return $output;
    }


    public static function cubewp_search_filters_builder_settings( $form_fields ) {
        $output             = '<div class="cwpform-setting-field">';
        $output             .= '<label>' . esc_html__( "Enable Taxonomy-Based Conditional Fields in Filters", "cubewp-framework" ) . '</label>';
        $input_attrs = array(
            'name'        => 'conditional_filters',
            'class'       => 'form-field conditional_filters',
            'value'       => isset( $form_fields['conditional_filters'] ) ? $form_fields['conditional_filters'] : '0',
            'extra_attrs' => 'data-name="conditional_filters"',
        );
        $output             .= cwp_render_switch_input( $input_attrs );
        $output             .= '</div>';

        return $output;
    }

    public static function cubewp_form_builder_settings( $form_fields ) {
        $field_args = [];
        $field_args['submit_button_title'] = array(
            'class'       => 'form-field',
            'label'       => esc_html__( "Submit Button Title", "cubewp-framework" ),
            'name'       => 'submit_button_title',
            'type'       => 'text',
            'value'       => isset( $form_fields['submit_button_title'] ) ? $form_fields['submit_button_title'] : '',
            'extra_attrs' => 'data-name="submit_button_title"',
        );
        $field_args['submit_button_class'] = array(
            'class'       => 'form-field',
            'label'       => esc_html__( "Submit Button Class", "cubewp-framework" ),
            'name'       => 'submit_button_class',
            'type'       => 'text',
            'value'       => isset( $form_fields['submit_button_class'] ) ? $form_fields['submit_button_class'] : '',
            'extra_attrs' => 'data-name="submit_button_class"',
        );

        $input_attrs = apply_filters( 'cubewp/builder/settings/fields', $field_args, $form_fields );
        $output = '';
        foreach($input_attrs as $input_attr){
            if($input_attr['type'] == 'hidden'){
                $output .= call_user_func('cwp_render_hidden_input',$input_attr);
            }else{
                $field_type = $input_attr['type'];
                if(isset($input_attr['input_type']) && !empty($input_attr['input_type'])){
                    $input_attr['type'] = $input_attr['input_type'];
                }
                $output .= '<div class="cwpform-setting-field">';
                if(isset($input_attr['label']) && !empty($input_attr['label'])){
                    $output .= '<label>' . $input_attr['label'] . '</label>';
                }
                $output .= call_user_func('cwp_render_'.$field_type.'_input',$input_attr);
                $output .= '</div>';
            }
        }
        return $output;
    }

    /**
     * Method cwpform_section_popup_ui
     *
     * @param string $type form type
     *
     * @return void
     * @since  1.0.0
     */
    public function cwpform_section_popup_ui( string $type) {
        $fields = $this->cwpform_section_fields_list($type);
        ?>
        <div id="cwp-layout-builder-ovelay">
            <div class="layout-builder-content">
                <div class="layout-builder-false" style="display:none"></div>
                <div class="lp-submit-form-add-section">
                    <h2 class="new-section-title"><?php esc_html_e( 'New Section', 'cubewp-framework' ); ?></h2>
                    <form id="section_form" name="section-form" method="post">
                        <?php 
                        if(!empty($fields)){
                            foreach($fields as $input_attr){
                                if($input_attr['type'] == 'hidden'){
                                    echo call_user_func('cwp_render_hidden_input',$input_attr);
                                }else{
                                    $field_type = $input_attr['type'];
                                    if(isset($input_attr['input_type']) && !empty($input_attr['input_type'])){
                                        $input_attr['type'] = $input_attr['input_type'];
                                    }
                                    ?>
                                    <div class="section-form-field">
                                        <?php if(isset($input_attr['label']) && !empty($input_attr['label'])){ ?>
                                        <label for="section_class"><?php echo $input_attr['label']; ?></label>
                                        <?php } ?>
                                        <?php echo call_user_func('cwp_render_'.$field_type.'_input',$input_attr); ?>
                                    </div>
                                    <?php
                                }
                            }
                        }
                        ?>
                        <div class="form-section-form-btns">
                            <button type="button"
                                    class="cwpform-cancel-section button"><?php esc_html_e( 'Cancel', 'cubewp-framework' ); ?></button>
                            <button type="button"
                                    class="cwpform-save-section button button-primary"><?php esc_html_e( 'Add', 'cubewp-framework' ); ?></button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <?php
    }

    /**
     * Method cwpform_section_fields_list
     *
     * @param string $builder_type form type
     * @param string $form_relation post type
     *
     * @return array
     * @since  1.0.0
     */
    public function cwpform_section_fields_list( string $builder_type ) {

        $field_args = [];
        $field_args['form_relation'] = array(
            'class'   => 'section-field',
            'label'   => esc_html__( "", "cubewp-framework" ),
            'name'    => 'form_relation',
            'id'      => 'form_relation',
            'type'    => 'hidden',
        );
        $field_args['form_type'] = array(
            'class'   => 'section-field',
            'label'   => esc_html__( "", "cubewp-framework" ),
            'name'    => 'form_type',
            'id'      => 'form_type',
            'type'    => 'hidden',
        );
        $field_args['section_id'] = array(
            'class'   => 'section-field form-control',
            'label'   => esc_html__( "", "cubewp-framework" ),
            'name'    => 'section_id',
            'id'      => 'section_id',
            'type'    => 'hidden',
        );
        $field_args['section_title'] = array(
            'class'   => 'section-field form-control',
            'label'   => esc_html__( "Section Title", "cubewp-framework" ),
            'name'    => 'section_title',
            'id'      => 'section_title',
            'type'    => 'text',
        );
        $field_args['section_class'] = array(
            'class'   => 'section-field form-control',
            'label'   => esc_html__( "Section Class", "cubewp-framework" ),
            'name'    => 'section_class',
            'id'      => 'section_class',
            'type'    => 'text',
        );
        if ( $builder_type == 'single_layout' ) {
            $field_args['section_type'] = array(
                'class'   => 'section-field form-control',
                'label'   => esc_html__( "Section Type", "cubewp-framework" ),
                'name'    => 'section_type',
                'id'      => 'section_type',
                'type'    => 'dropdown',
                'options' => array(
                    'content' => esc_html__( 'Content Section', 'cubewp-framework' ),
                    'sidebar' => esc_html__( 'Sidebar Section', 'cubewp-framework' )
                ),
            );
        } else {
            $field_args['section_description'] = array(
                'class'   => 'section-field form-control',
                'label'   => esc_html__( "Section Description", "cubewp-framework" ),
                'name'    => 'section_description',
                'id'      => 'section_description',
                'type'    => 'textarea',
                'rows'    => '6',
            );
        }
        $input_attrs = apply_filters( 'cubewp/builder/section/fields', $field_args, $builder_type );

        return $input_attrs;
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
}