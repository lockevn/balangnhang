<?php

class Upgrade_Reportscore extends Upgrade {
	
	var $platfom = 'lms';
	
	var $mname = 'reportscore';
	
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
				
				$query = "CREATE TABLE `learning_coursereport` (
				  `id_report` int(11) NOT NULL auto_increment,
				  `id_course` int(11) NOT NULL default '0',
				  `title` varchar(255) NOT NULL default '',
				  `max_score` int(11) NOT NULL default '0',
				  `required_score` int(11) NOT NULL default '0',
				  `weight` int(3) NOT NULL default '0',
				  `show_to_user` enum('true','false') NOT NULL default 'true',
				  `use_for_final` enum('true','false') NOT NULL default 'true',
				  `sequence` int(3) NOT NULL default '0',
				  `source_of` enum('test','activity','final_vote') NOT NULL default 'test',
				  `id_source` int(11) NOT NULL default '0',
				  PRIMARY KEY  (`id_report`)
				)";
				if(!$this->db_man->query($query)) return $this->_getErrArray($start_version, 1);
				
				$query = "CREATE TABLE `learning_coursereport_score` (
				  `id_report` int(11) NOT NULL default '0',
				  `id_user` int(11) NOT NULL default '0',
				  `date_attempt` datetime NOT NULL default '0000-00-00 00:00:00',
				  `score` double(5,2) NOT NULL default '0.00',
				  `score_status` enum('valid','not_checked','not_passed','passed') NOT NULL default 'valid',
				  `comment` text NOT NULL,
				  PRIMARY KEY  (`id_report`,`id_user`)
				)";
				if(!$this->db_man->query($query)) return $this->_getErrArray($start_version, 2);
				
				$query = "
				SELECT id, idref, val
				FROM learning_pagel_vote_items ";
				$re_votes = $this->db_man->query($query);
				while(list($id, $idref, $val) = $this->db_man->fetchRow($re_votes)) {
					
					$vote_scale[$idref][$id] = $val;
					if(!isset($vote_scale[$idref]['max_score'])) $vote_scale[$idref]['max_score'] = $val;
					elseif($vote_scale[$idref]['max_score'] < $val) {
						
						$vote_scale[$idref]['max_score'] = $val;
					}
				}
				
				$index = array();
				$query = "
				SELECT id, idc, dsc
				FROM learning_pagel_atvt";
				$re_activities = $this->db_man->query($query);
				while(list($id, $id_course, $descr) = $this->db_man->fetchRow($re_activities)) {
					
					if(!isset($index[$id_course])) $index[$id_course] = 1;
					else $index[$id_course]++;
					
					$query = "INSERT INTO learning_coursereport 
					( id_report, id_course, title, max_score, required_score, weight, show_to_user, use_for_final, sequence, source_of, id_source ) VALUES 
					( 	'".$id."',
						'".$id_course."', 
						'".$descr."', 
						'', 
						'', 
						'100',
						'true', 
						'false', 
						'".$index[$id_course]."', 
						'activity',
						'0' ) ";
					$this->db_man->query($query);
				}
				
				
				$query = "
				SELECT idc, iduser, idatvt, idcatval, idvote, adate, title, comment, status
				FROM learning_pagel";
				$re_activities = $this->db_man->query($query);
				while($data = $this->db_man->fetchArray($re_activities)) {
					
					$query = "INSERT INTO learning_coursereport_score 
					( id_report, id_user, date_attempt, score, score_status, comment ) VALUES 
					( 	'".$data['idatvt']."',
						'".$data['iduser']."', 
						'".$data['adate']."', 
						'".$vote_scale[$data['idcatval']][$data['idvote']]."', 
						'".( $data['status'] ? 'valid' : 'not_checked' )."',
						'".addslashes($data['comment'])."' ) ";
					
					$this->db_man->query($query);
					
					$query = "
					UPDATE learning_coursereport
					SET max_score = '".$vote_scale[$data['idcatval']]['max_score']."' 
					WHERE id_report = '".$data['idatvt']."'";
					$this->db_man->query($query);
				}
				
				$this->end_version = '3.0';
				return true;
			};break;
			case "3.0.4" : {
				
				$query = "ALTER TABLE `learning_coursereport` CHANGE `max_score` `max_score` FLOAT( 11 ) DEFAULT '0' NOT NULL";
				if(!$this->db_man->query($query)) return $this->_getErrArray($start_version, 1);
				
				$query = "ALTER TABLE `learning_coursereport` CHANGE `required_score` `required_score` FLOAT( 11 ) DEFAULT '0' NOT NULL ;";
				if(!$this->db_man->query($query)) return $this->_getErrArray($start_version, 2);
				
				$this->end_version = '3.0.5';
				return true;
			};break;
			case "3.0.6" : {
				$i = 0;
				
				$content = "ALTER TABLE `learning_coursereport` CHANGE `source_of` `source_of` ENUM( 'test', 'activity', 'scorm', 'final_vote' ) DEFAULT 'test' NOT NULL";
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, ++$i);
				
				$content = "ALTER TABLE `learning_coursereport` CHANGE `id_source` `id_source` VARCHAR( 255 ) DEFAULT '0' NOT NULL";
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, ++$i);
		
				$this->end_version = '3.5.0';
				return true;
			};break;
		}
		return true;
	}
}

?>