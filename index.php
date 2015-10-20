<?

	require_once "init.php";

	//$room_name = "2층 2303 회의실 1(원형)";

	if ( $_GET["date"] == '' ) $_GET["date"] = date( "Y-m-d" );

	$m = new modlist( "roomreserve" );
	$m->setopt( "listall", 1 );

	$colors = array( "#0072bc", "#00a651", "#f7941d", "ff66cc", "9999ff", "33ccff", "ccff33" );

	$line = array();
	$cnt = 0;
	while( $data = $m->get( "*", "rr_room='".$room_name."' and rr_date='".$_GET["date"]."' and rr_deleted='0'" ) ) {
		$line[] = $data;

		$subject = $data["rr_subject"];

		$t_b = $data["rr_time_begin"];
		$t_e = $data["rr_time_end"];

		$du_b_h = substr( $t_b, 0, 2 );
		$du_b_m = substr( $t_b, 2, 2 );

		$du_e_h = substr( $t_e, 0, 2 );
		$du_e_m = substr( $t_e, 2, 2 );

		$resv_time = ( $du_b_h.":".$du_b_m."-".$du_e_h.":".$du_e_m );
		if ( $data["rr_begin_check"] != '' ) {
			$resv_time .= " <font size='1' color='pink'>CHECK</font>";
		}

		// calculate height
		// check if split is needed
		if ( $t_b < 1300 && $t_e > 1300 ) {
			$dur_h_1 = $du_e_h - 13;
			$dur_m_1 = $du_e_m - 00;

			$dur_h_2 = 13 - $du_b_h;
			$dur_m_2 = 00 - $du_b_m;

			$height_1 = ( $dur_h_1 * 68 ) + ( $dur_m_1 * 34 );
			$height_2 = ( $dur_h_2 * 68 ) + ( $dur_m_2 * 34 );

			$r[$t_b] = get_template( array(
				"color" => $colors[$cnt],
				"height" => $height_1,
				"resv_time" => $resv_time,
				"resv_subject" => $subject
			), $data );
			$r["1300"] = get_template( array(
				"color" => $colors[$cnt],
				"height" => $height_2,
				"resv_time" => "",
				"resv_subject" => $subject
			), $data );
		}
		else if ( $t_b < 1900 && $t_e > 1900 ) {
			$dur_h_1 = $du_e_h - 19;
			$dur_m_1 = $du_e_m - 00;

			$dur_h_2 = 19 - $du_b_h;
			$dur_m_2 = 00 - $du_b_m;

			$height_1 = ( $dur_h_1 * 68 ) + ( $dur_m_1 * 34 );
			$height_2 = ( $dur_h_2 * 68 ) + ( $dur_m_2 * 34 );

			$r[$t_b] = get_template( array(
				"color" => $colors[$cnt],
				"height" => $height_1,
				"resv_time" => "",
				"resv_subject" => $subject
			), $data );
			$r["1900"] = get_template( array(
				"color" => $colors[$cnt],
				"height" => $height_2,
				"resv_time" => $resv_time,
				"resv_subject" => $subject
			), $data );
		}
		else {
			$dur_h = $du_e_h - $du_b_h;
			$dur_m = ( $du_e_m - $du_b_m ) / 30;

			$height = ( $dur_h * 68 ) + ( $dur_m * 34 );

			$r[$t_b] = get_template( array(
				"color" => $colors[$cnt],
				"height" => $height,
				"resv_time" => $resv_time,
				//"resv_subject" => $data["rr_subject"]."(".$dur_h."+".$dur_m.")=".$height
				"resv_subject" => $subject
			), $data );
		}
		// place on the table
		// if split, place 2 splitted holder on the table

		$cnt++;
		if ( $cnt >= 7 ) $cnt = 1;
	}

	//$m->lastsql();

/*
	print "<xmp>";
	print_r( $line );
	print "</xmp>";

	$line_exp = array();
	$line_exp[] = array( "color" => "#0072bc", "height" => "136", "resv_time" => "01:00-03:00 PM", "resv_subject" => "의공 윤형진 교수" );
	$line_exp[] = array( "color" => "#00a651", "height" => "68", "resv_time" => "7:30-8:30 AM", "resv_subject" => "피부과 회의" );
	$line_exp[] = array( "color" => "#f7941d", "height" => "68", "resv_time" => "10:00-11:00 AM", "resv_subject" => "흉부외과 회의" );
*/

	function get_template( $data, $db_data ) {
		$color = $data["color"];
		$height = $data["height"];
		$resv_time_text = $data["resv_time"];
		$resv_subject_text = $data["resv_subject"];
		$template = "
	<div style=\"position:relative; width:100%; height:100%; cursor:pointer;\" onclick=\"go_popup('".$db_data["rr_no"]."');\">
		<div class=\"rounded_corner\" style=\"height:".$height."px; background-color:".$color.";\">
			<div class=\"inner_box\">
			<div id=\"div_time_".$db_data["rr_no"]."\" class=\"box_time\">".$resv_time_text."</div>
			<div id=\"div_subject_".$db_data["rr_no"]."\">".$resv_subject_text."</div>
			</div>
		</div>
	</div>
	";
		return $template
;	}
	

?>
<html>
<head>
	<meta charset="utf-8" />
	<style>
		body { background:url('image/background.jpg'); margin:0px; background-repeat:no-repeat; }
		td { margin:0px; padding:0px; }
		#notice { background:url('image/notice_back.jpg'); position:absolute; margin:0px; width:990px; height:107px; left:20px; top:80px; }
		#notice_content { position:absolute; left:20px; top:50px; width:494px; height:31px; font-family:"Malgun Gothic", dotum; font-size:20px; font-weight:bold; text-align:center; }
		#main { background:url('image/main_back.jpg'); position:absolute; top:202px; left:20px; width:990px; height:552px; }
		#main_room { text-align:right; position:absolute; left:648px; top:10px; width:320px; height:39px; overflow:hidden; font-family:"Malgun Gothic", dotum; font-weight:bold; font-size:26px; color:#428abc; text-align:right; text-shadow: 1px 1px #707070; }
		#menu_top { position:absolute; top:10px; left:253px; }
		#main_title { position:absolute; top:10px; left:20px; width:233px; height:39px; overflow:hidden; font-family:"Malgun Gothic", dotum; font-weight:bold; font-size:26px; color:black; text-align:left; text-shadow: 1px 1px #707070; }
		#time_table { position:absolute; top:90px; left:20px; }
		#btn_add { position: absolute; top:451px; left:751px; }
		#popup_div { background:url('image/resv_popup_back.gif'); position: absolute; top:90px; left:16px; width:954px; height:455px; }

		.inner_box { color:white; padding:10px; font-family:"Malgun gothic", dotum; font-weight:normal; font-size:14px; line-height: 160%; }
		.rounded_corner { width:214px; position:absolute; -moz-border-radius: 7px; -webkit-border-radius: 7px; -khtml-border-radius: 7px; border-radius: 7px; box-shadow: 4px 4px 4px #888888; margin-bottom:4px; }
		.box_time { color:yellow; text-shadow: 1px 1px #303030; }
	</style>
	<script type="text/javascript" src="jquery-1.11.0.min.js"></script>
</head>
<body>
	<input type="hidden" id="popup_rr_no" value="" />
<SCRIPT>
	function go_prev() { location.href = "index.php?date=<?=date( "Y-m-d", strtotime( "-1 day", strtotime( $_GET["date"] ) ) )?>"; }
	function go_next() { location.href = "index.php?date=<?=date( "Y-m-d", strtotime( "+1 day", strtotime( $_GET["date"] ) ) )?>"; }
	function go_today() { location.href = "index.php?date=<?=date( "Y-m-d" )?>"; }
	function go_reserve( begin ) { location.href = "write.php?date=<?=$_GET["date"]?>&begin=" + begin; }
	function go_month() { location.href = "month.php?month=<? list( $year, $month, $tmp ) = explode( "-", $_GET["date"] ); print $year."-".$month; ?>"; }
	function go_popup( rr_no ) {
		document.getElementById("popup_rr_no").value = rr_no;
		var t_obj = document.getElementById("div_time_" + rr_no);
		var s_obj = document.getElementById("div_subject_" + rr_no);
		document.getElementById("popup_time").innerText = t_obj.innerText;
		document.getElementById("popup_subject").innerText = s_obj.innerText;
		document.getElementById("popup_div").style.display = "block";
	}
	function go_popup_close() {
		document.getElementById("popup_div").style.display = "none";
	}
	function go_check_begin() {
		var rr_no_val = document.getElementById("popup_rr_no").value;
		$.ajax( {
			url: "prc.php",
			data: {
				mode: "prc_begin_check",
				rr_no : rr_no_val
			},
			dataType: "json",
			success: function( data ) {
				alert( data["result"] + ": " + data["message"] );
				console.log( data );

				if ( data["result"] == "성공" ) {
					location.reload();
				}
			},
		});
	}
	function go_delete() {
		var pop_tel_val = document.getElementById("pop_tel").value;
		var rr_no_val = document.getElementById("popup_rr_no").value;
		if ( pop_tel_val == '삭제시 전화번호 입력' || pop_tel_val == '' ) {
			alert('예약 시 입력하였던 전화번호를 위 입력란에 입력하세요.');
		}
		else {
			$.ajax( {
				url: "prc.php",
				data: {
					mode: "prc_delete_check",
					rr_no : rr_no_val,
					tel : pop_tel_val
				},
				dataType: "json",
				success: function( data ) {
					alert( data["result"] + ": " + data["message"] );
					console.log( data );

					if ( data["result"] == "삭제 성공" ) {
						location.reload();
						//document.location.href = "index.php?date=" + data["redirect_date"];
					}
					else {
						var obj = document.getElementById("pop_tel");
						obj.value = "삭제시 전화번호 입력";
						obj.style.color='#A0A0A0';
					}
				},
			});
		}
	}
</SCRIPT>
	<div id="notice">
		<div id="notice_content" style="width:95%;">
			<marquee width="100%">
				<?=get_notice_str()?>
			</marquee>
		</div>
	</div>
	<div id="main">
		<div id="main_title">
		<? if ( $_GET["date"] == date( "Y-m-d") ): ?>
			오늘 예약된 회의
		<? else: ?>
			<? list( $y, $m, $d ) = explode( "-", $_GET["date"] ); ?>
			<?=($y * 1)?>년 <?=($m * 1)?>월 <?=($d * 1)?>일
		<? endif ?>
		</div>
		<div id="menu_top">
			<table cellpadding="0" cellspacing="0" border="0"><tr>
			<td><a href="javascript:go_prev();"><img src="image/btn_left.jpg" border="0" /></a></td>
			<td width="5">&nbsp;</td>
			<td><a href="javascript:go_today()"><img src="image/btn_today.jpg" border="0" /></a></td>
			<td width="5">&nbsp;</td>
			<td><a href="javascript:go_next();"><img src="image/btn_right.jpg" border="0" /></a></td>
			<td width="5">&nbsp;</td>
			<td><a href="javascript:go_month();"><img src="image/btn_month.jpg" border="0" /></a></td>
			</tr></table>
		</div>
		<div id="main_room"><?=$room_name?></div>
		<div id="btn_add" style="z-index:50">
			<table cellpadding="0" cellspacing="0" border="0"><tr>
			<td>
				<a href="javascript:alert('화면에서 예약하시려는 시간을 터치해 주세요');"><img src="image/btn_add.jpg" border="0"/></a>
			</td>
			</tr></table>
		</div>
		<div id="popup_div" style="z-index:100; display:none;">
			<div id="popup_time" style="position:absolute; top:40px; left:45px; font-family:'Malgun gothic', Dotum; font-size:24pt; color:#175c55; font-weight:bold;">1:30 - 2:00PM</div>
			<div id="popup_subject" style="position:absolute; top:120px; left:62px; width:535px; height:122px; font-family:'Malgun gothic', Dotum; font-size:27pt; color:#323050; font-weight:bold;">-</div>
			<div id="popup_tel" style="position:absolute; top:110px; left:685px;"><input type="text" id="pop_tel" style="width:193px; height:45px; font-size:20px; color:#A0A0A0; border:none; text-align:center;" value="삭제시 전화번호 입력" onfocus="this.value=''; this.style.color='black';" /></div>
			<div id="popup_btn_begin" style="position:absolute; top:295px; left:216px; cursor:pointer;"><img src="image/pop_btn_begin.gif" border="0" onclick="go_check_begin();" /></div>
			<!--<div id="popup_btn_end" style="position:absolute; top:295px; left:357px; cursor:pointer;"><img src="image/pop_btn_end.gif" border="0" /></div>-->
			<div id="popup_btn_delete" style="position:absolute; top:184px; left:670px; cursor:pointer;"><img src="image/pop_btn_delete.gif" border="0" onclick="go_delete();" /></div>
			<div id="popup_btn_close" style="position:absolute; top:295px; left:670px; cursor:pointer;"><img src="image/pop_btn_close.gif" border="0" onclick="go_popup_close();" /></div>
		</div>
		<div id="time_table">
			<table cellpadding="0" cellspacing="0" border="0">
			<tr height="36">
				<td id="AM07" width="72" height="72" rowspan="2"><img src="image/AM07.gif" border="0" style="cursor:pointer;" onclick="go_reserve('07:00 AM');" /></td>
				<td width="10" rowspan="20"></td>
				<td id="AM0700" width="220" height="36"><?=$r["0700"]?></td>
				<td width="10" rowspan="20"></td>
				<td id="PM01" width="72" height="72" rowspan="2"><img src="image/PM01.gif" border="0" style="cursor:pointer;" onclick="go_reserve('01:00 PM');" /></td>
				<td width="10" rowspan="20"></td>
				<td id="PM0100" width="220" height="36"><?=$r["1300"]?></td>
				<td width="10" rowspan="20"></td>
				<td id="PM07" width="72" height="72" rowspan="2"><img src="image/PM07.gif" border="0" style="cursor:pointer;" onclick="go_reserve('07:00 PM');" /></td>
				<td width="10" rowspan="20"></td>
				<td id="PM0700" width="220" height="36"><?=$r["1900"]?></td>
			</tr>
			<tr height="36">
				<td id="AM0730" width="220" height="36"><?=$r["0730"]?></td>
				<td id="PM0130" width="220" height="36"><?=$r["1330"]?></td>
				<td id="PM0730" width="220" height="36"><?=$r["1930"]?></td>
			</tr>

			<tr height="36">
				<td id="AM08" width="72" height="72" rowspan="2"><img src="image/AM08.gif" border="0" style="cursor:pointer;" onclick="go_reserve('08:00 AM');" /></td>
				<td id="AM0800" width="220" height="36"><?=$r["0800"]?></td>
				<td id="PM02" width="72" height="72" rowspan="2"><img src="image/PM02.gif" border="0" style="cursor:pointer;" onclick="go_reserve('02:00 PM');" /></td>
				<td id="PM0200" width="220" height="36"><?=$r["1400"]?></td>
				<td id="PM08" width="72" height="72" rowspan="2"><img src="image/PM08.gif" border="0" style="cursor:pointer;" onclick="go_reserve('08:00 PM');" /></td>
				<td id="PM0800" width="220" height="36"><?=$r["2000"]?></td>
			</tr>
			<tr height="36">
				<td id="AM0830" width="220" height="36"><?=$r["0830"]?></td>
				<td id="PM0220" width="220" height="36"><?=$r["1430"]?></td>
				<td id="PM0830" width="220" height="36"><?=$r["2030"]?></td>
			</tr>

			<tr height="36">
				<td id="AM09" width="72" height="72" rowspan="2"><img src="image/AM09.gif" border="0" style="cursor:pointer;" onclick="go_reserve('09:00 AM');" /></td>
				<td id="AM0700" width="220" height="36"><?=$r["0900"]?></td>
				<td id="PM01" width="72" height="72" rowspan="2"><img src="image/PM03.gif" border="0" style="cursor:pointer;" onclick="go_reserve('03:00 PM');" /></td>
				<td id="PM0100" width="220" height="36"><?=$r["1500"]?></td>
				<td id="PM07" width="72" height="72" rowspan="2"><img src="image/PM09.gif" border="0" style="cursor:pointer;" onclick="go_reserve('09:00 PM');" /></td>
				<td id="PM0700" width="220" height="36"><?=$r["2100"]?></td>
			</tr>
			<tr height="36">
				<td id="AM0730" width="220" height="36"><?=$r["0930"]?></td>
				<td id="PM0130" width="220" height="36"><?=$r["1530"]?></td>
				<td id="PM0730" width="220" height="36"><?=$r["2130"]?></td>
			</tr>

			<tr height="36">
				<td id="AM10" width="72" height="72" rowspan="2"><img src="image/AM10.gif" border="0" style="cursor:pointer;" onclick="go_reserve('10:00 AM');" /></td>
				<td id="AM0700" width="220" height="36"><?=$r["1000"]?></td>
				<td id="PM01" width="72" height="72" rowspan="2"><img src="image/PM04.gif" border="0" style="cursor:pointer;" onclick="go_reserve('04:00 PM');" /></td>
				<td id="PM0100" width="220" height="36"><?=$r["1600"]?></td>
				<td id="PM07" width="72" height="72" rowspan="2"><img src="image/PM10.gif" border="0" style="cursor:pointer;" onclick="go_reserve('10:00 PM');" /></td>
				<td id="PM0700" width="220" height="36"><?=$r["2200"]?></td>
			</tr>
			<tr height="36">
				<td id="AM1030" width="220" height="36"><?=$r["1030"]?></td>
				<td id="PM0130" width="220" height="36"><?=$r["1630"]?></td>
				<td id="PM0730" width="220" height="36"><?=$r["2230"]?></td>
			</tr>

			<tr height="36">
				<td id="AM11" width="72" height="72" rowspan="2"><img src="image/AM11.gif" border="0" style="cursor:pointer;" onclick="go_reserve('11:00 AM');" /></td>
				<td id="AM0700" width="220" height="36"><?=$r["1100"]?></td>
				<td id="PM01" width="72" height="72" rowspan="2"><img src="image/PM05.gif" border="0" style="cursor:pointer;" onclick="go_reserve('05:00 PM');" /></td>
				<td id="PM0100" width="220" height="36"><?=$r["1700"]?></td>
				<td id="PM07" width="72" height="72" rowspan="2"><img src="image/PM11.gif" border="0" style="cursor:pointer;" onclick="go_reserve('11:00 PM');" /></td>
				<td id="PM0700" width="220" height="36"><?=$r["2300"]?></td>
			</tr>
			<tr height="36">
				<td id="AM1130" width="220" height="36"><?=$r["1130"]?></td>
				<td id="PM0130" width="220" height="36"><?=$r["1730"]?></td>
				<td id="PM0730" width="220" height="36"><?=$r["2330"]?></td>
			</tr>

			<tr height="36">
				<td id="PM12" width="72" height="72" rowspan="2"><img src="image/PM12.gif" border="0" style="cursor:pointer;" onclick="go_reserve('12:00 PM');" /></td>
				<td id="PM1200" width="220" height="36"><?=$r["1200"]?></td>
				<td id="PM01" width="72" height="72" rowspan="2"><img src="image/PM06.gif" border="0" style="cursor:pointer;" onclick="go_reserve('06:00 PM');" /></td>
				<td id="PM0100" width="220" height="36"><?=$r["1800"]?></td>
				<td width="72" height="72" rowspan="2"></td>
				<td width="220" height="36"></td>
			</tr>
			<tr height="36">
				<td id="PM1220" width="220" height="36"><?=$r["1230"]?></td>
				<td id="PM0130" width="220" height="36"><?=$r["1830"]?></td>
				<td width="220" height="36"></td>
			</tr>

			</table>
		</div>
	</div>
</body>
</html>