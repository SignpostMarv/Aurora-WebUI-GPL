<?php
	require_once('_header.php');
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
?>
		<table class=regions-list>
			<thead>
				<tr>
					<th class=region-name><?php echo esc_html(__('Region Name')); ?></th>
					<th class=region-loc-x><?php echo esc_html(__('Location: X')); ?></th>
					<th class=region-loc-y><?php echo esc_html(__('Location: Y')); ?></th>
				</tr>
			</thead>
			<tbody>
<?php foreach(Globals::i()->WebUI->GetRegions() as $region){ ?>
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