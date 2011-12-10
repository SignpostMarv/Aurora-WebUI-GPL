<?php
	use Aurora\Addon\WebUI\Configs;

	require_once('_header.php');
?>
	<section>
		<h1><?php echo esc_html(__('Login')); ?></h1>
		<form method=post>
			<fieldset>
				<legend><?php echo esc_html(__('Grid')); ?></legend>
				<select name=grid>
<?php
	Configs::i()->rewind();
	foreach(Configs::i() as $k=>$webui){ ?>
					<option value="<?php echo esc_attr($k); ?>"<?php if(Configs::d() === $webui){?> selected <?php } ?>><?php echo esc_html($webui->get_grid_info('gridnick')); ?></option>
<?php } ?>
				</select>
			</fieldset>
			<fieldset>
				<legend><?php echo esc_html(__('Account Credentials')); ?></legend>
				<ol>
					<li><label for=username><?php echo esc_html(__('Username')); ?>: </label><input id=username name=username<?php if(isset($_POST['username'])){ echo ' value="',esc_attr($_POST['username']),'" '; } ?>></li>
					<li><label for=password><?php echo esc_html(__('Password')); ?>: </label><input id=password name=password type=password></li>
				</ol>
				<button type=submit><?php echo esc_html(__('Login')); ?></button>
			</fieldset>
		</form>
	</section>
<?php
	require_once('_footer.php');
?>