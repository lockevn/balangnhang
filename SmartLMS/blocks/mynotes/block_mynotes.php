<?PHP //$Id: block_mynotes.php,v 1.0 2007/03/09 11:33:00 hugomarcelo $_

class block_mynotes extends block_base {
  
  function init() {
    $this->title = get_string('blocktitle', 'block_mynotes');
    $this->version = 2007020700;
  }
  
  function has_config() {
    return true;
  }
  
  function get_content() {
    global $CFG;
    global $USER;
    
    if(!isset($USER->id)) { 
      $this->content->text = '<div class="description">'.get_string('noaccess','block_mynotes').'</div>';
    }
    
    if($this->content !== NULL) {
      return $this->content;
    }
    
    $this->content = new stdClass;
    $this->content->footer = '';
    
    if (empty($this->instance)) {
      $this->content->text   = '';
      return $this->content;
    }
    
    // optional params
    $id            = optional_param('id');
    $remove        = optional_param('remove');         // used in method remove_note()
    $notetext      = optional_param('notetext');       // used in method add_note()
    $noteid        = optional_param('noteid');         // used in method edit_note()
    $notetext_edit = optional_param('notetext_edit');  // used in method edit_note()
    $notepriority  = optional_param('priority');       // used in method add_note() and edit_note()
    $sort          = optional_param('sort');           // sort notes by : 0 -> last update; 1 -> priority
    
    $goto = $id ? $_SERVER['PHP_SELF']."?id=$id" : $_SERVER['PHP_SELF']; 

    if($notetext)      {
      $text = clean_text($notetext);
      if(! $this->addnote($text, $notepriority)) { } //error(get_string('error_inserting','block_mynotes')); } 
      else { redirect($goto); }
    }
    
    if($notetext_edit) {
      $text = clean_text($notetext_edit);
      if(! $this->edit_note($noteid, $text, $notepriority)) { } //error(get_string('error_editing','block_mynotes')); } 
      else { redirect($goto);}
    }
    
    if($remove) { // remove a note
      if(! $this->removenote($remove)) { } //error(get_string('error_removing', 'block_mynotes')); }
    }
    
    // Display default block content
    // Get user notes, sorted by $sort
    $dbfield = ($sort) ? 'priority DESC, last_updated DESC' : 'last_updated DESC, priority DESC';
    $notes = get_records('block_mynotes','userid',$USER->id, "$dbfield");
    
    $this->content->text = $this->get_javascript($goto).'<div class="block_mynotes">';
    if($notes) { // List notes
      
      $this->content->text .= '<table class="mytable">';
      foreach($notes as $note) {
	
	// define priority class
	$color_class = ($note->priority == 1) ? "pr1" : (($note->priority == 2) ? "pr2" : "pr3");
	// create edition form and note visualization foreach note, to be used in a javascript popup window
	$edit_form = $this->make_edit_form($note->id, $goto,$id);
	$note_popup = $note->text.'<hr />'.$note->last_updated;
	
	// create the table record
	$this->content->text .= '<tr><td class="td">';
	$this->content->text .= "<a href='javascript:mynotes_popup(300,220,\"".$note_popup."\");' class=\"link_text\">";
	$this->content->text .= $this->text_fix($note->text).'</a></td>';
	$this->content->text .= '<td width="2%"><div class='.$color_class.'><strong>*</strong></font></td>';
	$this->content->text .= '<td width="6%">';
	$this->content->text .= "<a href='javascript:mynotes_popup(300,220,\"$edit_form\");'>";
	$this->content->text .= '<img src="'.$CFG->pixpath.'/t/edit.gif" hspace="2" border="0"';
	$this->content->text .= 'alt="'.get_string('edit','block_mynotes').'"></a>';
	$this->content->text .= '</td>';
        $this->content->text .= '<td width="6%">';
	$this->content->text .= "<a href='javascript:mynotes_remove(\"".$note->id."\");'>";
	$this->content->text .= '<img src="'.$CFG->pixpath.'/t/delete.gif" hspace="2" height="11" width="11" border="0"';
	$this->content->text .= 'alt="'.get_string('delete','block_mynotes').'"></a>';
	$this->content->text .= '</td>';
	$this->content->text .= '</tr><tr>';
	$this->content->text .= '<td class="date" colspan="4"><div class="tr1">('.$note->last_updated.')</div></td></tr>'; 
      }
      $this->content->text  .= '</table>';
    } else { // no user notes
      $this->content->text .= '<div class="description">'.get_string('nonotes','block_mynotes').'</div>';
    }
    
    // form to insert notes and to order by date or priority
    $this->content->text .= '<form enctype="multipart/form-data" name="index" action="'.$goto.'" style="display:inline"><br>';
    $this->content->text .= '<table class="mytable"><tr><td>';
    $this->content->text .= get_string('add','block_mynotes').':</td><td align="right">Pr:</td><td></td></tr>';
    $this->content->text .= '<tr><td valign="bottom"><input name="notetext" class="notetext" type="text" size="*" value="" alt="search" /></td>';
    $this->content->text .= '<td align="right" valign="bottom" style="padding-bottom:2px;"><select name="priority" class="select">';
    $this->content->text .= '<option class="pr3" value="3">3</option>';
    $this->content->text .= '<option class="pr2" value="2">2</option>';
    $this->content->text .= '<option class="pr1" value="1" selected>1</option>';
    $this->content->text .= '</select></td>';
    $this->content->text .= '<td align="left"><input value=">" style="font-size:0.90em;" type="submit" /></td></tr></table>';
    $this->content->text .= '<input name="remove"   type="hidden" value=""/>';
    if($id) { // it we are in a course
      $this->content->text .= '<input name="id"       type="hidden" value="'.$id.'"/>';
    }
    $this->content->text .= get_string('orderby','block_mynotes').'&nbsp;<select name="sort" class="select" onchange="document.index.submit()">';
    $this->content->text .= '<option value="0" '.(($sort==0) ? 'selected' : '' ).'> '.get_string('last_updated','block_mynotes').'</option>';
    $this->content->text .= '<option value="1" '.(($sort==1) ? 'selected' : '' ).'> '.get_string('priority'    ,'block_mynotes').'</option>';
    $this->content->text .= '</select>';
    $this->content->text .= '</form></div>';
    
    return $this->content;
  }
  
  /**
   * The method text_fix(1) takes an introduction of the text, to show in the block
   * uses a configuration variable with the number of chars to be shown in block for each note text
   * 
   * @param string $text  : the note text
   *
   * @return string     
   */
  function text_fix($text) {
    global $CFG;
    
    $chars = (isset($CFG->block_mynotes_chars)) ? $CFG->block_mynotes_chars : 35;
    $points = (strlen($text) > $chars) ? '...' : '';
    return (substr($text, 0, $chars).$points);
  }
  
  
  /**
   * The method addnote(2) creates a new note for the user
   *
   * @param string $text
   * @param int $priority
   *
   * @return bool
   */
  function addnote($text, $priority) {
    global $USER;
    
    $note = new Object;
    $note->userid = $USER->id;
    $note->text = $text;
    $note->priority = $priority;
    $note->last_updated = date('Y-m-d H:i:s');
    
    if(empty($note->text)) { return false;}
    return (insert_record('block_mynotes', $note));
  }
  
  
  /**
   * The method removenote(1) removes a user note
   *
   * @param int $noteid
   *
   * @return bool
   *
   */
  function removenote($noteid) {
    global $USER;
    $note_user = get_field('block_mynotes','userid', 'id', $noteid);
    if($note_user != $USER->id) { return false; }
    return (delete_records('block_mynotes', 'id', $noteid)); 
  }
  
  
  /**
   * The method make_edit_form(1) creates the edition form of a note
   * ready to be inserted in the javascript function mypopup
   * 
   * @param int $noteid : Note id to edit
   *
   * @noreturn
   */
  function make_edit_form($noteid, $goto, $courseid) {
    global $CFG;
    if(!$note = get_record('block_mynotes', 'id', $noteid)) {
      error(get_string('note_notfound', 'block_mynotes'));
    }
    
    $return  = "<form enctype=\\\"multipart/form-data\\\" name=\\\"index\\\"";
    $return .= "action=\\\"".$goto."\\\" style=\\\"display:inline\\\"";
    $return .= " target=\\\"Editar\\\" onsubmit=\\\"setTimeout(self.close() ,2000)\\\" >";
    $return .= "<table><tr><td>".get_string('text','block_mynotes')."</td><td>".get_string('priority','block_mynotes')."</td></tr>";
    $return .= "<tr><td><textarea name=\\\"notetext_edit\\\" rows=\\\"6\\\" cols=\\\"20\\\">".$note->text."</textarea></td>";
    $return .= "<td valign=\\\"top\\\">";
    $return .= "<table><tr><td align=\\\"center\\\"><select name=\\\"priority\\\">";
    $return .= "<option style=\\\"color:#CC0000;font-weight:bold\\\" value=\\\"3\\\" ".(($note->priority==3) ? "selected" : "").">3</option>";
    $return .= "<option style=\\\"color:#CCCC00;font-weight:bold\\\" value=\\\"2\\\" ".(($note->priority==2) ? "selected" : "").">2</option>";
    $return .= "<option style=\\\"color:#00CC00;font-weight:bold\\\" value=\\\"1\\\" ".(($note->priority==1) ? "selected" : "").">1</option>";
    $return .= "</select></td></tr>";
    $return .= "<input name=\\\"noteid\\\" type=\\\"hidden\\\" value=\\\"".$noteid."\\\">";
    if($courseid) { $return .= "<input name=\\\"id\\\" type=\\\"hidden\\\" value=\\\"".$courseid."\\\">"; }
    $return .= "<tr><td align=\\\"center\\\">";
    $return .= "<input value=\\\"".get_string('save','block_mynotes')."\\\" type=\\\"submit\\\" /></td></tr>";
    $return .= "</table></td></tr></table>";
    $return .= "</form>";
    
    return $return;
  }
  
  /**
   * The method edit_note(3) edits an existing note when the editing form is submitted
   * 
   * @param int $noteid   : Note id to edit
   * @param $notetext     : Current text of the note
   * @param $notepriority : Current priority of the note
   *
   * @return bool
   */
  function edit_note($noteid, $notetext, $notepriority) {
    global $USER;
    $note = get_record('block_mynotes','id', $noteid);
    if($note->userid != $USER->id) { return false; }
    if(! set_field('block_mynotes','text'       , $notetext,"id",$noteid)) {return false; }
    if(! set_field('block_mynotes','priority'   , $notepriority,"id",$noteid)) {return false; }
    if(! set_field('block_mynotes','last_updated',date('Y-m-d H:i:s') ,"id",$noteid)) {return false; }
    return true;
  }


  /**
   * The method get_javascript() returns all javascript necessary to the block
   *
   */
  
  /****************
   *  Javascript  *
   ***************/
  
  /** function mypopup(3)
   * Javascript function to provide a html form (htmlstring) in a popup 
   *
   * @param int width
   * @param int height
   * @param string htmlstring
   *
   *
   */
  
  /**
   * function remove_note(1)
   * asks for confirmation to delete and submit the form
   *
   * @param int noteid
   *
   */
  
  function get_javascript($goto) {
    global $CFG;
    
    $javascript = 
      '<SCRIPT type="text/javascript">'.
      'expopup = false;'.
      'var str;'.
      'function mynotes_popup(width,height,htmlstring) {'.
      'str=htmlstring;'.
      'str=str+"<br><p align=\'center\'>'.
      '<span style=\'color:darkblue;cursor:pointer\' onclick=\'javascript:self.opener.location=\"'.$goto.'\";window.close(); \'>Close</span> ";'.
      'path = "'.$CFG->wwwroot.'"+"/blocks/mynotes/";'.
      'if(!expopup) {'.
      'expopup=true;'.
      'window.name="Editar";'.   
      'if(window.innerWidth){'.
      'LeftPosition =(window.innerWidth-width)/2;'.
      'TopPosition =((window.innerHeight-height)/4)-50;'.
      '} else {'.
      'LeftPosition =(parseInt(window.screen.width)-	width)/2;'.
      'TopPosition=((parseInt(window.screen.height)-height)/2)-50;'.
      '}'.
      'attr = "toolbar=no,directories=no, status=no, menubar=no, resizable=no,scrollbars=no,width=" + width + ",height=" +'.
      'height + ",screenX=300,screenY=200,left=" + LeftPosition + ",top=" +'.
      'TopPosition + "";'.
      'generator=window.open(path+"blank.html", "new_window", attr);'.
      'var browserName=navigator.appName;'.
      'if (browserName=="Microsoft Internet Explorer")'.
      '{'.
      'generator.onload=mynotes_doWrites();'.
      '} else {'.
      'generator.onload=mynotes_doWrites;'.
      '}'.
      'generator.focus();'.
      '} }'.
      'function mynotes_doWrites() {'.
      'var doc = generator.document;'.
      'target = doc.getElementsByTagName("body")[0];'.
      'target.innerHTML = "<p>"+str+"</p>"; }'.
      'function mynotes_remove(noteid) {'.
      'var_confirm=confirm("'.get_string('confirm_delete', 'block_mynotes').'");'.
      'if(var_confirm==true) {'.
      'document.index.remove.value=noteid;'.
      'document.index.submit();'.
      '} else { exit; } }'.
      '</SCRIPT>';
    
    return $javascript; 
  }
}
// DO NOT enter newlines after this line
?>