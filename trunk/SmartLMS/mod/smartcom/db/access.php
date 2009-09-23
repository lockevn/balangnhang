<?php

  $mod_smartcom_capabilities = array(
	'mod/smartcom:buyticket' => array(
		'riskbitmask' => RISK_CONFIG,
		'captype' => 'read',
		'contextlevel' => CONTEXT_COURSE,
		'legacy' => array(
			'student' => CAP_ALLOW			
		)
	)
)

?>