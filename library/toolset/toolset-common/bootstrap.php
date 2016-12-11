<?php

/**
* Toolset_Common_Bootstrap
*
* General class to manage common code loading for all Toolset plugins
*
* This class is used to load Toolset Common into all Toolset plugins that have it as a dependency.
* Note that Assets, Menu, Utils, Settings, Localization, Promotion, Debug, Admin Bar and WPML compatibility are always loaded when first instantiating this class.
* Toolset_Common_Bootstrap::load_sections must be called after_setup_theme:10 with an array of sections to load, named as follows:
* 	toolset_forms						Toolset Forms, the shared component for Types and CRED
* 	toolset_visual_editor				Visual Editor Addon, to display buttons and dialogs over editors
* 	toolset_parser						Toolset Parser, to parse conditionals
*
* New sections can be added here, following the same structure.
*
* Note that you have available the following constants:
* 	TOOLSET_COMMON_VERSION				The Toolset Common version
* 	TOOLSET_COMMON_PATH					The path to the active Toolset Common directory
* 	TOOLSET_COMMON_DIR					The name of the directory of the active Toolset Common
* 	TOOLSET_COMMON_URL					The URL to the root of Toolset Common, to be used in backend - adjusted as per SSL settings
* 	TOOLSET_COMMON_FRONTEND_URL			The URL to the root of Toolset Common, to be used in frontend - adjusted as per SSL settings
*
* 	TOOLSET_COMMON_PROTOCOL				Deprecated - To be removed - The protocol of TOOLSET_COMMON_URL - http | https
* 	TOOLSET_COMMON_FRONTEND_PROTOCOL	Deprecated - To be removed - The protocol of TOOLSET_COMMON_FRONTEND_URL - http | https
*
* @todo create an admin page with Common info: path, bundled libraries versions, etc
*/

class Toolset_Common_Bootstrap {

    private static $instance;
	private static $sections_loaded;
	
	public $assets_manager;
	public $object_relationship;
	public $menu;
	public $export_import_screen;
	public $settings_screen;
	public $localization;
	public $settings;
	public $promotion;
	public $wpml_compatibility;

	// Names of various sections/modules of the common library that can be loaded.
	const TOOLSET_AUTOLOADER = 'toolset_autoloader';
	const TOOLSET_DEBUG = 'toolset_debug';
	const TOOLSET_FORMS = 'toolset_forms';
	const TOOLSET_VISUAL_EDITOR = 'toolset_visual_editor';
	const TOOLSET_PARSER = 'toolset_parser';
	const TOOLSET_RESOURCES = 'toolset_res';
	const TOOLSET_LIBRARIES = 'toolset_lib';
	const TOOLSET_INCLUDES = 'toolset_inc';
	const TOOLSET_UTILS = 'toolset_utils';
	const TOOLSET_DIALOGS = 'toolset_dialogs';
	const TOOLSET_HELP_VIDEOS = 'toolset_help_videos';
	const TOOLSET_GUI_BASE = 'toolset_gui_base';
	const TOOLSET_RELATIONSHIPS = 'toolset_relationships';


    // Request mode
    const MODE_UNDEFINED = '';
    const MODE_AJAX = 'ajax';
    const MODE_ADMIN = 'admin';
    const MODE_FRONTEND = 'frontend';


    /**
     * @var string One of the MODE_* constants.
     */
    private $mode = self::MODE_UNDEFINED;


    private function __construct() {
		self::$sections_loaded = array();

	    // Register assets, utils, settings, localization, promotion, debug, admin bar and WPML compatibility
		$this->register_utils();
		$this->register_res();
		$this->register_libs();
		$this->register_inc();
		
		add_filter( 'toolset_is_toolset_common_available', '__return_true' );
		
		add_action( 'switch_blog', array( $this, 'clear_settings_instance' ) );

        /**
         * Action when the Toolset Common Library is completely loaded.
         *
         * @since m2m
         */
        do_action( 'toolset_common_loaded' );
    }

	/**
	 * @return Toolset_Common_Bootstrap
	 * @deprecated Use get_instance() instead.
	 */
	public static function getInstance() {
        return self::get_instance();
    }


	/**
	 * @return Toolset_Common_Bootstrap
	 * @since 2.1
	 */
	public static function get_instance() {
		if ( ! self::$instance ) {
			self::$instance = new Toolset_Common_Bootstrap();
		}
		return self::$instance;
	}


	/**
	 * Determine if a given section is already loaded.
	 * 
	 * @param string $section_name
	 * @return bool
	 * @since 2.1
	 */
	private function is_section_loaded( $section_name ) {
		return in_array( $section_name, self::$sections_loaded );
	}

	
	/**
	 * Add a section name to the list of the loaded ones.
	 * 
	 * @param string $section_name
	 * @since 2.1
	 */
	private function add_section_loaded( $section_name ) {
		self::$sections_loaded[] = $section_name;
	}


	/**
	 * Decide whether a particular section needs to be loaded.
	 * 
	 * @param string[] $sections_to_load Array of sections that should be loaded, or empty array to load all of them. 
	 * @param string $section_name Name of a section.
	 * @return bool
	 * @since 2.1
	 */
	private function should_load_section( $sections_to_load, $section_name ) {
		return ( empty( $sections_to_load ) || in_array( $section_name, $sections_to_load ) );
	}


	/**
	 * Apply a filter on the array of names loaded sections.
	 * 
	 * @param string $filter_name Name of the filter.
	 * @since 2.1
	 */
	private function apply_filters_on_sections_loaded( $filter_name ) {
		self::$sections_loaded = apply_filters( $filter_name, self::$sections_loaded );
	}

	
	/**
	 * Load sections on demand
	 *
	 * This needs to be called after after_setup_theme:10 because this file is not loaded before that
	 *
	 * @since 1.9
	 *
	 * @param string[] $load Names of sections to load or an empty array to load everything.
	 */
	public function load_sections( $load = array() ) {

		// Load toolset_debug on demand
		if ( $this->should_load_section( $load, self::TOOLSET_DEBUG ) ) {
			$this->register_debug();
		}

		// Maybe register forms
		if ( $this->should_load_section( $load, self::TOOLSET_FORMS ) ) {
			$this->register_toolset_forms();
		}

		// Maybe register the editor addon
		if ( $this->should_load_section( $load, self::TOOLSET_VISUAL_EDITOR ) ) {
			$this->register_visual_editor();
		}

		if ( $this->should_load_section( $load, self::TOOLSET_PARSER ) ) {
			$this->register_parser();
		}

	}
	
	public function register_res() {

		if ( ! $this->is_section_loaded( self::TOOLSET_RESOURCES ) ) {
			$this->add_section_loaded( self::TOOLSET_RESOURCES );
			// Use the class provided by Ric
			require_once( TOOLSET_COMMON_PATH . '/inc/toolset.assets.manager.class.php' );
			$this->assets_manager = Toolset_Assets_Manager::getInstance();
			$this->apply_filters_on_sections_loaded( 'toolset_register_assets_section' );
		}
	}
	
	public function register_libs() {

		if ( ! $this->is_section_loaded( self::TOOLSET_LIBRARIES ) ) {

			$this->add_section_loaded( self::TOOLSET_LIBRARIES );

			if ( ! class_exists( 'ICL_Array2XML' ) ) {
				require_once( TOOLSET_COMMON_PATH . '/lib/array2xml.php' );
			}
			if ( ! class_exists( 'Zip' ) ) {
				require_once( TOOLSET_COMMON_PATH . '/lib/Zip.php' );
			}
			if ( ! function_exists( 'adodb_date' ) ) {
				require_once( TOOLSET_COMMON_PATH . '/lib/adodb-time.inc.php' );
			}
			if ( ! class_exists( 'Toolset_CakePHP_Validation' ) ) {
				require_once( TOOLSET_COMMON_PATH . '/lib/cakephp.validation.class.php' );
			}
			if ( ! class_exists( 'Toolset_Validate' ) ) {
				require_once( TOOLSET_COMMON_PATH . '/lib/validate.class.php' );
			}
			if ( ! class_exists( 'Toolset_Enlimbo_Forms' ) ) {
				require_once( TOOLSET_COMMON_PATH . '/lib/enlimbo.forms.class.php' );
			}

			$this->apply_filters_on_sections_loaded( 'toolset_register_library_section' );
		}
	}
	
	public function register_inc() {

		if ( ! $this->is_section_loaded( self::TOOLSET_INCLUDES ) ) {

			$this->add_section_loaded( self::TOOLSET_INCLUDES );

			if ( ! class_exists( 'Toolset_Settings' ) ) {
				require_once( TOOLSET_COMMON_PATH . '/inc/toolset.settings.class.php' );
				$this->settings = Toolset_Settings::get_instance();
			}
			if ( ! class_exists( 'Toolset_Localization' ) ) {
				require_once( TOOLSET_COMMON_PATH . '/inc/toolset.localization.class.php' );
				$this->localization = new Toolset_Localization();
			}
			if ( ! class_exists( 'Toolset_WPLogger' ) ) {
				require_once( TOOLSET_COMMON_PATH . '/inc/toolset.wplogger.class.php' );
			}
			if ( ! class_exists( 'Toolset_Object_Relationship' ) ) {
				require_once( TOOLSET_COMMON_PATH . '/inc/toolset.object.relationship.class.php' );
				$this->object_relationship = Toolset_Object_Relationship::get_instance();
			}
			if ( ! class_exists( 'Toolset_Settings_Screen' ) ) {
				require_once( TOOLSET_COMMON_PATH . '/inc/toolset.settings.screen.class.php' );
				$this->settings_screen = new Toolset_Settings_Screen();
			}
			if ( ! class_exists( 'Toolset_Export_Import_Screen' ) ) {
				require_once( TOOLSET_COMMON_PATH . '/inc/toolset.export.import.screen.class.php' );
				$this->export_import_screen = new Toolset_Export_Import_Screen();
			}
			if ( ! class_exists( 'Toolset_Menu' ) ) {
				require_once( TOOLSET_COMMON_PATH . '/inc/toolset.menu.class.php' );
				$this->menu = new Toolset_Menu();
			}
			if ( ! class_exists( 'Toolset_Promotion' ) ) {
				require_once( TOOLSET_COMMON_PATH . '/inc/toolset.promotion.class.php' );
				$this->promotion = new Toolset_Promotion();
			}
			if ( ! class_exists( 'Toolset_Admin_Bar_Menu' ) ) {
				require_once( TOOLSET_COMMON_PATH . '/inc/toolset.admin.bar.menu.class.php' );
				global $toolset_admin_bar_menu;
				$toolset_admin_bar_menu = Toolset_Admin_Bar_Menu::get_instance();
			}
			if ( ! class_exists( 'Toolset_Internal_Compatibility' ) ) {
				require_once( TOOLSET_COMMON_PATH . '/inc/toolset.internal.compatibility.class.php' );
				$this->internal_compatibility = new Toolset_Internal_Compatibility();
			}
			if ( ! class_exists( 'Toolset_WPML_Compatibility' ) ) {
				require_once( TOOLSET_COMMON_PATH . '/inc/toolset.wpml.compatibility.class.php' );
				$this->wpml_compatibility = new Toolset_WPML_Compatibility();
			}
			if ( ! class_exists( 'Toolset_Relevanssi_Compatibility' ) ) {
				require_once( TOOLSET_COMMON_PATH . '/inc/toolset.relevanssi.compatibility.class.php' );
				$this->relevanssi_compatibility = new Toolset_Relevanssi_Compatibility();
			}

            if ( ! class_exists( 'Toolset_CssComponent' ) ) {
				require_once( TOOLSET_COMMON_PATH . '/inc/toolset.css.component.class.php' );
				$toolset_bs_component = Toolset_CssComponent::getInstance();
			}

			require_once( TOOLSET_COMMON_PATH . '/inc/toolset.compatibility.php' );
			require_once( TOOLSET_COMMON_PATH . '/inc/toolset.function.helpers.php' );
			require_once( TOOLSET_COMMON_PATH . '/deprecated.php' );

			$this->apply_filters_on_sections_loaded( 'toolset_register_include_section' );
		}
	}
	
	public function register_utils() {

		if( ! $this->is_section_loaded( self::TOOLSET_AUTOLOADER ) ) {
			// This needs to happen very very early
			require_once TOOLSET_COMMON_PATH . '/utility/autoloader.php';
			Toolset_Common_Autoloader::initialize();
			$this->add_section_loaded( self::TOOLSET_AUTOLOADER );
		}

		if ( ! $this->is_section_loaded( self::TOOLSET_UTILS ) ) {
			$this->add_section_loaded( self::TOOLSET_UTILS );
			require_once TOOLSET_COMMON_PATH . '/utility/utils.php';
		}

		// Although this is full of DDL prefixes, we need to actually port before using it.
		if ( ! $this->is_section_loaded( self::TOOLSET_DIALOGS ) ) {
			$this->add_section_loaded( self::TOOLSET_DIALOGS );
			require_once TOOLSET_COMMON_PATH . '/utility/dialogs/toolset.dialog-boxes.class.php' ;
		}

        if( ! $this->is_section_loaded( self::TOOLSET_HELP_VIDEOS ) ) {
            $this->add_section_loaded( self::TOOLSET_HELP_VIDEOS );
            require_once TOOLSET_COMMON_PATH . '/utility/help-videos/toolset-help-videos.php';
        }

		$this->apply_filters_on_sections_loaded( 'toolset_register_utility_section' );
	}


	public function register_debug() {

		if ( ! $this->is_section_loaded( self::TOOLSET_DEBUG ) ) {
			$this->add_section_loaded( self::TOOLSET_DEBUG );
			require_once( TOOLSET_COMMON_PATH . '/debug/debug-information.php' );

			$this->apply_filters_on_sections_loaded( 'toolset_register_debug_section' );
		}
	}
	
	public function register_toolset_forms() {
		
		if ( ! $this->is_section_loaded( self::TOOLSET_FORMS ) ) {
			$this->add_section_loaded( self::TOOLSET_FORMS );
			if ( ! class_exists( 'WPToolset_Forms_Bootstrap' ) ) {
				require_once TOOLSET_COMMON_PATH . '/toolset-forms/bootstrap.php';
			}
			$this->apply_filters_on_sections_loaded( 'toolset_register_forms_section' );
		}
	}
	
	public function register_visual_editor() {
		
		if ( ! $this->is_section_loaded( self::TOOLSET_VISUAL_EDITOR ) ) {
			$this->add_section_loaded( self::TOOLSET_VISUAL_EDITOR );
			require_once( TOOLSET_COMMON_PATH . '/visual-editor/editor-addon-generic.class.php' );
			require_once( TOOLSET_COMMON_PATH . '/visual-editor/editor-addon.class.php' );
			require_once( TOOLSET_COMMON_PATH . '/visual-editor/views-editor-addon.class.php' );
			$this->apply_filters_on_sections_loaded( 'toolset_register_visual_editor_section' );
		}
	}
	
	public function register_parser() {
		
		if ( ! $this->is_section_loaded( self::TOOLSET_PARSER ) ) {
			$this->add_section_loaded( self::TOOLSET_PARSER );
			if ( ! class_exists( 'Toolset_Regex' ) ) {
				require_once( TOOLSET_COMMON_PATH . '/expression-parser/parser.php' );
			}
			$this->apply_filters_on_sections_loaded( 'toolset_register_parsers_section' );
		}
	}

	public function clear_settings_instance() {
		Toolset_Settings::clear_instance();
	}


    /**
     * See get_request_mode().
     *
     * @since 2.3
     */
    private function determine_request_mode() {
        if( is_admin() ) {
            if( defined( 'DOING_AJAX' ) ) {
                $this->mode = self::MODE_AJAX;
            } else {
                $this->mode = self::MODE_ADMIN;
            }
        } else {
            $this->mode = self::MODE_FRONTEND;
        }
    }


    /**
     * Get current request mode.
     *
     * Possible values are:
     * - MODE_UNDEFINED before the main controller initialization is completed
     * - MODE_AJAX when doing an AJAX request
     * - MODE_ADMIN when showing a WP admin page
     * - MODE_FRONTEND when rendering a frontend page
     *
     * @return string
     * @since 2.3
     */
    public function get_request_mode() {
        if( self::MODE_UNDEFINED == $this->mode ) {
            $this->determine_request_mode();
        }
        return $this->mode;
    }

};