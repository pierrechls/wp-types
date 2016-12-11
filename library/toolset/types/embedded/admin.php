<?php
/**
 *
 *
 */
require_once(WPCF_EMBEDDED_TOOLSET_ABSPATH . '/toolset-common/visual-editor/editor-addon.class.php');
require_once WPCF_EMBEDDED_ABSPATH . '/includes/post-relationship.php';

if ( defined( 'DOING_AJAX' ) ) {
    require_once WPCF_EMBEDDED_INC_ABSPATH . '/ajax.php';
    add_action( 'wp_ajax_wpcf_ajax', 'wpcf_ajax_embedded' );
}

/**
 * admin_init hook.
 */
function wpcf_embedded_admin_init_hook() {
    // Add callbacks for post edit pages
    add_action( 'load-post.php', 'wpcf_admin_edit_screen_load_hook' );
    add_action( 'load-post-new.php', 'wpcf_admin_edit_screen_load_hook' );

    // Meta boxes hook
    add_action( 'add_meta_boxes', 'wpcf_admin_add_meta_boxes', 10, 2 );

    // Add callback for 'media-upload.php'
    add_filter( 'get_media_item_args', 'wpcf_get_media_item_args_filter' );

    // Add save_post callback
    add_action( 'save_post', 'wpcf_admin_save_post_hook', 10, 2 );

    // Add Media callback
    add_action( 'add_attachment', 'wpcf_admin_save_attachment_hook', 10 );
    add_action( 'add_attachment', 'wpcf_admin_add_attachment_hook', 10 );
    add_action( 'edit_attachment', 'wpcf_admin_save_attachment_hook', 10 );

    // Render messages
    wpcf_show_admin_messages();

    // Render JS settings
    add_action( 'admin_head', 'wpcf_admin_render_js_settings' );

    // Media insert code
    if ( (isset( $_GET['context'] ) && $_GET['context'] == 'wpcf-fields-media-insert')
            || (isset( $_SERVER['HTTP_REFERER'] )
            && strpos( $_SERVER['HTTP_REFERER'],
                    'context=wpcf-fields-media-insert' ) !== false)
    ) {
        require_once WPCF_EMBEDDED_INC_ABSPATH . '/fields/file.php';
        // Add types button
        add_filter( 'attachment_fields_to_edit', 'wpcf_fields_file_attachment_fields_to_edit_filter', PHP_INT_MAX, 2 );
        // Filter media TABs
        add_filter( 'media_upload_tabs', 'wpcf_fields_file_media_upload_tabs_filter' );
    }

    register_post_type( TYPES_CUSTOM_FIELD_GROUP_CPT_NAME,
            array(
        'public' => false,
        'label' => 'Types Groups',
        'can_export' => false,
            )
    );
    register_post_type( TYPES_USER_META_FIELD_GROUP_CPT_NAME,
            array(
        'public' => false,
        'label' => 'Types User Groups',
        'can_export' => false,
            )
    );

	register_post_type(
		TYPES_TERM_META_FIELD_GROUP_CPT_NAME,
		array(
			'public' => false,
			'label' => 'Types Term Groups',
			'can_export' => false,
		)
	);

    add_filter( 'icl_custom_fields_to_be_copied',
            'wpcf_custom_fields_to_be_copied', 10, 2 );

    // WPML editor filters
    add_filter( 'icl_editor_cf_name', 'wpcf_icl_editor_cf_name_filter' );
    add_filter( 'icl_editor_cf_description',
            'wpcf_icl_editor_cf_description_filter', 10, 2 );
    add_filter( 'icl_editor_cf_style', 'wpcf_icl_editor_cf_style_filter', 10, 2 );
    // Initialize translations
    if ( function_exists( 'icl_register_string' )
            && defined( 'WPML_ST_VERSION' )
            && !get_option( 'wpcf_strings_translation_initialized', false ) ) {
        wpcf_admin_bulk_string_translation();
        update_option( 'wpcf_strings_translation_initialized', 1 );
    }
}

/**
 * Add meta boxes hook.
 * 
 * @param type $post_type
 * @param type $post
 */
function wpcf_admin_add_meta_boxes( $post_type, $post ) {
    require_once WPCF_EMBEDDED_INC_ABSPATH . '/fields.php';
    require_once WPCF_EMBEDDED_INC_ABSPATH . '/fields-post.php';

    wpcf_add_meta_boxes( $post_type, $post );
}

/**
 * save_post hook.
 * 
 * @param type $post_ID
 * @param type $post 
 */
function wpcf_admin_save_post_hook( $post_ID, $post ) {
    require_once WPCF_EMBEDDED_INC_ABSPATH . '/fields.php';
    require_once WPCF_EMBEDDED_INC_ABSPATH . '/fields-post.php';
    wpcf_admin_post_save_post_hook( $post_ID, $post );
}

/**
 * Save attachment hook.
 * 
 * @param type $attachment_id
 */
function wpcf_admin_add_attachment_hook( $attachment_id )
{
    $post = get_post( $attachment_id );
    wpcf_admin_post_add_attachment_hook( $attachment_id, $post );
}

/**
 * Save attachment hook.
 * 
 * @param type $attachment_id
 */
function wpcf_admin_save_attachment_hook( $attachment_id ) {
    $post = get_post( $attachment_id );
    wpcf_admin_save_post_hook( $attachment_id, $post );
}

/**
 * Triggers post procceses.
 */
function wpcf_admin_edit_screen_load_hook() {
    require_once WPCF_EMBEDDED_INC_ABSPATH . '/fields-post.php';
    global $wpcf;
    $wpcf->post = wpcf_admin_get_edited_post();
    wpcf_admin_post_init( $wpcf->post );
}

/**
 * Add styles to admin fields groups
 */
function wpcf_admin_fields_postfields_styles(){

    require_once WPCF_EMBEDDED_INC_ABSPATH . '/fields.php';
    require_once WPCF_EMBEDDED_INC_ABSPATH . '/fields-post.php';

//    $groups = wpcf_admin_fields_get_groups();
    $groups = wpcf_admin_post_get_post_groups_fields( wpcf_admin_get_edited_post() );

    if ( !empty( $groups ) ) {
		echo '<style type="text/css">';
        foreach ( $groups as $group ) {
            echo str_replace( "}", "}\n",
                    wpcf_admin_get_groups_admin_styles_by_group( $group['id'] ) );
        }
		echo '</style>';
    }
}

/**
 * Add styles to userfields groups
 */
function wpcf_admin_fields_userfields_styles(){

    require_once WPCF_EMBEDDED_INC_ABSPATH . '/usermeta-post.php';


//    $groups = wpcf_admin_fields_get_groups();
    $groups = wpcf_admin_usermeta_get_groups_fields();

    if ( !empty( $groups ) ) {
		echo '<style type="text/css">';
        foreach ( $groups as $key => $group ) {
            echo str_replace( "}", "}\n",
                    wpcf_admin_get_groups_admin_styles_by_group( $group['id'] ) );
        }
		echo '</style>';
    }
}

/** @noinspection PhpUndefinedClassInspection */

/**
 * Initiates/returns specific form.
 * 
 * @staticvar array $wpcf_forms
 * @param string $id
 * @param array $form
 * @return Enlimbo_Forms_Wpcf
 * @deprecated Please avoid using Enlimbo forms for new code. Consider using Twig templates instead.
 */
function wpcf_form( $id, $form = array() ) {
	static $wpcf_forms = array();

	if ( isset( $wpcf_forms[ $id ] ) ) {
		return $wpcf_forms[ $id ];
	}

	require_once WPCF_EMBEDDED_ABSPATH . '/classes/forms.php';
	/** @noinspection PhpUndefinedClassInspection */

	$new_form = new Enlimbo_Forms_Wpcf();
	$new_form->autoHandle( $id, $form );

	$wpcf_forms[ $id ] = $new_form;

	return $wpcf_forms[ $id ];
}

/**
 * Renders form elements.
 * 
 * @staticvar string $form
 * @param array $elements
 * @return array 
 * @deprecated Please avoid using Enlimbo forms for new code. Consider using Twig templates instead.
 */
function wpcf_form_simple( $elements ) {
    static $form = NULL;
    require_once WPCF_EMBEDDED_ABSPATH . '/classes/forms.php';
    if ( is_null( $form ) ) {
        $form = new Enlimbo_Forms_Wpcf();
    }
    return $form->renderElements( $elements );
}

/**
 * Validates form elements (simple).
 * 
 * @staticvar string $form
 * @param type $elements
 * @return type 
 * @deprecated Please avoid using Enlimbo forms for new code. Consider using Twig templates instead.
 */
function wpcf_form_simple_validate( &$elements ) {
    static $form = NULL;
    require_once WPCF_EMBEDDED_ABSPATH . '/classes/forms.php';
    if ( is_null( $form ) ) {
        $form = new Enlimbo_Forms_Wpcf();
    }
    $form->validate( $elements );
    return $form;
}

/**
 * Stores JS validation rules.
 * 
 * @staticvar array $validation
 * @param type $element
 * @return array
 * @deprecated Please avoid using Enlimbo forms for new code. Consider using Twig templates instead. 
 */
function wpcf_form_add_js_validation( $element ) {
    static $validation = array();
    if ( $element == 'get' ) {
        $temp = $validation;
        $validation = array();
        return $temp;
    }
    $validation[$element['#id']] = $element;
}

/**
 * Renders JS validation rules.
 * 
 * @global type $wpcf
 * @param type $selector Can be CSS class or element ID
 * @param type $echo
 * @return string
 * @deprecated Please avoid using Enlimbo forms for new code. Consider using Twig templates instead.
 */
function wpcf_form_render_js_validation( $selector = '.wpcf-form-validate',
        $echo = true ) {
    $output = WPCF_Validation::renderJsonData( $selector );
    if ( $echo ) {
        echo $output;
    }
    return $output;
}

/**
 * wpcf_custom_fields_to_be_copied
 *
 * Hook the copy custom fields from WPML and remove any of the fields
 * that wpcf will copy.
 */
function wpcf_custom_fields_to_be_copied( $copied_fields, $original_post_id ) {

    // see if this is one of our fields.
    $groups = wpcf_admin_post_get_post_groups_fields( get_post( $original_post_id ) );

    foreach ( $copied_fields as $id => $copied_field ) {
        foreach ( $groups as $group ) {
            if ( isset( $group['fields'] ) && is_array( $group['fields'] ) ) {
                foreach ( $group['fields'] as $field ) {
                    if ( $copied_field == wpcf_types_get_meta_prefix( $field ) . $field['slug'] ) {
                        unset( $copied_fields[$id] );
                    }
                }
            }
        }
    }
    return $copied_fields;
}

/**
 * Holds validation messages.
 * 
 * @param type $method
 * @return type 
 */
function wpcf_admin_validation_messages( $method = false, $sprintf = '' ) {
    $messages = array(
        'required' => __( 'This field is required.', 'wpcf' ),
        'email' => __( 'Please enter a valid email address.', 'wpcf' ),
        'url' => __( 'Please enter a valid URL address.', 'wpcf' ),
        'date' => __( 'Please enter a valid date.', 'wpcf' ),
        'digits' => __( 'Please enter numeric data.', 'wpcf' ),
        'number' => __( 'Please enter numeric data.', 'wpcf' ),
        'alphanumeric' => __( 'Letters, numbers, spaces or underscores only please.', 'wpcf' ),
        'nospecialchars' => __( 'Letters, numbers, spaces, underscores and dashes only please.', 'wpcf' ),
        'rewriteslug' => __( 'Letters, numbers, slashes, underscores and dashes only please.', 'wpcf' ),
        'negativeTimestamp' => __( 'Please enter a date after 1 January 1970.', 'wpcf' ),
        'maxlength' => sprintf( __( 'Maximum of %s characters exceeded.', 'wpcf' ), strval( $sprintf ) ),
        'minlength' => sprintf( __( 'Minimum of %s characters has not been reached.', 'wpcf' ), strval( $sprintf ) ),
        /**
         * see 
         * https://support.skype.com/en/faq/FA10858/what-is-a-skype-name-and-how-do-i-find-mine
         */
        'skype' => __( 'Letters, numbers, dashes, underscores, commas and periods only please.', 'wpcf' ),
    );
    if ( $method ) {
        return isset( $messages[$method] ) ? $messages[$method] : '';
    }
    return $messages;
}


/**
 * Sanitize admin notice.
 *
 * @param string $message
 * @return string
 */
function wpcf_admin_message_sanitize( $message )
{
    $allowed_tags = array(
        'a' => array(
            'href' => array(),
            'title' => array()
        ),
        'br' => array(),
        'b' => array(),
        'div' => array(),
        'em' => array(),
        'i' => array(),
        'p' => array(),
        'strong' => array(),
    );
    $message = wp_kses($message, $allowed_tags);
    return stripslashes(html_entity_decode($message, ENT_QUOTES));
}

/**
 * Adds admin notice.
 *
 * @param string $message
 * @param string $class
 * @param string $mode 'action'|'echo'
 */
function wpcf_admin_message( $message, $class = 'updated', $mode = 'action' )
{
    if ( 'action' == $mode ) {
        add_action( 'admin_notices',
            create_function( '$a=1, $class=\'' . $class . '\', $message=\''
                    . htmlentities( $message, ENT_QUOTES ) . '\'',
                        '$screen = get_current_screen(); if (!$screen->is_network) echo "<div class=\"message $class\"><p>" . wpcf_admin_message_sanitize ($message) . "</p></div>";' ) );
    } elseif ( 'echo' == $mode ) {
        printf(
            '<div class="message %s is-dismissible"><p>%s</p> <button type="button" class="notice-dismiss">
            <span class="screen-reader-text">
               '. __( 'Dismiss this notice.' ) .'
            </span>
        </button></div>',
            $class,
            wpcf_admin_message_sanitize($message)
        );
    }
}

/**
 * Shows stored messages.
 */
function wpcf_show_admin_messages($mode = 'action')
{
    $messages = get_option( 'wpcf-messages', array() );
    $messages_for_user = isset( $messages[get_current_user_id()] ) ? $messages[get_current_user_id()] : array();
    $dismissed = get_option( 'wpcf_dismissed_messages', array() );
    if ( !empty( $messages_for_user ) && is_array( $messages_for_user ) ) {
        foreach( $messages_for_user as $message_id => $message ) {
            if( ! in_array( $message['keep_id'], $dismissed ) ) {
                wpcf_admin_message( $message['message'], $message['class'], $mode );
            }
            if( empty( $message['keep_id'] )
                || in_array( $message['keep_id'], $dismissed )
            ) {
                unset( $messages[ get_current_user_id() ][ $message_id ] );
            }
        }
    }
    update_option( 'wpcf-messages', $messages );
}

/**
 * Stores admin notices if redirection is performed.
 * 
 * @param string $message
 * @param string $class
 */
function wpcf_admin_message_store( $message, $class = 'updated', $keep_id = false )
{
    /**
     * Allow to store or note
     *
     * Filter allow to turn off storing messages in Types
     *
     * @since 1.6.6
     *
     * @param boolean $var default value is true to show messages
     */
    if (!apply_filters('wpcf_admin_message_store', true) ) {
        return;
    }
    $messages = get_option( 'wpcf-messages', array() );
    $messages[get_current_user_id()][md5( $message )] = array(
        'message' => $message,
        'class' => $class,
        'keep_id' => $keep_id ? $keep_id : false,
    );
    update_option( 'wpcf-messages', $messages );
}

/**
 * Admin notice with dismiss button.
 * 
 * @param type $ID
 * @param string $message
 * @param type $store
 * @return boolean 
 */
function wpcf_admin_message_dismiss( $ID, $message, $store = true ) {
    $dismissed = get_option( 'wpcf_dismissed_messages', array() );
    if ( in_array( $ID, $dismissed ) ) {
        return false;
    }
    $message = $message . '<div style="float:right; margin:-15px 0 0 15px;"><a onclick="jQuery(this).parent().parent().fadeOut();jQuery.get(\''
            . admin_url( 'admin-ajax.php?action=wpcf_ajax&amp;wpcf_action=dismiss_message&amp;id='
                    . $ID . '&amp;_wpnonce=' . wp_create_nonce( 'dismiss_message' ) ) . '\');return false;"'
            . 'class="button-secondary" href="javascript:void(0);">'
            . __( 'Dismiss', 'wpcf' ) . '</a></div>';
    if ( $store ) {
        wpcf_admin_message_store( $message, 'updated', $ID );
    } else {
        wpcf_admin_message( $message );
    }
}

/**
 * Checks if message is dismissed.
 * 
 * @param type $message_id
 * @return boolean
 */
function wpcf_message_is_dismissed( $message_id ) {
    return in_array( $message_id,
                    (array) get_option( '_wpcf_dismissed_messages', array() ) );
}

/**
 * Adds dismissed message to record.
 * 
 * @param type $ID 
 */
function wpcf_admin_message_set_dismissed( $ID ) {
    $messages = get_option( 'wpcf_dismissed_messages', array() );
    if ( !in_array( $ID, $messages ) ) {
        $messages[] = $ID;
        update_option( 'wpcf_dismissed_messages', $messages );
    }
}

/**
 * Removes dismissed message from record.
 * 
 * @param type $ID 
 */
function wpcf_admin_message_restore_dismissed( $ID ) {
    $messages = get_option( 'wpcf_dismissed_messages', array() );
    $key = array_search( $ID, $messages );
    if ( $key !== false ) {
        unset( $messages[$key] );
        update_option( 'wpcf_dismissed_messages', $messages );
    }
}

/**
 * Saves cookie.
 * 
 * @param type $data 
 */
function wpcf_cookies_add( $data ) {
    if ( isset( $_COOKIE['wpcf'] ) ) {
        $data = array_merge( (array) $_COOKIE['wpcf'], $data );
    }
    setcookie( 'wpcf', $data, time() + $lifetime, COOKIEPATH, COOKIE_DOMAIN );
}

/**
 * Renders page head.
 * 
 * @see WPCF_Template::ajax_header()
 * @global type $pagenow
 * @param type $title
 */
function wpcf_admin_ajax_head( $title = '' ) {

    /*
     * Since Types 1.2 and WP 3.5
     * AJAX head is rendered differently
     */
    global $wp_version;
    if ( version_compare( $wp_version, '3.4', '>' ) ) {
        // WP Header
        include WPCF_EMBEDDED_ABSPATH . '/includes/ajax/admin-header.php';
        return true;
    }

    global $pagenow;
    $hook_suffix = $pagenow;

    ?>
    <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
    <html xmlns="http://www.w3.org/1999/xhtml" <?php do_action( 'admin_xml_ns' ); ?> <?php language_attributes(); ?>>
        <head>
            <meta http-equiv="Content-Type" content="<?php bloginfo( 'html_type' ); ?>; charset=<?php echo get_option( 'blog_charset' ); ?>" />
            <title><?php echo $title; ?></title>
            <?php
            if ( wpcf_compare_wp_version( '3.2.1', '<=' ) ) {
                wp_admin_css( 'global' );
            }
            wp_admin_css();
            wp_admin_css( 'colors' );
            wp_admin_css( 'ie' );
//            do_action('admin_enqueue_scripts', $hook_suffix);
            do_action( "admin_print_styles-$hook_suffix" );
            do_action( 'admin_print_styles' );
//            do_action("admin_print_scripts-$hook_suffix");
            do_action( 'admin_print_scripts' );
//            do_action("admin_head-$hook_suffix");
//            do_action('admin_head');
            do_action( 'admin_head_wpcf_ajax' );

            ?>
            <style type="text/css">
                html { height: auto; }
            </style>

            <script type="text/javascript">
                // <![CDATA[
                jQuery(document).ready(function(){
                    // Position the help link in the title bar.
                    var title = jQuery('#TB_closeAjaxWindow', window.parent.document);
                    if (title.length != 0) {
                        title.after(jQuery('.wpcf-help-link'));
                    }
                });
                // ]]>
            </script>

            <link rel="stylesheet" href="<?php echo WPCF_EMBEDDED_RES_RELPATH . '/css/basic.css'; ?>" type="text/css" media="all" />

        </head>
        <body style="padding: 20px;">
            <?php
        }

        /**
         * Renders page footer
         * 
         * @see WPCF_Template::ajax_footer()
         */
        function wpcf_admin_ajax_footer() {

            /*
             * Since Types 1.2 and WP 3.5
             * AJAX footer is rendered differently
             */
            global $wp_version, $wpcf;
            if ( version_compare( $wp_version, '3.4', '>' ) ) {
                // WP Footer
                do_action( 'admin_footer_wpcf_ajax' );
                include WPCF_EMBEDDED_ABSPATH . '/includes/ajax/admin-footer.php';
                return true;
            }

            global $pagenow;
            do_action( 'admin_footer_wpcf_ajax' );
//    do_action('admin_footer', '');
//    do_action('admin_print_footer_scripts');
//    do_action("admin_footer-" . $pagenow);

            ?>
        </body>
    </html>

    <?php
        }

/**
 * Renders JS settings.
 * 
 * @return type 
 */
function wpcf_admin_render_js_settings() {
    $settings = wpcf_admin_add_js_settings( 'get' );
    if ( empty( $settings ) ) {
        return '';
    }

    ?>
    <script type="text/javascript">
        //<![CDATA[
    <?php
    foreach ( $settings as $id => $setting ) {
        if ( is_string( $setting ) ) {
            $setting = trim( $setting, '\'' );
            $setting = "'" . esc_js( $setting ) . "'";
        } else {
            $setting = intval( $setting );
        }
        echo 'var ' . $id . ' = ' . $setting . ';' . "\r\n";
    }

    ?>
        //]]>
    </script>
    <?php
}

/**
 * wpcf_get_fields
 *
 * returns the fields handled by types
 *
 */
function wpcf_get_post_meta_field_names() {
    require_once WPCF_EMBEDDED_INC_ABSPATH . '/fields.php';
    $fields = wpcf_admin_fields_get_fields();

    $field_names = array();
    foreach ( $fields as $field ) {
        $field_names[] = wpcf_types_get_meta_prefix( $field ) . $field['slug'];
    }

    return $field_names;
}

/**
 * Forces 'Insert into post' link when called from our WYSIWYG.
 * 
 * @param array $args
 * @return boolean 
 */
function wpcf_get_media_item_args_filter( $args ) {
    if ( strpos( $_SERVER['SCRIPT_NAME'], '/media-upload.php' ) === false ) {
        return $args;
    }
    if ( !empty( $_COOKIE['wpcfActiveEditor'] )
            && strpos( $_COOKIE['wpcfActiveEditor'], 'wpcf-wysiwyg-' ) !== false ) {
        $args['send'] = true;
    }
    return $args;
}

/**
 * Gets post.
 * 
 * @return type 
 */
function wpcf_admin_get_edited_post() {
    // Global $post_ID holds post IDs for new posts too.
    global $post_ID;
    if ( !empty( $post_ID ) ) {
        $post_id = $post_ID;
    } else if ( isset( $_GET['post'] ) ) {
        $post_id = (int) $_GET['post'];
    } else if ( isset( $_POST['post_ID'] ) ) {
        $post_id = (int) $_POST['post_ID'];
    } else {
        $post_id = 0;
    }
    if ( $post_id ) {
        return get_post( $post_id );
    } else {
        return array();
    }
}

/**
* wpcf_admin_get_current_edited_post
*
* Wrapper for wpcf_admin_get_edited_post returning nul instead of an empty array when the current post can not be guessed.
*
* Used on the Views integration at /library/tolset/types/embedded/classes/wpviews.php
* when calculating the current post so we can display the right fields in the Fields and Views dialog.
*
* @since 2.2
*/

add_filter( 'wpcf_filter_wpcf_admin_get_current_edited_post', 'wpcf_admin_get_current_edited_post' );

function wpcf_admin_get_current_edited_post( $current_post = null ) {
	$current_post = wpcf_admin_get_edited_post();
	if ( empty( $current_post ) ) {
		return null;
	}
	return $current_post;
}

/**
 * Gets post type.
 * 
 * @param type $post
 * @return boolean 
 */
function wpcf_admin_get_edited_post_type( $post = null ) {
    if ( !empty( $post->ID ) ) {
        $post_type = get_post_type( $post );
    } else {
        if ( !isset( $_GET['post_type'] ) ) {
            $post_type = 'post';
        } else if ( in_array( $_GET['post_type'],
                        get_post_types( array('show_ui' => true) ) ) ) {
            $post_type = sanitize_text_field( $_GET['post_type'] );
        } else {
            $post_type = 'post';
        }
    }
    return $post_type;
}
