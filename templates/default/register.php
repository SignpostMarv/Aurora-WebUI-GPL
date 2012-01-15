<?php
	use Aurora\Addon\WebUI\Configs;
	use Aurora\Addon\WebUI\Template;

	$registeredUser = null;
	$WebUI = null;
	$activationToken = null;
	if(isset($_SERVER['REQUEST_METHOD']) === true && $_SERVER['REQUEST_METHOD'] === 'POST'){
		$registration_is_valid = apply_filters('registration_is_valid', true, $_POST);
		if(Globals::i()->registrationEmailRequired !== true){
			$_POST['email'] = isset($_POST['email']) ? $_POST['email'] : '';
			$_POST['confirm-email'] = isset($_POST['confirm-email']) ? $_POST['confirm-email'] : '';
		}
		if($registration_is_valid === true && isset(
			$_POST['grid'],
			$_POST['username'],
			$_POST['password'],
			$_POST['confirm-password'],
			$_POST['email'],
			$_POST['confirm-email']
		) === true){
			if($_POST['password'] === $_POST['confirm-password']){
				$WebUI = Configs::i()->offsetGet($_POST['grid']);
				if(isset($WebUI) === true){
					list($registeredUser, $activationToken) = $WebUI->CreateAccount($_POST['username'], $_POST['password'], $_POST['email']);
				}
			}
		}
	}

	require_once('_header.php');
	if(isset($registeredUser, $WebUI) === false){
?>
	<section>
		<h1><?php echo esc_html(__('Register')); ?></h1>
		<form id=registration method=post>
<?php
	do_action('pre_grid_selector_fieldset', 'register');
	if(Configs::i()->count() >= 2){
		do_action('grid_selector_fieldset', Globals::i()->WebUI);
	}else{
?>
		<input type=hidden name=grid value="<?php echo esc_attr(Configs::i()->valueOffset(Globals::i()->WebUI)); ?>">
		<p><?php echo esc_html(sprintf(__('Grid: %1$s'), Globals::i()->WebUI->get_grid_info('gridnick'))); ?></p>
<?php
	}
	do_action('post_grid_selector_fieldset', 'register');

	do_action('pre_register_account_fieldset', 'register');
?>
			<fieldset class=account>
				<legend><?php echo esc_html(__('Account Information')); ?></legend>
				<ol>
					<li><label for=register-username><?php echo esc_html(__('Account name')); ?>: </label><input id=register-username name=username required pattern="^[A-z]{1}[A-z0-9]*\ [A-z]{1}[A-z0-9]*$"<?php if(isset($_POST['username'])){ ?> value="<?php echo esc_attr($_POST['username']); ?>"<?php } ?>></li>
					<li><label for=register-password><?php echo esc_html(__('Password')); ?>: </label><input id=register-password name=password type=password required pattern="^.{8}.*$"></li>
					<li><label for=register-confirm-password><?php echo esc_html(__('Confirm Password')); ?>: </label><input id=register-confirm-password name=confirm-password type=password required pattern="^.{8}.*$"></li>
					<li><label for=register-email><?php echo esc_html(__('Email')); ?>: </label><input id=register-email name=email type=email<?php if(Globals::i()->registrationEmailRequired === true){ ?> required<?php } ?><?php if(isset($_POST['email'])){ ?> value="<?php echo esc_attr($_POST['email']); ?>"<?php } ?>></li>
					<li><label for=register-confirm-email><?php echo esc_html(__('Confirm Email')); ?>: </label><input id=register-confirm-email name=confirm-email type=email<?php if(Globals::i()->registrationEmailRequired === true){ ?> required<?php } ?>></li>
					<li><label for=register-dob title="<?php echo esc_attr(__('Date of Birth')); ?>"><?php echo esc_html(__('D.O.B')); ?>: </label><input id=register-dob name=dob type=date required<?php if(isset($_POST['dob'])){ ?> value="<?php echo esc_attr($_POST['dob']); ?>"<?php } ?>></li>
				</ol>
			</fieldset>
<?php
	do_action('post_register_account_fieldset', 'register');

	if(Globals::i()->registrationPostalRequired === true){
		do_action('pre_register_account_postal_fieldset', 'register');
?>
			<fieldset class=postal>
				<legend><?php echo esc_html(__('Postal Information')); ?></legend>
				<ol>
					<li><label for=register-name><?php echo esc_html(__('Name')); ?>: </label><input id=register-name name=name></li>
					<li><label for=register-address><?php echo esc_html(__('Address')); ?>: </label><textarea id=register-address rows=5 cols=20></textarea></li>
					<li><label for=register-city><?php echo esc_html(__('City')); ?>: </label><input id=register-city name=city></li>
					<li><label for=register-zip><?php echo esc_html(__('Postal Code')); ?>: </label><input id=register-zip name=zip></li>
					<li><label for=register-country><?php echo esc_html(__('Country')); ?>: </label><input id=register-country name=country></li>
				</ol>
			</fieldset>
<?php
		do_action('post_register_account_postal_fieldset', 'register');
	}
	do_action('pre_buttons_fieldset', 'register');
?>
			<fieldset class=buttons>
				<button type=submit><?php echo esc_html(__('Register')); ?></button>
			</fieldset>
<?php
	do_action('post_buttons_fieldset', 'register');
?>
		</form>
	</section>
<?php
	}else{
?>
	<section>
		<h1><?php echo esc_html(__('Registration successful!')); ?></h1>
<?php
		if(Globals::i()->registrationActivationRequired === false){
?>
		<p><?php echo esc_html(sprintf(__('Registration successful! You can now log into %1$s!'), $WebUI->get_grid_info('gridnick'))); ?></p>
<?php	
		}else{
?>
		<p><?php echo esc_html(sprintf(__('Registration successful! However, you first need to activate your account before you can log into %1$s!'), $WebUI->get_grid_info('gridnick'))); ?></p>
		<p><a href="<?php echo esc_attr(Template\link('activate-account/?token=' . $activationToken)); ?>"><?php echo esc_html(__('Activate your account!')); ?></a></p>
<?php
		}
?>
	</section>
<?php
	}
	require_once('_footer.php');
?>