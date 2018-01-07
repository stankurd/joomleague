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
<fieldset class="batch">
	<legend><?php echo Text::_('COM_JOOMLEAGUE_ADMIN_PROJECTS_BATCH_OPTIONS');?></legend>
	<p><?php echo Text::_('COM_JOOMLEAGUE_ADMIN_PROJECTS_BATCH_TIP'); ?></p>

	<fieldset id="batch-fix-game-dates-action" class="combo">
		<label id="batch-fix-game-dates-lbl" for="batch-category-id">
		<?php echo Text::_('COM_JOOMLEAGUE_ADMIN_PROJECTS_BATCH_FIX_DATES_LABEL'); ?>
	</label>
		<button type="submit" class="btn"
			onclick="submitbutton('projects.fixdates');">
		<?php echo Text::_('COM_JOOMLEAGUE_ADMIN_PROJECTS_BATCH_FIX_DATES_BUTTON'); ?>
	</button>
	</fieldset>
</fieldset>