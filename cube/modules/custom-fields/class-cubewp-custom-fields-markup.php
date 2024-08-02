<?php
/**
 * complete custom fields markup.
 *
 * @version 1.0.9
 * @package cubewp/cube/classes
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * CubeWp_Custom_Fields_Markup
 */
class CubeWp_Custom_Fields_Markup
{

    /**
     * Method add_new_field
     *
     * @param array $FieldData
     *
     * @return string html
     * @since  1.0.0
     */
    public static function add_new_field($FieldData = array())
    {
        $defaults = array(
            'label' => '',
            'name' => 'cwp_field_' . rand( (int) 10000000, (int) 1000000000000 ),
            'type' => '',
            'description' => '',
            'map_use' => '',
            'default_value' => '',
            'minimum_value' => 0,
            'maximum_value' => 100,
            'steps_count' => 1,
            'file_types' => '',
            'upload_size' => '',
            'max_upload_files' => '',
            'placeholder' => '',
            'editor_media' => 0,
            'filter_post_types' => '',
            'filter_taxonomy' => '',
            'filter_user_roles' => '',
            'appearance' => '',
            'rel_attr' => 'do-follow',
            'current_user_posts' => '',
            'options' => '',
            'char_limit' => '',
            'admin_size' => '1/1',
            'multiple' => 0,
            'select2_ui' => 0,
            'auto_complete' => 0,
            'required' => '',
            'relationship' => 0,
            'rest_api' => 0,
            'validation_msg' => '',
            'id' => 'cwp_field_' . rand( (int) 10000000, (int) 1000000000000 ),
            'class' => '',
            'container_class' => '',
            'conditional' => '',
            'conditional_field' => '',
            'conditional_operator' => '',
            'conditional_value' => '',
            'sub_fields' => '',
            'fields_type' => '',
            'files_save' => 'ids',
            'files_save_separator' => 'array',
        );
        $FieldData = wp_parse_args($FieldData, $defaults);
        $field_name = !empty($FieldData['label']) ? $FieldData['label'] : esc_html__('Field Label', 'cubewp-framework');
        $field_settings = array();

        $closed_class = (isset($FieldData['label']) && $FieldData['label'] != '') ? 'closed' : '';
        $hide_class = (isset($FieldData['label']) && $FieldData['label'] != '') ? 'hidden' : '';
        $field_type = (isset($FieldData['type']) && $FieldData['type'] == '') ? 'text' : $FieldData['type'];
        $group_secure = isset($_GET['groupid']) ? cubewp_custom_field_group_secure($_GET['groupid']) : false;
        $secure_class = ($group_secure == true) ? 'group_visibility_secure' : '';
        $counter = isset($FieldData["counter"]) ? $FieldData["counter"] : 1;

        $html = '
        <div class="parent-field cwp-field-set cwp-add-form-feild ' . $secure_class . '">
            <div class="parent-field-header field-header ' . $closed_class . '">

                <div class="field-order-counter">
                <div class="field-order parent-field-order">
                        <svg xmlns="SVG namespace" width="22px" height="22px" viewBox="0 0 320 512" fill="#BFBFBF">
                            <path d="M40 352c-22.1 0-40 17.9-40 40l0 48c0 22.1 17.9 40 40 40l48 0c22.1 0 40-17.9 40-40l0-48c0-22.1-17.9-40-40-40l-48 0zm192 0c-22.1 0-40 17.9-40 40l0 48c0 22.1 17.9 40 40 40l48 0c22.1 0 40-17.9 40-40l0-48c0-22.1-17.9-40-40-40l-48 0zM40 320l48 0c22.1 0 40-17.9 40-40l0-48c0-22.1-17.9-40-40-40l-48 0c-22.1 0-40 17.9-40 40l0 48c0 22.1 17.9 40 40 40zM232 192c-22.1 0-40 17.9-40 40l0 48c0 22.1 17.9 40 40 40l48 0c22.1 0 40-17.9 40-40l0-48c0-22.1-17.9-40-40-40l-48 0zM40 160l48 0c22.1 0 40-17.9 40-40l0-48c0-22.1-17.9-40-40-40L40 32C17.9 32 0 49.9 0 72l0 48c0 22.1 17.9 40 40 40zM232 32c-22.1 0-40 17.9-40 40l0 48c0 22.1 17.9 40 40 40l48 0c22.1 0 40-17.9 40-40l0-48c0-22.1-17.9-40-40-40l-48 0z"></path>
                        </svg>
                    </div>
                    <div class="field-counter"><span>' . $counter . '</span></div>
                </div>
                <div class="field-title" data-label="' . esc_html__('Field Name', 'cubewp-framework') . '">
                    <div class="field-label">' . esc_html($field_name) . '</div>
                    <div class="field-slug">' . esc_html($FieldData['name']) . '</div>
                    <div class="field-type">' . esc_html($field_type) . '</div>
                </div>
                <div class="field-actions">

                    <a class="duplicate-field" data-field_type="posttype" data-field_id=' . esc_html($FieldData['name']) . ' data-fields_type=' . CubeWp_Custom_Fields_Processor::get_field_option_name() . ' href=""><span class="dashicons dashicons-admin-page"></span></a>';
        if (!$group_secure) {
            $html .= '<a class="remove-field" href="javascript:void(0);"><span class="dashicons dashicons-trash"></span></a>';
        }

        $html .= '   <a class="edit-field" href="javascript:void(0);"><span class="dashicons dashicons-arrow-down-alt2"></span></a>
            </div>
            </div>
            <div class="cwp-collapsible-inner ' . $hide_class . '">
            <table class="parent-fields">
            <tbody>';
        $field_settings['field_label'] = array(
            'label' => esc_html__('Field Label', 'cubewp-framework'),
            'name' => 'cwp[fields][' . $FieldData['name'] . '][label]',
            'type' => 'text',
            'id' => '',
            'class' => 'field-label',
            'placeholder' => esc_html__('Put your field label here', 'cubewp-framework'),
            'value' => $FieldData['label'],
            'extra_attrs' => 'maxlength=30 ',
            'required' => true,
        );
        $field_settings['field_name'] = array(
            'label' => esc_html__('Field Name', 'cubewp-framework'),
            'name' => 'cwp[fields][' . $FieldData['name'] . '][name]',
            'type' => 'text',
            'id' => '',
            'class' => 'cubewp-locked-field field-name',
            'placeholder' => esc_html__('Put your field name here', 'cubewp-framework'),
            'value' => $FieldData['name'],
            'extra_attrs' => 'maxlength=20 ',
            'required' => true,
        );
        $field_settings['field_type'] = array(
            'label' => esc_html__('Field Type', 'cubewp-framework'),
            'name' => 'cwp[fields][' . $FieldData['name'] . '][type]',
            'id' => '',
            'type' => 'dropdown',
            'options' => self::cwp_form_field_types( $FieldData['fields_type'] ),
            'value' => $FieldData['type'],
            'placeholder' => '',
            'option-class' => 'form-option option',
            'class' => 'field-type',
            'required' => true,
        );
        $field_settings['field_map_use'] = array(
            'label' => esc_html__('Do you want to use this field for archive map?', 'cubewp-framework'),
            'name' => 'cwp[fields][' . $FieldData['name'] . '][map_use]',
            'value' => '1',
            'placeholder' => '',
            'type' => 'text',
            'checked' => $FieldData['map_use'],
            'type_input' => 'checkbox',
            'class' => 'field-map_use-checkbox checkbox cwp-switch-check',
            'id' => 'field-map-use-' . str_replace('cwp_field_', '', $FieldData['name']),
            'tr_class' => 'conditional-field',
            'tr_extra_attr' => 'data-equal="google_address"',
            'extra_label' => esc_html__('Use for Archive Map', 'cubewp-framework'),
        );
        $field_settings['field_desc'] = array(
            'label' => esc_html__('description', 'cubewp-framework'),
            'name' => 'cwp[fields][' . $FieldData['name'] . '][description]',
            'type' => 'textarea',
            'id' => '',
            'placeholder' => esc_html__('Write description about this field', 'cubewp-framework'),
            'value' => $FieldData['description'],
        );
        $field_settings['field_default_value'] = array(
            'label' => esc_html__('Default Value', 'cubewp-framework'),
            'name' => 'cwp[fields][' . $FieldData['name'] . '][default_value]',
            'type' => 'text',
            'id' => '',
            'placeholder' => esc_html__('You can set default value eg: for color "#eee", for range field "50" and for text "anything..." ', 'cubewp-framework'),
            'value' => $FieldData['default_value'],
            'tr_class' => 'conditional-field',
            'class' => 'field-default-value',
            'id' => '',
            'tr_extra_attr' => 'data-equal="text,textarea,color,range"',
        );
        $field_settings['field_minimum_value'] = array(
            'label' => esc_html__('Minimum Value', 'cubewp-framework'),
            'name' => 'cwp[fields][' . $FieldData['name'] . '][minimum_value]',
            'type' => 'text',
            'id' => '',
            'placeholder' => esc_html__('Minimum Value', 'cubewp-framework'),
            'value' => $FieldData['minimum_value'],
            'tr_class' => 'conditional-field',
            'class' => 'field-minimum-value',
            'tr_extra_attr' => 'data-equal="range"',
        );
        $field_settings['field_maximum_value'] = array(
            'label' => esc_html__('Maximum Value', 'cubewp-framework'),
            'name' => 'cwp[fields][' . $FieldData['name'] . '][maximum_value]',
            'type' => 'text',
            'id' => '',
            'placeholder' => esc_html__('Maximum Value', 'cubewp-framework'),
            'value' => $FieldData['maximum_value'],
            'tr_class' => 'conditional-field',
            'class' => 'field-maximum-value',
            'tr_extra_attr' => 'data-equal="range"',
        );
        $field_settings['field_steps_count'] = array(
            'label' => esc_html__('Step', 'cubewp-framework'),
            'name' => 'cwp[fields][' . $FieldData['name'] . '][steps_count]',
            'type' => 'text',
            'id' => '',
            'placeholder' => esc_html__('Step', 'cubewp-framework'),
            'value' => $FieldData['steps_count'],
            'tr_class' => 'conditional-field',
            'class' => 'field-step-count',
            'tr_extra_attr' => 'data-equal="range"',
        );
        $field_settings['field_file_types'] = array(
            'label' => esc_html__('File Types (MIME)', 'cubewp-framework'),
            'name' => 'cwp[fields][' . $FieldData['name'] . '][file_types]',
            'type' => 'text',
            'id' => '',
            'placeholder' => esc_html__('Enter Allowed File Types. EG: (image/png,application/zip)', 'cubewp-framework'),
            'value' => $FieldData['file_types'],
            'tr_class' => 'conditional-field',
            'class' => 'field-file-types',
            'tr_extra_attr' => 'data-equal="gallery,file,image"',
        );
        $field_settings['field_upload_size'] = array(
            'label' => esc_html__('Upload Size', 'cubewp-framework'),
            'name' => 'cwp[fields][' . $FieldData['name'] . '][upload_size]',
            'type' => 'text',
            'id' => '',
            'placeholder' => esc_html__('Enter Maximum Upload Size In MBs. EG: 5', 'cubewp-framework'),
            'value' => $FieldData['upload_size'],
            'tr_class' => 'conditional-field',
            'class' => 'field-file-types',
            'tr_extra_attr' => 'data-equal="gallery,file,image"',
        );
        $field_settings['field_max_upload_files'] = array(
            'label' => esc_html__('Max Number Of Images', 'cubewp-framework'),
            'name' => 'cwp[fields][' . $FieldData['name'] . '][max_upload_files]',
            'type' => 'text',
            'id' => '',
            'placeholder' => esc_html__('Enter Maximum Number of Images Can Upload. EG: 4', 'cubewp-framework'),
            'value' => $FieldData['max_upload_files'],
            'tr_class' => 'conditional-field',
            'class' => 'field-max_upload_files',
            'tr_extra_attr' => 'data-equal="gallery"',
        );
        $field_settings['field_placeholder'] = array(
            'label' => esc_html__('Placeholder', 'cubewp-framework'),
            'name' => 'cwp[fields][' . $FieldData['name'] . '][placeholder]',
            'type' => 'text',
            'id' => '',
            'placeholder' => esc_html__('Put your field placeholder here', 'cubewp-framework'),
            'value' => $FieldData['placeholder'],
            'tr_class' => 'conditional-field',
            'class' => 'field-placeholder',
            'id' => '',
            'tr_extra_attr' => 'data-equal="text,textarea,number,email,url,password,map,dropdown,taxonomy,post,user"',
        );
        $field_settings['field_options'] = array(
            'label' => esc_html__('Options', 'cubewp-framework'),
            'name' => 'cwp[fields][' . $FieldData['name'] . ']',
            'type' => 'options',
            'id' => '',
            'options' => $FieldData['options'],
            'default_value' => $FieldData['default_value'],
            'tr_class' => 'field-options-row conditional-field',
            'tr_extra_attr' => 'data-equal="dropdown,checkbox,radio"',
        );
        $field_settings['field_char_limit'] = array(
            'label' => esc_html__('Character Limit', 'cubewp-framework'),
            'name' => 'cwp[fields][' . $FieldData['name'] . '][char_limit]',
            'type' => 'number',
            'placeholder' => esc_html__('Put your field character limit here', 'cubewp-framework'),
            'value' => $FieldData['char_limit'],
            'tr_class' => 'conditional-field',
            'class' => 'field-char-limit',
            'id' => '',
            'tr_extra_attr' => 'data-equal="text,textarea,email,url,password,number"',
        );
        $field_settings['field_multiple_values'] = array(
            'label' => esc_html__('Multiple', 'cubewp-framework'),
            'name' => 'cwp[fields][' . $FieldData['name'] . '][multiple]',
            'value' => '1',
            'placeholder' => '',
            'type' => 'text',
            'checked' => $FieldData['multiple'],
            'type_input' => 'checkbox',
            'class' => 'field-multiple-checkbox checkbox cwp-switch-check',
            'id' => 'field-multiple-' . str_replace('cwp_field_', '', $FieldData['name']),
            'tr_class' => 'conditional-field',
            'tr_extra_attr' => 'data-equal="dropdown"',
            'extra_label' => esc_html__('Multiple Values', 'cubewp-framework'),
        );
        $field_settings['field_editor_media'] = array(
            'label' => esc_html__('Allow Media', 'cubewp-framework'),
            'name' => 'cwp[fields][' . $FieldData['name'] . '][editor_media]',
            'value' => '1',
            'placeholder' => '',
            'type' => 'text',
            'checked' => $FieldData['editor_media'],
            'type_input' => 'checkbox',
            'class' => 'field-editor-media-checkbox checkbox cwp-switch-check',
            'id' => 'field-allow-media-' . str_replace('cwp_field_', '', $FieldData['name']),
            'tr_class' => 'conditional-field',
            'tr_extra_attr' => 'data-equal="wysiwyg_editor"',
            'extra_label' => esc_html__('Allow Media', 'cubewp-framework'),
        );
        $field_settings['field_filter_post_types'] = array(
            'label' => esc_html__('Filter by Post Types', 'cubewp-framework'),
            'name' => 'cwp[fields][' . $FieldData['name'] . '][filter_post_types]',
            'type' => 'dropdown',
            'id' => '',
            'options' => cwp_post_types(),
            'value' => $FieldData['filter_post_types'],
            'placeholder' => esc_html__('Select Post Type', 'cubewp-framework'),
            'option-class' => 'form-option option',
            'class' => 'field-filter-post-types',
            'tr_class' => 'conditional-field',
            'tr_extra_attr' => 'data-equal="post"',
            'required' => true,
            'validation_msg' => esc_html__('Please select Post-Type', 'cubewp-framework'),
        );
        $field_settings['field_filter_taxonomy'] = array(
            'label' => esc_html__('Filter by Taxonomy', 'cubewp-framework'),
            'name' => 'cwp[fields][' . $FieldData['name'] . '][filter_taxonomy]',
            'type' => 'dropdown',
            'id' => '',
            'options' => cwp_taxonomies(),
            'value' => $FieldData['filter_taxonomy'],
            'placeholder' => esc_html__('Select Taxonomy', 'cubewp-framework'),
            'option-class' => 'form-option option',
            'class' => 'field-filter-taxonomy',
            'tr_class' => 'conditional-field',
            'tr_extra_attr' => 'data-equal="taxonomy"',
            'required' => true,
            'validation_msg' => esc_html__('Please select taxonomy', 'cubewp-framework'),
        );
        $field_settings['field_filter_user_roles'] = array(
            'label' => esc_html__('Filter by role', 'cubewp-framework'),
            'name' => 'cwp[fields][' . $FieldData['name'] . '][filter_user_roles]',
            'type' => 'dropdown',
            'id' => '',
            'options' => cwp_get_user_roles_name(),
            'value' => $FieldData['filter_user_roles'],
            'placeholder' => esc_html__('Select User Role', 'cubewp-framework'),
            'option-class' => 'form-option option',
            'class' => 'field-filter-user-role',
            'tr_class' => 'conditional-field',
            'tr_extra_attr' => 'data-equal="user"',
			'required' => true,
            'validation_msg' => esc_html__('Please select role', 'cubewp-framework'),
        );
        $field_settings['field_rel_attr'] = array(
		    'label' => esc_html__('Link Behavior', 'cubewp-framework'),
		    'name' => 'cwp[fields][' . $FieldData['name'] . '][rel_attr]',
		    'type' => 'dropdown',
		    'id' => 'field-rel_attr-' . str_replace('cwp_field_', '', $FieldData['name']),
		    'options' => array(
			    'do-follow' => __('Follow (Search engines will follow the link)', 'cubewp-framework'),
			    'nofollow' => __('No Follow (Instructs search engines not to follow the link)', 'cubewp-framework'),
			    'external' => __('External (Indicates that the linked document is located on a different website)', 'cubewp-framework'),
		    ),
		    'value' => $FieldData['rel_attr'],
		    'placeholder' => '',
		    'option-class' => 'form-option option',
		    'class' => 'field-rel_attr',
		    'tr_class' => 'conditional-field',
		    'tr_extra_attr' => 'data-equal="url"',
		    'required' => true,
		    'validation_msg' => esc_html__('Please select link behavior', 'cubewp-framework'),
	    );
        $field_settings['field_appearance'] = array(
            'label' => esc_html__('Field Appearance', 'cubewp-framework'),
            'name' => 'cwp[fields][' . $FieldData['name'] . '][appearance]',
            'type' => 'dropdown',
            'id' => 'field-appearance-' . str_replace('cwp_field_', '', $FieldData['name']),
            'options' => array(
                'select' => __('Dropdown', 'cubewp-framework'),
                'multi_select' => __('Multi Dropdown', 'cubewp-framework'),
                'checkbox' => __('Checkbox', 'cubewp-framework'),
            ),
            'value' => $FieldData['appearance'],
            'placeholder' => '',
            'option-class' => 'form-option option',
            'class' => 'field-appearance',
            'tr_class' => 'conditional-field hide-field-on-selection',
            'tr_extra_attr' => 'data-equal="post,user,taxonomy" data-hide-option="checkbox" data-hide-field="field-placeholder"',
            'required' => true,
            'validation_msg' => esc_html__('Please select appearance', 'cubewp-framework'),
        );
        $field_settings['field_current_user_posts'] = array(
		    'label' => esc_html__('Logged-in User Posts', 'cubewp-framework'),
		    'name' => 'cwp[fields][' . $FieldData['name'] . '][current_user_posts]',
		    'value' => '1',
		    'placeholder' => '',
		    'type' => 'text',
		    'checked' => $FieldData['current_user_posts'],
		    'type_input' => 'checkbox',
		    'class' => 'field-multiple-checkbox checkbox cwp-switch-check',
		    'id' => 'field-current-user-posts-ui-' . str_replace('cwp_field_', '', $FieldData['name']),
		    'tr_class' => 'conditional-field',
		    'tr_extra_attr' => 'data-equal="post"',
		    'extra_label' => esc_html__('LoggedIn User Posts', 'cubewp-framework'),
		    'tooltip' => "Enable this option if you want to fetch posts which are submitted by currently loggedin user.",
	    );
        $field_settings['field_select2_ui'] = array(
            'label' => esc_html__('Select2 UI', 'cubewp-framework'),
            'name' => 'cwp[fields][' . $FieldData['name'] . '][select2_ui]',
            'value' => '1',
            'placeholder' => '',
            'type' => 'text',
            'checked' => $FieldData['select2_ui'],
            'type_input' => 'checkbox',
            'class' => 'field-multiple-checkbox checkbox cwp-switch-check',
            'id' => 'field-select-ui-' . str_replace('cwp_field_', '', $FieldData['name']),
            'tr_class' => 'conditional-field',
            'tr_extra_attr' => 'data-equal="dropdown,post,user,taxonomy"',
            'extra_label' => esc_html__('Select2 UI', 'cubewp-framework'),
            'tooltip' => "This option will add select2 UI js on your dropdown field only",
        );
        $field_settings['field_autocomplete_ui'] = array(
            'label' => esc_html__('Autocomplete (Ajax)', 'cubewp-framework'),
            'name' => 'cwp[fields][' . $FieldData['name'] . '][auto_complete]',
            'value' => '1',
            'placeholder' => '',
            'type' => 'text',
            'checked' => $FieldData['auto_complete'],
            'type_input' => 'checkbox',
            'class' => 'field-multiple-checkbox checkbox cwp-switch-check',
            'id' => 'field-auto_complete-' . str_replace('cwp_field_', '', $FieldData['name']),
            'tr_class' => 'conditional-field',
            'tr_extra_attr' => 'data-equal="post,user,taxonomy"',
            'extra_label' => esc_html__('Autocomplete', 'cubewp-framework'),
            'tooltip' => "If you enable this option, Then field will appear as text and it will fetch result on user input.",
        );
        $field_settings['field_relationship'] = array(
            'label' => esc_html__('Relationship ', 'cubewp-framework'),
            'name' => 'cwp[fields][' . $FieldData['name'] . '][relationship]',
            'value' => '1',
            'placeholder' => '',
            'type' => 'text',
            'checked' => $FieldData['relationship'],
            'type_input' => 'checkbox',
            'class' => 'field-required-checkbox checkbox cwp-switch-check',
            'id' => 'field-relation-' . str_replace('cwp_field_', '', $FieldData['name']),
            'tr_class' => 'conditional-field',
            'tr_extra_attr' => 'data-equal="post,user"',
            'extra_label' => esc_html__('Relationship', 'cubewp-framework'),
            'tooltip' => "Enable a complex relationship for a two-way connection between post-to-post or post-to-user.
                                When the user selects a value in this field, it will create a relation with the selected value, whether it is post or user,
                                and a relationship field will appear on the selected post or user edit page.",
        );
        $field_settings['field_validation'] = array(
            'label' => esc_html__('Required', 'cubewp-framework'),
            'name' => 'cwp[fields][' . $FieldData['name'] . '][required]',
            'value' => '1',
            'placeholder' => '',
            'type' => 'text',
            'id' => '',
            'checked' => $FieldData['required'],
            'type_input' => 'checkbox',
            'class' => 'field-required-checkbox checkbox cwp-switch-check',
            'id' => 'field-required-' . str_replace('cwp_field_', '', $FieldData['name']),
            'tr_class' => 'conditional-field',
            'tr_extra_attr' => 'data-not_equal="gallery,repeating_field,switch"',
            'extra_label' => esc_html__('Required', 'cubewp-framework'),
        );
        $trclass = 'validation-msg-row cwp-hide-row conditional-field';
        if (isset($FieldData['required']) && $FieldData['required'] == 1) {
            $trclass = 'validation-msg-row conditional-field';
        }
        $field_settings['field_validation_msg'] = array(
            'label' => esc_html__('Required error message', 'cubewp-framework'),
            'name' => 'cwp[fields][' . $FieldData['name'] . '][validation_msg]',
            'value' => $FieldData['validation_msg'],
            'placeholder' => esc_html__('Required error message', 'cubewp-framework'),
            'type' => 'text',
            'id' => '',
            'type_input' => 'text',
            'class' => 'field-validation-msg',
            'id' => '',
            'tr_class' => $trclass,
            'tr_extra_attr' => 'data-not_equal="gallery,repeating_field,switch"',
        );
        $field_settings['field_files_save'] = array(
            'label' => esc_html__('Save Format', 'cubewp-framework'),
            'name' => 'cwp[fields][' . $FieldData['name'] . '][files_save]',
            'id' => '',
            'type' => 'dropdown',
            'options' => array(
                'ids' => esc_html__("ID's", "cubewp-framework"),
                'urls' => esc_html__("URLs", "cubewp-framework"),
            ),
            'placeholder' => '',
            'value' => $FieldData['files_save'],
            'option-class' => 'form-option option',
            'class' => 'field-files-save',
            'tr_class' => 'conditional-field',
            'tr_extra_attr' => 'data-equal="file,image,gallery"',
            'extra_label' => esc_html__('Save Format', 'cubewp-framework'),
        );
        $save_separators = array(
            'array' => esc_html__("Array", "cubewp-framework"),
            ',' => esc_html__("String separated by , (Comma)", "cubewp-framework"),
            '|' => esc_html__("String separated by | (Pipe)", "cubewp-framework"),
            ':' => esc_html__("String separated by : (Colon)", "cubewp-framework"),
            ';' => esc_html__("String separated by ; (Semicolon)", "cubewp-framework"),
        );
        $save_separators = apply_filters( 'cubewp/custom/field/save/format/separators', $save_separators, $FieldData['files_save_separator'] );
        $field_settings['field_files_save_separator'] = array(
            'label' => esc_html__('Save Format Separator', 'cubewp-framework'),
            'name' => 'cwp[fields][' . $FieldData['name'] . '][files_save_separator]',
            'id' => '',
            'type' => 'dropdown',
            'options' => $save_separators,
            'placeholder' => '',
            'option-class' => 'form-option option',
            'value' => $FieldData['files_save_separator'],
            'class' => 'field-files-save-separator',
            'tr_class' => 'conditional-field',
            'tr_extra_attr' => 'data-equal="gallery,dropdown,checkbox"',
            'extra_label' => esc_html__('Save Format Separator', 'cubewp-framework'),
        );
        $field_settings['field_id'] = array(
            'label' => esc_html__('ID', 'cubewp-framework'),
            'name' => 'cwp[fields][' . $FieldData['name'] . '][id]',
            'type' => 'text',
            'id' => '',
            'class' => 'field-id',
            'placeholder' => esc_html__('ID for css', 'cubewp-framework'),
            'value' => $FieldData['id'],
            'required' => true,
            'validation_msg' => esc_html__('Please enter id for css and JS purpose', 'cubewp-framework'),
        );
        $field_settings['field_class'] = array(
            'label' => esc_html__('Class', 'cubewp-framework'),
            'name' => 'cwp[fields][' . $FieldData['name'] . '][class]',
            'type' => 'text',
            'id' => '',
            'class' => 'field-class',
            'placeholder' => esc_html__('Class for css', 'cubewp-framework'),
            'value' => $FieldData['class'],
            'tr_class' => 'conditional-field',
            'tr_extra_attr' => 'data-not_equal="repeating_field"',
        );
        $field_settings['field_container_class'] = array(
            'label' => esc_html__('Container Class', 'cubewp-framework'),
            'name' => 'cwp[fields][' . $FieldData['name'] . '][container_class]',
            'type' => 'text',
            'id' => '',
            'class' => 'field-container-class',
            'placeholder' => esc_html__('Container Class for css', 'cubewp-framework'),
            'value' => $FieldData['container_class'],
        );
        $field_settings['field_admin_size'] = array(
            'label' => esc_html__('Metabox Field Size', 'cubewp-framework'),
            'name' => 'cwp[fields][' . $FieldData['name'] . '][admin_size]',
            'id' => '',
            'type' => 'dropdown',
            'options' => array(
                '1/1' => esc_html__("1/1", "cubewp-framework"),
                '1/2' => esc_html__("1/2", "cubewp-framework"),
            ),
            'placeholder' => '',
            'value' => $FieldData['admin_size'],
            'option-class' => 'form-option option',
            'class' => 'field-admin-size',
        );
        $field_settings['field_rest_api'] = array(
            'label' => esc_html__('Show in REST API', 'cubewp-framework'),
            'name' => 'cwp[fields][' . $FieldData['name'] . '][rest_api]',
            'value' => '1',
            'placeholder' => '',
            'type' => 'text',
            'id' => '',
            'checked' => $FieldData['rest_api'],
            'type_input' => 'checkbox',
            'class' => 'field-rest_api-checkbox checkbox cwp-switch-check',
            'id' => 'field-rest_api-' . str_replace('cwp_field_', '', $FieldData['name']),
            'tr_class' => '',
            'tr_extra_attr' => 'data-not_equal=""',
            'extra_label' => esc_html__('Show in REST API', 'cubewp-framework'),
        );
        $field_settings['field_conditional'] = array(
            'label' => esc_html__('Conditional Logic', 'cubewp-framework'),
            'name' => 'cwp[fields][' . $FieldData['name'] . '][conditional]',
            'value' => '1',
            'id' => 'field-conditional-' . str_replace('cwp_field_', '', $FieldData['name']),
            'placeholder' => '',
            'type' => 'text',
            'checked' => $FieldData['conditional'],
            'type_input' => 'checkbox',
            'class' => 'field-conditional cwp-switch-check',
            'tr_class' => 'conditional-field',
            'tr_extra_attr' => 'data-not_equal="repeating_field"',
            'extra_label' => esc_html__('Conditional', 'cubewp-framework'),
        );
        $conditional_rule_hide_row = 'cwp-hide-row';
        if (isset($FieldData['conditional']) && $FieldData['conditional'] == 1) {
            $conditional_rule_hide_row = '';
        }
        $field_settings['field_conditional_rule'] = array(
            'label' => esc_html__('Show this field if', 'cubewp-framework'),
            'name' => 'cwp[fields][' . $FieldData['name'] . '][conditional_field]',
            'name_operator' => 'cwp[fields][' . $FieldData['name'] . '][conditional_operator]',
            'name_value' => 'cwp[fields][' . $FieldData['name'] . '][conditional_value]',
            'type' => 'conditional',
            'id' => '',
            'value_operator' => $FieldData['conditional_operator'],
            'value_value' => $FieldData['conditional_value'],
            'options' => array(
                '!empty' => esc_html__('Has any value', 'cubewp-framework'),
                'empty' => esc_html__('Has no value', 'cubewp-framework'),
                '==' => esc_html__('Value is equal to', 'cubewp-framework'),
                '!=' => esc_html__('Value is not equal to', 'cubewp-framework'),
            ),
            'default_value' => $FieldData['default_value'],
            'tr_class' => 'conditional-rule ' . esc_attr($conditional_rule_hide_row),
            'select_extra_attr' => 'data-value="' . $FieldData['conditional_field'] . '"',
            'class' => '',
            'select_class' => 'field-appearance',
        );
        $field_settings = apply_filters("cubewp/custom_fields/{$FieldData['fields_type']}/fields", $field_settings, $FieldData);
        foreach ($field_settings as $field_setting) {
            $fields = apply_filters("cubewp/admin/{$field_setting['type']}/customfield", '', $field_setting);
            $html .= apply_filters("cubewp/custom_fields/{$field_setting['type']}/output", $fields, $field_setting, $FieldData);
        }

        $html .= '<tr class="sub-fields-holder">';
        $html .= '<td>';
        $html .= '<label>' . esc_html__('Sub Fields', 'cubewp-framework') . '</label>';
        $html .= '</td>';
        $html .= '<td>';
        $html .= '<div class="sub-fields">';
        $html .= CubeWp_Custom_Fields_Processor::get_sub_fields($FieldData['sub_fields'], $FieldData['name'], $FieldData['fields_type']);
        $html .= '</div>';
        $html .= '<button class="button button-primary add-sub-field" data-parent_field="' . $FieldData['name'] . '" type="button">' . esc_html__('Add Sub Field', 'cubewp-framework') . '</button>';
        $html .= '</td>';
        $html .= '</tr>';
        $html .= '</tbody>
                </table>
            </div>
        </div>';
        return apply_filters('cwp_custom_field_settings_html', $html, $FieldData);
    }

    /**
     * Method add_new_sub_field
     *
     * @param string $parent_field
     * @param array $FieldData
     *
     * @return string html
     * @since  1.0.0
     */
    public static function add_new_sub_field($FieldData = array(), $parent_field = '')
    {
        $defaults = array(
            'label' => '',
            'name' => 'cwp_field_' . rand(10000000, 1000000000000),
            'type' => '',
            'description' => '',
            'default_value' => '',
            'minimum_value' => 0,
            'maximum_value' => 100,
            'steps_count' => 1,
            'placeholder' => '',
            'options' => '',
            'upload_size' => '',
            'max_upload_files' => '',
            'counter' => 1,
            'char_limit' => '',
            'multiple' => 0,
            'rel_attr' => 'do-follow',
            'select2_ui' => 0,
            'auto_complete' => 0,
            'filter_post_types' => '',
            'current_user_posts' => '',
            'file_types' => '',
            'appearance' => '',
            'required' => '',
            'validation_msg' => '',
            'id' => 'cwp_field_' . rand(10000000, 1000000000000),
            'class' => '',
            'files_save' => 'ids',
            'files_save_separator' => 'array',
        );
        $FieldData = wp_parse_args($FieldData, $defaults);
        $field_settings = array();

        $field_settings['field_label'] = array(
            'label' => esc_html__('Field Label', 'cubewp-framework'),
            'name' => 'cwp[sub_fields][' . $parent_field . '][' . $FieldData['name'] . '][label]',
            'type' => 'text',
            'id' => '',
            'class' => 'field-label',
            'placeholder' => esc_html__('Put your field label here', 'cubewp-framework'),
            'value' => $FieldData['label'],
            'extra_attrs' => 'maxlength=30 ',
            'required' => true,
        );
        $field_settings['field_name'] = array(
            'label' => esc_html__('Field Name', 'cubewp-framework'),
            'name' => 'cwp[sub_fields][' . $parent_field . '][' . $FieldData['name'] . '][name]',
            'type' => 'text',
            'id' => '',
            'class' => 'cubewp-locked-field field-name',
            'placeholder' => esc_html__('Put your field name here', 'cubewp-framework'),
            'value' => $FieldData['name'],
            'extra_attrs' => 'maxlength=20 ',
            'required' => true,
        );
        $field_settings['field_type'] = array(
            'label' => esc_html__('Field Type', 'cubewp-framework'),
            'id' => '',
            'name' => 'cwp[sub_fields][' . $parent_field . '][' . $FieldData['name'] . '][type]',
            'type' => 'dropdown',
            'options' => self::cwp_form_sub_field_types(),
            'value' => $FieldData['type'],
            'placeholder' => '',
            'option-class' => 'form-option option',
            'class' => 'field-type',
            'required' => true,
        );
        $field_settings['field_desc'] = array(
            'label' => esc_html__('description', 'cubewp-framework'),
            'id' => '',
            'name' => 'cwp[sub_fields][' . $parent_field . '][' . $FieldData['name'] . '][description]',
            'type' => 'textarea',
            'placeholder' => esc_html__('Write description about this field', 'cubewp-framework'),
            'value' => $FieldData['description'],
        );
        $field_settings['field_default_value'] = array(
            'label' => esc_html__('Default Value', 'cubewp-framework'),
            'name' => 'cwp[sub_fields][' . $parent_field . '][' . $FieldData['name'] . '][default_value]',
            'type' => 'text',
            'placeholder' => esc_html__('You can set default value eg: for color "#eee", for range field "50" and for text "anything..." ', 'cubewp-framework'),
            'value' => $FieldData['default_value'],
            'tr_class' => 'conditional-field',
            'class' => 'field-default-value',
            'id' => '',
            'tr_extra_attr' => 'data-equal="text,textarea,color,range"',
        );
        $field_settings['field_minimum_value'] = array(
            'label' => esc_html__('Minimum Value', 'cubewp-framework'),
            'name' => 'cwp[sub_fields][' . $parent_field . '][' . $FieldData['name'] . '][minimum_value]',
            'type' => 'text',
            'id' => '',
            'placeholder' => esc_html__('Minimum Value', 'cubewp-framework'),
            'value' => $FieldData['minimum_value'],
            'tr_class' => 'conditional-field',
            'class' => 'field-minimum-value',
            'tr_extra_attr' => 'data-equal="range"',
        );
        $field_settings['field_maximum_value'] = array(
            'label' => esc_html__('Maximum Value', 'cubewp-framework'),
            'name' => 'cwp[sub_fields][' . $parent_field . '][' . $FieldData['name'] . '][maximum_value]',
            'type' => 'text',
            'id' => '',
            'placeholder' => esc_html__('Maximum Value', 'cubewp-framework'),
            'value' => $FieldData['maximum_value'],
            'tr_class' => 'conditional-field',
            'class' => 'field-maximum-value',
            'tr_extra_attr' => 'data-equal="range"',
        );
        $field_settings['field_steps_count'] = array(
            'label' => esc_html__('Step', 'cubewp-framework'),
            'name' => 'cwp[sub_fields][' . $parent_field . '][' . $FieldData['name'] . '][steps_count]',
            'type' => 'text',
            'id' => '',
            'placeholder' => esc_html__('Step', 'cubewp-framework'),
            'value' => $FieldData['steps_count'],
            'tr_class' => 'conditional-field',
            'class' => 'field-step-count',
            'tr_extra_attr' => 'data-equal="range"',
        );
        $field_settings['field_file_types'] = array(
            'label' => esc_html__('File Types (MIME)', 'cubewp-framework'),
            'name' => 'cwp[sub_fields][' . $parent_field . '][' . $FieldData['name'] . '][file_types]',
            'type' => 'text',
            'id' => '',
            'placeholder' => esc_html__('Enter Allowed File Types. EG: (image/png,application/zip)', 'cubewp-framework'),
            'value' => $FieldData['file_types'],
            'tr_class' => 'conditional-field',
            'class' => 'field-file-types',
            'tr_extra_attr' => 'data-equal="gallery,file,image"',
        );
        $field_settings['field_upload_size'] = array(
            'label' => esc_html__('Upload Size', 'cubewp-framework'),
            'name' => 'cwp[sub_fields][' . $parent_field . '][' . $FieldData['name'] . '][upload_size]',
            'type' => 'text',
            'id' => '',
            'placeholder' => esc_html__('Enter Maximum Upload Size In MBs. EG: 5', 'cubewp-framework'),
            'value' => $FieldData['upload_size'],
            'tr_class' => 'conditional-field',
            'class' => 'field-file-types',
            'tr_extra_attr' => 'data-equal="gallery,file,image"',
        );
        $field_settings['field_max_upload_files'] = array(
            'label' => esc_html__('Max Number Of Images', 'cubewp-framework'),
            'name' => 'cwp[sub_fields][' . $parent_field . '][' . $FieldData['name'] . '][max_upload_files]',
            'type' => 'text',
            'id' => '',
            'placeholder' => esc_html__('Enter Maximum Number of Images Can Upload. EG: 4', 'cubewp-framework'),
            'value' => $FieldData['max_upload_files'],
            'tr_class' => 'conditional-field',
            'class' => 'field-max_upload_files',
            'tr_extra_attr' => 'data-equal="gallery"',
        );
        $field_settings['field_placeholder'] = array(
            'label' => esc_html__('Placeholder', 'cubewp-framework'),
            'name' => 'cwp[sub_fields][' . $parent_field . '][' . $FieldData['name'] . '][placeholder]',
            'type' => 'text',
            'placeholder' => esc_html__('Put your field placeholder here', 'cubewp-framework'),
            'value' => $FieldData['placeholder'],
            'tr_class' => 'conditional-field',
            'class' => 'field-placeholder',
            'id' => '',
            'tr_extra_attr' => 'data-equal="dropdown,text,textarea,number,email,url,post"',
        );
        $field_settings['field_options'] = array(
            'label' => esc_html__('Options', 'cubewp-framework'),
            'name' => 'cwp[sub_fields][' . $parent_field . '][' . $FieldData['name'] . ']',
            'type' => 'options',
            'id' => 'field-options-' . str_replace('cwp_field_', '', $FieldData['name']),
            'options' => $FieldData['options'],
            'default_value' => $FieldData['default_value'],
            'tr_class' => 'field-options-row conditional-field',
            'tr_extra_attr' => 'data-equal="dropdown,checkbox,radio"',
        );
        $field_settings['field_char_limit'] = array(
            'label' => esc_html__('Character Limit', 'cubewp-framework'),
            'name' => 'cwp[sub_fields][' . $parent_field . '][' . $FieldData['name'] . '][char_limit]',
            'type' => 'number',
            'placeholder' => esc_html__('Put your field character limit here', 'cubewp-framework'),
            'value' => $FieldData['char_limit'],
            'tr_class' => 'conditional-field',
            'class' => 'field-char-limit',
            'id' => '',
            'tr_extra_attr' => 'data-equal="text,textarea,email,url,password,number"',
        );
        $field_settings['field_multiple_values'] = array(
            'label' => esc_html__('Multiple', 'cubewp-framework'),
            'name' => 'cwp[sub_fields][' . $parent_field . '][' . $FieldData['name'] . '][multiple]',
            'value' => '1',
            'placeholder' => '',
            'type' => 'text',
            'checked' => $FieldData['multiple'],
            'type_input' => 'checkbox',
            'class' => 'field-multiple-checkbox checkbox cwp-switch-check',
            'id' => 'field-multiple-' . str_replace('cwp_field_', '', $FieldData['name']),
            'tr_class' => 'conditional-field',
            'tr_extra_attr' => 'data-equal="dropdown"',
            'extra_label' => esc_html__('Multiple Values', 'cubewp-framework'),
        );
        $field_settings['field_select2_ui'] = array(
            'label' => esc_html__('Select 2', 'cubewp-framework'),
            'name' => 'cwp[sub_fields][' . $parent_field . '][' . $FieldData['name'] . '][select2_ui]',
            'value' => '1',
            'placeholder' => '',
            'type' => 'text',
            'checked' => $FieldData['select2_ui'],
            'type_input' => 'checkbox',
            'class' => 'field-multiple-checkbox checkbox cwp-switch-check',
            'id' => 'field-select-ui-' . str_replace('cwp_field_', '', $FieldData['name']),
            'tr_class' => 'conditional-field',
            'tr_extra_attr' => 'data-equal="dropdown,post"',
            'extra_label' => esc_html__('Select2 UI', 'cubewp-framework'),
        );
        $field_settings['field_current_user_posts'] = array(
            'label' => esc_html__('LoggedIn User Posts', 'cubewp-framework'),
            'name' => 'cwp[sub_fields][' . $parent_field . '][' . $FieldData['name'] . '][current_user_posts]',
            'value' => '1',
            'placeholder' => '',
            'type' => 'text',
            'checked' => $FieldData['current_user_posts'],
            'type_input' => 'checkbox',
            'class' => 'field-multiple-checkbox checkbox cwp-switch-check',
            'id' => 'field-current-user-posts-' . str_replace('cwp_field_', '', $FieldData['name']),
            'tr_class' => 'conditional-field',
            'tr_extra_attr' => 'data-equal="post"',
            'extra_label' => esc_html__('LoggedIn User Posts', 'cubewp-framework'),
            'tooltip' => "Enable this option if you want to fetch posts which are submitted by currently loggedin user.",
        );
        $field_settings['field_autocomplete_ui'] = array(
            'label' => esc_html__('Autocomplete', 'cubewp-framework'),
            'name' => 'cwp[sub_fields][' . $parent_field . '][' . $FieldData['name'] . '][auto_complete]',
            'value' => '1',
            'placeholder' => '',
            'type' => 'text',
            'checked' => $FieldData['auto_complete'],
            'type_input' => 'checkbox',
            'class' => 'field-multiple-checkbox checkbox cwp-switch-check',
            'id' => 'field-auto_complete-' . str_replace('cwp_field_', '', $FieldData['name']),
            'tr_class' => 'conditional-field',
            'tr_extra_attr' => 'data-equal="post"',
            'extra_label' => esc_html__('Autocomplete', 'cubewp-framework'),
        );
        $field_settings['field_filter_post_types'] = array(
            'label' => esc_html__('Filter by Post Types', 'cubewp-framework'),
            'name' => 'cwp[sub_fields][' . $parent_field . '][' . $FieldData['name'] . '][filter_post_types]',
            'type' => 'dropdown',
            'id' => '',
            'options' => cwp_post_types(),
            'value' => $FieldData['filter_post_types'],
            'placeholder' => esc_html__('Select Post Type', 'cubewp-framework'),
            'option-class' => 'form-option option',
            'class' => 'field-filter-post-types',
            'tr_class' => 'conditional-field',
            'tr_extra_attr' => 'data-equal="post"',
            'required' => true,
            'validation_msg' => esc_html__('Please select Post-Type', 'cubewp-framework'),
        );
        $field_settings['field_appearance'] = array(
            'label' => esc_html__('Field Appearance', 'cubewp-framework'),
            'name' => 'cwp[sub_fields][' . $parent_field . '][' . $FieldData['name'] . '][appearance]',
            'type' => 'dropdown',
            'id' => '',
            'options' => array(
                'select' => __('Dropdown', 'cubewp-framework'),
                'multi_select' => __('Multi Dropdown', 'cubewp-framework'),
                'checkbox' => __('Checkbox', 'cubewp-framework'),
            ),
            'value' => $FieldData['appearance'],
            'placeholder' => '',
            'option-class' => 'form-option option',
            'class' => 'field-appearance',
            'tr_class' => 'conditional-field',
            'tr_extra_attr' => 'data-equal="post"',
            'required' => true,
            'validation_msg' => esc_html__('Please select appearance', 'cubewp-framework'),
        );
        $field_settings['field_rel_attr'] = array(
            'label' => esc_html__('Link Behavior', 'cubewp-framework'),
            'name' => 'cwp[sub_fields][' . $parent_field . '][' . $FieldData['name'] . '][rel_attr]',
            'type' => 'dropdown',
            'id' => '',
            'options' => array(
	            'do-follow' => __('Follow (Search engines will follow the link)', 'cubewp-framework'),
	            'nofollow' => __('No Follow (Instructs search engines not to follow the link)', 'cubewp-framework'),
	            'external' => __('External (Indicates that the linked document is located on a different website)', 'cubewp-framework'),
            ),
            'value' => $FieldData['rel_attr'],
            'placeholder' => '',
            'option-class' => 'form-option option',
            'class' => 'field-rel_attr',
            'tr_class' => 'conditional-field',
            'tr_extra_attr' => 'data-equal="url"',
        );
        $field_settings['field_validation'] = array(
            'label' => esc_html__('Required', 'cubewp-framework'),
            'name' => 'cwp[sub_fields][' . $parent_field . '][' . $FieldData['name'] . '][required]',
            'value' => '1',
            'placeholder' => '',
            'type' => 'text',
            'checked' => $FieldData['required'],
            'type_input' => 'checkbox',
            'class' => 'field-required-checkbox checkbox cwp-switch-check',
            'id' => 'field-required-' . str_replace('cwp_field_', '', $FieldData['name']),
            'tr_class' => 'conditional-field',
            'tr_extra_attr' => 'data-not_equal="gallery,repeating_field,switch"',
            'extra_label' => esc_html__('Required', 'cubewp-framework'),
        );
        $trclass = 'validation-msg-row cwp-hide-row conditional-field';
        if (isset($FieldData['required']) && $FieldData['required'] == 1) {
            $trclass = 'validation-msg-row conditional-field';
        }
        $field_settings['field_validation_msg'] = array(
            'label' => esc_html__('Required error message', 'cubewp-framework'),
            'name' => 'cwp[sub_fields][' . $parent_field . '][' . $FieldData['name'] . '][validation_msg]',
            'value' => $FieldData['validation_msg'],
            'placeholder' => esc_html__('Required error message', 'cubewp-framework'),
            'type' => 'text',
            'type_input' => 'text',
            'class' => 'field-validation-msg',
            'id' => '',
            'tr_class' => $trclass,
            'tr_extra_attr' => 'data-not_equal="gallery,repeating_field"',
        );
        $field_settings['field_files_save'] = array(
		    'label' => esc_html__('Save Format', 'cubewp-framework'),
		    'name' => 'cwp[sub_fields][' . $parent_field . '][' . $FieldData['name'] . '][files_save]',
		    'id' => '',
		    'type' => 'dropdown',
		    'options' => array(
			    'ids' => esc_html__("ID's", "cubewp-framework"),
			    'urls' => esc_html__("URLs", "cubewp-framework"),
		    ),
		    'placeholder' => '',
		    'value' => $FieldData['files_save'],
		    'option-class' => 'form-option option',
		    'class' => 'field-files-save',
		    'tr_class' => 'conditional-field',
		    'tr_extra_attr' => 'data-equal="file,image,gallery"',
		    'extra_label' => esc_html__('Save Format', 'cubewp-framework'),
	    );
	    $save_separators = array(
		    'array' => esc_html__("Array", "cubewp-framework"),
		    ',' => esc_html__("String separated by , (Comma)", "cubewp-framework"),
		    '|' => esc_html__("String separated by | (Pipe)", "cubewp-framework"),
		    ':' => esc_html__("String separated by : (Colon)", "cubewp-framework"),
		    ';' => esc_html__("String separated by ; (Semicolon)", "cubewp-framework"),
	    );
	    $save_separators = apply_filters( 'cubewp/custom/field/save/format/separators', $save_separators, $FieldData['files_save_separator'] );
	    $field_settings['field_files_save_separator'] = array(
		    'label' => esc_html__('Save Format Separator', 'cubewp-framework'),
		    'name' => 'cwp[sub_fields][' . $parent_field . '][' . $FieldData['name'] . '][files_save_separator]',
		    'id' => '',
		    'type' => 'dropdown',
		    'options' => $save_separators,
		    'placeholder' => '',
		    'option-class' => 'form-option option',
		    'value' => $FieldData['files_save_separator'],
		    'class' => 'field-files-save-separator',
		    'tr_class' => 'conditional-field',
		    'tr_extra_attr' => 'data-equal="gallery,dropdown,checkbox"',
		    'extra_label' => esc_html__('Save Format Separator', 'cubewp-framework'),
	    );
        $field_settings['field_id'] = array(
            'label' => esc_html__('ID', 'cubewp-framework'),
            'name' => 'cwp[sub_fields][' . $parent_field . '][' . $FieldData['name'] . '][id]',
            'type' => 'text',
            'id' => '',
            'class' => 'field-id',
            'placeholder' => esc_html__('ID for css', 'cubewp-framework'),
            'value' => $FieldData['id'],
            'required' => true,
            'validation_msg' => esc_html__('Please enter id for css and JS purpose', 'cubewp-framework'),
        );
        $field_settings['field_class'] = array(
            'label' => esc_html__('Class', 'cubewp-framework'),
            'name' => 'cwp[sub_fields][' . $parent_field . '][' . $FieldData['name'] . '][class]',
            'type' => 'text',
            'id' => '',
            'class' => 'field-class',
            'placeholder' => esc_html__('Class for css', 'cubewp-framework'),
            'value' => $FieldData['class'],
        );

        $field_settings = apply_filters('cubewp/custom_fields/single/subfield/add', $field_settings, $FieldData);
        $field_name = !empty($FieldData['label']) ? $FieldData['label'] : esc_html__('Field Label', 'cubewp-framework');
        $closed_class = (isset($FieldData['label']) && $FieldData['label'] != '') ? 'closed' : '';
        $hide_class = (isset($FieldData['label']) && $FieldData['label'] != '') ? 'hidden' : '';
        $field_type = (isset($FieldData['type']) && $FieldData['type'] == '') ? 'text' : $FieldData['type'];
        $field_type = isset($field_types[$field_type]) ? $field_types[$field_type] : $field_type;
        $counter = isset($FieldData["counter"]) ? $FieldData["counter"] : 1;
	    $html = '
        <div class="cwp-field-set cwp-add-form-feild">
            <div class="field-header sub-field-header ' . $closed_class . '">
                <div class="field-order-counter">
                    <div class="field-order sub-field-order">
                        <svg xmlns="SVG namespace" width="22px" height="22px" viewBox="0 0 320 512" fill="#BFBFBF">
                            <path d="M40 352c-22.1 0-40 17.9-40 40l0 48c0 22.1 17.9 40 40 40l48 0c22.1 0 40-17.9 40-40l0-48c0-22.1-17.9-40-40-40l-48 0zm192 0c-22.1 0-40 17.9-40 40l0 48c0 22.1 17.9 40 40 40l48 0c22.1 0 40-17.9 40-40l0-48c0-22.1-17.9-40-40-40l-48 0zM40 320l48 0c22.1 0 40-17.9 40-40l0-48c0-22.1-17.9-40-40-40l-48 0c-22.1 0-40 17.9-40 40l0 48c0 22.1 17.9 40 40 40zM232 192c-22.1 0-40 17.9-40 40l0 48c0 22.1 17.9 40 40 40l48 0c22.1 0 40-17.9 40-40l0-48c0-22.1-17.9-40-40-40l-48 0zM40 160l48 0c22.1 0 40-17.9 40-40l0-48c0-22.1-17.9-40-40-40L40 32C17.9 32 0 49.9 0 72l0 48c0 22.1 17.9 40 40 40zM232 32c-22.1 0-40 17.9-40 40l0 48c0 22.1 17.9 40 40 40l48 0c22.1 0 40-17.9 40-40l0-48c0-22.1-17.9-40-40-40l-48 0z"></path>
                        </svg>
                    </div>
                    <div class="field-counter"><span>' . $counter . '</span></div>
                </div>
                <div class="field-title" data-label="' . esc_html__('Field Name', 'cubewp-framework') . '">
                    <div class="sub-field-order"><i class="fa fa-arrows-alt"></i></div>
                    <div class="field-label">' . esc_html($field_name) . '</div>
                    <div class="field-type">' . esc_html($field_type) . '</div>
                    <div class="field-slug">' . esc_html($FieldData['name']) . '</div>
                </div>
                <div class="field-actions">
                    <a class="remove-field" href="javascript:void(0);"><span class="dashicons dashicons-trash"></span></a>
                    <a class="edit-sub-field" href="javascript:void(0);"><span class="dashicons dashicons-arrow-down-alt2"></a>
                </div>
            </div>
            <div class="cwp-sub-field-inner ' . $hide_class . '">
                <table class="subfields">
                    <tbody>';
        foreach ($field_settings as $field_setting) {
            $fields = apply_filters("cubewp/admin/{$field_setting['type']}/customfield", '', $field_setting);
            $html .= apply_filters('cubewp/custom_fields/single/subfield/output', $fields, $field_setting);
        }
        $html .= '</tbody>
                </table>
            </div>
        </div>';
        return apply_filters('cwp_custom_sub_field_settings_html', $html, $FieldData);
    }

    /**
     * Method cwp_form_field_types
     *
     * @return array
     * @since  1.0.0
     */
    public static function cwp_form_field_types( $request_from = '' )
    {
        $field_types = array();
        $field_types[esc_html__('Basic', 'cubewp-framework')]['text'] = esc_html__('Text', 'cubewp-framework');
        $field_types[esc_html__('Basic', 'cubewp-framework')]['number'] = esc_html__('Number', 'cubewp-framework');
        $field_types[esc_html__('Basic', 'cubewp-framework')]['email'] = esc_html__('Email', 'cubewp-framework');
        $field_types[esc_html__('Basic', 'cubewp-framework')]['url'] = esc_html__('URL', 'cubewp-framework');
        $field_types[esc_html__('Basic', 'cubewp-framework')]['password'] = esc_html__('Password', 'cubewp-framework');
        $field_types[esc_html__('Basic', 'cubewp-framework')]['textarea'] = esc_html__('Textarea', 'cubewp-framework');
	    $field_types[esc_html__('Basic', 'cubewp-framework')]['range'] = esc_html__('Range', 'cubewp-framework');
	    $field_types[esc_html__('Basic', 'cubewp-framework')]['color'] = esc_html__('Color', 'cubewp-framework');

	    $field_types[esc_html__('Media', 'cubewp-framework')]['wysiwyg_editor'] = esc_html__('Wysiwyg Editor', 'cubewp-framework');
        $field_types[esc_html__('Media', 'cubewp-framework')]['oembed'] = esc_html__('oEmbed', 'cubewp-framework');
        $field_types[esc_html__('Media', 'cubewp-framework')]['file'] = esc_html__('File', 'cubewp-framework');
        $field_types[esc_html__('Media', 'cubewp-framework')]['image'] = esc_html__('Image', 'cubewp-framework');
        $field_types[esc_html__('Media', 'cubewp-framework')]['gallery'] = esc_html__('Gallery', 'cubewp-framework');

		$field_types[esc_html__('Choice', 'cubewp-framework')]['switch'] = esc_html__('Switch', 'cubewp-framework');
        $field_types[esc_html__('Choice', 'cubewp-framework')]['dropdown'] = esc_html__('Dropdown', 'cubewp-framework');
        $field_types[esc_html__('Choice', 'cubewp-framework')]['checkbox'] = esc_html__('Checkbox', 'cubewp-framework');
        $field_types[esc_html__('Choice', 'cubewp-framework')]['radio'] = esc_html__('Radio Button', 'cubewp-framework');
        $field_types[esc_html__('Choice', 'cubewp-framework')]['business_hours'] = esc_html__('Business Hours', 'cubewp-framework');

		$field_types[esc_html__('jQuery', 'cubewp-framework')]['google_address'] = esc_html__('Google Address', 'cubewp-framework');
        $field_types[esc_html__('jQuery', 'cubewp-framework')]['date_picker'] = esc_html__('Date Picker', 'cubewp-framework');
        $field_types[esc_html__('jQuery', 'cubewp-framework')]['date_time_picker'] = esc_html__('Date Time Picker', 'cubewp-framework');
        $field_types[esc_html__('jQuery', 'cubewp-framework')]['time_picker'] = esc_html__('Time Picker', 'cubewp-framework');

        $field_types[esc_html__('Relationship', 'cubewp-framework')]['post'] = esc_html__('Post', 'cubewp-framework');
        $field_types[esc_html__('Relationship', 'cubewp-framework')]['taxonomy'] = esc_html__('Taxonomy', 'cubewp-framework');
        $field_types[esc_html__('Relationship', 'cubewp-framework')]['user'] = esc_html__('User', 'cubewp-framework');

        if ( empty( $request_from ) ) {
            $request_from = CubeWp_Custom_Fields_Processor::get_field_option_name();
        }
        if ( $request_from != 'settings' ) {
            $field_types[esc_html__('Layout', 'cubewp-framework')]['repeating_field'] = esc_html__('Repeating Field', 'cubewp-framework');
        }

        return apply_filters('cubewp/post/custom_fields/types', $field_types, $request_from);
    }

    /**
     * Method cwp_form_sub_field_types
     *
     * @return array
     * @since  1.0.0
     */
    private static function cwp_form_sub_field_types()
    {
        $field_types = array();
        $field_types[esc_html__('Basic', 'cubewp-framework')]['text'] = esc_html__('Text', 'cubewp-framework');
        $field_types[esc_html__('Basic', 'cubewp-framework')]['number'] = esc_html__('Number', 'cubewp-framework');
        $field_types[esc_html__('Basic', 'cubewp-framework')]['email'] = esc_html__('Email', 'cubewp-framework');
        $field_types[esc_html__('Basic', 'cubewp-framework')]['url'] = esc_html__('URL', 'cubewp-framework');
        $field_types[esc_html__('Basic', 'cubewp-framework')]['textarea'] = esc_html__('Textarea', 'cubewp-framework');
	    $field_types[esc_html__('Basic', 'cubewp-framework')]['color'] = esc_html__('Color', 'cubewp-framework');
	    $field_types[esc_html__('Basic', 'cubewp-framework')]['range'] = esc_html__('Range', 'cubewp-framework');

        $field_types[esc_html__('Media', 'cubewp-framework')]['file'] = esc_html__('File', 'cubewp-framework');
        $field_types[esc_html__('Media', 'cubewp-framework')]['image'] = esc_html__('Image', 'cubewp-framework');
        $field_types[esc_html__('Media', 'cubewp-framework')]['gallery'] = esc_html__('Gallery', 'cubewp-framework');

        $field_types[esc_html__('Choice', 'cubewp-framework')]['switch'] = esc_html__('Switch', 'cubewp-framework');
        $field_types[esc_html__('Choice', 'cubewp-framework')]['dropdown'] = esc_html__('Dropdown', 'cubewp-framework');
        $field_types[esc_html__('Choice', 'cubewp-framework')]['checkbox'] = esc_html__('Checkbox', 'cubewp-framework');
        $field_types[esc_html__('Choice', 'cubewp-framework')]['radio'] = esc_html__('Radio Button', 'cubewp-framework');

        $field_types[esc_html__('jQuery', 'cubewp-framework')]['google_address'] = esc_html__('Google Address', 'cubewp-framework');
        $field_types[esc_html__('jQuery', 'cubewp-framework')]['date_picker'] = esc_html__('Date Picker', 'cubewp-framework');
        $field_types[esc_html__('jQuery', 'cubewp-framework')]['time_picker'] = esc_html__('Time Picker', 'cubewp-framework');

        $field_types[esc_html__('Relationship', 'cubewp-framework')]['post'] = esc_html__('Post', 'cubewp-framework');
        return apply_filters('cubewp/post/custom_fields/sub/types', $field_types);
    }
}