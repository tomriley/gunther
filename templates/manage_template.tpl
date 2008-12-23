<html>
<head>
<title>{title}</title>
<style type="text/css">
<!--


/********************************************************************
 * CSS rules which may be present in html code that gunther generates
 * from page content. These should always be present but maybe edited
 * at will.
 ********************************************************************/
 
font,th,td,p	{ font-family: Verdana, Arial, Helvetica, sans-serif }
a:link,a:active,a:visited	{ color : #226699; }
a:hover			{ text-decoration: underline; color : #DD6955; }
hr				{ height: 0px; border: solid #D1D7DC 0px; border-top: 1px solid #D1D7DC;}

pre {	color : #333333; border: 1px #98AAB1 solid;
		background-color: #F3F3F3;
		border-width: 1px;
		margin: 22px; padding: 10px;
		font-size : 10 px;
	}

h5 { font-size : 12px; margin-bottom: 0; }
h4 { font-size : 14px; margin-bottom: 0; }
h3 { font-size : 18px; margin-bottom: 0; }
h2 { font-size : 22px; margin-bottom: 0; }
h1 { font-size : 26px; margin-bottom: 0; }

.tableheader { background-color: #EFEFEF;
			border-bottom: 1px #A9BBC2 solid;
			font-weight: bold; }
.tablecell { background-color: #FFFFFF;
			border-bottom: 1px #A9BBC2 solid; }
.tableborder { background-color: #FFFFFF;
			border-top: 1px #A9BBC2 solid;
			border-left: 1px #A9BBC2 solid;
			border-right: 1px #A9BBC2 solid;
			font-size: 12px; }





/********************************************************************
 * CSS rules used in the template. These can be removed or replaced
 * with rules specific to your template.
 ********************************************************************/

form { margin-bottom: 0 ; }

.bodyborder	{ background-color: #FFFFFF; border: 1px #98AAB1 solid; }

.headercell { background-color: #BDC6D9; border-bottom: 1px #98AAB1 solid; }

body { background-color: #E5E5E5; }

select { font-size: 10px; }

/* General text */
.p { font-size : 12px; }
.gen { font-size : 12px; }
.genmed { font-size : 11px; }
.gensmall { font-size : 10px; }
.gen,.genmed,.gensmall { color : #000000; }
a.gen,a.genmed,a.gensmall { color: #226699; text-decoration: none; }
a.gen:hover,a.genmed:hover,a.gensmall:hover	{ color: #DD6955; text-decoration: underline; }


.thinpre {	color : #333333; border: 1px #98AAB1 solid;
		background-color: #F3F3F3;
		border-width: 1px;
		margin: 5px; padding: 10px;
		font-size : 10 px;
	}

-->

</style>
</head>
<body>

<table width="100%" cellpadding="10" border="0" align="center">
<tr><td>

<table width="100%" class="bodyborder" cellspacing="0" cellpadding="8" border="0" align="center">
	<tr>
		<td class="headercell"><h4>{title}</h4></td>
		<td class="headercell" align="right">{statistics}</td>

	</tr>
	<tr>
		<td colspan="2" class="gen">{body}</td>
	</tr>
</table>

</td></tr>
</table>

<p align="center" class="genmed">{logout}</p>

</body>
</html>