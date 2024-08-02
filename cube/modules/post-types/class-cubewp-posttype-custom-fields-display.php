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
class CubeWp_Posttype_Custom_Fields_Display extends CubeWp_Custom_Fields_Processor {
        
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
        $customFieldsGroupTable = new CubeWp_Post_Types_Custom_Fields_Table();
        ?>
        <div class="wrap cwp-post-type-title flex-none margin-none">
            <div class="cwp-post-type-title-nav">
                <h1 class="wp-heading-inline"><?php esc_html_e("Custom Fields", 'cubewp-framework'); ?></h1>
                <nav class="nav-tab-wrapper wp-clearfix">
                    <a class="nav-tab nav-tab-active" href="?page=custom-fields"><?php esc_html_e("Post Types", 'cubewp-framework'); ?></a>
                    <a class="nav-tab" href="?page=taxonomy-custom-fields"><?php esc_html_e('Taxonomies', 'cubewp-framework'); ?></a>
                    <a class="nav-tab" href="?page=user-custom-fields"><?php esc_html_e('User Roles', 'cubewp-framework'); ?></a>
                    <a class="nav-tab" href="?page=settings-custom-fields"><?php esc_html_e('Settings', 'cubewp-framework'); ?></a>
                </nav>
            </div>
            <a href="<?php echo CubeWp_Submenu::_page_action('custom-fields', 'new'); ?>" class="page-title-action">+ <?php esc_html_e('Add New', 'cubewp-framework'); ?></a>
        </div>
        <hr class="wp-header-end">
        <?php $customFieldsGroupTable->prepare_items(); ?>
        <form method="post">
            <input type="hidden" name="page" value="custom-fields">
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
                <div id="postbox-container-1" class="postbox-container">
                    <div id="side-sortables" class="meta-box-sortables ui-sortable">
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
            return '<h1>'. esc_html(__('Edit Custom Fields Group (Post Types)', 'cubewp-framework')) .'</h1>';
        } else {
            return '<h1>'. esc_html(__('Create Custom Fields Group (Post Types)', 'cubewp-framework')) .'</h1>';
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
            $html .= '<input type="checkbox" class="cwp-custom-fields-post-types" name="cwp[group][types][]" '.$checked.' value="'.$type.'">'.$type.' <br>';
            $html .= '</li>';
        }
        return $html;
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