<?php
	date_default_timezone_set('Europe/London'); // this is just to get rid of pesky errors
	header('Content-Type: text/plain');
	require_once('../libs/load.php');
	$WebUI = Aurora\Addon\WebUI::r(
		'http://localhost:8007/WIREDUX',
		'Password'
	);
	var_dump(
		$WebUI->GetAbuseReports(),
		$WebUI->GetAbuseReports(0,25,false)
	);
?>