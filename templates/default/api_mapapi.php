<?php
//! This file is an implementation of mapapi.cs (https://github.com/SignpostMarv/mapapi.cs )
//! As it's currently the only public example implementation using mapapi.cs and that this project is GPL'd,
//!	consider this file (and this file only) to be under the same license as that project.

try{
	$MapAPI = Globals::i()->WebUI->getAttachedAPI('MapAPI');
	if(isset($MapAPI) === false){
		header('HTTP/1.0 404 Not Found');
		echo json_encode(array('Error'=>esc_html(__('There is no MapAPI end point specified for this grid.'))));
	}
	$parts = array_slice(explode('/', Globals::i()->section), 2);
	if(count($parts) < 2){
		header('HTTP/1.0 400 Bad Request');
		echo json_encode(array('Error'=>__('Not a valid request.')));
	}else{
		switch($parts[0]){
			case 'RegionDetails':
				if(count($parts) <= 4){
					try{
						$args = array_slice($parts, 1);
						foreach($args as $k=>$arg){
							$args[$k] = urldecode($arg);
						}
						header('Content-Type: application/json');
						echo json_encode(call_user_func_array(array($MapAPI, 'RegionDetails'), $args));
					}catch(\Aurora\Addon\MapAPI\Exception $e){
						echo json_encode(array('Error'=>esc_html($e->getMessage())));
					}
				}
			break;
			default:
				header('HTTP/1.1 400 Bad Request');
				echo json_encode(array('Error'=>esc_html(sprintf(__('\'%s\' is not a supported method.'), $parts[0]))));
			break;
		}
	}
}catch(\Exception $e){
	header('HTTP/1.0 500 Internal Server Error');
	error_log($e);
	echo json_encode(array('Error'=>esc_html(__('An unexpected exception occurred. If the problem persists, contact the site administrator.'))));
}
?>