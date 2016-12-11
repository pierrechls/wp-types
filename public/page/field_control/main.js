var Types = Types || {};

// the head.js object
Types.head = Types.head || head;

Types.page = Types.page || {};

// Everything related to this page.
Types.page.fieldControl = {};
Types.page.fieldControl.viewmodels = {};
Types.page.fieldControl.strings = {};


/**
 * Page controller class.
 *
 * Handles page initialization.
 *
 * @param $ jQuery
 * @constructor
 */
Types.page.fieldControl.Class = function ($) {

    var self = this;

    /**
     * Turns on debug messages.
     * @type {boolean}
     * @since 2.0
     */
    self.isDebug = false;


    self.init = function () {

        // Read model data, a JSON string encoded in base64 to avoid parsing issues.
        var modelData = $.parseJSON(WPV_Toolset.Utils.editor_decode64($('#types_model_data').html()));
        self.debug('modelData', modelData);
        
        //noinspection JSUnresolvedVariable
        Types.page.fieldControl.jsPath = modelData.jsIncludePath;
        
        self.initTemplates(modelData);
        self.initStaticData(modelData);
        self.initAjax();
        self.initKnockout();
        self.initDialogs();

        // Continue after loading the view of the listing table.
        Types.head.load(

            Types.page.fieldControl.jsPath + '/viewmodels/BulkChangeManagementStatusDialogViewModel.js',
            Types.page.fieldControl.jsPath + '/viewmodels/DeleteDialogViewModel.js',
            Types.page.fieldControl.jsPath + '/viewmodels/ChangeAssignDialogViewModel.js',
            Types.page.fieldControl.jsPath + '/viewmodels/ChangeFieldTypeDialogViewModel.js',
            Types.page.fieldControl.jsPath + '/viewmodels/FieldDefinitionViewModel.js',
            Types.page.fieldControl.jsPath + '/viewmodels/ListingViewModel.js',

            function() {

                // Render the listing.
                self.viewModel = new Types.page.fieldControl.viewmodels.ListingViewModel(modelData.fieldDefinitions);

                self.afterInit();
            }
        );

    };


    /**
     * Initialize the getter function for templates.
     *
     * Reads the data passed from PHP and assigns a function to Types.page.fieldControl.getTemplate that will
     * retrieve the template content. This way it becomes the single method of retrieving a template.
     *
     * @param modelData
     * @since 2.0
     */
    self.initTemplates = function(modelData) {

        if( _.has(modelData, 'templates') && _.isObject(_.property('templates')(modelData))) {

            Types.page.fieldControl.templates = new function() {

                var templates = this;

                templates.raw = _.property('templates')(modelData);

                /**
                 * @param {string} templateName
                 * @returns {string} Raw template content.
                 */
                templates.getRawTemplate = function(templateName) {
                    if(_.has(templates.raw, templateName)) {
                        return templates.raw[templateName];
                    } else {
                        self.log('Template "' + templateName + '" not found.');
                        return '';
                    }
                };

                templates.compiledUnderscoreTemplates = {};

                /**
                 * @param {string} templateName
                 * @returns {function} Compiled underscore template
                 */
                templates.getUnderscoreTemplate = function(templateName) {
                    if(!_.has(templates.compiledUnderscoreTemplates, templateName)) {
                        templates.compiledUnderscoreTemplates[templateName] = _.template(templates.getRawTemplate(templateName));
                    }
                    return templates.compiledUnderscoreTemplates[templateName];
                };


                /**
                 * Compile an underscore template (with using cache) and render it.
                 *
                 * @param {string} templateName
                 * @param {object} context Underscore context for rendering the template.
                 * @returns {string} Rendered markup.
                 */
                templates.renderUnderscore = function(templateName, context) {
                    var compiled = templates.getUnderscoreTemplate(templateName);
                    return compiled(context);
                };

            };

        }
        
    };


    /**
     * Fill the Types.page.fieldControl.strings object with data passed from PHP.
     *
     * @param modelData
     * @since 2.0
     */
    self.initStaticData = function(modelData) {

        Types.page.fieldControl.strings = modelData.strings || {};

        Types.page.fieldControl.strings.misc = Types.page.fieldControl.strings.misc || {};

        Types.page.fieldControl.fieldTypeDefinitions = modelData.fieldTypeDefinitions || {};
        
        Types.page.fieldControl.ajaxInfo = modelData.ajaxInfo || {};
        
        Types.page.fieldControl.currentDomain = modelData.currentDomain || {};

        Types.page.fieldControl.groups = modelData.groups || {};

        Types.page.fieldControl.typeConversionMatrix = modelData.typeConversionMatrix || {};

        Types.page.fieldControl.itemsPerPage = modelData.itemsPerPage || {};
    };


    /**
     * Initialize everything AJAX-related here.
     *
     * @since 2.0
     */
    self.initAjax = function() {

        /**
         * Ensure that response is always an object with the success property.
         *
         * If it's not, return a dummy object indicating a failure.
         *
         * @param response {*} Response from the AJAX call.
         * @returns {{success: boolean}} Sanitized response.
         *
         * @since 2.0
         */
        var parseResponse = function(response) {
            if( typeof(response.success) === 'undefined' ) {
                self.log("parseResponse: no success value", response);
                return { success: false };
            } else {
                return response;
            }
        };


        /**
         * Perform an AJAX call on field definitions.
         *
         * @param {string} fieldAction Name of the action, see Types_Ajax::callback_field_control_action() for details.
         * @param {string} nonce Name of the nonce for this action.
         * @param {object|[object]} fields One or more models of fields this action applies to. 
         * @param {object} data Custom action-specific data.
         * @param {function} successCallback Callback to be used after AJAX call is completed. It will get two parameters,
         *     the complete AJAX response and the 'data' element for convenience.
         * @param {function} [failCallback] Analogous to successCallback for the case of failure. If missing,
         *     successCallback will be used instead.
         *
         * @since 2.0
         */
        Types.page.fieldControl.doAjax = function(fieldAction, nonce, fields, data, successCallback, failCallback) {


            if(!_.isArray(fields)) {
                fields = [fields];
            }

            var ajaxData = {
                action: 'types_field_control_action',
                field_action: fieldAction,
                wpnonce: nonce,
                fields: fields,
                domain: Types.page.fieldControl.currentDomain,
                action_specific: data
            };

            
            if( typeof(failCallback) == 'undefined' ) {
                failCallback = successCallback;
            }

            $.ajax({
                async: true,
                type: 'POST',
                url: ajaxurl,
                data: ajaxData,

                success: function(originalResponse) {
                    var response = parseResponse(originalResponse);

                    self.debug('AJAX response', ajaxData, originalResponse);

                    if(response.success) {
                        successCallback(response, response.data || {});
                    } else {
                        failCallback(response, response.data || {});
                    }
                },

                error: function( ajaxContext ) {
                    console.log('Error:', ajaxContext.responseText);
                    failCallback({ success: false, data: {} }, {});
                }
            });

        }
    };


    /**
     * Initialize custom Knockout bindings and other modifications.
     *
     * @since 2.0
     */
    self.initKnockout = function() {

        // Taken from http://knockoutjs.com/examples/animatedTransitions.html
        // Here's a custom Knockout binding that makes elements shown/hidden via jQuery's fadeIn()/fadeOut() methods
        ko.bindingHandlers.fadeVisible = {
            init: function(element, valueAccessor) {
                // Initially set the element to be instantly visible/hidden depending on the value
                var value = valueAccessor();
                $(element).toggle(ko.unwrap(value)); // Use "unwrapObservable" so we can handle values that may or may not be observable
            },
            update: function(element, valueAccessor) {
                // Whenever the value subsequently changes, slowly fade the element in or out
                var value = valueAccessor();
                ko.unwrap(value) ? $(element).fadeIn() : $(element).fadeOut();
            }
        };


        var applyDisplayMode = function(displayMode, element, immediately) {
            switch(displayMode) {
                case 'show':
                    element.css('visibility', 'visible');
                    if(immediately) {
                        element.show();
                    } else {
                        element.slideDown().css('display', 'none').fadeIn();
                    }
                    break;
                case 'hide':
                    element.css('visibility', 'hidden');
                    if(immediately) {
                        element.show();
                    } else {
                        element.slideDown();
                    }
                    break;
                case 'remove':
                    if(immediately) {
                        element.hide();
                    } else {
                        element.slideUp().fadeOut();
                    }
                    element.css('visibility', 'hidden');
                    break;
            }
        };


        /**
         * Binding for displaying an element in three modes:
         * 
         * - 'show' will simply display the element
         * - 'hide' will hide it, but leave the free space for another message to be displayed soon
         * - 'remove' will hide it completely
         * 
         * Show/remove values use animations.
         * 
         * @since 2.0
         */
        ko.bindingHandlers.threeModeVisibility = {
            init: function(element, valueAccessor) {
                var displayMode = ko.unwrap(valueAccessor());
                applyDisplayMode(displayMode, $(element), true);
            },
            update: function(element, valueAccessor) {
                var displayMode = ko.unwrap(valueAccessor());
                applyDisplayMode(displayMode, $(element), false);
            }
        };

        
        var highlightedBorder = function(element, valueAccessor) {
            var isHighlighted = ko.unwrap(valueAccessor());
            if(isHighlighted) {
                $(element).addClass('types-highlighted-border').focus();
            } else {
                $(element).removeClass('types-highlighted-border');
            }
        };
        
        /**
         * Binding for highlighting a button with selected field type.
         *
         * @since 2.0
         */
        ko.bindingHandlers.highlightedBorder = {
            init: highlightedBorder,
            update: highlightedBorder
        };


        var redButton = function(element, valueAccessor) {
            var isRed = ko.unwrap(valueAccessor());
            if(isRed) {
                $(element).addClass('types-delete-button');
            } else {
                $(element).removeClass('types-delete-button');
            }
        };


        /**
         * Add or remove a class that makes a button red.
         * 
         * @since 2.0
         */
        ko.bindingHandlers.redButton = {
            init: redButton,
            update: redButton
        };

        var disablePrimary = function(element, valueAccessor) {
            var isDisabled = ko.unwrap(valueAccessor());
            if(isDisabled) {
                $(element).prop('disabled', true).removeClass('button-primary');
            } else {
                $(element).prop('disabled', false).addClass('button-primary');
            }
        };

        /**
         * Disable primary button and update its class.
         *
         * @since 2.0
         */
        ko.bindingHandlers.disablePrimary = {
            init: disablePrimary,
            update: disablePrimary
        };

    };


    /**
     * Initialize the dialog handler for displaying standard Toolset dialogs.
     * 
     * @since 2.0
     */
    self.initDialogs = function() {
        Types.page.fieldControl.dialogHandler = new function() {

            var self = this;

            self.create = function(dialogId, title, templateContext, buttons, options) {

                var dialogDuplicate = DDLayout.DialogView.extend({});

                var dialog = new dialogDuplicate(_.defaults(options || {}, {
                    title: title,
                    selector: '#' + dialogId,
                    template_object: templateContext,
                    buttons: buttons,
                    width: 600
                }));
                
                return dialog;
            }

        };
    };


    /**
     * Custom actions after the listing table is rendered.
     * 
     * @since 2.0
     */
    self.afterInit = function() {

        var pageContent = $('#types-page-content');

        // Show the listing after it's been fully rendered by knockout.
        pageContent.find('.types-listing-spinner').hide();
        pageContent.find('.types-listing-wrapper').show();

        // Focus the search field so that the user can start typing immediately.
        pageContent.find('.types-field-search').focus();

        // Immediately apply the item per page setting without reloading the page.
        $('#types_field_control_fields_per_page').change(function() {
            self.viewModel.itemsPerPage($(this).val());
        });

        Types.page.menuLinkAdjuster.addMenuParams({key: 'domain', value: Types.page.fieldControl.currentDomain});
        
    };


    /**
     * Log all arguments to console if debugging is turned on.
     *
     * @since 2.0
     */
    self.debug = function () {
        if (self.isDebug) {
            console.log.apply(console, arguments);
        }
    };


    self.log = function() {
        console.log.apply(console, arguments);
    };


    $(document).ready(self.init);

};


Types.page.fieldControl.main = new Types.page.fieldControl.Class(jQuery);