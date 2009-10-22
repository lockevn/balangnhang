<?PHP // $Id: index.php,v 1.1 2003/09/14 12:30:08 moodler Exp $

    require_once("../../config.php");
    require_once("lib.php");

    $id = required_param('id',PARAM_INT);   // course

    redirect("$CFG->wwwroot/course/view.php?id=$id");

?>
