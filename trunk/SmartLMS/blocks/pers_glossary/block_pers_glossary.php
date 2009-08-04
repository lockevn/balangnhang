<?php

class block_pers_glossary extends block_base
{
	function init()
	{
		$this->title = get_string('blockname', 'block_pers_glossary');
		$this->version = 2007052000;
	}
	function get_content()
	{
		global $CFG;
		$this->content = new stdClass;
		$this->content->text = '<a href="'.$CFG->wwwroot.'/blocks/pers_glossary/glossary.php">'.get_string("openglossary",'block_pers_glossary').'</a><br />';
		$this->content->text .= '<a href="'.$CFG->wwwroot.'/blocks/pers_glossary/quiz.php">'.get_string('startquiz','block_pers_glossary').'</a><br />';
		$this->content->text .= '<br />';
		$this->content->text .= '<a href="'.$CFG->wwwroot.'/blocks/pers_glossary/newlang.php">'.get_string('newlang','block_pers_glossary').'</a><br />';
		$this->content->footer = '';
		return $this->content;
	}
	function has_config()
	{
		return false;
	}

	function applicable_formats() {
		return array('my'=>true);
	} // function applicable_formats()
}

?>
