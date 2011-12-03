<?php
	date_default_timezone_set('Europe/London'); // this is just to get rid of pesky errors
	header('Content-Type: text/plain');
	require_once('../libs/load.php');
	$WebUI = Aurora\Addon\WebUI::r(
		'http://localhost:8007/WIREDUX',
		'Password'
	);
	
	$user = $WebUI->CreateAccount(
		md5(uniqid('DeleteUser', true)) . ' TesterDeleteUser',
		'testpass',
		'foo@example.com',
		'AuroraTest',
		0,
		'1970-01-01',
		'Bob'
	);

	var_dump(
		$user,
		$WebUI->DeleteUser(
			$user // this could be a UUID string instead of an instance of WebUI::abstractUser
		)
	);
?>