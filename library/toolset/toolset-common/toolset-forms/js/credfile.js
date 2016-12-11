
var wptCredfile = (function ($) {
    function init(selector) {
        $('.js-wpt-credfile-delete, .js-wpt-credfile-undo').on('click', function (e) {
            e.preventDefault();
            var thiz = $(this),
                    credfile_action = thiz.data('action'),
                    credfile_container = thiz.closest('.wpt-repctl');
            if (credfile_container.length < 1) {
                credfile_container = thiz.closest('.js-wpt-field-items');
            }
            var thiz_delete_button = $('.js-wpt-credfile-delete', credfile_container),
                    thiz_undo_button = $('.js-wpt-credfile-undo', credfile_container),
                    thiz_hidden_input = $('.js-wpv-credfile-hidden', credfile_container),
                    thiz_file_input = $('.js-wpt-credfile-upload-file', credfile_container),
                    thiz_preview = $('.js-wpt-credfile-preview', credfile_container),
                    thiz_existing_value = thiz_hidden_input.val();
            var myid = thiz_hidden_input.attr('name');
            if (credfile_action == 'delete') {
                thiz_file_input.prop('disabled', false).show().val('');
                thiz_hidden_input.prop('disabled', true);
                thiz_preview.hide();
                //thiz_delete_button.hide();
                if (thiz_existing_value != '') {
                    thiz_undo_button.show();
                } else {
                    thiz_undo_button.hide();
                }
                if (myid == '_featured_image') {
                    $('#attachid_' + myid).val('');                    
                } else {
                    if (thiz.closest('.js-wpt-repetitive').length > 0) {
                    } else
                        $('#' + myid).prop('disabled', false);
                        //$("<input type='hidden' id='" + myid + "' name='" + myid + "' value=''>").insertAfter('#' + thiz_hidden_input.attr('id'));
                }
                thiz_file_input.trigger('change');
            } else if (credfile_action == 'undo') {
                thiz_file_input.prop('disabled', true).hide();
                thiz_hidden_input.prop('disabled', false);
                thiz_file_input.trigger('change');
                thiz_preview.show();
                //thiz_delete_button.show();
                thiz_undo_button.hide();
                if (myid == '_featured_image')
                    $('#attachid_' + myid).val($("input[name='_cred_cred_prefix_post_id']").val());
                else {
                    if (thiz.closest('.js-wpt-repetitive').length > 0) {
                    } else
                        $('#' + myid).prop('disabled', false);
                }
            }
        });

        $('.js-wpt-credfile-upload-file').on('change', function (e) {
            e.preventDefault();
            var thiz = $(this),
                    credfile_container = thiz.closest('.wpt-repctl');
            if (credfile_container.length < 1) {
                credfile_container = thiz.closest('.js-wpt-field-items');
            }
            var thiz_delete_button = $('.js-wpt-credfile-delete', credfile_container),
                    thiz_undo_button = $('.js-wpt-credfile-undo', credfile_container),
                    thiz_hidden_input = $('.js-wpv-credfile-hidden', credfile_container),
                    thiz_preview = $('.js-wpt-credfile-preview', credfile_container),
                    thiz_existing_value = thiz_hidden_input.val();
//            if (thiz.val() != '') {
//                thiz_delete_button.show();
//            } else {
//                thiz_delete_button.hide();
//            }
            if (thiz_existing_value != '' && thiz_existing_value != thiz.val()) {
                thiz_undo_button.show();
            } else {
                thiz_undo_button.hide();
            }
        });
    }
    return {
        init: init
    };
})(jQuery);

jQuery(document).ready(function () {
    wptCredfile.init('body');
});