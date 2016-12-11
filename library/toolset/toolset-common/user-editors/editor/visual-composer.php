<?php

if( ! class_exists( 'Toolset_User_Editors_Editor_Abstract', false ) )
	require_once( TOOLSET_COMMON_PATH . '/user-editors/editor/abstract.php' );

class Toolset_User_Editors_Editor_Visual_Composer
	extends Toolset_User_Editors_Editor_Abstract {

	protected $id = 'vc';
	protected $name = 'Visual Composer';
	protected $option_name = '_toolset_user_editors_vc';

	/**
	 * Minimum Version
	 * @var string version number
	 */
	protected $minimum_version = '4.11';

	public function requiredPluginActive() {
		
		if ( ! apply_filters( 'toolset_is_views_available', false ) ) {
			return false;
		}
		
		if( ! defined( 'WPB_VC_VERSION' ) )
			return false;

		// version too low
		// Todo generalise prove of version and move to abstract for all editors
		if( version_compare( WPB_VC_VERSION, $this->minimum_version ) < 0 ) {
			add_filter( 'wpv_ct_control_switch_editor_buttons', array( $this, '_filterAddDisabledButton' ) );
			return false;
		}

		return true;
	}

	public function run() {
		// register medium slug
		add_filter( 'vc_check_post_type_validation', array( $this, '_filterSupportMedium' ), 10, 2 );
	}

	/**
	 * If version requirements does not met, we show a hint.
	 *
	 * @param $buttons
	 * @return array
	 */
	public function _filterAddDisabledButton( $buttons ) {
		$buttons[] = '<button class="button-secondary" onClick="javascript:alert( jQuery( this ).attr( \'title\' ) );" title="' . sprintf( __( 'Version %s or higher required', 'wpv-views' ), $this->minimum_version ) . '">' . $this->name . '</button>';
		$buttons = array_reverse( $buttons );
		return $buttons;
	}

	/**
	 * We need to add Views type of content templates
	 * to the allowed types of Visual Composer
	 *
	 *
	 * @param $default
	 * @param $type
	 *
	 * @return bool
	 */
	public function _filterSupportMedium( $default, $type ) {
		if( $type == $this->medium->getSlug() )
			return true;

		return $default;
	}
}
