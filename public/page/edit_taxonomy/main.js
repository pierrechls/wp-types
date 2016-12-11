var Types = Types || {};

Types.page = Types.page || {};

Types.page.editTaxonomy = {};

/**
 * Edit Taxonomy page controller.
 *
 * Works together with the legacy script, however completely new code should be added here.
 *
 * @param $ jQuery
 * @constructor
 * @since 2.1
 */
Types.page.editTaxonomy.Class = function($) {

    var self = this;

    self.init = function() {
        self.initRewriteSlugChecker();
    };


    /**
     * Start checking for rewrite slug conflicts within post types and taxonomies.
     *
     * Displays a warning (not error) message after the input field as long as there is a conflict, but doesn't block the
     * form submitting.
     *
     * @since 2.1
     */
    self.initRewriteSlugChecker = function() {
        var rewriteSlugInput = $('input[name="ct[rewrite][slug]"]');

        if (rewriteSlugInput.length == 0) {
            return;
        }

        var checker = Types.slugConflictChecker.build(
            rewriteSlugInput,
            ['post_type_rewrite_slugs', 'taxonomy_rewrite_slugs'],
            'taxonomy_rewrite_slugs',
            $('input[name="ct[wpcf-tax]"]').val(),
            $('input[name="types_check_slug_conflicts_nonce"]').val(),
            function (isConflict, displayMessage) {

                var errorLabel = rewriteSlugInput.parent().find('label.wpcf-form-error.types-slug-conflict');
                if (0 !== errorLabel.length) {
                    errorLabel.remove();
                }

                if (isConflict) {
                    rewriteSlugInput.after(
                        '<label class="wpcf-form-error types-slug-conflict">' + displayMessage + '</label>'
                    );
                }
            }
        );

        checker.bind();

        // Check even if rewrite is not enabled at the moment. When enabled later, the warning will be already in place.
        if (rewriteSlugInput.val().length > 0) {
            checker.check();
        }
    };

    $(document).ready(self.init);
};


Types.page.editTaxonomy.main = new Types.page.editTaxonomy.Class(jQuery);


