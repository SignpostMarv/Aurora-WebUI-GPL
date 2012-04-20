<?php
	use Aurora\Addon\WebUI\Template;
	use Aurora\Addon\WebUI\Template\FormProblem;
	if(Globals::i()->loggedIn !== true){
		header('Location: ' . Globals::i()->baseURI . Template\link('/'));
		exit;
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

	$searchTerm = null;
	$regex = '^\s*[^0-9\s]+(\s+[^\s]+)*\s*$';
	$FormProblem = FormProblem::i();
	if($_SERVER['REQUEST_METHOD'] === 'POST'){
		if(isset($_POST['search']) === false){
			$FormProblem['search-users-username'] = __('No avatar name was specified');
		}
		
		if(preg_match('/' . $regex . '/', $_POST['search']) != 1){
			$FormProblem['search-users-username'] = __('Search term was invalid');
		}
		$searchTerm = trim($_POST['search']);
	}
	$results = Globals::i()->WebUI->FindUsers(isset($searchTerm) ? $searchTerm : '', $start, $_GET['per']);

	require_once('_header.php');
?>
	<section>
		<nav>
			<ol>
<?php
	if($results->count() > $_GET['per']){
?>
				<li><?php if($_GET['page'] > 1){ ?><a href="<?php echo esc_attr(Template\link('world/users/?' . http_build_query(array_merge($query, array('page'=>1, 'per'=>$_GET['per']))))); ?>"><?php echo esc_html(__('First')); ?></a><?php }else{ ?><?php echo esc_html(__('First')); } ?></li>
<?php	if((integer)ceil($results->count() / $_GET['per']) == 2){ ?>
				<li><?php if($_GET['page'] !== 2){ ?><a href="<?php echo esc_attr(Template\link('world/users/?' . http_build_query(array_merge($query, array('page'=>2, 'per'=>$_GET['per']))))); ?>"><?php echo esc_html(__('Last')); ?></a><?php }else{ ?><?php echo esc_html(__('Last')); } ?></li>
<?php	}else{ ?>
<?php		if($_GET['page'] > 1){ ?>
				<li><a href="<?php echo esc_attr(Template\link('world/users/?' . http_build_query(array_merge($query, array('page'=>$_GET['page'] - 1, 'per'=>$_GET['per']))))); ?>" title="<?php echo esc_attr(__('Previous')); ?>"><?php echo esc_html(__('Prev')); ?></a></li>
<?php		} ?>
<?php		if($_GET['page'] < $last){ ?>
				<li><a href="<?php echo esc_attr(Template\link('world/users/?' . http_build_query(array_merge($query, array('page'=>$_GET['page'] + 1, 'per'=>$_GET['per']))))); ?>"><?php echo esc_html(__('Next')); ?></a></li>
<?php		} ?>
				<li><?php if($_GET['page'] !== $last){ ?><a href="<?php echo esc_attr(Template\link('world/users/?' . http_build_query(array_merge($query, array('page'=>$last, 'per'=>$_GET['per']))))); ?>"><?php echo esc_html(__('Last')); ?></a><?php 
}else{ ?><?php echo esc_html(__('Last')); } ?></li>
<?php	} ?>
<?php
	}
?>
				<li><?php if($_GET['per'] !== 10){  ?><a href="<?php echo esc_attr(Template\link('world/users/?' . http_build_query(array_merge($query, array('page'=>$_GET['page'], 'per'=>10 )))));  ?>">10</a><?php  }else{ ?>10<?php  } ?></li>
				<li><?php if($_GET['per'] !== 20){  ?><a href="<?php echo esc_attr(Template\link('world/users/?' . http_build_query(array_merge($query, array('page'=>$_GET['page'], 'per'=>20 )))));  ?>">20</a><?php  }else{ ?>20<?php  } ?></li>
				<li><?php if($_GET['per'] !== 50){  ?><a href="<?php echo esc_attr(Template\link('world/users/?' . http_build_query(array_merge($query, array('page'=>$_GET['page'], 'per'=>50 )))));  ?>">50</a><?php  }else{ ?>50<?php  } ?></li>
				<li><?php if($_GET['per'] !== 100){ ?><a href="<?php echo esc_attr(Template\link('world/users/?' . http_build_query(array_merge($query, array('page'=>$_GET['page'], 'per'=>100))))); ?>">100</a><?php }else{ ?>100<?php } ?></li>
			</ol>
		</nav>
		<form method=post>
			<fieldset>
				<legend><?php echo esc_html(__('Search for a registered user')); ?></legend>
				<ul>
					<li>
<?php if(isset($FormProblem['search-users-username'])){?>
						<p class=problem><?php echo esc_html($FormProblem['search-users-username']); ?></p>
<?php	} ?>
						<label for=search-users-username><?php echo esc_html(__('Avatar Name')); ?>: </label><input type=search id=search-users-username name=search required pattern="<?php echo esc_attr($regex); ?>"  <?php if(isset($searchTerm) === true){ ?>value="<?php echo esc_attr($searchTerm); ?>" <?php } ?>></li>
<?php do_action('search-users-form'); ?>
					<li><button type=submit><?php echo esc_html(__('Search')); ?></button></li>
				</ul>
			</fieldset>
		</form>
<?php if($results->count() > 0){ ?>
		<ol>
<?php	for($i=$start;$i<($start + $_GET['per']) && $i < $results->count();++$i){
			$user = $results->current();
?>
			<li><a href="<?php echo esc_attr(Template\link('world/user/' . $user->Name())); ?>"><?php echo esc_html($user->Name()); ?></a></li>
<?php
			$results->next();
		} ?>
		</ol>
<?php } ?>
	</section>
<?php
	require_once('_footer.php');
?>