<?php
	date_default_timezone_set('Europe/London'); // this is just to get rid of pesky errors
	header('Content-Type: text/plain');
	require_once('../libs/load.php');
	$WebUI = Aurora\Addon\WebUI::r(
		'http://localhost:8007/WIREDUX',
		'Password'
	);

	$password = 'testpass';
	if($WebUI->CheckIfUserExists('Tester ChangePassword') === false){
		$user = $WebUI->CreateAccount(
			'Tester ChangePassword',
			$password,
			'foo@example.com',
			'AuroraTest',
			0,
			'1970-01-01',
			'Bob'
		);
	}else{
		try{
			$user = $WebUI->Login(
				'Tester ChangePassword',
				$password
			);
			$newPassword = 'passtest';
		}catch(Exception $e){
			$password = 'passtest';
			$user = $WebUI->Login(
				'Tester ChangePassword',
				$password
			);
			$newPassword = 'testpass';
		}
	}

	var_dump(
		$user,
		$WebUI->ChangePassword(
			$user, // this could be a UUID string instead of an instance of WebUI::GridUserInfo
			$password,
			$newPassword
		)
	);
?>