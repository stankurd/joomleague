<?php 
/**
 * Joomleague
 *
 * @copyright	Copyright (C) 2006-2015 joomleague.at. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @link		http://www.joomleague.at
 */

use Joomla\CMS\Date\Date;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;

defined('_JEXEC') or die;
HTMLHelper::_('behavior.framework');
HTMLHelper::_('formbehavior.chosen', 'select');

?>
<style>
fieldset input,
fieldset textarea,
fieldset select,
fieldset img,
fieldset button {
    float: none;
    margin: 5px 5px 5px 0;
    width: auto;
}
</style>
	<fieldset class="form-horizontal">
		<legend><?php echo Text::sprintf('COM_JOOMLEAGUE_ADMIN_MATCHES_TITLE2','<i>'.$this->round->name.'</i>','<i>'.$this->project->name.'</i>'); ?></legend>
		<p class="note"><?php echo Text::sprintf('COM_JOOMLEAGUE_ADMIN_MATCHES_ALL_TIMES_IN_S_TZ', $this->project->timezone); ?></p>
		<?php echo $this->loadTemplate('roundselect'); ?>
		<!-- Start games list -->
		<form action="<?php echo $this->request_url; ?>" method="post" id="adminForm" name="adminForm">
			<?php
			$colspan=($this->project->allow_add_time) ? 16 : 15;
			?>
			<table class="table table-striped">
				<thead>
					<tr>
						<th width="5" ><?php echo count($this->items).'/'.$this->pagination->total; ?></th>
						<th width="1%" class="center">
							<?php echo HTMLHelper::_('grid.checkall'); ?>
						</th>
						<th width="20" >&nbsp;</th>
						<th width="20" >
							<?php echo HTMLHelper::_('grid.sort','COM_JOOMLEAGUE_ADMIN_MATCHES_MATCHNR','mc.match_number',$this->lists['order_Dir'],$this->lists['order']); ?>
						</th>
						<th class="title" >
							<?php echo HTMLHelper::_('grid.sort','COM_JOOMLEAGUE_ADMIN_MATCHES_DATE','mc.match_date',$this->lists['order_Dir'],$this->lists['order']); ?>
						</th>
						<th class="title" ><?php echo Text::_('COM_JOOMLEAGUE_ADMIN_MATCHES_TIME'); ?></th>
						<th class="title" ><?php echo Text::_('COM_JOOMLEAGUE_ADMIN_MATCH_F_MD_ATT' ); ?></th>
						<?php
							if($this->project->project_type=='DIVISIONS_LEAGUE') {
								$colspan++;
						?>
						<th >
							<?php
								echo HTMLHelper::_('grid.sort','COM_JOOMLEAGUE_ADMIN_MATCHES_DIVISION','divhome.id',$this->lists['order_Dir'],$this->lists['order']);
								echo '<br>'.HTMLHelper::_(	'select.genericlist',
													$this->lists['divisions'],
													'division',
													'class="inputbox" size="1" onchange="window.location.href=window.location.href.split(\'&division=\')[0]+\'&division=\'+this.value"',
													'value','text', $this->division);

							?>
						</th>
						<?php
							}
						?>
						<th class="title" ><?php echo Text::_('COM_JOOMLEAGUE_ADMIN_MATCHES_HOME_TEAM'); ?></th>
						<th class="title" ><?php echo Text::_('COM_JOOMLEAGUE_ADMIN_MATCHES_AWAY_TEAM'); ?></th>
						<th style="  "><?php echo Text::_('COM_JOOMLEAGUE_ADMIN_MATCHES_RESULT'); ?></th>
						<?php
						if ($this->project->allow_add_time)
						{
							?>
							<th style="text-align:center;  "><?php echo Text::_('COM_JOOMLEAGUE_ADMIN_MATCHES_RESULT_TYPE'); ?></th>
							<?php
						}
						?>
						<th class="title" ><?php echo Text::_('COM_JOOMLEAGUE_ADMIN_MATCHES_EVENTS'); ?></th>
						<th class="title" ><?php echo Text::_('COM_JOOMLEAGUE_ADMIN_MATCHES_STATISTICS'); ?></th>
						<th class="title" width="40px" ><?php echo Text::_('COM_JOOMLEAGUE_ADMIN_MATCHES_REFEREE'); ?></th>
						<th width="1%" ><?php echo Text::_('COM_JOOMLEAGUE_GLOBAL_PUBLISHED'); ?></th>
						<th width="1%" class="title" >
							<?php echo HTMLHelper::_('grid.sort','COM_JOOMLEAGUE_GLOBAL_ID','mc.id',$this->lists['order_Dir'],$this->lists['order']); ?>
						</th>
					</tr>
				</thead>
				<tfoot><tr><td colspan="<?php echo $colspan; ?>"><?php echo $this->pagination->getListFooter(); ?></td></tr></tfoot>
				<tbody>
					<?php
					$n = count($this->items);
					foreach ($this->items as $i => $row) :
						$link = Route::_('index.php?option=com_joomleague&task=match.edit&id='.$row->id);
						$checked	= HTMLHelper::_('grid.checkedout',$row,$i,'id');
						$published	= HTMLHelper::_('jgrid.published', $row->published, $i, 'matches.');
						?>
						<tr class="row<?php echo $i % 2; ?>">
						<?php if(($row->cancel)>0)
								{
									$style="text-align:center;  background-color: #FF9999;";
								}
								else
								{
									$style="text-align:center; ";
								}
								?>
							<td style="<?php echo $style;?>">
								<?php
								echo $this->pagination->getRowOffset($i);
								?>
							</td>
							<td class="center">
								<?php
								echo $checked;
								?>
							</td>
							<td class="center">
								<a href="<?php echo $link; ?>">
								<?php
									$imageTitle = Text::_('COM_JOOMLEAGUE_ADMIN_LEAGUES_EDIT_DETAILS');
									echo HTMLHelper::_('image','administrator/components/com_joomleague/assets/images/edit.png',$imageTitle,'title= "' . $imageTitle . '"');
								?>
								</a>
							</td>
							<td class="center">
								<input onchange="document.getElementById('cb<?php echo $i; ?>').checked=true" type="text" name="match_number<?php echo $row->id; ?>"
										value="<?php echo $row->match_number; ?>" size="6" tabindex="1" class="inputbox" />
							</td>
							<td class="center">
								<?php
								$date = JoomleagueHelper::getMatchDate($row, Text::_('COM_JOOMLEAGUE_ADMIN_MATCHES_DATE_FORMAT'));
								echo HTMLHelper::calendar(	$date, 'match_date'.$row->id, 'match_date'.$row->id,
														Text::_('COM_JOOMLEAGUE_ADMIN_MATCHES_DATE_FORMAT_CAL'),
														'size="9"  tabindex="2" ondblclick="copyValue(\'match_date\')"
														 onchange="document.getElementById(\'cb'.$i.'\').checked=true"');
								?>
							</td>
							<td class="left">
								<?php
								$time = JoomleagueHelper::getMatchTime($row);
								?>
								<input ondblclick="copyValue('match_time')" onchange="document.getElementById('cb<?php echo $i; ?>').checked=true" type="text" name="match_time<?php echo $row->id; ?>"
										value="<?php echo $time; ?>" size="4" maxlength="5" tabindex="3" class="inputbox" />

								<a	href="javascript:void(0)"
									onclick="switchMenu('present<?php echo $row->id; ?>')">&nbsp;
									<?php echo HTMLHelper::image('administrator/components/com_joomleague/assets/images/arrow_open.png',
															Text::_('COM_JOOMLEAGUE_ADMIN_MATCHES_PRESENT'),
															'title= "'.Text::_('COM_JOOMLEAGUE_ADMIN_MATCHES_PRESENT').'"');
									?>
								</a><br />
								<span id="present<?php echo $row->id; ?>" style="display: none">
									<br />
										<input ondblclick="copyValue('time_present')" onchange="document.getElementById('cb<?php echo $i; ?>').checked=true" type="text" name="time_present<?php echo $row->id; ?>"
												value="<?php echo $row->time_present; ?>" size="4" maxlength="5" tabindex="3" class="inputbox" title="<?php echo Text::_('COM_JOOMLEAGUE_ADMIN_MATCHES_PRESENT'); ?>" />
										<?php echo Text::_('COM_JOOMLEAGUE_ADMIN_MATCHES_PRESENT_SHORT'); ?>
								</span>
							</td>
							<td class="center">
								<input onchange="document.getElementById('cb<?php echo $i; ?>').checked=true" type="text" name="crowd<?php echo $row->id; ?>"
										value="<?php echo $row->crowd; ?>" size="4" maxlength="5" tabindex="4" class="inputbox" />
							</td>
							<?php
								if($this->project->project_type=='DIVISIONS_LEAGUE') {
							?>
							<td class="center">
								<?php echo $row->divhome; ?>
							</td>
							<?php
								}

								// Ask which prefill method should be used - only if there is no line-up set already
								if(($row->homeplayers_count>0 || $row->homestaff_count>0 ) && $this->prefill = 4) {
									$prefill = 0;
								} else {
									$prefill = $this->prefill;
								}
							?>
							<td class="right" >
								<a	onclick="handleRosterIconClick(<?php echo $prefill; ?>, this, '<?php echo Text::_('COM_JOOMLEAGUE_ADMIN_MATCH_PREFILL_LAST_ROSTER_ALERT'); ?>', '<?php echo Text::_('COM_JOOMLEAGUE_ADMIN_MATCH_PREFILL_PROJECTTEAM_PLAYERS_ALERT')?>')"
									href="<?php echo Route::_('index.php?option=com_joomleague&view=match&layout=editlineup&match_id='.$row->id.'&team_id='.$row->projectteam1_id.'&prefill='); ?>"
									 title="<?php echo Text::_('COM_JOOMLEAGUE_ADMIN_MATCHES_EDIT_LINEUP_HOME'); ?>">
									 <?php
									 if($row->homeplayers_count==0 || $row->homestaff_count==0 ) {
									 	$image = 'players_add.png';
									 } else {
									 	$image = 'players_edit.png';
									 }
									 $title=  ' '.Text::_('COM_JOOMLEAGUE_F_PLAYERS').': ' .$row->homeplayers_count. ', ' .
													 ' '.Text::_('COM_JOOMLEAGUE_F_TEAM_STAFF').': ' .$row->homestaff_count . ' ';

									echo '<sub>'.$row->homeplayers_count.'</sub> ';


									 echo HTMLHelper::image('administrator/components/com_joomleague/assets/images/'.$image,
													 Text::_('COM_JOOMLEAGUE_ADMIN_MATCHES_EDIT_LINEUP_HOME'),
													 'title= "' .$title. '"');

									 echo '<sub>'.$row->homestaff_count.'</sub> ';
									 									 ?>
								</a>
								<?php
								$append='';
								if ($row->projectteam1_id == 0)
								{
									$append=' style="background-color:#bbffff"';
								}
								$append.=' onchange="document.getElementById(\'cb'.$i.'\').checked=true" ';
								echo HTMLHelper::_(	'select.genericlist',$this->lists['teams_'+$row->divhomeid],'projectteam1_id'.$row->id,
												'class="inputbox select-hometeam" size="1"'.$append,'value','text',$row->projectteam1_id);
								?>
							</td>
							<td class="left" >
								<?php
								$append='';
								if ($row->projectteam2_id == 0)
								{
									$append=' style="background-color:#bbffff"';
								}
								$append.=' onchange="document.getElementById(\'cb'.$i.'\').checked=true" ';
								echo HTMLHelper::_(	'select.genericlist',$this->lists['teams_'+$row->divhomeid],'projectteam2_id'.$row->id,
												'class="inputbox select-awayteam" size="1"'.$append,'value','text',$row->projectteam2_id);

								// Ask which prefill method should be used - only if there is no line-up set already
								if(($row->awayplayers_count>0 || $row->awaystaff_count>0) && $this->prefill = 4) {
									$prefill = 0;
								} else {
									$prefill = $this->prefill;
								}

								?>
								<a	onclick="handleRosterIconClick(<?php echo $prefill; ?>, this, '<?php echo Text::_('COM_JOOMLEAGUE_ADMIN_MATCH_PREFILL_LAST_ROSTER_ALERT'); ?>', '<?php echo Text::_('COM_JOOMLEAGUE_ADMIN_MATCH_PREFILL_PROJECTTEAM_PLAYERS_ALERT')?>')"
									href="<?php echo Route::_('index.php?option=com_joomleague&view=match&layout=editlineup&match_id='.$row->id.'&team_id='.$row->projectteam2_id.'&prefill=');?>"
									title="<?php echo Text::_('COM_JOOMLEAGUE_ADMIN_MATCHES_EDIT_LINEUP_AWAY'); ?>">
									
									 <?php
									 if($row->awayplayers_count==0 || $row->awaystaff_count==0 ) {
									 	$image = 'players_add.png';
									 } else {
									 	$image = 'players_edit.png';
									 }
									 $title=' '.Text::_('COM_JOOMLEAGUE_F_PLAYERS').': ' .$row->awayplayers_count. ', ' .
													 ' '.Text::_('COM_JOOMLEAGUE_F_TEAM_STAFF').': ' .$row->awaystaff_count;

									 echo '<sub>'.$row->awayplayers_count.'</sub> ';
									 echo HTMLHelper::image('administrator/components/com_joomleague/assets/images/'.$image,
													 Text::_('COM_JOOMLEAGUE_ADMIN_MATCHES_EDIT_LINEUP_AWAY'),
													 'title= "' .$title. '"');
									 echo '<sub>'.$row->awaystaff_count.'</sub> ';

									  ?>
								</a>
							</td>
							<td class="center">
								<input onchange="document.getElementById('cb<?php echo $i; ?>').checked=true" <?php if($row->alt_decision==1) echo "class=\"subsequentdecision\" title=\"".Text::_('COM_JOOMLEAGUE_ADMIN_MATCHES_SUB_DECISION')."\"" ?> type="text" name="team1_result<?php echo $row->id; ?>"
										value="<?php echo $row->team1_result; ?>" size="2" tabindex="5" class="inputbox" /> :
								<input onchange="document.getElementById('cb<?php echo $i; ?>').checked=true" <?php if($row->alt_decision==1) echo "class=\"subsequentdecision\" title=\"".Text::_('COM_JOOMLEAGUE_ADMIN_MATCHES_SUB_DECISION')."\"" ?> type="text" name="team2_result<?php echo $row->id; ?>"
										value="<?php echo $row->team2_result; ?>" size="2" tabindex="5" class="inputbox" />
								<a	href="javascript:void(0)"
									onclick="switchMenu('part<?php echo $row->id; ?>')">&nbsp;
									<?php echo HTMLHelper::image('administrator/components/com_joomleague/assets/images/arrow_open.png',
															Text::_('COM_JOOMLEAGUE_ADMIN_MATCHES_PERIOD_SCORES'),
															'title= "'.Text::_('COM_JOOMLEAGUE_ADMIN_MATCHES_PERIOD_SCORES').'"');
									?>
								</a><br />
								<span id="part<?php echo $row->id; ?>" style="display: none">
									<br />
									<?php
									$partresults1=explode(";",$row->team1_result_split);
									$partresults2=explode(";",$row->team2_result_split);
									for ($x=0; $x < ($this->project->game_parts); $x++)
									{
										 ?>

										<input	onchange="document.getElementById('cb<?php echo $i; ?>').checked=true" onchange="document.getElementById(\'cb'<?php echo $i; ?>'\').checked=true" type="text" style="font-size: 9px;"
												name="team1_result_split<?php echo $row->id;?>[]"
												value="<?php echo (isset($partresults1[$x])) ? $partresults1[$x] : ''; ?>"
												size="2" tabindex="6" class="inputbox" /> :
										<input	onchange="document.getElementById('cb<?php echo $i; ?>').checked=true" onchange="document.getElementById(\'cb'<?php echo $i; ?>'\').checked=true" type="text" style="font-size: 9px;"
												name="team2_result_split<?php echo $row->id; ?>[]"
												value="<?php echo (isset($partresults2[$x])) ? $partresults2[$x] : ''; ?>"
												size="2" tabindex="6" class="inputbox" />
										<?php
										echo '&nbsp;&nbsp;'.($x+1).".<br />";
									}
									if ($this->project->allow_add_time == 1)
									{
										 ?>

										<input onchange="document.getElementById('cb<?php echo $i; ?>').checked=true" type="text" style="font-size: 9px;" name="team1_result_ot<?php echo $row->id;?>"
											value="<?php echo (isset($row->team1_result_ot)) ? $row->team1_result_ot : '';?>"
											size="2" tabindex="7" class="inputbox" /> :
										<input onchange="document.getElementById('cb<?php echo $i; ?>').checked=true" type="text" style="font-size: 9px;" name="team2_result_ot<?php echo $row->id;?>"
											value="<?php echo (isset($row->team2_result_ot)) ? $row->team2_result_ot : '';?>"
											size="2" tabindex="7" class="inputbox" />
										<?php echo '&nbsp;&nbsp;OT:<br />';?>

										<input onchange="document.getElementById('cb<?php echo $i; ?>').checked=true" type="text" style="font-size: 9px;" name="team1_result_so<?php echo $row->id;?>"
											value="<?php echo (isset($row->team1_result_so)) ? $row->team1_result_so : '';?>"
											size="2" tabindex="8" class="inputbox" /> :
										<input onchange="document.getElementById('cb<?php echo $i; ?>').checked=true" type="text" style="font-size: 9px;" name="team2_result_so<?php echo $row->id;?>"
											value="<?php echo (isset($row->team2_result_so)) ? $row->team2_result_so : '';?>"
											size="2" tabindex="8" class="inputbox" />
										<?php echo '&nbsp;&nbsp;SO:<br />';?>
										<?php
									}
									?>
								</span>
							</td>
							<?php
							if ($this->project->allow_add_time)
							{
								?>
								<td>
									<?php
									echo HTMLHelper::_(	'select.genericlist',$this->lists['match_result_type'],
													'match_result_type'.$row->id,'class="inputbox" size="1"','value','text',
													$row->match_result_type);
									?>
								</td>
								<?php
							}

							// Ask which prefill method should be used - only if there is no line-up set already
							if (  (($row->awayplayers_count>0 || $row->awaystaff_count>0) ||
							      ($row->homeplayers_count>0 || $row->homestaff_count>0) ) &&
							      $this->prefill = 4) {
								$prefill = 0;
							} else {
								$prefill = $this->prefill;
							}
							?>
							<td class="center">
								<a	onclick="handleRosterIconClick(<?php echo $prefill; ?>, this, '<?php echo Text::_('COM_JOOMLEAGUE_ADMIN_MATCH_PREFILL_LAST_ROSTER_ALERT'); ?>', '<?php echo Text::_('COM_JOOMLEAGUE_ADMIN_MATCH_PREFILL_PROJECTTEAM_PLAYERS_ALERT')?>')"
									href="index.php?option=com_joomleague&view=match&layout=editevents&match_id=<?php echo $row->id; ?>&prefill="
									title="<?php echo Text::_('COM_JOOMLEAGUE_ADMIN_MATCHES_EDIT_EVENTS'); ?>">
									 <?php
									 echo HTMLHelper::image('administrator/components/com_joomleague/assets/images/events.png',
													 Text::_('COM_JOOMLEAGUE_ADMIN_MATCHES_EDIT_EVENTS'),'title= "'.Text::_('COM_JOOMLEAGUE_ADMIN_MATCHES_EDIT_EVENTS').'"');
									 ?>
								</a>
							</td>
							<td class="center">
								<?php
								//start statistics:
								?>
								<a	onclick="handleRosterIconClick(<?php echo $prefill; ?>, this, '<?php echo Text::_('COM_JOOMLEAGUE_ADMIN_MATCH_PREFILL_LAST_ROSTER_ALERT'); ?>', '<?php echo Text::_('COM_JOOMLEAGUE_ADMIN_MATCH_PREFILL_PROJECTTEAM_PLAYERS_ALERT')?>')"
									href="index.php?option=com_joomleague&&view=match&layout=editstats&match_id=<?php echo $row->id; ?>&prefill=0"
									title="<?php echo Text::_('COM_JOOMLEAGUE_ADMIN_MATCHES_EDIT_STATS'); ?>">
									 <?php
									 echo HTMLHelper::_(	'image','administrator/components/com_joomleague/assets/images/calc16.png',
													 Text::_('COM_JOOMLEAGUE_ADMIN_MATCHES_EDIT_STATS'),'title= "'.Text::_('COM_JOOMLEAGUE_ADMIN_MATCHES_EDIT_STATS').'"');
								?>
								</a>
							</td>
							<td class="center">
							
								<a
									href="<?php echo Route::_('index.php?option=com_joomleague&view=match&layout=editreferees&match_id='.$row->id.'&team_id='.$row->projectteam1_id); ?>"
									 title="<?php echo Text::_('COM_JOOMLEAGUE_ADMIN_MATCHES_EDIT_REFEREES'); ?>">
									 <?php
									 if($row->referees_count==0) {
									 	$image = 'icon-16-Referees.png';
									 } else {
									 	$image = 'icon-16-Referees.png';
									 }
									 $title= '';
									 echo HTMLHelper::image('administrator/components/com_joomleague/assets/images/'.$image,
													 Text::_('COM_JOOMLEAGUE_ADMIN_MATCHES_EDIT_REFEREES'),
													 'title= "'. $title. '"') ;
									 echo ' <sub>'.$row->referees_count.'</sub> ';
									 ?>
								</a>
							</td>
							<td class="center">
								<?php
								echo $published;
								?>
							</td>
							<td class="center">
								<?php
								echo $row->id;
								?>
							</td>
						</tr>
						<?php endforeach; ?>
				</tbody>
			</table>

			<?php
			$round_date_first = new Date($this->round->round_date_first);
			$dValue = $round_date_first->format(Text::_('COM_JOOMLEAGUE_ADMIN_MATCHES_DATE_FORMAT')).' '.$this->project->start_time;
			?>

			<input type="hidden" name="match_date" value="<?php echo $dValue; ?>" />
			<input type="hidden" name="boxchecked" value="0" />
			<input type="hidden" name="search_mode" value="<?php echo $this->lists['search_mode']; ?>" />
			<input type="hidden" name="filter_order" value="<?php echo $this->lists['order']; ?>" />
			<input type="hidden" name="filter_order_Dir" value="" />
			<input type="hidden" name="rid[]" value="<?php echo $this->round->id; ?>" />
			<input type="hidden" name="project_id" value="<?php echo $this->round->project_id; ?>" />
			<input type="hidden" name="act" value="" />
			<input type="hidden" name="task" value="" id="task" />
			<?php echo HTMLHelper::_('form.token'); ?>
		</form>
	</fieldset>
