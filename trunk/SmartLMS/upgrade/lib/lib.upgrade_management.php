<?php

require_once($GLOBALS['where_upgrade'].'/lib/lib.domxml.php');

class UpdateManager {
	
	var $xml_doc = NULL;
	
	var $xpath;
	
	var $context = NULL;
	
	function UpdateManager() {}
	
	function openXmlDescription($file_xml) {
		
		$this->xml_doc = new DoceboDOMDocument();
		if(!$this->xml_doc) return false;
		if(!$this->xml_doc->load($file_xml)) return false;
		
		if(!$this->xpath = new DoceboDOMXPath($this->xml_doc)) return false;
		
		return true;
	}
	
	function getStartVersionList() {
		
		$start_versions = array();
		$NodeList_start_version = $this->xpath->query('/upgrade_info/upgrade_step/start_version');
		
		for($i = 0; $i < $NodeList_start_version->length; $i++) {
		
			$elem = $NodeList_start_version->item($i);
			$start_versions[$elem->textContent] = $elem->textContent;
		}
		return $start_versions;
	}
	
	function getMaxEndVersion() {
		
		$NodeList_start_version = $this->xpath->query('/upgrade_info/upgrade_step[last()]/end_version');
		$elem = $NodeList_start_version->item(0);
		return $elem->textContent;
	}
	
	function getModulesDescriptors() {
		
		$modules_descr = array();
		$NodeList_module = $this->xpath->query('/upgrade_info/module_set/module');
		for($i = 0; $i < $NodeList_module->length; $i++) {
			
			$module = $NodeList_module->item($i);
			
			$platform 	= $module->getAttribute('platform');
			$mname 		= $module->getAttribute('mname');
			
			$c_name = $this->xpath->query('class_name/text()', $module);
			$c_file= $this->xpath->query('class_file/text()', $module);
			$c_node = $c_name->item(0);
			$f_node = $c_file->item(0);
			
			$modules_descr[$platform.'-'.$mname] = array(
				'platform' => $platform,
				'mname' => $mname,
				'class_name' => $c_node->textContent,
				'class_file' => $f_node->textContent );
		}
		return $modules_descr;
	}
	
	function getVersionStepSequence($version) {
		
		$modules_seq = array();
		$xpath = '/upgrade_info/upgrade_step[start_version=\''.$version.'\']/module_upgrade_sequence/step';
		$NodeList_module = $this->xpath->query($xpath);
		
		if($NodeList_module->length == 0) return $modules_seq;
		for($i = 0; $i < $NodeList_module->length; $i++) {
		
			$module = $NodeList_module->item($i);
			
			$platform 	= $module->getAttribute('platform');
			$mname 		= $module->getAttribute('mname');
			$modules_seq[] = $platform.'-'.$mname;
		}
		return $modules_seq;
	}
	
	function getVersionListFrom($start_version) {
	
		$versions = array();
		$founded = false;
		$NodeList_start_version = $this->xpath->query('/upgrade_info/upgrade_step');
		for($i = 0; $i < $NodeList_start_version->length; $i++) {
			
			$up_step = $NodeList_start_version->item($i);
			$list_elem = $this->xpath->query('start_version/text()', $up_step);
			$elem = $list_elem->item(0);
			if($elem->textContent == $start_version) $founded = true;
			if($founded) $versions[$elem->textContent] = $elem->textContent;
		}
		return $versions;
	}
	
}

?>
