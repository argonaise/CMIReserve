<?

	$m = new modlist("noticeinfo");
	$m->setopt("listall", 1);

?>
<div id="autocomplete" style="position:absolute; top:0px; left:0px; width:200px; height:200px; display:none;">
<iframe id="autocomplete_iframe" style="width:100%; height:100%"></iframe>
</div>

<SCRIPT>
	function fnToggleActive( ni_no ) {
		location.href = 'prc.php?mode=prc_toggleactive&ni_no=' + ni_no;
	}
</SCRIPT>

<table cellspacing="0" cellpadding="0">
<tr align="center" class="line_toggle_1">
	<th width="30">#</th>
	<th width="70">표시여부</th>
	<th width="500">상단에 표시될 공지내용</th>
	<th width="100">표시순서</th>
	<th width="110">관리</th>
</tr>
<form method="post" action="prc.php?mode=prc_addnotice">
<tr align="center" class="line_toggle_2">
	<th>+</th>
	<th>+</th>
	<th><input type="text" name="ni_subject" style="width:400px;" value="" /></th>
	<th>+</th>
	<th><input type="submit" value="추가" /></th>
</tr>
</form>
<?
	$toggle_color = 1;
	$color = array( 0 => "line_toggle_1", 1 => "line_toggle_2" );

	while( $data = $m->get( "*", $where, "order by ni_seq desc" ) ):

		if ( $toggle_color == 0 ) $toggle_color = 1; else $toggle_color = 0;
?>
<tr class="<?=$color[$toggle_color]?>" align="center" height="40">
	<td><?=$data["lc"]?></td>
	<td><input type="button" value="<?=($data["ni_active"]=='0')?"표시안함":"표시됨"?>" onclick="fnToggleActive(<?=$data["ni_no"]?>)" /></td>
	<td><?=nl2br( $data["ni_subject"] )?></td>
	<td>
		<input type="button" value="▲" onclick="location.href='prc.php?mode=prc_sequp&ni_no=<?=$data["ni_no"]?>&ni_seq=<?=$data["ni_seq"]?>'">
		<input type="button" value="▼" onclick="location.href='prc.php?mode=prc_seqdown&ni_no=<?=$data["ni_no"]?>&ni_seq=<?=$data["ni_seq"]?>'">
	</td>
	<td>
		<input type="button" value="삭제" onclick="location.href='prc.php?mode=prc_delnotice&ni_no=<?=$data["ni_no"]?>'">
	</td>
</tr>
<?
	endwhile;
?>
<? if ( $m->nodata() ): ?>
<? $toggle_color = 0; ?>
<tr class="<?=$color[$toggle_color]?>" align="center" height="70">
	<td colspan="6">표시할 항목이 없습니다.</td>
</tr>
<? endif ?>
</table><br />
<SCRIPT>
	function fnSeqUp( ni_no ) {
		
		alert(iframe);
		iframe.src = 'prc.php?mode=prc_sequp&ni_no=' + ni_no;
	}

	function fnSeqDown( ni_no ) {
		var iframe = document.getElementById( "autocomplete_iframe" );
		iframe.src = 'prc.php?mode=prc_seqdown&ni_no=' + ni_no;
	}
</SCRIPT>