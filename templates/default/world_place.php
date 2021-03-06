<?php
use Aurora\Addon\WebUI;
use Aurora\Addon\WebUI\Template;
use Aurora\Addon\WebUI\LandData;
use Aurora\Addon\WebUI\GridRegion;
use Aurora\Addon\WebUI\EstateSettings;
$pathParts = explode('/', Globals::i()->section);
$group = false;
if(count($pathParts) >= 3){
	$estateName = urldecode($pathParts[2]);
	$estate = Globals::i()->WebUI->GetEstate($estateName);
	if($estate instanceof EstateSettings && $estateName !== $estate->EstateName()){
		$pathParts[2] = urlencode($estate->EstateName());
		header('Location: ' . Globals::i()->baseURI . Template\link(implode('/', $pathParts)));
		exit;
	}
}
if(count($pathParts) >= 4){
	$regionName = urldecode($pathParts[3]);
	$region = Globals::i()->WebUI->GetRegion($regionName);
	if($region instanceof GridRegion && ($regionName !== $region->RegionName() || $estateName !== $estate->EstateName())){
		$pathParts[2] = urlencode($estate->EstateName());
		$pathParts[3] = urlencode($region->RegionName());
		header('Location: ' . Globals::i()->baseURI . Template\link(implode('/', $pathParts)));
		exit;
	}
}

add_filter('body_class', function($classNames){
	switch(count(explode('/', Globals::i()->section))){
		case 3:
			$classNames[] = 'world-place-estate';
		break;
		case 4:
			$classNames[] = 'world-place-region';
		break;
		case 6:
			$classNames[] = 'world-place-parcel';
		break;
	}
	return $classNames;
});

if(count($pathParts) < 3 || (count($pathParts) >= 3 && (($estate instanceof EstateSettings) === false || $estate->PublicAccess() === false )) || (count($pathParts) >= 4 && ($region instanceof GridRegion) === false) || (count($pathParts) !== 3 && count($pathParts) !== 4 && count($pathParts) !== 6)){
	require_once('404.php');
	return;
}

$_GET['page'] = isset($_GET['page']) ? (integer)$_GET['page'] : 1;
$_GET['per'] = isset($_GET['per']) ? (integer)$_GET['per'] : 50;

if($_GET['page'] < 1){
	$_GET['page'] = 1;
}
if($_GET['per'] < 10){
	$_GET['per'] = 10;
}
$query = array();

if(count($pathParts) === 3){ // estate listing
	$estateOwner = Globals::i()->WebUI->GetProfile('',$estate->EstateOwner());
	require_once('_header.php');
?>
	<section class=vcard>
		<hgroup>
			<h1><?php echo esc_html(__('Estate')); ?></h1>
			<h2 class=fn><?php echo esc_html($estate->EstateName()); ?></h2>
		</hgroup>
		<p class=vcard><?php echo esc_html(__('Owner')); ?>: <a class="url fn" href="<?php echo esc_attr(Template\link($estateOwner)); ?>"><?php echo esc_html($estateOwner->Name()); ?></a></p>
	</section>
<?php
	$regions = Globals::i()->WebUI->GetRegionsInEstate($estate, null, ($_GET['page'] - 1) * $_GET['per'], $_GET['per'], true);
	if($regions->count() > 0){
?>
	<section class=regions>
		<h1><?php echo esc_html(__('Regions')); ?></h1>
		<nav>
			<ol>
<?php
	if($regions->count() > $_GET['per']){
?>
				<li><?php if($_GET['page'] > 1){ ?><a href="<?php echo esc_attr(Template\link($estate, '/?' . http_build_query(array_merge($query, array('page'=>1, 'per'=>$_GET['per']))))); ?>"><?php echo esc_html(__('First')); ?></a><?php }else{ ?><?php echo esc_html(__('First')); } ?></li>
<?php	if((integer)ceil($regions->count() / $_GET['per']) == 2){ ?>
				<li><?php if($_GET['page'] !== 2){ ?><a href="<?php echo esc_attr(Template\link($estate, '/?' . http_build_query(array_merge($query, array('page'=>2, 'per'=>$_GET['per']))))); ?>"><?php echo esc_html(__('Last')); ?></a><?php }else{ ?><?php echo esc_html(__('Last')); } ?></li>
<?php	}else{ ?>
<?php		if($_GET['page'] > 1){ ?>
				<li><a href="<?php echo esc_attr(Template\link($estate, '/?' . http_build_query(array_merge($query, array('page'=>$_GET['page'] - 1, 'per'=>$_GET['per']))))); ?>" title="<?php echo esc_attr(__('Previous')); ?>"><?php echo esc_html(__('Prev')); ?></a></li>
<?php		} ?>
<?php		if($_GET['page'] < $last){ ?>
				<li><a href="<?php echo esc_attr(Template\link($estate, '/?' . http_build_query(array_merge($query, array('page'=>$_GET['page'] + 1, 'per'=>$_GET['per']))))); ?>"><?php echo esc_html(__('Next')); ?></a></li>
<?php		} ?>
				<li><?php if($_GET['page'] !== $last){ ?><a href="<?php echo esc_attr(Template\link($estate, '/?' . http_build_query(array_merge($query, array('page'=>$last, 'per'=>$_GET['per']))))); ?>"><?php echo esc_html(__('Last')); ?></a><?php 
}else{ ?><?php echo esc_html(__('Last')); } ?></li>
<?php	} ?>
<?php
	}
?>
				<li><?php if($_GET['per'] !== 10){  ?><a href="<?php echo esc_attr(Template\link($estate, '/?' . http_build_query(array_merge($query, array('page'=>$_GET['page'], 'per'=>10 )))));  ?>">10</a><?php  }else{ ?>10<?php  } ?></li>
				<li><?php if($_GET['per'] !== 20){  ?><a href="<?php echo esc_attr(Template\link($estate, '/?' . http_build_query(array_merge($query, array('page'=>$_GET['page'], 'per'=>20 )))));  ?>">20</a><?php  }else{ ?>20<?php  } ?></li>
				<li><?php if($_GET['per'] !== 50){  ?><a href="<?php echo esc_attr(Template\link($estate, '/?' . http_build_query(array_merge($query, array('page'=>$_GET['page'], 'per'=>50 )))));  ?>">50</a><?php  }else{ ?>50<?php  } ?></li>
				<li><?php if($_GET['per'] !== 100){ ?><a href="<?php echo esc_attr(Template\link($estate, '/?' . http_build_query(array_merge($query, array('page'=>$_GET['page'], 'per'=>100))))); ?>">100</a><?php }else{ ?>100<?php } ?></li>
			</ol>
		</nav>
		<ul>
<?php
		$i = ($_GET['page'] - 1) * $_GET['per'];
		$j = $_GET['page'] * $_GET['per'];
		foreach($regions as $region){
?>
			<li class=vcard><a class="url fn" href="<?php echo esc_attr(Template\link($region)); ?>"><?php echo esc_html($region->RegionName()); ?></a></li>
<?php
			if(++$i >= $j){
				break;
			}
		}
?>
		</ul>
	</section>
<?php
	}
}else if(count($pathParts) === 4){ // single region
	require_once('_header.php');
?>
	<section class=vcard data-size-x="<?php echo esc_attr($region->RegionSizeX()); ?>" data-size-y="<?php echo esc_attr($region->RegionSizeY()); ?>" data-size-z="<?php echo esc_attr($region->RegionSizeZ()); ?>">
		<hgroup>
			<h1><?php echo esc_html(__('Region')); ?></h1>
			<h2 class=fn><?php echo esc_html($region->RegionName()); ?></h2>
		</hgroup>
<?php	if($region->EstateOwner() !== '00000000-0000-0000-0000-000000000000'){
			$estateOwner = Globals::i()->WebUI->GetGridUserInfo($region->EstateOwner());
?>
		<p class="vcard estate-owner"><?php echo esc_html(__('Estate Owner')); ?>: <a class="fn url" href="<?php echo esc_attr(Template\link($estateOwner)); ?>"><?php echo esc_html($estateOwner->Name()); ?></a></p>
<?php	}else{ ?>
		<p class=estate-owner><?php echo esc_html(__('Estate Owner')); ?>: <?php echo esc_html(__('Unknown User')); ?></p>
<?php	} ?>
		<img class=photo src="<?php echo esc_attr(Globals::i()->WebUI->MapTexture($region)); ?>" alt="<?php echo esc_attr(sprintf(__('Map texture for %s'), $region->RegionName())); ?>">
		<span class="uuid uid"><?php echo esc_html($region->RegionID()); ?></span>
	</section>
<?php
	$parcels = Globals::i()->WebUI->GetParcelsByRegion(($_GET['page'] - 1) * $_GET['per'], $_GET['per'], $region);
	if($parcels->count() > 0){
?>
	<section class=parcels>
		<h1><?php echo esc_html(sprintf(__('Parcels in %1$s'), $region->RegionName())); ?></h1>
		<nav>
			<ol>
<?php
	if($parcels->count() > $_GET['per']){
?>
				<li><?php if($_GET['page'] > 1){ ?><a href="<?php echo esc_attr(Template\link($region, '/?' . http_build_query(array_merge($query, array('page'=>1, 'per'=>$_GET['per']))))); ?>"><?php echo esc_html(__('First')); ?></a><?php }else{ ?><?php echo esc_html(__('First')); } ?></li>
<?php	if((integer)ceil($parcels->count() / $_GET['per']) == 2){ ?>
				<li><?php if($_GET['page'] !== 2){ ?><a href="<?php echo esc_attr(Template\link($region, '/?' . http_build_query(array_merge($query, array('page'=>2, 'per'=>$_GET['per']))))); ?>"><?php echo esc_html(__('Last')); ?></a><?php }else{ ?><?php echo esc_html(__('Last')); } ?></li>
<?php	}else{ ?>
<?php		if($_GET['page'] > 1){ ?>
				<li><a href="<?php echo esc_attr(Template\link($region, '/?' . http_build_query(array_merge($query, array('page'=>$_GET['page'] - 1, 'per'=>$_GET['per']))))); ?>" title="<?php echo esc_attr(__('Previous')); ?>"><?php echo esc_html(__('Prev')); ?></a></li>
<?php		} ?>
<?php		if($_GET['page'] < $last){ ?>
				<li><a href="<?php echo esc_attr(Template\link($region, '/?' . http_build_query(array_merge($query, array('page'=>$_GET['page'] + 1, 'per'=>$_GET['per']))))); ?>"><?php echo esc_html(__('Next')); ?></a></li>
<?php		} ?>
				<li><?php if($_GET['page'] !== $last){ ?><a href="<?php echo esc_attr(Template\link($region, '/?' . http_build_query(array_merge($query, array('page'=>$last, 'per'=>$_GET['per']))))); ?>"><?php echo esc_html(__('Last')); ?></a><?php 
}else{ ?><?php echo esc_html(__('Last')); } ?></li>
<?php	} ?>
<?php
	}
?>
				<li><?php if($_GET['per'] !== 10){  ?><a href="<?php echo esc_attr(Template\link($region, '/?' . http_build_query(array_merge($query, array('page'=>$_GET['page'], 'per'=>10 )))));  ?>">10</a><?php  }else{ ?>10<?php  } ?></li>
				<li><?php if($_GET['per'] !== 20){  ?><a href="<?php echo esc_attr(Template\link($region, '/?' . http_build_query(array_merge($query, array('page'=>$_GET['page'], 'per'=>20 )))));  ?>">20</a><?php  }else{ ?>20<?php  } ?></li>
				<li><?php if($_GET['per'] !== 50){  ?><a href="<?php echo esc_attr(Template\link($region, '/?' . http_build_query(array_merge($query, array('page'=>$_GET['page'], 'per'=>50 )))));  ?>">50</a><?php  }else{ ?>50<?php  } ?></li>
				<li><?php if($_GET['per'] !== 100){ ?><a href="<?php echo esc_attr(Template\link($region, '/?' . http_build_query(array_merge($query, array('page'=>$_GET['page'], 'per'=>100))))); ?>">100</a><?php }else{ ?>100<?php } ?></li>
			</ol>
		</nav>
		<ul>
<?php
		$i = ($_GET['page'] - 1) * $_GET['per'];
		$j = $_GET['page'] * $_GET['per'];
		foreach($parcels as $parcel){
?>
			<li class=vcard><a class="url fn" href="<?php echo esc_attr(Template\link($parcel)); ?>"><?php echo esc_html($parcel->Name()); ?></a></li>
<?php
			if(++$i >= $j){
				break;
			}
		}
?>
		</ul>
	</section>
<?php
	}
}else if(count($pathParts) === 6){ // single parcel
	$parcelInfoUUID = str_split(str_pad(preg_replace_callback('/g\d+/',function($matches){
			return str_repeat('0', (integer)substr($matches[0],1));
		}, $pathParts[5]),32, '0'),4);
	$parcelInfoUUID = $parcelInfoUUID[0] . $parcelInfoUUID[1] . '-' . $parcelInfoUUID[2] . '-' . $parcelInfoUUID[3] . '-' . $parcelInfoUUID[4] . '-' . $parcelInfoUUID[5] . $parcelInfoUUID[6] . $parcelInfoUUID[7];
	if(preg_match(WebUI::regex_UUID, $parcelInfoUUID) != 1){
		throw new WebUI\InvalidArgumentException('Parcel ID was not a valid UUID.');
	}
	$parcel = Globals::i()->WebUI->GetParcel($parcelInfoUUID);
	if(($parcel instanceof LandData) === false){
		require_once('404.php');
		return;
	}
	require_once('_header.php');
?>
	<section class=vcard>
		<hgroup>
			<h1><?php echo esc_html(__('Parcel')); ?></h1>
			<h2 class=fn><?php echo esc_html($parcel->Name()); ?></h2>
		</hgroup>
<?php if($parcel->OwnerID() !== '00000000-0000-0000-0000-000000000000'){
			$parcelOwner = Globals::i()->WebUI->GetGridUserInfo($parcel->OwnerID());		
?>
		<p class="vcard parcel-owner"><?php echo esc_html(__('Parcel Owner')); ?>: <a class="url fn" href="<?php echo esc_attr(Template\link('/world/user/' . $parcelOwner->Name())); ?>"><?php echo esc_html($parcelOwner->Name()); ?></a></p>
<?php }else{ ?>
		<p class=parcel-owner><?php echo esc_html(__('Parcel Owner')); ?>: <?php echo esc_html(__('Unknown User')); ?></p>
<?php } ?>
<?php if($parcel->SnapshotID() !== '00000000-0000-0000-0000-000000000000'){ ?>
		<img class=photo src="<?php echo esc_attr(Globals::i()->WebUI->GridTexture($parcel->SnapshotID())); ?>" alt="<?php echo esc_attr(sprintf(__('Parcel snapshot for %s'), $parcel->Name())); ?>">
<?php } ?>
		<span class="uuid uid"><?php echo esc_html($parcel->GlobalID()); ?></span>
	</section>
<?php
}
require_once('_footer.php');
?>