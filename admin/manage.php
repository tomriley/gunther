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
	
	$startTime = microtime();
	
	require_once('../config.php');
	require_once('../include/common.php');
	require_once('../include/tables.php');
	require_once('../include/badge.php');
	require_once('../include/stats.php');
	
	// Check whether user is admin
	
	if (!is_admin_user())
		redirect($web_root.$LOGIN_SCRIPT);
	
	if (!$allfiles = list_files(GUNTHER_PAGE_DIR))
		builder_error("Failed to read list of files from GUNTHER_PAGE_DIR");
	
	// Add FORM to submit
	$listhtml = begin_wide_table();
	
	$listhtml .= begin_row();
	$listhtml .= table_header_cell('<h4>Listing all pages...</h4>', 2, 1);
	$listhtml .= table_header_cell(build_default_template_menu(), 2, 1, "right");
	$listhtml .= finish_row();
	
	if (count($allfiles) == 0)
	{
		$listhtml .= begin_row();
		$listhtml .= table_cell('<center><i>no pages exist</i></center>', 4, 1);
		$listhtml .= finish_row();
	}
	else
		$listhtml .= build_file_list(GUNTHER_PAGE_DIR, $allfiles, true, true); // list of pages
	
	// Add New Page button
	
	$listhtml .= begin_row();
	$listhtml .= '<form name="AdminForm" action="'.$web_root.$ADD_SCRIPT.'" method="GET">';
	$listhtml .= table_cell('<input class="gen" type="text" name="page" size="16" />', 1, 1);//&nbsp;&nbsp;<input type="submit" value="Add Page" />', 4);
	$listhtml .= table_cell('<input type="submit" name="add_page" value="Add New Page" />', 3, 1);
	$listhtml .= '</form>';
	$listhtml .= finish_row();
	
	// Add upgrade options if needed
	
	if (rcs_pages_exist())
	{
		$num_rcs = count_files_of_type(GUNTHER_PAGE_DIR, ",v");
		$num_page = count_files_of_type(GUNTHER_PAGE_DIR, ".page");
		
		if ($num_rcs > $num_page)
		{
			$action = "import";
			$label = "Import RCS Pages ($num_rcs Exist)";
		}
		else
		{
			$action = "remove";
			$label = "Remove Old RCS Pages ($num_rcs Exist)";
		}
		
		$listhtml .= begin_row();
		$listhtml .= '<form name="AdminForm" action="'.$web_root.$UPGRADE_SCRIPT.'" method="GET">';
		$listhtml .= table_cell('<input type="hidden" name="action" value="'.$action.'" /><input type="submit" value="'.$label.'" />', 4);
		$listhtml .= '</form>';
		$listhtml .= finish_row();
	}
	
	// Now make list of templates
	
	$listhtml .= begin_row();
	$listhtml .= table_header_cell('<h4>User templates...</h4>', 4, 1);
	$listhtml .= finish_row();

	$allfiles = array();
	
	if (file_exists(GUNTHER_TPLS_DIR) && !$allfiles = list_files(GUNTHER_TPLS_DIR))
		builder_error("Failed to read list of files from ".GUNTHER_TPLS_DIR);
	
	if (count($allfiles) == 0)
	{
		$listhtml .= begin_row();
		$listhtml .= table_cell('<center><i>no user templates exist</i></center>', 4, 1);
		$listhtml .= finish_row();
	}
	else
		$listhtml .= build_file_list(GUNTHER_TPLS_DIR, $allfiles, true, false); // list of templates
	
	$listhtml .= begin_row();
	$listhtml .= '<form name="AdminForm" action="'.$web_root.$ADD_SCRIPT.'" method="GET">';
	$listhtml .= table_cell('<input class="gen" type="text" name="template" size="16" />', 1, 1);//&nbsp;&nbsp;<input type="submit" value="Add Page" />', 4);
	$listhtml .= table_cell('<input type="submit" name="add_template" value="Add New Template" />', 3, 1);
	$listhtml .= '</form>';
	$listhtml .= finish_row();
	
	$listhtml .= finish_table();
	$listhtml .= "\n</form>";
	
	// Fill out template
	
	$template = get_template('manage_template');
	$template = str_replace("{statistics}", make_badge(0+get_global('global_hit_count')), $template);
	$template = str_replace("{title}", "Manage Website", $template);
	$template = str_replace("{body}", $listhtml, $template);
	
	// Calculate page creation time
	
	$stopTime = microtime();
	$startTime = explode( ' ', $startTime);
	$stopTime = explode( ' ', $stopTime);
	 
	$creationTime = sprintf("%01.2f", (float)(($stopTime[0] + $stopTime[1]) - ($startTime[0] + $startTime[1])));
	$template = str_replace("{logout}", make_logout_link()."<br><br>Page created in $creationTime seconds.<br>Gunther version: ".GUNTHER_VERSION, $template);
	
	echo $template;
	
	
	//
	// Make a table row for each file
	//
	
	function build_file_list ($dir, $files, $delete_option, $template_option)
	{
		$listhtml = '';
		// Header row
		$listhtml .= begin_row();
		$listhtml .= table_cell("<span class=\"gensmall\"><b><i>Page Name:</i></b></span>", 1, 1);
		
		if ($template_option)
		{
			$listhtml .= table_cell("<span class=\"gensmall\"><b><i>Page Title:</i></b></span>", 1, 1);
			$listhtml .= table_cell("<span class=\"gensmall\"><b><i>Template:</i></b></span>", 1, 1, "right");
		}
		else
		{
			$listhtml .= table_cell('&nbsp;', 2, 1);
		}
		
		$listhtml .= table_cell("<span class=\"gensmall\"><b><i>Actions:</i></b></span>", 1, 1, "right");
		$listhtml .= finish_row();
		
		$count = count($files);
		
		for ($i = 0; $i < $count; $i++)
		{
			if ($files[$i]{0} != '.' && is_page_file($files[$i]))
			{
				$page = path_2_page($files[$i]);
				$pageobj = new Page($page);
				
				$listhtml .= begin_row();
				$listhtml .= table_cell(make_view_link($page), 1, 1);
				
				$title = $pageobj->title();
				
				if (empty($title))
					$title = '&nbsp;';
				$listhtml .= table_cell($title, 1, 1);

				// add template selector for this file
				$v_template = $pageobj->get_meta("view_template");
				$listhtml .= table_cell(build_view_template_menu($page, $v_template), 1, 1, "right");
								
				$listhtml .= table_cell(
					($template_option ? 
						make_edit_link($page) : make_template_edit_link($page))
					.($delete_option ? 
						("&nbsp;&nbsp;".make_delete_link($page)
							. "&nbsp;&nbsp;" . make_rename_link($page)) : '')
					, 1, 1, "right");
				$listhtml .= finish_row();
			}
			else if ($files[$i]{0} != '.' && is_tpl_file($files[$i]))
			{
				$page = path_2_template($files[$i]);
				
				$listhtml .= begin_row();
				$listhtml .= table_cell(make_template_view_link($page), 1, 1);
				$listhtml .= table_cell('&nbsp;', 2, 1);
								
				$listhtml .= table_cell(
					($template_option ? 
						make_edit_link($page) : make_template_edit_link($page))
					.($delete_option ? 
						("&nbsp;&nbsp;".make_delete_template_link($page))
							. "&nbsp;&nbsp;" . make_rename_template_link($page) : '')
					, 1, 1, "right");
				$listhtml .= finish_row();
			}
		}
		
		return $listhtml;
	}
	
	//
	// Returns html for <select> element that selects the view
	// template for a single page.
	//
	
	function build_view_template_menu ($page, $current_template)
	{
		global $web_root, $TEMPLATES_SCRIPT;
		
		// XXX - need to be passing in a page object
		$pageobj = new Page($page);
		$selected = $pageobj->get_meta("view_template");
		
		$form_name = 'page_form_'.$page;
		
		$html = "<form name=\"$form_name\">\n";
		$html .= '<select name="tpl_select" onChange="document.location.href=';
		$html .= "'".$web_root.$TEMPLATES_SCRIPT."?page=".$page."&template='"."+document.$form_name.tpl_select.options[document.$form_name.tpl_select.selectedIndex].value\">\n";
		
		// Add Default item
		
		$html .= "<option value=\"\">Default</option>\n";
		
		// Add item for each template
		
		$html .= build_template_menu_options($current_template);
		
		$html .= "</select>\n";
		$html .= '</form>';
		
		return $html;
	}
	
	//
	// Return html <select> element. A popup menu to choose the
	// default template for pages.
	//
	
	function build_default_template_menu ()
	{
		global $web_root, $TEMPLATES_SCRIPT, $gunther_demo_mode;
		
		$selected = get_global("default_view_template");
		
		$html = '<form name="DefaultTplForm">';
		
		$html .= "Default Page Template: ";
		
		$html .= '<select name="DefaultTemplate" onChange="document.location.href=';
		$html .= "'".$web_root.$TEMPLATES_SCRIPT."?default='"."+document.DefaultTplForm.DefaultTemplate.options[document.DefaultTplForm.DefaultTemplate.selectedIndex].value\">\n";
		
		if (!isset($gunther_demo_mode))
			$html .= "<option value=\"\">Gunther Default\n";
		$html .= build_template_menu_options($selected);
		
		$html .= "</select>\n";
		$html .= '</form>';
		
		return $html;
	}
	
	
	//
	// Return list of <option> elements. The value and label of 
	// each element is an available template name.
	//
	
	function build_template_menu_options ($selected)
	{	
		$html = '';
		
		foreach (Template::get_list() as $t)
		{
			$sel_html = ($t->name() == $selected) ? 'selected' : '';
			$html .= "<option value=\"".$t->name()."\" $sel_html>".$t->name()."</option>\n";
		}
		
		return $html;
	}
	
	
	//
	// Functions to do with upgrading
	//
	
	function rcs_pages_exist()
	{
		return (count_files_of_type(GUNTHER_PAGE_DIR, ",v") > 0);
	}
	
	
	
?>


