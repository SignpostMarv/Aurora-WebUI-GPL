<?php
	use Aurora\Addon\WebUI\Template;
	$_GET['page'] = isset($_GET['page']) ? (integer)$_GET['page'] : 1;
	$_GET['per'] = isset($_GET['per']) ? (integer)$_GET['per'] : 10;

	if($_GET['page'] < 1){
		$_GET['page'] = 1;
	}
	if($_GET['per'] < 10){
		$_GET['per'] = 10;
	}
	$sortByRegion = isset($_GET['srn']) ? (bool)$_GET['srn'] : null;
	$sortByLocX   = isset($_GET['slx']) ? (bool)$_GET['slx'] : null;
	$sortByLocY   = isset($_GET['sly']) ? (bool)$_GET['sly'] : null;
	$query = array();
	if(isset($sortByRegion) === true){
		$query['srn'] = (integer)$sortByRegion;
	}
	if(isset($sortByLocX) === true){
		$query['slx'] = (integer)$sortByLocX;
	}
	if(isset($sortByLocY) === true){
		$query['sly'] = (integer)$sortByLocY;
	}
	$start = (($_GET['page'] - 1) * $_GET['per']);
	try{
		$regions = Globals::i()->WebUI->GetRegions(null, $start, $_GET['per'], $sortByRegion, $sortByLocX, $sortByLocY);
		if($start >= $regions->count()){
			require_once('404.php');
			return;
		}
	}catch(Aurora\Addon\WebUI\LengthException $e){
		require_once('404.php');
		return;
	}
	$last = (integer)ceil($regions->count() / $_GET['per']);
	require_once('_header.php');
?>
	<section>
		<h1><?php echo esc_html(__('Region List')); ?></h1>
		<nav>
<?php
	if($regions->count() > $_GET['per']){
?>
			<ol>
				<li><?php if($_GET['page'] > 1){ ?><a href="<?php echo esc_attr(Template\link('/world/regions/?' . http_build_query(array_merge($query, array('page'=>1, 'per'=>$_GET['per']))))); ?>"><?php echo esc_html(__('First')); ?></a><?php }else{ ?><?php echo esc_html(__('First')); } ?></li>
<?php	if((integer)ceil($regions->count() / $_GET['per']) == 2){ ?>
				<li><?php if($_GET['page'] !== 2){ ?><a href="<?php echo esc_attr(Template\link('/world/regions/?' . http_build_query(array_merge($query, array('page'=>2, 'per'=>$_GET['per']))))); ?>"><?php echo esc_html(__('Last')); ?></a><?php }else{ ?><?php echo esc_html(__('Last')); } ?></li>
<?php	}else{ ?>
<?php		if($_GET['page'] > 1){ ?>
				<li><a href="<?php echo esc_attr(Template\link('/world/regions/?' . http_build_query(array_merge($query, array('page'=>$_GET['page'] - 1, 'per'=>$_GET['per']))))); ?>" title="<?php echo esc_attr(__('Previous')); ?>"><?php echo esc_html(__('Prev')); ?></a></li>
<?php		} ?>
<?php		if($_GET['page'] < $last){ ?>
				<li><a href="<?php echo esc_attr(Template\link('/world/regions/?' . http_build_query(array_merge($query, array('page'=>$_GET['page'] + 1, 'per'=>$_GET['per']))))); ?>"><?php echo esc_html(__('Next')); ?></a></li>
<?php		} ?>
				<li><?php if($_GET['page'] !== $last){ ?><a href="<?php echo esc_attr(Template\link('/world/regions/?' . http_build_query(array_merge($query, array('page'=>$last, 'per'=>$_GET['per']))))); ?>"><?php echo esc_html(__('Last')); ?></a><?php 
}else{ ?><?php echo esc_html(__('Last')); } ?></li>
<?php	} ?>
<?php
	}
?>
				<li><?php if($_GET['per'] !== 10){  ?><a href="<?php echo esc_attr(Template\link('/world/regions/?' . http_build_query(array_merge($query, array('page'=>$_GET['page'], 'per'=>10 )))));  ?>">10</a><?php  }else{ ?>10<?php  } ?></li>
				<li><?php if($_GET['per'] !== 20){  ?><a href="<?php echo esc_attr(Template\link('/world/regions/?' . http_build_query(array_merge($query, array('page'=>$_GET['page'], 'per'=>20 )))));  ?>">20</a><?php  }else{ ?>20<?php  } ?></li>
				<li><?php if($_GET['per'] !== 50){  ?><a href="<?php echo esc_attr(Template\link('/world/regions/?' . http_build_query(array_merge($query, array('page'=>$_GET['page'], 'per'=>50 )))));  ?>">50</a><?php  }else{ ?>50<?php  } ?></li>
				<li><?php if($_GET['per'] !== 100){ ?><a href="<?php echo esc_attr(Template\link('/world/regions/?' . http_build_query(array_merge($query, array('page'=>$_GET['page'], 'per'=>100))))); ?>">100</a><?php }else{ ?>100<?php } ?></li>
			</ol>
		</nav>
		<table class=regions-list>
			<thead>
				<tr>
					<th class=region-name><a href="<?php echo esc_attr(Template\link('/world/regions/?' . http_build_query(array_merge($query, array('srn'=>!$sortByRegion))))); ?>"><?php echo esc_html(__('Region Name')); ?></a></th>
					<th class=region-loc-x><a href="<?php echo esc_attr(Template\link('/world/regions/?' . http_build_query(array_merge($query, isset($sortByLocX) ? array('slx'=>!$sortByLocX) : array('slx'=>0))))); ?>"><?php echo esc_html(__('Location: X')); ?></a></th>
					<th class=region-loc-y><a href="<?php echo esc_attr(Template\link('/world/regions/?' . http_build_query(array_merge($query, isset($sortByLocY) ? array('sly'=>!$sortByLocY) : array('sly'=>0))))); ?>"><?php echo esc_html(__('Location: Y')); ?></a></th>
				</tr>
			</thead>
			<tbody>
<?php
	$i=0;
	foreach($regions as $region){ ?>
				<tr>
					<th scope=row><?php echo esc_html($region->RegionName()); ?></th>
					<td><?php echo esc_html($region->RegionLocX() / 256); ?></td>
					<td><?php echo esc_html($region->RegionLocY() / 256); ?></td>
				</tr>
<?php
		++$i;
		if($i >= $_GET['per']){
			break;
		}
	} ?>
			</tbody>
		</table>
	</section>
<?php
	require_once('_footer.php');
?>