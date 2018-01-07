<?php
/**
 * Joomleague
 *
 * @copyright	Copyright (C) 2006-2015 joomleague.at. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @link		http://www.joomleague.at
 */
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;

defined ( '_JEXEC' ) or die ();
if (isset ( $this->preFillSuccess ) && $this->preFillSuccess) {
	Factory::getApplication ()->enqueueMessage ( Text::_ ( 'COM_JOOMLEAGUE_ADMIN_MATCH_PREFILL_DONE' ), 'message' );
}
?>
<fieldset class="adminform">
	<legend><?php echo Text::_('COM_JOOMLEAGUE_ADMIN_MATCH_ELUP_START_LU'); ?></legend>
	<table class='adminlist'>
		<thead>
			<tr>
				<th>
					<?php echo Text::_('COM_JOOMLEAGUE_ADMIN_MATCH_ELUP_ROSTER'); ?>
					</th>
				<th>
					<?php echo Text::_('COM_JOOMLEAGUE_ADMIN_MATCH_ELUP_ASSIGNED'); ?>
					</th>
			</tr>
		</thead>
		<tr>
			<td style="text-align: center; vertical-align: top;">					
						<?php
						// echo select list of non assigned players from team roster
						echo $this->lists ['team_players'];
						?>
					</td>
			<td style="text-align: center; vertical-align: top;">
				<table>
							<?php
							foreach ( $this->positions as $position_id => $pos ) {
								?>
								<tr>
						<td style='text-align: center; vertical-align: middle;'>
							<!-- left / right buttons --> <br /> <input type="button"
							id="moveright-<?php echo $position_id;?>"
							class="inputbox pmove-right" value="&gt;&gt;" /><br />
							&nbsp;&nbsp; <input type="button"
							id="moveleft-<?php echo $position_id;?>"
							class="inputbox pmove-left" value="&lt;&lt;" /> &nbsp;&nbsp;
						</td>
						<td>
							<!-- player affected to this position --> <b><?php echo Text::_($pos->text);?></b><br />
										<?php echo $this->lists['team_players'.$position_id];?>
									</td>
						<td style='text-align: center; vertical-align: middle;'>
							<!-- up/down buttons --> <br /> <input type="button"
							id="moveup-<?php echo $position_id;?>" class="inputbox pmove-up"
							value="<?php echo Text::_('COM_JOOMLEAGUE_GLOBAL_UP'); ?>" /><br />
							<input type="button" id="movedown-<?php echo $position_id;?>"
							class="inputbox pmove-down"
							value="<?php echo Text::_('COM_JOOMLEAGUE_GLOBAL_DOWN'); ?>" />
						</td>
					</tr>
								<?php
							}
							?>
						</table>
			</td>
		</tr>
	</table>
</fieldset>
