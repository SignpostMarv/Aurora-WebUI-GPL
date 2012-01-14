<?php
	$MapAPI = Globals::i()->WebUI->getAttachedAPI('MapAPI');
	if(isset($MapAPI) === false){
		require_once('404.php');
		return;
	}

	add_action('webui_head', function(){
		$pathParts = explode('/', Globals::i()->section);
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
<?php
		$regionName = null;
		if(count($pathParts) >= 2){
			$regionName = urldecode($pathParts[1]);
		}
		$localX = $localY = 128;
		if(count($pathParts) >= 3 && ctype_digit($pathParts[2]) === true){
			$localX = (integer)$pathParts[2];
			if(count($pathParts) >= 4 && ctype_digit($pathParts[3]) === true){
				$localY = (integer)$pathParts[3];
			}
		}
?>
		var
			region = <?php echo json_encode($regionName); ?>,
			localX = <?php echo json_encode($localX); ?>,
			localY = <?php echo json_encode($localY); ?>,
			pos = {'x' : 1000 + (localX / 256), 'y' : 1000  + (localY / 256)}
		;
		map.focus(pos.x, pos.y,0);
/*
		if(history.pushState){
			window.onpopstate = function(e){
				console.log(e.state);
				if(e.state){
					if(e.state.pos){
						map.focus(e.state.pos);
					}
				}
			};
		}
		if(history.replaceState){
			history.replaceState({
				'pos' : pos,
				'region' : region
			}, map.gridConfig.name, window.location.pathname);
		}
*/
<?php	if(isset($regionName) === true){ ?>
		map.gridConfig.region2pos(<?php echo json_encode($regionName); ?>, function(e){			
			e.pos.x += localX / 256;
			e.pos.y += localY / 256;
			map.focus(e.pos);
/*
			history.pushState({
				'pos'    : e.pos,
				'region' : region
			}, map.gridConfig.name, window.location.pathname);
*/
		});
<?php	} ?>
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