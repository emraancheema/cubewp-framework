<?php
class CubeWp_User_Custom_Fields_UI extends CubeWp_Custom_Fields_Processor{
    
    
    
    public static function manage_user_fields() {
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
        return 'user';
    }

    private static function group_display()
    {
        if (isset($_GET['action']) && ('new' == $_GET['action'] || 'edit' == $_GET['action'])) {
            return;
        }

        $customFieldsGroupTable = new CubeWp_User_Custom_Fields_Table();
        ?>
        <div class="wrap cwp-post-type-wrape margin-40">
            <div class="wrap cwp-post-type-title flex-none margin-none">
                <div class="cwp-post-type-title-nav">
                    <h1 class="wp-heading-inline"><?php esc_html_e("Custom Fields", 'cubewp-framework'); ?></h1>
                    <nav class="nav-tab-wrapper wp-clearfix">
                        <a class="nav-tab " href="?page=custom-fields"><?php esc_html_e('Post Types', 'cubewp-framework'); ?></a>
                        <a class="nav-tab " href="?page=taxonomy-custom-fields"><?php esc_html_e('Taxonomies', 'cubewp-framework'); ?></a>
                        <a class="nav-tab nav-tab-active" href="?page=user-custom-fields"><?php esc_html_e('User Roles', 'cubewp-framework'); ?></a>
                        <a class="nav-tab" href="?page=settings-custom-fields"><?php esc_html_e('Settings', 'cubewp-framework'); ?></a>
                    </nav>
                </div>
                <a href="<?php echo CubeWp_Submenu::_page_action('user-custom-fields', 'new'); ?>" class="page-title-action">+ <?php esc_html_e('Add New', 'cubewp-framework'); ?></a>
            </div>
            <hr class="wp-header-end">
            <?php $customFieldsGroupTable->prepare_items(); ?>
            <form method="post">
                <input type="hidden" name="page" value="user-custom-fields">
                <?php $customFieldsGroupTable->display(); ?>
            </form>
        </div>

    <?php
    }
    
    private static function add_new_group() {
        if(isset($_GET['action']) && ('new' == $_GET['action'] || 'edit' == $_GET['action'])){
            self::edit_group();
        }
    }
    private static function _title() {
        if (isset($_GET['action']) && ('edit' == $_GET['action'] && !empty($_GET['groupid']))) {
            return '<h1>'. esc_html(__('Edit Custom Fields Group (User Roles)', 'cubewp-framework')) .'</h1>';
        } else {
            return '<h1>'. esc_html(__('Create Custom Fields Group (User Roles)', 'cubewp-framework')) .'</h1>';
        }
    }
         
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
            'user_roles'   => '',
            'description'  => '',
        );
        $group = wp_parse_args( $group, $defaults );
        
        ?>
        <div class="wrap">            
            <form id="post" class="cwpgroup" method="post" action="" enctype="multipart/form-data">
                <div class="wrap cwp-post-type-title  margin-bottom-0 width-40 margin-left-minus-20  margin-right-0">
                    <?php echo self::_title();    ?>
                    <?php echo self::save_button(); ?>
                </div>
				<hr class="wp-header-end">
                <input type="hidden" name="cwp_group_nonce" value="<?php echo wp_create_nonce( basename( __FILE__ ) ); ?>">
                <input type="hidden" class="" name="cwp[group][id]" value="<?php echo esc_attr($group['id']); ?>">
                <div id="poststuff"  class="padding-0">
                    <div id="post-body" class="metabox-holder columns-2">
                        <div id="postbox-container-1" class="postbox-container">
                            <div id="side-sortables" class="meta-box-sortables ui-sortable">
                                <div class="postbox">
                                    <div class="postbox-header">
                                        <h2 class="hndle"><?php esc_html_e('Group Relation With User Role', 'cubewp-framework'); ?></h2>
                                    </div>
                                    <div class="inside">
                                        <div class="main">
                                            <table class="form-table cwp-validation">
                                                <tr class="required" data-validation_msg="">
                                                    <td class="text-left">
                                                        <ul class="cwp-checkbox-outer margin-0">
                                                            <?php
                                                               echo self::_get_user_roles($group['user_roles']);
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
                        <div class='clear'></div>
                    </div>
                </div>
            </form>
        </div>
    <?php
    }
    
    private static function save_button() {
        if(isset($_GET['action']) && ('edit' == $_GET['action'] && !empty($_GET['groupid']))){            
            $name = 'cwp_edit_group';
        }else{
            $name = 'cwp_save_group';
        }
        return '<input type="submit" class="cwp-custom-fields-group-btn cwp-save-button button button-primary button-large" name="'.$name.'" value="'.__( 'Save Group', 'cubewp-framework' ).'" />';
    }

    protected static function _get_user_roles($get_user_roles) {
        $user_roles        = cwp_get_user_roles();
     
        $get_user_roles    = isset($get_user_roles) && !empty($get_user_roles) ? explode(',', $get_user_roles) : array();
     
        $html = '';
        if(isset($user_roles) && !empty($user_roles)){
            foreach($user_roles as $key => $user_role){
                $checked = '';
                if(isset($get_user_roles) && in_array($key, $get_user_roles)){
                    $checked = ' checked="checked"';
                }
                $html .= '<li class="pull-left">';
                $html .= '<input type="checkbox" class="cwp-custom-fields-post-types" name="cwp[group][user_roles][]" placeholder="" '.$checked.' value="'. esc_attr($key) .'">'. esc_html($user_role['name']) .' <br>';
                $html .= '</li>';
            }
        }
        return $html;
    }
    
    public static function save_group() {
        
        if (isset($_POST['cwp']['group'])) {

            $group           = isset($_POST['cwp']['group'])   ? $_POST['cwp']['group']      : array();
            $groupID         = isset($group['id'])             ? sanitize_text_field($group['id'])          : '';
            $groupName       = isset($group['name'])           ? sanitize_text_field($group['name'])        : '';
            $groupDesc       = isset($group['description'])    ? sanitize_text_field($group['description']) : '';
            $groupOrder      = isset($group['order'])          ? sanitize_text_field($group['order'])       : 0;
            $groupUserRoles  = isset($group['user_roles'])     ? CubeWp_Sanitize_text_Array($group['user_roles'])  : array();

            if (!empty($groupName)) {
                if (isset($_POST['cwp_save_group'])) {
                    $post_data          =   array(
                        'post_type'     =>  'cwp_user_fields',
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
                if (!empty($post_id) && !empty($groupUserRoles)) {
                    $groupUserRoles = implode(",", $groupUserRoles);
                    update_post_meta($post_id, '_cwp_group_user_roles', $groupUserRoles);
                }else{
                    delete_post_meta($post_id, '_cwp_group_user_roles');
                }
                
            }
            self::save_custom_fields($_POST['cwp'],$post_id,'user');
        
            if (!empty($post_id) ) {
                wp_redirect( CubeWp_Submenu::_page_action('user-custom-fields') );
            }
        }
        
    }
}