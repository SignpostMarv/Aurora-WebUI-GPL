<?php
	use Aurora\Addon\WebUI\Template;
	$pathParts = explode('/', Globals::i()->section);
	$group = false;
	$feed = null;
	if(count($pathParts) >= 4){
		if(in_array(end($pathParts), array('feed.atom')) !== false){
			$feed = substr(array_pop($pathParts), 5);
			reset($pathParts);
		}
		$groupName = urldecode(implode('/', array_slice($pathParts, 3)));
		$group = Globals::i()->WebUI->GetGroup($groupName);
		Globals::i()->sectionGroup = $group;
	}
	if(count($pathParts) === 2){
		header('Location: ' . Globals::i()->baseURI . Template\link('/world/groups/'));
		exit;
	}else if(count($pathParts) < 4 || ($group === false || $group->ShowInList() === false)){
		require_once('404.php');
		return;
	}
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
		$news = Globals::i()->WebUI->GroupNotices($start, $_GET['per'], array($group));
		if($start >= $news->count()){
			require_once('404.php');
			return;
		}
	}catch(Aurora\Addon\WebUI\LengthException $e){
		require_once('404.php');
		return;
	}
	$last = (integer)ceil($news->count() / $_GET['per']);
	if(isset($feed) === true){
		switch($feed){
			case 'atom':
				header('Content-Type: application/atom+xml');
			break;
		}
		do_action('group_notices', $news, $feed);
		return;
	}
	
	add_action('webui_head', function(){
		do_action('webui_head_group_notices', Globals::i()->sectionGroup, Globals::i()->WebUI);
	});
	add_action('webui_head_group_notices', function($group){
		echo '<link rel="alternate" title="', esc_attr(sprintf(__('Group Notices for %s'), $group->GroupName())), '" type="application/atom+xml" href="', esc_attr(Globals::i()->baseURI . Template\link('world/group/notices/' . urlencode($group->GroupName()) . '/feed.atom')), '">';
	}, 10, 2);
	require_once('_header.php');
?>
	<section>
		<h1><?php echo esc_html(__('Group Notices')); ?> - <a href="<?php echo esc_attr(Template\link('/world/group/' . urlencode($group->GroupName()))); ?>"><?php echo esc_html($group->GroupName()); ?></a></h1>
		<nav>
			<ol>
<?php
	if($news->count() > $_GET['per']){
?>
				<li><?php if($_GET['page'] > 1){ ?><a href="<?php echo esc_attr(Template\link('/world/group/notices/' . urlencode($group->GroupName()) . '/?' . http_build_query(array_merge($query, array('page'=>1, 'per'=>$_GET['per']))))); ?>"><?php echo esc_html(__('First')); ?></a><?php }else{ ?><?php echo esc_html(__('First')); } ?></li>
<?php	if((integer)ceil($news->count() / $_GET['per']) == 2){ ?>
				<li><?php if($_GET['page'] !== 2){ ?><a href="<?php echo esc_attr(Template\link('/world/group/notices/' . urlencode($group->GroupName()) . '/?' . http_build_query(array_merge($query, array('page'=>2, 'per'=>$_GET['per']))))); ?>"><?php echo esc_html(__('Last')); ?></a><?php }else{ ?><?php echo esc_html(__('Last')); } ?></li>
<?php	}else{ ?>
<?php		if($_GET['page'] > 1){ ?>
				<li><a href="<?php echo esc_attr(Template\link('/world/group/notices/' . urlencode($group->GroupName()) . '/?' . http_build_query(array_merge($query, array('page'=>$_GET['page'] - 1, 'per'=>$_GET['per']))))); ?>" title="<?php echo esc_attr(__('Previous')); ?>"><?php echo esc_html(__('Prev')); ?></a></li>
<?php		} ?>
<?php		if($_GET['page'] < $last){ ?>
				<li><a href="<?php echo esc_attr(Template\link('/world/group/notices/' . urlencode($group->GroupName()) . '/?' . http_build_query(array_merge($query, array('page'=>$_GET['page'] + 1, 'per'=>$_GET['per']))))); ?>"><?php echo esc_html(__('Next')); ?></a></li>
<?php		} ?>
				<li><?php if($_GET['page'] !== $last){ ?><a href="<?php echo esc_attr(Template\link('/world/group/notices/' . urlencode($group->GroupName()) . '/?' . http_build_query(array_merge($query, array('page'=>$last, 'per'=>$_GET['per']))))); ?>"><?php echo esc_html(__('Last')); ?></a><?php 
}else{ ?><?php echo esc_html(__('Last')); } ?></li>
<?php	} ?>
<?php
	}
?>
				<li><?php if($_GET['per'] !== 10){  ?><a href="<?php echo esc_attr(Template\link('/world/group/notices/' . urlencode($group->GroupName()) . '/?' . http_build_query(array_merge($query, array('page'=>$_GET['page'], 'per'=>10 )))));  ?>">10</a><?php  }else{ ?>10<?php  } ?></li>
				<li><?php if($_GET['per'] !== 20){  ?><a href="<?php echo esc_attr(Template\link('/world/group/notices/' . urlencode($group->GroupName()) . '/?' . http_build_query(array_merge($query, array('page'=>$_GET['page'], 'per'=>20 )))));  ?>">20</a><?php  }else{ ?>20<?php  } ?></li>
				<li><?php if($_GET['per'] !== 50){  ?><a href="<?php echo esc_attr(Template\link('/world/group/notices/' . urlencode($group->GroupName()) . '/?' . http_build_query(array_merge($query, array('page'=>$_GET['page'], 'per'=>50 )))));  ?>">50</a><?php  }else{ ?>50<?php  } ?></li>
				<li><?php if($_GET['per'] !== 100){ ?><a href="<?php echo esc_attr(Template\link('/world/group/notices/' . urlencode($group->GroupName()) . '/?' . http_build_query(array_merge($query, array('page'=>$_GET['page'], 'per'=>100))))); ?>">100</a><?php }else{ ?>100<?php } ?></li>
			</ol>
		</nav>
<?php	do_action('group_notices', $news, 'hAtom'); ?>
	</section>
<?php
	require_once('_footer.php');
?>