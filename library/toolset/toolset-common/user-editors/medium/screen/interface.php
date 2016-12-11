<?php


interface Toolset_User_Editors_Medium_Screen_Interface {
	public function isActive();

	/**
	 * To save resources drop Screens which are not active,
	 * We need this because some screens need to be checked on 'wp', but in general we
	 * check the screens on plugins_loaded.
	 */
	public function dropIfNotActive();

	/**
	 * This function is called if the Screen and the equivalent Editor Screen is active
	 * e.g. Screen_Beaver_Backend is active && Screen_CT_Backend is active = Screen_CT_Backend->equivalentEditorScreenIsActive()
	 */
	public function equivalentEditorScreenIsActive();

	/**
	 * This manager class uses this function to make itself available for the medium
	 * @param Toolset_User_Editors_Manager_Interface $manager
	 */
	public function addManager( Toolset_User_Editors_Manager_Interface $manager );
}
