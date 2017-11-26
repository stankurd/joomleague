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
<fieldset class="adminform">
	<legend>
					<?php
					echo JText::_('COM_JOOMLEAGUE_ADMIN_P_TEAM_TITLE_TRAINING');
					?>
				</legend>
	<table class="table table-striped" id="projectteamTrainingList">
		<tr>
			<td>
						<?php
						echo $this->form->renderField('add_trainingData');
						?>
						</td>
			<td class="center"><b><?php echo JText::_('COM_JOOMLEAGUE_ADMIN_P_TEAM_DAY'); ?></b></td>
			<td class="center"><b><?php echo JText::_('COM_JOOMLEAGUE_ADMIN_P_TEAM_STARTTIME'); ?></b></td>
			<td class="center"><b><?php echo JText::_('COM_JOOMLEAGUE_ADMIN_P_TEAM_ENDTIME'); ?></b></td>
			<td class="center"><b><?php echo JText::_('COM_JOOMLEAGUE_ADMIN_P_TEAM_PLACE'); ?></b></td>
			<td class="center"><b><?php echo JText::_('COM_JOOMLEAGUE_ADMIN_P_TEAM_NOTES'); ?></b></td>
		</tr>
					<?php
					if(! empty($this->trainingData))
					{
						?>
						<input type='hidden' name='tdCount' value='<?php echo count($this->trainingData); ?>' />
						<?php
						foreach($this->trainingData as $td)
						{
							$hours = ($td->time_start / 3600);
							$hours = (int) $hours;
							$mins = (($td->time_start - (3600 * $hours)) / 60);
							$mins = (int) $mins;
							$startTime = sprintf('%02d',$hours) . ':' . sprintf('%02d',$mins);
							$hours = ($td->time_end / 3600);
							$hours = (int) $hours;
							$mins = (($td->time_end - (3600 * $hours)) / 60);
							$mins = (int) $mins;
							$endTime = sprintf('%02d',$hours) . ':' . sprintf('%02d',$mins);
							?>
							<tr>
			<td class='key' nowrap='nowrap'>
									<?php echo JText::_('COM_JOOMLEAGUE_GLOBAL_DELETE');?>&nbsp;<input
				type='checkbox' name='delete_<?php echo $td->id; ?>' value=''
				onchange='javascript:Joomla.submitform("projectteam.apply");' />
			</td>
			<td nowrap='nowrap' width='5%'><?php echo $this->lists['dayOfWeek'][$td->id]; ?></td>
			<td nowrap='nowrap' width='5%'><input class="input-small" type='text'
				name='time_start_<?php echo $td->id; ?>' maxlength='5'
				value='<?php echo $startTime; ?>' /></td>
			<td nowrap='nowrap' width='5%'><input class="input-small" type='text'
				name='time_end_<?php echo $td->id; ?>' maxlength='5'
				value='<?php echo $endTime; ?>' /></td>
			<td><input class="input-medium" type='text'
				name='place_<?php echo $td->id; ?>' maxlength='255'
				value='<?php echo $td->place; ?>' /></td>
			<td><textarea class='text_area' name='notes_<?php echo $td->id; ?>'
					rows='3' cols='40' value='' /><?php echo $td->notes; ?></textarea>
				<input type='hidden' name='tdids[]' value='<?php echo $td->id; ?>' />
			</td>
		</tr>
							<?php
						}
					}
					?>
				</table>
</fieldset>