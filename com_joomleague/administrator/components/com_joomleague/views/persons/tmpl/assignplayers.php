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
<script>

	function searchPerson(val)
	{
		var f = $('adminForm');
		if(f)
		{
			f.elements['search'].value=val;
			f.elements['search_mode'].value= 'matchfirst';
			f.submit();
		}
	}
</script>
<form action="<?php echo $this->request_url; ?>" method="post"
	id="adminForm" name="adminForm">
	<fieldset class="adminform">
		<legend>
			<?php
			switch($this->type)
			{
				case 2:
					{
						echo Text::sprintf('Assign Person(s) as Referee to project [%1$s]','<i>' . $this->prj_name . '</i>');
					}
					break;
				
				case 1:
					{
						echo Text::sprintf('Assign Person(s) as Staff to [%1$s] in project [%2$s]','<i>' . $this->team_name . '</i>',
								'<i>' . $this->prj_name . '</i>');
					}
					break;
				default:
				case 0:
					{
						echo Text::sprintf('Assign Person(s) as Player to [%1$s] in project [%2$s]','<i>' . $this->team_name . '</i>',
								'<i>' . $this->prj_name . '</i>');
					}
					break;
			}
			?>
		</legend>


		<div class="clearfix">
			<div class="btn-wrapper input-append pull-left">
		<?php echo Text::_('COM_JOOMLEAGUE_GLOBAL_FILTER' ); ?>:
		<input type="text" name="search" id="search"
					value="<?php echo $this->lists['search'];?>" class="text_area"
					onchange="document.adminForm.submit();" />
				<button class="btn hasTooltip" onclick="this.form.submit();">
					<span class="icon-search"></span>
				</button>
				<button class="btn hasTooltip"
					onclick="document.getElementById('search').value='';this.form.submit();">
					<span class="icon-remove"></span>
				</button>
			</div>
			<div class="btn-wrapper pull-right">
	<?php
	for($i = 65;$i < 91;$i ++)
	{
		printf("<a href=\"javascript:searchPerson('%s')\">%s</a>&nbsp;&nbsp;&nbsp;&nbsp;",chr($i),chr($i));
	}
	?>
	</div>
		</div>
		<div id="editcell">
			<table class="adminlist table table-striped">
				<thead>
					<tr>
						<th width="5" style="vertical-align: top;"><?php echo Text::_('COM_JOOMLEAGUE_GLOBAL_NUM'); ?></th>
						<th width="1%" class="center">
							<?php echo HTMLHelper::_('grid.checkall'); ?>
					</th>
						<th class="title" class="nowrap">
							<?php echo HTMLHelper::_('grid.sort','COM_JOOMLEAGUE_ADMIN_PERSONS_L_NAME','pl.lastname',$this->lists['order_Dir'],$this->lists['order']); ?>
						</th>
						<th class="title" class="nowrap">
							<?php echo HTMLHelper::_('grid.sort','COM_JOOMLEAGUE_ADMIN_PERSONS_F_NAME','pl.firstname',$this->lists['order_Dir'],$this->lists['order']); ?>
						</th>
						<th class="title" class="nowrap">
							<?php echo HTMLHelper::_('grid.sort','COM_JOOMLEAGUE_ADMIN_PERSONS_N_NAME','pl.nickname',$this->lists['order_Dir'],$this->lists['order']); ?>
						</th>
						<th class="title" class="nowrap">
							<?php echo HTMLHelper::_('grid.sort','COM_JOOMLEAGUE_ADMIN_PERSONS_INFO','pl.info',$this->lists['order_Dir'],$this->lists['order']); ?>
						</th>
						<th class="title" class="nowrap"><?php echo Text::_('COM_JOOMLEAGUE_ADMIN_PERSONS_IMAGE'); ?></th>
						<th class="title" class="nowrap">
							<?php echo HTMLHelper::_('grid.sort','COM_JOOMLEAGUE_ADMIN_PERSONS_BIRTHDAY','pl.birthday',$this->lists['order_Dir'],$this->lists['order']); ?>
						</th>
						<th class="title" class="nowrap">
							<?php echo HTMLHelper::_('grid.sort','COM_JOOMLEAGUE_ADMIN_PERSONS_NATIONALITY','pl.country',$this->lists['order_Dir'],$this->lists['order']); ?>
						</th>
						<th width="1%" class="nowrap">
							<?php echo HTMLHelper::_('grid.sort','COM_JOOMLEAGUE_GLOBAL_ID','pl.id',$this->lists['order_Dir'],$this->lists['order']); ?>
						</th>



					</tr>
				</thead>
				<tfoot>
					<tr>
						<td colspan="10"><?php echo $this->pagination->getListFooter(); ?></td>
					</tr>
				</tfoot>
				<tbody>
					<?php
					$n = count($this->items);
					foreach($this->items as $i=>$row)
					:
						if(($row->firstname != '!Unknown') && ($row->lastname != '!Player')) // Ghostplayer
						                                                                      // for
						                                                                      // match-events
						{
							$checked = HTMLHelper::_('grid.checkedout',$row,$i);
							?>
							<tr class="row<?php echo $i % 2; ?>">
						<td class="center"><?php echo $this->pagination->getRowOffset($i); ?></td>
						<td class="center"><?php echo $checked; ?></td>
						<td><?php echo $row->lastname; ?></td>
						<td><?php echo $row->firstname; ?></td>
						<td><?php echo $row->nickname; ?></td>

						<td><?php echo $row->info; ?></td>

						<td class="center">
									<?php
							if($row->picture == '')
							{
								$imageTitle = Text::_('COM_JOOMLEAGUE_ADMIN_PERSONS_NO_IMAGE');
								echo HTMLHelper::_('image','administrator/components/com_joomleague/assets/images/delete.png',$imageTitle,
										'title= "' . $imageTitle . '"');
							}
							elseif($row->picture == JoomleagueHelper::getDefaultPlaceholder("player"))
							{
								$imageTitle = Text::_('COM_JOOMLEAGUE_ADMIN_PERSONS_DEFAULT_IMAGE');
								echo HTMLHelper::_('image','administrator/components/com_joomleague/assets/images/information.png',$imageTitle,
										'title= "' . $imageTitle . '"');
							}
							elseif($row->picture == ! '')
							{
								$playerName = JoomleagueHelper::formatName(null,$row->firstname,$row->nickname,$row->lastname,0);
								echo JoomleagueHelper::getPictureThumb($row->picture,$playerName,0,21,4);
							}
							?>
								</td>
						<td class="nowrap" class="center"><?php echo JoomleagueHelper::convertDate($row->birthday); ?></td>
						<td class="nowrap" class="center"><?php echo Countries::getCountryFlag($row->country); ?></td>
						<td align="center"><?php echo $row->id; ?></td>
					</tr>
							<?php
						}
					endforeach
					;
					?>
				</tbody>
			</table>
		</div>
	</fieldset>
	<input type="hidden" name="search_mode"
		value="<?php echo $this->lists['search_mode']; ?>" id="search_mode" />
	<input type="hidden" name="task" value="" /> <input type="hidden"
		name="boxchecked" value="0" /> <input type="hidden" name="type"
		value="<?php echo $this->type; ?>" /> <input type="hidden"
		name="project_team_id" value="<?php echo $this->project_team_id; ?>" />
	<input type="hidden" name="filter_order"
		value="<?php echo $this->lists['order']; ?>" /> <input type="hidden"
		name="filter_order_Dir" value="" />
	<?php echo HTMLHelper::_('form.token'); ?>
</form>