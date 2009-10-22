<?PHP //$Id: block_course_summary.php,v 1.26.2.2 2008/03/03 11:41:02 moodler Exp $

class block_lecture_guide extends block_base {
    function init() {
        $this->title = get_string('lectureguide', 'block_lecture_guide');
        $this->version = 2007101509;
    }

    function specialization() {
        //global $COURSE;
        if($this->instance->pagetype == PAGE_RESOURCE_VIEW) {
            $this->title = get_string('lectureguide', 'block_lecture_guide');
        }
    }

    function get_content() {
        global $CFG, $COURSE, $PAGE;

        if($this->content !== NULL) {
            return $this->content;
        }

        if (empty($this->instance) || $PAGE->type != PAGE_RESOURCE_VIEW) {
            return '';
        }
        
        
        
		$resourceId = $PAGE->id;
		$resource = get_record('resource', 'id', $resourceId, 'lotype', 'lecture');
		if(empty($resource) || empty($resource->summary)) {
			return '';
		}
        
        $this->content = new object();
        $options = new object();
        $options->noclean = true;    // Don't clean Javascripts etc
        $this->content->text = format_text($resource->summary, FORMAT_HTML, $options);
        
        $this->content->footer = '';

        return $this->content;
    }



    function preferred_width() {
        return 210;
    }
    
    function applicable_formats() {
        return array('all' => true);
    }

}

?>
