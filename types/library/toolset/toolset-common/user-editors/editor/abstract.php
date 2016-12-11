<?php

if( ! interface_exists( 'Toolset_User_Editors_Editor_Interface', false ) )
	require_once( TOOLSET_COMMON_PATH . '/user-editors/editor/interface.php' );

abstract class Toolset_User_Editors_Editor_Abstract
	implements Toolset_User_Editors_Editor_Interface {

	protected $id;
	protected $name;
	protected $option_name = '_toolset_user_editors_editor_default';

	/**
	 * All possible screens.
	 * @var Toolset_User_Editors_Editor_Screen_Interface[]
	 */
	protected $screens;

	/**
	 * @var Toolset_User_Editors_Medium_Interface
	 */
	protected $medium;

	public function __construct( Toolset_User_Editors_Medium_Interface $medium ) {
		$this->medium = $medium;
	}

	public function getId() {
		return $this->id;
	}

	public function getName() {
		return $this->name;
	}

	public function getOptionName() {
		return $this->option_name;
	}

	public function requiredPluginActive() {
		return false;
	}

	public function addScreen( $id, Toolset_User_Editors_Editor_Screen_Interface $screen ) {
		$screen->addEditor( $this );
		$screen->addMedium( $this->medium );
		$this->screens[$id] = $screen;
	}

	/**
	 * @param $id
	 *
	 * @return false|Toolset_User_Editors_Editor_Screen_Interface
	 */
	public function getScreenById( $id ) {
		if( $this->screens === null )
			return false;

		if( array_key_exists( $id, $this->screens ) )
			return $this->screens[$id];

		return false;
	}
}

