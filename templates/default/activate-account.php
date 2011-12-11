<?php
	use Aurora\Addon\WebUI;
	use Aurora\Addon\WebUI\Configs;

	if(isset($_SERVER['REQUEST_METHOD']) === true && $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['username'], $_POST['password'], $_POST['grid'], $_POST['token']) === true){
		$WebUI = Configs::i()->offsetGet($_POST['grid']);
		if(isset($WebUI) === true){
			if($WebUI->ActivateAccount($_POST['username'], $_POST['password'], $_POST['token']) === true){
				require_once('login.php');
				return;
			}
		}
	}

	require_once('_header.php');
?>
	<section>
		<h1>Account Activation</h1>
<?php
	if(isset($_GET['token']) === true){
		if(preg_match(WebUI::regex_UUID, $_GET['token']) === 1){
			function hiddenActivationToken($inputs){
				return $inputs . '<input type=hidden name=token value="' . esc_attr($_GET['token']) . '">';
			}
			add_filter('login_form_hidden_input', 'hiddenActivationToken');
?>
		<p>Enter your login details in order to finalise activation of your account!</p>
<?php
			do_action('login_form');
		}else{
			header('HTTP/1.1 400 Bad Request');
?>
		<p><?php echo esc_html(__('Cannot activate an account with an invalid token.')); ?></p>
<?php
		}
	}else{
		header('HTTP/1.1 400 Bad Request');
?>
		<p><?php echo esc_html(__('Cannot activate an account without an activation token.')); ?></p>
<?php
	}
?>
	</section>
<?php
	require_once('_footer.php');
?>