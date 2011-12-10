<?php
namespace{
	require_once('../config.php');

	use Aurora\Addon\WebUI\Configs;

	if(Configs::d()->CheckIfUserExists('Tester BanUser') === false){
		list($user) = Configs::d()->CreateAccount(
			'Tester BanUser',
			'testpass',
			'foo@example.com',
			'AuroraTest',
			0,
			'1970-01-01',
			'Bob'
		);
	}else{
		$user = Configs::d()->Login(
			'Tester BanUser',
			'testpass'
		);
	}

	var_dump(
		Configs::d()->UnBanUser($user)
	);
}
?>