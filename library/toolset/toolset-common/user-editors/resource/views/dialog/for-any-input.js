var wpv_open_shortcode_dialog = jQuery( '.js-wpv-fields-and-views-in-adminbar' ),
    wpv_add_shortcode_to,
    wpv_add_shortcode_to_parent;

(function( $ ) {
    $( 'document' ).ready( function() {
        $.each( toolset_for_any_input, function( key, input ) {
            $( 'body' ).on( 'focus', input.stringSelector, function() {
                wpv_add_shortcode_to_parent = $( this ).closest( input.stringParentSelector );
                wpv_add_shortcode_to_parent.css( 'position', 'relative' );
                wpv_open_shortcode_dialog.appendTo( wpv_add_shortcode_to_parent );
                wpv_open_shortcode_dialog.css( 'display', 'block' );
                wpv_add_shortcode_to = $( this );
            } );
        } );
    } );
})( jQuery );