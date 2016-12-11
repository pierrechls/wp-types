<?php


if( ! interface_exists( 'Toolset_User_Editors_Editor_Screen_Interface', false ) )
	require_once( TOOLSET_COMMON_PATH . '/user-editors/editor/screen/interface.php' );

abstract class Toolset_User_Editors_Editor_Screen_Abstract
	implements Toolset_User_Editors_Editor_Screen_Interface {

	/**
	 * @var Toolset_User_Editors_Medium_Interface
	 */
	protected $medium;

	/**
	 * @var Toolset_User_Editors_Editor_Interface
	 */
	protected $editor;


	public function addMedium( Toolset_User_Editors_Medium_Interface $medium ) {
		$this->medium = $medium;
	}

	public function addEditor( Toolset_User_Editors_Editor_Interface $editor ) {
		$this->editor = $editor;
	}

	public function isActive() {
		return false;
	}
}