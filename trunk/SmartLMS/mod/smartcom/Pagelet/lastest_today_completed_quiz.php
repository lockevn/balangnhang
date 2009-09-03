<?php require_once($_SERVER['DOCUMENT_ROOT']."/Gconfig.php");
require_once($_SERVER['DOCUMENT_ROOT']."/config.php");


$userid = required_param('userid', PARAM_INT);    // A particular attempt ID for review
$courseid = required_param('courseid', PARAM_INT);    // A particular attempt ID for review


// $timeStartOfTheDay = strtotime(date('Y-m-d'));
$quizreview = get_records_sql(
"select userid, quiz,  max(attempt) as maxattempt, max(timefinish) as timefinish from `mdl_quiz_attempts` as qa
join mdl_quiz as q on qa.quiz = q.id
where userid = $userid
and q.course = $courseid
and FROM_UNIXTIME(timefinish) <= date(now())

group by quiz, userid
order by timefinish desc
limit 0,1"
);

$quizreview = array_values($quizreview);
$tpl->assign('quizreview', $quizreview[0]);
	
$FILENAME = 'lastest_today_completed_quiz';
echo $$FILENAME = $tpl->fetch("$FILENAME.tpl.php");

?>