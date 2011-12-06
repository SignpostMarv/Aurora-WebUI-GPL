<!DOCTYPE html>
<html>
<head>
<title><?php echo esc_html(Globals::i()->WebUI->get_grid_info('gridname'));?></title>
<base href="<?php echo esc_attr(Globals::i()->baseURI); ?>">
<link rel="stylesheet" type="text/css" href="css/templates/default/style.css" />
<meta charset="UTF-8" />
<script></script>
<?php do_action('webui_head'); ?>
</head>
<body>
<header>
	<hgroup>
		<h1><?php echo esc_html(apply_filters('page_title', Globals::i()->WebUI->get_grid_info('gridname'))); ?></h1>
		<h2><?php echo esc_html(apply_filters('page_title', Globals::i()->WebUI->get_grid_info('gridnick'))); ?> - <?php echo esc_html(__('Powered by AuroraSim')); ?></h2>
	</hgroup>
<?php do_action('grid_selector'); ?>
</header>
<?php
	do_action('webui_footer');
?>
<footer>
	<p>&copy;2011<?php echo esc_html((date('Y') !== '2011') ? ' - ' . date('Y') : '');?> <?php echo wp_kses(implode(', ', array_unique(apply_filters('copyright',array(
		'<a href=http://signpostmarv.name/ zomg="this will be removed by wp_kses()" >SignpostMarv</a>'
	)))), array('a'=>array('href'=>array(), 'title'=>array())), array('http','https')); ?></p>
</footer>
</body>
</html>