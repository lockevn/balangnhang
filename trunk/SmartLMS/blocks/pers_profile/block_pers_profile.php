<?php
   class block_pers_profile extends block_base {

      function init () {
         $this->title = get_string('blockname', 'block_pers_profile');
	     $this->version = 2007071700;
      } // function init()

      function has_config() {
         return false;
      } // function has_config()

      function get_content() {
         global $db, $USER, $CFG;

         if ($this->content !== NULL) {
	    return $this->content;
         }
	  
	 $this->content = new stdClass;
	 if ($USER->picture)
		 $picturepathname = $CFG->wwwroot.'/user/pix.php/'.$USER->id.'/f1.jpg';
	 else
		 $picturepathname = $CFG->wwwroot.'/pix/u/f1.png';

     /*
	 $this->content->text = '<table border="0" cellpadding="4" cellspacing="2" width="100%"><tr><td><a href="'.$CFG->wwwroot.'/user/view.php?id='.$USER->id.'">';
	 $this->content->text .= '<img src="'.$picturepathname.'" align="right" width="50" height="50"></a><b><span style="font-size: 6px">&nbsp;<br></span><span style="font-size: 18px">Hi, '.$USER->firstname.'!</span></td></tr></table>'; 
	 $this->content->footer = '';
     */
     
        $this->content->text = '
                
                        <table cellpadding="0" cellspacing="0" width="100%">
                            <tr>
                                <td height="30px">
                                    Hello <a href="" class="leftpaneltext">'.$USER->firstname.'</a>!
                                </td>
                                <td height="30px" align="right">
                                    <a style="float:right;" href="/login/logout.php?sesskey='.sesskey().'" class="leftpaneltext">Log out</a>
                                </td>
                            </tr>
                            <tr><td height="30px" align="left">
                                            Tin nhắn: ' . "<a href='$CFG->wwwroot/message/index.php'>" . $this->getTotalUnreadMessageOfUser($USER->id) . "</a>" . '
                                        </td></tr>
                                        
                        </table>
                    
        ';
        $this->content->footer = '';

         return $this->content;

      } // function get_content()

      function instance_allow_config() {
         return false;
      } // function instance_allow_config()

      function config_save($data) {
         foreach ($data as $name => $value) {
            set_config($name, $value);
         }
         return true;
      } // function config_save()

      function applicable_formats() {
         return array('all'=>true);
      } // function applicable_formats()
      
      private function getTotalUnreadMessageOfUser($userid) {
      		$result = get_record("message", "useridto", $userid, "", "", "", "", "count(id) as count");
      		if(!empty($result)) {
      			return $result->count;
      		}
      		return 0;
      }

   } // class block_countdown

?>
