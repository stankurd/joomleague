<?php
/**
 * Joomleague
 *
 * @copyright	Copyright (C) 2006-2015 joomleague.at. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @link		http://www.joomleague.at
 */
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;

defined('_JEXEC') or die;

HTMLHelper::_('behavior.tooltip');

$app = Factory::getApplication();
$user = Factory::getUser();
$userId = $user->get('id');
?>
<script>
function searchposition(val,key)
{
	jQuery('#filter_search'].val(val);
	jQuery('#adminForm').submit();
}
</script>
<form action="<?php echo Route::_('index.php?option=com_joomleague&view=projectpositions'); ?>" method="post" name="adminForm" id="adminForm">
	<div id="j-main-container" class="j-main-container">
	<fieldset class="form-horizontal">
		<legend><?php echo Text::sprintf('COM_JOOMLEAGUE_ADMIN_P_POSITION_LEGEND','<i>'.$this->project->name.'</i>'); ?></legend>
		<div id="j-main-container" class="j-main-container">
		<table class="table table-striped" id="projectpositionList">
			<thead>
				<tr>
					<th>
						<?php echo HTMLHelper::_('grid.sort','COM_JOOMLEAGUE_ADMIN_P_POSITION_STANDARD_NAME_OF_POSITION','po.name',$this->lists['order_Dir'],$this->lists['order']); ?>
					</th>
					<th>
						<?php echo Text::_('COM_JOOMLEAGUE_ADMIN_P_POSITION_TRANSLATION'); ?>
					</th>
					<th>
						<?php echo HTMLHelper::_('grid.sort','COM_JOOMLEAGUE_ADMIN_P_POSITION_PARENTNAME','po.parent_id',$this->lists['order_Dir'],$this->lists['order']); ?>
					</th>
					<th width="20">
						<?php echo HTMLHelper::_('grid.sort','COM_JOOMLEAGUE_ADMIN_P_POSITION_PLAYER_POSITION','persontype',$this->lists['order_Dir'],$this->lists['order']); ?>
					</th>
					<th width="20">
						<?php echo HTMLHelper::_('grid.sort','COM_JOOMLEAGUE_ADMIN_P_POSITION_STAFF_POSITION','persontype',$this->lists['order_Dir'],$this->lists['order']); ?>
					</th>
					<th width="20">
						<?php echo HTMLHelper::_('grid.sort','COM_JOOMLEAGUE_ADMIN_P_POSITION_REFEREE_POSITION','persontype',$this->lists['order_Dir'],$this->lists['order']); ?>
					</th>
					<th width="20">
						<?php echo HTMLHelper::_('grid.sort','COM_JOOMLEAGUE_ADMIN_P_POSITION_CLUBSTAFF_POSITION','persontype',$this->lists['order_Dir'],$this->lists['order']); ?>
					</th>
						<?php
						/*
						 * ?>
						 * <th class="title">
						 * <?php echo
						 * HTMLHelper::_('grid.sort','COM_JOOMLEAGUE_ADMIN_P_POSITION_SPORTSTYPE','po.sports_type_id',$listDirn, $listOrder);
						 * ?>
						 * </th>
						 * <?php
						 */
						?>
					<th width="5%">
						<?php echo Text::_('COM_JOOMLEAGUE_ADMIN_P_POSITION_HAS_EVENTS'); ?>
					</th>
					<th width="5%">
						<?php echo Text::_('COM_JOOMLEAGUE_ADMIN_P_POSITION_HAS_STATS'); ?>
					</th>
					<th width="1%">
						<?php echo HTMLHelper::_('grid.sort','PID','po.id',$this->lists['order_Dir'],$this->lists['order']); ?>
					</th>
					<th width="1%">
						<?php echo HTMLHelper::_('grid.sort','COM_JOOMLEAGUE_GLOBAL_ID','a.id',$this->lists['order_Dir'],$this->lists['order']); ?>
					</th>
				</tr>
			</thead>
			<tbody>
			<?php
			$n = count($this->items);
			foreach($this->items as $i=>$row):
				$imageFileOk = 'administrator/components/com_joomleague/assets/images/ok.png';
			?>
				<tr class="row<?php echo $i % 2; ?>">
					<td><?php echo $row->name; ?></td>
					<td><?php if ($row->name != Text::_($row->name)){echo Text::_($row->name);} ?></td>
					<td><?php echo Text::_($row->parent_name); ?></td>
					<td class="center">
					<?php
					if($row->persontype == 1)
					{
						$imageTitle = Text::_('COM_JOOMLEAGUE_ADMIN_P_POSITION_PLAYER_POSITION');
						$imageParams = 'title= "' . $imageTitle . '"';
						echo HTMLHelper::image($imageFileOk,$imageTitle,$imageParams);
					}
					?>
					</td>
					<td class="center">
					<?php
					if($row->persontype == 2)
					{
						$imageTitle = Text::_('COM_JOOMLEAGUE_ADMIN_P_POSITION_STAFF_POSITION');
						$imageParams = 'title= "' . $imageTitle . '"';
						echo HTMLHelper::image($imageFileOk,$imageTitle,$imageParams);
					}
					?>
					</td>
					<td class="center">
					<?php
					if($row->persontype == 3)
					{
						$imageTitle = Text::_('COM_JOOMLEAGUE_ADMIN_P_POSITION_REFEREE_POSITION');
						$imageParams = 'title= "' . $imageTitle . '"';
						echo HTMLHelper::image($imageFileOk,$imageTitle,$imageParams);
					}
					?>
					</td>
					<td class="center">
					<?php
					if($row->persontype == 4)
					{
						$imageTitle = Text::_('COM_JOOMLEAGUE_ADMIN_P_POSITION_CLUBSTAFF_POSITION');
						$imageParams = 'title= "' . $imageTitle . '"';
						echo HTMLHelper::image($imageFileOk,$imageTitle,$imageParams);
					}
					?>
					</td>
					<?php
					/*
					 * ?>
					 * <td class="center"><?php echo
					 * Text::_(JoomleagueHelper::getSportsTypeName($row->sports_type_id));
					 * ?></td>
					 * <?php
					 */
					?>
					<td class="center">
					<?php
					if($row->countEvents == 0)
					{
						$imageFile = 'administrator/components/com_joomleague/assets/images/error.png';
						$imageTitle = Text::_('COM_JOOMLEAGUE_ADMIN_P_POSITION_NO_EVENTS');
						$imageParams = 'title= "' . $imageTitle . '"';
						echo HTMLHelper::image($imageFile,$imageTitle,$imageParams);
					}
					else
					{
						$imageTitle = Text::sprintf('COM_JOOMLEAGUE_ADMIN_P_POSITION_NR_EVENTS',$row->countEvents);
						$imageParams = 'title= "' . $imageTitle . '"';
						echo HTMLHelper::image($imageFileOk,$imageTitle,$imageParams);
					}
					?>
					</td>
					<td class="center">
					<?php
					if($row->countStats == 0)
					{
						$imageFile = 'administrator/components/com_joomleague/assets/images/error.png';
						$imageTitle = Text::_('COM_JOOMLEAGUE_ADMIN_P_POSITION_NO_STATISTICS');
						$imageParams = 'title= "' . $imageTitle . '"';
						echo HTMLHelper::image($imageFile,$imageTitle,$imageParams);
					}
					else
					{
						$imageTitle = Text::sprintf('COM_JOOMLEAGUE_ADMIN_P_POSITION_NR_STATISTICS',$row->countStats);
						$imageParams = 'title= "' . $imageTitle . '"';
						echo HTMLHelper::image($imageFileOk,$imageTitle,$imageParams);
					}
					?>
					</td>
					<td class="center">
					<?php
						$position_edit_link = Route::_('index.php?option=com_joomleague&task=position.edit&id='.$row->id.'&return=projectpositions');
					?>
						<a href="<?php echo $position_edit_link ?>">
						<?php
							echo $row->id;
						?>
						</a>
					</td>
					<td class="center"><?php echo $row->projectpositionid;?></td>
				</tr>
				<?php endforeach; ?>
			</tbody>
			<tfoot>
				<tr>
					<td colspan='12'><?php echo $this->pagination->getListFooter(); ?></td>
				</tr>
			</tfoot>
		</table>
	</fieldset>
	</div>
	<!-- input fields -->
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="filter_order" value="<?php echo $this->lists['order']; ?>" />
	<input type="hidden" name="filter_order_Dir" value="" />
	<?php echo HTMLHelper::_('form.token'); ?>
</form>
