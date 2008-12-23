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
	require_once('config.php');
	require_once('include/common.php');
	require_once('include/tables.php');
	require_once('include/format.php');
	require_once('include/template.php');
	require_once('include/pages.php');
	
	if (!isset($_GET['page']) || !isset($_GET['image']))
	   trigger_error('no page or image', E_USER_ERROR);
	
	$page = new Page($_GET['page']);
	$image = $_GET['image'];
	
	$fname = str_replace("%20", "_", $image);
	$fname = str_replace(" ", "_", $fname);
	
    // Strip disallowed characters
    
    $fname = ereg_replace("[^A-Za-z0-9._]", "", $fname);
    
    if ($fname != $image)
        trigger_error('bad image name', E_USER_ERROR);
	
	
	// View a named page
		
	$all = $page->contents();
	
	$body = body_of($all);
	
	$tmpl = '';
	$w = 9999;
	$h = 9999;
	$border = 4;
	
	function extract_gallery_images($body)
	{
		global $tmpl, $w, $h, $border;
		
		$comments = array();
		$lines = explode("\n", $body);
		$in_gallery = false;
		
		foreach ($lines as $line)
		{
			$line = trim($line);
			if (substr($line, 0, 8) == '[Gallery' or substr($line, 0, 9) == '[:Gallery')
			{
				$tag_len = $line{1} == ':' ? 9 : 8;
				$in_gallery = true;
				$params = explode(' ', trim(substr($line, $tag_len, strlen($line)-($tag_len+1))));
				foreach ($params as $param)
				{
					list($p, $v) = explode(':', trim($param));
					if ($p == 'template')
						$tmpl = $v;
					else if ($p == "bigsize")
					{
						if (preg_match('/(\d+)x(\d+)b(\d+)/e', $v, $tokens))
							list(, $w, $h, $border) = $tokens;
						else if (preg_match('/(\d+)x(\d+)/e', $v, $tokens))
							list(, $w, $h) = $tokens;
					}
				}
			}
			else if (substr($line, 0, 10) == '[/Gallery]')
				$in_gallery = false;
			else if ($in_gallery)
			{
				if ($line)
				{
					$image = substr($line, 0, strpos($line, ' '));
					$comments[$image] = substr($line, strpos($line, ' ')+1);
				}
			}
		}
		
		return $comments;
	}
	
	// mapping from image name to comment
	$comments = extract_gallery_images($all);
	$files = array_keys($comments);
	$comment = $comments[$image];
	
	// sort out next and previous links
	
	$index = array_search($image, $files);
	
	$body = '<center>';
	
	if ($index > 0)
		$body .= '<a href="'.$web_root.'gallery_view.php?page='.$page->name().'&image='.$files[$index-1].'">&laquo; Previous Image</a>';
	else
		$body .= "&laquo; Previous Image";
	
	if (count($files) > $index+1)
		$body .= '&nbsp;|&nbsp;<a href="'.$web_root.'gallery_view.php?page='.$page->name().'&image='.$files[$index+1].'">Next Image &raquo;</a>';
	else
		$body .= "&nbsp;|&nbsp;Next Image &raquo;";
	
	$body .= '<br><br>';
	
	$url = $page->upload_path() .'/'.$image;
	// <img src="'.$page->upload_path().'/'.$image.'" border=0>
	$body .= $comment.'<br><br>'.make_thumbnail($page->name(), $image, $w, $h, $border, $url, $comment).'</center>';
	
	
	// Figure our which template to use
	$tmpl_dir = GUNTHER_TPLS_DIR;
	
	if (!$tmpl)
		$tmpl = $page->get_meta("view_template");
	
	if (!$tmpl)
		$tmpl = get_global('default_view_template');

	if (!$tmpl)
	{
		// No user template specified so use core view template
		$tmpl = 'view_template';
		$tmpl_dir = GUNTHER_CORE_TPLS_DIR;
	}
	
	$smarty = new GuntherTemplate($tmpl_dir);
	
	$smarty->assign('page_title', $image);
	$smarty->assign('page_body', $body);
	$smarty->assign('last_mod_time', strftime("%a, %e %B %Y (%r)", filemtime($page->path())));
	$smarty->assign('tpl_base_url', GUNTHER_TPLS_UPLOADS_BASE_URL);
	
	/*
	* XXXX - This is cheating. And only works because class Template doesn't
	*      - check if the template actually exists 
	*      - only if the name is of the right form.
	*      - May need a CoreTemplate class to fix this properly.
	*/
	$tmpl = new Template($tmpl);
	$smarty->display($tmpl->filename().'.tpl');

?>



