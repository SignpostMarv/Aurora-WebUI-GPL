<?php
	use Aurora\Addon\WebUI\Configs;

	if(Globals::i()->section === 'logout'){
		if(Configs::i()->valueOffset(Globals::i()->WebUI) !== false && isset($_SESSION['loggedin'][Configs::i()->valueOffset(Globals::i()->WebUI)]) === true){
			unset($_SESSION['loggedin'][Configs::i()->valueOffset(Globals::i()->WebUI)]);
			session_regenerate_id(true);
		}
		header('Location: ' . Globals::i()->baseURI);
		exit;
	}
?>