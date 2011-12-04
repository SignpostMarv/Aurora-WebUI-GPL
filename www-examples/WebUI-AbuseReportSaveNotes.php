<?php
namespace{
	require_once('../config.php');

	use Aurora\Addon\WebUI\Configs;

	var_dump(
		Configs::d()->AbuseReportSaveNotes(1, 'Elementary, my dear Watson; The accused is an asshat.')
	);
}
?>