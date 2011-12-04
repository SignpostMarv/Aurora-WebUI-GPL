<?php
namespace{
	require_once('../config.php');

	use Aurora\Addon\WebUI\Configs;
	use Aurora\Addon\WebUI\RLInfo;

	if(Configs::d()->CheckIfUserExists('Tester EditUser') === false){
		$user = Configs::d()->CreateAccount(
			'Tester EditUser',
			'testpass',
			'foo@example.com',
			'AuroraTest',
			0,
			'1970-01-01',
			'Bob'
		);
	}else{
		$user = Configs::d()->Login(
			'Tester EditUser',
			'testpass'
		);
	}

	var_dump(
		Configs::d()->EditUser(
			$user, // this could be a UUID string instead of an instance of WebUI::abstractUser
			$user->Name(),
			'bar@example.com',
			new RLInfo(
				'',
				'',
				'',
				'',
				''
			)
		),
		Configs::d()->GetProfile(
			$user
		),
		Configs::d()->EditUser(
			$user, // this could be a UUID string instead of an instance of WebUI::abstractUser
			$user->Name(),
			'bar@example.com',
			new RLInfo(
				'Sherlock Holmes',
				'221b Baker Street',
				'NW1 6XE',
				'London',
				'England'
			)
		),
		Configs::d()->GetProfile(
			$user
		)
	);
}
?>