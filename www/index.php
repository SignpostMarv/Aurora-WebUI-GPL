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
	break;
	case 'path':
	default:
		$request = trim(isset($_GET['path']) ? $_GET['path'] : '');
	break;
}

if(substr($request,0,1) === '/'){
	$request = substr($request,1);
}
if(substr($request,-1) === '/'){
	$request = substr($request,0,-1);
}

Globals::i()->section = (trim($request) !== '') ? trim($request) : 'home';

if(isset(Globals::i()->WebUI) === false){
	Globals::i()->WebUI = Configs::d();
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

$pathParts = explode('/', Globals::i()->section);
$section = implode('/',$pathParts);
$file = new SplFileInfo('../templates/default/' . (str_replace('/','_',(strpos($section, '_') === 0 ? substr($section,1) : $section))) . '.php');
while(($file->isFile() === false || $file->isReadable() === false) && count($pathParts) > 1){
	array_pop($pathParts);
	$section = implode('/',$pathParts);
	$file = new SplFileInfo('../templates/default/' . (str_replace('/','_',(strpos($section, '_') === 0 ? substr($section,1) : $section))) . '.php');
}
Globals::i()->sectionFile = $section;
ob_start();
if($file->isFile() === true && $file->isReadable() === true){
	try{
		require_once($file->getPathname()); // not implementing a proper template system yet.
	}catch(Exception $e){
		error_log(print_r($e,true));
		ob_end_clean();
		ob_start();
		require_once('../templates/default/500.php');
	}
}else{
	require_once('../templates/default/404.php');
}
ob_end_flush();
?>