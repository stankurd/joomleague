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
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;

defined('_JEXEC') or die;

HTMLHelper::_('behavior.tooltip');

$app = Factory::getApplication();
$user = Factory::getUser();
$userId = $user->get('id');
$listOrder = $this->escape($this->state->get('list.ordering'));
$listDirn = $this->escape($this->state->get('list.direction'));
$archived = $this->state->get('filter.published') == 2 ? true : false;
$trashed = $this->state->get('filter.published') == - 2 ? true : false;
$saveOrder = $listOrder == 'a.ordering';
if($saveOrder)
{
	$saveOrderingUrl = 'index.php?option=com_joomleague&task=eventtypes.saveOrderAjax&tmpl=component';
	HTMLHelper::_('sortablelist.sortable','eventtypeList','adminForm',strtolower($listDirn),$saveOrderingUrl);
}
Factory::getDocument()->addScriptDeclaration('
	Joomla.submitbutton = function(task)
	{
		document.adminForm.task.value=task;
		if (task == "eventtypes.export") {
			Joomla.submitform(task, document.getElementById("adminForm"));
			document.adminForm.task.value="";
		} else {
      		Joomla.submitform(task, document.getElementById("adminForm"));
		}
	};
');
?>
<form action="<?php echo Route::_('index.php?option=com_joomleague&view=eventtypes'); ?>" method="post" id="adminForm" name="adminForm">
		<div id="j-main-container" class="j-main-container">
		<?php
		// Search tools bar
		echo LayoutHelper::render('searchtools.default',array('view' => $this),Uri::root().'administrator/components/com_joomleague/layouts');
		?>
		<div class="btn-wrapper pull-right">
			<div class="btn-wrapper pull-right">
				<div>
					<?php echo $this->lists['sportstypes'].'&nbsp;&nbsp;'; ?>
					<?php echo $this->lists['state']; ?>
				</div>
			</div>
		</div>
	</div>
	<?php if (empty($this->items)) : ?>
	<div class="alert alert-no-items">
		<?php echo Text::_('JGLOBAL_NO_MATCHING_RESULTS'); ?>
	</div>
	<?php else : ?>
	<div id="j-main-container" class="j-main-container">
	<table class="table table-striped" id="eventtypeList">
		<thead>
			<tr>
				<th width="1%">
					<?php echo HTMLHelper::_('searchtools.sort', '', 'a.ordering', $listDirn, $listOrder, null, 'asc', 'JGRID_HEADING_ORDERING', 'icon-menu-2'); ?>
				</th>
				<th width="1%" class="center">
					<?php echo HTMLHelper::_('grid.checkall'); ?>
				</th>
				<th width="20">&nbsp;</th>
				<th>
					<?php echo HTMLHelper::_('searchtools.sort','COM_JOOMLEAGUE_ADMIN_EVENTTYPES_STANDARD_NAME_OF_EVENT','a.name',$listDirn, $listOrder);?>
				</th>
				<th>
					<?php echo Text::_('COM_JOOMLEAGUE_ADMIN_EVENTTYPES_TRANSLATION'); ?>
				</th>
				<th width="10%">
					<?php echo Text::_('COM_JOOMLEAGUE_ADMIN_EVENTTYPES_ICON');?>
				</th>
				<th width="10%">
					<?php echo HTMLHelper::_('searchtools.sort','COM_JOOMLEAGUE_ADMIN_EVENTTYPES_SPORTSTYPE','a.sports_type_id',$listDirn, $listOrder);?>
				</th>
				<th width="1%">
					<?php echo Text::_('COM_JOOMLEAGUE_GLOBAL_PUBLISHED');?>
				</th>
				<th width="1%">
					<?php echo HTMLHelper::_('searchtools.sort','COM_JOOMLEAGUE_GLOBAL_ID','a.id',$listDirn, $listOrder);?>
				</th>
			</tr>
		</thead>
		<tbody>
		<?php
		$n = count($this->items);
		foreach($this->items as $i=>$row):
			$link = Route::_('index.php?option=com_joomleague&task=eventtype.edit&id='.$row->id);
			$checked = HTMLHelper::_('grid.checkedout',$row,$i);
			$published = HTMLHelper::_('jgrid.published',$row->published,$i,'eventtypes.');

			$canEdit = $user->authorise('core.edit','com_joomleague.eventtype.'.$row->id);
			$canCheckin = $user->authorise('core.manage','com_checkin') || $row->checked_out == $userId || $row->checked_out == 0;
			//$canEditOwn = $user->authorise('core.edit.own','com_joomleague.eventtype.'.$row->id) && $row->created_by == $userId;
			$canChange = $user->authorise('core.edit.state','com_joomleague.eventtype.'.$row->id) && $canCheckin;
		?>
			<tr class="row<?php echo $i % 2; ?>">
				<td class="order nowrap center hidden-phone">
				<?php
				$iconClass = '';
				if (!$canChange) {
					$iconClass = ' inactive';
				} elseif (!$saveOrder) {
					$iconClass = ' inactive tip-top hasTooltip" title="'.HTMLHelper::tooltipText('JORDERINGDISABLED');
				}
				?>
					<span class="sortable-handler<?php echo $iconClass ?>"><span class="icon-menu"></span></span>
					<?php if ($canChange && $saveOrder) : ?>
					<input type="text" style="display: none" name="order[]" size="5" value="<?php echo $row->ordering; ?>" class="width-20 text-area-order " />
					<?php endif; ?>
				</td>
				<td class="center"><?php echo $checked; ?></td>
				<?php
				if(JLTable::_isCheckedOut($this->user->get('id'),$row->checked_out))
				{
					$inputappend = ' disabled="disabled"';
				?>
				<td class="center">&nbsp;</td>
				<?php
				}
				else
				{
					$inputappend = '';
				?>
				<td class="center">
					<a href="<?php echo $link; ?>">
				<?php
					$imageTitle = Text::_('COM_JOOMLEAGUE_ADMIN_EVENTS_EDIT_DETAILS');
					echo HTMLHelper::_('image','administrator/components/com_joomleague/assets/images/edit.png',$imageTitle,'title= "' . $imageTitle . '"');
				?>
					</a>
				</td>
				<?php
				}
				?>
				<td><?php echo $row->name; ?></td>
				<td>
				<?php
				if($row->name == Text::_($row->name))
				{
					echo '&nbsp;';
				}
				else
				{
					echo Text::_($row->name);
				}
				?>
				</td>
				<td class="center">
				<?php
					$desc = Text::_($row->name);
					echo JoomleagueHelper::getPictureThumb($row->icon,$desc,0,21,4);
				?>
				</td>
				<td class="center"><?php echo JoomleagueHelper::getSportsTypeName($row->sports_type_id);?></td>
				<td class="center"><?php echo $published;?></td>
				<td class="center"><?php echo $row->id;?></td>
			</tr>
			<?php endforeach; ?>
		</tbody>
		<tfoot>
			<tr>
				<td colspan="10">
					<?php echo $this->pagination->getListFooter(); ?>
				</td>
			</tr>
		</tfoot>
	</table>
	<?php endif; ?>
	</div>
	<!-- input fields -->
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />
	<?php echo HTMLHelper::_('form.token'); ?>
</form>