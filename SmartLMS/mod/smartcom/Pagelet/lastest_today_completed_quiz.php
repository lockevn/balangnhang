<?php require_once($_SERVER['DOCUMENT_ROOT']."/Gconfig.php");
require_once($_SERVER['DOCUMENT_ROOT']."/config.php");
	
$userid = required_param('userid', PARAM_INT);    // A particular attempt ID for review
$courseid = required_param('courseid', PARAM_INT);    // A particular attempt ID for review

// $timeStartOfTheDay = strtotime(date('Y-m-d'));
$quizOfUser = get_records_sql(
"select qa.id as attemptid, q.name, lastest_quiz.*

from
(
select userid, quiz, max(timefinish) as timefinish
from mdl_quiz_attempts
where userid = $userid
and FROM_UNIXTIME(timefinish) <= date(now())
group by userid, quiz
) as lastest_quiz

join `mdl_quiz_attempts` as qa
on lastest_quiz.userid = qa.userid
and lastest_quiz.quiz = qa.quiz
and lastest_quiz.timefinish = qa.timefinish

join mdl_quiz as q
on qa.quiz = q.id
and lastest_quiz.quiz = qa.quiz
and q.course = $courseid
"
);

$quizOfUser = array_values($quizOfUser);

$tpl->assign('courseid', $courseid);
$tpl->assign('userid', $userid);
$tpl->assign('quizOfUser', $quizOfUser);
		
$FILENAME = 'lastest_today_completed_quiz';
$$FILENAME = $tpl->display("$FILENAME.tpl.php");

?>