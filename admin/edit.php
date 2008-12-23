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
	
	$docpart_name = $_GET['page'];
	
	if (!$docpart_name)
		builder_error("no page specified");
	
	if ($_GET['type'] == 'template')
		$docpart = new Template($docpart_name);
	else
		$pageobj = new Page($docpart_name);
	
	if ($_GET['type'] == 'template')
	{
		$tpl = new Template($_GET['page']);
		
		if ($tpl->exists())
		{
			$tpl_path = $tpl->path();
			// Locking
			ignore_user_abort(true);
			$out_file = fopen($tpl_path, 'r');
			
			if (flock($out_file, LOCK_SH))
			{
				$body = file_get_contents($tpl_path);
				flock($out_file, LOCK_UN); // Unlock file
				ignore_user_abort(false);
				fclose($out_file);
			}
			else
			{
				ignore_user_abort(false);
				fclose($out_file);
				builder_error("Failed to aquire lock on file $tpl_path");
			}
		}
		else
		{
			$body = "<!-- THIS IS A COPY OF THE DEFAULT VIEW TEMPLATE. PLEASE EDIT. -->\n\n\n";
			$body .= file_get_contents(GUNTHER_CORE_TPLS_DIR.'view_template.tpl');
		}
	}
	else if ($pageobj->exists())
	{
		$contents = $pageobj->contents();
		$title = title_of($contents);
		$body = body_of($contents);
	}
	else
	{
		$title = "Untitled";
		$body = "";
	}
	
	$submit_action = $web_root.$COMMIT_SCRIPT."?type=".$_GET['type']."&amp;page=$docpart_name";
	
	$smarty = new GuntherTemplate($edible_base_dir.'templates/');
	
	if ($_GET['type'] == "page")
		$smarty->assign('existing_uploads', build_download_array($pageobj->upload_path().'/', GUNTHER_PAGE_UPLOADS_BASE_URL.$pageobj->filename().'/', false));
	else
		$smarty->assign('existing_uploads', build_download_array(GUNTHER_TPLS_UPLOADS_BASE_DIR, GUNTHER_TPLS_UPLOADS_BASE_URL, true));
	$smarty->assign('edit_type', $_GET['type']);
	$smarty->assign('manage', make_manage_link().' | '.make_logout_link());
	$smarty->assign('page_body', htmlentities($body));
	$smarty->assign('submit_action', $submit_action);
	$smarty->assign('docpart_name', $docpart_name);
	
	if ($_GET['type'] == 'page')
	{
		$smarty->assign('page_title', $title);
	}
	$smarty->display('edit.tpl');
	
	
	
	//
	// Creates array of download data to pass to template
	//
	
	function build_download_array ($dir, $dir_url, $is_template)
	{
		if (!file_exists($dir))
			return array();
		
		if ($files = list_files($dir))
		{
			$count = count($files);
			$n = 0;
			$uploads = array();
			
			for ($i = 0; $i < $count; $i++)
			{
				$file = $dir.$files[$i];
				
				if (is_file($file))
				{
					$uploads[$n]['name'] = $files[$i];
					$uploads[$n]['size'] = format_file_size(filesize($file));
					$uploads[$n]['modtime'] = date("F d Y g:i A", filemtime($file));
					$uploads[$n]['URL'] = "$dir_url$files[$i]";
					$uploads[$n]['formid'] = str_replace('.', '', 'delete_'.$files[$i]);
					
					if (!$is_template)
						$uploads[$n]['insertText'] = "[@$files[$i]]";
					else
						$uploads[$n]['insertText'] = "{\$tpl_base_url}$files[$i]";
					
					$n++;
				}
			}
			
			return $uploads;
		}
		else
			return array();
	}
		

?>
