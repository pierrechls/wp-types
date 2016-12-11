<?php

/**
* Toolset_Object_Relationship
*
* Manages the id="XXX" attribute on Types and Views shortcodes.
*
* @since 1.9
*/

if ( ! class_exists( 'Toolset_Object_Relationship' ) ) {
	class Toolset_Object_Relationship
	{
		private static $instance;
		
		public function __construct()
        {
			$this->relations				= array();
			$this->post_relationship_depth	= 0;
			$this->post_relationship_track	= array();
			$this->post_relationship		= array();
			
            add_action( 'admin_init',								array( $this, 'admin_init' ) );
			
			add_filter( 'the_content', 								array( $this, 'record_post_relationship_belongs' ), 0, 1 );
			add_filter( 'wpv_filter_wpv_the_content_suppressed', 	array( $this, 'record_post_relationship_belongs' ), 0, 1 );
			
			add_filter( 'the_content', 								array( $this, 'restore_post_relationship_belongs' ), PHP_INT_MAX, 1 );
			add_filter( 'wpv_filter_wpv_the_content_suppressed', 	array( $this, 'restore_post_relationship_belongs' ), PHP_INT_MAX, 1 );
        }
		
		public static function get_instance() {
			if ( ! self::$instance ) {
				self::$instance = new Toolset_Object_Relationship();
			}
			return self::$instance;
		}

        public function admin_init() {
            
        }
		
		function record_post_relationship_belongs( $content ) {

			global $post;
			$this->post_relationship_depth = ( $this->post_relationship_depth + 1 );

			if ( 
				! empty( $post->ID ) 
				&& function_exists( 'wpcf_pr_get_belongs' ) 
			) {

				if ( ! isset( $this->relations[ $post->post_type ] ) ) {
					$this->relations[ $post->post_type ] = wpcf_pr_get_belongs( $post->post_type );
				}
				if ( is_array( $this->relations[ $post->post_type ] ) ) {
					foreach ( $this->relations[ $post->post_type ] as $post_type => $data ) {
						$related_id = wpcf_pr_post_get_belongs( $post->ID, $post_type );
						if ( $related_id ) {
							$this->post_relationship['$' . $post_type . '_id'] = $related_id;
						} else {
							$this->post_relationship['$' . $post_type . '_id'] = 0;
						}
					}
				}
			}
			
			$this->post_relationship_track[ $this->post_relationship_depth ] = $this->post_relationship;

			return $content;
		}
		
		function restore_post_relationship_belongs( $content ) {
			$this->post_relationship_depth = ( $this->post_relationship_depth - 1 );
			if ( 
				$this->post_relationship_depth > 0 
				&& isset( $this->post_relationship_track[ $this->post_relationship_depth ] )
			) {
				$this->post_relationship = $this->post_relationship_track[ $this->post_relationship_depth ];
			} else {
				$this->post_relationship = array();
			}
			return $content;
		}
	}
}

/**
 * class WPV_wpcf_switch_post_from_attr_id
 *
 * This class handles the "id" attribute in a wpv-post-xxxxx shortcode
 * and sets the global $id, $post, and $authordata
 *
 * It also handles types. eg [types field='my-field' id='233']
 *
 * id can be a integer to refer directly to a post
 * id can be $parent to refer to the parent
 * id can be $current_page or refer to the current page
 *
 * id can also refer to a related post type
 * eg. for a stay the related post types could be guest and room
 * [types field='my-field' id='$guest']
 * [types field='my-field' id='$room']
 */
if ( ! class_exists( 'WPV_wpcf_switch_post_from_attr_id' ) ) {
	class WPV_wpcf_switch_post_from_attr_id
	{

		function __construct( $atts ) {
			$this->found = false;
			$this->reassign_original_post = false;
			$this->toolset_object_relationship = Toolset_Object_Relationship::get_instance();
			$this->post_relationship = $this->toolset_object_relationship->post_relationship;

			if ( isset( $atts['id'] ) ) {

				global $post, $authordata, $id;

				$post_id = 0;

				if ( strpos( $atts['id'], '$' ) === 0 ) {
					// Handle the parent if the id is $parent
					if ( 
						$atts['id'] == '$parent' 
						&& isset( $post->post_parent ) 
					) {
						$post_id = $post->post_parent;
					} else if ( $atts['id'] == '$current_page' ) {
						if ( is_single() || is_page() ) {
							global $wp_query;
							if ( isset( $wp_query->posts[0] ) ) {
								$current_post = $wp_query->posts[0];
								$post_id = $current_post->ID;
							}
						}
					} else {
						// See if Views has the variable
						global $WP_Views;
						if ( isset( $WP_Views ) ) {
							$post_id = $WP_Views->get_variable( $atts['id'] . '_id' );
						}
						if ( $post_id == 0 ) {
							// Try the local storage.
							if ( isset( $this->post_relationship[ $atts['id'] . '_id' ] ) ) {
								$post_id = $this->post_relationship[ $atts['id'] . '_id' ];
							}
						}
					}
				} else {
					$post_id = intval( $atts['id'] );
				}

				if ( $post_id > 0 ) {
					
					// if post does not exists
					if( get_post_status( $post_id ) === false ) {

						// set to true to reapply backup post in __destruct()
						$this->reassign_original_post = true;

						// save original post
						$this->post = ( isset( $post ) && ( $post instanceof WP_Post ) ) ? clone $post : null;

						$msg_post_does_not_exists = __(
							sprintf(
								'A post with the ID %s does not exist.',
								'<b>'.$atts['id'].'</b>'
							)
							, 'wpv-views'
						);

						$post->post_title = $post->post_content = $post->post_excerpt = $msg_post_does_not_exists;

						return;
					}

					$this->found = true;

					// save original post 
					$this->post = ( isset( $post ) && ( $post instanceof WP_Post ) ) ? clone $post : null;
					if ( $authordata ) {
						$this->authordata = clone $authordata;
					} else {
						$this->authordata = null;
					}
					$this->id = $id;

					// set the global post values
					$id = $post_id;
					$post = get_post( $id );

					$authordata = new WP_User( $post->post_author );

				}
			}

		}

		function __destruct() {
			if ( $this->found ) {
				global $post, $authordata, $id;
				// restore the global post values.
				$post = ( isset( $this->post ) && ( $this->post instanceof WP_Post ) ) ? clone $this->post : null;
				if ( $this->authordata ) {
					$authordata = clone $this->authordata;
				} else {
					$authordata = null;
				}
				$id = $this->id;
			}
			
			if( isset( $this->reassign_original_post ) && $this->reassign_original_post ) {
				global $post;

				$post = ( isset( $this->post ) && ( $this->post instanceof WP_Post ) ) ? clone $this->post : null;
			}

		}

	}
}