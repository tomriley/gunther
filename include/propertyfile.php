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
	
	//
	// Given and property file, property name and value, update
	// the property value in the file.
	//
	
	function set_property($prop_file, $prop_name, $prop_value)
	{
		if (file_exists($prop_file) == false)
			touch($prop_file);
		
		ignore_user_abort(true);
		
		$out_file = fopen($prop_file, 'a+');
		
		if (!$out_file)
		{
			ignore_user_abort(false);
			trigger_error("Failed to open property file $prop_file", E_USER_WARNING);
			return;
		}
		
		if (!flock($out_file, LOCK_EX))
		{
			ignore_user_abort(false);
			trigger_error("Failed to aquire lock on property file $prop_file", E_USER_WARNING);
			fclose($out_file);
			return;
		}
		
		$contents = file_get_contents($prop_file);
		$lines = explode("\n", $contents);
		
		$is_php = (substr($prop_file, strlen($prop_file)-4) == '.php');
		if ($is_php)
			$lines = strip_php($lines);
		$n = count($lines);
		$updated = false;
		$output = array();
		
		ftruncate($out_file, 0);
		
		for ($i=0 ; $i<$n ; $i++)
		{
			if ($lines[$i] == "")
			{
				// Ignore empty lines
			}
			else if (substr($lines[$i], 0, strlen($prop_name)+1) == $prop_name.'=')
			{
				if ($updated == false) // get's rid of duplicates if they exist
				{
					//echo "matched\n";
					//fwrite($out_file, $prop_name.'='.$prop_value."\n");
					$output[] = $prop_name.'='.$prop_value;
					$updated = true;
				}
			}
			else
			{
				//echo "no match\n";
				//fwrite($out_file, $lines[$i]."\n");
				$output[] = $lines[$i];
			}
		}
		
		if ($updated == false)
			$output[] = $prop_name.'='.$prop_value;
		
		if ($is_php)
			$output = wrap_php($output);
		fwrite($out_file, implode("\n", $output));
		
		flock($out_file, LOCK_UN); // Unlock file
		fclose($out_file);
		
		ignore_user_abort(false);
	}
	
	
	//
	// Return the value of a property in a given property file.
	//
	
	function get_property($prop_file, $prop_name)
	{
		if (file_exists($prop_file) == false)
			return null;
		
		$out_file = fopen($prop_file, 'r');
		if (flock($out_file, LOCK_SH))
		{
			$contents = file_get_contents($prop_file);
			flock($out_file, LOCK_UN); // Unlock file
			fclose($out_file);
		}
		else
		{
			fclose($out_file);
			trigger_error("Failed to aquire lock on property file $prop_file", E_USER_WARNING);
			return null;
		}
		
		$lines = explode("\n", $contents);
		$is_php = (substr($prop_file, strlen($prop_file)-4) == '.php');
		if ($is_php)
			$lines = strip_php($lines);
		$n = count($lines);
		
	//	echo (implode('<br>', $lines));
		
		for ($i=0 ; $i<$n ; $i++)
		{
			$parts = explode('=', $lines[$i]);
			if (count($parts) == 2 && $parts[0] == $prop_name)
				return $parts[1];
		}
		
		return null;
	}
	
	
	
	function strip_php($lines)
	{
		$output = array();
		foreach ($lines as $line)
		{
			if ($line != '<'.'?php' && $line != '/'.'*' &&
				$line != '*'.'/' && $line != '?'.'>')
				$output[] = $line;
		}
		return $output;
	}
	
	
	function wrap_php($lines)
	{
		$output = array('<'.'?php', '/'.'*');
		foreach ($lines as $line)
			$output[] = $line;
		$output[] = '*'.'/';
		$output[] = '?'.'>';
		return $output;
	}
?>