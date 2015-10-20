<?

if ( !defined( "__LIB_MODLIST__" ) )
{
	define( "__LIB_MODLIST__", "1" );

/******************************************

usage :

	include "libmodlist.php";

	$DB = new DB( "PositiveNew", "63.105.207.5", "www" );

	$m = new modList( "Premium_User a, Profile b" );
	$m->setopt( "debug", $REMOTE_ADDR );
	//$m->setopt( "pagename", "fff" );
	$m->setopt( "key", "a.No" );
	//$m->setopt( "keymode", "asc" );
	//$m->setopt( "seek", "3" );

	while( $data = $m->get( "a.No, a.Domain, b.Name, b.Tel", "a.No=b.No and a.Domain='finder'", "order by a.No" ) )
	{
		print "'".$data[lc]."'-".$data[Domain]."<br>";
	}

	print "<div align=center>".$m->page()."</div>";

******************************************/

	include "libpage.php";
        
	class modlist
	{
		function modlist( $table = '', $PageName = 'Page', $PageSize = '15' )
		{
			$this->pagesize = $PageSize;
			$this->pagename = $PageName;
			$this->debug = 0;
			$this->commit = 0;
			$this->key = '';
			$this->keymode = 'asc';
			$this->attract = $PageSize * 500;
			$this->listall = 0;
			$this->seek = 0;

			if ( $table != '' ) $this->usedb( $table );
		}

		function usedb( $table, $dbvar = 'DB' )
		{
			$DB = &$GLOBALS[$dbvar];
			if ( $DB == '' ) die( "Can't make use of \$$dbvar." );
			$this->tbl = $DB->table( $table );
		}

		function setopt( $key, $val )
		{
			if ( $this->commit ) print "warning : settings already commited, so setopt will not affect results<br>\n";

			switch( $key )
			{
				case "name":
				case "pagesize":
				case "pagename":
				case "page":
				case "debug":
				case "key":
				case "keymode":
				case "listall":
				case "seek":
				break;
				default:
					die( $key.' named option not found.' );
			}
			$this->$key = $val;
		}

		function set( $field = '*', $where = '', $spec = '' )
		{
			global $REMOTE_ADDR, $_k, $_x, $_GET;
                        
			$this->page = $_GET[ $this->pagename ];

			if ( $this->page == '' ) $this->page = 1;

			$Seek = ( $this->page - 1 ) * $this->pagesize;

			$count = $this->tbl->selectfetch( "", "count(*) as cnt", $where );
			$count = floor( $count[cnt] );

			if ( $_k != '' )
			{
				list( $min, $max ) = $this->tbl->selectfetch( "", "Min($this->key), Max($this->key)" );
				$_k++;
				switch( $this->keymode )
				{
					case "asc":
						$at = $_k + $this->attract;
						if ( $at > $max ) $at = $max;
						$str = "$_k and $at";
					break;
					case "desc":
						$at = $_k - $this->attract;
						if ( $at < $min ) $at = $min;
						$str = "$at and $_k";
					break;
				}
 				$where .= " and $this->key between $str";
				$spec .= " limit 15";
			}

			if ( $this->key != '' )
			{
				list( $tmp, $this->keyfield ) = explode( ".", $this->key );
				$this->keyfield = $this->key;
			}

			$this->tbl->select( $field, $where, $spec );

			if ( $this->debug === $REMOTE_ADDR ) $this->tbl->lastsql();

			if ( $_x != '' ) $this->lines = $_x;
			else $this->lines = $count;

			if ( $_k == '' && $Seek ) $this->tbl->seek( $Seek );

			$this->ct = 0;
			$this->linecnt = $count - $Seek;

			if ( $this->seek > 0 )
			{
				$this->tbl->seek( $this->seek - 1 );
				$this->tbl->fetch();
			}

			$this->commit = 1;
		}

		function get( $field = '*', $where = '', $spec = '' )
		{
			if ( !$this->commit ) $this->set( $field, $where, $spec );

			if ( $this->listall || ( $this->ct < $this->pagesize ) )
			{
				$data = $this->tbl->fetch();
				if ( $data != '' ) $data[lc] = $this->linecnt;
				if ( $this->key != '' ) $this->_k = $data[ $this->keyfield ];

				$this->linecnt--;
			}
			$this->ct++;

			return $data;
		}

		function reset(){
			$this->tbl->free();
			$this->commit = 0;
		}
		
		function nodata()
		{
		    if ( $this->lines > 0 ) return false;
		    else return true;
		}

		function page()
		{
			global $_GET, $_POST, $PHP_SELF;

			$listno = new ListNo();
			$listno->NoPageFrontStr 	= "";
			$listno->NoPageNoFrontStr 	= "<b>&lt;";
			$listno->NoPageNoRearStr 	= "&gt;</b>";
			$listno->NoPageRearStr		= "&nbsp;&nbsp;&nbsp;";
			$listno->NoLinkFrontStr		= "";
			$listno->NoLinkNoFrontStr	= "[";
			$listno->NoLinkNoRearStr 	= "]";
			$listno->NoLinkRearStr		= "&nbsp;&nbsp;&nbsp;";

			$listno->PageNoName = $this->pagename;
			$listno->ListNoInit( '', $this->lines, $this->pagesize, 5 );

			$cgi = '';

			if ( is_array ( $_GET ) ){
				$_GET["dummy"] = time();
				foreach( $_GET as $key => $val )
				{
					switch( $key )
					{
						case $this->pagename:
						case "_k":
						case "_x":
						break;
						default:
							$cgi .= "&$key=$val";
					}
				}
			}

			if ( is_array ( $_POST ) ){
				foreach( $_POST as $key => $val )
				{
					switch( $key )
					{
						case $this->pagename:
						case "_k":
						case "_x":
						break;
						default:
							$cgi .= "&$key=$val";
					}
				}
			}

			if ( $this->key != '' )
			{
				$nextkey = "&_k=".$this->_k."&_x=".$this->lines;

				if ( $this->page < $listno->TotalPage )
				{
					$nextlinkS = "<a href='$PHP_SELF?$cgi&$this->pagename=".($this->page + 1)."$nextkey'>";
					$nextlinkE = "</a>";
					$nextlink = "&nbsp;".$nextlinkS."[next]".$nextlinkE;
				}
			}

			while( list( $key, $val ) = @each( $listno->ListNoFetch() ) )
			{
				if ( $val["PageNo"] == ( $this->page + 1 ) ) $keylist = $nextkey; else $keylist = '';
                                $tag = ( $val[QueryString] ) ? "<a href='$PHP_SELF?$cgi&$val[QueryString]$keylist'>{$val[No]}</a>" : $val[No];
                                print $val[NoFRONT].$tag.$val[NoREAR];
			}

			print $nextlink;
		}

		function pageEx()
		{
			global $_GET, $_POST, $PHP_SELF;

			$listno = new ListNo();
			$listno->NoFirstStr = "<font color=#000000 size=1>◀</font>";
			$listno->NoLastStr = "<font color=#000000 size=1>▶</font>";
			$listno->NoPageNoFrontStr = "<b>";
			$listno->NoPageNoRearStr = "</b>";
			$listno->NoLinkNoFrontStr = "";
			$listno->NoLinkNoRearStr = "";
			$listno->PageNoName = $this->pagename;
			$listno->ListNoInit( '', $this->lines, $this->pagesize, 10 );

			$cgi = '';
			if (  is_array ( $_GET ) ){
				$_GET[dummy] = time();
				foreach( $_GET as $key => $val )
				{
					switch( $key )
					{
						case $this->pagename:
						case "_k":
						case "_x":
						break;
						default:
							$cgi .= "&$key=$val";
					}
				}
			}

			if ( is_array ( $_POST ) ){
				foreach( $_POST as $key => $val )
				{
					switch( $key )
					{
						case $this->pagename:
						case "_k":
						case "_x":
						break;
						default:
							$cgi .= "&$key=$val";
					}
				}
			}


			if ( $this->key != '' )
			{
				$nextkey = "&_k=".$this->_k."&_x=".$this->lines;

				if ( $this->page < $listno->TotalPage )
				{
					$nextlinkS = "<a href='$PHP_SELF?$cgi&$this->pagename=".($this->page + 1)."$nextkey'>";
					$nextlinkE = "</a>";
					$nextlink = "&nbsp;".$nextlinkS."[next]".$nextlinkE;
				}
			}

			$str = array();
			while( list( $key, $val ) = @each( $listno->ListNoFetch() ) )
			{
				if ( $val[PageNo] == ( $this->page + 1 ) ) $keylist = $nextkey; else $keylist = '';
	    		$tag = ( $val[QueryString] ) ? "<a href='$PHP_SELF?$cgi&$val[QueryString]$keylist'>{$val[No]}</a>" : $val[No];
	    		$str[] = $val[NoFRONT].$tag.$val[NoREAR];
			}
			$link = implode( " . ", $str );
			$link .= $nextlink;

			return $link;
		}

		function lastsql( $invisible = false )
		{
			$this->tbl->lastsql( $invisible );
		}

		function maxrecord()
		{
			return $this->lines;
		}
	}

}

?>
