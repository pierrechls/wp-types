var wpv_preview_post_container = jQuery( '.toolset-editors-select-preview-post' ),
    wpv_preview_post = jQuery( '#wpv-ct-preview-post' );

(function( $ ) {
    $( 'document' ).ready( function() {
        FLBuilder._updateLayout();
        wpv_preview_post_container.prependTo( '.fl-builder-bar-actions' );
        wpv_preview_post_container.show();
    } );

    $( window ).load( function() {
        FLBuilder._exitUrl = toolset_user_editors.mediumUrl;
    } );

    wpv_preview_post.on( 'change', function() {
        $.ajax( {
            type: 'post',
            dataType: 'json',
            url: ajaxurl,
            data: {
                action: 'set_preview_post',
                ct_id: toolset_user_editors.mediumId,
                preview_post_id: this.value,
                nonce: toolset_user_editors.nonce
            },
            complete: function() {
                FLBuilder._updateLayout();
            }
        } );
    } );
})( jQuery );