<?php


if( ! class_exists( 'Toolset_User_Editors_Editor_Abstract', false ) )
	require_once( TOOLSET_COMMON_PATH . '/user-editors/editor/abstract.php' );

class Toolset_User_Editors_Editor_Basic
	extends Toolset_User_Editors_Editor_Abstract {

	protected $id = 'basic';
	protected $name = 'HTML';

	public function requiredPluginActive() {
		return true;
	}

	public function run() {

	}
}