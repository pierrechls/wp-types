<?php

if( ! class_exists( 'Toolset_User_Editors_Editor_Screen_Abstract', false ) )
	require_once( TOOLSET_COMMON_PATH . '/user-editors/editor/screen/abstract.php' );

class Toolset_User_Editors_Editor_Screen_Beaver_Frontend
	extends Toolset_User_Editors_Editor_Screen_Abstract {

	private $active_medium_id;
	private $beaver_filter_enabled;
	private $beaver_post_id_stack;
	private $beaver_post_id_assets_rendered;

	public function __construct() {
		
		// Pre-process Views shortcodes in the frontend editor and its AJAX update, as well as in the frontend rendering
		// Make sure the $authordata global is correctly set
		add_filter( 'fl_builder_before_render_shortcodes',		array( $this, 'beforeRenderShortcodes' ) );
		
		// Do nothing else in an admin, frontend editing and frontend editing AJAX refresh
		if ( 
			is_admin() 
			|| isset( $_GET['fl-builder'] ) 
			|| isset( $_POST['fl_builder_data'] ) 
		) {
			return;
		}

		/*
		// Those actions are not needed anymore
		add_action( 'wpv_before_shortcode_post_body', array( $this, '_actionSetPostIdForViewsBodyShortcode' ) );
		add_action( 'wpv_after_shortcode_post_body',  array( $this, '_actionSetMediumIdAfterViewsBodyShortcode' ) );
		add_action( 'wp', array( $this, '_actionGlobalizeMediumId' ) );

		add_filter( 'wpv_filter_content_template_output', array( $this, '_filterArchiveContent' ), 10, 4 );
		*/
		add_filter( 'fl_builder_post_types',					array( $this, '_filterSupportMedium' ) );
		
		add_filter( 'body_class',								array( $this, 'bodyClass' ) );
		
		add_filter( 'wpv_filter_content_template_output',		array( $this, 'filterContentTemplateOutput' ), 10, 4 );
		add_filter( 'the_content',								array( $this, 'restoreBeaverFilter' ), 9999 );
		
		$this->beaver_filter_enabled = true;
		$this->beaver_post_id_stack = array();
		$this->beaver_post_id_assets_rendered = array();

	}

	public function isActive() {
		return true;
	}
	
	// @todo we need to set the $authordata global, but we need to use it on do_shortcode
	// which happens after this filter callback, and as we need to restore after rendering
	// we can not do it here
	public function beforeRenderShortcodes( $content ) {
		/*
		global $authordata;
		$authordata_old = $authordata;
		$current_post_id = FLBuilderModel::get_post_id();
		if ( $current_post_id ) {
			$current_post_author = get_post_field( 'post_author', $current_post_id );
			$authordata = new WP_User( $current_post_author );
		}
		*/
		$content = WPV_Frontend_Render_Filters::pre_process_shortcodes( $content );
		/*
		$authordata = $authordata_old;
		*/
		return $content;
	}
	
	public function _filterSupportMedium( $allowed_types ) {
		if( ! is_array( $allowed_types ) ) {
			return array( $this->medium->getSlug() );
		}
		$medium_slug = $this->medium->getSlug();
		if ( ! in_array( $medium_slug, $allowed_types ) ) {
			$allowed_types[] = $medium_slug;
		}
		return $allowed_types;
	}
	
	public function bodyClass( $classes ) {
		if ( ! is_archive() ) {
			$current_post = get_post( FLBuilderModel::get_post_id() );
			if ( $current_post ) {
				$post_has_ct = get_post_meta( $current_post->ID, '_views_template', true );
				if ( $post_has_ct ) {
					$ct_has_beaver = get_post_meta( $post_has_ct, '_fl_builder_enabled', true );
					if ( $ct_has_beaver ) {
						$classes[] = 'fl-builder';
					}
				}
			}
		}
		return $classes;
	}
	
	public function filterContentTemplateOutput( $content, $template_selected, $id, $kind ) {
		
		if (
			$template_selected 
			&& $template_selected > 0
		) {
			
			// There is a CT applied, either on single/archive pages or on a wpv-post-body shortcode
			// Render the BB content of the CT, if any, and prevent Beaver from overwriting it
			
			$editor_choice = get_post_meta( $template_selected, $this->medium->getOptionNameEditorChoice(), true );
			
			if (
				$editor_choice
				&& $editor_choice == $this->editor->getId()
			) {
				
				FLBuilderModel::update_post_data( 'post_id', $template_selected );
				
				$this->beaver_post_id_stack[] = $template_selected;
				
				$content = FLBuilder::render_content( $content );
				
				if ( ! in_array( $template_selected, $this->beaver_post_id_assets_rendered ) ) {
					FLBuilder::enqueue_layout_styles_scripts();
					$this->beaver_post_id_assets_rendered[] = $template_selected;
				}
				
				array_pop( $this->beaver_post_id_stack );
				if ( count( $this->beaver_post_id_stack ) > 0 ) {
					$aux_array = array_slice( $this->beaver_post_id_stack, -1 );
					$bb_post_id = array_pop( $aux_array );
					FLBuilderModel::update_post_data( 'post_id', $bb_post_id );
				} else {
					FLBuilderModel::update_post_data( 'post_id', get_the_ID() );
				}
				
			}
			
			remove_filter( 'the_content', 'FLBuilder::render_content' );
			$this->beaver_filter_enabled = false;
			
		} else {
			global $post;
			if ( isset( $post->view_template_override ) ) {
				$this_id = get_the_ID();
				// This is coming from a wpv-post-body shortcode with view_template="None" so we do need to apply BB here 
				FLBuilderModel::update_post_data( 'post_id', $this_id );
				
				$this->beaver_post_id_stack[] = $this_id;
				
				$content = FLBuilder::render_content( $content );
				
				if ( ! in_array( $template_selected, $this->beaver_post_id_assets_rendered ) ) {
					//FLBuilder::enqueue_layout_styles_scripts();
					$this->beaver_post_id_assets_rendered[] = $this_id;
				}
				
				array_pop( $this->beaver_post_id_stack );
				if ( count( $this->beaver_post_id_stack ) > 0 ) {
					$aux_array = array_slice( $this->beaver_post_id_stack, -1 );
					$bb_post_id = array_pop( $aux_array );
					FLBuilderModel::update_post_data( 'post_id', $bb_post_id );
				}
				
			}
		}
		
		return $content;
	}
	
	public function restoreBeaverFilter( $content ) {
		if ( ! $this->beaver_filter_enabled ) {
			add_filter( 'the_content', 'FLBuilder::render_content' );
		}
		return $content;
	}

	public function _filterArchiveContent( $content, $template_selected, $id, $kind ) {

		if( $this->getActiveMediumId() && $this->getActiveMediumId() == $template_selected ) {
			FLBuilderModel::update_post_data( 'post_id', $this->getActiveMediumId() );
			$content = FLBuilder::render_content( $content );
		}


		return $content;
	}

	/**
	 * Beaver is looking for $_POST['post_id'] first, to select content
	 * we use that to get beaver content of our medium id
	 */
	public function _actionGlobalizeMediumId() {
		if( $this->getActiveMediumId() )
			FLBuilderModel::update_post_data( 'post_id', $this->getActiveMediumId() );
	}

	private function getActiveMediumId() {
		if( $this->active_medium_id === null )
			$this->active_medium_id = $this->fetchActiveMediumId();

		return $this->active_medium_id;
	}

	private function fetchActiveMediumId() {
		$medium_id = $this->medium->getId();

		$editor_choice = get_post_meta( $medium_id, $this->medium->getOptionNameEditorChoice(), true );

		if(
			$editor_choice
		    && $editor_choice == $this->editor->getId()
		    && isset( $medium_id ) && $medium_id
		)
			return $medium_id;


		return false;
	}

	public function _actionSetPostIdForViewsBodyShortcode() {
		add_filter( 'the_content', 'FLBuilder::render_content' );
		FLBuilderModel::update_post_data( 'post_id', get_the_ID() );

		add_filter( 'wpv_filter_content_template_output', 'FLBuilder::render_content' );
	}

	public function _actionSetMediumIdAfterViewsBodyShortcode() {
		remove_filter( 'the_content', 'FLBuilder::render_content' );

		if( $this->getActiveMediumId() )
			FLBuilderModel::update_post_data( 'post_id', $this->getActiveMediumId() );
	}


}