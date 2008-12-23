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
	
	require_once('propertyfile.php');
	
	function try_to_login($username, $password)
	{
		global $CLIENT_IP;
		
		$hash = md5($password);
		$store = get_property(GUNTHER_PASSWD_FILE, $username);
		
		if ($hash == $store)
		{
			$_SESSION['admin'] = true;
			$_SESSION['user'] = $username;
		}
		
	}
	
	function is_valid_password($password)
	{
		global $gunther_demo_mode;
		if (isset($gunther_demo_mode))
			return true;
			
		if (strlen($password) < 6)
			return false;
		
		return is_valid_username($password);
	}
	
	function is_valid_username($username)
	{
		if (!$username)
			return false;
		
		// Strip out disallowed chars and compare with original
		
		#if (ereg_replace("[^A-Za-z0-9_]", "", $username) != $username)
		#dashes are now taken here as valid user characters
		if (ereg_replace("[^A-Za-z0-9_\-]", "", $username) != $username)
			return false;
		
		return true;
	}
	
	function set_user_password($username, $password)
	{
		if (!is_valid_password($password))
		{
			trigger_error("set_user_password() not setting, invalid password", E_USER_WARNING);
			return;
		}
		
		set_property(GUNTHER_PASSWD_FILE, $username, md5($password));
	}
?>
