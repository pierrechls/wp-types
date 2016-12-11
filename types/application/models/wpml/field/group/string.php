<?php

abstract class Types_Wpml_Field_Group_String implements Types_Wpml_Interface {

	const CONTEXT                   = 'plugin Types';
	const TRANSLATE_FILTER          = 'wpml_translate_single_string';

	/**
	 * @var Types_Field_Group
	 */
	protected $group;

	/**
	 * String to translate
	 * @var string
	 */
	protected $string_to_translate;

	/**
	 * Types_Wpml_Field_Group_String constructor.
	 *
	 * @param Types_Field_Group $group
	 */
	public function __construct( Types_Field_Group $group ) {
		$this->group = $group;
	}

	/**
	 * The pattern of the string name in icl_strings
	 *  - [name]: group %s name
	 *  - [description]: group %s description
	 *
	 * @return string
	 */
	abstract protected function get_db_pattern();


	/**
	 * Returns the string which should be translated
	 *
	 * @return string
	 */
	protected function get_string_to_translate() {
		return $this->string_to_translate;
	}


	/**
	 * Get the db identifier (uses slug of group)
	 *
	 * @return string
	 */
	protected function get_db_identifier() {
		return sprintf( $this->get_db_pattern(), $this->group->get_slug() );
	}

	/**
	 * Get the db identifier (uses id of group)
	 *
	 * @return string
	 */
	protected function get_db_identifier_legacy() {
		return sprintf( $this->get_db_pattern(), $this->group->get_id() );
	}


	/**
	 * Translate name of the group
	 *
	 * @return string
	 */
	public function translate() {
		if ( empty( $this->string_to_translate ) || ! is_string( $this->string_to_translate ) ) {
			return $this->string_to_translate;
		}

		// trying the new pattern, which uses the GROUP NAME
		$translated_string = $this->get_translation(
			$this->string_to_translate,
			$this->get_db_identifier()
		);

		if( $translated_string && $translated_string != $this->string_to_translate )
			return $translated_string;

		// nothing found yet, try the old pattern for group storage
		return $this->translate_legacy();
	}

	/**
	 * Returning the legacy pattern which is using the GROUP ID
	 *
	 * @return mixed|void
	 */
	private function translate_legacy() {
		$translated_string = $this->get_translation(
			$this->string_to_translate,
			$this->get_db_identifier_legacy()
		);

		// no translation found
		if( ! $translated_string )
			return $this->string_to_translate;

		// update pattern of name field in "icl_strings" table
		// to use name of group instead of id
		$this->update_db_identifier();

		// return translated string
		return $translated_string;
	}

	/**
	 * Get translation of string
	 *
	 * @param $string
	 * @param $field_id
	 *
	 * @return string|false
	 */
	private function get_translation( $string, $field_id ) {

		// check if translation exists
		$is_registered = apply_filters(
			'wpml_string_id',
			null,
			array(
				'context' => self::CONTEXT,
				'name'    => $field_id
			)
		);

		if( $is_registered === null )
			return false;

		// string is registered, return translation
		return apply_filters(
			self::TRANSLATE_FILTER,
			$string,
			self::CONTEXT,
			$field_id
		);
	}

	/**
	 * Update the identifier on db table "{prefix}_icl_strings" to use the "name" of a group instead of the "id"
	 */
	private function update_db_identifier() {
		global $wpdb;
		
		$wpdb->query(
			$wpdb->prepare(
				"UPDATE {$wpdb->prefix}icl_strings 
				 SET name = %s, 
				     domain_name_context_md5 = md5( CONCAT( context, name, gettext_context ) )
                 WHERE name = %s",
				$this->get_db_identifier(),
				$this->get_db_identifier_legacy()
			)
		);
	}

	/**
	 * Say Hello to WPML
	 */
	public function register( $slug_update = false ) {
		// abort if needed function not exists
		if( ! function_exists( 'icl_register_string' ) )
			return;

		// update string identifier
		if( $slug_update ) {
			global $wpdb;

			$wpdb->query(
				$wpdb->prepare(
					"UPDATE {$wpdb->prefix}icl_strings 
				 SET name = %s
                 WHERE name = %s",
					$this->get_db_identifier(),
					sprintf( $this->get_db_pattern(), $slug_update )
				)
			);
		}

		// register/update string
		icl_register_string(
			self::CONTEXT,
			$this->get_db_identifier(),
			$this->get_string_to_translate()
		);
	}
}