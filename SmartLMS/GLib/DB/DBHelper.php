<?php require_once($_SERVER['DOCUMENT_ROOT']."/config.php");


class DBHelper
{
	private $dbhost;
	private $dbname;
	private $dbuser;
	private $dbpass;

	/**
	*@desc a mysqli object
	*/
	public $mysqli;
	
	private static $instance;	
	protected function DBHelper()
	{	}


	public static function GetInstance()
	{
		if(!isset(DBHelper::$instance) || DBHelper::$instance->mysqli == null)
		{
			DBHelper::$instance = new DBHelper();
		}
		return DBHelper::$instance;
	}

	

	/**
	* @author LockeVN
	* @desc STATIC FUNCTION. Get ServerInfo by DB name. Server = f(DB).
	* @param string DB name
	* @returns ServerInfo as asocciate array('address'=>,'username'=>,'password'=>)
	*/
	public final function GetServer($DB)
	{
		$ServerInfo = GCONFIG::$DB_Server_Mapping[$DB];

		if($ServerInfo == false)
		{
			die("<error>SEngine: DBServerMapping error, no server is configured for [$DB]</error>");			
		}
		
		return $ServerInfo;
	}
	

	/**
	* @author LockeVN
	* @desc link to server, select database. Use with carefully. This function is design for use with NonShardEntity (Archived, Setting) or ShardEntity (ENTITY_USER, ENTITY_GROUP.
	* You will get failure if DBLInk_ByDatabase, and then GetRecord of ShardingEntity (Msg, MsgGroup, Friend, ...). If you really want to use with ShardingEntity, set your own p_eid and shardEntity by using SetEntityID() and SetShardEntity()
	* @returns databaselink
	*/
	public function DBLink($DB)
	{
		$this->dbname = $DB;
		$ServerInfo = $this->GetServer($this->dbname);
		if($this->dbhost ==  $ServerInfo['address'] &&
			$this->dbuser == $ServerInfo['username'] &&
			$this->dbpass == $ServerInfo['password'])
		{
			return $this->ChangeDB();
		}
		else
		{
			$this->dbhost =  $ServerInfo['address'];
			$this->dbuser = $ServerInfo['username'];
			$this->dbpass = $ServerInfo['password'];
			return $this->OpenDBConnection();
		}
	}


	private function OpenDBConnection()
	{
		$this->mysqli = new mysqli($this->dbhost, $this->dbuser, $this->dbpass);
		if ($this->mysqli->connect_error)
		{
			die("MessioDBEngine: unable to connect to MySQL Server");
		}

		if ($this->mysqli->select_db($this->dbname) == false)
		{
			die("MessioDBEngine: unable to select MySQL Database");
		}

		$this->mysqli->set_charset('utf8');
		return $this->mysqli;
	}


	/**
	*@desc get serverinfo by p_eid and shardentity.
	* If that serverinfo still equals to current dbinfo (address,u,p), return false, no new connection require.
	* This DBhelper setting still OK, we save time.
	*/
	function IsNewConnectionRequired($p_eid, $p_shardentity = ENTITY_USER)
	{
		$this->m_eid = $p_eid;
		$this->m_shardentity = $p_shardentity;
		$this->dbname = ShardHelper::GetDB($this->m_eid, $this->m_shardentity);
		$ServerInfo = ShardHelper::GetServer($this->dbname);

		if($this->dbhost ==  $ServerInfo['address'] &&
			$this->dbuser == $ServerInfo['username'] &&
			$this->dbpass == $ServerInfo['password'])
		{
			return false;
		}
		else
		{
			return true;
		}
	}


	function ChangeDB()
	{
		if($this->mysqli->ping())
		{			
		}
		else
		{
			$this->OpenDBConnection();            
		}
		$this->mysqli->select_db($this->dbname);
		return $this->mysqli;
	}


	function CloseDBLink()
	{
		if($this->mysqli != null)
		{
			$this->mysqli->close();

		}
	}








	/**
	* @author LockeVN
	* @desc fetched data from database	
	* @param string SQL query string
	* @return mixed array of assocarray, each assocarray is a row
	*/
	function GetRecords($q)
	{
		$result = $this->mysqli->query($q);
		return DBHelper::GetAssocArray($result);
	}
	


	


	
	

	/**
	* @author dannm
	* @desc insert a business object into the corresponding table
	* @param $fromEntity: entity name; $object: business object
	* @returns new record id if succeed, else 0
	*/
	function InsertObject($fromEntity, $object)
	{
		//number of sharding tables for this entity
/*        $shardNumber = ShardConfig::$Entity_AmountShardTable[$fromEntity];
		-------- if no sharding ----------
		if($shardNumber == 1) {
			$tableName = ShardConfig::$Entity_Table_Mapping[$fromEntity];
		}
		-------- if sharding ----------
		else*/

		// TODO: please recheck, this function must accept shardentity, to provide for GetTable function.
		$tableName = ShardHelper::GetTable($fromEntity, $this->m_eid);

		/*-------- construct field list string ---------*/
		$insertStr = "INSERT INTO `$tableName` (";
		$arr = $object->toArray();
		foreach ($arr as $key => $value)
			if(!is_null($value))
				$insertStr .= $key . ",";
		$insertStr = rtrim($insertStr, ",") . ")";
		$insertStr .= "VALUES(" ;
		$arr = $object->toArray();
		foreach ($arr as $key => $value) {
			if(!is_null($value)) {
				$dataType = DBEntityFieldMapping::$EntityField_Type[$fromEntity][$key];
				if($dataType == "s") //type string
					$insertStr .= "'$value',";
				else if($dataType == "i") //type int
					$insertStr .= "$value,";
			}
		}
		$insertStr = rtrim($insertStr, ",") . ")";
		if($this->mysqli->query($insertStr)) {
			$newid = $this->mysqli->insert_id;
			return $newid;
		}
		return 0;
	}

	/**
	* @author dannm
	* @desc update a business object into the corresponding table
	* @param $fromEntity: entity name; $object: business object
	* @returns new record id if succeed, else 0
	*/
	function UpdateObject($fromEntity, $object, $where='') {
		$tableName = ShardHelper::GetTable($fromEntity, $this->m_eid);
		$updateStr = "UPDATE `$tableName` SET ";
		$arr = $object->toArray();
		foreach ($arr as $key => $value) {
			if($key != "id" && !is_null($value) && $value != '')     {
				$dataType = DBEntityFieldMapping::$EntityField_Type[$fromEntity][$key];
				if($dataType == "s") { //type string
					$updateStr .=  $key . '=' . "'" . trim($value) . "',";
				}
				else if($dataType == "i") { //type int
					$updateStr .= $key . '=' . trim($value) . ',';
				}
			}
		}
		$updateStr = rtrim($updateStr, ",");
		if(empty($where))
		{
			$updateStr .= " WHERE id=".$object->getId();	
		}
		else
		{
			$updateStr .= " $where";
		}
		$result = $this->mysqli->query($updateStr); 
		if($result) 
		{
			if(empty($where))
			{
				return $object->getId();
			}
			else
			{
				return $result;
			}
		}
		return 0;
	}

	function DeleteObject($fromEntity, $object, $where='') {

		$tableName = ShardHelper::GetTable($fromEntity, $this->m_eid);
		if(empty($where))
		{
			if($fromEntity == ENTITY_MSG || $fromEntity == ENTITY_MSG_ARCHIVED)
			{
				$deleteStr = "DELETE FROM `$tableName` WHERE guid='". $object->getGUId() . "'";
			}
			else
			{
				$deleteStr = "DELETE FROM `$tableName` WHERE id=". $object->getId();
			}

			if($this->mysqli->query($deleteStr))
			{

				//return $object->getId();
				return $this->mysqli->affected_rows;
			}
		}
		else
		{
			$deleteStr = "DELETE FROM `$tableName` $where";
			$result = $this->mysqli->query($deleteStr);
			if($result)
			{
				return $this->mysqli->affected_rows;
			}
		}

		return 0;
	}

	function CountRecord($result)
	{
		if(!$result)
		{
			return false;
		}

		$rec_count = $result->num_rows;

		return $rec_count;
	}

	
	
	
	
	/**
	* @author dannm
	* @desc execute a stored procedure.
	* @param stored procedure call string
	* @returns array result
	*/
	function ExecuteStoredProcedure($sql)
	{
		//$this->mysqli = new mysqli($this->dbhost, $this->dbuser, $this->dbpass, $this->dbname);
		if($this->mysqli->multi_query($sql))
		{
			$result = $this->mysqli->store_result();
			$array = $result->fetch_array();
			$result->close();
			return $array;
		}
		return 0;
	}

	 /**
	* @author lockevn
	* @desc execute sql string, return resultset
	* @param string sql
	* @returns true on success, false on failure
	*/
	function ExecuteNonQuery($sql)
	{
		return $this->mysqli->query($sql) ;
	}

	/**
	*@desc run query, return single object
	* @return object first object returned
	*/
	public function ExecuteAggregate($querystring)
	{
		$dbresult = $this->mysqli->query($querystring) ;
		if(!$dbresult)
		{
			return -1;
		}
		$rec = $dbresult->fetch_row();
		$dbresult->close();
		return $rec[0];
	}

	
	
	/**
	* @author LockeVN
	* @desc STATIC FUNCTION. fetch dbresult to assoc array, free result.
	* @param resource dbresult
	* @returns array of assoc array. each assoc array is a record as database row.
	*/
	public static function GetAssocArray($dbresult)
	{
		if(!$dbresult)
		{
			return false;
		}

		while ($rec = $dbresult->fetch_assoc())
		{
			$arrRecs[] = $rec; // append to end of array
		}
		$dbresult->close();

		return $arrRecs;
	}
	
	
}   // end class

?>