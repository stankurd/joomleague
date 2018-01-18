<?php 
/**
 * Joomleague
 *
 * @copyright	Copyright (C) 2006-2016 joomleague.at. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @link		http://www.joomleague.at
 */

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

defined('_JEXEC') or die; ?>

<!-- Player stats History START -->
<h2><?php	echo Text::_('COM_JOOMLEAGUE_PERSON_PERSONAL_STATISTICS');	?></h2>
<table style="width:96%;align:center;border:0;cellpadding:0;cellspacing:0">
	<tr>
		<td>
		<table id="playercareer" class="table">
			<thead>
			<tr class="sectiontableheader">
				<th class="td_l" class="nowrap"><?php echo Text::_('COM_JOOMLEAGUE_PERSON_COMPETITION'); ?></th>
				<th class="td_l" class="nowrap"><?php echo Text::_('COM_JOOMLEAGUE_PERSON_TEAM'); ?></th>
				<th class="td_c"><?php
				$imageTitle=Text::_('COM_JOOMLEAGUE_PERSON_PLAYED');
				echo HTMLHelper::image(	'images/com_joomleague/database/events/'.$this->project->fs_sport_type_name.'/played.png',
				$imageTitle,array(' title' => $imageTitle,' width' => 20,' height' => 20));
				?></th>
				<?php
				if ($this->config['show_substitution_stats'])
				{
					if ((isset($this->overallconfig['use_jl_substitution'])) && ($this->overallconfig['use_jl_substitution']==1))
					{
						?>
				<th class="td_c"><?php
				$imageTitle=Text::_('COM_JOOMLEAGUE_PERSON_STARTROSTER');
				echo HTMLHelper::image(	'images/com_joomleague/database/events/'.$this->project->fs_sport_type_name.'/startroster.png',
				$imageTitle,array(' title' => $imageTitle));
				?></th>
				<th class="td_c"><?php
				$imageTitle=Text::_('COM_JOOMLEAGUE_PERSON_IN');
				echo HTMLHelper::image(	'images/com_joomleague/database/events/'.$this->project->fs_sport_type_name.'/in.png',
				$imageTitle,array(' title' => $imageTitle));
				?></th>
				<th class="td_c"><?php
				$imageTitle=Text::_('COM_JOOMLEAGUE_PERSON_OUT');
				echo HTMLHelper::image(	'images/com_joomleague/database/events/'.$this->project->fs_sport_type_name.'/out.png',
				$imageTitle,array(' title' => $imageTitle));
				?></th>
				<th class="td_c"><?php
				$imageTitle=Text::_('COM_JOOMLEAGUE_PERSON_TOTAL_TIME_PLAYED');
				echo HTMLHelper::image(	'images/com_joomleague/database/events/'.$this->project->fs_sport_type_name.'/playtime.gif',
				$imageTitle,array('title'=> $imageTitle));
				?></th>
				<?php
					}
				}
				if ($this->config['show_career_events_stats'])
				{
					if (count($this->AllEvents))
					{
						foreach($this->AllEvents as $eventtype)
						{
				?>
				<th class="td_c"><?php
				$iconPath=$eventtype->icon;
				if (!strpos(" ".$iconPath,"/")){$iconPath="images/com_joomleague/database/events/".$iconPath;}
				echo HTMLHelper::image($iconPath,
					Text::_($eventtype->name),
					array(	"title" => Text::_($eventtype->name),
						"align" => "top",
						"hspace" => "2"));
				?>&nbsp;</th>
				<?php
						}
					}
				}
				if ($this->config['show_career_stats'])
				{
					foreach ($this->stats as $stat)
					{
						//do not show statheader when there are no stats
						if (!empty($stat)) {
							if ($stat->showInPlayer()) {
						
				?>
				<th class="td_c"><?php echo !empty($stat) ? $stat->getImage() : ""; ?>&nbsp;</th>
				<?php 			}
						}
					}
				}
				?>
			</tr>
			</thead>
			<tbody>
			<?php
			$k=0;
			$career=array();
			$career['played']=0;
			$career['started']=0;
			$career['in']=0;
			$career['out']=0;
			$career['playedtime']=0;
			$player = JLGModel::getInstance("Person","JoomleagueModel");

			if (count($this->historyPlayer) > 0)
			{
				foreach ($this->historyPlayer as $player_hist)
				{
					$model = $this->getModel();
					$this->inoutstat = $model->getPlayerInOutStats($player_hist->project_id, $player_hist->ptid, $player_hist->tpid);
					// time played
					$timePlayed = 0;
					$this->timePlayed = $model->getTimePlayed($player_hist->tpid,$this->project->game_regular_time);
					$timePlayed  = $this->timePlayed;
					$link1=JoomleagueHelperRoute::getPlayerRoute($player_hist->project_slug,$player_hist->team_slug,$this->person->slug);
					$link2=JoomleagueHelperRoute::getTeamInfoRoute($player_hist->project_slug,$player_hist->team_slug);
					?>
			<tr class="<?php echo ($k==0)? $this->config['style_class1'] : $this->config['style_class2']; ?>">
				<td class="td_l" nowrap="nowrap"><?php echo HTMLHelper::link($link1,$player_hist->project_name); ?>
				</td>
				<td class="td_l" class="nowrap">
				<?php
					if ($this->config['show_playerstats_teamlink'] == 1) {
						echo HTMLHelper::link($link2,$player_hist->team_name);
					} else {
						echo $player_hist->team_name;
					} 
				?>
				</td>
				<!-- Player stats History - played start -->
				<td class="td_c"><?php
				echo ($this->inoutstat->played > 0) ? $this->inoutstat->played : $this->overallconfig['zero_events_value'];
				$career['played'] += $this->inoutstat->played;
				?></td>
				<?php
				if ($this->config['show_substitution_stats'])
				{
					//substitution system
					if ((isset($this->overallconfig['use_jl_substitution']) && ($this->overallconfig['use_jl_substitution']==1)))
					{
						?>
						<!-- Player stats History - startroster start -->
						<td class="td_c"><?php
						$career['started'] += $this->inoutstat->started;
						echo ($this->inoutstat->started > 0 ? $this->inoutstat->started : $this->overallconfig['zero_events_value']);
						?></td>
						<!-- Player stats History - substitution in start -->
						<td class="td_c"><?php
						$career['in'] += $this->inoutstat->sub_in;
						echo ($this->inoutstat->sub_in > 0 ? $this->inoutstat->sub_in : $this->overallconfig['zero_events_value']);
						?></td>
						<!-- Player stats History - substitution out start -->
						<td class="td_c"><?php
						$career['out'] += $this->inoutstat->sub_out;
						echo ($this->inoutstat->sub_out > 0 ? $this->inoutstat->sub_out : $this->overallconfig['zero_events_value']);
						?></td>
						<!-- Player stats History - played time -->
						<td class="td_c"><?php
						$career['playedtime'] += $timePlayed;
						echo ($timePlayed > 0 ? $timePlayed : $this->overallconfig['zero_events_value']);
						?></td>
						<?php
					}
				}
				?>
				<!-- Player stats History - allevents start -->
				<?php
				if ($this->config['show_career_events_stats'])
				{
					// stats per project
					if (count($this->AllEvents))
					{
						foreach($this->AllEvents as $eventtype)
						{
							$stat=$player->getPlayerEvents($eventtype->id, $player_hist->project_id, $player_hist->ptid);
							?>
				<td class="td_c"><?php echo ($stat > 0) ? $stat : $this->overallconfig['zero_events_value']; ?></td>
				<?php
						}
					}
				}
				if ($this->config['show_career_stats'])
				{
					foreach ($this->stats as $stat)
					{
						//do not show when there are no stats
						if (!empty($stat)) {
						    if ($stat->showInPlayer()) {    
				?>
				<td class="td_c hasTip" title="<?php echo Text::_($stat->name); ?>">
				<?php
							if(isset($this->projectstats[$stat->id][$player_hist->project_id][$player_hist->ptid])) {
								echo ($this->projectstats[$stat->id][$player_hist->project_id][$player_hist->ptid] > 0 ? $this->projectstats[$stat->id][$player_hist->project_id][$player_hist->ptid] : $this->overallconfig['zero_events_value']);
							} else {
								echo $this->overallconfig['zero_events_value'];
							}
						    }
						}
				?></td>
				<?php
					}
				}
				?>
				<!-- Player stats History - allevents end -->
			</tr>
			<?php
			$k=(1-$k);
				}
			}
			?>
			<tr class="career_stats_total">
				<td class="td_r" colspan="2"><b><?php echo Text::_('COM_JOOMLEAGUE_PERSON_CAREER_TOTAL'); ?></b></td>
				<td class="td_c"><?php echo ($career['played'] > 0 ? $career['played'] : $this->overallconfig['zero_events_value']); ?></td>
				<?php //substitution system
				if	($this->config['show_substitution_stats'] && isset($this->overallconfig['use_jl_substitution']) &&
				($this->overallconfig['use_jl_substitution']==1))
				{
					?>
				<td class="td_c"><?php echo ($career['started'] > 0 ? $career['started'] : $this->overallconfig['zero_events_value']); ?></td>
				<td class="td_c"><?php echo ($career['in'] > 0 ? $career['in'] : $this->overallconfig['zero_events_value']); ?></td>
				<td class="td_c"><?php echo ($career['out'] > 0 ? $career['out'] : $this->overallconfig['zero_events_value']); ?></td>
				<td class="td_c"><?php echo ($career['playedtime'] > 0 ? $career['playedtime'] : $this->overallconfig['zero_events_value']); ?></td>
				<?php
				}
				?>
				<?php // stats per project
				if ($this->config['show_career_events_stats'])
				{
					if (count($this->AllEvents))
					{
						foreach($this->AllEvents as $eventtype)
						{
							if (isset($player_hist))
							{
								$total=$player->getPlayerEvents($eventtype->id);
							}
							else
							{
								$total='';
							}
							?>
				<td class="td_c"><?php echo (($total) ? $total : $this->overallconfig['zero_events_value']); ?></td>
				<?php
						}
					}
				}
				if ($this->config['show_career_stats'])
				{
					foreach ($this->stats as $stat)
					{
						if(!empty($stat)) {
						    if ($stat->showInPlayer()) {
						?>
							<td class="td_c" title="<?php echo Text::_($stat->name); ?>">
							<?php
								if (isset($this->projectstats) &&
								    array_key_exists($stat->id, $this->projectstats))
								{
									echo ($this->projectstats[$stat->id]['totals'] > 0 ? $this->projectstats[$stat->id]['totals'] : $this->overallconfig['zero_events_value']);
								}
								else	// In case there are no stats for the player
								{
									echo $this->overallconfig['zero_events_value'];
								}
							?>
							</td>
						<?php
						    }
						}
					}
				}
				?>
			</tr>
			</tbody>
		</table>
		</td>
	</tr>
</table>

<!-- Player stats History END -->
