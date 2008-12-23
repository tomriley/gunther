<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
             "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Edit {$edit_type}: {$docpart_name}</title>

<script language="JavaScript" type="text/JavaScript"> 
{literal}
<!--
	/* Append text to element 'element' */
	function appendText(element, text) 
	{
		if (element.createTextRange && element.caretPos)
		{
			element.caretPos.text = caretPos.text + text;
		}
		else
		{
			element.value += text;
		}
		element.focus();
	}
	
	function setText(element, text)
	{
		element.value = text;
	}

	function openWindow(location)
	{
		var x = 40, y = 5;
		var width = 500, height = 550;
		
		if (document.all)
		{
			x = screen.width-width-x;
			height = screen.height-40;
		}
		
		msgWindow = window.open('','newWin','toolbar=0,scrollbars=1,location=0,statusbar=1,menubar=0,resizable=1,width='+width+',height='+height+',screenX='+x+',screenY='+y+',top='+y+',left='+x);
		msgWindow.location.href = location;
	}
	
-->
{/literal}
</script>


<style type="text/css">
<!--

{literal}

body {
	background-color: #E5E5E5;
}

font,th,td,p { font-family: Verdana, Arial, Helvetica, sans-serif }
a:link,a:active,a:visited { color : #226699; }
a:hover		{ text-decoration: underline; color : #DD6955; }
hr	{ height: 0px; border: solid #D1D7DC 0px; border-top: 1px solid #D1D7DC;}

form { margin-bottom: 0 ; }

.bodyline	{ background-color: #FFFFFF; border: 1px #98AAB1 solid; }

.headercell { background-color: #CFDDDE; border-bottom: 1px #98AAB1 solid; }

.tableheader { background-color: #EFEFEF;
				border-bottom: 1px #A9BBC2 solid; }
.tablecell { background-color: #FFFFFF; border-bottom: 1px #A9BBC2 solid; }
.tableborder { background-color: #FFFFFF;
			border-top: 1px #A9BBC2 solid;
			border-left: 1px #A9BBC2 solid;
			border-right: 1px #A9BBC2 solid; }


h5 { font-size : 12px; margin-bottom: 0; }
h4 { font-size : 14px; margin-bottom: 0; }
h3 { font-size : 18px; margin-bottom: 0; }
h2 { font-size : 22px; margin-bottom: 0; }
h1 { font-size : 26px; margin-bottom: 0; }

/* General text */
.gen { font-size : 12px; }
.genmed { font-size : 11px; }
.gensmall { font-size : 10px; }
.gen,.genmed,.gensmall { color : #000000; }
a.gen,a.genmed,a.gensmall { color: #226699; text-decoration: none; }
a.gen:hover,a.genmed:hover,a.gensmall:hover	{ color: #DD6955; text-decoration: underline; }

{/literal}

-->
</style>
</head>
<body>


<form enctype="multipart/form-data" name="editform" action="{$submit_action}" method="POST">

<table width="100%" cellpadding="10" cellspacing="0" border="0" align="center">
<tr><td>

<table width="100%" class="bodyline" cellspacing="0" cellpadding="8" border="0" align="center">
	<tr>
	{if $edit_type == 'page'}
		<td class="headercell"><span class="gen"><b>Title:</b> <input class="gen" type="text" name="title" size="50" value="{$page_title}"/></span></td>
		<td class="headercell" align="right"><input type="button" onClick="openWindow('{$web_root}help.php')" value="Quick Help..."></td>
	{else}
               <td class="headercell gen">
               Editing Template: {$docpart_name}
               </td>

	{/if}
	</tr>
	<tr>
		<td class="tablecell" colspan="2">
		<textarea name="content" rows="25" cols="80" class="gen">{$page_body}</textarea>
		</td>
		</tr>
		<tr><td class="gen" align="right" colspan="2">
			<input type="submit" value="Commit Changes" />
			<input type="reset" value="Reset changes" />
		</td>
	</tr>
</table>

<br>

<table width="100%" class="bodyline" cellspacing="0" cellpadding="8" border="0" align="center">

<tr>
<td class="tablecell">

<!-- existing uploads -->

<table width="100%" class="tableborder" cellspacing="0" cellpadding="4" border="0" align="center">

<tr>
<td class="headercell gen" colspan="3"><b>Existing Uploads:</b></td>
</tr>

{foreach from=$existing_uploads item=upload}
	<tr>
		<td class="tablecell gen"><a href="{$upload.URL}">{$upload.name}</a> ({$upload.size})</td>
		<td class="tablecell gen"><img src="{$web_root}images/tag.gif" border=0 /> <code>{$upload.insertText}</code></td>
		<!--     <a href="#" onClick="setText(document.editform.snippet, '{$upload.insertText}')">insert</a> -->
		 
        <td class="tablecell gen" align="right"><input type="checkbox" name="{$upload.formid}" value="y" /> Delete</td>
	</tr>
{foreachelse}	
	<tr>
	<td class="tablecell gen" colspan="3"><i>None</i></td>
	</tr>
{/foreach}

</table>


</td>
</tr>

<!-- file upload form items -->
<input type="hidden" name="MAX_FILE_SIZE" value="2000000" />
<tr><td class="tablecell">
<p class="gen">Send this file: <input name="fileOne" type="file" /></p></td></tr>
<tr><td class="tablecell">
<p class="gen">Send this file: <input name="fileTwo" type="file" /></p></td></tr>
<tr><td class="tablecell">
<p class="gen">Send this file: <input name="fileThree" type="file" /></p></td></tr>
<tr><td class="gen" align="right">
<input type="submit" value="Submit &amp; Upload" /></td></tr>

</table>

</td></tr>
</table>

<p align="center" class="genmed">{$manage}</p>
</form>
</body>
</html>
