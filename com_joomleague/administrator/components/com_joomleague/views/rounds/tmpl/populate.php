<?php
/**
 * Joomleague
 *
 * @copyright	Copyright (C) 2006-2015 joomleague.at. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @link		http://www.joomleague.at
 */
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

defined('_JEXEC') or die;

HTMLHelper::_('behavior.tooltip');
?>
<form method="post" id="adminForm" name="adminForm" action="<?php echo $this->request_url; ?>">
	<fieldset class="adminform">
	<?php echo Text::_('COM_JOOMLEAGUE_ADMIN_ROUNDS_POPULATE_DESC'); ?>
	<legend><?php echo Text::sprintf('COM_JOOMLEAGUE_ADMIN_ROUNDS_POPULATE_LEGEND','<i>'.$this->project->name.'</i>'); ?></legend>
		<?php echo HTMLHelper::_('form.token'); ?>
		<table class='admintable'>
			<tbody>

				<tr>
					<td nowrap='nowrap' class="key hasTip"
						title="<?php echo Text::_('COM_JOOMLEAGUE_ADMIN_ROUNDS_POPULATE_TYPE_LABEL').'::'.Text::_('COM_JOOMLEAGUE_ADMIN_ROUNDS_POPULATE_TYPE_TIP'); ?>">
						<label for="scheduling"><?php echo Text::_('COM_JOOMLEAGUE_ADMIN_ROUNDS_POPULATE_TYPE_LABEL'); ?></label>
					</td>
					<td><?php echo $this->lists['scheduling']; ?></td>
				</tr>

				<tr>
					<td nowrap='nowrap' class="key hasTip"
						title="<?php echo Text::_('COM_JOOMLEAGUE_ADMIN_ROUNDS_POPULATE_STARTTIME_LABEL').'::'.Text::_('COM_JOOMLEAGUE_ADMIN_ROUNDS_POPULATE_STARTTIME_TIP'); ?>">
						<label for="time"><?php echo Text::_('COM_JOOMLEAGUE_ADMIN_ROUNDS_POPULATE_STARTTIME_LABEL'); ?></label>
					</td>
					<td><input type="text" name="time" value="20:00" /></td>
				</tr>

				<tr>
					<td nowrap='nowrap' class="key hasTip"
						title="<?php echo Text::_('COM_JOOMLEAGUE_ADMIN_ROUNDS_POPULATE_MATCHNUMBER_LABEL').'::'.Text::_('COM_JOOMLEAGUE_ADMIN_ROUNDS_POPULATE_MATCHNUMBER_TIP'); ?>">
						<label for="time"><?php echo Text::_('COM_JOOMLEAGUE_ADMIN_ROUNDS_POPULATE_MATCHNUMBER_LABEL'); ?></label>
					</td>
					<td><input type="text" name="matchnumber" value="" /></td>
				</tr>

				<tr>
					<td nowrap='nowrap' class="key hasTip"
						title="<?php echo Text::_('COM_JOOMLEAGUE_ADMIN_ROUNDS_POPULATE_ROUNDS_INTERVAL_LABEL').'::'.Text::_('COM_JOOMLEAGUE_ADMIN_ROUNDS_POPULATE_ROUNDS_INTERVAL_TIP'); ?>">
						<label for="interval"><?php echo Text::_('COM_JOOMLEAGUE_ADMIN_ROUNDS_POPULATE_ROUNDS_INTERVAL_LABEL'); ?></label>
					</td>
					<td><input type="text" name="interval" value="7" /></td>
				</tr>

				<tr>
					<td nowrap='nowrap' class="key hasTip"
						title="<?php echo Text::_('COM_JOOMLEAGUE_ADMIN_ROUNDS_POPULATE_STARTDATE_LABEL').'::'.Text::_('COM_JOOMLEAGUE_ADMIN_ROUNDS_POPULATE_STARTDATE_TIP'); ?>">
						<label for="start"><?php echo Text::_('COM_JOOMLEAGUE_ADMIN_ROUNDS_POPULATE_STARTDATE_LABEL'); ?></label>
					</td>
					<td><?php echo HTMLHelper::calendar(strftime('%Y-%m-%d'), 'start', 'start', '%Y-%m-%d'); ?></td>
				</tr>

				<tr>
					<td nowrap='nowrap' class="key hasTip"
						title="<?php echo Text::_('COM_JOOMLEAGUE_ADMIN_ROUNDS_POPULATE_NEW_ROUND_NAME_LABEL').'::'.Text::_('COM_JOOMLEAGUE_ADMIN_ROUNDS_POPULATE_NEW_ROUND_NAME_TIP'); ?>">
						<label for="roundname"><?php echo Text::_('COM_JOOMLEAGUE_ADMIN_ROUNDS_POPULATE_NEW_ROUND_NAME_LABEL'); ?></label>
					</td>
					<td><input type="text" name="roundname"
						value="<?php echo Text::_('COM_JOOMLEAGUE_ADMIN_ROUNDS_POPULATE_NEW_ROUND_NAME'); ?>" /></td>
				</tr>
		<tr id="sortable_teams">
			<td nowrap='nowrap' class="key hasTip" title="<?php echo Text::_('COM_JOOMLEAGUE_ADMIN_ROUNDS_POPULATE_TEAMS_ORDER_LABEL').'::'.Text::_('COM_JOOMLEAGUE_ADMIN_ROUNDS_POPULATE_TEAMS_ORDER_TIP'); ?>">
				<label for="roundname"><?php echo Text::_('COM_JOOMLEAGUE_ADMIN_ROUNDS_POPULATE_TEAMS_ORDER_LABEL'); ?></label>
			</td>
			<td>
				<?php echo $this->lists['teamsorder']; ?>
				<div id="ordering_buttons">
					<button type="button" id="buttonup"><?php echo Text::_('COM_JOOMLEAGUE_ADMIN_ROUNDS_POPULATE_TEAMS_ORDER_UP')?></button>
					<button type="button" id="buttondown"><?php echo Text::_('COM_JOOMLEAGUE_ADMIN_ROUNDS_POPULATE_TEAMS_ORDER_DOWN')?></button>
				</div>
			</td>
		</tr>
			</tbody>
		</table>
	</fieldset>

	<input type="hidden" name="task" value="" />
	<input type='hidden' name='project_id' value='<?php echo $this->project->id; ?>' />
</form>
