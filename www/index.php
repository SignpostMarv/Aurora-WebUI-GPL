<?php
require_once('../config.php');
require_once('../plugins/load.php');

if(isset(Globals::i()->linkStyle) === false){
	Globals::i()->linkStyle = 'query';
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
		Globals::i()->section = trim($request);
	break;
	case 'path':
	default:
		Globals::i()->section = trim(isset($_GET['path']) ? $_GET['path'] : '');
	break;
}

header('Content-Type: text/html');

$file = new SplFileInfo('../templates/default/' . ((Globals::i()->section === '') ? 'index' : str_replace('/','_',(strpos(Globals::i()->section, '_') === 0 ? substr(Globals::i()->section,1) : Globals::i()->section))) . '.php');
if($file->isFile() === true && $file->isReadable() === true){
	require_once($file->getPathname()); // not implementing a proper template system yet.
}else{
	require_once('../templates/default/404.php');
}
?>