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
		$this->content->text = $this->print_glossary_gui();
		return $this->content;
	}
	function has_config()
	{
		return false;
	}

	function applicable_formats() {
		if (has_capability('moodle/site:config', get_context_instance(CONTEXT_SYSTEM))) {
			return array('all' => true);
		} else {
			return array('site' => true);
		}
	} // function applicable_formats()
	
    
    function print_glossary_gui() {
    	global $CFG;
    	$str = "
    
    	<script type=\"text/javascript\" src=\"$CFG->wwwroot/lib/yui/yahoo/yahoo.js\"></script>
		<script type=\"text/javascript\" src=\"$CFG->wwwroot/lib/yui/event/event.js\"></script>
		<script type=\"text/javascript\" src=\"$CFG->wwwroot/lib/yui/connection/connection.js\"></script>
		<script type=\"text/javascript\" src=\"$CFG->wwwroot/blocks/pers_glossary/json.js\"></script>
		<style type=\"text/css\">
			.menu { position: relative; width: 120px; left: 650px; top: 10px; border: 1px solid #ccc; background: #eee; padding: 5px;}
			.spacer { font-size: 1px; clear: both; margin: 0; padding: 0; height:0; }
			.clearfix { zoom: 1; }
			
			.clearfix:after { content: "."; display: block; height: 0; clear: both; visibility: hidden; }
			
			* html .clearfix { height: 1%; }
		</style>		
		<h3>Personal Glossary</h3>
		<form method=\"post\" id=\"termform\">
		<input type=\"hidden\" name=\"id\" value=\"-1\" />
		<div style=\"float: left\">
			<table border=\"0\">
				  <tr>
				    <td>
				      <strong>Source term</strong>
				     </td>
				     <td> 
				      <input type=\"text\" name=\"sl_value\" />
				    </td>
				  </tr>
				  <tr>
				    <td>
				      <strong>Target term</strong>
				    </td>
				    <td>
				      <input type=\"text\" name=\"tl_value\" />
				    </td>
				  </tr>
				  <tr>
				   <! -- default button is just save! -->
				   <td align=\"left\">
				     <div style=\"float: left; width: 100px\" id=\"status\"></div>
				     
				   </td>
				   <td align=\"right\" id=\"buttons\">
				     
				     <input type=\"button\" onclick=\"saveForm()\" value=\"Save\" />
				     <input type=\"button\" onclick=\"newEntry()\" value=\"New\" />
				   </td>
				  </tr>				  				 
			</table>
		</div>

		
		<div class=\"spacer clearfix\">&nbsp;</div>		
		<div id=\"termSelector\">
		</div>";
		
		$str .= $this->print_script();
		
		return $str;
    }
    
    function print_script()
    {
    	global $CFG;
    	
    	$str = "
		<!-- fetch and display a big A B C D thingamageek for the terms.. -->
		
		<script type=\"text/javascript\" src=\"$CFG->wwwroot/blocks/pers_glossary/glossary.js\">
							
		</script>";		
		return $str;
    }

		
    
    
    
    
}

?>
