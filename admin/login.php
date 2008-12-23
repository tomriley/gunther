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

	define('IN_BUILDER_ADMIN', true);
	define('IN_BUILDER', true);
	
	include('../config.php');
	include('../include/common.php');
	include('../include/users.php');
	
	// First we must check whether a 0.1 password store need upgrading
	if (file_exists(GUNTHER_PASSWD_FILE))
	{
		$contents = file_get_contents(GUNTHER_PASSWD_FILE);
		if (strlen($contents) > 0 && strpos($contents, "=") === false)
		{
			set_property(GUNTHER_PASSWD_FILE, 'admin', $contents);
		}
	}
		
	
	if (!file_exists(GUNTHER_PASSWD_FILE) ||
		strlen(file_get_contents(GUNTHER_PASSWD_FILE)) == 0 ||
		strpos(file_get_contents(GUNTHER_PASSWD_FILE), "=") === false)
	{
		if (isset($_POST['firstrun']))
		{
			if (!is_valid_username($_POST['username']))
				show_password_request_page("Invalid login name. Login name must contain only letters, numbers and underscores.");
			else if (!is_valid_password($_POST['password']))
				show_password_request_page("Password must be at least six letters long and<br> contain only letters, numbers and underscores.");
			else
			{
				if ($_POST['password'] != $_POST['password2'])
					show_password_request_page('The two passwords provided do not match. Please re-enter desired login name and password.');
				
				if (!$file = fopen(GUNTHER_PASSWD_FILE, "w+"))
					builder_error("Password store file is not writable. Please check your Gunther configuration file.");
				else
					fclose($file);
				
				set_user_password($_POST['username'], $_POST['password']);
				
				// Update session info
				try_to_login($_POST['username'], $_POST['password']);
				
				redirect($web_root.$MANAGE_SCRIPT);
			}
		}
		else
		{
			// First run - ask user to provide admin password
			show_password_request_page(null);
		}
	}
	else if (is_admin_user() && isset($_GET['logout']))
	{
		$_SESSION['admin'] = false;
		$_SESSION['username'] = null;
		show_login_page("Logged Out");
	}
	else if (!is_admin_user() && isset($_POST['username']) && isset($_POST['password']))
	{	
		// Handle login attempt
		try_to_login($_POST['username'], $_POST['password']);
		
		if (!is_admin_user())
			show_login_page("Bad login name or password.");
		else
			redirect($web_root.$MANAGE_SCRIPT);
	}
	else if (is_admin_user())
	{
		output_simple_page("Login", "You are already logged in.");
	}
	else
	{
		// Output login page
		show_login_page(null);
	}
	
	//
	// Output login page, possibly with an extra message for the user
	//
	
	function show_login_page($message)
	{
		global $web_root, $LOGIN_SCRIPT, $gunther_demo_mode;
		
		// Output login page
		if ($message)
			$form = '<center>'.$message.'<br><br>';
		else
			$form = '<center>';
		
		if (isset($gunther_demo_mode))
			$form .= "<u><b>Gunther Demo Mode:</b></u> Demonstration login name is 'demo' and the password is 'demo'.<br><br>";
			
		$form .= '<form action="'.$web_root.$LOGIN_SCRIPT.'" method="POST">';
		$form .= 'Login name: <input type="text" name="username" size="20" '.(isset($gunther_demo_mode) ? 'value="demo"':'').' /><br><br>';
		$form .= 'Password: <input type="password" name="password" size="20" '.(isset($gunther_demo_mode) ? 'value="demo"':'').' /><br><br>';
		$form .= '<input type="submit" value="Login" /></center>';
		
		output_simple_page("Login", $form);
		exit;
	}
	
	//
	// Output first-run password request page, with optional message
	//
	
	function show_password_request_page($message)
	{
		global $web_root, $LOGIN_SCRIPT;
		
		$form = '<form action="'.$web_root.$LOGIN_SCRIPT.'" method="POST"><center><input type="hidden" name="firstrun" value="" />';
		$form .= 'This appears to be a new installation. Please supply an administrator login name and password.<br><br>';
		
		if ($message)
			$form .= '<br><b>'.$message.'</b><br><br>';
		
		$form .= 'Login name: <input type="text" name="username" size="20" /><br><br>';
		$form .= 'Password: <input type="password" name="password" size="20" /><br><br>';
		$form .= 'Re-enter password: <input type="password" name="password2" size="20" /><br><br>';
		$form .= '<input type="submit" value="Create Account" /></center>';
		
		output_simple_page("Login", $form);
		exit;
	}
?>