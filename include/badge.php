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
	
	require_once('../config.php');
	
	function make_badge($contents)
	{
		global $web_root;
		
		$style = 'font-size : 11px; font-weight: bold; color : #FFFFFF;';
		$html = '<table border="0" cellpadding="0" cellspacing="0"><tr>';
		
		if (strlen($contents) <= 1)
		{
			$html .= '<td valign="middle" align="center" style="'.$style.'" background="'.$web_root.'images/Badge.png" height="26" width="26">'."\n";
			$html .= $contents;
			$html .= '</td></tr>'."\n";
		}
		else
		{
			$style2 = 'background-repeat: no-repeat; background-image: url('.$web_root.'images/BadgeLeft.png); ';
			$html .= '<td style="'.$style2.'" height="26" width="12"></td>'."\n";
			$html .= '<td valign="middle" align="center" class="gen" style="'.$style.'" background="'.$web_root.'images/BadgeMiddle.png" height="26" width="12">';
			$html .= $contents."\n";
			$style2 = 'background-repeat: no-repeat; background-image: url('.$web_root.'images/BadgeRight.png); ';
			$html .= '</td><td style="'.$style2.'" height="26" width="12"></td>'."\n";
		}
		
		$html .= "</table>\n";
		return $html;
	}

	
?>
