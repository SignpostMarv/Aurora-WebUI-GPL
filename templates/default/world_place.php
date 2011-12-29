<?php
use Aurora\Addon\WebUI\Template;
$pathParts = explode('/', Globals::i()->section);
$group = false;
if(count($pathParts) >= 3){
	$regionName = urldecode(implode('/', array_slice($pathParts, 2)));
	$region = Globals::i()->WebUI->GetRegion($regionName);
	if($region instanceof Aurora\Addon\WebUI\GridRegion && $regionName !== $region->RegionName()){
		$pathParts[2] = $region->RegionName();
		header('Location: ' . Globals::i()->baseURI . Template\link(implode('/', $pathParts)));
		exit;
	}
}
if(count($pathParts) === 2){
	header('Location: ' . Globals::i()->baseURI . Template\link('/world/place/' . urlencode($region->RegionName()) . '/'));
	exit;
}else if(count($pathParts) < 3 || ($region instanceof Aurora\Addon\WebUI\GridRegion) === false || (count($pathParts) !== 3)){
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

require_once('_header.php');
if(count($pathParts) === 3){ // single region
?>
	<section class=vcard data-size-x="<?php echo esc_attr($region->RegionSizeX()); ?>" data-size-y="<?php echo esc_attr($region->RegionSizeY()); ?>" data-size-z="<?php echo esc_attr($region->RegionSizeZ()); ?>">
		<hgroup>
			<h1><?php echo esc_html(__('Region')); ?></h1>
			<h2 class=fn><?php echo esc_html($region->RegionName()); ?></h2>
		</hgroup>
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
				<li><?php if($_GET['page'] > 1){ ?><a href="<?php echo esc_attr(Template\link('/world/place/' . urlencode($region->RegionName()) . '/?' . http_build_query(array_merge($query, array('page'=>1, 'per'=>$_GET['per']))))); ?>"><?php echo esc_html(__('First')); ?></a><?php }else{ ?><?php echo esc_html(__('First')); } ?></li>
<?php	if((integer)ceil($parcels->count() / $_GET['per']) == 2){ ?>
				<li><?php if($_GET['page'] !== 2){ ?><a href="<?php echo esc_attr(Template\link('/world/place/' . urlencode($region->RegionName()) . '/?' . http_build_query(array_merge($query, array('page'=>2, 'per'=>$_GET['per']))))); ?>"><?php echo esc_html(__('Last')); ?></a><?php }else{ ?><?php echo esc_html(__('Last')); } ?></li>
<?php	}else{ ?>
<?php		if($_GET['page'] > 1){ ?>
				<li><a href="<?php echo esc_attr(Template\link('/world/place/' . urlencode($region->RegionName()) . '/?' . http_build_query(array_merge($query, array('page'=>$_GET['page'] - 1, 'per'=>$_GET['per']))))); ?>" title="<?php echo esc_attr(__('Previous')); ?>"><?php echo esc_html(__('Prev')); ?></a></li>
<?php		} ?>
<?php		if($_GET['page'] < $last){ ?>
				<li><a href="<?php echo esc_attr(Template\link('/world/place/' . urlencode($region->RegionName()) . '/?' . http_build_query(array_merge($query, array('page'=>$_GET['page'] + 1, 'per'=>$_GET['per']))))); ?>"><?php echo esc_html(__('Next')); ?></a></li>
<?php		} ?>
				<li><?php if($_GET['page'] !== $last){ ?><a href="<?php echo esc_attr(Template\link('/world/place/' . urlencode($region->RegionName()) . '/?' . http_build_query(array_merge($query, array('page'=>$last, 'per'=>$_GET['per']))))); ?>"><?php echo esc_html(__('Last')); ?></a><?php 
}else{ ?><?php echo esc_html(__('Last')); } ?></li>
<?php	} ?>
<?php
	}
?>
				<li><?php if($_GET['per'] !== 10){  ?><a href="<?php echo esc_attr(Template\link('/world/place/' . urlencode($region->RegionName()) . '/?' . http_build_query(array_merge($query, array('page'=>$_GET['page'], 'per'=>10 )))));  ?>">10</a><?php  }else{ ?>10<?php  } ?></li>
				<li><?php if($_GET['per'] !== 20){  ?><a href="<?php echo esc_attr(Template\link('/world/place/' . urlencode($region->RegionName()) . '/?' . http_build_query(array_merge($query, array('page'=>$_GET['page'], 'per'=>20 )))));  ?>">20</a><?php  }else{ ?>20<?php  } ?></li>
				<li><?php if($_GET['per'] !== 50){  ?><a href="<?php echo esc_attr(Template\link('/world/place/' . urlencode($region->RegionName()) . '/?' . http_build_query(array_merge($query, array('page'=>$_GET['page'], 'per'=>50 )))));  ?>">50</a><?php  }else{ ?>50<?php  } ?></li>
				<li><?php if($_GET['per'] !== 100){ ?><a href="<?php echo esc_attr(Template\link('/world/place/' . urlencode($region->RegionName()) . '/?' . http_build_query(array_merge($query, array('page'=>$_GET['page'], 'per'=>100))))); ?>">100</a><?php }else{ ?>100<?php } ?></li>
			</ol>
		</nav>
		<ul>
<?php
		$i = ($_GET['page'] - 1) * $_GET['per'];
		$j = $_GET['page'] * $_GET['per'];
		foreach($parcels as $parcel){
?>
			<li class=vcard><a class="url fn" href="<?php echo esc_attr(Template\link('/world/place/' . urlencode($region->RegionName()) . '/' . urlencode($parcel->Name()) . '/' . urlencode(preg_replace_callback('/0{3,}/',function($matches){return 'g' . strlen($matches[0]);}, rtrim(str_replace('-','',$parcel->InfoUUID()),'0'))))); ?>"><?php echo esc_html($parcel->Name()); ?></a></li>
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
}
require_once('_footer.php');
?>