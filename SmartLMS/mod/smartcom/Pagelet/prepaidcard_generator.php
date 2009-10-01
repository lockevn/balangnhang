<?php require_once($_SERVER['DOCUMENT_ROOT']."/Gconfig.php");
require_once($_SERVER['DOCUMENT_ROOT']."/config.php");


require_login();
$userid = $USER->id;

$context = get_context_instance(CONTEXT_SYSTEM);
require_capability('mod/smartcom:prepaidcardgenerator', $context);


$FILENAME = 'prepaidcard_generator';
$$FILENAME = $tpl->display("$FILENAME.tpl.php");

?>