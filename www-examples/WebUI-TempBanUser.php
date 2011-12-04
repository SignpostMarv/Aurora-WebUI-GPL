<?php
	date_default_timezone_set('Europe/London'); // this is just to get rid of pesky errors
	header('Content-Type: text/plain');
	require_once('../libs/load.php');
	$WebUI = Aurora\Addon\WebUI::r(
		'http://localhost:8007/WIREDUX',
		'Password'
	);

	if($WebUI->CheckIfUserExists('Tester TempBanUser') === false){
		$user = $WebUI->CreateAccount(
			'Tester TempBanUser',
			'testpass',
			'foo@example.com',
			'AuroraTest',
			0,
			'1970-01-01',
			'Bob'
		);
	}else{
		$user = $WebUI->Login(
			'Tester TempBanUser',
			'testpass'
		);
	}

	var_dump(
		$WebUI->BanUser($user, new DateTime('+1 hour')),
		$WebUI->TempBanUser($user, new DateTime('+1 hour')),
		$WebUI->TempBanUser($user, '+1 hour'),
		$WebUI->TempBanUser($user, '2014-01-01 00:00:00')
	);
?>