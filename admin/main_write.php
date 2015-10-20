<script type="text/javascript" src="datepicker.js">{"describedby":"fd-dp-aria-describedby"}</script>
<link href="datepicker.css" rel="stylesheet" type="text/css" />

<div style="padding-left:5px;">
	<h3 class="h3" style="font-size:22pt;">
	<? if ( $_GET["rr_no"] != '' ): ?>
	예약 정보 수정
	<? else: ?>
	예약 정보 등록
	<? endif ?>
	</h3>
</div>

<?
	$w = array();

	if ( $_GET["rr_no"] != '' ) {
		$table = $DB->table( "roomreserve" );
		$w = $table->selectfetch( "", "*", "rr_no='".$_GET["rr_no"]."'" );
	}
	else {
		if ( $_GET["date"] == '' ) $w["rr_date"] = date( "Y-m-d" );
		else $w["rr_date"] = $_GET["date"];
		$now = time();
		$now_h = date( "H" );
		$next_h = date( "H", $now + 3600 );
		$w["rr_time_begin"] = $now_h."00";
		$w["rr_time_end"] = $next_h."00";
	}
?>

<SCRIPT>
	function fnSetName() {
		var s = document.getElementById("rr_name_select");
		document.getElementById("rr_name").value = s.options[s.selectedIndex].text;
		document.getElementById("rr_phone").value = s.options[s.selectedIndex].value;
	}

	function fnSuccess() {
		alert( '정보를 저장하였습니다.' );
		parent.document.location.reload();
	}
</SCRIPT>

<div id="prc_frame_div" style="background-color:white; position:absolute; top:0px; left:0px; width:500px; height:60px; display:none;">
<iframe id="prc_iframe" name="prc_iframe" style="width:100%; height:100%"></iframe>
</div>

<form name="InputForm" method="post" action="prc.php?mode=prc_adminwrite" target="prc_iframe">
<input type="hidden" name="rr_no" value="<?=$_GET["rr_no"]?>" />
<table cellpadding="0" cellspacing="0" border="0" width="97%" style="margin:10px;">
<tr align="center" class="line_toggle_1">
	<th width="200">이름</th>
	<th align="left">
		<select id="rr_name_select" onchange="fnSetName()">
		<option value="">--------------</option>
		<? foreach( $mem_regno as $regno => $name ): ?>
			<option value="<?=$regno?>"><?=$name?></option>
		<? endforeach ?>
		</select>
		<input type="text" id="rr_name" name="rr_name" size="10" value="<?=$w["rr_name"]?>" />
	</th>
</tr>
<tr align="center" class="line_toggle_2">
	<th>전화번호</th>
	<th align="left"><input type="text" id="rr_phone" name="rr_phone" size="10" value="<?=$w["rr_phone"]?>" /></th>
</tr>
<tr align="center" class="line_toggle_1">
	<th>회의주제</th>
	<th align="left"><input type="text" name="rr_subject" size="40" value="<?=$w["rr_subject"]?>" /></th>
</tr>
<tr align="center" class="line_toggle_2">
	<th>회의실</th>
	<th align="left">
		<select id="rr_name_select" name="rr_room">
		<? foreach( $confroom as $room ): ?>
			<option value="<?=$room?>" <? if ( $w["rr_room"] == $room ): ?>selected<? endif ?>><?=$room?></option>
		<? endforeach ?>
		</select>
	</th>
</tr>
<tr align="center" class="line_toggle_1" height="80">
	<th>사용일시</th>
	<th align="left">
		사용일자 : <input type="text" id="rr_date" name="rr_date" style="width:75px; text-align:center; background-color:#dddddd;" value="<?=$w["rr_date"]?>" readonly />
		<script type="text/javascript"> var opts = { formElements:{"rr_date":"Y-ds-m-ds-d"} }; datePickerController.createDatePicker(opts); </script>
		<br />
		* 24시간 기준으로, 분단위는 30분 단위로 HHMM 형식으로 기록해 주세요.<br />
		시작시간 : <input type="text" name="rr_time_begin" size="4" maxlength="4" value="<?=$w["rr_time_begin"]?>" onfocus="this.value=''" /> ~
		종료시간 <input type="text" name="rr_time_end" size="4" maxlength="4" value="<?=$w["rr_time_end"]?>" onfocus="this.value=''" />
	</th>
</tr>
<tr align="center" class="line_toggle_2" height="70">
	<th>반복설정</th>
	<th align="left">
	<? $week_set = explode( ",", $w["rr_repeat_week"] ); ?>
		<input type="checkbox" name="rr_repeat_week[]" value="월" <? if ( in_array( "월", $week_set ) == true ): ?>checked<? endif ?> /> 월
		<input type="checkbox" name="rr_repeat_week[]" value="화" <? if ( in_array( "화", $week_set ) == true ): ?>checked<? endif ?> /> 화
		<input type="checkbox" name="rr_repeat_week[]" value="수" <? if ( in_array( "수", $week_set ) == true ): ?>checked<? endif ?> /> 수
		<input type="checkbox" name="rr_repeat_week[]" value="목" <? if ( in_array( "목", $week_set ) == true ): ?>checked<? endif ?> /> 목
		<input type="checkbox" name="rr_repeat_week[]" value="금" <? if ( in_array( "금", $week_set ) == true ): ?>checked<? endif ?> /> 금
		<input type="checkbox" name="rr_repeat_week[]" value="토" <? if ( in_array( "토", $week_set ) == true ): ?>checked<? endif ?> /> 토
		<br />
		반복시작일자 : <input type="text" id="rr_repeat_begin" name="rr_repeat_begin" style="width:75px; text-align:center; background-color:#dddddd;" value="<?=$w["rr_repeat_begin"]?>" readonly />
		<script type="text/javascript"> var opts = { formElements:{"rr_repeat_begin":"Y-ds-m-ds-d"} }; datePickerController.createDatePicker(opts); </script> ~

		반복종료일자 : <input type="text" id="rr_repeat_end" name="rr_repeat_end" style="width:75px; text-align:center; background-color:#dddddd;" value="<?=$w["rr_repeat_end"]?>" readonly />
		<script type="text/javascript"> var opts = { formElements:{"rr_repeat_end":"Y-ds-m-ds-d"} }; datePickerController.createDatePicker(opts); </script>
	</th>
</tr>
</table>
<br />

<div align="center">
	<input type="submit" value="저장하기" style="width:150px; height:50px; background-color:blue; border:none; color:white;" />
	<input type="button" value="닫기" onclick="parent.call_write_close()" style="width:150px; height:50px; background-color:red; border:none; color:white;" />
</div>
</form>