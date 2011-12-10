<?php
	header('HTTP/1.1 404 Not Found');
	require_once('_header.php');
?>
	<section>
		<h1><?php echo esc_html(__('Not Found')); ?></h1>
		<p><?php echo esc_html(__('The file you requested was not found.')); ?></p>
	</section>
<?php
	require_once('_footer.php');
?>