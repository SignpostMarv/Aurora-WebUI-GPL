<?php
use Aurora\Addon\WebUI\Configs;
use Aurora\Addon\WebUI\Template;

if(Globals::i()->loggedIn !== true){
	header('Location: ' . Globals::i()->baseURI . Template\link('/'));
	exit;
}

add_filter('GlobalDataLists::RegionNames', function($val){ return true ; });

$UserInfo = Globals::i()->WebUI->GetGridUserInfo(Globals::i()->loggedInAs->PrincipalID());
require_once('_header.php');
?>
<section>
	<h1><?php echo esc_html(__('Edit Account')); ?></h1>
	<form method=post>
		<fieldset>
			<legend><?php echo esc_html(__('Home Region')); ?></legend>
			<ol>
				<li><label for=edit-home-region><?php echo esc_html(__('Region Name')); ?>: </label><input id=edit-home-region name=home-region list=datalist-region-names <?php if(trim($UserInfo->HomeName()) !== ''){ ?>placeholder="<?php echo esc_attr(trim($UserInfo->HomeName())); ?>" <?php } ?>></li>
			</ol>
		</fieldset>
		<fieldset>
			<legend><?php echo esc_html(__('Password')); ?></legend>
			<ol>
				<li><label for=old-password><?php echo esc_html(__('Old Password')); ?>: </label><input id=old-password name=old-password type=password></li>
				<li><label for=new-password><?php echo esc_html(__('New Password')); ?>: </label><input id=new-password name=new-password type=password pattern="<?php echo esc_attr(Globals::i()->regexPassword); ?>"></li>
				<li><label for=confirm-new-password><?php echo esc_html(__('Confirm Password')); ?>: </label><input id=confirm-new-password name=confirm-new-password type=password pattern="<?php echo esc_attr(Globals::i()->regexPassword); ?>"></li>
			</ol>
		</fieldset>
		<fieldset>
			<legend><?php echo esc_html(__('Email Address')); ?></legend>
			<ol>
				<li><span><?php echo esc_html(__('Old Email')); ?>: </span><span><?php echo esc_html($UserInfo->Email()); ?></span></li>
				<li><label for=new-email><?php echo esc_html(__('Email')); ?>: </label><input id=new-email name=new-email type=email></li>
				<li><label for=confirm-new-email><?php echo esc_html(__('Confirm Email')); ?>: </label><input id=confirm-new-email name=confirm-new-email type=email></li>
			</ol>
		</fieldset>
		<fieldset>
			<legend><?php echo esc_html(__('Account Name')); ?></legend>
			<ol>
				<li><label for=new-account-name><?php echo esc_html(__('New Account Name')); ?>: </label><input id=new-account-name placeholder="<?php echo esc_attr($UserInfo->Name()); ?>" pattern="<?php echo esc_attr(Globals::i()->regexUsername); ?>"></li>
			</ol>
		</fieldset>
		<fieldset>
			<legend><?php echo esc_html(__('Purge Appearance?')); ?></legend>
			<ol>
				<li><label for=purge-appearance-yes><?php echo esc_html(__('Yes')); ?></label> <input type=radio id=purge-appearance-yes name=purge-appearance value=yes></li>
				<li><label for=purge-appearance-no><?php echo esc_html(__('No')); ?></label> <input type=radio id=purge-appearance-no name=purge-appearance value=no></li>
			</ol>
		</fieldset>
		<button type=submit><?php echo esc_html(__('Submit')); ?></button>
	</form>
</section>
<?php
require_once('_footer.php');
?>
