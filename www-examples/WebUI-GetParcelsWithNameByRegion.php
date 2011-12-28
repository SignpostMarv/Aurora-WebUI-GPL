<?php
namespace{
	require_once('../config.php');

	use Aurora\Addon\WebUI\Configs;

	var_dump(
		Configs::d()->GetParcelsWithNameByRegion(
			0, // start point
			10, // max results to return in batch
			'Your Parcel',
			Configs::d()->GetRegion(
				'Foo 1' // region name
		)),
		Configs::d()->GetParcelsWithNameByRegion(
			0, // start point
			10, // max results to return in batch
			'Non-existant Parcel',
			Configs::d()->GetRegion(
				'Foo 1' // region name
		))
	);
}
?>