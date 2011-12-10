<?php
namespace{
	require_once('../config.php');

	use Aurora\Addon\WebUI\Configs;

	list($user) = Configs::d()->CreateAccount(
		md5(uniqid('DeleteUser', true)) . ' TesterDeleteUser',
		'testpass',
		'foo@example.com',
		'AuroraTest',
		0,
		'1970-01-01',
		'Bob'
	);

	var_dump(
		$user,
		Configs::d()->DeleteUser(
			$user // this could be a UUID string instead of an instance of WebUI::abstractUser
		)
	);
}
?>