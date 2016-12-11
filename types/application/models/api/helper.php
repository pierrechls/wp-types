<?php


class Types_Api_Helper {

	/**
	 * Returns all ids of field groups assigned to the given post type.
	 * This is used by CRED (auto complete form)
	 *
	 * @todo write tests and refactor code.
	 * @param $post_type
	 *
	 * @return array
	 */
	public static function get_field_group_ids_by_post_type( $value, $post_type ) {
		global $wpdb;
		$sql = 'SELECT post_id FROM ' .$wpdb->postmeta . '
                    WHERE meta_key="_wp_types_group_post_types"
                    AND (meta_value LIKE "%'.$post_type.'%" OR meta_value="all" OR meta_value REGEXP "^[,]+$")
                    ORDER BY post_id ASC';
		$post_ids = $wpdb->get_col( $sql );

		return $post_ids;
	}
}