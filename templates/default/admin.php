<?php
	use Aurora\Addon\WebUI\Configs;
	if(isset($_SESSION['loggedinadmin'], $_SESSION['loggedinadmin'][Configs::i()->valueOffset(Globals::i()->WebUI)]) === false){
		require_once('admin-login.php');
		return;
	}

	require_once('_header.php');
?>
	<section>
		<h1><?php echo esc_html(__('Admin')); ?></h1>
	</section>
<?php
	require_once('_footer.php');
?>