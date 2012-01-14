<?php
namespace{
	date_default_timezone_set('Europe/London'); // this is just to get rid of pesky errors

	header('Content-Type: text/plain');

	require_once('libs/load.php');

	use Aurora\Addon\WebUI;
	use Aurora\Addon\WebUI\Configs;
	use Aurora\Addon\MapAPI;

	$configs = Configs::i();

	$configs[] = WebUI::r(
		'http://localhost:8007/WEBUI',
		'Password'
	);
	$configs[$configs->count() - 1]->attachAPI(MapAPI::r(
		'http://localhost:8007/mapapi'
	));

	Globals::i()->baseURI = 'http://localhost/';
	Globals::i()->linkStyle = 'query'; // mod_rewrite or query
	Globals::i()->registrationPostalRequired = false; // TRUE if postal address info is required for registration, FALSE otherwise.
	Globals::i()->registrationActivationRequired = false; // TRUE if activation is required for registration, FALSE otherwise. NOTE: we're not specifying activation method here for a reason.
	Globals::i()->registrationEmailRequired = false; // TRUE if emails are required, FALSE if they're optional.
}
?>
