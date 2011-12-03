<?php
	date_default_timezone_set('Europe/London'); // this is just to get rid of pesky errors
	header('Content-Type: text/plain');
	require_once('../libs/load.php');
	$WebUI = Aurora\Addon\WebUI::r(
		'http://localhost:8007/WIREDUX',
		'Password'
	);

	$password = 'testpass';
	if($WebUI->CheckIfUserExists('Tester ConfirmUserEmailName') === false){
		$user = $WebUI->CreateAccount(
			'Tester ConfirmUserEmailName',
			'testpass',
			'foo@example.com',
			'AuroraTest',
			0,
			'1970-01-01',
			'Bob'
		);
	}else{
		$user = $WebUI->Login(
			'Tester ConfirmUserEmailName',
			'testpass'
		);
	}

	var_dump(
		$user,
		$WebUI->ConfirmUserEmailName(
			$user->Name(), // this could be a UUID string instead of an instance of WebUI::GridUserInfo
			'foo@example.com'
		)
	);
?>