<?php
/**
 * Joomleague
 *
 * @copyright	Copyright (C) 2006-2015 joomleague.at. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @link		http://www.joomleague.at
 */
defined('_JEXEC') or die;
?>

<fieldset class="form-horizontal">
	<legend><?php echo JText::_('COM_JOOMLEAGUE_ADMIN_DIVISION');?></legend>
	<?php
	echo $this->form->renderField('name');
	echo $this->form->renderField('alias');
	echo $this->form->renderField('middle_name');
	echo $this->form->renderField('shortname');
	echo $this->form->renderField('id');
	?>
	<div class="control-group">
		<div class="control-label"><?php echo JText::_('COM_JOOMLEAGUE_ADMIN_DIVISION_PARENT');?></div>
		<div class="controls"><?php echo $this->lists['parents'];?></div>
	</div>
</fieldset>