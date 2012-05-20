<?php
	use Aurora\Addon\WebAPI\Configs;

	if(Globals::i()->section === 'logout'){
		$gridIndex = Configs::i()->valueOffset(Globals::i()->WebUI);
		if($gridIndex !== false){
			if(isset($_SESSION['loggedin'][$gridIndex]) === true){
				unset($_SESSION['loggedin'][$gridIndex]);
			}
			if(isset($_SESSION['loggedinadmin'][$gridIndex]) === true){
				unset($_SESSION['loggedinadmin'][$gridIndex]);
			}
			session_regenerate_id(true);
		}
		header('Location: ' . Globals::i()->baseURI);
		exit;
	}
?>