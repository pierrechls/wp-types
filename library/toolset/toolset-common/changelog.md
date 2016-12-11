# Toolset Common Library

## 2.3.0

- Extend the post objects relationships management with two actions to gather data on demand.
- Only include the jQuery datepicker stylesheet on demand when the current page contains a datepicker from Toolset.
- Include the user editors in the common bootstrap class.

## 2.2.6

- Fix a CRED issue with added validation rules (types-988).
- Handle several issues related to using "0" as a default field value and saving it to the database (toolsetcommon-106).
- Minor compatibility fixes for the upcoming CRED 1.8.4 release.

## 2.2.5 (November 5, 2016)
 
- Thorough check for security vulnerabilities.

## 2.2.4 (November 2, 2016)

- Fixed a problem with some assets management by definind better rules on constant definitions.

## 2.2.3 (October 10, 2016)

- Fixed select2 edge cases when methods are called on non-select2 initialised element
- Refined special handling of old inputs by making sure target is only a select and not the hidden relative element
- Extended the valid date formats that Types and CRED supports for the Date field.

## 2.2.2 (September 26, 2016)

- Updated the bundled select2 script to version 4.0.3
- Fixed a problem with some assets URLs lacking a backslash
- Improved management of CRED file fields uploads
- Improved the frontend markup for CRED taxonomy fields
- Added an internal Toolset compatibility class

## 2.2.1 (August 25, 2016)
 
- Avoid translating the Toolset top level admin menu label
	
## 2.2 (August 24, 2016)

- Added compatibility classes for Relevanssi and Beaver Builder
- Added a CSS components class
- Improved the Toolset Common assets management
- Added the Glyphicons library

## 2.1 (June 13, 2016)

- Refactored event-manager library to toolset-event manager to namespace it and avoid conficts with ACF Plugin
- Added a new class for promotional and help videos management
- Improved compatibility with PHP 5.2
- Improved compatibility with WPML, including admin language switchers and loading times
- Improved compatibility for CRED file uploads and bundled scripts
- Fixed double slashes on assets URLs

## 2.0 (April 7, 2016)

- Created new loader to load resources for all plugins when plugin loads
- Refactored in a more organised way files and resources to be compatible with the new loader
- Added scripts and styles manager class to register and enqueue static resources with a unified system
- Added Toolset Dialog Boxes to provide a unified way to create and render dialogs
- Fixed various bugs

## 1.9.2 (March 17, 2016)
  
- Fixed issue in validation messages on Amazon S3

## 1.9.1 (March 15, 2016)
  
- Added control to filter array to prevent exceptions
- Prevented error when object does not have property or key is not set in array filter callback
- Fixed glitch in validation library
- Absolute path to include toolset-forms/api.php
- Fixed search terms with language translation

## 1.9 (February 15, 2016)
  
- Tagged for Types 1.9, Views 1.12, CRED 1.5, Layouts 1.5 and Access 1.2.8
- Updated parser.php constructors for PHP7 compatibility.
- Updated the adodb time library for PHP7 compatibility.
- Introduced the Shortcode Generator class.
- New utils.

## 1.8 (November 10, 2015)
  
- Tagged for Views 1.11.1, Types 1.8.9 and CRED 1.4.2
- Improved the media manager script.
- Added helper functions for dealing with $_GET, $_POST and arrays.
- Improved CRED file uploads.
- Improved taxonomy management in CRED forms.
- Improved usermeta fields management in CRED forms.

## 1.7 (October 30, 2015)
  
- Tagged for Views 1.11 and Layouts 1.4

## 1.6.2 (September 25, 2015)
 
- Tagged for CRED 1.4, Types 1.8.2

## 1.6.1 (August 17, 2015)
  
- Tagged for Composer Types 1.8, Views 1.10, CRED 1.4 and Layouts 1.3

## 1.6 (June 11, 2015)
  
- Tagged for Types 1.7, Views 1.9 and Layouts 1.2

## 1.5 (Apr 1, 2015)
  
- Tagged for Types 1.6.6, Views 1.8, CRED 1.3.6 and Layouts 1.1.
- Fixed issue when there is more than one CRED form on a page with the same taxonomy.
- Fixed a little problem with edit skype button modal window - was too narrow.
- Fixed empty title problem for filter "wpt_field_options" on user edit/add screen.
https://wp-types.com/forums/topic/populate-select-field-in-wpcf-um-group/
- Added filter "toolset_editor_add_form_buttons" to disable Toolset buttons on the post editor.
- Added placeholder attributes to fields.
- Updated CakePHP validation URL method to allow new TLD's.

## 1.4 (Feb 2 2015)
  
- Tagged for Views 1.7, Types 1.6.5, CRED 1.3.5 and Layouts 1.0 beta1
- Updated Installer to 1.5

## 1.3.1 (Dec 16 2014)
  
- Tagged for Views 1.7 beta1 and Layouts 1.0 beta1
- Fixed issue about Editor addon and ACF compatibility
- Fixed issue about branding loader

## 1.3 (Dec 15 2014)
  
- Tagged for Views 1.7 beta1 and Layouts 1.0 beta1
