<? 
/** To inspect the RESTful URL of vdict dictionary, inspect the javascript in this http://vdict.com/voys.php
* When I read http://vdict.com/voys.php, I see http://js.vdict.com/searchform.js
* Read http://js.vdict.com/searchform.js, I have: 
* http://vdict.com/fsearch.php?word=hello&dictionaries=eng2vie for English To Vietnamese
* http://vdict.com/fsearch.php?word=long%20lanh&dictionaries=vie2eng for Vietnamest to English
*/


class block_dictionary_mc extends block_list {
	
	// The init() method does not need to change at all
	function init() {
		// HACK: LockeVN
		$this->title = get_string('dic_mc_title', 'block_dictionary_mc');
		$this->version = 2009180800;
	}
	
	function preferred_width() {
	// The preferred value is in pixels
	return 300;
}

function applicable_formats() {
	if (has_capability('moodle/site:config', get_context_instance(CONTEXT_SYSTEM))) 
	{
		return array('all' => true);
	} 
	else 
	{
		return array('site' => true);
	}
}

function get_content() {
	if ($this->content !== NULL) 
	{
		return $this->content;
	}
	
	global $CFG;
	$this->content->items[] = '
	<form id="gurucore_dictionary_popup" action="#" method="get">
		<div style="padding-bottom:6px"> '.get_string('dic_mc_db', 'block_dictionary_mc').' <br /> 
		<select id="gurucore_dictionary_dictionaries" name="dictionaries" > 
			<option value="eng2vie" selected="true">'.get_string('dic_mc_db_ev', 'block_dictionary_mc').'</option>             
			<option value="vie2eng">'.get_string('dic_mc_db_ve', 'block_dictionary_mc').'</option> 
		</select></div>
		<div> '.get_string('dic_mc_enter_word', 'block_dictionary_mc').'<br /> 
		<input type="text" name="word" id="gurucore_dictionary_word" />	    
		<a title="Dictionary" class="thickbox" href="#">'.get_string('dic_mc_button_search', 'block_dictionary_mc').'</a>
		</div> 
	</form>
	
	<div id="modalWindow" class="jqmWindow">
		<div id="jqmTitle">
			<button class="jqmClose">X</button>
			<span id="jqmTitleText">Title of modal window</span>
		</div>
		<iframe id="jqmContent" src="">
		</iframe>
	</div>
	
	
	<script type="text/javascript" src="'.$CFG->wwwroot.'/blocks/dictionary_mc/js/dictionary_popup.js"></script>
	';
	return $this->content;
}

}
?>