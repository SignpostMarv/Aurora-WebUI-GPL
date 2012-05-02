<?php
use Aurora\Addon\WebUI\Template;
use Aurora\Addon\WebUI\Template\FormProblem;

require_once('_header.php');

$parts = array_reverse(explode('/', Globals::i()->section));
$user = $id = null;
if($parts[1] === 'edit'){
	$id = Template\unsquishUUID($parts[0]);
}
?>
	<section>
		<hgroup>
			<h1><?php echo esc_html(__('Manage Users')); ?></h1>
			<h2><?php echo esc_html(__('Edit User')); ?></h2>
		</hgroup>
<?php
if(isset($id) === false){
	header('HTTP/1.0 400 Bad Request');
?>
		<p class=problem><?php echo esc_html(__('No user ID specified.')); ?></p>
<?php
}else{
	$rlname = $street = $zip = $city = $country = '';
	$active = null;
	$FormProblems = array();
	try{
		$user = Globals::i()->WebUI->GetProfile('',$id);
		if($user->RLInfo() !== null){
			$rlname       = trim($user->RLInfo()->Name());
			$street       = trim($user->RLInfo()->Address());
			$zip          = trim($user->RLInfo()->Zip());
			$city         = trim($user->RLInfo()->City());
			$country      = trim($user->RLInfo()->Country());
		}
		$active = $user->UserLevel() >= 0;
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
			}else if(isset($_POST['account-name'], $_POST['real-name'], $_POST['real-street'], $_POST['real-zip'], $_POST['real-city'], $_POST['real-country'], $_POST['email'], $_POST['status']) === false){
				if(isset($_POST['account-name']) === false){
					$FormProblems['user-account-name'] = __('No account name specified.');
				}
				if(isset($_POST['real-name']) === false){
					$FormProblems['user-real-name'] = __('No real name specified.');
				}
				if(isset($_POST['real-street']) === false){
					$FormProblems['user-real-street'] = __('No street name was specified.');
				}
				if(isset($_POST['real-zip']) === false){
					$FormProblems['user-real-zip'] = __('No postal code was specified.');
				}
				if(isset($_POST['real-city']) === false){
					$FormProblems['user-real-city'] = __('No city was specified.');
				}
				if(isset($_POST['real-country']) === false){
					$FormProblems['user-real-country'] = __('No country was specified.');
				}
				if(isset($_POST['email']) === false){
					$FormProblems['user-email'] = __('No email address was specified.');
				}
				if(isset($_POST['status']) === false){
					$FormProblem['user-status'] = __('No account status was specified.');
				}
			}else{
				$edit = false;
				if($_POST['account-name'] !== $user->Name()){
					if(preg_match('/' . Globals::i()->regexUsername . '/', $_POST['account-name']) !== 1){
						$FormProblems['user-account-name'] = __('Specified account name was invalid.');
					}else{
						$edit = true;
					}
				}
				if(trim($_POST['real-name']) !== $rlname){
					$_POST['real-name'] = trim($_POST['real-name']);
					if($_POST['real-name'] !== '' && ctype_print($_POST['real-name']) === false){
						$FormProblems['user-real-name'] = __('Specified real name contained non-printable characters.');
					}else{
						$edit = true;
					}
				}
				if(trim($_POST['real-street']) !== $street){
					$_POST['real-street'] = trim($_POST['real-street']);
					if($_POST['real-street'] !== '' && ctype_print($_POST['real-street']) === false){
						$FormProblems['user-real-street'] = __('Specified street contained non-printable characters.');
					}else{
						$edit = true;
					}
				}
				if(trim($_POST['real-zip']) !== $zip){
					$_POST['real-zip'] = trim($_POST['real-zip']);
					if($_POST['real-zip'] !== '' && ctype_print($_POST['real-zip']) === false){
						$FormProblems['user-real-zip'] = __('Specified postal code contained non-printable characters.');
					}else{
						$edit = true;
					}
				}
				if(trim($_POST['real-city']) !== $city){
					$_POST['real-city'] = trim($_POST['real-city']);
					if($_POST['real-city'] !== '' && ctype_print($_POST['real-city']) === false){
						$FormProblems['user-real-city'] = __('Specified city contained non-printable characters.');
					}else{
						$edit = true;
					}
				}
				if(trim($_POST['real-country']) !== $country){
					$_POST['real-country'] = trim($_POST['real-country']);
					if($_POST['real-country'] !== '' && ctype_print($_POST['real-country']) === false){
						$FormProblems['user-real-country'] = __('Specified country contained non-printable characters.');
					}else{
						$edit = true;
					}
				}
				if(trim($_POST['email']) !== trim($user->Email())){
					$_POST['email'] = trim($_POST['email']);
					if(is_email($_POST['email']) === false){
						$FormProblems['user-email'] = __('Email address appears to be invalid.');
					}else{
						$edit = true;
					}
				}
				if(in_array($_POST['status'], array('active', 'inactive')) === false){
					$FormProblems['user-status'] = __('Account status can only be specified as Active or Inactive.');
				}else{
					$active_val = $active ? 'active' : 'inactive';
					if($_POST['status'] !== $active_val){
						$edit = true;
					}
				}
				if($edit === true){
					Globals::i()->WebUI->EditUser(
						$_POST['id'],
						$_POST['account-name'],
						$_POST['email'],
						new Aurora\Addon\WebUI\RLInfo(
							$_POST['real-name'],
							$_POST['real-street'],
							$_POST['real-zip'],
							$_POST['real-city'],
							$_POST['real-country']
						),
						($_POST['status'] == 'active' ? 1 : -1)
					);
					$user = Globals::i()->WebUI->GetProfile('',$id);
					if($user->RLInfo() !== null){
						$rlname       = trim($user->RLInfo()->Name());
						$street       = trim($user->RLInfo()->Address());
						$zip          = trim($user->RLInfo()->Zip());
						$city         = trim($user->RLInfo()->City());
						$country      = trim($user->RLInfo()->Country());
					}
					$active = $user->UserLevel() >= 0;
				}
			}
		}
	}catch(\Aurora\Addon\UnexpectedValueException $e){
		header('HTTP/1.0 404 Not Found');
?>
		<p class=problem><?php echo esc_html(__('No user found with the specified UUID.')); ?></p>
<?php
	}
	if(isset($user) === true){
?>
		<form method=post>
				<input type=hidden name=id value="<?php echo esc_attr($user->PrincipalID()); ?>">
				<?php if(isset($FormProblems['nonce']) === true){ ?><p class=problem><?php echo esc_html($FormProblems['nonce']); ?></p><?php } ?><input type=hidden name=nonce value="<?php echo esc_attr(Globals::i()->Nonces->get(300)); ?>">
			<table>
				<caption><?php echo esc_html(sprintf(__('Profile information for %s'), $user->Name())); ?></caption>
				<tbody>
					<tr>
						<th scope=row><?php echo esc_html(__('User ID')); ?></th>
						<td><?php echo esc_html($user->PrincipalID()); ?></td>
					</tr>
					<tr>
						<th scope=row><label for=user-account-name><?php echo esc_html(__('Avatar Name')); ?></label></th>
						<td><?php if(isset($FormProblems['user-account-name']) === true){ ?><p class=problem><?php echo esc_html($FormProblems['user-account-name']); ?></p><?php } ?><input id=user-account-name name=account-name value="<?php echo esc_attr($user->Name()); ?>" required pattern="<?php echo esc_attr(Globals::i()->regexUsername); ?>"></td>
					</tr>
					<tr>
						<th scope=row><label for=user-real-name><?php echo esc_html(__('Real Name')); ?></label></th>
						<td><?php if(isset($FormProblems['user-real-name']) === true){ ?><p class=problem><?php echo esc_html($FormProblems['user-real-name']); ?></p><?php } ?><input id=user-real-name name=real-name value="<?php echo esc_attr($rlname); ?>"></td>
					</tr>
					<tr>
						<th scope=row><label for=user-real-street><?php echo esc_html(__('Street')); ?></label></th>
						<td><?php if(isset($FormProblems['user-real-street']) === true){ ?><p class=problem><?php echo esc_html($FormProblems['user-real-street']); ?></p><?php } ?><input id=user-real-street name=real-street value="<?php echo esc_attr($street); ?>"></td>
					</tr>
					<tr>
						<th scope=row><label for=user-real-zip><?php echo esc_html(__('Postal Code')); ?></label></th>
						<td><?php if(isset($FormProblems['user-real-zip']) === true){ ?><p class=problem><?php echo esc_html($FormProblems['user-real-zip']); ?></p><?php } ?><input id=user-real-zip name=real-zip value="<?php echo esc_attr($zip); ?>"></td>
					</tr>
					<tr>
						<th scope=row><label for=user-real-city><?php echo esc_html(__('City')); ?></label></th>
						<td><?php if(isset($FormProblems['user-real-city']) === true){ ?><p class=problem><?php echo esc_html($FormProblems['user-real-city']); ?></p><?php } ?><input id=user-real-city name=real-city value="<?php echo esc_attr($city); ?>"></td>
					</tr>
					<tr>
						<th scope=row><label for=user-real-country><?php echo esc_html(__('Country')); ?></label></th>
						<td><?php if(isset($FormProblems['user-real-country']) === true){ ?><p class=problem><?php echo esc_html($FormProblems['user-real-country']); ?></p><?php } ?><input id=user-real-country name=real-country value="<?php echo esc_attr($country); ?>"></td>
					</tr>
					<tr>
						<th scope=row><label for=user-email><?php echo esc_html(__('Email')); ?></label></th>
						<td><?php if(isset($FormProblems['user-email']) === true){ ?><p class=problem><?php echo esc_html($FormProblems['user-email']); ?></p><?php } ?><input id=user-email name=email value="<?php echo esc_attr($user->Email()); ?>" type=email></td>
					</tr>
					<tr>
						<th scope=row><label for=user-status><?php echo esc_html(__('Status')); ?></th>
						<td><?php if(isset($FormProblems['user-status']) === true){ ?><p class=problem><?php echo esc_html($FormProblems['user-status']); ?></p><?php } ?><select id=user-status name=status>
							<option value=active <?php if($active === true){ ?>selected <?php } ?>><?php echo esc_html(__('Active')); ?></option>
							<option value=inactive <?php if($active === false){ ?>selected <?php } ?>><?php echo esc_html(__('Inactive')); ?></option>
						</select></td>
					</tr>
				</tbody>
			</table>
			<button type=submit><?php echo esc_html(__('Save Changes')); ?></button>
		</form>
<?php
	}
}
?>
	</section>
<?php
require_once('_footer.php');
?>