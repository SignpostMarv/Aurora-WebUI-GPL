<?php
	use Aurora\Addon\WebUI\Exception;
	use Aurora\Addon\WebUI\InvalidArgumentException;
	use Aurora\Addon\WebUI\Configs;
	use Aurora\Addon\WebUI\Template\FormProblem;

	if(isset($_SESSION['loggedin'], $_SESSION['loggedin'][Configs::i()->valueOffset(Globals::i()->WebUI)]) === true && Globals::i()->loggedIn === true){
		header('Location: ' . Globals::i()->baseURI);
		exit;
	}else if(isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] === 'POST'){
		if(isset($_POST['login-username'], $_POST['login-password']) === false){
			FormProblem::i()->offsetSet('login-account-credentials', __('Both username and password must be specified.'));
		}
	}

	require_once('_header.php');
?>
	<section>
		<h1><?php echo esc_html(__(Globals::i()->section === 'admin' ? 'Admin Login' : 'Login')); ?></h1>
<?php	do_action('login_form'); ?>
	</section>
<?php
	require_once('_footer.php');
?>