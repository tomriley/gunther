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
	
	// Deprecated... remove this please
	
	//
	// Get the global page view count
	//
	
	function get_global_view_count()
	{
		$count_file = GUNTHER_PAGE_DIR.'global.hitcount';
		if (!file_exists($count_file))
			return 0;
		return file_get_contents($count_file);
	}
?>