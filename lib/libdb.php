<?

if ( !defined( "__LIBDB__" ) )
{
define( "__LIBDB__", 1 );

class db_error
{
	var $dbh;
	var $error;
	var $facts;
	var $Method = "errorprint";
	var $admin_email = "airdrive@itstandard.co.kr";

	function db_error()
	{
		$this->error = "
		<font color=red>		
		죄송합니다. 페이지 작업중입니다.<br>
		조속한 시일내에 복구하도록 하겠습니다.<br>
		</font>
		";				
	}

	function queryerror( $dbh, $sql )
	{
		global $_GET, $_POST, $_SERVER;

		$errno = @mysql_errno( $dbh );
		$errmsg = @mysql_error( $dbh );

		$fp = fopen( "last_db_error.log", "a+" );

		$this->facts = "<table border=0 cellspacing=0 cellpaddong=0><tr><td bgcolor=white><font color=white>
		일시 : ".date( "Y-m-d H:i:s" )."<br>
		에러 번호 : $errno<br>
		에러 메세지 : $errmsg<br>
		입력한 sql : $sql<br>
		GET: <xmp>".print_r( $_GET, true )."</xmp><br />
		POST: <xmp>".print_r( $_POST, true )."</xmp><br />
		SERVER: <xmp>".print_r( $_SERVER, true )."</xmp><br />
		</td></tr></table>";

		fwrite( $fp, $this->facts );
		fclose( $fp );
		
		$method = $this->Method;
		$this->$method();
	}

	function connectionerror()
	{
		$this->facts = "
		<table border=0 cellspacing=0 cellpaddong=0>
		<tr><td bgcolor=white>
		<font color=white>
		데이터베이스에 연결할 수 없습니다!<br>
		</td></tr>
		</table>
		";
		
		$method = $this->Method;
		$this->$method();
	}

	function dbselecterror( $dbname )
	{
		$this->facts = "
		<table border=0 cellspacing=0 cellpaddong=0>
		<tr><td bgcolor=white>
		<font color=white>
		$dbname 데이터베이스를 선택할 수 없습니다!<br>
		</td></tr>
		</table>
		";

		$method = $this->Method;
		$this->$method();
	}

	function errorlog()
	{
		exit;
	} // 추후예정

	function errormail()
	{
		exit;
	} // 추후예정

	function errorprint()
	{
		print $this->error;
		print $this->facts;
		exit;
	}
}

class dbconn extends db_error
{
	var $dbh;
	var $db_name;
	var $db_user;
	var $db_host;
	var $db_pwd;
	var $lastsql;
	var $debug = false;

	function dbconn( $db_name='',$db_host='localhost',$db_user='www',$db_pwd='' )
	{
		$this->db_error();
		if ( $db_name ) $this->call( $db_name, $db_host, $db_user, $db_pwd );
	}

	function connect( $db_host = 'localhost', $db_user = 'www', $db_pwd = '' )
	{
		$this->db_user = $db_user;
		$this->db_host = $db_host;
		$this->db_pwd = $db_pwd;
		$this->dbh = mysql_connect( $db_host, $db_user, $db_pwd );
		if ( !$this->dbh ) $this->connectionerror();
		$this->query( "set collation_connection = @@collation_database" );
		$this->query( "set names 'utf8'" );
	}

	function selectdb( $db_name )
	{
		$this->db_name = $db_name;
		if ( mysql_select_db( $db_name, $this->dbh ) == false )
		{
			$this->dbselecterror( $db_name );
		}
	}

	function call( $db_name, $db_host = 'localhost', $db_user = 'www', $db_pwd = '' )
	{
		$this->connect( $db_host, $db_user, $db_pwd );
		$this->selectdb( $db_name );
	}

	function table( $name = "" )
	{
		if ( $name )
		{
			$handler = new table( $name );
			$handler->_name = $name;
		}
		else
		{
			$handler = new table();
		}

		$handler->_db = $this;
		return $handler;
	}

	function table_debug( $name )
	{
		$handler = new tabledebug();
		$handler->_name = $name;
		$handler->_db = $this;
		return $handler;
	}

	function handler( $name = "" )
	{
		if ( $name )
		{
			$handler = new table( $name );
			$handler->_name = $name;
		}
		else
		{
			$handler = new handler();
		}

		$handler->_db = $this;
		return $handler;
	}

	function query($sql)
	{
		$Result = mysql_query( trim( $sql ), $this->dbh );		
		
		if ( !$Result ) $this->queryerror( $this->dbh, $sql );
		else return $Result;
	}

	function close()
	{
		mysql_close($this->dbh);
	}

}

class dbconn_test extends dbconn
{
	function dbconn( $db_name='',$db_host='localhost',$db_user='www',$db_pwd='' )
	{
		$this->db_error();
		if ( $db_name ) $this->call( $db_name, $db_host, $db_user, $db_pwd );
	}

	function query($sql)
	{
		print "<div style='font-size:12px; font-weight:bold; color:green;'>".$sql."</div>\n";
	}
}

class handler {

	var $_db;
	var $_Result;

	function handler()
	{
	}

	function query( $sql )
	{
		$this->lastsql = $sql;

		$this->_Result = $this->_db->query( $sql );
	}

	function fetch()
	{
		$tmp = @mysql_fetch_array( $this->_Result );
		if ( !is_array( $tmp ) ) $this->free();
		return $tmp;
	}

	function maxrecord()
	{
		return mysql_num_rows( $this->_Result );
	}

	function seek($num)
	{
		mysql_data_seek( $this->_Result, $num ); 
	}

	function sql()
	{
		print $this->lastsql."<br>";
	}

	function numfields()
	{
	    return mysql_num_fields( $this->_Result ); 
	}

	function free()
	{
		@mysql_free_result( $this->_Result );
	}

	function getinsertid()
	{
		return mysql_insert_id( $this->_db->dbh );
	}

} // End class handler

class table extends handler
{
	var $_name;

	function table()
	{
		$this->handler();
	}

	function name( $name )
	{
		$this->_name = $name;
	}

	function seek( $num )
	{
		@mysql_data_seek( $this->_Result, $num );
	}
	
	function select( $field="*", $where="", $special="" )
	{
		if ( $where ) $where = "where $where";
		$this->query( "select $field from ".$this->_name." $where $special" );
	}

	function selectfetch( $tmp="", $field="*", $where="", $special="" )
	{
		if ( $where ) $where = "where $where";
		$this->query( "select $field from ".$this->_name." $where $special limit 1" );
		$data = $this->fetch();
		return $data;
	}

	function insert( $values, $fields="" )
	{
		if ( $fields ) $fields = "( $fields )";
		$this->query( "insert into ".$this->_name." $fields VALUES ( $values )" );
	}
        
        function get_test_insert( $values, $fields = "" )
        {
		if ( $fields ) $fields = "( $fields )";
		return "insert into ".$this->_name." $fields VALUES ( $values )";
        }

	function delete( $where )
	{
		return $this->query( "delete from ".$this->_name." where $where" );
	}

	function update( $set, $where )
	{
		return $this->query( "update ".$this->_name." set $set where $where" );
	}
        
        function get_test_update( $values, $fields = "" )
        {
		return "update ".$this->_name." set $set where $where";
        }

	function lastsql( $invisible = false )
	{
		$color = "black";
		if ( $invisible == true ) $color = "white";
		print "<div style='font-size:12px; font-family:verdana; color:".$color.";'><b>sql executed:</b> ".htmlspecialchars( $this->lastsql )."</div><br>";
	}

	function getlastsql()
	{
		return htmlspecialchars( $this->lastsql );
	}
        
        function truncate()
        {
                $this->query( "truncate table ".$this->_name );
        }

} // End class table

class rowedit
{
	var $data;
	var $fields;
	var $values;
	var $update;

	function rowedit()
	{
		$this->Init();
	}

	function init()
	{
		$this->data = "";
	}

	function data( $field, $value )
	{
		$this->data[ $field ] = $value;
		$this->process();
	}

	function process()
	{
		$this->fields = "";
		$this->values = "";
		$this->update = "";

		reset( $this->data );
		while( list( $key, $val ) = each ( $this->data ) )
		{
			// quote processing
			//$val = str_replace( "'", "\'", $val ); // localhost db supports magic quote
			//$val = str_replace( "\"", "\\\"", $val );

			$this->fields .= "$key, ";

			$val = addslashes( $val );

			if ( substr( $val, 0, 3 ) == "FN:" )
			{
				$this->values .= substr( $val, 3 ).", ";
				$this->update .= "$key=".substr( $val, 3 ).", ";
			}
			else
			{
				$this->values .= "'$val', ";
				$this->update .= "$key='$val', ";
			}
		}
		$this->fields = substr( $this->fields, 0, -2 );
		$this->values = substr( $this->values, 0, -2 );
		$this->update = substr( $this->update, 0, -2 );
	}

	function fields()
	{
		return $this->fields;
	}

	function values()
	{
		return $this->values;
	}

	function update()
	{
		return $this->update;
	}

} // End class rowedit

} // End Def

?>
