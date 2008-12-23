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
	require_once('include/tables.php');
	require_once('include/common.php');
	require_once('include/format.php');

	if (isset($_GET['topic']))
		$topic = $_GET['topic'];
	else 
		$topic = 'quick';

	
	unset($gunther_demo_mode); // avoid stripping html
	$body = process_content('Help', get_help_markup($topic));
	
	$smarty = new GuntherTemplate(GUNTHER_CORE_TPLS_DIR);
	
	$smarty->assign('page_title', 'Help');
	$smarty->assign('page_body', $body);
	
	$smarty->display('basic_template.tpl');
	
	//
	// Bit chunk of help text.
	//
	
	function get_help_markup ()
	{
		return <<<EOT
!Basic Styles

Be aware that a lot of the following styles (for example, the header styles) are customisable by changing CSS rules within your own templates.

><
||Feature||><Mark-up||><Resulting format||
| Underlining | {{&#95;&#95;text&#95;&#95;}} | __text__ |
| Bold | {{&#42;&#42;text&#42;&#42;}} | **text** |
| Italic | {{&#47;&#47;text&#47;&#47;}} | //text// |
| Superscript | super{{&#94;&#94;script&#94;&#94;}} | super^^script^^ |
| Subscript | sub{{&#126;&#126;script&#126;&#126;}} | sub~~script~~ |
| Strikethrough | {{&#45;&#45;text&#45;&#45;}} | --text-- |
| Small header | !Header Text | <h4>Header Text</h4> |
| Medium header | !!Header Text | <h3>Header Text</h3> |
| Large header | !!!Header Text | <h2>Header Text</h2> |
| Preformatted section | {{&#123;&#123;}}<br>{{&nbsp;&nbsp;text}}<br> {{&#125;&#125;}} | <pre class="thinpre">text</pre> |
| Code | &#123;&#123;some text&#125;&#125; | {{some text}} |
| Link to page | &#91;PageName&#93; | <a href="#">PageName</a> |
| Link to page with alternative label | &#91;PageName&#124;link label&#93; | <a href="#">link label</a> |
| Link with image | &#91;PageName&#124;@filename.jpg&#93; |>< <a href="#"><img border="0" src="images/water.jpg" /></a> |
| Embed an uploaded image |  &#91;@filename.jpg&#93; |>< <img border="0" src="images/water.jpg" /> |
| Thumbnail an uploaded image |  &#91;@filename.jpg:100x100&#93; |>< <img border="0" src="images/water2.jpg" /> |
| Thumbnail with border |  &#91;@filename.jpg:100x100b5&#93; |>< <img border="0" src="images/water3.gif" /> |
| Horizontal rule (on a line) | ---- | <hr/> |
><


!Lists

You can create an unordered list by prepending each line with a single hyphen or multiple hyphens to create nested lists.
{{
- item one
- item two
-- sub item
}}
Creates:

- item one
- item two
-- sub item

Ordered lists are created by prepending lines with hashes:
{{
# item one
# item two
## sub item
}}
Creates:

# item one
# item two
## sub item



!Text Alignment

Center alignment is achieved by surrounding a section with lines containing '><':
{{
><
__Hello World__
><
}}
Aligning text to the right is similarly achieved with the '>>' tag:
{{
>>
__Hello World__
>>
}}

!Image Alignment

When you place an image using the &#91;@image&#93; tag, you can align the image to the left or to the right causing the text to flow down either the left of right side of the image. For example:
{{
[@<imagename.jpg]

[@>imagename.jpg]
}}
The first tag aligns the image to the left and the second aligns the image to the right.

Centering the image left-to-right is also possible.
{{
[@|imagename.jpg]
}}


!Making a table
{{
|| Header Text           || Another Header    ||
|  contents of cell one  |  cell two          |
| another cell           | __some more text__ |
|>< center aligned       |>> right aligned    |
}}
The first vertical bar must be the first character on each line. It is not important for the other vertical bars | to be aligned with each other. You can have header-style rows in several places if you want to. You can use formatting tags and links within cells.

Here is the resulting table:

><
|| Header Text || Another Header   ||
|   contents of cell one   |  cell two         |
| another cell | __some more text__ |
|>< center aligned |>> right aligned |
><

You can also place a + sign at the end of the first table row to indicate the the table should be made to be full width (100%):
{{
|| Header Text           || Another Header    ||+
...
}}

Row span and column spans may also be specifed at the start of a cell. The format is '+c.r' where 'c' is the number of columns and 'r' is the number of rows. If 'r' is omitted, the '.' must also be omitted.

{{
||+2.2>< test +2.2>< ||>> just >>|| hello ||
|+2 '+2' |
| x | y | z |
|=+1.2 '=+1.2' works? | yet more |
|and more|
}}

Which renders as:

><
||+2.2>< test +2.2>< ||>> just >>|| hello ||
|+2 '+2' |
| x | y | z |
|=+1.2 '=+1.2' works? | yet more |
|and more|
><

The '=' at the begining of the first cell in the fourth row says to treat that cell as if it was on a header row.


!Using PHP Within Pages

This is now possible using the delimiters &#91;:PHP] and &#91;/PHP] on their own lines. For example:
{{
[:PHP]
    echo 'hello world';
[/PHP]
}}


!Escaping Formatting

The &#91;:Raw] directive allows you to place unmodifed text in the output. This is useful for placing html or css or javascript fragments in the web page. No formatting sequences are honored and no line breaks are generated:
{{
[:Raw]
<table>
<tr><td>a raw html table<td><tr>
</table>
[/Raw]
}}


!Create An Image Gallery

To create an image gallery you need to upload the full-sized photos. The following two examples assume that you have uploaded the images to page on which you will be placing the gallery of thumbnails. A later example shows how to reference images uploaded on a different page. and add a block of ':Gallery' markup to your page where you want the thumbnails to be displayed. A simple gallery:
{{
[:Gallery]

image1.jpg Caption for image 1
image2.jpg Caption for image 2
image3.jpg Caption for image 3
image4.jpg Caption for image 4
image5.jpg Caption for image 5
image6.jpg Caption for image 6

[/Gallery]
}}
The example above will display six thumbnails in a table. Each thumbnail will link to a page displaying the full-sized image. The default table dimensions, thumbnail size and other settings may not be suitable. You can customise your gallery by adding some parameters to the opening &#91;:Gallery&#93; tag. Here is an example:
{{
[:Gallery size:120x120b4 columns:3 bigsize:400x1000]
}}
This will create a gallery of thumbnails at most 120 pixels wide and 120 pixels high and with a 4 pixel border. The thumbnail table will be three columns wide. The size of the enlarged images will be at most 400 pixels wide and 1000 pixels high.

You can also use pictures uploaded on another page by specifying a 'page' parameter:
{{
[:Gallery size:100x100 columns:3 page:MyPhotoPage]
}}
Gunther will then look for the images listed as uploads on page 'MyPhotoPage'.

You can also choose which template to use when displaying full-sized images:
{{
[:Gallery size:100x100 columns:3 template:TemplateName]
}}

!Embedding Pages within Pages

All or part of the contents of a page may be embedded in a second page using the [:Embed] directive.

{{
[:Embed page:"Name of page" entry:"Name of entry" class:class-name]
}}

Quotes are only needed if the page or entry name has spaces in it. 

{{page}} must be present. 

If {{entry:}} is present, only that entry will be embedded (see below). The special entry names {{FIRST}} and {{LAST}} choose respectively, the lexically first and last entry in the page. If {{entry}} is not given, the entire page is embedded.

{{Class}} sets the class attribute for the &lt;div> tag that will surround the embedded contents. This allows you to modify the presentation of the embedded page using CSS rules. If {{class}} is not given, it defaults to 'embed-page'.

The above example would result in html of the form:

{{
<div class="class-name">
<!-- contents of the "Name of entry" entry in "name of page" page -->
</div>
}}

!Marking off entries

Sections of content may be marked off as an {{entry}} using an [:Entry] tag block.

{{
[:Entry name:"My entry" class:some-class]
...
[/Entry]
}}

This will translate to:

{{
<div class="some-class">
...
</div>
}}

If {{class}} is not given, it defaults to 'entry'. The {{name}} may be referenced in [:Embed] tags to control content embedding.

EOT;
	}
