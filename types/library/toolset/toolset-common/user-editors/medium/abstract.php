<?php

if( ! interface_exists( 'Toolset_User_Editors_Medium_Interface', false ) ) {
	require_once( TOOLSET_COMMON_PATH . '/user-editors/medium/interface.php' );
}


/**
 * Class Toolset_User_Editors_Medium_Abstract
 */
abstract class Toolset_User_Editors_Medium_Abstract
	implements Toolset_User_Editors_Medium_Interface {

	/**
	 * ID of the post the editor is related to
	 * e.g. Content Template ID
	 *
	 * @var int
	 */
	protected $id;

	/**
	 * Slug of Medium
	 * e.g. for Content Template it is 'view-template'
	 * @var string
	 */
	protected $slug;

	/**
	 * All possible screens.
	 * @var Toolset_User_Editors_Medium_Screen_Interface[]
	 */
	protected $screens;

	/**
	 * our defined slug of editor
	 * e.g. for Beaver Builder we use 'beaver'
	 * 
	 * @var string
	 */
	protected $user_editor_choice;


	/**
	 * @var Toolset_User_Editors_Manager_Interface
	 */
	protected $manager;


	protected $option_name_editor_choice;

	/**
	 * @param $id
	 */
	public function setId( $id ) {
		$this->id = $id;
	}

	public function getId() {
		return $this->id;
	}

	public function getSlug() {
		return $this->slug;
	}

	public function getOptionNameEditorChoice() {
		return $this->option_name_editor_choice;
	}

	/**
	 * @param $id
	 * @param Toolset_User_Editors_Medium_Screen_Interface $screen
	 */
	public function addScreen( $id, Toolset_User_Editors_Medium_Screen_Interface $screen ) {
		$this->screens[$id] = $screen;
	}

	/**
	 * @param $id
	 */
	public function removeScreen( $id ) {
		if( array_key_exists( $id, $this->screens ) ) {
			unset( $this->screens[$id] );
		}
	}

	/**
	 * @return Toolset_User_Editors_Medium_Screen_Interface[]
	 */
	public function getScreens() {
		return $this->screens;
	}

	public function addManager( Toolset_User_Editors_Manager_Interface $manager ) {
		$this->manager = $manager;
	}

}