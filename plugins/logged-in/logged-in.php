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
	use Aurora\Addon\WebUI\Configs;
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


	function login_form(){
?>
		<form method=post>
<?php
		do_action('pre_login_form_fieldset');
?>
			<fieldset>
				<legend><?php echo esc_html(__('Grid')); ?></legend>
<?php	echo wp_kses(apply_filters('login_form_hidden_input', ''), array('input'=>array('type'=>array('hidden'), 'name'=>array(), 'value'=>array()))),"\n"; ?>
				<select name=grid>
<?php
	Configs::i()->rewind();
	foreach(Configs::i() as $k=>$webui){ ?>
					<option value="<?php echo esc_attr($k); ?>"<?php if(Configs::d() === $webui){?> selected <?php } ?>><?php echo esc_html($webui->get_grid_info('gridnick')); ?></option>
<?php } ?>
				</select>
			</fieldset>
<?php
		do_action('post_login_form_fieldset');
		do_action('pre_account_credentials_fieldset');
?>
			<fieldset>
				<legend><?php echo esc_html(__('Account Credentials')); ?></legend>
				<ol>
					<li><label for=username><?php echo esc_html(__('Username')); ?>: </label><input id=username name=username<?php if(isset($_POST['username'])){ echo ' value="',esc_attr($_POST['username']),'" '; } ?>></li>
					<li><label for=password><?php echo esc_html(__('Password')); ?>: </label><input id=password name=password type=password></li>
				</ol>
				<button type=submit><?php echo esc_html(__(apply_filters('login_form_button_login','Login'))); ?></button>
			</fieldset>
<?php
		do_action('post_account_credentials_fieldset');
?>
		</form>
<?php
	}


	add_filter('main_nav_links', __NAMESPACE__ . '\main_nav_links', 10, 1);
	add_action('login_form', __NAMESPACE__ . '\login_form');
}
?>
