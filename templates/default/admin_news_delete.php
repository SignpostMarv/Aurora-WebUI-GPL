<?php
use Aurora\Addon\WebUI\Template;
use Aurora\Addon\WebUI\Template\FormProblem;

$_GET['id'] = end(explode('/', Globals::i()->section));
if($_GET['id'] === 'delete'){
	unset($_GET['id']);
}

require_once('_header.php');
$FormProblems = FormProblem::i();
if(isset($_GET['id']) === false){
	header('HTTP/1.0 400 Bad Request');
?>
	<section>
		<h1><?php echo esc_html(__('Edit News')); ?></h1>
		<p class=problem><?php echo esc_html(__('No news ID specified.')); ?></p>
	</section>
<?php
	require_once('_footer.php');
	return;
}
try{
	$newsItem = Globals::i()->WebUI->GetGroupNotice(Template\unsquishUUID($_GET['id']));
}catch(Aurora\Addon\Exception $e){
	header('HTTP/1.0 400 Bad Request');
?>
	<section>
		<h1><?php echo esc_html(__('Edit News')); ?></h1>
		<p class=problem><?php echo esc_html($e->getMessage()); ?></p>
	</section>
<?php
	require_once('_footer.php');
	return;
}
if($_SERVER['REQUEST_METHOD'] === 'POST'){
	$trimMaybeRemove = array('id', 'title', 'message', 'nonce');
	foreach($trimMaybeRemove as $v){
		if(isset($_POST[$v]) === true){
			$_POST[$v] = trim($_POST[$v]);
			if($_POST[$v] === ''){
				unset($_POST[$v]);
			}
		}
	}
	if(isset($_POST['id']) === false){
			$FormProblems['id'] = __('Edit item ID was absent.');
	}else if($_POST['id'] !== Template\unsquishUUID($_GET['id'])){
		$FormProblems['id'] = __('Edit item ID, and display item ID did not match.');
	}else if(isset($_POST['nonce']) === false){
		$FormProblems['nonce'] = __('Nonce was absent');
	}else if(Globals::i()->Nonces->isValid($_POST['nonce']) === false){
		$FormProblems['nonce'] = __('Nonce has expired');
	}else{
		Globals::i()->Nonces->useNonce($_POST['nonce']);
		if(Globals::i()->WebUI->RemoveGroupNotice($newsItem->GroupID(), $newsItem->NoticeID()) === false){ ?>
		<p class=problem><?php echo esc_html(__('Failed to delete news item.')); ?></p>
<?php
		}else{
			header('Location: ' . SYSURL . Template\link('/admin/news/'));
			exit;
		}
	}
}
if(FormProblem::i()->count() > 0){
	header('HTTP/1.0 400 Bad Request');
}
?>
	<section>
		<form method=post>
			<fieldset>
				<legend><?php echo esc_html(__('Delete News')); ?></legend>
				<?php if(isset($FormProblems['id']) === true){ ?><p class=problem><?php echo esc_html($FormProblems['id']); ?></p><?php } ?><input type=hidden name=id value="<?php echo esc_attr($newsItem->NoticeID()); ?>">
				<?php if(isset($FormProblems['nonce']) === true){ ?><p class=problem><?php echo esc_html($FormProblems['nonce']); ?></p><?php } ?><input type=hidden name=nonce value="<?php echo esc_attr(Globals::i()->Nonces->get(300)); ?>">
				<article>
					<header>
						<h1><?php echo esc_html($newsItem->Subject()); ?></h1>
						<p><?php wp_kses(sprintf(__('Published: %s'), '<time pubdate="' . esc_attr(date('r', $newsItem->Timestamp())) . '">' . esc_html(date("l M d Y", $newsItem->Timestamp())) . '</time>'), array('time'=>array('pubdate'=>array()))); ?></p>
					</header>
					<p><?php echo wp_kses(nl2br($newsItem->Message()), array('br'=>array())); ?></p>
				</article>
			</fieldset>
			<button type=submit><?php echo esc_html(__('Delete this item.')); ?></button>
		</form>
	</section>
<?php
require_once('_footer.php');
?>
