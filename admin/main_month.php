<?

	if ( $_GET["month"] == '' ) $_GET["month"] = date( "Y-m" );

	$m = new modlist( "roomreserve" );
	$m->setopt( "listall", 1 );

	list( $cur_year, $cur_month ) = explode( "-", $_GET["month"] );
	$last_day = date( "t", mktime( 0, 0, 0, $cur_month, 1, $cur_year ) );

	while( $data = $m->get( "rr_subject, rr_time_begin, rr_date, rr_no", "( rr_date between '".$_GET["month"]."-01' and '".$_GET["month"]."-".$last_day."') and rr_deleted='0'", "order by rr_date, rr_time_begin" ) ) {
		$schedule[ $data["rr_date"] ][] = $data["rr_time_begin"]." ".$data["rr_subject"];
		$schedule_no[ $data["rr_date"] ][] = $data["rr_no"];
	}

	$prev_month = date( "Y-m", strtotime( "-1 month", strtotime( $_GET["month"]."-01" ) ) );
	$next_month = date( "Y-m", strtotime( "+1 month", strtotime( $_GET["month"]."-01" ) ) );

?>
	<SCRIPT>
		function call_write( cal_day, rr_no ) {
			var frm_div = document.getElementById( "write_frame_div" );
			frm_div.style.display = "block";

			var frm = document.getElementById( "write_frame" );
			frm.src = "?mode=main&submode=write&suppress_layout=true&date=" + cal_day + "&rr_no=" + rr_no;
		}

		function go_day( cal_day ) {
			location.href = "?mode=main&submode=day&date=" + cal_day;
		}
	</SCRIPT>
		<div id="write_frame_div" style="display:none; left:30px; top:240px; width:700px; height:600px; position:absolute; border:1px solid black; background-color:white; z-index:10;">
			<iframe id="write_frame" style="width:100%; height:100%;"></iframe>
		</div>
		<div align="center" class="h1">
			<input type="button" value="◀1M" style="font-size:12pt;" onclick="location.href='?mode=main&submode=month&month=<?=$prev_month?>'" />
			<?=($cur_year * 1)?>년 <?=($cur_month * 1)?>월 예약현황
			<input type="button" value="1M▶" style="font-size:12pt;" onclick="location.href='?mode=main&submode=month&month=<?=$next_month?>'" />
		</div>
		<div align="left" style="padding:10px;">
			<li style="font-size:15pt; color:green; font-family:'Malgun gothic', Dotum;">해당 일자에는 모든 회의실의 예약이 보여집니다. 자세한 정보를 보시려면 해당 일자를 클릭하여 화면으로 이동하여 주세요</li>
		</div>

		<?
			$last_day = date( "t", mktime( 0, 0, 0, $cur_month, 1, $cur_year ) );
			$start_week = date( "w", mktime( 0, 0, 0, $cur_month, 1, $cur_year ) );
			$total_week = ceil( ( $last_day + $start_week ) / 7);
			$last_week = date( "w", strtotime( $cur_year."-".$cur_month."-".$last_day ) );
			$day = 1;

			$height_perc = floor( 90 / $total_week );

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
		<table width="100%" height="700" cellpadding="0" cellspacing="1" bgcolor="#707070">
		<tr align="center" height="20">
			<td class="weekheader" width="14%"><font color="red">SUN</font></td>
			<td class="weekheader" width="14%">MON</td>
			<td class="weekheader" width="14%">TUE</td>
			<td class="weekheader" width="14%">WED</td>
			<td class="weekheader" width="14%">THU</td>
			<td class="weekheader" width="14%">FRI</td>
			<td class="weekheader" width="14%"><font color="blue">SAT</font></td>
		</tr>
		<? for( $i=1; $i<=$total_week; $i++ ): ?>
		<tr align="center" valign="top" height="<?=$height_perc?>%">
			<? for( $j=0; $j<7; $j++ ): ?>
			<td class="calday" style="cursor:pointer;" onclick="go_day('<?=sprintf( "%04d-%02d-%02d", $cur_year, $cur_month, $day )?>')">
			<? if ( !( ( $i == 1 && $j < $start_week ) || ( $i == $total_week && $j > $last_week ) ) ): ?>
			<div class="caldaytext"><?=$day?></div>
			<?
				$cal_day = sprintf( "%s-%02d", $_GET["month"], $day );
				$resv_list = $schedule[ $cal_day ];
				$resv_no = $schedule_no[ $cal_day ];
			?>
			<? for( $k=0; $k<sizeof($resv_list); $k++): ?>
				<div class="rounded_corner_small" style="background-color:<?=$colors[$k]?>; padding-left:5px; color:white; font-size:12px;">
					<?=$resv_list[$k]?>
				</div>
			<? endfor ?>
			<? $day += 1; ?>
			<? endif ?>
			</td>
			<? endfor ?>
		</tr>
		<? endfor ?>
		</table>
