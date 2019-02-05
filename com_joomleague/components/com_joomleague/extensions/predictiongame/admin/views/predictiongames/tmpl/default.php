<?php 
/**
* @copyright	Copyright (C) 2007-2012 JoomLeague.net. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* Joomla! is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;

defined('_JEXEC') or die('Restricted access');
JLoader::register('JLTable',JPATH_SITE.'/administrator/components/com_joomleague/tables/jltables.php');
echo '<br />#<pre>'; print_r( $this->items ); echo '</pre>#<br />';
HTMLHelper::_('behavior.tooltip');
HTMLHelper::_('behavior.modal');
//Ordering allowed ?
$app = Factory::getApplication();
$user = Factory::getUser();
$userId = $user->get('id');
$listOrder = $this->escape($this->state->get('list.ordering'));
$listDirn = $this->escape($this->state->get('list.direction'));
$archived = $this->state->get('filter.published') == 2 ? true : false;
$trashed = $this->state->get('filter.published') == - 2 ? true : false;
$saveOrder = $listOrder == 'pre.ordering';
$ordering = ($this->lists['order']=='pre.ordering');

?>
<form action="<?php echo Route::_('index.php?option=com_joomleague&view=predictiongames'); ?>" method="post" id="adminForm">
	<table>
		<tr>
			<td align="left" width="100%">
				<?php
				if ( $this->dPredictionID == 0 )
				{
				?>
					<?php
					echo Text::_('COM_JOOMLEAGUE_GLOBAL_FILTER'); ?>: <input	type="text" name="search" id="search"
														value="<?php echo $this->lists['search'];?>" class="text_area"
														onchange="document.adminForm.submit();" />
					<button onclick="this.form.submit();">
						<?php
						echo Text::_('COM_JOOMLEAGUE_GLOBAL_GO');
						?>
					</button>
					<button onclick="document.getElementById('search').value='';this.form.submit();">
						<?php
						echo Text::_('COM_JOOMLEAGUE_GLOBAL_RESET');
						?>
					</button>
					<?php
					}
					else {echo '&nbsp';}
					?>
			</td>
			<td nowrap='nowrap' align='right'>
				<?php
				echo $this->lists['predictions'] . '&nbsp;&nbsp;';
				?>
			</td>
			<?php
			if ($this->dPredictionID==0)
			{
			?>
				<td nowrap='nowrap'>
					<?php
					echo $this->lists['state'];
					?>
				</td>
			<?php
			}
			?>
		</tr>
	</table>
	<div id='editcell'>
		<?php
		if ($this->dPredictionID > 0)
		{
			?>
			<fieldset class="adminform">
				<legend>
					<?php
					echo Text::sprintf('COM_JOOMLEAGUE_ADMIN_PGAMES_TITLE2','<i>'.$this->items[0]->name.'</i>');
					?>
				</legend>
			<?php
		}
		?>
		<table width='100%' class='adminlist'>
			<thead>
				<tr>
					<th width='10'><?php echo Text::_('COM_JOOMLEAGUE_GLOBAL_NUM'); ?></th>
					<th width='20'>
					<?php echo HTMLHelper::_('grid.checkall'); ?>
					</th>
					<th width='20'>&nbsp;</th>
					<th class='title' nowrap='nowrap'>
						<?php
						echo HTMLHelper::_('grid.sort',Text::_('COM_JOOMLEAGUE_ADMIN_PGAMES_NAME'),'pre.name',$listDirn, $listOrder);
						?>
					</th>
					<th class='title' nowrap='nowrap' colspan='2'><?php echo Text::_('COM_JOOMLEAGUE_ADMIN_PGAMES_PROJ_COUNT'); ?></th>
					<th class='title' nowrap='nowrap' colspan='2'><?php echo Text::_('COM_JOOMLEAGUE_ADMIN_PGAMES_ADMIN_COUNT'); ?></th>
					<th class='title' width='5%' nowrap='nowrap'>
						<?php
						echo HTMLHelper::_('grid.sort',Text::_('COM_JOOMLEAGUE_GLOBAL_PUBLISHED'),'pre.published',$this->lists['order_Dir'],$this->lists['order']);
						?>
					</th>
					<th class='title' width='20' nowrap='nowrap'>
						<?php
						echo HTMLHelper::_('grid.sort',Text::_('JGRID_HEADING_ID'),'pre.id',$this->lists['order_Dir'],$this->lists['order']);
						?>
					</th>
				</tr>
			</thead>
			<?php
			if ($this->dPredictionID==0)
			{
				?><tfoot><tr><td colspan='10'><?php echo $this->pagination->getListFooter(); ?></td></tr></tfoot><?php
			}
			?>
			<tbody>
			<?php
			$k = 0;
			for ($i=0, $n=count($this->items); $i < $n; $i++)
			{
				$row			= $this->items[$i];
				$pred_projects	= $this->getModel()->getChilds($row->id);
				$pred_admins	= $this->getModel()->getAdmins($row->id);
				$checked		= HTMLHelper::_('grid.checkedout',$row,$i);
				//$published		= HTMLHelper::_('grid.published',$row,$i);
                $published  = HTMLHelper::_('grid.published',$row,$i,'tick.png','publish_x.png','predictiongame.');
        
				$link			= Route::_('index.php?option=com_joomleague&task=predictiongame.edit&cid[]=' . $row->id);
				?>
				<tr class='<?php echo "row$k"; ?>'>
					<td style='text-align:right; '><?php echo $this->pagination->getRowOffset( $i ); ?></td>
					<td><?php echo $checked; ?></td>
					<td style='text-align:center; '><?php
						/**if ( JLTable::isCheckedOut($user->get ('id'), $row->checked_out ) )
						{
							$inputappend = " disabled='disabled'";
							?>&nbsp;<?php
						}
						else*/
						{
							?><a href='<?php echo $link; ?>'>
									<img	src='<?php echo Uri::root(); ?>administrator/components/com_joomleague/assets/images/edit.png'
											border='0'
											alt='<?php echo Text::_( 'COM_JOOMLEAGUE_ADMIN_PGAMES_EDIT_DETAILS' ); ?>'
											title='<?php echo Text::_( 'COM_JOOMLEAGUE_ADMIN_PGAMES_EDIT_DETAILS' ); ?>'>
								</a>
							<?php
						}
					?></td>
					<td>
						<?php
						/**if ( JLTable::isCheckedOut($user->get('id'), $row->checked_out ) )
						{
							echo $row->name;
						}
						else*/
						{
						?><a	href="#"
								onclick="document.adminForm.prediction_id.value='<?php echo $row->id; ?>';document.adminForm.submit();"
								title="<?php echo Text::sprintf(	'COM_JOOMLEAGUE_ADMIN_PGAMES_SELECT_PGAME',
																	$row->name ); ?>">
								<?php
								echo $row->name;
								?>
							</a>
						<?php
						}
						?>
					</td>
					<td style='text-align:center; ' colspan='2'><?php echo count( $pred_projects ); ?></td>
					<td style='text-align:center; ' colspan='2'><?php echo count( $pred_admins ); ?></td>
					<td style='text-align:center; '><?php echo $published; ?></td>
					<td style='text-align:center; '><?php echo $row->id; ?></td>
				</tr>
				<?php
				$k = 1 - $k;
			}

			if ( $this->dPredictionID > 0 )
			{
			echo '<br />#<pre>'; print_r( $this->predictionProjects ); echo '</pre>#<br />';
			echo '<br />#<pre>'; print_r( $this->predictionAdmins ); echo '</pre>#<br />';
				?>
				<thead>
					<tr>
						<th>&nbsp;</th>
						<th><?php echo Text::_( 'NUM' ); ?></th>
						<th>&nbsp;</th>
						<th class='title'><?php echo Text::_( 'COM_JOOMLEAGUE_ADMIN_PGAMES_PROJ_NAME' ); ?></th>
						<th class='title'><?php echo Text::_( 'COM_JOOMLEAGUE_ADMIN_PGAMES_MODE' ); ?></th>
						<th class='title'><?php echo Text::_( 'COM_JOOMLEAGUE_ADMIN_PGAMES_OVERVIEW' ); ?></th>
						<th class='title'><?php echo Text::_( 'COM_JOOMLEAGUE_ADMIN_PGAMES_JOKER' ); ?></th>
						<th class='title'><?php echo Text::_( 'COM_JOOMLEAGUE_ADMIN_PGAMES_CHAMP' ); ?></th>
						<th class='title'><?php echo Text::_( 'JL_GLOBAL_PUBLISHED' ); ?></th>
						<th class='title'><?php echo Text::_( 'JL_GLOBAL_ID' ); ?></th>
					</tr>
				</thead>
				<?php
				$ii	= 0;
				$k	= 1;
				if ( count( $this->predictionProjects ) > 0 )
				{
					foreach ( $this->predictionProjects AS $pred_project )
					{
						//echo '<br />#<pre>'; print_r( $pred_project ); echo '</pre>#<br />';
						$link = Route::_(	'index.php?option=com_joomleague&' .
											'' .
											'task=predictiongame.predsettings&cid[]=' . $pred_project['id'] );
						?>
						<tr class='<?php echo "row$k"; ?>'>
							<td style='text-align:right; '>&nbsp;</td>
							<td style='text-align:center; '><?php echo $ii+1; ?></td>
							<td style='text-align:center; '>&nbsp;</td>
							<td>
								<a	href='<?php echo $link; ?>' title='<?php echo Text::_( 'COM_JOOMLEAGUE_ADMIN_PGAMES_EDIT_SETTINGS' ); ?>' />
									<?php echo $pred_project['project_name']; ?>
								</a>
							</td>
							<td style='text-align:center; '><?php
								if ( $pred_project['mode'] == '0' )
								{
									echo Text::_( 'COM_JOOMLEAGUE_ADMIN_PGAMES_STANDARD' );
								}
								else
								{
									echo Text::_( 'COM_JOOMLEAGUE_ADMIN_PGAMES_TOTO' );
								}
								?></td>
							<td style='text-align:center; '><?php
								if ( $pred_project['overview'] == '0' )
								{
									echo Text::_( 'COM_JOOMLEAGUE_ADMIN_PGAMES_FULL_SEASON' );
								}
								else
								{
									echo Text::_( 'COM_JOOMLEAGUE_ADMIN_PGAMES_HALF_SEASON' );
								}
								?></td>
							<td style='text-align:center; '><?php
								if ( $pred_project['joker'] == '1' )
								{
									if ($pred_project['joker_limit']==0){$maxJ=Text::_('UNLIMITED');}else{$maxJ=$pred_project['joker_limit'];}
									$imageTitle = Text::sprintf( 'COM_JOOMLEAGUE_ADMIN_PGAMES_MAX_JOKER', $maxJ );
									$imageFile = 'administrator/components/com_joomleague/assets/images/ok.png';
								}
								else
								{
									$imageTitle = Text::_( 'COM_JOOMLEAGUE_ADMIN_PGAMES_NO_JOKER' );
									$imageFile = 'administrator/components/com_joomleague/assets/images/delete.png';
								}
								echo HTMLHelper::_(	'image', $imageFile, $imageTitle, 'title= "' . $imageTitle . '"' );
								?></td>
							<td style='text-align:center; '><?php
								if ( $pred_project['champ'] == '1' )
								{
									$imageTitle = Text::_( 'COM_JOOMLEAGUE_ADMIN_PGAMES_PICK_CHAMP' );
									$imageFile = 'administrator/components/com_joomleague/assets/images/ok.png';
								}
								else
								{
									$imageTitle = Text::_( 'COM_JOOMLEAGUE_ADMIN_PGAMES_NO_PICK_CHAMP' );
									$imageFile = 'administrator/components/com_joomleague/assets/images/delete.png';
								}
								echo HTMLHelper::_(	'image', $imageFile, $imageTitle, 'title= "' . $imageTitle . '"' );
								?></td>
							<td style='text-align:center; ' >
								<?php
									if ( $pred_project['published'] == '1' )
									{
										$imageTitle = Text::_( 'COM_JOOMLEAGUE_ADMIN_PGAMES_PUBLISHED' );
										$imageFile = 'administrator/components/com_joomleague/assets/images/ok.png';
									}
									else
									{
										$imageTitle = Text::_( 'COM_JOOMLEAGUE_ADMIN_PGAMES_UNPUBLISHED' );
										$imageFile = 'administrator/components/com_joomleague/assets/images/delete.png';
									}
									echo HTMLHelper::_(	'image', $imageFile, $imageTitle, 'title= "' . $imageTitle . '"' );
								?>
							</td>
							<td style='text-align:center; ' nowrap='nowrap'><?php echo $pred_project['project_id']; ?></td>
						</tr>
						<?php
						$k = 1 - $k;
						$ii++;
					}
				}
			}
			?>
			</tbody>
		</table>
		<?php
		if ( $this->dPredictionID > 0 )
		{
			?>
			</fieldset>
			<?php
		}
		?>
	</div>

	
	<input type="hidden" name="view"				value="predictiongames" />
	<input type='hidden' name='task'				value='predictiongame.display' />
	<input type='hidden' name='boxchecked'			value='0' />
	<input type='hidden' name='filter_order'		value='<?php echo $this->lists['order']; ?>' />
	<input type='hidden' name='filter_order_Dir'	value='' />
	<?php echo HTMLHelper::_( 'form.token' ); ?>
</form>