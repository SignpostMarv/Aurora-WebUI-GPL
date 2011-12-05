<?php
namespace{
	require_once('../config.php');
	require_once('../plugins/load.php');

	header('Content-Type: text/html');
	do_action('grid_selector');
}
?>