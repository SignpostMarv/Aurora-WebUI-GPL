<?php
	use Aurora\Addon\WebUI\Configs;

	require_once('_header.php');
?>
	<section>
		<h1><?php echo esc_html(__('Register')); ?></h1>
		<form method=post>
<?php
		do_action('pre_grid_selector_fieldset', 'register');
		do_action('grid_selector_fieldset', Globals::i()->WebUI);
		do_action('post_grid_selector_fieldset', 'register');

		do_action('pre_register_account_fieldset', 'register');
?>
			<fieldset class=account>
				<legend><?php echo esc_html(__('Account Information')); ?></legend>
				<ol>
					<li><label for=register-username><?php echo esc_html(__('Account name')); ?>: </label><input id=register-username name=username required pattern="^[A-z]{1}[A-z0-9]*\ [A-z]{1}[A-z0-9]*$"></li>
					<li><label for=register-password><?php echo esc_html(__('Password')); ?>: </label><input id=register-password name=password type=password required pattern="^.{8}.*$"></li>
					<li><label for=register-confirm-password><?php echo esc_html(__('Confirm Password')); ?>: </label><input id=register-confirm-password name=confirm-password type=password required pattern="^.{8}.*$"></li>
					<li><label for=register-email><?php echo esc_html(__('Email')); ?>: </label><input id=register-email name=email type=email required></li>
					<li><label for=register-confirm-email><?php echo esc_html(__('Confirm Email')); ?>: </label><input id=register-confirm-email name=confirm-email type=email required></li>
				</ol>
			</fieldset>
<?php
		do_action('post_register_account_fieldset', 'register');
?>
			<fieldset class=buttons>
				<button type=submit><?php echo esc_html(__('Register')); ?></button>
			</fieldset>
		</form>
	</section>
<?php
	require_once('_footer.php');
?>