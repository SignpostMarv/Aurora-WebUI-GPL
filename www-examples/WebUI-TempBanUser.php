<?php
namespace{
	require_once('../config.php');

	use Aurora\Addon\WebUI\Configs;

	if(Configs::d()->CheckIfUserExists('Tester TempBanUser') === false){
		list($user) = Configs::d()->CreateAccount(
			'Tester TempBanUser',
			'testpass',
			'foo@example.com',
			'AuroraTest',
			0,
			'1970-01-01',
			'Bob'
		);
	}else{
		$user = Configs::d()->Login(
			'Tester TempBanUser',
			'testpass'
		);
	}

	var_dump(
		Configs::d()->BanUser($user, new DateTime('+1 hour')),
		Configs::d()->TempBanUser($user, new DateTime('+1 hour')),
		Configs::d()->TempBanUser($user, '+1 hour'),
		Configs::d()->TempBanUser($user, '2014-01-01 00:00:00')
	);
}
?>