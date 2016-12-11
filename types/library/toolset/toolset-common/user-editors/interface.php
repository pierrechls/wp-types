<?php


interface Toolset_User_Editors_Manager_Interface {
	public function getEditors();

	/**
	 * @return Toolset_User_Editors_Editor_Interface
	 */
	public function getActiveEditor();
	public function run();
	public function addEditor( Toolset_User_Editors_Editor_Interface $editor );

	/**
	 * @return Toolset_User_Editors_Medium_Interface
	 */
	public function getMedium();
}