var WPViews = WPViews || {};

WPViews.ViewEditScreenUserEditorBeaver = function( $ ) {
	
	var self = this;
	
	self.selector			= '.js-wpv-ct-listing';
	self.overlay			= "<div class='wpv-setting-overlay js-wpv-layout-template-overlay' style='top:36px'>";
	self.overlay				+= "<div class='wpv-transparency' style='opacity:0.9'></div>";
	//self.overlay				+= "<i class='icon-lock fa fa-lock'></i>";
	self.overlay				+= "<div class='wpv-layout-template-overlay-info toolset-alert toolset-alert-info' style='position:absolute;top:5px;left:5px;right:5px;bottom:5px;margin:0;'>";
	self.overlay					+= "<p><strong>" + toolset_user_editors_beaver_layout_template_i18n.template_overlay.title + "</strong></p>";
	self.overlay					+= "<p>" + toolset_user_editors_beaver_layout_template_i18n.template_overlay.text + "</p>";
	self.overlay					+= "<p><a href='" + toolset_user_editors_beaver_layout_template_i18n.template_editor_url + "' target='_blank' class='button button-secondary js-wpv-layout-template-overlay-info-link'>" + toolset_user_editors_beaver_layout_template_i18n.template_overlay.button + " <i class='fa fa-chevron-right' aria-hidden='true'></i></a></p>";
	self.overlay				+= "</div>";
	self.overlay			+= "</div>";
	self.overlay_container	= $( self.overlay );
	
	self.init_beaver_editors = function() {
		$( self.selector ).each( function() {
			self.init_beaver_editor( $( this ) );
		});
		return self;
	};
	
	self.init_beaver_editor = function( item ) {
		if ( 
			item.hasClass( 'js-wpv-ct-listing-user-editor-beaver-inited' ) 
			|| item.find( '.CodeMirror' ).length == 0
		) {
			// This has been inited before, it it is rendered closed
			return self;
		}
		var attributes = item.data( 'attributes' );
		_.defaults( attributes, { builder: 'basic' } );
		item.addClass( 'js-wpv-ct-listing-user-editor-beaver-inited' );
		if ( attributes.builder == 'beaver' ) {
			item.prepend( self.overlay_container );
			item.find( '.CodeMirror' ).css( { 'height' : '0px'} );
			self.update_beaver_ct_editor_link_target( item );
		} else if ( attributes.builder == 'basic' ) {
			var template_id = item.data( 'id' ),
				ct_editor_basic_panel_args = { 
				editor:		'wpv_ct_inline_editor_' + template_id,
				content:	'',
				keep:		'permanent',
				type:		'info'
			};
			//Toolset.hooks.doAction( 'wpv-action-wpv-add-codemirror-panel', ct_editor_basic_panel_args );
			// @todo I would add a Codemirror panel here to let users know how to add BB to this CT
		}
	};
	
	self.reload_beaver_editors_link_target = function() {
		$( self.selector ).each( function() {
			self.update_beaver_ct_editor_link_target( $( this ) );
		});
		return self;
	};
	
	self.update_beaver_ct_editor_link_target = function( item ) {
		ct_editor_link = item.find( '.js-wpv-layout-template-overlay-info-link' ),
		ct_editor_link_target = toolset_user_editors_beaver_layout_template_i18n.template_editor_url + '&ct_id=' + item.data( 'id' );
		var query_mode = Toolset.hooks.applyFilters( 'wpv-filter-wpv-edit-screen-get-query-mode', 'normal' );
		switch ( query_mode ) {
			case 'normal':
				var query_type = $( '.js-wpv-query-type:checked' ).val();
				switch ( query_type ) {
					case 'posts':
						$('.js-wpv-query-post-type:checked').map( function() {
							ct_editor_link_target += '&preview_post_type[]=' + $( this ).val();
						});
						break;
					case 'taxonomy':
						$('.js-wpv-query-taxonomy-type:checked').map( function() {
							ct_editor_link_target += '&preview_taxonomy[]=' + $( this ).val();
						});
						break;
					case 'users':
						$('.js-wpv-query-users-type:checked').map( function() {
							ct_editor_link_target += '&preview_user[]=' + $( this ).val();
						});
						break;
				}
				break;
			case 'archive':
				$( '.js-wpv-settings-archive-loop input:checked' ).map( function() {
					switch ( $( this ).data( 'type' ) ) {
						case 'native':
							
							break;
						case 'post_type' :
							ct_editor_link_target += '&preview_post_type_archive[]=' + $( this ).data( 'name' );
							break;
						case 'taxonomy':
							ct_editor_link_target += '&preview_taxonomy_archive[]=' + $( this ).data( 'name' );
							break;
					}
				});
				break;
		}
		ct_editor_link.attr( 'href', ct_editor_link_target );
		return self;
	};
	
	$( document ).on( 'js_event_wpv_query_type_options_saved', '.js-wpv-query-type-update', function( event, query_type ) {
		self.reload_beaver_editors_link_target();
	});
	
	self.set_inline_content_template_events = function( template_id ) {
		self.init_beaver_editor( $( '.js-wpv-ct-listing-' + template_id ) );
	};
	
	$( document ).on( 'js_event_wpv_ct_inline_editor_inited', function( event, template_id ) {
		self.init_beaver_editor( $( '.js-wpv-ct-listing-' + template_id ) );
	});
	
	self.init_hooks = function() {
		Toolset.hooks.addAction( 'wpv-action-wpv-set-inline-content-template-events', self.set_inline_content_template_events );
		return self;
	};
	
	self.init = function() {
		self.init_beaver_editors()
			.init_hooks();
		
	};
	
	self.init();

};

jQuery( document ).ready( function( $ ) {
    WPViews.view_edit_screen_user_editor_beaver = new WPViews.ViewEditScreenUserEditorBeaver( $ );
});