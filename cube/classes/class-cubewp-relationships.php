<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * cubewp relationship.
 *
 * @since   1.0.5
 *
 * CubeWp_Relationship
 * @version 1.0
 * @package cubewp/cube/classes
 */
class CubeWp_Relationships {

	private static $meta_prefix = 'cwp_relation_';

	private $wp_db;

	public function __construct() {
		$this->config_relationships();

		add_action( 'show_user_profile', array( $this, 'cubewp_relation_user_metabox' ) );
		add_action( 'edit_user_profile', array( $this, 'cubewp_relation_user_metabox' ) );
		add_action( 'user_new_form', array( $this, 'cubewp_relation_user_metabox' ) );

		add_action( 'add_meta_boxes', array( $this, 'cubewp_relation_metabox' ) );

		add_action( 'wp_ajax_cubewp_remove_relation', array( $this, 'cubewp_remove_relation' ) );
	}

	private function config_relationships() {
		global $wpdb;
		$this->wp_db = $wpdb;

		$charset_collate = $this->wp_db->get_charset_collate();
		$this->wp_db->query( "CREATE TABLE IF NOT EXISTS `" . $this->wp_db->prefix . "cube_relationships` (
            `ID` bigint(20) NOT NULL AUTO_INCREMENT PRIMARY KEY,
            `relation_key` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
            `relation_data` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
            `relation_type` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL
        ) $charset_collate" );
	}

	public function cubewp_relation_metabox() {
		global $post;
		if ( isset( $post->ID ) ) {
			$post_id   = $post->ID;
			$relations = $this->get_relations( null, array( 'PTP', 'UTP' ), false );
			if ( ! empty( $relations ) ) {
				$break = false;
				foreach ( $relations as $relation ) {
					$relation_key = $relation['relation_key'];
					$relation_data    = is_serialized($relation['relation_data']) ? unserialize($relation['relation_data']) : $relation['relation_data'];
					$relation_key = self::$meta_prefix.$relation_key;
					$meta_value   = get_post_meta( $post_id, $relation_key, true );
					if ( ! empty( $meta_value ) && ! empty($relation_data)) {
						foreach ($relation_data as $data) {
							if ( ! empty($data) && is_array($data) && in_array($post_id, $data)) {
								$post_type = get_post_type( $post_id );
								add_meta_box( 'cubewp-relations-metabox', __( 'Relations', 'cubewp-framework' ), array(
									$this,
									'cubewp_relation_metabox_render'
								), $post_type, 'side' );
								$break = true;
								break;
							}
						}
					}
					if ($break) {
						break;
					}
				}
			}
		}
	}

	public function get_relations( $relation_key = '', $relation_type = '', $relation_data_only = true ) {
		if ( empty( $relation_type ) ) {
			return array();
		}
		if ( is_array( $relation_type ) ) {
			$relation_type_condition = "";
			foreach ( $relation_type as $key => $relation_typ ) {
				if ( $key != 0 ) {
					$relation_type_condition .= " OR ";
				}
				$relation_type_condition .= " relation_type = '{$relation_typ}' ";
			}
		} else {
			$relation_type_condition = "relation_type = '{$relation_type}'";
		}
		if ( empty( $relation_key ) ) {
			$query_results = $this->wp_db->get_results( "SELECT * FROM {$this->wp_db->prefix}cube_relationships WHERE {$relation_type_condition} ", ARRAY_A );
			if ( ! empty( $query_results ) && count( $query_results ) > 0 ) {
				if ( $relation_data_only ) {
					$return = array();
					foreach ( $query_results as $query_result ) {
						$relation_data = is_serialized( $query_result['relation_data'] ) ? unserialize( $query_result['relation_data'] ) : $query_result['relation_data'];
						if ( ! empty( $relation_data ) && count( $relation_data ) > 0 ) {
							$return[] = $relation_data;
						}
					}

					return $return;
				}

				return $query_results;
			}
		} else {
			$query_results = $this->wp_db->get_row( "SELECT * FROM {$this->wp_db->prefix}cube_relationships WHERE relation_key = '{$relation_key}' AND relation_type = '{$relation_type}' ", ARRAY_A );
			if ( ! empty( $query_results ) && count( $query_results ) > 0 ) {
				if ( $relation_data_only ) {
					return is_serialized( $query_results['relation_data'] ) ? unserialize( $query_results['relation_data'] ) : $query_results['relation_data'];
				}

				return $query_results;
			}
		}

		return array();
	}

	public function cubewp_relation_user_metabox( $user ) {
		if ( isset( $user->ID ) ) {
			$output        = '<div class="cubewp-relations-metabox">';
			$user_id       = $user->ID;
			$PTU_relations = $this->get_relations( null, 'PTU', false );
			if ( ! empty( $PTU_relations ) ) {
				$post_relation_output = '';
				foreach ( $PTU_relations as $relation ) {
					$post_relation_output .= self::cubewp_relation_metabox_ui( $relation, $user_id );
				}
				if ( ! empty( $post_relation_output ) ) {
					$output .= '<div class="cubewp-post-relations">';
					$output .= '<h4>' . esc_html__( "Post To User Relations", "cubewp-framework" ) . '</h4>';
					$output .= $post_relation_output;
					$output .= '</div>';
				}
			}

			$UTU_relations = $this->get_relations( null, 'UTU', false );
			if ( ! empty( $UTU_relations ) ) {
				$user_relation_output = '';
				foreach ( $UTU_relations as $relation ) {
					$user_relation_output .= self::cubewp_relation_metabox_ui( $relation, $user_id );
				}
				if ( ! empty( $user_relation_output ) ) {
					$output .= '<div class="cubewp-post-relations">';
					$output .= '<h4>' . esc_html__( "User To User Relations", "cubewp-framework" ) . '</h4>';
					$output .= $user_relation_output;
					$output .= '</div>';
				}
			}
			$output .= '</div>';

			echo $output;
		}
	}

	private static function cubewp_relation_metabox_ui( $relation, $primary_id ) {
		$relation_ID      = $relation['ID'];
		$relation_type    = $relation['relation_type'];
		$relation_key     = $relation['relation_key'];
		$relation_data    = is_serialized($relation['relation_data']) ? unserialize($relation['relation_data']) : $relation['relation_data'];
		$relation_ui      = '';
		foreach ($relation_data as $ID => $data) {
			if (in_array($primary_id, $data)) {
				$cwp_relation_key = self::$meta_prefix.$relation_key;
				$edit_url         = '';
				$selected_text    = '';
				$relation_field   = array();

				if ( ! empty( $ID ) ) {
					if ( $relation_type == 'PTP' || $relation_type == 'PTU' ) {
						$edit_url       = get_edit_post_link( $ID );
						$selected_text  = get_the_title( $ID );
						$relation_field = get_field_options( $relation_key );
					} else if ( $relation_type == 'UTU' || $relation_type == 'UTP' ) {
						$edit_url       = get_edit_post_link( $ID );
						$selected_text  = get_userdata( $ID );
						$relation_field = get_user_field_options( $relation_key );
						if ( empty( $selected_text ) || is_wp_error( $selected_text ) || ! is_object( $selected_text ) ) {
							return $relation_ui;
						}
						$selected_text = $selected_text->display_name;
					}
					$relation_of = $ID;
					$relation_ui .= '<div class="cubewp-post-relation">';
					$relation_ui .= '<label for="' . esc_attr( $cwp_relation_key ) . '">' . esc_html( $relation_field['label'] ) . '</label>';
					$relation_ui .= '<a href="' . esc_url( $edit_url ) . '" target="_blank"><select id="' . esc_attr( $cwp_relation_key ) . '">';
					$relation_ui .= '<option value="' . esc_attr( $ID ) . '">' . esc_html( $selected_text ) . ' (' . esc_attr( $ID ) . ')</option>';
					$relation_ui .= '</select></a>';
					$relation_ui .= '<p class="cubewp-delete-relation" data-relation-id="' . esc_attr( $relation_ID ) . '" data-relation-of="' . esc_attr( $relation_of ) . '" data-relation-with="' . esc_attr( $primary_id ) . '">';
					$relation_ui .= esc_html__( "Remove Relation", "cubewp-framework" );
					$relation_ui .= '</p>';
					$relation_ui .= '</div>';
				}
			}
		}

		return $relation_ui;
	}

	public function cubewp_relation_metabox_render( $post ) {
		if ( isset( $post->ID ) ) {
			$output        = '<div class="cubewp-relations-metabox">';
			$post_id       = $post->ID;
			$PTP_relations = $this->get_relations( null, 'PTP', false );
			if ( ! empty( $PTP_relations ) ) {
				$post_relation_output = '';
				foreach ( $PTP_relations as $relation ) {
					$post_relation_output .= self::cubewp_relation_metabox_ui( $relation, $post_id );
				}
				if ( ! empty( $post_relation_output ) ) {
					$output .= '<div class="cubewp-post-relations">';
					$output .= '<h4>' . esc_html__( "Post To Post Relations", "cubewp-framework" ) . '</h4>';
					$output .= $post_relation_output;
					$output .= '</div>';
				}
			}

			$UTP_relations = $this->get_relations( null, 'UTP', false );
			if ( ! empty( $UTP_relations ) ) {
				$user_relation_output = '';
				foreach ( $UTP_relations as $relation ) {
					$user_relation_output .= self::cubewp_relation_metabox_ui( $relation, $post_id );
				}
				if ( ! empty( $user_relation_output ) ) {
					$output .= '<div class="cubewp-post-relations">';
					$output .= '<h4>' . esc_html__( "User To Post Relations", "cubewp-framework" ) . '</h4>';
					$output .= $user_relation_output;
					$output .= '</div>';
				}
			}
			$output .= '</div>';

			echo $output;
		}
	}

	public function cubewp_remove_relation() {
		if ( ! wp_verify_nonce( sanitize_text_field( $_POST['nonce'] ), 'cubewp_remove_nonce' ) ) {
			wp_send_json( array(
				'status' => 'error',
				'msg'    => esc_html__( "Security verification failed. Try again later.", "cubewp-framework" )
			) );
		}
		$relation_id   = sanitize_text_field( $_POST['relation_id'] );
		$relation_of   = sanitize_text_field( $_POST['relation_of'] );
		$relation_with = sanitize_text_field( $_POST['relation_with'] );
		if ( empty( $relation_id ) || empty( $relation_of ) || empty( $relation_with ) ) {
			wp_send_json( array(
				'status' => 'error',
				'msg'    => esc_html__( "Something went wrong. Try again later.", "cubewp-framework" )
			) );
		}

		$query_results = $this->wp_db->get_row( "SELECT * FROM {$this->wp_db->prefix}cube_relationships WHERE ID = '{$relation_id}' ", ARRAY_A );
		if ( ! empty( $query_results ) && count( $query_results ) > 0 ) {
			$relation_data = is_serialized( $query_results['relation_data'] ) ? unserialize( $query_results['relation_data'] ) : $query_results['relation_data'];
			$relation_key  = $query_results['relation_key'];
			$relation_type = $query_results['relation_type'];
			if ( ! empty( $relation_data ) ) {
				if ( isset( $relation_data[ $relation_of ] ) && ! empty( $relation_data[ $relation_of ] ) ) {
					$position = array_search( $relation_with, $relation_data[ $relation_of ] );
					if ( $position !== false ) {
						unset( $relation_data[ $relation_of ][ $position ] );
					}
					if ( empty( $relation_data[ $relation_of ] ) ) {
						unset( $relation_data[ $relation_of ] );
					}
					$relationship_name = self::$meta_prefix.$relation_key;

					if ( $relation_type == 'PTP' ) {
						$relation_of_meta = get_post_meta( $relation_of, $relation_key, true );
						if ( is_array( $relation_of_meta ) ) {
							$position = array_search( $relation_with, $relation_of_meta );
							if ( $position !== false ) {
								unset( $relation_of_meta[ $position ] );
							}
							if ( empty( $relation_of_meta ) ) {
								delete_post_meta( $relation_of, $relation_key );
							} else {
								update_post_meta( $relation_of, $relation_key, $relation_of_meta );
							}
						} else {
							delete_post_meta( $relation_of, $relation_key );
						}
						$meta_relation = get_post_meta($relation_with, $relationship_name, true );
						if (!is_array($meta_relation)) {
							$meta_relation = array();
						}
						$position = array_search( $relation_of, $meta_relation );
						if ( $position !== false ) {
							unset($meta_relation[$position]);
						}
						if (empty($meta_relation)) {
							delete_post_meta( $relation_with, $relationship_name );
						}else {
							update_post_meta( $relation_with, $relationship_name, $meta_relation );
						}
					} else if ( $relation_type == 'PTU' ) {
						$relation_of_meta = get_post_meta( $relation_of, $relation_key, true );
						if ( is_array( $relation_of_meta ) ) {
							$position = array_search( $relation_with, $relation_of_meta );
							if ( $position !== false ) {
								unset( $relation_of_meta[ $position ] );
							}
							if ( empty( $relation_of_meta ) ) {
								delete_post_meta( $relation_of, $relation_key );
							} else {
								update_post_meta( $relation_of, $relation_key, $relation_of_meta );
							}
						} else {
							delete_post_meta( $relation_of, $relation_key );
						}
						$meta_relation = get_user_meta($relation_with, $relationship_name, true );
						if (!is_array($meta_relation)) {
							$meta_relation = array();
						}
						$position = array_search( $relation_of, $meta_relation );
						if ( $position !== false ) {
							unset($meta_relation[$position]);
						}
						if (empty($meta_relation)) {
							delete_user_meta( $relation_with, $relationship_name );
						}else {
							update_user_meta( $relation_with, $relationship_name, $meta_relation );
						}
					} else if ( $relation_type == 'UTP' ) {
						$relation_of_meta = get_user_meta( $relation_of, $relation_key, true );
						if ( is_array( $relation_of_meta ) ) {
							$position = array_search( $relation_with, $relation_of_meta );
							if ( $position !== false ) {
								unset( $relation_of_meta[ $position ] );
							}
							if ( empty( $relation_of_meta ) ) {
								delete_user_meta( $relation_of, $relation_key );
							} else {
								update_user_meta( $relation_of, $relation_key, $relation_of_meta );
							}
						} else {
							delete_user_meta( $relation_of, $relation_key );
						}
						$meta_relation = get_post_meta($relation_with, $relationship_name, true );
						if (!is_array($meta_relation)) {
							$meta_relation = array();
						}
						$position = array_search( $relation_of, $meta_relation );
						if ( $position !== false ) {
							unset($meta_relation[$position]);
						}
						if (empty($meta_relation)) {
							delete_post_meta( $relation_with, $relationship_name );
						}else {
							update_post_meta( $relation_with, $relationship_name, $meta_relation );
						}
					} else if ( $relation_type == 'UTU' ) {
						$relation_of_meta = get_user_meta( $relation_of, $relation_key, true );
						if ( is_array( $relation_of_meta ) ) {
							$position = array_search( $relation_with, $relation_of_meta );
							if ( $position !== false ) {
								unset( $relation_of_meta[ $position ] );
							}
							if ( empty( $relation_of_meta ) ) {
								delete_user_meta( $relation_of, $relation_key );
							} else {
								update_user_meta( $relation_of, $relation_key, $relation_of_meta );
							}
						} else {
							delete_user_meta( $relation_of, $relation_key );
						}
						$meta_relation = get_user_meta($relation_with, $relationship_name, true );
						if (!is_array($meta_relation)) {
							$meta_relation = array();
						}
						$position = array_search( $relation_of, $meta_relation );
						if ( $position !== false ) {
							unset($meta_relation[$position]);
						}
						if (empty($meta_relation)) {
							delete_user_meta( $relation_with, $relationship_name );
						}else {
							update_user_meta( $relation_with, $relationship_name, $meta_relation );
						}
					}
					$this->update_relations( $relation_key, $relation_data, $relation_type );
					wp_send_json( array(
						'status' => 'success',
						'msg'    => esc_html__( "Relation successfully removed.", "cubewp-framework" )
					) );
				}
			}
		}

		wp_send_json( array(
			'status' => 'error',
			'msg'    => esc_html__( "Something went wrong. Try again later.", "cubewp-framework" )
		) );
	}

	private function update_relations( $relation_key, $relation_data, $relation_type ) {
		if ( empty( $relation_key ) || empty( $relation_type ) ) {
			return false;
		}
		$existing_data = $this->get_relations( $relation_key, $relation_type, false );
		if ( $existing_data ) {
			$where = array( 'ID' => $existing_data['ID'] );
			$this->wp_db->update( $this->wp_db->prefix . "cube_relationships", array(
				'relation_data' => is_serialized( $relation_data ) ? $relation_data : serialize( $relation_data ),
			), $where );

			return $existing_data['ID'];
		} else {
			$this->wp_db->insert( $this->wp_db->prefix . "cube_relationships", array(
				'relation_key'  => $relation_key,
				'relation_data' => is_serialized( $relation_data ) ? $relation_data : serialize( $relation_data ),
				'relation_type' => $relation_type,
			), array( '%s', '%s', '%s' ) );

			return $this->wp_db->insert_id;
		}
	}

	/**
	 * Method save_post_relation
	 *
	 * @param int    $primary_id
	 * @param array  $relationship_with
	 * @param string $meta_name
	 * @param string $relation_type
	 *
	 * @return bool
	 *
	 * @since  1.0.5
	 */
	public function save_relationship( int $primary_id, array $relationship_with, string $meta_name, string $relation_type ) {
		$this->destroy_post_relations( $primary_id, $meta_name, $relation_type );
		$cubewp_relationship_posts = $this->get_relations( $meta_name , $relation_type );
		$cubewp_relationship_posts = ( ! empty( $cubewp_relationship_posts ) && is_array( $cubewp_relationship_posts ) ) ? $cubewp_relationship_posts : array();
		if ( ! empty( $relationship_with ) ) {
			foreach ( $relationship_with as $relationship_with_id ) {
				if ( empty( $relationship_with_id ) ) {
					continue;
				}
				$relationship_name = self::$meta_prefix.$meta_name;
				if ( $relation_type == 'PTP' || $relation_type == 'UTP' ) {
					$meta_relation = get_post_meta( $relationship_with_id, $relationship_name, true );
					if ( ! is_array($meta_relation)) { $meta_relation = array(); }
					$meta_relation[] = $primary_id;
					update_post_meta( $relationship_with_id, $relationship_name, $meta_relation );
				} else if ( $relation_type == 'PTU' || $relation_type == 'UTU' ) {
					$meta_relation = get_user_meta($relationship_with_id, $relationship_name, true );
					if ( ! is_array($meta_relation)) { $meta_relation = array(); }
					$meta_relation[] = $primary_id;
					update_user_meta( $relationship_with_id, $relationship_name, $meta_relation );
				}
				$cubewp_relationship_posts[ $primary_id ][] = $relationship_with_id;
			}
			if ( ! empty( $cubewp_relationship_posts ) ) {
				$this->update_relations( $meta_name, $cubewp_relationship_posts, $relation_type );
			}
		}

		return true;
	}

	/**
	 * Method save_post_relation
	 *
	 * @param int    $primary_id
	 * @param string $meta_name
	 * @param string $relation_type
	 *
	 * @return void
	 * @since  1.0.5
	 */
	private function destroy_post_relations( int $primary_id, string $meta_name, string $relation_type ) {
		$all_posts_relations = $this->get_relations( $meta_name, $relation_type );
		if ( isset( $all_posts_relations[ $primary_id ] ) && ! empty( $all_posts_relations[ $primary_id ] ) ) {
			$post_relations = $all_posts_relations[ $primary_id ];
			unset( $all_posts_relations[ $primary_id ] );
			foreach ( $post_relations as $relation_id ) {
				$relationship_name = self::$meta_prefix.$meta_name;
				if ( $relation_type == 'PTP' || $relation_type == 'UTP' ) {
					$meta_relation = get_post_meta( $relation_id, $relationship_name, true );
					if (!is_array($meta_relation)) {
						$meta_relation = array();
					}
					$position = array_search( $primary_id, $meta_relation );
					if ( $position !== false ) {
						unset($meta_relation[$position]);
					}
					if (empty($meta_relation) || count($meta_relation) < 1) {
						delete_post_meta( $relation_id, $relationship_name );
					}else {
						update_post_meta( $relation_id, $relationship_name, $meta_relation );
					}
				} else if ( $relation_type == 'PTU' || $relation_type == 'UTU' ) {
					$meta_relation = get_user_meta($relation_id, $relationship_name, true );
					if (!is_array($meta_relation)) {
						$meta_relation = array();
					}
					$position = array_search( $primary_id, $meta_relation );
					if ( $position !== false ) {
						unset($meta_relation[$position]);
					}
					if (empty($meta_relation) || count($meta_relation) < 1) {
						delete_user_meta( $relation_id, $relationship_name );
					}else {
						update_user_meta( $relation_id, $relationship_name, $meta_relation );
					}
				}
			}
			$this->update_relations( $meta_name, $all_posts_relations, $relation_type );
		}
	}

	public static function init() {
		$CubeClass = __CLASS__;
		new $CubeClass;
	}
}