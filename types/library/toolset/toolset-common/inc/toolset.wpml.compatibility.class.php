<?php

/**
* ########################################
* Common WPML compatibility
* ########################################
*/

if ( defined( 'WPT_WPML_COMPATIBILITY' ) ) {
    return; 
}

define( 'WPT_WPML_COMPATIBILITY', true );

if ( ! class_exists( 'Toolset_WPML_Compatibility' ) ) {
	class Toolset_WPML_Compatibility {
		
		function __construct() {
			add_action( 'init', array( $this, 'stub_wpml_add_shortcode' ), 100 );
		}
		
		// @todo check in another way, against a global is not our best option
		// Check with Andrea
		function stub_wpml_add_shortcode() {
			global $WPML_String_Translation;
			if ( ! isset( $WPML_String_Translation ) ) {
				// WPML string translation is not active
				// Add our own do nothing shortcode
				add_shortcode( 'wpml-string', array( $this, 'stub_wpml_string_shortcode' ) );

			}
		}
		
		function stub_wpml_string_shortcode( $atts, $value ) {
			// return un-processed.
			return do_shortcode($value);
		}

	}

}