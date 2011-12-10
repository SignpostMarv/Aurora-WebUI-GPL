<?php
	use Aurora\Addon\WebUI\Template;

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
<body class="<?php echo esc_attr(str_replace(array('/',' '),array('-','_'), Globals::i()->section)); ?>">
<header>
	<hgroup>
		<h1><?php echo esc_html(__('OH NOES!')); ?></h1>
		<h2><?php echo esc_html(__('An Exception occurred')); ?></h2>
	</hgroup>
	<nav id="main-nav">
		<ul>
			<?php echo wp_kses(apply_filters('main_nav_links', 
				'<li><a href="' . esc_attr(Template\link('/')) . '">' . esc_html(__('Home')) . '</a></li>'
			), array('li'=>array(), 'ul'=>array(), 'a'=>array('href'=>array(), 'rel'=>array('nofollow'))), array('http', 'https')); ?>
		</ul>
	</nav>
</header>
<div id="main-content">
