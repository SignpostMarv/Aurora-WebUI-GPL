<?php
	use Aurora\Addon\WebUI\Template;
	$_GET['page'] = isset($_GET['page']) ? (integer)$_GET['page'] : 1;
	$_GET['per'] = isset($_GET['per']) ? (integer)$_GET['per'] : 10;

	if($_GET['page'] < 1){
		$_GET['page'] = 1;
	}
	if($_GET['per'] < 10){
		$_GET['per'] = 10;
	}

	$query = array();

	$start = (($_GET['page'] - 1) * $_GET['per']);
	try{
		$news = Globals::i()->WebUI->NewsFromGroupNotices($start, $_GET['per']);
		if($start >= $news->count()){
			require_once('404.php');
			return;
		}
	}catch(Aurora\Addon\WebUI\LengthException $e){
		require_once('404.php');
		return;
	}
	$last = (integer)ceil($news->count() / $_GET['per']);
	require_once('_header.php');
?>
	<section>
		<h1><?php echo esc_html(__('News')); ?></h1>
		<nav>
			<ol>
<?php
	if($news->count() > $_GET['per']){
?>
				<li><?php if($_GET['page'] > 1){ ?><a href="<?php echo esc_attr(Template\link('/news/?' . http_build_query(array_merge($query, array('page'=>1, 'per'=>$_GET['per']))))); ?>"><?php echo esc_html(__('First')); ?></a><?php }else{ ?><?php echo esc_html(__('First')); } ?></li>
<?php	if((integer)ceil($news->count() / $_GET['per']) == 2){ ?>
				<li><?php if($_GET['page'] !== 2){ ?><a href="<?php echo esc_attr(Template\link('/news/?' . http_build_query(array_merge($query, array('page'=>2, 'per'=>$_GET['per']))))); ?>"><?php echo esc_html(__('Last')); ?></a><?php }else{ ?><?php echo esc_html(__('Last')); } ?></li>
<?php	}else{ ?>
<?php		if($_GET['page'] > 1){ ?>
				<li><a href="<?php echo esc_attr(Template\link('/news/?' . http_build_query(array_merge($query, array('page'=>$_GET['page'] - 1, 'per'=>$_GET['per']))))); ?>" title="<?php echo esc_attr(__('Previous')); ?>"><?php echo esc_html(__('Prev')); ?></a></li>
<?php		} ?>
<?php		if($_GET['page'] < $last){ ?>
				<li><a href="<?php echo esc_attr(Template\link('/news/?' . http_build_query(array_merge($query, array('page'=>$_GET['page'] + 1, 'per'=>$_GET['per']))))); ?>"><?php echo esc_html(__('Next')); ?></a></li>
<?php		} ?>
				<li><?php if($_GET['page'] !== $last){ ?><a href="<?php echo esc_attr(Template\link('/news/?' . http_build_query(array_merge($query, array('page'=>$last, 'per'=>$_GET['per']))))); ?>"><?php echo esc_html(__('Last')); ?></a><?php 
}else{ ?><?php echo esc_html(__('Last')); } ?></li>
<?php	} ?>
<?php
	}
?>
				<li><?php if($_GET['per'] !== 10){  ?><a href="<?php echo esc_attr(Template\link('/news/?' . http_build_query(array_merge($query, array('page'=>$_GET['page'], 'per'=>10 )))));  ?>">10</a><?php  }else{ ?>10<?php  } ?></li>
				<li><?php if($_GET['per'] !== 20){  ?><a href="<?php echo esc_attr(Template\link('/news/?' . http_build_query(array_merge($query, array('page'=>$_GET['page'], 'per'=>20 )))));  ?>">20</a><?php  }else{ ?>20<?php  } ?></li>
				<li><?php if($_GET['per'] !== 50){  ?><a href="<?php echo esc_attr(Template\link('/news/?' . http_build_query(array_merge($query, array('page'=>$_GET['page'], 'per'=>50 )))));  ?>">50</a><?php  }else{ ?>50<?php  } ?></li>
				<li><?php if($_GET['per'] !== 100){ ?><a href="<?php echo esc_attr(Template\link('/news/?' . http_build_query(array_merge($query, array('page'=>$_GET['page'], 'per'=>100))))); ?>">100</a><?php }else{ ?>100<?php } ?></li>
			</ol>
		</nav>
		<ol class=hfeed>
<?php
	$i=0;
	foreach($news as $item){ ?>
				<li class=hentry id="<?php echo esc_attr('group-notice_' . $item->NoticeID()); ?>">
					<h2 class=entry-title><?php echo esc_html($item->Subject()); ?></h2>
					<abbr class=published title="<?php echo esc_attr(date('c', $item->Timestamp())); ?>"><?php echo esc_html(date(apply_filters('news_date_format', 'F h:ia'), $item->Timestamp())); ?></abbr>
					<p class=entry-content><?php echo wp_kses(nl2br($item->Message()), array('br'=>array())); ?></p>
					<ul class="vcard author">
						<li class=user><?php echo esc_html(__('Author')); ?>: <a class="url fn" href="<?php echo esc_attr(Template\link('/world/user/' . urlencode($item->FromName()))); ?>"><?php echo esc_html($item->FromName()); ?></a></li>
						<li class=org><?php echo esc_html(__('Group')); ?>: <a class="url fn" href="<?php echo esc_attr(Template\link('/world/group/' . urlencode(Globals::i()->WebUI->GetGroup($item->GroupID())->GroupName()))); ?>"><?php echo esc_html(Globals::i()->WebUI->GetGroup($item->GroupID())->GroupName()); ?></a></li>
					</ul>
				</li>
<?php
		++$i;
		if($i >= $_GET['per']){
			break;
		}
	} ?>
		</ol>
	</section>
<?php
	require_once('_footer.php');
?>