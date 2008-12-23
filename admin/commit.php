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
	require_once('../include/pages.php');
	
	// Check whether user is admin
	
	if (!is_admin_user())
		redirect($web_root.$LOGIN_SCRIPT);
		
	$page = $_GET['page'];
	$is_template = ($_GET['type'] == 'template');
	
	if ($is_template && isset($gunther_demo_mode))
		builder_error("Sorry, you cannot edit templates in the demo mode.");
	
	if (!$page)
		builder_error("no page specified");

	if (!$is_template)
		$page = new Page($page);
	
	$content = stripslashes($_POST['content']);
	
	// If committing a page we expect a title and a comment
	
	if (!$is_template)
	{
		$title = stripslashes($_POST['title']);
		
		if (!$title)
			builder_error("missing title");
		
	}
		
	//if (!$content)
	//	builder_error("missing content");
	
	if ($is_template)
	{
		
		// Now redirect back to manage
		
		//redirect($web_root.$MANAGE_SCRIPT);
		
		$uploads_dir = GUNTHER_TPLS_UPLOADS_BASE_DIR;
	}
	else
	{
		$uploads_dir = $page->upload_path();
	}
	
	// Deal with upload deletion
	
	$deleted_files = false; // assume we haven't
	
	if (file_exists($uploads_dir) && $files = list_files($uploads_dir))
	{
		$count = count($files);
		
		for ($i = 0; $i < $count; $i++)
		{
			$file = $uploads_dir.'/'.$files[$i];
			
			if (is_file($file))
			{
				// check for value in get
				if (isset($_POST[str_replace('.', '', 'delete_'.$files[$i])]))
				{
					unlink($file);
					$deleted_files = true;
				}
			}
		}
	}
	
	if ($is_template)
	{
		$tpl = new Template($page);
		// Easy - just overwrite old template file
		$path = $tpl->path();
		
		ignore_user_abort(true); // Avoid being killed half way through write
		$file = fopen($path, 'a+');
		
		if (flock($file, LOCK_EX))
		{
			ftruncate($file, 0);
			fwrite($file, $content);
			flock($file, LOCK_UN);
		}
		else
		{
			fclose($file);
			ignore_user_abort(false);
			builder_error("Failed to aquire lock on file $path");
		}
		
		fclose($file);
		ignore_user_abort(false);
	}
	else
	{
		if ($page->exists())
			$previous = $page->contents();
			
		if (!$page->exists() || title_of($previous) != $title || body_of($previous) != $content)
		{
			$page->put_contents($title."\n".$content);
		}
		else if (!$_FILES['fileOne']['size'] &&
			!$_FILES['fileTwo']['size'] &&
			!$_FILES['fileThree']['size'] &&
			!$deleted_files)
		{
			builder_error("Nothing changed");
		}
	}
			
	// Handle any file uploads
	
	if (!file_exists($uploads_dir))
	{
		if (!mkdir($uploads_dir))
			builder_error("Failed to create uploads dir at $uploads_dir!");
	}
	else if (!is_writable($uploads_dir))
		builder_error("Uploads directory $uploads_dir is not writable!");
	
	foreach ($_FILES as $details)
	{
	// Replace spaces
		
		$fname = str_replace("%20", "_", $details['name']);
		$fname = str_replace(" ", "_", $fname);
		
		// Strip disallowed characters
		
		$fname = ereg_replace("[^A-Za-z0-9._]", "", $fname);
		
		if ($details['size'] <= 0)
			continue;
		
		if (!isset($gunther_demo_mode) || 
			strcasecmp(substr($fname, strlen($fname)-4), ".jpg") == 0 ||
			 strcasecmp(substr($fname, strlen($fname)-4), ".gif") == 0)
		{	
			if (!move_uploaded_file($details['tmp_name'], $uploads_dir.'/'.$fname))
				builder_error("Failed to receive file named \"$fname\"");
		}
	}
			
	if ($is_template)
		redirect($web_root.$MANAGE_SCRIPT);
	else
		redirect($web_root.$VIEW_SCRIPT.'/page/'.$page->name());
?>
