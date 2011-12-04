<?php
namespace{
	require_once('../config.php');

	use Aurora\Addon\WebUI\Configs;

	$user = Configs::d()->Login(
		'Test User',
		'testpass'
	);
	var_dump(
		Configs::d()->SaveEmail(
			$user, // we can use either User objects
			'foo@example.com'
		),
		Configs::d()->SaveEmail( // because GetGridUserInfo wraps to a registry method, this should always be the same instance unless the user logged in/out between calls.
			$user->PrincipalID(), // or UUID strings.
			'foo@example.com'
		)
	);
}
?>