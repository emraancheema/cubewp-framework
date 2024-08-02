<?php

class CubeWp_Post_Types_Custom_Fields_Table extends WP_List_Table{
    
    
    
    public static $customTax = array();
    function __construct(){
    global $status, $page;
        parent::__construct(self::$customTax );
    }
    
    
        /**
     * Define what data to show on each column of the table
     *
     * @param  Array $item Data
     * @param  String $column_name - Current column name
     *
     * @return Mixed
     */
    public function column_default( $item, $column_name ){
        return isset($item[$column_name]) ? $item[$column_name] : '-';
    }
    
    public function get_sortable_columns() {
      $sortable_columns = array(
        'group_name'  => array('group_name',false),
        'group_order' => array('group_order',false),
        'post_types'   => array('post_types',false),
        'taxonomies'   => array('taxonomies',false)
      );
      return $sortable_columns;
    }
    
   /**
     * Override the parent columns method. Defines the columns to use in your listing table
     *
     * @return Array
     */
    public function get_columns(){

        $columns = array(
            'cb'            =>   '<input type="checkbox" />',
            'group_name'    =>   esc_html__('Group Name', 'cubewp-framework'),
            'group_order'   =>   esc_html__('Order', 'cubewp-framework'),
            'description'   =>   esc_html__('Description', 'cubewp-framework'),
            'post_types'    =>   esc_html__('Post Types', 'cubewp-framework'),
            'taxonomies'    =>   esc_html__('Taxonomies', 'cubewp-framework'),
        );
        return $columns;
    }
    
    public function usort_reorder( $a, $b ) {
        // If no sort, default to title
        $orderby = ( ! empty( $_GET['orderby'] ) ) ? sanitize_text_field($_GET['orderby']) : 'group_name';
        // If no order, default to asc
        $order = ( ! empty($_GET['order'] ) ) ? sanitize_text_field($_GET['order']) : 'asc';
        // Determine sort order
        $result = strcmp( $a[$orderby], $b[$orderby] );
        // Send final sort direction to usort
        return ( $order === 'asc' ) ? $result : -$result;
    }
    
    /**
    * Method for group_name column
    * @param array $item an array of DB data
    * @return string
    */
    function column_group_name( $item ) {
        $group_status = get_post_status(  $item['ID'] ) == 'inactive' ? '<span class="post-state"><span class="dashicons dashicons-hidden"></span> Inactive</span>' : '';
        
        $title = sprintf( '<a href="%s"><strong>' . $item['group_name'] .'</strong></a>'.$group_status, CubeWp_Submenu::_page_action('custom-fields','edit', '&groupid='.absint( $item['ID']), '&_wpnonce='.wp_create_nonce( 'cwp_edit_group' )));
        $actions = [
            'edit' => sprintf( '<a href="%s">'. esc_html__('Edit', 'cubewp-framework') .'</a>', CubeWp_Submenu::_page_action('custom-fields','edit', '&groupid='.absint( $item['ID']), '&_wpnonce='.wp_create_nonce( 'cwp_edit_group' ))),
            'duplicate' => sprintf( '<a href="%s">'. esc_html__('Duplicate', 'cubewp-framework') .'</a>', CubeWp_Submenu::_page_action('custom-fields','duplicate', '&groupid='.absint( $item['ID']), '&_wpnonce='.wp_create_nonce( 'cwp_duplicate_group' ))),
        ];
        if( ! cubewp_custom_field_group_secure( absint( $item['ID'] ) ) ) {
            $actions['delete'] = sprintf( '<a href="%s">'. esc_html__('Delete', 'cubewp-framework') .'</a>', CubeWp_Submenu::_page_action('custom-fields','delete', '&groupid='.absint( $item['ID']), '&_wpnonce='.wp_create_nonce( 'cwp_delete_group' )));
        }
        if(!empty($group_status)){
            $actions['Activate'] = sprintf( '<a href="%s">'. esc_html__('Activate', 'cubewp-framework') .'</a>', CubeWp_Submenu::_page_action('custom-fields','activate', '&groupid='.absint( $item['ID']), '&_wpnonce='.wp_create_nonce( 'cwp_status_group' )));
        }else{
            $actions['Deactivate'] = sprintf( '<a href="%s">'. esc_html__('Deactivate', 'cubewp-framework') .'</a>', CubeWp_Submenu::_page_action('custom-fields','deactivate', '&groupid='.absint( $item['ID']), '&_wpnonce='.wp_create_nonce( 'cwp_status_group' )));
        }
        return $title . $this->row_actions( $actions );
    }
    /**
	 * Get value for checkbox column.
	 *
	 * REQUIRED if displaying checkboxes or using bulk actions! The 'cb' column
	 * is given special treatment when columns are processed. It ALWAYS needs to
	 * have it's own method.
	 *
	 * @param object $item A singular item (one full row's worth of data).
	 * @return string Text to be placed inside the column <td>.
	 */
	protected function column_cb( $item ) {
		return sprintf(
			'<input type="checkbox" name="%1$s[]" value="%2$s" />',
			'cwp_group_bulk_action',  // Let's simply repurpose the table's singular label ("movie").
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
		// Detect when a bulk action is being triggered.
		if ( 'delete' === $this->current_action() ) { 
            $nonce = esc_html( $_REQUEST['_wpnonce'] );
            if(wp_verify_nonce( $nonce, 'bulk-' . $this->_args['plural'] ) ) {
                if(isset($_REQUEST['cwp_group_bulk_action'])){

                    $bulk_request = CubeWp_Sanitize_text_Array($_REQUEST['cwp_group_bulk_action']);
                   foreach($bulk_request as $group){
                       new CubeWp_Update_Frontend_Forms(array('group_id'=>$group,'group_options'=>true));
                       wp_delete_post($group, true);
                   } 
                }                                
            }
        }
        if(isset($_REQUEST['action']) && $_REQUEST['action'] == 'delete') {
            $nonce = esc_html( $_REQUEST['_wpnonce'] );
            if(wp_verify_nonce( $nonce, 'cwp_delete_group')) {
                if(isset($_REQUEST['groupid'])){
                    new CubeWp_Update_Frontend_Forms(array('group_id'=>sanitize_text_field($_REQUEST['groupid']),'group_options'=>true));
                    wp_delete_post(sanitize_text_field($_REQUEST['groupid']), true);
                }
                wp_redirect( CubeWp_Submenu::_page_action('custom-fields') );
            }
        }
        if(isset($_REQUEST['action']) && $_REQUEST['action'] == 'duplicate') {
            $nonce = esc_html( $_REQUEST['_wpnonce'] );
            if(wp_verify_nonce( $nonce, 'cwp_duplicate_group')) {
                if(isset($_REQUEST['groupid'])){
                    self::duplicate_group($_REQUEST['groupid']);
                }
                wp_redirect( CubeWp_Submenu::_page_action('custom-fields') );
            }
        }
        if(isset($_REQUEST['action']) && $_REQUEST['action'] == 'deactivate') {
            $nonce = esc_html( $_REQUEST['_wpnonce'] );
            if(wp_verify_nonce( $nonce, 'cwp_status_group')) {
                if(isset($_REQUEST['groupid'])){
                    self::deactivate_group($_REQUEST['groupid']);
                }
                wp_redirect( CubeWp_Submenu::_page_action('custom-fields') );
            }
        }
        if(isset($_REQUEST['action']) && $_REQUEST['action'] == 'activate') {
            $nonce = esc_html( $_REQUEST['_wpnonce'] );
            if(wp_verify_nonce( $nonce, 'cwp_status_group')) {
                if(isset($_REQUEST['groupid'])){
                    self::activate_group($_REQUEST['groupid']);
                }
                wp_redirect( CubeWp_Submenu::_page_action('custom-fields') );
            }
        }
        
		
	}
    public function deactivate_group($post_id = 0){
        $data = array(
            'ID' => $post_id,
            'post_type'   => 'cwp_form_fields',
            'post_status' => 'inactive',
        );
          
        wp_update_post( $data );
    }
    public function activate_group($post_id = 0){
        $data = array(
            'ID' => $post_id,
            'post_type'   => 'cwp_form_fields',
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

		/*
		 * REQUIRED. Now we need to define our column headers. This includes a complete
		 * array of columns to be displayed (slugs & titles), a list of columns
		 * to keep hidden, and a list of columns that are sortable. Each of these
		 * can be defined in another method (as we've done here) before being
		 * used to build the value for our _column_headers property.
		 */
		$columns  = $this->get_columns();
		$hidden   = array();
		$sortable = $this->get_sortable_columns();

		/*
		 * REQUIRED. Finally, we build an array to be used by the class for column
		 * headers. The $this->_column_headers property takes an array which contains
		 * three other arrays. One for all columns, one for hidden columns, and one
		 * for sortable columns.
		 */
		$this->_column_headers = array( $columns, $hidden, $sortable );

		/**
		 * Optional. You can handle your bulk actions however you see fit. In this
		 * case, we'll handle them within our package just to keep things clean.
		 */
		$this->process_bulk_action();

		/*
		 * GET THE DATA!
		 * 
		 * Instead of querying a database, we're going to fetch the example data
		 * property we created for use in this plugin. This makes this example
		 * package slightly different than one you might build on your own. In
		 * this example, we'll be using array manipulation to sort and paginate
		 * our dummy data.
		 * 
		 * In a real-world situation, this is probably where you would want to 
		 * make your actual database query. Likewise, you will probably want to
		 * use any posted sort or pagination data to build a custom query instead, 
		 * as you'll then be able to use the returned query data immediately.
		 *
		 * For information on making queries in WordPress, see this Codex entry:
		 * http://codex.wordpress.org/Class_Reference/wpdb
		 */       
        $args = array(
          'numberposts' => -1,
          'fields'      => 'ids',
          'post_type'   => 'cwp_form_fields',
          'post_status' => array('inactive','publish')
        );

        $allGroups = get_posts( $args );
        if(isset($allGroups) && !empty($allGroups)){
            $_data = array();
            foreach($allGroups as $group){
                $Group_data = $this->get_group_by_ID($group);
                $data = array();
                $data['ID']          = $group;
                $data['group_name']  = get_the_title($group);
                $data['description'] = $Group_data['content'];
                $data['post_types']  = $Group_data['types'];
                $data['taxonomies']  = $Group_data['terms'];
                $data['group_order'] = $Group_data['group_order'];
                $_data[] = $data;
            }
            $data = $_data;
        }else{
            $data = array();
        }
		/*
		 * This checks for sorting input and sorts the data in our array of dummy
		 * data accordingly (using a custom usort_reorder() function). It's for 
		 * example purposes only.
		 *
		 * In a real-world situation involving a database, you would probably want
		 * to handle sorting by passing the 'orderby' and 'order' values directly
		 * to a custom query. The returned data will be pre-sorted, and this array
		 * sorting technique would be unnecessary. In other words: remove this when
		 * you implement your own query.
		 */
		usort( $data, array( $this, 'usort_reorder' ) );

		/*
		 * REQUIRED for pagination. Let's figure out what page the user is currently
		 * looking at. We'll need this later, so you should always include it in
		 * your own package classes.
		 */
		$current_page = $this->get_pagenum();

		/*
		 * REQUIRED for pagination. Let's check how many items are in our da  ta array.
		 * In real-world use, this would be the total number of items in your database,
		 * without filtering. We'll need this later, so you should always include it
		 * in your own package classes.
		 */
		$total_items = count( $data );

		/*
		 * The WP_List_Table class does not handle pagination for us, so we need
		 * to ensure that the data is trimmed to only the current page. We can use
		 * array_slice() to do that.
		 */
		$data = array_slice( $data, ( ( $current_page - 1 ) * $per_page ), $per_page );

		/*
		 * REQUIRED. Now we can add our *sorted* data to the items property, where
		 * it can be used by the rest of the class.
		 */
		$this->items = $data;

		/**
		 * REQUIRED. We also have to register our pagination options & calculations.
		 */
		$this->set_pagination_args( array(
			'total_items' => $total_items,                     // WE have to calculate the total number of items.
			'per_page'    => $per_page,                        // WE have to determine how many items to show on a page.
			'total_pages' => ceil( $total_items / $per_page ), // WE have to calculate the total number of pages.
		) );
	}
    

    public function get_group_by_ID($GroupID) {
        $groupType        = get_post_meta($GroupID, '_cwp_group_types');
        $group['types']   = implode(",", array_unique($groupType));
        $groupTerms       = get_post_meta($GroupID, '_cwp_group_terms');
        
        $group_terms = $comma = '';
        foreach($groupTerms as $groupTerm){
            $terms = explode(',', $groupTerm);
            foreach($terms as $term){
                $terms = get_term($term);
                if(isset($terms->name)){
                    $group_terms .= $comma.$terms->name;
                    $comma = ', ';
                }
                   
            }
        }
        
        $group['terms']       = $group_terms;
        $groupFields          = get_post_meta($GroupID, '_cwp_group_fields');
        $group['fields']      = implode(",",$groupFields);
        $group['title']       = get_post_field( 'post_title', $GroupID );
        $group['content']     = get_post_field( 'post_content', $GroupID );
        $group['group_order'] = get_post_meta( $GroupID, '_cwp_group_order', true );

        return $group;        
    }
    /**
     * Method save_group
     *
     * @return void
     * @since  1.0.0
     */
    protected static function duplicate_group($groupID) {
        $post = get_post( $groupID );
            
        $groupName       = $post->post_title.' - Copy';
        $groupDesc       = $post->post_content;
        $groupOrder      = get_post_meta($groupID, '_cwp_group_order', true);
        $groupTypes      = get_post_meta($groupID, '_cwp_group_types', true);
        $groupTerms      = get_post_meta($groupID, '_cwp_group_terms', true);
        $group_fields    = get_post_meta($groupID, '_cwp_group_fields', true);
        $group_sub_fields    = get_post_meta($groupID, '_cwp_group_sub_fields', true);

        if (!empty($groupName)) {
            $post_id = wp_insert_post(array(
                'post_type' => 'cwp_form_fields',
                'post_title' => $groupName,
                'post_content' => $groupDesc,
                'post_status' => 'publish',
            ));
            
            update_post_meta($post_id, '_cwp_group_order', $groupOrder);
            update_post_meta($post_id, '_cwp_group_types', $groupTypes);
            update_post_meta($post_id, '_cwp_group_terms', $groupTerms);
            
            if(isset($group_fields) && !empty($group_fields)){
                $group_fields = explode(',', $group_fields);
                foreach($group_fields as $field){
                    $field = get_field_options($field);
                    $fieldName = $field['name'];
                    $field['name'] = $field['name'].'copy';
                    $field['group_id'] = $post_id;
                    
                    if(isset($group_sub_fields) && !empty($group_sub_fields)){
                        $sub_fieldsdata = json_decode($group_sub_fields, true);
                        if(isset($sub_fieldsdata[$fieldName])){
                            foreach($sub_fieldsdata[$fieldName] as $sub_field){
                                
                                $sub_field = get_field_options($sub_field);
                                $sub_field['name'] = $sub_field['name'].'copy';
                                $sub_field['group_id'] = $post_id;
                                $sub_field_names[$field['name']][] = $sub_field['name'];
                                self::set_option($sub_field['name'], $sub_field);
                                $sub_fields[] = $sub_field['name'];
                            }
                            $field['sub_fields'] = implode(',', $sub_fields);
                        }
                    }
                    
                    self::set_option($field['name'], $field);
                    $field_names[] = $field['name'];
                }
                $field_names = implode(",", $field_names);            
                update_post_meta($post_id, '_cwp_group_fields', $field_names);
                if(isset($sub_field_names) && !empty($sub_field_names)){
                    update_post_meta($post_id, '_cwp_group_sub_fields', json_encode($sub_field_names) );
                }
            }
        }
    }

    public static function set_option($name, $val) {
        if ($name) {
            $options = CWP()->get_custom_fields( 'post_types' );
            $options = $options == '' ? array() : $options;
            $options[$name] = $val;
            return CWP()->update_custom_fields( 'post_types', $options );
        } else {
            return false;
        }
    }
}