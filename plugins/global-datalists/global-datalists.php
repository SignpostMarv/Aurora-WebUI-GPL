<?php
/*
Plugin Name: Global Datalists
Plugin URI: https://github.com/SignpostMarv/Aurora-WebUI-GPL
Description: Makes datalist elements globally available by adding them in the footer of a page.
Version: 0.1
Author: SignpostMarv
Author URI: https://github.com/SignpostMarv/
*/


namespace Aurora\Addon\WebUI\plugins\GlobalDataLists{

	use Globals;


	function RegionNames($output){
		if(apply_filters('GlobalDataLists::RegionNames', false) === true){
?>
<datalist id=datalist-region-names>
<?php foreach(Globals::i()->WebUI->GetRegions() as $region){ ?>
	<option value="<?php echo esc_attr($region->RegionName()); ?>">
<?php } ?>
</datalist>

<?php
		}
	}

	add_action('webui_footer', __NAMESPACE__ . '\RegionNames');
}
?>
