<?php
/**
 * CubeWp Business hours field 
 *
 * @version 1.1.15
 * @package cubewp/cube/fields/admin
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * CubeWp_Admin_Business_Hours_Field
 */
class CubeWp_Admin_Business_Hours_Field extends CubeWp_Frontend {

    private static $timings = [];

    public function __construct( ) {
        add_filter('cubewp/admin/post/business_hours/field', array($this, 'render_business_hours_admin'), 10, 2);
    }

    /**
     * Method cwp_booking_days
     *
     * @return string
     * @since  1.0.0
     */
    public static function cwp_booking_days()
    {
        return json_encode(array(
            'label' => array('Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'),
            'value' => array('monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'),
        ));
    }
    
    /**
     * Method render_business_hours_admin
     *
     * @param string $output
     * @param array $args
     *
     * @return string
     * @since  1.0.0
     */
    public static function render_business_hours_admin($output = '', $args = array())
    {
        wp_enqueue_script('jquery-ui-datepicker');
        wp_enqueue_script('cwp-timepicker');
        wp_enqueue_style('cwp-timepicker');
        wp_enqueue_script('cubewp-business-hour');
        wp_enqueue_style('yellow-books-admin-styles');
        $args = apply_filters('cubewp/admin/field/parametrs', $args);
        if ($args['wrap'] != true) {
            return '';
        }
        $output = CubeWp_Admin::cwp_field_wrap_start($args);
        $output .= '<div class="cwp-field-business_hours">';
        $args['options']       = self::cwp_booking_days();
        $field_name = $args['name'];
        $field_id = $args['id'];
        $business_hours = $args['value'];
        $time_format = get_option('time_format');
        $output .= '<div class="yb-business-hours-display">';
        if (is_array($business_hours) && !empty($business_hours)) {
            foreach ($business_hours as $day => $business_hour) {

                if(!is_array($business_hour) && is_string($business_hour) && $business_hour == '24-hours-open'){
                    self::$timings = [
                        'fullday' => __('24 hours open', 'cubewp-framework'),
                        'open_time' => '24-hours-open',
                        'close_time' => '24-hours-open',
                        'start_time' => '',
                        'end_time' => '',
                        'day' => $day,
                        'fullhoursclass' => 'fullhours',
                        'field_name' => $field_name,
                        'dash' => '',
                        'meta_open' => '',
                        'meta_close' => '',
                    ];
                    $output .= self::business_hours_display_render();
                }else{
                    if ( empty($fullhoursclass) && !empty($business_hour['open']) && !empty($business_hour['close'])) {
                        $open_time = $business_hour['open'];
                        $close_time = $business_hour['close'];
                        for ($i = 0; $i < count($open_time); $i++) {
                            self::$timings = [
                                'fullday' => '',
                                'open_time' => $open_time[$i],
                                'close_time' => $close_time[$i],
                                'start_time' => date_i18n($time_format, strtotime($open_time[$i])),
                                'end_time' => date_i18n($time_format, strtotime($close_time[$i])),
                                'day' => $day,
                                'fullhoursclass' => '',
                                'field_name' => $field_name,
                                'dash' => '~',
                                'meta_open' => '[open][]',
                                'meta_close' => '[close][]',
                            ];
                            $output .= self::business_hours_display_render();
                        }
                        
                    }
                }
                
            }
        }
        $output .= '</div>';
        $args['value'] = '';
        $output .= '<div class="yb-business-hour-fields">';
        $args['type'] = 'dropdown';
        $args['name'] = $args['custom_name'] = $field_name . '_day';
        $args['id'] = $field_id . '_day';
        $args['placeholder'] = esc_html__('Select Day', 'cubewp-yellow-books');
        $args['class'] = 'business-days';
        $output .= self::yellow_books_render_dropdown_admin($args);

        $args['name'] = $args['custom_name'] = $field_name . '_open_time';
        $args['id'] = $field_id . '_open_time';
        $args['type'] = 'time_picker';
        $args['placeholder'] = esc_html__('Open Time', 'cubewp-yellow-books');
        $args['class'] = 'business-open-time';
        $output .= self::yellow_books_render_timepicker_admin($args);

        $args['name'] = $args['custom_name'] = $field_name . '_close_time';
        $args['id'] = $field_id . '_close_time';
        $args['placeholder'] = esc_html__('Close Time', 'cubewp-yellow-books');
        $args['admin_size'] = '1/3';
        $args['class'] = 'business-close-time';
        $output .= self::yellow_books_render_timepicker_admin($args);

        $output .= '<div class="yb_business_hour_fulldayopen">
                        <input type="checkbox" id="yb_fulldayopen" class="yb_fulldayopen">
                        <label>' . esc_html__('24 Hours', 'cubewp-yellow-books') . '</label>
                    </div>';
        $output .= '</div>';
        $output .= '<button class="cwp-add-new-business-hour" data-id="' . $field_id . '" data-name="' . $field_name . '"  data-fullday="' . __('24 hours open', 'cubewp-yellow-books') . '">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                            <path fill-rule="evenodd" d="M8 2a.5.5 0 0 1 .5.5v5h5a.5.5 0 0 1 0 1h-5v5a.5.5 0 0 1-1 0v-5h-5a.5.5 0 0 1 0-1h5v-5A.5.5 0 0 1 8 2Z"/>
                        </svg>
                    </button>';
        $output      .= '</div></div>';
        $output .= CubeWp_Admin::cwp_field_wrap_end($args);

        return apply_filters("cubewp/admin/{$args['name']}/field", $output, $args);
    }

    private static function business_hours_display_render(){
        
        $timings = self::$timings;

        if ( current_cubewp_page() == 'cubewp_settings'){
            $open_name = 'name="' . $timings['field_name'] . '[' . $timings['day'] . ']' .$timings['meta_open'] . '"';
            $close_name = 'name="' . $timings['field_name'] . '[' . $timings['day'] . ']' .$timings['meta_close'] . '"';
        }else{
            $open_name = 'name="cwp_meta[' . $timings['field_name'] . '][' . $timings['day'] . ']' .$timings['meta_open'] . '"';
            $close_name = 'name="cwp_meta[' . $timings['field_name'] . '][' . $timings['day'] . ']' .$timings['meta_close'] . '"';
        }

        $output = '<div class="business-hours ' . $timings['day'] . ' ' . $timings['day'] . '-' . $timings['field_name'] . ' ' . $timings['fullhoursclass'] . ' ">
                    <div class="day-hours">
                    <span class="weekday">' . $timings['day'] . '</span>
                    <span class="start-end fullday">' . $timings['fullday'] . '</span>
                    <span class="open">' . $timings['start_time'] . '</span>
                    <span class="dash">' . $timings['dash'] . '</span>
                    <span class="close">' . $timings['end_time'] . '</span>
                    <a class="remove-business-hours" href="#" data-field_name ="' . $timings['field_name'] . '" data-weekday ="' . $timings['day'] . '">
                        <span class="dashicons dashicons-no"></span>
                    </a>';
                    
        $output .= '<input class="' . $timings['day'] . '-open" '.$open_name.' value="' . $timings['open_time'] . '" type="hidden">
        <input class="' . $timings['day'] . '-close" '.$close_name.' value="' . $timings['close_time'] . '" type="hidden">';

        $output .= '</div></div>';
        return $output;
    }

    /**
     * Method yellow_books_render_timepicker_admin
     *
     * @param array $args
     *
     * @return string
     * @since  1.0.0
     */
    public static function yellow_books_render_timepicker_admin($args)
    {
        $output = '';
        $output .= '<div class="cwp-time-picker cwp-field-time_picker  yb-business-hour-field-admin">';
        $input_attrs = array(
            'id'           => $args['id'],
            'class'        => $args['class'] . '',
            'name'         => '',
            'value'        => $args['value'],
            'placeholder'  => $args['placeholder']
        );
        $extra_attrs = isset($args['extra_attrs']) ? $args['extra_attrs'] : '';
        if (isset($args['required']) && $args['required'] == 1) {
            $input_attrs['class'] .= ' required';
            $validation_msg = isset($args['validation_msg']) ? $args['validation_msg'] : '';
            $extra_attrs .= ' data-validation_msg="' . $validation_msg . '"';
        }
        $input_attrs['extra_attrs'] = $extra_attrs;

        $output .= cwp_render_text_input($input_attrs);

        $input_attrs = array(
            'name'         => !empty($args['custom_name']) ? $args['custom_name'] : $args['name'],
            'value'        => $args['value']
        );
        $output .= cwp_render_hidden_input($input_attrs);
        $output .= '</div>';
        return $output;
    }

    /**
     * Method yellow_books_render_dropdown_admin
     *
     * @param array $args
     *
     * @return string
     * @since  1.0.0
     */
    public static function yellow_books_render_dropdown_admin($args)
    {
        $output = '';
        $output .= '<div class="yb-business-hour-field-admin">';
        $input_attrs = array(
            'options'     => isset($args['options']) ? $args['options'] : '',
            'id'          => $args['id'],
            'class'       => $args['class'],
            'name'        => !empty($args['custom_name']) ? $args['custom_name'] : $args['name'],
            'value'       => $args['value'],
            'placeholder' => $args['placeholder']
        );
        $extra_attrs = isset($args['extra_attrs']) ? $args['extra_attrs'] : '';
        if (isset($args['required']) && $args['required'] == 1) {
            $input_attrs['class'] .= ' required';
            $validation_msg       = isset($args['validation_msg']) ? $args['validation_msg'] : '';
            $extra_attrs          .= ' data-validation_msg="' . $validation_msg . '" ';
        }
        $output .= cwp_render_dropdown_input($input_attrs);
        $output .= '</div>';
        return $output;
    }

}
new CubeWp_Admin_Business_Hours_Field();