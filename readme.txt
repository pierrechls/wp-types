=== Toolset Types ===
Contributors: AmirHelzer, brucepearson, christianglingener, jadpm, zaantar
Donate link: http://wp-types.com
Tags: CMS, custom field, custom fields, custom post type, custom post types, field, fields post, post type, post types, taxonomies, taxonomy, toolset
Text Domain: wpcf
Domain Path: /embedded/locale
License: GPLv2
Requires at least: 3.7
Tested up to: 4.7
Stable tag: 2.2.5

The complete and reliable plugin for managing custom post types, custom taxonomies and custom fields.

== Description ==

**Types let's you customize the WordPress admin by adding content types, custom fields and taxonomies. You will be able to craft the WordPress admin and turn it into your very own content management system.**

[vimeo https://vimeo.com/176428571]

= COMPLETE DOCUMENTATION, POWERFUL API, SIMPLE GUI FOR NON-CODERS =
If you're an experienced PHP developer, you'll appreciate Types comprehensive [fields API](https://wp-types.com/documentation/customizing-sites-using-php/functions/).

You will find detailed guides on [adding custom post types, fields and taxonomy to the front-end](https://wp-types.com/documentation/customizing-sites-using-php/), including:

* [Creating templates for single custom posts](https://wp-types.com/documentation/customizing-sites-using-php/creating-templates-single-custom-posts)
* [Creating templates for custom post type archives](https://wp-types.com/documentation/customizing-sites-using-php/creating-templates-custom-post-type-archives)
* [Creating custom user profiles](https://wp-types.com/documentation/customizing-sites-using-php/creating-custom-user-profiles)
* [Create taxonomy term archives](https://wp-types.com/documentation/customizing-sites-using-php/creating-taxonomy-term-archives)

and [more](https://wp-types.com/documentation/customizing-sites-using-php/).

**Too much technical stuff to learn?** The full [Toolset](http://wp-types.com) package lets you build complete WordPress sites from within the admin dashboard.

= CUSTOM FIELDS FOR CONTENT AND USERS =
Types lets you add custom fields for both posts (meaning, WordPress posts, pages and custom content types), as well as users. You can add any field types to different user profiles.

= ACCESS CONTROL FOR FIELDS =
Using [Access](https://wp-types.com/home/toolset-components/#access), you will be able to control what fields different users can edit and view. This way, you can make some field groups read-only for certain users, and fully-editable for other users.

For example, when you build a membership site, the site admin will be able to change membership levels for everyone and users will see their membership fields as read-only.

= DESIGN CUSTOM FIELDS WITH EASE =
Types fields come with a built-in CSS editor, letting you design how fields appear in the WordPress admin. You can design both full-edit and read-only field display modes.

= RELIABLE SUPPORT =
To get support for Types, please join our [technical support forum](http://wp-types.com/forums/). You will receive support directly from our developers, helping you deliver great sites on time and correctly.

= CUSTOM FIELDS =

Types includes support for a wide list of custom fields.

* **Single-line text**
* **Multi-line text**
* **WYSIWYG** (WordPress Visual Editor)
* **Checkbox**
* **Multi-value Checkboxes**
* **Radio group**
* **Drop-down Select**
* **File upload**
* **Image** (Types includes a robust image-resize and caching engine)
* **Date** (includes a JS date-picker)
* **Email**
* **Number**
* **Phone**
* **Skype**
* **URL**
* **Audio**
* **Video**
* **Embedded media**
* **Colorpicker**
* **Post reference** (using Types Parent / Child relationships management)

Types custom fields use the standard WordPress post-meta table, making it cross-compatible with any theme or plugin. Additionally, all fields can be **repeating fields**.

= CUSTOM POST TYPES AND TAXONOMIES =

Types lets you easily setup custom post types and taxonomies. You can create new post types in seconds and use the advanced settings to customize every aspect.

* Full control over every feature
* Associate taxonomies with custom post types
* Integrated control over custom fields display for different post types

= BUILD RELATIONAL SITES =

Types lets you define parent / child relationship between different post types. You'll easily setup one-to-many and many-to-many relationships and build powerful sites.

= MULTILINGUAL READY =

Types is the only custom fields and post types plugin that's built multilingual-ready. It plays perfectly with [WPML](http://wpml.org). You'll be able to translate everything, including texts and labels in the WordPress admin and user-content for front-page.

= BUILT FOR STABILITY =

Types is part of a family of plugins, including WPML and Toolset, developed and maintained by [OnTheGoSystems](http://www.onthegosystems.com). Our plugins power over 500,000 commercial sites, using WordPress as a complete CMS. While we love features, we know that stability, performance, usability and security are critical. All our plugins go through comprehensive testing, QA and security analysis before every release.

== Installation ==

1. Upload 'types' to the '/wp-content/plugins/' directory
2. Activate the plugin through the 'Plugins' menu in WordPress


== Frequently Asked Questions ==

= How can I display custom post types on the home-page? =

By default, WordPress will either display your blog posts or a specific page on the home-page.

To display custom post types on the home-page, you have two options:

1. If you're comfortable with PHP and WordPress API, edit the site's template files (probably index.php) and load the custom post types there. Different themes do this differently, so we can't really say what single approach works best. You should look at [get_posts](http://codex.wordpress.org/Template_Tags/get_posts), which is part of the WordPress Template Tags system.
2. If you want to build sites right away, without becoming an expert in WordPress API and try our [Toolset Views](http://wp-types.com/). You'll be able to load whatever content you need from the database and display it anywhere and in whatever way you choose.

We're sorry, but we don't know of any third option which is both free and requires no coding.

= Can I use Types without Views? =

Sure you can! Types, by itself, replaces several other plugins that define custom types and fields. We believe that it does it much better, but it's up to you to decide.

If you also buy Views, with Toolset, you'll have a complete solution for both **defining** and **displaying** custom data. You can achieve everything that Views does if you're fluent in PHP and know WordPress API. When you buy Views, you're also supporting Types development, but we're not looking for donations. You should consider Views for its value and nothing else.

= I am already a ninja developer, do I really need Views? =

We honestly think so. Even if you're an expert developer, do you really enjoy doing the same stuff over and over again? With Views, you can concentrate on the unique features of every new site that you build, without wasting time on routine stuff.

Views was originally inspired by the Drupal module with the same name. Around 90% of all Drupal sites use the Drupal Views module and many consider it as one of the most powerful features in Drupal. Now, you too can enjoy the same power (and even more), but without any of the complexity of Drupal.

= Can Types display custom fields that I defined somehow else? =

Yes! You can tell Types to manage any other custom fields. For example, if you're using an e-commerce plugin, you can tell Types to manage product pricing. This will greatly help you display these fields with Types API or with Views.

Go to Custom fields control, under the Types menu. There, you can tell Types to manage existing custom fields.

= How do I migrate an existing site to use Types? =

The most important thing is to remember not to define custom post types and taxonomies in more than one place. If you previously defined them in PHP, first, remove your PHP code with the manual definition. The content will appear to temporarily vanish. Don't panic. Now, redefine the same custom post types and taxonomies with Types. Everything will return :-)

Types also includes data import from other plugins such as Custom Post UI and Advanced Custom Fields.

= Can I import and export my Types settings? =

Yes! Types includes its own import and export features, using an XML settings file. If you create a development site, you can easily transfer settings to the production site.

= What is the advantage of using Types over separate plugins for custom post types, taxonomies and fields? =

Types offers a much richer integration, which is simply impossible with separate plugins. For example, you have fine-grained control of where to display custom meta-boxes. Because Types defines both the post types and fields, we have greater control of where things can go.

Additionally, Types is the only plugin that lets you define parent/child relationships between different post types AND use that information to edit child data when editing a parent.


== Screenshots ==

1. Post Types list
2. Custom Taxonomies list
3. Edit Post Type
4. Edit Taxonomy
5. Editing post field group
6. Editing user field group
7. Inserting custom fields to content
8. Custom fields on the post editing page
9. Bulk editing child content using Field Tables

== Changelog ==

= 2.2.5 =

* Use a different validation rule for file fields that also allows domains without TLD.
* Fix a CRED issue with added validation rules.
* Handle several issues related to using "0" as a default field value and saving it to the database.
* Fix an edge-case bug when loading field groups on the Edit Post page.

= 2.2.4 =

* Fix an issue with registering custom taxonomies in WordPress 4.7.
* Implement an alternative escaping mechanism for custom format setting of the date field.
* types_render_shortcode() function and [types] shortcode now allows to use "id" attribute for $parent-post selection
* Exclude Media from post relationships since the current GUI isn't able to support it properly
* Fix exporting taxonomies with legacy "object_type" setting that was causing syntax errors in the output XML.
* Support all CPTs in Toolset Dashboard.
* Change the way we store the context of a Types field for string translation (use field group name instead of ID)
* Fix a WordPress 4.7 compatibility issue with direct access to $wp_filter.
* Make the manipulation with repetitive user field values more similar to post fields. Fix a front-end notice when there is only one value in a repeating user Skype field.
* Add missing mandatory URL validation to file fields.
* Add PHP template example files.
* Fix an issue with Types export and non-latin characters in a field group slug.

= 2.2.3 =

* Fixed several security issues.

= 2.2.2 =

* Toolset Dashboard now supports custom post types created by theme or other plugins
* Updated select2 to version 4
* Fixed issue using [types] shortcode in CRED notification
* Fixed minor incompatibility with the latest version of Toolset Access.
* Fixed issue with custom values for checkboxes fields not being respected.

= 2.2.1 =

* Fixed path for Toolset Installer

= 2.2 =

* Added feature to output title, description, caption and alt text using placeholders in image field.
* Added types_import_from_zip_file API filter.
* Added types_finished_saving_child_posts API action as a workaround for https://core.trac.wordpress.org/ticket/17817.
* Fixed error when generic field definitions are queried by Types-specific arguments.
* Fixed warnings when saving a child post with repetitive field in the parent's edit page.
* Fixed issue where internal "Types Term Groups" post type was appearing in some places that it should not.
* Fixed issue in RTL languages where save button was being overlapped by cancel button on field group conditions.
* Fixed issue with post relationship query in WPML default language.
* Fixed issue with term field checkboxes options which were not able to be edit
* Fixed performance issue with frontend display table.
* Fixed issue with same fields in multiple field groups and they are now being displayed only once in post/user/term edit pages.

= 2.1 =

* Added dashboard for Toolset.
* Added setting to hide “Front-end Display” table.
* Added types_filter_query_field_definitions API filter.
* Added Import and Export support for term fields and field groups.
* Added ability to add term fields values on Add Term page.
* Added warning about possible conflicts between post type and taxonomy rewrite slugs.
* Added feature in Edit Field Group where fields with errors will be expanded on submit to make the error visible to the user.
* Added feature on term listing page to hide term field columns, if there are too many and user has not changed screen options .
* Added feature on post type edit screen to also display complex fields in “Post Fields to be shown as columns in Post Type listing in WordPress Admin” section, even if they are not available in the listing.
* Added ability to delete a taxonomy in edit page.
* Added ability to delete a field group in edit page.
* Added support for "id" in types_render_field() function.
* Changed label "Numeric" to "Number".
* Updated “Where to include this Field Group” section, which is no longer a meta box and is now placed under “Name and description”.
* Updated “Front-end Display” table, which will no longer display "Template" for built-in post types.
* Updated “Cancel” links in dialogs, which are now buttons to match WordPress style.
* Updated [types] shortcode attributes and they are now using single quotes, instead of double quotes.
* Updated screen header tags to h1, instead of h2.
* Updated plugin license information.
* Updated POT language file.
* Fixed issue with child post types not created by Types that could not be edited in child-relationship table.
* Fixed issue with name and singular name of post types and post field groups that were not being sanitised.
* Fixed issue with Types admin screens access when user role has been degraded from Administrator role.
* Fixed issue with post count display in "At a Glance" section, when it was above a thousand.
* Fixed issue in Conditional Display that advanced logic did not work without simple logic.
* Fixed issue in Conditional Display that simple logic was always being displayed, even if advanced logic was used.
* Fixed issue in Conditional Display that slugs with hyphens were not usable in advanced logic.
* Fixed issue with field control pages that were not displaying all groups.
* Fixed issue with fields that changing a slug removed the field conditions.
* Fixed issue with term fields showing field content instead of "Display text".

= 2.0.1 =

* Release date: 2016-04-12
* Fixed issue with information table and Layouts.

= 2.0 =

* Release date: 2016-04-11
* Revamped Field Control page for all field domains.
* Added information table on post, post type and field group edit pages.
* Added columns with previews of field values in taxonomy term listing pages.
* Fixed issue with cursor that was moved automatically to the end of the slug input.
* Fixed issue with slugs that could not be numeric.
* Fixed disabled buttons when saving a child post in post-relationship table.
* Fixed upgrade issue from version 1.9 to 1.9.1 that was removing assignment between custom post types and native taxonomies.
* Fixed issue with initial expression of "Advanced text mode" in data-dependent display conditions for field groups.
* Fixed issue with special characters when displaying taxonomy names on the Edit Taxonomy page.
* Fixed issue with special characters when displaying term names on the Edit Post Field Group page.

= 1.9.1 =

* Released date: 2016-03-08
* Fixed javascript infinite loop in post field group edit page.
* Fixed conflict between post relationship table and WordPress heartbeat ajax call.
* Fixed field slug limit of 20 characters.
* Fixed issue with filtering by multiple checkboxes fields that failed to return results.
* Fixed custom post type icon on "At a Glance" section.
* Fixed hidden visibility option of post types that did not work for built-in types.
* Fixed issue when adding an existing field that was always attached to bottom of the list.
* Fixed issue with parent custom post type that could not be stored in "Select child fields from Child to be displayed in Post Relationship table"
* Fixed options of unsaved checkbox / select / radio fields weren't sortable.
* Fixed an issue when adding a existing field to another group.
* Fixed an issue with cursor that was moved automatically to the end of slug input.

= 1.9 =

* Release date: 2016-02-17
* New: Taxonomy term meta (custom fields for taxonomy).
* Major upgrade to user interface.
* Renamed "Custom Fields" to "Post Fields".
* Added post arguments “show_rest” and “rest_base” to options on post type edit screen.
* Added ability to rename built-in post types “Posts”, “Pages” and “Media”.
* Added new filter "wpcf_exclude_meta_boxes_on_post_type" that allows to exclude own Post Types from wpcf_add_meta_boxes() function in order to avoid adding Types meta boxes to certain custom posts.
* Added ability to modify the title placeholder, displayed when creating a new post, for each post type.
* Added ability to deactivate built-in taxonomies.
* CPTs can now be positioned anywhere in the admin menu.
* Promotional messages can now be disabled in settings.
* Fixed date fields so they properly display "hour" and "minutes" when they provide those options.
* Fix wrong field type conversion options for checkboxes fields.
* Avoid clearing the roles for the current user as global when editing a user by visiting its profile.
* Properly escape data used as attributes on a javascript methods controlling the post types, taxonomies, Content Templates or user roles assigned to a field group.
* Ensure a user creating children posts on a Fields Table table has the right capabilities to do so, including Access rights.
* Extend meta queries coming from Views so they work with Types checkboxes fields for users and taxonomy terms. Also, allow filtering by checkbox value in addition to checkbox title.
* Allow to filter checkboxes fields by a value that contains a comma in its title.
* Fix pagination in the Fields Table of a parent post type: it was returning the posts per page setting to its default state of 5.
* Fix the Next pagination button missing on Fields Tables when the table is set to show N children each time and you have N+1 children assigned to that parent.
* Fix custom taxonomy export/import when it is attached to a post type whose slug starts with a number.

= 1.8.11 =

* Release date: 2015-12-07
* Fixed compatibility with WPML related to custom field translations.
* Fixed compatibility with WordPress 4.4 related to menu management.
* Added 'action' to the list of reserved words that can not be used to name post types or taxonomies.

= 1.8.10 =

* Release date: 2015-11-18
* Added filter "wpcf_init_custom_types_taxonomies".

= 1.8.9 =

* Release date: 2015-11-10
* Changed Installer version to 1.7
* Changed Common version to 1.8

= 1.8.8 =

* Release date: 2015-11-02
* Replaced esc_attr_e to esc_attr in skype field.
* Changed Installer version to 1.6.8.

= 1.8.7.2 =

* Release date: 2015-10-28
* Fixed the problem with select post parent if WPML is active.
* Replaced sum of array by array merge to avoid losing values.

= 1.8.7.1 =

* Release date: 2015-10-20
* Added check to do not translate if value to translate is empty or not a string.

= 1.8.7 =

* Release date: 2015-10-18
* Fixed a problem with shortcode playlist.
* Fixed a problem with backslash in WYSIWYG field name.
* Improved WPML integration, replace `icl_t()` by filter `wpml_translate_single_string`.
* Changed Installer version to 1.6.7 - to reduce requests to Toolset API.

= 1.8.6.2 =

* Release date: 2015-09-29
* Fixed a problem with "Feature Image".
* Fixed a problem with loading parent data in child table.

= 1.8.6.1 =

* Release date: 2015-09-28
* Fixed a problem with get_plugins() function is some Installer actions.

= 1.8.6 =

* Release date: 2015-09-28
* Replaced parameter "numberposts" with "posts_per_page" in post relationships query.
* Fixed a meta post data before use and if is too complex just do not handle this in Types.
* Fixed a problem with selecting file in child tabele when is no WYSIWYG or other file field on edit screen.
* When we get User Group we added information about affected roles.
* Fixed a problem with playlist.

= 1.8.5 =

* Release date: 2015-09-02
* Fixed a problem with display post parent in post children table after pagination.
* Fixed a problem with display post parent in post children table after sorting.

= 1.8.4 =

* Release date: 2015-09-01
* Fixed a problem with display post parent in post children table after pagination.
* Fixed a problem with some AJAX action (can't add new field, can't choose child fields, etc. etc.)

= 1.8.3 =

* Release date: 2015-08-31
* Fixed a problem with saving child posts when author role is "Author".

= 1.8.2 =

* Release date: 2015-08-27
* Fixed a problem with selecting child post Events from Event Calendar when evens are "expired".

= 1.8.1 =

* Release date: 2015-08-25
* Fixed a problem with select2 and new child posts.

= 1.8 =

* Release date: 2015-08-17
* Added the ability to choose Custom Field as a column for Posts and Pages.
* Added the ability to select the Feature image in the child Post table.
* Added the ability to turn off the standard WordPress Custom Fields metabox.
* Added default value to a Custom Field.
* Added an "Edit" button for the parent post on the child Post editing page.
* Added an error message for Custom Fields in children entries.
* Added a new filter “wpcf_config_options_(type)”.
* Added "ico" file type as a proper image file.
* Added "parent" keyword into the list of reserved words for Custom Post Type or Custom Taxonomy name.
* Added "select2" options script in parent Post field.
* Changed "View template" string to "Content Template" in order to avoid inconsistencies.
* Added a check for Custom Fields in order to display only active ones in lists.
* Fixed a problem about the visibility of Types button in Post edit screen editor, when there are User Fields groups but no Custom Fields groups available.
* Fixed a problem about the Module Manager box.
* Fixed a problem with the usage of "dashboard_glance_items" filter.
* Fixed a problem about alternative text and title of repetitive image fields.
* Fixed a problem about lost translation when a parent Post was saved (https://wpml.org/forums/topic/custom-post-type-relationships/).
* Fixed a conflict of validation field with CRED plugin.
* Removed "Styling Editor" section in Custom Fields groups and User Fields groups edit screen. In order to enable it again, you can define “TYPES_USE_STYLING_EDITOR” constant in wp-config.php file.
* Improved Module Manager box in Custom Post Type and Custom Taxonomy edit screen.
* Moved marketing message into the "Need Help?" tab.
* Reviewed "Add New" buttons in all edit screens for consistency.
* Removed the "auto-import" option.
* Turned off migration from "Advanced Custom Fields Pro", as this plugin has different data structure than "Advanced Custom Fields".
* Updated list of Font Awesome icons.
* Updated Skype field according to the new Skype Buttons API.

= 1.7.11 =

* Release date: 2015-08-05
* Fixed a problem when saving HTML in meta fields.

= 1.7.10 =

* Release date: 2015-08-04
* Fixed a problem when saving HTML in meta fields.

= 1.7.9 =

* Release date: 2015-08-04
* Fixed a problem when saving HTML in meta fields.

= 1.7.8 =

* Release date: 2015-08-03
* Fixed WYSIWYG field for WP 4.2.3 security release.
* Added HTML frontend rendering settings.

= 1.7.7 =

* Release date: 2015-07-21
* Fixed a problem with the Getting Started tutorials.

= 1.7.6 =

* Release date: 2015-07-20
* Changed Installer version to 1.6.4 - to reduce load time and avoid to much update requests.

= 1.7.6 =

* Release date: 2015-07-20
* Changed Installer version to 1.6.4 - to reduce load time and avoid to much update requests.

= 1.7.5 =

* Release date: 2015-07-15
* Fixed a problem with Custom Fields Group edit screen to allow (again) underscore in Custom Fields names. https://wp-types.com/forums/topic/underscores-in-custom-field-names-possible-bug/

= 1.7.4 =

* Release date: 2015-07-09
* Changed Installer version to 1.6.1

= 1.7.3 =

* Release date: 2015-06-25
* Fixed problem with "View All" in menu builder for Custom Post Types. https://wp-types.com/forums/topic/appearance-menu-php-errornotice/

= 1.7.2 =

* Release date: 2015-06-23
* Field a problem with "file type" field on post edit screen when is no WYSIWYG editor. https://wordpress.org/support/topic/image-field-not-working-1

= 1.7.1 =

* Release date: 2015-06-22
* Fixed a problem with constant ICL_SITEPRESS_VERSION https://wordpress.org/support/topic/types-17-notice-undefined-constant-icl_sitepress_version
* Field a problem with "file type" field on user profile screen. https://wordpress.org/support/topic/image-field-not-working-1
* Types and Access integration - You can define roles and control who can add, edit or change Custom Post Types, Custom Taxonomies, Custom Field Groups and User Meta Groups.

= 1.7 =

* Release date: 2015-06-15
* Added the word "mode" to the list of words reserved by WordPress. https://wp-types.com/forums/topic/when-types-is-activated-i-cant-filter-articles-by-category-in-the-wp-backend/
* Added the feature that automatically creates a slug for the Custom Post Type and Custom Taxonomy.
* Added bulk delete options to Custom Field Groups listing page.
* Added bulk delete options to Custom Post Types listing page.
* Added bulk delete options to Custom Taxonomies listing page.
* Added the automatic check of availability for the "Title" and "Editor" fields in the child relationship dialog.
* Added the duplicate option for Custom Post Type and Custom Taxonomy.
* Added the "Excerpt" field to the Child Posts table.
* Added the "wpcf_field_image_max_width" filter which allows user to change image width on admin listing pages.
* Added the option to specify the custom archive slug for the Custom Post Type http://wp-types.com/forums/topic/specify-cpt-archive-slug-as-string/
* All custom fields on Custom Post Type listing pages are now sortable.
* Changes to the Types fields GUI for easier support.
* Fixed a problem with Custom Post Type, Custom Taxonomy and Custom Fields Group editing pages where forms would "freeze" after validation fails.
* Fixed a problem with selecting an image for the Custom Image Field in the Child Posts table, after using the "Add New", "Save All" and "Save" buttons.  https://wp-types.com/forums/topic/featured-image-cannot-be-changed-after-first-save/
* Fixed a problem with multi-line field not being wrapped with P (paragraph) HTML tag https://wp-types.com/forums/topic/multi-line-text-fields-are-missing-paragraph-tags/
* Fixed problem with fields being covered by colorbox on the Custom Post Type editing page.
* Fixed an issue where a wrong message was displayed when minimum number of characters has not been reached.

= 1.6.6.6 =

* Release date: 2015-06-10
* Fixed problem with "playlist" word.  https://wp-types.com/forums/topic/front-end-warning-from-wysiwyg-php/

= 1.6.6.5 =

* Release date: 2015-05-20
* Fixed problem with Uncaught ReferenceError: pagenow is not defined. http://wp-types.com/forums/topic/nextgen-gallery-broken-urgent/

= 1.6.6.4 =

* Release date: 2015-05-12
* Fixed problem Export/Import for CPT with custom fields.

= 1.6.6.3 =

* Release date: 2015-04-27
* Fixed problem with Commercial tab on Install new Plugin Page.

= 1.6.6.2 =

* Release date: 2015-04-10
* Fixed problem with File Field which do not work when edited from the Parent Post Type. https://wp-types.com/forums/topic/1-6-6-seems-to-break-child-fields-when-parent-has-an-image-field/

= 1.6.6.1 =

* Release date: 2015-04-03
* Fixed problem with archive page for custom post type.

= 1.6.6 =

* Release date: 2015-04-02
* Fixed problem with shortcode "playlist" used in WYSIWYG field. http://wp-types.com/forums/topic/media-play-list-not-outputting-from-custom-wysiwyg-field-js-error/
* Fixed empty title problem for filter "wpt_field_options" on user edit/add screen https://wp-types.com/forums/topic/populate-select-field-in-wpcf-um-group/
* Added ability to create CPT without title and editor. https://wp-types.com/forums/topic/inaccurate-warning-message-when-creatingediting-a-cpt/
* Added Skype field validation.
* Fixed problem with loading custom CSS when user meta group is inactive or not assign to certain user role.
* Added ability to add to menu link to archive of post type.
* Added ability to setup meta box callback function. https://wp-types.com/forums/topic/add-support-for-meta_box_cb-in-custom-taxonomy/
* Added ability to add HTML5 placeholder attribute for custom post fields.
* Fixed problem with CPT labels. https://wp-types.com/forums/topic/after-save-cpts-cutom-labels-always-revert-to-default-label/
* Added a filters to the post title as option text in the select dropdown for post parents. wpcf_pr_belongs_items for array of options and wpcf_pr_belongs_item for one option. https://wp-types.com/forums/topic/help-to-distinguish-duplicate-titles-in-post-relationship/
* Added ability to choose custom fields to display it on custom posts admin list.
* Fixed problem with saving parent data into child data. On parent edit screen.
* Added check group name for Custom Fields and User Fields.
* Fixed missing "Edit" button on group edit screen when we close custom logic form.

= 1.6.5.1 =

* Release date: 2015-02-24
* Fixed Installer patch to plugins.
* Fixed problem with "Access Control and User Roles" menu in Types, when Access is active http://wp-types.com/forums/topic/update-issues-fatal-error-require_once-failed-opening-required-wpcf_access_/
* Changed utm_media used in links on "Getting Started" pages.

= 1.6.5 =

* Release date: 2015-02-10
* Changed in relationships, now all posts are showed, even those which have show_ui to false.
* Added ability to hide custom post types on post relationships list. https://wp-types.com/forums/topic/post-relationship-doesnt-show-post-type-events-created-by-events-espresso/ using filter add_filter('wpcf_show_ui_hide_in_relationships', '__return_false');
* Fixed a problem with deleting last children on post relationships table.
* Added filter to allow use "?" in image url. https://wp-types.com/forums/topic/image-custom-field-is-not-storing-image-path-with-parameters/
* Added option for child table, when editing parent to allow show only list of children instead edit form. http://wp-types.com/forums/topic/miss-settings-for-post-relationship-child-options/
* Fixed a problem with slug in custom fields, when field have special chars.
* Fixed wrong display message about custom fields not manageable by Types.
* Fixed a conflict with Formidable-Pro plugin https://wp-types.com/forums/topic/plugin-conflict/
* Fixed creating new post in relationships. WP 4.1 need real title not faked by one space.
* Fixed problem with validate fields on user create page. http://wp-types.com/forums/topic/custom-usermeta-bypassed-even-required-is-set/
* Improved Edit CPT and Edit CT screens to be more compatible with WP Admin UI
* Fixed problem with default label which contains single quote character (eg. French) https://wp-types.com/forums/topic/default-label-always-shown/
* Improved display list of custom fields groups.

= 1.6.4 =

* Release date: 2014-11-17
* Fixed an issue with dependency between custom taxonomies and custom posts when importing data from the "Custom Post Type UI" plugin.
* Fixed an issue with editing checkboxes with the option "save 0 to the database" selected, created for Custom Posts. http://wp-types.com/forums/topic/checkbox-custom-field-doesnt-save-value-since-upgrade-to-version-1-6-2/ http://wp-types.com/forums/topic/types-checkbox-field-not-saving-after-save-0-to-the-database/
* Fixed an issue with PHP notices being thrown when relative URLs to images were used. http://wp-types.com/forums/topic/php-notice-undefined-index-host-in-image-php/
* Fixed the example file which adds the Google Map field to Types and allows users to enter coordinates to display a map on the front-end.
* Added the "wpcf_delete_relation_meta" filter which allows deletion of all post relationships when deleting a custom post type.
* Fixed an issue with the file name being changed when the file was uploaded. https://icanlocalize.basecamphq.com/projects/7393061-toolset/todo_items/189560556/comments http://wp-types.com/forums/topic/types-1-6-update-breaks-layout-that-worked-in-types-1-5-7/
* Fixed a problem with duplicate slugs on "Edit Group" screen. https://icanlocalize.basecamphq.com/projects/7393061-toolset/todo_items/187118123/comments http://wp-types.com/forums/topic/cant-add-more-custom-fields/
* Fixed a problem with default description not disappearing for non-English placeholders. https://icanlocalize.basecamphq.com/projects/7393061-toolset/todo_items/189787190/comments
* Fixed a problem with Custom Taxonomy metaboxes still appearing on the Custom Post editing page even after removing a Custom Taxonomy from a Custom Post Type.
* Fixed embedding OTGS CSS for the admin area. https://wordpress.org/support/topic/four-stylesheets-being-loaded-at-frontend
* Fixed a problem with Checkbox field value not being saved. https://wp-types.com/forums/topic/checkbox-value-not-saved/
* Added the option to select posts with the "Private" post status as parents in a parent-child Custom Post Types relationships. http://wp-types.com/forums/topic/cred-child-form-not-working-with-private-ctp/
* Fixed a problem with the date-picker. https://icanlocalize.basecamphq.com/projects/7393061-toolset/todo_items/191190651/comments
* Fixed a problem with label menu in wp-admin for child posts.
* Fixed a problem with child table when edit parent post and children do not have title.
* Prevent to chose repetitive field in child table on edit parent screen. http://wp-types.com/forums/topic/wp-types-select-child-fields-to-be-displayed-specific-fields-not-working/
* Added a dynamic "posted x minutes/hours ago" for Types fields. https://wp-types.com/forums/topic/adding-a-dynamic-posted-x-minuteshours-ago-in-a-view/
* Improved post relation table by using more precise labels. http://wp-types.com/forums/topic/displaying-the-best-names-of-cpts-in-applicable-contexts/
* Fixed a problem with display checkbox value from database if checkbox is empty.

= 1.6.3 =

* Release date: 2014-10-23
* Added the message to ask users to answer a short survey for feedback on their work using the Types plugin.
* Fixed a problem where the custom field group’s description was missing from the post/page editing page. http://wp-types.com/forums/topic/custom-field-group-descriptions-no-longer-visible-in-cpt-add-newedit-screen/
* Fixed a problem where the field descriptions weren’t displayed on the user profile editing page. http://wp-types.com/forums/topic/checkbox-description-fields-no-longer-display-in-types-1-6-2/
* Fixed a problem where users weren’t able to untick the single and multiple checkbox fields on the user profile editing page.
* Fixed a problem where the value of date field couldn’t be cleared and added new button which clears the date field value. http://wp-types.com/forums/topic/problem-2-after-update/
* Replaced the deprecated like_escape function with the wpdb::esc_like function.
* Fixed a problem where the parent-child relations between custom post types persisted after deleting and re-creating a custom post type.
* Fixed a problem where date picker scripts were being enqueued in the front end. https://wordpress.org/support/topic/datepicker-css-enqueued-on-public


= 1.6.2 =

* Release date: 2014-08-29
* Fixed addslashes warning
* Fixed display problems with Types shortcodes
* Fixed PHP error for checkboxes

= 1.6.1 =

* Release date: 2014-08-22
* Fixed Formfactory::createForm and Formfactory::displayForm errors on some systems
* Fixed anonymous function problem with wysiwyg field
* Fixed datepicker.css so it only styles the datepicker popup
* Fixed escaping problems with the Types shortcode

= 1.6 =

* Release date: 2014-08-22
* Added ability to add extra options by filter *wpt_field_options* to fields with "options".
* Added ability to create custom post type with the same singular and plural name.
* Added ability to select menu icon (WordPress dashicon) for custom post type.
* Added delete attached repetitive files.
* Added filter "wpcf_pr_belongs_post_status" that allows to change post_status for parent/child posts.
* Added sanitization for uploaded file names, to remove non-latin1 characters.
* Added debug page to help retrieving debug information.
* Fixed export problem if system temporary directory is not allowed to write by open_basedir restriction.
* Fixed export problem, when temporary directory is full or not available.
* Fixed import of slug for custom fields.
* Fixed problems with calling static method in PHP 5.2
* Fixed problems with embedding scripts when WordPress works on non-standard port.
* Fixed problems with getting not existing array keys when register new post type.
* Fixed problems with manage more than one flat taxonomy on one CRED screen.
* Improved import button, which is disabled until user select a import file.
* Improved custom display logic UI for conditional display in custom fields edit screen.
* Prevent raising group chose overlay on "Custom Fields Control" screen if there is no group.
* Removed serialize/unserialize for exported relationships to avoid object injection.
* Removed Zebra library and created new Toolset fields library.
* Tweak import/export screen when is something wrong with imported file
* Fixed several security issues
* Unify code with other Toolset plugins
* Added debug information output for improved customer support
* Added "required" validation audio, file and video fields

= 1.5.7 =

* Fixed action "admin_header" to "admin_head" thx for (anarchocoder)[https://wordpress.org/support/profile/anarchocoder]
* Fixed english suggests in non-english sites.
* Fixed missing icons in WordPress menu on custom fields edit screen.
* Fixed problem with posts relations if post has no parent.
* Fixed problems with checkbox field. Sometimes "check for new posts" do not works correctly.
* Fixed Media Library size if post have no editor.
* Fixed empty fields in "Styling Editor" when editing custom fields.
* Fixed usage of custom fields with prefix "wpcf-" but added by default interface.
* Fixed save fields on attachment edit page.
* Fixed save custom checkbox fields for new attachments.
* Fixed problems with id when repetitive fields are added very fast.
* Fixed problems with displaying options witch apostrophe like "90's".
* Removed clickable links on preview when editing Custom Fields Groups.
* Check compatibility with WordPress 3.9.1.

= 1.5.6 =

* Avoid to get WPML configuration if WPML is not installed.
* Check compatibility with WordPress 3.9.
* Fixed missing setting get in module manager.
* Fixed problems with getting post data in relationship when post do not exist.
* Fixed setting override when import data.

= 1.5.5 =

* Added file name normalization to avoid some storing problems.
* Added post type slug check due changing.
* Added showing hidden fields on fields control screen
* Added "Show hidden fields" checkbox to show hidden custom fields.
* Adjusted media file path normalization
* Allowed choosing parent posts of all post statuses
* Introduced TYPES_EMBEDDED_URL constant for embedded code
* Fixed bug with child posts and WPML
* Fixed checks if usermeta control screen have hidden fields allowed
* Fixed conditionals are not saved when adding existing fields to group
* Fixed missing relations between posts, after changing parent post type slug.
* Fixed missing relations between post and custom fields, after changing parent post type slug.
* Fixed missing relations between post and taxonomy, after changing parent post type slug.
* Fixed missing WPML translations, after changing parent post type slug.
* Fixed on changing parent posts select to fetch all statuses
* Fixed order of parent form now sorted for pubblished and draft
* Fixed saving conditional settings
* Fixed 'wpcf-post-type' notice.

= 1.5.4 =
* Caching improvements
* Image resizing adjustments

= 1.5.3 =
* Fixed caching field data before applying WPML filters
* Fixed forced saving Uncategorized category in child table taxonomy forms
* Fixed indexing bugs with repetitive fields
* Fixed changing numeric to single-line do not remove numeric validation
* Fixed deleted fields showing in conditional dropdown
* Fixed checkboxes special characters in modal screen
* Added preview warning about not updated meta fields
* Added better filtering malformed fields
* Added not allowed saving fields with numeric slugs
* Added 'suppress_filters' parameter for WYSIWYG field
* Added support for W3TC CDN hosted resized images
* Improved JS validation performance
* Removed image exif_imagetype check

= 1.5.2 =
* Fixed image resizing when only width or height is specified
* Fixed image saving for Win
* Fixed validation for radio field

= 1.5.1 =
* Fixed the URLs of image fields on several server configurations

= 1.5 =
* Added new field Colorpicker
* Added new field Video
* Added new field Audio
* Added new field Embedded Media
* Added Usermeta fields to 'Add New User' screen
* Added backward compatibility for 'output' parameter
* Added show_admin_column support for taxonomies
* Added option to have padded cropped image and real non-proportional resize
* Added taxonomy terms selection to child post tables
* Added datepicker support for year range
* Added handling post_id to API call types_child_posts() for custom queries
* Fixed WPML updating fields from original translation
* Fixed WPML edit post screen forms for copied fields
* Fixed WPML creating and updating child posts
* Fixed WPML copied fields appear locked if post do not have original post
* Fixed WPML deleting translated post fields marked as 'translatable' when original is updated
* Fixed WPML changed all fields to be disabled when copied
* Fixed WPML unlocked copied fields when Translation Management is not active
* Fixed WPML removed translation preferences form when Translation Management is not active
* Fixed changing child post status when updating from child table or updating parent
* Fixed setting post parent as 'Not selected' for child post
* Fixed processing shortcodes from field value
* Fixed bug with caching types_child_posts() API call
* Fixed 'maxlength' validation
* Fixed saving post as draft when required field is hidden by other conditional field
* Fixed conditionals triggered on profile page if postmeta and usermeta have same ID
* Fixed custom fields named with prefix 'wpcf-' put under Types control
* Fixed possible issues with relative paths for embedded mode
* Fixed bug with Group conditional and date field
* Fixed bug with fields group conditional and date field
* Fixed saving checkbox zero value for usermeta
* Fixed saving checkboxes for usermeta
* Fixed migrating checkbox for usermeta
* Fixed migrating checkboxes for usermeta
* Fixed child table may show inactive fields

= 1.4 =
* Views 1.3 compatibility
* New Editor ( new GUI, complete parameter list available, improved inserting shortcodes )
* Added 'url' parameter for Image field
* Added 'target' parameter for URL field
* Added Asterisk for required fields titles
* API functions updated
* Added support for custom image sizes ( registered using add_image_size() )
* Removed un-necessary controls from Media Upload for field
* Improved performance on AJAX conditional check
* Fixed inconsistencies when creating first child
* Fixed various issues with Usermeta fields
* Fixed and improved sorting child posts
* Fixed bugs with validation JS
* Date conditional form improved ( added Date select )
* Checkboxes removed from conditional selection
* WPML synchronization when custom post type or taxonomy is changed (translation preferences, translation connections, belonging terms connections)
* Fixed various issues with WPML-copied fields
* Added support for Tabify plugin

= 1.3 =
* Added support for User Meta fields
* Added customization for styling of fields
* Added Access control for fields
* Added Read-only mode for fields
* Added no_protocol attribute for url field
* Fixed Date issues when Date is empty
* Better checks for Date values added
* Fixed Date formats issues
* Added Datepicker localization
* WPML and Group terms filter compatibility added
* Fixed Checkboxes 'save zero' setting and display issues
* Fixed Checkbox 'save zero' inserting value on new post
* Added missing Filters association Group setting in Export
* Fixed JS issues when adding first child post
* Fixed WYSIWYG editor not showing in child form
* Reviewed filters for Images for Windows server
* Fixed adding inactive images to editor dropdown
* Performance improvements ( caching results, JS reviews )

= 1.2.1.1 =
* Fixed problem with some dates showing as a time stamp
* Fixed number field so it excepts 0 (zero)
* Fixed raw="true" mode so it doesn't process shortcodes
* Fixed translations missing in some languages
* Fixed wrong language being displayed for missing translations
* Fixed repeater fields and conditional display issues

= 1.2.1 =
* Fixed compatibility with ACF, Events Calendar and a number of other plugins due to removed actions
* Fixed a problem with WooCommerce Extensions, due to too late initialization
* Fixed translations
* Fixed a problem with stypes_child_posts function on updates
* Fixed a problem with Types API for field render
* Fixed a problem with conditional fields and wpv_condition
* Fixed a bug with repeating fields in translated content
* Fixed a problem with many-to-many relationship
* Fixed a bug with fields inserted into the wrond WYSIWYG field

= 1.2 =
* Added allowing ordering of repeater fields
* Added allow duplicate repeater fields
* Added support for translating Custom Post Type slugs
* Added control of the number of children displayed in the Fields table
* Added optional hour and minutes to the Date field
* Added check to make sure the single and plural names of a Custom Post Type are different
* Fixed handling of required conditional fields
* Removed use of mb_ereg and mb_string functions
* Fixed JavaScript escaping
* Fixed rendering of shortcodes inside types shortcode
* Fixed Open_basedir restriction
* Fixed AJAX popup CSS and JS
* Fixed translation of "Add another field" and "Delete field" buttons
* Fixed exporting and importing of Types Taxonomy
* Fixed exporting and importing of conditional settings for groups

= 1.1.3.4 =
* Fixed adding child posts for WordPress 3.5

= 1.1.3.2 =
* Fixed 'em' tags in radio.php and select.php
* Added support for localized custom post slugs via WPML

= 1.1.3.1 =
* Fixed saving fields in WP 3.5
* Fixed a bug displaying Types credit footer when not asked to do so

= 1.1.3 =
* Added support for resizing remote images
* Fixed long and short date formats
* Fixed many small bugs and glitches
* Sync with Views 1.1.3

= 1.0.4 =
* Some fixes for textarea rendering without automatic paragraph insertion
* Some fixes for WPML compatibility
* Support for Views 1.1.1

= 1.0.3 =
* Fixes for repeating fields

= 1.0.2 =
* Improved WPML support with repeating fields
* Fixed problems with decimal repeating fields
* Post relationship meta box goes through standard WordPress filters
* Fixed field display conditions for date fields
* Fixed field count when adding or deleting fields
* Stopped saving child posts when saving the parent, to avoid conflicts with other plugins
* Checkboxes can save '0' for empty fields

= 1.0.1 =
* Fixes a number of small bugs, related with JS interaction with other plugins

= 1.0 =
* Added an option to make fields repeatable
* Added multiple-option checkboxes
* Added an option to output just URLs for resized images
* Added support for global class and style for all fields
* Added AJAX support for conditional fields
* Added support for non-ASCII characters in CPT URLs
* Added translations for Spanish, French, German, Portuguese, Italian and Dutch
* Fixed many small bugs and glitches

= 0.9.5.4 =
* Fixed a javascript bug on group edit pages

= 0.9.5.1 =
* Fixed a last-minute bug with post relationship

= 0.9.5 =
* Added support for parent/child post relationship between different types
* Added Field Tables, for bulk editing child fields from the parent editor
* Streamlined the field insert GUI

= 0.9.4.2 =
* Fixes a few bugs.

= 0.9.4.1 =
* Fixed a problem adding custom fields to a group on some servers
* Fixed so that standard tags and categories work again with custom post types
* Fixed custom field groups not being shown for some content templates

= 0.9.4 =
* Added an option to display custom field groups on specific templates only
* Fixed a number of bugs with Javascript and with Windows servers

= 0.9.3 =
* Added an import screen from Advanced Custom Fields
* Added an import screen from Custom Posts UI
* Added support for non-English character in custom field names
* Eliminated messages about how to insert custom fields in PHP
* Check if fields already exist with the same name before creating them
* Improved compatibility with WPML

= 0.9.2 =
* Added WYSIWYG custom fields
* Improved the usability for setting up custom taxonomies
* Date fields use the date format specified by WordPress
* Fixed a few bugs for WordPress 3.3
* Checks that fields cannot be created twice
* Checks that only local images are resized
* Added bulk-delete for custom fields
* Fixed a few issues with WPML support

= 0.9.1 =
* Added Embedded mode
* Allows to manage existing custom fields with Types
* Added a .po file for translating Types interface

= 0.9 =
* First release

== Upgrade Notice ==

= 0.9.1 =
* The new Embedded mode allows integrating Types functionality in WordPress plugins and themes.

= 0.9.2 =
* Check out the new WYSIWYG custom fields.

= 0.9.3 =
* This version streamlines the admin screens and includes a importers from other plugins

= 0.9.4 =
* You can now enable custom field groups for content with specific templates

= 0.9.4.1 =
* Fixed a few problems found in the 0.9.4 release

= 0.9.5 =
Try the new parent/child relationship between different post types!

= 0.9.5.1 =
Fixed a last-minute bug with post relationship

= 0.9.5.4 =
Fixed a javascript bug on group edit pages

= 1.0 =
You can make any field repeating now

= 1.0.1 =
Small bugfix release

= 1.0.2 =
Better support for multilingual sites with repeating fields

= 1.1.3 =
Includes support for resizing remote images

= 1.1.3.1 =
Fix for WP 3.5

= 1.1.3.2 =
You can have localized slugs for custom post types

= 1.1.3.4 =
Fix adding child posts for WordPress 3.5

= 1.2 =
Drag and Drop ordering of repeating fields

= 1.2.1 =
Just bug fixes. Usermeta fields are coming in Types 1.3!

= 1.3 =
Add Usermeta fields and Access control of fields.
