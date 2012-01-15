<?php
//!	This is where we load all PHP libs we explicitly need.
namespace{
	require_once('backpress/includes/functions.plugin-api.php'); // we need this for the is_email() function.
	require_once('backpress/includes/functions.formatting.php'); // we need this for the is_email() function.
	require_once('backpress/includes/functions.bp-options.php'); // we need this for the esc_attr() function.
	require_once('backpress/includes/functions.kses.php'); // we need this for the wp_kses() function.
	require_once('backpress/includes/pomo/mo.php'); // we need this for the esc_attr() function.
	require_once('recaptcha/recaptchalib.php'); // we need this for the esc_attr() function.
	require_once('Aurora/load.php');

	use Aurora\Addon\WebUI;

	function __($text, $domain = 'default') {
		return WebUI\pomo::i()->get_translations($domain)->translate($text);
	}
}
?>