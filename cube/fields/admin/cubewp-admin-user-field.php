<?php
/**
 * CubeWp admin user field 
 *
 * @version 1.0
 * @package cubewp/cube/fields/admin
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class CubeWp_Admin_User_Field {
    
    public function __construct( ) {
        add_filter('cubewp/admin/post/user/field', array($this, 'render_user_field'), 10, 2);
    }
        
    /**
     * Method render_user_field
     *
     * @param string $output
     * @param array $args
     *
     * @return string html
     * @since  1.0.0
     */
    public function render_user_field( $output = '', $args = array() ) {
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
			    foreach ($args['value'] as $user_id) {
				    $user              = get_userdata($user_id);
				    $options[$user_id] = is_object($user) ? esc_html($user->display_name) : '';
			    }
		    } else if ( ! empty($args['value']) && ! is_array($args['value'])) {
			    $user                    = get_userdata($args['value']);
			    $options[$args['value']] = esc_html($user->display_name);
		    }
		    $args['options'] = $options;
		    $args['class']  = $args['class'] . ' cubewp-remote-options ';
            $args['extra_attrs'] = isset($args['extra_attrs']) ? $args['extra_attrs'] : '';
		    $args['extra_attrs']  = $args['extra_attrs'] . ' data-dropdown-type="user" data-dropdown-values="' . $args['filter_user_roles'] . '" ';
	    }else {
		    $args['options'] = cwp_get_users_by_role( $args['filter_user_roles'] );
	    }

	    return apply_filters("cubewp/admin/post/{$args['type']}/field", '', $args);
    }
    
}
new CubeWp_Admin_User_Field();