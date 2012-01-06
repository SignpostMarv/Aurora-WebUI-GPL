<?php
use Aurora\Addon\WebUI\Template;
$pathParts = explode('/', Globals::i()->section);
$group = false;
if(count($pathParts) >= 3){
	$userName = urldecode(implode('/', array_slice($pathParts, 2)));
	$user = Globals::i()->WebUI->GetProfile($userName);
	if($user->AllowPublish() === false){
		require_once('404.php');
		return;
	}
}
if(count($pathParts) === 2){
	header('Location: ' . Globals::i()->baseURI . Template\link('/world/users/'));
	exit;
}else if(count($pathParts) < 3 || ($user === false || $user->AllowPublish() === false)){
	require_once('404.php');
	return;
}
	require_once('_header.php');
?>
	<section class=vcard>
		<h1 class=fn><?php echo esc_html($user->Name()); ?></h1>
		<p class=vevent><span class=summary><?php echo esc_html(__('Rezday')); ?></span>: <abbr class=dtstart title="<?php echo esc_attr(date('c', $user->Created())); ?>"><?php echo esc_html(date(apply_filters('rezday_format', 'Y-m-d'), $user->Created())); ?></abbr></p>
<?php if($user->Image() !== '00000000-0000-0000-0000-000000000000'){ ?>
		<img class=logo src="<?php echo esc_attr(Globals::i()->WebUI->GridTexture($user->Image())); ?>" alt="<?php echo esc_attr(sprintf(__('Avatar image for %s', $user->Name()))); ?>">
<?php } ?>
<?php if($user->AboutText() !== ''){ ?>
		<p><?php echo wp_kses(nl2br($user->AboutText()), array('br'=>array())); ?></p>
<?php } ?>
<?php if($user->FirstLifeImage() !== '00000000-0000-0000-0000-000000000000' || $user->FirstLifeAboutText() !== ''){ ?>
		<section class=first-life>
			<h1><?php echo esc_html(__('First Life')); ?></h1>
<?php	if($user->FirstLifeImage() !== '00000000-0000-0000-0000-000000000000'){ ?>
			<img class=photo src="<?php echo esc_attr(Globals::i()->WebUI->GridTexture($user->FirstLifeImage())); ?>" alt="<?php echo esc_attr(sprintf(__('User photo for %s'), $user->Name())); ?>">
<?php	} ?>
<?php	if($user->FirstLifeAboutText() !== ''){ ?>
			<p><?php echo wp_kses(nl2br($user->FirstLifeAboutText()), array('br'=>array())); ?></p>
<?php	} ?>
		</section>
<?php } ?>
	</section>
<?php
		$Estates = Globals::i()->WebUI->GetEstates($user, array('PublicAccess'=>true));
		if($Estates->count() > 0){
?>
	<section class=estates>
		<h1><?php echo esc_html(__('Estates')); ?></h1>
		<ul>
<?php
			foreach($Estates as $Estate){
?>
			<li class=vcard><a class="url fn" href="<?php echo esc_attr(Template\link($Estate)); ?>"><?php echo esc_html($Estate->EstateName()); ?></a></li>
<?php
			}
?>
		</ul>
	</section>
<?php
		}
?>
<?php
	require_once('_footer.php');
?>