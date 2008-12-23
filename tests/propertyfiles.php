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

//
// Simple test code for propertyfile.php
//

	define('IN_BUILDER', true);
	include('../config.php');
	include('../include/propertyfile.php');
	
	$file = $base_page_dir.'prop_file_test.props';
	
	set_property($file, "asdf", "fd");
	//set_property($file, "a", "");
	//set_property($file, "hello", "world2");
	
	echo get_property($file, "asdf");
?>