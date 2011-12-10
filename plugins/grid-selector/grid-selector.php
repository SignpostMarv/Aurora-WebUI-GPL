<?php
/*
Plugin Name: Grid Selector
Plugin URI: https://github.com/SignpostMarv/Aurora-WebUI-GPL
Description: Spits out a grid selection box, optionally based on the section of the website.
Version: 0.1
Author: SignpostMarv
Author URI: https://github.com/SignpostMarv/
*/

namespace Aurora\Addon\WebUI\plugins{

	use Aurora\Addon\WebUI;
	use Aurora\Addon\WebUI\Configs;

//!	Spits out a grid selection box.
/**
*	@param string $section site section
*	@param mixed $currentGrid NULL or instance of Aurora::Addon::WebUI corresponding to the currently selected grid.
*/
	function grid_selector($section = '', WebUI $currentGrid = null){
		if(isset($currentGrid) === false){
			$currentGrid = Configs::d();
		}
		Configs::i()->rewind();

		do_action('before_grid_selector', $section, $currentGrid);
		echo '<form method=post action=?select-grid class="', esc_attr(implode(' ', array_unique(array_merge(array('grid-selector'), apply_filters('grid_selector_class', array()))))),'">';
		do_action('pre_grid_selector_fieldset', $section, $currentGrid);
		do_action('grid_selector_fieldset', $currentGrid);
		do_action('post_grid_selector_fieldset', $section, $currentGrid);
		echo '<fieldset class=buttons><button type=submit>',esc_html(__('Submit')),'</button></fieldset>','</form>';
		do_action('after_grid_selector', $section, $currentGrid);
	}

	function grid_selector_fieldset(WebUI $currentGrid){
		if(isset($currentGrid) === false){
			$currentGrid = Configs::d();
		}
		Configs::i()->rewind();
?>
			<fieldset class="<?php echo esc_attr(implode(' ', array_unique(array_merge(array('grid-selector'), apply_filters('grid_selector_class', array()))))); ?>">
				<legend><?php echo esc_html(__('Select Grid')); ?></legend>
				<select class=grids name=grid>
<?php
	Configs::i()->rewind();
	foreach(Configs::i() as $k=>$webui){ ?>
					<option value="<?php echo esc_attr($k); ?>"<?php if($currentGrid === $webui){?> selected <?php } ?>><?php echo esc_html($webui->get_grid_info('gridnick')); ?></option>
<?php } ?>
				</select>
			</fieldset>
<?php
	}

	function grid_selector_class_gridCount(array $classNames){
		if(Configs::i()->count() >= 2){
			$classNames[] = 'multi';
		}
		return $classNames;
	}

	add_action('grid_selector_fieldset', __NAMESPACE__ . '\grid_selector_fieldset');
	add_action('grid_selector', __NAMESPACE__ . '\grid_selector', 10, 2);
	add_filter('grid_selector_class', __NAMESPACE__ . '\grid_selector_class_gridCount');
}
?>