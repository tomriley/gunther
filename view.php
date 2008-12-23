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
		define('IN_BUILDER', true);
	
	require_once('config.php');
	require_once('include/common.php');
	require_once('include/tables.php');
	require_once('include/stats.php');
	require_once('include/format.php');
	require_once('include/pages.php');
	
	if (isset($_GET['template']) && $_GET['template'])
	{
		// simply output template
		$tpl = new Template($_GET['template']);
		if ($tpl->exists())
		{
			$contents = $tpl->contents();
			$contents = str_replace('{$tpl_base_url}', GUNTHER_TPLS_UPLOADS_BASE_URL, $contents);
			echo $contents;
		}
		else
			builder_error("No such template.");

		return;
	}
	
	if (!isset($_GET['page']) || !$_GET['page'])
		builder_error("no page specified");
	
	$pageobj = new Page($_GET['page']);
	
	// Use correct template
	
	$tmpl = $pageobj->get_meta('view_template');
	
	if ($tmpl)
		Template::validate_name($tmpl); // template to use, should pass
	else if (isset($gunther_demo_mode))
		$tmpl = 'DemoTemplate';
	
	// if we're an admin user and the page doesn't exist, go to edit
	if (!$pageobj->exists())
		redirect($web_root.$EDIT_SCRIPT.'?type=page&page='.$pageobj->name());
	
	// Increment hit count for page
	
	$pageobj->set_meta("hit_count", $pageobj->get_meta("hit_count")+1);
	
	// View a named page
		
	$all = $pageobj->contents();
	
	$title = title_of($all);
	$rawbody = body_of($all);
	$body = '';
	
	if (isset($gunther_demo_mode) && strip_tags($rawbody) != $rawbody)
	{
		$body .= "<i>The source for this Gunther page contains HTML tags which have been stripped out.\n";
		$body .= "HTML tags are only removed in this online demonstration version.</i><br><br>\n";
	}
	
	$body .= process_content($pageobj->name(), $rawbody);
	
	// Increment global page view count
	if (!is_admin_user())
	{
		$glob_hits = get_global('global_hit_count');
		if (empty($glob_hits))
			$glob_hits = get_global_view_count();
		set_global('global_hit_count', $glob_hits+1);
	}
	
	// Figure our which template to use
	$tmpl_dir = GUNTHER_TPLS_DIR;
	
	if (!$tmpl)
	{
		// No page specific template - look for global preference
		$tmpl = get_global('default_view_template');
		if (!$tmpl)
		{
			// No user template specified so use core view template
			$tmpl = 'view_template';
			$tmpl_dir = GUNTHER_CORE_TPLS_DIR;
		}
	}
	
	$smarty = new GuntherTemplate($tmpl_dir);
	
	// If admin session we add some links
	if (is_admin_user())
	{
		$line = make_edit_link($pageobj->name()).' | '.make_manage_link().' | '.make_logout_link();
		$smarty->assign('manage', $line);
	}
	else
		$smarty->assign('manage', '');
	
	$smarty->assign('page_title', $title);
	$smarty->assign('page_name', $pageobj->name());
	$smarty->assign('page_body', $body);
	$smarty->assign('last_mod_time', filemtime($pageobj->path()));
	$smarty->assign('tpl_base_url', GUNTHER_TPLS_UPLOADS_BASE_URL);
	
	// XXX - cheating again
	$tmplobj = new Template($tmpl);
	$smarty->display($tmplobj->filename().'.tpl');
	
	//
	// BBClone web stats support. Administrator hits are not logged.
	// 
	
	if ($using_bbclone && (!is_admin_user() || isset($gunther_demo_mode)))
	{
		define("_BBC_PAGE_NAME", $_GET['page']);
		define("_BBCLONE_DIR", $bbclone_dir);
		define("COUNTER", $bbclone_dir."mark_page.php");
		if (is_readable(COUNTER)) include_once(COUNTER);
	}

?>
