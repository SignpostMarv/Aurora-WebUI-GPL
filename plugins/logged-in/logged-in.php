<?php
/*
Plugin Name: Logged In
Plugin URI: https://github.com/SignpostMarv/Aurora-WebUI-GPL
Description: Plugins for altering the WebUI output based on whether a user is logged in or not.
Version: 0.1
Author: SignpostMarv
Author URI: https://github.com/SignpostMarv/
*/


namespace Aurora\Addon\WebUI\plugins\logged_in{

	use Globals;
	use Aurora\Addon\WebUI\Template;


	function main_nav_links($nav_links){
		if(Globals::i()->loggedIn){
			return $nav_links . '<li><a href="' . esc_attr(Template\link('logout')) . '">' . esc_html(__('Logout')) . '</a></li>';
		}else{
			return $nav_links .
				'<li><a href="' . esc_attr(Template\link('login')) . '">' . esc_html(__('Login')) . '</a></li>' .
				'<li><a href="' . esc_attr(Template\link('register')) . '">' . esc_html(__('Register')) . '</a></li>'
			;
		}
	}


	add_filter('main_nav_links', __NAMESPACE__ . '\main_nav_links', 10, 1);
}
?>
