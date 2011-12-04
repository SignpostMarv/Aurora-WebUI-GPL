<?php
	date_default_timezone_set('Europe/London'); // this is just to get rid of pesky errors
	header('Content-Type: text/plain');
	require_once('../libs/load.php');
	$WebUI = Aurora\Addon\WebUI::r(
		'http://localhost:8007/WIREDUX',
		'Password'
	);

	if($WebUI->CheckIfUserExists('Tester BanUser') === false){
		$user = $WebUI->CreateAccount(
			'Tester BanUser',
			'testpass',
			'foo@example.com',
			'AuroraTest',
			0,
			'1970-01-01',
			'Bob'
		);
	}else{
		$user = $WebUI->Login(
			'Tester BanUser',
			'testpass'
		);
	}

	var_dump(
		$WebUI->UnBanUser($user)
	);
?>