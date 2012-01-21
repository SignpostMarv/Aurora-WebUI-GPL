<?php
	use Aurora\Addon\WebUI\Exception;
	use Aurora\Addon\WebUI\InvalidArgumentException;
	use Aurora\Addon\WebUI\Configs;
	use Aurora\Addon\WebUI\Template;
	use Aurora\Addon\WebUI\Template\FormProblem;

	if(isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] === 'POST'){	
		if(isset($_POST['username'], $_POST['password'], $_POST['grid']) === true && Configs::i()->offsetExists($_POST['grid']) === true){
			Globals::i()->WebUI = Configs::i()->offsetGet($_POST['grid']);
			$success = false;
			try{
				$login = Globals::i()->WebUI->AdminLogin($_POST['username'], $_POST['password']);
				$success = true;
			}catch(InvalidArgumentException $e){
				FormProblem::i()->offsetSet('login-account-credentials',$e->getMessage());
			}
			if($success){
				session_regenerate_id(true);
				$_SESSION['loggedinadmin'][$_POST['grid']] = $login;
				header('Location: ' . Globals::i()->baseURI . Template\link('/admin/'));
				exit;
			}
		}
	}

	require_once('_header.php');
?>
	<section>
		<h1><?php echo esc_html(__('Admin Login')); ?></h1>
<?php	do_action('login_form'); ?>
	</section>
<?php
	require_once('_footer.php');
?>