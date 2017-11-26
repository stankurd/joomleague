<?php defined('_JEXEC') or die; ?>

<?php
	$description = isset($this->club->notes) ? $this->club->notes : '';
	if (trim($description != ''))
	{
		?>
	<div class='description'>
		<br />
		<table width='100%' border='0' cellpadding='0' cellspacing='0'>
			<tr class='sectiontableheader'>
				<td><?php echo '&nbsp;' . JText::_('COM_JOOMLEAGUE_CLUBINFO_CLUBINFORMATION'); ?> </td>
			</tr>
		</table>
		<table width='100%' border='0' cellpadding='0' cellspacing='0'>
			<tr>
				<td>
					<?php
					$description = JHtml::_('content.prepare', $description);
					echo stripslashes($description);
					?>
				</td>
			</tr>
		</table>
	</div>
	<?php
	}
?>
	<br />