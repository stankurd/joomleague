<?php
/**
 * Joomleague
 *
 * @copyright	Copyright (C) 2006-2015 joomleague.at. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @link		http://www.joomleague.at
 */
defined('_JEXEC') or die;
JHtml::_('behavior.tooltip');
?>
<form method="post" name="adminForm" id="generatenode" class="">
	<div>
		<fieldset class="adminform">
			<legend><?php echo JText::_('COM_JOOMLEAGUE_ADMIN_TREETOS_TITLE_GENERATENODE'); ?></legend>
			<ul class="adminformlist">
			<?php foreach($this->form->getFieldset('generate') as $field): ?>
				<li><?php echo $field->label;echo $field->input;?></li>
			<?php endforeach; ?>
				<li><input type="submit"
					value="<?php echo JText::_('COM_JOOMLEAGUE_ADMIN_TREETO_GENERATE'); ?>" />
				</li>
			</ul>
		</fieldset>
	</div>

	<input type="hidden" name="project_id"
		value='<?php echo $this->project->id; ?>' /> <input type="hidden"
		name="id" value='<?php echo $this->item->id; ?>' /> <input
		type="hidden" name="task" value="treeto.generatenode" />
	<?php echo JHtml::_('form.token'); ?>
</form>

