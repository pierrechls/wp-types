<?php

_doing_it_wrong(
	'toolset-common/functions.php',
	'The file functions.php in Toolset Common is deprecated and will be removed before next release. The functions are now defined in another file that\'s being automatically loaded by Toolset_Common_Bootstrap. Please stop including this file!',
	'Toolset Common Library 2.1'
);

require_once dirname( __FILE__ ) . '/inc/toolset.function.helpers.php';