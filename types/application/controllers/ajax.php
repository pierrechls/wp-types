<?php

/**
 * Main AJAX call controller for Types.
 *
 * When DOING_AJAX, you need to run initialize() to register the callbacks, only creating an instance will not be enough.
 *
 * When implementing AJAX actions, please follow these rules:
 *
 * 1.  All AJAX action names are automatically prefixed with 'wp_ajax_types_'. Only lowercase characters and underscores
 *     can be used.
 * 2.  Action names (without a prefix) should be defined as constants, and be part of the Types_Ajax::$callbacks array.
 * 3.  For each action, there should be a dedicated class implementing the Types_Ajax_Handler_Interface. Name of the class
 *     must be Types_Ajax_Handler_{%capitalized_action_name}. So for example, for a hook to
 *     'wp_ajax_types_field_control_action' you need to create a class 'Types_Ajax_Handler_Field_Control_Action'.
 * 4.  All callbacks must use the ajax_begin() and ajax_finish() methods.
 *
 * @since 2.0
 */
final class Types_Ajax {

	// Action names
	const CALLBACK_FIELD_CONTROL_ACTION = 'field_control_action';
	const CALLBACK_CHECK_SLUG_CONFLICTS = 'check_slug_conflicts';
	const CALLBACK_SETTINGS_ACTION      = 'settings_action';

	
	/** Prefix for the callback method name */
	const CALLBACK_PREFIX = 'callback_';

	/** Prefix for the handler class name */
	const HANDLER_CLASS_PREFIX = 'Types_Ajax_Handler_';

	const DELIMITER = '_';
	
	
	private static $instance;

	
	public static function get_instance() {
		if( null == self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	
	public static function initialize() {
		$instance = self::get_instance();

		$instance->register_callbacks();
		$instance->additional_ajax_init();
	}


	private function __clone() { }


	private function __construct() { }


	private static $callbacks = array(
		self::CALLBACK_FIELD_CONTROL_ACTION,
		self::CALLBACK_CHECK_SLUG_CONFLICTS,
		self::CALLBACK_SETTINGS_ACTION,
	);


	private $callbacks_registered = false;


	/**
	 * Register all callbacks. 
	 * 
	 * Each callback is registered as a "types_{$callback}" action and needs to have a "callback_{$callback_name}"
	 * method in this class.
	 * 
	 * @since 2.0
	 */
	private function register_callbacks() {

		if( $this->callbacks_registered ) {
			return;
		}

		foreach( self::$callbacks as $callback_name ) {
			add_action( 'wp_ajax_types_' . $callback_name, array( $this, self::CALLBACK_PREFIX . $callback_name ) );
		}

		$this->callbacks_registered = true;

	}
	
	
	public function get_action_js_name( $action ) {
		return 'types_' . $action;
	}


	/**
	 * Handle a call to undefined method on this class, hopefully an AJAX call.
	 *
	 * @param string $name Method name.
	 * @param array $parameters Method parameters.
	 * @since 2.1
	 */
	public function __call( $name, $parameters ) {
		// Check for the callback prefix in the method name
		$name_parts = explode( self::DELIMITER, $name );
		if( 0 !== strcmp( $name_parts[0] . self::DELIMITER, self::CALLBACK_PREFIX ) ) {
			// Not a callback, resign.
			return;
		}

		// Deduct the handler class name from the callback name
		unset( $name_parts[0] );
		$class_name = implode( self::DELIMITER, $name_parts );
		$class_name = strtolower( $class_name );
		$class_name = mb_convert_case( $class_name, MB_CASE_TITLE );
		$class_name = self::HANDLER_CLASS_PREFIX . $class_name;

		// Obtain an instance of the handler class.
		try {
			/** @var Types_Ajax_Handler_Interface $handler */
			$handler = new $class_name( $this );
		} catch( Exception $e ) {
			// The handler class could not have been instantiated, resign.
			return;
		}

		// Success
		$handler->process_call( $parameters );
	}



	/**
	 * Perform basic authentication check.
	 *
	 * Check user capability and nonce. Dies with an error message (wp_json_error() by default) if the authentization
	 * is not successful.
	 *
	 * @param array $args Arguments (
	 *     @type string $nonce Name of the nonce that should be verified. Mandatory
	 *     @type string $nonce_parameter Name of the parameter containing nonce value.
	 *         Optional, defaults to "wpnonce".
	 *     @type string $parameter_source Determines where the function should look for the nonce parameter.
	 *         Allowed values are 'get' and 'post'. Optional, defaults to 'post'.
	 *     @type string $capability_needed Capability that user has to have in order to pass the check.
	 *         Optional, default is "manage_options".
	 *     @type string $type_of_death How to indicate failure:
	 *         - 'die': Call wp_json_error with array( 'type' => 'capability'|'nonce', 'message' => $error_message )
	 *         - 'return': Do not die, just return the error array as above.
	 *         Optional, default is 'die'.
	 *     )
	 *
	 * @return array|void
	 *
	 * @since 2.0
	 */
	private function ajax_authenticate( $args = array() ) {
		// Read arguments
		$type_of_death = wpcf_getarr( $args, 'type_of_death', 'die', array( 'die', 'return' ) );
		$nonce_name = wpcf_getarr( $args, 'nonce' );
		$nonce_parameter = wpcf_getarr( $args, 'nonce_parameter', 'wpnonce' );
		$capability_needed = wpcf_getarr( $args, 'capability_needed', 'manage_options' );
		$parameter_source_name = wpcf_getarr( $args, 'parameter_source', 'post', array( 'get', 'post' ) );
		$parameter_source = ( $parameter_source_name == 'get' ) ? $_GET : $_POST;

		$is_error = false;
		$error_message = null;
		$error_type = null;

		// Check permissions
		if ( ! current_user_can( $capability_needed ) ) {
			$error_message = __( 'You do not have permissions for that.', 'wpv-views' );
			$error_type = 'capability';
			$is_error = true;
		}

		// Check nonce
		if ( !$is_error && !wp_verify_nonce( wpcf_getarr( $parameter_source, $nonce_parameter, '' ), $nonce_name ) ) {
			$error_message = __( 'Your security credentials have expired. Please reload the page to get new ones.', 'wpv-views' );
			$error_type = 'nonce';
			$is_error = true;
		}

		if( $is_error ) {
			$error_description = array( 'type' => $error_type, 'message' => $error_message );
			switch( $type_of_death ) {

				case 'die':
					wp_send_json_error( $error_description );
					break;

				case 'return':
				default:
					return $error_description;
			}
		}

		return true;
	}


	/**
	 * Begin an AJAX call handling.
	 * 
	 * To be extended in the future.
	 * 
	 * @param array $args See ajax_authenticate for details
	 * @return array|void
	 * @since 2.0
	 */
	public function ajax_begin( $args ) {
		return $this->ajax_authenticate( $args );
	}


	/**
	 * Complete an AJAX call handling.
	 * 
	 * Sends a success/error response in a standard way.
	 * 
	 * To be extended in the future.
	 * 
	 * @param array $response Custom response data
	 * @param bool $is_success
	 * @since 2.0
	 */
	public function ajax_finish( $response, $is_success = true ) {
		if( $is_success ) {
			wp_send_json_success( $response );
		} else {
			wp_send_json_error( $response );
		}
	}


	/**
	 * Handles all initialization of except AJAX callbacks itself that is needed when
	 * we're DOING_AJAX.
	 *
	 * Since this is executed on every AJAX call, make sure it's as lightweight as possible.
	 *
	 * @since 2.1
	 */
	private function additional_ajax_init() {

		// On the Add Term page, we need to initialize the page controller WPCF_GUI_Term_Field_Editing
		// so that it saves term fields (if there are any).
		add_action( 'create_term', array( $this, 'prepare_for_term_creation' ) );

		add_action( 'updated_user_meta', array( $this, 'capture_columnshidden_update' ), 10, 4 );
	}
	

	/**
	 * On the Add Term page, we need to initialize the page controller WPCF_GUI_Term_Field_Editing
	 * so that it saves term fields (if there are any).
	 *
	 * @since 2.1
	 */
	public function prepare_for_term_creation() {

		// Takes care of the rest, mainly we're interested about the create_{$taxonomy} action which follows
		// immediately after create_term.
		//
		// On actions fired on the Add Term page, the action POST variable is allways add-tag and the screen is set
		// to edit-{$taxonomy}. When creating the term on the post edit page, for example, the screen is not set. We use
		// this to further limit the resource wasting. However, initializing the controller even if it's not supposed to
		// will not lead to any errors - it gives up gracefully.
		$action = wpcf_getpost( 'action' );
		$screen = wpcf_getpost( 'screen', null );
		if( 'add-tag' == $action && null !== $screen ) {
			WPCF_GUI_Term_Field_Editing::initialize();
		}

	}


	/**
	 * When updating screen options with hidden listing columns, we may need to store additional data.
	 *
	 * See WPCF_GUI_Term_Field_Editing::maybe_disable_column_autohiding() for details.
	 *
	 * @param mixed $meta_id Ignored.
	 * @param mixed $object_id Ignored.
	 * @param string $meta_key Meta key.
	 * @param mixed $_meta_value Meta value. We expect it to be an array.
	 * @since 2.1
	 */
	public function capture_columnshidden_update(
		/** @noinspection PhpUnusedParameterInspection */ $meta_id, $object_id, $meta_key, $_meta_value )
	{
		// We're looking for a meta_key that looks like "manage{$page_name}columnshidden".
		$txt_columnshidden = 'columnshidden';
		$is_columnshidden_option = ( 0 == strcmp( $txt_columnshidden, substr( $meta_key, strlen( $txt_columnshidden ) * -1 ) ) );

		if( $is_columnshidden_option ) {

			// Extract the page name from the meta_key
			$strip_begin = strlen( 'manage' );
			$strip_end = strlen( $txt_columnshidden );
			$page_name = substr( $meta_key, $strip_begin, strlen( $meta_key ) - ( $strip_begin + $strip_end ) );

			// Determine if we're editing a taxonomy
			$txt_edit = 'edit-';
			$txt_edit_len = strlen( $txt_edit );
			$is_tax_edit_page = ( 0 == strcmp( $txt_edit, substr( $page_name, 0, $txt_edit_len ) ) );

			// This is not 100% certain but attempting to handle a taxonomy that doesn't exist does no harm.
			if( $is_tax_edit_page ) {

				// Now we know that we need to perform the extra action.
				$taxonomy_name = substr( $page_name, $txt_edit_len );
				$edit_term_page_extension = WPCF_GUI_Term_Field_Editing::get_instance();
				$edit_term_page_extension->maybe_disable_column_autohiding( $taxonomy_name, $_meta_value, $page_name );
			}
		}
	}
}