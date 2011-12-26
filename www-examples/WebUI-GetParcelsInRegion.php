<?php
namespace{
	require_once('../config.php');

	use Aurora\Addon\WebUI\Configs;

	var_dump(
		Configs::d()->GetParcelsByRegion(Configs::d()->GetRegion(
			'Foo 1'
		))
	);
}
?>