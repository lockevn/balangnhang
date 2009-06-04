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
	private $m_eid;
	private $m_shardentity;
	/**
	*@desc Use with care, use when you use DBLink_ByDatabase() only
	*/
	public function SetEntityID($p_eid)
	{
		$this->m_eid = $p_eid;
	}
	/**
	*@desc Use with care, use when you use DBLink_ByDatabase() only
	*/
	public function SetShardEntity($p_shardentity)
	{
		$this->m_shardentity = $p_shardentity;
	}




	// private $oShardHelper;

	private static $instance;


	protected function DBHelper()
	{	}


	public static function GetDBHelperInstance()
	{
		if(!isset(DBHelper::$instance) || DBHelper::$instance->mysqli == null)
		{
			DBHelper::$instance = new DBHelper();
		}
		return DBHelper::$instance;
	}

	/**
	* @author dannm, LockeVN
	* @desc return dblink to database which contains non-shard tables. Use ShardConfig::$NonShardEntity_DB_Name_Mapping
	* @param enity: entity name
	* @returns databaselink if OK, false if not.
	*/
	function DBLinkNonShard($entity)
	{
		if(array_key_exists($entity, ShardConfig::$NonShardEntity_DB_Name_Mapping))
		{
			// HACK: LockeVN: $this->dbname = ShardConfig::$NonShardEntity_DB_Name_Mapping[$entity];
			$this->dbname = ShardHelper::GetDB(0, $entity);
		}
		else
		{
			return false;
		}


		$ServerInfo = ShardHelper::GetServer($this->dbname);
		if( $this->dbhost == $ServerInfo['address'] &&
			$this->dbuser == $ServerInfo['username'] &&
			$this->dbpass == $ServerInfo['password'])
		{
			return $this->ChangeDB();
		}
		else
		{
			$this->dbhost = $ServerInfo['address'];
			$this->dbuser = $ServerInfo['username'];
			$this->dbpass = $ServerInfo['password'];
			return $this->OpenDBConnection();
		}
	}


	/**
	* @author LockeVN
	* @desc link to server, select database, base on shardentityID.
	* Eg: DBLink(1, ENTITY_USER) will link to database contain user.userid = 1,
	* @returns database link
	*/
	public function DBLink($p_eid, $p_shardentity = ENTITY_USER)
	{
		if(!$this->IsNewConnectionRequired($p_eid, $p_shardentity))
		{
			return $this->ChangeDB();
		}
		else
		{
			$ServerInfo = ShardHelper::GetServer($this->dbname);
			$this->dbhost =  $ServerInfo['address'];
			$this->dbuser = $ServerInfo['username'];
			$this->dbpass = $ServerInfo['password'];
			return $this->OpenDBConnection();
		}
	}
	
	/**
	* @author dannm
	* @desc link to DB server specified by serverinfo{address, username, password, dbname} 
	* @returns databaselink
	*/
	public function DBLink_ByServerInfo($serverInfo)
	{
		$this->dbhost = $serverInfo['address'];
		$this->dbpass = $serverInfo['password'];
		$this->dbuser = $serverInfo['username'];
		$this->dbname = $serverInfo['dbname'];
		return $this->OpenDBConnection();
	}


	/**
	* @author LockeVN
	* @desc link to server, select database. Use with carefully. This function is design for use with NonShardEntity (Archived, Setting) or ShardEntity (ENTITY_USER, ENTITY_GROUP.
	* You will get failure if DBLInk_ByDatabase, and then GetRecord of ShardingEntity (Msg, MsgGroup, Friend, ...). If you really want to use with ShardingEntity, set your own p_eid and shardEntity by using SetEntityID() and SetShardEntity()
	* @returns databaselink
	*/
	public function DBLink_ByDatabase($DB)
	{
		$this->dbname = $DB;
		$ServerInfo = ShardHelper::GetServer($this->dbname);
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
			$this->mysqli->select_db($this->dbname);
		}
		else
		{
			$this->OpenDBConnection();
		}

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
	* @param mixed from what?
	* @param mixed field list
	* @param mixed where condition
	* @param mixed order
	* @param mixed page, start from 1
	* @param mixed item per page
	* @return mixed array of assocarray, each assocarray is a row
	*/
	function GetRecords($fromEntity, $fl, $where='', $order='', $page, $ipp)
	{
		/***** SHARDING ****/
		$table = ShardHelper::GetTable($fromEntity, $this->m_eid, $this->m_shardentity);
		/***** SHARDING ****/

		$q = "select $fl from `$table` ";

		if($where)
		{
			$q .= " where $where";
		}
		if($order)
		{
			$q .= " order by $order";
		}


		if(empty($page))
		{
			$page = 1;
		}

		// this check can be omit, because of we check in URLParamHelper.GetITEMPERPAGE()
		// remove this check if API work OK for more 5 revision.
		// HACK: LockeVN: revision 2000, 07/02/2009
		if(empty($ipp))
		{
			$ipp = APIConfig::EXTERNAL_DEFAULT_RECORD_PER_QUERY;
		}


		$start = ($page-1)*$ipp;
		$q .= " limit $start,$ipp";

		$result = $this->mysqli->query($q);
		return DBHelper::GetAssocArray($result);
	}

	function GetRecordsWithJoin($fromEntity, $join, $fl, $where='', $order='', $page, $ipp) {
		 /***** SHARDING ****/
		$table = ShardHelper::GetTable($fromEntity, $this->m_eid, $this->m_shardentity);
		/***** SHARDING ****/

		$q = "select $fl from `$table` ";

		if($join)
		{
			$q .= " $join";
		}

		if($where)
		{
			$q .= " where $where";
		}
		if($order)
		{
			$q .= " order by $order";
		}


		if(empty($page))
		{
			$page = 1;
		}
		if(empty($ipp))
		{
			$ipp = APIConfig::EXTERNAL_DEFAULT_RECORD_PER_QUERY;
		}

		$start = ($page-1)*$ipp;
		$q .= " limit $start,$ipp";

		$result = $this->mysqli->query($q);
		return DBHelper::GetAssocArray($result);
	}

	/**
	* @desc get only first record
	*/
	function GetRecord($fromEntity, $fl, $where='')
	{
		return $this->GetRecords($fromEntity, $fl, $where, '', 1, 1);
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
	*@desc
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

}   // end class

?>