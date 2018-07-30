<?php
/**
 * Joomleague
 *
 * @copyright	Copyright (C) 2006-2015 joomleague.at. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @link		http://www.joomleague.at
 */
use Joomla\CMS\Factory;
use Joomla\CMS\Filesystem\File;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

defined ( '_JEXEC' ) or die ();
jimport ( 'joomla.filesystem.file' );

if (isset ( $this->preFillSuccess ) && $this->preFillSuccess) {
	Factory::getApplication ()->enqueueMessage ( $this->teams->team2 . ": " . Text::_ ( 'COM_JOOMLEAGUE_ADMIN_MATCH_PREFILL_DONE' ), 'message' );
}
?>
<fieldset class="adminform">
	<legend>
				<?php
				echo $this->teams->team2;
				?>
			</legend>
	<table class="table table-striped">
		<thead>
			<tr>
				<th style="text-align: left; width: 10px;"></th>
				<th style="text-align: left;"><?php echo Text::_('COM_JOOMLEAGUE_ADMIN_MATCH_EEBB_PERSON' ); ?></th>
				<?php
				foreach ( $this->events as $ev ) {
					?>
					<th style="text-align: center;">
					<?php
					if (File::exists ( JPATH_SITE . '/' . $ev->icon )) {
						$imageTitle = Text::sprintf ( '%1$s', Text::_ ( $ev->text ) );
						$iconFileName = $ev->icon;
						echo HTMLHelper::image ( $iconFileName, $imageTitle, 'title= "' . $imageTitle . '"' );
					} else {
						echo Text::_ ( $ev->text );
					}
					?>
					</th>
					<?php
				}
				?>
			</tr>
		</thead>
		<tbody>
			<?php
			$k = 0;
			$teap = 0;
			$model = $this->getModel ();
			for($i = 0, $n = count ( $this->awayRoster ); $i < $n; $i ++) {
				$row = $this->awayRoster [$i];
				if ($row->value == 0)
					continue;
				?>
					<tr class="row<?php echo $k;?>">
				<td class="left"><input type="hidden"
					name="player_id_a_<?php echo $i;?>"
					value="<?php echo $row->value;?>" /> <input type="hidden"
					name="team_id_a_<?php echo $i;?>"
					value="<?php echo $row->projectteam_id;?>" /> <input
					type="checkbox" id="cb_a<?php echo $i;?>"
					name="cid_a<?php echo $i;?>" value="cb_a"
					onclick="isChecked(this.checked);" /></td>
				<td class="left">
						<?php
				if ($row->jerseynumber > 0) {
					echo ' (' . $row->jerseynumber . ')';
				}
				switch ($this->default_name_dropdown_list_order) {
					case 'lastname' :
					case 'firstname' :
						echo JoomleagueHelper::formatName ( null, $row->firstname, $row->nickname, $row->lastname, $this->default_name_format );
						break;
					
					case 'position' :
						echo '(' . Text::_ ( $row->positionname ) . ') - ' . JoomleagueHelper::formatName ( null, $row->firstname, $row->nickname, $row->lastname, $this->default_name_format );
						break;
				}
				?>
						</td>
						<?php
				// total events away player
				$teap = 0;
				foreach ( $this->events as $ev ) {
					$teap ++;
					$this->evbb = $model->getPlayerEventsbb ( $row->value, $ev->value, $this->match_id );
					?>
							<td class="leftdashed"><input type="hidden"
					name="event_type_id_a_<?php echo $i.'_'.$teap;?>"
					value="<?php echo $ev->value;?>" /> <input type="hidden"
					name="event_id_a_<?php echo $i.'_'.$teap;?>"
					value="<?php echo $this->evbb[0]->id;?>" /> <input type="text"
					size="1" class="inputbox"
					name="event_sum_a_<?php echo $i.'_'.$teap; ?>"
					value="<?php echo (($this->evbb[0]->event_sum > 0) ? $this->evbb[0]->event_sum : '' ); ?>"
					title="<?php echo Text::_('COM_JOOMLEAGUE_ADMIN_MATCH_EE_VALUE_SUM' )?>"
					onchange="document.getElementById('cb_a<?php echo $i;?>').checked=true" />
					<input type="text" size="2" class="inputbox"
					name="event_time_a_<?php echo $i.'_'.$teap; ?>"
					value="<?php echo (($this->evbb[0]->event_time > 0) ? $this->evbb[0]->event_time : '' ); ?>"
					title="<?php echo Text::_('COM_JOOMLEAGUE_ADMIN_MATCH_EE_TIME' )?>"
					onchange="document.getElementById('cb_a<?php echo $i;?>').checked=true" />
					<input type="text" size="3" class="inputbox"
					name="notice_a_<?php echo $i.'_'.$teap; ?>"
					value="<?php echo ((strlen($this->evbb[0]->notice) > 0) ? $this->evbb[0]->notice : '' ); ?>"
					title="<?php echo Text::_('COM_JOOMLEAGUE_ADMIN_MATCH_EE_MATCH_NOTICE' )?>"
					onchange="document.getElementById('cb_a<?php echo $i;?>').checked=true" />
					&nbsp;&nbsp;</td>

						<?php
				}
				?>
					</tr>
					<?php
				$k = 1 - $k;
			}
			?>
		</tbody>
	</table>
	<input type="hidden" name="total_a_players" value="<?php echo $i;?>" />
	<input type="hidden" name="teap" value="<?php echo $teap;?>" />
</fieldset>
