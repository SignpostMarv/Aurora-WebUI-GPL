<?php
namespace{
	require_once('../config.php');

	use Aurora\Addon\WebUI\Configs;

	var_dump(
		Configs::d()->GetParcel('Your Parcel', Configs::d()->GetRegion(
			'Foo 1'
		))
	);
}
?>