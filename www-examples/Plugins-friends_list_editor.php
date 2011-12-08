<?php
namespace{
	require_once('../config.php');
	require_once('../plugins/load.php');
	use Aurora\Addon\WebUI\Configs;

	header('Content-Type: text/html');
	do_action('friends_list_editor', Configs::d()->GetFriends(Configs::d()->Login(
		'Test User',
		'testpass'
	)));
}
?>