<?

	require_once "init.php";

	switch( $_GET["mode"] ) {
		case "prc_begin_check":
			$table = $DB->table( "roomreserve" );
			$data = $table->selectfetch( "", "*", "rr_no='".$_GET["rr_no"]."'" );

			$now = time();

			list( $y, $m, $d ) = explode( "-", $data["rr_date"] );
			list( $b_h, $b_m ) = explode( ":", time_format( $data["rr_time_begin"] ) );
			list( $e_h, $e_m ) = explode( ":", time_format( $data["rr_time_end"] ) );

			//$begin = mktime( $b_h, $b_m, 0, $m, $d, $y );
			$begin = strtotime( $data["rr_date"]." ".time_format( $data["rr_time_begin"] ) );
			$end = strtotime( $data["rr_date"]." ".time_format( $data["rr_time_end"] ) );

			// 시작하기 15분 전, 끝나고 15분 후까지 예약 체크가 가능함..
			$begin -= 900;
			$end += 900;

			if ( $now < $begin || $now > $end ) {
				$result = "실패";
				$message = "예약 시작시간 15분 전부터 사용 종료시간 15분 후까지 사용 체크가 가능합니다. 이외의 시간에는 사용 체크를 할 수 없습니다.";
			}
			else {
				$table->update( "rr_begin_check=now()", "rr_no='".$_GET["rr_no"]."'" );
				$result = "성공";
				$message = "예약 확인이 완료되었습니다";
			}

			print json_encode( array( "result" => $result, "message" => $message, "now" => $now, "b" => $begin, "e" => $end, "subj" => $data["rr_subject"] ) );
		break;
		case "prc_delete_check":
			$table = $DB->table( "roomreserve" );
			list( $tel ) = $table->selectfetch( "", "rr_phone", "rr_no='".$_GET["rr_no"]."'" );

			if ( $tel == trim( $_GET["tel"] ) ) {
				$table->update( "rr_deleted='1', rr_update_date=now(), rr_update_ip='".$_SERVER["REMOTE_ADDR"]."'", "rr_no='".$_GET["rr_no"]."'" );
				print json_encode( array( "result" => "삭제 성공", "message" => "예약이 삭제되었습니다" ) );
				exit;
			}
			else {
				print json_encode( array( "result" => "삭제 실패", "message" => "예약 시 입력한 전화번호와 일치하지 않습니다" ) );	
				exit;
			}
		break;
		case "prc_write":
			// 겹치는 시간이 없으면 바로 정보 등록!

			$table = $DB->table( "roomreserve" );
			$row = new rowedit;

			$time_begin = time_format( $_POST["rr_time_begin"] );
			$time_end = time_format( $_POST["rr_time_end"] );

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
			print "<SCRIPT> document.location.href='index.php?date=".$redirect_date."' </SCRIPT>";
		break;

		case "prc_duplicate_check":
			$result = "등록 성공";
			$message = "";

			$table = $DB->table( "roomreserve" );

			$time_begin = time_format( $_GET["begin"] );
			$time_end = time_format( $_GET["end"] );

			// 시간 및 날짜 확인
			if ( $time_begin == $time_end ) {
				$result = "등록 실패";
				$message = "사용 시작 시간과 종료 시간이 같습니다";
				print json_encode( array( "rows" => $rows, "result" => $result, "message" => $message ) );
				exit;
			}

			if ( $time_begin > $time_end ) {
				$result = "등록 실패";
				$message = "사용 시작 시간이 종료 시간보다 나중이 될 수 없습니다";
				print json_encode( array( "rows" => $rows, "result" => $result, "message" => $message ) );
				exit;
			}

			// 1달 이내에만 등록 가능하도록 제한
			list( $y, $m, $d ) = explode( "-", $_GET["date"] );
			$date_stamp = mktime( 0, 0, 0, $m, $d, $y );
			list( $y, $m, $d ) = explode( "-", date( "Y-m-d" ) );
			$today_stamp = mktime( 0, 0, 0, $m, $d, $y );

			if ( $date_stamp - $today_stamp > 31 * 86400 ) {
				$result = "등록 실패";
				$message = "오늘로부터 1개월 이내의 기간만 등록할 수 있습니다. 1개월 이후의 날짜에 예약을 원하시면 관리자에게 연락 바랍니다.";
				print json_encode( array( "rows" => $rows, "result" => $result, "message" => $message ) );
				exit;
			}

			// 먼저 등록하려는 시간대가 비어 있는지 확인한다.

			// 반복 설정이 되어 있다면, 등록 일자 자체는 무시한다.
			if (
				$_GET["week_1"] == "true" ||
				$_GET["week_2"] == "true" ||
				$_GET["week_3"] == "true" ||
				$_GET["week_4"] == "true" ||
				$_GET["week_5"] == "true" ||
				$_GET["week_6"] == "true"
			) {
				$dates_to_check = array();
				list( $y, $m, $d ) = explode( "-", $_GET["repeat_begin"] );
				$s_begin = mktime( 0, 0, 0, $m, $d, $y);

				list( $y, $m, $d ) = explode( "-", $_GET["repeat_end"] );
				$s_end = mktime( 0, 0, 0, $m, $d, $y);

				$dates_to_check = array();
				for( $stamp=$s_begin; $stamp<=$s_end; $stamp+=86400 ) {
					$week_num = date( "w", $stamp );
					if ( $week_num == 1 && $_GET["week_1"] == "true" ) $dates_to_check[] = date( "Y-m-d", $stamp );
					if ( $week_num == 2 && $_GET["week_2"] == "true" ) $dates_to_check[] = date( "Y-m-d", $stamp );
					if ( $week_num == 3 && $_GET["week_3"] == "true" ) $dates_to_check[] = date( "Y-m-d", $stamp );
					if ( $week_num == 4 && $_GET["week_4"] == "true" ) $dates_to_check[] = date( "Y-m-d", $stamp );
					if ( $week_num == 5 && $_GET["week_5"] == "true" ) $dates_to_check[] = date( "Y-m-d", $stamp );
					if ( $week_num == 6 && $_GET["week_6"] == "true" ) $dates_to_check[] = date( "Y-m-d", $stamp );
				}
				$dates_to_check_str = "'".implode( "', '", $dates_to_check )."'";

				// 위에서 수집한 회의 반복 일자에 대해 비어있는지 여부를 검사한다.
				// 지운 항목과는 시간이 겹쳐도 됨...(2015.10.20)
				$table->select( "*", "rr_deleted=0 and rr_room='".$_GET["room"]."' and rr_date in (".$dates_to_check_str.")" );
			}
			else {
				// 반복 설정이 되어 있지 않다면, 등록 일자를 기준으로 query한다.
				// 지운 항목과는 시간이 겹쳐도 됨...(2015.10.20)
				$table->select( "*", "rr_deleted=0 and rr_room='".$_GET["room"]."' and rr_date='".$_GET["date"]."'" );
			}

			$rows = $table->maxrecord();

			$requested = "신청한 시간(".time_human( $time_begin )."~".time_human( $time_end ).")";
			while( $data = $table->fetch() ) {
				$summary = $data["rr_subject"]."(".time_human( $data["rr_time_begin"] )."~".time_human( $data["rr_time_end"] ).")";

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

			print json_encode( array( "rows" => $rows, "result" => $result, "message" => $message, "redirect_date" => $redirect_date, "sql" => $table->lastsql ) );
		break;
	}

?>