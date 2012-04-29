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
	use libAurora\Template\navigation\Page;
	use libAurora\Template\navigation\Pages;


	function main_nav_links(Pages $nav_links){
		if(Globals::i()->loggedIn){
			$nav_links['Admin']   = Page::f(__('Admin'), -9000, null, '', 3);
			$nav_links['Admin']['NewsManager'] = Page::f(__('News Manager'), 0, '/admin/news/', '', 3);
			$nav_links['Account'] = Page::f(__('Account'), -8888, esc_attr(Template\link('account')), '', 1);
			$nav_links['Logout']  = Page::f(__('Logout'), PHP_INT_MAX, esc_attr(Template\link('logout')), '', 1);
		}else{
			$nav_links['Login']    = Page::f(__('Login'), PHP_INT_MAX - 1, esc_attr(Template\link('login')), '', 0);
			$nav_links['Register'] = Page::f(__('Register'), PHP_INT_MAX, esc_attr(Template\link('login')), '', 0);
		}
		return $nav_links;
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
