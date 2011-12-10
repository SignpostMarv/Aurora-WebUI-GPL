<?php
require_once('../config.php');
require_once('../plugins/load.php');

use Aurora\Addon\WebUI\Configs;

if(isset(Globals::i()->linkStyle) === false){
	Globals::i()->linkStyle = 'query';
}

session_start();
if(isset($_SESSION['loggedin']) === false){
	$_SESSION['loggedin'] = array();
}

switch(Globals::i()->linkStyle){
	case 'mod_rewrite':
		$request = parse_url($_SERVER['REQUEST_URI']);
		$request = $request['path'];
		if(substr($request,0,1) === '/'){
			$request = substr($request,1);
		}
		if(substr($request,-1) === '/'){
			$request = substr($request,0,-1);
		}
	break;
	case 'path':
	default:
		$request = trim(isset($_GET['path']) ? $_GET['path'] : '');
	break;
}
Globals::i()->section = (trim($request) !== '') ? trim($request) : 'home';

if($_SERVER['REQUEST_METHOD'] === 'POST'){
	switch(Globals::i()->section){
		case 'login':
			if(isset($_POST['username'], $_POST['password'], $_POST['grid']) === true && Configs::i()->offsetExists($_POST['grid']) === true){
				Globals::i()->WebUI = Configs::i()->offsetGet($_POST['grid']);
				$login = Globals::i()->WebUI->Login($_POST['username'], $_POST['password']);
				$_SESSION['loggedin'][$_POST['grid']] = $login;
				header('Location: ' . Globals::i()->baseURI);
				exit;
			}
		break;
	}
}

if(isset(Globals::i()->WebUI) === false){
	Globals::i()->WebUI = Configs::d();
}

if(Globals::i()->section === 'logout'){
	if(Configs::i()->valueOffset(Globals::i()->WebUI) !== false && isset($_SESSION['loggedin'][Configs::i()->valueOffset(Globals::i()->WebUI)]) === true){
		unset($_SESSION['loggedin'][Configs::i()->valueOffset(Globals::i()->WebUI)]);
		session_regenerate_id(true);
	}
	header('Location: ' . Globals::i()->baseURI);
	exit;
}

foreach(Configs::i() as $k=>$v){
	if(Globals::i()->WebUI === $v && isset($_SESSION['loggedin'][$k]) === true){
		Globals::i()->loggedIn   = true;
		Globals::i()->loggedInAs = $_SESSION['loggedin'][$k];
		break;
	}
}
if(isset(Globals::i()->loggedIn) === false){
	Globals::i()->loggedIn = false;
}

$file = new SplFileInfo('../templates/default/' . (str_replace('/','_',(strpos(Globals::i()->section, '_') === 0 ? substr(Globals::i()->section,1) : Globals::i()->section))) . '.php');
if($file->isFile() === true && $file->isReadable() === true){
	require_once($file->getPathname()); // not implementing a proper template system yet.
}else{
	require_once('../templates/default/404.php');
}
?>