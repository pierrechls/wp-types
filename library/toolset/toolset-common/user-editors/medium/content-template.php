<?php

if( ! class_exists( 'Toolset_User_Editors_Medium_Abstract', false ) )
	require_once( TOOLSET_COMMON_PATH . '/user-editors/medium/abstract.php' );

class Toolset_User_Editors_Medium_Content_Template
	extends Toolset_User_Editors_Medium_Abstract {

	protected $slug = 'view-template';
	protected $allowed_templates;
	protected $option_name_editor_choice = '_toolset_user_editors_editor_choice';

	public function __construct() {
		if( array_key_exists( 'ct_id', $_REQUEST ) )
			$this->id  = (int) $_REQUEST['ct_id'];

		if( $this->id && array_key_exists( 'ct_editor_choice', $_REQUEST ) )
			update_post_meta( $this->id, $this->option_name_editor_choice, sanitize_text_field( $_REQUEST['ct_editor_choice'] ) );

		add_filter( 'toolset_user_editors_backend_html_editor_select', array( $this, '_filterEditorSelection' ) );
	}

	public function userEditorChoice() {
		if( $this->user_editor_choice !== null )
			return $this->user_editor_choice;

		if( ! $this->getId() )
			return false;
		
		$content_template_id  = wpv_getget( 'ct_id' );

		if( $editor_choice = get_post_meta( $content_template_id, $this->option_name_editor_choice, true ) ) {
			$this->user_editor_choice = $editor_choice;
			return $editor_choice;
		} // backward compatibility (since Views Visual Comopser Beta we used 'wpv_ct_editor_choice')
		elseif ( $editor_choice = get_post_meta( $content_template_id, 'wpv_ct_editor_choice', true ) ) {
			$this->user_editor_choice = $editor_choice;
			update_post_meta( $content_template_id, $this->option_name_editor_choice, $editor_choice );
			delete_post_meta( $content_template_id, 'wpv_ct_editor_choice' );
			return $editor_choice;
		} elseif( get_post_type( $content_template_id ) == $this->slug ) {
			update_post_meta( $content_template_id, $this->option_name_editor_choice, 'basic' );
			return 'basic';
		}

		return false;
	}

	public function getFrontendTemplates() {
		
		if( $this->allowed_templates !== null )
			return $this->allowed_templates;

		$content_template_usages = $this->getUsages();
		$theme_template_files    = (array) wp_get_theme()->get_files( 'php', 1, true );

		$wpv_options_patterns = array(
			'views_template_for_'         => array(
				'label'              => __( 'Single page', 'wpv-views' ),
				'domain'             => 'post',
				'template_hierarchy' => array(
					'single-%NAME%.php',
					'single.php',
					'singular.php',
					'index.php'
				)
			),
			'views_template_archive_for_' => array(
				'label'              => __( 'Post archive', 'wpv-views' ),
				'domain'             => 'post',
				'template_hierarchy' => array(
					'archive-%NAME%.php',
					'archive.php',
					'index.php'
				)
			),
			'views_template_loop_'        => array(
				'label'              => __( 'Taxonomy archive', 'wpv-views' ),
				'domain'             => 'taxonomy',
				'template_hierarchy' => array(
					'taxonomy-%NAME%.php',
					'taxonomy.php',
					'archive.php',
					'index.php'
				)
			),
			'view_loop_preview_post_type_'	=> array(
				'label'              => __( 'View loop', 'wpv-views' ),
				'domain'             => 'post',
				'template_hierarchy' => array(
					'single-%NAME%.php',
					'single.php',
					'singular.php',
					'index.php'
				)
			),
			'view_wpa_loop_preview_post_type_'	=> array(
				'label'              => __( 'WordPress Archive loop', 'wpv-views' ),
				'domain'             => 'post',
				'template_hierarchy' => array(
					'archive-%NAME%.php',
					'archive.php',
					'index.php'
				)
			),
			'view_wpa_loop_preview_taxonomy_'	=> array(
				'label'              => __( 'WordPress Archive loop', 'wpv-views' ),
				'domain'             => 'taxonomy',
				'template_hierarchy' => array(
					'taxonomy-%NAME%.php',
					'taxonomy.php',
					'archive.php',
					'index.php'
				)
			),
		);

		$this->allowed_templates = array();

		foreach( $content_template_usages as $usage => $ct_id ) {
			foreach( $wpv_options_patterns as $pattern => $settings ) {
				if( strpos( $usage, $pattern ) !== false ) {
					$type_name   = str_replace( $pattern, '', $usage );
					$type_object = $settings['domain'] == 'post'
						? get_post_type_object( $type_name )
						: get_taxonomy( $type_name );

					foreach( $settings['template_hierarchy'] as $template_file ) {
						$template_file = str_replace( '%NAME%', $type_object->name, $template_file );
						if( array_key_exists( $template_file, $theme_template_files ) ) {
							$this->allowed_templates[] = array(
								'slug'              => $type_object->name,
								'domain'            => $settings['domain'],
								'form-option-label' => $settings['label'] . ' ' . $type_object->labels->name,
								'path'              => $theme_template_files[ $template_file ]
							);
							break;
						}
					}
				}
			}
		}
		
		// Make sure that the stored template path is in the allowed ones, or force it otherwise
		$allowed_paths = wp_list_pluck( $this->allowed_templates, 'path' );
		$current_template = get_post_meta( (int) $_GET['ct_id'], $this->manager->getActiveEditor()->getOptionName(), true );
		
		if ( 
			isset( $_GET['ct_id'] ) 
			&& ! empty( $allowed_paths ) 
			&& (
				! isset( $current_template['template_path'] ) 
				|| ! in_array( $current_template['template_path'], $allowed_paths ) 
			)
		) {
			$slide_allowed_template = array_slice( $this->allowed_templates, 0, 1 );
			$first_allowed_template = array_shift( $slide_allowed_template );
			$settings_to_store = array(
				'template_path' => wp_slash( $first_allowed_template['path'] ),
				'preview_domain' => $first_allowed_template['domain'],
				'preview_slug' => $first_allowed_template['slug']
			);

			update_post_meta( (int) $_GET['ct_id'], $this->manager->getActiveEditor()->getOptionName(), $settings_to_store );
			$stored = get_post_meta( (int) $_GET['ct_id'], $this->manager->getActiveEditor()->getOptionName(), true );
		}

		return $this->allowed_templates;
	}

	private function getUsages() {
		$views_settings	= WPV_Settings::get_instance();
		$views_options	= $views_settings->get();
		$views_options	= array_filter( $views_options, array( $this, 'filterTemplatesByTemplateId' ) );
		
		if ( isset( $_GET['ct_id'] ) ) {
			
			if ( 
				isset( $_GET['preview_post_type'] ) 
				&& is_array( $_GET['preview_post_type'] ) 
				&& ! empty ( $_GET['preview_post_type'] )
			) {
				$preview_post_type = array_map( 'sanitize_text_field', $_GET['preview_post_type'] );
				foreach ( $preview_post_type as $prev_cpt ) {
					$views_options[ 'view_loop_preview_post_type_' . $prev_cpt ] = (int) $_GET['ct_id'];
				}
			}
			
			if ( 
				isset( $_GET['preview_post_type_archive'] ) 
				&& is_array( $_GET['preview_post_type_archive'] ) 
				&& ! empty ( $_GET['preview_post_type_archive'] )
			) {
				$preview_post_type_archive = array_map( 'sanitize_text_field', $_GET['preview_post_type_archive'] );
				foreach ( $preview_post_type_archive as $prev_cpt ) {
					$views_options[ 'view_wpa_loop_preview_post_type_' . $prev_cpt ] = (int) $_GET['ct_id'];
				}
			}
			
			if ( 
				isset( $_GET['preview_taxonomy_archive'] ) 
				&& is_array( $_GET['preview_taxonomy_archive'] ) 
				&& ! empty ( $_GET['preview_taxonomy_archive'] )
			) {
				$preview_taxonomy_archive = array_map( 'sanitize_text_field', $_GET['preview_taxonomy_archive'] );
				foreach ( $preview_taxonomy_archive as $prev_cpt ) {
					$views_options[ 'view_wpa_loop_preview_taxonomy_' . $prev_cpt ] = (int) $_GET['ct_id'];
				}
			}
			
			// @todo implement the rest of the Layout Loop usages
			
		}

		return $views_options;
	}

	private function filterTemplatesByTemplateId( $stored_value ) {
		if( ! isset( $_GET['ct_id'] ) )
			return false;

		return( $stored_value == $_GET['ct_id'] );
	}

	/**
	 * @param $content_function callable
	 */
	public function setHtmlEditorBackend( $content_function ) {
		add_filter( 'toolset_user_editors_backend_html_active_editor', $content_function );
	}


	public function _filterEditorSelection() {
		$control_editor_select = '';
		$editors = $this->manager->getEditors();

		if( count( $editors ) > 1 ) {
			$admin_url = admin_url( 'admin.php?page=ct-editor&ct_id='. (int) $_GET['ct_id'] );

			$editor_current = '';
			$editor_switch_buttons = array();

			foreach( $editors as $editor ) {
				if ( $editor->getId() == $this->manager->getActiveEditor()->getId() ) {
					if ( 'basic' != $editor->getId() ) {
						$editor_current = sprintf( __( 'Using %1$s ', 'wpv-views' ), '<strong>' . $editor->getName() . '</strong>' );
					}
				} else {
					$editor_switch_buttons[] = '<a class="button" href="'.$admin_url.'&ct_editor_choice='.$editor->getId().'">'.sprintf( __( 'Design with %1$s', 'wpv-views' ), $editor->getName() ).'</a>';
				}
			}

			$control_editor_select .= '<div class="wpv-ct-control-switch-editor">';
			$control_editor_select .= $editor_current;
			//$control_editor_select .= __( 'Select Editor: ', 'wpv-views' );
			$control_editor_select .= join( ' ', array_reverse( $editor_switch_buttons ) );
			$control_editor_select .= '</div>';
		}

		return $control_editor_select;
	}

	public function pageReloadAfterBackendSave() {
		add_action( 'admin_print_footer_scripts', array( $this, '_actionPageReloadAfterBackendSave' ) );
	}
	
	public function _actionPageReloadAfterBackendSave() {
		echo "<script>jQuery( document ).on('ct_saved', function() { location.reload(); });</script>";
	}
}