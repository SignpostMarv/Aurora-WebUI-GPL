<?php
	use Aurora\Addon\WebUI\Configs;
	use Aurora\Addon\WebUI\Template;

	use libAurora\Template\navigation\Page;
	use libAurora\Template\navigation\Pages;

	$htmlClass = array_unique(apply_filters('htmlClass', array()));
	header('Content-Type: text/html');
?><!DOCTYPE html>
<html<?php echo (count($htmlClass) > 0) ? ' class="' . esc_attr(implode(' ', $htmlClass)) . '"' : ''; ?>>
<head>
<title><?php echo esc_html(Globals::i()->WebUI->get_grid_info('gridname'));?></title>
<base href="<?php echo esc_attr(Globals::i()->baseURI); ?>">
<link rel="stylesheet" type="text/css" href="css/templates/default/style.css" />
<meta charset="UTF-8" />
<?php do_action('webui_head'); ?>
</head>
<body class="<?php echo esc_attr(implode(' ', array_merge(array(str_replace(array('/',' '),array('-','_'), Globals::i()->sectionFile)),array_unique(apply_filters('body_class', array()))))); ?>">
<header>
	<hgroup>
		<h1><?php echo esc_html(apply_filters('page_title', Globals::i()->WebUI->get_grid_info('gridname'))); ?></h1>
		<h2><?php echo esc_html(apply_filters('page_title', Globals::i()->WebUI->get_grid_info('gridnick'))); ?> - <?php echo esc_html(__('Powered by AuroraSim')); ?></h2>
	</hgroup>
	<nav id="main-nav">
<?php
	$nav = Pages::f();
	$nav['Home']  = Page::f(__('Home'), -9999, esc_attr(Template\link('/')), '', 2);
	$nav['News']  = Page::f(__('News'), 0, esc_attr(Template\link('/news/')), '', 2);
	$nav['World'] = Page::f(__('World'), 100, esc_attr(Template\link('/world/')), '', 2);
	$nav['World']['Regions'] = Page::f(__('Regions'), 0, esc_attr(Template\link('/world/regions/')), '', 2);
	$nav['World']['Groups']  = Page::f(__('Groups'), 0, esc_attr(Template\link('/world/groups/')), '', 2);
	$nav['World']['Users']   = Page::f(__('Users'), 0, esc_attr(Template\link('/world/users/')), '', 1);
	if(Globals::i()->WebUI->getAttachedAPI('MapAPI') !== null){
		$nav['World']['Map'] = Page::f(__('Map'), 0, esc_attr(Template\link('/map/')), '', 2);
	}

	apply_filters('main_nav_links', $nav)->sort();
	$htmlFilter = array(2);
	$htmlFilter[] = Globals::i()->loggedIn ? 1 : 0;
	if(Globals::i()->loggedInAsAdmin === true){
		$htmlFilter[] = 3;
	}

	echo wp_kses($nav->toHTML($htmlFilter), array('ul'=>array(), 'li'=>array(), 'a'=>array('href'=>array(), 'rel'=>array('nofollow'))), array('http', 'https'));
?>
	</nav>
</header>
<div id="main-content">
