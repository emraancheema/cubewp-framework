<?php
/**
 * CubeWp admin post field 
 *
 * @version 1.0
 * @package cubewp/cube/fields/admin
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * CubeWp_Admin_Post_Field
 */
class CubeWp_Admin_Post_Field {
    
    public function __construct( ) {
        add_filter('cubewp/admin/post/post/field', array($this, 'render_post_field'), 10, 2);
    }
        
    /**
     * Method render_post_field
     *
     * @param string $output
     * @param array $args
     *
     * @return string html
     * @since  1.0.0
     */
    public function render_post_field( $output = '', $args = array() ) {
        $args   =  apply_filters( 'cubewp/admin/field/parametrs', $args );
		
        if($args['appearance'] == 'select'){
            $args['type'] = 'dropdown';
        } else if($args['appearance'] == 'multi_select'){
            $args['type']     = 'dropdown';
            $args['multiple'] = 1;
        }else{
            $args['type']  = $args['appearance'];
        }

	    if (isset($args['auto_complete']) && ! empty($args['auto_complete']) && $args['type'] == 'dropdown') {
			$args['select2_ui'] = true;
		    $options = array();
		    if ( ! empty($args['value']) && is_array($args['value'])) {
			    foreach ($args['value'] as $post_id) {
				    $options[$post_id] = esc_html(get_the_title($post_id));
			    }
		    } else if ( ! empty($args['value']) && ! is_array($args['value'])) {
			    $options[$args['value']] = esc_html(get_the_title($args['value']));
		    }
		    $args['options']     = $options;
		    $args['class']       = $args['class'] . ' cubewp-remote-options ';
			$args['extra_attrs'] = isset($args['extra_attrs']) ? $args['extra_attrs'] : '';
		    if ( isset( $args['current_user_posts'] ) && $args['current_user_posts'] ) {
			    $args['extra_attrs'] = $args['extra_attrs'] . ' data-dropdown-type="user-posts" data-dropdown-values="' . $args['filter_post_types'] . '" ';
		    }else {
			    $args['extra_attrs'] = $args['extra_attrs'] . ' data-dropdown-type="post" data-dropdown-values="' . $args['filter_post_types'] . '" ';
		    }
	    } else {
		    $query_args = array(
			    'post_type'      => $args['filter_post_types'],
			    'post_status'    => 'publish',
			    'posts_per_page' => -1,
			    'fields'         => 'ids'
		    );
		    if ( isset( $args['current_user_posts'] ) && $args['current_user_posts'] ) {
			    if ( is_user_logged_in() ) {
				    $query_args['author'] = get_current_user_id();
			    }else {
				    $query_args = array();
			    }
		    }
		    if ( ! empty( $query_args ) ) {
			    $posts   = get_posts( $query_args );
			    $options = array();
			    if ( isset( $posts ) && ! empty( $posts ) ) {
				    foreach ( $posts as $post_id ) {
					    $options[ $post_id ] = esc_html( get_the_title( $post_id ) );
				    }
			    }
			    $args['options'] = $options;
		    }
	    }
	    return apply_filters("cubewp/admin/post/{$args['type']}/field", '', $args);
    }
    
}
new CubeWp_Admin_Post_Field();