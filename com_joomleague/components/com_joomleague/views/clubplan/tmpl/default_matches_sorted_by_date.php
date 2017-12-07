<?php use Joomla\CMS\Factory;
use Joomla\CMS\Date\Date;
use Joomla\CMS\HTML\HTMLHelper;

defined('_JEXEC') or die; ?>

<!-- START: matches -->
<?php
$app = Factory::getApplication();
$jinput = $app->input;
$club_id = $jinput->getInt('cid') != -1 ? $jinput->getInt('cid') : false;

// sort matches by dates
if ($this->config['MatchesOrderBy'] == 'ASC')
{
	usort($this->matches, function($a, $b) {
		if (empty($a->match_date)) {
			$a->match_date = new Date('9999-12-31 0:00:00', 'UTC');
		}
		if (empty($b->match_date)) {
			$b->match_date = new Date('9999-12-31 0:00:00', 'UTC');
		}
		return strnatcasecmp($a->match_date->format('Y-m-d H:i:s'), $b->match_date->format('Y-m-d H:i:s'));
	});
}
else
{
	usort($this->matches, function($a, $b) {
		if (empty($a->match_date)) {
			$a->match_date = new Date('9999-12-31 0:00:00', 'UTC');
		}
		if (empty($b->match_date)) {
			$b->match_date = new Date('9999-12-31 0:00:00', 'UTC');
		}
			return strnatcasecmp($b->match_date->format('Y-m-d H:i:s'), $a->match_date->format('Y-m-d H:i:s'));
	});
}

$k = 0;
$counter = 1;
$MatchDateLine = '';
$MatchDay = '';
foreach ($this->matches as $game)
{
	?>
	<table class='clubplan'>
	<?php
	if ($this->config['showMatchDateLine'] && 
		$MatchDateLine != JoomleagueHelper::getMatchDate($game, JText::_('COM_JOOMLEAGUE_CLUBPLAN_MATCHDATE')))
	{
		?>
		<tr class='sectiontableheader'>
			<th colspan='16'>
				<?php
				if (JoomleagueHelper::getMatchDate($game, 'Y-m-d') == '9999-12-31') {
					echo JText::_('COM_JOOMLEAGUE_TEAMPLAN_NOT_YET_TERMINATED');
				}
				else
				{
					echo JoomleagueHelper::getMatchDate($game, JText::_('COM_JOOMLEAGUE_CLUBPLAN_MATCHDATE'));
				}
				?>
			</th>
		</tr>
		<?php
	}

	if ($this->config['show_matchday'] && $MatchDay != $game->roundname)
	{
		?>
		<h3><?php echo $game->roundname;?></h3>
		<?php
	}

	$class = ($k==0)? 'sectiontableentry1' : 'sectiontableentry2';
	$result_link = JoomleagueHelperRoute::getResultsRoute($game->project_id, $game->roundid, $game->division_id);
	$nextmatch_link = JoomleagueHelperRoute::getNextmatchRoute($game->project_id, $game->id);
	$teaminfo1_link = JoomleagueHelperRoute::getTeamInfoRoute($game->project_id, $game->team1_id);
	$teaminfo2_link = JoomleagueHelperRoute::getTeamInfoRoute($game->project_id, $game->team2_id);
	$teamstats1_link = JoomleagueHelperRoute::getTeamStatsRoute($game->project_id, $game->team1_id);
	$teamstats2_link = JoomleagueHelperRoute::getTeamStatsRoute($game->project_id, $game->team2_id);
	$playground_link = JoomleagueHelperRoute::getPlaygroundRoute($game->project_id, $game->playground_id);

	$hometeam				= $game;
	$awayteam				= $game;

	$isFavTeam				= false;
	$isFavTeam				= in_array($game->team1_id, $this->favteams);
	$hometeam->name			= $game->tname1;
	$hometeam->team_id		= $game->team1_id;
	$hometeam->id 			= $game->team1_id;
	$hometeam->short_name	= $game->tname1_short;
	$hometeam->middle_name	= $game->tname1_middle;
	$hometeam->project_id	= $game->prid;
	$hometeam->club_id		= $game->t1club_id;
	$hometeam->projectteamid = $game->projectteam1_id;
	$tname1 = JoomleagueHelper::formatTeamName($hometeam, 'c' . $counter . 'g' . $game->id, $this->config, $isFavTeam);

	$isFavTeam				= false;
	$isFavTeam				= in_array($game->team2_id, $this->favteams);
	$awayteam->name			= $game->tname2;
	$awayteam->team_id		= $game->team2_id;
	$awayteam->id 			= $game->team2_id;
	$awayteam->short_name	= $game->tname2_short;
	$awayteam->middle_name	= $game->tname2_middle;
	$awayteam->project_id	= $game->prid;
	$awayteam->club_id		= $game->t2club_id;
	$awayteam->projectteamid = $game->projectteam2_id;
	$tname2 = JoomleagueHelper::formatTeamName($awayteam, 'c' . $counter .'g' . $game->id, $this->config, $isFavTeam);


	$favStyle = '';
	if ($this->config['highlight_fav'] == 1 && !$club_id) {
		$isFavTeam = in_array($game->team1_id,$this->favteams) ? 1 : in_array($game->team2_id, $this->favteams);
		if ($isFavTeam && $this->project->fav_team_highlight_type == 1)
		{
			if(trim($this->project->fav_team_color) != '')
			{
				$color = trim($this->project->fav_team_color);
			}
			$format = '%s';
			$favStyle = ' style="';
			$favStyle .= ($this->project->fav_team_text_bold != '') ? 'font-weight:bold;' : '';
			$favStyle .= (trim($this->project->fav_team_text_color) != '') ? 'color:'.trim($this->project->fav_team_text_color).';' : '';
			$favStyle .= ($color != '') ? 'background-color:' . $color . ';' : '';
			if ($favStyle != ' style="')
			{
			  $favStyle .= '"';
			}
			else {
			  $favStyle = '';
			}

		}
	}

	?>
		<tr class='<?php echo $class; ?>'<?php echo $favStyle; ?>>
		<?php
			/*
				if ($this->config['show_matchday'] == 1) { ?>
			<td>
				<?php if ($this->config['which_link'] == 0) { ?>
				<?php
				echo $game->roundid ;
				}
				?>
				<?php if ($this->config['which_link'] == 1) { ?>
				<?php
				echo HTMLHelper::link($result_link, $game->roundid);
				}
				?>
				<?php if ($this->config['which_link'] == 2) { ?>
				<?php
				echo HTMLHelper::link($nextmatch_link, $game->roundid);
				}
				?>
			</td>
			<?php } ;

			*/?>

			<?php if ($this->config['show_match_nr']==1) { ?>
			<td><?php echo $game->match_number ; ?></td>
			<?php } ;?>

			<td nowrap="nowrap">
			<?php
			if (JoomleagueHelper::getMatchDate($game, 'Y-m-d') == '9999-12-31') {
				$timeSuffix=JText::_('COM_JOOMLEAGUE_GLOBAL_CLOCK');
				if ($timeSuffix == 'COM_JOOMLEAGUE_GLOBAL_CLOCK') {
					$timeSuffix = '%1$s&nbsp;h';
				}
				$matchTime='--&nbsp;:&nbsp;--';
				if ($this->config['show_time_suffix'])
				{
					echo sprintf($timeSuffix,$matchTime);
				}
				else
				{
					echo $matchTime;
				}
			}
			else
			{
				echo JoomleagueHelper::getMatchTime($game);
			}
			?>
			</td>

			<?php if ($this->config['show_time_present'] == 1) { ?>
			<td nowrap='nowrap'><?php echo $game->time_present; ?></td>
			<?php } ?>

			<?php if ($this->config['show_league'] == 1) { ?>
	   		<td><?php echo $game->l_name; ?></td>
			<?php } ?>

			<td class='td_r'>
			<?php if ($this->config['which_link2'] == 0) { ?>
			<?php
			echo $tname1 ;
			}
			?>
			<?php if ($this->config['which_link2'] == 1) { ?>
			<?php
			echo HTMLHelper::link($teaminfo1_link, $tname1);
			}
			?>
			<?php if ($this->config['which_link2'] == 2) { ?>
			<?php
			echo HTMLHelper::link($teamstats1_link, $tname1);
			}
			?>
			</td>

			<?php if ($this->config['show_club_logo'] == 1) { ?>
			<td class='icon'>
			<?php
			//dynamic object property string
			$pic = '';
			$pic = 'home_'.$this->config['show_picture'];

			$type=3;
			switch ($this->config['show_picture']) {
				case 'logo_small':
					$picture = $game->$pic;
					$type = 3;
					echo JoomleagueHelper::getPictureThumb(
							$picture,
							$game->tname1,
							$this->config['picture_width'],
							$this->config['picture_height'],
							$type
					);
					break;
				case 'logo_medium':
					$picture = $game->$pic;
					$type = 2;
					echo JoomleagueHelper::getPictureThumb(
							$picture,
							$game->tname1,
							$this->config['picture_width'],
							$this->config['picture_height'],
							$type
					);
					break;
				case 'logo_big':
					$picture = $game->$pic;
					$type = 1;
					echo JoomleagueHelper::getPictureThumb(
							$picture,
							$game->tname1,
							$this->config['picture_width'],
							$this->config['picture_height'],
							$type
					);
					break;
				case 'country_small':
					$type = 6;
					$pic = 'home_country';
					if($game->$pic != '' && !empty($game->$pic)) {
						echo Countries::getCountryFlag($game->$pic, 'height="11"');
					}
					break;
				case 'country_big':
					$type = 7;
					$pic = 'home_country';
					if($game->$pic != '' && !empty($game->$pic)) {
						echo Countries::getCountryFlag($game->$pic, 'height="50"');
					}
					break;
			}
			?>
			</td>
			<?php } ?>

			<td class='vs'>-</td>

			<?php if ($this->config['show_club_logo'] == 1) { ?>
			<td class='icon'>
			<?php
			//dynamic object property string
			$pic = '';
			$pic = 'away_'.$this->config['show_picture'];

			$type=3;
			switch ($this->config['show_picture']) {
				case 'logo_small':
					$picture = $game->$pic;
					$type = 3;
					echo JoomleagueHelper::getPictureThumb(
							$picture,
							$game->tname1,
							$this->config['picture_width'],
							$this->config['picture_height'],
							$type
					);
					break;
				case 'logo_medium':
					$picture = $game->$pic;
					$type = 2;
					echo JoomleagueHelper::getPictureThumb(
							$picture,
							$game->tname1,
							$this->config['picture_width'],
							$this->config['picture_height'],
							$type
					);
					break;
				case 'logo_big':
					$picture = $game->$pic;
					$type = 1;
					echo JoomleagueHelper::getPictureThumb(
							$picture,
							$game->tname1,
							$this->config['picture_width'],
							$this->config['picture_height'],
							$type
					);
					break;
				case 'country_small':
					$type = 6;
					$pic = 'home_country';
					if($game->$pic != '' && !empty($game->$pic)) {
						echo Countries::getCountryFlag($game->$pic, 'height="11"');
					}
					break;
				case 'country_big':
					$type = 7;
					$pic = 'home_country';
					if($game->$pic != '' && !empty($game->$pic)) {
						echo Countries::getCountryFlag($game->$pic, 'height="50"');
					}
					break;
			}
			?>
			</td>
			<?php } ?>

			<td class='td_l'>
			<?php if ($this->config['which_link2'] == 0) { ?>
			<?php
				echo $tname2 ;
			}
			?>

			<?php if ($this->config['which_link2'] == 1) { ?>
			<?php
			echo HTMLHelper::link($teaminfo2_link, $tname2);
			}
			?>
			<?php if ($this->config['which_link2'] == 2) { ?>
			<?php
			echo HTMLHelper::link($teamstats2_link, $tname2);
			}
			?>
			</td>

			<?php if ($this->config['show_referee'] == 1) { ?>
			<td>
			<?php
			$matchReferees = $this->model->getMatchReferees($game->match_id);
			foreach ($matchReferees AS $matchReferee)
			{
				$referee_link = JoomleagueHelperRoute::getRefereeRoute($game->project_id, $matchReferee->id);
				echo HTMLHelper::link($referee_link, $matchReferee->firstname . ' ' . $matchReferee->lastname);
				echo '<br />';
			}
			?>
			</td>
			<?php } ;?>

			<?php if ($this->config['show_playground'] == 1) { ?>
			<td>
			<?php
			echo HTMLHelper::link($playground_link, $game->pl_name);
			?>
			</td>
			<?php } ;?>

			<?php
			$score = '';
			if (!$game->alt_decision)
			{
				$e1 = $game->team1_result;
				$e2 = $game->team2_result;
			}
			else
			{
				$e1 = isset($game->team1_result_decision) ? $game->team1_result_decision : 'X';
				$e2 = isset($game->team2_result_decision) ? $game->team2_result_decision : 'X';
			}
			if ($game->cancel==0) {
				?>
			<td align='center'><?php echo $e1; ?></td>
			<td align='center'>-</td>
			<td align='center'><?php echo $e2; ?></td>
				<?php
			} else {
				?>
			<td align='center' valign='top' colspan='3'><?php echo $game->cancel_reason; ?></td>
				<?php
			}
			if ($this->config['show_thumbs_picture']==1) {
			   switch ($this->config['type_matches']) {
			   case 1 : // home matches
					$team1 = $e1;
					$team2 = $e2;
					break;
			   case 2 : // away matches
					$team2 = $e1;
					$team1 = $e2;
					break;
				default : // home+away matches, but take care of the select club from the menu item to have the icon correct displayed
					if ($game->club1_id == $club_id) {
						$team1 = $e1;
						$team2 = $e2;
					} else if ($game->club2_id == $club_id) {
						$team1 = $e2;
						$team2 = $e1;
					} else {
						$team1 = $e1;
						$team2 = $e2;
					}
			   }
				?>
			<td>
				<?php
				if(isset($team1) && isset($team2) && ($team1 == $team2)) {
					echo HTMLHelper::image("media/com_joomleague/jl_images/draw.png", "draw.png",
						array("title" => JText::_('COM_JOOMLEAGUE_CLUBPLAN_MATCH_DRAW')))."&nbsp;";
				} else {
					if($team1 > $team2) {
						echo HTMLHelper::image("media/com_joomleague/jl_images/thumbs_up.png", "thumbs_up.png",
							array("title" => JText::_('COM_JOOMLEAGUE_CLUBPLAN_MATCH_WON')))."&nbsp;";
					} elseif($team2 > $team1) {
						echo HTMLHelper::image("media/com_joomleague/jl_images/thumbs_down.png", "thumbs_down.png",
							array("title" => JText::_('COM_JOOMLEAGUE_CLUBPLAN_MATCH_LOST')))."&nbsp;";
					}
					else
					{
						echo "&nbsp;";
					}
				}
			}
			?>
			</td>
		</tr>
	<?php
	$k = 1 - $k;
	$counter++;
	$MatchDateLine = JoomleagueHelper::getMatchDate($game, JText::_('COM_JOOMLEAGUE_CLUBPLAN_MATCHDATE'));
	$MatchDay = $game->roundname;
}
?>
</table>
<br />
<!-- END: matches -->