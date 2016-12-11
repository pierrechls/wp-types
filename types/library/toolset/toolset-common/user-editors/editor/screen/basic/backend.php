<?php

if( ! class_exists( 'Toolset_User_Editors_Editor_Screen_Abstract', false ) )
	require_once( TOOLSET_COMMON_PATH . '/user-editors/editor/screen/abstract.php' );

class Toolset_User_Editors_Editor_Screen_Basic_Backend
	extends Toolset_User_Editors_Editor_Screen_Abstract {

	public function isActive() {
		$this->action();
		return true;
	}

	private function action() {
		$this->medium->setHtmlEditorBackend( array( $this, 'htmlOutput' ) );
	}

	public function htmlOutput() {

		if( ! isset( $_GET['ct_id'] ) )
			return 'No valid content template id';

		ob_start();
			include_once( dirname( __FILE__ ) . '/backend.phtml' );
			$content = ob_get_contents();
		ob_end_clean();

		return $content;
	}
}