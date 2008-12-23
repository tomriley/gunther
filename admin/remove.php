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
	
	if (isset($_GET['page'])) {
		$what = 'page';
		$docpart = new Page($_GET[$what]);
	} else if (isset($_GET['template'])) {
		$what = 'template';
		$docpart = new Template($_GET[$what]);
	} else 
		builder_error("No page name specified");
	
	
	
	if (!isset($_GET['confirm']) || !$_GET['confirm'])
	{
		// If it is the default view template, then don't allow the delete
		if ($what == 'template') {
			if ($docpart->name() == get_global("default_view_template"))
				builder_error("Cannot delete the default view template");
		}
		// Ask user to confirm
		$html = '<center>Remove '.$what.' "'.$_GET[$what].'"?<br><br>';
		$html .= '<form action="'.$web_root.$REMOVE_SCRIPT.'" method="GET">'."\n";
		$html .= '<input type="hidden" name="'.$what.'" value="'.$_GET[$what].'" />'."\n";
		$html .= '<input type="hidden" name="confirm" value="y" />'."\n";
		$html .= '<input type="submit" value="YES" /></center>';
		
		output_simple_page("Please Confirm", $html);
		exit;
	}
	

	if ($what == 'template') {
		foreach (Page::get_list() as $p) {
			if ($p->get_meta('view_template') == $docpart->name()) 
				$p->set_meta('view_template', '');
		}
	}

	$docpart->remove();
		
	output_simple_page("Done", "The $what '" . $docpart->name() . 
		"' has been deleted. <a href=\"".
		$web_root.$MANAGE_SCRIPT."\">Return to Manage Website.</a>");
	
?>
