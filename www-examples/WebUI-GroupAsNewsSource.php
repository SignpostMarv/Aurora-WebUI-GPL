<?php
namespace{
	require_once('../config.php');

	use Aurora\Addon\WebUI\Configs;
	use Aurora\Addon\WebUI\GroupRecord;

	$group = Configs::d()->GetGroup('Foobar');
	
	var_dump(
		($group instanceof GroupRecord) ? Configs::d()->GroupAsNewsSource(
			$group
		) : false
	);
}
?>