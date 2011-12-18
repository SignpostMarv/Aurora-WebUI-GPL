<?php
use Aurora\Addon\WebUI\Template;
$pathParts = explode('/', Globals::i()->section);
$group = false;
if(count($pathParts) >= 3){
	$groupName = urldecode(implode('/', array_slice($pathParts, 2)));
	$group = Globals::i()->WebUI->GetGroup($groupName);
}
if(count($pathParts) === 2){
	header('Location: ' . Globals::i()->baseURI . Template\link('groups/'));
	exit;
}else if(count($pathParts) < 3 || ($group === false || $group->ShowInList() === false)){
	require_once('404.php');
	return;
}
	require_once('_header.php');
?>
	<section class=vcard>
		<h1 class="org fn"><?php echo esc_html($group->GroupName()); ?></h1>
<?php if($group->GroupPicture() !== '00000000-0000-0000-0000-000000000000'){ ?>
		<img class=logo src="<?php echo esc_attr(Globals::i()->WebUI->GridTexture($group->GroupPicture())); ?>" alt="<?php echo esc_attr(sprintf(__('Group Insignia for %s'), $group->GroupName())); ?>">
<?php } ?>
		<p class=founder><?php echo esc_html(__('Founded by')); ?>: <a href="<?php echo esc_attr(Template\link('world/user/' . urlencode(Globals::i()->WebUI->GetGridUserInfo($group->FounderID())->Name()))); ?>"><?php echo esc_html(Globals::i()->WebUI->GetGridUserInfo($group->FounderID())->Name()); ?></a></p>
<?php if(trim($group->Charter()) !== '' && trim($group->Charter() !== 'Group Charter')) ?>
		<h2><?php echo esc_html(__('Charter')); ?>:</h2>
		<p><?php echo wp_kses(nl2br($group->Charter().false), array('br'=>array())); ?></p>
		<h2 title="<?php echo esc_attr(__('Group Information')); ?>"><?php echo esc_html(__('Group Info')); ?></h2>
		<table>
			<tr>
				<th scope=row><?php echo esc_html(__('Memerbship Fee')); ?></th>
				<td><?php echo esc_html($group->MembershipFee()); ?></td>
			</tr>
			<tr>
				<th scope=row><?php echo esc_html(__('Enrollment')); ?></th>
				<td><?php echo esc_html(__($group->OpenEnrollment() ? 'Open' : 'Closed')); ?></td>
			</tr>
			<tr>
				<th scope=row><?php echo esc_html(__('Rating')); ?></th>
				<td><?php echo esc_html(__($group->MaturePublish() ? 'Mature' : 'General')); ?></td>
			</tr>
		</table>
	</section>
<?php
	require_once('_footer.php');
?>