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
	
		
	if (!isset($_GET['page']) || !isset($_GET['image']) ||
		!isset($_GET['w']) || !isset($_GET['h']))
		exit;
	
	$page = new Page($_GET['page']);
	$image = $_GET['image'];
	
	if ($image{0} == '.' || strpos($image, '/'))
		die("Hacking");
	
	$thumbs_dir = GUNTHER_PAGE_UPLOADS_BASE_DIR.$page->filename().'/thumbnails';
	
	if (!file_exists($thumbs_dir))
		mkdir($thumbs_dir);
	
	$original_file = GUNTHER_PAGE_UPLOADS_BASE_DIR.$page->filename().'/'.$image;
	
	if (!file_exists($original_file))
		exit;
	
	$border = (isset($_GET['border']) ? $_GET['border'] : '12');
	
	$thumb_file_name = $image.'_'.$_GET['w'].'x'.$_GET['h'].'b'.$border;
	$thumb_file = $thumbs_dir.'/'.$thumb_file_name;
	
	if (file_exists($thumb_file) &&
		filemtime($thumb_file) > filemtime($original_file) &&
		filemtime($thumb_file) > filemtime(__FILE__)) // regenerate if this script has changed
	{
		redirect_to_image();
		exit;
	}
	
	$ext = file_extension($original_file);
	if ($ext == 'JPG' || $ext == 'JPEG')
		$im = imagecreatefromjpeg($original_file);
	else if ($ext == 'PNG')
		$im = imagecreatefrompng($original_file);
	else
		exit;
	
	$orig_w = imagesx($im);
	$orig_h = imagesy($im);
	$w = $_GET['w'];
	$h = $_GET['h'];
	
	// don't go bigger than original
	if ($w > $orig_w)
		$w = $orig_w;
	if ($h > $orig_h)
		$h = $orig_h;
	
	$scale = $orig_w/$w;
	if ($orig_h/$scale > $h)
		$scale = $orig_h/$h;
	
	
	// only admin can create new thumbs
	if (!is_admin_user() && filemtime($thumb_file) >= filemtime(__FILE__))
	{
		$error_img = @imagecreatetruecolor($orig_w/$scale + $border*2, $orig_h/$scale + $border*2);
		//$error_img = imagecreatefromstring("You must be logged in to create new thumbnails");
		imagestring($error_img, 2, 5, 5, "Not Admin", 16777215);
		header("Content-type: image/png");
		imagepng($error_img);
		imagedestroy($error_img);
		//trigger_error("only admin can create new thumbnails", E_USER_ERROR);
	}
	
	define('TOP', $edible_base_dir.'images/shadow_top.png');
	define('BOTTOM', $edible_base_dir.'images/shadow_bottom.png');
	define('LEFT', $edible_base_dir.'images/shadow_left.png');
	define('RIGHT', $edible_base_dir.'images/shadow_right.png');
	define('TOPRIGHT', $edible_base_dir.'images/shadow_topright.png');
	define('TOPLEFT', $edible_base_dir.'images/shadow_topleft.png');
	define('BOTTOMRIGHT', $edible_base_dir.'images/shadow_bottomright.png');
	define('BOTTOMLEFT', $edible_base_dir.'images/shadow_bottomleft.png');
	
	$top = imagecreatefrompng(TOP);
	$bottom = imagecreatefrompng(BOTTOM);
	$left = imagecreatefrompng(LEFT);
	$right = imagecreatefrompng(RIGHT);
	$topleft = imagecreatefrompng(TOPLEFT);
	$topright = imagecreatefrompng(TOPRIGHT);
	$bottomleft = imagecreatefrompng(BOTTOMLEFT);
	$bottomright = imagecreatefrompng(BOTTOMRIGHT);
	
	$total_w = $orig_w/$scale + $border*2 +imagesx($left)+imagesx($right);
	$total_h = $orig_h/$scale + $border*2 +imagesy($top)+imagesy($bottom);
	
	$im2 = @imagecreatetruecolor($total_w, $total_h);
	
	// fill whole area with white
	imagefilledrectangle($im2, 0, 0, $total_w, $total_h, 16777215);
	
	// copy scaled image into centre
	imagecopyresampled($im2, $im, $border+imagesx($left), $border+imagesy($top), 0, 0, $orig_w/$scale, $orig_h/$scale, $orig_w, $orig_h);
	
	// add drop-shadow
	imagecopyresampled($im2, $bottom, imagesx($left), $total_h-imagesy($bottom), 0, 0,
					$total_w-imagesx($left)-imagesx($right), imagesy($bottom), imagesx($bottom), imagesy($bottom));
	imagecopyresampled($im2, $top, imagesx($left), 0, 0, 0,
					$total_w-imagesx($left)-imagesx($right), imagesy($top), imagesx($top), imagesy($top));
	imagecopyresampled($im2, $topleft, 0, 0, 0, 0,
					imagesx($topleft), imagesy($topleft), imagesx($topleft), imagesy($topleft));
	imagecopyresampled($im2, $topright, $total_w-imagesx($topright), 0, 0, 0,
					imagesx($topright), imagesy($topright), imagesx($topright), imagesy($topright));
	imagecopyresampled($im2, $bottomleft, 0, $total_h-imagesy($bottomleft), 0, 0,
					imagesx($bottomleft), imagesy($bottomleft), imagesx($bottomleft), imagesy($bottomleft));
	imagecopyresampled($im2, $bottomright, $total_w-imagesx($bottomright), $total_h-imagesy($bottomright), 0, 0,
					imagesx($bottomright), imagesy($bottomright), imagesx($bottomright), imagesy($bottomright));
	imagecopyresampled($im2, $left, 0, imagesy($topleft), 0, 0,
					imagesx($left), $total_h-imagesy($bottomleft)-imagesy($topleft), imagesx($left), imagesy($left));
	imagecopyresampled($im2, $right, $total_w-imagesx($bottomright), imagesy($topright), 0, 0,
					imagesx($right), $total_h-imagesy($bottomright)-imagesy($topright), imagesx($right), imagesy($right));
	
	
	imagejpeg($im2, $thumb_file, 85);
	
	imagedestroy($im2);
	imagedestroy($im);
	
	imagedestroy($bottom);
	imagedestroy($top);
	imagedestroy($left);
	imagedestroy($right);
	imagedestroy($topleft);
	imagedestroy($topright);
	imagedestroy($bottomright);
	imagedestroy($bottomleft);
	
	redirect_to_image();
	
	function redirect_to_image()
	{
		global $thumb_file_name, $page;
		header("Content-type: image/jpeg");
		header("Location: ".GUNTHER_PAGE_UPLOADS_BASE_URL.$page->filename().'/thumbnails/'.$thumb_file_name);
	}

?>
