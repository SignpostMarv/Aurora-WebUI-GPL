<?php
require_once('../config.php');
require_once('../plugins/load.php');

use Aurora\Addon\WebUI\Configs;
use Aurora\Addon\WebUI\Template\FormProblem;

if(isset(Globals::i()->linkStyle) === false){
	Globals::i()->linkStyle = 'query';
}

session_start();
if(isset($_SESSION['loggedin']) === false){
	$_SESSION['loggedin'] = array();
}
if(isset($_SESSION['loggedinadmin']) === false){
	$_SESSION['loggedinadmin'] = array();
}

switch(Globals::i()->linkStyle){
	case 'mod_rewrite':
		$request = parse_url($_SERVER['REQUEST_URI']);
		$baseURI = parse_url(Globals::i()->baseURI);		
		$pos = strpos($request['path'], $baseURI['path']);
		if($pos >= 0){
			$request['path'] = substr($request['path'], strlen($baseURI['path']));
		}
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

$gridIndex = Configs::i()->valueOffset(Globals::i()->WebUI);

if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login-username'], $_POST['login-password'], $_POST['login-nonce']) === true){
	if(Globals::i()->Nonces->isValid($_POST['login-nonce'])){
		Globals::i()->Nonces->useNonce($_POST['login-nonce']);
		$success = false;
		try{
			$login = Globals::i()->section === 'admin' ? Globals::i()->WebUI->AdminLogin($_POST['login-username'], $_POST['login-password']) : Globals::i()->WebUI->Login($_POST['login-username'], $_POST['login-password']);
			$success = true;
		}catch(InvalidArgumentException $e){
			FormProblem::i()->offsetSet('login-account-credentials',$e->getMessage());
		}
		if($success){
			$_SESSION['loggedin'][$gridIndex] = $login;
			if(Globals::i()->section === 'admin'){
				$_SESSION['loggedinadmin'][$gridIndex] = $login;
			}
			Globals::i()->loggedIn   = true;
			Globals::i()->loggedInAs = $_SESSION['loggedin'][$gridIndex];
		}
	}else{
		FormProblem::i()->offsetSet('login-nonce', __('Nonce has expired'));
	}
}
if(isset(Globals::i()->loggedIn) === false){
	Globals::i()->loggedIn = isset($_SESSION['loggedin'][$gridIndex]);
	Globals::i()->loggedInAs = isset($_SESSION['loggedin'][$gridIndex]) ? $_SESSION['loggedin'][$gridIndex] : null;
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
$doc = ob_get_clean();
if(in_array('gzip', str_getcsv($_SERVER['HTTP_ACCEPT_ENCODING'])) === true){
	$doc = gzencode($doc, 9);
	header('Content-Encoding: gzip');
	header('Vary: Accept-Encoding');
}

header('Last-Modified: ' . date('r', $_SERVER['REQUEST_TIME']));
header('Expires: ' . date('r', $_SERVER['REQUEST_TIME'] + 3600));
header('Cache-Control: max-age=3600, must-revalidate');

$ETag = sha1($doc);
if(isset($_SERVER['HTTP_IF_NONE_MATCH']) === true){
	if(in_array($ETag, str_getcsv($_SERVER['HTTP_IF_NONE_MATCH'])) === true){
		header('HTTP/1.1 304 Not Modified');
		exit;
	}
}

header('Content-Length: ' . strlen($doc));
header('ETag: ' . $ETag);
die($doc);
?>
