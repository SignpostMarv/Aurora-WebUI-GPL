<?php
	add_action('webui_head', function(){
?>
	<script src="http://maps.google.com/maps/api/js?sensor=false"></script>
	<script src="./js/mapapi.js/mapapi-complete.js"></script>
	
	<script>
	window.onload = function(){
		var
			mapui = new mapapi.userinterfaces.minimalist({
				container  : document.getElementById('webui-gpl-mapapi-container'),
				gridConfig : mapapi['gridConfigs']['aurorasim']({
					'mapTextureURL' : 'http://127.0.0.1:8002/index.php?method=MapTexture2&x=_%x%_&y=_%y%_&zoom=_%zoom%_',
					'namespace'     : 'localhost.aurorawebuigpl',
					'vendor'        : 'Aurora Sim',
					'name'          : 'Marvville',
					'description'   : 'Marvville',
					'gridLabel'     : 'gridLabel',
					'gridLookup'    : {
						1000 : {
							1000 : {
								'name' : 'Foo 1',
								'uuid' : '6699b4ba-ac0c-4cc6-8ae8-b69ce981dfce'
							}
						}
					}
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