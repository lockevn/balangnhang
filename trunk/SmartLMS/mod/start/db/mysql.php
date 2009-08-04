<?PHP

function start_upgrade($oldversion) {
/// This function does anything necessary to upgrade 
/// older versions to match current functionality 

    global $CFG;

    if ($oldversion < 2006062301) { //DROP first
        execute_sql("ALTER TABLE {$CFG->prefix}start DROP INDEX course;",false);
        modify_database('','ALTER TABLE prefix_start ADD INDEX course (course);');
    }

    return true;
}

?>
