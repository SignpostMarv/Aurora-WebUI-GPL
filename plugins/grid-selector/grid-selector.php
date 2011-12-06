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

	use Aurora\Addon\WebUI\Configs;
	use Aurora\Addon\WebUI;

//!	Spits out a grid selection box.
/**
*	@param string $section site section
*	@param object instance of Aurora::Addon::WebUI corresponding to the currently selected grid.
*/
	function grid_selector($section = '', WebUI $currentGrid = null){
		static $gridSelector = 0;
		if(isset($currentGrid) === false){
			$currentGrid = Configs::d();
		}
		Configs::i()->rewind();
		
		do_action('before_grid_selector', $section, $currentGrid);
		echo '<form method=post action=?select-grid class="', esc_attr(implode(' ', array_unique(array_merge(array('grid-selector'), apply_filters('grid_selector_class', array()))))),'">';
		do_action('pre_grid_selector_fieldset', $section, $currentGrid);
		echo '<fieldset>';
		echo '<label for="grid_selector_', esc_attr($gridSelector++), '">',__('Select Grid'),'</label>';
		echo '<select class=grids id="grid_selector_',esc_attr($gridSelector),'" name=grid>';
		foreach(Configs::i() as $k=>$WebUI){
			$gridInfo = $WebUI->get_grid_info();
			echo '<option value="', esc_attr($k),'"',(($WebUI == $currentGrid) ? ' selected ' : ''),'>',esc_html(apply_filters('grid_selector_grid_nick',isset($gridInfo['gridnick']) ? $gridInfo['gridnick'] : 'AuroraSim')),'</option>';
		}
		echo '</select><button type=submit>',esc_html(__('Submit')),'</button></fieldset>';
		do_action('post_grid_selector_fieldset', $section, $currentGrid);
		echo '</form>';
		do_action('after_grid_selector', $section, $currentGrid);
	}

	function grid_selector_class_gridCount(array $classNames){
		if(Configs::i()->count() >= 2){
			$classNames[] = 'multi';
		}
		return $classNames;
	}

	add_action('grid_selector', __NAMESPACE__ . '\grid_selector', 10, 2);
	add_filter('grid_selector_class', __NAMESPACE__ . '\grid_selector_class_gridCount', 10, 1);
}
?>