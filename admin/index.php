<?

	date_default_timezone_set('Asia/Seoul');

	include "../init.php";

	$popup = false;

	if ( $_GET["mode"] == '' ) {
		$_GET["mode"] = $mode = "main";
	}
	else $mode = $_GET["mode"];

?>
<html>
<head>
<title>CMI 의학혁신연구센터 회의실 관리 MAIN</title>
<link rel="stylesheet" type="text/css" href="theme_purple.css">
<link rel="shortcut icon" href="favicon.ico">
</head>

<body topmargin="0" leftmargin="0">
<? if ( $_GET["suppress_layout"] == '' ): ?>
<table border="0" cellspacing="0" cellpadding="0" width="100%">
<tr>
	<td style="padding:5px; padding-bottom:10px;">
		<a href="?" /><img src="image/logo.png" width="400" /></a>
	</td>
	<td valign="bottom">
		<div align="right" style="padding-left:10px;" valign="bottom">
		</div>
	</td>
</tr>
</table>

<table border="0" cellspacing="0" cellpadding="5" width="100%">
  <tr class="headline">
	<th width="70" scope="col"><a href="?mode=main" <? if ( $mode == "main" ): ?>class="menu_on"<? endif ?>>회의예약관리</a></th>
	<th width="60" scope="col"><a href="?mode=notice" <? if ( $mode == "notice" ): ?>class="menu_on"<? endif ?>>공지사항관리</a></th>
	<th width="60" scope="col"><a href="?mode=device" <? if ( $mode == "device" ): ?>class="menu_on"<? endif ?>>단말기관리</a></th>
	<th width="60" scope="col"><a href="?mode=stat" <? if ( $mode == "stat" ): ?>class="menu_on"<? endif ?>>사용통계관리</a></th>
	<th width="10" scope="col" style="padding-right:0px;">&nbsp;</th>
  </tr>
</table>
<br>

<table width="100%" cellpadding="10" border="0">
<tr>
	<td width="100%">
	<div align="left">
<? endif // suppress_layout ?>
<?

	switch( $mode ) {
		case "main":
			if ( $_GET["submode"] == '' ) $_GET["submode"] = "month";
			include $_GET["mode"]."_".$_GET["submode"].".php";
		break;
		case "stat": include "stat.php"; break;
		case "device": include "device.php"; break;
		case "notice": include "notice.php"; break;
	}

?>
<? if ( $_GET["suppress_layout"] == '' ): ?>
	</div>
	</td>
</tr>
</table>
<? endif // suppress_layout ?>	
</body>
</html>