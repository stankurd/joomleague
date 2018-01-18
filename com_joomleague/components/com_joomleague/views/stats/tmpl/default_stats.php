<?php
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

defined('_JEXEC') or die;
?>

<div id="jl_stats">

<div class="jl_substats">
<table cellspacing="0" border="0" width="100%">
<thead>
	<tr class="sectiontableheader">
		<th colspan="2"><?php	echo Text::_('COM_JOOMLEAGUE_STATS_GENERAL'); ?></th>
	</tr>
</thead>
<tbody>
	<tr class="sectiontableentry1">
		<td class="statlabel"><?php	echo Text::_('COM_JOOMLEAGUE_STATS_MATCHDAYS'); ?>:</td>
		<td class="statvalue"><?php	echo $this->totalrounds; ?></td>
	</tr>
	<tr class="sectiontableentry2">
		<td class="statlabel"><?php	echo Text::_('COM_JOOMLEAGUE_STATS_CURRENT_MATCHDAY');	?>:
		</td>
		<td class="statvalue"><?php	echo $this->actualround; ?></td>
	</tr>
	<tr class="sectiontableentry1">
		<td class="statlabel"><?php echo Text::_('COM_JOOMLEAGUE_STATS_MATCHES_PER_MATCHDAY'); ?>:</td>
		<td class="statvalue"><?php	echo ($this->totalrounds > 0 ? round (($this->totals->totalmatches / $this->totalrounds),2) : 0); ?>
		</td>
	</tr>
	<tr class="sectiontableentry2">
		<td class="statlabel"><?php echo Text::_('COM_JOOMLEAGUE_STATS_MATCHES_OVERALL');?>:</td>
		<td class="statvalue"><?php	echo $this->totals->totalmatches;?></td>
	</tr>
	<tr  class="sectiontableentry1">
		<td class="statlabel"><?php echo Text::_('COM_JOOMLEAGUE_STATS_MATCHES_PLAYED');?>:</td>
		<td class="statvalue"><?php	echo $this->totals->playedmatches;?></td>
	</tr>
	
	<?php	if ($this->config['home_away_stats']): ?>
	<tr  class="sectiontableentry2">
		<td class="statlabel"><b><?php echo Text::_('COM_JOOMLEAGUE_STATS_MATCHES_HIGHEST_WON_HOME');?>:</b>
		<br />
		<?php
		if($this->totals->playedmatches>0 && $this->highest_home) {
			echo $team = '';
			//dynamic object property string
			$pic = $this->config['show_picture'];
			if($pic!="") {
				$team .= JoomleagueHelper::getPictureThumb($this->hhHomeTeaminfo->$pic,
															$this->hhHomeTeaminfo->name,
															$this->config['team_picture_width'],
															$this->config['team_picture_height'],
															3);
				$team .= " ";
			}
			$team .= $this->hhHomeTeaminfo->name;
			if ($this->config['show_teaminfo_link']==1)
			{
				$link = JoomleagueHelperRoute::getProjectTeamInfoRoute($this->project->id, $this->highest_home->project_hometeam_id);
				echo HTMLHelper::link($link, $team);
			} else {
				echo $team;
			}
			
			echo " - ";
			
			$team = '';
			//dynamic object property string
			$pic = $this->config['show_picture'];
			if($pic!="") {
				$team .= JoomleagueHelper::getPictureThumb($this->hhAwayTeaminfo->$pic,
															$this->hhAwayTeaminfo->name,
															$this->config['team_picture_width'],
															$this->config['team_picture_height'],
															3);
				$team .=" ";
			}
			$team .= $this->hhAwayTeaminfo->name;
			if ($this->config['show_teaminfo_link']==1)
			{
				$link = JoomleagueHelperRoute::getProjectTeamInfoRoute($this->project->id, $this->highest_home->project_awayteam_id);
				echo HTMLHelper::link($link, $team);
			} else {
				echo $team;
			}
		}
		?>
		
		</td>
		<td class="statvalue"><br />
		<?php
		if($this->totals->playedmatches>0 && $this->highest_home)
		echo $this->highest_home->homegoals.$this->overallconfig['seperator'].$this->highest_home->guestgoals; ?>
		</td>
	</tr>
	<tr  class="sectiontableentry1">
		<td class="statlabel"><b><?php echo Text::_('COM_JOOMLEAGUE_STATS_MATCHES_HIGHEST_WON_AWAY');?>:</b>
		<br />
		<?php
		if($this->totals->playedmatches>0 && $this->highest_away) {
			$team = '';
			//dynamic object property string
			$pic = $this->config['show_picture'];
			if($pic!="") {
				$team .= JoomleagueHelper::getPictureThumb($this->haHomeTeaminfo->$pic,
														$this->haHomeTeaminfo->name,
														$this->config['team_picture_width'],
														$this->config['team_picture_height'],
														3);
				$team .= " ";
			}
			$team .= $this->haHomeTeaminfo->name;
			if ($this->config['show_teaminfo_link']==1)
			{
				$link = JoomleagueHelperRoute::getProjectTeamInfoRoute($this->project->id, $this->highest_away->project_hometeam_id);
				echo HTMLHelper::link($link, $team);
			} else {
				echo $team;
			}
			echo " - ";
			$team = '';
			$pic = $this->config['show_picture'];
			if($pic!="") {
				$team .= JoomleagueHelper::getPictureThumb($this->haAwayTeaminfo->$pic,
						$this->haAwayTeaminfo->name,
						$this->config['team_picture_width'],
						$this->config['team_picture_height'],
						3);
				$team .= " ";
			}
			
			$team .= $this->haAwayTeaminfo->name;
			if ($this->config['show_teaminfo_link']==1)
			{
				$link = JoomleagueHelperRoute::getProjectTeamInfoRoute($this->project->id, $this->highest_away->project_awayteam_id);
				echo HTMLHelper::link($link, $team);
			} else {
				echo $team;
			}
		}
		
		?>
		</td>
		<td class="statvalue"><br />
		<?php
		if($this->totals->playedmatches>0 && $this->highest_away) {
			echo $this->highest_away->homegoals.$this->overallconfig['seperator'].$this->highest_away->guestgoals;
		}
		?>
		</td>
	</tr>
	<?php	else :
		if ( ( $this->highest_home->homegoals - $this->highest_home->guestgoals ) >
		( $this->highest_away->guestgoals - $this->highest_away->homegoals ) )
		{
			$this->highest = $this->highest_home;
		}
		else
		{
			$this->highest = $this->highest_away;
		}
		?>
	<tr  class="sectiontableentry2">
		<td class="statlabel"><b><?php echo Text::_('COM_JOOMLEAGUE_STATS_MATCHES_HIGHEST_WIN');?>:</b>
		<br />
		<?php 
			echo $this->highest->hometeam." - ".$this->highest->guestteam; 
		?>
		</td>
		<td class="statvalue"><br />
		<?php echo $this->highest->homegoals." : ".$this->highest->guestgoals;?>
		</td>
	</tr>
	<?php endif; ?>
</tbody>	
</table>
</div>

</div>
