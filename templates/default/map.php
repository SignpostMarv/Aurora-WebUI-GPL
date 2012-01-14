<?php
	$MapAPI = Globals::i()->WebUI->getAttachedAPI('MapAPI');
	if(isset($MapAPI) === false){
		require_once('404.php');
		return;
	}

	add_action('webui_head', function(){
?>
	<script src="http://maps.google.com/maps/api/js?sensor=false"></script>
	<script src="./js/mapapi.js/mapapi-complete.js"></script>
	
	<script>
	window.onload = function(){
		var
			resetCSS = mapapi.ui.prototype.css.indexOf('reset.css')
		;
		if(resetCSS >= 0){
			mapapi.ui.prototype.css.splice(resetCSS, 1);
		}
		var
			mapui = new mapapi.userinterfaces.minimalist({
				container  : document.getElementById('webui-gpl-mapapi-container'),
				gridConfig : mapapi['gridConfigs']['aurorasim']({
					'mapTextureURL' :  <?php echo json_encode(Globals::i()->WebUI->getAttachedAPI('MapAPI')->mapTextureURL()); ?>,
					'namespace'     : <?php
						$baseURI = parse_url(Globals::i()->baseURI);
						$baseURI = implode('.', array_reverse(explode('.', $baseURI['host']))) . '.' . Globals::i()->WebUI->get_grid_info('gridnick');
						echo json_encode($baseURI);
					?>,
					'vendor'        : 'Aurora Sim',
					'name'          : <?php echo json_encode(Globals::i()->WebUI->get_grid_info('gridname')); ?>,
					'description'   : <?php echo json_encode(apply_filters('page_title', Globals::i()->WebUI->get_grid_info('gridnick')) . ' - ' . __('Powered by AuroraSim')); ?>,
					'gridLabel'     : <?php echo json_encode(Globals::i()->WebUI->get_grid_info('gridnick')); ?>,
					'gridLookup'    : <?php echo json_encode(Globals::i()->WebUI->getAttachedAPI('MapAPI')->MonolithicRegionLookup()); ?>
				})
			}),
			map   = mapui.renderer
		;
		map.scrollWheelZoom(true);
		map.smoothZoom(true);
		map.draggable(true);
		map.focus(1000,1000,0);
	};
	</script>
<?php
	});
	require_once('_header.php');
?>
	<section>
		<h1><?php echo esc_html(sprintf(__('Map of %s'), Globals::i()->WebUI->get_grid_info('gridname'))); ?></h1>
		<div id=webui-gpl-mapapi-container>
			<p>You need to have JavaScript enabled in order to see the map.</p>
		</div>
	</section>
<?php
	require_once('_footer.php');
?>