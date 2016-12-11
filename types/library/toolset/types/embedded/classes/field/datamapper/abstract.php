<?php

/**
 * Data mapper object for translating field value between what comes from $_POST (toolset-forms, usually), what is used
 * in Types internally and what is stored to database.
 *
 * These classes should (eventually) define all three formats for all the field types.
 *
 * The correct data mapper for a field is chosen in WPCF_Field_Definition::get_data_mapper().
 *
 * Note that data mappers do not deal with displaying field values (except in form inputs). To properly display a field,
 * use one of existing renderers (WPCF_Field_Renderer_Factory) or create new one.
 *
 * @since 1.9
 */
abstract class WPCF_Field_DataMapper_Abstract {

	protected $field_definition;

	public function __construct( $field_definition ) {
		if( ! $field_definition instanceof WPCF_Field_Definition_Abstract ) {
			throw new InvalidArgumentException( 'Field definition instance expected.' );
		}

		$this->field_definition = $field_definition;
	}


	public function database_to_intermediate( $value ) {
		return $value;
	}

	public function intermediate_to_database( $value ) {
		return $value;
	}


	/**
	 * @param mixed $post_value Field value as obtained from the POST data.
	 * @param array $form_data Complete form data.
	 *
	 * @return mixed
	 */
	public function post_to_intermediate( $post_value, /** @noinspection PhpUnusedParameterInspection */ $form_data ) {
		return $post_value;
	}
}
