<?php
class CubeWp_Taxonomies_List_Table extends WP_List_Table{
    
    public static $taxonomies = array();
    function __construct(){
    global $status, $page;

        parent::__construct(self::$taxonomies );

    }
    
    public function Post_types(){
        $get_CustomTax = get_option('cwp_custom_taxonomies');
        if (isset($get_CustomTax) && !empty($get_CustomTax)) {
            foreach ($get_CustomTax as $single_tax) {
                $data = array();
                $data['singular']    = $single_tax['label'];
                $data['plural']  = $single_tax['singular'];
                $data['ajax']           = 'true';
            }
            self::$taxonomies = $data;
        }
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
        'plural_name'  => array('plural_name',false),
        'singular_name' => array('singular_name',false),
        'slug'   => array('slug',false),
        'post_types'   => array('post_types',false)
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
            'cb'              =>   '<input type="checkbox" />',
            'plural_name'     =>   esc_html__('Plural Label', 'cubewp-framework'),
            'singular_name'   =>   esc_html__('Singular Label', 'cubewp-framework'),
            'slug'            =>   esc_html__('Slug', 'cubewp-framework'),
            'post_types'      =>   esc_html__('Post types', 'cubewp-framework'),
            
        );
        return $columns;
    }
    
    public function usort_reorder( $a, $b ) {
      // If no sort, default to title
      $orderby = ( ! empty( $_GET['orderby'] ) ) ? sanitize_text_field($_GET['orderby']) : 'plural_name';
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
    function column_plural_name( $item ) {
        $title = sprintf( '<a href="%s"><strong>' . $item['plural_name'] . '</strong></a>', CubeWp_Submenu::_page_action('cubewp-taxonomies','edit', '&CWPtermid='.$item['slug'], '&_wpnonce='.wp_create_nonce( 'cwp_edit_post_type' )));
        $actions = [
            'edit' => sprintf( '<a href="%s">'. esc_html__('Edit', 'cubewp-framework') .'</a>', CubeWp_Submenu::_page_action('cubewp-taxonomies','edit', '&CWPtermid='.$item['slug'], '&_wpnonce='.wp_create_nonce( 'cwp_edit_post_type' ))),
            
            'delete' => sprintf( '<a href="%s">'. esc_html__('Delete', 'cubewp-framework') .'</a>', CubeWp_Submenu::_page_action('cubewp-taxonomies','delete', '&termslug='.$item['slug'], '&_wpnonce='.wp_create_nonce( 'cwp_delete_post_type' ))),
        ];
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
			'cwp_tax_bulk_action',  // Let's simply repurpose the table's singular label ("movie").
			$item['slug']                // The value of the checkbox should be the record's ID.
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
                $get_CustomTax = get_option('cwp_custom_taxonomies');
                $tax_custom_fields = CWP()->get_custom_fields( 'taxonomy' );
                if(!empty($_REQUEST['cwp_tax_bulk_action'])){
                    $bulk_request = CubeWp_Sanitize_text_Array($_REQUEST['cwp_tax_bulk_action']);
                    foreach($bulk_request as $type){  
                        if($type){
                            new CubeWp_Update_Frontend_Forms(array('taxnomoy_slug'=>$type));
                            unset($get_CustomTax[$type]);
                            if($tax_custom_fields){
                                unset($tax_custom_fields[$type]);
                                CWP()->update_custom_fields( 'taxonomy', $tax_custom_fields);
                            }
                            update_option('cwp_custom_taxonomies', $get_CustomTax);
                        }
                    }
                }
                               
            }
        }
        if(isset($_REQUEST['action']) && $_REQUEST['action'] == 'delete') {
            $nonce = esc_html( $_REQUEST['_wpnonce'] );
            if(wp_verify_nonce( $nonce, 'cwp_delete_post_type')) {
                $get_CustomTax = get_option('cwp_custom_taxonomies');
                $tax_custom_fields = CWP()->get_custom_fields( 'taxonomy' );
                $termSlug = sanitize_text_field($_REQUEST['termslug']);
                if(isset($get_CustomTax[$termSlug])){
                    new CubeWp_Update_Frontend_Forms(array('taxnomoy_slug'=>$termSlug));
                    unset($get_CustomTax[$termSlug]);
                    if($tax_custom_fields){
                        unset($tax_custom_fields[$termSlug]);
                        CWP()->update_custom_fields( 'taxonomy', $tax_custom_fields);
                    }
                    update_option('cwp_custom_taxonomies', $get_CustomTax);                    
                }
                
                wp_redirect( CubeWp_Submenu::_page_action('taxonomies') );
            }
        }
        
		
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
        $get_CustomTaxonomies = get_option('cwp_custom_taxonomies');
        if ($get_CustomTaxonomies) {
            $_data = array();
            foreach ($get_CustomTaxonomies as $single_ctax) {
                $post_type_label = $comma = '';
                if(isset($single_ctax['post_types']) && !empty($single_ctax['post_types']) && is_array($single_ctax['post_types'])){
                    foreach($single_ctax['post_types'] as $post_type){
                        $post_type_object = get_post_type_object( $post_type );
                        if(isset($post_type_object) && !empty($post_type_object)){
                            $post_type_label .= $comma . $post_type_object->label;
                        }
                        $comma = ', ';
                    }
                }
                
                $data = array();
                $data['plural_name']    = $single_ctax['name'];
                $data['singular_name']  = $single_ctax['singular'];
                $data['slug']           = $single_ctax['slug'];
                $data['post_types']     = $post_type_label;
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
		 * REQUIRED for pagination. Let's check how many items are in our data array.
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
}