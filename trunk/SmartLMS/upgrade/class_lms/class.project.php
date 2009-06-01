<?php

class Upgrade_Project extends Upgrade {
	
	var $platfom = 'lms';
	
	var $mname = 'project';
	
	/**
	 * upgrade the module version
	 * @param string 	$start_version 	the start version, automaticaly prooceed to the next
	 *
	 * @return mixed 	true if the version jump was successful, else an array with an error code and an error message
	 * 					array( 'error_code', 'error_msg' )
	 **/
	function oneStepUpgrade($start_version) {
		
		switch($start_version) {
			case "3.0.1" : {
				
				$must_do = false;
				$query = "SHOW tables";
				$re_tables = $this->db_man->query($query);
				
				while(list($tab_name) = $this->db_man->fetchRow($re_tables)) {
					
					if($tab_name == 'learning_prj_msg') $must_do = true;
				}
				
				if($must_do === true) {
					
					$query_msg = "SELECT id, pid, mid, mfrom, mdate, msub, mfile, mtxt, mread
					FROM learning_prj_msg";
					$re_msg = $this->db_man->query($query_msg);
					while($data_msg = $this->db_man->fetchAssoc($re_msg)) {
					
						$query = "
						INSERT INTO learning_sysforum ( idMessage, key1, key2, key3, title, textof, posted, author, attach, locked ) VALUES (
							'".$data_msg['id']."',
							'project_message', 
							'".$data_msg['pid']."',
							NULL, 
							'".addslashes($data_msg['msub'])."',
							'".addslashes($data_msg['mtxt'])."',
							'".$data_msg['mdate']."',
							'".$data_msg['mfrom']."',
							'".addslashes($data_msg['mfile'])."',
							'0'
						)";
						$this->db_man->querySingle($query);
					}
				}
				$this->end_version = '3.0.2';
				return true;
			};break;
			
		}
		return true;
	}
}

?>