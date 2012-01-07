<?php
namespace{
	require_once('../config.php');

	use Aurora\Addon\WebUI\Configs;
	$Estate = Configs::d()->GetEstates(Configs::d()->Login(
		'Test User',
		'testpass'
	))->current();

	var_dump(
		Configs::d()->GetRegionsInEstate(
			$Estate,
			Aurora\Framework\RegionFlags::RegionOnline
		),
		Configs::d()->GetRegionsInEstate(
			$Estate,
			Aurora\Framework\RegionFlags::RegionOnline,
			0,
			10
		),
		Configs::d()->GetRegionsInEstate(
			$Estate,
			Aurora\Framework\RegionFlags::RegionOnline,
			0,
			10,
			true
		),
		Configs::d()->GetRegionsInEstate(
			$Estate,
			Aurora\Framework\RegionFlags::RegionOnline,
			0,
			10,
			true,
			true
		),
		Configs::d()->GetRegionsInEstate(
			$Estate,
			Aurora\Framework\RegionFlags::RegionOnline,
			0,
			10,
			true,
			true,
			true
		)
	);
}
?>