<?php

/*
function lms_getSimpleInterfaceOptions() {

	$text =Form::getCheckbox(_LMS_ENABLE_EVENT_UI, "enable_event_ui", "enable_event_ui", 1, true);
	$text.=Form::getCheckbox(_LMS_ENABLE_GROUPSUB_UI, "enable_groupsub_ui", "enable_groupsub_ui", 1, true);

	$text.=Form::getCheckbox(_LMS_USE_CATALOGUE, "lms_use_catalogue", "lms_use_catalogue", 1, false);
	$text.=Form::getCheckbox(_LMS_SEQUENCED_COURSES, "lms_sequenced_courses", "lms_sequenced_courses", 1, false);

	return $text;
}


function lms_checkSimpleInterfaceOptions() {
}


function lms_saveSimpleInterfaceOptions() {
}
*/


function lms_postInstall() {

	$qtxt ="UPDATE learning_setting SET param_value='".$_SESSION["default_sender_email"]."' ";
	$qtxt.="WHERE param_name='admin_mail'";
	$q=$GLOBALS["db"]->query($qtxt);

}

?>
