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
    public static $search_type = '';
    public function __construct() {
        parent::__construct(self::$cubewp_tb );
    }

    public function no_items() {
        echo '<div class="elementor-template_library-blank_state">
				<div class="elementor-blank_state">
				<i class="eicon-folder"></i>
				<h3>Create Your First Theme Template</h3>
				<p>Create theme template and edit with Elementor and become theme developer with zero coding knowledge.</p>
				<a href="#" class="ctb-add-new-template page-title-action">'.esc_html__('Add New Template', 'cubewp-framework').'</a>
			</div>
		</div>';
    }

    public function display() {
        $search_type = self::$search_type;
        $found_type = 'all';
        if(!empty($search_type) && !in_array($search_type, array('activated', 'deactivated','all'))){
            $found_type = array_filter($this->items, function($template) use ($search_type) {
                return isset($template['type']) && $template['type'] === $search_type;
            });
        }
        if (!empty($found_type)) {
            $singular = $this->_args['singular'];
            $this->display_tablenav( 'top' );

            $this->screen->render_screen_reader_content( 'heading_list' );
            ?>
                <table class="wp-list-table <?php echo implode( ' ', $this->get_table_classes() ); ?>">
                        <?php $this->print_table_description(); ?>
                    <thead>
                    <tr>
                        <?php $this->print_column_headers(); ?>
                    </tr>
                    </thead>

                    <tbody id="the-list"
                        <?php
                        if ( $singular ) {
                            echo " data-wp-lists='list:$singular'";
                        }
                        ?>
                        >
                        <?php $this->display_rows_or_placeholder(); ?>
                    </tbody>

                    <tfoot>
                    <tr>
                        <?php $this->print_column_headers( false ); ?>
                    </tr>
                    </tfoot>

                </table>
            <?php
            $this->display_tablenav( 'bottom' );
        }else{
            echo $this->no_items();
        }
    }

    function column_name( $item ) {

        $tb_demo_id = (isset($item['tb_demo_id']) && !empty($item['tb_demo_id'])) ? '&tb_demo_id='.$item['tb_demo_id'] : '';
        
        $status = get_post_status(  $item['ID'] ) == 'inactive' ? '<span class="post-state inactive"> Inactive </span>' : '';
        $status = empty($status) ? '<span class="post-state"> Active </span>' : $status;
        
        $title = '<strong>' . ucfirst($item['name']) .'</strong> '.$status;
        $actions = [
            'edit' => '<a href="#" data-tid="'. $item['ID'] .'" data-tlocation="'. $item['location'] .'" data-ttype="'. $item['type'] .'" data-tname="'. $item['name'] .'" class="ctb-add-new-template ctb-edit-template">Edit</a>',
        ];
        $actions['delete'] = sprintf( '<a href="%s">'. esc_html__('Delete', 'cubewp-framework') .'</a>', CubeWp_Submenu::_page_action('cubewp-theme-builder','delete', '&template_id='.absint( $item['ID']), '&_wpnonce='.wp_create_nonce( 'cwp_delete_group' )));
        
        $actions['edit-with-elementor'] = sprintf( '<a href="%s">'. esc_html__('Edit with Elementor', 'cubewp-framework') .'</a>', admin_url('post.php?post='.absint( $item['ID']). '&action=elementor'.$tb_demo_id));
        
        
        $status_btn = get_post_status(  $item['ID'] ) == 'inactive' ? true : false;
        if($status_btn){
            $actions['Activate'] = sprintf( '<a href="%s">'. esc_html__('Activate', 'cubewp-framework') .'</a>', CubeWp_Submenu::_page_action('cubewp-theme-builder','activate', '&template_id='.absint( $item['ID']), '&_wpnonce='.wp_create_nonce( 'cwp_template_status' )));
        }else{
            $actions['Deactivate'] = sprintf( '<a href="%s">'. esc_html__('Deactivate', 'cubewp-framework') .'</a>', CubeWp_Submenu::_page_action('cubewp-theme-builder','deactivate', '&template_id='.absint( $item['ID']), '&_wpnonce='.wp_create_nonce( 'cwp_template_status' )));
        }
        return $title . $this->row_actions( $actions );
    }

    function column_location( $item ) {

        return isset($item['location_display']) ? $item['location_display']: '';
    }

    function column_type( $item ) {

        return isset($item['type']) ? ucfirst($item['type']): '';
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

    /**
     * Get the post type name using the slug.
     *
     * @param string $slug The post type slug.
     * @return string The post type name or an empty string if not found.
     */
    public static function get_post_type_slug($string) {
        // Check if the string is empty
        if (empty($string)) {
            return '';
        }
    
        // Split the string by underscores
        $parts = explode('_', $string);
    
        // Check if the second part exists and return it
        if (isset($parts[1])) {
            return $parts[1];
        }
    
        // Return an empty string if no second part is found
        return '';
    }

    /**
     * Get the first post ID of the post type using the slug.
     *
     * @param string $slug The post type slug.
     * @return int The first post ID or 0 if no posts are found.
     */
    public static function get_first_post_id_by_post_type($slug) {
        // Query to get the first post ID of the post type
        $query_args = array(
            'post_type'      => $slug,
            'posts_per_page' => 1,
            'order'          => 'ASC',
            'orderby'        => 'ID',
            'fields'         => 'ids',
        );
        $query = new WP_Query($query_args);

        // Return the first post ID or 0 if no posts are found
        return ($query->have_posts()) ? $query->posts[0] : 0;
    }

    public static function check_if_post_available_by_status($post_status = 'publish') {

        $args = array(
            'numberposts' => 1,
            'fields'      => 'ids',
            'post_type'   => 'cubewp-tb',
            'post_status' => $post_status,
        );
        $posts = get_posts( $args );
        if(isset($posts) && !empty($posts)){
            return true;
        }
        return false;
    }

    public static function convert_to_capitalized_words($string) {
        // Check if the string is empty
        if (empty($string)) {
            return $string;
        }
    
        // Split the string by underscores
        $words = explode('_', $string);
    
        // Remove empty elements caused by consecutive underscores
        $words = array_filter($words, function($word) {
            return !empty($word);
        });
    
        // Capitalize each word
        $words = array_map('ucfirst', $words);
    
        // Join the words back together with a space
        $capitalizedString = implode(' ', $words);
    
        return $capitalizedString;
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
        // Getting type of template
        self::$search_type =  isset($_GET['cwp-template-type']) && !empty($_GET['cwp-template-type']) ? $_GET['cwp-template-type'] : 'activated';
        $search_type = self::$search_type;

        if(!empty($search_type) && $search_type == 'activated'){
            $args['post_status'] = 'publish';
        }elseif(!empty($search_type) && $search_type == 'deactivated'){
            $args['post_status'] = 'inactive';
        }

        if(!empty($search_type) && !in_array($search_type, array('activated', 'deactivated','all'))){
            $args['meta_query']  = array(
                array(
                    'key'   => 'template_type',
                    'value' => $search_type,
                    'compare' => '=',
                )
            );
        }

        $posts = get_posts( $args );
        if(isset($posts) && !empty($posts)){
            $_data = array();
            foreach($posts as $post){
                $type = get_post_meta($post, 'template_type', true);
                $tem_location = get_post_meta($post, 'template_location', true);

                $data = array();
                $data['ID']          = $post;
                $data['name']  = get_the_title($post);
                $data['type']  = $type;
                $data['location']  = $tem_location;
                $data['location_display']  = self::convert_to_capitalized_words($tem_location);

                if($type == 'single'){
                    $post_type_slug = self::get_post_type_slug($tem_location);
                    $first_post_id = self::get_first_post_id_by_post_type($post_type_slug);

                    $data['tb_demo_id']  = $first_post_id;
                }
                
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