<?php
use Aurora\Addon\WebUI\Template;
use Aurora\Addon\WebUI\Template\FormProblem;

require_once('_header.php');

$parts = array_reverse(explode('/', Globals::i()->section));
$user = $id = null;
if($parts[1] === 'ban'){
	$id = Template\unsquishUUID($parts[0]);
}
?>
	<section>
		<hgroup>
			<h1><?php echo esc_html(__('Manage Users')); ?></h1>
			<h2><?php echo esc_html(__('Ban User')); ?></h2>
		</hgroup>
<?php
if(isset($id) === false){
	header('HTTP/1.0 400 Bad Request');
?>
		<p class=problem><?php echo esc_html(__('No user ID specified.')); ?></p>
<?php
}else{
	try{
		$user = Globals::i()->WebUI->GetProfile('',$id);
	}catch(\Aurora\Addon\UnexpectedValueException $e){
		header('HTTP/1.0 404 Not Found');
?>
		<p class=problem><?php echo esc_html(__('No user found with the specified UUID.')); ?></p>
<?php
	}
	if(isset($user) === true){
		if($_SERVER['REQUEST_METHOD'] === 'POST'){
			$FormProblems = FormProblem::i();
			if(isset($_POST['id']) === false){
					$FormProblems['id'] = __('Edit item ID was absent.');
			}else if($_POST['id'] !== $id){
				$FormProblems['id'] = __('Edit item ID, and display item ID did not match.');
			}else if(isset($_POST['nonce']) === false){
				$FormProblems['nonce'] = __('Nonce was absent');
			}else if(Globals::i()->Nonces->isValid($_POST['nonce']) === false){
				$FormProblems['nonce'] = __('Nonce has expired');
			}else if(isset($_POST['yes']) === true){
				Globals::i()->Nonces->useNonce($_POST['nonce']);
				if(($user->Flags() & (16 | 32) ? Globals::i()->WebUI->UnBanUser($user->PrincipalID()) : Globals::i()->WebUI->BanUser($user->PrincipalID())) === false){
					$FormProblems['id'] = __('Failed to ban user.');
				}else{
					$wasBanned = $user->Flags() & (16 | 32);
					$user = Globals::i()->WebUI->GetProfile('', $id);
					if($wasBanned === ($user->Flags() & (16 | 32))){
						$FormProblems['id'] = __('API access was successful, but ban/unban operation has not persisted.');
					}else{
?>
		<p class=success><?php echo esc_html(sprintf($wasBanned ? __('User \'%s\' successfully unbanned.') :__('User \'%s\' successfully banned.'), $user->Name())); ?></p>
	</section>
<?php
						require_once('_footer.php');
						return;
					}
				}
			}
			if($FormProblems->count() < 1){
				header('Location:' . Globals::i()->baseURI . Template\link('/admin/manageuser/'));
				exit;
			}
		}
?>
		<form method=post>
			<fieldset>
				<?php if(isset($FormProblems['id']) === true){ ?><p class=problem><?php echo esc_html($FormProblems['id']); ?></p><?php } ?><input type=hidden name=id value="<?php echo esc_attr($user->PrincipalID()); ?>">
				<?php if(isset($FormProblems['nonce']) === true){ ?><p class=problem><?php echo esc_html($FormProblems['nonce']); ?></p><?php } ?><input type=hidden name=nonce value="<?php echo esc_attr(Globals::i()->Nonces->get(300)); ?>">
				<legend><?php echo esc_html(sprintf($user->Flags() & (16 | 32) ? __('Do you want to unban %s?') : __('Do you want to ban %s?'), $user->Name())); ?></legend>
				<button type=submit name=yes><?php echo esc_html(__('Yes')); ?></button>
				<button type=submit name=no><?php echo esc_html(__('No')); ?></button>
			</fieldset>
		</form>
<?php
	}
}
?>
	</section>
<?php
require_once('_footer.php');
?>
