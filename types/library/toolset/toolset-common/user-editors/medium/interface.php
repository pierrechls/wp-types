<?php


if( ! interface_exists( 'Toolset_User_Editors_Medium_Screen_Interface', false ) )
	require_once( TOOLSET_COMMON_PATH . '/user-editors/medium/screen/interface.php' );


interface Toolset_User_Editors_Medium_Interface {

	/**
	 * Add a screen with an id. The id should be the same as used for the equivalent editor screen id.
	 * e.g. 'backend' for Medium_Screen_Backend && also 'backend' for Editor_Screen_Backend
	 *
	 * @param $id
	 * @param Toolset_User_Editors_Medium_Screen_Interface $screen
	 */
	public function addScreen( $id, Toolset_User_Editors_Medium_Screen_Interface $screen );

	/**
	 * Return all registered screens
	 *
	 * @return Toolset_User_Editors_Medium_Screen_Interface[]
	 */
	public function getScreens();

	/**
	 * If a screen is not active it should get removed, because sometimes
	 * a needs another round of check.
	 * e.g. ct frontend display needs to run on 'wp' hook, because global $post must be set
	 *
	 * @param $id
	 */
	public function removeScreen( $id );

	/**
	 * This function is used by screen objects.
	 * e.g. Medium_Screen_Content_Template_Backend set it if $_REQUEST['ct_id'] is available
	 * @param $id
	 */
	public function setId( $id );

	/**
	 * Id of the current medium element
	 * e.g. Id of a content template
	 *
	 * @return int
	 */
	public function getId();

	/**
	 * Slug of medium type
	 * e.g. Content template has the slug 'view-template'
	 *
	 * @return string
	 */
	public function getSlug();

	/**
	 * The id of the editor the user has chosen
	 * e.g. for Beaver Builder it would return 'beaver'
	 *
	 * @return string id of the editor
	 */
	public function userEditorChoice();

	/**
	 * List of allowed templates for using a Frontend-Editor
	 * e.g. Content Templates provides the templates of the 'Usage' assignments
	 * @return mixed
	 */
	public function getFrontendTemplates();

	/**
	 * Used by the editor, to give the medium the editor backend output
	 * The medium than decides where the output is placed
	 * e.g. Beaver generates "Select template and start..." and gives
	 *      it to Content Template which decides where it is placed
	 *
	 * @param $content callable
	 */
	public function setHtmlEditorBackend( $content );

	/**
	 * Used by setup, to give the medium the user selection for editors
	 * e.g. Setup generates editor selection 'Default | Visual Composer | Beaver'
	 *      and gives it to Content Template which decides where to place
	 *
	 * @param $selection callable
	 */
	// public function setHtmlEditorSelection( $selection );

	/**
	 * Some editors needs a page reload after changes are done to the medium
	 * with this function the medium will do a reload, even if by default it's stored via ajax
	 * e.g. this function is called by Beaver when used on Content Templates
	 */
	public function pageReloadAfterBackendSave();


	/**
	 * This manager class uses this function to make itself available for the medium
	 * @param Toolset_User_Editors_Manager_Interface $manager
	 */
	public function addManager( Toolset_User_Editors_Manager_Interface $manager );


	/**
	 * Returns the stored editor id for the current medium
	 * e.g. user selected Beaver on CT, this will return 'beaver'
	 * @return string
	 */
	public function getOptionNameEditorChoice();
}