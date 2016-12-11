/*
 * Toolset Views plugin.
 *
 * Loaded on Views or Views Template edit screens.
 */
var typesWPViews = (function(window, $){

    function openFrameForWizard( fieldID, metaType, postID, shortcode ) {
        var colorboxWidth = 750 + 'px';

        if ( !( jQuery.browser.msie && parseInt(jQuery.browser.version) < 9 ) ) {
            var documentWidth = jQuery(document).width();
            if ( documentWidth < 750 ) {
                colorboxWidth = 600 + 'px';
            }
        }

        var url = ajaxurl+'?action=wpcf_ajax&wpcf_action=editor_callback'
        + '&_typesnonce=' + types.wpnonce
        + '&callback=views_wizard'
        + '&field_id=' + fieldID
        + '&field_type=' + metaType
        + '&post_id=' + postID
        + '&shortcode=' + shortcode;

        jQuery.colorbox({
            href: url,
            iframe: true,
            inline : false,
            width: colorboxWidth,
            opacity: 0.7,
            closeButton: false
        });
    }
	
	function openFrameForAdminBar( fieldID, metaType, postID ) {
        var colorboxWidth = 750 + 'px';

        if ( !( jQuery.browser.msie && parseInt(jQuery.browser.version) < 9 ) ) {
            var documentWidth = jQuery(document).width();
            if ( documentWidth < 750 ) {
                colorboxWidth = 600 + 'px';
            }
        }

        var url = ajaxurl+'?action=wpcf_ajax&wpcf_action=editor_callback'
        + '&_typesnonce=' + types.wpnonce
        + '&callback=admin_bar'
        + '&field_id=' + fieldID
        + '&field_type=' + metaType
        + '&post_id=' + postID;

        jQuery.colorbox({
            href: url,
            iframe: true,
            inline : false,
            width: colorboxWidth,
            opacity: 0.7,
            closeButton: false
        });
    }

    return {
        wizardEditShortcode: function( fieldID, metaType, postID, shortcode ) {
            openFrameForWizard( fieldID, metaType, postID, shortcode );
        },
        wizardSendShortcode: function( shortcode ) {
            window.wpv_restore_wizard_popup(shortcode);
        },
		adminBarEditShortcode: function( fieldID, metaType, postID ) {
			openFrameForAdminBar( fieldID, metaType, postID );
		},
		adminBarCreateShortcode: function( shortcode ) {
			window.parent.jQuery.colorbox.close();
			$( document ).trigger( 'js_types_shortcode_created', shortcode );
		},
        wizardCancel: function() {
            window.wpv_cancel_wizard_popup();
        }
    };
})(window, jQuery, undefined);