<?php
class block_glossary extends block_base {

	function init() {
		$this->title   = get_string('module_name', 'block_glossary');
		$this->version = 2009081800;
	}

	function get_content() {

		GLOBAL $CFG;
		if ($this->content !== NULL) {
			return $this->content;
		}
		$this->content = new stdClass();
		$id = $this->config->cmid;
		
		/*check if module really exists*/
		if (!empty($id)) {
			if (! $cm = get_coursemodule_from_id('glossary', $id)) {
				error("Course Module ID was incorrect");
			}
			if (! $course = get_record("course", "id", $cm->course)) {
				error("Course is misconfigured");
			}
			if (! $glossary = get_record("glossary", "id", $cm->instance)) {
				error("Course module is incorrect");
			}
		} else {
			//error("Must specify glossary ID or course module ID");
			$this->content->text = get_string('config_required','block_glossary');
			return $this->content;
		}

		require_course_login($course->id, true, $cm);
		$context = get_context_instance(CONTEXT_MODULE, $cm->id);
		$glossaryID = $cm->instance;
		/*generate content*/
		$this->content->text = $this->print_entry_list($glossaryID);
		$this->content->footer = '';			
		return $this->content;
	}

	/**
	 * allow to customize what goes into the block
	 * to make Moodle display an "Edit..." icon in our block's header
	 * when we turn editing mode on in any course
	 *
	 * @return unknown
	 */
	function instance_allow_config() {
		return true;
	}

	function specialization() {
		if(!empty($this->config->title)){
			$this->title = $this->config->title;
		}else{
			$this->config->title = 'Glossary';
		}
		if(empty($this->config->text)){
			$this->config->text = 'Init config text';
		}
	}
	
	function print_entry_list($glossaryID) {
	
		GLOBAL $CFG;
		$sqlStr = "SELECT concept as glossarypivot, definition, attachment 
						FROM {$CFG->prefix}glossary_entries
						WHERE glossaryid = $glossaryID
						ORDER BY glossarypivot ASC";
		$allentries = get_records_sql($sqlStr);
		$textlib = textlib_get_instance();
		$str = '';
		if(empty($allentries))
		{
			$str .= get_string("noentry","glossary");
			return $str;
		}
		
		$upperpivotArr = array();
		/*calculate the upperpivotArr to display as index bar */
		foreach($allentries as $entry)
		{
			$pivot = $entry->glossarypivot;
			$upperpivot = $textlib->strtoupper($pivot);
			// Reduce pivot to 1cc if necessary
			$upperpivot = $textlib->substr($upperpivot, 0, 1);
			$upperpivotArr[] = $upperpivot;
		}
		$str .= '<div style="height:150px;overflow:auto">';
		$str .= '<div class="glossaryexplain"></b>' . get_string("explainalphabet","glossary") . '<b></div>';
		/*display the index bar*/
		$str .= $this->glossary_block_print_alphabet_links($upperpivotArr);

		$currentpivot = '';
		/*display the entry list*/
		foreach($allentries as $entry)
		{
			$pivot = $entry->glossarypivot;
			$upperpivot = $textlib->strtoupper($pivot);
			// Reduce pivot to 1cc if necessary
			$upperpivot = $textlib->substr($upperpivot, 0, 1);
			// if there's a group break
			if ( $currentpivot != $upperpivot ) {
				$str .= $this->print_group_beak($upperpivot, $currentpivot);
			}
			$str .= $this->print_entry($entry->glossarypivot, $entry->definition);
		}
		$str .= '</div>';			
		return $str;
	}
	
	function print_group_beak($upperpivot, $pivottoshow) {
		
		$str = '<div class="glossarycategoryheader">';					
		$str .= "<h2><a name=\"prefix_$upperpivot\">".stripslashes_safe($pivottoshow)."</h2>";
		$str .= "</div>\n";
		return $str;
	}
	
	function print_entry($term, $definition) {
		$str = "<table class=\"glossarypost dictionary\" cellspacing=\"0\">
					<tbody>
						<tr valign=\"top\">
							<td class=\"entry\">
								<div class=\"concept\">
									<h3 class=\"nolink\">
										<span class = \"nolink\">$term </span>
									</h3>:
									$definition
								</div>				
							</td>
						</tr>
					</tbody>
				</table>";
		return $str;
	}
	
	
	

	function glossary_block_print_alphabet_links($pivotArr) {
		$retStr = "";
		$alphabet = explode(",", get_string("alphabet"));
		$letters_by_line = 14;
		$retStr .= "</b>";
		$retStr .= "<div style=\"text-align:center;\">";
		for ($i = 0; $i < count($alphabet); $i++) {
			if(in_array($alphabet[$i], $pivotArr))
			{
				$retStr .= "<a href=\"#prefix_$alphabet[$i]\">$alphabet[$i]</a>";

				if ((int) ($i % $letters_by_line) != 0 or $i == 0) {
					$retStr .= ' | ';
				} else {
					$retStr .= '<br />';
				}
			}
		}
		$retStr .= "</div>";
		$retStr .= "<b>";

		return $retStr;
	}
	
	/*danhut added to display this block in module page*/
    function applicable_formats() {
        if (has_capability('moodle/site:config', get_context_instance(CONTEXT_SYSTEM))) {
            return array('all' => true);
        } else {
            return array('site' => true);
        }
    }



}
?>