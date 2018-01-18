<?php
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

defined('_JEXEC') or die;
?>

<div id='jl_teamstats'>
	<div class='jl_teamsubstats'>
		<table cellspacing='0' border='0' width='100%'>
			<thead>
				<tr class='sectiontableheader'>
					<th>&nbsp;</th>
					<th align='right' width='20%'><?php echo Text::_('COM_JOOMLEAGUE_TEAMSTATS_TOTAL'); ?></th>
					<?php if ($this->config['home_away_stats']): ?>
					<th align='right' width='20%'><?php echo Text::_('COM_JOOMLEAGUE_TEAMSTATS_HOME'); ?></th>
					<th align='right' width='20%'><?php echo Text::_('COM_JOOMLEAGUE_TEAMSTATS_AWAY'); ?></th>
					<?php endif; ?>
				</tr>
			</thead>
			<tbody>
				<?php
				if ($this->config['show_general_stats'])
				{
				$totalmatches = $this->totalshome->totalmatches + $this->totalsaway->totalmatches;
				$totalplayedmatches = $this->totalshome->playedmatches + $this->totalsaway->playedmatches;
				?>
				<tr class='sectiontableentry1'>
					<td class='statlabel'><?php echo Text::_('COM_JOOMLEAGUE_TEAMSTATS_MATCHES_OVERALL'); ?>:</td>
					<td class='statvalue'><?php echo $totalmatches; ?></td>
					<?php if ($this->config['home_away_stats']): ?>
					<td class='statvalue'><?php echo $this->totalshome->totalmatches; ?></td>
					<td class='statvalue'><?php echo $this->totalsaway->totalmatches; ?></td>
					<?php endif; ?>
				</tr>
				<tr class='sectiontableentry2'>
					<td class='statlabel'><?php echo Text::_('COM_JOOMLEAGUE_TEAMSTATS_MATCHES_PLAYED'); ?>:</td>
					<td class='statvalue'><?php echo $totalplayedmatches; ?></td>
					<?php if ($this->config['home_away_stats']): ?>
					<td class='statvalue'><?php echo $this->totalshome->playedmatches; ?></td>
					<td class='statvalue'><?php echo $this->totalsaway->playedmatches; ?></td>
					<?php endif; ?>
				</tr>
				<tr class='sectiontableentry1'>
					<td class='statlabel'><?php echo Text::_('COM_JOOMLEAGUE_TEAMSTATS_WIN'); ?>:</td>
					<td class='statvalue'><?php echo count($this->results['win']); ?></td>
					<?php if ($this->config['home_away_stats']):?>
					<td class='statvalue'><?php echo $this->results['home_wins']; ?></td>
					<td class='statvalue'><?php echo $this->results['away_wins']; ?></td>
					<?php endif; ?>
				</tr>
				<tr class='sectiontableentry2'>
					<td class='statlabel'><?php echo Text::_('COM_JOOMLEAGUE_TEAMSTATS_DRAW'); ?>:</td>
					<td class='statvalue'><?php echo count($this->results['tie']); ?></td>
					<?php if ($this->config['home_away_stats']): ?>
					<td class='statvalue'><?php echo $this->results['home_draws']; ?></td>
					<td class='statvalue'><?php echo $this->results['away_draws']; ?></td>
					<?php endif; ?>
				</tr>
				<tr class='sectiontableentry1'>
					<td class='statlabel'><?php echo Text::_('COM_JOOMLEAGUE_TEAMSTATS_LOST'); ?>:</td>
					<td class='statvalue'><?php echo count($this->results['loss']); ?></td>
					<?php if ($this->config['home_away_stats']): ?>
					<td class='statvalue'><?php echo $this->results['home_losses']; ?></td>
					<td class='statvalue'><?php echo $this->results['away_losses']; ?></td>
					<?php endif; ?>
				</tr>
				<tr class='sectiontableentry2'>
				<?php if (count($this->results['forfeit'])): ?>
					<td class='statlabel'><?php echo Text::_('COM_JOOMLEAGUE_TEAMSTATS_FORFEIT'); ?>:</td>
					<td class='statvalue'><?php echo count($this->results['forfeit']); ?></td>
					<?php if ($this->config['home_away_stats']): ?>
					<td class='statvalue'><!-- TODO: determine home forfeits --></td>
					<td class='statvalue'><!-- TODO: determine away forfeits --></td>
					<?php endif; ?>
				<?php else: ?>
					<td class='statlabel'>&nbsp;</td>
					<td class='statvalue'>&nbsp;</td>
					<?php if ($this->config['home_away_stats']): ?>
					<td class='statvalue'>&nbsp;</td>
					<td class='statvalue'>&nbsp;</td>
					<?php endif; ?>
				<?php endif; ?>
				</tr>
				<?php if ($this->config['home_away_stats']): ?>
				<tr class='sectiontableentry1'>
					<td class='statlabel'><?php echo Text::_('COM_JOOMLEAGUE_TEAMSTATS_MATCHES_HIGHEST_WIN'); ?>:</td>
					<td class='statvalue'>
						<?php if (!empty($this->highest_home)): ?>
						<!-- TODO: determine total highest win -->
						<?php endif; ?>
					</td>
					<!-- highest win home -->
					<?php if ($this->config['home_away_stats']): ?>
						<?php if (!empty($this->highest_home)): ?>
					<td class='statvalue'>
						<?php 
							$link=JoomleagueHelperRoute::getMatchReportRoute($this->project->id, $this->highest_home->matchid);
							$highest_home_result = $this->highest_home->homegoals . $this->overallconfig['seperator'] .
								$this->highest_home->guestgoals;
							echo HTMLHelper::link($link, $highest_home_result);
						?>
						<br>
						<?php 
							$team1 	= JoomleagueHelperRoute::getTeamInfoRoute($this->project->id, $this->highest_home->team1_id);
							$team2 	= JoomleagueHelperRoute::getTeamInfoRoute($this->project->id, $this->highest_home->team2_id);
							$match	= HTMLHelper::link($team1,$this->highest_home->hometeam) . '<br>' . $this->overallconfig['seperator'] .
								'<br>' . HTMLHelper::link($team2,$this->highest_home->guestteam);
							echo $match;
						?>
					</td>
						<?php else: ?>
					<td class='statvalue'>
					----
					</td>
						<?php endif; ?>
					<!-- highest win away -->
						<?php if (!empty($this->highest_away)): ?>
					<td class='statvalue'>
						<?php 
							$link=JoomleagueHelperRoute::getMatchReportRoute($this->project->id,$this->highest_away->matchid);
							$highest_away_result = $this->highest_away->homegoals . $this->overallconfig['seperator'] .
								$this->highest_away->guestgoals;
							echo HTMLHelper::link($link,$highest_away_result);
						?>
						<br>
						<?php 
							$team1 	= JoomleagueHelperRoute::getTeamInfoRoute($this->project->id, $this->highest_away->team1_id);
							$team2 	= JoomleagueHelperRoute::getTeamInfoRoute($this->project->id, $this->highest_away->team2_id);
							$match	= HTMLHelper::link($team1,$this->highest_away->hometeam) . '<br>' . $this->overallconfig['seperator'] .
								'<br>' . HTMLHelper::link($team2,$this->highest_away->guestteam);
							echo $match;
						?>
					</td>
						<?php else: ?>
					<td class='statvalue'>
					----
					</td>
						<?php endif; ?>
					<?php endif; ?>
				</tr>
				<tr class='sectiontableentry2'>
					<td class='statlabel'><?php echo Text::_('COM_JOOMLEAGUE_TEAMSTATS_MATCHES_HIGHEST_LOSS'); ?>:</td>
					<td class='statvalue'>
						<?php if (!empty($this->highestdef_home)): ?>
						<!-- TODO: determine total highest loss -->
						<?php endif; ?>
					</td>
					<!-- highest defeat home -->
					<?php if ($this->config['home_away_stats']): ?>
						<?php if (!empty($this->highestdef_home)): ?>
					<td class='statvalue'>
						<?php 
							$link=JoomleagueHelperRoute::getMatchReportRoute($this->project->id,$this->highestdef_home->matchid);
							$highestdef_home_result = $this->highestdef_home->homegoals . $this->overallconfig['seperator'] .
								$this->highestdef_home->guestgoals;
							echo HTMLHelper::link($link,$highestdef_home_result);
						?>
						<br>
						<?php 
							$team1 	= JoomleagueHelperRoute::getTeamInfoRoute($this->project->id, $this->highestdef_home->team1_id);
							$team2 	= JoomleagueHelperRoute::getTeamInfoRoute($this->project->id, $this->highestdef_home->team2_id);
							$match	= HTMLHelper::link($team1,$this->highestdef_home->hometeam) . '<br>' . $this->overallconfig['seperator'] .
								'<br>' . HTMLHelper::link($team2,$this->highestdef_home->guestteam);
							echo $match;
						?>
					</td>
						<?php else: ?>
					<td class='statvalue'>
					----
					</td>
						<?php endif; ?>
					<!-- highest defeat away -->
						<?php if (!empty($this->highestdef_away)): ?>
					<td class='statvalue'>
						<?php 
							$link=JoomleagueHelperRoute::getMatchReportRoute($this->project->id,$this->highestdef_away->matchid);
							$highestdef_away_result = $this->highestdef_away->homegoals . $this->overallconfig['seperator'] .
								$this->highestdef_away->guestgoals;
							echo HTMLHelper::link($link,$highestdef_away_result);
						?>
						<br>
						<?php 
							$team1 	= JoomleagueHelperRoute::getTeamInfoRoute($this->project->id, $this->highestdef_away->team1_id);
							$team2 	= JoomleagueHelperRoute::getTeamInfoRoute($this->project->id, $this->highestdef_away->team2_id);
							$match	= HTMLHelper::link($team1,$this->highestdef_away->hometeam) . '<br>' . $this->overallconfig['seperator'] .
								'<br>' . HTMLHelper::link($team2,$this->highestdef_away->guestteam);
							echo $match;
						?>
					</td>
						<?php else: ?>
					<td class='statvalue'>
					----
					</td>
						<?php endif; ?>
					<?php endif; ?>
				</tr>
				<?php endif; ?>
				<?php
				}
				// ======================= Goal statistics ==========================
				if ($this->config['show_goals_stats'])
				{
					$totalGoals = $this->totalshome->totalgoals + $this->totalsaway->totalgoals;
					$totalGoalsFor = $this->totalshome->goalsfor + $this->totalsaway->goalsfor;
					$totalGoalsAgainst = $this->totalshome->goalsagainst + $this->totalsaway->goalsagainst;
					$totalPlayedMatches = $this->totalshome->playedmatches + $this->totalsaway->playedmatches;
				?>
				<tr class='sectiontableentry1'>
					<td class='statlabel'><?php echo Text::_('COM_JOOMLEAGUE_TEAMSTATS_GOALS_TOTAL'); ?>:</td>
					<td class='statvalue'><?php echo $totalGoals; ?></td>
					<?php if ($this->config['home_away_stats']): ?>
					<td class='statvalue'><?php echo $this->totalshome->totalgoals; ?></td>
					<td class='statvalue'><?php echo $this->totalsaway->totalgoals; ?></td>
					<?php endif; ?>
				</tr>
				<tr class='sectiontableentry2'>
					<td class='statlabel'><?php echo Text::_('COM_JOOMLEAGUE_TEAMSTATS_GOALS_TOTAL_PER_MATCH'); ?>:</td>
					<td class='statvalue'><?php echo $this->averageValue($totalGoals, $totalPlayedMatches); ?></td>
					<?php if ($this->config['home_away_stats']) : ?>
					<td class='statvalue'><?php echo $this->averageValue($this->totalshome->totalgoals, $this->totalshome->playedmatches); ?></td>
					<td class='statvalue'><?php echo $this->averageValue($this->totalsaway->totalgoals, $this->totalsaway->playedmatches); ?></td>
					<?php endif; ?>
				</tr>
				<tr class='sectiontableentry1'>
					<td class='statlabel'><?php echo Text::_('COM_JOOMLEAGUE_TEAMSTATS_GOALS_FOR'); ?></td>
					<td class='statvalue'><?php echo $totalGoalsFor; ?></td>
					<?php if ($this->config['home_away_stats']): ?>
					<td class='statvalue'><?php echo $this->totalshome->goalsfor; ?></td>
					<td class='statvalue'><?php echo $this->totalsaway->goalsfor; ?></td>
					<?php endif; ?>
				</tr>
				<tr class='sectiontableentry2'>
					<td class='statlabel'><?php echo Text::_('COM_JOOMLEAGUE_TEAMSTATS_GOALS_FOR_PER_MATCH');?>:</td>
					<td class='statvalue'><?php echo $this->averageValue($totalGoalsFor, $totalPlayedMatches); ?></td>
					<?php if ($this->config['home_away_stats']): ?>
					<td class='statvalue'><?php echo $this->averageValue($this->totalshome->goalsfor, $this->totalshome->playedmatches); ?></td>
					<td class='statvalue'><?php echo $this->averageValue($this->totalsaway->goalsfor, $this->totalsaway->playedmatches); ?></td>
					<?php endif; ?>
				</tr>
				<tr class='sectiontableentry1'>
					<td class='statlabel'><?php echo Text::_('COM_JOOMLEAGUE_TEAMSTATS_GOALS_AGAINST'); ?></td>
					<td class='statvalue'><?php echo $totalGoalsAgainst; ?></td>
					<?php if ($this->config['home_away_stats']): ?>
					<td class='statvalue'><?php echo $this->totalshome->goalsagainst; ?></td>
					<td class='statvalue'><?php echo $this->totalsaway->goalsagainst; ?></td>
					<?php endif; ?>
				</tr>
				<tr class='sectiontableentry2'>
					<td class='statlabel'><?php echo Text::_('COM_JOOMLEAGUE_TEAMSTATS_GOALS_AGAINST_PER_MATCH'); ?>:</td>
					<td class='statvalue'><?php echo $this->averageValue($totalGoalsAgainst, $totalPlayedMatches); ?></td>
					<?php if ($this->config['home_away_stats']): ?>
					<td class='statvalue'><?php echo $this->averageValue($this->totalshome->goalsagainst, $this->totalshome->playedmatches); ?></td>
					<td class='statvalue'><?php echo $this->averageValue($this->totalsaway->goalsagainst, $this->totalsaway->playedmatches); ?></td>
					<?php endif; ?>
				</tr>
				<tr class='sectiontableentry1'>
					<td class='statlabel'><?php echo Text::_('COM_JOOMLEAGUE_TEAMSTATS_NO_GOALS_AGAINST'); ?></td>
					<td class='statvalue'><?php echo $this->nogoals_against->totalzero; ?></td>
					<?php if ($this->config['home_away_stats']): ?>
					<td class='statvalue'><?php echo empty($this->nogoals_against->homezero) ? 0 : $this->nogoals_against->homezero; ?></td>
					<td class='statvalue'><?php echo empty($this->nogoals_against->awayzero) ? 0 : $this->nogoals_against->awayzero; ?></td>
					<?php endif; ?>
				</tr>
				<?php
				}
			?>
			</tbody>
		</table>
	</div>
</div>
<div class='clr'></div>
