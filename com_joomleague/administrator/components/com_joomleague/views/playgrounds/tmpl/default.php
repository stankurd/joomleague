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
	$saveOrderingUrl = 'index.php?option=com_joomleague&task=playgrounds.saveOrderAjax&tmpl=component';
	HTMLHelper::_('sortablelist.sortable','playgroundList','adminForm',strtolower($listDirn),$saveOrderingUrl);
}
?>
<form action="<?php echo Route::_('index.php?option=com_joomleague&view=playgrounds'); ?>" method="post" id="adminForm" name="adminForm">
	<div id="j-main-container" class="j-main-container">
	<?php
	// Search tools bar
	echo LayoutHelper::render('searchtools.default',array('view' => $this),Uri::root().'administrator/components/com_joomleague/layouts');
	?>
	<div class="btn-wrapper pull-right">
		<div style="max-width: 700px; overflow: auto; float: right">
		<?php
		$startRange = hexdec($this->component_params->get('character_filter_start_hex','0041'));
		$endRange = hexdec($this->component_params->get('character_filter_end_hex','005A'));
		for($i = $startRange;$i <= $endRange;$i ++)
		{
			printf("<a href=\"javascript:searchPlayground('%s')\">%s</a>&nbsp;&nbsp;&nbsp;&nbsp;",chr($i),chr($i));
		}
		?>
		</div>
	</div>
	</div>
	<?php if (empty($this->items)) : ?>
	<div class="alert alert-no-items">
		<?php echo Text::_('JGLOBAL_NO_MATCHING_RESULTS'); ?>
	</div>
	<?php else : ?>
	<div id="j-main-container" class="j-main-container">
	<table class="table table-striped" id="playgroundList">
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
					<?php echo HTMLHelper::_('searchtools.sort','COM_JOOMLEAGUE_ADMIN_PLAYGROUNDS_NAME','a.name',$listDirn, $listOrder);?>
				</th>
				<th>
					<?php echo HTMLHelper::_('searchtools.sort','COM_JOOMLEAGUE_ADMIN_PLAYGROUNDS_S_NAME','a.short_name',$listDirn, $listOrder);?>
				</th>
				<th>
					<?php echo HTMLHelper::_('searchtools.sort','COM_JOOMLEAGUE_ADMIN_PLAYGROUNDS_CLUBNAME','club',$listDirn, $listOrder);?>
				</th>
				<th class="center">
					<?php echo HTMLHelper::_('searchtools.sort','COM_JOOMLEAGUE_ADMIN_PLAYGROUNDS_CAPACITY','a.max_visitors',$listDirn, $listOrder);?>
				</th>
				<th>
					<?php echo Text::_('COM_JOOMLEAGUE_ADMIN_PLAYGROUNDS_IMAGE');?>
				</th>
				<th width="1%">
					<?php echo HTMLHelper::_('searchtools.sort','COM_JOOMLEAGUE_GLOBAL_ID','a.id',$listDirn, $listOrder); ?>
				</th>
			</tr>
		</thead>
		<tbody>
		<?php
		$n = count($this->items);
		foreach($this->items as $i=>$row) :
			$link = Route::_('index.php?option=com_joomleague&task=playground.edit&id='.$row->id);
			$checked = HTMLHelper::_('grid.checkedout',$row,$i);
			$canEdit = $user->authorise('core.edit','com_joomleague.playground.'.$row->id);
			$canCheckin = $user->authorise('core.manage','com_checkin') || $row->checked_out == $userId || $row->checked_out == 0;
			//$canEditOwn = $user->authorise('core.edit.own', 'com_joomleague.playground.'.$row->id) && $row->created_by == $userId;
			$canChange = $user->authorise('core.edit.state', 'com_joomleague.playground.'.$row->id) && $canCheckin;
		?>
			<tr class="row<?php echo $i % 2; ?>">
				<td class="order nowrap center hidden-phone">
				<?php
					$iconClass = '';
				if (!$canChange) {
					$iconClass = ' inactive';
				} elseif (!$saveOrder) {
					$iconClass = ' inactive tip-top hasTooltip" title="'.HTMLHelper::tooltipText ('JORDERINGDISABLED');
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
					$inputappend = ' disabled="disabled" ';
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
					$imageTitle = Text::_('COM_JOOMLEAGUE_ADMIN_PLAYGROUNDS_EDIT_DETAILS');
					echo HTMLHelper::_('image','administrator/components/com_joomleague/assets/images/edit.png',$imageTitle,'title= "'.$imageTitle.'"');
				?>
					</a>
				</td>
				<?php
				}
				?>
				<td><?php echo $row->name; ?></td>
				<td><?php echo $row->short_name; ?></td>
				<td><?php echo $row->club; ?></td>
				<td class="center"><?php echo $row->max_visitors; ?></td>
				<td width="5%" class="center">
				<?php	
				if($row->picture == '')
				{
					$imageTitle = Text::_('COM_JOOMLEAGUE_ADMIN_PLAYGROUNDS_NO_IMAGE');
					echo HTMLHelper::_('image','administrator/components/com_joomleague/assets/images/delete.png',$imageTitle,'title= "'.$imageTitle.'"');
				}
				elseif($row->picture === JoomleagueHelper::getDefaultPlaceholder("playground"))
				{	
					$imageTitle = Text::_('COM_JOOMLEAGUE_ADMIN_PLAYGROUNDS_DEFAULT_IMAGE');
					echo HTMLHelper::_('image','administrator/components/com_joomleague/assets/images/information.png',$imageTitle,'title= "'.$imageTitle.'"');
				}
				elseif($row->picture !== '')
				{
					$imageTitle = Text::_('COM_JOOMLEAGUE_ADMIN_PLAYGROUNDS_CUSTOM_IMAGE');
					echo HTMLHelper::_('image','administrator/components/com_joomleague/assets/images/ok.png',$imageTitle,'title= "'.$imageTitle.'"');
				}
				?>
				</td>
				<td class="center"><?php echo $row->id; ?></td>
			</tr>
		<?php endforeach; ?>
		</tbody>
		<tfoot>
			<tr>
				<td colspan="10"></td>
			</tr>
		</tfoot>
	</table>
	<?php endif; ?>
	<?php echo $this->pagination->getListFooter(); ?>
	</div>
	<!-- input fields -->
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />
	<?php echo HTMLHelper::_('form.token'); ?>
</form>
