<?php


if( ! class_exists( 'Toolset_User_Editors_Editor_Screen_Abstract', false ) )
	require_once( TOOLSET_COMMON_PATH . '/user-editors/editor/screen/abstract.php' );

class Toolset_User_Editors_Editor_Screen_Visual_Composer_Frontend
	extends Toolset_User_Editors_Editor_Screen_Abstract {


	public function __construct() {
		// make sure all vc shortcodes are loaded (needed for ajax pagination)
		if ( method_exists( 'WPBMap', 'addAllMappedShortcodes' ) )
			WPBMap::addAllMappedShortcodes();

		add_action( 'the_content', array( $this, 'render_custom_css' ) );

		// this adds the [Fields and Views] to editor of visual composers text element
		if( array_key_exists( 'action', $_POST ) && $_POST['action'] == 'vc_edit_form' ) {
			add_filter( 'wpv_filter_dialog_for_editors_requires_post', '__return_false' );
		}
	}

	/**
	 * Visual Composer stores custom css as postmeta.
	 * We need to check if current post has content_template and if so apply the custom css.
	 * Hooked to the_content
	 *
	 * @param $content
	 * @return mixed
	 */
	public function render_custom_css( $content ) {
		if(
			method_exists( 'Vc_Base', 'addPageCustomCss' )
			&& method_exists( 'Vc_Base', 'addShortcodesCustomCss' )
		) {
			$content_template = get_post_meta( get_the_ID(), '_views_template', true );

			if( $content_template && ! isset( $this->log_rendered_css[$content_template] ) ) {
				$vcbase = new Vc_Base();
				$vcbase->addPageCustomCss( $content_template );
				$vcbase->addShortcodesCustomCss( $content_template );
				$this->log_rendered_css[$content_template] = true;
			}
		}
		return $content;
	}

}