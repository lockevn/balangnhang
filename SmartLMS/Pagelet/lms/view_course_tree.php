<?php require_once($_SERVER['DOCUMENT_ROOT']."/config.php");

require_once(ABSPATH."Lib/External/Savant3.php");
require_once(ABSPATH."Lib/DB/DBHelper.php");

$db = DBHelper::GetInstance();
$db->DBLink('docebo');
$result = $db->GetRecords('select * from core_setting');

$FILENAME = 'lms/view_course_tree';
${PageBuilder::PAGELET_PREFIX.$FILENAME} = $tpl->fetch("$FILENAME.tpl.php");

?>