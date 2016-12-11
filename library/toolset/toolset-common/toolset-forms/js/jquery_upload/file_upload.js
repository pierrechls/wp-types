function getExtension(filename) {
    return filename.split('.').pop().toLowerCase();
}

function isImage(file) {
    switch (getExtension(file)) {
        //if .jpg/.gif/.png do something
        case 'jpg':
        case 'gif':
        case 'png':
        case 'jpeg':
        case 'bmp':
        case 'svg':
            return true;
            break;

    }
    return false;
}

//new RegExp('/regex'+DATA-FROM-INPUT+'', 'i');
jQuery(function () {
    'use strict';
    // Change this to the location of your server-side upload handler:

//    jQuery.each(jQuery(".wpt-form-hidden"),function(i, val){
//        console.log(i);
//        jQuery(val).prop('');
//    });

    function o(i, file) {
        var url = settings.ajaxurl;
        //console.log("URL:" + url);
        var nonce = settings.nonce;
        //console.log("NONCE:" + nonce);

        var curr_file = file;
        //var validation = jQuery(curr_file).attr('data-wpt-validate');
        var validation = (jQuery(curr_file).attr('data-wpt-validate')) ? jQuery(curr_file).attr('data-wpt-validate') : '[]';
        //console.log(validation);
        var obj_validation = jQuery.parseJSON(validation);

        for (var x in obj_validation) {
            if (x == 'extension') {
                for (var y in obj_validation[x]) {
                    if (y == 'args') {
                        var validation_args = obj_validation[x][y][0];
                        //validation_args = validation_args.split('|').join(',');
                    }
                    if (y == 'message') {
                        var validation_message = obj_validation[x][y];
                    }
                }
            }
        }

        var myid = jQuery("input[name='_cred_cred_prefix_post_id']").val();
        var myformid = jQuery("input[name='_cred_cred_prefix_form_id']").val();
        var post_id = myid;

        jQuery(file).fileupload({
            url: url + '?id=' + myid + '&formid=' + myformid + '&nonce=' + nonce,
            dataType: 'json',
            cache: false,
            maxChunkSize: 0,
            drop: function (e, data) {
                return false
            },
            dragover: function (e) {
                return false
            },
            formData: {id: myid, formid: myformid},
            //acceptFileTypes: /(\.|\/)(gif|jpe?g|png)$/i,
            done: function (e, data) {
                var id = jQuery(curr_file).attr('id');
                //progress bar hiding            
                var wpt_id = jQuery('#' + id).siblings(".meter").attr("id"); //id.replace("_file", "");
                //console.log(wpt_id);
                jQuery('#' + wpt_id).show();
                jQuery('#' + wpt_id + ' .progress-bar').css(
                        {'width': '0%'}
                );
                jQuery('#' + wpt_id).hide();

                if (data._response.result.error && data._response.result.error != '') {
                    alert(data._response.result.error);
                }
                if (data.result.files) {
                    jQuery.each(data.result.files, function (index, file) {
                        //console.log("url: " + file);
                        var id = jQuery(curr_file).attr('id');
                        //console.log("id: " + id);
                        var wpt_id = id.replace("_file", "");
                        var myid = wpt_id;
                        //console.log("wpt_id: " + wpt_id);

                        var number = 0;
                        if (id.toLowerCase().indexOf("wpt-form-el") >= 0) {
                            var number = id.replace(/[^0-9]/g, '');
                            //console.log("number: " + number);
                            var new_num = number - 1;
                            //console.log("new_num: " + new_num);
                            var hidden_id = "wpt-form-el" + new_num;
                        } else
                            var hidden_id = wpt_id + '_hidden';

                        //console.log("hidden_id: " + hidden_id);

                        var is_repetitive = jQuery('#' + id).parent().parent().hasClass("js-wpt-repetitive");
                        //console.log("is_repetitive: " + is_repetitive);
                        if (is_repetitive) {
                            var newname = wpt_id.replace(number, "[" + number + "]");
                            jQuery('input[name="' + newname + '"]#' + wpt_id).val(file);
                            //console.log(jQuery('input[name="' + wpt_id + '[' + number + ']"]#' + wpt_id).val());
                        } else {
                            jQuery('input[name=' + wpt_id + ']#' + wpt_id).val(file);
                        }

                        //hidden text set
                        jQuery('#' + hidden_id).val(file);
                        jQuery('#' + hidden_id).prop('disabled', false);
                        //file field disabled and hided
                        jQuery('#' + id).hide();
                        jQuery('#' + id).prop('disabled', true);

                        //remove restore button
                        jQuery('#' + id).siblings(".js-wpt-credfile-undo").hide();

                        var preview_span = jQuery('#' + id).siblings(".js-wpt-credfile-preview");

                        //add image/file uploaded and button to delete
                        if (isImage(file) && data.result.previews) {
                            var preview = data.result.previews[index];
                            var attachid = data.result.attaches[index];

                            //console.log(preview_span);
                            if (typeof preview_span !== undefined) {
                                //console.log(jQuery(preview_span).find("img").length>0);
                                if (jQuery(preview_span).find("img").length > 0 &&
                                        jQuery(preview_span).find("input").length > 0) {
                                    jQuery(preview_span).find("img").attr("src", preview);
                                    jQuery(preview_span).find("input").attr("rel", preview);
                                } else {
                                    //append new image and delete button to the span
                                    jQuery("<img id='loaded_" + myid + "' src='" + preview + "'>").prependTo(preview_span);
                                }
                                
                                if (myid == '_featured_image') {
                                    if (jQuery("#attachid_" + myid).lenght > 0) {
                                        jQuery("#attachid_" + myid).attr("value", attachid);
                                    } else {
                                        jQuery("<input id='attachid_" + myid + "' name='attachid_" + myid + "' type='hidden' value='" + attachid + "'>").appendTo(preview_span.parent());
                                    }
                                }
                            }

                        } else {
                            //<input id='butt_" + myid + "' style='width:100%;margin-top:2px;margin-bottom:2px;' type='button' value='" + settings.delete_text + "' rel='" + file + "' class='delete_ajax_file'>
                            jQuery("<a id='loaded_" + myid + "' href='" + file + "' target='_blank'>" + file + "</a></label>").insertAfter('#' + jQuery(curr_file).attr('id'));
                        }
                        if (typeof preview_span !== undefined)
                            jQuery(preview_span).show();

                        wptCredfile.init('body');
                    });
                    credfile_fu_init();
                }
            },
            add: function (e, data) {
                if (validation_args) {
                    var uploadErrors = [];
                    var acceptFileTypes = new RegExp('/regex' + validation_args + '', 'i'); //^image\/(gif|jpe?g|png)$/i;
                    if (data.originalFiles[0]['type'].length && !acceptFileTypes.test(data.originalFiles[0]['type'])) {
                        uploadErrors.push(validation_message);
                    }
                    if (data.originalFiles[0]['size'].length && data.originalFiles[0]['size'] > 5000000) {
                        uploadErrors.push(settings.too_big_file_alert_text);
                    }
                    if (uploadErrors.length > 0) {
                        alert(uploadErrors.join("\n"));
                    } else {
                        data.submit();
                    }
                } else {
                    data.submit();
                }

            },
            progressall: function (e, data) {
                var progress = parseInt(data.loaded / data.total * 100, 10);
                var id = jQuery(curr_file).attr('id');

                var wpt_id = jQuery('#' + id).siblings(".meter").attr("id"); //id.replace("_file", "");
                jQuery('#' + wpt_id).show();
                //jQuery('#progress_' + wpt_id).css({'width': '100%'});
                jQuery('#' + wpt_id + ' .progress-bar').css(
                        {'width': progress + '%'}
                );
            },
            fail: function (e, data) {
                var id = jQuery(curr_file).attr('id');
                var wpt_id = id.replace("_file", "");
                jQuery('#progress_' + wpt_id).hide();
                //jQuery('#progress_' + wpt_id).css({'width': '100%'});
                jQuery('#progress_' + wpt_id + ' .progress-bar').css(
                        {'width': '0%'}
                );
                alert("Upload Failed !");
            }
        }).prop('disabled', !jQuery.support.fileInput)
                .parent().addClass(jQuery.support.fileInput ? undefined : 'disabled');

        jQuery(document).bind('dragover', function (e) {
            e.preventDefault();
            e.stopPropagation();
            return false;
        });
    }

    function credfile_fu_init() {
        jQuery('input[type="file"]:visible').each(o);

        jQuery(document).off('click', '.js-wpt-credfile-delete, .js-wpt-credfile-undo', null);
        jQuery(document).on('click', '.js-wpt-credfile-delete, .js-wpt-credfile-undo', function (e) {
            jQuery('input[type="file"]:visible').each(o);
        });

        //AddRepetitive add event
        wptCallbacks.addRepetitive.add(function () {
            jQuery('input[type="file"]:visible').each(o);
        });

        //AddRepetitive remove event
        wptCallbacks.addRepetitive.remove(function () {
            //console.log("TODO: delete file related before removing")
        });
    }

//    jQuery('.js-wpt-repadd').on('click', function (e) {
//        e.preventDefault();
//        alert("ciao");
//        jQuery('input[type="file"]').each(o);
//    });

//    jQuery(".wpt-credfile-preview-item").each(function (i) {
//        var max_size = settings.media_settings.width;
//        if (jQuery(this).height() > jQuery(this).width()) {
//            var h = max_size;
//            var w = Math.ceil(jQuery(this).width() / jQuery(this).height() * max_size);
//        } else {
//            var w = max_size;
//            var h = Math.ceil(jQuery(this).height() / jQuery(this).width() * max_size);
//        }
//        jQuery(this).css({height: h, width: w});
//    });

    //Fix the not visible field under false conditional
    jQuery(document).off('click', 'input[type="file"]', null);
    jQuery(document).on('click', 'input[type="file"]', function () {
        credfile_fu_init();
    });

    credfile_fu_init();
});