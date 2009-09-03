<?php require_once($_SERVER['DOCUMENT_ROOT']."/Gconfig.php");
require_once($_SERVER['DOCUMENT_ROOT']."/config.php");

$nLastSeenSecond = 3000; //Seconds default, last seen
$timefrom = 100 * floor((time()-$nLastSeenSecond) / 100); // Round to nearest 100 seconds for better query cache

$onlineUsers = get_records_sql(
"select u.id as id, username, ul.timeaccess as lastaccess
from mdl_user as u join mdl_user_lastaccess as ul
on u.id = ul.userid
where courseid = $courseid
and ul.timeaccess > $timefrom
order by lastaccess DESC"
);


$tpl->assign('courseid', $courseid);
$tpl->assign('onlineUsers', $onlineUsers);
		
$FILENAME = 'realtime_performance_check';
$$FILENAME = $tpl->fetch("$FILENAME.tpl.php");

?>