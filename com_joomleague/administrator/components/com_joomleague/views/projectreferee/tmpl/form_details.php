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
	<legend>
	<?php
		echo JText::sprintf('COM_JOOMLEAGUE_ADMIN_P_REF_DETAILS_TITLE',
		JoomleagueHelper::formatName(null,$this->item->firstname,$this->item->nickname,$this->item->lastname,0),$this->project->name);
	?>
	</legend>
	<div class="control-group">
		<div class="control-label"><?php echo JText::_('COM_JOOMLEAGUE_ADMIN_P_REF_DETAILS_STAND_REF_POS');?></div>
		<div class="controls"><?php echo $this->lists['refereepositions'];?></div>
	</div>
</fieldset>