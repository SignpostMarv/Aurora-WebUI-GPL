</div>
<?php
	do_action('webui_footer');
?>
<footer id=copyright>
	<p>&copy;2011<?php echo esc_html((date('Y') !== '2011') ? ' - ' . date('Y') : '');?> <?php echo wp_kses(implode(', ', array_unique(apply_filters('copyright',array(
		'<a href=http://signpostmarv.name/ zomg="this will be removed by wp_kses()" >SignpostMarv</a>'
	)))), array('a'=>array('href'=>array(), 'title'=>array())), array('http','https')); ?></p>
</footer>
</body>
</html>
