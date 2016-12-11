<?php

if( ! interface_exists( 'Toolset_User_Editors_Resource_Interface', false ) )
	require_once( TOOLSET_COMMON_PATH . '/user-editors/resource/interface.php' );

class Toolset_User_Editors_Resource_Views_Dialog_Types_Fields
	implements Toolset_User_Editors_Resource_Interface {
	private static $instance;
	private $loaded;

	private function __construct(){}
	private function __clone(){}

	/**
	 * @return Toolset_User_Editors_Resource_Views_Dialog_Types_Fields
	 */
	public static function getInstance() {
		if( self::$instance === null )
			self::$instance = new self;

		return self::$instance;
	}

	private function isLoaded() {
		$this->loaded = true;
	}

	public function load() {
		// abort on admin screen or if already loaded
		if( is_admin() || $this->loaded !== null )
			return;

		// Types Fields for "Fields and Views"
		if( defined( 'TYPES_RELPATH' ) )
			add_filter( 'editor_addon_menus_wpv-views', array( $this, '_filterTypesFieldsToViewsDialog' ) );

		$this->isLoaded();
	}

	/**
	 * FOLLOWING IS SIMPLY COPIED OUT OF TYPES
	 * TODO make original source reusable and delete copied code
	 */
	public function _filterTypesFieldsToViewsDialog( $menu ) {
		if( is_admin() || ! defined( 'TYPES_RELPATH' ) )
			return;

		wp_enqueue_script(
			'wpcf-js-embedded',
			TYPES_RELPATH . '/library/toolset/types/embedded/resources/js/basic.js',
			array('jquery', 'jquery-ui-sortable', 'jquery-ui-draggable', 'jquery-ui-tabs', 'toolset_select2'),
			WPCF_VERSION
		);

		$this->types_js_settings();

		$post_type = 'wp-types-group';
		$only_active = false;
		$add_fields = false;

		$cache_group = 'types_cache_groups';
		$cache_key = md5( 'group::_get_group' . $post_type );
		$cached_object = wp_cache_get( $cache_key, $cache_group );
		if ( false === $cached_object ) {
			$groups = get_posts( 'numberposts=-1&post_type=' . $post_type . '&post_status=null' );
			wp_cache_add( $cache_key, $groups, $cache_group );
		} else {
			$groups = $cached_object;
		}
		$_groups = array();
		if ( !empty( $groups ) ) {
			foreach ( $groups as $k => $group ) {
				$group = wpcf_admin_fields_adjust_group( $group, $add_fields );
				if ( $only_active && !$group['is_active'] ) {
					continue;
				}
				$_groups[$k] = $group;
			}
		}

		$groups = $_groups;

		$all_post_types = implode( ' ', get_post_types( array('public' => true) ) );
		$add = array();
		if ( !empty( $groups ) ) {

			// $group_id is blank therefore not equal to $group['id']
			// use array for item key and CSS class
			$item_styles = array();
			global $post;

			foreach ( $groups as $group_id => $group ) {

				$fields = $this->wpcf_admin_fields_get_fields_by_group( $group['id'],
					'slug', true, false, true );
				if ( !empty( $fields ) ) {
					// code from Types used here without breaking the flow
					// get post types list for every group or apply all
					$post_types = get_post_meta( $group['id'],
						'_wp_types_group_post_types', true );
					if ( $post_types == 'all' ) {
						$post_types = $all_post_types;
					}
					$post_types = trim( str_replace( ',', ' ', $post_types ) );
					$item_styles[$group['name']] = $post_types;

					foreach ( $fields as $field_id => $field ) {

						$callback = 'wpcfFieldsEditorCallback(\'' . $field['id']
						            . '\', \'postmeta\', ' . $post->ID . ')';

						$menu[$group['name']][stripslashes( $field['name'] )] = array(
							stripslashes( $field['name'] ), trim( $this->copy_fields_get_shortcode( $field ),
								'[]' ), $group['name'], $callback);

					}
				}
			}
		}

		return $menu;
	}

	public function types_js_settings() {

		$settings['wpnonce'] = wp_create_nonce( '_typesnonce' );
		$settings['validation'] = array();
		echo '
		<style>
		#colorbox.js-wpcf-colorbox-with-iframe { z-index: 110000 !important; }
</style>
        <script type="text/javascript">
            //<![CDATA[
            var types = ' . json_encode( $settings ) . ';
            //]]>
        </script>';
	}

	protected function copy_fields_get_shortcode( $field, $add = '', $content = '' ) {
		$shortcode = '[';
		$shortcode .= "types field='" . $field['slug'] . "'" . $add;
		$shortcode .= ']' . $content . '[/types]';
		$shortcode = apply_filters( 'wpcf_fields_shortcode', $shortcode, $field );
		$shortcode = apply_filters( 'wpcf_fields_shortcode_type_' . $field['type'], $shortcode, $field );
		$shortcode = apply_filters( 'wpcf_fields_shortcode_slug_' . $field['slug'], $shortcode, $field );
		return $shortcode;
	}

	protected function wpcf_admin_fields_get_fields_by_group( $group_id, $key = 'slug',
	                                                          $only_active = false, $disabled_by_type = false,
	                                                          $strictly_active = false, $post_type = 'wp-types-group',
	                                                          $option_name = 'wpcf-fields', $use_cache = true ) {
		static $cache = array();
		$cache_key = md5( serialize( func_get_args() ) );
		if ( $use_cache && isset( $cache[$cache_key] ) ) {
			return $cache[$cache_key];
		}
		$group_fields = get_post_meta( $group_id, '_wp_types_group_fields', true );
		if ( empty( $group_fields ) ) {
			return array();
		}
		$group_fields = explode( ',', trim( $group_fields, ',' ) );
		$fields = wpcf_admin_fields_get_fields( $only_active, $disabled_by_type,
			$strictly_active, $option_name );
		$results = array();
		foreach ( $group_fields as $field_id ) {
			if ( !isset( $fields[$field_id] ) ) {
				continue;
			}
			$results[$field_id] = $fields[$field_id];
		}
		if ( $use_cache ) {
			$cache[$cache_key] = $results;
		}
		return $results;
	}


}