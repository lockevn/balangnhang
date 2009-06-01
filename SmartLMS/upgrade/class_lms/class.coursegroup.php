<?php

class Upgrade_Coursegroup extends Upgrade {
	
	var $platfom = 'lms';
	
	var $mname = 'Coursegroup';
	
	function _registerGroup($groupid) {
		
		$query = "
		INSERT INTO core_st ( idst ) VALUES ( '' );
		INSERT INTO core_group "
		." (idst, groupid, description, hidden, type, show_on_platform ) VALUES ( LAST_INSERT_ID(), '".$groupid."', '' ,'false', 'course', 'lms,' );";
		$this->db_man->query($query);
		
		return $this->db_man->lastInsertId();
	}
	
	function _registerGroupMembers($id_user, $id_group) {
		
		$query = "INSERT INTO core_group_members "
					."( idst, idstMember ) "
					."VALUES ( '".$id_group."','".$id_user."' )";
		$this->db_man->query($query);
	}
	
	/**
	 * upgrade the module version
	 * @param string 	$start_version 	the start version, automaticaly prooceed to the next
	 *
	 * @return mixed 	true if the version jump was successful, else an array with an error code and an error message
	 * 					array( 'error_code', 'error_msg' )
	 **/
	function oneStepUpgrade($start_version) {
		
		switch($start_version) {
			case "2.0.4" : {
				
				$query = "
				SELECT idGroup, idCourse, groupName, description, owner, level
				FROM learning_coursegroup";
				$re_groups = $this->db_man->query($query);
				
				while($data = $this->db_man->fetchArray($re_groups)) {
					
					$id_group = $this->_registerGroup('/lms/course/'.$data['idCourse'].'/group/'.$data['groupName']);
					
					$query = "
					UPDATE `learning_prj` 
					SET `pgroup` = '".$id_group."' 
					WHERE pgroup = '".$data['idGroup']."'";
					$this->db_man->query($query);
					
					$query = "
					SELECT idUser 
					FROM learning_coursegroupuser 
					WHERE idGroup = '".$data['idGroup']."'";
					$re_memb = $this->db_man->query($query);
					while(list($id_user) = $this->db_man->fetchRow($re_memb)) {
						
						$this->_registerGroupMembers($id_user, $id_group);
					}
				}
				
				$query = "DROP TABLE learning_coursegroup;";
				$this->db_man->query($query);
				$query = "DROP TABLE learning_coursegroupuser;";
				$this->db_man->query($query);
				
				$this->end_version = '3.0';
				return true;
			};break;
			
		}
		return true;
	}
}

?>