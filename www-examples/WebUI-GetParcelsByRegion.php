<?php
namespace{
	require_once('../config.php');

	use Aurora\Addon\WebUI\Configs;

	var_dump(
		Configs::d()->GetParcelsByRegion(
			0, // start point
			10, // max results to return in batch
			Configs::d()->GetRegion(
				'Foo 1' // region name
		))
	);
}
?>