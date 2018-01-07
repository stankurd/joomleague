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
<form action="<?php echo $this->request_url; ?>" method="post" id="adminForm3" name="adminForm3">
	<fieldset class="form-horizontal">
	<p class="alert alert-info">Here you can perfom several actions</p>
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
						$link = Route::_( 'index.php?option=com_joomleague&task=tools.cleancache' );
						?>
						<a href="<?php echo $link; ?>" title="<?php echo Text::_( 'COM_JOOMLEAGUE_MDL_TOOLS_CLEANCACHE' ); ?>">
							<?php
							echo Text::_('COM_JOOMLEAGUE_MDL_TOOLS_CLEANCACHE');
							?>
						</a>
					</td>
					<td>
						<?php
						echo Text::_("COM_JOOMLEAGUE_MDL_TOOLS_CLEANCACHE_DESC");
						?>
					</td>
				</tr>		
				<tr>
					<td class="nowrap" valign="top">
						<?php
						$link = Route::_('index.php?option=com_joomleague&task=tools.removelanguagefiles');
						?>
						<a href="<?php echo $link; ?>" title="<?php echo Text::_( 'COM_JOOMLEAGUE_MDL_TOOLS_LANGUAGEFILES_REMOVE' ); ?>">
							<?php
							echo Text::_('COM_JOOMLEAGUE_MDL_TOOLS_LANGUAGEFILES_REMOVE');
							?>
						</a>
					</td>
					<td>
						<?php
						echo Text::_("COM_JOOMLEAGUE_MDL_TOOLS_LANGUAGEFILES_REMOVE_DESC");
						?>
					</td>
				</tr>
				<tr>
					<td class="nowrap" valign="top">
						<?php
						$link = Route::_('index.php?option=com_joomleague&task=tools.clearuserstate');
						?>
						<a href="<?php echo $link; ?>" title="<?php echo Text::_('Clear Userstate'); ?>">
							<?php
							echo Text::_('Clear userstate');
							?>
						</a>
					</td>
					<td>
						<?php
						echo Text::_("Clear userstate variables of Joomleague");
						?>
					</td>
				</tr>
			</tbody>
		</table>
	</div>

	<input type="hidden" name="task" value="" />
	<?php echo HTMLHelper::_('form.token'); ?>
</form>