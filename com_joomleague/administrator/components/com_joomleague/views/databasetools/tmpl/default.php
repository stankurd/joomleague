<?php 
/**
 * Joomleague
 *
 * @copyright	Copyright (C) 2006-2015 joomleague.at. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @link		http://www.joomleague.at
 */

defined('_JEXEC') or die;

JHtml::_('behavior.tooltip');
?>
<div id="j-main-container" class="span10">
<form action="<?php echo $this->request_url; ?>" method="post" id="adminForm" name="adminForm">
	<div id="editcell">
		<table class="table table-striped">
			<thead>
				<tr>
					<th class="title" class="nowrap">
						<?php
						echo JText::_('COM_JOOMLEAGUE_ADMIN_DBTOOLS_TOOL');
						?>
					</th>
					<th class="title" class="nowrap">
						<?php
						echo JText::_('COM_JOOMLEAGUE_ADMIN_DBTOOLS_DESCR');
						?>
					</th>
				</tr>
			</thead>
			<tfoot>
				<tr>
					<td colspan="2">
						<?php
						echo "&nbsp;";
						?>
					</td>
				</tr>
			</tfoot>
			<tbody>
				<tr>
					<td class="nowrap" valign="top">
						<?php
						$link = JRoute::_('index.php?option=com_joomleague&task=databasetool.optimize');
						?>
						<a href="<?php echo $link; ?>" title="<?php echo JText::_('COM_JOOMLEAGUE_ADMIN_DBTOOLS_OPTIMIZE2'); ?>">
							<?php
							echo JText::_('COM_JOOMLEAGUE_ADMIN_DBTOOLS_OPTIMIZE');
							?>
						</a>
					</td>
					<td>
						<?php
						echo JText::_("COM_JOOMLEAGUE_ADMIN_DBTOOLS_OPTIMIZE_DESCR");
						?>
					</td>
				</tr>

				<tr>
					<td class="nowrap" valign="top">
						<?php
						$link = JRoute::_('index.php?option=com_joomleague&task=databasetool.repair');
						?>
						<a href="<?php echo $link; ?>" title="<?php echo JText::_('COM_JOOMLEAGUE_ADMIN_DBTOOLS_REPAIR2'); ?>">
							<?php
							echo JText::_('COM_JOOMLEAGUE_ADMIN_DBTOOLS_REPAIR');
							?>
						</a>
					</td>
					<td>
						<?php
						echo JText::_("COM_JOOMLEAGUE_ADMIN_DBTOOLS_REPAIR_DESCR");
						?>
					</td>
				</tr>

			</tbody>
		</table>
	</div>

	<input type="hidden" name="task" value="databasetool.execute" />
	<?php echo JHtml::_('form.token'); ?>
</form>
</div>