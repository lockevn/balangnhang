<?php require_once($_SERVER['DOCUMENT_ROOT']."/config.php");

require_once(ABSPATH."Lib/External/Savant3.php");
require_once(ABSPATH."Lib/URLParamHelper.php");
require_once(ABSPATH."Lib/Text.php");

if($tpl->authkey)
{    
    // only render with AU user
    $FILENAME = 'post_msg';
    $$FILENAME = $tpl->fetch("$FILENAME.tpl.php");
}

?>