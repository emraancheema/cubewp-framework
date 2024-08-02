<?php
if ( ! defined('ABSPATH')) {
	exit;
}

class CubeWp_Settings {

	protected static $settings_helpers;
	protected static $ajax_helper = 'CubeWp_Settings_Ajax_Hooks';
	protected $options;
	protected $options_values;
	protected $required;
	protected $folds;
	protected $required_child;
	protected $fields_hidden;
	protected $fields;

	public function __construct($parent = null) {
		add_filter('cubewp_settings_params', array($this, '_localization'));
		add_action('cubewp_settings', array($this, 'cubewp_settings_callback'));
		add_action( 'settings_saved', array('CubeWp_Settings_Ajax_Hooks', 'cwp_save_default_options'),10,2);
		new CubeWp_Ajax('', self::$ajax_helper, 'cwp_save_options');
		new CubeWp_Ajax('', self::$ajax_helper, 'cwp_get_font_attributes');
		$this->fields = new CubeWp_Settings_Fields();
		if(empty(get_option('cwpOptions'))){
			do_action( 'settings_saved', 'all',0 );
		}
	}

	public static function init() {
		$CubeClass = __CLASS__;
		new $CubeClass;
	}

	public function cubewp_settings_callback() {
		$this->cwp_settings();
		$this->settings_page();
	}

	public function cwp_settings() {
		$settings      = require CWP_PLUGIN_PATH . 'cube/functions/settings/cubewp-default-options.php';
		$this->options = apply_filters('cubewp/options/sections', $settings);
		foreach ($this->options as $key => $data) {
			$data['fields']      = apply_filters("cubewp/settings/section/{$key}", $data['fields']);
			$this->options[$key] = $data;
		}
	}

	public function settings_page() {
		?>
        <div id="cubewp-settings" class="cubewp-settings">
			<?php settings_fields('plugin_settings'); ?>
            <form id="cwp-options-form" method="post" action="" enctype="multipart/form-data">
				<?php
				self::cubewp_setting_tabs();
				self::cubewp_setting_tabs_content();
				?>
            </form>
        </div>
		<?php
	}

	public function cubewp_setting_tabs() {
		?>
        <div id="cubewp-settings-tabs">
            <div class="cubewp-settings-tabs-header">
                <img src="<?php echo esc_url(CWP_PLUGIN_URI . "cube/assets/admin/images/logo-2.png"); ?>"
                     alt="<?php esc_html_e("CubeWP Logo", "cube"); ?>">
            </div>
			<?php
			$counter = 0;
			foreach ($this->options as $data) {
				$id    = $data['id'];
				$class = $data['class'] ?? "";
				$class .= " " . self::current_active_tab($id, $counter);
				$icon  = $data['icon'] ?? "";
                if (empty($icon)) {
                    $icon = "dashicons-admin-generic";
                }
				$counter ++;
				?>
                <div class="cubewp-setting-tab <?php esc_attr_e($class); ?>" data-target-id="<?php esc_attr_e($id); ?>">
                    <span class="dashicons <?php esc_attr_e($icon); ?>"></span>
					<?php echo esc_html($data['title']); ?>
                </div>
				<?php
			}
			$groups = cwp_get_groups_of_settings();
			if(isset($groups) && !empty($groups)){
				foreach($groups as $group){
					$id = 'section_'.$group;
					$class = 'custom-section';
					?>
					<div class="cubewp-setting-tab <?php esc_attr_e($class); ?>" data-target-id="<?php esc_attr_e($id); ?>">
						<span class="dashicons <?php esc_attr_e($icon); ?>"></span>
						<?php echo get_the_title($group) ?>
					</div>
					<?php
				}
			}
			?>
        </div>
		<?php
	}

	public function cubewp_setting_tabs_content() {
		?>
        <div id="cubewp-settings-tabs-content">
			<?php
			self::cubewp_setting_actions("header");
			$counter = 0;
			foreach ($this->options as $data) {
				$id    = $data['id'];
				$class = $data['class'] ?? "";
				$class .= " " . self::current_active_tab($id, $counter);
				$counter ++;
				?>
                <div id="<?php esc_attr_e($id) ?>" class="cubewp-settings-tabs-content <?php esc_attr_e($class) ?>">
                    <h2><?php echo esc_html($data['title']) ?></h2>
                    <table class="form-table">
                        <tbody>
						<?php
						foreach ($data['fields'] as $field) {
							$field = self::set_field_value($field);
							?>
                            <tr>
								<?php echo apply_filters("cubewp/settings/{$field['type']}/field", '', $field) ?>
                            </tr>
							<?php
							$this->check_dependencies($field);
						}
						?>
                        </tbody>
                    </table>
                </div>
				<?php
			}
			$groups = cwp_get_groups_of_settings();
			if(isset($groups) && !empty($groups)){
				foreach($groups as $group){
					$fields = get_post_meta($group, '_cwp_group_fields', true);
					$id = 'section_'.$group;
					$class = 'custom-section';
					?>
					<div id="<?php esc_attr_e($id) ?>" class="cubewp-settings-tabs-content <?php esc_attr_e($class) ?>">
						<h2><?php echo get_the_title($group) ?></h2>
						<table class="form-table">
							<tbody>
							<?php
							$fields = explode(",", $fields);
							foreach ($fields as $field) {
								$field = get_setting_field_options($field);
								$field['id'] = $field['name'];
								$field['custom_name'] = $field['name'];
								if ($field['type'] == 'google_address') {
								   $field['custom_name_lat'] = $field['name'] . '_lat';
								   $field['custom_name_lng'] = $field['name'] . '_lng';
								}
								$field['desc'] = $field['description'];
								$field['title'] = $field['label'];
								$field = self::set_field_value($field);
								echo apply_filters("cubewp/admin/post/{$field['type']}/field", '', $field);
								$this->check_dependencies($field);
							}
							?>
							</tbody>
						</table>
					</div>
				<?php
				}
			}
			self::cubewp_setting_actions("footer");
			?>
        </div>
		<?php
	}

	/**
	 * @param $tab_id
	 * @param $counter
	 *
	 * @return string
	 */
	public function current_active_tab(string $tab_id, int $counter) {
		$class = "";

		$cookie_name = "cwp-options-lastUsedTab";
		if (isset($_COOKIE[$cookie_name]) && ! empty($_COOKIE[$cookie_name])) {
			$lastUsedTab = sanitize_text_field( $_COOKIE[$cookie_name] );
		}

		if ($counter == 0 && empty($lastUsedTab)) {
			$class = " active";
		} else if ( ! empty($lastUsedTab) && $lastUsedTab == $tab_id) {
			$class = " active";
		}else if ( ! empty($lastUsedTab) && ! isset($this->options[$lastUsedTab]) && $counter == 0) {
			$class = " active";
		 }

		return $class;
	}

	public function cubewp_setting_actions(string $position) {
		?>
        <div class="cubewp-setting-actions cubewp-setting-actions-<?php esc_attr_e($position); ?>">
            <button class="button-primary cwp-save-settings">
		        <?php esc_html_e('Save settings', 'cubewp-framework'); ?>
            </button>
            <button class="button-secondary cwp-reset-section">
				<?php esc_html_e('Reset section', 'cubewp-framework'); ?>
            </button>
            <button class="button-secondary cwp-reset-settings">
		        <?php esc_html_e('Reset all', 'cubewp-framework'); ?>
            </button>
        </div>
		<?php
	}

	/**
	 * @param $field
	 *
	 * @return array
	 */
	public function set_field_value($field) {
		global $cwpOptions;
		$val = null;
		if (isset($cwpOptions[$field['id']])) {
			$val = $cwpOptions[$field['id']];
		} else if (isset($field['default'])) {
			$val = $field['default'];
		}
		$this->options_values[$field['id']] = $val;
		$field['value'] = $val;
		if ($field['type'] == 'google_address') {
			if ( isset( $cwpOptions[ $field['id'] . '_lat' ] ) ) {
			   $field['lat'] = $cwpOptions[ $field['id'] . '_lat' ];
			}
			if ( isset( $cwpOptions[ $field['id'] . '_lng' ] ) ) {
			   $field['lng'] = $cwpOptions[ $field['id'] . '_lng' ];
			}
		}
		return $field;
	}

	public function check_dependencies($field = array()) {

		if ( ! empty($field['required'])) {
			if ( ! isset($this->required_child[$field['id']])) {
				$this->required_child[$field['id']] = array();
			}

			if ( ! isset($this->required[$field['id']])) {
				$this->required[$field['id']] = array();
			}

			if (is_array($field['required'][0])) {
				foreach ($field['required'] as $value) {
					if (is_array($value) && 3 === count($value)) {
						$data               = array();
						$data['parent']     = $value[0];
						$data['operation']  = $value[1];
						$data['checkValue'] = $value[2];

						$this->required[$data['parent']][$field['id']][] = $data;

						if ( ! in_array($data['parent'], $this->required_child[$field['id']], true)) {
							$this->required_child[$field['id']][] = $data;
						}

						$this->check_required_dependencies($field, $data);
					}
				}
			} else {
				$data               = array();
				$data['parent']     = $field['required'][0];
				$data['operation']  = $field['required'][1];
				$data['checkValue'] = $field['required'][2];

				$this->required[$data['parent']][$field['id']][] = $data;

				if ( ! in_array($data['parent'], $this->required_child[$field['id']], true)) {
					$this->required_child[$field['id']][] = $data;
				}

				$this->check_required_dependencies($field, $data);
			}
		}

	}

	private function check_required_dependencies($field, $data) {

		if ( ! isset($this->fields_hidden)) {
			$this->fields_hidden = array();
		}

		// required field must not be hidden. otherwise hide this one by default.
		if ( ! in_array($data['parent'], $this->fields_hidden, true) && ( ! isset($this->folds[$field['id']]) || 'hide' !== $this->folds[$field['id']])) {
			if (isset($this->options_values[$data['parent']])) {
				$return = $this->compare_value_dependencies($this->options_values[$data['parent']], $data['checkValue'], $data['operation']);
			}
		}

		if ((isset($return) && $return) && ( ! isset($this->folds[$field['id']]) || 'hide' !== $this->folds[$field['id']])) {
			$this->folds[$field['id']] = 'show';
		} else {
			$this->folds[$field['id']] = 'hide';

			if ( ! in_array($field['id'], $this->fields_hidden, true)) {
				$this->fields_hidden[] = $field['id'];
			}
		}
	}

	public function compare_value_dependencies($parent_value, $check_value, $operation) {
		$return = false;

		$settings_helpers = new CubeWp_Settings_Helpers();

		switch ($operation) {
			case '=':
			case 'equals':
				$data['operation'] = '=';

				if (is_array($parent_value)) {
					foreach ($parent_value as $idx => $val) {
						if (is_array($check_value)) {
							foreach ($check_value as $i => $v) {
								if ($settings_helpers->make_bool_str($val) === $settings_helpers->make_bool_str($v)) {
									$return = true;
								}
							}
						} else {
							if ($settings_helpers->make_bool_str($val) === $settings_helpers->make_bool_str($check_value)) {
								$return = true;
							}
						}
					}
				} else {
					if (is_array($check_value)) {
						foreach ($check_value as $i => $v) {
							if ($settings_helpers->make_bool_str($parent_value) === $settings_helpers->make_bool_str($v)) {
								$return = true;
							}
						}
					} else {
						if ($settings_helpers->make_bool_str($parent_value) === $settings_helpers->make_bool_str($check_value)) {
							$return = true;
						}
					}
				}
				break;

			case '!=':
			case 'not':
				$data['operation'] = '!==';
				if (is_array($parent_value)) {
					foreach ($parent_value as $idx => $val) {
						if (is_array($check_value)) {
							foreach ($check_value as $i => $v) {
								if ($settings_helpers->make_bool_str($val) !== $settings_helpers->make_bool_str($v)) {
									$return = true;
								}
							}
						} else {
							if ($settings_helpers->make_bool_str($val) !== $settings_helpers->make_bool_str($check_value)) {
								$return = true;
							}
						}
					}
				} else {
					if (is_array($check_value)) {
						foreach ($check_value as $i => $v) {
							if ($settings_helpers->make_bool_str($parent_value) !== $settings_helpers->make_bool_str($v)) {
								$return = true;
							}
						}
					} else {
						if ($settings_helpers->make_bool_str($parent_value) !== $settings_helpers->make_bool_str($check_value)) {
							$return = true;
						}
					}
				}

				break;
			case '>':
			case 'greater':
			case 'is_larger':
				$data['operation'] = '>';
				if ($parent_value > $check_value) {
					$return = true;
				}
				break;
			case '>=':
			case 'greater_equal':
			case 'is_larger_equal':
				$data['operation'] = '>=';
				if ($parent_value >= $check_value) {
					$return = true;
				}
				break;
			case '<':
			case 'less':
			case 'is_smaller':
				$data['operation'] = '<';
				if ($parent_value < $check_value) {
					$return = true;
				}
				break;
			case '<=':
			case 'less_equal':
			case 'is_smaller_equal':
				$data['operation'] = '<=';
				if ($parent_value <= $check_value) {
					$return = true;
				}
				break;
			case 'contains':
				if (is_array($parent_value)) {
					$parent_value = implode(',', $parent_value);
				}

				if (is_array($check_value)) {
					foreach ($check_value as $idx => $opt) {
						if (strpos($parent_value, (string) $opt) !== false) {
							$return = true;
						}
					}
				} else {
					if (strpos($parent_value, (string) $check_value) !== false) {
						$return = true;
					}
				}

				break;
			case 'doesnt_contain':
			case 'not_contain':
				if (is_array($parent_value)) {
					$parent_value = implode(',', $parent_value);
				}

				if (is_array($check_value)) {
					foreach ($check_value as $idx => $opt) {
						if (strpos($parent_value, (string) $opt) === false) {
							$return = true;
						}
					}
				} else {
					if (strpos($parent_value, (string) $check_value) === false) {
						$return = true;
					}
				}

				break;
			case 'is_empty_or':
				if (empty($parent_value) || $check_value === $parent_value) {
					$return = true;
				}
				break;
			case 'not_empty_and':
				if ( ! empty($parent_value) && $check_value !== $parent_value) {
					$return = true;
				}
				break;
			case 'is_empty':
			case 'empty':
			case '!isset':
				if (empty($parent_value) || '' === $parent_value || null === $parent_value) {
					$return = true;
				}
				break;
			case 'not_empty':
			case '!empty':
			case 'isset':
				if ( ! empty($parent_value) && '' !== $parent_value && null !== $parent_value) {
					$return = true;
				}
				break;
		}

		return $return;
	}

	public function _localization($params) {
		$this->_get_required_data();
		$params = array(
			'ajax_url'       => admin_url('admin-ajax.php'),
			'admin_url'      => admin_url(),
			'folds'          => $this->folds,
			'required'       => $this->required,
			'required_child' => $this->required_child
		);

		return $params;
	}

	public function _get_required_data() {
		$settings = $this->cwp_settings();
		foreach ($this->options as $key => $data) {
			foreach ($data['fields'] as $field) {
				$field = self::set_field_value($field);
				$this->check_dependencies($field);
			}
		}
	}

}