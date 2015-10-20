<?

	$m = new modlist("deviceinfo");
	$m->setopt("listall", 1);

?>

<table cellspacing="0" cellpadding="0">
<tr align="center" class="line_toggle_1">
	<th width="30">#</th>
	<th width="200">회의실명</th>
	<th width="100">단말기기 IP</th>
	<th width="110">관리</th>
</tr>
<form method="post" action="prc.php?mode=prc_addip">
<tr align="center" class="line_toggle_2">
	<th width="30">+</th>
	<th width="200">
		<select name="di_room">
		<? foreach( $confroom as $roomname ): ?>
			<option value='<?=$roomname?>'><?=$roomname?></option>
		<? endforeach ?>
		</select>
	</th>
	<th width="100"><input type="text" name="di_ip" value="" /></th>
	<th width="110"><input type="submit" value="추가" /></th>
</tr>
</form>
<?
	$toggle_color = 1;
	$color = array( 0 => "line_toggle_1", 1 => "line_toggle_2" );

	while( $data = $m->get( "*", $where, "order by di_room" ) ):

		if ( $toggle_color == 0 ) $toggle_color = 1; else $toggle_color = 0;
?>
<tr class="<?=$color[$toggle_color]?>" align="center" height="40">
	<td><?=$data["lc"]?></td>
	<td><?=$data["di_room"]?></td>
	<td><?=$data["di_ip"]?></td>
	<td>
		<input type="button" value="삭제" onclick="location.href='prc.php?mode=prc_delip&di_no=<?=$data["di_no"]?>'">
	</td>
</tr>
<?
	endwhile;
?>
</table><br />
