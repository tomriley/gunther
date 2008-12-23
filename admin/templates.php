<?php
/*
	Gunther
	http://gunther.sourceforge.net

	Copyright (c) 2003, Tom Riley

	Released under the GNU General Public License

	This program is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation; either version 2 of the License, or
	(at your option) any later version.
*/
	
	define('IN_BUILDER', true);
	define('IN_BUILDER_ADMIN', true);
	
	require_once('../config.php');
	require_once('../include/common.php');
	
	// Check whether user is admin
	
	if (!is_admin_user())
		redirect($web_root.$LOGIN_SCRIPT);
		
	if (isset($_GET['default']))
	{
		// Update default template preference
		
		Template::validate_name($_GET['default']);
		
		set_global("default_view_template", $_GET['default']);
	}
	else if (isset($_GET['page']) && isset($_GET['template']))
	{
		// Set template for individual page

		Template::validate_name($_GET['template']);
		$page = new Page($_GET['page']);
		$page->set_meta("view_template", $_GET['template']);
	}
	
	// Redirect user back to manage page
	
	redirect($web_root.$MANAGE_SCRIPT);
	
	
?>
