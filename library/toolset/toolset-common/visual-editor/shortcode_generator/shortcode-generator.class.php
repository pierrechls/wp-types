<?php

/**
* Toolset_Shortcode_Generator
*
* Generic class to manage the Toolset shortcodes admin bar entry.
*
* Use the filter toolset_shortcode_generator_register_item before admin_init:99
* Register your items as follows:
* 		add_filter( 'toolset_shortcode_generator_register_item', 'register_my_shortcodes_in_the_shortcode_generator' );
* 		function register_my_shortcodes_in_the_shortcode_generator( $registered_sections ) {
* 			// Do your logic here to determine whether you need to add your section or not, and check if you need specific assets
* 			// In case you do, register as follows:
* 			$registered_sections['section-id'] = array(
* 				'id'		=> 'section-id',						// The ID of the item
*				'title'		=> __( 'My fields', 'my-textdomain' ),	// The title for the item
*				'href'		=> '#my_anchor',						// The href attribute for the link of the item
*				'parent'	=> 'toolset-shortcodes',				// Set the parent item as the known 'toolset-shortcodes'
*				'meta'		=> 'js-my-classname'					// Cloassname for the li container of the item
* 			);
* 			return $registered_sections;
* 		}
*
* Note that you will have to take care of displaying the dialog after clicking on the item, and deal with what is should do.
*
* @since 1.9
*/

if ( ! class_exists( 'Toolset_Shortcode_Generator' ) ) {
    
    abstract class Toolset_Shortcode_Generator {

	private static $registered_admin_bar_items	= array();
	private static $can_show_admin_bar_item		= false;
	private static $target_dialog_added			= false;
	
	function __construct() {
		
		add_action( 'admin_init',		array( $this, 'register_shortcodes_admin_bar_items' ), 99 );
	    add_action( 'admin_bar_menu',	array( $this, 'display_shortcodes_admin_bar_items' ), 99 );
		add_action( 'admin_footer',		array( $this, 'display_shortcodes_target_dialog' ) );
	}
	
	public function register_shortcodes_admin_bar_items() {
		$registered_items = self::$registered_admin_bar_items;
		$registered_items = apply_filters( 'toolset_shortcode_generator_register_item', $registered_items );
		self::$registered_admin_bar_items = $registered_items;
	}
	
	/*
	 * Add admin bar main item for shortcodes
	 */
	public function display_shortcodes_admin_bar_items( $wp_admin_bar ) {
		if ( ! is_admin() ) {
			return;
		}
		$registered_items = self::$registered_admin_bar_items;
		if ( empty( $registered_items ) ) {
			return;
		}
		self::$can_show_admin_bar_item = true;
	    $this->create_admin_bar_item( $wp_admin_bar, 'toolset-shortcodes', __( 'Toolset shortcodes', 'wpv-views' ), '#', false );
		foreach ( $registered_items as $item_key => $item_args ) {
			$this->create_admin_bar_item( $wp_admin_bar, $item_args['id'], $item_args['title'], $item_args['href'], $item_args['parent'], $item_args['meta'] );
		}
	}
	
	/*
	 * General function for creating admin bar menu items
	 * 
	 */
	public static function create_admin_bar_item( $wp_admin_bar, $id, $name, $href, $parent, $classes = null ) {
	    $args = array(
			'id'		=> $id,
			'title'		=> $name,
			'href'		=> $href,
			'parent'	=> $parent,
			'meta' 		=> array( 'class' => $id . '-shortcode-menu ' . $classes )
	    );
	    $wp_admin_bar->add_node( $args );
	}
	
	/*
	 * Dialog Template HTML code
	 */
	public function display_shortcodes_target_dialog() {
	    if ( 
			self::$can_show_admin_bar_item
			&& self::$target_dialog_added === false 
		) {
			?>
			<div class="toolset-dialog-container" style="display:none">
				<div id="toolset-shortcode-generator-target-dialog" class="toolset-shortcode-gui-dialog-container js-toolset-shortcode-generator-target-dialog">
					<textarea id="toolset-shortcode-generator-target" class="textarea" rows="4" style="width:100%;"></textarea>
				</div>
			</div>
			<?php
			self::$target_dialog_added = true; 
		}

	}
    


    }
    
}