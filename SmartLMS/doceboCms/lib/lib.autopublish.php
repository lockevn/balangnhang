<?php

/*************************************************************************/
/* DOCEBO CMS - Content Management System                                */
/* ============================================                          */
/*                                                                       */
/* Copyright (c) 2005 by Giovanni Derks <virtualdarkness[AT]gmail-com>   */
/* http://www.docebocms.org                                              */
/*                                                                       */
/* This program is free software. You can redistribute it and/or modify  */
/* it under the terms of the GNU General Public License as published by  */
/* the Free Software Foundation; either version 2 of the License.        */
/*************************************************************************/


class CmsAutoPublish {

	var $table_info=array();
	
	function CmsAutoPublish() {
		
		$this->checkAutoPublish();
		
	}
	
	function initTableInfo() {
		
		$this->appendTableInfo("content", "publish", "pubdate", "expdate");
		$this->appendTableInfo("docs", "publish", "pubdate", "expdate");
		$this->appendTableInfo("links", "publish", "pubdate", "expdate");
		$this->appendTableInfo("media", "publish", "pubdate", "expdate");
		$this->appendTableInfo("news", "publish", "pubdate", "expdate");
		
	}

	function getTableInfo() {
		return (array)$this->table_info;
	}
	
	function appendTableInfo($tab_name, $publish, $pubdate, $expdate) {
		
		if ((!isset($this->table_info)) || (!is_array($this->table_info)))
			$this->table_info=array();
			
		if (count($this->table_info) < 1)
			$index=0;
		else
			$index=end(array_keys($this->table_info))+1;
			
		$this->table_info[$index]["name"]=$tab_name;
		$this->table_info[$index]["publish"]=$publish;
		$this->table_info[$index]["pubdate"]=$pubdate;
		$this->table_info[$index]["expdate"]=$expdate;
				
	}
	
	
	function saveLastCheckTime($time=FALSE) {
	
		if (($time === FALSE) || ($time == 0))
			$time=time();
	
		$qtxt ="UPDATE ".$GLOBALS["prefix_cms"]."_setting SET param_value='".$time."' ";
		$qtxt.="WHERE param_name='last_auto_publish'";
		$q=mysql_query($qtxt);
		
		$GLOBALS["cms"]["last_auto_publish"]=$time;
	}


	function checkAutoPublish() {
		
		if ($GLOBALS["cms"]["last_auto_publish"]+60 < time()) {
		
			$this->initTableInfo();
			$tab_info=$this->getTableInfo();			
			
			foreach ($tab_info as $key=>$val) {
				
				$qtxt ="UPDATE ".$GLOBALS["prefix_cms"]."_".$val["name"]." ";
				$qtxt.="SET ".$val["pubdate"]."=NULL, ".$val["publish"]."='1' ";
				$qtxt.="WHERE ".$val["pubdate"]." IS NOT NULL AND ".$val["pubdate"]." < NOW() ";
				$qtxt.="AND ".$val["publish"]." = '0'";
				
				$q=mysql_query($qtxt);
				//-debug-// echo $qtxt."<br /><br /><br />";
				
				
				$qtxt ="UPDATE ".$GLOBALS["prefix_cms"]."_".$val["name"]." ";
				$qtxt.="SET ".$val["publish"]."='0' ";
				$qtxt.="WHERE ".$val["pubdate"]." IS NOT NULL AND ".$val["pubdate"]." > NOW() ";
				$qtxt.="AND ".$val["publish"]." = '1'";
				
				$q=mysql_query($qtxt);
				//-debug-// echo $qtxt."<br /><br /><br />";
				
				
				$qtxt ="UPDATE ".$GLOBALS["prefix_cms"]."_".$val["name"]." ";
				$qtxt.="SET ".$val["expdate"]."=NULL, ".$val["publish"]."='0' ";
				$qtxt.="WHERE ".$val["expdate"]." IS NOT NULL AND ".$val["expdate"]." < NOW() ";
				$qtxt.="AND ".$val["publish"]." = '1'";
				
				$q=mysql_query($qtxt);
				//-debug-// echo $qtxt."<br /><br /><br />";				
				
			}
			
			$this->saveLastCheckTime();
		}
		
	}
	
	
}
	
?>
