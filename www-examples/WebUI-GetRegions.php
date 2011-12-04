<?php
namespace{
	require_once('../config.php');

	use Aurora\Addon\WebUI\Configs;

	var_dump(
		Configs::d()->getRegions(
			Aurora\Framework\RegionFlags::RegionOnline
		)
	);
}
?>