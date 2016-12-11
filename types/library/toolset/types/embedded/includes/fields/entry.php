<?php

/**
 * Register data (called automatically).
 * 
 * @return array field definition
 */
function wpcf_fields_entry() {
    return array(
        'id' => 'wpcf-entry',
        'title' => __( 'Entry', 'wpcf' ),
        'description' => __( 'Entry', 'wpcf' ),
        'validate' => array(
            'required' => array(
                'form-settings' => include( dirname( __FILE__ ) . '/patterns/validate/form-settings/required.php' )
            )
        ),
        'font-awesome' => 'file-text-o',
    );
}

/**
 * Meta box form.
 * 
 * @param type $field
 * @return string 
 */
function wpcf_fields_entry_meta_box_form( $field ) {
    $form = array();
    $form['name'] = array(
        '#type' => 'textfield',
        '#name' => 'wpcf[' . $field['slug'] . ']',
    );
    return $form;
}

/**
 * Editor callback form.
 */
function wpcf_fields_entry_editor_callback($field, $settings)
{
    $post_type  = get_post_type_object( $field['data']['post_type'] );
    if ( null == $post_type ) {
        return;
    }

    $data = wpcf_fields_entry_get_options();

    foreach( $data['options'] as $key => $field_data ) {
        if ( wpcf_fields_entry_check_is_available( $field['data']['post_type'], $field_data ) ) {
            continue;
        }
        unset($data['options'][$key]);
    }

    return array(
        'supports' => array('style'),
        'tabs' => array(
            'display' => array(
                'menu_title' => __( 'Display options', 'wpcf' ),
                'title' => __( 'Display options for this field:', 'wpcf' ),
                'content' => WPCF_Loader::template( 'editor-modal-entry', $data),
            )
        ),
    );
}

/**
 * Editor callback form submit.
 */
function wpcf_fields_entry_editor_submit($data, $field, $context)
{
    $shortcode = '';
    if (
        isset($data['display'])
        && preg_match('/^post-[\-a-z]+$/', $data['display'])
    ) {
        $add = sprintf(' display="%s"', $data['display']);
        if ( $context == 'usermeta' ) {
            $shortcode = wpcf_usermeta_get_shortcode( $field, $add );
        } else {
            $shortcode = wpcf_fields_get_shortcode( $field, $add );
        }
    }
    return $shortcode;
}

/**
 * View function.
 *
 * @param type $params
 */
function wpcf_fields_entry_view($params)
{
    if (
        !isset($params['field'])
        || !isset($params['display'])
        || !isset($params['field_value'])
        || empty($params['field_value'])
    ) {
        return '__wpcf_skip_empty';
    }
    /**
     * use cache
     */
    static $wpcf_fields_entry_view_cache;
    if (
        isset($wpcf_fields_entry_view_cache[$params['field']['id']])
        && isset($wpcf_fields_entry_view_cache[$params['field']['id']][$params['display']])
    ) {
        return $wpcf_fields_entry_view_cache[$params['field']['id']][$params['display']];
    }
    $post = get_post($params['field_value']);
    $data = wpcf_fields_entry_get_options();
    foreach( $data['options'] as $key => $field_data ) {
        if ( wpcf_fields_entry_check_is_available( $post->post_type, $field_data ) ) {
            $value = '__wpcf_skip_empty';
            switch( $key ) {
            case 'post-title':
                $value = apply_filters('post_title', $post->post_title);
                break;
            case 'post-link':
                $value = sprintf(
                    '<a href="%s" title="%s">%s</a>',
                    esc_attr(get_permalink($post)),
                    esc_attr(apply_filters('post_title', $post->post_title)),
                    apply_filters('post_title', $post->post_title)
                );
                break;
            case 'post-url':
                $value = get_permalink($post);
                break;
            case 'post-body':
                $value = apply_filters('the_content', $post->post_content);
                break;
            case 'post-excerpt':
                $value = apply_filters('the_excerpt', $post->post_excerpt);
                break;
            case 'post-date':
                $value = get_the_date(null, $post->ID);
                break;
            case 'post-author':
                $value = get_the_author_meta('display_name', $post->author_id);
                break;
            case 'post-featured-image':
                $value = get_the_post_thumbnail($post->ID);
                break;
            case 'post-slug':
                $value = $post->post_name;
                break;
            case 'post-type':
                $value = $post->post_type;
                break;
            case 'post-status':
                $value = $post->post_status;
                break;
            case 'post-class':
                $value = implode(' ', get_post_class('', $post->ID));
                break;
            case 'post-edit-link':
                $value = get_edit_post_link($post->ID);
                break;
            default:
                $value = $key;
            }
            $wpcf_fields_entry_view_cache[$params['field_value']][$key] = $value;
        } else {
            d(array($post->post_type, $key, $field_data));
           $wpcf_fields_entry_view_cache[$params['field_value']][$key] = '__wpcf_skip_empty';
        }
    }
    return $wpcf_fields_entry_view_cache[$params['field_value']][$params['display']];
}

function wpcf_fields_entry_check_is_available($post_type, $field)
{
    /**
     * remove some option if certain post type do not supports it
     */
    if( isset( $field['support_field'] ) ) {
        if ( !post_type_supports( $post_type, $field['support_field'])) {
            return false;
        }
    }
    /**
     * remove some option if certain post type definition not match
     */
    if( isset( $field['post_type'] ) ) {
        $post_type = get_post_type_object($post_type);
        if (
            !isset($post_type->$field['post_type'])
            || empty($post_type->$field['post_type'])
        ) {
            return false;
        }
    }
    return true;
}

function wpcf_fields_entry_get_options()
{
    return array(
        'options' => array(
            'post-title' => array(
                'support_field' => 'title',
                'label' => __('Post title', 'wpcf'),
            ),
            'post-link' => array(
                'support_field' => 'title',
                'post_type' => 'public',
                'label' => __('Post title with a link', 'wpcf'),
            ),
            'post-url' => array(
                'post_type' => 'public',
                'label' => __('Post URL', 'wpcf'),
            ),
            'post-body' => array(
                'support_field' => 'editor',
                'label' => __('Post body', 'wpcf'),
            ),
            'post-excerpt' => array(
                'support_field' => 'excerpt',
                'label' => __('Post excerpt', 'wpcf'),
            ),
            'post-date' => array(
                'label' => __('Post date', 'wpcf'),
            ),
            'post-author' => array(
                'support_field' => 'author',
                'label' => __('Post author', 'wpcf'),
            ),
            'post-featured-image' => array(
                'support_field' => 'thumbnail',
                'label' => __('Post featured image', 'wpcf'),
            ),
            'post-id' => array(
                'label' => __('Post ID', 'wpcf'),
            ),
            'post-slug' => array(
                'label' => __('Post slug', 'wpcf'),
            ),
            'post-type' => array(
                'label' => __('Post type', 'wpcf'),
            ),
            'post-format' => array(
                'support_field' => 'post-formats',
                'label' => __('Post format', 'wpcf'),
            ),
            'post-status' => array(
                'label' => __('Post status', 'wpcf'),
            ),
            'post-comments-number' => array(
                'support_field' => 'comments',
                'label' => __('Post comments number', 'wpcf'),
            ),
            'post-class' => array(
                'label' => __('Post class', 'wpcf'),
            ),
            'post-edit-link' => array(
                'label' => __('Post edit link', 'wpcf'),
            ),
        ),
        'default' => 'post-title',
    );
}

