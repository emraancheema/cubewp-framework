<?php

/**
 * Creates the submenu item for the plugin.
 *
 * @package Custom_Admin_Settings
 * Creates the submenu item for the plugin.
 *
 * Registers a new menu item under 'Tools' and uses the dependency passed into
 * the constructor in order to display the page corresponding to this menu item.
 *
 * @package CubeWp_Taxonomy_Custom_Fields
 */
class CubeWp_Taxonomy_Custom_Fields {
    
    public static function manage_taxonomy_custom_fields() {
        self::taxonomy_custom_fields_display();
        self::edit_taxonomy_custom_fields();
        self::save_taxonomy_field();
        
    }
    
    private static function taxonomy_custom_fields_display()
    {
        if (isset($_GET['action']) && ('new' == $_GET['action'] || 'edit' == $_GET['action'])) {
            return;
        }

        $taxonomycustomFieldsTable = new CubeWp_Taxonomy_Custom_Fields_Table();
        ?>
        <div class="wrap cwp-post-type-wrape margin-40">
            <div class="wrap cwp-post-type-title flex-none margin-none">
                <div class="cwp-post-type-title-nav">
                    <h1 class="wp-heading-inline"><?php esc_html_e("Custom Fields", 'cubewp-framework'); ?></h1>
                    <nav class="nav-tab-wrapper wp-clearfix">
                        <a class="nav-tab " href="?page=custom-fields"><?php esc_html_e('Post Types', 'cubewp-framework'); ?></a>
                        <a class="nav-tab nav-tab-active" href="?page=taxonomy-custom-fields"><?php esc_html_e('Taxonomies', 'cubewp-framework'); ?></a>
                        <a class="nav-tab" href="?page=user-custom-fields"><?php esc_html_e('User Roles', 'cubewp-framework'); ?></a>
                        <a class="nav-tab" href="?page=settings-custom-fields"><?php esc_html_e('Settings', 'cubewp-framework'); ?></a>
                    </nav>
                </div>
                <a href="<?php echo CubeWp_Submenu::_page_action('taxonomy-custom-fields', 'new'); ?>" class="page-title-action">+ <?php esc_html_e('Add New', 'cubewp-framework'); ?></a>
            </div>
            <hr class="wp-header-end">
            <?php $taxonomycustomFieldsTable->prepare_items(); ?>
            <form method="post">
                <input type="hidden" name="page" value="taxonomy-custom-fields">
                <?php $taxonomycustomFieldsTable->display(); ?>
            </form>
        </div>
    <?php
    }
    
    private static function edit_taxonomy_custom_fields() {
        if(!isset($_GET['action'])){
            return;
        }

        
        $FieldData = array();
        $tax_custom_fields = CWP()->get_custom_fields( 'taxonomy' );
        
        if(isset($_GET['fieldid']) && $_GET['fieldid'] != '' ){
            $tax_custom_field = array_column($tax_custom_fields, sanitize_text_field($_GET['fieldid']));
            $FieldData = isset($tax_custom_field[0]) ? $tax_custom_field[0] : array();
        }
        
        $defaults = array(
            'name'           => '',
            'slug'           => 'cwp_field_'. rand(10000000,1000000000000),
            'type'           => '',
            'description'    => '',
            'placeholder'    => '',
            'default_value'  => '',
            'taxonomies'     => '',
        );
        $FieldData       = wp_parse_args( $FieldData, $defaults );

        $field_settings['field_name'] = array(
                'label' => esc_html__('Title of Field', 'cubewp-framework'),
                'name' => 'cwp_tax_fields[name]',
                'type' => 'text',
                'id' => '',
                'class' => 'field-title',
                'value' => $FieldData['name'],
                'placeholder' => esc_html__('Put your field name here', 'cubewp-framework'),
                'required' => true,
        );
        $field_settings['field_slug'] = array(
                'label' => esc_html__('Slug of Field', 'cubewp-framework'),
                'name' => 'cwp_tax_fields[slug]',
                'type' => 'text',
                'id' => '',
                'class' => 'cubewp-locked-field field-name',
                'value' => $FieldData['slug'],
                'placeholder' => esc_html__('Put your field slug here', 'cubewp-framework'),
                'extra_input_name' => 'cwp_tax_fields[old_slug]',
                'extra_input_class' => 'field-old-slug',
                'required' => true,
        );
        $field_settings['field_types'] = array(
                'label' => esc_html__('Field Type', 'cubewp-framework'),
                'name' => 'cwp_tax_fields[type]',
                'type' => 'dropdown',
                'options' => self::cwp_taxonomy_field_types(),
                'class' => 'field-type',
                'option-class' => 'form-option option',
                'id' => 'field-type-'. str_replace('cwp_field_', '', $FieldData['slug']),
                'value' => $FieldData['type'],
                'placeholder' => '',
        );
        $field_settings['field_desc'] = array(
                'label' => esc_html__('Description', 'cubewp-framework'),
                'name' => 'cwp_tax_fields[description]',
                'type' => 'textarea',
                'class' => 'field-desc',
                'placeholder' => esc_html__('Write description about this field', 'cubewp-framework'),
                'value' => $FieldData['description'],
        );
        $field_settings['field_default_value'] = array(
                'label' => esc_html__('Default Value', 'cubewp-framework'),
                'name' => 'cwp_tax_fields[default_value]',
                'type' => 'text',
                'placeholder' => esc_html__('Default Value', 'cubewp-framework'),
                'value' => $FieldData['default_value'],
                'tr_class' => 'conditional-field',
                'class' => 'field-default-value',
                'id' => '',
                'tr_extra_attr' => 'data-equal="text,textarea"',
        );
        $field_settings['field_placeholder'] = array(
                'label' => esc_html__('Placeholder', 'cubewp-framework'),
                'name' => 'cwp_tax_fields[placeholder]',
                'type' => 'text',
                'placeholder' => esc_html__('Put your field placeholder here', 'cubewp-framework'),
                'value' => $FieldData['placeholder'],
                'tr_class' => 'conditional-field',
                'class' => 'field-placeholder',
                'id' => '',
                'tr_extra_attr' => 'data-equal="text,textarea"',
        );
        $field_settings = apply_filters( 'cwp_taxonomy_custom_field_settings', $field_settings, $FieldData);
        
        ?>

        <div class="wrap">            
            <form method="post" action=""  id="post">
                <div class="wrap cwp-post-type-title width-40  margin-bottom-0 margin-left-minus-20  margin-right-0">
                    <?php echo self::_title();    ?>
                    <?php echo self::save_button(); ?>
                </div>
				<hr class="wp-header-end">
                <div id="poststuff"  class="padding-0">
                    <div id="post-body" class="metabox-holder columns-2">
                        <div id="postbox-container-1" class="postbox-container">
                        <div id="side-sortables" class="meta-box-sortables ui-sortable">
                            <div class="postbox">
                            <div class="postbox-header">
                                <h2 class="hndle"><?php esc_html_e('Select Taxonomy For This Field', 'cubewp-framework'); ?></h2>
                            </div>
                            <div class="inside">
                                <div class="main">
                                    <table class="form-table cwp-validation">
                                        <tr class="required" data-validation_msg="">
                                            <td class="text-left">
                                                <ul class="cwp-checkbox-outer margin-0">
                                                    <?php
                                                       echo apply_filters('cubewp/admin/taxonomies/taxonomy/customfield', '', array(
                                                            'id'             =>    'taxonomies-list',
                                                            'name'           =>    'cwp_taxonomies[]]',
                                                            'value'          =>    $FieldData['taxonomies'],
                                                            'class'          =>    'cwp-group',
                                                            'placeholder'    =>    esc_html__('Type new group name here..', 'cubewp-framework'),
                                                            'label'          =>    esc_html__('Taxonomies', 'cubewp-framework'),
                                                            'required'       =>    true,
                                                        ));
                                                    ?>
                                                </ul>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                        </div>
                        </div>
                        <div id="postbox-container-2" class="postbox-container postbox-container-top">
                            <div class="postbox">
                                <div class="postbox-header">
                                    <h2><span><?php esc_html_e('Field Settings', 'cubewp-framework'); ?></span></h2>
                                </div>
                                <div class="inside">
                                    <div class="main">
                                        <table class="form-table cwp-validation">
                                            <tbody class="cwp-field-set">
                                                <?php
                                                foreach( $field_settings as $field_setting ){
                                                    $fields = apply_filters("cubewp/admin/taxonomies/{$field_setting['type']}/customfield", '', $field_setting);
                                                    echo apply_filters( 'cubewp/taxonomies/custom_fields/single/field/output', $fields, $field_setting);
                                                }
                                                ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <?php
    }
    
    private static function save_taxonomy_field() {
            
        if(isset($_POST['cwp_taxonomies']) && !empty($_POST['cwp_taxonomies'])){
            if(isset($_POST['cwp_tax_fields']) && !empty($_POST['cwp_tax_fields'])){
                
                $cwp_tax_custom_fields = CWP()->get_custom_fields( 'taxonomy' );
                $cwp_tax_custom_fields = isset($cwp_tax_custom_fields) && !empty($cwp_tax_custom_fields) ? $cwp_tax_custom_fields : array();
                $field_options         =  isset($_POST['cwp_tax_fields']) ? CubeWp_Sanitize_text_Array($_POST['cwp_tax_fields']) : array();

                if(isset($cwp_tax_custom_fields) && !empty($cwp_tax_custom_fields)){
                    foreach($cwp_tax_custom_fields as $taxonomy => $cwp_tax_custom_field){
                        if(isset($cwp_tax_custom_fields[$taxonomy][$field_options['old_slug']])){
                            unset($cwp_tax_custom_fields[$taxonomy][$field_options['old_slug']]);
                        }
                        if(isset($cwp_tax_custom_fields[$taxonomy]) && empty($cwp_tax_custom_fields[$taxonomy])){
                            unset($cwp_tax_custom_fields[$taxonomy]);
                        }
                    }
                }
                
                foreach($_POST['cwp_taxonomies'] as $taxonomy){
                    if(isset($_POST['cwp_taxonomies']) && !empty($_POST['cwp_taxonomies'])){
                        $taxnomies = CubeWp_Sanitize_text_Array($_POST['cwp_taxonomies']);
                        $field_options['taxonomies'] = implode(',', $taxnomies);
                    }
                    $cwp_tax_custom_fields[$taxonomy][$field_options['slug']] = $field_options;
                }
                CWP()->update_custom_fields( 'taxonomy', $cwp_tax_custom_fields );
                wp_redirect( CubeWp_Submenu::_page_action('taxonomy-custom-fields') );
                exit;
            }
        }
    }
    private static function _title() {
        if (isset($_GET['action']) && ('edit' == $_GET['action'] && !empty($_GET['groupid']))) {
            return '<h1>'. esc_html(__('Edit Taxonomy Field', 'cubewp-framework')) .'</h1>';
        } else {
            return '<h1>'. esc_html(__('Create New Taxonomy Field', 'cubewp-framework')) .'</h1>';
        }
    }
     private static function save_button() {
        if(isset($_GET['action']) && ('edit' == $_GET['action'] && !empty($_GET['groupid']))){            
            $name = 'cwp_save_field';
        }else{
            $name = 'cwp_save_field';
        }
        return '<input type="submit" class="cwp-custom-fields-group-btn button button-primary button-large cwp-save-button" name="'.$name.'" value="'.__( 'Save field', 'cubewp-framework' ).'" />';
	}
    
    public static function cwp_taxonomy_field_types() {
        $field_types                   = array();
        $field_types['text']           = esc_html__('Text', 'cubewp-framework');
        $field_types['textarea']       = esc_html__('Textarea', 'cubewp-framework');
        $field_types['image']          = esc_html__('Image', 'cubewp-framework');
        $field_types['color']          = esc_html__('Color', 'cubewp-framework');
        $field_types['url']    = esc_html__('URL', 'cubewp-framework');
        $field_types['gallery']    = esc_html__('Gallery', 'cubewp-framework');
        $field_types['oembed']    = esc_html__('oEmbed', 'cubewp-framework');
        $field_types['google_address']    = esc_html__('Google Address', 'cubewp-framework');
        
        return apply_filters('cwp_taxonomy_custom_field_types', $field_types);
    }

}