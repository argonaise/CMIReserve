<html>
<head>
	<meta charset="utf-8" />
	<style>
		body { background:url('image/background.jpg'); margin:0px; background-repeat:no-repeat; }
		td { margin:0px; padding:0px; }
		#main { background:url('image/main_back_large.jpg'); position:absolute; top:81px; left:20px; width:990px; height:674px; }
		#main_title { position:absolute; top:10px; left:20px; width:300px; height:39px; overflow:hidden; font-family:"Malgun Gothic", dotum; font-weight:bold; font-size:26px; color:black; text-align:left; text-shadow: 1px 1px #707070; }

		.inner_box { color:white; padding:10px; font-family:"Malgun gothic", dotum; font-weight:normal; font-size:14px; line-height: 160%; }
		.rounded_corner { width:214px; position:absolute; -moz-border-radius: 7px; -webkit-border-radius: 7px; -khtml-border-radius: 7px; border-radius: 7px; box-shadow: 4px 4px 4px #888888; margin-bottom:4px; }
		.box_time { color:yellow; text-shadow: 1px 1px #303030; }

		#write_name { position: absolute; top:75px; left:52px; }
		#write_phone { position: absolute; top:75px; left:492px; }
		#write_subject { position: absolute; top:172px; left:52px; }
		#write_room { position: absolute; top:269px; left:52px; }
		#write_date { position: absolute; top:269px; left:492px; }
		#write_begin { position: absolute; top:366px; left:52px; }
		#write_end { position: absolute; top:366px; left:492px; }

		#write_repeat { position: absolute; top:463px; left:52px; }

		#write_ok { position: absolute; top:564px; left:483px; width:211px; height:76px; }
		#write_back { position: absolute; top:564px; left:727px; width:211px; height:76px; }

		.form_back_s { width:278px; height:61px; background:url('image/write_back_s.gif'); }
		.form_back_l { width:727px; height:61px; background:url('image/write_back_l.gif'); }

		.textbox_s { height:40px; width:210px; margin-left:30px; border:1px solid #B0B0B0; font-family:"Malgun gothic", dotum; font-size:24px; }
		.textbox_l { height:40px; width:650px; margin-left:30px; border:1px solid #B0B0B0; font-family:"Malgun gothic", dotum; font-size:24px; }
		.select { height:40px; margin-left:30px; border:1px solid #B0B0B0; font-family:"Malgun gothic", dotum; font-size:24px; }

		.btn { font-family:"Malgun gothic", dotum; font-size:20px; }

		.week { font-family:"Malgun gothic", dotum; font-weight:bold; font-size:25px; }
		.big_checkbox { width:20px; height:20px; }

		.timebox
		{
			background: #FFFFFF url(image/time_icon.png) no-repeat 170px;
			padding-right: 18px;
			border:1px solid #ccc;
		}

		.datebox
		{
			background: #FFFFFF url(image/cal_icon.png) no-repeat 170px;
			padding-right: 18px;
			border:1px solid #ccc;
		}

		#repeat_div { background:url('image/repeat_back_main.gif'); position:absolute; top:253px; left:20px; width:944px; height:210px; z-index:300; }

		#repeat_week { position: absolute; top:16px; left:32px; }
		#repeat_begin { position: absolute; top:113px; left:32px; }
		#repeat_end { position: absolute; top:113px; left:481px; }

		#repeat_opt_text { position:relative; left:200px; font-family:"Malgun gothic", dotum; font-size:24px; }

		.repeat_back_s { width:278px; height:61px; background:url('image/repeat_back_s.gif'); }
		.repeat_back_l { width:727px; height:61px; background:url('image/repeat_back_l.gif'); }

	</style>
	<link rel="stylesheet" type="text/css" href="cs-select.css" />
	<link rel="stylesheet" type="text/css" href="cs-skin-overlay.css" />
	<link rel="stylesheet" type="text/css" href="DateTimePicker.css" />
	<script type="text/javascript" src="jquery-1.11.0.min.js"></script>
	<script type="text/javascript" src="DateTimePicker.js"></script>
</head>
<body>
	<div id="main">
		<form id="InputForm" method="post" action="prc.php?mode=prc_write">
		<div id="main_title">회의실 사용 예약하기</div>

		<div id="write_name"><table cellpadding="0" cellspacing="0" border="0" width="431" height="61">
		<tr>
			<td background="image/write_name.gif" width="153"></td>
			<td class="form_back_s">
				<input type="text" name="rr_name" class="textbox_s" size="10" value="" />
			</td>
		</tr>
		</table></div>

		<div id="write_phone"><table cellpadding="0" cellspacing="0" border="0" width="431" height="61">
		<tr>
			<td background="image/write_phone.gif" width="153"></td>
			<td class="form_back_s">
				<input type="text" name="rr_phone" class="textbox_s" size="10" value="" />
			</td>
		</tr>
		</table></div>
		
		<div id="write_subject"><table cellpadding="0" cellspacing="0" border="0" width="880" height="61">
		<tr>
			<td background="image/write_subject.gif" width="153"></td>
			<td class="form_back_l">
				<input type="text" name="rr_subject" class="textbox_l" value="" />
			</td>
		</tr>
		</table></div>

		<div id="write_room"><table cellpadding="0" cellspacing="0" border="0" width="431" height="61">
		<tr>
			<td background="image/write_room.gif" width="153"></td>
			<td class="form_back_s">
				<select class="cs-select cs-skin-overlay cs-select-room" name="rr_room">
					<option value="" disabled selected>회의실 선택</option>
					<optgroup label="2층 회의실">
						<option value="2층 2303 회의실 1(원형)">2층 2303 회의실 1(원형)</option>
						<option value="2층 2302 회의실 2">2층 2302 회의실 2</option>
						<option value="2층 2301 회의실 3">2층 2301 회의실 3</option>
					</optgroup>
					<optgroup label="3층 회의실">
						<option value="3층 3301 회의실 1(원형)">3층 3301 회의실 1(원형)</option>
						<option value="3층 3220 소회의실 1">3층 3220 소회의실 1</option>
						<option value="3층 3221 소회의실 2">3층 3221 소회의실 2</option>
						<option value="3층 3222 소회의실 3">3층 3222 소회의실 3</option>
					</optgroup>
					<optgroup label="4층 회의실">
						<option value="4층 4301 회의실 1(원형)">4층 4301 회의실 1(원형)</option>
						<option value="4층 4220 소회의실 1">4층 4220 소회의실 1</option>
						<option value="4층 4221 소회의실 2">4층 4221 소회의실 2</option>
						<option value="4층 4222 소회의실 3">4층 4222 소회의실 3</option>
					</optgroup>
				</select>
			</td>
		</tr>
		</table></div>
		
		<div id="write_date"><table cellpadding="0" cellspacing="0" border="0" width="431" height="61">
		<tr>
			<td background="image/write_date.gif" width="153"></td>
			<td class="form_back_s">
				<input type="text" id="rr_date" name="rr_date" class="textbox_s datebox" data-field="date" data-format="yyyy-MM-dd" value="<?=$_GET["date"]?>" readonly>
			</td>
		</tr>
		</table></div>

		<div id="write_begin"><table cellpadding="0" cellspacing="0" border="0" width="431" height="61">
		<tr>
			<td background="image/write_begin.gif" width="153"></td>
			<td class="form_back_s">
				<input type="text" id="time_begin" name="rr_time_begin" rem-data-startend="start" rem-data-startendelem="#time_end" class="textbox_s timebox" data-field="time" data-format="hh:mm AA" readonly value="<?=$_GET["begin"]?>" />
			</td>
		</tr>
		</table></div>

		<div id="write_end"><table cellpadding="0" cellspacing="0" border="0" width="431" height="61">
		<tr>
			<td background="image/write_end.gif" width="153"></td>
			<td class="form_back_s">
				<input type="text" id="time_end" name="rr_time_end" rem-data-startend="end" rem-data-startendelem="#time_begin" class="textbox_s timebox" data-field="time" data-format="hh:mm AA" readonly />
			</td>
		</tr>
		</table></div>

		<div id="write_repeat"><table cellpadding="0" cellspacing="0" border="0" width="880" height="61">
		<tr>
			<td background="image/write_repeat.gif" width="153"></td>
			<td class="form_back_l">
				<table><tr>
				<td>
					<input type="button" value="반복설정" class="rounded_corner btn" style="width:150px; height:40px; top:10px; left:180px;" onclick="toggle_repeat()" />
				</td>
				<td>
					<div id="repeat_opt_text"></div>
				</td>
				</tr></table>
			</td>
		</tr>
		</table></div>

		<div id="repeat_div" style="display:none; overflow:hidden; height:0px;">

			<div id="repeat_week"><table cellpadding="0" cellspacing="0" border="0" width="880" height="61">
			<tr>
				<td background="image/repeat_week.gif" width="153"></td>
				<td class="repeat_back_l week">
					&nbsp;&nbsp;&nbsp;
					<label for="week_1">월 <input id="week_1" onclick="updateRepeatText();" type="checkbox" name="rr_repeat_week[]" value="월" class="big_checkbox" /></label> &nbsp;
					<label for="week_2">화 <input id="week_2" onclick="updateRepeatText();" type="checkbox" name="rr_repeat_week[]" value="화" class="big_checkbox" /></label> &nbsp;
					<label for="week_3">수 <input id="week_3" onclick="updateRepeatText();" type="checkbox" name="rr_repeat_week[]" value="수" class="big_checkbox" /></label> &nbsp;
					<label for="week_4">목 <input id="week_4" onclick="updateRepeatText();" type="checkbox" name="rr_repeat_week[]" value="목" class="big_checkbox" /></label> &nbsp;
					<label for="week_5">금 <input id="week_5" onclick="updateRepeatText();" type="checkbox" name="rr_repeat_week[]" value="금" class="big_checkbox" /></label> &nbsp;
					<label for="week_6">토 <input id="week_6" onclick="updateRepeatText();" type="checkbox" name="rr_repeat_week[]" value="토" class="big_checkbox" /></label> &nbsp;
				</td>
			</tr>
			</table></div>

			<div id="repeat_begin"><table cellpadding="0" cellspacing="0" border="0" width="431" height="61">
			<tr>
				<td background="image/repeat_begin.gif" width="153"></td>
				<td class="repeat_back_s">
					<input type="text" id="repeat_date_begin" name="rr_repeat_begin" data-startend="start" data-startendelem="#repeat_date_end" class="textbox_s datebox" data-field="date" data-format="yyyy-MM-dd" value="<?=date("Y-m-d")?>" readonly />
				</td>
			</tr>
			</table></div>

			<div id="repeat_end"><table cellpadding="0" cellspacing="0" border="0" width="431" height="61">
			<tr>
				<td background="image/repeat_end.gif" width="153"></td>
				<td class="repeat_back_s">
					<input type="text" id="repeat_date_end" name="rr_repeat_end" data-startend="end" data-startendelem="#repeat_date_begin" class="textbox_s datebox" data-field="date" data-format="yyyy-MM-dd" value="<?=date("Y-m-d", time() + (86400*30))?>" readonly />
				</td>
			</tr>
			</table></div>

		</div>

		<div id="write_ok"><img src="image/write_btn_ok.gif" border="0" style="cursor:pointer;" onclick="fnCheckForm();"></div>
		<div id="write_back"><img src="image/write_btn_back.gif" border="0" style="cursor:pointer;" onclick="history.back();"></div>
		</form>

		<div id="dtBox"></div>

		<script src="selector/js/classie.js"></script>
		<script src="selector/js/selectFx.js"></script>
		<script>
			function fnCheckForm() {
				var f = document.forms["InputForm"];

				if ( f.rr_name.value == '' ) {
					alert( '회의 신청자 이름을 입력해 주세요.' );
					return false;
				}

				if ( f.rr_phone.value == '' ) {
					alert( '회의 신청자 연락처를 입력해 주세요.' );
					return false;
				}

				if ( f.rr_subject.value == '' ) {
					alert( '회의 주제를 입력해 주세요.' );
					return false;
				}

				if ( f.rr_room.value == '' ) {
					alert( '회의실을 선택해 주세요.' );
					return false;
				}

				if ( f.rr_date.value == '' ) {
					alert( '회의 날짜를 선택해 주세요.' );
					return false;
				}

				if ( f.rr_time_begin.value == '' ) {
					alert( '회의 시작시간을 선택해 주세요.' );
					return false;
				}

				if ( f.rr_time_end.value == '' ) {
					alert( '회의 종료시간을 선택해 주세요.' );
					return false;
				}

				if (
					document.getElementById("week_1").checked == true ||
					document.getElementById("week_2").checked == true ||
					document.getElementById("week_3").checked == true ||
					document.getElementById("week_4").checked == true ||
					document.getElementById("week_5").checked == true ||
					document.getElementById("week_6").checked == true
				 ) {
					if ( f.repeat_date_begin.value == '' || f.repeat_date_end.value == '' ) {
						alert( '반복 시작날짜 및 종료날짜를 입력해 주세요.' );
						return false;
					}
				}

				duplicate_check_then_submit();
			}

			function duplicate_check_then_submit() {
				var f = document.forms["InputForm"];
				$.ajax( {
					url: "prc.php",
					data: {
						mode: "prc_duplicate_check",
						date: f.rr_date.value,
						begin: f.rr_time_begin.value,
						end: f.rr_time_end.value,
						room: f.rr_room.value,
						week_1 : document.getElementById("week_1").checked,
						week_2 : document.getElementById("week_2").checked,
						week_3 : document.getElementById("week_3").checked,
						week_4 : document.getElementById("week_4").checked,
						week_5 : document.getElementById("week_5").checked,
						week_6 : document.getElementById("week_6").checked,
						repeat_begin: f.rr_repeat_begin.value,
						repeat_end: f.rr_repeat_end.value
					},
					dataType: "json",
					success: function( data ) {
						alert( data["result"] + ": " + data["message"] );
						console.log( data );

						if ( data["result"] == "등록 성공" ) {
							f.submit();
							//document.location.href = "index.php?date=" + data["redirect_date"];
						}
					},
				});
			}

			function toggle_repeat() {
				//alert($("#repeat_div").css("display"));
				var div = $("#repeat_div");
				if ( div.css("display") == "none" ) {
					div.css("display", "block");
					div.animate({height: 210}, "slow");
				}
				else {
					div.animate({height: 0}, "slow", hide_repeat_div);
				}
			}

			function hide_repeat_div() {
				var div = $("#repeat_div");
				div.css("display", "none");
			}

			function updateRepeatText() {
				var div = $("#repeat_opt_text");

				var weeks = $("[name='rr_repeat_week[]']");
				var s = [];
				weeks.each(function() {
					if ( $(this).prop("checked") == true ) {
						s.push( $(this).val() );
					}
				});

				var d_obj = document.getElementById("rr_date");

				if ( s.length > 0 ) {
					d_obj.disabled = true;
					d_obj.style.backgroundColor = "#e1e1e1";
					div.prop("innerText", s.join(", ") + " " + $("#repeat_date_begin").val() + "~" + $("#repeat_date_end").val());
				}
				else {
					d_obj.disabled = false;
					d_obj.style.backgroundColor = "white";
					div.prop("innerText", "");
				}

			}

			(function() {
				[].slice.call( document.querySelectorAll( 'select.cs-select' ) ).forEach( function(el) {	
					new SelectFx(el, {
						stickyPlaceholder: false
					});
				} );
			})();

			$(document).ready(function()
			{
				$("#dtBox").DateTimePicker({
					minuteInterval: 30,
					dateFormat: "yyyy-MM-dd",
					timeFormat: "hh:mm AA",
					dateTimeFormat: "yyyy-MM-dd hh:mm:ss AA",
					shortDayNames: ["일", "월", "화", "수", "목", "금", "토"],
					fullDayNames: ["일요일", "월요일", "화요일", "수요일", "목요일", "금요일", "토요일"],
					shortMonthNames: ["01", "02", "03", "04", "05", "06", "07", "08", "09", "10", "11", "12"],
					fullMonthNames: ["01", "02", "03", "04", "05", "06", "07", "08", "09", "10", "11", "12"],
					//fullMonthNames: ["Januar", "Februar", "M?z", "April", "Mai", "Juni", "Juli", "August", "September", "Oktober", "November", "Dezember"],
		
					titleContentDate: "날짜 설정",
					titleContentTime: "시간 설정",
					titleContentDateTime: "날짜 및 시간 설정",
				
					setButtonContent: "설정",
					clearButtonContent: "지우기",

					beforeShow: function(oInputElement) {
						//alert(oInputElement.id);
					},

					beforeHide: function(oInputElement) {
						//alert(oInputElement.id);
						if ( oInputElement.id == "repeat_date_begin" || oInputElement.id == "repeat_date_end" ) {
							updateRepeatText();
						}
					},

					afterShow: function(oInputElement) {
						//alert(oInputElement.id);
					},

					afterHide: function(oInputElement) {
						//alert(oInputElement.id);
					},

					buttonClicked: function(sButtonType, oInputElement) {
						//alert(sButtonType);
						//alert(oInputElement.id);
					}
				});

				
			});
		</script>
		

	</div>
</body>
</html>