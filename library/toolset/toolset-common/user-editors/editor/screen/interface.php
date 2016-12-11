<?php


interface Toolset_User_Editors_Editor_Screen_Interface {
	public function isActive();
	public function addMedium( Toolset_User_Editors_Medium_Interface $medium );
	public function addEditor( Toolset_User_Editors_Editor_Interface $editor );
}
