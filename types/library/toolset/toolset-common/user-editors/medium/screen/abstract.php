<?php

if( ! interface_exists( 'Toolset_User_Editors_Medium_Screen_Interface', false ) )
	require_once( TOOLSET_COMMON_PATH . '/user-editors/medium/screen/interface.php' );

abstract class Toolset_User_Editors_Medium_Screen_Abstract
	implements Toolset_User_Editors_Medium_Screen_Interface {

	/**
	 * @var Toolset_User_Editors_Manager_Interface
	 */
	protected $manager;

	public function isActive() {
		return false;
	}

	public function dropIfNotActive() {
		return true;
	}

	public function equivalentEditorScreenIsActive() {}

	public function addManager( Toolset_User_Editors_Manager_Interface $manager ) {
		$this->manager = $manager;
	}
}