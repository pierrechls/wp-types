<?php

if ( defined( 'WPT_ASSETS_MANAGER' ) ) {
    return; 
}

define( 'WPT_ASSETS_MANAGER', true );

class Toolset_Style
{

	public function __construct( $handle, $path = 'wordpress_default', $deps = array(), $ver = false, $media = 'screen' ) {
		$this->handle	= $handle;
		$this->path		= $path;
		$this->deps		= $deps;
		$this->ver		= $ver;
		$this->media	= $media;

		if ( 
			$this->compare_versions() 
			&& $this->path != 'wordpress_default'
		) {
			wp_register_style( $this->handle, $this->path, $this->deps, $this->ver, $this->media );
		}
	}

	public function enqueue() {
		if ( $this->is_enqueued() === false ) {
			wp_enqueue_style( $this->handle );
		}
	}

	protected function compare_versions() {
			global $wp_styles;

			if ( isset( $wp_styles->registered ) ) {
				$wpt_registered_styles = $wp_styles->registered;
				if ( isset( $wpt_registered_styles[ $this->handle ] ) ) {
					$registered = $wpt_registered_styles[ $this->handle ];
					if ( (float) $registered->ver < (float) $this->ver ) {
						$wp_styles->remove( $this->handle );
						return true;
					} else {
						return false;
					}
				}
			}

			return $this->is_registered() === false;
	}

	public function deregister() {
		if ( $this->is_registered() !== false ) {
			wp_deregister_style( $this->handle );
		}
	}


	protected function is_registered() {
		return wp_style_is( $this->handle, 'registered' );
	}

	protected function is_enqueued() {
		return wp_style_is( $this->handle, 'enqueued' );
	}
}

class Toolset_Script
{
	public function __construct( $handle, $path = 'wordpress_default', $deps = array(), $ver = false, $in_footer = false ) {
		$this->handle		= $handle;
		$this->path			= $path;
		$this->deps			= $deps;
		$this->ver			= $ver;
		$this->in_footer	= $in_footer;

		if ( 
			$this->compare_versions() 
			&& $this->path != 'wordpress_default' 
		) {
			wp_register_script( $this->handle, $this->path, $this->deps, $this->ver, $this->in_footer );
		}
	}

	public function enqueue() {
		if ( $this->is_enqueued() === false ) {
			wp_enqueue_script( $this->handle );
		}
	}

    protected function compare_versions() {
        global $wp_scripts;

        if ( isset( $wp_scripts->registered ) ) {
			$wpt_registered_scripts = $wp_scripts->registered;
			if ( isset( $wpt_registered_scripts[ $this->handle ] ) ) {
				$registered = $wpt_registered_scripts[$this->handle];
				if ( (float) $registered->ver < (float) $this->ver ) {
					$wp_scripts->remove( $this->handle );
					return true;
				} else {
					return false;
				}
			}
        }

        return $this->is_registered() === false;
    }

	public function localize( $object, $args ) {
		if ( $this->is_registered() ) {
			wp_localize_script( $this->handle, $object, $args );
		}
	}

	public function deregister() {
		if ( $this->is_registered() !== false ) {
			wp_deregister_script( $this->handle );
		}
	}

	protected function is_registered() {
		return wp_script_is( $this->handle, 'registered' );
	}

	protected function is_enqueued() {
		return wp_script_is( $this->handle, 'enqueued' );
	}
}

class Toolset_Assets_Manager
{
	protected static $instance;
	protected $styles		= array();
	protected $scripts		= array();
	
	/**
	* assets_url
	*
	* Base URL for the Toolset Common instance.
	*
	* @note Does not have a trailing slash due to untrailingslashit, add it when registering each asset.
	* @since 2.0
	*/
	protected $assets_url	= '';

	protected function __construct() {
		
		if ( is_admin() ) {
			$this->assets_url = TOOLSET_COMMON_URL;
		} else {
			$this->assets_url = TOOLSET_COMMON_FRONTEND_URL;
		}
		$this->assets_url = untrailingslashit( $this->assets_url );
		
		add_action( 'init', 					array( $this, 'init' ), 99 );
		//be
		add_action( 'admin_enqueue_scripts',	array( $this, 'get_rid_of_default_scripts' ) );
		add_action( 'admin_enqueue_scripts',	array( $this, 'get_rid_of_default_styles' ) );
		//fe
		add_action( 'wp_enqueue_scripts',		array( $this, 'get_rid_of_default_scripts' ) );
		add_action( 'wp_enqueue_scripts',		array( $this, 'get_rid_of_default_styles' ) );

        add_action( 'toolset_enqueue_scripts',	array( $this,'enqueue_scripts' ), 10, 1 );
        add_action( 'toolset_enqueue_styles',	array( $this,'enqueue_styles' ), 10, 1 );
        add_action( 'toolset_localize_script',	array( $this,'localize_script' ), 10, 3 );
	}

	final public static function getInstance() {
		static $instances = array();
		$called_class = get_called_class();

		if( isset( $instances[ $called_class ] ) ) {
			return $instances[ $called_class ];
		} else {
			if( class_exists( $called_class ) ) {
				$instances[ $called_class ] = new $called_class();
				return $instances[ $called_class ];
			} else {
				// This can unfortunately happen when the get_called_class() workaround for PHP 5.2 misbehaves.
				return false;
			}
		}

	}

	public function init() {
		$this->__initialize_styles();
		$this->__initialize_scripts();
	}
	
	public function get_assets_url() {
		return $this->assets_url;
	}

	/*
	 * @return void
	 * pushes to our scripts array other scripts so we can enqueue using our methods
	 */
	public function get_rid_of_default_scripts() {
		global $wp_scripts;
		if ( is_array( $wp_scripts->registered ) ) {
			foreach ( $wp_scripts->registered as $registered ) {
				$this->scripts[ $registered->handle ] = new Toolset_Script( $registered->handle );
			}
		}
	}

	/*
	 * @return void
	 * pushes to our scripts array other scripts so we can enqueue using our methods
	 */
	public function get_rid_of_default_styles() {
		global $wp_styles;

		if ( is_array( $wp_styles->registered ) ) {
			foreach ( $wp_styles->registered as $registered ) {
				$this->styles[ $registered->handle ] = new Toolset_Style( $registered->handle );
			}
		}
	}

	protected function __initialize_styles() {
		// ----------
		// Libraries
		// ----------
		$this->styles['toolset-select2-css']						= new Toolset_Style(
																		'toolset-select2-css', 
																		$this->assets_url . '/res/lib/select2/select2.css'
																	);
        $this->styles['layouts-select2-overrides-css']				= new Toolset_Style(
																		'layouts-select2-overrides-css', 
																		$this->assets_url . '/res/lib/select2/select2-overrides.css'
																	);
        $this->styles['font-awesome']								= new Toolset_Style(
																		'font-awesome', 
																		$this->assets_url . '/res/lib/font-awesome/css/font-awesome.min.css', 
																		array(), 
																		'4.4.0', 
																		'screen'
																	);
		$this->styles['toolset-meta-html-codemirror-css']			= new Toolset_Style(
																		'toolset-meta-html-codemirror-css', 
																		$this->assets_url . '/visual-editor/res/js/codemirror/lib/codemirror.css', 
																		array(), 
																		"5.5.0"
																	);
        $this->styles['toolset-meta-html-codemirror-css-hint-css']	= new Toolset_Style(
																		'toolset-meta-html-codemirror-css-hint-css', 
																		$this->assets_url . '/visual-editor/res/js/codemirror/addon/hint/show-hint.css', 
																		array(), 
																		"5.5.0"
																	);
		$this->styles['toolset-colorbox']							= new Toolset_Style(
																		'toolset-colorbox', 
																		$this->assets_url . '/res/lib/colorbox/colorbox.css', 
																		array(), 
																		'1.4.31'
																	);
		// ----------
		// Custom styles
		// ----------
		$this->styles['toolset-select2-overrides-css']				= new Toolset_Style(
																		'toolset-select2-overrides-css', 
																		$this->assets_url . '/res/lib/select2/select2-overrides.css',
																		array( 'toolset-select2-css' )
																	);
        $this->styles['wpt-toolset-backend']						= new Toolset_Style(
																		'wpt-toolset-backend', 
																		$this->assets_url . '/toolset-forms/css/wpt-toolset-backend.css',
																		array(),
																		TOOLSET_COMMON_VERSION
																	);
        
        $this->styles['toolset-notifications-css']					= new Toolset_Style(
																		'toolset-notifications-css', 
																		$this->assets_url . '/res/css/toolset-notifications.css',
																		array(),
																		TOOLSET_COMMON_VERSION
																	);
        $this->styles['toolset-common']								= new Toolset_Style(
																		'toolset-common', 
																		$this->assets_url. '/res/css/toolset-common.css',
																		array(),
																		TOOLSET_COMMON_VERSION
																	);
        $this->styles['toolset-promotion']							= new Toolset_Style(
																		'toolset-promotion', 
																		$this->assets_url. '/res/css/toolset-promotion.css',
																		array( 'toolset-colorbox', 'onthego-admin-styles' ),
																		TOOLSET_COMMON_VERSION
																	);
		$this->styles['editor_addon_menu']							= new Toolset_Style(
																		'editor_addon_menu', 
																		$this->assets_url. '/visual-editor/res/css/pro_dropdown_2.css',
																		array(),
																		TOOLSET_COMMON_VERSION
																	);
		$this->styles['editor_addon_menu_scroll']					= new Toolset_Style(
																		'editor_addon_menu_scroll', 
																		$this->assets_url. '/visual-editor/res/css/scroll.css',
																		array(),
																		TOOLSET_COMMON_VERSION
																	);

        $this->styles['ddl-dialogs-forms-css'] = new Toolset_Style('ddl-dialogs-forms-css', $this->assets_url . '/utility/dialogs/css/dd-dialogs-forms.css', TOOLSET_VERSION);
        $this->styles['ddl-dialogs-general-css'] = new Toolset_Style('ddl-dialogs-general-css', $this->assets_url . '/utility/dialogs/css/dd-dialogs-general.css', array( 'wp-jquery-ui-dialog' ), TOOLSET_VERSION);
        $this->styles['ddl-dialogs-css'] = new Toolset_Style('ddl-dialogs-css', $this->assets_url . '/utility/dialogs/css/dd-dialogs.css', array('ddl-dialogs-general-css'), TOOLSET_VERSION );

        $this->styles['toolset-dialogs-overrides-css'] = new Toolset_Style(
                        'toolset-dialogs-overrides-css', $this->assets_url . '/res/css/toolset-dialogs.css', array(), TOOLSET_COMMON_VERSION
                );

        return apply_filters( 'toolset_add_registered_styles', $this->styles );
    }

	protected function __initialize_scripts() {
        // ----------
		// Libraries
		// ----------
		$this->scripts['headjs']											= new Toolset_Script(
																				'headjs', 
																				$this->assets_url . "/res/lib/head.min.js", 
																				array(), 
																				TOOLSET_COMMON_VERSION, 
																				true
																			);
        $this->scripts['jstorage']											= new Toolset_Script(
																				'jstorage', 
																				$this->assets_url . "/res/lib/jstorage.min.js", 
																				array(), 
																				TOOLSET_COMMON_VERSION, 
																				true
																			);

        $this->scripts['toolset-select2-compatibility']											= new Toolset_Script(
																				'toolset-select2-compatibility', 
																				$this->assets_url . "/res/js/toolset-select2-compatibility.js", 
																				array( 'jquery' ), 
																				TOOLSET_COMMON_VERSION, 
																				true
																			);

		$this->scripts['toolset_select2']											= new Toolset_Script(
																				'toolset_select2',
																				$this->assets_url . "/res/lib/select2/select2.js",
																				array( 'jquery', "toolset-select2-compatibility" ), 
																				'4.0.3', 
																				true
																			);
		$this->scripts['toolset-colorbox']									= new Toolset_Script(
																				'toolset-colorbox', 
																				$this->assets_url . "/res//lib/colorbox/jquery.colorbox-min.js", 
																				array( 'jquery' ), 
																				'1.4.31', 
																				true
																			);
                
		/**
		 * For compatibility with ACF Plugin that's not using the right handle for this module (wp-event-manager)
		 * we are using ACF handle to prevent unwanted overrides of window.wp.hooks namespace (******!)
		 */
		$this->scripts['acf-input']									= new Toolset_Script(
                                                                                'acf-input',
																				$this->assets_url . "/res/lib/events-manager/event-manager.min.js", 
																				array(), 
																				'1.0', 
																				true
																			);

        $this->scripts['toolset-event-manager'] = new Toolset_Script(
            'toolset-event-manager',
            $this->assets_url . "/res/lib/toolset-event-manager/toolset-event-manager.min.js",
            array(),
            '1.0',
            true
        );
		
		
		$this->scripts['toolset-codemirror-script']							= new Toolset_Script(
																				'toolset-codemirror-script', 
																				$this->assets_url . '/visual-editor/res/js/codemirror/lib/codemirror.js', 
																				array( 'jquery' ), 
																				"5.5.0"
																			);
        $this->scripts['toolset-meta-html-codemirror-overlay-script']		= new Toolset_Script(
																				'toolset-meta-html-codemirror-overlay-script', 
																				$this->assets_url . '/visual-editor/res/js/codemirror/addon/mode/overlay.js', 
																				array( 'toolset-codemirror-script' ), 
																				"5.5.0"
																			);
        $this->scripts['toolset-meta-html-codemirror-xml-script']			= new Toolset_Script(
																				'toolset-meta-html-codemirror-xml-script', 
																				$this->assets_url . '/visual-editor/res/js/codemirror/mode/xml/xml.js', 
																				array( 'toolset-meta-html-codemirror-overlay-script' ), 
																				"5.5.0"
																			);
        $this->scripts['toolset-meta-html-codemirror-css-script']			= new Toolset_Script(
																				'toolset-meta-html-codemirror-css-script', 
																				$this->assets_url . '/visual-editor/res/js/codemirror/mode/css/css.js', 
																				array( 'toolset-meta-html-codemirror-overlay-script' ), 
																				"5.5.0"
																			);
        $this->scripts['toolset-meta-html-codemirror-js-script']			= new Toolset_Script(
																				'toolset-meta-html-codemirror-js-script', 
																				$this->assets_url . '/visual-editor/res/js/codemirror/mode/javascript/javascript.js', 
																				array( 'toolset-meta-html-codemirror-overlay-script' ), 
																				"5.5.0"
																			);
        $this->scripts['toolset-meta-html-codemirror-utils-search']			= new Toolset_Script(
																				'toolset-meta-html-codemirror-utils-search', 
																				$this->assets_url . '/visual-editor/res/js/codemirror/addon/search/search.js', 
																				array( 'toolset-codemirror-script' ), 
																				"5.5.0" 
																			);
        $this->scripts['toolset-meta-html-codemirror-utils-search-cursor']	= new Toolset_Script(
																				'toolset-meta-html-codemirror-utils-search-cursor', 
																				$this->assets_url . '/visual-editor/res/js/codemirror/addon/search/searchcursor.js', 
																				array( 'toolset-meta-html-codemirror-utils-search' ), 
																				"5.5.0" 
																			);
        $this->scripts['toolset-meta-html-codemirror-utils-hint']			= new Toolset_Script(
																				'toolset-meta-html-codemirror-utils-hint', 
																				$this->assets_url . '/visual-editor/res/js/codemirror/addon/hint/show-hint.js', 
																				array( 'toolset-codemirror-script' ), 
																				"5.5.0" 
																			);
        $this->scripts['toolset-meta-html-codemirror-utils-hint-css']		= new Toolset_Script(
																				'toolset-meta-html-codemirror-utils-hint-css', 
																				$this->assets_url . '/visual-editor/res/js/codemirror/addon/hint/css-hint.js', 
																				array( 'toolset-meta-html-codemirror-utils-hint' ), "5.5.0" 
																			);
        $this->scripts['toolset-meta-html-codemirror-utils-panel']			= new Toolset_Script(
																				'toolset-meta-html-codemirror-utils-panel', 
																				$this->assets_url . '/visual-editor/res/js/codemirror/addon/display/panel.js', 
																				array( 'toolset-codemirror-script' ), 
																				"5.5.0" 
																			);
		// ----------
		// Custom scripts
		// ----------
        $this->scripts['toolset-utils']										= new Toolset_Script(
																				'toolset-utils', 
																				$this->assets_url . "/utility/js/utils.js", 
																				array( 'jquery', 'underscore', 'backbone', 'jquery-ui-core','jquery-ui-widget', 'jquery-ui-dialog' ),
																				'1.2.2', 
																				true
																			);
        $this->scripts['icl_editor-script']									= new Toolset_Script(
																				'icl_editor-script', 
																				$this->assets_url . '/visual-editor/res/js/icl_editor_addon_plugin.js', 
																				array(  'jquery', 'quicktags', 'wplink', 'toolset-codemirror-script' ), 
																				TOOLSET_COMMON_VERSION
																			);
		$this->scripts['icl_media-manager-js']								= new Toolset_Script(
																				'icl_media-manager-js', 
																				$this->assets_url . '/visual-editor/res/js/icl_media_manager.js', 
																				array(  'icl_editor-script' ), 
																				TOOLSET_COMMON_VERSION
																			);
		$this->scripts['wptoolset-parser']									= new Toolset_Script(
																				'wptoolset-parser', 
																				$this->assets_url . '/res/js/toolset-parser.js', 
																				array( 'jquery' ), 
																				TOOLSET_COMMON_VERSION,
																				true
																			);
		$this->scripts['toolset-promotion']									= new Toolset_Script(
																				'toolset-promotion', 
																				$this->assets_url . "/res/js/toolset-promotion.js", 
																				array( 'underscore', 'toolset-colorbox' ),
																				TOOLSET_COMMON_VERSION, 
																				true
																			);
		$this->scripts['toolset-settings']									= new Toolset_Script(
																				'toolset-settings', 
																				$this->assets_url . "/res/js/toolset-settings.js", 
																				array( 'jquery', 'underscore', 'toolset-utils' ),
																				TOOLSET_COMMON_VERSION, 
																				true
																			);
		$this->scripts['toolset-export-import']								= new Toolset_Script(
																				'toolset-export-import', 
																				$this->assets_url . "/res/js/toolset-export-import.js", 
																				array( 'jquery', 'underscore' ),
																				TOOLSET_COMMON_VERSION, 
																				true
																			);

		$this->localize_script(
            'toolset-utils',
            'toolset_utils_texts',
            array(
                'wpv_dont_show_it_again'			=> __( "Got it! Don't show this message again", 'wpv-views'),
				'wpv_close'							=> __( 'Close', 'wpv-views')
            )
        );
		
		$this->localize_script(
            'icl_editor-script',
            'icl_editor_localization_texts',
            array(
                'wpv_insert_conditional_shortcode'	=> __( 'Insert conditional shortcode', 'wpv-views' ),
                'wpv_conditional_button'			=> __( 'conditional output', 'wpv-views' ),
                'wpv_editor_callback_nonce'			=> wp_create_nonce( 'wpv_editor_callback' )
            )
        );
		
		$this->localize_script(
            'icl_media-manager-js',
            'icl_media_manager',
            array(
                'only_img_allowed_here'				=> __( "You can only use an image file here", 'wpv-views' )
            )
        );
		
		$this->localize_script(
            'toolset-settings',
            'toolset_settings_texts',
            array(
                'autosave_saving'					=> '<i class="fa fa-refresh fa-spin"></i>' . __( 'Saving...', 'wpv-views' ),
				'autosave_saved'					=> '<i class="fa fa-check"></i>' . __( 'All changes saved', 'wpv-views' ),
				'autosave_failed'					=> '<i class="fa fa-exclamation-triangle"></i>' . __( 'Saving failed. Please reload the page and try again.', 'wpv-views' )
            )
        );

        return apply_filters( 'toolset_add_registered_script', $this->scripts );
    }

	public function enqueue_scripts( $handles ) {
		if ( is_array( $handles ) ) {
			foreach ( $handles as $handle ) {
				if ( isset( $this->scripts[ $handle ] ) ) {
					$this->scripts[ $handle ]->enqueue();
				}
			}
		} else if ( is_string( $handles ) ) {
			if ( isset( $this->scripts[ $handles ] ) ) {
				$this->scripts[ $handles ]->enqueue();
			}
		}
	}

	public function enqueue_styles( $handles ) {
		if ( is_array( $handles ) ) {
			foreach ( $handles as $handle ) {
				if ( isset( $this->styles[ $handle ] ) ) {
					$this->styles[ $handle ]->enqueue();
				}
			}
		} else if ( is_string( $handles ) ) {
			if ( isset( $this->styles[ $handles] ) ) {
				$this->styles[ $handles ]->enqueue();
			}
		}
	}

	public function deregister_scripts( $handles ) {
		if ( is_array( $handles ) ) {
			foreach ( $handles as $handle ) {
				if ( isset( $this->scripts[ $handle ] ) ) {
					$this->scripts[ $handle ]->deregister();
					unset( $this->scripts[ $handle ] );
				}
			}
		} else if ( is_string( $handles ) ) {
			if ( isset( $this->scripts[ $handles ] ) ) {
				$this->scripts[ $handles ]->deregister();
				unset( $this->scripts[ $handles ] );
			}
		}
	}

	public function deregister_styles( $handles ) {
		if ( is_array( $handles ) ) {
			foreach ( $handles as $handle ) {
				if ( isset( $this->styles[ $handle ] ) ) {
					$this->styles[ $handle ]->deregister();
					unset( $this->styles[ $handle ] );
				}
			}
		} else if ( is_string( $handles ) ) {
			if ( isset( $this->styles[ $handles ] ) ) {
				$this->styles[ $handles ]->deregister();
				unset( $this->styles[ $handles ] );
			}
		}
	}

	public function register_script( $handle, $path = '', $deps = array(), $ver = false, $in_footer = false ) {
		if ( ! isset( $this->scripts[ $handle ] ) ) {
			$this->scripts[ $handle ] = new Toolset_Script( $handle, $path, $deps, $ver, $in_footer );
		}
	}

	public function register_style( $handle, $path = '', $deps = array(), $ver = false, $media = 'screen' ) {
		if ( ! isset( $this->styles[ $handle ] ) ) {
			$this->scripts[ $handle ] = new Toolset_Style( $handle, $path, $deps, $ver, $media );
		}
	}

	public function localize_script( $handle, $object, $args ) {
		if ( isset( $this->scripts[ $handle ] ) ) {
			$this->scripts[ $handle ]->localize( $object, $args );
		}
	}
}
