<?php
	use Aurora\Addon\WebUI\Template;

	add_filter('htmlClass', function(array $classNames){
		return array_merge($classNames, array(Globals::i()->sectionFile));
	});
	add_action('webui_footer', function(){
		try{
			$news = Globals::i()->WebUI->NewsFromGroupNotices(0, 5);
			if($news->count() <= 0){
				return;
			}
		}catch(Aurora\Addon\WebUI\LengthException $e){
			return;
		}
?>
		<footer id=news>
			<ol>
<?php
		$i = 0;
		foreach($news as $item){
			if($i++ >= 5){
				break;
			}
?>
				<li><a href="<?php echo esc_attr(Template\link('/news/' . Template\squishUUID($item->NoticeID()))); ?>"><?php echo esc_html($item->Subject()); ?></a> <abbr title="<?php echo esc_attr(date('c', $item->Timestamp())); ?>"><?php echo esc_html(date(apply_filters('loginscreen_news_date_format', 'D d M g:iA'), $item->Timestamp())); ?></abbr></li>
<?php
		}
?>
			</ol>
		</footer>
<?php
	});

	require_once('_header.php');
	
	$serverStatusTable = apply_filters('serverStatusTable',array(
		__('Grid Status') => array('grid-status' ,'<td class=' . (Globals::i()->WebUI->OnlineStatus()->Online() ? 'on' : 'off') . 'line>' . __(Globals::i()->WebUI->OnlineStatus()->Online() ? 'Online' : 'Offline') . '</td>'),
		__('Logins')      => array('login-status','<td class=' . (Globals::i()->WebUI->OnlineStatus()->LoginEnabled() ? 'en' : 'dis') . 'abled>' . __(Globals::i()->WebUI->OnlineStatus()->LoginEnabled() ? 'Enabled' : 'Disabled') . '</td>')
	));
?>
	<section id=server-status>
		<table>
<?php	foreach($serverStatusTable as $k=>$v){
			list($rowClass, $content) = $v;
?>
			<tr class="<?php echo esc_attr($rowClass); ?>"><th><?php echo esc_html($k); ?></th><?php echo wp_kses($content, array('td'=>array('class'=>array()))); ?></tr>
<?php	} ?>
		</table>
	</section>
<?php
	require_once('_footer.php');
?>