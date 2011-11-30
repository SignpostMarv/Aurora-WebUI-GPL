<?php
	date_default_timezone_set('Europe/London'); // this is just to get rid of pesky errors
	header('Content-Type: text/plain');
	require_once('../libs/load.php');
	$WebUI = Aurora\Addon\WebUI::r(
		'http://localhost:8007/WIREDUX',
		'Password'
	);
	$user = $WebUI->Login(
		'Test User',
		'testpass'
	);
	var_dump($WebUI->Authenticated(
		$user // we can use either User objects
	),$WebUI->Authenticated(
		$user->PrincipalID() // or UUID strings.
	));
?>