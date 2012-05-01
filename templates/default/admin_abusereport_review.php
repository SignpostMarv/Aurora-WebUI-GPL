<?php
use OpenMetaverse\Vector3;

use Aurora\Addon\WebUI\Template;

require_once('_header.php');
$id = end(explode('/', Globals::i()->section));
?>
	<section>
		<h1><?php echo esc_html(__('Review Abuse Report')); ?></h1>
<?php
if(ctype_digit($id) === false){
	header('HTTP/1.0 400 Bad Request');
?>
		<p class=problem><?php echo esc_html(__('No valid Abuse Report ID specified.')); ?></p>
	</section>
<?php
}else{
	$id = (integer)$id;

	$dirty = false;

	try{
		$AbuseReport = Globals::i()->WebUI->GetAbuseReport($id);
	}catch(\Aurora\Addon\Exception $e){
		header('HTTP/1.0 500 Internal Server Error');
?>
		<p class=problem><?php echo esc_html(__('Failed to get abuse report.')); ?><br><?php echo wp_kses(sprintf(__('An <em>%s</em> exception was thrown'), get_class($e)), array('em'=>array()))?>: <q><?php echo esc_html($e->getMessage()); ?></q></p>
	</section>
<?php
		require_once('_footer.php');
		return;
	}

	if($_SERVER['REQUEST_METHOD'] === 'POST'){
		if(isset($_POST['mark-complete']) === true){
			Globals::i()->WebUI->AbuseReportMarkComplete($AbuseReport->Number());
			$dirty = true;
		}else if(isset($_POST['notes']) === true){
			Globals::i()->WebUI->AbuseReportSaveNotes($AbuseReport->Number(), $_POST['notes']);
			$dirty = true;
		}
	}

	if($dirty === true){
		try{
			$AbuseReport = Globals::i()->WebUI->GetAbuseReport($AbuseReport->Number());
		}catch(\Aurora\Addon\Exception $e){
			header('HTTP/1.0 500 Internal Server Error');
?>
		<p class=problem><?php echo esc_html(__('Failed to get abuse report.')); ?><br><?php echo wp_kses(sprintf(__('An <em>%s</em> exception was thrown'), get_class($e)), array('em'=>array()))?>: <q><?php echo esc_html($e->getMessage()); ?></q></p>
	</section>
<?php
			require_once('_footer.php');
			return;
		}
	}

	$pos = new Vector3($AbuseReport->ObjectPosition());

	if($AbuseReport->Active() === false){
?>
		<p><?php echo esc_html(__('This abuse report has been closed.')); ?></p>
<?php
	}
?>
		<details>
			<summary><?php echo wp_kses(sprintf('%s: %s said <q>%s</q> near %s',
				__($AbuseReport->Category()),
				'<a href="' . esc_attr(Template\link('/world/user/' . rawurlencode($AbuseReport->ReporterName()) . '/')) . '">' . esc_html($AbuseReport->ReporterName()) . '</a>',
				$AbuseReport->Summary(),
				'<a href="secondlife:///' . esc_attr(rawurlencode($AbuseReport->RegionName()) . '/' . rawurlencode($pos->X()) . '/' . rawurlencode($pos->Y()) . '/' . rawurlencode($pos->Z()) . '/') . '">' . esc_html($AbuseReport->RegionName()) . '</a> ' . esc_html($AbuseReport->ObjectPosition())
			), array('a'=>array('href'=>array()), 'q'=>array()), array('http','https','secondlife')); ?></summary>
			<blockquote><p><?php echo wp_kses(nl2br($AbuseReport->Details()), array('br'=>array())); ?></p></blockquote>
			<table>
				<tbody>
					<tr>
						<th id="abusereport_category_<?php echo esc_attr($AbuseReport->Number()); ?>" scope=row><?php echo esc_html(__('Category')); ?></th>
						<td headers="abusereport_category_<?php echo esc_attr($AbuseReport->Number()); ?>"><?php echo esc_html(__($AbuseReport->Category())); ?></td>
					</tr>
					<tr>
						<th id="abusereport_reportername_<?php echo esc_attr($AbuseReport->Number()); ?>" scope=row><?php echo esc_html(__('Reporter Name')); ?></th>
						<td headers="abusereport_reportername_<?php echo esc_attr($AbuseReport->Number()); ?>"><a href="<?php echo esc_attr(Template\link('/world/user/' . rawurlencode($AbuseReport->ReporterName()) . '/')); ?>"><?php echo esc_html($AbuseReport->ReporterName()); ?></a></td>
					</tr>
<?php	if(trim($AbuseReport->ObjectName()) !== ''){ ?>
					<tr>
						<th id="abusereport_objectname_<?php echo esc_attr($AbuseReport->Number()); ?>" scope=row><?php echo esc_html(__('Object Name')); ?></th>
						<td headers="abusereport_objectname_<?php echo esc_attr($AbuseReport->Number()); ?>"><?php echo esc_html(trim($AbuseReport->ObjectName())); ?></td>
					</tr>
					<tr>
						<th id="abusereport_objectpos_<?php echo esc_attr($AbuseReport->Number()); ?>" scope=row><?php echo esc_html(__('Object Position')); ?></th>
						<td headers="abusereport_objectpos_<?php echo esc_attr($AbuseReport->Number()); ?>"><a href="secondlife:///<?php echo esc_attr(rawurlencode($AbuseReport->RegionName()) . '/' . rawurlencode($pos->X()) . '/' . rawurlencode($pos->Y()) . '/' . rawurlencode($pos->Z()) . '/'); ?>"><?php echo esc_html($AbuseReport->RegionName()); ?></a> <?php echo esc_html($AbuseReport->ObjectPosition()); ?></td>
<?php	} ?>
<?php	if(trim($AbuseReport->UserName()) !== ''){ ?>
					<tr>
						<th id="abusereport_abusername_<?php echo esc_attr($AbuseReport->Number()); ?>" scope=row><?php echo esc_html(__('Abuser Name')); ?></th>
						<td headers="abusereport_abusername_<?php echo esc_attr($AbuseReport->Number()); ?>"><a href="<?php echo esc_attr(Template\link('/world/user/' . rawurlencode($AbuseReport->UserName()) . '/')); ?>"><?php echo esc_html($AbuseReport->UserName()); ?></a></td>
					</tr>
<?php	} ?>
				</tbody>
			</table>
			<form method=post>
				<fieldset>
					<?php if(isset($FormProblems['nonce']) === true){ ?><p class=problem><?php echo esc_html($FormProblems['nonce']); ?></p><?php } ?><input type=hidden name=nonce value="<?php echo esc_attr(Globals::i()->Nonces->get(300)); ?>">
					<ol>
						<li><?php if(isset($FormProblems['notes']) === true){ ?><p class=problem><?php echo esc_html($FormProblems['notes']); ?></p><?php } ?><label for="abusereport_notes_<?php echo esc_attr($AbuseReport->Number()); ?>"><?php echo esc_html(__('Notes')); ?></legend><textarea id="abusereport_notes_<?php echo esc_attr($AbuseReport->Number()); ?>" name=notes><?php echo esc_html($AbuseReport->Notes()); ?></textarea></li>
					</ol>
				</fieldset>
				<ul>
					<li><button type=submit><?php echo esc_html(__('Save Notes')); ?></button></li>
<?php	if($AbuseReport->Active()){ ?>
					<li><button type=submit name=mark-complete><?php echo esc_html(__('Mark abuse report as closed')); ?></button></li>
<?php	} ?>
				</ul>
			</form>
		</details>
	</section>
<?php
}
require_once('_footer.php');
?>