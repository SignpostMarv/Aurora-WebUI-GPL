<?php
	use Aurora\Addon\WebUI\Template;

	use libAurora\Template\navigation\Page;
	use libAurora\Template\navigation\Pages;

	header('Content-Type: text/html');
?><!DOCTYPE html>
<html>
<head>
<title><?php echo esc_html('Exception occurred');?></title>
<base href="<?php echo esc_attr(Globals::i()->baseURI); ?>">
<link rel="stylesheet" type="text/css" href="css/templates/default/style.css" />
<meta charset="UTF-8" />
<script></script>
<?php do_action('webui_head'); ?>
</head>
<body class="<?php echo esc_attr(implode(' ', array_merge(array(str_replace(array('/',' '),array('-','_'), Globals::i()->sectionFile)),array_unique(apply_filters('body_class', array()))))); ?>">
<header>
	<hgroup>
		<h1><?php echo esc_html(__('OH NOES!')); ?></h1>
		<h2><?php echo esc_html(__('An Exception occurred')); ?></h2>
	</hgroup>
	<nav id="main-nav">
<?php
	$nav = Pages::f();
	$nav['Home']  = Page::f(__('Home'), -9999, esc_attr(Template\link('/')), '', 2);
	echo wp_kses($nav->toHTML(), array('ul'=>array(), 'li'=>array(), 'a'=>array('href'=>array(), 'rel'=>array('nofollow'))), array('http', 'https'));
?>
	</nav>
</header>
<div id="main-content">
