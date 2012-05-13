<?php
header('HTTP/1.1 500 Internal Server Error');
require('_header-exception.php');
?>
	<section>
<?php
if(isset($e) && $e instanceof \Aurora\Addon\APIMethodException){
?>
		<hgroup>
			<h1><?php echo esc_html(__('API Method Exception')); ?></h1>
<?php
	if($e instanceof \Aurora\Addon\APIAccessForbiddenException){
?>
			<h2><?php echo esc_html(__('API Access Forbidden')); ?></h2>
		</hgroup>
		<p class=problem><?php echo esc_html(sprintf(__('Access to API method %s was forbidden'), $e->GetAPIMethod())); ?>.</p>
<?php
	}else if($e instanceof \Aurora\Addon\APIAccessRateLimitException){
?>
			<h2><?php echo esc_html(__('API Access Forbidden')); ?></h2>
		</hgroup>
		<p class=problem><?php echo esc_html(sprintf(__('Access to API method %s temporarily suspended due to reaching the hourly limit.'), $e->GetAPIMethod())); ?>.</p>
<?php
	}else{
?>

<?php
	}
}else{
?>
		<hgroup>
			<h1><?php echo esc_html(__('Internal Server Error')); ?></h1>
<?php
	if(isset($e)){
		if($e instanceof \Aurora\Addon\WebUI\Exception || $e instanceof \libAurora\Exception || $e instanceof \Aurora\Addon\APIAccessFailedException){
?>
			<h2><?php echo esc_html(__('Exception message')); ?></h2>
		</hgroup>
		<p class=problem><?php echo esc_html($e->getMessage()); ?></p>
<?php
		}else{
?>
		</hgroup>
		<p><?php echo esc_html(sprintf(__('There was an exception of type %1$s, code %2$u.'), get_class($e), $e->getCode())); ?></p>
<?php
		}
	}else{
?>
		<p class=problem><?php echo esc_html(__('We apologise for the inconvinience, it appears there was an unknown error. Please try again later.')); ?></p>
<?php
	}
}
?>
	</section>
<?php
require('_footer-exception.php');
?>
