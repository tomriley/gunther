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
	
	require_once('common.php');
	require_once('linesource.php');
	
	//
	// Process page content. Returns processed page content.
	// The page name must be provided in order to resolve
	// embedded images, thumbnails, etc.
	//
	
	function process_content($page, $body)
	{
		global $gunther_demo_mode;
		
		if (isset($gunther_demo_mode))
			$body = strip_tags($body);

		$src =& linesource::from_text($body);

		return parse_markup($page, $src);
	}

	function parse_markup(&$page, &$src, $until_reg = NULL) {
		
		$in_center_align = false;
		$in_right_align = false;
		$in_left_align = false;
		$in_list = false;
		$in_table = false;
		$in_pre = false;
		$ol_level = 0;
		$ul_level = 0;
		$resultant = '';
		
		$make_link_wrapper = create_function('$m',
			"return make_link('$page', \$m);" );
		
		echo "\n<!-- parse_markup '$page' '$until_reg' {$src->buffer->count} {$src->index} -->\n";
		while (($line = $src->getline()) !== false)
		{
			$line = rtrim($line);

			if ($until_reg != NULL and preg_match($until_reg, $line))
				return $resultant;

			$out = '';
			
			// pre-check for finishing table
			
			if ($in_table && (strlen($line) == 0 || $line{0} != '|'))
			{
				// end table
				$resultant .= finish_table()."\n";
				$in_table = false;
			}
			
			// pre-check for finishing lists
			
			if ($ol_level > 0 && (strlen($line) == 0 || $line{0} != '#'))
			{
				$resultant .= move_to_list_level($ol_level, 0, "<ol>", "</ol>");
				$ol_level = 0;
			}
			if ($ul_level > 0 && (strlen($line) == 0 || $line{0} != '-'))
			{
				$resultant .= move_to_list_level($ul_level, 0, "<ul>", "</ul>");
				$ul_level = 0;
			}	
			
			// First deal with single line tags
			
			if ($in_pre && $line != "}}")
			{
				// don't add newline to final line
				$out = str_replace('<','&#60;',$line).(($lines[$i+1]!="}}")?"\n":"");
				$out = str_replace("\t", "   ", $out);
			}
			else if (preg_match('/\[:Gallery(\s|\])/', $line)) {
				$resultant .= parse_gallery($page, $line, $src);
				continue;
			}
			else if ($line == '[:PHP]' && !isset($gunther_demo_mode))
			{
				$resultant .= parse_php($page, $line, $src);
				continue;
			}
			else if ($line == '[:Raw]')
			{
				$resultant .= parse_raw($page, $line, $src);
				continue;
			}
			else if (preg_match('/^\s*\[:Embed(\s|\])/', $line)) {
				$resultant .= parse_embed($page, $line, $src);
				 continue;
			}
			else if (preg_match('/^\s*\[:Entry(\s|\])/', $line)) {
				$resultant .= parse_entry($page, $line, $src);
				 continue;
			}
			else if ($line == "><")
			{
				if ($in_center_align)
				{
					$out = "</div>";
					$in_center_align = false;
				}
				else
				{
					$out = '<div align="center">';
					$in_center_align = true;
				}
			}
			else if ($line == ">>")
			{
				if ($in_right_align)
				{
					$out = "</div>";
					$in_right_align = false;
				}
				else
				{
					$out = '<div align="right">';
					$in_right_align = true;
				}
			}
			else if ($line == "<<")
			{
				if ($in_left_align)
				{
					$out = "</div>";
					$in_left_align = false;
				}
				else
				{
					$out = '<div align="left">';
					$in_left_align = true;
				}
			}
			else if ($line == "----")
			{
				$out = "<hr />";
			}
			else if ($line == "{{")
			{
				$out = "<pre>";
				$in_pre = true;
			}
			else if ($line == "}}")
			{
				$out = "</pre>\n";
				$in_pre = false;
			}
			else if ($line == "")
			{
				if (($nline = $src->peek(1)) !== false && 
						strlen($nline) > 0 &&
						( $nline{0}  == '!' ||
						$nline{0} == '-' ||
						$nline{0} == '#' ))
					;
				else if (($nline = $src->peek(-1)) !== false && 
						strlen($nline) > 0 &&
						($nline{0} == '-' ||
						$nline{0} == '#' ))
					;
				else
					$out = "<br />";
			}
			else
			{
				// Okay, now replace elements within the line and end it with a <br>
				
				// preformatted sections are exclusice
				$out = preg_replace('/{{(.+?)}}/', "<code>\$1</code>", $line);
				// underlines
				$out = preg_replace('/__(.+?)__/', "<u>\$1</u>", $out);
				// italics
				$out = preg_replace('/(^|[^:])\/\/(.+?)([^:])\/\//', "\$1<i>\$2\$3</i>", $out);
				// superscript
				$out = preg_replace('/\^\^(.+?)\^\^/', "<sup>\$1</sup>", $out);
				// subscript
				$out = preg_replace('/~~(.+?)~~/', "<sub>\$1</sub>", $out);
				// bolds
				$out = preg_replace('/\*\*(.+?)\*\*/', "<b>\$1</b>", $out);
				// strikethrough
				$out = preg_replace('/--(.+?)--/', "<strike>\$1</strike>", $out);
				// embedded images
				$out = preg_replace('/\[\@([^\:\]]+?)\]/e', "make_image_tag('$page', '\\1')", $out);
				// embedded thumbnails
				$out = preg_replace('/\[\@(.+?):(\d+)x(\d+)\]/e', "make_thumbnail('$page','\\1','\\2','\\3')", $out);
				// embedded thumbnails
				$out = preg_replace('/\[\@(.+?):(\d+)x(\d+)b(\d+)\]/e', "make_thumbnail('$page','\\1','\\2','\\3','\\4')", $out);
				// now deal with links
				$out = preg_replace_callback('/\[([^:].+?)\]/', $make_link_wrapper, $out);

				// table rows
				if (($is_header_row = (substr($out, 0, 2) == '||')) || $out{0} == '|')
				{
					$tbl_stuff = ''; // append table code here
					
					if (!$in_table)
					{
						// a + sign at the end of the first row indicates
						// that we want a 100% width table
						if ($out{strlen($out)-1} == '+')
							$tbl_stuff = begin_wide_table();
						else
							$tbl_stuff = begin_table();
						$in_table = true;
					}
					
					$tbl_stuff .= begin_row();
					
					// add cells
					$cells = explode($is_header_row?'||':'|', $out);
					for ($j=1 ; $j<count($cells)-1 ; $j++)
					{
						$cell = $cells[$j];
						$is_header_cell = false;
						$align = 'left';
						$colspan = 1;
						$rowspan = 1;
						$options = array();
						if (preg_match('/^(=)?(\+\d+(?:\.\d+)?)?(>>|><|<<)?/x', $cell, $options) && strlen($options[0]) > 0) 
						{
							$cell = substr($cell, strlen($options[0]));
							for ($k=1; $k<count($options); $k++) {
								$opt = $options[$k];
								if (strlen($opt) < 1)
									continue;
								else if ($opt == '>>')
									$align = "right";
								else if ($opt == '><')
									$align = "center";
								else if ($opt == '<<')
									$align = "left";
								else if ($opt == '=')
									$is_header_cell = true;
								else if ($opt{0} == '+')
								{
									$nums = explode('.', substr($opt,1));
									$colspan = $nums[0];
									if (isset($nums[1]))
										$rowspan = $nums[1];
								}
							}
						}
						
						if ($is_header_row || $is_header_cell)
							$tbl_stuff .= table_header_cell($cell, $colspan, $rowspan, $align);
						else
							$tbl_stuff .= table_cell($cell, $colspan, $rowspan, $align);
					}
					
					$tbl_stuff .= finish_row();
					$out = $tbl_stuff;
				}
				else if (substr($out, 0, 4) == "!!!!")
					$out = "<h1>".substr($out, 4)."</h1>";
				else if (substr($out, 0, 3) == "!!!")
					$out = "<h2>".substr($out, 3)."</h2>";
				else if (substr($out, 0, 2) == "!!")
					$out = "<h3>".substr($out, 2)."</h3>";
				else if ($out{0} == "!")
					$out = "<h4>".substr($out, 1)."</h4>";
				else if ($out{0} == "#")
				{
					$level = get_indentation_level($out);
					$out = move_to_list_level($ol_level, $level, "<ol>", "</ol>")."<li>".$out."\n";
					$ol_level = $level;
				}
				else if ($out{0} == "-")
				{
					$level = get_indentation_level($out);
					$out = move_to_list_level($ul_level, $level, "<ul>", "</ul>")."<li>".$out."\n";
					$ul_level = $level;
				}
				else if ($src->more()) // don't add br to last line
					// add break return to end of line
					$out .= "<br />\n";
				
				// now include embedded pages, we only do this if
				// we're not viewing a template
			//	if (substr($page, strlen($page)-9) != "_template")
			//		$out = preg_replace('/{(.+?)}/e', "embed_page('\\1')", $out);
				
			}
			
			$resultant .= $out;
		}
		
		if ($in_left_align || $in_right_align || $in_center_align)
			$resultant .= "</div>\n";
		
		if ($in_pre)
			$resultant .= "</pre>\n";
		
		if ($in_table)
			$resultant .= finish_table()."\n";
		
		return $resultant;
	}
	
	//
	// Return indentation level for list line and strip indentation
	// markup from start of $line
	//
	
	function get_indentation_level(&$line)
	{
		$level = 0;
		if ($line{0} == '#')
		{
			while ($line{$level} == '#')
				$level++;
		}
		else if ($line{0} == '-')
		{
			while ($line{$level} == '-')
				$level++;
		}
		
		$line = substr($line, $level);
		return $level;
	}
	
	//
	// Returns the necessary opening and closing tags
	//
	
	function move_to_list_level($from_level, $to_level, $open_tag, $close_tag)
	{
		$diff = $from_level - $to_level;
		
		if ($diff == 0)
			return '';
		
		if ($diff > 0)
			$tag = $close_tag;
		else
			$tag = $open_tag;
		
		$n = abs($diff);
		$result = '';
		
		while ($n-- > 0)
		{
			$result .= $tag."\n";
		}
		
		return $result;
	}
	
	//
	// Return absolute URL locating an uploaded image file. If
	// the image name contains a forward-slash then it is assumed
	// to be a valid path already (could be relative or absolute) and
	// is returned unchanged.
	//
	
	function image_name_to_url($page, $imagename)
	{
		if (strpos($imagename, '/'))
			return $imagename;
		else
			return upload_to_url($page, $imagename);
	}
	
	function make_image_tag ($page, $imagename)
	{
		if ($imagename{0} == '>')
		{
			$align = "right";
			$imagename = substr($imagename, 1);
		}
		else if ($imagename{0} == '<')
		{
			$align = "left";
			$imagename = substr($imagename, 1);
		}
		else if ($imagename{0} == '|')
		{
			$align = "center";
			$imagename = substr($imagename, 1);
		}
		 
		$html = '<img border="0" src="'.image_name_to_url($page, $imagename).'" ' ;
		if (isset($align) && $align != "center")
			$html .= 'align="'.$align.'" ';
		$html .= "/>";
		
		if ($align == "center")
			$html = "<div align=\"center\">$html</div>";
		
		return $html;
	}
	
	
	// 	 
	// Interpret a link. 	 
	function make_link($page, $matches) 	 
	{ 	 
		$link = $matches[1];
			
		if (strpos($link, '|') !== false)
		{
			$link_page = substr($link, 0, strpos($link, '|'));
			$link_text = substr($link, strpos($link, '|')+1);
			
			if (preg_match('/\@(.+?):(\d+)x(\d+)b(\d+)/e', $link_text, $tokens))
			{
				// thumbnailed image
				return make_thumbnail($page, $tokens{1}, $tokens{2}, $tokens{3}, $tokens{4}, parse_link($link_page, $link_text));
			}
			else if (preg_match('/\@(.+?):(\d+)x(\d+)/e', $link_text, $tokens))
			{
				// thumbnailed image
				return make_thumbnail($page, $tokens{1}, $tokens{2}, $tokens{3}, 0, parse_link($link_page, $link_text));
			}
			else if ($link_text{0} == '@')
			{
				// image
				$link_text = make_image_tag($page, substr($link_text, 1));
			}
		}
		else
		{
			$link_page = $link;
			$link_text = $link;
		}
		
		$url = parse_link($link_page, $link_text);
		
		return '<a href="'.$url.'">'.$link_text.'</a>';
	}
	
	
	function parse_link($link_page, &$link_text)
	{
		global $web_root, $VIEW_SCRIPT, $EDIT_SCRIPT, $page;
		
		// append link
		if (strpos($link_page, '://'))
			return $link_page;
		else if (strpos($link_page, '@'))
			return 'mailto:'.$link_page; // email links
		else if (page_exists($link_page))
			return make_view_url($link_page);
		else if (upload_exists($page, $link_page))
			return upload_to_url($page, $link_page);
		else
		{
			$link_text .= '<font color="#DD0000">?</font>';
			return make_edit_url($link_page);
		}
	}
	
	
	function make_thumbnail($page, $image, $width, $height, $border=0, $link_url=null, $caption='')
	{
		global $web_root;
		
		if ($image{0} == '>')
		{
			$align = "right";
			$image = substr($image, 1);
		}
		else if ($image{0} == '<')
		{
			$align = "left";
			$image = substr($image, 1);
		}
		else if ($image{0} == '|')
		{
			$align = "center";
			$image = substr($image, 1);
		}
		
		$thumb_file_name = $image.'_'.$width.'x'.$height.'b'.$border;
		$thumb_file_name = GUNTHER_PAGE_UPLOADS_BASE_DIR.$page.'/thumbnails/'.$thumb_file_name;
		
		$img_html = $web_root.'thumbnail.php?page='.$page.'&amp;image='.$image.'&amp;w='.$width.'&amp;h='.$height.($border != -1 ? '&amp;border='.$border : '');
		if (file_exists($thumb_file_name))
			list($width, $height, $type, $attr) = getimagesize($thumb_file_name);
		
		$img_html = '<img src="'.$img_html.'" border="0" '. $attr." alt=\"$caption\" ";
		if ($align != "center")
			$img_html .= "align=\"$align\"";
		$img_html .= ">";
		if ($align == "center")
			$img_html = "<div align=\"$align\">$img_html</div>";
		if ($link_url)
			$img_html = '<a href="'.$link_url.'">'.$img_html.'</a>';
		
		return $img_html;
	}
	
	
	
	function build_gallery ($page, $images, $comments, $max_width, $max_height, $border, $columns)
	{
		global $web_root;
				
		$html = "<table cellpadding=0 cellspacing=8 border=0>\n";
		
		$count = 0;
		
		//if ($images = images_for_page($page))
		{
			foreach ($images as $image)
			{
				if ($count == 0)
					$html .= "<tr>\n";
				
				// add thumbnail
				$html .= '<td valign="middle" align="center">'.
										make_thumbnail($page, $image, $max_width, $max_height, $border, $web_root.'gallery_view.php?page='.$page.'&amp;image='.htmlentities($image)).'<br><span class="gensmall">'.$comments[$image].'</span><br>&nbsp;</td>';
				$count += 1;
				
				if ($count == $columns)
				{
					$html .= '</tr>';
					$count = 0;
				}
			}
			
			// pad out unfinished row
			
			if ($count > 0)
			{
				while ($count < $columns)
				{
					$html .= "<td></td>\n";
					$count += 1;
				}
				$html .= "</tr>\n";
			}
			
		}
		
		$html .= "</table>\n";
		
		return $html;
	}

	function parse_gallery(&$page, $line, &$src) {
		$output = '';
		$params = array( 'page' => $page, 'columns' => 4 );

		breakout_parms($line, $params);

		$gallery_page = $params['page'];
		$gallery_columns =  $params['columns'];

		$gallery_thumb_w = 100;
		$gallery_thumb_h = 100;
		$gallery_thumb_border = 4;

		if (array_key_exists('size', $params)) {
			if (preg_match('/(\d+)x(\d+)b(\d+)/e', $params['size'], $tokens))
				list(, $gallery_thumb_w, $gallery_thumb_h, $gallery_thumb_border) = $tokens;
			else if (preg_match('/(\d+)x(\d+)/e', $params['size'], $tokens))
				list(, $gallery_thumb_w, $gallery_thumb_h) = $tokens;
		}
			
		$output .= "\n<!-- Gallery\n";
		$output .="page:$gallery_page\n";
		$output .="w:$gallery_thumb_w\n";
		$output .="h:$gallery_thumb_h\n";
		$output .="border:$gallery_thumb_border\n";
		$output .="cols:$gallery_columns\n";
		$output .= "-->\n";
				
				
		// Okay, now collect images and comments
				
		$images = array();
		$comments = array();
				
		while ($src->more())
		{
			$line = trim($src->getline());
			if ($line == '[/Gallery]')
				break;
				
			if ($line)
			{
				$image = $images[] = substr($line, 0, strpos($line, ' '));
				$comments[$image] = substr($line, strpos($line, ' ')+1);
				$comments[$image] = parse_markup($gallery_page, linesource::from_text($comments[$image]));

//				$output .= "Image:$image Comment:".$comments[$image].".<br>";
			}
		}

		$output .= build_gallery($gallery_page, $images, $comments, $gallery_thumb_w, $gallery_thumb_h, $gallery_thumb_border, $gallery_columns);
		return $output;
	}

	function parse_php( &$page, $line, &$src) {
		$script = '';

		while ($src->more())
		{
			$line = trim($src->getline());
			if ($line == '[/PHP]')
				break;
			$script .= $line."\n"; // else add line of php
		}
				
		// now eval script, buffering output
		ob_start();
		eval($script);
		$output = ob_get_contents();
		ob_end_clean();

		return $output;
	}
				
	function parse_raw( &$page, $line, &$src) {
		$output = '';
		while ($src->more())
		{
			$line = trim($src->getline());
			if ($line == '[/Raw]')
				break;
			$output .= $line."\n"; // else add line of php
		}
		return $output;
	}

	function parse_embed( &$page, $line, &$src) {
		$parms = array( 'class' => 'embed-page', 'count' => 1);
		breakout_parms($line, $parms);


		/*
		echo "\n<!--\n";
		print_r($parms);
		echo "\n-->\n";
		*/


		if (!isset($parms['page'])) {
			return "\n<!-- bad :Embed tag -- no page parameter -- $line -->\n";
		}

		if ($parms['page'] == $page) {
			return "\n<!-- bad :Embed tag -- recursion -- $line -->\n";
		}

		$newpage = new Page($parms['page']);

		if (!$newpage->exists()) {
			return "\n<!-- bad :Embed tag -- page '{$parms['page']}' nonexistant -- $line -->\n";
		}


		$newsrc =& linesource::from_text($newpage->body());

		$out = '';
		if (isset($parms['entry'])) {
			if ( ($psrc =& find_entry($newsrc, $parms['entry'], $parms['count'])) != false) {
				$first = true;
				foreach ($psrc as $p) {
					if ($first)
						$first = false;
					else
						$out .= "<p/>";
					$out .= "\n<div class=\"{$parms['class']}\">\n";
					$p->rewind();
					$out .= parse_markup($parms['page'], $p);
					$out .= "\n</div>\n";
				}
			} else {
				$out .= "\n <!-- bad :Embed tag - entry '{$parms['entry']}' not found in '{$parms['page']}' -- $line -->\n";
			}
		} else  {
			$out .= "\n<div class=\"{$parms['class']}\">\n";
			$out .= parse_markup($parms['page'], $newsrc);
			$out .= "\n</div>\n";
		}

		return $out;

	}

	/*
	 * split a tag into any parameters. Tags look like:
	 *
	 * [:TagName parm1:val1 parm2:"val2 with space" ]
	 *
	 * Updates an InOut parameter with found parameters.
	 * This allows the caller to specify 'defaults' before
	 * the call.
	 */
	function breakout_parms($line, &$ret) {
		if (!isset($ret) or $ret == NULL)
			$ret = array();

		/*
		echo "\n<!-- breakout_parms - before\n";
		print_r($line);
		echo "\n";
		print_r($ret);
		echo "\n-->\n";
		*/

		$matches = array();
		preg_match_all('/([a-z]+):(([^"\s\]]+) | "([^\"\]]+)")/x', $line, $matches, PREG_SET_ORDER);
		foreach ($matches as $match) {
			$pname = $match[1];
			if ($match[3])
				$pval = $match[3];
			else
				$pval = $match[4];

			$ret[$pname] = $pval;
		}

		/*
		echo "\n<!-- breakout_parms - after\n";
		print_r($ret);
		echo "\n-->\n";
		*/

	}

	/*
	 * find all the [:Entry]..[/Entry] pairs in
	 * a linesource.
	 *
	 * returns an array with entries like:
	 * 	array($startpos, $endpos, $name)
	 * $startpos = the index in the source for the first line after the [:Entry]
	 * $endpos   = the index in the source for the line just before the [/Entry]
	 * $name     = the value of the name parameter if given.
	 *
	 * $src->sub_source($startpos, $endpos) will leave you with just the body of
	 * the block.
	 *
	 * Handles nested blocks. The blocks appear sorted by $startpos
	 */

	function find_entries(&$src) {
		$stack = array();
		$found = array();

		while (($line = $src->getline()) !== false) {
			if (preg_match('/^\s*\[:Entry(\s|\])/', $line)) {
				$parms = array();
				breakout_parms($line, $parms);
				array_push($stack, array($src->getpos(), -1, $parms['name']));
			} else if (preg_match('!^\s*\[/Entry\]!', $line)) {
				$entry = array_pop($stack);
				$entry[1] = $src->getpos()-2;
				array_push($found, $entry);
			}
		}

		usort($found, create_function('$a, $b', 'return ($a[0] == $b[0] ? 0 : ($a[0] > $b[0] ? 1 : -1));'));
		$src->rewind();

		return $found;
	}

	function &find_entry(&$src, $name, $count) {
		$entries = find_entries($src);

		echo "<!-- found_entry: '$name' $count entries = \n";
		print_r($entries);
		echo "\n-->\n";

		if (count($entries) == 0)
			return false;
		

		switch ($name) {
			case 'FIRST':
				$picked_entries = array_slice($entries,0, $count);
				break;
			case 'LAST':
				$picked_entries = array_slice($entries, -$count);
				break;
			default :
				$picked_entries = array();
				foreach ($entries as $entry) {
					if ($entry[2] == $name) {
						$picked_entries[] = $entry;
					}
				}
				break;
		}

		if (!count($picked_entries))
			return false;

		$psrc = array();
		foreach ($picked_entries as $pe)
			$psrc[] =& $src->sub_source($pe[0], $pe[1]);

		return $psrc;


	}

	function parse_entry( &$page, $line, &$src) {

		$parms = array( 'class' => 'entry');
		breakout_parms($line, $parms);

		$out = "\n<div class=\"{$parms['class']}\">\n";
		$out .= parse_markup($page, $src, '!^\s*\[/Entry\]!');
		$out .= "\n</div>\n";

		return $out;
	}


// vim:set ai ts=3 sw=3:
?>
