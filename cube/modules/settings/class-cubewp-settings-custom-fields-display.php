<?php
/**
 * display fields of custom fields.
 *
 * @version 1.0
 * @package cubewp/cube/modules/settings
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * CubeWp_Settings_Custom_Fields_Display
 */
class CubeWp_Settings_Custom_Fields_Display extends CubeWp_Custom_Fields_Processor {
        
    /**
     * Method cwp_custom_fields_run
     *
     * @return void
     * @since  1.0.0
     */
    public static function cwp_custom_fields_run(  ) {
        add_filter( 'cubewp/custom_fields/type', array(__CLASS__, 'custom_form_name'), 9 );
        self::save_group();
        self::group_display();
        self::add_new_group();
    }

    /**
     * Method custom_form_name
     *
     * @return string
     * @since  1.0.0
     */
    public static function custom_form_name() {
        return 'settings';
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
    public static function group_display()
    {
        if (isset($_GET['action']) && ('new' == $_GET['action'] || 'edit' == $_GET['action'])) {
            return;
        }
        $customFieldsGroupTable = new CubeWp_settings_Custom_Fields_Table();
        ?>
        <div class="wrap cwp-post-type-title flex-none margin-none">
            <div class="cwp-post-type-title-nav">
                <h1 class="wp-heading-inline"><?php esc_html_e("Custom Fields", 'cubewp-framework'); ?></h1>
                <nav class="nav-tab-wrapper wp-clearfix">
                    <a class="nav-tab" href="?page=custom-fields"><?php esc_html_e("Post Types", 'cubewp-framework'); ?></a>
                    <a class="nav-tab" href="?page=taxonomy-custom-fields"><?php esc_html_e('Taxonomies', 'cubewp-framework'); ?></a>
                    <a class="nav-tab" href="?page=user-custom-fields"><?php esc_html_e('User Roles', 'cubewp-framework'); ?></a>
                    <a class="nav-tab nav-tab-active" href="?page=settings-custom-fields"><?php esc_html_e('Settings', 'cubewp-framework'); ?></a>
                </nav>
            </div>
            <a href="<?php echo CubeWp_Submenu::_page_action('settings-custom-fields', 'new'); ?>" class="page-title-action">+ <?php esc_html_e('Add New', 'cubewp-framework'); ?></a>
        </div>
        <hr class="wp-header-end">
        <?php $customFieldsGroupTable->prepare_items(); ?>
        <form method="post">
            <input type="hidden" name="page" value="settings-custom-fields">
            <?php $customFieldsGroupTable->display(); ?>
        </form>

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
            'cwp_settings' => '',
            'fields'       => '',
            'sub_fields'   => '',
            'terms'        => '',
            'user_roles'    => '',
            'description'  => '',
        );
        $group = wp_parse_args( $group, $defaults );
        
        ?>
        <div class="wrap">
        <form id="post" class="cwpgroup" method="post" action="" enctype="multipart/form-data">
            
            <div class="wrap cwp-post-type-title width-40 margin-bottom-0 margin-left-minus-20  margin-right-0">
				<?php echo self::_title();    ?>
				<?php echo self::save_button(); ?>
			</div>
			<hr class="wp-header-end">
            <input type="hidden" name="cwp_group_nonce" value="<?php echo wp_create_nonce( basename( __FILE__ ) ); ?>">
            <input type="hidden" class="" name="cwp[group][id]" value="<?php echo esc_attr($group['id']); ?>">
            <div id="poststuff"  class="padding-0">
            <div id="post-body" class="metabox-holder columns-2">
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
                        <div class="cwp-group-fields-head">
                            <div class="cwp-field-order-head">
                            </div> 
                            <div class="cwp-fields-title-head">   
                                <div class="cwp-field-label-head">
                                    <?php esc_html_e('Field Label', 'cubewp-framework'); ?>
                                </div>
                                <div class="cwp-field-name-head">
                                    <?php esc_html_e('Field Name', 'cubewp-framework'); ?>
                                </div>
                                <div class="cwp-field-type-head">
                                    <?php esc_html_e('Field Type', 'cubewp-framework'); ?>
                                </div>
                            </div>
                            <div class="cwp-fields-actions-head">
                                <?php esc_html_e('Actions', 'cubewp-framework'); ?>
                            </div>
                        </div>
                        <div class="cwp-group-fields-content">
                            <?php echo self::get_fields($group['fields'], $group['sub_fields']); ?>
                        </div>
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
            return '<h1>'. esc_html(__('Edit Custom Fields Group (Settings)', 'cubewp-framework')) .'</h1>';
        } else {
            return '<h1>'. esc_html(__('Create Custom Fields Group (Settings)', 'cubewp-framework')) .'</h1>';
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

    public static function save_group() {
        
        if (isset($_POST['cwp']['group'])) {

            $group           = isset($_POST['cwp']['group'])   ? $_POST['cwp']['group']      : array();
            $groupID         = isset($group['id'])             ? sanitize_text_field($group['id'])          : '';
            $groupName       = isset($group['name'])           ? sanitize_text_field($group['name'])        : '';
            $groupDesc       = isset($group['description'])    ? sanitize_text_field($group['description']) : '';
            $groupOrder      = isset($group['order'])          ? sanitize_text_field($group['order'])       : 0;

            if (!empty($groupName)) {
                if (isset($_POST['cwp_save_group'])) {
                    $post_data          =   array(
                        'post_type'     =>  'cwp_settings_fields',
                        'post_title'    =>  $groupName,
                        'post_content'  =>  $groupDesc,
                        'post_status'   =>  'publish',
                    );
                    $post_id = wp_insert_post($post_data);
                } else if (isset($_POST['cwp_edit_group']) && !empty($groupID)) {
                    $post_data         =   array(
                        'ID'           =>  $groupID,
                        'post_title'   =>  $groupName,
                        'post_content' =>  $groupDesc,
                    );
                    wp_update_post($post_data);
                    $post_id = $groupID;
                }
                if(isset($groupOrder) && is_numeric($groupOrder) && $groupOrder > 0){
                    update_post_meta($post_id, '_cwp_group_order', $groupOrder);
                }else{
                    update_post_meta($post_id, '_cwp_group_order', 0);
                }
                
            }
            self::save_custom_fields($_POST['cwp'],$post_id,'post_types');
        
            if (!empty($post_id) ) {
                wp_redirect( CubeWp_Submenu::_page_action('settings-custom-fields') );
            }
        }
        
    }
    
}