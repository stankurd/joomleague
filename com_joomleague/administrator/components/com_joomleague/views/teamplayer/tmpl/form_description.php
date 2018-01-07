<?php
/**
 * Joomleague
 *
 * @copyright	Copyright (C) 2006-2015 joomleague.at. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @link		http://www.joomleague.at
 */
use Joomla\CMS\Language\Text;

defined('_JEXEC') or die;
?>
<fieldset class="form-vertical">
	<legend>
	<?php
	echo Text::sprintf('COM_JOOMLEAGUE_ADMIN_TEAMPLAYER_DESCR_TITLE',
			JoomleagueHelper::formatName(null,$this->item->firstname,$this->item->nickname,$this->item->lastname,0),
			'<i>' . $this->projectteam->name . '</i>','<i>' . $this->project->name . '</i>');
	?>
	</legend>
	<?php foreach ($this->form->getFieldset('description') as $field): ?>
	<div class="control-group">
		<div class="control-label"><?php echo $field->label; ?></div>
		<div class="controls"><?php echo $field->input; ?></div>
	</div>
	<?php endforeach; ?>
</fieldset>