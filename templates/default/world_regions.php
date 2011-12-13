<?php
	require_once('_header.php');

	use Aurora\Addon\WebUI\Template;
?>
	<section>
		<h1><?php echo esc_html(__('Region List')); ?></h1>
<?php
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
?>
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
	foreach(Globals::i()->WebUI->GetRegions(null, 0, 10, $sortByRegion, $sortByLocX, $sortByLocY) as $region){ ?>
				<tr>
					<th scope=row><?php echo esc_html($region->RegionName()); ?></th>
					<td><?php echo esc_html($region->RegionLocX() / 256); ?></td>
					<td><?php echo esc_html($region->RegionLocY() / 256); ?></td>
				</tr>
<?php } ?>
			</tbody>
		</table>
	</section>
<?php
	require_once('_footer.php');
?>