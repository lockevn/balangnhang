<?php
/* $Id: mysqlupgrade.php,v 1.3 2005/01/31 22:04:02 shimon Exp $ */
// upgrade CHARACTER SET for MySQL 4.1.0 +
// 
// Did you export all databases including mysql database before runing this file ?
//
// known bug of this program it dont know to treat FULLTEXT index
//
//by Shimon Doodkin shimon_d@hotmail.com
/*
$charset 					= "utf8";
$collate 					= "utf8_general_ci";
*/

function PMA_getDbCollation($db_conn, $db) {
	$sq='SHOW CREATE DATABASE `'.$db.'`;';
	$res = $db_conn->query($sq);
	if(!$res) {
		
		echo "\n\n".$sq."\n".mysql_error()."\n\n"; 
	} elseif($row = mysql_fetch_row($res)) {
		
		$tokenized = explode(' ', $row[1]);
		unset($row, $res, $sq);
		for ($i = 1; $i + 3 < count($tokenized); $i++) {
			
			if ($tokenized[$i] == 'DEFAULT' && $tokenized[$i + 1] == 'CHARACTER' && $tokenized[$i + 2] == 'SET') {
				
				if (isset($tokenized[$i + 5]) && $tokenized[$i + 4] == 'COLLATE') {
     				
					return array($tokenized [$i + 3],$tokenized[$i + 5]); // We found the collation!
				} else {
					
					return array($tokenized [$i + 3]);
				}
				
			} // end if
			
		} // end for
		
	} // end if
	
	return '';
}

function convert_utf($db_conn, $dbname, $collate, $charset, $alter_without_bin = false) {
	
	$printonly 					= false;
	$altertablecharset 			= true;
	$alterdatabasecharser 		= true;
	
	$ignore_list = array(
		'core_lang_text', 
		'core_lang_text_translation',
		'core_event_property', 
		'learning_scorm_items_track',
		'learning_scorm_tracking',
		'learning_tracksession',
		'learning_trackingeneral'
	);
	
	$ignore_list = array_flip($ignore_list);
		
	$rs = $db_conn->query("SHOW TABLES"); 
	if(!$rs) {
		echo mysql_error()."\n";
		return false;
	}
	while($data = mysql_fetch_row($rs)) {
		
		$rs1 = $db_conn->query("show FULL columns from $data[0]"); 
		if(!$rs1) echo mysql_error()."\n\n"; else
		
		while ($data1 = mysql_fetch_assoc($rs1)) {
						
			if(in_array(array_shift(split("\\(",$data1['Type'],2)), array(	'char',
																			'varchar',
																			'tinytext',
																			'text',
																			'mediumtext',
																			'longtext',
																			'enum',
																			'set' )))  {
			   
			  
				if(substr($data1['Collation'],0,4)!='utf8') {
						
					if(!isset($ignore_list[$data[0]]) && $alter_without_bin == false) { 			
					
						$sq="ALTER TABLE `$data[0]` CHANGE `".$data1['Field'].'` `'.$data1['Field'].'` '.$data1['Type'].' CHARACTER SET binary '.($data1['Default']==''?'':($data1['Default']=='NULL'?' DEFAULT NULL':' DEFAULT \''.mysql_escape_string($data1['Default']).'\'')).($data1['Null']=='YES'?' NULL ':' NOT NULL').';';
						$re = $db_conn->query($sq);
						//echo $sq.'<br />';
						if(!$printonly && !$re) {
							//echo "\n\n".$sq."\n".mysql_error()."\n\n"; 
						} 
					} else {
						$re = true;
					}
					if($re) {
						$sq="ALTER TABLE `$data[0]` CHANGE `".$data1['Field'].'` `'.$data1['Field'].'` '.$data1['Type']." CHARACTER SET $charset ".($collate==''?'':"COLLATE $collate").($data1['Default']==''?'':($data1['Default']=='NULL'?' DEFAULT NULL':' DEFAULT \''.mysql_escape_string($data1['Default']).'\'')).($data1['Null']=='YES'?' NULL ':' NOT NULL').($data1['Comment']==''?'':' COMMENT \''.mysql_escape_string($data1['Comment']).'\'').';';
					    if(!$printonly) if(!$db_conn->query($sq)) {
					    //	 echo "\n\n".$sq."\n".mysql_error()."\n\n";
					    }
					}
				}
			}
		} // end while
		if($altertablecharset) {
			/*
			$sq='ALTER TABLE `'.$data[0]."` DEFAULT CHARACTER SET binary";
			echo ($sq."\n") ; 
			if(!mysql_query($sq)) echo "\n\n".$sq."\n".mysql_error()."\n\n";
			*/
			$sq='ALTER TABLE `'.$data[0]."` DEFAULT CHARACTER SET $charset ".($collate==''?'':"COLLATE $collate");
			//echo $sq.'<br />';
			if(!$printonly) if(!$db_conn->query($sq)) echo "\n\n".$sq."\n".mysql_error()."\n\n";
		}
		
	} // end while
	if($alterdatabasecharser) {
		/*
		$sq='ALTER DATABASE `'.$data2[0]."` DEFAULT CHARACTER SET binary";
		echo ($sq."\n") ; 
		if(!mysql_query($sq)) echo "\n\n".$sq."\n".mysql_error()."\n\n";
		*/ 
		$sq='ALTER DATABASE `'.$dbname."` DEFAULT CHARACTER SET $charset ".($collate==''?'':"COLLATE $collate");
		//echo $sq.'<br />';
		if(!$printonly) if(!$db_conn->query($sq)) echo "\n\n".$sq."\n".mysql_error()."\n\n";
	}
	return '';
}

?>
