<?php
return array(
	/* Post Type with has_archive = false */
	'no-archive-support' => array(
		'type' => 'archive',

		'priority' => 'important',

		'conditions'=> array(
			'Types_Helper_Condition_Archive_No_Support'
		),

		'description' => array(
			array(
				'type' => 'paragraph',
				'content' => __( 'The archive is disabled for this post type.', 'types' )
			),
			array(
				'type' => 'paragraph',
				'content' => __( 'To enable, go to <a href="%POST-TYPE-EDIT-HAS-ARCHIVE%">Options</a> and mark "has_archive".', 'types' )
			),
		),
	),

	/* No Views, No Layouts, Archive missing */
	'archive-missing' => array(
		'type' => 'archive',

		'priority' => 'important',

		'conditions'=> array(
			'Types_Helper_Condition_Layouts_Missing',
			'Types_Helper_Condition_Views_Missing',
			'Types_Helper_Condition_Archive_Missing'
		),

		'description' => array(
			array(
				'type' => 'paragraph',
				'content' => __( 'Your theme is missing the standard WordPress archive for %POST-LABEL-PLURAL%.', 'types' )
			),
			array(
				'type' => 'link',
				'external' => true,
				'label' => __( 'Visit the %POST-LABEL-PLURAL% archive', 'types' ),
				'target'  => '%POST-ARCHIVE-PERMALINK%'
			),
			array(
				'type'   => 'dialog',
				'class'  => 'button-primary types-button',
				'label'  => __( 'Resolve', 'types' ),
				'dialog' => array(
					'id' => 'resolve-no-archive',
					'description' => array(
						array(
							'type' => 'paragraph',
							'content' => __( 'Toolset plugins let you design archive pages without writing PHP. Your archives will include all
                    the fields that you need and your design.', 'types' )
						),
						array(
							'type'   => 'link',
							'class'  => 'button-primary types-button',
							'external' => true,
							'label'  => __( 'Learn about creating archives with Toolset', 'types' ),
							'target' => Types_Helper_Url::get_url( 'creating-archives-with-toolset', 'popup' ),
						),
					)
				)
			)
		),
	),

	/* No Views, No Layouts, Archive without Fields */
	'archive-fields-missing' => array(
		'type' => 'archive',

		'priority' => 'important',

		'conditions'=> array(
			'Types_Helper_Condition_Layouts_Missing',
			'Types_Helper_Condition_Views_Missing',
			'Types_Helper_Condition_Archive_No_Fields',
		),

		'description' => array(
			array(
				'type' => 'paragraph',
				'content' => __( 'The %POST-LABEL-PLURAL% archive of your theme is missing custom fields.', 'types' )
			),
			array(
				'type' => 'link',
				'external' => true,
				'label' => __( 'Visit the %POST-LABEL-PLURAL% archive', 'types' ),
				'target'  => '%POST-ARCHIVE-PERMALINK%'
			),
			array(
				'type'   => 'dialog',
				'class'  => 'button-primary types-button',
				'label'  => __( 'Resolve', 'types' ),
				'dialog' => array(
					'id' => 'resolve-no-custom-fields',
					'description' => array(
						array(
							'type' => 'paragraph',
							'content' => __( 'Toolset plugins let you design archives with custom fields, without writing PHP.', 'types' )
						),
						array(
							'type'   => 'link',
							'class'  => 'button-primary types-button',
							'external' => true,
							'label'  => __( 'Learn about creating archives with Toolset', 'types' ),
							'target' => Types_Helper_Url::get_url( 'creating-archives-with-toolset', 'popup' ),
						)
					)
				)
			)
		),
	),

	/* No Views, No Layouts, Archive Fields */
	'archive-fields' => array(
		'type' => 'archive',

		'conditions'=> array(
			'Types_Helper_Condition_Layouts_Missing',
			'Types_Helper_Condition_Views_Missing',
			'Types_Helper_Condition_Archive_Has_Fields',
		),

		'description' => array(
			array(
				'type' => 'paragraph',
				'content' => __( 'Your theme displays the %POST-LABEL-SINGULAR% archive using the file: %POST-ARCHIVE-FILE%', 'types' )
			),
			array(
				'type' => 'link',
				'external' => true,
				'label' => __( 'Visit the %POST-LABEL-PLURAL% archive', 'types' ),
				'target'  => '%POST-ARCHIVE-PERMALINK%'
			),
		),
	),

	/* Views, template missing */
	'views-archive-missing' => array(
		'type' => 'archive',

		'priority' => 'important',

		'conditions'=> array(
			'Types_Helper_Condition_Layouts_Missing',
			'Types_Helper_Condition_Views_Archive_Missing'
		),

		'description' => array(
			array(
				'type' => 'paragraph',
				'content' => __( 'There is no WordPress Archive for %POST-LABEL-PLURAL%.', 'types' )
			),
			array(
				'type' => 'link',
				'external' => true,
				'label' => __( 'Visit the %POST-LABEL-PLURAL% archive', 'types' ),
				'target'  => '%POST-ARCHIVE-PERMALINK%'
			),
			array(
				'type'   => 'link',
				'class'  => 'button-primary types-button',
				'target' => '%POST-CREATE-VIEWS-ARCHIVE%',
				'label'  => __( 'Create archive', 'types' )
			)

		),

	),

	/* Views, archive */
	'views-archive' => array(
		'type' => 'archive',

		'conditions'=> array(
			'Types_Helper_Condition_Layouts_Missing',
			'Types_Helper_Condition_Views_Archive_Exists'
		),

		'description' => array(
			array(
				'type' => 'paragraph',
				'content' => __( 'The WordPress Archive for %POST-LABEL-PLURAL% is "%POST-VIEWS-ARCHIVE%"', 'types' )
			),

			array(
				'type' => 'link',
				'external' => true,
				'label' => __( 'Visit the %POST-LABEL-PLURAL% archive', 'types' ),
				'target'  => '%POST-ARCHIVE-PERMALINK%'
			),

			array(
				'type'   => 'link',
				'class'  => 'button',
				'label'  => __( 'Edit WordPress Archive', 'types' ),
				'target' => '%POST-EDIT-VIEWS-ARCHIVE%',
			)
		),

	),

	/* Layouts, Archive missing */
	'layouts-archive-missing' => array(
		'type' => 'archive',

		'priority' => 'important',

		'conditions'=> array(
			'Types_Helper_Condition_Layouts_Active',
			'Types_Helper_Condition_Layouts_Archive_Missing'
		),

		'description' => array(
			array(
				'type' => 'paragraph',
				'content' => __( 'There is no layout for the %POST-LABEL-PLURAL% archive.', 'types' )
			),

			array(
				'type' => 'link',
				'external' => true,
				'label' => __( 'Visit the %POST-LABEL-PLURAL% archive', 'types' ),
				'target'  => '%POST-ARCHIVE-PERMALINK%'
			),

			array(
				'type'   => 'link',
				'class'  => 'button-primary types-button',
				'label'  => __( 'Create archive', 'types' ),
				'target' => '%POST-CREATE-LAYOUT-ARCHIVE%',
			)
		),
	),

	/* Layouts, Archive */
	'layouts-archive' => array(
		'type' => 'archive',

		'conditions'=> array(
			'Types_Helper_Condition_Layouts_Active',
			'Types_Helper_Condition_Layouts_Archive_Exists'
		),

		'description' => array(
			array(
				'type' => 'paragraph',
				'content' => __( 'The layout for the %POST-LABEL-PLURAL% archive is "%POST-LAYOUT-ARCHIVE%".', 'types' )
			),
			array(
				'type' => 'link',
				'external' => true,
				'label' => __( 'Visit the %POST-LABEL-PLURAL% archive', 'types' ),
				'target'  => '%POST-ARCHIVE-PERMALINK%'
			),
			array(
				'type'   => 'link',
				'class'  => 'button',
				'label'  => __( 'Edit layout', 'types' ),
				'target' => '%POST-EDIT-LAYOUT-ARCHIVE%',
			)
		),
	)
);