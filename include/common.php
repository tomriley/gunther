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
	
	if (!defined('IN_BUILDER'))
		die("Hacking");
	
	// Record client ip
	
	$CLIENT_IP = ( !empty($_SERVER['REMOTE_ADDR']) ) ? $_SERVER['REMOTE_ADDR'] : ( ( !empty($_ENV['REMOTE_ADDR']) ) ? $_ENV['REMOTE_ADDR'] : $REMOTE_ADDR );
	
	require_once('pages.php');
	require_once('session.php');
	require_once('propertyfile.php');
	require_once('template.php');
	
	// Start session if we don't have one
	if (!defined('NO_SESSIONS'))
	{
		session_start ();
	
		if (!is_admin_user())
			init_default_session_data();
	}
	
	// Explode $PATH_INFO

  $PATH_INFO = getenv('PATH_INFO');

	if (!isset($PATH_INFO))
	{
		$PATH_INFO = substr($HTTP_SERVER_VARS['REQUEST_URI'], strlen($HTTP_SERVER_VARS['SCRIPT_NAME']));
		if ($PATH_INFO{0} != '/')
			unset($PATH_INFO);
	}

	if (isset($PATH_INFO) && !empty($PATH_INFO))
	{
		$vardata = explode('/', $PATH_INFO);
		$num_params = count($vardata);

		if ($num_params % 2 == 0)
		{
			$vardata[] = '';
			$num_params++;
		}

		for ($i=1 ; $i < $num_params ; $i+=2)
		{
			$_GET[$vardata[$i]] = $vardata[$i+1];
		}
	}

	
	//
	// Given entire RCS checkout, return body
	//
	
	function body_of($data)
	{
		return substr($data, strpos($data, "\n")+1);
	}
	
	//
	// Given entire RCS checkout, return page title
	//
	
	function title_of($data)
	{
		return substr($data, 0, strpos($data, "\n"));
	}
	
	//
	// Build and display a simple page
	//
		
	function output_simple_page($title, $body)
	{
		$smarty = new GuntherTemplate(GUNTHER_CORE_TPLS_DIR);
	
		if (is_admin_user())
			$smarty->assign('manage', make_manage_link().' | '.make_logout_link());
		else
			$smarty->assign('manage', '');
		
		$smarty->assign('page_title', $title);
		$smarty->assign('page_body', $body);
		
		$smarty->display('basic_template.tpl');
		
		//echo $template;
	}
	
	
	//
	// Return true if filename ends in ".php"
	//
	
	function is_php_file($file_path)
	{
		return (substr($file_path, strlen($file_path)-4) == '.php');
	}
	
	//
	// Given a file name return the file extension in upper case
	//
	
	function file_extension($file_path)
	{
		$i = strrpos($file_path, '.');
		if ($i !== FALSE)
		{
			return strtoupper(substr($file_path, $i+1));
		}
		return FALSE;
	}
	
	//
	// Return a html link to given page
	//
	
	function make_view_link($page)
	{
		return '<a href="'.make_view_url($page).'">'.$page.'</a>';
	}
	
	//
	// Return URL to view given page
	//
	
	function make_view_url($page)
	{
		global $web_root, $VIEW_SCRIPT;
		return $web_root.$VIEW_SCRIPT."/page/".$page;
	}
	
	//
	// Return a html link to view the given template
	//
	
	function make_template_view_link($page)
	{
		global $web_root, $VIEW_SCRIPT;
		return '<a href="'.$web_root.$VIEW_SCRIPT."/template/".$page.'">'.$page.'</a>';
	}
	
	//
	// Return a html link to edit given page
	//
	
	function make_edit_link($page)
	{
		return '<a href="'.make_edit_url($page).'">edit</a>';
	}
	
	//
	// Return URL to edit given page
	//
	
	function make_edit_url($page)
	{
		global $web_root, $EDIT_SCRIPT;
		return $web_root.$EDIT_SCRIPT."?type=page&amp;page=".$page;
	}
	
	//
	// Return a html link to edit given page
	//
	
	function make_template_edit_link($page)
	{
		global $web_root, $EDIT_SCRIPT;
		return '<a href="'.$web_root.$EDIT_SCRIPT."?type=template&page=".$page.'">edit</a>';
	}
	
	
	//
	// Return an html link to delete given page
	//
	
	function make_delete_link($page)
	{
		global $web_root, $REMOVE_SCRIPT;
		return '<a href="'.$web_root.$REMOVE_SCRIPT."?page=".$page.'">delete</a>';
	}

	function make_rename_link($page)
	{
		global $web_root, $RENAME_SCRIPT;
		return '<a href="'.$web_root.$RENAME_SCRIPT."?page=".$page.'">rename</a>';
	}
	
	//
	// Return an html link to delete given template
	//
	
	function make_delete_template_link($page)
	{
		global $web_root, $REMOVE_SCRIPT;
		return '<a href="'.$web_root.$REMOVE_SCRIPT."?template=".$page.'">delete</a>';
	}
	
	function make_rename_template_link($template)
	{
		global $web_root, $RENAME_SCRIPT;
		return '<a href="'.$web_root.$RENAME_SCRIPT."?template=".$template.'">rename</a>';
	}
	
	//
	// Return a html link to log out
	//
	
	function make_logout_link()
	{
		global $web_root, $LOGIN_SCRIPT;
		return '<a href="'.$web_root.$LOGIN_SCRIPT.'?logout">logout</a>';
	}
	
	//
	// Return a html link to the management page
	//
	
	function make_manage_link()
	{
		global $web_root, $MANAGE_SCRIPT;
		return '<a href="'.$web_root.$MANAGE_SCRIPT.'">manage</a>';
	}
	
	//
	// Makes link to an uploaded file
	//
	
	function make_download_link($page, $filename, $label=null)
	{
		if (!$label)
			$label = $filename;
		return '<a href="'.upload_to_url($page, $filename)."\">$label</a>";
	}
	
	//
	// Get download URL for uploaded file
	//
	
	function upload_to_url($page, $filename)
	{
		$p = new Page($page);
		return GUNTHER_PAGE_UPLOADS_BASE_URL.$p->filename().'/'.$filename;
	}
	
	//
	// Return true if named upload for page exists
	//
	
	function upload_exists($page, $filename)
	{
		$p = new Page($page);
		return file_exists($p->upload_path() . '/'.$filename);
	}
	
	//
	// Return template page as single string. Embeds embedded pages
	// before returning body of template.
	//
	
	function get_template($name)
	{
		$t = new Template($name);
		if ($t->exists())
			return $t->contents();

		$file = GUNTHER_CORE_TPLS_DIR.$name.'.tpl';
		
		if (!file_exists($file))
			builder_error("Failed to locate template \"$name\"");
		
		return file_get_contents($file);
	}
	
	//
	// Returns processed contents of $page. Page should not be
	// a template page. If the page does not exist then a link
	// to create the page is returned.
	//
	
	function embed_page ($page)
	{
		global $EDIT_SCRIPT, $web_root;
		
		if (!Page::valid_name($page))
			return '<i>Invalid page name: \''.$page.'\'</i>';

		$page = new Page($page);
		
		if ($page->exists())
		{
			$all = $page->contents();
			
			$title = title_of($all);
			$body = body_of($all);
			
			$content = process_content($page->name(), $body);
			
			// If admin user, add edit link
			if (is_admin_user())
			{
				if (strpos($content, '<br />') !== false ||
					strpos($content, '<br>') !== false ||
					strpos($content, '<table') !== false)
					$content .= '<br \><br \>';
				
				$content .= ' <span class="genmed">[<a href="'.make_edit_url($page->name()).'">edit '.$page->name().'</a>]</span>';	
			}
			
			return $content;
		}
		else
		{
			return '{<a href="'.make_edit_url($page->name()).'">'.$page->name().'<font color="#DD0000">?</font></a>}';
		}
	}
	
	//
	// Output an error page then exit immediately
	//
	
	function builder_error($message)
	{
		output_simple_page ("Error", "<b>Message:</b> ".$message);
		exit;
	}
	
	
	//
	// Redirect users browser to another URL
	//
	
	function redirect($url)
	{
		header('Location: '.$url);
		exit;
	}
	
	//
	// Convert size in bytes to a sensible file size string
	//
	
	function format_file_size($bytes)
	{
		if ($bytes < 3000)
			return "$bytes bytes";
		if ($bytes < 1000000)
			return (int)($bytes/1000)." kb";
		else
			return number_format($bytes/1000000, 2)." MB";
	}
	
	//
	// Return sorted array containing all files and directories
	// found with directory $dir. Will return FALSE upon failure.
	//
	
	function list_files($dir)
	{
		if ($dh = opendir($dir))
		{
			while (false !== ($filename = readdir($dh))) {
				$files[] = $filename;
			}
			sort($files);
			return $files;
		}
		else
			return false;
	}
	
	//
	// Get a global setting.
	//
	
	function get_global($glob_name)
	{
		return get_property(GUNTHER_GLOBALS_FILE, $glob_name);
	}
	
	//
	// Set a global setting.
	//
	
	function set_global($glob_name, $value)
	{
		set_property(GUNTHER_GLOBALS_FILE, $glob_name, $value);
	}
	
	//
	// Recursively remove directory $file and any files
	// and directories below $file
	//
	
	function delete_dir_structure($dir, $limit_depth)
	{
		if ($limit_depth <= 0)
			return;
		
		$limit_depth -= 1;
		
		if ($files = list_files($dir))
		{
			foreach ($files as $file)
			{
				if (is_file($dir.'/'.$file))
				{
					//echo $dir.'/'.$file."\n";
					if (!unlink($dir.'/'.$file))
						trigger_error("Failed to remove file $dir/$file", E_USER_WARNING);
				}
				else if ($file != '.' && $file != '..' && is_dir($dir.'/'.$file))
					delete_dir_structure($dir.'/'.$file, $limit_depth);
			}
			
			if (!rmdir($dir))
				trigger_error("Failed to remove directory $dir", E_USER_WARNING);
		}
	}
	
	//
	// Count the number of files with a given file extension
	// in a directory.
	//
	
	function count_files_of_type($dir, $ext)
	{
		if (!$allfiles = list_files($dir))
			return 0;
		
		$count = 0;
		
		foreach ($allfiles as $file)
		{
			if (substr($file, strlen($file)-strlen($ext)) == $ext)
				$count++;
		}
		
		return $count;
	}


	// cribbed from http://www.ragnarokonline.de/snippets/file_get_contents.phps with minor E_ALL change
	if (!function_exists('file_get_contents'))
	{
	    function file_get_contents($filename, $use_include_path = 0)
	    {
		$file = @fopen($filename, 'rb', $use_include_path);
		if ($file)
		{
		    if ($fsize = @filesize($filename))
		    {
			$data = fread($file, $fsize);
		    }
		    else
		    {
			$data = '';
			while (!feof($file))
			{
			    $data .= fread($file, 1024);
			}
		    }
		    fclose($file);
		}
		return $data;
	    }
	}
?>
