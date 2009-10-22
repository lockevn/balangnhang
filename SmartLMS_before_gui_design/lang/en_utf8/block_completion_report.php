<?PHP // $Id: block_course_summary.php,v 1.1.8.1 2006/11/01 09:37:06 moodler Exp $ 
      // block_course_summary.php - created with Moodle 1.7 beta + (2006101003)

/* configure.php */

$string['blockname'] = 'Completion Report';
$string['completionreport'] = 'Completion Report';
$string['helpwithcompletionreport'] = 'Help with Completion Report';
$string['configurerequirementrubrics'] = 'Configure&nbsp;Requirement&nbsp;Rubrics';
$string['generatereport'] = 'Generate&nbsp;Report';

$string['configurecompletionrequirements'] = 'Configure Completion Requirements';
$string['pleasechooseacourse'] = 'Please choose a course to configure its completion requirement rubrics:';
$string['configuringcoursecompletionrequirementrubrics'] = 'Configuring Course Completion Requirement Rubrics for the Course:';
$string['savesetting'] = 'Save the settings for each section separately by clicking on the four \"Save ... Settings\" buttons on the right.';

$string['assignmentcompletionisrequired'] = '<b>Assignment Completion</b> is Required if this is checked ';
$string['passinggradeforassignment'] = 'Passing Grade for Assignment: ';
$string['gradeappliesto'] = 'Grade applies to:&nbsp;';
$string['finalproject'] = 'Final Project';
$string['averageofallassignments'] = 'Average of All Assignments';
$string['choosefinalproject'] = 'If grade applies to a final project, choose the Final Project:&nbsp;';
$string['saveassignmentsettings'] = 'Save Assignment Settings';

$string['quizcompletionisrequired'] = '<b>Quiz Completion</b> is Required if this is checked';
$string['passinggradeforquizzes'] = 'Passing Grade for Quizzes:';
$string['finalexam'] = 'Final Exam';
$string['averageofallquizzes'] = 'Average of All Quizzes';
$string['choosefinalexam'] = 'If grade applies to a final exam, choose the Final Exam:&nbsp;';
$string['savequizsettings'] = 'Save Quiz Settings';

$string['resourcecompletionisrequired'] = '<b>Resource Completion</b> is Required if this is checked '; 
$string['listofrequiredresources'] = 'List of Required Resources';
$string['listofresourcesnotrequired'] = 'List of Resources Not Required';
$string['removefromrequirements'] = 'Remove from Requirements';
$string['addtorequirements'] = 'Add to Requirements';
$string['saveresourcessettings'] = 'Save Resources Settings';

$string['manualgradeisapplied'] = '<b>Manual Grade</b> is applied if this is checked'; 
$string['passinggrade'] = 'Passing&nbsp;Grade:&nbsp;';
$string['savemanualgradesettings'] = 'Save Manual Grade Settings';

$string['newassignmentsettingssaved'] = 'New assignment settings saved';
$string['assignmentsettingsnotsaved'] = 'New assignment settings not saved; please try again.';
$string['newquizsettingssaved'] = 'New quiz settings saved';
$string['quizsettingsnotsaved'] = 'New quiz settings not saved; please try again.';
$string['newresourcessettingssaved'] = 'New resources settings saved';
$string['resourcessettingsnotsaved'] = 'New resources settings not saved; please try again.';
$string['newmanualgradesettingssaved'] = 'New manual grade settings saved';
$string['manualsettingsnotsaved'] = 'New manual grade settings not saved; please try again.';

$string['displaycompletionreportforthiscourse'] = 'Display Completion Report for this course';

/* report.php */

$string['choosecoursedisplayreport'] = 'Please choose a course to display its Completion Report:';
$string['showcompletionreportforselectedcourse'] = 'show completion report for the selected course';
$string['configurerequirementrubricsforthiscourse'] = 'Configure Requirement Rubrics for this course';
$string['completionreportforthecourse'] = 'Completion Report for the Course:';
$string['numberofrequiredresources'] = 'Number of Required Resources';
$string['clicktoseelistofrequiredresources'] = 'Click here to see list of Required Resources';
$string['manualgradepassinggrade'] = 'Manual&nbsp;Grade<br />passing&nbsp;grade:&nbsp;'; 
$string['completionstatus'] = 'Completion Status';
$string['numberofrequiredresourcesaccessed'] = 'Number of Required Resources Accessed';
$string['clickheretoseelistofaccessedresources'] = 'Click here to see list of Accessed Resources';

$string['notrequired'] = 'Not Required';

$string['completed'] = 'Completed';
$string['incomplete'] = 'Incomplete';

$string['participant'] = 'Participant';
$string['notgraded'] = 'not graded';
$string['achievedgrade'] = 'Achieved&nbsp;Grade:';
$string['assignments'] = 'Assignments';
$string['quizzes'] = 'Quizzes';
$string['resources'] = 'Resources';
$string['manualgrade'] = 'Manual&nbsp;Grade';
$string['none'] = 'none';

$string['coursecompletion'] = 'Course&nbsp;Completion';

/* required_resources.php */

$string['requiredresourcesinthecourse'] = 'Required Resources in the course';
$string['requiredresources'] = 'Required Resources';
$string['noresourcesarerequired'] = 'No resources are required.';

/* accessed_resources.php */

$string['inthecourse'] = 'in the course';
$string['accessed'] = 'accessed&nbsp;'; 
$string['outof'] = '&nbsp;out&nbsp;of&nbsp;'; 
$string['oftherequiredresources'] = ' of the Required Resources';
$string['othersnotinthelistofrequiredresources'] = ' others not in the list of Required Resources';
$string['notinthelistofrequiredresources'] = ' not in the list of Required Resources';
$string['alsoaccessed'] = 'Also accessed&nbsp;';
$string['and'] = 'and';
$string['resources'] = 'Resources';

/* participant.php */

$string['course'] = 'Course';
$string['participants'] = 'Participants';
$string['showcompletionreportforthisuser'] = 'show Completion Report for this user';
$string['completionreportfor'] = 'Completion Report for';
$string['assignmentscompletionstatus'] = 'Assignments Completion Status';
$string['passinggraderequired'] = 'Passing Grade required';
$string['currentgrade'] = 'Current Grade';
$string['averagegradesfromallassignments'] = 'Average Grades from all assignments';
$string['completionreportfor'] = 'Completion Report for '; 
$string['quizzescompletionstatus'] = 'Quizzes Completion Status';
$string['averagegradesfromallquizzes'] = 'Average Grades from all Quizzes';
$string['resourcescompletionstatus'] = 'Resources Completion Status';
$string['resourcesrequired'] = 'Resources required';
$string['resourcescompleted'] = 'Resources completed';
$string['manualgradecompletionstatus'] = 'Manual Grade Completion Status';
$string['changecurrentgrade'] = 'Change Current Grade';
$string['coursecompletionstatussummary'] = 'Course Completion Status Summary';
$string['for'] = 'for';

$string['thefinalprojectnamed'] = 'the Final Project named,';
$string['notattempted'] = 'not attempted';














?>
