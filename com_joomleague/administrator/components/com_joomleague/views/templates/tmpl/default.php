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
<script>
	function searchTemplate(val,key)
	{
		var f = $('adminForm');
		if(f){
			f.elements['search'].value=val;
			f.elements['search_mode'].value= 'matchfirst';
			f.submit();
		}
	}
</script>
	<fieldset class="adminform">
		<legend><?php echo JText::sprintf('COM_JOOMLEAGUE_ADMIN_TEMPLATES_LEGEND','<i>'.$this->project->name.'</i>'); ?></legend>
		<?php if ($this->project->master_template){echo $this->loadTemplate('import');} ?>
		<form action="index.php?option=com_joomleague&view=templates" method="post" id="adminForm" name="adminForm">
			<table class="table table-striped">
				<thead>
					<tr>
						<th width="1%" class="center">
							<?php echo HTMLHelper::_('grid.checkall'); ?>
						</th>
						<th width="20">&nbsp;</th>
						<th>
							<?php echo HTMLHelper::_('grid.sort','COM_JOOMLEAGUE_ADMIN_TEMPLATES_TEMPLATE','tmpl.template',$this->lists['order_Dir'],$this->lists['order']); ?>
						</th>
						<th>
							<?php echo HTMLHelper::_('grid.sort','COM_JOOMLEAGUE_ADMIN_TEMPLATES_DESCR','tmpl.template',$this->lists['order_Dir'],$this->lists['order']); ?>
						</th>
						<th>
							<?php echo JText::_('COM_JOOMLEAGUE_ADMIN_TEMPLATES_TYPE'); ?>
						</th>
						<th width="1%">
							<?php echo HTMLHelper::_('grid.sort','COM_JOOMLEAGUE_GLOBAL_ID','tmpl.id',$this->lists['order_Dir'],$this->lists['order']); ?>
						</th>
					</tr>
				</thead>
				<tbody>
					<?php
					$n = count($this->templates);
					foreach ($this->templates as $i => $row) :
						$row = $this->templates[$i];
						$link1=Route::_('index.php?option=com_joomleague&task=template.edit&cid[]='.$row->id);
						$checked=HTMLHelper::_('grid.checkedout',$row,$i);
						?>
						<tr class="row<?php echo $i % 2; ?>">
							<td class="center"><?php echo $checked; ?></td>
							<td><?php
								$imageFile='administrator/components/com_joomleague/assets/images/edit.png';
								$imageTitle=JText::_('COM_JOOMLEAGUE_ADMIN_TEMPLATES_EDIT_DETAILS');
								$imageParams='title= "'.$imageTitle.'"';
								$image=HTMLHelper::image($imageFile,$imageTitle,$imageParams);
								$linkParams='';
								echo HTMLHelper::link($link1,$image);
								?></td>
							<td><?php echo $row->template; ?></td>
							<td><?php echo JText::_($row->title); ?></td>
							<td><?php
								echo '<span style="font-weigth:bold; color:';
								echo ($row->isMaster) ? 'red; ">'.JText::_('COM_JOOMLEAGUE_ADMIN_TEMPLATES_MASTER') : 'green;">&nbsp;'.JText::_('COM_JOOMLEAGUE_ADMIN_TEMPLATES_INDEPENDENT');
								echo '</span>';
								?></td>
							<td class="center"><?php
								echo $row->id;
								?><input type='hidden' name='isMaster[<?php echo $row->id; ?>]' value='<?php echo $row->isMaster; ?>' /><?php ?></td>
						</tr>
						<?php endforeach; ?>
				</tbody>
			</table>
			
			<div class="center">
			<?php echo $this->pagination->getListFooter(); ?>
			</div>
			<input type="hidden" name="task" value="template.display" />
			<input type="hidden" name="boxchecked" value="0" />
			<input type="hidden" name="filter_order_Dir" value="" />
			<input type="hidden" name="filter_order" value="<?php echo $this->lists['order']; ?>" />
			<input type="hidden" name="search_mode" value="<?php echo $this->lists['search_mode'];?>" />
			<?php echo HTMLHelper::_('form.token'); ?>
		</form>
	</fieldset>