<?php

if( ! class_exists( 'Toolset_User_Editors_Medium_Screen_Abstract', false ) ) {
	require_once( TOOLSET_COMMON_PATH . '/user-editors/medium/screen/abstract.php' );
}

class Toolset_User_Editors_Medium_Screen_Content_Template_Frontend
	extends Toolset_User_Editors_Medium_Screen_Abstract {

	public function dropIfNotActive() {
		return false;
	}

	public function isActive() {
		if( is_admin() ) {
			return false;
		}

		global $post, $wp_query;

		if( ! is_object( $wp_query ) ) {
			return false;
		}

		if( $id = $this->isActiveSinglePost() ) {
			return $id;
		}

		if( $id = $this->isActiveTaxonomyArchive() ) {
			return $id;
		}

		if( $id = $this->isActivePostArchive() ) {
			return $id;
		}

		return false;
	}

	private function isActiveSinglePost() {
		global $post;

		if( is_single() && is_object( $post ) ) {
			$template_selected = get_post_meta( $post->ID, '_views_template', true );
		}

		if( isset( $template_selected ) && $template_selected ) {
			return $template_selected;
		}

		return false;
	}

	private function isActiveTaxonomyArchive() {
		global $wp_query;
		if (
			is_tax()
			|| is_category()
			|| is_tag()
		) {
			$views_settings	= WPV_Settings::get_instance();
			$wpv_options	= $views_settings->get();
			$term = $wp_query->get_queried_object();
			if( array_key_exists( 'views_template_loop_' . $term->taxonomy, $wpv_options ) ) {
				return $wpv_options['views_template_loop_' . $term->taxonomy];
			}
		}

		return false;
	}

	private function isActivePostArchive() {
		global $post;

		if( is_object( $post ) == false ) {
			return;
		}

		if( is_post_type_archive( $post->post_type ) && is_object( $post )  ) {
			$views_settings	= WPV_Settings::get_instance();
			$wpv_options	= $views_settings->get();

			if( array_key_exists( 'views_template_archive_for_' . $post->post_type, $wpv_options ) ) {
				return $wpv_options['views_template_archive_for_' . $post->post_type];
			}
		}

		return false;
	}
}