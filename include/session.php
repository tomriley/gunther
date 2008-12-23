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
		die("Hacking!");
	
	// Always use cookies for session id
	ini_set('session.use_only_cookies', '1');
	
	if (!defined('NO_SESSIONS'))
		session_name($cookie_name);
	
	// At the moment I am turning on full error reporting
	// for the admin user
	
	if (!defined('NO_SESSIONS') && is_admin_user())
		error_reporting(E_ALL);
		
	
	function init_default_session_data()
	{
		global $CLIENT_IP;
		
		$_SESSION['admin'] = false;
		$_SESSION['user'] = null;
		$_SESSION['ip'] = $CLIENT_IP;
	}
	
	function is_admin_user()
	{
		global $CLIENT_IP;
		
		return	isset($_SESSION) && array_key_exists('admin', $_SESSION) &&
				$_SESSION['admin'] == true &&
				$_SESSION['ip'] == $CLIENT_IP &&
				$_SESSION['user'] != null;
	}
?>
