<?php require_once($_SERVER['DOCUMENT_ROOT']."/Gconfig.php");
require_once($_SERVER['DOCUMENT_ROOT']."/config.php");

require_login();

$courseid = required_param('courseid', PARAM_INT);   // course

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


$context = get_context_instance(CONTEXT_SYSTEM);
foreach ($onlineUsers as $key => &$onlineuser) {
	if(has_capability('mod/smartcom:realtimesupported', $context, $key, false))
	{
	}
	else
	{
		// tháo bỏ các user không có quyền realtimesupported
		unset($onlineUsers[$key]);
	}
}
unset($onlineuser);


$tpl->assign('courseid', $courseid);
$tpl->assign('onlineUsers', $onlineUsers);
		
$FILENAME = 'online_user_in_course';
$$FILENAME = $tpl->display("$FILENAME.tpl.php");

?>