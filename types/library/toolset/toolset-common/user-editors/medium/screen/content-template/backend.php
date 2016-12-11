<?php

if( ! class_exists( 'Toolset_User_Editors_Medium_Screen_Abstract', false ) ) {
	require_once( TOOLSET_COMMON_PATH . '/user-editors/medium/screen/abstract.php' );
}

class Toolset_User_Editors_Medium_Screen_Content_Template_Backend
	extends Toolset_User_Editors_Medium_Screen_Abstract {

	public function isActive() {
		if( ! is_admin() || ! array_key_exists( 'ct_id', $_REQUEST ) ) {
			return false;
		}

		return (int) $_REQUEST['ct_id'];
	}

	public function equivalentEditorScreenIsActive() {
		add_action( 'admin_enqueue_scripts', array( $this , '_actionScripts' ) ) ;
	}

	public function _actionScripts(){
		wp_localize_script( 'views-ct-editor-js', 'toolset_user_editor_choice', $this->manager->getActiveEditor()->getId() );
	}

}