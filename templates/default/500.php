<?php
	header('HTTP/1.1 500 Internal Server Error');
	require('_header-exception.php');
?>
	<section>
		<h1><?php echo esc_html(__('Internal Server Error')); ?></h1>
<?php if(isset($e)){
		if($e instanceof Exception){ ?>
		<p><?php echo esc_html(sprintf(__('There was an exception of type %1$s, code %2$u.'), get_class($e), $e->getCode())); ?></p>
<?php	}
		if($e instanceof Aurora\Addon\WebUI\Exception){ ?>
		<h2><?php echo esc_html(__('Exception message')); ?></h2>
		<p><?php echo esc_html($e->getMessage()); ?></p>
<?php	}
	}else{ ?>
		<p><?php echo esc_html(__('We apologise for the inconvinience, it appears there was an unknown error. Please try again later.')); ?></p>
<?php } ?>
	</section>
<?php
	require('_footer.php');
?>