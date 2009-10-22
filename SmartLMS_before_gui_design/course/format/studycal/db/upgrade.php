<?php  
function xmldb_format_studycal_upgrade($oldversion=0) {

    global $CFG;
    $result = true;
    
    @include_once(dirname(__FILE__).'/../local/transaction_wrapper.php');
    if (!class_exists('transaction_wrapper')) {
        require_once($CFG->dirroot.'/local/transaction_wrapper.php');
    }
    $tw=new transaction_wrapper();

    if ($result && $oldversion < 2006111000) { 
        $result = table_column('studycal','startdate','startdateoffset','integer','10','signed','0','not null');
    }
    if ($result && $oldversion < 2006111300) { 
        $result = table_column('studycal','shownumber','hidenumbers','integer','1','unsigned','0','not null');        
        $result&= execute_sql("ALTER TABLE {$CFG->prefix}studycal DROP COLUMN startnumber"); 
    }
    if ($result && $oldversion < 2006112000) {
        $result = table_column('studycal_weeks','','hidedate','integer','1','unsigned','0','not null');        
        $result&= table_column('studycal_weeks','','title','varchar','255','','','');        
    }
    if ($result && $oldversion < 2006112400) {
        $result&= execute_sql("CREATE TABLE {$CFG->prefix}studycal_imported (courseid INTEGER NOT NULL,coursemoduleid INTEGER NOT NULL,col INTEGER NOT NULL)"); 
    }  
    if ($result && $oldversion < 2006112401) {
        $result&= execute_sql("ALTER TABLE {$CFG->prefix}studycal_imported ADD CONSTRAINT {$CFG->prefix}studycal_imported_pkey PRIMARY KEY (courseid,coursemoduleid)"); 
    }
    if ($result && $oldversion < 2006120100) {
        $result&= execute_sql("CREATE TABLE {$CFG->prefix}studycal_hideboxes (id SERIAL, courseid INTEGER NOT NULL,coursemoduleid INTEGER,eventid INTEGER)"); 
    }  
    if ($result && $oldversion < 2007061200) {
        $result&= execute_sql("UPDATE {$CFG->prefix}capabilities SET component = 'format/studycal' WHERE component = 'course/format/studycal'"); 
    }  
    
    if($result) {
        $tw->commit();
    }

    return $result;
}
?>
