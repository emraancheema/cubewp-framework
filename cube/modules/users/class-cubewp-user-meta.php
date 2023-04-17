<?php
class CubeWp_User_Meta
{

    private static function cwp_get_groups()
    {

        $args = array(
            'numberposts' => -1,
            'post_type' => 'cwp_user_fields',
            'meta_key' => '_cwp_group_order',
            'orderby' => 'meta_value_num',
            'order' => 'ASC',
            'meta_query' => array(
                'key' => '_cwp_group_user_roles',
                'value' => '',
                'compare' => '!=',
            ),
        );
        $allGroups = get_posts($args);
        return $allGroups;

    }

    private static function get_group_fields($groupID = 0)
    {
        $fields = get_post_meta($groupID, '_cwp_group_fields', true);
        $sub_fields = get_post_meta($groupID, '_cwp_group_sub_fields', true);
        $fields = isset($fields) && !empty($fields) ? json_decode($fields, true) : array();
        $sub_fields = isset($sub_fields) && !empty($sub_fields) ? json_decode($sub_fields, true) : array();

        $fieldOptions = CWP()->get_custom_fields('user');
        $fieldBox = array();
        $SubFieldOption = array();
        if (isset($fields) && !empty($fields)) {
            foreach ($fields as $field) {
                $SingleFieldOption = $fieldOptions[$field];

                $fieldBox[$SingleFieldOption['name']] = $SingleFieldOption;
                if (isset($sub_fields[$SingleFieldOption['name']]) && !empty($sub_fields[$SingleFieldOption['name']])) {
                    $fieldBox[$SingleFieldOption['name']]['sub_fields'] = array();
                    foreach ($sub_fields[$SingleFieldOption['name']] as $key => $sub_field) {
                        $fieldBox[$SingleFieldOption['name']]['sub_fields'][] = $fieldOptions[$sub_field];
                    }
                }
            }
        }
        return $fieldBox;
    }

    public static function cwp_user_profile_fields($user)
    {

        CubeWp_Enqueue::enqueue_style('cubewp-admin');
        wp_enqueue_media();

        $user_id = isset($user->ID) ? $user->ID : '';
        $groups = self::cwp_get_groups();
        $input_attrs = array(
            'type' => 'hidden',
            'name' => 'cwp_meta_box_nonce',
            'value' => wp_create_nonce(basename(__FILE__)),
        );
        $output = cwp_render_hidden_input($input_attrs);

        foreach ($groups as $group) {
            $group_user_roles = get_post_meta($group->ID, '_cwp_group_user_roles', true);
            $group_user_roles = isset($group_user_roles) && !empty($group_user_roles) ? explode(',', $group_user_roles) : array();

            $user_group_display = '';
            if (isset($user->roles) && !empty($user->roles) && !in_array($user->roles[0], $group_user_roles)) {
                $user_group_display = ' style="display:none;"';
            }
            if (isset($user->roles) && !empty($user->roles) && get_current_user_id() == $user->ID && !in_array($user->roles[0], $group_user_roles)) {
                continue;
            }
            $output .= '<div class="cwp-validation userbox cwp-metaboxes cwp-user-meta-fields" data-role="' . esc_attr(implode(',', $group_user_roles)) . '"' . $user_group_display . '>'; 
            $output .= '<h2' . $user_group_display . '>' . esc_html($group->post_title) . '</h2>';
            $output .= '<div class="inside">'; 
            $output .= '<table class="form-table">';
            $output .= '<tbody>';

            $user_fields = self::get_group_fields($group->ID);
            if (isset($user_fields) && !empty($user_fields)) {
                foreach ($user_fields as $id => $field) {

                    $Old_Value = get_user_meta($user_id, $id, true);
                    if ($Old_Value) {
                        $value = $Old_Value;
                    } else {
                        $value = isset($field['default_value']) ? $field['default_value'] : '';
                    }

                    $field['value'] = $value;
                    $field['custom_name'] = 'cwp_meta[' . $field['name'] . ']';
                    $field['wrap'] = true;

                    if ($field['type'] == 'google_address') {
                        $field['custom_name_lat'] = 'cwp_meta[' . $field['name'] . '_lat' . ']';
                        $field['custom_name_lng'] = 'cwp_meta[' . $field['name'] . '_lng' . ']';
                        $field['lat'] = get_user_meta($user_id, $field['name'] . '_lat', true);
                        $field['lng'] = get_user_meta($user_id, $field['name'] . '_lng', true);
                    }

                    $output .= apply_filters("cubewp/admin/post/{$field['type']}/field", '', $field);

                }
            }

            $output .= '</tbody>';
            $output .= '</table>';
            $output .= '</div>';
            $output .= '</div>'; 

        }
        echo cubewp_core_data($output);

    }

    public static function cwp_save_user_fields($user_id = '')
    {

        if (isset($_POST['cwp_meta'])) {

            // verify nonce
            if (!wp_verify_nonce($_POST['cwp_meta_box_nonce'], basename(__FILE__))) {
                return $user_id;
            }

            // check autosave
            if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
                return $user_id;
            }

            // check permissions
            if (!current_user_can('edit_user', $user_id)) {
                return $user_id;
            }

            $fields = CubeWp_Sanitize_Fields_Array($_POST['cwp_meta'], 'user');

            $fieldOptions = CWP()->get_custom_fields('user');

            foreach ($fields as $key => $value) {

                $_key = str_replace('cwp-', '', $key);
                $singleFieldOptions = isset($fieldOptions[$_key]) ? $fieldOptions[$_key] : array();
                $_val = $value;
                if ((isset($singleFieldOptions['type']) && isset($singleFieldOptions['relationship']) && $singleFieldOptions['type'] == 'post' && $singleFieldOptions['relationship']) && is_array($singleFieldOptions) && count($singleFieldOptions) > 0) {
                    if (!is_array($_val)) {
                        $_val = array($_val);
                    }
                    if (!empty($_val) && count($_val) > 0) {
                        (new CubeWp_Relationships)->save_relationship($user_id, $_val, $_key, 'UTP');
                    }
                } else if ((isset($singleFieldOptions['type']) && isset($singleFieldOptions['relationship']) && $singleFieldOptions['type'] == 'user' && $singleFieldOptions['relationship']) && is_array($singleFieldOptions) && count($singleFieldOptions) > 0) {
                    if (!is_array($_val)) {
                        $_val = array($_val);
                    }
                    if (!empty($_val) && count($_val) > 0) {
                        (new CubeWp_Relationships)->save_relationship($user_id, $_val, $_key, 'UTU');
                    }
                }

                if (!empty($singleFieldOptions) && isset($singleFieldOptions['type']) && $singleFieldOptions['type'] == 'repeating_field') {
                    $arr = array();
                    foreach ($value as $_key => $_val) {
                        foreach ($_val as $field_key => $field_val) {
                            $arr[$field_key][$_key] = $field_val;
                        }
                    }

                    if (isset($arr) && !empty($arr)) {
                        $_arr = array_filter($arr);
                        update_user_meta($user_id, $key, $_arr);
                    } else {
                        delete_user_meta($user_id, $key);
                    }
                } else {
                    $old = get_post_meta($user_id, $key, true);
                    $new = $fields[$key];
                    if ($new) {
                        if ($new != $old) {
                            update_user_meta($user_id, $key, $new);
                        }
                    } else {
                        delete_user_meta($user_id, $key);
                    }
                }
            }
        }

    }

}