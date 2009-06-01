<?php


function cms_postInstall() {

	$qtxt ="UPDATE cms_setting SET param_value='".$_SESSION["default_sender_email"]."' ";
	$qtxt.="WHERE param_name='cms_admin_mail'";
	$q=$GLOBALS["db"]->query($qtxt);

}


?>
