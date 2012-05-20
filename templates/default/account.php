<?php
use Aurora\Addon\WebUI\Exception as WebUIException;

use Aurora\Addon\WebAPI\Configs;
use Aurora\Addon\WebUI\Template;
use Aurora\Addon\WebUI\Template\FormProblem;

if(Globals::i()->loggedIn !== true){
	header('Location: ' . Globals::i()->baseURI . Template\link('/'));
	exit;
}

add_filter('GlobalDataLists::RegionNames', function($val){ return true ; });

$FormProblems = FormProblem::i();
$UserInfo = Globals::i()->WebUI->GetGridUserInfo(Globals::i()->loggedInAs->PrincipalID());
$UserInfoIsDirty = false;

if($_SERVER['REQUEST_METHOD'] === 'POST'){
	$stripKeys = array(
		'home-region',
		'old-password',
		'new-password',
		'confirm-new-password',
	);
	foreach($stripKeys as $key){
		if(isset($_POST[$key]) === true){
			$_POST[$key] = trim($_POST[$key]);
			if($_POST[$key] === ''){
				unset($_POST[$key]);
			}
		}
	}

	if(isset($_POST['for']) === false){
		$FormProblems['for'] = __('The user ID was not specified, please reload the page and try again.');
	}else if($_POST['for'] !== $UserInfo->PrincipalID()){
		$FormProblems['for'] = __('You appear to have previously been logged in as another user, please ensure you are logged in with the account you intend to edit before trying again.');
	}else{
		if(isset($_POST['home-region']) === true){
			try{
				$region = Globals::i()->WebUI->GetRegion($_POST['home-region']);
				if(Globals::i()->WebUI->SetHomeLocation($UserInfo->PrincipalID(), $region) === false){
					$FormProblems['home-region'] = __('Failed to set home location.');
				}else{
					$UserInfoIsDirty = true;
				}
			}catch(WebUIException $e){
				$FormProblems['home-region'] = $e->getMessage();
			}
		}
		if(isset($_POST['old-password']) === true){
			if(isset($_POST['new-password'], $_POST['confirm-new-password']) === false){
				if(isset($_POST['new-password']) === false){
					$FormProblems['new-password'] = __('New password is not specified.');
				}
				if(isset($_POST['confirm-new-password']) === false){
					$FormProblems['confirm-new-password'] = __('New password confirmation is not specified.');
				}
			}else if($_POST['new-password'] !== $_POST['confirm-new-password']){
				$FormProblems['confirm-new-password'] = __('New passwords do not match.');
			}else if(preg_match('/' . Globals::i()->regexPassword . '/', $_POST['new-password']) != 1){
				$FormProblems['new-password'] = __('New password does not appear to be of a valid format.');
			}else if(Globals::i()->WebUI->ChangePassword($UserInfo->PrincipalID(), $_POST['old-password'], $_POST['new-password'])){
				session_unset();
				session_destroy();
				header('Location: ' . Globals::i()->baseURI . Template\link('/login/'));
				exit;
			}else{
				$FormProblems['old-password'] = __('Could not change password, old password is still in effect.');
			}
		}
		if(isset($_POST['new-email']) === true){
			if(isset($_POST['confirm-new-email']) === false){
				$FormProblems['confirm-new-password'] = __('New email address confirmation not specified.');
			}else if($_POST['new-email'] !== $_POST['confirm-new-email']){
				$FormProblems['confirm-new-email'] = __('New email address does not match confirmation');
			}else if(is_email($_POST['new-email']) === false){
				$FormProblems['new-email'] = __('New email address is not valid.');
			}else if(Globals::i()->WebUI->SaveEmail($UserInfo->PrincipalID(), $_POST['new-email']) === false){
				$FormProblems['new-email'] = __('Failed to save email address.');
			}else{
				$UserInfoIsDirty = true;
			}
		}
		if(isset($_POST['new-account-name']) === true){
			if(preg_match('/' . Globals::i()->regexUsername . '/', $_POST['new-account-name']) != 1){
				$FormProblems['new-account-name'] = __('Format of new account name is invalid.');
			}else if($_POST['new-account-name'] === $UserInfo->Name()){
				$FormProblems['new-account-name'] = __('You cannot change your account name to what it already is.');
			}else if(strtolower($_POST['new-account-name']) !== strtolower($UserInfo->Name()) && Globals::i()->WebUI->CheckIfUserExists($_POST['new-account-name']) === true){
				$FormProblems['new-account-name'] = __('That name is already taken.');
			}else if(Globals::i()->WebUI->ChangeName($UserInfo->PrincipalID(), $_POST['new-account-name']) === false){
				$FormProblems['new-account-name'] = __('Failed to change account name.');
			}else{
				$UserInfoIsDirty = true;
			}
		}
		if(isset($_POST['purge-appearance']) === true){
			if(in_array($_POST['purge-appearance'], array('yes', 'no')) === false){
				$FormProblems['purge-appearance'] = __('Valid choices are Yes or No');
			}else if($_POST['purge-appearance'] === 'yes'){
				if(Globals::i()->WebUI->ResetAvatar($UserInfo->PrincipalID()) === false){
					$FormProblems['purge-appearance'] = __('Could not reset your appearance.');
				}
			}
		}
	}
}
if($UserInfoIsDirty === true){
	if($FormProblems->count() < 1){
		header('Location: ' . Globals::i()->baseURI . Template\link(Globals::i()->section));
		exit;
	}else{
		$UserInfo = Globals::i()->WebUI->GetGridUserInfo(Globals::i()->loggedInAs->PrincipalID());
		$UserInfoIsDirty = false;
	}
}
require_once('_header.php');
?>
<section>
	<h1><?php echo esc_html(__('Edit Account')); ?></h1>
	<form method=post>
		<input type=hidden name=for value="<?php echo esc_attr($UserInfo->PrincipalID()); ?>">
<?php if(isset($FormProblems['for']) === true){ ?>
		<p class=problem><?php echo esc_html($FormProblems['for']); ?></p>
<?php } ?>
		<fieldset>
			<legend><?php echo esc_html(__('Home Region')); ?></legend>
			<ol>
				<li><?php if(isset($FormProblems['home-region']) === true){ ?><p class=problem><?php echo esc_html($FormProblems['home-region']); ?></p><?php } ?><label for=edit-home-region><?php echo esc_html(__('Region Name')); ?>: </label><input id=edit-home-region name=home-region list=datalist-region-names required <?php if(trim($UserInfo->HomeName()) !== ''){ ?>placeholder="<?php echo esc_attr(trim($UserInfo->HomeName())); ?>" <?php } ?>></li>
			</ol>
		</fieldset>
		<button type=submit><?php echo esc_html(__('Submit')); ?></button>
	</form>
	<form method=post>
		<input type=hidden name=for value="<?php echo esc_attr($UserInfo->PrincipalID()); ?>">
		<fieldset>
			<legend><?php echo esc_html(__('Password')); ?></legend>
			<ol>
				<li><?php if(isset($FormProblems['old-password']) === true){ ?><p class=problem><?php echo esc_html($FormProblems['old-password']); ?></p><?php } ?><label for=old-password><?php echo esc_html(__('Old Password')); ?>: </label><input id=old-password name=old-password type=password required></li>
				<li><?php if(isset($FormProblems['new-password']) === true){ ?><p class=problem><?php echo esc_html($FormProblems['new-password']); ?></p><?php } ?><label for=new-password><?php echo esc_html(__('New Password')); ?>: </label><input id=new-password name=new-password type=password required pattern="<?php echo esc_attr(Globals::i()->regexPassword); ?>"></li>
				<li><?php if(isset($FormProblems['confirm-new-password']) === true){ ?><p class=problem><?php echo esc_html($FormProblems['confirm-new-password']); ?></p><?php } ?><label for=confirm-new-password><?php echo esc_html(__('Confirm Password')); ?>: </label><input id=confirm-new-password name=confirm-new-password type=password required pattern="<?php echo esc_attr(Globals::i()->regexPassword); ?>"></li>
			</ol>
		</fieldset>
		<button type=submit><?php echo esc_html(__('Submit')); ?></button>
	</form>
	<form method=post>
		<input type=hidden name=for value="<?php echo esc_attr($UserInfo->PrincipalID()); ?>">
		<fieldset>
			<legend><?php echo esc_html(__('Email Address')); ?></legend>
			<ol>
				<li><?php if(isset($FormProblems['old-email']) === true){ ?><p class=problem><?php echo esc_html($FormProblems['old-email']); ?></p><?php } ?><span><?php echo esc_html(__('Old Email')); ?>: </span><span><?php echo esc_html($UserInfo->Email()); ?></span></li>
				<li><?php if(isset($FormProblems['new-email']) === true){ ?><p class=problem><?php echo esc_html($FormProblems['new-email']); ?></p><?php } ?><label for=new-email><?php echo esc_html(__('Email')); ?>: </label><input id=new-email name=new-email type=email required></li>
				<li><?php if(isset($FormProblems['confirm-new-email']) === true){ ?><p class=problem><?php echo esc_html($FormProblems['confirm-new-email']); ?></p><?php } ?><label for=confirm-new-email><?php echo esc_html(__('Confirm Email')); ?>: </label><input id=confirm-new-email name=confirm-new-email type=email required></li>
			</ol>
		</fieldset>
		<button type=submit><?php echo esc_html(__('Submit')); ?></button>
	</form>
	<form method=post>
		<input type=hidden name=for value="<?php echo esc_attr($UserInfo->PrincipalID()); ?>">
		<fieldset>
			<legend><?php echo esc_html(__('Account Name')); ?></legend>
			<ol>
				<li><?php if(isset($FormProblems['new-account-name']) === true){ ?><p class=problem><?php echo esc_html($FormProblems['new-account-name']); ?></p><?php } ?><label for=new-account-name><?php echo esc_html(__('New Account Name')); ?>: </label><input id=new-account-name name=new-account-name placeholder="<?php echo esc_attr($UserInfo->Name()); ?>" required pattern="<?php echo esc_attr(Globals::i()->regexUsername); ?>"></li>
			</ol>
		</fieldset>
		<button type=submit><?php echo esc_html(__('Submit')); ?></button>
	</form>
	<form method=post>
		<input type=hidden name=for value="<?php echo esc_attr($UserInfo->PrincipalID()); ?>">
		<fieldset>
			<legend><?php echo esc_html(__('Purge Appearance?')); ?></legend>
<?php if(isset($FormProblems['purge-appearance']) === true){ ?>
				<p class=problem><?php echo esc_html($FormProblems['purge-appearance']); ?></p><?php } ?>
			<ol>
				<li><input type=radio id=purge-appearance-yes name=purge-appearance value=yes><label for=purge-appearance-yes><?php echo esc_html(__('Yes')); ?></label></li>
				<li><input type=radio id=purge-appearance-no name=purge-appearance value=no checked><label for=purge-appearance-no><?php echo esc_html(__('No')); ?></label></li>
			</ol>
		</fieldset>
		<button type=submit><?php echo esc_html(__('Submit')); ?></button>
	</form>
</section>
<?php
require_once('_footer.php');
?>
