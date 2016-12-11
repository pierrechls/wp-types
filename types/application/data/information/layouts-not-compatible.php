<?php
return array(
	'layouts-not-compatible' => array(

		'conditions'=> array(),

		'description' => array(
			array(
				'type' => 'paragraph',
				'content' => __( 'Layouts plugin is installed in your site, but it is not integrated with your theme.
								This will make it difficult to display custom content and fields. You can:', 'types' )
			),
			array(
				'type' => 'list',
				'content' => array(
					__( 'Disable Layouts plugin and use Views plugin to design front-end display', 'types' ),
					sprintf(
						__( 'Choose a different theme (<a href="%s" target="_blank">themes compatible with Layouts</a>)', 'types' ),
						Types_Helper_Url::get_url( 'themes-compatible-with-layouts', 'table' )
					),
					sprintf(
						__( 'Integrate Layouts with this theme (<a href="%s" target="_blank">Layouts integration instructions</a>)', 'types' ),
						Types_Helper_Url::get_url( 'layouts-integration-instructions', 'table' )
					),
				)
			)
		),

	),
);