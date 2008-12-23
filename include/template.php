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
	
	define('SMARTY_DIR',$edible_base_dir.'smarty/');
	require_once(SMARTY_DIR.'Smarty.class.php');
	require_once('common.php');
	
	class GuntherTemplate extends Smarty {
		
		function GuntherTemplate($tmpl_dir) {
			
			global $base_page_dir, $edible_base_dir, $web_root;
			
			$this->Smarty();
			
			$this->template_dir = $tmpl_dir;
			$this->compile_dir = GUNTHER_PAGE_DIR.'templates_c/';
			$this->config_dir = GUNTHER_CORE_TPLS_DIR.'configs/';
			$this->cache_dir = GUNTHER_PAGE_DIR.'templates_cache/';
			
			if (!file_exists($this->compile_dir))
				mkdir($this->compile_dir);
			if (!file_exists($this->cache_dir))
				mkdir($this->cache_dir);
			
			$this->assign('is_admin', is_admin_user());
			$this->assign('web_root', $web_root);
			
			$this->register_function("gunther_include", "gunther_include");
			$this->register_function("gunther_page_url", "gunther_page_url");
		}
	}
	
	function print_current_date ($params)
	{
		if(empty($params['format']))
			$format = "%A, %e %B %Y";
		else
			$format = $params['format'];
		return strftime($format,time());
	}
	
	function gunther_include($params)
	{
		if(!empty($params['page']))
			return embed_page($params['page']);
	    else
	    	return '';
	}
	
	function gunther_page_url($params)
	{
		global $web_root, $VIEW_SCRIPT;
		
		if(!empty($params['page']))
			return $web_root.$VIEW_SCRIPT.'/page/'.$params['page'];
	    else
	    	return '';
	}
?>