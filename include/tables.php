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
	
	function begin_wide_table($class="tableborder", $padding=5)
	{
		return '<table width="100%" border="0" cellspacing="0" cellpadding="'.$padding.'" class="'.$class.'">'."\n";
	}
	
	function begin_table($class="tableborder", $padding=5)
	{
		return '<table border="0" cellspacing="0" cellpadding="'.$padding.'" class="'.$class.'">'."\n";
	}
	
	function finish_table()
	{
		return '</table>'."\n";
	}
	
	function begin_row()
	{
		return "<tr>\n";
	}
	
	function finish_row()
	{
		return "</tr>\n";
	}
	
	function table_cell($contents, $colspan, $rowspan, $alignment="left", $class="tablecell")
	{
		return '<td class="'.$class.'" align="'.$alignment.'" colspan="'.$colspan.'" rowspan="'.$rowspan.'">'.$contents.'</td>';
	}
	
	function table_header_cell($contents, $colspan, $rowspan, $alignment="left", $class="tableheader")
	{
		return table_cell($contents, $colspan, $rowspan, $alignment, $class);
	}

?>
