<?php defined('_JEXEC') or die; ?>
<!-- Person description START -->
<?php
$description = $this->getDescription();
if ($description != ''): ?>
<h2><?php echo JText::_('COM_JOOMLEAGUE_PERSON_INFO'); ?></h2>
<table class='table'>
	<tr>
		<td><?php $description = JHtml::_('content.prepare', $description); echo stripslashes($description); ?></td>
	</tr>
</table>
<?php endif; ?>
<!-- Person description END -->