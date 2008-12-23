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
		
	if (isset($_GET['page']))
	{
		// We're adding a normal page
		$page_name = $_GET['page'];
		$type = 'page';
	}
	else if (isset($_GET['template']))
	{
		// Add a user template
		$page_name = $_GET['template'];
		$type = 'template';
	}
	else
		builder_error("Don't know what to add!");
	
	// Redirect to edit new page
	
	redirect($web_root.$EDIT_SCRIPT.'?type='.$type.'&page='.$page_name);
?>