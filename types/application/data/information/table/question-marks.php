<?php
$question_marks = array(
	'template' => array(
		'id'            => 'template',
		'title'         => __( 'Template', 'types' ),
		'description'   => array(
			array(
				'type' => 'paragraph',
				'content' => __( 'A template displays single-item pages with your design and fields.', 'types' )
			),
			array(
				'type'   => 'link',
				'external' => true,
				'label'  => __( 'Learn more', 'types' ),
				'target' => Types_Helper_Url::get_url( 'learn-how-template', 'tooltip' )
			),
		)
	),

	'archive' => array(
		'id'            => 'archive',
		'title'         => __( 'Archive', 'types' ),
		'description'   => array(
			array(
				'type' => 'paragraph',
				'content' => __( 'An archive is the standard list that WordPress produces for content.', 'types' )
			),
			array(
				'type'   => 'link',
				'external' => true,
				'label'  => __( 'Learn more', 'types' ),
				'target' => Types_Helper_Url::get_url( 'learn-how-archive', 'tooltip' )
			),
		)
	),

	'views' => array(
		'id'            => 'views',
		'title'         => __( 'Views', 'types' ),
		'description'   => array(
			array(
				'type' => 'paragraph',
				'content' => __( 'Views are custom lists of content, which you can display anywhere in the site.', 'types' )
			),
			array(
				'type'   => 'link',
				'external' => true,
				'label'  => __( 'Learn more', 'types' ),
				'target' => Types_Helper_Url::get_url( 'learn-how-views', 'tooltip' )
			),
		)
	),

	'forms' => array(
		'id'            => 'forms',
		'title'         => __( 'Forms', 'types' ),
		'description'   => array(
			array(
				'type' => 'paragraph',
				'content' => __( 'Forms allow to create and edit content from the site’s front-end.', 'types' )
			),
			array(
				'type'   => 'link',
				'external' => true,
				'label'  => __( 'Learn more', 'types' ),
				'target' => Types_Helper_Url::get_url( 'learn-how-forms', 'tooltip' )
			),
		)
	)
);

// Visual Composer
if( defined( 'WPB_VC_VERSION' ) ) {
	$question_marks['template']['description'][1]['label'] = __( 'Creating templates with Visual Composer', 'types' );
}
// Beaver Builder
else if( class_exists( 'FLBuilderLoader' ) ) {
	$question_marks['template']['description'][1]['label'] = __( 'Creating templates with Beaver Builder', 'types' );
}
// Layouts
else if( defined( 'WPDDL_DEVELOPMENT' ) || defined( 'WPDDL_PRODUCTION' ) ) {
	$question_marks['template']['description'][1]['label'] = __( 'Creating templates with Layouts', 'types' );
}

return $question_marks;