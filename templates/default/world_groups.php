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

	$sortByName   = isset($_GET['sn']) ? (bool)$_GET['sn'] : null;
	$sortByFee    = isset($_GET['sf']) ? (bool)$_GET['sf'] : null;
	$sortByEnroll = isset($_GET['se']) ? (bool)$_GET['se'] : null;
	$sortByMature = isset($_GET['sm']) ? (bool)$_GET['sm'] : null;
	$sort         = array();
	if(isset($sortByName) === true){
		$sort['Name'] = $sortByName;
		$query['sn']  = (integer)$sortByName;
	}
	if(isset($sortByFee) === true){
		$sort['MembershipFee'] = $sortByFee;
		$query['sf']  = (integer)$sortByFee;
	}
	if(isset($sortByEnroll) === true){
		$sort['OpenEnrollment'] = $sortByEnroll;
		$query['se']  = (integer)$sortByEnroll;
	}
	if(isset($sortByMature) === true){
		$sort['MaturePublish'] = $sortByMature;
		$query['sm']  = (integer)$sortByMature;
	}

	$boolEnroll = isset($_GET['be']) ? (bool)$_GET['be'] : null;
//	$boolList   = isset($_GET['bl']) ? (bool)$_GET['bl'] : null;
	$boolList   = true;
	$boolPublic = isset($_GET['bp']) ? (bool)$_GET['bp'] : null;
	$boolMature = isset($_GET['bm']) ? (bool)$_GET['bm'] : null;
	$bool       = array();
	if(isset($boolEnroll) === true){
		$bool['OpenEnrollment'] = $boolEnroll;
		$query['be']  = (integer)$boolEnroll;
	}
	if(isset($boolList) === true){
		$bool['ShowInList'] = $boolList;
		$query['bl']  = (integer)$boolList;
	}
	if(isset($boolPublic) === true){
		$bool['AllowPublish'] = $boolPublic;
		$query['bp']  = (integer)$boolPublic;
	}
	if(isset($sortByMature) === true){
		$bool['MaturePublish'] = $boolMature;
		$query['bm']  = (integer)$sortByMature;
	}

	$start = (($_GET['page'] - 1) * $_GET['per']);
	try{
		$groups = Globals::i()->WebUI->GetGroups($start, $_GET['per'], $sort, $bool);
		if($start >= $groups->count()){
			require_once('404.php');
			return;
		}
	}catch(Aurora\Addon\WebUI\LengthException $e){
		require_once('404.php');
		return;
	}
	$last = (integer)ceil($groups->count() / $_GET['per']);
	require_once('_header.php');
?>
	<section>
		<h1><?php echo esc_html(__('Groups')); ?></h1>
		<nav>
<?php
	if($groups->count() > $_GET['per']){
?>
			<ol>
				<li><?php if($_GET['page'] > 1){ ?><a href="<?php echo esc_attr(Template\link('/world/groups/?' . http_build_query(array_merge($query, array('page'=>1, 'per'=>$_GET['per']))))); ?>"><?php echo esc_html(__('First')); ?></a><?php }else{ ?><?php echo esc_html(__('First')); } ?></li>
<?php	if((integer)ceil($groups->count() / $_GET['per']) == 2){ ?>
				<li><?php if($_GET['page'] !== 2){ ?><a href="<?php echo esc_attr(Template\link('/world/groups/?' . http_build_query(array_merge($query, array('page'=>2, 'per'=>$_GET['per']))))); ?>"><?php echo esc_html(__('Last')); ?></a><?php }else{ ?><?php echo esc_html(__('Last')); } ?></li>
<?php	}else{ ?>
<?php		if($_GET['page'] > 1){ ?>
				<li><a href="<?php echo esc_attr(Template\link('/world/groups/?' . http_build_query(array_merge($query, array('page'=>$_GET['page'] - 1, 'per'=>$_GET['per']))))); ?>" title="<?php echo esc_attr(__('Previous')); ?>"><?php echo esc_html(__('Prev')); ?></a></li>
<?php		} ?>
<?php		if($_GET['page'] < $last){ ?>
				<li><a href="<?php echo esc_attr(Template\link('/world/groups/?' . http_build_query(array_merge($query, array('page'=>$_GET['page'] + 1, 'per'=>$_GET['per']))))); ?>"><?php echo esc_html(__('Next')); ?></a></li>
<?php		} ?>
				<li><?php if($_GET['page'] !== $last){ ?><a href="<?php echo esc_attr(Template\link('/world/groups/?' . http_build_query(array_merge($query, array('page'=>$last, 'per'=>$_GET['per']))))); ?>"><?php echo esc_html(__('Last')); ?></a><?php 
}else{ ?><?php echo esc_html(__('Last')); } ?></li>
<?php	} ?>
<?php
	}
?>
				<li><?php if($_GET['per'] !== 10){  ?><a href="<?php echo esc_attr(Template\link('/world/groups/?' . http_build_query(array_merge($query, array('page'=>$_GET['page'], 'per'=>10 )))));  ?>">10</a><?php  }else{ ?>10<?php  } ?></li>
				<li><?php if($_GET['per'] !== 20){  ?><a href="<?php echo esc_attr(Template\link('/world/groups/?' . http_build_query(array_merge($query, array('page'=>$_GET['page'], 'per'=>20 )))));  ?>">20</a><?php  }else{ ?>20<?php  } ?></li>
				<li><?php if($_GET['per'] !== 50){  ?><a href="<?php echo esc_attr(Template\link('/world/groups/?' . http_build_query(array_merge($query, array('page'=>$_GET['page'], 'per'=>50 )))));  ?>">50</a><?php  }else{ ?>50<?php  } ?></li>
				<li><?php if($_GET['per'] !== 100){ ?><a href="<?php echo esc_attr(Template\link('/world/groups/?' . http_build_query(array_merge($query, array('page'=>$_GET['page'], 'per'=>100))))); ?>">100</a><?php }else{ ?>100<?php } ?></li>
			</ol>
		</nav>
		<table class=groups-list>
			<thead>
				<tr>
					<th class=group-name><a href="<?php echo esc_attr(Template\link('/world/groups/?' . http_build_query(array_merge($query, array('sn'=>!$sortByName))))); ?>"><?php echo esc_html(__('Group Name')); ?></a></th>
					<th class=group-fee title="<?php echo esc_html(__('Membership Fee')); ?>"><a href="<?php echo esc_attr(Template\link('/world/groups/?' . http_build_query(array_merge($query, isset($sortByFee) ? array('sf'=>!$sortByFee) : array('sf'=>0))))); ?>"><?php echo esc_html(__('Fee')); ?></a></th>
					<th class=group-enrollment><a href="<?php echo esc_attr(Template\link('/world/groups/?' . http_build_query(array_merge($query, isset($sortByEnroll) ? array('se'=>!$sortByEnroll) : array('se'=>0))))); ?>"><?php echo esc_html(__('Open Enrollment')); ?></a></th>
					<th class=group-mature><a href="<?php echo esc_attr(Template\link('/world/groups/?' . http_build_query(array_merge($query, isset($sortByMature) ? array('se'=>!$sortByMature) : array('se'=>0))))); ?>"><?php echo esc_html(__('Mature')); ?></a></th>
				</tr>
			</thead>
			<tbody>
<?php
	$i=0;
	foreach($groups as $group){ ?>
				<tr>
					<th scope=row><a href="<?php echo esc_attr(Template\link('/world/group/' . urlencode($group->GroupName()) . '/')); ?>"><?php echo esc_html($group->GroupName()); ?></a></th>
					<td><?php echo esc_html($group->MembershipFee()); ?></td>
					<td><?php echo esc_html(__($group->OpenEnrollment() ? 'Open' : 'Closed')); ?></td>
					<td><?php echo esc_html(__($group->MaturePublish() ? 'Mature' : 'PG')); ?></td>
				</tr>
<?php
		++$i;
		if($i >= $_GET['per']){
			break;
		}
	} ?>
			</tbody>
		</table>
	</section>
<?php
	require_once('_footer.php');
?>