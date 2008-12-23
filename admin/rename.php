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

	
	$oldname = $_GET[$what];
	if (!isset($_GET['newname']) )
	{
		// Ask user for new name
		$html = '<center>Rename '.$what.' "'.$_GET[$what].'" to ';
		$html .= '<form action="'.$web_root.$RENAME_SCRIPT.'" method="GET">'."\n";
		$html .= '<input type="hidden" name="'.$what.'" value="'.$_GET[$what].'" />'."\n";
		$html .= '<input type="text" name="newname" />'."\n";
		$html .= '<input type="submit" value="go" /></center>';
		
		output_simple_page("New name", $html);
		exit;
	}

	$newpart =& $docpart->get_object($_GET['newname']);

	if ($newpart->exists()) 
		 builder_error("A $what name '" . $newpart->name() . "' already exists");

	if (!$docpart->exists())
		builder_error("The $what being renamed (" . $docpart->name() . ") does not exist!");

	$docpart->rename($newpart->name());

        if ($what == 'template') {
		if ($oldname == get_global("default_view_template"))
			set_global("default_view_template",  $newpart->name());

                foreach (Page::get_list() as $p) {
                        if ($p->get_meta('view_template') == $oldname)
                                $p->set_meta('view_template', $newpart->name());
                }
        }

	
	output_simple_page("Done", "The $what '$oldname' has been renamed to '" . $newpart->name() . 
		"'. <a href=\"{$web_root}{$MANAGE_SCRIPT}\">Return to Manage Website.</a>");
	
?>
