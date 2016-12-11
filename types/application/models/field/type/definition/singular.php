<?php

/**
 * Field type definition for single-value only fields.
 * 
 * @since 2.0
 */
class Types_Field_Type_Definition_Singular extends Types_Field_Type_Definition {

	/**
	 * @inheritdoc
	 * @return bool
	 */
	public function can_be_repetitive() {
		return false;
	}
	
}