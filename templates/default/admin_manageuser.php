<?php
use Aurora\Addon\WebUI\Template;

require_once('_header.php');

$parts = array_reverse(explode('/', Globals::i()->section));
$page  = '1';
$per   = '25';
$query = '';
if($parts[1] === 'manageuser'){
	$page = $parts[0];
	if(ctype_digit($page) === false){
		require_once('404.php');
		return;
	}
}else if($parts[2] === 'manageuser'){
	$page = $parts[0];
	$per  = $parts[1];
	if(ctype_digit($per) === false){
		require_once('404.php');
		return;
	}
}else if($parts[3] === 'manageuser'){
	$page  = $parts[0];
	$per   = $parts[1];
	$query = trim($parts[2]);
	if(ctype_digit($per) === false){
		require_once('404.php');
		return;
	}
}
$per -= ($per % 10);
$per = (string)$per;
if($_SERVER['REQUEST_METHOD'] === 'POST'){
	$query = null;
	if(isset($_POST['query']) === true){
		$query = trim($_POST['query']);
	}
}
?>
	<section>
		<h1><?php echo esc_html(__('Manage Users')); ?></h1>
<?php
if(ctype_digit($page) === false){
	header('HTTP/1.0 400 Bad Request');
?>
		<p class=problem><?php echo esc_html(__('Page number was not a number.')); ?></p>
<?php
}
if(ctype_digit($per) === false){
	header('HTTP/1.0 400 Bad Request');
?>
		<p class=problem><?php echo esc_html(__('Results per-page was not a number.')); ?></p>
<?php
}
if(isset($query) === false){
	header('HTTP/1.0 400 Bad Request');
?>
		<p class=problem><?php echo esc_html(__('No search query specified.')); ?></p>
<?php
}else if($query !== '' && ctype_print($query) === false){
	header('HTTP/1.0 400 Bad Request');
?>
		<p class=problem><?php echo esc_html(__('Search query contained invalid characters.')); ?></p>
<?php
}
if(ctype_digit($page) === true && ctype_digit($per) === true){
	$page = (integer)$page;
	$per  = (integer)$per;

	$page = ($page < 1) ? 1 : $page;
	$per  = ($per < 10) ? 10 : ($per > 100 ? 100 : $per);

	$targetSection = 'admin/manageuser/' . ($query === '' ? '' : rawurlencode($query) . '/') . $per . '/' . $page;
	if(Globals::i()->section !== $targetSection){
		header('Location: ' . Globals::i()->baseURI . Template\link('/' . $targetSection));
		exit;
	}
	$UserSearch = Globals::i()->WebUI->FindUsers($query, ($page - 1) * $per, $per);
	$count = $UserSearch->count();
	$lastPage = (int)ceil($count / $per);

	if($count < 1){
		header('HTTP/1.0 404 Not Found');
?>
		<p class=problem><?php echo esc_html(__('No users matching your query have been found.')); ?></p>
<?php
	}else if(($page - 1) * $per > $count){
		$page = $lastPage;
		$targetSection = 'admin/manageuser/' . ($query === '' ? '' : rawurlencode($query) . '/') . $per . '/' . $page;
		header('Location: ' . Globals::i()->baseURI . Template\link('/' . $targetSection));
		exit;
	}
}
?>
		<form method=post>
			<fieldset>
				<legend><?php echo esc_html(__('Search Users')); ?></legend>
				<ol>
					<li><label for=manage-user-search><?php echo esc_html(__('Name')); ?>: </label><input type=search id=manage-user-search name=query value="<?php echo esc_attr($query); ?>"></li>
				</ol>
			</fieldset>
			<button type=submit><?php echo esc_html(__('Search')); ?></button>
		</form>
		<nav>
			<ol>
<?php
if($page > 1){
	$targetSection = 'admin/manageuser/' . ($query === '' ? '' : rawurlencode($query) . '/') . $per . '/1';
?>
				<li><a href="<?php echo esc_attr(Template\link($targetSection)); ?>" rel=first><?php echo esc_html(__('First')); ?></a></li>
<?php
	if($page > 2){
		$targetSection = 'admin/manageuser/' . ($query === '' ? '' : rawurlencode($query) . '/') . $per . '/' . ($page - 1);
?>
				<li><a href="<?php echo esc_attr(Template\link($targetSection)); ?>" rel=prev title="<?php echo esc_attr(__('Previous')); ?>"><?php echo esc_html(__('Prev')); ?></a></li>
<?php
	}
}
if($lastPage > 1 && $page < $lastPage){
	if($page + 1 != $lastPage){
		$targetSection = 'admin/manageuser/' . ($query === '' ? '' : rawurlencode($query) . '/') . $per . '/' . ($lastPage + 1);
?>
				<li><a href="<?php echo esc_attr(Template\link($targetSection)); ?>" rel=next><?php echo esc_html(__('Next')); ?></a></li>
<?php
	}
	$targetSection = 'admin/manageuser/' . ($query === '' ? '' : rawurlencode($query) . '/') . $per . '/' . $lastPage;
?>
				<li><a href="<?php echo esc_attr(Template\link($targetSection)); ?>" rel=last><?php echo esc_html(__('Last')); ?></a></li>
<?php
}
if($per != 10 && $count >= 10){
	$targetSection = 'admin/manageuser/' . ($query === '' ? '' : rawurlencode($query) . '/') . '10/' . floor((($page - 1) * $per) / 10);
?>
				<li><a href="<?php echo esc_attr(Template\link($targetSection)); ?>">10</a></li>
<?php
}else{
?>
				<li>10</li>
<?php
}
if($per != 20 && $count >= 10 && ($count % 20 || $count >= 20)){
	$targetSection = 'admin/manageuser/' . ($query === '' ? '' : rawurlencode($query) . '/') . '20/' . floor((($page - 1) * $per) / 20);
?>
				<li><a href="<?php echo esc_attr(Template\link($targetSection)); ?>">20</a></li>
<?php
}else{
?>
				<li>20</li>
<?php
}
if($per != 50 && $count >= 20 && ($count % 50 || $count >= 50)){
	$targetSection = 'admin/manageuser/' . ($query === '' ? '' : rawurlencode($query) . '/') . '50/' . floor((($page - 1) * $per) / 50);
?>
				<li><a href="<?php echo esc_attr(Template\link($targetSection)); ?>">50</a></li>
<?php
}else{
?>
				<li>50</li>
<?php
}
if($per != 100 && $count >= 50 && ($count % 100 || $count >= 100)){
	$targetSection = 'admin/manageuser/' . ($query === '' ? '' : rawurlencode($query) . '/') . '100/' . floor((($page - 1) * $per) / 100);
?>
				<li><a href="<?php echo esc_attr(Template\link($targetSection)); ?>">100</a></li>
<?php
}else{
?>
				<li>100</li>
<?php
}
?>
			</ol>
		</nav>
		<table>
			<caption><?php echo esc_html(sprintf(__('%s users found.'), $count)); ?></caption>
			<thead>
				<tr>
					<th id=manage-user-name><?php echo esc_html(__('Name')); ?></th>
					<th id=manage-user-created><?php echo esc_html(__('Created')); ?></th>
					<th id=manage-user-status><?php echo esc_html(__('Status')); ?></th>
					<th id=manage-user-action colspan=3><?php echo esc_html(__('Action')); ?></th>
				</tr>
			</thead>
			<tbody>
<?php
$start = ($page - 1) * $per;
$end = min($start + $per, $count);
foreach($UserSearch as $user){
	if($start++ >= $end){
		break;
	}
?>
				<tr>
					<th scope=row headers=manage-user-name><?php echo esc_html(__($user->Name())); ?></th>
					<td headers=manage-user-created><time datetime="<?php echo esc_attr(date('r', $user->Created())); ?>"><?php echo esc_html(date('Y-m-d', $user->Created())); ?></time></td>
					<td headers=manage-user-status><?php
	if($user->Flags() & (16 | 32)){
		echo esc_html(__('Banned'));
	}else if($user->UserFlags() & 3){
		echo esc_html(__('Not Confirmed'));
	}else{
		echo esc_html($user->UserLevel() >= 0 ? __('Active') : __('Inactive'));
	}
?></td>
					<td headers=manage-user-action><a href="<? echo esc_attr(Template\link('/admin/manageuser/edit/' . rawurlencode(Template\squishUUID($user->PrincipalID())))); ?>"><?php echo esc_html(__('Edit')); ?></a></td>
					<td headers=manage-user-action><a href="<? echo esc_attr(Template\link('/admin/manageuser/ban/' . rawurlencode(Template\squishUUID($user->PrincipalID())))); ?>"><?php echo esc_html(($user->Flags() & (16 | 32)) ? __('Unban') : __('Ban')); ?></a></td>
					<td headers=manage-user-action><a href="<? echo esc_attr(Template\link('/admin/manageuser/delete/' . rawurlencode(Template\squishUUID($user->PrincipalID())))); ?>"><?php echo esc_html(__('Delete')); ?></a></td>
				</tr>
<?php
}
?>
			</tbody>
		</table>
	</section>
<?php
require_once('_footer.php');
?>
