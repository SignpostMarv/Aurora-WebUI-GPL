<?php
use Aurora\Addon\WebUI\Template;
use Aurora\Addon\WebUI\Template\FormProblem;

$_GET['id'] = end(explode('/', Globals::i()->section));
if($_GET['id'] === 'edit'){
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
	if(isset($_POST['id'], $_POST['title'], $_POST['message']) === false){
		if(isset($_POST['id']) === false){
			$FormProblems['id'] = __('Edit item ID was absent.');
		}
		if(isset($_POST['title']) === false){
			$FormProblems['edit-news-title'] = __('Edit item title was absent.');
		}
		if(isset($_POST['message']) === false){
			$FormProblems['edit-news-message'] = __('Edit item message was absent.');
		}
	}else if($_POST['id'] !== Template\unsquishUUID($_GET['id'])){
		$FormProblems['id'] = __('Edit item ID, and display item ID did not match.');
	}else if(isset($_POST['nonce']) === false){
		$FormProblems['nonce'] = __('Nonce was absent');
	}else if(Globals::i()->Nonces->isValid($_POST['nonce']) === false){
		$FormProblems['nonce'] = __('Nonce has expired');
	}else{
		Globals::i()->Nonces->useNonce($_POST['nonce']);
		if(Globals::i()->WebUI->EditGroupNotice($_POST['id'], $_POST['title'], $_POST['message']) === false){ ?>
		<p class=problem><?php echo esc_html(__('Failed to update news item.')); ?></p>
<?php
		}else{
			header('Location: ' . SYSURL . Template\link('/admin/news/#news_' . $_POST['id']));
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
				<legend><?php echo esc_html(__('Edit News')); ?></legend>
				<?php if(isset($FormProblems['id']) === true){ ?><p class=problem><?php echo esc_html($FormProblems['id']); ?></p><?php } ?><input type=hidden name=id value="<?php echo esc_attr($newsItem->NoticeID()); ?>">
				<?php if(isset($FormProblems['nonce']) === true){ ?><p class=problem><?php echo esc_html($FormProblems['nonce']); ?></p><?php } ?><input type=hidden name=nonce value="<?php echo esc_attr(Globals::i()->Nonces->get(300)); ?>">
				<ol>
					<li><label for=edit-news-title><?php echo esc_html(__('Title')); ?>: </label><?php if(isset($FormProblems['edit-news-title']) === true){ ?><p class=problem><?php echo esc_html($FormProblems['edit-news-title']); ?></p><?php } ?><input id=edit-news-title name=title value="<?php echo esc_attr($newsItem->Subject()); ?>"></li>
					<li><label for=edit-news-message><?php echo esc_html(__('Message')); ?>: </label><?php if(isset($FormProblems['edit-news-message']) === true){ ?><p class=problem><?php echo esc_html($FormProblems['edit-news-message']); ?></p><?php } ?><textarea id=edit-news-message name=message ><?php echo esc_html($newsItem->Message()); ?></textarea></li>
				</ol>
			</fieldset>
			<button type=submit ><?php echo esc_html(__('Submit')); ?></button>
		</form>
	</section>
<?php
require_once('_footer.php');
?>
