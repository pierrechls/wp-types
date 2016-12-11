<?php

/**
 *
 *
 */

require 'abstract.field.php';

abstract class FieldFactory extends FieldAbstract
{
    protected $_nameField, $_data, $_value, $_use_bootstrap;

    function __construct($data, $global_name_field, $value)
    {
        $this->_nameField = $global_name_field;
        $this->_data = $data;
        $this->_value = $value;

        $this->init();
    }

    public function init()
    {
        $cred_cred_settings = get_option( 'cred_cred_settings' );
        $this->_use_bootstrap = is_array($cred_cred_settings) && array_key_exists( 'use_bootstrap', $cred_cred_settings ) && $cred_cred_settings['use_bootstrap'];
        $this->set_placeholder_as_attribute();
    }

    public function set_placeholder_as_attribute()
    {
        if ( !isset($this->_data['attribute']) ) {
            $this->_data['attribute'] = array();
        }
        if ( isset($this->_data['placeholder']) && !empty($this->_data['placeholder'])) {
            $this->_data['attribute']['placeholder'] = htmlentities(stripcslashes($this->_data['placeholder']));
        }
    }

    public function set_metaform($metaform)
    {
        $this->_metaform = $metaform;
    }

    public function get_metaform()
    {
        return $this->_metaform;
    }

    public function get_data()
    {
        return $this->data;
    }

    public function set_data($data)
    {
        $this->data = $data;
    }

    public function set_nameField($nameField)
    {
        $this->_nameField = $nameField;
    }

    public function get_nameField()
    {
        return $this->_nameField;
    }

    public function getId()
    {
        return $this->_data['id'];
    }

    public function getType()
    {
        return $this->_data['type'];
    }

    public function getValue()
    {
        global $post;
        $value = $this->_value;

	    $value = $this->maybe_apply_default_value( $value );

        $value = apply_filters( 'wpcf_fields_value_get', $value, $post );
        if ( array_key_exists('slug', $this->_data ) ) {
            $value = apply_filters( 'wpcf_fields_slug_' . $this->_data['slug'] . '_value_get', $value, $post );
        }
        $value = apply_filters( 'wpcf_fields_type_' . $this->_data['type'] . '_value_get', $value, $post );
        return $value;
    }


	/**
	 * Determine whether the actual field value needs to be replaced by a default one.
	 *
	 * @param mixed $actual_value
	 * @return string|mixed The actual value or the default one if the actual one is empty.
	 * @since 2.2.3
	 */
    private function maybe_apply_default_value( $actual_value ) {

    	// empty( "0" ) == true but we don't want that
    	$is_default_value_needed = ( empty( $actual_value ) && ! is_numeric( $actual_value ) );

	    if( $is_default_value_needed ) {

		    $default_value = toolset_getarr( $this->_data, 'user_default_value', null );

		    // Again, handle "0".
		    $is_default_value_defined = ( ( ! empty( $default_value ) ) || is_numeric( $default_value ) );

		    if( $is_default_value_defined ) {
		    	return stripcslashes( $default_value );
		    }
	    }

	    return $actual_value;
    }


    public function getTitle($_title = false)
    {
        if ( $_title && empty($this->_data['title']) && isset($this->_data['_title']) ) {
            return $this->_data['_title'];
        }
        return $this->_data['title'];
    }

    public function getDescription()
    {
        return wpautop( wp_filter_post_kses( $this->_data['description'] ) );
    }

    public function getName()
    {
        return $this->_data['name'];
    }

    public function getData()
    {
        return $this->_data;
    }

    public function getValidationData()
    {
        return !empty( $this->_data['validation'] ) ? $this->_data['validation'] : array();
    }

    public function setValidationData($validation)
    {
        $this->_data['validation'] = $validation;
    }

    public function getSettings()
    {
        return isset( $this->_settings ) ? $this->_settings : array();
    }

    public function isRepetitive()
    {
        return (bool)$this->_data['repetitive'];
    }

    public function getAttr() {
        if ( array_key_exists( 'attribute', $this->_data ) ) {
            /**
             * Change field attributes
             *
             * This filter allow to change field attributes.
             *
             * @since x.x.x
             *
             * @param array $attributes array with field attributes
             * @param object $field current field
             */
            return apply_filters( 'toolset_field_factory_get_attributes', $this->_data['attribute'], $this);
        }
        return apply_filters( 'toolset_field_factory_get_attributes', array(), $this);
    }

    public function getWPMLAction()
    {
        if ( array_key_exists( 'wpml_action', $this->_data ) ) {
            return $this->_data['wpml_action'];
        }
        return 0;
    }

    public static function registerScripts() {}
    public static function registerStyles() {}
    public static function addFilters() {}
    public static function addActions() {}

    public function enqueueScripts() {}
    public function enqueueStyles() {}
    public function metaform() {}
    public function editform() {}
    public function mediaEditor() {}
}
