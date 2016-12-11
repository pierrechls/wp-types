<?php
/*
 * Fields and groups functions
 *
 *
 */
require_once WPCF_EMBEDDED_INC_ABSPATH . '/fields.php';

/**
 * Gets taxonomies supported by specific group.
 *
 * @global object $wpdb
 *
 * @param type $group_id
 * @return type
 */
function wpcf_admin_get_taxonomies_by_group( $group_id )
{
    /**
     * sanitized $group_id
     */
    $group_id = intval($group_id);
    if ( empty($group_id) ) {
        return array();
    }
    global $wpdb;
    $terms = get_post_meta( $group_id, '_wp_types_group_terms', true );
    if ( $terms == 'all' ) {
        return array();
    }
    $terms = explode( ',', trim( $terms, ',' ) );
    $taxonomies = array();
    if ( !empty( $terms ) ) {
        foreach ( $terms as $term ) {
            $term = $wpdb->get_row(
                $wpdb->prepare(
                    "SELECT tt.term_taxonomy_id, tt.taxonomy, t.term_id, t.slug, t.name FROM {$wpdb->term_taxonomy} tt JOIN {$wpdb->terms} t WHERE t.term_id = tt.term_id AND tt.term_taxonomy_id = %d",
                    $term
                ),
                ARRAY_A
            );
            if ( !empty( $term ) ) {
                $taxonomies[$term['taxonomy']][$term['term_taxonomy_id']] = $term;
            }
        }
    } else {
        return array();
    }
    return $taxonomies;
}

/**
 * Gets templates supported by specific group.
 *
 * @param type $group_id
 * @return type
 */
function wpcf_admin_get_templates_by_group( $group_id )
{
    /**
     * sanitized $group_id
     */
    $group_id = intval($group_id);
    if ( empty($group_id) ) {
        return array();
    }
    $data = get_post_meta( $group_id, '_wp_types_group_templates', true );
    if ( $data == 'all' ) {
        return array();
    }
    $data = explode( ',', trim( $data, ',' ) );
    $templates = get_page_templates();
    $templates[] = 'default';
    $templates_views = get_posts( 'post_type=view-template&numberposts=-1&status=publish' );
    foreach ( $templates_views as $template_view ) {
        $templates[] = $template_view->ID;
    }
    $result = array();
    if ( !empty( $data ) ) {
        foreach ( $templates as $template ) {
            if ( in_array( $template, $data ) ) {
                $result[] = $template;
            }
        }
    }
    return $result;
}

/**
 * Activates group.
 * Modified by Gen, 13.02.2013
 *
 * @param int $group_id
 * @param string $post_type
 *
 * @return int
 * @global object $wpdb
 *
 */
function wpcf_admin_fields_activate_group( $group_id, $post_type = TYPES_CUSTOM_FIELD_GROUP_CPT_NAME )
{
    global $wpdb;
    return $wpdb->update( $wpdb->posts, array('post_status' => 'publish'),
                    array('ID' => intval( $group_id ), 'post_type' => $post_type),
                    array('%s'), array('%d', '%s')
    );
}

/**
 * Deactivates group.
 * Modified by Gen, 13.02.2013
 *
 * @param int $group_id
 * @param string $post_type
 *
 * @return int
 * @global object $wpdb
 *
 */
function wpcf_admin_fields_deactivate_group( $group_id,
        $post_type = TYPES_CUSTOM_FIELD_GROUP_CPT_NAME ) {
    global $wpdb;
    return $wpdb->update( $wpdb->posts, array('post_status' => 'draft'),
                    array('ID' => intval( $group_id ), 'post_type' => $post_type),
                    array('%s'), array('%d', '%s')
    );
}

/**
 * Removes specific field from group.
 *
 * @param $group_id
 * @param $field_id
 *
 * @return bool
 */
function wpcf_admin_fields_remove_field_from_group( $group_id, $field_id ) {
    $group_fields = get_post_meta( $group_id, '_wp_types_group_fields', true );
    if ( empty( $group_fields ) ) {
        return false;
    }
    $group_fields = str_replace( ',' . $field_id . ',', ',', $group_fields );
    return update_post_meta( $group_id, '_wp_types_group_fields', $group_fields );
}

/**
 * Bulk removal
 *
 * @param $group_id
 * @param $fields
 * @deprecated
 */
function wpcf_admin_fields_remove_field_from_group_bulk( $group_id, $fields ) {
    foreach ( $fields as $field_id ) {
		$field_id = sanitize_text_field( $field_id );
        wpcf_admin_fields_remove_field_from_group( $group_id, $field_id );
    }
}

/**
 * Deletes field.
 * Modified by Gen, 13.02.2013
 *
 * @param string $field_id
 *
 * @param string $post_type
 * @param string $meta_name
 *
 * @return bool
 * @global object $wpdb
 *
 */
function wpcf_admin_fields_delete_field( $field_id,
        $post_type = TYPES_CUSTOM_FIELD_GROUP_CPT_NAME, $meta_name = 'wpcf-fields' ) {
    global $wpdb;
    $fields = get_option( $meta_name, array() );
    if ( isset( $fields[$field_id] ) ) {
        // Remove from groups
        $groups = wpcf_admin_fields_get_groups( $post_type );
        foreach ( $groups as $key => $group ) {
            wpcf_admin_fields_remove_field_from_group( $group['id'], $field_id );
        }
        // Remove from posts
        if ( !wpcf_types_cf_under_control( 'check_outsider', $field_id, $post_type, $meta_name ) ) {
            $results = $wpdb->get_results(
                $wpdb->prepare(
                    "SELECT post_id, meta_key FROM $wpdb->postmeta WHERE meta_key = %s",
                    wpcf_types_get_meta_prefix( $fields[$field_id] ) . strval( $field_id )
                )
            );
            foreach ( $results as $result ) {
                delete_post_meta( $result->post_id, $result->meta_key );
            }
        }
        unset( $fields[$field_id] );
        wpcf_admin_fields_save_fields( $fields, true, $meta_name );
        return true;
    } else {
        return false;
    }
}

/**
 * Deletes group by ID.
 * Modified by Gen, 13.02.2013
 *
 * @param int $group_id
 * @param string $post_type
 *
 * @return int
 */
function wpcf_admin_fields_delete_group( $group_id,
        $post_type = TYPES_CUSTOM_FIELD_GROUP_CPT_NAME ) {
    $group = get_post( $group_id );
    if ( empty( $group ) || $group->post_type != $post_type ) {
        return false;
    }
    wp_delete_post( $group_id, true );
}

/**
 * Update an existing field group or create new one.
 *
 * @param array $group
 * @param string $post_type
 * @return int Group ID.
 */
function wpcf_admin_fields_save_group( $group, $post_type = TYPES_CUSTOM_FIELD_GROUP_CPT_NAME, $which_fields = 'none' )
{
    if ( !isset( $group['name'] ) || empty( $group['name'] ) ) {
        return false;
    }


    $post = array(
        'post_status' => 'publish',
        'post_type' => $post_type,
        'post_title' => sanitize_text_field($group['name']),
        'post_name' => sanitize_text_field($group['name']),
        'post_content' => empty( $group['description'] )? '': $group['description'],
    );

    if( empty( $post['post_title'] ) ) {
        wpcf_admin_message( __( 'Please set name', 'wpcf' ), 'error' );
        return false;
    }

    $update = false;
    $slug_pre_save = false;

    if ( isset( $group['id'] ) && !empty($group['id']) ) {
        $update = true;
        $post_to_update = get_post( $group['id'] );
        if ( empty( $post_to_update ) || $post_to_update->post_type != $post_type ) {
            return false;
        }
        $post['ID'] = $post_to_update->ID;
        $slug_pre_save = $post_to_update->post_name;
        $post['post_status'] = $post_to_update->post_status;
    }

    if ( $update ) {
        $group_id = wp_update_post( $post );
        if ( !$group_id ) {
            return false;
        }
        update_post_meta( $group_id, TOOLSET_EDIT_LAST, time());
    } else {
        $group_id = wp_insert_post( $post, true );
        if ( is_wp_error( $group_id ) ) {
            return false;
        }
    }

    if( isset( $group['admin_styles'] ) ) {
        wpcf_admin_fields_save_group_admin_styles( $group_id, $group['admin_styles'] );
    }

    if ( !empty( $group['filters_association'] ) ) {
        update_post_meta( $group_id, '_wp_types_group_filters_association', $group['filters_association'] );
    } else {
        delete_post_meta( $group_id, '_wp_types_group_filters_association' );
    }

    // WPML register strings
    if ( function_exists( 'icl_register_string' ) ) {

	    try {
		    // Legacy function gives us only the underlying post type of the field group.
		    $group_factory = Types_Field_Utils::get_group_factory_by_post_type( $post_type );
		    $field_group = $group_factory->load_field_group( sanitize_title( $group['name'] ) );

		    // Skip registering if the group does not exist.
		    if( null != $field_group ) {
			    $group_wpml = new Types_Wpml_Field_Group( $field_group );
			    $group_wpml->register( $slug_pre_save );
		    }

	    } catch( InvalidArgumentException $e ) {
		    // Something is seriously wrong - there's no field group factory for given post type, bail.
	    }
    }

    // admin message
    wpcf_admin_fields_save_message( $update, $which_fields );

    return $group_id;
}

/**
 * Saves all fields.
 *
 * @param array $fields
 * @param bool $forced If true, overwrite the whole option.
 * @param string $option_name
 */
function wpcf_admin_fields_save_fields( $fields, $forced = false, $option_name = 'wpcf-fields' ) {
    $original = get_option( $option_name, array() );
    if ( !$forced ) {
        $fields = array_merge( $original, $fields );
    }
    update_option( $option_name, $fields );
}

/**
 * Saves field.
 *
 * Note: This is (probably) intended only for saving field definition data on the Edit Field Group pages.
 * 
 * @param array $field Field data
 * @param string $post_type
 * @param string $option_name
 *
 * @return string|WP_Error Field slug or an error object.
 */
function wpcf_admin_fields_save_field( $field, $post_type = TYPES_CUSTOM_FIELD_GROUP_CPT_NAME, $option_name = 'wpcf-fields' )
{

    if ( !isset( $field['name'] ) || !isset( $field['type'] ) ) {
        return new WP_Error( 'wpcf_save_field_no_name_or_type', __( "Error saving field", 'wpcf' ) );
    }

    $field = wpcf_sanitize_field( $field );

    if ( empty( $field['name'] ) || empty( $field['slug'] ) ) {
        return new WP_Error( 'wpcf_save_field_no_name', __( "Please set name for field", 'wpcf' ) );
    }

    $field['id'] = $field['slug'];

    // Set field specific data
    // NOTE: This was $field['data'] = $field and seemed to work on most systems.
    // I changed it to asign via a temporary variable to fix on one system.
    $temp_field = $field;
    $field['data'] = $temp_field;
    // Unset default fields
	// fixme These lines effectively erases all new values in "data", does anyone know why?
	// fixme This function needs SERIOUS review.
    unset( $field['data']['type'], $field['data']['slug'],
            $field['data']['name'], $field['data']['description'],
            $field['data']['user_id'], $field['data']['id'],
            $field['data']['data'] );

    // Merge previous data (added because of outside fields)
    // @TODO Remember why
    if ( wpcf_types_cf_under_control( 'check_outsider', $field['id'],
                    $post_type, $option_name ) ) {
        $field_previous_data = wpcf_admin_fields_get_field( $field['id'], false,
                true, false, $option_name );
        if ( !empty( $field_previous_data['data'] ) ) {
            $field['data'] = array_merge( $field_previous_data['data'],
                    $field['data'] );
        }
    }

    $field['data'] = apply_filters( 'wpcf_fields_' . $field['type'] . '_meta_data', $field['data'], $field );

    // Check validation
    if ( isset( $field['data']['validate'] ) ) {
        foreach ( $field['data']['validate'] as $method => $data ) {
            if ( !isset( $data['active'] ) ) {
                unset( $field['data']['validate'][$method] );
            }
        }
        if ( empty( $field['data']['validate'] ) ) {
            unset( $field['data']['validate'] );
        }
    }

    $save_data = array();
    $save_data['id'] = $field['id'];
    $save_data['slug'] = $field['slug'];
    $save_data['type'] = $field['type'];
    $save_data['name'] = $field['name'];
    $save_data['description'] = $field['description'];
    $save_data['data'] = $field['data'];
    $save_data['data']['disabled_by_type'] = 0;

    // For radios or select
    if ( !empty( $field['data']['options'] ) ) {
        foreach ( $field['data']['options'] as $name => $option ) {
            if ( isset( $option['title'] ) ) {
                $option['title'] = $field['data']['options'][$name]['title'] = htmlspecialchars_decode( $option['title'] );
            }
            if ( isset( $option['value'] ) ) {
                $option['value'] = $field['data']['options'][$name]['value'] = htmlspecialchars_decode( $option['value'] );
            }
            if ( isset( $option['display_value'] ) ) {
                $option['display_value'] = $field['data']['options'][$name]['display_value'] = htmlspecialchars_decode( $option['display_value'] );
            }
            // For checkboxes
            if ( $field['type'] == 'checkboxes' && isset( $option['set_value'] )
                    && $option['set_value'] != '1' ) {
                $option['set_value'] = $field['data']['options'][$name]['set_value'] = htmlspecialchars_decode( $option['set_value'] );
            }
            if ( $field['type'] == 'checkboxes' && !empty( $option['display_value_selected'] ) ) {
                $option['display_value_selected'] = $field['data']['options'][$name]['display_value_selected'] = htmlspecialchars_decode( $option['display_value_selected'] );
            }
            if ( $field['type'] == 'checkboxes' && !empty( $option['display_value_not_selected'] ) ) {
                $option['display_value_not_selected'] = $field['data']['options'][$name]['display_value_not_selected'] = htmlspecialchars_decode( $option['display_value_not_selected'] );
            }
        }
    }

    // For checkboxes
    if ( 'checkbox' == $field['type'] && isset($field['set_value']) && $field['set_value'] != '1' ) {
        $field['set_value'] = htmlspecialchars_decode( $field['set_value'] );
    }
    if ( $field['type'] == 'checkbox' && !empty( $field['display_value_selected'] ) ) {
        $field['display_value_selected'] = htmlspecialchars_decode( $field['display_value_selected'] );
    }
    if ( $field['type'] == 'checkbox' && !empty( $field['display_value_not_selected'] ) ) {
        $field['display_value_not_selected'] = htmlspecialchars_decode( $field['display_value_not_selected'] );
    }

    // Save field
    $fields = get_option( $option_name, array() );

    // prevent erasing saved conditional display data (it's controlled via ajax)
    if( isset( $fields[$field['slug']]['data']['conditional_display'] )
        && ! empty ( $fields[$field['slug']]['data']['conditional_display'] ) ) {
        $save_data['data']['conditional_display'] = $fields[$field['slug']]['data']['conditional_display'];
    }

    // on changing a field slug
    if( isset($field['slug-pre-save'] ) && $field['slug'] != $field['slug-pre-save'] ) {

        // takeover conditions on renamed slug
        if( isset( $fields[$field['slug-pre-save']]['data']['conditional_display'] )
            && ! empty ( $fields[$field['slug-pre-save']]['data']['conditional_display'] ) ) {
            $save_data['data']['conditional_display'] = $fields[$field['slug-pre-save']]['data']['conditional_display'];
        }

        // field is assigned ONLY to current group
        $belongs_to_groups = wpcf_admin_fields_get_groups_by_field( $field['slug-pre-save'], $post_type );
        if( isset( $_GET['group_id'] )
            && isset( $fields[$field['slug-pre-save']] )
            && count( $belongs_to_groups ) == 1
            && isset( $belongs_to_groups[$_GET['group_id']] )
        ) {
            global $wpdb;

            // as it's the only group with this field we can update the slug for all items
            $wpdb->update(
                $wpdb->postmeta,
                array( 'meta_key' => 'wpcf-'.$field['slug'] ),
                array( 'meta_key' => 'wpcf-'.$field['slug-pre-save'] ),
                array( '%s'),
                array( '%s')
            );

            // change slug in field conditions
            foreach( $fields as $key => &$single_field ) {
                if( isset( $single_field['data']['conditional_display']['conditions'] ) ) {
                    foreach( $single_field['data']['conditional_display']['conditions'] as &$single_condition ) {
                        if( $single_condition['field'] == $field['slug-pre-save'] )
                            $single_condition['field'] = $field['slug'];
                    }
                }
            }

            // get all group conditions which contain the old slug
            $groups_conditions = $wpdb->get_results( "
                SELECT post_id, meta_value
                FROM   $wpdb->postmeta
                WHERE  meta_key = '_wpcf_conditional_display'
                AND    meta_value LIKE '%" . $field['slug-pre-save'] . "%'
	        " );

            if( $groups_conditions ) {
                foreach( $groups_conditions as $group_condition ) {
                    $meta_value = unserialize( $group_condition->meta_value );

                    // proper proof for slug
                    if( isset( $meta_value['conditions'] ) ) {
                        foreach( $meta_value['conditions'] as &$single_condition ) {
                            if( $single_condition['field'] == $field['slug-pre-save'] ) {
                                // update conditions slug
                                $single_condition['field'] = $field['slug'];
                                update_post_meta( $group_condition->post_id, '_wpcf_conditional_display', $meta_value );
                                break;
                            }
                        }
                    }
                }
            }

            // delete field
            wpcf_admin_fields_delete_field( $field['slug-pre-save'], $post_type, $option_name );
            unset( $fields[$field['slug-pre-save']] );

        // update items by post type
        } elseif( isset( $_GET['group_id'] ) ) {

            switch( $post_type ) {
                case 'wp-types-group':
                    $group = Types_Field_Group_Post_Factory::load( $_GET['group_id'] );
                    break;
                case 'wp-types-term-group':
                    $group = Types_Field_Group_Term_Factory::load( $_GET['group_id'] );
                    break;
                case 'wp-types-user-group':
                    $group = Types_Field_Group_User_Factory::load( $_GET['group_id'] );
                    break;
            }
	        
	        if( null != $group ) {

		        $assigned_types = $group->get_assigned_to_types();

		        if ( ! empty( $assigned_types ) ) {
			        $items = $group->get_assigned_to_items();

			        if ( is_array( $items ) && ! empty( $items ) ) {
				        global $wpdb;
				        foreach ( $items as $item ) {
					        $wpdb->update(
						        $wpdb->postmeta,
						        array( 'meta_key' => 'wpcf-' . $field['slug'] ),
						        array( 'meta_key' => 'wpcf-' . $field['slug-pre-save'], 'post_id' => $item->ID ),
						        array( '%s', '%d' ),
						        array( '%s' )
					        );
				        }
			        }
		        }
	        }
        }
    }


    $fields[$field['slug']] = $save_data;
    update_option( $option_name, $fields );
    $field_id = $field['slug'];

    // WPML register strings
    if ( function_exists( 'icl_register_string' ) ) {
        if ( isset($_POST['wpml_cf_translation_preferences'][$field_id] ) ) {
            $__wpml_action = intval( $_POST['wpml_cf_translation_preferences'][$field_id] );
        } else {
            $__wpml_action = wpcf_wpml_get_action_by_type( $field['type'] );
        }

        wpcf_translate_register_string( 'plugin Types',
                'field ' . $field_id . ' name', $field['name'] );
        wpcf_translate_register_string( 'plugin Types',
                'field ' . $field_id . ' description', $field['description'] );

        // For radios or select
        if ( !empty( $field['data']['options'] ) ) {
            foreach ( $field['data']['options'] as $name => $option ) {
                if ( $name == 'default' ) {
                    continue;
                }
                if ( isset( $option['title'] ) ) {
                    wpcf_translate_register_string( 'plugin Types',
                            'field ' . $field_id . ' option ' . $name . ' title',
                            $option['title'] );
                }
                if ($__wpml_action === 2) {
                    if ( isset( $option['value'] ) ) {
                        wpcf_translate_register_string( 'plugin Types',
                                'field ' . $field_id . ' option ' . $name . ' value',
                                $option['value'] );
                    }
                }
                if ( isset( $option['display_value'] ) ) {
                    wpcf_translate_register_string( 'plugin Types',
                            'field ' . $field_id . ' option ' . $name . ' display value',
                            $option['display_value'] );
                }
                // For checkboxes
                if ( isset( $option['set_value'] ) && $option['set_value'] != '1' ) {
                    wpcf_translate_register_string( 'plugin Types',
                            'field ' . $field_id . ' option ' . $name . ' value',
                            $option['set_value'] );
                }
                if ( !empty( $option['display_value_selected'] ) ) {
                    wpcf_translate_register_string( 'plugin Types',
                            'field ' . $field_id . ' option ' . $name . ' display value selected',
                            $option['display_value_selected'] );
                }
                if ( !empty( $option['display_value_not_selected'] ) ) {
                    wpcf_translate_register_string( 'plugin Types',
                            'field ' . $field_id . ' option ' . $name . ' display value not selected',
                            $option['display_value_not_selected'] );
                }
            }
        }

        if ( $field['type'] == 'checkbox' && $field['set_value'] != '1' ) {
            // we need to translate the check box value to store
            wpcf_translate_register_string( 'plugin Types',
                    'field ' . $field_id . ' checkbox value',
                    $field['set_value'] );
        }

        if ( $field['type'] == 'checkbox' && !empty( $field['display_value_selected'] ) ) {
            // we need to translate the check box value to store
            wpcf_translate_register_string( 'plugin Types',
                    'field ' . $field_id . ' checkbox value selected',
                    $field['display_value_selected'] );
        }

        if ( $field['type'] == 'checkbox' && !empty( $field['display_value_not_selected'] ) ) {
            // we need to translate the check box value to store
            wpcf_translate_register_string( 'plugin Types',
                    'field ' . $field_id . ' checkbox value not selected',
                    $field['display_value_not_selected'] );
        }

        // Validation message
        if ( !empty( $field['data']['validate'] ) ) {
            foreach ( $field['data']['validate'] as $method => $validation ) {
                if ( !empty( $validation['message'] ) ) {
                    // Skip if it's same as default
                    $default_message = wpcf_admin_validation_messages( $method );
                    if ( $validation['message'] != $default_message ) {
                        wpcf_translate_register_string( 'plugin Types',
                                'field ' . $field_id . ' validation message ' . $method,
                                $validation['message'] );
                    }
                }
            }
        }
    }

    /**
     * WPML update translation status
     */
    if (
        isset($save_data['data'])
        && isset($save_data['data']['submit-key'])
    ) {
        if ( isset( $_POST[ 'wpml_cf_translation_preferences' ][ $save_data['data']['submit-key'] ] ) ) {
            do_action(
                'wpml_config',
                array(
                    'section'   => 'custom-fields',
                    'key'       => wpcf_types_get_meta_prefix( wpcf_admin_fields_get_field( $save_data['slug']) ) . $save_data['slug'],
                    'value'     => intval( $_POST[ 'wpml_cf_translation_preferences' ][ $save_data['data']['submit-key'] ] ),
                    'read_only' => true
                )
            );
        }
    }

    return $field_id;
}

/*
 * Admin messages on post save/update
 */
function wpcf_admin_fields_save_message( $updated = false, $which_fields = 'none' ) {

    switch( $which_fields ) {
        case 'custom':
            $msg = $updated
                ? __( 'Post Field Group saved.', 'wpcf' )
                : __( 'New Post Field Group created.', 'wpcf' );
            break;

        case 'user':
            $msg = $updated
                ? __( 'User Field Group saved.', 'wpcf' )
                : __( 'New User Field Group created.', 'wpcf' );
            break;

        case 'term':
            $msg = $updated
                ? __( 'Term Field Group saved.', 'wpcf' )
                : __( 'New Term Field Group created.', 'wpcf' );
            break;
    }

    if( isset( $msg ) ) {
        wpcf_admin_message_store(
            $msg,
            'updated notice notice-success is-dismissible'
        );
    }

}


/**
 * Get a matrix of allowed conversions between field types.
 *
 * @since unknown
 * @return string[][]|mixed Associative array of string arrays (unless the used filters return something broken).
 * @deprecated Since 2.0 use Types_Field_Type_Converter instead.
 */
function wpcf_admin_custom_fields_change_type_allowed_matrix() {
	return Types_Field_Type_Converter::get_instance()->get_conversion_matrix();
}


/**
 * Saves group's fields.
 * Modified by Gen, 13.02.2013
 *
 * @param int $group_id
 * @param array $fields
 * @param bool $add
 * @param string $post_type
 *
 * @return bool
 */
function wpcf_admin_fields_save_group_fields( $group_id, $fields, $add = false, $post_type = TYPES_CUSTOM_FIELD_GROUP_CPT_NAME, $option_name = null )
{
	if( null == $option_name ) {
		$option_name = ( $post_type == TYPES_CUSTOM_FIELD_GROUP_CPT_NAME ? 'wpcf-fields' : 'wpcf-usermeta' );
	}
    $fields = wpcf_types_cf_under_control( 'add', array('fields' => $fields), $post_type, $option_name );
    if ( $add ) {
        $existing_fields = wpcf_admin_fields_get_fields_by_group( $group_id, 'slug', false, true, false, $post_type, $option_name );
        $order = array();
        if ( !empty( $existing_fields ) ) {
            foreach ( $existing_fields as $field_id => $field ) {
                if ( in_array( $field['id'], $fields ) ) {
                    continue;
                }
                $order[] = $field['id'];
            }
            foreach ( $fields as $field ) {
                $order[] = sanitize_text_field( $field );
            }
            $fields = $order;
        }
    }
    if ( empty( $fields ) ) {
        delete_post_meta( $group_id, '_wp_types_group_fields' );
        return false;
    }
    $fields = ',' . implode( ',', (array) $fields ) . ',';
    update_post_meta( $group_id, '_wp_types_group_fields', $fields );
}

/**
 * Saves group's post types.
 *
 * @param type $group_id
 * @param type $post_types
 */
function wpcf_admin_fields_save_group_post_types( $group_id, $post_types )
{
    if ( !empty($post_types) ) {
        $post_types = array_unique($post_types);
    }
    /**
     * remove key "all"
     */
    if(($key = array_search('all', $post_types)) !== false) {
        unset($post_types[$key]);
    }
    if ( empty( $post_types ) ) {
        update_post_meta( $group_id, '_wp_types_group_post_types', 'all' );
        return true;
    }
    $post_types = ',' . implode( ',', (array) $post_types ) . ',';
    update_post_meta( $group_id, '_wp_types_group_post_types', $post_types );
}

/**
 * Saves group's terms.
 *
 * @param type $group_id
 * @param type $terms
 */
function wpcf_admin_fields_save_group_terms( $group_id, $terms ) {
    if ( empty( $terms ) ) {
        update_post_meta( $group_id, '_wp_types_group_terms', 'all' );
        return true;
    }
    $terms = ',' . implode( ',', (array) $terms ) . ',';
    update_post_meta( $group_id, '_wp_types_group_terms', $terms );
}

/**
 * Saves group's templates.
 *
 * @param type $group_id
 * @param type $terms
 */
function wpcf_admin_fields_save_group_templates( $group_id, $templates ) {
    if ( empty( $templates ) ) {
        update_post_meta( $group_id, '_wp_types_group_templates', 'all' );
        return true;
    }
    $templates = ',' . implode( ',', (array) $templates ) . ',';
    update_post_meta( $group_id, '_wp_types_group_templates', strip_tags($templates) );
}

/**
 * Returns HTML formatted AJAX activation link.
 *
 * @param int $group_id
 * @param string $action
 *
 * @return string
 * @global object $wpdb
 *
 */
function wpcf_admin_fields_get_ajax_activation_link( $group_id, $action = 'activate_group' ) {
    return '<a href="' . admin_url( 'admin-ajax.php?action=wpcf_ajax&amp;'
                    . 'wpcf_action=' . $action . '&amp;group_id='
                    . $group_id . '&amp;wpcf_ajax_update=wpcf_list_ajax_response_'
                    . $group_id ) . '&amp;_wpnonce=' . wp_create_nonce( $action )
            . '" class="wpcf-ajax-link" id="wpcf-list-activate-'
            . $group_id . '">'
            . __( 'Activate', 'wpcf' ) . '</a>';
}

/**
 * Returns HTML formatted AJAX deactivation link.
 * @param int $group_id
 * @return string
 */
function wpcf_admin_fields_get_ajax_deactivation_link( $group_id, $action = 'deactivate_group' ) {
    return '<a href="' . admin_url( 'admin-ajax.php?action=wpcf_ajax&amp;'
                    . 'wpcf_action=' . $action . '&amp;group_id='
                    . $group_id . '&amp;wpcf_ajax_update=wpcf_list_ajax_response_'
                    . $group_id ) . '&amp;_wpnonce=' . wp_create_nonce( $action )
            . '" class="wpcf-ajax-link" id="wpcf-list-activate-'
            . $group_id . '">'
            . __( 'Deactivate', 'wpcf' ) . '</a>';
}

/**
 * Check how many posts needs checkbox update.
 *
 * @param type $field
 * @param type $action
 * @return boolean|int
 */
function wpcf_admin_fields_checkbox_migrate_empty_check( $field, $action ) {
    if ( $field['type'] != 'checkbox' ) {
        return false;
    }
    if ($field['meta_type'] == 'postmeta') {
        $filter = wpcf_admin_fields_get_filter_by_field( $field['id'] );
        if ( !empty( $filter ) ) {
            $posts = array();
            $meta_key = esc_sql( wpcf_types_get_meta_prefix( $field ) . $field['id'] );
            $meta_query = '';
            if ( $action == 'do_not_save_check' ) {
                $meta_query = "(m.meta_key = '$meta_key' AND m.meta_value = '0')";
                $posts = wpcf_admin_fields_get_posts_by_filter( $filter, $meta_query );
            } else if ( $action == 'save_check' ) {
                $posts = wpcf_admin_fields_get_posts_by_filter_missing_meta( $filter,
                        $meta_key );
            }
            $option = get_option( 'wpcf_checkbox_migration', array() );
            $cache_key = $action == 'do_not_save_check' ? 'do_not_save' : 'save';
            $option[$cache_key] = $posts;
            update_option( 'wpcf_checkbox_migration', $option );
            return $posts;
        }
    } else if ($field['meta_type'] == 'usermeta') {
        $option = get_option( 'wpcf_checkbox_migration_usermeta', array() );
        $cache_key = $action == 'do_not_save_check' ? 'do_not_save' : 'save';
        if ( $action == 'do_not_save_check' ) {
            $user_query = new WP_User_Query( array('meta_key' => $field['meta_key'],
                'meta_value' => '0', 'meta_compare' => '=', 'fields' => 'ID') );
            $r =  $user_query->results;
        } else if ( $action == 'save_check' ) {
            global $wpdb;
            $_query = "SELECT u.ID FROM {$wpdb->users} u WHERE NOT EXISTS (SELECT um.user_id FROM {$wpdb->usermeta} um WHERE u.ID = um.user_id AND um.meta_key = '%s')";
            $r = $wpdb->get_col($wpdb->prepare( $_query, $field['meta_key']) );
        }
        $option[$field['meta_key']][$cache_key] = $r;
        update_option( 'wpcf_checkbox_migration_usermeta', $option );
        return $r;
    }
    return false;
}

/**
 * Update posts checkboxes fields.
 *
 * @param type $field
 * @param type $action
 * @return boolean|int
 */
function wpcf_admin_fields_checkbox_migrate_empty( $field, $action ) {
    if ( $field['type'] != 'checkbox' ) {
        return false;
    }
    if ( $field['meta_type'] == 'usermeta' ) {
        $option = get_option( 'wpcf_checkbox_migration_usermeta', array() );
        if ( empty( $option[$field['meta_key']][$action] ) ) {
            $users = wpcf_admin_fields_checkbox_migrate_empty_check( $field,
                    $action . '_check' );
        } else {
            $users = $option[$field['meta_key']][$action];
        }
        if ( !empty( $users ) ) {
            if ( $action == 'do_not_save' ) {
                $count = 0;
                foreach ( $users as $temp_key => $user_id ) {
                    if ( $count == 1000 ) {
                        $option[$field['meta_key']][$action] = $users;
                        update_option( 'wpcf_checkbox_migration', $option );
                        $data = array('offset' => $temp_key);
                        return $data;
                    }
                    delete_user_meta( $user_id, $field['meta_key'], 0 );
                    unset( $users[$temp_key] );
                    $count++;
                }
                unset( $option[$field['meta_key']][$action] );
                update_option( 'wpcf_checkbox_migration_usermeta', $option );
                return $users;
            } else if ( $action == 'save' ) {
                $count = 0;
                foreach ( $users as $temp_key => $user_id ) {
                    if ( $count == 1000 ) {
                        $option[$field['meta_key']][$action] = $users;
                        update_option( 'wpcf_checkbox_migration_usermeta', $option );
                        $data = array('offset' => $temp_key);
                        return $data;
                    }
                    update_user_meta( $user_id, $field['meta_key'], 0 );
                    unset( $users[$temp_key] );
                    $count++;
                }
                unset( $option[$field['meta_key']][$action] );
                update_option( 'wpcf_checkbox_migration_usermeta', $option );
                return $users;
            }
        }
        return false;
    }
    $option = get_option( 'wpcf_checkbox_migration', array() );
    $meta_key = wpcf_types_get_meta_prefix( $field ) . $field['id'];
    if ( empty( $option[$action] ) ) {
        $posts = wpcf_admin_fields_checkbox_migrate_empty_check( $field,
                $action . '_check' );
    } else {
        $posts = $option[$action];
    }

    if ( !empty( $posts ) ) {
        if ( $action == 'do_not_save' ) {
            $count = 0;
            foreach ( $posts as $temp_key => $post_id ) {
                if ( $count == 1000 ) {
                    $option[$action] = $posts;
                    update_option( 'wpcf_checkbox_migration', $option );
                    $data = array('offset' => $temp_key);
                    return $data;
                }
                delete_post_meta( $post_id, $meta_key, 0 );
                unset( $posts[$temp_key] );
                $count++;
            }
            unset( $option[$action] );
            update_option( 'wpcf_checkbox_migration', $option );
            return $posts;
        } else if ( $action == 'save' ) {
            $count = 0;
            foreach ( $posts as $temp_key => $post_id ) {
                if ( $count == 1000 ) {
                    $option[$action] = $posts;
                    update_option( 'wpcf_checkbox_migration', $option );
                    $data = array('offset' => $temp_key);
                    return $data;
                }
                update_post_meta( $post_id, $meta_key, 0 );
                unset( $posts[$temp_key] );
                $count++;
            }
            unset( $option[$action] );
            update_option( 'wpcf_checkbox_migration', $option );
            return $posts;
        }
    }
    return false;
}

/**
 * Gets all filters required for field to be used.
 *
 * @param type $field
 * @return boolean|string
 */
function wpcf_admin_fields_get_filter_by_field( $field ) {
    $field = wpcf_admin_fields_get_field( $field );
    if ( empty( $field ) ) {
        return false;
    }
    $filter = array();
    $filter['types'] = array();
    $filter['terms'] = array();
    $filter['templates'] = array();
    $groups = wpcf_admin_fields_get_groups_by_field( $field['id'] );
    foreach ( $groups as $group_id => $group_data ) {
        // Get filters
        $filter['types'] = array_merge( $filter['types'],
                explode( ',',
                        trim( get_post_meta( $group_id,
                                        '_wp_types_group_post_types', true ),
                                ',' ) ) );
        $filter['terms'] = array_merge( $filter['terms'],
                explode( ',',
                        trim( get_post_meta( $group_id, '_wp_types_group_terms',
                                        true ), ',' ) ) );
        $filter['templates'] = array_merge( $filter['templates'],
                explode( ',',
                        trim( get_post_meta( $group_id,
                                        '_wp_types_group_templates', true ), ',' ) ) );
        $filter['association'] = isset( $group_data['filters_association'] ) && $group_data['filters_association'] == 'any' ? 'OR' : 'AND';
    }
    if ( in_array( 'all', $filter['types'] ) ) {
        $filter['types'] = 'all';
    }
    if ( in_array( 'all', $filter['terms'] ) ) {
        $filter['terms'] = 'all';
    }
    if ( in_array( 'all', $filter['templates'] ) ) {
        $filter['templates'] = 'all';
    }

    return $filter;
}

/**
 * Gets posts by filter fetched with wpcf_admin_fields_get_filter_by_field().
 *
 * @param array $filter
 * @param string $meta_query This argument needs to be allways sanitized!
 * @return array
 */
function wpcf_admin_fields_get_posts_by_filter( $filter, $meta_query = '' ) {
    global $wpdb, $wpcf;
    $query = array();
    $join = array();
    if ( $filter['types'] != 'all' && !empty( $filter['types'] ) ) {
        $post_types = array();
        foreach( $filter['types'] as $post_type ) {
            $post_types[] = esc_sql( $post_type );
        }
        $query[] = 'p.post_type IN (\'' . implode( '\',\'', $post_types ) . '\')';
    } else {
        $post_types = get_post_types( array('show_ui' => true), 'names' );
        foreach ( $post_types as $post_type_slug => $post_type ) {
            if ( in_array( $post_type_slug, $wpcf->excluded_post_types ) ) {
                unset( $post_types[$post_type_slug] );
            } else {
                $post_types[$post_type_slug] = esc_sql( $post_type );
            }
        }
        $query[] = 'p.post_type IN (\'' . implode( '\',\'', $post_types ) . '\')';
    }
    if ( $filter['terms'] != 'all' && !empty( $filter['terms'] ) ) {
        $ttid = array();
        foreach ( $filter['terms'] as $term_id ) {
            $term_taxonomy_id = $wpdb->get_var(
                $wpdb->prepare(
                    "SELECT term_taxonomy_id FROM $wpdb->term_taxonomy WHERE term_id=%d",
                    $term_id
                )
            );
            if ( !empty( $term_taxonomy_id ) ) {
                $ttid[] = esc_sql( $term_taxonomy_id );
            }
        }
        $query[] = 't.term_taxonomy_id IN (\'' . implode( '\',\'', $ttid ) . '\')';
        $join[] = "LEFT JOIN $wpdb->term_relationships t ON p.ID = t.object_id ";
    }
    if ( $filter['templates'] != 'all' && !empty( $filter['templates'] ) ) {
        $templates = array();
        foreach( $filter['templates'] as $template ) {
            $templates[] = esc_sql( $template );
        }
        $query[] = '(m.meta_key = \'_wp_page_template\' AND m.meta_value IN (\'' . implode( '\',\'',
                        $templates ) . '\'))';
    }
    if ( !empty( $meta_query )
            || ($filter['templates'] != 'all' && !empty( $filter['templates'] )) ) {
        $join[] = "LEFT JOIN $wpdb->postmeta m ON p.ID = m.post_id ";
    }

    $_query = "SELECT p.ID FROM $wpdb->posts p " . implode( '', $join );
    if ( !empty( $query ) ) {
        $association = ( strtoupper( trim( $filter['association'] ) ) == 'OR' ) ? 'OR' : 'AND';
        $_query .= "WHERE " . implode( ' ' . $association . ' ',
                        $query ) . ' ';
        if ( !empty( $meta_query ) ) {
            $_query .= ' AND ' . $meta_query . ' ';
        }
    } else if ( !empty( $meta_query ) ) {
        $_query .= "WHERE " . $meta_query . ' ';
    }
    $_query .= "GROUP BY p.ID";
    $posts = $wpdb->get_col( $_query );
    return $posts;
}

function wpcf_admin_fields_get_posts_by_filter_missing_meta_helper( $e )
{
    return '%s';
}

/**
 * Gets posts by filter with missing meta fetched
 * with wpcf_admin_fields_get_filter_by_field().
 *
 * @global object $wpdb
 * @param type $filter
 * @return type
 */
function wpcf_admin_fields_get_posts_by_filter_missing_meta( $filter, $meta_key = '' )
{
    global $wpdb, $wpcf;
    $query = $join = array();
    $args = array(
        'post_types' => array(),
        'templates' => array(),
    );

    /**
     * post types
     */
    if ( $filter['types'] != 'all' && !empty( $filter['types'] ) ) {
        $args['post_types'] = $filter['types'];
    } else {
        $post_types_all = array_keys(get_post_types( array('show_ui' => true), 'names' ));
        foreach ( $post_types_all as $post_type ) {
            if ( in_array( $post_type, $wpcf->excluded_post_types ) ) {
                continue;
            }
            $args['post_types'][$post_type] = $post_type;
        }
    }
    $query[] = sprintf(
        ' p.post_type IN ( %s ) ',
        implode( ', ', array_map('wpcf_admin_fields_get_posts_by_filter_missing_meta_helper', $args['post_types'] ))
    );

    /**
     * types
     */
    if ( $filter['terms'] != 'all' && !empty( $filter['terms'] ) ) {
        $ttid = array();
        foreach ( $filter['terms'] as $term_id ) {
            $term_taxonomy_id = $wpdb->get_var(
                $wpdb->prepare(
                    "SELECT term_taxonomy_id FROM $wpdb->term_taxonomy WHERE term_id=%d",
                    $term_id
                )
            );
            if ( !empty( $term_taxonomy_id ) ) {
                $ttid[] = intval( $term_taxonomy_id );
            }
        }
        $query[] = 't.term_taxonomy_id IN (\'' . implode( '\',\'', $ttid ) . '\')';
        $join[] = "LEFT JOIN $wpdb->term_relationships t ON p.ID = t.object_id ";
    }

    /**
     * templates
     */
    if ( $filter['templates'] != 'all' && !empty( $filter['templates'] ) ) {
        $args['templates'] = array();
        foreach( $filter['templates'] as $template ) {
            $args['templates'][$template] = $template;
        }
        $query[] = sprintf(
            ' (m.meta_key = \'_wp_page_template\' AND m.meta_value IN (%s))',
            implode( ', ', array_map('wpcf_admin_fields_get_posts_by_filter_missing_meta_helper', $args['templates'] ))
        );
        $join[] = "LEFT JOIN $wpdb->postmeta m ON p.ID = m.post_id ";
    }

    $_query = "SELECT DISTINCT p.ID FROM $wpdb->posts p " . implode( '', $join );
    $_query .= "WHERE NOT EXISTS ( SELECT * FROM $wpdb->postmeta mm WHERE p.ID = mm.post_id AND mm.meta_key = %s )";

    /**
     * merge $args to one array
     */
    $args = array_values(array( $meta_key ) + $args['post_types'] + $args['templates']);

    if ( !empty( $query ) ) {
        $association = ( strtoupper( trim( $filter['association'] ) ) == 'OR' ) ? 'OR' : 'AND';
        $_query .= " AND (" . implode( ' ' . $association . ' ', $query ) . ') ';
    }
    $_query .= "GROUP BY p.ID";

    return $wpdb->get_col( $wpdb->prepare($_query, $args));
}

/**
 * Check how many posts needs checkboxes update.
 *
 * @global object $wpdb
 *
 * @param type $field
 * @param type $action
 * @return boolean|int
 */
function wpcf_admin_fields_checkboxes_migrate_empty_check( $field, $action ) {
    if ( $field['type'] != 'checkboxes' || empty( $field['data']['options'] ) ) {
        return false;
    }
    if ( $field['meta_type'] == 'usermeta' ) {
        global $wpdb;
        if ( $action == 'do_not_save_check' ) {
            $query = array();
            foreach ( $field['data']['options'] as $option_id => $option_data ) {
                $query[] = '\"' . esc_sql( $option_id ) . '\";i:0;';
            }
            $meta_query = "SELECT u.ID FROM {$wpdb->users} u
                LEFT JOIN {$wpdb->usermeta} um ON u.ID = um.user_id
                WHERE (um.meta_key = '%s' AND (um.meta_value LIKE '%%"
                    . implode( "%%' OR um.meta_value LIKE '%%", $query ) . "%%'))";
        } else if ( $action == 'save_check' ) {
            $query = array();
            foreach ( $field['data']['options'] as $option_id => $option_data ) {
                // Check only if missing
                $query[] = '\"' . esc_sql( $option_id ) . '\"';
            }
            $meta_query = "SELECT u.ID FROM {$wpdb->users} u
                LEFT JOIN {$wpdb->usermeta} um ON u.ID = um.user_id
                WHERE (um.meta_key = '%s' AND (um.meta_value NOT LIKE '%%"
                    . implode( "%%' OR um.meta_value NOT LIKE '%%", $query ) . "%%'))";
        }
        $users = $wpdb->get_col( $wpdb->prepare( $meta_query, $field['meta_key'] ) );
        $option = get_option( 'wpcf_checkboxes_migration_usermeta', array() );
        $cache_key = $action == 'do_not_save_check' ? 'do_not_save' : 'save';
        $option[$field['meta_key']][$cache_key] = $users;
        update_option( 'wpcf_checkboxes_migration_usermeta', $option );
        return $users;
    }
    $filter = wpcf_admin_fields_get_filter_by_field( $field['id'] );
    if ( !empty( $filter ) ) {
        $posts = array();
        $meta_key = esc_sql( wpcf_types_get_meta_prefix( $field ) . $field['id'] );
        $meta_query = '';
        // "wpcf-fields-checkboxes-option-1873650245";s:1:"1";
        if ( $action == 'do_not_save_check' ) {
            $query = array();
            foreach ( $field['data']['options'] as $option_id => $option_data ) {
                $query[] = '\"' . esc_sql( $option_id ) . '\";i:0;';
            }
            $meta_query = "(m.meta_key = '$meta_key' AND (m.meta_value LIKE '%%"
                    . implode( "%%' OR m.meta_value LIKE '%%", $query ) . "%%'))";
            $posts = wpcf_admin_fields_get_posts_by_filter( $filter, $meta_query );
        } else if ( $action == 'save_check' ) {
            $query = array();
            foreach ( $field['data']['options'] as $option_id => $option_data ) {
                // Check only if missing
                $query[] = '\"' . esc_sql( $option_id ) . '\"';
            }
            $meta_query = "(m.meta_key = '$meta_key' AND (m.meta_value NOT LIKE '%%"
                    . implode( "%%' OR m.meta_value NOT LIKE '%%", $query ) . "%%'))";
            $posts = wpcf_admin_fields_get_posts_by_filter( $filter, $meta_query );
        }
        $option = get_option( 'wpcf_checkboxes_migration', array() );
        $cache_key = $action == 'do_not_save_check' ? 'do_not_save' : 'save';
        $option[$cache_key] = $posts;
        update_option( 'wpcf_checkboxes_migration', $option );
        return $posts;
    }
    return false;
}

/**
 * Update posts checkboxes fields.
 *
 * @param type $field
 * @param type $action
 * @return boolean|int
 */
function wpcf_admin_fields_checkboxes_migrate_empty( $field, $action ) {
    if ( $field['type'] != 'checkboxes' || empty( $field['data']['options'] ) ) {
        return false;
    }
    if ( $field['meta_type'] == 'usermeta' ) {
        $option = get_option( 'wpcf_checkboxes_migration_usermeta', array() );
        if ( empty( $option[$field['meta_key']][$action] ) ) {
            $users = wpcf_admin_fields_checkboxes_migrate_empty_check( $field,
                    $action . '_check' );
        } else {
            $users = $option[$field['meta_key']][$action];
        }

        if ( !empty( $users ) ) {
            if ( $action == 'do_not_save' ) {
                $count = 0;
                foreach ( $users as $temp_key => $user_id ) {
                    if ( $count == 1000 ) {
                        $option[$field['meta_key']][$action] = $users;
                        update_option( 'wpcf_checkboxes_migration_usermeta', $option );
                        $data = array('offset' => $temp_key);
                        return $data;
                    }
                    $meta_saved = get_user_meta( $user_id, $field['meta_key'] );
                    if ( !empty( $meta_saved ) ) {
                        foreach ( $meta_saved as $key => $value ) {
                            if ( !is_array( $value ) ) {
                                $value_check = array();
                            } else {
                                $value_check = $value;
                            }
                            foreach ( $field['data']['options'] as $option_id => $option_data ) {
                                if ( isset( $value_check[$option_id] ) && $value_check[$option_id] == '0' ) {
                                    unset( $value_check[$option_id] );
                                }
                            }
                            update_user_meta( $user_id, $field['meta_key'], $value_check,
                                    $value );
                        }
                    }
                    unset( $users[$temp_key] );
                    $count++;
                }
                unset( $option[$field['meta_key']][$action] );
                update_option( 'wpcf_checkboxes_migration_usermeta', $option );
                return $users;
            } else if ( $action == 'save' ) {
                $count = 0;
                foreach ( $users as $temp_key => $user_id ) {
                    if ( $count == 1000 ) {
                        $option[$field['meta_key']][$action] = $users;
                        update_option( 'wpcf_checkboxes_migration_usermeta', $option );
                        $data = array('offset' => $temp_key);
                        return $data;
                    }
                    $meta_saved = get_user_meta( $user_id, $field['meta_key'] );
                    if ( !empty( $meta_saved ) ) {
                        foreach ( $meta_saved as $key => $value ) {
                            if ( !is_array( $value ) ) {
                                $value_check = array();
                            } else {
                                $value_check = $value;
                            }
                            $set_value = array();
                            foreach ( $field['data']['options'] as $option_id => $option_data ) {
                                if ( !isset( $value_check[$option_id] ) ) {
                                    $set_value[$option_id] = 0;
                                }
                            }
                            $updated_value = $value_check + $set_value;
                            update_user_meta( $user_id, $field['meta_key'],
                                    $updated_value, $value );
                        }
                    }
                    unset( $users[$temp_key] );
                    $count++;
                }
                unset( $option[$field['meta_key']][$action] );
                update_option( 'wpcf_checkboxes_migration_usermeta', $option );
                return $users;
            }
        }
        return false;
    }
    $option = get_option( 'wpcf_checkboxes_migration', array() );
    $meta_key = wpcf_types_get_meta_prefix( $field ) . $field['id'];
    if ( empty( $option[$action] ) ) {
        $posts = wpcf_admin_fields_checkboxes_migrate_empty_check( $field,
                $action . '_check' );
    } else {
        $posts = $option[$action];
    }

    if ( !empty( $posts ) ) {
        if ( $action == 'do_not_save' ) {
            $count = 0;
            foreach ( $posts as $temp_key => $post_id ) {
                if ( $count == 1000 ) {
                    $option[$action] = $posts;
                    update_option( 'wpcf_checkboxes_migration', $option );
                    $data = array('offset' => $temp_key);
                    return $data;
                }
                $meta_saved = get_post_meta( $post_id, $meta_key );
                if ( !empty( $meta_saved ) ) {
                    foreach ( $meta_saved as $key => $value ) {
                        if ( !is_array( $value ) ) {
                            $value_check = array();
                        } else {
                            $value_check = $value;
                        }
                        foreach ( $field['data']['options'] as $option_id =>
                                    $option_data ) {
                            if ( isset( $value_check[$option_id] )  && $value_check[$option_id] == '0' ) {
                                unset( $value_check[$option_id] );
                            }
                        }
                        update_post_meta( $post_id, $meta_key, $value_check, $value );
                    }
                }
                unset( $posts[$temp_key] );
                $count++;
            }
            unset( $option[$action] );
            update_option( 'wpcf_checkboxes_migration', $option );
            return $posts;
        } else if ( $action == 'save' ) {
            $count = 0;
            foreach ( $posts as $temp_key => $post_id ) {
                if ( $count == 1000 ) {
                    $option[$action] = $posts;
                    update_option( 'wpcf_checkboxes_migration', $option );
                    $data = array('offset' => $temp_key);
                    return $data;
                }
                $meta_saved = get_post_meta( $post_id, $meta_key );
                if ( !empty( $meta_saved ) ) {
                    foreach ( $meta_saved as $key => $value ) {
                        if ( !is_array( $value ) ) {
                            $value_check = array();
                        } else {
                            $value_check = $value;
                        }
                        $set_value = array();
                        foreach ( $field['data']['options'] as $option_id =>
                                    $option_data ) {
                            if ( !isset( $value_check[$option_id] ) ) {
                                $set_value[$option_id] = 0;
                            }
                        }
                        $updated_value = $value_check + $set_value;
                        update_post_meta( $post_id, $meta_key, $updated_value, $value );
                    }
                }
                unset( $posts[$temp_key] );
                $count++;
            }
            unset( $option[$action] );
            update_option( 'wpcf_checkboxes_migration', $option );
            return $posts;
        }
    }
    return false;
}

function wpcf_admin_fields_form_fix_styles()
{
    $suffix = SCRIPT_DEBUG ? '' : '.min';
    wp_enqueue_style(
        'wpcf-dashicons',
        site_url( "/wp-includes/css/dashicons$suffix.css" )
    );
}
