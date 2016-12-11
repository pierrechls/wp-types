<?php
return array(
	/* Views missing */
	'views-missing' => array(
		'type' => 'views',

		'conditions'=> array(
			'Types_Helper_Condition_Views_Missing'
		),

		'description' => array(
			array(
				'type'   => 'dialog',
				'class'  => 'button',
				'label'  => __( 'Create View', 'types' ),
				'dialog' => array(
					'id' => 'create-view',
					'description' => array(
						array(
							'type' => 'paragraph',
							'content' => __( 'To create a View for this content type, you need to have Views plugin installed. Views is part of the
                    complete Toolset package for adding and displaying custom content.', 'types' )
						),
						array(
							'type' => 'link',
							'external' => true,
							'label' => __( 'Learn how Views work', 'types' ),
							'target'  => Types_Helper_Url::get_url( 'how-views-work', 'popup' )
						),
						/*
						array(
							'type' => 'link',
							'external' => true,
							'label' => __( 'Free Toolset Trial', 'types' ),
							'target'  => Types_Helper_Url::get_url( 'free-trial', 'popup' )
						)
						*/
					)
				)
			)
		),

	),

	/* Views, views missing */
	'views-views-missing' => array(
		'type' => 'views',

		'conditions'=> array(
			'Types_Helper_Condition_Layouts_Missing',
			'Types_Helper_Condition_Views_Views_Missing',
		),

		'description' => array(
			array(
				'type'   => 'link',
				'class'  => 'button',
				'target' => '%POST-CREATE-VIEW%',
				'label'  => __( 'Create View', 'types' )
			)
		)
	),

	/* Views, views */
	'views-views' => array(
		'type' => 'views',

		'conditions'=> array(
			'Types_Helper_Condition_Layouts_Missing',
			'Types_Helper_Condition_Views_Views_Exist',
		),

		'description' => array(
			array(
				'type' => 'paragraph',
				'content' => '%POST-VIEWS-LIST%', 'types'
			),
			array(
				'type'   => 'link',
				'class'  => 'button',
				'target' => '%POST-CREATE-VIEW%',
				'label'  => __( 'Create View', 'types' )
			)
		)
	),

	/* Views Layouts, views missing */
	'views-layouts-views-missing' => array(
		'type' => 'views',

		'conditions'=> array(
			'Types_Helper_Condition_Layouts_Active',
			'Types_Helper_Condition_Views_Views_Missing'
		),

		'description' => array(
			array(
				'type' => 'paragraph',
				'content' => __(
					'Edit any layout and add a View cell to it, to display lists of %POST-LABEL-PLURAL%.', 'types'
				)
			),
			array(
				'type'   => 'link',
				'external' => true,
				'target' => Types_Helper_Url::get_url( 'adding-views-to-layouts', 'table' ),
				'label'  => __( 'Learn how', 'types' )
			),
		)
	),

	/* Views Layouts, views */
	'views-layouts-views' => array(
		'type' => 'views',

		'conditions'=> array(
			'Types_Helper_Condition_Layouts_Active',
			'Types_Helper_Condition_Views_Views_Exist'
		),

		'description' => array(
			array(
				'type' => 'paragraph',
				'content' => '%POST-VIEWS-LIST%', 'types'
			),
			array(
				'type'   => 'link',
				'external' => true,
				'target' => Types_Helper_Url::get_url( 'adding-views-to-layouts', 'table' ),
				'label'  => __( 'How to add Views to layouts', 'types' )
			),
		)
	),
);