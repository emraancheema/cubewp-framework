<?php

/**
 * CubeWp Builder List table
 *
 * @version 1.0.0
 * @package cubewp/cube/mobules/theme builder
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


/**
 * CubeWP_TB_List_Table
 */

class CubeWp_Theme_Builder_Table extends WP_List_Table {

    public static $cubewp_tb = array();
    public function __construct() {
        parent::__construct(self::$cubewp_tb );
    }

    public function no_items() {
        _e('No theme builders found.', 'cubewp-framework');
    }

    function column_name( $item ) {
        
        $status = get_post_status(  $item['ID'] ) == 'inactive' ? '<span class="post-state inactive"> Inactive </span>' : '';
        $status = empty($status) ? '<span class="post-state"> Active </span>' : $status;
        
        $title = '<strong>' . ucfirst($item['name']) .'</strong> '.$status;
        $actions = [
            'edit' => '<a href="#" data-tid="'. $item['ID'] .'" data-tlocation="'. $item['location'] .'" data-ttype="'. $item['type'] .'" data-tname="'. $item['name'] .'" class="ctb-add-new-template ctb-edit-template">Edit</a>',
        ];
        $actions['delete'] = sprintf( '<a href="%s">'. esc_html__('Delete', 'cubewp-framework') .'</a>', CubeWp_Submenu::_page_action('cubewp-theme-builder','delete', '&template_id='.absint( $item['ID']), '&_wpnonce='.wp_create_nonce( 'cwp_delete_group' )));
        
        $actions['edit-with-elementor'] = sprintf( '<a href="%s">'. esc_html__('Edit with Elementor', 'cubewp-framework') .'</a>', admin_url('post.php?post='.absint( $item['ID']). '&action=elementor'));
        
        
        $status_btn = get_post_status(  $item['ID'] ) == 'inactive' ? true : false;
        if($status_btn){
            $actions['Activate'] = sprintf( '<a href="%s">'. esc_html__('Activate', 'cubewp-framework') .'</a>', CubeWp_Submenu::_page_action('cubewp-theme-builder','activate', '&template_id='.absint( $item['ID']), '&_wpnonce='.wp_create_nonce( 'cwp_template_status' )));
        }else{
            $actions['Deactivate'] = sprintf( '<a href="%s">'. esc_html__('Deactivate', 'cubewp-framework') .'</a>', CubeWp_Submenu::_page_action('cubewp-theme-builder','deactivate', '&template_id='.absint( $item['ID']), '&_wpnonce='.wp_create_nonce( 'cwp_template_status' )));
        }
        return $title . $this->row_actions( $actions );
    }

    public function column_default( $item, $column_name ){
        return isset($item[$column_name]) ? $item[$column_name] : '-';
    }

    public function get_columns(){

        $columns = array(
            'cb'            =>   '<input type="checkbox" />',
            'name'    =>   esc_html__('Name', 'cubewp-framework'),
            'type'    =>   esc_html__('Template', 'cubewp-framework'),
            'location'    =>   esc_html__('Template Location', 'cubewp-framework'),
        );
        return $columns;
    }

    protected function column_cb( $item ) {
        return sprintf(
            '<input type="checkbox" name="%1$s[]" value="%2$s" />',
            'cwp_tb_bulk_action',  // Let's simply repurpose the table's singular label ("movie").
            $item['ID']                // The value of the checkbox should be the record's ID.
        );
    }

    protected function get_bulk_actions() {
        $actions = array(
            'delete' => _x( 'Delete', 'List table bulk action', 'cubewp-framework' ),
        );

        return $actions;
    }

    protected function process_bulk_action() {
        //cwp_pre($this->_args['plural']); exit;
        // Detect when a bulk action is being triggered.
        if ( 'delete' === $this->current_action() ) { 
            $nonce = esc_html( $_REQUEST['_wpnonce'] );
            if(wp_verify_nonce( $nonce, 'bulk-' . $this->_args['plural'] ) ) {
                if(isset($_REQUEST['cwp_tb_bulk_action'])){
                    $bulk_request = CubeWp_Sanitize_text_Array($_REQUEST['cwp_tb_bulk_action']);
                foreach($bulk_request as $post){
                    wp_delete_post($post, true);
                } 
                }                                
            }
        }
        if(isset($_REQUEST['action']) && $_REQUEST['action'] == 'delete') {
            $nonce = esc_html( $_REQUEST['_wpnonce'] );
            if(wp_verify_nonce( $nonce, 'cwp_delete_group')) {
                if(isset($_REQUEST['template_id'])){
                    wp_delete_post(sanitize_text_field($_REQUEST['template_id']), true);
                }
                wp_redirect( CubeWp_Submenu::_page_action('cubewp-theme-builder') );
            }
        }
        if(isset($_REQUEST['action']) && $_REQUEST['action'] == 'deactivate') {
            $nonce = esc_html( $_REQUEST['_wpnonce'] );
            if(wp_verify_nonce( $nonce, 'cwp_template_status')) {
                if(isset($_REQUEST['template_id'])){
                    self::deactivate_group($_REQUEST['template_id']);
                }
                wp_redirect( CubeWp_Submenu::_page_action('cubewp-theme-builder') );
            }
        }
        if(isset($_REQUEST['action']) && $_REQUEST['action'] == 'activate') {
            $nonce = esc_html( $_REQUEST['_wpnonce'] );
            if(wp_verify_nonce( $nonce, 'cwp_template_status')) {
                if(isset($_REQUEST['template_id'])){
                    self::activate_group($_REQUEST['template_id']);
                }
                wp_redirect( CubeWp_Submenu::_page_action('cubewp-theme-builder') );
            }
        }
        
    }

    public function deactivate_group($post_id = 0){
        $data = array(
            'ID' => $post_id,
            'post_type'   => 'cubewp-tb',
            'post_status' => 'inactive',
        );
        
        wp_update_post( $data );
    }
    public function activate_group($post_id = 0){
        $data = array(
            'ID' => $post_id,
            'post_type'   => 'cubewp-tb',
            'post_status' => 'publish',
        );
        
        wp_update_post( $data );
    }


    public function prepare_items() {
        global $wpdb; //This is used only if making any database queries

        /*
        * First, lets decide how many records per page to show
        */
        $per_page = 20;

        
        $columns  = $this->get_columns();
        $hidden   = array();
        $sortable = $this->get_sortable_columns();

        
        $this->_column_headers = array( $columns, $hidden, $sortable );

        
        $this->process_bulk_action();

        
        $args = array(
        'numberposts' => -1,
        'fields'      => 'ids',
        'post_type'   => 'cubewp-tb',
        'post_status' => array('inactive','publish')
        );

        $posts = get_posts( $args );
        if(isset($posts) && !empty($posts)){
            $_data = array();
            foreach($posts as $post){
                $data = array();
                $data['ID']          = $post;
                $data['name']  = get_the_title($post);
                $data['type']  = get_post_meta($post, 'template_type', true);
                $data['location']  = get_post_meta($post, 'template_location', true);
                $_data[] = $data;
            }
            $data = $_data;
        }else{
            $data = array();
        }
    
        $current_page = $this->get_pagenum();

        
        $total_items = count( $data );

        
        $data = array_slice( $data, ( ( $current_page - 1 ) * $per_page ), $per_page );

        
        $this->items = $data;

        
        $this->set_pagination_args( array(
            'total_items' => $total_items,                     // WE have to calculate the total number of items.
            'per_page'    => $per_page,                        // WE have to determine how many items to show on a page.
            'total_pages' => ceil( $total_items / $per_page ), // WE have to calculate the total number of pages.
        ) );
    }
}