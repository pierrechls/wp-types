<?php

if( ! interface_exists( 'Toolset_User_Editors_Resource_Interface', false ) )
	require_once( TOOLSET_COMMON_PATH . '/user-editors/resource/interface.php' );

class Toolset_User_Editors_Resource_Views_Dialog
	implements Toolset_User_Editors_Resource_Interface {
	
	private static $instance;
	private $loaded;

	private function __construct(){}
	private function __clone(){}

	/**
	 * @return Toolset_User_Editors_Resource_Views_Dialog
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

		// this allows Views "Fields and Views" dialogs to load without a post type
		add_filter( 'wpv_filter_dialog_for_editors_requires_post', '__return_false' );
		add_filter( 'wpv_render_dialogs_on_frontend', '__return_true' );
		require_once( WPV_PATH . '/inc/classes/shortcodes/selector/frontend.php' );

		add_action( 'init', array( $this, '_actionRegisterViewsButton') );
		add_action( 'wp_enqueue_scripts', array( $this, '_actionScriptsAndStyles' ) );

		// woocommerce views
		if( function_exists( 'wcviews_shortcodes_gui_init' )
		    && function_exists( 'wcviews_shortcodes_gui_js_init' ) ) {
			add_action( 'init', 'wcviews_shortcodes_gui_init' );
			add_action( 'wp_head', 'wcviews_shortcodes_gui_js_init' );
		}

		$this->isLoaded();
	}

	public function _actionRegisterViewsButton() {

		if( ! class_exists( 'WP_Views' ) ) {
			remove_action( 'wp_enqueue_scripts', array( $this, '_actionScriptsAndStyles' ) );
			$this->isLoaded();
			return;
		}

		$view = new WP_Views();
		$view->wpv_register_assets();
		$view->add_dialog_to_editors();
	}

	public function _actionScriptsAndStyles() {
		if ( ! wp_script_is( 'views-shortcodes-gui-script' ) ) {
			wp_enqueue_script( 'views-shortcodes-gui-script' );
		}
		if ( ! wp_script_is( 'jquery-ui-resizable' ) ) {
			wp_enqueue_script('jquery-ui-resizable');
		}
		if ( ! wp_style_is( 'views-admin-css' ) ) {
			wp_enqueue_style( 'views-admin-css' );
		}

		if ( ! wp_script_is( 'views-codemirror-conf-script' ) ) {
			wp_enqueue_script( 'views-codemirror-conf-script' );
		}
		if ( ! wp_style_is( 'toolset-meta-html-codemirror-css' ) ) {
			wp_enqueue_style( 'toolset-meta-html-codemirror-css' );
		}
		if ( ! wp_script_is( 'views-embedded-script' ) ) {
			wp_enqueue_script( 'views-embedded-script' );
		}
		if ( ! wp_script_is( 'views-utils-script' ) ) {
			wp_enqueue_script( 'views-utils-script' );
		}

		wp_enqueue_style(
			'toolset-user-editors-ressource-views-dialog',
			TOOLSET_COMMON_URL . '/user-editors/resource/views/dialog/dialog.css',
			array(),
			TOOLSET_COMMON_VERSION
		);
	}
}