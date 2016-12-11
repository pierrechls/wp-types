<?php

if( ! interface_exists( 'Toolset_User_Editors_Editor_Screen_Interface', false ) )
	require_once( TOOLSET_COMMON_PATH . '/user-editors/editor/screen/interface.php' );

interface Toolset_User_Editors_Editor_Interface {
	public function requiredPluginActive();
	public function addScreen( $id, Toolset_User_Editors_Editor_Screen_Interface $screen );
	public function run();

	/**
	 * @return false|Toolset_User_Editors_Editor_Screen_Interface
	 */
	public function getScreenById( $id );

	public function getId();
	public function getName();
	public function getOptionName();
}

