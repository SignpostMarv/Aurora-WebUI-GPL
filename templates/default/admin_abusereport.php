<?php
use Aurora\Addon\WebUI\Template;
use Aurora\Addon\WebUI\Template\FormProblem;

require_once('_header.php');
$FormProblems = FormProblem::i();

$_GET['active'] = isset($_GET['active']) ? $_GET['active'] : '1';

if(isset($_GET['active']) === true && $_GET['active'] !== '1'){
	unset($_GET['active']);
}

$AbuseReports = Globals::i()->WebUI->GetAbuseReports(0,1, isset($_GET['active']));
?>
	<section>
		<h1><?php echo esc_html(__(isset($_GET['active']) ? 'Abuse Reports' : 'Inactive Abuse Reports')); ?></h1>
		<p><a href="<?php echo esc_attr(Template\link('/admin/abusereport/?active=' . (isset($_GET['active']) ? '0' : '1'))); ?>"><?php echo esc_html(__(isset($_GET['active']) ? 'View inactive abuse reports.' : 'View active abuse reports.')); ?></a></p>
<?php if($AbuseReports->count() < 1){ ?>
		<p class=problem><?php echo esc_html(__(isset($_GET['active']) ? 'No active abuse reports.' : 'No inactive abuse reports.')); ?></p>
<?php }else{ ?>
		<table summary="<?php echo esc_attr(__('You can process abuse report tickets from this page.')); ?>">
			<caption><?php echo esc_html(sprintf(__(isset($_GET['active']) ? 'There are %u active abuse reports.' : 'There are %u inactive abuse reports.'), $AbuseReports->count())); ?></caption>
			<thead>
				<tr>
					<th id=abuse-report-from><?php echo esc_html(__('From')); ?></th>
					<th id=abuse-report-category><?php echo esc_html(__('Category')); ?></th>
					<th id=abuse-report-summary><?php echo esc_html(__('Summary')); ?></th>
					<th id=abuse-report-action><?php echo esc_html(__('Action')); ?></th>
				</tr>
			</thead>
			<tbody>
<?php
		foreach($AbuseReports as $ar){
?>
				<tr id="ar_<?php echo esc_attr($ar->Number()); ?>">
					<td headers=abuse-report-from><a href="<?php echo esc_attr(Template\link('/world/user/' . rawurlencode($ar->ReporterName()))); ?>"><?php echo esc_html($ar->ReporterName()); ?></a></td>
					<td headers=abuse-report-category><?php echo esc_html(__($ar->Category())); ?></td>
					<td headers=abuse-report-summary><?php echo esc_html($ar->Summary()); ?></td>
					<td headers=abuse-report-action><a href="<?php echo esc_attr(Template\link('/admin/abusereport/review/' . rawurlencode($ar->Number()))); ?>"><?php echo esc_html(__('Review')); ?></a></td>
				</tr>
<?php
		}
?>
			</tbody>
		</table>
<?php } ?>
	</section>
<?php
require_once('_footer.php');
?>
