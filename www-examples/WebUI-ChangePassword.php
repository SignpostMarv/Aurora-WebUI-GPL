<?php
namespace{
	require_once('../config.php');

	use Aurora\Addon\WebUI\Configs;

	$password    = 'testpass';
	$newPassword = 'passtest';
	if(Configs::d()->CheckIfUserExists('Tester ChangePassword') === false){
		list($user) = Configs::d()->CreateAccount(
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
			$user = Configs::d()->Login(
				'Tester ChangePassword',
				$password
			);
		}catch(Exception $e){
			$password = 'passtest';
			$user = Configs::d()->Login(
				'Tester ChangePassword',
				$password
			);
			$newPassword = 'testpass';
		}
	}

	var_dump(
		$user,
		Configs::d()->ChangePassword(
			$user, // this could be a UUID string instead of an instance of WebUI::GridUserInfo
			$password,
			$newPassword
		)
	);
}
?>