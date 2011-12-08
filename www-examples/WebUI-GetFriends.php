<?php
namespace{
	require_once('../config.php');

	use Aurora\Addon\WebUI\Configs;
	var_dump(
		Configs::d()->GetFriends(Configs::d()->Login(
			'Test User',
			'testpass'
		))
	);
}
?>