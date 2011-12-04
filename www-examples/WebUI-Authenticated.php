<?php
namespace{
	require_once('../config.php');

	use Aurora\Addon\WebUI\Configs;

	$user = Configs::d()->Login(
		'Test User',
		'testpass'
	);
	var_dump(
		Configs::d()->Authenticated($user), // we can use either User objects
		Configs::d()->Authenticated($user->PrincipalID()) // or UUID strings.
	);
}
?>