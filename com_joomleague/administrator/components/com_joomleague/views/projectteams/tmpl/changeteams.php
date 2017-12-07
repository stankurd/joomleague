<?php
/**
 * Joomleague
 *
 * @copyright	Copyright (C) 2006-2015 joomleague.at. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @link		http://www.joomleague.at
 */
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Router\Route;

defined('_JEXEC') or die;
?>
<form action="<?php echo Route::_('index.php?option=com_joomleague&view=projectteams&layout=changeteams'); ?>" method="post" id="adminForm" name="adminForm">
	<fieldset class="adminform" style="float: left;">
		<legend>
		<?php
		echo JText::_('COM_JOOMLEAGUE_ADMIN_PROJECTTEAMS_ASSIGN_PROJ_TEAMS');
		?>
		</legend>
		<table class="adminlist table table-striped">
			<thead>
				<tr>
					<th class="title"><?PHP echo JText::_( 'COM_JOOMLEAGUE_ADMIN_PROJECTTEAMS_CHANGE' ); ?>
					</th>
					<th class="title"><?PHP echo JText::_( 'COM_JOOMLEAGUE_ADMIN_PROJECTTEAMS_SELECT_OLD_TEAM' ); ?>
					</th>
					<th class="title"><?PHP echo JText::_( 'COM_JOOMLEAGUE_ADMIN_PROJECTTEAMS_SELECT_NEW_TEAM' ); ?>
					</th>
				</tr>
			</thead>

			<?php
			$k = 0;
			$i = 0;

			foreach($this->items as $row)
			{
				$checked = HTMLHelper::_('grid.id','oldptid' . $i,$row->id,$row->checked_out,'oldptid');
				$append = ' style="background-color:#bbffff"';
				$inputappend = '';
				$selectedvalue = 0;
				?>
			<tr class="<?php echo "row$k"; ?>">
				<td class="center"><?php
				echo $checked;
				?>
				</td>
				<td><?php
				echo $row->name;
				?>
				</td>
				<td class="nowrap" class="center"><?php
				echo HTMLHelper::_('select.genericlist',$this->lists['all_teams'],'newptid[' . $row->id . ']',
						$inputappend . 'class="inputbox" size="1" onchange="document.getElementById(\'cboldptid' . $i . '\').checked=true"' . $append,
						'value','text',$selectedvalue);
				?>
				</td>
			</tr>
			<?php
				$i ++;
				$k = (1 - $k);
			}
			?>
		</table>
	</fieldset>
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />
	<?php echo HTMLHelper::_('form.token'); ?>
</form>
