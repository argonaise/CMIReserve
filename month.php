<?

	require_once "init.php";

	//$room_name = "2층 2303 회의실 1(원형)";

	if ( $_GET["month"] == '' ) $_GET["month"] = date( "Y-m" );

	$m = new modlist( "roomreserve" );
	$m->setopt( "listall", 1 );

	list( $cur_year, $cur_month ) = explode( "-", $_GET["month"] );
	$last_day = date( "t", mktime( 0, 0, 0, $cur_month, 1, $cur_year ) );

	while( $data = $m->get( "rr_subject, rr_time_begin, rr_date", "rr_room='".$room_name."' and ( rr_date between '".$_GET["month"]."-01' and '".$_GET["month"]."-".$last_day."') and rr_deleted='0'", "order by rr_date, rr_time_begin" ) ) {
		$schedule[ $data["rr_date"] ][] = $data["rr_time_begin"]." ".$data["rr_subject"];
	}

	//$m->lastsql();
	//print_r($schedule);

	$colors = array( "#0072bc", "#00a651", "#f7941d", "ff66cc", "9999ff", "33ccff", "ccff33" );

	$line = array();
	$cnt = 0;

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
		#menu_top { position:absolute; top:10px; left:313px; }
		#main_title { position:absolute; top:10px; left:20px; width:270px; height:39px; overflow:hidden; font-family:"Malgun Gothic", dotum; font-weight:bold; font-size:26px; color:black; text-align:left; text-shadow: 1px 1px #707070; }
		#time_table { position:absolute; top:60px; left:20px; width:95%; }
		#btn_add { position: absolute; top:451px; left:751px; }

		.inner_box { color:white; padding:10px; font-family:"Malgun gothic", dotum; font-weight:normal; font-size:14px; line-height: 160%; }
		.rounded_corner { width:214px; -moz-border-radius: 7px; -webkit-border-radius: 7px; -khtml-border-radius: 7px; border-radius: 7px; box-shadow: 4px 4px 4px #888888; margin-bottom:4px; }
		.rounded_corner_small { width:120px; -moz-border-radius: 3px; -webkit-border-radius: 3px; -khtml-border-radius: 3px; border-radius: 3px; box-shadow: 2px 2px 2px #888888; margin-bottom:5px; margin-left:3px; margin-top:2px; overflow: hidden; }
		.box_time { color:yellow; text-shadow: 1px 1px #303030; }
		.weekheader { background-color:#a0a0a0; font-weight:bold; font-size:13px; }
		.calday { background-color: white; text-align:left; }
		.caldaytext { position:relative; top:3px; left:3px; font-size:10px; }
	</style>
</head>
<body>
<SCRIPT>
	function go_prev() { location.href = "month.php?month=<?=date( "Y-m", strtotime( "-1 month", mktime( 0, 0, 0, $cur_month, 1, $cur_year ) ) )?>"; }
	function go_next() { location.href = "month.php?month=<?=date( "Y-m", strtotime( "+1 month", mktime( 0, 0, 0, $cur_month, 1, $cur_year ) ) )?>"; }
	function go_today() { location.href = "month.php?date=<?=date( "Y-m" )?>"; }
	function go_day( date_str ) { location.href = "index.php?date=" + date_str; }
</SCRIPT>
	<div id="notice">
		<div id="notice_content" style="width:95%;">
			<marquee width="100%">
				<?=get_notice_str()?><br />
			</marquee>
		</div>
	</div>
	<div id="main">
		<div id="main_title">
			<?=($cur_year * 1)?>년 <?=($cur_month * 1)?>월 예약현황
		</div>
		<div id="menu_top">	
		<table cellpadding="0" cellspacing="0" border="0"><tr>
			<td><a href="javascript:go_prev();"><img src="image/btn_left.jpg" border="0" /></a></td>
			<td width="5">&nbsp;</td>
			<td><a href="javascript:go_today()"><img src="image/btn_today.jpg" border="0" /></a></td>
			<td width="5">&nbsp;</td>
			<td><a href="javascript:go_next();"><img src="image/btn_right.jpg" border="0" /></a></td>
			</tr></table>
		</div>
		<div id="main_room"><?=$room_name?></div>
		<div id="time_table">
		<?
			$last_day = date( "t", mktime( 0, 0, 0, $cur_month, 1, $cur_year ) );
			$start_week = date( "w", mktime( 0, 0, 0, $cur_month, 1, $cur_year ) );
			$total_week = ceil( ( $last_day + $start_week ) / 7);
			$last_week = date( "w", strtotime( $cur_year."-".$cur_month."-".$last_day ) );
			$day = 1;

			$height_perc = floor( 90 / $total_week );

			//print $cur_year."-".$cur_month."->".$last_day."-".$last_week;
		?>
		<table width="100%" height="450" cellpadding="0" cellspacing="1" bgcolor="#707070">
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
			<td class="calday" style="cursor:pointer;" onclick="go_day('<?=$cur_year?>-<?=$cur_month?>-<?=$day?>')">
			<? if ( !( ( $i == 1 && $j < $start_week ) || ( $i == $total_week && $j > $last_week ) ) ): ?>
			<div class="caldaytext"><?=$day?></div>
			<?
				$cal_day = sprintf( "%s-%02d", $_GET["month"], $day );
				$resv_list = $schedule[ $cal_day ];
			?>
			<? for( $k=0; $k<min(3, sizeof($resv_list)); $k++): ?>
				<div class="rounded_corner_small" style="background-color:<?=$colors[$k]?>; padding-left:5px; color:white; font-size:12px;"><?=$resv_list[$k]?></div>
			<? endfor ?>
			<? $day += 1; ?>
			<? endif ?>
			</td>
			<? endfor ?>
		</tr>
		<? endfor ?>
		</table>
		</div>
	</div>
</body>
</html>