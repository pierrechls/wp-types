<?php

if( ! class_exists( 'Toolset_User_Editors_Medium_Screen_Abstract', false ) ) {
	require_once( TOOLSET_COMMON_PATH . '/user-editors/medium/screen/abstract.php' );
}

class Toolset_User_Editors_Medium_Screen_Content_Template_Frontend_Editor
	extends Toolset_User_Editors_Medium_Screen_Abstract {

	private $original_global_post;

	public function __construct() {
		add_action( 'wp_ajax_set_preview_post', array( $this, 'ajax_set_preview_post' ) );
	}

	public function isActive() {
		if( is_admin() || ! current_user_can( 'manage_options' ) ) {
			return false;
		}

		return true;
	}

	public function equivalentEditorScreenIsActive() {
		add_action( 'init', array( $this, '_actionRegisterAsPostType' ) );
		add_action( 'wp', array( $this, '_actionLoadMediumIdByPost' ), -1 );
	}

	public function _actionLoadMediumIdByPost() {
		global $post;

		if( ! is_object( $post ) || $post->post_type != 'view-template'  ) {
			return false;
		}

		$this->manager->getMedium()->setId( $post->ID );

		// todo outsource complete preview selector to 'resources'
		add_action( 'wp_footer', array( $this, 'render_preview_post_selector') );

		// set preview post as global post
		// todo move to beaver/screen/backend
		add_action( 'fl_builder_before_render_module', array( $this, 'set_preview_post' ) );

		// reset global post after content is loaded via ajax
		// todo move to beaver/screen/backend
		add_action( 'fl_builder_after_render_content', array( $this, 'reset_preview_post' ) );

		add_action( 'wp_enqueue_scripts', array( $this, '_actionStyleAndScripts' ) );
	}


	public function _actionStyleAndScripts() {
		// ./backend.css
		wp_enqueue_style(
			'toolset-user-editors-ct-frontend-editor-style',
			TOOLSET_COMMON_URL . '/user-editors/medium/screen/content-template/frontend-editor.css',
			array(),
			TOOLSET_COMMON_VERSION
		);

		// ./backend.js
		wp_enqueue_script(
			'toolset-user-editors-ct-frontend-editor-script',
			TOOLSET_COMMON_URL . '/user-editors/medium/screen/content-template/frontend-editor.js',
			array( 'jquery' ),
			TOOLSET_COMMON_VERSION,
			true
		);

		wp_localize_script( 'toolset-user-editors-ct-frontend-editor-script', 'toolset_user_editors', array(
			'nonce' => wp_create_nonce( 'toolset_user_editors' ),
			'mediumId' => $this->manager->getMedium()->getId(),
			'mediumUrl' => admin_url( 'admin.php?page=ct-editor&ct_id=' . $this->manager->getMedium()->getId() ),
		) );
	}
	

	public function _actionRegisterAsPostType() {
		register_post_type( 'view-template', array(
			'public'             => false,
			'publicly_queryable' => true,
			'show_ui'            => false,
			'show_in_menu'       => false,
			'rewrite'            => array( 'slug' => 'view-template' ),
			'supports'           => array( 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'comments' )
		) );

		flush_rewrite_rules();
	}

	public function set_preview_post() {
		if( isset( $_POST['fl_builder_data'] ) ) {
			global $post;

			if( $this->original_global_post === null ) {
				$this->original_global_post = $post;
			}

			$preview_post = $this->get_preview_post_id( $this->original_global_post->ID );

			if( $preview_post ) {
				$post = get_post( $preview_post );
			}
			// no preview post selected or selected does not exist anymore
			else {
				// disable shortcode rendering
				add_filter( 'fl_builder_render_shortcodes', '__return_false' );
			}
		}
	}

	public function reset_preview_post() {
		if( isset( $_POST['fl_builder_data'] ) ) {
			global $post;
			$post = $this->original_global_post;
		}
	}

	public function render_preview_post_selector() {
		if( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) {
			global $post;

			$toolset_frontend_editor = get_post_meta( $post->ID, '_toolset_user_editors_beaver_template', true );
			$preview_slug = array_key_exists( 'preview_slug', $toolset_frontend_editor )
				? $toolset_frontend_editor['preview_slug']
				: false;


			if( $toolset_frontend_editor['preview_domain'] == 'post' ) {
				$preview_posts =  wp_get_recent_posts( array( 'post_type' => $preview_slug ), ARRAY_A );
			} else {

				$terms = get_terms( $preview_slug );
				$term_ids = array();
				foreach( $terms as $term ) {
					$term_ids[] = $term->term_id;
				}

				$preview_posts =  wp_get_recent_posts( array( 'post_type' => 'any', 'tax_query' => array(
					array(
						'taxonomy' => $preview_slug,
						'field' => 'id',
						'terms' => $term_ids
					))), ARRAY_A );
			}

			$preview_post = $this->get_preview_post_id( $post->ID );
			
			$selected = $preview_post == 0
				? ' selected="selected"'
				: '';

			echo '<div class="toolset-editors-select-preview-post" style="display: none;">';
			echo __( 'Preview with:', 'wpv-views' );

			echo ' <select id="wpv-ct-preview-post">';
			echo '<option value="0"' . $selected . '>'.__( 'No Shortcode Rendering', 'wp-views' ).'</option>';

			foreach( $preview_posts as $single_post ) {
				$selected = $preview_post == $single_post['ID']
					? ' selected="selected"'
					: '';
				echo '<option value="' . $single_post['ID'] . '"' . $selected . '>'.$single_post['post_title'].'</option>';
			}
			echo '</select>';
			echo '<i class="fa fa-question-circle fa-lg wpv-ct-preview-post-help js-wpv-ct-preview-post-help" aria-hidden="true" onclick="alert(\'' . esc_js( 'You are using Beaver Builder to design a template. This selector lets you choose with what content to preview the template.', 'wpv-views' ) . '\');"></i>';
			echo '</div>';
		}
	}

	public function ajax_set_preview_post() {
		if ( ! wp_verify_nonce( $_REQUEST['nonce'], 'toolset_user_editors' ) ) {
			die( -1 );
		}

		if( isset( $_REQUEST['ct_id'] ) && isset( $_REQUEST['preview_post_id'] ) ) {
			$this->store_preview_post_id( (int) $_REQUEST['ct_id'], (int) $_REQUEST['preview_post_id'] );
		}

		die( 1 );
	}

	private function store_preview_post_id( $ct_id, $preview_post_id ) {
		update_post_meta( $ct_id, '_toolset_user_editors_frontend_editor_preview_post', $preview_post_id );
	}

	private function get_preview_post_id( $ct_id ) {
		$stored_template = get_post_meta( $ct_id, '_toolset_user_editors_frontend_editor_preview_post', true  );

		// stored template available and is an allowed template
		if( $stored_template && get_post( $stored_template ) ) {
			return $stored_template;
		}

		return false;
	}
}