;( function( $ ) {

    var btnLaunchBuilder
        = $( '.toolset-user-editors-beaver-backend .fl-launch-builder' );

    var loadingIcon
        = $( '.toolset-user-editors-beaver-backend .toolset-user-editors-beaver-spinner' );

	$( '#toolset-user-editors-beaver-template-file' ).on( 'change', function() {
		btnLaunchBuilder.hide( 0, function() { loadingIcon.show() } );
		jQuery.ajax( {
			type : 'post',
			dataType : 'json',
			url : ajaxurl,
			data : {
				action: 'toolset_user_editors_beaver',
				post_id: toolset_user_editors_beaver.mediumId,
				template_path: this.value,
				preview_domain: jQuery( '#toolset-user-editors-beaver-template-file option:selected' ).data( 'preview-domain' ),
				preview_slug: jQuery( '#toolset-user-editors-beaver-template-file option:selected' ).data( 'preview-slug' ),
				nonce: toolset_user_editors_beaver.nonce
			},
			complete: function() {
				loadingIcon.hide( 0, function() { btnLaunchBuilder.show() } );
			}
		} );
	});
	
})( jQuery );