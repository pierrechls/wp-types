<?php
return array(
	/* No Views, No Layouts, Single Missing */
	'single-missing' => array(
		'type' => 'template',

		'priority' => 'important',

		'conditions'=> array(
			'Types_Helper_Condition_Layouts_Missing',
			'Types_Helper_Condition_Views_Missing',
			'Types_Helper_Condition_Single_Missing'
		),

		'description' => array(
			array(
				'type' => 'paragraph',
				'content' => __( 'Your theme doesn’t have a template to display %POST-LABEL-PLURAL%.', 'types' )
			),
			array(
				'type'   => 'dialog',
				'class'  => 'button',
				'label'  => __( 'Create template', 'types' ),
				'dialog' => array(
					'id' => 'resolve-single-no-template',
					'description' => array(
						array(
							'type' => 'paragraph',
							'content' => __( 'Toolset plugins let you design templates for single items (%POST-LABEL-SINGULAR% pages) without
                        writing PHP. Your templates will include all the fields that you need and your design.', 'types' )
						),
						array(
							'type'   => 'link',
							'class'  => 'button-primary types-button',
							'external' => true,
							'label'  => __( 'Learn about creating templates with Toolset', 'types' ),
							'target' => Types_Helper_Url::get_url( 'creating-templates-with-toolset', 'popup' ),
						),
					)
				)
			)
		),
	),

	/* No Views, No Layouts, Single, without Fields */
	'single-fields-missing' => array(
		'type' => 'template',

		'priority' => 'important',

		'conditions'=> array(
			'Types_Helper_Condition_Layouts_Missing',
			'Types_Helper_Condition_Views_Missing',
			'Types_Helper_Condition_Single_No_Fields',
		),

		'description' => array(
			array(
				'type' => 'paragraph',
				'content' => __( 'Your theme’s template file for displaying %POST-LABEL-SINGULAR% items is missing custom fields.', 'types' )
			),
			array(
				'type'   => 'dialog',
				'class'  => 'button-primary types-button',
				'label'  => __( 'Resolve', 'types' ),
				'dialog' => array(
					'id' => 'resolve-single-no-fields',
					'description' => array(
						array(
							'type' => 'paragraph',
							'content' => __( 'Toolset plugins let you design templates for single items (%POST-LABEL-SINGULAR% pages),
                    with all the fields that you need to display.', 'types' )
						),
						array(
							'type'   => 'link',
							'class'  => 'button-primary types-button',
							'external' => true,
							'label'  => __( 'Learn about creating templates with Toolset', 'types' ),
							'target' => Types_Helper_Url::get_url( 'creating-templates-with-toolset', 'popup' ),
						),
					)
				)
			)
		),
	),

	/* No Views, No Layouts, Single with Fields */
	'single-fields' => array(
		'type' => 'template',

		'conditions'=> array(
			'Types_Helper_Condition_Layouts_Missing',
			'Types_Helper_Condition_Views_Missing',
			'Types_Helper_Condition_Single_Has_Fields',
		),

		'description' => array(
			array(
				'type' => 'paragraph',
				'content' => __( '%POST-TEMPLATE-FILE%', 'types' )
			),
		),
	),

	/* Views, template missing */
	'views-template-missing' => array(
		'type' => 'template',

		'priority' => 'important',

		'conditions'=> array(
			'Types_Helper_Condition_Layouts_Missing',
			'Types_Helper_Condition_Views_Template_Missing'
		),

		'description' => array(
			array(
				'type' => 'paragraph',
				'content' => __( 'There is no Content Template for %POST-LABEL-SINGULAR% items.', 'types' )
			),
			array(
				'type'   => 'link',
				'class'  => 'button',
				'target' => '%POST-CREATE-CONTENT-TEMPLATE%',
				'label'  => __( 'Create template', 'types' )
			)
		),

	),

	/* Views, template */
	'views-template' => array(
		'type' => 'template',

		'conditions'=> array(
			'Types_Helper_Condition_Layouts_Missing',
			'Types_Helper_Condition_Views_Template_Exists'
		),

		'description' => array(
			array(
				'type' => 'link',
				'label' => '%POST-CONTENT-TEMPLATE-NAME%',
				'target'  => '%POST-EDIT-CONTENT-TEMPLATE%'
			),
		),

	),

	/* Layouts, template missing*/
	'layouts-template-missing' => array(
		'type' => 'template',

		'priority' => 'important',

		'conditions'=> array(
			'Types_Helper_Condition_Layouts_Active',
			'Types_Helper_Condition_Layouts_Template_Missing'
		),

		'description' => array(
			array(
				'type' => 'paragraph',
				'content' => __( 'There is no template layout for %POST-LABEL-SINGULAR% items.', 'types' )
			),
			array(
				'type'   => 'link',
				'class'  => 'button',
				'label'  => __( 'Create template', 'types' ),
				'target' => '%POST-CREATE-LAYOUT-TEMPLATE%',
			)
		),
	),

	/* Layouts, template */
	'layouts-template' => array(
		'type' => 'template',

		'conditions'=> array(
			'Types_Helper_Condition_Layouts_Active',
			'Types_Helper_Condition_Layouts_Template_Exists'
		),

		'description' => array(
			array(
				'type' => 'link',
				'label' => '%POST-LAYOUT-TEMPLATE%',
				'target'  => '%POST-EDIT-LAYOUT-TEMPLATE%'
			),
		),
	),
);