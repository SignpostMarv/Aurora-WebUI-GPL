<?php
	date_default_timezone_set('Europe/London'); // this is just to get rid of pesky errors
	header('Content-Type: text/plain');
	require_once('../libs/load.php');
	$WebUI = Aurora\Addon\WebUI::r(
		'http://localhost:8007/WIREDUX',
		'Password'
	);

	if($WebUI->CheckIfUserExists('Tester EditUser') === false){
		$user = $WebUI->CreateAccount(
			'Tester EditUser',
			'testpass',
			'foo@example.com',
			'AuroraTest',
			0,
			'1970-01-01',
			'Bob'
		);
	}else{
		try{
			$user = $WebUI->Login(
				'Tester EditUser',
				'testpass'
			);
		}catch(Exception $e){
			$user = $WebUI->Login(
				'Tester EditUser',
				'testpass'
			);
		}
	}

	var_dump(
		$WebUI->EditUser(
			$user, // this could be a UUID string instead of an instance of WebUI::abstractUser
			$user->Name(),
			'bar@example.com',
			new Aurora\Addon\WebUI\RLInfo(
				'',
				'',
				'',
				'',
				''
			)
		),
		$WebUI->GetProfile(
			$user
		),
		$WebUI->EditUser(
			$user, // this could be a UUID string instead of an instance of WebUI::abstractUser
			$user->Name(),
			'bar@example.com',
			new Aurora\Addon\WebUI\RLInfo(
				'Sherlock Holmes',
				'221b Baker Street',
				'NW1 6XE',
				'London',
				'England'
			)
		),
		$WebUI->GetProfile(
			$user
		)
	);
?>