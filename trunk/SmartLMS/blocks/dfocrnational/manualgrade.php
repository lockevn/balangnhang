<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title>Untitled Document</title>
</head>

<body>
<?php 
	global $CFG, $USER, $COURSE;
	require_once("../../config.php");
	require_once("../../lib/datalib.php");
	require_login();
	//require_once($CFG->dirroot.'/blocks/dfocrnational/lib.php');
	$giid = $_GET[giid];
	$sql = "SELECT gi.iteminfo, gi.itemname, gi.courseid FROM {$CFG->prefix}grade_items gi WHERE gi.id=$giid";
	$iteminfo = get_record_sql($sql);
	$cid = $iteminfo->courseid;
	$sql = "SELECT c.fullname FROM {$CFG->prefix}course c WHERE c.id=$cid";
	$coursename = get_record_sql($sql);
	echo "<h2> $coursename->fullname </h2>";
	echo "<h3> $iteminfo->itemname </h3>";
	echo $iteminfo->iteminfo;
?>
</body>
</html>
