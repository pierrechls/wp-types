<?php

if( ! class_exists( 'Toolset_User_Editors_Editor_Screen_Abstract', false ) )
	require_once( TOOLSET_COMMON_PATH . '/user-editors/editor/screen/abstract.php' );

class Toolset_User_Editors_Editor_Screen_Beaver_Frontend_Editor
	extends Toolset_User_Editors_Editor_Screen_Abstract {

	/**
	 * Let's activate "Views and Fields" button for any frontend-editor
	 * Not only for our defined 'mediums' like Content Template
	 */
	public function __construct() {
		if( ! array_key_exists( 'fl_builder', $_REQUEST ) )
			return;

		/* disable Toolset Starters "No Content Template assigned" message */
		add_filter( 'toolset_starter_show_msg_no_content_template', '__return_false' );

		/* "Views and Fields" Button */
		if( ! class_exists( 'Toolset_User_Editors_Resource_Views_Dialog', false ) ) {
			require_once( TOOLSET_COMMON_PATH . '/user-editors/resource/views/dialog/dialog.php' );
		}

		$resource = Toolset_User_Editors_Resource_Views_Dialog::getInstance();
		$resource->load();

		/* Types Fields in "Views and Fields" Button */
		if( ! class_exists( 'Toolset_User_Editors_Resource_Views_Dialog_Types_Fields', false ) ) {
			require_once( TOOLSET_COMMON_PATH . '/user-editors/resource/views/dialog/types-fields.php' );
		}

		$resource = Toolset_User_Editors_Resource_Views_Dialog_Types_Fields::getInstance();
		$resource->load();

		/* "Views and Fields" dialog for any input */
		if( ! class_exists( 'Toolset_User_Editors_Resource_Views_Dialog_For_Any_Input', false ) ) {
			require_once( TOOLSET_COMMON_PATH . '/user-editors/resource/views/dialog/for-any-input.php' );
		}

		add_filter( 'toolset_user_editors_for_any_input_selectors', array( $this, '_filterAddBeaverInputsToDialogForAnyInput' ) );
		$resource = Toolset_User_Editors_Resource_Views_Dialog_For_Any_Input::getInstance();
		$resource->load();
	}

	public function isActive() {
		if ( ! array_key_exists( 'fl_builder', $_REQUEST ) ) {
			return false;
		}
		
		$this->action();
		return true;
	}

	private function action() {
		// todo move to content-template frontend-editor
		// we need to change the frontend editor template
		add_filter( 'template_include', array( $this, '_filterFrontendEditorTemplateFile' ) );
	}

	public function _filterAddBeaverInputsToDialogForAnyInput( $inputs ) {
		$beaver_inputs = array(
			'stringSelector' => '.fl-lightbox-content input:text',
			'stringParentSelector' => 'td'
		);

		if( ! is_array( $inputs ) || empty( $inputs ) ) {
			$inputs = array( $beaver_inputs );
		} else {
			$inputs[] = $beaver_inputs;
		}

		return $inputs;
	}

	public function _filterFrontendEditorTemplateFile( $template_file ) {
		global $post;

		if( $post->post_type != $this->medium->getSlug() ) {
			return $template_file;
		}

		$template_selected_usage = $this->getFrontendEditorTemplateFile( $post->ID );

		// todo in Types we have a Filesystem helper class /library/filesystem
		// we should consider moving it to common for such tasks like this
		$file_handle = fopen( $template_selected_usage, 'r' );
		$function_the_content_exists = $function_the_excerpt_exists = false;
		while( ( $line = fgets( $file_handle ) ) !== false) {

			if( strpos( $line , 'the_content' ) !== false ) {
				$function_the_content_exists = true;
				break;
			} elseif( strpos( $line , 'the_excerpt' ) !== false ) {
				$function_the_excerpt_exists = true;
			}
		}

		// (get_)the_content() exists in template file
		if( $function_the_content_exists ) {
			return $template_selected_usage;
		}

		// (get_)the_excerpt() exists
		if( $function_the_excerpt_exists ) {
			add_filter( 'get_the_excerpt', array( $this, '_filterContentInsteadOfExcerpt' ) );
			return $template_selected_usage;
		}

		// no (get_)the_content and no (get_)the_excerpt
		return dirname( __FILE__ ) . '/frontend-editor-template-fallback.php';
	}

	public function _filterContentInsteadOfExcerpt( $excerpt ) {
		ob_start();
			the_content();
			$content = ob_get_contents();
		ob_end_clean();
		return $content;
	}

	private function getFrontendEditorTemplateFile( $ct_id ) {
		$stored_template = get_post_meta( $ct_id, $this->editor->getOptionName(), true );
		$stored_template = array_key_exists( 'template_path', $stored_template )
			? $stored_template['template_path']
			: false;

		if( $stored_template ) {
			return $stored_template;
		}

		// shouldn't happen
		return dirname( __FILE__ ) . '/frontend-editor-template-fallback.php';
	}
}