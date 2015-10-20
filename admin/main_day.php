<?

	list( $cur_year, $cur_month, $cur_day ) = explode( "-", $_GET["date"] );

	$line_colors = array( "line_toggle_1", "line_toggle_2" );
	$toggle = 0;

	$m = new modlist( "roomreserve" );
	$m->setopt( "listall", 1 );

	while( $data = $m->get( "*", "( rr_date='".$_GET["date"]."' ) and rr_deleted='0'", "order by rr_time_begin" ) ) {
		$schedule_data[ $data["rr_no"] ] = $data;

		if ( $data["rr_time_begin"] < 1200 ) {
			$schedule_am[ $data["rr_room"] ][] = $data[ "rr_no" ];
		}
		else {
			$schedule_pm[ $data["rr_room"] ][] = $data[ "rr_no" ];
		}
	}

?>
	<style>
		.inner_box { color:white; padding:10px; font-family:"Malgun gothic", dotum; font-weight:normal; font-size:14px; line-height: 160%; }
		.rounded_corner { width:214px; -moz-border-radius: 7px; -webkit-border-radius: 7px; -khtml-border-radius: 7px; border-radius: 7px; box-shadow: 4px 4px 4px #888888; margin-bottom:4px; }
		.rounded_corner_small { width:90%; -moz-border-radius: 3px; -webkit-border-radius: 3px; -khtml-border-radius: 3px; border-radius: 3px; box-shadow: 2px 2px 2px #888888; margin-bottom:5px; margin-left:3px; margin-top:2px; overflow: hidden; }
		.box_time { color:yellow; text-shadow: 1px 1px #303030; }
		.weekheader { background-color:#a0a0a0; font-weight:bold; font-size:13px; }
		.calday { background-color: white; text-align:left; }
		.caldaytext { position:relative; top:3px; left:3px; font-size:15px; }
	</style>

	<SCRIPT>
		function getOffset( el ) {
		    var _x = 0;
		    var _y = 0;
		    while( el && !isNaN( el.offsetLeft ) && !isNaN( el.offsetTop ) ) {
		        _x += el.offsetLeft - el.scrollLeft;
		        //_y += el.offsetTop - el.scrollTop;
				_y += el.offsetTop;
		        el = el.offsetParent;
		    }
		    return { top: _y, left: _x };
		}

		function call_delete( rr_no ) {
			if ( confirm( '정말 삭제하시겠습니까?' ) == true ) {
				location.href = "prc.php?mode=prc_delete&rr_no=" + rr_no + "&get=<?=urlencode( $_SERVER["QUERY_STRING"] )?>";
			}
		}

		function call_write( event_obj, cal_day, rr_no ) {
			var pos = getOffset( event_obj );

			var div = document.getElementById( "autocomplete" );
			div.style.top = pos.top + 30;
			div.style.left = pos.left;
			div.style.display = "block";

			var iframe = document.getElementById( "autocomplete_iframe" );
			iframe.src = "?mode=main&submode=write&suppress_layout=true&date=" + cal_day + "&rr_no=" + rr_no;
		}

		function call_write_right( event_obj, cal_day, rr_no ) {
			var pos = getOffset( event_obj );

			var div = document.getElementById( "autocomplete" );
			div.style.top = pos.top;
			div.style.left = pos.left - 708;
			div.style.display = "block";

			var iframe = document.getElementById( "autocomplete_iframe" );
			iframe.src = "?mode=main&submode=write&suppress_layout=true&date=" + cal_day + "&rr_no=" + rr_no;
		}

		function call_write_close() {
			var div = document.getElementById( "autocomplete" );
			div.style.display = "none";
		}
	</SCRIPT>

	<div id="autocomplete" style="position:absolute; top:0px; left:0px; width:750px; height:700px; background-color:white; display:none;">
	<iframe id="autocomplete_iframe" style="width:100%; height:100%" scrolling="no" style="overflow-y: hidden;"></iframe>
	</div>

	<div align="center" class="h1">
		<input type="button" value="◀1M" style="font-size:12pt;" onclick="location.href='?mode=main&submode=day&date=<?=date( "Y-m-d", strtotime( "-1 month", strtotime( $_GET["date"] ) ) )?>'" />
		<input type="button" value="◀" style="font-size:12pt;" onclick="location.href='?mode=main&submode=day&date=<?=date( "Y-m-d", strtotime( "-1 day", strtotime( $_GET["date"] ) ) )?>'" />
		<?=($cur_year * 1)?>년 <?=($cur_month * 1)?>월 <?=($cur_day * 1)?>일 예약현황
		<input type="button" value="MONTH" style="font-size:12pt; font-weight:bold;" onclick="location.href='?mode=main&submode=month&month=<?=substr( $_GET["date"], 0, 7 )?>'" />
		<input type="button" value="▶" style="font-size:12pt;" onclick="location.href='?mode=main&submode=day&date=<?=date( "Y-m-d", strtotime( "+1 day", strtotime( $_GET["date"] ) ) )?>'" />
		<input type="button" value="1M▶" style="font-size:12pt;" onclick="location.href='?mode=main&submode=day&date=<?=date( "Y-m-d", strtotime( "+1 month", strtotime( $_GET["date"] ) ) )?>'" />
	</div>
	<div align="left" style="padding:10px;">
	<ul style="font-size:12pt; color:red; font-family:'Malgun gothic', Dotum;">
		<li>직접 예약을 등록하고 싶으실 때 회의실명 밑의 예약 버튼을 눌러 주세요</li>
		<li style="font-size:12pt; color:red; font-family:'Malgun gothic', Dotum;">예약된 항목의 내용을 수정하거나 삭제하고 싶으실 때 해당 항목의 수정 버튼을 클릭해 주세요</li>
	</ol>
	</div>

	<table width="100%" cellpadding="0" cellspacing="1" bgcolor="#707070">
	<tr align="center" height="20">
		<th style="color:white;" width="20%">회의실</th>
		<th style="color:white;" width="40%">오전 예약</th>
		<th style="color:white;" width="40%">오후 예약</th>
	</tr>
	<? foreach ($confroom as $roomname): ?>
	<? $toggle = !$toggle; ?>
	<tr align="center" class="<?=$line_colors[$toggle]?>" height="100">
		<td width="20%" style="font-size:14pt; font-family:'Malgun gothic', Dotum; font-weight:bold;">
			<?=$roomname?><br />
			<input type="button" value="예약 등록" onclick="call_write(this,'<?=$_GET["date"]?>','')" />
		</th>
		<td width="40%" valign="top">
		<? $cnt = 0; ?>
		<? foreach( $schedule_am[ $roomname ] as $rr_no ): ?>
		<div class="rounded_corner_small inner_box" style="background-color:<?=$colors[$cnt]?>; padding-left:5px; color:white; font-size:11pt; text-align:left; line-height:120%;">
			<? $data = $schedule_data[$rr_no]; ?>
			<?=time_human( $data["rr_time_begin"] )."-".time_human( $data["rr_time_end"] )?>
			<?=$data["rr_name"]?> (<?=$data["rr_phone"]?>)<br />
			<div style="padding-left:10px; font-weight:bold; font-size:16pt; line-height:200%;">
				<input type="button" value="수정" onclick="call_write(this,'<?=$_GET["date"]?>',<?=$rr_no?>);" />
				<input type="button" value="삭제" onclick="call_delete('<?=$rr_no?>');" />
				<?=$data["rr_subject"]?>
			</div>
			&nbsp;&nbsp;&nbsp;사용 체크시간 : <?=($data["rr_begin_check"]!='')?$data["rr_begin_check"]:"-"?>
		</div>
		<? $cnt++; ?>
		<? endforeach ?>
		</th>
		<td width="40%" valign="top">
		<? $cnt = 0; ?>
		<? foreach( $schedule_pm[ $roomname ] as $rr_no ): ?>
		<div class="rounded_corner_small inner_box" style="background-color:<?=$colors[$cnt]?>; padding-left:5px; color:white; font-size:11pt; text-align:left; line-height:120%;">
			<? $data = $schedule_data[$rr_no]; ?>
			&nbsp;&nbsp;<b style="color:yellow;"><?=time_human( $data["rr_time_begin"] )."-".time_human( $data["rr_time_end"] )?></b>
			<?=$data["rr_name"]?> (<?=$data["rr_phone"]?>)<br />
			<div style="padding-left:10px; padding-bottom:7px; font-weight:bold; font-size:16pt; line-height:200%;">
				<input type="button" value="수정" onclick="call_write_right(this,'<?=$_GET["date"]?>',<?=$rr_no?>);" />
				<input type="button" value="삭제" onclick="call_delete('<?=$rr_no?>');" />
				<?=$data["rr_subject"]?>
			</div>
			&nbsp;&nbsp;&nbsp;사용 체크시간 : <?=($data["rr_begin_check"]!='')?$data["rr_begin_check"]:"-"?>
		</div>
		<? $cnt++; ?>
		<? endforeach ?>
		</th>
	</tr>
	<? endforeach ?>
