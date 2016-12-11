<?php

interface Toolset_User_Editors_Resource_Interface {

	/*
	 * All Resources are Singletons
	 */
	public static function getInstance();
	public function load();
}
