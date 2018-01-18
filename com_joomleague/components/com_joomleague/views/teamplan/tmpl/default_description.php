<?php
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

defined('_JEXEC') or die;
?>

<?php
$description = isset($this->projectteam->projectteam_notes) ? $this->projectteam->projectteam_notes : '';
if(trim($description  != ''))
{
	?>
	<br />
	<table width='100%' border='0' cellpadding='0' cellspacing='0'>
		<tr class='sectiontableheader'>
			<th><?php echo Text::_('COM_JOOMLEAGUE_TEAMPLAN_TEAMINFORMATION'); ?></th>
		</tr>
	</table>

	<table width='100%' border='0' cellpadding='0' cellspacing='0'>
		<tr>
			<td>
				<?php
				$description = HTMLHelper::_('content.prepare', $description);
				echo stripslashes($description);
				?>
			</td>
		</tr>
	</table>
<?php
}
?>
<br />