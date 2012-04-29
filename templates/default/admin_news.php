<?php
use Aurora\Addon\WebUI\Template;

require_once('_header.php');
?>
	<section>
		<h1><?php echo esc_html(__('News Manager')); ?></h1>
<?php
$news = Globals::i()->WebUI->NewsFromGroupNotices(0,1);
if($news->count() >= 1){
?>
		<table summary="<?php echo esc_attr(__('You can edit or delete the news items that appear on the news section and login screen from this page.')); ?>">
			<thead>
				<tr>
					<th id=news-manager-title><?php echo esc_html(__('Title')); ?></th>
					<th id=news-manager-date><?php echo esc_html(__('Date')); ?></th>
					<th id=news-manager-action colspan=2><?php echo esc_html(__('Action')); ?></th>
				</tr>
			</thead>
			<tbody>
<?php
	foreach(Globals::i()->WebUI->NewsFromGroupNotices(0, $news->count()) as $newsItem){
?>
				<tr id="news_<?php echo esc_attr($newsItem->NoticeID()); ?>">
					<th scope=row headers=news-manager-title><?php echo esc_html($newsItem->Subject()); ?></th>
					<td headers=news-manager-date><time datetime="<?php echo esc_attr(date('r', $newsItem->Timestamp())); ?>"><?php echo esc_html(date("l M d Y", $newsItem->Timestamp())); ?></time></td>
					<td headers=news-manager-action><a href="<?php echo esc_attr(Template\link('/admin/news/edit/?id=' . Template\squishUUID($newsItem->NoticeID()))); ?>"><?php echo esc_html(__('Edit')); ?></a></td>
					<td headers=news-manager-action><a href="<?php echo esc_attr(Template\link('/admin/news/delete/?id=' . Template\squishUUID($newsItem->NoticeID()))); ?>"><?php echo esc_html(__('Delete')); ?></a></td>
				</tr>
<?php
	}
?>
			</tbody>
		</table>
<?php
}else{
?>
		<p class=problem><?php echo esc_html(__('There is no news.')); ?></p>
<?php
}
?>
	</section>
<?php
require_once('_footer.php');
?>