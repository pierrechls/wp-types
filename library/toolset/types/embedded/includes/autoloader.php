<?php

/**
 * Autoloader for Types classes.
 *
 * See:
 * @link https://git.onthegosystems.com/toolset/layouts/wikis/layouts-theme-integration#wpddl_theme_integration_autoloader
 *
 * Warning: Please be careful when using the autoloader for older code that is supposed to be loaded at certain time.
 * The autoloader might grab some classses too early.
 *
 * @since 1.9
 * @deprecated This autoloader is to be used only for legacy code. Since 2.0, use Toolset_Autoloader instead.
 */
final class WPCF_Autoloader {

	private static $instance;

	protected $paths = array();


	protected $prefixes = array();


	/**
	 * For production version of Types we use a class map
	 * you can disable using the classmap by simply deleting /autoload_classmap.php
	 *
	 * Note: Not using the classmap will lower the performance significant
	 *
	 * @var bool|array
	 * @since 2.1
	 */
	private $classmap = false;


	protected function __construct() {
		spl_autoload_register( array( $this, 'autoload' ) );

		if( file_exists( WPCF_ABSPATH . '/autoload_classmap.php' ) ) {
			$this->classmap = include( WPCF_ABSPATH . '/autoload_classmap.php' );
		}
	}


	public static function get_instance() {
		if( self::$instance === null )
			self::$instance = new self;

		return self::$instance;
	}


	/**
	 * Add base path to search for class files.
	 *
	 * @param string $path
	 * @return bool True if path was added
	 */
	public function add_path( $path ) {

		// check if path is readable
		if( is_readable( $path ) ) {
			array_push( $this->paths, $path );
			return true;
		}

		return false;
	}


	/**
	 * Add multiple base paths.
	 *
	 * @param array $paths
	 */
	public function add_paths( $paths ) {
		// run this->addPath for each value
		foreach( $paths as $path ) {
			$this->add_path( $path );
		}
	}


	public function get_paths() {
		return $this->paths;
	}


	public function add_prefix( $prefix ) {
		array_push( $this->prefixes, $prefix );

		// We assume that most specific (longest) prefixes will have higher probability of a match.
		// This is useful when one prefix is substring of another.
		rsort( $this->prefixes );

		return $this;
	}


	public function get_prefixes() {
		return $this->prefixes;
	}


	public function autoload( $class ) {
		// use class map if defined
		if( $this->classmap ) {
			if( isset( $this->classmap[$class] ) && file_exists( $this->classmap[$class] ) ) {
				require_once( $this->classmap[$class] );
				return true;
			}

			return false;
		}

		foreach( $this->prefixes as $prefix ) {

			// Will be equal to $class if no replacement happens.
			$class_without_prefix = preg_replace( '#^'.$prefix.'_#', '', $class );

			if( $class != $class_without_prefix ) {

				$result = $this->try_autoload_without_prefix( $class, $class_without_prefix );

				// false means we should try with other prefixes
				if( false !== $result ) {
					return $result;
				}
			}
		}

		return false;
	}


	/**
	 * Try to load class after matching it's prefix.
	 *
	 * @param string $full_class_name Full name of the class.
	 * @param string $class_name_without_prefix Name of the class without the registered prefix.
	 * @return bool|mixed include_once() result or false if the file was not found.
	 */
	private function try_autoload_without_prefix( $full_class_name, $class_name_without_prefix ) {

		// explode class by _
		$explode_class = explode( '_' , $class_name_without_prefix );

		// get class filename
		$class_filename = array_pop( $explode_class );
		$class_filename = strtolower( $class_filename ) . '.php';

		// get class path
		$class_path = '';
		foreach( $explode_class as $path ) {
			$class_path .= strtolower( $path ) . '/';
		}

		$file = $class_path . $class_filename;

		// check for file in path
		foreach( $this->get_paths() as $path ) {

			$next_filename = $file;

			while( true ) {
				$candidate_filename = $next_filename;

				$candidate_path = $path . '/' . $candidate_filename;

				$result = $this->try_load_file( $candidate_path, $full_class_name );
				if( false !== $result ) {
					return $result;
				}

				// Replace the last slash by underscore.
				// This allows to use underscores in class filename instead of subfolders
				$next_filename = preg_replace( '/(\/(?!.*\/))/', '_', $candidate_filename );

				// If there was no change, we have tried all possibilities for this filename.
				if( $next_filename == $candidate_filename ) {
					break;
				}

			}

		}

		// Last attempt for legacy classes.
		return $this->try_load_legacy_class( $full_class_name );
	}


	/**
	 * Try loading a legacy class.
	 *
	 * Uses several mechanisms to find a legacy class, for details see other try_load_* methods.
	 *
	 * @param string $full_class_name
	 * @return bool|mixed include_once() result or false if the file was not found. Note that under some circumstances
	 *     you may get a false negative here.
	 */
	private function try_load_legacy_class( $full_class_name ) {
		$loading_result = $this->try_load_generic_legacy_class( $full_class_name );
		if( false == $loading_result ) {
			$loading_result = $this->try_load_toolset_forms_class( $full_class_name );
		}
		return $loading_result;
	}


	/**
	 * Try loading a toolset-forms class.
	 *
	 * Currently it recongnizes WPToolset_* and WPToolset_Field_* classes if they're named in
	 * the class.*.php pattern and placed in toolset-forms class directory.
	 *
	 * @param $full_class_name
	 * @return bool|mixed
	 */
	private function try_load_toolset_forms_class( $full_class_name ) {
		// Bail if we don't have toolset-forms.
		if( ! defined( 'WPTOOLSET_FORMS_ABSPATH' ) ) {
			return false;
		}

		// Handle classes named WPToolset_Someting_Something*
		$explode_class = explode( '_' , $full_class_name );
		if( count( $explode_class ) >= 2 && 'WPToolset' == $explode_class[0] ) {

			// We will search for Something_Something*
			array_shift( $explode_class );

			// If we have Field_Something*, we will omit the "Field" part
			if( 'Field' == $explode_class[0] && count( $explode_class ) >= 2 ) {
				array_shift( $explode_class );
			}

			$filename = 'class.' . strtolower( implode( '.', $explode_class ) ) . '.php';
			$candidate_path = WPTOOLSET_FORMS_ABSPATH . '/classes/' . $filename;

			return $this->try_load_file( $candidate_path, $full_class_name );

		} else {
			return false;
		}
	}


	/**
	 * Load a generic Types legacy class.
	 *
	 * Works for classes placed directly inside one of the base paths, whose names translate into filenames like this:
	 * "Types_Some_Class" => "class.types.some.class.php". Disregards the set of defined prefixes - it would work for
	 * any class.
	 *
	 * @param $full_class_name
	 * @return bool|mixed
	 */
	private function try_load_generic_legacy_class( $full_class_name ) {
		$explode_class = explode( '_' , $full_class_name );
		$filename = 'class.' . strtolower( implode( '.', $explode_class ) ) . '.php';

		foreach( $this->get_paths() as $path ) {
			$candidate_path = $path . '/' . $filename;
			$result = $this->try_load_file( $candidate_path, $full_class_name );
			if( false !== $result ) {
				return $result;
			}
		}

		// Class not found.
		return false;
	}


	/**
	 * Try loading a file and check if given class exists afterwards.
	 *
	 * @param string $full_path Absolute path to the file.
	 * @param string $full_class_name Full class name to be checked.
	 *
	 * @return bool|mixed include_once() result or false if the class couldn't be loaded. It may give false negatives.
	 */
	private function try_load_file( $full_path, $full_class_name ) {
		if( is_file( $full_path ) ) {
			/** @noinspection PhpIncludeInspection */
			$result = include_once( $full_path ) ;

			// Do not stop trying if we load the file but it doesn't contain the requested class.
			if( class_exists( $full_class_name ) ) {
				return $result;
			}
		}

		return false;
	}

}