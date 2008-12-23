<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
             "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>{$page_title}</title>


<style type="text/css">
<!--

{literal}

/********************************************************************
 * CSS rules which may be present in html code that gunther generates
 * from page content. These should always be present but maybe edited
 * at will.
 ********************************************************************/
 
font,th,td,p	{ font-family: Verdana, "Lucida Grande", Tahoma, Helvetica; }
a:link,a:active,a:visited	{ color : #000000; border-bottom: 1px #CCCCCC solid; text-decoration: none; }
a:hover			{ color : #66758A; }
hr				{ height: 0px; border: solid #CCCCCC 0px; border-top: 1px solid #CCCCCC;}

pre {	color : #333333;
		background-color: #F4F4F4;
		margin: 18px; padding: 10px;
		font-size : 10 px;
		border: 1px #98AAB1 solid;
		overflow: none;
	}

h5 { font-family: Georgia, Times, Serif; font-size : 12px; font-weight: normal; margin-bottom: 0; }
h4 { font-family: Georgia, Times, Serif; font-size : 14px; font-weight: normal; margin-bottom: 0; }
h3 { font-family: Georgia, Times, Serif; font-size : 16px; font-weight: normal; margin-bottom: 0; }
h2 { font-family: Georgia, Times, Serif; font-size : 20px; font-weight: normal; margin-bottom: 0; }

.tableheader { background-color: #F4F4F4;
			border-bottom: 1px #CCCCCC solid;
			border-right: 1px #CCCCCC solid;
			font-weight: bold; }
.tablecell { background-color: #FFFFFF;
			border-bottom: 1px #CCCCCC solid;
			border-right: 1px #CCCCCC solid; }
.tableborder { background-color: #FFFFFF;
			border-top: 1px #CCCCCC solid;
			border-left: 1px #CCCCCC solid;
			font-size: 11px; }

ol
{
	list-style-position: outside;
  	padding-left: 12px;
  	margin-left: 8px;
}

ul
{
	list-style-position: outside;
  	padding-left: 12px;
  	margin-left: 8px;
}


/********************************************************************
 * CSS rules used in the template. These can be removed or replaced
 * with rules specific to your template.
 ********************************************************************/

form { margin-bottom: 0 ; }

.bluebar { background-color: #F5F5F5; padding-left: 9px; padding-right: 6px; padding-top: 4px; padding-bottom: 4px;
			font-size: 11px; border-top: 1px #BBBBBB solid;
				border-bottom: 1px #BBBBBB solid;  }

.sidebarcell { background-color: #FFFFFF;  border-right: 1px #BBBBBB solid;
				border-left: 1px #BBBBBB solid; border-bottom: 1px #BBBBBB solid;  }

.bodyCell { background-color: #FFFFFF;
			border-right: 1px #BBBBBB solid;
			border-bottom: 1px #BBBBBB solid; }

.headerTable { background-color: #FFFFFF; border-top: 1px #BBBBBB solid;
				border-right: 1px #BBBBBB solid;
				border-left: 1px #BBBBBB solid; }

{/literal}
{if $is_admin}
{literal}
body { background-color: #DDDDDD; }
{/literal}
{else}
{literal}
body { background-color: #E4E4E4; }
{/literal}
{/if}
{literal}

a.bluebar 	{ color : #FFFFFF; }

/* General text */
.p { font-size : 11px; line-height: 15px; }
.gen { font-size : 11px; line-height: 15px; }
.genmed { font-size : 10px; line-height: 14px; }
.gensmall { font-size : 9px; line-height: 13px; }
.gen,.genmed,.gensmall { color : #000000; }

.bigtitle {
	font-family: Georgia, Times, Serif;
	font-size : 22px;
	padding-left: 3px;
	padding-right: 10px;
	margin-bottom: 4px;
}



.thinpre {	color : #333333; border: 1px #98AAB1 solid;
		background-color: #F3F3F3;
		border-width: 1px;
		margin: 5px; padding: 10px;
		font-size : 10px;
	}

#left
{
	padding: 0px;
	margin: 0px;
	width: 165px;
	float: right;
	padding-left : 5px;
	padding-bottom : 15px;
	margin-bottom : 10px;
	margin-left: 15px;
	border-left: 1px solid #bbb;
	border-bottom: 1px solid #bbb;
	line-height: 140%;
}

body {
   margin: 0
}


-->

{/literal}

</style>
</head>

<body

<img src="{$web_root}images/spacer.gif" width="1" height="32" alt="">

<div align="center">

<table border="0" cellspacing="0" cellpadding="6" width="600" class="gen headerTable">
	<tr>
		<td rowspan="2" valign="bottom" align="left" height="80">
		<div class="bigtitle">{$page_title}</div>
		</td>
		<td align="right" valign="top" class="gen" nowrap>
		{$smarty.now|date_format:"%A, %e %B %Y"}
		</td>
	</tr>
	
	<tr>
	<td align="right" valign="bottom" class="gen" nowrap>
	{gunther_include page="TopNavItems"}
	</td>
	</tr>
	
	<tr><td colspan="2" class="bluebar">{gunther_include page="TopBarMessage"}</td></tr>
	
</table>

<table cellpadding="9" width="600" cellspacing="0" border="0" class="gen">
	<tr>
		<td class="sidebarcell" width="150" height="440" valign="top" align="left" nowrap>
		{gunther_include page="SideBar"}
		</td>
	
		<td class="bodyCell" width="500" valign="top" align="left">
			<table width="100%" cellpadding="2" cellspacing="0" border="0" class="gen">
			<tr>
				<td valign="top">
				{$page_body}
<br>
<img src="{$web_root}images/spacer.gif" width="1" height="32" border="0" alt="" >
				</td>
			</tr>
			</table>
		</td>
	</tr>
</table>


<table border="0" cellspacing="0" cellpadding="4" width="600" class="gen">
	
	<tr>
<td align="left" class="genmed" valign="middle">
<a href="http://gunther.sourceforge.net">
<img src="{$web_root}images/guntherbadge.gif" width="80" border="0" height="15" align="center" alt="gunther logo"></a>&nbsp;{$manage}</td>
<td align="right" class="genmed" valign="middle">
		Last modified: {$last_mod_time|date_format:"%a, %e %B %Y (%r)"}&nbsp;</td>
	</tr>
	
</table>

</div>
<img src="{$web_root}images/spacer.gif" width="1" height="40" alt="">

</body>
</html>
