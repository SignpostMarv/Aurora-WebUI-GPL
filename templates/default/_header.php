<?php
	use Aurora\Addon\WebUI\Configs;
	use Aurora\Addon\WebUI\Template;

	header('Content-Type: text/html');
?><!DOCTYPE html>
<html>
<head>
<title><?php echo esc_html(Globals::i()->WebUI->get_grid_info('gridname'));?></title>
<base href="<?php echo esc_attr(Globals::i()->baseURI); ?>">
<link rel="stylesheet" type="text/css" href="css/templates/default/style.css" />
<meta charset="UTF-8" />
<script></script>
<?php do_action('webui_head'); ?>
</head>
<body class="<?php echo esc_attr(implode(' ', array_merge(array(str_replace(array('/',' '),array('-','_'), Globals::i()->sectionFile)),array_unique(apply_filters('body_class', array()))))); ?>">
<header>
	<hgroup>
		<h1><?php echo esc_html(apply_filters('page_title', Globals::i()->WebUI->get_grid_info('gridname'))); ?></h1>
		<h2><?php echo esc_html(apply_filters('page_title', Globals::i()->WebUI->get_grid_info('gridnick'))); ?> - <?php echo esc_html(__('Powered by AuroraSim')); ?></h2>
	</hgroup>
	<nav id="main-nav">
		<ul>
			<?php echo wp_kses(apply_filters('main_nav_links', 
				'<li><a href="' . esc_attr(Template\link('/')) . '">' . esc_html(__('Home')) . '</a></li>' .
				'<li><a href="' . esc_attr(Template\link('/news/')) . '">' . esc_html(__('News')) . '</a></li>' .
				'<li><a href="' . esc_attr(Template\link('world/')) . '">' . __('World') . '</a><ul>' .
					'<li><a href="' . esc_attr(Template\link('world/regions/')) . '">' . esc_html(__('Regions')) . '</a></li>' .
					'<li><a href="' . esc_attr(Template\link('world/groups/')) . '">' . esc_html(__('Groups')) . '</a></li>' .
				'</ul></li>'
						
			), array('li'=>array(), 'ul'=>array(), 'a'=>array('href'=>array(), 'rel'=>array('nofollow'))), array('http', 'https')); ?>
<?php if(Configs::i()->count() > 1){ ?>
			<li><?php do_action('grid_selector');?></li>
<?php } ?>
		</ul>
	</nav>
</header>
<div id="main-content">
