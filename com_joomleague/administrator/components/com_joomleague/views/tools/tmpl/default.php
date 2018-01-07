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

defined('_JEXEC') or die;
?>
<?php
	$p=1;
	echo HTMLHelper::_('bootstrap.startTabSet', 'tabs', array('active' => 'panel1'));
	echo HTMLHelper::_('bootstrap.addTab', 'tabs', 'panel'.$p++,'<span class="icon-database"></span>'.Text::_('COM_JOOMLEAGUE_TABS_TOOLS_TABLES', true));
?>
<form action="<?php echo $this->request_url; ?>" method="post" id="adminForm" name="adminForm">
<fieldset class="form-horizontal">
	<p class="alert alert-info">Here you can perfom several Table actions.</p>
</fieldset>
<table class="table tools table-hover">
	<thead>
		<tr>
			<th class="center">Select</th>
			<th>Table</th>
			<th class="center">CSV</th>
			<th class="center">SQL</th>
			<th class="center">Truncate</th>
		</tr>
	</thead>
	<tbody>
	<?php 
		$n = count($this->tables);
		foreach ($this->tables as $i => $row) :
	?>
		<tr class="j-main-container">

			<td class="center"><?php echo HTMLHelper::_('grid.id', $i, $row); ?></td>
			<td><?php echo $row; ?></td>
			<td class="center">
				<a class="exportcsv" id="csvexport" onclick="return listItemTask(<?php echo '\'cb'.$i.'\'' ?>,'tools.exporttablecsv')" href="javascript:void(0)"><?php echo HTMLHelper::_('image', 'com_joomleague/export_excel.png',null, NULL, true); ?></a>
			</td>
			<td class="center">
				<a class="exportsql" id="sqlexport" onclick="return listItemTask(<?php echo '\'cb'.$i.'\'' ?>,'tools.exporttablesql')" href="javascript:void(0)"><?php echo HTMLHelper::_('image', 'com_joomleague/sql.png', null, NULL, true); ?></a>
			</td>
			<td class="center">
				<a class="truncate" id="truncate" onclick="return listItemTask(<?php echo '\'cb'.$i.'\''; ?>,'tools.truncate')" href="javascript:void(0)"><?php echo HTMLHelper::_('image', 'com_joomleague/truncate.png', null, NULL, true); ?></a>
			</td>
		</tr>
	
	<?php endforeach; ?>
	</tbody>
	<tfoot><tr><td class="col-md-10"></td></tr></tfoot>
</table>
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="task" value="" />
	<?php echo HTMLHelper::_('form.token'); ?>
</form>
<?php  
	echo HTMLHelper::_('bootstrap.endTab'); 
	
	echo HTMLHelper::_('bootstrap.addTab', 'tabs', 'panel'.$p++,'<span class="icon-database"></span>'.Text::_('COM_JOOMLEAGUE_TABS_TOOLS_DB', true));
	echo $this->loadTemplate('db');
	echo HTMLHelper::_('bootstrap.endTab');
	echo HTMLHelper::_('bootstrap.addTab', 'tabs', 'panel'.$p++,'<span class="icon-database"></span>'.Text::_('COM_JOOMLEAGUE_TABS_TOOLS_OTHER', true));
	echo $this->loadTemplate('other');
	echo HTMLHelper::_('bootstrap.endTab');

	echo HTMLHelper::_('bootstrap.endTabSet');
	?>
