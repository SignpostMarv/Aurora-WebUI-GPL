<?php
namespace{
	require_once('../config.php');

	use Aurora\Addon\WebUI\Configs;
	
	$MapAPI = Configs::d()->getAttachedAPI('MapAPI');

	var_dump(
		isset($MapAPI) ? $MapAPI->MonolithicRegionLookup() : 'No MapAPI has been attached to the WebUI config'
	);
}
?>