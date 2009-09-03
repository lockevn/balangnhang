<?php //$Id: block_messages.php,v 1.13.4.4 2008/03/03 11:41:03 moodler Exp $

class block_messages extends block_base {
	function init() {
		$this->title = get_string('messages','message');
		$this->version = 2007101509;
	}

	function get_content() {
		global $USER, $CFG;
		global $tpl;

		if (!$CFG->messaging) {
			return ''; 
		}

		if ($this->content !== NULL) {
			return $this->content;
		}

		$this->content = new stdClass;
		$this->content->text = '';
		$this->content->footer = '';
		
		if (empty($this->instance) 		or empty($USER->id) 
		or isguest()         or empty($CFG->messaging)) 
		{
			return $this->content;
		}

		$tpl->assign('userid', $USER->id);
		$tpl->assign('courseid', $this->instance->pageid);		
		$this->content->text .= $tpl->fetch("~/blocks/messages/block_messages.tpl.php");
		
		$this->content->footer = '<a href="'.$CFG->wwwroot.'/message/index.php" onclick="this.target=\'message\'; return openpopup(\'/message/index.php\', \'message\', \'menubar=0,location=0,scrollbars,status,resizable,width=400,height=500\', 0);">'.get_string('messages', 'message').'</a>...';					
		return $this->content;
	}
}

?>