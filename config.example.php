<?php
namespace{
	@date_default_timezone_set(@date_default_timezone_get()); // this is just to get rid of pesky errors

	header('Content-Type: text/plain');

	require_once('libs/load.php');

	define('libAuroraTemplateNavigationNoStrictID', true);

	use Aurora\Addon\WebUI;
	use Aurora\Addon\WebUI\Configs;
	use Aurora\Addon\MapAPI;

	$configs = Configs::i();

	$configs[] = WebUI::r(
		'http://localhost:8007/WEBUI',
		'Password'
	);
//	$configs[$configs->count() - 1]->attachAPI(MapAPI::r(
//		'http://localhost:8007/mapapi'
//	));

	Globals::i()->baseURI = 'http://localhost/';
	Globals::i()->linkStyle = 'query'; // mod_rewrite or query
	Globals::i()->registrationPostalRequired = false; // TRUE if postal address info is required for registration, FALSE otherwise.
	Globals::i()->registrationActivationRequired = false; // TRUE if activation is required for registration, FALSE otherwise. NOTE: we're not specifying activation method here for a reason.
	Globals::i()->registrationEmailRequired = false; // TRUE if emails are required, FALSE if they're optional.
	Globals::i()->regexUsername = '^[A-z]{1}[A-z0-9]*\ [A-z]{1}[A-z0-9]*$';
	Globals::i()->regexPassword = '^.{8}.*$';
//	Globals::i()->DBLink = new libAurora\DataManager\MySQLDataLoader('', 'WebUIGPL', false, true, '5.5');
//	Globals::i()->Nonces = libAurora\Nonces::r(Globals::i()->DBLink);

//!	In order to use the reCAPTCHA lib, you must get public and private keys from https://www.google.com/recaptcha/admin/create
//	Globals::i()->recaptcha                 = true;
//	Globals::i()->recaptchaPublicKey        = 'foo' ;
//	Globals::i()->recaptchaPrivateKey       = 'bar' ;
//	Globals::i()->recaptchaEnableJavaScript = false ; // set to TRUE to enable the prettier but JavaScript-powered reCAPTCHA input
}
?>
