<?php
/**
 * display fields of custom fields.
 *
 * @version 1.0
 * @package cubewp/cube/modules/post-types
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * CubeWp_Posttype_Custom_Fields_Display
 */
class CubeWp_Posttype_Custom_Fields_Display extends CubeWp_Posttype_Custom_Fields {
        
    /**
     * Method cwp_custom_fields_run
     *
     * @return void
     * @since  1.0.0
     */
    public static function cwp_custom_fields_run(  ) {
        self::save_group();
        self::group_display();
        self::add_new_group();
    }
    
    /**
     * Method process_add_field
     *
     * @return object
     * @since  1.0.0
     */
    public static function process_add_field() {
        check_ajax_referer( 'cubewp_custom_fields_nonce', 'nonce' );
        if( true )
            wp_send_json_success(self::add_new_field());
        else
            wp_send_json_error( array( 'error' => $custom_error ) );
    }
        
    /**
     * Method cwp_add_custom_sub_field
     *
     * @return object
     * @since  1.0.0
     */
    public static function cwp_add_custom_sub_field(){
        check_ajax_referer( 'cubewp_custom_fields_nonce', 'nonce' );
        if( true ){
            wp_send_json_success(self::add_new_sub_field(array(), sanitize_text_field($_POST['parent_field'])));
        }else{
            wp_send_json_error( array( 'error' => $custom_error ) );
        }
    }    
    /**
     * Method add_new_group
     *
     * @return void
     * @since  1.0.0
     */
    public static function add_new_group() {
        if(isset($_GET['action']) && ('new' == $_GET['action'] || 'edit' == $_GET['action'])){
            self::edit_group();
        }
    }    
    /**
     * Method group_display
     *
     * @return string html
     * @since  1.0.0
     */
    public static function group_display() {
        if(isset($_GET['action']) && ('new' == $_GET['action'] || 'edit' == $_GET['action'])){
            return;
        }
        $customFieldsGroupTable = new CubeWp_Post_Types_Custom_Fields_Table();
        ?>
        <div class="wrap cwp-post-type-wrape">
            <nav class="nav-tab-wrapper wp-clearfix">
                <a class="nav-tab nav-tab-active" href="?page=custom-fields"><?php esc_html_e("Custom Fields (Post Types)", 'cubewp-framework'); ?></a>
                <a class="nav-tab" href="?page=taxonomy-custom-fields"><?php esc_html_e('Custom Fields (Taxonomies)', 'cubewp-framework'); ?></a>
                <a class="nav-tab" href="?page=user-custom-fields"><?php esc_html_e('Custom Fields (User Roles)', 'cubewp-framework'); ?></a>
                
            </nav>
            <h1 class="wp-heading-inline"><?php esc_html_e("All Groups (Post Types)", 'cubewp-framework'); ?></h1>
            <a href="<?php echo CubeWp_Submenu::_page_action('custom-fields','new'); ?>" class="page-title-action"><?php esc_html_e('Add New', 'cubewp-framework'); ?></a>
            <hr class="wp-header-end">
            <?php $customFieldsGroupTable->prepare_items(); ?>
            <form method="post">
                <input type="hidden" name="page" value="custom-fields">
                <?php $customFieldsGroupTable->display(); ?>
            </form>
        </div>
        <?php
    }    

    /**
     * Method edit_group
     *
     * @return string
     * @since  1.0.0
     */
    public static function edit_group() {
        
        $group = self::get_group();
        $defaults = array(
            'id'           => '',
            'name'         => '',
            'order'        => 0,
            'types'        => '',
            'fields'       => '',
            'sub_fields'   => '',
            'terms'        => '',
            'user_role'    => '',
            'description'  => '',
        );
        $group = wp_parse_args( $group, $defaults );
        
        ?>
        <div class="wrap">
        <form id="post" class="cwpgroup" method="post" action="" enctype="multipart/form-data">
            
            <div class="cwpform-title-outer  margin-bottom-0 margin-left-minus-20  margin-right-0">
                <?php echo self::_title();	?>			
            </div>
            <input type="hidden" name="cwp_group_nonce" value="<?php echo wp_create_nonce( basename( __FILE__ ) ); ?>">
            <input type="hidden" class="" name="cwp[group][id]" value="<?php echo esc_attr($group['id']); ?>">
            <div id="poststuff"  class="padding-0">
            <div id="post-body" class="metabox-holder columns-2">
                <div id="postbox-container-1" class="postbox-container">
                    <div id="side-sortables" class="meta-box-sortables ui-sortable">
                        <div class="postbox">
                            <div class="postbox-header">
                                <h2 class="hndle"><?php esc_html_e("Save Custom Field's Group", 'cubewp-framework'); ?></h2>
                            </div>
                            <div class="inside">
                                <div id="major-publishing-actions">
                                    <div id="publishing-action" style="float:none">
                                        <?php echo self::save_button(); ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="postbox">
                            <div class="postbox-header">
                                <h2 class="hndle"><?php esc_html_e('Assign To Post Type', 'cubewp-framework'); ?></h2>
                            </div>
                            <div class="inside">
                                <div class="main">
                                    <table class="form-table cwp-validation">
                                        <tr class="required" data-validation_msg="">
                                            <td class="text-left">
                                                <ul class="cwp-checkbox-outer margin-0">
                                                    <?php
                                                       echo self::get__types($group['types']);
                                                    ?>
                                                </ul>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="custom-fields-conditional-taxonomies-list">
                            <?php
                                echo self::get_taxonomies_by_post_types( $group['types'], $group['terms'] );
                            ?>
                        </div>
                    </div>
                </div>
                <div id="postbox-container-2" class="postbox-container postbox-container-top">
                    <div class="postbox">
                        <div class="postbox-header">
                            <h2><span><?php esc_html_e('Basic Settings', 'cubewp-framework'); ?></span></h2>
                        </div>
                        <div class="inside">
                            <div class="main">
                                <table class="form-table cwp-validation">
                                    <tbody>
                                        <?php
                                        echo apply_filters('cubewp/admin/group/text/field', '', array(
                                            'id'             =>    '',
                                            'name'           =>    'cwp[group][name]',
                                            'value'          =>    $group['name'],
                                            'class'          =>    'cwp-group',
                                            'placeholder'    =>    esc_html__('Type new group name here..', 'cubewp-framework'),
                                            'label'          =>    esc_html__('Group Name', 'cubewp-framework'),
                                            'required'       =>    true,
                                            'extra_attrs'    =>    'maxlength=20',
                                            'tooltip'        =>    'Give a name for this group. Which will be used to show grouped data in metaboxes',
                                        ));
                                        echo apply_filters('cubewp/admin/group/text/field', '', array(
                                            'id'             =>    '',
                                            'type'           =>    'number',
                                            'name'           =>    'cwp[group][order]',
                                            'value'          =>    $group['order'],
                                            'class'          =>    'cwp-group',
                                            'placeholder'    =>    esc_html__('Set group order', 'cubewp-framework'),
                                            'label'          =>    esc_html__('Group Order', 'cubewp-framework'),
                                            'required'       =>    true,
                                            'extra_attrs'    =>    'maxlength=20',
                                            'tooltip'        =>    'Give a order number for this group. Which will be used to show in order',
                                        ));
                                        echo apply_filters('cubewp/admin/group/text/field', '', array(
                                            'id'             =>    '',
                                            'name'           =>    'cwp[group][description]',
                                            'value'          =>    $group['description'],
                                            'class'          =>    'cwp-group',
                                            'placeholder'    =>    esc_html__('Write group description to identify the group', 'cubewp-framework'),
                                            'label'          =>    esc_html__('Description', 'cubewp-framework'),
                                            'required'       =>    true,
                                            'extra_attrs'    =>    'maxlength=100',
                                            'tooltip'        =>    'Give a description for this group. Which will be used to show under the group title',
                                        ));
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="cwp-group-fields cwp-validation">
                    <?php echo self::get_fields($group['fields'], $group['sub_fields']); ?>
                    </div>
                </div>
                <?php self::add_new_field_btn(); ?>
                <div class="clear"></div>
            </div>
            </div>
            </form>
        </div>
        <?php
    }
    /**
     * page title
     * page title split for edit or add post type form. 
     * @since 1.0
     */  
    private static function _title() {
        if (isset($_GET['action']) && ('edit' == $_GET['action'] && !empty($_GET['groupid']))) {
            return '<h1>'. esc_html(__('Edit Group', 'cubewp-framework')) .'</h1>';
        } else {
            return '<h1>'. esc_html(__('Create New Group', 'cubewp-framework')) .'</h1>';
        }
    }     

    /**
     * Method save_button
     *
     * @return string html
     * @since  1.0.0
    */
     private static function save_button() {
        if(isset($_GET['action']) && ('edit' == $_GET['action'] && !empty($_GET['groupid']))){            
            $name = 'cwp_edit_group';
        }else{
            $name = 'cwp_save_group';
        }
        return '<input type="submit" class="cwp-custom-fields-group-btn button button-primary button-large cwp-save-button" name="'.$name.'" value="'.__( 'Save Group', 'cubewp-framework' ).'" />';
	}
        
    /**
     * Method add_new_field_btn
     *
     * @return string html
     * @since  1.0.0
     */
    private static function add_new_field_btn() {
        echo '<a class="button button-primary button-large" href="javascript:void(0);" id="cwp-add-new-field-btn">'. __('Add New Field', 'cubewp-framework') .'</a>';
    }
        
    /**
     * Method get__types
     *
     * @param array $getTypes
     *
     * @return string html
     * @since  1.0.0
     */
    private static function get__types($getTypes) {
        $types = cwp_post_types();
        if($getTypes){
            $getTypes = implode(",",$getTypes);
            $getTypes = explode(",",$getTypes);
        }
        $html = '';
        foreach($types as $type){
            if(is_array($getTypes) && in_array($type,$getTypes)){
                $checked = 'checked';
            }else{
                $checked = '';
            }
            $html .= '<li class="pull-left">';
            $html .= '<input type="checkbox" class="cwp-custom-fields-post-types" name="cwp[group][types][]" placeholder="" '.$checked.' value="'.$type.'">'.$type.' <br>';
            $html .= '</li>';
        }
        return $html;
	}
        
    /**
     * Method add_new_field
     *
     * @param array $FieldData
     *
     * @return string html
     * @since  1.0.0
     */
    public static function add_new_field($FieldData = array()) {
        $defaults = array(
            'label'                   =>   '',
            'name'                    =>   'cwp_field_'. rand(10000000,1000000000000),
            'type'                    =>   '',
            'description'             =>   '',
            'map_use'             =>   '',
            'default_value'           =>   '',
            'placeholder'             =>   '',
            'filter_post_types'       =>   '',
            'filter_taxonomy'         =>   '',
            'filter_user_roles'       =>   '',
            'appearance'              =>   '',
            'options'                 =>   '',
            'multiple'                =>   0,
            'select2_ui'              =>   0,
            'auto_complete'           =>   0,
            'required'                =>   '',
            'validation_msg'          =>   '',
            'id'                      =>   'cwp_field_'. rand(10000000,1000000000000),
            'class'                   =>   '',
            'container_class'         =>   '',
            'conditional'             =>   '',
            'conditional_field'       =>   '',
            'conditional_operator'    =>   '',
            'conditional_value'       =>   '',
            'sub_fields'              =>   '',
        );
        $FieldData = wp_parse_args( $FieldData, $defaults );
        $field_name = !empty($FieldData['label']) ? $FieldData['label'] : esc_html__('Field Label', 'cubewp-framework');
        $field_settings = array();

        $closed_class = (isset($FieldData['label']) && $FieldData['label'] != '') ? 'closed' : '';
        $hide_class   = (isset($FieldData['label']) && $FieldData['label'] != '') ? 'hidden' : '';
        $field_type   = (isset($FieldData['type']) && $FieldData['type'] == '') ? 'text' : $FieldData['type'];
        $field_type   = isset($field_types[$field_type]) ? $field_types[$field_type] : $field_type;
        $html = '
        <div class="parent-field cwp-field-set cwp-add-form-feild">
            <div class="field-header '. $closed_class .'">
                <div class="field-title" data-label="'. esc_html__('Field Name', 'cubecubewp-frameworkwp') .'">
                    <div class="field-order"><span class="dashicons dashicons-move"></span></div>
                    <div class="field-label">'. esc_html($field_name) .'</div>
                    <div class="field-type">'. esc_html($field_type) .'</div>
                    <div class="field-slug">'. esc_html($FieldData['name']) .'</div>
                </div>
                <div class="field-actions">
                    <a class="edit-field" href="javascript:void(0);"><span class="dashicons dashicons-edit"></span></a>
                    <a class="remove-field" href="javascript:void(0);"><span class="dashicons dashicons-trash"></span></a>
                </div>
            </div>
            <div class="cwp-collapsible-inner '. $hide_class .'">
                <table>
                    <tbody>';
                    $field_settings['field_label'] = array(
                                'label' => esc_html__('Field Label', 'cubewp-framework'),
                                'name' => 'cwp[fields]['.$FieldData['name'].'][label]',
                                'type' => 'text',
                                'id' => '',
                                'class' => 'field-label',
                                'placeholder' => esc_html__('Put your field label here', 'cubewp-framework'),
                                'value' => $FieldData['label'],
                                'extra_attrs' =>    'maxlength=30 ',
                                'required'    =>    true,
                            );
                    $field_settings['field_name'] = array(
                                'label'       => esc_html__('Field Name', 'cubewp-framework'),
                                'name'        => 'cwp[fields]['.$FieldData['name'].'][name]',
                                'type'        => 'text',
                                'id'          => '',
                                'class'       => 'cubewp-locked-field field-name',
                                'placeholder' => esc_html__('Put your field name here', 'cubewp-framework'),
                                'value'       => $FieldData['name'],
                                'extra_attrs' =>    'maxlength=20 ',
                                'required'    =>    true,
                            );
                    $field_settings['field_type'] = array(
                                'label' => esc_html__('Field Type', 'cubewp-framework'),
                                'name' => 'cwp[fields]['.$FieldData['name'].'][type]',
                                'id'          => '',
                                'type' => 'dropdown',
                                'options' => self::cwp_form_field_types(),
                                'value' => $FieldData['type'],
                                'placeholder' => '',
                                'option-class' => 'form-option option',
                                'class' => 'field-type',
                            );
                    $field_settings['field_map_use'] = array(
                                'label' => esc_html__('Do you want to use this field for archive map', 'cubewp-framework'),
                                'name' => 'cwp[fields]['.$FieldData['name'].'][map_use]',
                                'value' => '1',
                                'id'          => '',
                                'placeholder' => '',
                                'type' => 'text',
                                'checked' => $FieldData['map_use'],
                                'type_input' => 'checkbox',
                                'class' => 'field-map_use-checkbox checkbox',
                                'id' => '',
                                'tr_class' => 'conditional-field',
                                'tr_extra_attr' => 'data-equal="google_address"',
                                'extra_label' => esc_html__('Use for Archive Map', 'cubewp-framework'),
                            );
                    $field_settings['field_desc'] = array(
                                'label' => esc_html__('description', 'cubewp-framework'),
                                'name' => 'cwp[fields]['.$FieldData['name'].'][description]',
                                'type' => 'textarea',
                                'id'          => '',
                                'placeholder' => esc_html__('Write description about this field', 'cubewp-framework'),
                                'value' => $FieldData['description'],
                            );
                    $field_settings['field_default_value'] = array(
                                'label' => esc_html__('Default Value', 'cubewp-framework'),
                                'name' => 'cwp[fields]['.$FieldData['name'].'][default_value]',
                                'type' => 'text',
                                'id'          => '',
                                'placeholder' => esc_html__('Default Value', 'cubewp-framework'),
                                'value' => $FieldData['default_value'],
                                'tr_class' => 'conditional-field',
                                'class' => 'field-default-value',
                                'id' => '',
                                'tr_extra_attr' => 'data-equal="text,textarea"',
                            );
                    $field_settings['field_placeholder'] = array(
                                'label' => esc_html__('Placeholder', 'cubewp-framework'),
                                'name' => 'cwp[fields]['.$FieldData['name'].'][placeholder]',
                                'type' => 'text',
                                'id'          => '',
                                'placeholder' => esc_html__('Put your field placeholder here', 'cubewp-framework'),
                                'value' => $FieldData['placeholder'],
                                'tr_class' => 'conditional-field',
                                'class' => 'field-placeholder',
                                'id' => '',
                                'tr_extra_attr' => 'data-equal="text,textarea,number,email,url,password,map,dropdown,taxonomy,post,user"',
                            );
                    $field_settings['field_options'] = array(
                                'label' => esc_html__('Options', 'cubewp-framework'),
                                'name' => 'cwp[fields]['.$FieldData['name'].']',
                                'type' => 'options',
                                'id' => '',
                                'options' => $FieldData['options'],
                                'default_value' => $FieldData['default_value'],
                                'tr_class' => 'field-options-row conditional-field',
                                'tr_extra_attr' => 'data-equal="dropdown,checkbox,radio"',
                            );
                    $field_settings['field_multiple_values'] = array(
                                'label' => esc_html__('Multiple', 'cubewp-framework'),
                                'name' => 'cwp[fields]['.$FieldData['name'].'][multiple]',
                                'value' => '1',
                                'placeholder' => '',
                                'type' => 'text',
                                'checked' => $FieldData['multiple'],
                                'type_input' => 'checkbox',
                                'class' => 'field-multiple-checkbox checkbox',
                                'id' => 'field-multiple-'. str_replace('cwp_field_', '', $FieldData['name']),
                                'tr_class' => 'conditional-field',
                                'tr_extra_attr' => 'data-equal="dropdown"',
                                'extra_label' => esc_html__('Multiple Values', 'cubewp-framework'),
                            );
                    $field_settings['field_select2_ui'] = array(
                                'label' => esc_html__('Select 2', 'cubewp-framework'),
                                'name' => 'cwp[fields]['.$FieldData['name'].'][select2_ui]',
                                'value' => '1',
                                'placeholder' => '',
                                'type' => 'text',
                                'checked' => $FieldData['select2_ui'],
                                'type_input' => 'checkbox',
                                'class' => 'field-multiple-checkbox checkbox',
                                'id' => 'field-select-ui-'. str_replace('cwp_field_', '', $FieldData['name']),
                                'tr_class' => 'conditional-field',
                                'tr_extra_attr' => 'data-equal="dropdown,post,user,taxonomy"',
                                'extra_label' => esc_html__('Select2 UI', 'cubewp-framework'),
                            );
                    $field_settings['field_autocomplete_ui'] = array(
                                'label' => esc_html__('Autocomplete', 'cubewp-framework'),
                                'name' => 'cwp[fields]['.$FieldData['name'].'][auto_complete]',
                                'value' => '1',
                                'placeholder' => '',
                                'type' => 'text',
                                'checked' => $FieldData['auto_complete'],
                                'type_input' => 'checkbox',
                                'class' => 'field-multiple-checkbox checkbox',
                                'id' => 'field-auto_complete-'. str_replace('cwp_field_', '', $FieldData['name']),
                                'tr_class' => 'conditional-field',
                                'tr_extra_attr' => 'data-equal="post,user,taxonomy"',
                                'extra_label' => esc_html__('Autocomplete', 'cubewp-framework'),
                            );
                    $field_settings['field_filter_post_types'] = array(
                                'label' => esc_html__('Filter by Post Types', 'cubewp-framework'),
                                'name' => 'cwp[fields]['.$FieldData['name'].'][filter_post_types]',
                                'type' => 'dropdown',
                                'id'          => '',
                                'options' => cwp_post_types(),
                                'value' => $FieldData['filter_post_types'],
                                'placeholder' => esc_html__('Select Post Type', 'cubewp-framework'),
                                'option-class' => 'form-option option',
                                'class' => 'field-filter-post-types',
                                'tr_class' => 'conditional-field',
                                'tr_extra_attr' => 'data-equal="post"',
                                'required'    =>    true,
                                'validation_msg'    =>  esc_html__('Please select Post-Type', 'cubewp-framework'),
                            );
                    $field_settings['field_filter_taxonomy'] = array(
                                'label' => esc_html__('Filter by Taxonomy', 'cubewp-framework'),
                                'name' => 'cwp[fields]['.$FieldData['name'].'][filter_taxonomy]',
                                'type' => 'dropdown',
                                'id'          => '',
                                'options' => cwp_taxonomies(),
                                'value' => $FieldData['filter_taxonomy'],
                                'placeholder' => esc_html__('Select Taxonomy', 'cubewp-framework'),
                                'option-class' => 'form-option option',
                                'class' => 'field-filter-taxonomy',
                                'tr_class' => 'conditional-field',
                                'tr_extra_attr' => 'data-equal="taxonomy"',
                                'required'    =>    true,
                                'validation_msg'    =>  esc_html__('Please select taxonomy', 'cubewp-framework'),
                            );
                    $field_settings['field_filter_user_roles'] = array(
                                'label' => esc_html__('Filter by role', 'cubewp-framework'),
                                'name' => 'cwp[fields]['.$FieldData['name'].'][filter_user_roles]',
                                'type' => 'dropdown',
                                'id'          => '',
                                'options' => cwp_get_user_roles_name(),
                                'value' => $FieldData['filter_user_roles'],
                                'placeholder' => esc_html__('Select User Role', 'cubewp-framework'),
                                'option-class' => 'form-option option',
                                'class' => 'field-filter-user-role',
                                'tr_class' => 'conditional-field',
                                'tr_extra_attr' => 'data-equal="user"',
                            );
                    $field_settings['field_appearance'] = array(
                                'label' => esc_html__('Field Appearance', 'cubewp-framework'),
                                'name' => 'cwp[fields]['.$FieldData['name'].'][appearance]',
                                'type' => 'dropdown',
                                'id'          => '',
                                'options' => array(
                                    'select'        =>   __('Select', 'cubewp-framework'),
                                    'multi_select'  =>   __('Multi Select', 'cubewp-framework'),
                                    'checkbox'      =>   __('Checkbox', 'cubewp-framework'),
                                ),
                                'value' => $FieldData['appearance'],
                                'placeholder' => '',
                                'option-class' => 'form-option option',
                                'class' => 'field-appearance',
                                'tr_class' => 'conditional-field',
                                'tr_extra_attr' => 'data-equal="post,user,taxonomy"',
                            );
                    $field_settings['field_validation'] = array(
                                'label' => esc_html__('Validation', 'cubewp-framework'),
                                'name' => 'cwp[fields]['.$FieldData['name'].'][required]',
                                'value' => '1',
                                'placeholder' => '',
                                'type' => 'text',
                                'id'          => '',
                                'checked' => $FieldData['required'],
                                'type_input' => 'checkbox',
                                'class' => 'field-required-checkbox checkbox',
                                'id' => 'field-required-'. str_replace('cwp_field_', '', $FieldData['name']),
                                'tr_class' => 'conditional-field',
                                'tr_extra_attr' => 'data-not_equal="gallery,repeating_field,switch"',
                                'extra_label' => esc_html__('Required', 'cubewp-framework'),
                            );
                    $trclass = 'validation-msg-row cwp-hide-row conditional-field';
                    if( isset($FieldData['required']) && $FieldData['required'] == 1 ){
                        $trclass = 'validation-msg-row conditional-field';
                    }
                    $field_settings['field_validation_msg'] = array(
                                'label' => esc_html__('Validation error message', 'cubewp-framework'),
                                'name' => 'cwp[fields]['.$FieldData['name'].'][validation_msg]',
                                'value' => $FieldData['validation_msg'],
                                'placeholder' => esc_html__('Validation error message', 'cubewp-framework'),
                                'type' => 'text',
                                'id'          => '',
                                'type_input' => 'text',
                                'class' => 'field-validation-msg',
                                'id' => '',
                                'tr_class' => $trclass,                       
                                'tr_extra_attr' => 'data-not_equal="gallery,repeating_field,switch"',
                            );
                    $field_settings['field_id'] = array(
                                'label' => esc_html__('ID', 'cubewp-framework'),
                                'name' => 'cwp[fields]['.$FieldData['name'].'][id]',
                                'type' => 'text',
                                'id' => '',
                                'class' => 'field-id',
                                'placeholder' => esc_html__('ID for css', 'cubewp-framework'),
                                'value' => $FieldData['id'],
                            );
                    $field_settings['field_class'] = array(
                                'label' => esc_html__('Class', 'cubewp-framework'),
                                'name' => 'cwp[fields]['.$FieldData['name'].'][class]',
                                'type' => 'text',
                                'id' => '',
                                'class' => 'field-class',
                                'placeholder' => esc_html__('Class for css', 'cubewp-framework'),
                                'value' => $FieldData['class'],
                            );
                    $field_settings['field_container_class'] = array(
                                'label' => esc_html__('Container Class', 'cubewp-framework'),
                                'name' => 'cwp[fields]['.$FieldData['name'].'][container_class]',
                                'type' => 'text',
                                'id' => '',
                                'class' => 'field-container-class',
                                'placeholder' => esc_html__('Container Class for css', 'cubewp-framework'),
                                'value' => $FieldData['container_class'],
                            );
                    $field_settings['field_conditional'] = array(
                                'label' => esc_html__('Conditional Logic', 'cubewp-framework'),
                                'name' => 'cwp[fields]['.$FieldData['name'].'][conditional]',
                                'value' => '1',
                                'id'          => '',
                                'placeholder' => '',
                                'type' => 'text',
                                'checked' => $FieldData['conditional'],
                                'type_input' => 'checkbox',
                                'class' => 'field-conditional',
                                'tr_class' => 'conditional-field',
                                'tr_extra_attr' => 'data-not_equal="gallery,repeating_field"',
                            );
                    $conditional_rule_hide_row = 'cwp-hide-row';
                    if( isset($FieldData['conditional']) && $FieldData['conditional'] == 1 ){
                        $conditional_rule_hide_row = '';
                    }
                    $field_settings['field_conditional_rule'] = array(
                                'label' => esc_html__('Show this field if', 'cubewp-framework'),
                                'name' => 'cwp[fields]['.$FieldData['name'].'][conditional_field]',
                                'name_operator' => 'cwp[fields]['.$FieldData['name'].'][conditional_operator]',
                                'name_value' => 'cwp[fields]['.$FieldData['name'].'][conditional_value]',
                                'type' => 'conditional',
                                'id'          => '',
                                'value_operator' => $FieldData['conditional_operator'],
                                'value_value' => $FieldData['conditional_value'],
                                'options' => array(
                                    '!empty'  =>  esc_html__('Has any value', 'cubewp-framework'),
                                    'empty'   =>  esc_html__('Has no value', 'cubewp-framework'),
                                    '=='      =>  esc_html__('Value is equal to', 'cubewp-framework'),
                                    '!='      =>  esc_html__('Value is not equal to', 'cubewp-framework'),
                                ),
                                'default_value' => $FieldData['default_value'],
                                'tr_class' => 'conditional-rule '. esc_attr($conditional_rule_hide_row),
                                'select_extra_attr' => 'data-value="'. $FieldData['conditional_field'] .'"',
                                'class' => '',
                                'select_class' => 'field-appearance',
                            );
                    $field_settings = apply_filters( 'cubewp/custom_fields/single/field/add', $field_settings, $FieldData);
        
                    foreach( $field_settings as $field_setting ){

                        $fields = apply_filters("cubewp/admin/posttype/{$field_setting['type']}/customfield", '', $field_setting);
                        $html .= apply_filters( 'cubewp/custom_fields/single/field/output', $fields, $field_setting);
                    }
                    
                    
                    $html .= '<tr class="sub-fields-holder">';
                        $html .= '<td>';
                            $html .= '<label>'. esc_html__('Sub Fields', 'cubewp-framework') .'</label>';
                        $html .= '</td>';
                        $html .= '<td>';
                            $html .= '<div class="sub-fields">';
                                $html .= self::get_sub_fields($FieldData['sub_fields'], $FieldData['name']);
                            $html .= '</div>';
                            $html .= '<button class="button button-primary add-sub-field" data-parent_field="'. $FieldData['name'] .'" type="button">'. esc_html__('Add Sub Field', 'cubewp-framework') .'</button>';
                        $html .= '</td>';
                    $html .= '</tr>';
                    $html .= '</tbody>
                </table>
            </div>
        </div>';
        return apply_filters( 'cwp_custom_field_settings_html', $html, $FieldData);
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
    public static function add_new_sub_field( $FieldData = array() , $parent_field = ''  ) {
        $defaults = array(
            'label'                =>   '',
            'name'                 =>   'cwp_field_'. rand(10000000,1000000000000),
            'type'                 =>   '',
            'description'          =>   '',
            'default_value'        =>   '',
            'placeholder'          =>   '',
            'options'              =>   '',
            'multiple'             =>   0,
            'select2_ui'           =>   0,
            'auto_complete'        =>   0,
            'filter_post_types'    =>   '',
            'appearance'           =>   '',
            'required'             =>   '',
            'validation_msg'       =>   '',
            'id'                   =>   'cwp_field_'. rand(10000000,1000000000000),
            'class'                =>   '',
        );
        $FieldData = wp_parse_args( $FieldData, $defaults );
        $field_settings = array();
        
        $field_settings['field_label'] = array(
                        'label' => esc_html__('Field Label', 'cubewp-framework'),
                        'name' => 'cwp[sub_fields]['. $parent_field .']['.$FieldData['name'].'][label]',
                        'type' => 'text',
                        'id' => '',
                        'class' => 'field-label',
                        'placeholder' => esc_html__('Put your field label here', 'cubewp-framework'),
                        'value' => $FieldData['label'],
                        'extra_attrs' =>    'maxlength=30 ',
                        'required'    =>    true,
        );
        $field_settings['field_name'] = array(
                        'label' => esc_html__('Field Name', 'cubewp-framework'),
                        'name' => 'cwp[sub_fields]['. $parent_field .']['.$FieldData['name'].'][name]',
                        'type' => 'text',
                        'id' => '',
                        'class' => 'cubewp-locked-field field-name',
                        'placeholder' => esc_html__('Put your field name here', 'cubewp-framework'),
                        'value' => $FieldData['name'],
                        'extra_attrs' =>    'maxlength=20 ',
                        'required'    =>    true,
        );
        $field_settings['field_type'] = array(
                        'label' => esc_html__('Field Type', 'cubewp-framework'),
                        'id' => '',               
                        'name' => 'cwp[sub_fields]['. $parent_field .']['.$FieldData['name'].'][type]',
                        'type' => 'dropdown',
                        'options' => self::cwp_form_sub_field_types(),
                        'value' => $FieldData['type'],
                        'placeholder' => '',
                        'option-class' => 'form-option option',
                        'class' => 'field-type',
        );
        $field_settings['field_desc'] = array(
                        'label' => esc_html__('description', 'cubewp-framework'),
                        'id' => '',
                        'name' => 'cwp[sub_fields]['. $parent_field .']['.$FieldData['name'].'][description]',
                        'type' => 'textarea',
                        'placeholder' => esc_html__('Write description about this field', 'cubewp-framework'),
                        'value' => $FieldData['description'],
        );
        $field_settings['field_default_value'] = array(
                        'label' => esc_html__('Default Value', 'cubewp-framework'),
                        'name' => 'cwp[sub_fields]['. $parent_field .']['.$FieldData['name'].'][default_value]',
                        'type' => 'text',
                        'placeholder' => esc_html__('Default Value', 'cubewp-framework'),
                        'value' => $FieldData['default_value'],
                        'tr_class' => 'conditional-field',
                        'class' => 'field-default-value',
                        'id' => '',
                        'tr_extra_attr' => 'data-equal="text,textarea,number"',
        );
        $field_settings['field_placeholder'] = array(
                        'label' => esc_html__('Placeholder', 'cubewp-framework'),
                        'name' => 'cwp[sub_fields]['. $parent_field .']['.$FieldData['name'].'][placeholder]',
                        'type' => 'text',
                        'placeholder' => esc_html__('Put your field placeholder here', 'cubewp-framework'),
                        'value' => $FieldData['placeholder'],
                        'tr_class' => 'conditional-field',
                        'class' => 'field-placeholder',
                        'id' => '',
                        'tr_extra_attr' => 'data-equal="dropdown,text,textarea,number,email,url"',
        );
        $field_settings['field_options'] = array(
                        'label' => esc_html__('Options', 'cubewp-framework'),
                        'name' => 'cwp[sub_fields]['. $parent_field .']['.$FieldData['name'].']',
                        'type' => 'options',
                        'id' => 'field-options-'. str_replace('cwp_field_', '', $FieldData['name']),
                        'options' => $FieldData['options'],
                        'default_value' => $FieldData['default_value'],
                        'tr_class' => 'field-options-row conditional-field',
                        'tr_extra_attr' => 'data-equal="dropdown,checkbox,radio"',                        
        );
         $field_settings['field_multiple_values'] = array(
                        'label' => esc_html__('Multiple', 'cubewp-framework'),
                        'name' => 'cwp[sub_fields]['. $parent_field .']['.$FieldData['name'].'][multiple]',
                        'value' => '1',
                        'placeholder' => '',
                        'type' => 'text',
                        'checked' => $FieldData['multiple'],
                        'type_input' => 'checkbox',
                        'class' => 'field-multiple-checkbox checkbox',
                        'id' => 'field-multiple-'. str_replace('cwp_field_', '', $FieldData['name']),
                        'tr_class' => 'conditional-field',
                        'tr_extra_attr' => 'data-equal="dropdown"',
                        'extra_label' => esc_html__('Multiple Values', 'cubewp-framework'),
        );
        $field_settings['field_select2_ui'] = array(
                        'label' => esc_html__('Select 2', 'cubewp-framework'),
                        'name' => 'cwp[sub_fields]['. $parent_field .']['.$FieldData['name'].'][select2_ui]',
                        'value' => '1',
                        'placeholder' => '',
                        'type' => 'text',
                        'checked' => $FieldData['select2_ui'],
                        'type_input' => 'checkbox',
                        'class' => 'field-multiple-checkbox checkbox',
                        'id' => 'field-select-ui-'. str_replace('cwp_field_', '', $FieldData['name']),
                        'tr_class' => 'conditional-field',
                        'tr_extra_attr' => 'data-equal="dropdown,post"',
                        'extra_label' => esc_html__('Select2 UI', 'cubewp-framework'),
        );
        $field_settings['field_autocomplete_ui'] = array(
                        'label' => esc_html__('Autocomplete', 'cubewp-framework'),
                        'name' => 'cwp[sub_fields]['. $parent_field .']['.$FieldData['name'].'][auto_complete]',
                        'value' => '1',
                        'placeholder' => '',
                        'type' => 'text',
                        'checked' => $FieldData['auto_complete'],
                        'type_input' => 'checkbox',
                        'class' => 'field-multiple-checkbox checkbox',
                        'id' => 'field-auto_complete-'. str_replace('cwp_field_', '', $FieldData['name']),
                        'tr_class' => 'conditional-field',
                        'tr_extra_attr' => 'data-equal="post"',
                        'extra_label' => esc_html__('Autocomplete', 'cubewp-framework'),
                );
        $field_settings['field_filter_post_types'] = array(
                        'label' => esc_html__('Filter by Post Types', 'cubewp-framework'),
                        'name' => 'cwp[sub_fields]['. $parent_field .']['.$FieldData['name'].'][filter_post_types]',
                        'type' => 'dropdown',
                        'id'          => '',
                        'options' => cwp_post_types(),
                        'value' => $FieldData['filter_post_types'],
                        'placeholder' => esc_html__('Select Post Type', 'cubewp-framework'),
                        'option-class' => 'form-option option',
                        'class' => 'field-filter-post-types',
                        'tr_class' => 'conditional-field',
                        'tr_extra_attr' => 'data-equal="post"',
                        'required'    =>    true,
                        'validation_msg'    =>  esc_html__('Please select Post-Type', 'cubewp-framework'),
        );
        $field_settings['field_appearance'] = array(
                        'label' => esc_html__('Field Appearance', 'cubewp-framework'),
                        'name' => 'cwp[sub_fields]['. $parent_field .']['.$FieldData['name'].'][appearance]',
                        'type' => 'dropdown',
                        'id'          => '',
                        'options' => array(
                            'select'        =>   __('Select', 'cubewp-framework'),
                            'multi_select'  =>   __('Multi Select', 'cubewp-framework'),
                            'checkbox'      =>   __('Checkbox', 'cubewp-framework'),
                        ),
                        'value' => $FieldData['appearance'],
                        'placeholder' => '',
                        'option-class' => 'form-option option',
                        'class' => 'field-appearance',
                        'tr_class' => 'conditional-field',
                        'tr_extra_attr' => 'data-equal="post"',
        );
        $field_settings['field_validation'] = array(
                        'label' => esc_html__('Validation', 'cubewp-framework'),
                        'name' => 'cwp[sub_fields]['. $parent_field .']['.$FieldData['name'].'][required]',
                        'value' => '1',
                        'placeholder' => '',
                        'type' => 'text',
                        'checked' => $FieldData['required'],
                        'type_input' => 'checkbox',
                        'class' => 'field-required-checkbox checkbox',
                        'id' => 'field-required-'. str_replace('cwp_field_', '', $FieldData['name']),
                        'tr_class' => 'conditional-field',
                        'tr_extra_attr' => 'data-not_equal="gallery,repeating_field,switch"',
                        'extra_label' => esc_html__('Required', 'cubewp-framework'),
        );
        $trclass = 'validation-msg-row cwp-hide-row conditional-field';
        if( isset($FieldData['required']) && $FieldData['required'] == 1 ){
            $trclass = 'validation-msg-row conditional-field';
        }
        $field_settings['field_validation_msg'] = array(
                        'label' => esc_html__('Validation error message', 'cubewp-framework'),
                        'name' => 'cwp[sub_fields]['. $parent_field .']['.$FieldData['name'].'][validation_msg]',
                        'value' => $FieldData['validation_msg'],
                        'placeholder' => esc_html__('Validation error message', 'cubewp-framework'),
                        'type' => 'text',
                        'type_input' => 'text',
                        'class' => 'field-validation-msg',
                        'id' => '',
                        'tr_class' => $trclass,                       
                        'tr_extra_attr' => 'data-not_equal="gallery,repeating_field"',
        );
        $field_settings['field_id'] = array(
                        'label' => esc_html__('ID', 'cubewp-framework'),
                        'name' => 'cwp[sub_fields]['. $parent_field .']['.$FieldData['name'].'][id]',
                        'type' => 'text',
                        'id' => '',
                        'class' => 'field-id',
                        'placeholder' => esc_html__('ID for css', 'cubewp-framework'),
                        'value' => $FieldData['id'],
        );
        $field_settings['field_class'] = array(
                        'label' => esc_html__('Class', 'cubewp-framework'),
                        'name' => 'cwp[sub_fields]['. $parent_field .']['.$FieldData['name'].'][class]',
                        'type' => 'text',
                        'id' => '',
                        'class' => 'field-class',
                        'placeholder' => esc_html__('Class for css', 'cubewp-framework'),
                        'value' => $FieldData['class'],
        );
        
        $field_settings = apply_filters( 'cubewp/custom_fields/single/subfield/add', $field_settings, $FieldData);
        $field_name   = !empty($FieldData['label']) ? $FieldData['label'] : esc_html__('Field Label', 'cubewp-framework');
        $closed_class = (isset($FieldData['label']) && $FieldData['label'] != '') ? 'closed' : '';
        $hide_class   = (isset($FieldData['label']) && $FieldData['label'] != '') ? 'hidden' : '';
        $field_type   = (isset($FieldData['type']) && $FieldData['type'] == '') ? 'text' : $FieldData['type'];
        $field_type   = isset($field_types[$field_type]) ? $field_types[$field_type] : $field_type;
        $html = '
        <div class="cwp-field-set cwp-add-form-feild">
            <div class="field-header sub-field-header '. $closed_class .'">
                <div class="field-title" data-label="'. esc_html__('Field Name', 'cubewp-framework') .'">
                    <div class="sub-field-order"><i class="fa fa-arrows-alt"></i></div>
                    <div class="field-label">'. esc_html($field_name) .'</div>
                    <div class="field-type">'. esc_html($field_type) .'</div>
                    <div class="field-slug">'. esc_html($FieldData['name']) .'</div>
                </div>
                <div class="field-actions">
                    <a class="edit-sub-field" href="javascript:void(0);"><span class="dashicons dashicons-edit"></span></a>
                    <a class="remove-field" href="javascript:void(0);"><span class="dashicons dashicons-trash"></span></a>
                </div>
            </div>
            <div class="cwp-sub-field-inner '. $hide_class .'">
                <table>
                    <tbody>';
                    foreach( $field_settings as $field_setting ){
                        $fields = apply_filters("cubewp/admin/posttype/{$field_setting['type']}/customfield", '', $field_setting);
                        $html .= apply_filters( 'cubewp/custom_fields/single/subfield/output', $fields, $field_setting);
                    }
                    $html .= '</tbody>
                </table>
            </div>
        </div>';
        return apply_filters( 'cwp_custom_sub_field_settings_html', $html, $FieldData);
    }

        
    /**
     * Method cwp_form_field_types
     *
     * @return array
     * @since  1.0.0
     */
    public static function cwp_form_field_types() {
        $field_types                        = array();
        $field_types['text']                = esc_html__('Text', 'cubewp-framework');
        $field_types['number']              = esc_html__('Number', 'cubewp-framework');
        $field_types['email']               = esc_html__('Email', 'cubewp-framework');
        $field_types['url']                 = esc_html__('URL', 'cubewp-framework');
        $field_types['password']            = esc_html__('Password', 'cubewp-framework');
        $field_types['textarea']            = esc_html__('Textarea', 'cubewp-framework');
        $field_types['wysiwyg_editor']      = esc_html__('Wysiwyg Editor', 'cubewp-framework');
        $field_types['oembed']              = esc_html__('oEmbed', 'cubewp-framework');
        $field_types['file']                = esc_html__('File', 'cubewp-framework');
        $field_types['image']               = esc_html__('Image', 'cubewp-framework');
        $field_types['gallery']             = esc_html__('Gallery', 'cubewp-framework');
        $field_types['switch']              = esc_html__('Switch', 'cubewp-framework');
        $field_types['dropdown']            = esc_html__('Dropdown', 'cubewp-framework');
        $field_types['checkbox']            = esc_html__('Checkbox', 'cubewp-framework');
        $field_types['radio']               = esc_html__('Radio Button', 'cubewp-framework');
        $field_types['google_address']      = esc_html__('Google Address', 'cubewp-framework');
        $field_types['date_picker']         = esc_html__('Date Picker', 'cubewp-framework');
        $field_types['date_time_picker']    = esc_html__('Date Time Picker', 'cubewp-framework');
        $field_types['time_picker']         = esc_html__('Time Picker', 'cubewp-framework');
        $field_types['post']                = esc_html__('Post', 'cubewp-framework');
        $field_types['taxonomy']            = esc_html__('Taxonomy', 'cubewp-framework');
        $field_types['user']                = esc_html__('User', 'cubewp-framework');
        $field_types['repeating_field']     = esc_html__('Repeating Field', 'cubewp-framework');
        return apply_filters('cubewp/post/custom_fields/types', $field_types);
    }
        
    /**
     * Method cwp_form_sub_field_types
     *
     * @return array
     * @since  1.0.0
     */
    private static function cwp_form_sub_field_types() {
        $field_types                   = array();
        $field_types['text']           = esc_html__('Text', 'cubewp-framework');
        $field_types['number']         = esc_html__('Number', 'cubewp-framework');
        $field_types['email']          = esc_html__('Email', 'cubewp-framework');
        $field_types['url']            = esc_html__('URL', 'cubewp-framework');
        $field_types['textarea']       = esc_html__('Textarea', 'cubewp-framework');
        $field_types['file']           = esc_html__('File', 'cubewp-framework');
        $field_types['image']          = esc_html__('Image', 'cubewp-framework');
        $field_types['switch']         = esc_html__('Switch', 'cubewp-framework');
        $field_types['dropdown']       = esc_html__('Dropdown', 'cubewp-framework');
        $field_types['checkbox']       = esc_html__('Checkbox', 'cubewp-framework');
        $field_types['radio']          = esc_html__('Radio Button', 'cubewp-framework');
        $field_types['google_address'] = esc_html__('Google Address', 'cubewp-framework');
        $field_types['date_picker']    = esc_html__('Date Picker', 'cubewp-framework');
        $field_types['time_picker']    = esc_html__('Time Picker', 'cubewp-framework');
        $field_types['post']           = esc_html__('Post', 'cubewp-framework');
        return apply_filters('cubewp/post/custom_fields/sub/types', $field_types);
    }
        
    /**
     * Method cwp_get_taxonomies_by_post_types
     *
     * @return array
     * @since  1.0.0
     */
    public static function cwp_get_taxonomies_by_post_types(){
        check_ajax_referer( 'cubewp_custom_fields_nonce', 'nonce' );
        $post_types = sanitize_text_field($_POST['post_types']);
        echo self::get_taxonomies_by_post_types( explode(',', $post_types) );
        wp_die();
    }
    /**
     * Method cwp_get_taxonomies_by_post_types
     * @param array $post_types
     * @param array $getTerms 
     * @return array
     * @since  1.0.0
     */
    private static function get_taxonomies_by_post_types( $post_types= array(), $getTerms = array() ){
        if(isset($post_types) && !empty($post_types)){
            $types = explode(",", implode(",", $post_types));
        }
        if(isset($getTerms) && !empty($getTerms)){
            $terms = explode(",", implode(",", $getTerms));
        }
        $types = isset($types) && !empty($types) ? $types : array();
        $terms2 = isset($terms) && !empty($terms) ? $terms : array();
        $html = '';
        if(isset($types) && !empty($types)){
            $taxonomies = get_object_taxonomies( $types, 'objects' );
            foreach($taxonomies as $single){
                $terms = get_terms( $single->name, array('hide_empty' => false, 'parent' => 0 ));
                if(isset($terms) && !empty($terms)){
             $html .= '<div class="postbox">
                    <div class="postbox-header">
                        <h2 class="hndle">'.esc_html__('Conditional with ', 'cubewp-framework').$single->name.esc_html__(' (Optional)', 'cubewp-framework').'</h2>
                    </div>
                    <div class="inside">
                        <div class="main">
                            <table class="form-table">
                                <tr>
                                    <td class="text-left">
                                        <ul class="cwp-checkbox-outer  margin-0">';
                                            foreach($terms as $term){
                                                if(is_array($terms2) && in_array($term->term_id, $terms2)){
                                                    $checked = 'checked';
                                                }else{
                                                    $checked = '';
                                                }
                                                $html .= '<li><input type="checkbox" class="" name="cwp[group][terms][]" placeholder="" '.$checked.' value="'. $term->term_id .'">'. $term->name .' </li>';
                                            }
                            $html .=   '</ul>
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <p class="description">'.esc_html__("Ignore this if you want to show this group's fields with all terms", 'cubewp-framework').'</p>
                    </div>
                </div>';
                }
            }
        }
        return $html;
    }
    
}