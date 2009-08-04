<?php // $Id: migrate2utf8.php,v 1.7 2006/03/10 03:43:32 patrickslee Exp $
function migrate2utf8_start_name($recordid){
    global $CFG, $globallang;

/// Some trivial checks
    if (empty($recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    if (!$start = get_record('start','id',$recordid)) {
        log_the_problem_somewhere();
        return false;
    }
    if ($globallang) {
        $fromenc = $globallang;
    } else {
        $sitelang   = $CFG->lang;
        $courselang = get_course_lang($start->course);  //Non existing!
        $userlang   = get_main_teacher_lang($start->course); //N.E.!!

        $fromenc = get_original_encoding($sitelang, $courselang, $userlang);
    }

/// We are going to use textlib facilities
    
/// Convert the text
    if (($fromenc != 'utf-8') && ($fromenc != 'UTF-8')) {
        $result = utfconvert($start->name, $fromenc);

        $newstart = new object;
        $newstart->id = $recordid;
        $newstart->name = $result;
        migrate2utf8_update_record('start',$newstart);
    }
/// And finally, just return the converted field
    return $result;
}

function migrate2utf8_start_content($recordid){
    global $CFG, $globallang;

/// Some trivial checks
    if (empty($recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    if (!$start = get_record('start','id',$recordid)) {
        log_the_problem_somewhere();
        return false;
    }
    if ($globallang) {
        $fromenc = $globallang;
    } else {
        $sitelang   = $CFG->lang;
        $courselang = get_course_lang($start->course);  //Non existing!
        $userlang   = get_main_teacher_lang($start->course); //N.E.!!

        $fromenc = get_original_encoding($sitelang, $courselang, $userlang);
    }

/// We are going to use textlib facilities
    
/// Convert the text
    if (($fromenc != 'utf-8') && ($fromenc != 'UTF-8')) {
        $result = utfconvert($start->content, $fromenc);

        $newstart = new object;
        $newstart->id = $recordid;
        $newstart->content = $result;
        migrate2utf8_update_record('start',$newstart);
    }
/// And finally, just return the converted field
    return $result;
}
?>
