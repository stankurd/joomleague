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
<form action="<?php echo $this->request_url; ?>" method="post" id="adminForm2" name="adminForm2">
	<fieldset class="form-horizontal">
	<p class="alert alert-info">Here you can perfom several Database actions</p>
</fieldset>
	
	<div id="editcell">
		<table class="adminlist table table-striped">
			<thead>
				<tr>
					<th class="title" class="nowrap">
						<?php
						echo Text::_( 'COM_JOOMLEAGUE_ADMIN_DBTOOLS_TOOL' );
						?>
					</th>
					<th class="title" class="nowrap">
						<?php
						echo Text::_( 'COM_JOOMLEAGUE_ADMIN_DBTOOLS_DESCR' );
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
						$link = Route::_( 'index.php?option=com_joomleague&task=tools.optimize' );
						?>
						<a href="<?php echo $link; ?>" title="<?php echo Text::_( 'COM_JOOMLEAGUE_ADMIN_DBTOOLS_OPTIMIZE2' ); ?>">
							<?php
							echo Text::_( 'COM_JOOMLEAGUE_ADMIN_DBTOOLS_OPTIMIZE' );
							?>
						</a>
					</td>
					<td>
						<?php
						echo Text::_( "COM_JOOMLEAGUE_ADMIN_DBTOOLS_OPTIMIZE_DESCR" );
						?>
					</td>
				</tr>

				<tr>
					<td class="nowrap" valign="top">
						<?php
						$link = Route::_( 'index.php?option=com_joomleague&task=tools.repair' );
						?>
						<a href="<?php echo $link; ?>" title="<?php echo Text::_( 'COM_JOOMLEAGUE_ADMIN_DBTOOLS_REPAIR2' ); ?>">
							<?php
							echo Text::_( 'COM_JOOMLEAGUE_ADMIN_DBTOOLS_REPAIR' );
							?>
						</a>
					</td>
					<td>
						<?php
						echo Text::_( "COM_JOOMLEAGUE_ADMIN_DBTOOLS_REPAIR_DESCR" );
						?>
					</td>
				</tr>
				
			</tbody>
		</table>
	</div>

	<input type="hidden" name="task" value="" />
	<?php echo HTMLHelper::_('form.token'); ?>
</form>