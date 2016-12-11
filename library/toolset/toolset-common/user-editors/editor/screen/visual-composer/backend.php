<?php

if( ! class_exists( 'Toolset_User_Editors_Editor_Screen_Abstract', false ) )
	require_once( TOOLSET_COMMON_PATH . '/user-editors/editor/screen/abstract.php' );

class Toolset_User_Editors_Editor_Screen_Visual_Composer_Backend
	extends Toolset_User_Editors_Editor_Screen_Abstract {

	private $post;
	public $editor;

	public function isActive() {
		if( ! $this->setMediumAsPost() )
			return false;

		// check for functions used
		if(
			! function_exists( 'vc_user_access' )
			|| ! class_exists( 'Vc_Shortcodes_Manager' )
			|| ! method_exists( 'Vc_Manager', 'backendEditor' )
		)
			return false;

		// don't show VC if user role is not allowed to use the backend editor
		if( ! vc_user_access()->part( 'backend_editor' )->can()->get() )
			return false;

		$this->action();
		return true;
	}

	private function action() {
		add_action( 'admin_init', array( $this, '_actionSetup' ) );

		add_action( 'admin_print_scripts', array( &$this->editor, 'enqueueEditorScripts' ) );
		add_action( 'admin_print_scripts', array( $this, 'print_scripts' ) );
		add_action( 'admin_print_scripts', array( Vc_Shortcodes_Manager::getInstance(), 'buildShortcodesAssets' ), 1 );

		$this->medium->setHtmlEditorBackend( array( $this, 'htmlOutput' ) );
	}

	/**
	 * Setup the editor
	 * called on action 'admin_init'
	 */
	public function _actionSetup() {
		// Disable Visual Composers Frontend Editor
		vc_disable_frontend();

		// Get backend editor object through VC_Manager (vc di container)
		global $vc_manager;
		$this->editor = $vc_manager->backendEditor();

		// VC_Backend_Editor->render() registers all needed scripts
		// the "real" render came later in $this->html_output();
		$this->editor->render( $this->post->post_type );
	}


	private function setMediumAsPost() {
		$medium_id  = $this->medium->getId();

		if( ! $medium_id )
			return false;

		$medium_post_object = get_post( $medium_id );
		if( $medium_post_object === null )
			return false;

		$this->post = $medium_post_object;

		return true;
	}

	public function htmlOutput() {
		ob_start(); ?>
		<div style="display: none;">
			<input type="hidden" id="post_ID" name="post_ID" value="<?php echo $this->post->ID; ?>">
			<textarea cols="30" rows="10" id="wpv_content" name="wpv_content" data-bind="textInput: postContentAccepted"></textarea>
			<?php wp_editor(  $this->post->post_content, 'content', array( 'media_buttons' => true ) ); ?>
		</div>

		<div id="wpb_visual_composer" style="padding-bottom: 5px; background: #fff;"><?php $this->editor->renderEditor( $this->post ); ?></div>
		<?php
		$script = "<script>
				jQuery( window ).load( function( ) {
					/* no fullscreen, no vc save button */
					jQuery( '#vc_navbar .vc_save-backend, #vc_fullscreen-button' ).remove();

					/* show vc editor */
					vc.app.show();
					vc.app.status = 'shown';
					
					var viewsBasicTextarea 		 = jQuery( '#wpv_content' );
					var wordpressDefaultTextarea = jQuery( '#content' );
					
					/* Visual Composer fires the 'sync' event everytime something is changed */
					/* we use this to enable button 'Save all sections at once' if content has changed */
					vc.shortcodes.on( 'sync', function() {
						if( wordpressDefaultTextarea.val() != viewsBasicTextarea.val() ) {
							viewsBasicTextarea.val( wordpressDefaultTextarea.val() );

							WPViews.ct_edit_screen.vm.postContentAccepted = function(){ return wordpressDefaultTextarea.val() };
							WPViews.ct_edit_screen.vm.propertyChangeByComparator( 'postContent', _.isEqual );
						}
					} );
				} );</script>";
		echo preg_replace('/\v(?:[\v\h]+)/', '', $script);
		$output = ob_get_contents();
		ob_end_clean();

		return $output;
	}

	/**
	 * We need some custom scripts ( &styles )
	 * called on 'admin_print_scripts'
	 */
	public function print_scripts() {

		// disable the 100% and fixed vc editor navigation when scrolling down
		$output = '
		<style type="text/css">
			body.toolset_page_ct-editor .composer-switch {
				display:none;
			}
			body.toolset_page_ct-editor .wpv-settings-section,
			body.toolset_page_ct-editor .wpv-setting-container {
				max-width: 96% !important;
			}
			
			body.toolset_page_ct-editor .wpv-setting-container .wpv-settings-header {
				width: 15% !important;
			}
			
			.wpv-setting {
				width: 84%;
			}
			
			.wpv-mightlong-list li {
				min-width: 21%;
			}

			body.toolset_page_ct-editor .js-wpv-content-section .wpv-settings-header {
				display: block;
			}
			
			body.toolset_page_ct-editor .wpv-ct-control-switch-editor {
				padding-left: 105px;
			}
			
			body.toolset_page_ct-editor .js-wpv-content-section .wpv-setting {
				width: 100% !important;
			}
			
			.vc_subnav-fixed{
				position:relative !important;
				top:auto !important;
				left:auto !important;
				z-index: 1 !important;
				padding-left:0 !important;
			}
		</style>';

		// disable our backbone extension due to conflicts with vc (see util.js)
		$output .= "<script>var ToolsetDisableBackboneExtension = '1';</script>";
		echo preg_replace('/\v(?:[\v\h]+)/', '', $output );
	}
}