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
HTMLHelper::_('bootstrap.tooltip');
?>
<form action="<?php echo Route::_('index.php?option=com_joomleague&view=treetos'); ?>" method="post" id="adminForm" name="adminForm">
		<div id="j-main-container" class="j-main-container">
		<fieldset class="adminform">
			<legend><?php echo Text::sprintf('COM_JOOMLEAGUE_ADMIN_TREETOS_TITLE','<i>','<i>'.$this->project->name.'</i>'); ?></legend>
			<?php
			$colspan = 11;
			?>
			<?php
			if($this->project->project_type == 'DIVISIONS_LEAGUE')
			{
				$colspan ++;
			?>
			<?php
				echo Text::_('COM_JOOMLEAGUE_ADMIN_TREETO_DIVISION');
				echo '<br>' . HTMLHelper::_('select.genericlist',$this->lists['divisions'],'division',
				'class="inputbox" size="1" onchange="window.location.href=window.location.href.split(\'&division=\')[0]+\'&division=\'+this.value"',
				'value','text',$this->division);
			?>
			<?php
			}
			?>
			<table class="table table-striped" id="treetoList">
				<thead>
					<tr>
						<th width="5" style="vertical-align: top;">
							<?php echo count($this->items).'/'.$this->pagination->total; ?>
						</th>
						<th width="1%" class="center">
							<?php echo HTMLHelper::_('grid.checkall'); ?>
						</th>
						<th width="20" style="vertical-align: top;">&nbsp;</th>
						<th width="20" style="vertical-align: top;">&nbsp;</th>
						<th class="title" nowrap="nowrap" style="vertical-align: top;">
							<?php echo Text::_('COM_JOOMLEAGUE_ADMIN_TREETOS_NAME'); ?>
						</th>
						<th>
						</th>
						<th width="1%"><?php echo Text::_('COM_JOOMLEAGUE_ADMIN_TREETOS_DEPTH'); ?></th>
						<th class="center"><?php echo Text::_('COM_JOOMLEAGUE_ADMIN_TREETOS_HIDE'); ?></th>
						<th width="1%"><?php echo Text::_('COM_JOOMLEAGUE_GLOBAL_PUBLISHED'); ?></th>
						<th width="1%"><?php echo Text::_('COM_JOOMLEAGUE_GLOBAL_ID'); ?></th>
					</tr>
				</thead>
				<tbody>
				<?php
				$n = count($this->items);
				foreach($this->items as $i=>$row) :
					$checked = HTMLHelper::_('grid.checkedout',$row,$i,'id');
					$published = HTMLHelper::_('jgrid.published',$row->published,$i,'treetos.');
				?>
					<tr class="row<?php echo $i % 2; ?>">
						<td style="text-align: center;"><?php echo $this->pagination->getRowOffset($i);?></td>
						<td style="text-align: center;"><?php echo $checked;?></td>
						<td style="text-align: center;">
							<a href="index.php?option=com_joomleague&task=treeto.edit&id=<?php echo $row->id; ?>">
							<?php
							echo HTMLHelper::_('image','administrator/components/com_joomleague/assets/images/edit.png',
							Text::_('COM_JOOMLEAGUE_ADMIN_TREETOS_EDIT_DETAILS'), 'title= "'.Text::_('COM_JOOMLEAGUE_ADMIN_TREETOS_EDIT_DETAILS').'"');
							?>
							</a>
						</td>
						<td class="center">
						<?php
						if($row->leafed == 0)
						{
						?>
							<a href="index.php?option=com_joomleague&task=treetos.genNode&cid[]=<?php echo $row->id; ?>">
							<?php
							echo HTMLHelper::_('image','administrator/components/com_joomleague/assets/images/update.png',
							Text::_('COM_JOOMLEAGUE_ADMIN_TREETOS_GENERATE'),'title= "'.Text::_('COM_JOOMLEAGUE_ADMIN_TREETOS_GENERATE').'"');
							?>
							</a>
						<?php
						}
						else
						{
						?>
							<a href="index.php?option=com_joomleague&view=treetonodes&tid[]=<?php echo $row->id; ?>">
							<?php
							echo HTMLHelper::_('image','administrator/components/com_joomleague/assets/images/icon-16-Tree.png',
							Text::_('COM_JOOMLEAGUE_ADMIN_TREETOS_EDIT_TREE'),'title= "'.Text::_('COM_JOOMLEAGUE_ADMIN_TREETOS_EDIT_TREE').'"');
							?>
							</a>
						<?php
						}
						?>
						</td>
						<td><?php echo $row->name;?></td>
						<?php
						if($this->project->project_type == 'DIVISIONS_LEAGUE')
						{
						?>
						<td nowrap="nowrap" class="center">
						<?php
							$append = '';
						if($row->division_id == 0)
						{
							$append = ' style="background-color:#bbffff"';
						}
							echo HTMLHelper::_('select.genericlist',$this->lists['divisions'],'division_id' . $row->id,
							$append . 'class="inputbox" size="1" onchange="document.getElementById(\'cb' . $i . '\').checked=true"' . $append,
							'value','text',$row->division_id);
						?>
						</td>
						<?php
						}
						?>
						<td class="center"><?php echo $row->tree_i;?></td>
						<td class="center"><?php echo $row->hide;?></td>
						<td class="center"><?php echo $published;?></td>
						<td class="center"><?php echo $row->id;?></td>
					</tr>
						<?php endforeach; ?>
				</tbody>
				<tfoot>
					<tr>
						<td colspan="<?php echo $colspan; ?>"><?php echo $this->pagination->getListFooter(); ?></td>
					</tr>
				</tfoot>
			</table>
		</fieldset>
		<div/>
	<!-- Input fields -->
	<input type="hidden" name="project_id" value="<?php echo $this->project->id; ?>" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="view" value="treetos" />
	<input type="hidden" name="count" value="<?php echo $this->pagination->total; ?>" />
	<?php echo HTMLHelper::_('form.token'); ?>
</form>
