<?php
//! This file is an implementation of mapapi.cs (https://github.com/SignpostMarv/mapapi.cs )
//! As it's currently the only public example implementation using mapapi.cs and that this project is GPL'd,
//!	consider this file (and this file only) to be under the same license as that project.
	use Aurora\Addon\WebUI\Template;

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
			map   = mapui.renderer,
			infoWindow = undefined,
			infoWindows = {},
			errorInfoWindows = {}
		;
		map.scrollWheelZoom(true);
		map.smoothZoom(true);
		map.draggable(true);
		map.addListener('click', function(e){
			var
				x = Math.floor(e.pos['x']),
				y = Math.floor(e.pos['y']),
				origX = e.pos['x'],
				origY = e.pos['y']
			;
			map.gridConfig.pos2region(e.pos, function(result){
				if(infoWindow != undefined){
					infoWindow.close();
				}
				infoWindow = mapui.infoWindow({
					content  : <?php echo json_encode(esc_html(__('Region Name'))); ?> + ': ' + result.region,
					position : e.pos
				});
				if(infoWindows[result.region] != undefined){
					infoWindows[result.region].close();
				}
				infoWindows[result.region] = infoWindow;
				if(window.XMLHttpRequest && window.JSON && window.JSON.parse){
					var
						xhr = new XMLHttpRequest(),
						url = <?php echo json_encode(Globals::i()->baseURI . Template\link('/api/mapapi/RegionDetails/%s')),"\n"; ?>
					;
					xhr.open('GET', url.replace('%s',escape(result.region)));
					xhr.onload = function(e){
						try{
							var
								result = JSON.parse(xhr.responseText)
							;
							if(result.Error){
								infoWindow.content(<?php echo json_encode(esc_html(__('API Error'))); ?> + ': ' + result.Error);
							}else if(!result.Region){
								infoWindow.content(<?php echo json_encode(esc_html(__('API Error'))); ?> + ': ' + <?php echo json_encode(esc_html(__('No message specified, but no region returned either.'))); ?>);
							}else{
								var
									region = result.Region,
									p = document.createElement('p'),
									a = document.createElement('a'),
									localX = (origX - (region.RegionLocX / 256)) * 256,
									localY = (origX - (region.RegionLocY / 256)) * 256
								;
								p.appendChild(a);
								infoWindow.content(
									<?php echo json_encode(esc_html(__('Region Name'))); ?> + ': ' + region.RegionName + "\n" +
									<?php echo json_encode(esc_html(__('Coordinates'))); ?> + ': ' + Math.floor(region.RegionLocX / 256) + ', ' + Math.floor(region.RegionLocY / 256) + "\n" +
									<?php echo json_encode(esc_html(__('Size'))); ?> + ': ' + region.RegionSizeX + ', ' + region.RegionSizeY + "\n" +
									<?php echo json_encode(esc_html(__('Owner'))); ?> + ': ' + region.EstateOwnerName
								);
								a.href = 'secondlife://' + escape(region.RegionName) + '/' + escape(localX) + '/' + escape(localY);
								a.appendChild(document.createTextNode(<?php echo json_encode(esc_html(__('Teleport'))); ?>));
								infoWindow.DOM.firstChild.firstChild.appendChild(p);
							}
						}catch(ex){
							infoWindow.content('Exception occurred: ' + ex);
						}
					};
					xhr.send();
				}else{
					map.gridConfig.region2pos(result.region, function(result){
						infoWindows[result.region].content(
							<?php echo json_encode(esc_html(__('Region Name'))); ?> + ': ' + result.region + "\n" +
							<?php echo json_encode(esc_html(__('Coordinates'))); ?> + ': ' + result.pos.x + ', ' + result.pos.y
						);
					});
				}
				infoWindow.open(mapui);
			}, function(errorMsg){
				if(infoWindow != undefined){
					infoWindow.close();
				}
				infoWindow = mapui.infoWindow({
					content  : errorMsg,
					position : e.pos
				});
				if(errorInfoWindows[x] != undefined && errorInfoWindows[x][y] != undefined){
					errorInfoWindows[x][y].close();
				}
				errorInfoWindows[x] = errorInfoWindows[x] || {};
				errorInfoWindows[x][y] = infoWindow;
				infoWindow.open(mapui);
			});
		});
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
<?php	if(isset($regionName) === true){ ?>
		map.gridConfig.region2pos(<?php echo json_encode($regionName); ?>, function(e){
			e.pos.x += localX / 256;
			e.pos.y += localY / 256;
			map.focus(e.pos);
		});
<?php	} ?>
	};
	</script>
	<style>
#webui-gpl-mapapi-container.mapapi-ui-minimalist .mapapi-ui-infowindow .mapapi-ui-item-contents p{
	white-space: nowrap ;
}
	</style>
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