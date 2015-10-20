<?

	include "../init.php";

	function alert_halt( $message ) {
		print "<SCRIPT> alert('".str_replace( "'", "\'", $message )."'); </SCRIPT>";
		exit;
	}

	function reserve_duplicate_check( $opt_room, $opt_date, $time_begin, $time_end, $opt_repeat_begin, $opt_repeat_end, $opt_repeat_weeks, $exclude_no = '' ) {
		global $DB;

		$result = "등록 성공";
		$message = "";

		$table = $DB->table( "roomreserve" );

		// 시간 및 날짜 확인
		if ( $time_begin == $time_end ) {
			$result = "등록 실패";
			$message = "사용 시작 시간과 종료 시간이 같습니다";
			return array( "rows" => $rows, "result" => $result, "message" => $message );
		}

		if ( $time_begin > $time_end ) {
			$result = "등록 실패";
			$message = "사용 시작 시간이 종료 시간보다 나중이 될 수 없습니다";
			return array( "rows" => $rows, "result" => $result, "message" => $message );
		}

		// 1달 이내에만 등록 가능하도록 제한
		// 관리자 예약이므로 제한 두지 않음...
		/*
		list( $y, $m, $d ) = explode( "-", $opt_date );
		$date_stamp = mktime( 0, 0, 0, $m, $d, $y );
		list( $y, $m, $d ) = explode( "-", date( "Y-m-d" ) );
		$today_stamp = mktime( 0, 0, 0, $m, $d, $y );

		if ( $date_stamp - $today_stamp > 31 * 86400 ) {
			$result = "등록 실패";
			$message = "오늘로부터 1개월 이내의 기간만 등록할 수 있습니다. 1개월 이후의 날짜에 예약을 원하시면 관리자에게 연락 바랍니다.";
			return array( "rows" => $rows, "result" => $result, "message" => $message );
		}
		*/

		// 먼저 등록하려는 시간대가 비어 있는지 확인한다.

		$update_exclude_sql = '';
		if ( $exclude_no != '' ) {
			$update_exclude_sql = " and rr_no != '".$exclude_no."'";
		}

		// 반복 설정이 되어 있다면, 등록 일자 자체는 무시한다.
		if (
			$opt_repeat_week["week_1"] == "true" ||
			$opt_repeat_week["week_2"] == "true" ||
			$opt_repeat_week["week_3"] == "true" ||
			$opt_repeat_week["week_4"] == "true" ||
			$opt_repeat_week["week_5"] == "true" ||
			$opt_repeat_week["week_6"] == "true"
		) {
			$dates_to_check = array();
			list( $y, $m, $d ) = explode( "-", $opt_repeat_begin );
			$s_begin = mktime( 0, 0, 0, $m, $d, $y);

			list( $y, $m, $d ) = explode( "-", $opt_repeat_end );
			$s_end = mktime( 0, 0, 0, $m, $d, $y);

			$dates_to_check = array();
			for( $stamp=$s_begin; $stamp<=$s_end; $stamp+=86400 ) {
				$week_num = date( "w", $stamp );
				if ( $week_num == 1 && $opt_repeat_week["week_1"] == "true" ) $dates_to_check[] = date( "Y-m-d", $stamp );
				if ( $week_num == 2 && $opt_repeat_week["week_2"] == "true" ) $dates_to_check[] = date( "Y-m-d", $stamp );
				if ( $week_num == 3 && $opt_repeat_week["week_3"] == "true" ) $dates_to_check[] = date( "Y-m-d", $stamp );
				if ( $week_num == 4 && $opt_repeat_week["week_4"] == "true" ) $dates_to_check[] = date( "Y-m-d", $stamp );
				if ( $week_num == 5 && $opt_repeat_week["week_5"] == "true" ) $dates_to_check[] = date( "Y-m-d", $stamp );
				if ( $week_num == 6 && $opt_repeat_week["week_6"] == "true" ) $dates_to_check[] = date( "Y-m-d", $stamp );
			}
			$dates_to_check_str = "'".implode( "', '", $dates_to_check )."'";

			// 위에서 수집한 회의 반복 일자에 대해 비어있는지 여부를 검사한다.
			// 지운 항목과는 시간이 겹쳐도 됨...(2015.10.20)
			// rr_deleted=0 과 rr_deleted='0'은 의미가 많이 틀리다!! 주의할 것.
			$table->select( "*", "rr_deleted='0' and rr_room='".$opt_room."' and rr_date in (".$dates_to_check_str.")".$update_exclude_sql );
			$table->lastsql();
		}
		else {
			// 반복 설정이 되어 있지 않다면, 등록 일자를 기준으로 query한다.
			// 지운 항목과는 시간이 겹쳐도 됨...(2015.10.20)
			// rr_deleted=0 과 rr_deleted='0'은 의미가 많이 틀리다!! 주의할 것.
			$table->select( "*", "rr_deleted='0' and rr_room='".$opt_room."' and rr_date='".$opt_date."'".$update_exclude_sql );
			$table->lastsql();
		}

		$rows = $table->maxrecord();

		$requested = "신청한 시간(".time_human( $time_begin )."~".time_human( $time_end ).")";
		while( $data = $table->fetch() ) {
			$summary = $data["rr_subject"]."(".time_human( $data["rr_time_begin"] )."~".time_human( $data["rr_time_end"] ).")";
			print "<div>time_begin : ".$data["rr_time_begin"]." == ".$time_begin." / time_end : ".$data["rr_time_end"]."==".$time_end."</div>\n";

			if ( $data["rr_time_begin"] == $time_begin ) {
				$result = "등록 실패";
				$message = "해당 시간에 회의가 이미 등록되어 있습니다(".$requested.", ".$summary.")";
				break;
			}
			if ( $data["rr_time_end"] > $time_begin && $data["rr_time_end"] < $time_end ) {
				$result = "등록 실패";
				$message = "해당 시간에 회의가 이미 등록되어 있습니다(".$requested.", ".$summary.")";
				break;
			}
			if ( $data["rr_time_begin"] < $time_begin && $data["rr_time_end"] >= $time_end ) {
				$result = "등록 실패";
				$message = "해당 시간에 회의가 이미 등록되어 있습니다(".$requested.", ".$summary.")";
				break;
			}
			if ( $data["rr_time_begin"] >= $time_begin && $data["rr_time_begin"] < $time_end ) {
				$result = "등록 실패";
				$message = "해당 시간에 회의가 이미 등록되어 있습니다(".$requested.", ".$summary.")";
				break;
			}
			if ( $data["rr_time_begin"] < $time_begin && $data["rr_time_end"] > $time_end ) {
				$result = "등록 실패";
				$message = "해당 시간에 회의가 이미 등록되어 있습니다(".$requested.", ".$summary.")";
				break;
			}
			if ( $data["rr_time_begin"] > $time_begin && $data["rr_time_end"] < $time_end ) {
				$result = "등록 실패";
				$message = "해당 시간에 회의가 이미 등록되어 있습니다(".$requested.", ".$summary.")";
				break;
			}

		}

		return array( "rows" => $rows, "result" => $result, "message" => $message, "redirect_date" => $redirect_date, "sql" => $table->lastsql );
	}

	switch( $_GET["mode"] ) {
		case "prc_adminwrite":
			print "<xmp>";
			print_r( $_GET );
			print_r( $_POST );
			print "</xmp>";

			if ( $_POST["rr_name"] == '' ) alert_halt( "예약자명을 입력해 주세요." );
			if ( $_POST["rr_phone"] == '' ) alert_halt( "전화번호를 입력해 주세요." );
			if ( $_POST["rr_subject"] == '' ) alert_halt( "회의주제를 입력해 주세요." );
			if ( $_POST["rr_date"] == '' || $_POST["rr_time_begin"] == '' || $_POST["rr_time_end"] == '' ) alert_halt( "사용일시를 입력해 주세요." );
			if ( strlen( $_POST["rr_time_begin"] ) != 4 || strlen( $_POST["rr_time_end"] ) != 4 ) alert_halt( "사용시작시간 또는 사용종료시간을 HHMM 형식으로 4글자로 입력해 주세요." );
			if ( substr( $_POST["rr_time_begin"], 0, 2 ) > 24 || substr( $_POST["rr_time_end"], 0, 2 ) > 24 ) alert_halt( "사용시간 시작 혹은 종료가 24시를 넘을 수 없습니다. HHMM형식을 확인하여 주세요." );
			if ( substr( $_POST["rr_time_begin"], -2 ) != "00" && substr( $_POST["rr_time_begin"], -2 ) != "30" ) alert_halt( "시작시간 분 단위를 30분 단위로 입력해 주세요." );
			if ( substr( $_POST["rr_time_end"], -2 ) != "00" && substr( $_POST["rr_time_end"], -2 ) != "30" ) alert_halt( "시작시간 분 단위를 30분 단위로 입력해 주세요." );

			$weeks = explode( ",", $_POST["rr_repeat_week"] );

			if ( in_array( $weeks, "월" ) == true ) $opt_weeks["weeks_1"] = "true";
			if ( in_array( $weeks, "화" ) == true ) $opt_weeks["weeks_2"] = "true";
			if ( in_array( $weeks, "수" ) == true ) $opt_weeks["weeks_3"] = "true";
			if ( in_array( $weeks, "목" ) == true ) $opt_weeks["weeks_4"] = "true";
			if ( in_array( $weeks, "금" ) == true ) $opt_weeks["weeks_5"] = "true";
			if ( in_array( $weeks, "토" ) == true ) $opt_weeks["weeks_6"] = "true";

			$result = reserve_duplicate_check(
				$_POST["rr_room"], $_POST["rr_date"], $_POST["rr_time_begin"], $_POST["rr_time_end"],
			 	$_POST["rr_time_begin"], $_POST["rr_time_end"], $weeks, $_POST["rr_no"]
			 );

			if ( $result["result"] == "등록 성공" ) {

				$table = $DB->table( "roomreserve" );

				// 수정 모드이면 새로 등록하기 전에 전의 것을 안보이게 지움..
				if ( $_POST["rr_no"] != '' ) {
					$table->update( "rr_deleted='1'", "rr_no='".$_POST["rr_no"]."'" );
					$table->lastsql();
				}

				$row = new rowedit;
				$row->init();

				// ----------------------------------------------------------------------------------------------------
				// 겹치는 시간이 없으면 바로 정보 등록!
				// ----------------------------------------------------------------------------------------------------

				$row = new rowedit;

				$time_begin = $_POST["rr_time_begin"];
				$time_end = $_POST["rr_time_end"];

				if ( sizeof( $_POST["rr_repeat_week"] ) > 0 ) {
					// 먼저 등록할 일자들을 뽑음..
					$dates_to_check = array();
					list( $y, $m, $d ) = explode( "-", $_POST["rr_repeat_begin"] );
					$s_begin = mktime( 0, 0, 0, $m, $d, $y);

					list( $y, $m, $d ) = explode( "-", $_POST["rr_repeat_end"] );
					$s_end = mktime( 0, 0, 0, $m, $d, $y);

					$w = array();
					foreach( $_POST["rr_repeat_week"] as $week ) {
						switch( $week ) {
							case "월": $w["week_1"] = "true"; break;
							case "화": $w["week_2"] = "true"; break;
							case "수": $w["week_3"] = "true"; break;
							case "목": $w["week_4"] = "true"; break;
							case "금": $w["week_5"] = "true"; break;
							case "토": $w["week_6"] = "true"; break;
						}
					}

					for( $stamp=$s_begin; $stamp<=$s_end; $stamp+=86400 ) {
						$week_num = date( "w", $stamp );
						if ( $week_num == 1 && $w["week_1"] == "true" ) $dates_to_check[] = date( "Y-m-d", $stamp );
						if ( $week_num == 2 && $w["week_2"] == "true" ) $dates_to_check[] = date( "Y-m-d", $stamp );
						if ( $week_num == 3 && $w["week_3"] == "true" ) $dates_to_check[] = date( "Y-m-d", $stamp );
						if ( $week_num == 4 && $w["week_4"] == "true" ) $dates_to_check[] = date( "Y-m-d", $stamp );
						if ( $week_num == 5 && $w["week_5"] == "true" ) $dates_to_check[] = date( "Y-m-d", $stamp );
						if ( $week_num == 6 && $w["week_6"] == "true" ) $dates_to_check[] = date( "Y-m-d", $stamp );
					}

					$rr_repeat_week_str = implode( ",", $_POST["rr_repeat_week"] );

					$redirect_date = $dates_to_check[0];

					// 처음 1회 등록 후 나머지 반복 항목들을 등록..
					if ( sizeof( $dates_to_check ) > 0 ) {
						$row->init();
						$row->data( "rr_name", $_POST["rr_name"] );
						$row->data( "rr_phone", $_POST["rr_phone"] );
						$row->data( "rr_subject", $_POST["rr_subject"] );
						$row->data( "rr_room", $_POST["rr_room"] );
						$row->data( "rr_date", $dates_to_check[0] );
						$row->data( "rr_time_begin", 	$time_begin );
						$row->data( "rr_time_end", 		$time_end );

						$row->data( "rr_repeat_rr_no", 	"0" );
						$row->data( "rr_repeat_week", 	$rr_repeat_week_str );
						$row->data( "rr_repeat_begin", 	$_POST["rr_repeat_begin"] );
						$row->data( "rr_repeat_end", 	$_POST["rr_repeat_end"] );

						$row->data( "rr_update_date", "FN:now()" );
						$row->data( "rr_update_ip", $_SERVER["REMOTE_ADDR"] );

						$table->insert( $row->values(), $row->fields() );
						$rr_no = $table->getinsertid();

						$table->update( "rr_repeat_rr_no='".$rr_no."'", "rr_no='".$rr_no."'" );
					}

					for( $i=1; $i<sizeof( $dates_to_check ); $i++ ) {
						$row->init();
						$row->data( "rr_name", $_POST["rr_name"] );
						$row->data( "rr_phone", $_POST["rr_phone"] );
						$row->data( "rr_subject", $_POST["rr_subject"] );
						$row->data( "rr_room", $_POST["rr_room"] );
						$row->data( "rr_date", $dates_to_check[$i] );
						$row->data( "rr_time_begin", 	$time_begin );
						$row->data( "rr_time_end", 		$time_end );

						$row->data( "rr_repeat_rr_no", 	$rr_no );
						$row->data( "rr_repeat_week", 	$rr_repeat_week_str );
						$row->data( "rr_repeat_begin", 	$_POST["rr_repeat_begin"] );
						$row->data( "rr_repeat_end", 	$_POST["rr_repeat_end"] );

						$row->data( "rr_update_date", "FN:now()" );
						$row->data( "rr_update_ip", $_SERVER["REMOTE_ADDR"] );

						$table->insert( $row->values(), $row->fields() );
					}
				}
				else {
					// 1회 등록 후 종료
					$row->init();
					$row->data( "rr_name", $_POST["rr_name"] );
					$row->data( "rr_phone", $_POST["rr_phone"] );
					$row->data( "rr_subject", $_POST["rr_subject"] );
					$row->data( "rr_room", $_POST["rr_room"] );
					$row->data( "rr_date", $_POST["rr_date"] );
					$row->data( "rr_time_begin", 	$time_begin );
					$row->data( "rr_time_end", 		$time_end );
					$row->data( "rr_update_date", "FN:now()" );
					$row->data( "rr_update_ip", $_SERVER["REMOTE_ADDR"] );

					$redirect_date = $_POST["rr_date"];
					$table->insert( $row->values(), $row->fields() );
				}

				//print "<xmp>".print_r( $_POST, true )."</xmp>";
				print "<SCRIPT> parent.fnSuccess(); </SCRIPT>";
				// ----------------------------------------------------------------------------------------------------
				// 등록 종료
				// ----------------------------------------------------------------------------------------------------
			}
			else {
				alert_halt( $result["result"]." : ".$result["message"] );
			}

		break;

		case "prc_delete":
			$table = $DB->table( "roomreserve" );
			$table->update( "rr_deleted='1', rr_update_date=now(), rr_update_ip='".$_SERVER["REMOTE_ADDR"]."'", "rr_no='".$_GET["rr_no"]."'" );
			
			print "<SCRIPT> location.href='index.php?".$_GET["get"]."'; </SCRIPT>";
		break;

		case "prc_addip":
			$table = $DB->table( "deviceinfo" );

			$row = new rowedit;
			$row->init();
			$row->data( "di_ip", $_POST["di_ip"] );
			$row->data( "di_room", $_POST["di_room"] );
			$row->data( "di_update_date", "FN:now()" );

			$table->insert( $row->values(), $row->fields() );

			print "<SCRIPT> location.href='index.php?mode=device' </SCRIPT>";
		break;

		case "prc_delip":
			$table = $DB->table( "deviceinfo" );
			$table->delete( "di_no='".$_GET["di_no"]."'" );

			print "<SCRIPT> location.href='index.php?mode=device' </SCRIPT>";
		break;

		case "prc_addnotice":
			$table = $DB->table( "noticeinfo" );

			$row = new rowedit;
			$row->init();
			$row->data( "ni_active", "FN:1" );
			$row->data( "ni_subject", $_POST["ni_subject"] );
			$row->data( "ni_seq", "FN:IF(max(ni_seq) is NULL,1,max(ni_seq)+1)" );
			$row->data( "ni_update_date", "FN:now()" );
			$row->data( "ni_update_ip", $_SERVER["REMOTE_ADDR"] );

			$table->query( "insert into noticeinfo (".$row->fields().") select ".$row->values()." from noticeinfo" );
			//$table->lastsql();

			print "<SCRIPT> location.href='index.php?mode=notice' </SCRIPT>";
		break;

		case "prc_delnotice":
			$table = $DB->table( "noticeinfo" );
			$table->delete( "ni_no='".$_GET["ni_no"]."'" );

			print "<SCRIPT> location.href='index.php?mode=notice' </SCRIPT>";
		break;

		case "prc_toggleactive":
			$table = $DB->table( "noticeinfo" );
			$table->update( "ni_active=IF(ni_active='1','0','1')", "ni_no='".$_GET["ni_no"]."'" );

			print "<SCRIPT> location.href='index.php?mode=notice' </SCRIPT>";
		break;

		case "prc_sequp":
			$table = $DB->table( "noticeinfo" );
			list( $tgt_no, $tgt_seq ) = $table->selectfetch( "", "ni_no, ni_seq", "ni_seq>'".$_GET["ni_seq"]."'", "order by ni_seq asc" );

			if ( $tgt_no != '' && $tgt_seq != '' ) {
				$table->update( "ni_seq=".$tgt_seq, "ni_no='".$_GET["ni_no"]."'" );
				$table->update( "ni_seq=".$_GET["ni_seq"], "ni_no='".$tgt_no."'" );
			}

			print "<SCRIPT> location.href='index.php?mode=notice' </SCRIPT>";
		break;

		case "prc_seqdown":
			$table = $DB->table( "noticeinfo" );
			list( $tgt_no, $tgt_seq ) = $table->selectfetch( "", "ni_no, ni_seq", "ni_seq<'".$_GET["ni_seq"]."'", "order by ni_seq desc" );

			if ( $tgt_no != '' && $tgt_seq != '' ) {
				$table->update( "ni_seq=".$tgt_seq, "ni_no='".$_GET["ni_no"]."'" );
				$table->update( "ni_seq=".$_GET["ni_seq"], "ni_no='".$tgt_no."'" );
			}

			print "<SCRIPT> location.href='index.php?mode=notice' </SCRIPT>";
		break;

	}


?>