<?php
namespace{
	require_once('../config.php');

	use Aurora\Addon\WebUI\Configs;

	var_dump(
		Configs::d()->CreateAccount(
			'Newly Created',
			'testpass',
			'foo@example.com',
			'AuroraTest',
			0,
			'1970-01-01',
			'Bob'
		)
	);
}
?>