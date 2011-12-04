<?php
namespace{
	require_once('../config.php');

	use Aurora\Addon\WebUI\Configs;

	var_dump(
		Configs::d()->FindUsers(),
		Configs::d()->FindUsers('User')
	);
}
?>