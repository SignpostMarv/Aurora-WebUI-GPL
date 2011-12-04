<?php
namespace{
	require_once('../config.php');

	use Aurora\Addon\WebUI\Configs;
	
	$user = Configs::d()->CreateAccount(
		'Tester ChangeName',
		'testpass',
		'foo@example.com',
		'AuroraTest',
		0,
		'1970-01-01',
		'Bob'
	);

	var_dump(
		$user,
		Configs::d()->ChangeName(
			$user, // this could be a UUID string instead of an instance of WebUI::GridUserInfo
			md5(uniqid($user->Name(), true)) . ' ' . $user->LastName()
		),
		Configs::d()->GetGridUserInfo($user)
	);
}
?>