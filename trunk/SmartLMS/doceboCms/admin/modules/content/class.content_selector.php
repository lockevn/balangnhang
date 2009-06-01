<?php
/*************************************************************************/
/* DOCEBO CMS - Content Management System                                */
/* =============================================                         */
/*                                                                       */
/* Copyright (c) 2006 by Giovanni Derks <virtualdarkness[AT]gmail-com>   */
/* http://www.docebocms.org                                              */
/*                                                                       */
/* This program is free software. You can redistribute it and/or modify  */
/* it under the terms of the GNU General Public License as published by  */
/* the Free Software Foundation; either version 2 of the License.        */
/*************************************************************************/

require_once($GLOBALS["where_cms"]."/admin/modules/content/content_class.php");



class Selector_Content_TreeView extends Content_TreeView {


	var $node_perm=array();



	function setNodePerm($node_perm) {
		$this->node_perm=$node_perm;
	}


	function getNodePerm() {
		return (is_array($this->node_perm) ? $this->node_perm : array());
	}


	function printElement(&$stack, $level) {
		$elem=parent::printParentElement($stack, $level);

		require_once($GLOBALS['where_framework'].'/lib/lib.form.php');
		$lang=& DoceboLanguage::createInstance("admin_manager", "framework");

		$id=$stack[$level]['folder']->id;


		$sel_val="no";
		$node_perm=$this->getNodePerm();
		if (in_array($id, $node_perm["normal"])) {
			$sel_val="yes";
		}
		else if (in_array($id, $node_perm["recursive"])) {
			$sel_val="inherit";
		}

		$checked=($sel_val == "inherit" ? "checked=\"checked\"" : "");
		$elem.=Form::getLabel('sel_inherit_'.$id, $lang->def("_INHERIT"),
					 'label_bold tree_view_image');
		$elem.= '<input type="radio" class="tree_view_image"'
				.' id="sel_inherit_'.$id.'" '
				.' name="sel_type['.$id.']" '
				.' value="inherit"'
				.$checked." />";

		$checked=($sel_val == "yes" ? "checked=\"checked\"" : "");
		$elem.=Form::getLabel('sel_yes_'.$id, $lang->def("_YES"),
					 'label_bold tree_view_image');
		$elem.= '<input type="radio" class="tree_view_image"'
				.' id="sel_yes_'.$id.'" '
				.' name="sel_type['.$id.']" '
				.' value="yes"'
				.$checked." />";


		$checked=($sel_val == "no" ? "checked=\"checked\"" : "");
		$elem.=Form::getLabel('sel_no_'.$id, $lang->def("_NO"),
					 'label_bold tree_view_image');
		$elem.= '<input type="radio" class="tree_view_image"'
				.' id="sel_no_'.$id.'" '
				.' name="sel_type['.$id.']" '
				.' value="no"'
				.$checked." />";

		return $elem;
	}



}


?>
