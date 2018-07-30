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
use Joomla\CMS\Session\Session;
use Joomla\CMS\Uri\Uri;

defined('_JEXEC') or die;
HTMLHelper::_('behavior.tooltip');
HTMLHelper::_('behavior.multiselect');

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
    $saveOrderingUrl = 'index.php?option=com_joomleague&task=projects.saveOrderAjax&tmpl=component'. Session::getFormToken() . '=1';
	HTMLHelper::_('sortablelist.sortable','projectList','adminForm',strtolower($listDirn),$saveOrderingUrl);
	HTMLHelper::_('draggablelist.draggable');
}
//$colSpan = $clientId === 1 ? 9 : 10;
Factory::getDocument()->addScriptDeclaration('
	Joomla.submitbutton = function(task)
	{
		document.adminForm.task.value=task;
		if (task == "projects.export") {
			Joomla.submitform(task, document.getElementById("adminForm"));
			document.adminForm.task.value="";
		} else {
      		Joomla.submitform(task, document.getElementById("adminForm"));
		}
	};
');
?>
<form action="<?php echo Route::_('index.php?option=com_joomleague&view=projects'); ?>" method="post" id="adminForm" name="adminForm">
	<div id="j-main-container" class="j-main-container">
		<?php
		// Search tools bar
		echo LayoutHelper::render('searchtools.default',array('view' => $this),Uri::root().'administrator/components/com_joomleague/layouts');
		?>
		<div class="btn-wrapper pull-right">
		<?php echo $this->lists['sportstypes'].'&nbsp;&nbsp;'; ?>
		<?php echo $this->lists['leagues'].'&nbsp;&nbsp;'; ?>
		<?php echo $this->lists['seasons'].'&nbsp;&nbsp;'; ?>
		<?php echo $this->lists['state']; ?>
		</div>
	
	<table class="table table-striped" id="projectList">
		<thead>
			<tr>
				<th width="1%">
					<?php echo HTMLHelper::_('searchtools.sort', '', 'a.ordering', $listDirn, $listOrder, null, 'asc', 'JGRID_HEADING_ORDERING', 'icon-menu-2'); ?>
				</th>
				<th width="1%" class="center">
					<?php echo HTMLHelper::_('grid.checkall'); ?>
				</th>
				<th width="20">&nbsp;</th>
				<th class="title">
					<?php echo HTMLHelper::_('searchtools.sort','COM_JOOMLEAGUE_ADMIN_PROJECTS_NAME_OF_PROJECT','a.name',$listDirn, $listOrder);?>
				</th>
				<th class="title">
					<?php echo HTMLHelper::_('searchtools.sort','COM_JOOMLEAGUE_ADMIN_PROJECTS_LEAGUE','l.name',$listDirn, $listOrder);?>
				</th>
				<th class="title">
					<?php echo HTMLHelper::_('searchtools.sort','COM_JOOMLEAGUE_ADMIN_PROJECTS_SEASON','s.name',$listDirn, $listOrder);?>
				</th>
				<th class="title">
					<?php echo HTMLHelper::_('searchtools.sort','COM_JOOMLEAGUE_ADMIN_PROJECTS_SPORTSTYPE','st.name',$listDirn, $listOrder);?>
				</th>
				<th class="title">
					<?php echo HTMLHelper::_('searchtools.sort','COM_JOOMLEAGUE_ADMIN_PROJECTS_PROJECTTYPE','a.project_type',$listDirn, $listOrder);?>
				</th>
				<th width="2%" class="center">
					<?php echo Text::_('COM_JOOMLEAGUE_ADMIN_PROJECTS_GAMES');?>
				</th>
				<th width="1%" class="center">
					<?php echo Text::_('COM_JOOMLEAGUE_ADMIN_PROJECTS_IS_UTC_CONVERTED');?>
				</th>
				<th width="1%" class="center">
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
			$link = Route::_('index.php?option=com_joomleague&task=project.edit&id='.$row->id.'&return=projects');
			$link2panel = Route::_('index.php?option=com_joomleague&task=joomleague.panel&layout=panel&pid[]='.$row->id.'&stid[]='.$row->sports_type_id.'&seasonid[]='.$row->seasonid);

			$checked = HTMLHelper::_('grid.checkedout',$row,$i);
			$canEdit = $user->authorise('core.edit','com_joomleague.project.'.$row->id);
			$canCheckin = $user->authorise('core.manage','com_checkin') || $row->checked_out == $userId || $row->checked_out == 0;
			//$canEditOwn = $user->authorise('core.edit.own','com_joomleague.project.'.$row->id) && $row->created_by == $userId;
			$canChange = $user->authorise('core.edit.state','com_joomleague.project.'.$row->id) && $canCheckin;

			if($row->is_utc_converted)
			{
				$img = 'administrator/components/com_joomleague/assets/images/';
				$alt = Text::_('COM_JOOMLEAGUE_ADMIN_PROJECT_ALREADY_CONVERTED');
				$is_utc_converted = HTMLHelper::_('image', $img. 'tick.png',$alt,array('title' => $alt));
			}
			else
			{
				$img = 'administrator/components/com_joomleague/assets/images/';
				$alt = Text::_('COM_JOOMLEAGUE_ADMIN_PROJECT_NOT_CONVERTED');
				$is_utc_converted = HTMLHelper::_('image', $img. 'delete.png',$alt,array('title' => $alt));
			}
		?>
			<tr class="row<?php echo $i % 2; ?>">
				<td class="order nowrap center hidden-phone">
				<?php
				$iconClass = '';
				if (!$canChange) {
					$iconClass = ' inactive';
				} elseif (!$saveOrder) {
					$iconClass = ' inactive tip-top hasTooltip" title="' . HTMLHelper::tooltipText ('JORDERINGDISABLED');
				}
				?>
					<span class="sortable-handler<?php echo $iconClass ?>"><span class="icon-menu"></span></span>
					<?php if ($canChange && $saveOrder) : ?>
					<input type="text" style="display: none" name="order[]" size="5" value="<?php echo $row->ordering; ?>" class="width-20 text-area-order " />
					<?php endif; ?>
				</td>
				<td width="5%" class="center"><?php echo $checked; ?></td>
				<?php
				if(JLTable::_isCheckedOut($user->get('id'),$row->checked_out))
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
						$imageTitle = Text::_('COM_JOOMLEAGUE_ADMIN_PROJECTS_EDIT_DETAILS');
						echo HTMLHelper::_('image','administrator/components/com_joomleague/assets/images/edit.png',$imageTitle,'title= "' . $imageTitle . '"');
					?>
					</a>
				</td>
				<?php
				}
				?>
				<td>
				<?php
				if(JLTable::_isCheckedOut($user->get('id'),$row->checked_out))
				{
					echo $row->name;
				}
				else
				{
				?>
					<a href="<?php echo $link2panel; ?>"><?php echo $row->name; ?></a>
				<?php
				}
				?>
				</td>
				<td><?php echo $row->league; ?></td>
				<td><?php echo $row->season; ?></td>
				<td><?php echo Text::_($row->sportstype); ?></td>
				<td><?php echo Text::_('COM_JOOMLEAGUE_'.$row->project_type); ?></td>
				<td class="center">
				<?php if ($row->current_round): ?>
				<?php
					$imageTitle = Text::_('COM_JOOMLEAGUE_ADMIN_PROJECTS_GAMES_DETAILS');
					$image = HTMLHelper::_('image','administrator/components/com_joomleague/assets/images/icon-16-Matchdays.png',$imageTitle,'title= "'.$imageTitle.'"');
					echo HTMLHelper::link('index.php?option=com_joomleague&view=matches&pid[]='.$row->id.'&rid[]='.$row->current_round,$image);
				?>
				<?php endif; ?>
				</td>
				<td class="center"><?php echo $is_utc_converted; ?></td>
				<td class="center">
					<?php echo HTMLHelper::_('jgrid.published',$row->published,$i,'projects.');?>
				</td>
				<td class="center"><?php echo $row->id; ?></td>
			</tr>
			<?php endforeach; ?>
		</tbody>
		<tfoot>
			<tr>
				<td colspan='13'>
				<?php echo $this->pagination->getListFooter(); ?>
				</td>
			</tr>
		</tfoot>
	</table>
	<?php
	// Load the batch processing form.
	echo $this->loadTemplate('batch');
	?>
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />
	<?php echo HTMLHelper::_('form.token'); ?>
	</div>
</form>
