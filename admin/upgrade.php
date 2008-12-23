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
	
	if (!isset($_GET['action']))
		builder_error("no action");
	
	$action = $_GET['action'];
	
	if (!$files = list_files(GUNTHER_PAGE_DIR))
		builder_error("Failed to read list of files from ".GUNTHER_PAGE_DIR);
	
	foreach ($files as $file)
	{
		if (is_rcs_file($file))
		{
			$path = GUNTHER_PAGE_DIR.$file;
				
			if ($action == "import")
			{
				$contents = rcs_co_latest(strip_rcs_name($path));
				
				// write to page
				$p = new Page(strip_rcs_name($file));
				$p->put_contents($contents);
			}
			else if ($action == "remove")
			{
				if (!unlink($path))
					trigger_error("Failed to delete $path", E_USER_WARNING);
			}
			else
				builder_error("bad action");
		}
	}

		
	// Redirect to edit new page
	
	redirect($web_root.$MANAGE_SCRIPT);
	
	
	
	
	//
	// Simply returns the latest revision of file at $file_path
	//
	
	function rcs_co_latest($file_path)
	{
		if (defined('NO_RCS'))
		{
			return no_rcs_checkout($file_path);
		}
		
		exec('co -q -p '.escapeshellarg($file_path), $output);
		return implode("\n", $output);
	}
	
	function no_rcs_checkout($file_path)
	{
		return file_get_contents($file_path.',v');
	}
	
	//
	// Return true if filename ends with ',v'
	//
	
	function is_rcs_file($file_path)
	{
		return (substr($file_path, strlen($file_path)-2) == ',v');
	}
	
	//
	// Convert an RCS file name to normal page name
	//
	
	function strip_rcs_name($rcs_filename)
	{
		return substr($rcs_filename, 0, strlen($rcs_filename)-2);
	}
	
?>
