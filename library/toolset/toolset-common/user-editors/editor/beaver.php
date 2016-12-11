<?php

if( ! class_exists( 'Toolset_User_Editors_Editor_Abstract', false ) )
	require_once( TOOLSET_COMMON_PATH . '/user-editors/editor/abstract.php' );

class Toolset_User_Editors_Editor_Beaver
	extends Toolset_User_Editors_Editor_Abstract {

	protected $id = 'beaver';
	protected $name = 'Page Builder';
	protected $option_name = '_toolset_user_editors_beaver_template';

	public function requiredPluginActive() {
		
		if ( ! apply_filters( 'toolset_is_views_available', false ) ) {
			return false;
		}
		
		if( defined( 'FL_BUILDER_VERSION' ) ) {
			$this->name = FLBuilderModel::get_branding();
			return true;
		}

		return false;
	}

	public function run() {
		// register medium slug
		add_filter( 'fl_builder_post_types', array( $this, '_filterSupportMedium' ) );
	}

	/**
	 * We need to register the slug of our Medium in Beaver
	 *
	 * @wp-filter fl_builder_post_types
	 * @param $allowed_types
	 * @return array
	 */
	public function _filterSupportMedium( $allowed_types ) {
		if( ! is_array( $allowed_types ) )
			return array( $this->medium->getSlug() );

		$allowed_types[] = $this->medium->getSlug();
		return $allowed_types;
	}
	
}
