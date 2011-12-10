<?php
namespace{
	require_once('../config.php');

	use Aurora\Addon\WebUI\Configs;

	$password = 'testpass';
	if(Configs::d()->CheckIfUserExists('Tester ConfirmUserEmailName') === false){
		list($user) = Configs::d()->CreateAccount(
			'Tester ConfirmUserEmailName',
			'testpass',
			'foo@example.com',
			'AuroraTest',
			0,
			'1970-01-01',
			'Bob'
		);
	}else{
		$user = Configs::d()->Login(
			'Tester ConfirmUserEmailName',
			'testpass'
		);
	}

	var_dump(
		$user,
		Configs::d()->ConfirmUserEmailName(
			$user->Name(), // this could be a UUID string instead of an instance of WebUI::GridUserInfo
			'foo@example.com'
		)
	);
}
?>