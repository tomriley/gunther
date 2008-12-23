<?php
/*
        Gunther
        http://gunther.sourceforge.net

        Copyright (c) 2004, Mark Hollomon

        Released under the GNU General Public License

        This program is free software; you can redistribute it and/or modify
        it under the terms of the GNU General Public License as published by
        the Free Software Foundation; either version 2 of the License, or
        (at your option) any later version.
*/

/*
 * This is basically a readonly array. 
 */
class ls_buffer {
	var $lines = NULL;
	var $count = 0;

	function ls_buffer (&$text) {
		$this->lines = explode("\n", $text);
		$this->count = count($this->lines);
	}

	function count() {
		return $this->count;
	}

	function get($index) {
		if ($index < $this->count and $index >= 0)
			return $this->lines[$index];
		else 
			return false;
	}
}

class linesource {
	var $buffer = NULL;
	var $index = 1000;
	var $start = 0;
	var $end = 0;

	function linesource() {
	}

	function &from_text(&$text) {
		$n = new linesource();
		$n->buffer =& new ls_buffer($text);
		$n->start  = 0;
		$n->index = $n->start;
		$n->end = $n->buffer->count() -1;

		return $n;
	}

	function &sub_source($start, $end) {
		$n = new linesource();
		$n->buffer =& $this->buffer;
		$n->start = $start;
		$n->index = $n->start;
		$n->end = $end;

		return $n;
	}
		

	function getline() {
		if ($this->more())
			return $this->buffer->get($this->index++);
		else
			return false;
	}

	function more () {
		return ($this->buffer != NULL and $this->index <= $this->end);
	}

	function putback () {
		if ($this->index > $this->start)
			$this->index--;
	}

	function rewind () {
		$this->index = $this->start;
	}

	function peek ($offset=0) {
		$i = $this->index + $offset;
		if ($i < $this->start or $i > $this->end)
			return false;
		else
			return $this->buffer->get($i);
	}

	function getpos () {
		return $this->index;
	}

}

// vim:set ai ts=4 sw=4:
?>
