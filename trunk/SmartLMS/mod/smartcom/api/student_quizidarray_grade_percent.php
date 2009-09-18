<?php require_once($_SERVER['DOCUMENT_ROOT']."/Gconfig.php");
require_once($_SERVER['DOCUMENT_ROOT']."/config.php");

define('AJAX_CALL',true);
require_login();
// header("Content-type: text/javascript;charset=utf-8");


$courseid = required_param('courseid', PARAM_INT);
$userid = required_param('userid', PARAM_INT);
$childquizid = required_param('childquizid', PARAM_SEQUENCE);

$recs = get_field_sql(
"
select ROUND(100 * sum(SumGrades) / sum(MaxSumGrades)) as grade
from
(
	select id as quiz, sumgrades as MaxSumGrades from mdl_quiz where id in ($childquizid) and sumgrades>0
) as MaxGrade
join
(
	select quiz, max(sumgrades) as sumgrades
	from `mdl_quiz_attempts`
	where userid=$userid and quiz in ($childquizid)
	group by quiz
) as UserGrade
on MaxGrade.quiz = UserGrade.quiz
"
);

if($recs)
{
	echo $recs;
}
else
{
	echo 0;
}

?>