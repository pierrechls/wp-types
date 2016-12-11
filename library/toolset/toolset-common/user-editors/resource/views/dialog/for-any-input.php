<?php

if( ! interface_exists( 'Toolset_User_Editors_Resource_Interface', false ) )
	require_once( TOOLSET_COMMON_PATH . '/user-editors/resource/interface.php' );

class Toolset_User_Editors_Resource_Views_Dialog_For_Any_Input
	implements Toolset_User_Editors_Resource_Interface {

	private static $instance;
	private $loaded;

	private function __construct(){}
	private function __clone(){}

	/**
	 * @return Toolset_User_Editors_Resource_Views_Dialog_For_Any_Input
	 */
	public static function getInstance() {
		if( self::$instance === null )
			self::$instance = new self;

		return self::$instance;
	}

	private function isLoaded() {
		$this->loaded = true;
	}

	public function load() {
		// abort on admin screen or if already loaded
		if( is_admin() || $this->loaded !== null )
			return;

		add_action( 'wp_enqueue_scripts', array( $this, '_actionScriptsAndStyles' ) );
		add_action( 'wp_footer', array( $this, '_actionRenderSelector') );
		$this->isLoaded();
	}

	public function _actionRenderSelector() {
		echo '<a class="js-wpv-fields-and-views-in-adminbar">+</a>';
	}

	public function _actionScriptsAndStyles() {
		wp_enqueue_style(
			'toolset-user-editors-ressource-views-dialog-for-any-input',
			TOOLSET_COMMON_URL . '/user-editors/resource/views/dialog/for-any-input.css',
			array(),
			TOOLSET_COMMON_VERSION
		);

		wp_enqueue_script(
			'toolset-user-editors-ressource-views-dialog-for-any-input',
			TOOLSET_COMMON_URL . '/user-editors/resource/views/dialog/for-any-input.js',
			array( 'jquery' ),
			TOOLSET_COMMON_VERSION,
			true
		);

		/*
		 * Use to add selectors. Each input selector has these fields
		 * array(
		 *      'stringSelector'       => '.fl-lightbox-content input:text',
		 *      'stringParentSelector' => 'td'
		 * );
		 */
		$input_selectors = apply_filters( 'toolset_user_editors_for_any_input_selectors', array() );

		wp_localize_script(
			'toolset-user-editors-ressource-views-dialog-for-any-input',
			'toolset_for_any_input',
			$input_selectors
		);
	}
}