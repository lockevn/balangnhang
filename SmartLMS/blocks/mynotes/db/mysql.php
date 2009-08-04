<?php //$Id: mysql.php,v 1.0 2007/02/28 11:00:00 Hugo Santos (hugomarcelo) Exp $

function mynotes_upgrade($oldversion) {
/// This function does anything necessary to upgrade 
/// older versions to match current functionality 

    global $CFG;

    if ($oldversion < 2003111500) {
        # Not necessary yet        
    }
    
    if ($oldversion < 2004112001) {
        # Not necessary  yet
    }
    return true;
}

?>
