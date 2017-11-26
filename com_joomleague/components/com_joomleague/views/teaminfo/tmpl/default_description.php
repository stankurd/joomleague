<?php defined('_JEXEC') or die; ?>

<?php
$description = isset ($this->team->notes) ? $this->team->notes : '';
?>
<?php if (trim($description != '')): ?>
<div class='description'>
	<br />
	<table width='100%' border='0' cellpadding='0' cellspacing='0'>
		<tr class='sectiontableheader'>
			<td><?php echo '&nbsp;' . JText::_('COM_JOOMLEAGUE_TEAMINFO_TEAMINFORMATION'); ?></td>
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
<?php endif; ?>
<br />