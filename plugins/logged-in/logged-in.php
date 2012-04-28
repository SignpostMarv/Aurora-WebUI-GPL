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
	use Aurora\Addon\WebUI\Template\FormProblem;


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
		<form method=post id=login>
<?php
		do_action('pre_account_credentials_fieldset');
?>
<?php	if(FormProblem::i()->offsetExists('login-nonce')){?>
			<p class=problem><?php echo esc_html(FormProblem::i()->offsetGet('login-nonce')); ?></p>
<?php	} ?>
			<input type=hidden name=login-nonce value="<?php echo esc_attr(Globals::i()->Nonces->get(300)); ?>">
			<fieldset id=login-account-credentials>
				<legend><?php echo esc_html(__('Account Credentials')); ?></legend>
<?php	if(FormProblem::i()->offsetExists('login-account-credentials')){?>
			<p class=problem><?php echo esc_html(FormProblem::i()->offsetGet('login-account-credentials')); ?></p>
<?php	} ?>
				<ol>
					<li><label for=login-username><?php echo esc_html(__('Username')); ?>: </label><input id=login-username name=login-username required pattern="<?php echo esc_attr(Globals::i()->regexUsername); ?>"<?php if(isset($_POST['username'])){ echo ' value="',esc_attr($_POST['username']),'" '; } ?>></li>
					<li><label for=login-password><?php echo esc_html(__('Password')); ?>: </label><input id=login-password name=login-password type=password required pattern="<?php echo esc_attr(Globals::i()->regexPassword); ?>"></li>
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
