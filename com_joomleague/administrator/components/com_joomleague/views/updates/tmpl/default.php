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
use Joomla\CMS\Router\Route;

defined('_JEXEC') or die;

HTMLHelper::_('behavior.tooltip');
?>
<form action="<?php echo $this->request_url; ?>" method="post" id="adminForm" name="adminForm">
	<div class="j-main-container">	
	<table class="table table-striped">
		<thead>
			<tr>
				<th class="title" class="nowrap"><?php echo HTMLHelper::_('grid.sort','COM_JOOMLEAGUE_ADMIN_UPDATES_FILE','name',$this->lists['order_Dir'],$this->lists['order']); ?></th>
				<th class="title" class="nowrap"><?php echo Text::_('COM_JOOMLEAGUE_ADMIN_UPDATES_DESCR'); ?></th>
				<th class="title" class="nowrap"><?php echo HTMLHelper::_('grid.sort','COM_JOOMLEAGUE_ADMIN_UPDATES_VERSION','version',$this->lists['order_Dir'],$this->lists['order']); ?></th>
				<th class="title" class="nowrap"><?php echo HTMLHelper::_('grid.sort','COM_JOOMLEAGUE_ADMIN_UPDATES_DATE','date',$this->lists['order_Dir'],$this->lists['order']); ?></th>
				<th class="title" class="nowrap"><?php echo Text::_('COM_JOOMLEAGUE_ADMIN_UPDATES_EXECUTED'); ?></th>
				<th class="title" class="nowrap"><?php echo Text::_('COM_JOOMLEAGUE_ADMIN_UPDATES_COUNT');?></th>
			</tr>
		</thead>
		<tfoot><tr><td col-md-7><?php echo '&nbsp;'; ?></td></tr></tfoot>
		<tbody>
		<?php
		$n = count($this->items);
		foreach ($this->items as $i => $row) :
			$link = Route::_('index.php?option=com_joomleague&view=updates&task=update.update&file_name='.$row['file_name']);
			?>
			<tr class="row<?php echo $i % 2; ?>">
				<td><?php
					$linkTitle=$row['file_name'];
					$linkParams="title='".Text::_('COM_JOOMLEAGUE_ADMIN_UPDATES_MAKE_UPDATE')."'";
					echo HTMLHelper::link($link,$linkTitle,$linkParams);
					?></td>
				<td><?php
					if($row['updateDescription'] != "")
					{
						echo $row['updateDescription'];
					}
					else
					{
						echo Text::sprintf('COM_JOOMLEAGUE_ADMIN_UPDATES_UPDATE',$row['last_version'],$row['version']);
					}
					?></td>
				<td class="center"><?php echo $row['version']; ?></td>
				<td class="center"><?php echo Text::_($row['updateFileDate']).' '.Text::_($row['updateFileTime']); ?></td>
				<td class="center"><?php echo $row['date']; ?></td>
				<td class="center"><?php echo $row['count']; ?></td>
			</tr>
			<?php endforeach; ?>
		</tbody>
	</table>
	<div/>
	<input type="hidden" name="view" value="updates" />
	<input type="hidden" name="task" value="update.display" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="filter_order" value="<?php echo $this->lists['order']; ?>" />
	<input type="hidden" name="filter_order_Dir" value="" />
	<?php echo HTMLHelper::_('form.token'); ?>
</form>
