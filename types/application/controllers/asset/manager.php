<?php

/**
 * The script and style asset manager for Types implemented in a standard Toolset way.
 * 
 * Keeping this separate from Types_Assets also for performance reasons (this is not needed at all times).
 * 
 * @since 2.0
 */
final class Types_Asset_Manager extends Toolset_Assets_Manager {

	const SCRIPT_KNOCKOUT = 'knockout';

	const SCRIPT_ADJUST_MENU_LINK = 'types-adjust-menu-link';
	const SCRIPT_SLUG_CONFLICT_CHECKER = 'types-slug-conflict-checker';

	const SCRIPT_PAGE_EDIT_POST_TYPE = 'types-page-edit-post-type';
	const SCRIPT_PAGE_EDIT_TAXONOMY = 'types-page-edit-taxonomy';

	// Registered in Toolset common
	
	const SCRIPT_DIALOG_BOXES = 'ddl-dialog-boxes';
	
	const SCRIPT_UTILS = 'toolset-utils';

	const SCRIPT_HEADJS = 'headjs';
	
	// WordPress Core handles
	
	const STYLE_JQUERY_UI_DIALOG = 'wp-jquery-ui-dialog';


	/**
	 * @return Types_Asset_Manager
	 */
	static public function get_instance() {
		return parent::getInstance();
	}
	
	
	protected function __initialize_styles() {
		return parent::__initialize_styles();
	}


	protected function __initialize_scripts() {

		$this->register_script(
			self::SCRIPT_KNOCKOUT,
			TYPES_RELPATH . '/library/knockout/knockout-3.4.0.debug.js',
			array(),
			'3.4.0'
		);

		$this->register_script(
			self::SCRIPT_ADJUST_MENU_LINK,
			TYPES_RELPATH . '/public/page/adjust_submenu_links.js',
			array( 'jquery', 'underscore' ),
			TYPES_VERSION
		);

		$this->register_script(
			self::SCRIPT_SLUG_CONFLICT_CHECKER,
			TYPES_RELPATH . '/public/js/slug_conflict_checker.js',
			array( 'jquery', 'underscore' ),
			TYPES_VERSION
		);

		$this->register_script(
			self::SCRIPT_PAGE_EDIT_POST_TYPE,
			TYPES_RELPATH . '/public/page/edit_post_type/main.js',
			array( 'jquery', 'underscore', self::SCRIPT_SLUG_CONFLICT_CHECKER, self::SCRIPT_UTILS ),
			TYPES_VERSION
		);

		$this->register_script(
			self::SCRIPT_PAGE_EDIT_TAXONOMY,
			TYPES_RELPATH . '/public/page/edit_taxonomy/main.js',
			array( 'jquery', 'underscore', self::SCRIPT_SLUG_CONFLICT_CHECKER, self::SCRIPT_UTILS ),
			TYPES_VERSION
		);


		return parent::__initialize_scripts();
	}


	/**
	 * @param Toolset_Script $script
	 */
	public function register_toolset_script( $script ) {
		if ( ! isset( $this->scripts[ $script->handle ] ) ) {
			$this->scripts[ $script->handle ] = $script;
		}
	}


	/**
	 * @param Toolset_Style $style
	 */
	public function register_toolset_style( $style ) {
		if( !isset( $this->styles[ $style->handle ] ) ) {
			$this->styles[ $style->handle ] = $style;
		}
	}

}