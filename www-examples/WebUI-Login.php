<?php
namespace{
	require_once('../config.php');

	use Aurora\Addon\WebUI\Configs;
	var_dump(
		Configs::d()->Login(
			'Test User',
			'testpass'
		)
	);
}
?>