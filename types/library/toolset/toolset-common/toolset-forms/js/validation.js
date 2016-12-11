/*
 * Validation JS
 *
 * - Initializes validation on selector (forms)
 * - Adds/removes rules on elements contained in var wptoolsetValidationData
 * - Checks if elements are hidden by conditionals
 *
 * @see class WPToolset_Validation
 *
 *
 */
//var wptValidationData = {};

var wptValidationForms = [];
var wptValidationDebug = false;
var wptValidation = (function ($) {
    function init() {
        /**
         * add extension to validator method
         */
        $.validator.addMethod("extension", function (value, element, param) {
            param = typeof param === "string" ? param.replace(/,/g, "|") : param;
            if ($(element).attr('res') && $(element).attr('res') != "")
                return true;
            return this.optional(element) || value.match(new RegExp(".(" + param + ")$", "i"));
        });

        /**
         * add hexadecimal to validator method
         */
        $.validator.addMethod("hexadecimal", function (value, element, param) {
            return value == "" || /(^#[0-9A-F]{6}$)|(^#[0-9A-F]{3}$)/i.test(value);
        });

        /**
         * add skype to validator method
         */
        $.validator.addMethod("skype", function (value, element, param) {
            return value == "" || /^([a-z0-9\.\_\,\-\#]+)$/i.test(value);
        });

        /**
         * add extension to validator method require
         */
        $.validator.addMethod("required", function (value, element, param) {
            //console.log(element.nodeName.toLowerCase() + " " + $(element).attr('name') + " default: " + value + " val: " + $(element).val());
            var _name = $(element).attr('name');
            var _value = $(element).val();

            // check if dependency is met
            if (!this.depend(param, element))
                return "dependency-mismatch";

            switch (element.nodeName.toLowerCase()) {
                case 'select':
                    return _value && $.trim(_value).length > 0;
                case 'input':
                    //Fixing YT cred-196
                    if (jQuery(element).hasClass("wpt-form-radio")) {
                        var val = jQuery('input[name="' + _name + '"]:checked').val();
                        if (wptValidationDebug)
                            console.log("radio " + (typeof val != 'undefined' && val && $.trim(val).length > 0));
                        return typeof val != 'undefined' && val && $.trim(val).length > 0;
                    }

                    //Fixing YT cred-104
                    element = jQuery(element).siblings('input[type="hidden"]');
                    if (element[0] &&
                            !jQuery(element[0]).prop("disabled") &&
                            (jQuery(element[0]).attr('data-wpt-type') == 'file' ||
                                    jQuery(element[0]).attr('data-wpt-type') == 'video' ||
                                    jQuery(element[0]).attr('data-wpt-type') == 'image'
                                    )) {
                        var val = jQuery(element[0]).val();
                        if (wptValidationDebug)
                            console.log("hidden " + (val && $.trim(val).length > 0));
                        return val && $.trim(val).length > 0;
                    }

                    //Fixing YT cred-173
                    element = jQuery(element).siblings('input[type="checkbox"]');
                    if (element[0]) {
                        if (wptValidationDebug)
                            console.log("checkbox " + (element[0].checked));
                        return element[0].checked;
                    }

                    if (jQuery(element).hasClass("hasDatepicker")) {
                        if (wptValidationDebug)
                            console.log("hasDatepicker");
                        return false;
                    }

                    if (this.checkable(element)) {
                        if (wptValidationDebug)
                            console.log("checkable " + (this.getLength(value, element) > 0));
                        return this.getLength(value, element) > 0;
                    }

                    if (wptValidationDebug)
                        console.log(_name + " default: " + value + " val: " + _value + " " + ($.trim(_value).length > 0));

                    return $.trim(_value).length > 0;
                default:
                    return $.trim(value).length > 0;
            }
        });

        /**
         * Add validation method for datepicker adodb_xxx format for date fields
         */
        $.validator.addMethod(
                "dateADODB_STAMP",
                function (a, b) {
                    return this.optional(b) || /^-?(?:\d+|\d{1,3}(?:,\d{3})+)(?:\.\d+)?$/.test(a) && -12219292800 < a && a < 32535215940
                },
                "Please enter a valid date"
                );

        if (wptValidationDebug) {
            console.log("INIT");
            console.log(wptValidationForms);
        }

        _.each(wptValidationForms, function (formID) {
            _initValidation(formID);
            applyRules(formID);
        });
    }

    function _initValidation(formID) {
        if (wptValidationDebug) {
            console.log("_initValidation " + formID);
        }
        var $form = $(formID);
        $form.validate({
            // :hidden is kept because it's default value.
            // All accepted by jQuery.not() can be added.
            ignore: 'input[type="hidden"]:not(.js-wpt-date-auxiliar),:not(.js-wpt-validate)',
            errorPlacement: function (error, element) {
                error.insertBefore(element);
            },
            highlight: function (element, errorClass, validClass) {
                // Expand container
                $(element).parents('.collapsible').slideDown();
                if (formID == '#post') {
                    var box = $(element).parents('.postbox');
                    if (box.hasClass('closed')) {
                        $('.handlediv', box).trigger('click');
                    }
                }
                // $.validator.defaults.highlight(element, errorClass, validClass); // Do not add class to element
            },
            unhighlight: function (element, errorClass, validClass) {
                $("input#publish, input#save-post").removeClass("button-primary-disabled").removeClass("button-disabled");
                // $.validator.defaults.unhighlight(element, errorClass, validClass);
            },
            invalidHandler: function (form, validator) {
                if (formID == '#post') {
                    $('#publishing-action .spinner').css('visibility', 'hidden');
                    $('#publish').bind('click', function () {
                        $('#publishing-action .spinner').css('visibility', 'visible');
                    });
                    $("input#publish").addClass("button-primary-disabled");
                    $("input#save-post").addClass("button-disabled");
                    $("#save-action .ajax-loading").css("visibility", "hidden");
                    $("#publishing-action #ajax-loading").css("visibility", "hidden");
                }
            },
//            submitHandler: function(form) {
//                // Remove failed conditionals
//                $('.js-wpt-remove-on-submit', $(form)).remove();
//                form.submit();
//            },
            errorClass: 'wpt-form-error'
        });

        // On some pages the form may not be ready yet at this point (e.g. Edit Term page).
        jQuery(document).ready(function () {
            //var formclone = $form.clone();
            if (wptValidationDebug)
                console.log($form.selector);

            jQuery(document).off('submit', $form.selector, null);
            jQuery(document).on('submit', $form.selector, function () {
                if (wptValidationDebug)
                    console.log("submit " + $form.selector);

                var myformid = formID.replace('#', '');
                myformid = myformid.replace('-', '_');
                var cred_settings = eval('cred_settings_' + myformid);

                if (typeof grecaptcha !== 'undefined') {
                    var $error_selector = jQuery(formID).find('div.recaptcha_error');
                    if (_recaptcha_id == -1) {
                        if (grecaptcha.getResponse() == '') {
                            $error_selector.show();
                            setTimeout(function () {
                                $error_selector.hide();
                            }, 5000);
                            return false;
                        }
                    } else {
                        if (grecaptcha.getResponse(_recaptcha_id) == '') {
                            $error_selector.show();
                            setTimeout(function () {
                                $error_selector.hide();
                            }, 5000);
                            return false;
                        }
                    }
                    $error_selector.hide();
                }

                if (wptValidationDebug)
                    console.log("validation...");

                if ($form.valid()) {
                    if (wptValidationDebug)
                        console.log("form validated " + $form);

                    $('.js-wpt-remove-on-submit', $(this)).remove();

                    if (cred_settings.use_ajax && cred_settings.use_ajax == 1) {
                        $('<input value="cred_ajax_form" name="action">').attr('type', 'hidden').appendTo(formID);
                        $('<input value="true" name="form_submit">').attr('type', 'hidden').appendTo(formID);

                        $body = $("body");
                        $body.addClass("wpt-loading");

                        $.ajax({
                            type: 'post',
                            url: $(formID).attr('action'),
                            data: $(formID).serialize(),
                            dataType: 'json',
                            complete: function (data) {
                                $body.removeClass("wpt-loading");
                            },
                            success: function (data) {
                                $body.removeClass("wpt-loading");
                                if (data) {
                                    //console.log(data);
                                    $(formID).replaceWith(data.output);
                                    reload_tinyMCE(formID);

                                    if (data.formtype == 'new') {
                                        if (data.result == 'ok') {
//                                            $(':input', formID)
//                                                    .not(':button, :submit, :reset, :hidden')
//                                                    .val('')
//                                                    .removeAttr('checked')
//                                                    .removeAttr('selected');

                                        }

                                        if (data.result != 'redirect') {
                                            check_current_cred_post_id();
                                        }
                                    }

                                    if (data.result == 'ok') {
                                        alert(cred_settings.operation_ok);
                                    } else {
                                        if (data.result != 'redirect')
                                            alert(cred_settings.operation_ko);
                                    }
                                    try_to_reload_reCAPTCHA(formID);
                                }
                            }
                        });
                    }
                } else {
                    if (wptValidationDebug)
                        console.log("form not valid!");
                }
                if (cred_settings.use_ajax && cred_settings.use_ajax == 1)
                    return false;
            });
        });
    }

    var _recaptcha_id = -1;
    function try_to_reload_reCAPTCHA(formID) {
        if (typeof grecaptcha !== 'undefined') {
            var _sitekey = jQuery(formID).find('div.g-recaptcha').data('sitekey');
            _recaptcha_id = grecaptcha.render($('.g-recaptcha')[0], {sitekey: _sitekey});
        }
    }

    function reload_tinyMCE(formID) {
        jQuery('.wpt-wysiwyg').each(function (index) {
            var $area = jQuery(this),
                    area_id = $area.prop('id');
            if (typeof area_id !== 'undefined') {
                tinyMCE.remove();
                tinyMCE.init(tinyMCEPreInit.mceInit[area_id]);
                var quick = quicktags(tinyMCEPreInit.qtInit[area_id]);
                Toolset.add_qt_editor_buttons(quick, area_id);
            }
        });

        if (typeof tinyMCE !== 'undefined') {
            var $area = jQuery('textarea[name="post_content"]'),
                    area_id = $area.prop('id');
            if (typeof area_id !== 'undefined') {
                tinyMCE.remove();
                tinyMCE.init(tinyMCEPreInit.mceInit[area_id]);
                var quick = quicktags(tinyMCEPreInit.qtInit[area_id]);
                Toolset.add_qt_editor_buttons(quick, area_id);
            }
        }
    }

    function isIgnored($el) {
        var ignore = $el.parents('.js-wpt-field').hasClass('js-wpt-validation-ignore') || // Individual fields
                $el.parents('.js-wpt-remove-on-submit').hasClass('js-wpt-validation-ignore'); // Types group of fields
        return ignore;
    }

    function applyRules(container) {
        $('[data-wpt-validate]', $(container)).each(function () {
            _applyRules($(this).data('wpt-validate'), this, container);
        });
    }

    function _applyRules(rules, selector, container) {
        var element = $(selector, $(container));
        if (element.length > 0) {
            if (isIgnored(element)) {
                element.rules('remove');
                element.removeClass('js-wpt-validate');
            } else if (!element.hasClass('js-wpt-validate')) {
                _.each(rules, function (value, rule) {
                    var _rule = {messages: {}};
                    _rule[rule] = value.args;
                    if (value.message !== 'undefined') {
                        _rule.messages[rule] = value.message;
                    }
                    element.rules('add', _rule);
                    element.addClass('js-wpt-validate');
                });
            }
        }
    }

    return {
        init: init,
        applyRules: applyRules,
        isIgnored: isIgnored,
    };

})(jQuery);


jQuery(document).ready(function () {
    wptCallbacks.reset.add(function () {
        wptValidation.init();
    });
    wptCallbacks.addRepetitive.add(function (container) {
        wptValidation.applyRules(container);
    });
    wptCallbacks.removeRepetitive.add(function (container) {
        wptValidation.applyRules(container);
    });
    wptCallbacks.conditionalCheck.add(function (container) {
        wptValidation.applyRules(container);
    });
});