<?php require_once($_SERVER['DOCUMENT_ROOT']."/Gconfig.php");

require_once($_SERVER['DOCUMENT_ROOT']."/config.php");

$courseid = required_param('courseid', PARAM_INT);   // course
$userid = required_param('userid', PARAM_INT);   // course


$recs = get_records_sql(
"select MaxGrade.quiz as quizid, 100*SumGrades/MaxSumGrades as grade
from
(
select id as quiz, sumgrades as MaxSumGrades from mdl_quiz where course=$courseid and sumgrades>0
) as MaxGrade
join
(
select quiz, max(sumgrades) as sumgrades
from `mdl_quiz_attempts`
where userid=$userid and quiz in (select id from mdl_quiz where course=$courseid and sumgrades>0)
group by quiz
) as UserGrade
on MaxGrade.quiz = UserGrade.quiz"
);

echo json_encode($recs);
?>