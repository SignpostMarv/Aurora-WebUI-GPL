<?php
//! plugin loader, currently loads everything it finds as we don't have anywhere to persist preferences yet.
namespace Aurora\Addon\WebUI\plugins{

	use DirectoryIterator;
	use SplFileInfo;

	$dir = new DirectoryIterator(dirname(__FILE__));

	foreach($dir as $fileInfo){
		if($fileInfo->isDot() === true || $fileInfo->isDir() === false || $fileInfo->isReadable() === false || preg_match('/^[A-z0-9\-\_]+$/S',$fileInfo->getBasename()) !== 1){
			continue;
		}
		$plugin = new SplFileInfo($fileInfo->getPathname() . '/' . $fileInfo->getBasename() . '.php');
		if($plugin->isFile() === false || $plugin->isReadable() === false){
			continue;
		}
		require_once($plugin->getPathname());
	}
}
?>