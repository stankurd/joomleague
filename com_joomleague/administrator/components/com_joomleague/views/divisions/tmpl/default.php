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
?>
<form action="<?php echo Route::_('index.php?option=com_joomleague&view=divisions'); ?>" method="post" id="adminForm" name="adminForm">
	<div id="j-main-container" class="j-main-container">
	<fieldset class="form-horizontal">
		<legend>
		<?php
			echo Text::sprintf('COM_JOOMLEAGUE_ADMIN_DIVS_TITLE2','<i>'.$this->project->name.'</i>');
		?>
		</legend>
	<div class="clearfix">	
	<?php
	// Search tools bar
	echo LayoutHelper::render('searchtools.default',array('view' => $this),Uri::root().'administrator/components/com_joomleague/layouts');
	?>
	<div class="btn-wrapper pull-right">
		<?php echo $this->lists['state']; ?>
	</div>
	</div>
	<table class="table table-striped" id="divisionList">
		<thead>
			<tr>
				<th width="1%" class="center">
					<?php echo HTMLHelper::_('grid.checkall'); ?>
				</th>
				<th width="20">&nbsp;</th>
				<th>
					<?php echo HTMLHelper::_('searchtools.sort','COM_JOOMLEAGUE_ADMIN_DIVS_NAME','a.name',$listDirn, $listOrder);?>
				</th>
				<th>
					<?php echo HTMLHelper::_('searchtools.sort','COM_JOOMLEAGUE_ADMIN_DIVS_S_NAME','a.shortname',$listDirn, $listOrder);?>
				</th>
				<th>
					<?php echo HTMLHelper::_('searchtools.sort','COM_JOOMLEAGUE_ADMIN_DIVS_PARENT_NAME','parent_name',$listDirn, $listOrder);?>
				</th>
				<th width="1%">
					<?php echo Text::_('COM_JOOMLEAGUE_GLOBAL_PUBLISHED'); ?>
				</th>
				<th width="1%">
					<?php echo HTMLHelper::_('searchtools.sort','COM_JOOMLEAGUE_GLOBAL_ID','a.id',$listDirn, $listOrder);?>
				</th>
			</tr>
		</thead>
		<tbody>
		<?php
		$n = count($this->items);
		foreach($this->items as $i=>$row) :
			$link = Route::_('index.php?option=com_joomleague&task=division.edit&id='.$row->id);
			$checked = HTMLHelper::_('grid.checkedout',$row,$i);
			$published = HTMLHelper::_('jgrid.published',$row->published,$i,'divisions.');
		?>
			<tr class="row<?php echo $i % 2; ?>">
				<td class="center"><?php echo $checked; ?></td>
				<?php
				if(JLTable::_isCheckedOut($user->get('id'),$row->checked_out))
				{
					$inputappend = ' disabled="disabled"';
				?>
				<td style="text-align: center;">&nbsp;</td>
				<?php
				}
				else
				{
					$inputappend = '';
				?>
				<td style="text-align: center;"><a href="<?php echo $link; ?>">
				<?php
					$imageTitle = Text::_('COM_JOOMLEAGUE_ADMIN_DIVS_EDIT_DETAILS');
					echo HTMLHelper::_('image','administrator/components/com_joomleague/assets/images/edit.png',$imageTitle,'title= "' . $imageTitle . '"');
				?></a>
				</td>
				<?php
				}
				?>
				<td><?php echo $row->name;?></td>
				<td><?php echo $row->shortname;?></td>
				<td><?php echo $row->parent_name; ?></td>
				<td class="center"><?php echo $published; ?></td>
				<td class="center"><?php echo $row->id; ?></td>
			</tr>
		<?php endforeach; ?>
		</tbody>
		<tfoot>
			<tr>
				<td colspan="8">
				<?php
					echo $this->pagination->getListFooter();
				?>
				</td>
			</tr>
		</tfoot>
	</table>
	</fieldset>
	<div/>
	<!-- input fields -->
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />
	<?php echo HTMLHelper::_('form.token'); ?>
</form>
