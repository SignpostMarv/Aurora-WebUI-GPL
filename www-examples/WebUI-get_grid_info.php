<?php
namespace{
	require_once('../config.php');

	use Aurora\Addon\WebUI\Configs;
	var_dump(
		Configs::d()->get_grid_info()
	);
}
?>