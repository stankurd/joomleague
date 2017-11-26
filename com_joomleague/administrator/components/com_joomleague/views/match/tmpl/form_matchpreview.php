<?php
/**
 * Joomleague
 *
 * @copyright	Copyright (C) 2006-2015 joomleague.at. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @link		http://www.joomleague.at
 */
defined ( '_JEXEC' ) or die ();
?>
<fieldset class="form-vertical">
	<legend><?php echo JText::_('COM_JOOMLEAGUE_ADMIN_MATCH_F_MP'); ?></legend>
	<div class="control-group">
		<div class="control-label"><?php echo $this->form->getLabel('preview'); ?></div>
		<div class="controls"><?php echo $this->form->getInput('preview'); ?></div>
	</div>
</fieldset>
