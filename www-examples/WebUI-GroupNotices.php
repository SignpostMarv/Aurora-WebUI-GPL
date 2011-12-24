<?php
namespace{
	require_once('../config.php');

	use Aurora\Addon\WebUI\Configs;

	var_dump(
		Configs::d()->GroupNotices(
			0,
			10,
			array(
				Configs::d()->GetGroup('Foobar')
			)
		)
	);
}
?>