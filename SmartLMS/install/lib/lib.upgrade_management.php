<?php

class UpdateManager {

	var $xml_doc = NULL;

	var $context = NULL;

	function UpdateManager() {}

	function openXmlDescription($file_xml) {

		if(!$this->xml_doc = domxml_open_file($file_xml)) return false;

		if(!$this->context = $this->xml_doc->xpath_new_context()) return false;
		$root = $this->xml_doc->document_element();

		return true;
	}

	function getStartVersionList() {

		$start_versions = array();
		$arr_start_version = $this->context->xpath_eval('//upgrade_info/upgrade_step');
		foreach($arr_start_version->nodeset as $up_step) {

			$elem = $this->context->xpath_eval('start_version/text()', $up_step);
			$start_versions[$elem->nodeset[0]->content] = $elem->nodeset[0]->content;
		}
		return $start_versions;
	}

	function getMaxEndVersion() {

		$arr_end_version = $this->context->xpath_eval('//upgrade_info/upgrade_step[last()]');
		$elem = $this->context->xpath_eval('end_version/text()', $arr_end_version->nodeset[0]);
		return $elem->nodeset[0]->content;
	}

	function getModulesDescriptors() {

		$modules_descr = array();
		$arr_module = $this->context->xpath_eval('//upgrade_info/module_set/module');
		foreach($arr_module->nodeset as $module) {

			$platform 	= $module->get_attribute('platform');
			$mname 		= $module->get_attribute('mname');

			$c_name = $this->context->xpath_eval('class_name/text()', $module);
			$c_file= $this->context->xpath_eval('class_file/text()', $module);

			$modules_descr[$platform.'-'.$mname] = array(
				'platform' => $platform,
				'mname' => $mname,
				'class_name' => $c_name->nodeset[0]->content,
				'class_file' => $c_file->nodeset[0]->content );

		}
		return $modules_descr;
	}

	function getVersionStepSequence($version) {

		$modules_seq = array();
		$xpath = '//upgrade_info/upgrade_step/start_version[contains(. , \''.$version.'\')]/../module_upgrade_sequence/step';
		$arr_module = $this->context->xpath_eval($xpath);

		if(!$arr_module) return $modules_seq;
		foreach($arr_module->nodeset as $step) {

			$platform 	= $step->get_attribute('platform');
			$mname 		= $step->get_attribute('mname');

			$modules_seq[] = $platform.'-'.$mname;
		}
		return $modules_seq;
	}

	function getVersionListFrom($start_version) {

		$versions = array();
		$founded = false;
		$arr_start_version = $this->context->xpath_eval('//upgrade_info/upgrade_step');
		foreach($arr_start_version->nodeset as $up_step) {

			$elem = $this->context->xpath_eval('start_version/text()', $up_step);
			if($elem->nodeset[0]->content == $start_version) $founded = true;
			if($founded) $versions[$elem->nodeset[0]->content] = $elem->nodeset[0]->content;
		}
		return $versions;
	}

}

?>
