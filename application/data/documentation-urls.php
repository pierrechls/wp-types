<?php

// Google Analytics
// ?utm_source=typesplugin&utm_campaign=types&utm_medium=%CURRENT-SCREEN%&utm_term=EMPTY&utm_content=EMPTY

$urls = array(
	'learn-how-template'               => 'https://wp-types.com/documentation/user-guides/benefits-of-templates-for-custom-types/',
	'learn-how-archive'                => 'https://wp-types.com/documentation/user-guides/what-archives-are-and-why-they-are-so-important/',
	'learn-how-views'                  => 'https://wp-types.com/documentation/user-guides/learn-what-you-can-do-with-views/',
	'learn-how-forms'                  => 'https://wp-types.com/home/cred/',
	'learn-how-post-types'             => 'https://wp-types.com/documentation/user-guides/create-a-custom-post-type/',
	'learn-how-fields'                 => 'https://wp-types.com/documentation/user-guides/using-custom-fields/',
	'learn-how-taxonomies'             => 'https://wp-types.com/documentation/user-guides/create-custom-taxonomies/',
	'creating-templates-with-toolset'  => 'https://wp-types.com/documentation/user-guides/learn-about-creating-templates-with-toolset/',
	'creating-templates-with-php'      => 'https://wp-types.com/documentation/user-guides/creating-templates-for-single-custom-posts-in-php/',
	'creating-archives-with-toolset'   => 'https://wp-types.com/documentation/user-guides/learn-about-creating-archives-with-toolset/',
	'creating-archives-with-php'       => 'https://wp-types.com/documentation/user-guides/creating-templates-custom-post-type-archives-php/',
	'how-views-work'                   => 'https://wp-types.com/documentation/user-guides/learn-what-you-can-do-with-views/',
	'how-to-add-views-to-layouts'      => 'https://wp-types.com/documentation/user-guides/views/',
	'learn-views'                      => 'https://wp-types.com/documentation/user-guides/learn-what-you-can-do-with-views/',
	'how-cred-work'                    => 'https://wp-types.com/documentation/user-guides/learn-what-you-can-do-with-cred/',
	'how-to-add-forms-to-layouts'      => 'https://wp-types.com/documentation/user-guides/creating-cred-forms/',
	'learn-cred'                       => 'https://wp-types.com/documentation/user-guides/learn-what-you-can-do-with-cred/',
	'free-trial'                       => 'https://wp-types.com/?add-to-cart=363363&buy_now=1',
	'adding-custom-fields-with-php'    => 'https://wp-types.com/documentation/user-guides/displaying-wordpress-custom-fields/#1',
	'themes-compatible-with-layouts'   => 'https://wp-types.com/documentation/user-guides/layouts-theme-integration/#popular-integrated-themes',
	'layouts-integration-instructions' => 'https://wp-types.com/documentation/user-guides/layouts-theme-integration/#replacing-wp-loop-with-layouts',
	'adding-views-to-layouts'          => 'https://wp-types.com/documentation/user-guides/adding-views-to-layouts/',
	'adding-forms-to-layouts'          => 'https://wp-types.com/documentation/user-guides/adding-cred-forms-to-layouts/',
	'using-post-fields'                => 'https://wp-types.com/user-guides/using-custom-fields/',
	'adding-fields'                    => 'https://wp-types.com/documentation/user-guides/using-custom-fields/#introduction-to-wordpress-custom-fields',
	'displaying-fields'                => 'https://wp-types.com/documentation/user-guides/displaying-wordpress-custom-fields/',
	'adding-user-fields'               => 'https://wp-types.com/documentation/user-guides/user-fields/',
	'displaying-user-fields'           => 'https://wp-types.com/documentation/user-guides/displaying-wordpress-user-fields/',
	'adding-term-fields'               => 'https://wp-types.com/documentation/user-guides/term-fields/',
	'displaying-term-fields'           => 'https://wp-types.com/documentation/user-guides/displaying-wordpress-term-fields/',
	'custom-post-types'                => 'https://wp-types.com/documentation/user-guides/create-a-custom-post-type/',
	'custom-taxonomy'                  => 'https://wp-types.com/documentation/user-guides/create-custom-taxonomies/',
	'post-relationship'                => 'https://wp-types.com/documentation/user-guides/creating-post-type-relationships/',
	'compare-toolset-php'              => 'https://wp-types.com/landing/toolset-vs-php/',
	'types-fields-api'                 => 'https://wp-types.com/documentation/functions/',
	'parent-child'                     => 'https://wp-types.com/documentation/user-guides/many-to-many-post-relationship/',
	'custom-post-archives'             => 'https://wp-types.com/documentation/user-guides/creating-wordpress-custom-post-archives/',
	'using-taxonomy'                   => 'https://wp-types.com/documentation/user-guides/create-custom-taxonomies/',
	'custom-taxonomy-archives'         => 'https://wp-types.com/documentation/user-guides/creating-wordpress-custom-taxonomy-archives/',
	'repeating-fields-group'           => 'https://wp-types.com/documentation/user-guides/creating-groups-of-repeating-fields-using-fields-tables/',
	'single-pages'                     => 'https://wp-types.com/documentation/user-guides/view-templates/',
	'content-templates'                => 'https://wp-types.com/documentation/user-guides/view-templates/',
	'views-user-guide'                 => 'https://wp-types.com/documentation/user-guides/views/',
	'wp-types'                         => 'https://wp-types.com/',
	'date-filters'                     => 'http://wp-types.com/documentation/user-guides/date-filters/',
	'getting-started-types'            => 'https://wp-types.com/documentation/user-guides/getting-starting-with-types/',
);

// Visual Composer
if( defined( 'WPB_VC_VERSION' ) ) {
	$urls['learn-how-template']         = 'https://wp-types.com/documentation/user-guides/benefits-of-templates-for-custom-types-vc/';
	$urls['creating-templates-with-toolset'] = 'https://wp-types.com/documentation/user-guides/benefits-of-templates-for-custom-types-vc/';
}
// Beaver Builder
else if( class_exists( 'FLBuilderLoader' ) ) {
	$urls['learn-how-template']         = 'https://wp-types.com/documentation/user-guides/benefits-of-templates-for-custom-types-bb/';
	$urls['creating-templates-with-toolset'] = 'https://wp-types.com/documentation/user-guides/benefits-of-templates-for-custom-types-bb/';
}
// Layouts
else if( defined( 'WPDDL_DEVELOPMENT' ) || defined( 'WPDDL_PRODUCTION' ) ) {
	$urls['learn-how-template']         = 'https://wp-types.com/documentation/user-guides/benefits-of-templates-for-custom-types-layouts/';
	$urls['creating-templates-with-toolset'] = 'https://wp-types.com/documentation/user-guides/benefits-of-templates-for-custom-types-layouts/';
}

return $urls;