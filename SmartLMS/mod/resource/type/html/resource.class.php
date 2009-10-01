<?php // $Id: resource.class.php,v 1.40.2.3 2008/07/01 22:25:26 skodak Exp $

require_once($CFG->libdir.'/blocklib.php');
require_once($CFG->libdir.'/pagelib.php');
require_once($CFG->dirroot.'/mod/resource/pagelib.php');



class resource_html extends resource_base {


function resource_html($cmid=0) {
    parent::resource_base($cmid);
}

function add_instance($resource) {
    $this->_postprocess($resource);    
    return parent::add_instance($resource);
       
}


function update_instance($resource) {
    $this->_postprocess($resource);
    return parent::update_instance($resource);
}

function _postprocess(&$resource) {
    global $RESOURCE_WINDOW_OPTIONS;
    $alloptions = $RESOURCE_WINDOW_OPTIONS;

    if ($resource->windowpopup) {
        $optionlist = array();
        foreach ($alloptions as $option) {
            $optionlist[] = $option."=".$resource->$option;
            unset($resource->$option);
        }
        $resource->popup = implode(',', $optionlist);
        unset($resource->windowpopup);
        $resource->options = '';

    } else {
        if (empty($resource->blockdisplay)) {
            $resource->options = '';
        } else {
            $resource->options = 'showblocks';
        }
        unset($resource->blockdisplay);
        $resource->popup = '';
    }
}


function display() {
    global $CFG, $PAGE, $COURSE, $SESSION;

    $formatoptions = new object();
    $formatoptions->noclean = true;

    /// Set up some shorthand variables
    $cm = $this->cm;
    $course = $this->course;
    $resource = $this->resource;

    // fix for MDL-9021, thanks Etienne Roz
    // fix for MDL-15387, thanks to John Beedell
    add_to_log($course->id, "resource", "view", "view.php?id={$cm->id}", $resource->id, $cm->id);

    /*danhut addded to enable blocks in resource page*/
    $PAGE       = page_create_instance($resource->id);
    $pageblocks = blocks_setup($PAGE, BLOCKS_PINNED_BOTH);   
    $left_blocks_preferred_width = 180;
    $right_blocks_preferred_width = 250;
    //$blocks_preferred_width = bounded_number(180, blocks_preferred_width($pageblocks[BLOCK_POS_LEFT]), 210);
    /*end of danhut addded*/
    
   /*danhut commented out: khôgn hiển thị toàn bộ block ở course page vào module page nữa*/
    /// Are we displaying the course blocks?
//    if ($this->resource->options == 'showblocks') {
//
//        parent::display_course_blocks_start();
//
//        echo format_text($this->resource->alltext, FORMAT_HTML, $formatoptions, $this->course->id);
//
//        parent::display_course_blocks_end();
//
//    } else {

    /*end of danhut's commentation*/
        /// Set up generic stuff first, including checking for access
    parent::display();

    $pagetitle = strip_tags($course->shortname.': '.format_string($resource->name));
    $inpopup = optional_param('inpopup', '', PARAM_BOOL);

    if ($resource->popup) {
    	if ($inpopup) {                    /// Popup only

    		print_header();
    		print_simple_box(format_text($resource->alltext, FORMAT_HTML, $formatoptions, $course->id),
                        "center clearfix", "", "", "20");


    		print_footer($course);
    	} else {                           /// Make a page and a pop-up window
    		$navigation = build_navigation($this->navlinks, $cm);
    		/*danhut modified: no need to put navmenu in header*/
    		print_header($pagetitle, $course->fullname, $navigation,
                        "", "", true, update_module_button($cm->id, $course->id, $this->strresource)/*,
    		navmenu($course, $cm)*/);


    		echo "\n<script type=\"text/javascript\">";
    		echo "\n//<![CDATA[\n";
    		echo "openpopup('/mod/resource/view.php?inpopup=true&id={$cm->id}','resource{$resource->id}','{$resource->popup}');\n";
    		echo "\n//]]>\n";
    		echo '</script>';

    		if (trim(strip_tags($resource->summary))) {
    			print_simple_box(format_text($resource->summary, FORMAT_MOODLE, $formatoptions, $course->id), "center clearfix");
    		}

    		$link = "<a href=\"$CFG->wwwroot/mod/resource/view.php?inpopup=true&amp;id={$cm->id}\" onclick=\"this.target='resource{$resource->id}'; return openpopup('/mod/resource/view.php?inpopup=true&amp;id={$cm->id}', 'resource{$resource->id}','{$resource->popup}');\">".format_string($resource->name,true)."</a>";

    		echo '<div class="popupnotice">';
    		print_string('popupresource', 'resource');
    		echo '<br />';
    		print_string('popupresourcelink', 'resource', $link);
    		echo '</div>';
    		/*danhut added*/
    		print_container_end();
    		echo '</td></tr></table>';
    		/*end of danhut added*/
    		print_footer($course);
    	}
    } else {    /// not a popup at all
    	$navigation = build_navigation($this->navlinks, $cm);

    	print_header($pagetitle, $course->fullname, $navigation,
                    "", "", true, update_module_button($cm->id, $course->id, $this->strresource),
    	navmenu($course, $cm));

    	/*danhut added*/
    	echo '<table id="layout-table" class="' .$resource->lotype . '"><tr>';
    	if(!empty($CFG->showblocksonmodpages) && (blocks_have_content($pageblocks, BLOCK_POS_LEFT) || $PAGE->user_is_editing())) {
    		echo '<td style="width: '.$left_blocks_preferred_width.'px;" id="left-column">';
    		print_container_start();
    		blocks_print_group($PAGE, $pageblocks, BLOCK_POS_LEFT);
    		print_container_end();
    		echo '</td>';
    	}
    	echo '<td id="middle-column">';
    	print_container_start();
    	/*end of danhut added*/
    	/*danhut modified: nếu là resource là test description 0 print navmenu*/
    	if($resource->lotype != 'testdescription') {
    		$menu = navmenu($course, $cm);
    		echo $menu;
    	}
    	print_simple_box(format_text($resource->alltext, FORMAT_HTML, $formatoptions, $course->id), "center clearfix", "", "", "20");

    	$strlastmodified = get_string("lastmodified");
    	echo "<div class=\"modified\">$strlastmodified: ".userdate($resource->timemodified)."</div>";

    	/*danhut added*/
    	if($resource->lotype != 'testdescription') {
    		echo $menu;
    	} else  {
    		/*danhut added: print next button for testdescription resource*/
    		$navLinks = navmenu($course, $cm, '', true);
    		$nextLink = $navLinks['nextLink'];
    		if(!empty($nextLink)) {
    			echo "<div class=\"submitbtns mdl-align $resource->lotype\">\n";
    			$start = optional_param('start'); 
    			if(empty($navLinks['backLink']) || !empty($start)) {
    				/*nếu là trang đầu tiên giới thiệu về bài test*/
    				if(isset($SESSION->attemptIdArr)) {
    					unset($SESSION->attemptIdArr);
    				}
    				$btnLabel = get_string("starttest", "smartcom");
    			} else {
    				$btnLabel = get_string("next", "smartcom");
    			}
    			echo "<a href='$nextLink'>" . $btnLabel . "</a>";
    			echo "</div>" ;
    		}
    	}

    	print_container_end();
    	echo '</td>';

    	if(!empty($CFG->showblocksonmodpages) && (blocks_have_content($pageblocks, BLOCK_POS_RIGHT) || $PAGE->user_is_editing())) {
    		echo '<td style="width: '.$right_blocks_preferred_width.'px;" id="right-column">';
    		print_container_start();
    		blocks_print_group($PAGE, $pageblocks, BLOCK_POS_RIGHT);
    		print_container_end();
    		echo '</td>';
    	}

    	echo '</tr></table>';
    	/*end of danhut added*/
    	print_footer($course);
            
        }

    //}

}

function setup_preprocessing(&$defaults){

    if (!isset($defaults['popup'])) {
        // use form defaults

    } else if (!empty($defaults['popup'])) {
        $defaults['windowpopup'] = 1;
        if (array_key_exists('popup', $defaults)) {
            $rawoptions = explode(',', $defaults['popup']);
            foreach ($rawoptions as $rawoption) {
                $option = explode('=', trim($rawoption));
                $defaults[$option[0]] = $option[1];
            }
        }
    } else {
        $defaults['windowpopup'] = 0;
        if (array_key_exists('options', $defaults)) {
            $defaults['blockdisplay'] = ($defaults['options']=='showblocks');
        }
    }
}

function setup_elements(&$mform) {
    global $CFG, $RESOURCE_WINDOW_OPTIONS;
    
    /*danhut added: lấy lại categoryid, lotype khi insert LO từ Lo Bank và store in form*/
    $cat = optional_param('cat','', PARAM_TEXT);
    $lotype = optional_param('lotype', '', PARAM_TEXT);       
    $indent = optional_param('indent', '', PARAM_INT);
    
    $mform->addElement('hidden', 'cat','');
    $mform->setDefault('cat', $cat);
    
    $loResourceTypeArr = array(
    						'lecture' => get_string('lecture', 'smartcom'), 
    						'lecturedescription' => get_string('lecturedescription', 'smartcom'), 
    						'testdescription' => get_string('testdescription', 'smartcom'));
    
    $mform->addElement('select', 'lotype','choose a type ...', $loResourceTypeArr );
    if(isset($loResourceTypeArr[$lotype])) {
    	$mform->setDefault('lotype', $lotype);
    }
    
    
    $mform->addElement('hidden', 'indent','');
    $mform->setDefault('indent', $indent);
    
//    $mform->addElement('hidden', 'smarttype','');
//    $mform->setDefault('smarttype', $lotype);
    
    /*lấy lại beforecm neu co*/
    $beforecm = optional_param('beforecm','', PARAM_INT);
    $mform->addElement('hidden', 'beforecm','');
    $mform->setDefault('beforecm', $beforecm);
    

    $mform->addElement('htmleditor', 'alltext', get_string('fulltext', 'resource'), array('cols'=>85, 'rows'=>30));
    $mform->setType('alltext', PARAM_RAW);
    $mform->setHelpButton('alltext', array('reading', 'writing', 'richtext'), false, 'editorhelpbutton');
    $mform->addRule('alltext', get_string('required'), 'required', null, 'client');

    $mform->addElement('header', 'displaysettings', get_string('display', 'resource'));

    $woptions = array(0 => get_string('pagewindow', 'resource'), 1 => get_string('newwindow', 'resource'));
    $mform->addElement('select', 'windowpopup', get_string('display', 'resource'), $woptions);
    $mform->setDefault('windowpopup', !empty($CFG->resource_popup));

    $mform->addElement('checkbox', 'blockdisplay', get_string('showcourseblocks', 'resource'));
    $mform->setDefault('blockdisplay', 0);
    $mform->disabledIf('blockdisplay', 'windowpopup', 'eq', '1');
    $mform->setAdvanced('blockdisplay');
    
    

    foreach ($RESOURCE_WINDOW_OPTIONS as $option) {
        if ($option == 'height' or $option == 'width') {
            $mform->addElement('text', $option, get_string('new'.$option, 'resource'), array('size'=>'4'));
            $mform->setDefault($option, $CFG->{'resource_popup'.$option});
            $mform->disabledIf($option, 'windowpopup', 'eq', '0');
        } else {
            $mform->addElement('checkbox', $option, get_string('new'.$option, 'resource'));
            $mform->setDefault($option, $CFG->{'resource_popup'.$option});
            $mform->disabledIf($option, 'windowpopup', 'eq', '0');
        }
        $mform->setAdvanced($option);
    }
}


}

?>
