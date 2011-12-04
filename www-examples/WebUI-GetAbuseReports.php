<?php
namespace{
	require_once('../config.php');

	use Aurora\Addon\WebUI\Configs;

	var_dump(
		Configs::d()->GetAbuseReports(),
		Configs::d()->GetAbuseReports(0,25,false)
	);
}
?>