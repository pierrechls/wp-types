<?php

if( ! interface_exists( 'Toolset_User_Editors_Manager_Interface', false ) ) {
	require_once( TOOLSET_COMMON_PATH . '/user-editors/interface.php' );
}

if( ! interface_exists( 'Toolset_User_Editors_Medium_Interface', false ) ) {
	require_once( TOOLSET_COMMON_PATH . '/user-editors/medium/interface.php' );
}

if( ! interface_exists( 'Toolset_User_Editors_Editor_Interface', false ) ) {
	require_once( TOOLSET_COMMON_PATH . '/user-editors/editor/interface.php' );
}

class Toolset_User_Editors_Manager implements  Toolset_User_Editors_Manager_Interface {

	/**
	 * The medium on which the editor should be used
	 * e.g. Views Content Template
	 * @var Toolset_User_Editors_Medium_Interface
	 */
	protected $medium;

	/**
	 * All available editors.
	 * @var Toolset_User_Editors_Editor_Interface[]
	 */
	protected $editors = array();

	/**
	 * Current active editor (chosen by user)
	 * @var Toolset_User_Editors_Editor_Interface
	 */
	protected $active_editor;

	/**
	 * Toolset_User_Editors_Provider constructor.
	 *
	 * @param Toolset_User_Editors_Medium_Interface $medium
	 */
	public function __construct( Toolset_User_Editors_Medium_Interface $medium ) {
		$this->medium = $medium;
		$this->medium->addManager( $this );
	}

	/**
	 * @param Toolset_User_Editors_Editor_Interface $editor
	 *
	 * @return bool
	 */
	public function addEditor( Toolset_User_Editors_Editor_Interface $editor ) {
		if( ! $editor->requiredPluginActive() ) {
			return false;
		}

		$this->editors[$editor->getId()] = $editor;
		return true;
	}

	/**
	 * Return all editors
	 * @return Toolset_User_Editors_Editor_Interface[]
	 */
	public function getEditors() {
		return $this->editors;
	}

	/**
	 * Return current active editor
	 *
	 * @return false|Toolset_User_Editors_Editor_Interface
	 */
	public function getActiveEditor() {
		if( $this->active_editor === null ) {
			$this->active_editor = $this->fetchActiveEditor();
		}

		return $this->active_editor;
	}

	public function getMedium() {
		return $this->medium;
	}

	/**
	 * @return bool
	 */
	protected function fetchActiveEditor() {

		$user_editor_choice = $this->medium->userEditorChoice();
		// check every screen of medium
		foreach( $this->medium->getScreens() as $id => $screen ) {

			// if screen is active
			if( $id_medium = $screen->isActive() ) {
				$screen->addManager( $this );

				// check editors
				foreach( $this->getEditors() as $editor ) {

					// skip if we have a user editor choice and current editor not matching selection
					if( $user_editor_choice
					    && array_key_exists( $user_editor_choice, $this->editors )
					    && $user_editor_choice !=  $editor->getId()
					)
						continue;

					// check editor screens
					if( $editor_screen = $editor->getScreenById( $id ) ) {
						$this->medium->setId( $id_medium );
						if( $editor_screen->isActive() ) {
							$screen->equivalentEditorScreenIsActive();
							
							return $editor;
						} else if( $screen->dropIfNotActive() ) {
							$this->medium->removeScreen( $id );
						}
					}
				}
			} else if( $screen->dropIfNotActive() ) {
				$this->medium->removeScreen( $id );
			}
		}

		// if we have no editor active here it still can be a frontend
		if ( $this->active_editor === null ) {
			add_action( 'wp', array( $this, 'run' ), -1000 );
		}

		return false;
	}

	public function run() {
		if( $this->active_editor == false ) {
			$this->active_editor = null;
		}

		if( $editor = $this->getActiveEditor() ) {
			$editor->run();
		}
	}
}