<?php
	date_default_timezone_set('Europe/London'); // this is just to get rid of pesky errors
	header('Content-Type: text/plain');
	require_once('../libs/load.php');
	print_r(Aurora\Addon\WebUI::r(
		'http://localhost:8007/WIREDUX',
		'Password'
	)->CreateAccount(
		'Newly Created',
		'testpass',
		'foo@example.com',
		'AuroraTest',
		0,
		'1970-01-01',
		'Bob'
	));
?>