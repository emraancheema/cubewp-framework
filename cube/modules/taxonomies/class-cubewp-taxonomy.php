<?php
if (!defined('ABSPATH'))
    exit;

class CubeWp_taxonomy {

    public function __construct() {
        add_action( 'cubewp_taxonomies', array( $this, 'create_new_ctax' ) );
    }
    
    public function create_new_ctax() {
        $this->save_CWPterm();
        $this->ctax_form_display();
        $this->add_new_ctax();
    }

    private function get_tax() {
        $defaultTEX = get_option('cwp_custom_taxonomies');
        return $defaultTEX;
    }

    private static function get_postTypes($types) {
       
        $html     = '';
        $checked = '';
        $get_CustomTypes = CWP_all_post_types();
        if(isset($get_CustomTypes) && !empty($get_CustomTypes)){
            foreach ($get_CustomTypes as $slug => $label) {
                if (isset($types) && !empty($types)) {
                    $checked = '';
                    foreach ($types as $type) {
                        if ($slug == $type) {
                            $checked = 'checked="checked"';
                        }
                    }
                }
                $html .= '<li class="pull-left">';                    
                $input_attrs = array(
                    'type'           =>    'checkbox',
                    'id'             =>    $slug,
                    'class'          =>    'form-checkbox checkbox',
                    'name'           =>    'cwp[CWPterm][post_types][]',
                    'value'          =>    $slug,
                    'wrap'           =>    false,
                );
                $extra_attrs = '';
                if(isset($checked) && !empty($checked)){
                    $extra_attrs .= ' checked="checked"';
                }
                $input_attrs['extra_attrs'] = $extra_attrs;
                $html .= cwp_render_text_input($input_attrs);
                $html .= CubeWp_Admin::cwp_label( $slug, $label );
                $html .= self::assigned_tax($slug);
                
                $html .= '</li>';
            }
        }else{
            $html .= '<li class="pull-left">'.esc_html__('No post type', 'cubewp-framework').'</li>';
        }
        return $html;
    }
    
    private static function assigned_tax($type = ''){
        $taxonomies = cwp_tax_by_PostType( $type, 'objects' );                
        if(isset($taxonomies) && !empty($taxonomies)){
            $comma = $tax_names = '';
            foreach($taxonomies as $taxonomy){
                if(isset($_GET['CWPtermid']) && $_GET['CWPtermid'] == $taxonomy->name ){
                    return;
                }
                $tax_names .= $comma . $taxonomy->label;
                $comma = ', ';
            }
            return '<span style="font-size:10px"> (' . $tax_names .')</span>';
        }
    }

    private function get_taxonomiesBySlug() {
        $get_CustomTaxonomies = get_option('cwp_custom_taxonomies');
        if (!empty($get_CustomTaxonomies)) {
            if (isset($_GET['action']) && 'edit' == $_GET['action'] && !empty($_GET['CWPtermid'])) {
                $singleCPT = $get_CustomTaxonomies[sanitize_text_field($_GET['CWPtermid'])];
                return $singleCPT;
            }
        }
        return;
    }

    public static function CWP_taxonomies() {
        $C_taxonomies = get_option('cwp_custom_taxonomies');
        if ($C_taxonomies) {
            foreach ($C_taxonomies as $single_ctax) {
                if(isset($single_ctax['post_types']) && !empty($single_ctax['post_types'])){
                    $labels = array(
                        'name'                 => _x($single_ctax['name'], 'taxonomy general name', 'textdomain'),
                        'singular_name'        => _x($single_ctax['singular'], 'taxonomy singular name', 'textdomain'),
                        'search_items'         => sprintf(__('Search %s', 'cubewp-framework'), $single_ctax['name']),
                        'all_items'            => sprintf(__('All %s', 'cubewp-framework'), $single_ctax['name']),
                        'parent_item'          => sprintf(__('Parent %s', 'cubewp-framework'), $single_ctax['name']),
                        'parent_item_colon'    => sprintf(__('Parent %s:', 'cubewp-framework'), $single_ctax['name']),
                        'edit_item'            => sprintf(__('Edit %s', 'cubewp-framework'), $single_ctax['singular']),
                        'update_item'          => sprintf(__('Update %s', 'cubewp-framework'), $single_ctax['singular']),
                        'add_new_item'         => sprintf(__('Add new %s', 'cubewp-framework'), $single_ctax['singular']),
                        'new_item_name'        => sprintf(__('New %s name', 'cubewp-framework'), $single_ctax['singular']),
                        'menu_name'            => sprintf(__('%s', 'cubewp-framework'), $single_ctax['name']),
                        'back_to_items'        => sprintf(__('Back to %s', 'cubewp-framework'), $single_ctax['name']),
                        'not_found'            => sprintf(__('No %s found', 'cubewp-framework'), $single_ctax['name']),
                    );

                    $args = array(
                        'hierarchical'         => cwp_boolean_value($single_ctax['hierarchical']),
                        'labels'               => $labels,
                        'public'               => cwp_boolean_value($single_ctax['public']),
                        'show_ui'              => cwp_boolean_value($single_ctax['show_ui']),
                        'show_admin_column'    => cwp_boolean_value($single_ctax['show_admin_column']),
                        'query_var'            => cwp_boolean_value($single_ctax['query_var']),
                        'show_in_rest'         => cwp_boolean_value($single_ctax['show_in_rest']),
                        'rewrite'              => array( 'slug' => $single_ctax['slug'] ),
                    );

                    register_taxonomy( $single_ctax['slug'], $single_ctax['post_types'], $args );
                }
            }
        }
    }

    public function save_CWPterm() {
        if (isset($_POST['cwp']['CWPterm'])) {
            
            if( ! wp_verify_nonce( $_POST['cwp_taxonomy_nonce'], basename( __FILE__ ) ) )
                return '';
            
            
            $ctax_slug = sanitize_text_field($_POST['cwp']['CWPterm']['slug']);
            $ctax = array(
                $ctax_slug                => array(
                    'slug'                => sanitize_text_field($_POST['cwp']['CWPterm']['slug']),
                    'name'                => sanitize_text_field($_POST['cwp']['CWPterm']['name']),
                    'singular'            => sanitize_text_field($_POST['cwp']['CWPterm']['singular']),
                    'post_types'          => isset($_POST['cwp']['CWPterm']['post_types']) ? CubeWp_Sanitize_text_Array($_POST['cwp']['CWPterm']['post_types']) : '',
                    'hierarchical'        => sanitize_text_field($_POST['cwp']['CWPterm']['hierarchical']),
                    'public'              => sanitize_text_field($_POST['cwp']['CWPterm']['public']),
                    'show_ui'             => sanitize_text_field($_POST['cwp']['CWPterm']['show_ui']),
                    'show_admin_column'   => sanitize_text_field($_POST['cwp']['CWPterm']['show_admin_column']),
                    'query_var'           => sanitize_text_field($_POST['cwp']['CWPterm']['query_var']),
                    'show_in_rest'        => sanitize_text_field($_POST['cwp']['CWPterm']['show_in_rest']),
                )
            );

            $get_CustomTypes = get_option('cwp_custom_taxonomies');
            if ($get_CustomTypes) {
                $dataMerge = array_merge($get_CustomTypes, $ctax);
            } else {
                $dataMerge = $ctax;
            }
            update_option('cwp_custom_taxonomies', $dataMerge);
            wp_redirect( CubeWp_Submenu::_page_action('cubewp-taxonomies') );
        }
    }

    public function add_new_ctax() {
        if (isset($_GET['action']) && ('new' == $_GET['action'] || 'edit' == $_GET['action'])) {
            $this->tax_form_edit();
        }
    }
    
    public function ctax_form_display()
    {
        if (isset($_GET['action']) && ('new' == $_GET['action'] || 'edit' == $_GET['action'])) {
            return;
        }

        $customFieldsTaxonomiesTable = new CubeWp_Taxonomies_List_Table();
?>
        <div class="wrap cwp-post-type-wrape">
            <div class="wrap cwp-post-type-title width-40">
                <h1 class="wp-heading-inline"><?php esc_html_e('Custom Taxonomies', 'cubewp-framework'); ?></h1>
                <a href="<?php echo CubeWp_Submenu::_page_action('cubewp-taxonomies', 'new'); ?>" class="page-title-action">+ <?php esc_html_e('Add New', 'cubewp-framework'); ?></a>
            </div>
            <hr class="wp-header-end">
            <?php $customFieldsTaxonomiesTable->prepare_items(); ?>
            <form method="post">
                <input type="hidden" name="page" value="cubewp-post-type">
                <?php $customFieldsTaxonomiesTable->display(); ?>
            </form>
        </div>
    <?php
    }
    
    public function tax_form_edit() {

        
        $CWPterm = $this->get_taxonomiesBySlug();
        $defaults = array(
            'name'                => '',
            'singular'            => '',
            'slug'                => '',
            'post_types'          => '',
            'hierarchical'        => true,
            'public'              => true,
            'show_ui'             => true,
            'show_admin_column'   => true,
            'query_var'           => true,
            'show_in_rest'        => true,
        );
        $CWPterm  = wp_parse_args($CWPterm, $defaults);
        ?>
         <div class="wrap">            
            <form id="post" class="cwptaxonomyform" method="post" action="" enctype="multipart/form-data">
                <div class="wrap cwp-post-type-title width-40 margin-bottom-0 margin-left-minus-20  margin-right-0">
                    <?php echo self::_title();    ?>
                    <?php echo self::save_button(); ?>
                </div>
                <hr class="wp-header-end">
                <input type="hidden" name="cwp_taxonomy_nonce" value="<?php echo wp_create_nonce(basename(__FILE__)); ?>">
                <div id="poststuff" class="padding-0">
                    <div id="post-body" class="metabox-holder columns-2">
                        <?php echo self::taxonomy_side_actions($CWPterm); ?>
                        <div id="postbox-container-2" class="postbox-container postbox-container-top">

                            <?php echo self::taxonomy_basic_settings($CWPterm); ?>
                            <?php echo self::taxonomy_options($CWPterm); ?>

                        </div>
                    </div>
                </div>
            </form>
        </div>
        <?php
    }
    
    /**
     * page title
     * page title split for edit or add taxonomy form. 
     * @since 1.0
     * @version 1.0
     */  
    private static function taxonomy_options( array $CWPterm ) {
    ?>
        <div class="postbox">
            <div class="postbox-header">
                <h2><span><?php esc_html_e('Advanced Settings', 'cubewp-framework'); ?></span></h2>
            </div>
            <div class="inside">
                <div class="main">
                    <table class="form-table">
                        <tbody>
                            <?php
                            echo apply_filters('cubewp/admin/taxonomy/dropdown/field', '', array(
                                'id'             =>    'public',
                                'name'           =>    'cwp[CWPterm][public]',
                                'value'          =>    $CWPterm['public'],
                                'label'          =>    esc_html__('Public', 'cubewp-framework'),
                                'options'        =>    array( 0 => 'False', 1 => 'True' ),
                                'description'    =>    esc_html__( '(default: true) Whether a taxonomy is intended for use publicly either via the admin interface or by front-end users.', 'cubewp-framework' )
                            ));

                            echo apply_filters('cubewp/admin/taxonomy/dropdown/field', '', array(
                                'id'             =>    'show_ui',
                                'name'           =>    'cwp[CWPterm][show_ui]',
                                'value'          =>    $CWPterm['show_ui'],
                                'label'          =>    esc_html__('Show UI', 'cubewp-framework'),
                                'options'        =>    array( 0 => 'False', 1 => 'True' ),
                                'description'    =>    esc_html__( '(default: true) Whether to generate a default UI for managing this custom taxonomy.', 'cubewp-framework' )
                            ));

                            echo apply_filters('cubewp/admin/taxonomy/dropdown/field', '', array(
                                'id'             =>    'show_admin_column',
                                'name'           =>    'cwp[CWPterm][show_admin_column]',
                                'value'          =>    $CWPterm['show_admin_column'],
                                'label'          =>    esc_html__('Show Admin Column', 'cubewp-framework'),
                                'options'        =>    array( 0 => 'False', 1 => 'True' ),
                                'description'    =>    esc_html__( '(default: true) Whether to allow automatic creation of taxonomy columns on associated post-types.', 'cubewp-framework' )
                            ));

                            echo apply_filters('cubewp/admin/taxonomy/dropdown/field', '', array(
                                'id'             =>    'hierarchical',
                                'name'           =>    'cwp[CWPterm][hierarchical]',
                                'value'          =>    $CWPterm['hierarchical'],
                                'label'          =>    esc_html__('Hierarchical', 'cubewp-framework'),
                                'options'        =>    array( 0 => 'False', 1 => 'True' ),
                                'description'    =>    esc_html__( '(default: true) Whether the taxonomy can have parent-child relationships.', 'cubewp-framework' )
                            ));

                            echo apply_filters('cubewp/admin/taxonomy/dropdown/field', '', array(
                                'id'             =>    'query_var',
                                'name'           =>    'cwp[CWPterm][query_var]',
                                'value'          =>    $CWPterm['query_var'],
                                'label'          =>    esc_html__('Query Var', 'cubewp-framework'),
                                'options'        =>    array( 0 => 'False', 1 => 'True' ),
                                'description'    =>    esc_html__( '(default: true) Sets the query_var key for this taxonomy.', 'cubewp-framework' )
                            ));

                            echo apply_filters('cubewp/admin/taxonomy/dropdown/field', '', array(
                                'id'             =>    'show_in_rest',
                                'name'           =>    'cwp[CWPterm][show_in_rest]',
                                'value'          =>    $CWPterm['show_in_rest'],
                                'label'          =>    esc_html__('Show in REST API', 'cubewp-framework'),
                                'options'        =>    array( 0 => 'False', 1 => 'True' ),
                                'description'    =>    esc_html__( '(Custom Post Type UI default: true) Whether to show this taxonomy data in the WP REST API.', 'cubewp-framework' )
                            ));
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    <?php
    }
        
    /**
     * page title
     * page title split for edit or add taxonomy form. 
     * @since 1.0
     * @version 1.0
     */  
    private static function taxonomy_basic_settings( array $CWPterm ) {
        ?>
        <div class="postbox">
            <div class="postbox-header">
                <h2><span><?php esc_html_e('Basic Settings', 'cubewp-framework'); ?></span></h2>
            </div>
            <div class="inside">
                <div class="main">
                    <table class="form-table cwp-validation">
                        <tbody>
                            <?php
                            echo apply_filters('cubewp/admin/taxonomy/text/field', '', array(
                                'id'             =>    'taxonomy_slug',
                                'name'           =>    'cwp[CWPterm][slug]',
                                'value'          =>    $CWPterm['slug'],
                                'label'          =>    esc_html__('Taxonomy Slug', 'cubewp-framework'),
                                'placeholder'    =>    esc_html__('Enter taxonomy slug', 'cubewp-framework'),
                                'required'       =>    true,
                                'extra_attrs'    =>    'maxlength=30' . isset($CWPterm['slug']) && !empty($CWPterm['slug']) ? 'readonly' : '',
                                'tooltip'        =>    'Give a slug for this taxonomy. Which will be used to get this taxonomy data',
                            ));

                            echo apply_filters('cubewp/admin/taxonomy/text/field', '', array(
                                'id'             =>    'name',
                                'name'           =>    'cwp[CWPterm][name]',
                                'value'          =>    $CWPterm['name'],
                                'label'          =>    esc_html__('Plural Label', 'cubewp-framework'),
                                'placeholder'    =>    esc_html__('Enter taxonomy plural label', 'cubewp-framework'),
                                'required'       =>    true,
                                'tooltip'        =>    'Give a name for this taxonomy. Enter taxonomy name with "s" at the end',
                            ));

                            echo apply_filters('cubewp/admin/taxonomy/text/field', '', array(
                                'id'             =>    'singular',
                                'name'           =>    'cwp[CWPterm][singular]',
                                'value'          =>    $CWPterm['singular'],
                                'label'          =>    esc_html__('Singular Label', 'cubewp-framework'),
                                'placeholder'    =>    esc_html__('Enter taxonomy singular label', 'cubewp-framework'),
                                'required'       =>    true,
                                'tooltip'        =>    'Give a name for this taxonomy. Enter taxonomy name without "s" at the end',
                            ));
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    <?php
    }
        
    /**
     * page title
     * page title split for edit or add taxonomy form. 
     * @since 1.0
     * @version 1.0
     */  
    private static function taxonomy_side_actions( array $CWPterm ) {
        ?>
        <div id="postbox-container-1" class="postbox-container">
            <div id="side-sortables" class="meta-box-sortables ui-sortable">
            <div class="postbox">
                <div class="postbox-header">
                    <h2 class="hndle"><?php esc_html_e('Assign To Post Type', 'cubewp-framework'); ?></h2>
                </div>
                <div class="inside">
                    <div class="main">
                        <table class="form-table">
                            <tbody>
                                <?php
                                $output = CubeWp_Admin::cwp_tr_start();
                                    $output .= '<td class="text-left">
                                        <ul class="cwp-checkbox-outer margin-0">
                                            '. self::get_postTypes($CWPterm['post_types']) .'
                                        </ul>
                                    </td>';
                                $output .= CubeWp_Admin::cwp_tr_end();
                                echo cubewp_core_data($output);
                                ?>
                            </tbody>
                        </table>
                        <a id="category-add-toggle" href="<?php echo CubeWp_Submenu::_page_action('cubewp-post-types','new'); ?>" class="hide-if-no-js taxonomy-add-new"><?php esc_html_e('+Add New Post Type', 'cubewp-framework'); ?></a>
                    </div>
                </div>
            </div>                        
            </div>
        </div>
    <?php
    }
    
    /**
     * page title
     * page title split for edit or add taxonomy form. 
     * @since 1.0
     * @version 1.0
     */  
    private static function _title() {
        if (isset($_GET['action']) && ('edit' == $_GET['action'] && !empty($_GET['CWPtermid']))) {
            return '<h1>'. esc_html(__('Edit Taxonomy', 'cubewp-framework')) .'</h1>';
        } else {
            return '<h1>'. esc_html(__('Create New Taxonomy', 'cubewp-framework')) .'</h1>';

        }
    }

    private static function save_button() {
        if (isset($_GET['action']) && ('edit' == $_GET['action'] && !empty($_GET['CWPtermid']))) {
            $button_label = __('Update', 'cubewp-framework');
        } else {
            $button_label = __('Save', 'cubewp-framework');
        }
        return '<input type="submit" class="cwp-save-button cwp-save-edit-post-button button button-primary button-large" name="Save" value="'. esc_html($button_label) .'" />';
    }

}
new CubeWp_taxonomy();