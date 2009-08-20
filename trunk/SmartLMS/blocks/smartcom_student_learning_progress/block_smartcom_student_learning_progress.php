<html>
<head>
</head>
<body>
<?php require_once($_SERVER['DOCUMENT_ROOT']."/config.php");
global $CFG;

require_once(ABSPATH.'lib/ofc-library/open_flash_chart_object.php');

echo 'Hello World!';
open_flash_chart_object( 500, 250, '/blocks/smartcom_student_learning_progress/student_learning_progress_ofc_data.php', false, '/' );

?>
</body>
</html>