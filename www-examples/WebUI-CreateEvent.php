<?php
namespace{
	require_once('../config.php');

	use Aurora\Addon\WebUI\Configs;

	var_dump(
		Configs::d()->CreateEvent(
			Configs::d()->Login(
				'Test User',
				'testpass'
			),
			Configs::d()->GetRegion('Foo 1'),
			new DateTime,
			0,
			0,
			0,
			10,
			new OpenMetaverse\Vector3(13,37,10),
			'Test Event',
			'Test Event Description',
			'Miscellaneous'
		)
	);
}
?>