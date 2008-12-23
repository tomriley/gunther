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
		die("pages.php");
	
	require_once('common.php');

class Page {
	
	var $name;
	var $filename;
	var $contents;

        function valid_name($page)
        {
                if (strpos($page, '/') !== FALSE ||
                        strpos($page, '\\') !== FALSE ||
                        strpos($page, '.') !== FALSE) 
                        return false;
                else
                        return true;
        }


	function validate_name($p) {
		if (!Page::valid_name($p)) {
			builder_error("Invalid page name '$p' (no slashes or dots please)");
		}
	}

	function Page($n) {
		Page::validate_name($n);

		$this->name = $n;
		$this->filename = preg_replace('/ /', '%20', $n);
	}

	function &get_object($n) {
		return new Page($n);
	}

	function name() {
		return $this->name;
	}

	function filename() {
		return $this->filename;
	}

	function path() {
		return GUNTHER_PAGE_DIR . $this->filename . '.page';
	}

	function lock_path() {
		return GUNTHER_PAGE_DIR . $this->filename . '.lock';
	}

	function upload_path() {
		return GUNTHER_PAGE_UPLOADS_BASE_DIR . $this->filename;
	}

	function exists() {
		return file_exists($this->path());
	}

	function contents() {
		if (!isset($this->contents) )
			if ($this->exists())
				$this->contents = file_get_contents($this->path());

		return $this->contents;
	}

	function put_contents($contents) {
		ignore_user_abort(true);
		
		if ($file = fopen($this->path(), "w"))
		{
			fwrite($file, $contents);
			fclose($file);
			$this->contents = $contents;
		}
		else
			trigger_error("Failed to open page file ".$this->path(), E_USER_WARNING);
		ignore_user_abort(false);
	}

	function title()
	{
		return title_of($this->contents());
	}
	
	
	function body()
	{
		return body_of($this->contents());
	}

        function meta_file_path()
        {
                return GUNTHER_PAGE_DIR . $this->filename . '.meta';
        }

        //
        // Get some meta-information about a page
        //

        function get_meta($preference)
        {
                return get_property($this->meta_file_path(), $preference);
        }

        //
        // Set some meta-information about a page.
        //

        function set_meta($preference, $value)
        {
                set_property($this->meta_file_path(), $preference, $value);
        }

        function clear_all_meta()
        {
                if (file_exists($file = $this->meta_file_path()))
                        return unlink($file);
                else
                        return true;
        }
	
	function rename($newname)
	{

		$newpage = new Page($newname);
		// we assume somebody already checked to make sure
		// the new page name wasn't already taken.


		rename($this->path(), $newpage->path());
		if ( file_exists($this->meta_file_path()))
			rename($this->meta_file_path(), $newpage->meta_file_path());
		if ( file_exists($this->lock_path()))
			rename($this->lock_path(), $newpage->lock_path());
		if (file_exists($this->upload_path()))
			rename($this->upload_path(), $newpage->upload_path());
	}

	function remove ()
	{

		if ($this->exists())
			if (!unlink($file = $this->path()))
				trigger_error("Failed to delete $file", E_USER_WARNING);

                // Remove meta-information for page
                $this->clear_all_meta();

                // Remove lock file if exists
                if (file_exists($file = $this->lock_path()))
                        if (!unlink($file))
                                trigger_error("Failed to delete $file", E_USER_WARNING);

                // Remove page uploads directory
                if (file_exists($dir = $this->upload_path()))
                        delete_dir_structure($dir, 2);

	}

	function &create_from_file($fn)
	{
		$fn = path_2_page($fn);
		return new Page($fn);
	}
		
	// returns an array of all the Pages
	function &get_list() 
	{
		if (!file_exists(GUNTHER_PAGE_DIR))
			 mkdir(GUNTHER_PAGE_DIR);

                if ($dh = opendir(GUNTHER_PAGE_DIR))
                {
			$files = array();
                        while (false !== ($filename = readdir($dh))) {
				if (is_page_file($filename))
					$files[] =& Page::create_from_file($filename);
                        }
                        return $files;
                }
                else
                        return false;
        }

};
	
	//
	// Return true if page exists
	//
	
	function page_exists($page)
	{
		$p = new Page($page);
		return $p->exists();
	}
	
	
	//
	// Strip '.page' from end of filename
	//
	
	function path_2_page($filename)
	{
		$p = substr($filename, 0, strlen($filename)-5);
		$p = preg_replace('/%20/', ' ', $p);
		return $p;
	}
	
	//
	// Return true if path is .page file
	//
	
	function is_page_file($file)
	{
		return (substr($file, strlen($file)-5) == '.page');
	}

	function path_2_template($fn)
	{
		$t = substr($fn, 0, strlen($fn)-4);
		$t = preg_replace('/%20/', ' ', $t);
		return $t;
	}

	function is_tpl_file($file)
	{
		return (substr($file, strlen($file)-4) == '.tpl');
	}

	
class Template {
	
	var $name;
	var $filename;
	var $contents;

        function valid_name($page)
        {
                if (strpos($page, '/') !== FALSE ||
                        strpos($page, '\\') !== FALSE ||
                        strpos($page, '.') !== FALSE) 
                        return false;
                else
                        return true;
        }


	function validate_name($p) {
		if (!Template::valid_name($p)) {
			builder_error("Invalid template name '$p' (no slashes or dots please)");
		}
	}

	function Template($n) {
		Template::validate_name($n);

		$this->name = $n;
		$this->filename = preg_replace('/ /', '%20', $n);
	}

	function &create_from_file($fn)
	{
		$fn = path_2_template($fn);
		return new Template($fn);
	}

	function &get_object($n) {
		return new Template($n);
	}

	function exists() {
		return file_exists($this->path());
	}

	function name() {
		return $this->name;
	}

	function filename() {
		return $this->filename;
	}

	function path() {
		return GUNTHER_TPLS_DIR . $this->filename . '.tpl';
	}

	function contents () {
		if (!isset($this->contents) )
			if ($this->exists())
				$this->contents = file_get_contents($this->path());

		return $this->contents;
	}
	function remove () {
		if ($this->exists()) {
			if (!unlink($this->path()))
				trigger_error("Failed to delete " . $this->path(),  E_USER_WARNING);
		} else {
			trigger_error("Why doesn't " . $this->path() . " exist?", E_USER_WARNING);
		}
	}

	function rename($newname)
	{

		$newtemplate = new Template($newname);
		// we assume somebody already checked to make sure
		// the new template name wasn't already taken.


		rename($this->path(), $newtemplate->path());
	}

	// returns an array of all the Templates
	function &get_list() 
	{
		if (!file_exists(GUNTHER_TPLS_DIR))
			 mkdir(GUNTHER_TPLS_DIR);

                if ($dh = opendir(GUNTHER_TPLS_DIR))
                {
			$files = array();
                        while (false !== ($filename = readdir($dh))) {
				if (is_tpl_file($filename))
					$files[] =& Template::create_from_file($filename);
                        }
                        return $files;
                }
                else
                        return false;
        }

		
};
?>
