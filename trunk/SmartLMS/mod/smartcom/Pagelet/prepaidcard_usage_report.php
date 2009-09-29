<?php require_once($_SERVER['DOCUMENT_ROOT']."/Gconfig.php");
require_once($_SERVER['DOCUMENT_ROOT']."/config.php");

require_login();

$context = get_context_instance(CONTEXT_SYSTEM);
require_capability('mod/smartcom:prepaidcardusagereport', $context);


$FILENAME = 'prepaidcard_usage_report';
$$FILENAME = $tpl->display("$FILENAME.tpl.php");

?>