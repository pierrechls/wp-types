<?php

if ( ! apply_filters( 'toolset_is_views_available', false ) ) {
	return;
}


// Medium - Content Template
if( ! class_exists( 'Toolset_User_Editors_Medium_Content_Template', false ) ) {
	require_once( TOOLSET_COMMON_PATH . '/user-editors/medium/content-template.php' );
}
if( ! class_exists( 'Toolset_User_Editors_Medium_Screen_Content_Template_Frontend_Editor', false ) ) {
	require_once( TOOLSET_COMMON_PATH . '/user-editors/medium/screen/content-template/frontend-editor.php' );
}
if( ! class_exists( 'Toolset_User_Editors_Medium_Screen_Content_Template_Frontend', false ) ) {
	require_once( TOOLSET_COMMON_PATH . '/user-editors/medium/screen/content-template/frontend.php' );
}
if( ! class_exists( 'Toolset_User_Editors_Medium_Screen_Content_Template_Backend', false ) ) {
	require_once( TOOLSET_COMMON_PATH . '/user-editors/medium/screen/content-template/backend.php' );
}

// Editor Manager
if( ! class_exists( 'Toolset_User_Editors_Manager', false ) ) {
	require_once( TOOLSET_COMMON_PATH . '/user-editors/manager.php' );
}

// Editor - Visual Composer
if( ! class_exists( 'Toolset_User_Editors_Editor_Visual_Composer', false ) ) {
	require_once( TOOLSET_COMMON_PATH . '/user-editors/editor/visual-composer.php' );
}
if( ! class_exists( 'Toolset_User_Editors_Editor_Visual_Composer_Backend', false ) ) {
	require_once( TOOLSET_COMMON_PATH . '/user-editors/editor/screen/visual-composer/backend.php' );
}
if( ! class_exists( 'Toolset_User_Editors_Editor_Visual_Composer_Frontend', false ) ) {
	require_once( TOOLSET_COMMON_PATH . '/user-editors/editor/screen/visual-composer/frontend.php' );
}

// Editor - Beaver
if( ! class_exists( 'Toolset_User_Editors_Editor_Beaver', false ) ) {
	require_once( TOOLSET_COMMON_PATH . '/user-editors/editor/beaver.php' );
}
if( ! class_exists( 'Toolset_User_Editors_Editor_Screen_Beaver_Backend', false ) ) {
	require_once( TOOLSET_COMMON_PATH . '/user-editors/editor/screen/beaver/backend.php' );
}
if( ! class_exists( 'Toolset_User_Editors_Editor_Screen_Beaver_Frontend', false ) ) {
	require_once( TOOLSET_COMMON_PATH . '/user-editors/editor/screen/beaver/frontend.php' );
}
if( ! class_exists( 'Toolset_User_Editors_Editor_Screen_Beaver_Frontend_Editor', false ) ) {
	require_once( TOOLSET_COMMON_PATH . '/user-editors/editor/screen/beaver/frontend-editor.php' );
}

// Editor - Basic
if( ! class_exists( 'Toolset_User_Editors_Editor_Basic', false ) ) {
	require_once( TOOLSET_COMMON_PATH . '/user-editors/editor/basic.php' );
}
if( ! class_exists( 'Toolset_User_Editors_Editor_Screen_Basic_Backend', false ) ) {
	require_once( TOOLSET_COMMON_PATH . '/user-editors/editor/screen/basic/backend.php' );
}


$medium = new Toolset_User_Editors_Medium_Content_Template();
$medium->addScreen( 'backend', new Toolset_User_Editors_Medium_Screen_Content_Template_Backend() );
$medium->addScreen( 'frontend', new Toolset_User_Editors_Medium_Screen_Content_Template_Frontend() );
$medium->addScreen( 'frontend-editor', new Toolset_User_Editors_Medium_Screen_Content_Template_Frontend_Editor() );

$editor_setup  = new Toolset_User_Editors_Manager( $medium );

$editor_vc = new Toolset_User_Editors_Editor_Visual_Composer( $medium );
if( $editor_setup->addEditor( $editor_vc ) ) {
	$editor_vc->addScreen( 'backend', new Toolset_User_Editors_Editor_Screen_Visual_Composer_Backend() );
	$editor_vc->addScreen( 'frontend', new Toolset_User_Editors_Editor_Screen_Visual_Composer_Frontend() );
}

$editor_beaver = new Toolset_User_Editors_Editor_Beaver( $medium );
if( $editor_setup->addEditor( $editor_beaver ) ) {
	$editor_beaver->addScreen( 'backend', new Toolset_User_Editors_Editor_Screen_Beaver_Backend() );
	$editor_beaver->addScreen( 'frontend', new Toolset_User_Editors_Editor_Screen_Beaver_Frontend() );
	$editor_beaver->addScreen( 'frontend-editor', new Toolset_User_Editors_Editor_Screen_Beaver_Frontend_Editor() );
}

$editor_basic  = new Toolset_User_Editors_Editor_Basic( $medium );
if( $editor_setup->addEditor( $editor_basic ) ) {
	$editor_basic->addScreen( 'backend', new Toolset_User_Editors_Editor_Screen_Basic_Backend() );
}


$editor_setup->run();
