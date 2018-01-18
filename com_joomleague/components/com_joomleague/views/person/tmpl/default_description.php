<?php

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

defined('_JEXEC') or die;
?>
<!-- Person description START -->
<?php
$description = $this->getDescription();
if ($description != ''): ?>
<h2><?php echo Text::_('COM_JOOMLEAGUE_PERSON_INFO'); ?></h2>
<table class='table'>
	<tr>
		<td><?php $description = HTMLHelper::_('content.prepare', $description); echo stripslashes($description); ?></td>
	</tr>
</table>
<?php endif; ?>
<!-- Person description END -->