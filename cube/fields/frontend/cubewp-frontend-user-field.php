<?php
/**
 * CubeWp admin user field 
 *
 * @version 1.0
 * @package cubewp/cube/fields/frontend
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * CubeWp_Frontend_User_Field
 */
class CubeWp_Frontend_User_Field extends CubeWp_Frontend {
    public function __construct( ) {
        add_filter('cubewp/frontend/user/field', array($this, 'render_user_field'), 10, 2);
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
    public function render_user_field($output = '', $args = array()) {
        $args = apply_filters('cubewp/frontend/field/parametrs', $args);
        if ($args['appearance'] == 'select') {
           $args['type'] = 'dropdown';
        } else if ($args['appearance'] == 'multi_select') {
           $args['type']  = 'dropdown';
           $args['multi'] = true;
        } else {
           $args['type'] = $args['appearance'];
        }

        if (isset($args['auto_complete']) && ! empty($args['auto_complete']) && $args['type'] == 'dropdown') {
         $args['select2_ui'] = true;
		    $options = array();
		    if ( ! empty($args['value']) && is_array($args['value'])) {
			    foreach ($args['value'] as $user_id) {
				    $user              = get_userdata($user_id);
				    $options[$user_id] = esc_html($user->display_name);
			    }
		    } else if ( ! empty($args['value']) && ! is_array($args['value'])) {
			    $user                    = get_userdata($args['value']);
			    $options[$args['value']] = esc_html($user->display_name);
		    }
		    $args['options'] = $options;
		    $args['class']  = $args['class'] . ' cubewp-remote-options ';
		    $args['extra_attrs']  = $args['extra_attrs'] . ' data-dropdown-type="user" data-dropdown-values="' . $args['filter_user_roles'] . '" ';
	    }else {
		    $args['options'] = cwp_get_users_by_role($args['filter_user_roles']);
	    }

	    return apply_filters("cubewp/frontend/{$args['type']}/field", $output, $args);
    }
}
new CubeWp_Frontend_User_Field();