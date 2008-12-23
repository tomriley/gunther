<?php
/*
	Gunther
	http://gunther.sourceforge.net

	Copyright (c) 2003, Thomas Riley

	Released under the GNU General Public License

	This program is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation; either version 2 of the License, or
	(at your option) any later version.
*/
	
	if (!defined('IN_BUILDER'))
		die("Hacking");
	
	umask(2);
	
	// Calculate path to Gunther base directory
	//$edible_base_dir = dirname($_SERVER['SCRIPT_FILENAME']);
	//if (defined('IN_BUILDER_ADMIN'))
	//	$edible_base_dir = dirname($edible_base_dir);
	//$edible_base_dir .= '/';
	
	$edible_base_dir = '';
	if (defined('IN_BUILDER_ADMIN'))
		$edible_base_dir = '../';
	
	
	// Full path to data directory
	define('GUNTHER_DATA_DIR', $edible_base_dir.'data/'); 
	define('GUNTHER_PAGE_DIR', GUNTHER_DATA_DIR.'pages/');
	define('GUNTHER_TPLS_DIR', GUNTHER_DATA_DIR.'templates/');
	define('GUNTHER_CORE_TPLS_DIR', $edible_base_dir.'templates/');
	
	if (!file_exists(GUNTHER_DATA_DIR))
		trigger_error("Gunther data directory does not exist at ".GUNTHER_DATA_DIR, E_USER_ERROR);
	else if (!is_writable(GUNTHER_DATA_DIR))
		trigger_error("Gunther data directory ".GUNTHER_DATA_DIR." is not writable by the webserver!", E_USER_ERROR);
	
	if (!file_exists(GUNTHER_PAGE_DIR))
		if (!mkdir(GUNTHER_PAGE_DIR))
			trigger_error ("Failed to create Gunther page directory at ".GUNTHER_PAGE_DIR.". Please ensure that your 'data' directory is writable by your webserver.", E_USER_ERROR);
	if (!file_exists(GUNTHER_TPLS_DIR))
		if (!mkdir(GUNTHER_TPLS_DIR))
			trigger_error ("Failed to create Gunther user templates directory at ".GUNTHER_TPLS_DIR.". Please ensure that your 'data' directory is writable by your webserver.", E_USER_ERROR);
	
	
	// Deal with https sites!
	$rel = dirname($_SERVER['SCRIPT_NAME']);
	if (defined('IN_BUILDER_ADMIN'))
		$rel = dirname($rel);
	$protocol = isset($_SERVER['HTTPS']) ? 'https' : 'http';
	$web_root = $protocol.'://'.$_SERVER['HTTP_HOST'].$rel.($rel != '/' ? '/' : '');
	
	// Full path to directory in which to put user uploads.
	// The directory should be accessible via the web and should
	// be writable by anyone (chmod 777).
	define('GUNTHER_UPLOADS_BASE_DIR', $edible_base_dir.'uploads/');
	define('GUNTHER_PAGE_UPLOADS_BASE_DIR', GUNTHER_UPLOADS_BASE_DIR.'pages/');
	define('GUNTHER_TPLS_UPLOADS_BASE_DIR', GUNTHER_UPLOADS_BASE_DIR.'templates/');
	
	if (!file_exists(GUNTHER_UPLOADS_BASE_DIR))
		trigger_error("Gunther uploads directory does not exist at ".GUNTHER_UPLOADS_BASE_DIR, E_USER_ERROR);
	else if (!is_writable(GUNTHER_UPLOADS_BASE_DIR))
		trigger_error("Gunther uploads directory ".GUNTHER_UPLOADS_BASE_DIR." is not writable by the webserver!", E_USER_ERROR);
	
	if (!file_exists(GUNTHER_PAGE_UPLOADS_BASE_DIR))
		if (!mkdir(GUNTHER_PAGE_UPLOADS_BASE_DIR))
			trigger_error ("Failed to create Gunther page uploads directory at ".GUNTHER_PAGE_UPLOADS_BASE_DIR.". Please ensure that your 'uploads' directory is writable by your webserver.", E_USER_ERROR);
	if (!file_exists(GUNTHER_TPLS_UPLOADS_BASE_DIR))
		if (!mkdir(GUNTHER_TPLS_UPLOADS_BASE_DIR))
			trigger_error ("Failed to create Gunther template uploads directory at ".GUNTHER_TPLS_UPLOADS_BASE_DIR.". Please ensure that your 'uploads' directory is writable by your webserver.", E_USER_ERROR);
	
	
	// HTTP URL to the uploads directory. It should end with
	// a forward slash.
	define('GUNTHER_UPLOADS_BASE_URL', $web_root.'uploads/');
	define('GUNTHER_PAGE_UPLOADS_BASE_URL', GUNTHER_UPLOADS_BASE_URL.'pages/');
	define('GUNTHER_TPLS_UPLOADS_BASE_URL', GUNTHER_UPLOADS_BASE_URL.'templates/');
	
	// Name of cookie to use for sessions. This can be anything.
	// Just use something descriptive.
	$cookie_name = 'Gunther_Cookie_'.md5($web_root);
	
	// Must not be readable via the web
	// Should check for .htaccess file
	// If server doesn't support htaccess, should move somewhere
	// else I guess.
	
	define('GUNTHER_PRIVATE_DIR', $edible_base_dir.'private/');
	define('GUNTHER_PASSWD_FILE', GUNTHER_PRIVATE_DIR.'passwd.php');
	define('GUNTHER_GLOBALS_FILE', GUNTHER_PRIVATE_DIR.'gunther.globs');
	
	if (!file_exists(GUNTHER_PRIVATE_DIR))
		trigger_error("Gunther 'private' directory does not exist. Please create a directory called 'private' at the root of your Gunther installation. It should be read/writable by your webserver.", E_USER_ERROR);
	if (!is_writable(GUNTHER_PRIVATE_DIR))
		trigger_error("Gunther 'private' directory is not writable by the webserver!", E_USER_ERROR);
		
	// Define Gunther version
	define('GUNTHER_VERSION', 'Beta 0.6 (18th February 2005)');
	
	
	// Set to true to use bbclone (http://www.bbclone.de) to collect
	// statistics on your Gunther pages. If set to true, you must also
	// supply a valid path to your bbclone directory below
	$using_bbclone = false;
	
	// This is the path to your installed bbclone directory. It is only
	// required if you have set $using_bbclone to true above.
	$bbclone_dir = $edible_base_dir.'bbclone/';

	
	// You can change this if, for some reason, you have to change
	// the file extension of php scripts on your server
	$php_ext = '.php';
	
	$EDIT_SCRIPT='admin/edit'.$php_ext;
	$VIEW_SCRIPT='view'.$php_ext;
	$COMMIT_SCRIPT='admin/commit'.$php_ext;
	$LOGIN_SCRIPT='admin/login'.$php_ext;
	$MANAGE_SCRIPT='admin/manage'.$php_ext;
	$THUMBNAIL_SCRIPT='thumbnail'.$php_ext;
	$REMOVE_SCRIPT='admin/remove'.$php_ext;
	$TEMPLATES_SCRIPT='admin/templates'.$php_ext;
	$ADD_SCRIPT='admin/add'.$php_ext;
	$UPGRADE_SCRIPT='admin/upgrade'.$php_ext;
	$RENAME_SCRIPT='admin/rename'.$php_ext;
	
	if (false) // debug config
	{
		echo 'edible_base_dir: '.$edible_base_dir;
		echo '<br>base_page_dir: '.$base_page_dir;
		echo '<br>web_root: '.$web_root;
		echo '<br>uploads_dir: '.$uploads_dir;
		echo '<br>uploads_web_root: '.$uploads_web_root;
		echo '<br>password_store_file: '.$password_store_file;
	}
	
?>
