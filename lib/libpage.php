<?

if ( !defined( "__LIB_PAGE__" ) )
{
	define( "__LIB_PAGE__", "1" );

/*=======================================================================================================
   Program   : 리스트분할페이지 클래스   
   Author    : JoonMan,Park 박준만 [zfirst@hanmail.net]  
   Manual   : 하단참조
=======================================================================================================*/

class ListNo {
	
	var $Total;		// 전체
	var $TotalPage;		// 전체 페이지
	var $TotalBottom;       // 바텀 전체페이지
	
	var $Max;
	var $Min;
	
	var $StartNo;		// 시작값
	var $EndNo;		// 한페이지 로딩 수
	var $BottomNo;		// 바텀 한페이지 로딩수	
	var $Name;      	// 리스트바텀의 중복을 방지하기위해 리스트 이름(한페이지에 리스트 두개)	
	
	var $Seqno;		// 순차번호
	
	var $PageNow;		// 현재 페이지
	var $BottomNow;		// 현제 바텀페이지
	
	var $BottomTags = array();	// 바텀 태그들	
	
	var $PrevQueryString;	// 이전페이지 쿼리스트링
	var $NextQueryString;	// 다음페이지 쿼리스트링
	var $FirstQueryString;	// 첫페이지 쿼리스트링
	var $LastQueryString;	// 마지막페이지 쿼리스트링
		
	var $PageNoName = "Page";		
	
	// 페이지번호에서 해당페이지일때
	var $NoPageFrontStr 	= "";
	var $NoPageNoFrontStr 	= "<b>[";
	var $NoPageNoRearStr 	= "]</b>";
	var $NoPageRearStr 	= "";
	
	// 페이지 링크걸릴때
	var $NoLinkFrontStr 	= "";
	var $NoLinkNoFrontStr 	= "[";
	var $NoLinkNoRearStr 	= "]";
	var $NoLinkRearStr 	= "";
	
	// 앞페이지블락 으로
	var $NoFirstFrontStr 	= "";
	var $NoFirstStr 	= "◀";
	var $NoFirstRearStr 	= " ...";
	
	// 뒷페이지블락 으로
	var $NoLastFrontStr 	= "... ";
	var $NoLastStr 		= "▶";
	var $NoLastRearStr 	= "";
	
	/*========================================================================================
		ListNo 선언 (startNo 값 선언하는 역활, bottomTags 생성)
		
		$ListNo = new ListNo();
		$ListNo->ListNoInit("board", $result[count], 15, 10, $fidTotal="");
	========================================================================================*/
	function ListNoInit($name, $total, $endNo, $bottomNo, $fidTotal="") {						
            
                global $_GET;
		
		$this->Name = $name;
                
		# 현재페이지들
		$this->PageNow = $_GET[ $name.$this->PageNoName ];
		$this->BottomNow = @ceil( $_GET[ $name.$this->PageNoName ]/$bottomNo);
		
		if(empty($this->PageNow)) $this->PageNow = 1;
		if(empty($this->BottomNow)) $this->BottomNow = 1;
		
		# 전체값및 페이지수 설정
		$this->Total = $total;
		$this->EndNo = $endNo;
		$this->BottomNo = $bottomNo;				
		
		# 시작번호
		$this->StartNo = (int) ($endNo * ($this->PageNow - 1));                                  						
		
		# 토탈페이지
		if($fidTotal!="") {
			
			$this->Max = (int)($fidTotal - (($this->PageNow - 1) * $endNo));    // $fidTotal 이 있을경우
			$this->Min = (int)(($this->Max - $endNo) + 1);						
			$this->TotalPage = @ceil($fidTotal/$this->EndNo);     // limit 을 안쓰고 fid 로 조건을 잡을경우(즉,$fidTotal 이 있을시)는 $fidTotal 로 전체페이지를 계산한다.
			
		} else {
			
			$this->TotalPage = @ceil($this->Total/$this->EndNo);   // 일반일땐 전체값으로 전체페이지를 계산
		}				
						
		$this->TotalBottom = @ceil($this->TotalPage/$this->BottomNo);	
		
		# 순차번호 매기기 (while문의 마지막에 --$Seqno  해서 사용)			
		$this->Seqno = $this->Total - ( ($this->PageNow - 1) * $this->EndNo );  								
		
		# 이동버튼들		
		if($this->PageNow > 1) {
			
			$p = $this->PageNow - 1;
			$b = ($p < ($this->BottomNo * ($this->BottomNow - 1)) + 1) ? $this->BottomNow - 1 : $this->BottomNow;			
			$this->PrevQueryString = $name.$this->PageNoName."=$p";								
			$this->FirstQueryString = $name.$this->PageNoName."=1";		
		} else {
			
			$this->PrevQueryString = "";
			$this->FirstQueryString = "";
		}
		
		if($this->PageNow < $this->TotalPage) {
			
			$p = $this->PageNow + 1;
			$b = ($p > $this->BottomNo * $this->BottomNow) ? $this->BottomNow + 1 : $this->BottomNow;			
			$this->NextQueryString = $name.$this->PageNoName."=$p";		
			$this->LastQueryString = $name.$this->PageNoName."=$this->TotalPage";										
		} else {
			
			$this->NextQueryString = "";
			$this->LastQueryString = "";
		}				
						
		# 바텀넘버태그 모음		
		$a = 0;				
		if($this->BottomNow > 1) {                     
            		            		
            		$p = ($this->BottomNow-1) * $this->BottomNo;            		            		
            		
            		$this->BottomTags[$a][NoFRONT] 	= $this->NoFirstFrontStr;
			//$this->BottomTags[$a][No] 	= $this->NoFirstStr;
			$this->BottomTags[$a][No] 	= $this->NoLinkNoFrontStr.$p.$this->NoLinkNoRearStr;
			$this->BottomTags[$a][PageNo] = $p;
			$this->BottomTags[$a][NoREAR] 	= $this->NoFirstRearStr;			
			$this->BottomTags[$a][QueryString] = $name.$this->PageNoName."=$p";
			++$a;
		            		
         	}
      		      		
      		for($i=(($this->BottomNow-1) * $this->BottomNo)+1; $i <= $this->TotalPage; $i++) {               	            		
            		
            		if($i > $this->BottomNo * $this->BottomNow) {                           
                  		
                  		$b = $this->BottomNow + 1;                  		                  		
                  		                  		
                  		$this->BottomTags[$a][NoFRONT] 	= $this->NoLastFrontStr;
				//$this->BottomTags[$a][No] 	= $this->NoLastStr;
				$this->BottomTags[$a][No] 	= $this->NoLinkNoFrontStr.$i.$this->NoLinkNoRearStr;
				$this->BottomTags[$a][PageNo] = $i;
				$this->BottomTags[$a][NoREAR] 	= $this->NoLastRearStr;				
				$this->BottomTags[$a][QueryString] = $name.$this->PageNoName."=$i";
				break;
			}
                         		
             		if($this->PageNow == $i) {
             			
             			$this->BottomTags[$a][NoFRONT] 	= $this->NoPageFrontStr;                            
				$this->BottomTags[$a][No] 	= $this->NoPageNoFrontStr.$i.$this->NoPageNoRearStr;
				$this->BottomTags[$a][PageNo] = $i;
				$this->BottomTags[$a][NoREAR] 	= $this->NoPageRearStr;				    
				$this->BottomTags[$a][QueryString] = "";
				
             		} else {             		             		             		
             		
             			$this->BottomTags[$a][NoFRONT] 	= $this->NoLinkFrontStr;
				$this->BottomTags[$a][No] 	= $this->NoLinkNoFrontStr.$i.$this->NoLinkNoRearStr;
				$this->BottomTags[$a][PageNo] = $i;
				$this->BottomTags[$a][NoREAR] 	= $this->NoLinkRearStr;
				$this->BottomTags[$a][QueryString] = $name.$this->PageNoName."=$i";
			}
			
			++$a;
		}
		
		reset($this->BottomTags);
	}
	
	/*========================================================================================
		바텀번호 추출  		
	========================================================================================*/
	function ListNoFetch() {
																	
		$temp = each($this->BottomTags);
		return $temp;
	}
	
	/*========================================================================================
		바텀번호에 관련된 태그 변경 ($variableName 은 해당 변수명이다. 상단 참조)
	========================================================================================*/ 
	function ListNoSetTag($variableName, $value) {
						
		$this->$variableName = $value;				
	}		
}




/*=======================================================================================================
   Manual : 
   
	$ListNo = new ListNo();
	
	$ListNo->ListNoSetTag("NoFirstStr", "<img src=''>");	// 리스트바텀에 찍히는 태그를 설정 (◀ 버튼을 이미지로 변경한것이다.)
	
	$ListNo->ListNoInit(리스트이름, 리스트전체값 + 1, 한페이지출력수, 페이지번호출력수, 백만게시판일때최근fid값 = "");
	
	echo $ListNo->Total;  	// 전체값
	echo $ListNo->TotalPage;  	// 전체 페이지
	echo $ListNo->TotalBottom;	// 전체 바텀 페이지
	echo $ListNo->Max;		// 한페이지 출력갯수를 where 에서 fid 를 걸때 Max 값
	echo $ListNo->Min;		// 한페이지 출력갯수를 where 에서 fid 를 걸때 Max 값
	echo $ListNo->Seqno;		// 순차번호 (while문의 마지막에 --$Seqno  해서 사용)
	echo $ListNo->StartNo;	// 한페이지 출력갯수를 limit 으로 할때 시작값
	echo $ListNo->EndNo;	// 한페이지 출력갯수
	echo $ListNo->BottomNo;	// 한페이지 페이지번호출력갯수
	echo $ListNo->Name;		// 리스트이름
	echo $ListNo->PageNow;	// 현재페이지
	echo $ListNo->BottomNow;	// 현재 바텀 페이지
	echo $ListNo->PrevQueryString;	// 이전페이지 쿼리스트링
	echo $ListNo->NextQueryString;	// 다음페이지 쿼리스트링
	echo $ListNo->FirstQueryString;	// 첫페이지 쿼리스트링
	echo $ListNo->LastQueryString;	// 마지막페이지 쿼리스트링
	
	echo $ListNo->PageNoName;	// 페이지번호 쿼리스트링 명 (앞에 리스트이름을 붙여야한다.)								
	
	while( list( $key, $val ) = @each( $ListNo->ListNoFetch() ) )
	{
	    $tag = ($val[QueryString])  ?  "<a href='$PHP_SELF?Filter=$Filter&Smode=$Smode&SmodeIdx=$SmodeIdx&Keyword=$Keyword&dummy=$dummy&{$val[QueryString]}'>{$val[No]}</a>"  :  $val[No];
	    echo $val[NoFRONT].$tag.$val[NoREAR];
	}
    
=======================================================================================================*/

}

?>
