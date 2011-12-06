<?php
namespace{
	date_default_timezone_set('Europe/London'); // this is just to get rid of pesky errors

	header('Content-Type: text/plain');

	require_once('libs/load.php');

	use Aurora\Addon\WebUI;
	use Aurora\Addon\WebUI\Configs;

	$configs = Configs::i();

	$configs[] = WebUI::r(
		'http://localhost:8007/WIREDUX',
		'Password'
	);

	Globals::i()->baseURI = 'http://localhost/';
	Globals::i()->WebUI = Configs::d(); // the idea here is to later use session/user-preference data to dynamically change this
}
?>
