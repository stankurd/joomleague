<?php use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;

defined('_JEXEC') or die; ?>
<!-- START: matches -->
<table class='clubplan'>
<?php
if ($this->config['type_matches'] != 0) {
?>
	<tr class='sectiontableheader'>
		<?php if ($this->config['show_matchday'] == 1) { ?>
		<th><?php echo JText::_('COM_JOOMLEAGUE_CLUBPLAN_MATCHDAY'); ?></th>
		<?php } ;?>
		<?php if ($this->config['show_match_nr'] == 1) { ?>
		<th><?php echo JText::_('COM_JOOMLEAGUE_CLUBPLAN_MATCH_NR'); ?></th>
		<?php } ;?>		
		<?php if ($this->config['show_match_date'] == 1) { ?>
		<th><?php echo JText::_('COM_JOOMLEAGUE_CLUBPLAN_DATE');?></th>
		<?php } ;?>
		<?php if ($this->config['show_match_time'] == 1) { ?>
		<th><?php echo JText::_('COM_JOOMLEAGUE_CLUBPLAN_TIME'); ?></th>
		<?php } ;?>
		<?php if ($this->config['show_time_present'] == 1) { ?>
		<th><?php echo JText::_('COM_JOOMLEAGUE_CLUBPLAN_TIME_PRESENT'); ?></th>
		<?php } ;?>
		<?php if ($this->config['show_league'] == 1) { ?>
		<th><?php echo JText::_('COM_JOOMLEAGUE_CLUBPLAN_LEAGUE'); ?></th>
		<?php } ;?>		
		<?php if ($this->config['show_club_logo'] == 1) { ?>
		<th></th>
		<?php } ?>
		<th>&nbsp;</th>
		<th>&nbsp;</th>
		<?php if ($this->config['show_club_logo'] == 1) { ?>
		<th>&nbsp;</th>
		<?php } ?>
		<th>&nbsp;</th>
		<?php if ($this->config['show_referee'] == 1) { ?>
		<th><?php echo JText::_('COM_JOOMLEAGUE_CLUBPLAN_REFEREE'); ?></th>
		<?php } ;?>
		<?php if ($this->config['show_playground'] == 1) { ?>
		<th><?php echo JText::_('COM_JOOMLEAGUE_CLUBPLAN_PLAYGROUND'); ?></th>
		<?php } ;?>
		<th colspan=3 align='center'><?php echo JText::_('COM_JOOMLEAGUE_CLUBPLAN_RESULT'); ?></th>
		<?php if ($this->config['show_thumbs_picture'] == 1) { ?>
		<th align='center'>&nbsp;</th>
		<?php } ;?>
	</tr>
<?php
}
$k   = 0;
$cnt = 0;
$app = Factory::getApplication();
$jinput = $app->input;
$club_id = $jinput->getInt('cid') != -1 ? $jinput->getInt('cid') : false;
$prevDate = '';
foreach ($this->matches as $game)
{
	$gameDate = JoomleagueHelper::getMatchDate($game, JText::_('COM_JOOMLEAGUE_CLUBPLAN_MATCHDATE'));
	if ($this->config['type_matches'] == 0) {
		if ($gameDate != $prevDate) {
			if($this->config['showMatchDateLine']) {
			?>
			<tr class='sectiontableheader'>
				<th colspan='16'>
					<?php
					if ($game->match_date)
					{
						echo $gameDate;
					}
					?>
				</th>
			</tr>
		<?php
			}
			$prevDate = $gameDate;
		}
	}

	$class = ($k==0)? $this->config['style_class1'] : $this->config['style_class2'];
	$result_link = JoomleagueHelperRoute::getResultsRoute($game->project_id, $game->roundid, $game->division_id);
	$nextmatch_link = JoomleagueHelperRoute::getNextmatchRoute($game->project_id, $game->id);
	$teaminfo1_link = JoomleagueHelperRoute::getTeamInfoRoute($game->project_id, $game->team1_id);
	$teaminfo2_link = JoomleagueHelperRoute::getTeamInfoRoute($game->project_id, $game->team2_id);
	$teamstats1_link = JoomleagueHelperRoute::getTeamStatsRoute($game->project_id, $game->team1_id);
	$teamstats2_link = JoomleagueHelperRoute::getTeamStatsRoute($game->project_id, $game->team2_id);
	$playground_link = JoomleagueHelperRoute::getPlaygroundRoute($game->project_id, $game->playground_id);
	$favs = JoomleagueHelper::getProjectFavTeams($game->project_id);
	$favteams = explode(",",$favs->fav_team);

	if ($this->config['which_link2'] == 1) {
		$link1 = $teaminfo1_link;
		$link2 = $teaminfo2_link;
	} else if ($this->config['which_link2'] == 2) {
		$link1 = $teamstats1_link;
		$link2 = $teamstats2_link;
	} else {
		$link1 = null;
		$link2 = null;
	}
	$hometeam               = $game;
	$awayteam               = $game;

	$isFavTeam              = false;
	$isFavTeam              = in_array($game->team1_id,$favteams);
	$hometeam->name         = $game->tname1;
	$hometeam->team_id      = $game->team1_id;
	$hometeam->id           = $game->team1_id;
	$hometeam->short_name   = $game->tname1_short;
	$hometeam->middle_name  = $game->tname1_middle;
	$hometeam->project_id   = $game->prid;
	$hometeam->club_id      = $game->t1club_id;
	$hometeam->projectteamid = $game->projectteam1_id;
	$hometeam->club_slug    = $game->club1_slug;
	$hometeam->team_slug    = $game->team1_slug;
	$tname1 = JoomleagueHelper::formatTeamName($hometeam, 'clubplanhome' . $cnt++, $this->config, $isFavTeam, $link1);

	$isFavTeam              = false;
	$isFavTeam              = in_array($game->team2_id,$favteams);
	$awayteam->name         = $game->tname2;
	$awayteam->team_id      = $game->team2_id;
	$awayteam->id           = $game->team2_id;
	$awayteam->short_name   = $game->tname2_short;
	$awayteam->middle_name  = $game->tname2_middle;
	$awayteam->project_id   = $game->prid;
	$awayteam->club_id      = $game->t2club_id;
	$awayteam->projectteamid = $game->projectteam2_id;
	$awayteam->club_slug    = $game->club2_slug;
	$awayteam->team_slug    = $game->team2_slug;
	$tname2 = JoomleagueHelper::formatTeamName($awayteam, 'clubplanaway' . $cnt++, $this->config, $isFavTeam, $link2);

	$favStyle = '';
	if ($this->config['highlight_fav'] == 1 && !$club_id) {
		$isFavTeam = in_array($game->team1_id,$favteams) || in_array($game->team2_id, $favteams);
		if ($isFavTeam && $favs->fav_team_highlight_type == 1)
		{
			if (trim($favs->fav_team_color) != '')
			{
				$color = trim($favs->fav_team_color);
			}
			$format = '%s';
			$favStyle = ' style="';
			$favStyle .= ($favs->fav_team_text_bold != '') ? 'font-weight:bold;' : '';
			$favStyle .= (trim($favs->fav_team_text_color) != '') ? 'color:'.trim($favs->fav_team_text_color).';' : '';
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
		<?php if ($this->config['show_matchday'] == 1) { ?>
		<td>
			<?php
			$roundcode = (!empty($game->roundcode)) ? $game->roundcode : '';
			if ($this->config['which_link'] == 0) { ?>
			<?php
			echo $roundcode;
			}
			?>
			<?php if ($this->config['which_link'] == 1) { ?>
			<?php
			echo HTMLHelper::link($result_link,$roundcode);
			}
			?>
			<?php if ($this->config['which_link'] == 2) { ?>
			<?php
			echo HTMLHelper::link($nextmatch_link, $roundcode);
			}
			?>
		</td>
		<?php } ;?>

		<?php if ($this->config['show_match_nr'] == 1) { ?>
		<td>
			<?php echo $game->match_number ; ?>
		</td>
		<?php } ;?>

		<?php if ($this->config['show_match_date'] == 1) { ?>
		<td>
			<?php
			if ($game->match_date)
			{
				echo $gameDate;
			}
			?>
		</td>
		<?php } ;?>

		<?php if ($this->config['show_match_time'] == 1) { ?>
		<td nowrap='nowrap'>
			<?php
			echo JoomleagueHelperHtml::showMatchTime($game, $this->config, $this->overallconfig, $this->project);
			?>
		</td>
		<?php } ;?>

		<?php if ($this->config['show_time_present'] == 1) { ?>
		<td nowrap='nowrap'>
			<?php
			echo $game->time_present;
			?>
		</td>
		<?php } ?>

		<?php if ($this->config['show_league'] == 1) { ?>
		<td>
			<?php echo $game->l_name; ?>
		</td>
		<?php } ?>

		<td class='td_r'>
			<?php
				echo $tname1;
			?>
		</td>

		<?php if ($this->config['show_club_logo'] == 1) { ?>
		<td class='icon'>
			<?php
			//dynamic object property string
			$pic = '';
			$pic = 'home_' . $this->config['show_picture'];

			$type = 3;
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

		<td class='vs'>
			-
		</td>

		<?php if ($this->config['show_club_logo'] == 1) { ?>
		<td class='icon'>
			<?php
			//dynamic object property string
			$pic = '';
			$pic = 'away_' . $this->config['show_picture'];
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
					$pic = 'away_country';
					if($game->$pic != '' && !empty($game->$pic)) {
						echo Countries::getCountryFlag($game->$pic, 'height="11"');
					}
					break;
				case 'country_big':
					$type = 7;
					$pic = 'away_country';
					if($game->$pic != '' && !empty($game->$pic)) {
						echo Countries::getCountryFlag($game->$pic, 'height="50"');
					}
					break;
			}
			?>
		</td>
		<?php } ?>

		<td  class='td_l'>
			<?php
				echo $tname2;
			?>
		</td>

		<?php if ($this->config['show_referee'] == 1) { ?>
		<td>
			<?php
			$matchReferees = $this->model->getMatchReferees($game->match_id);
			foreach ($matchReferees AS $matchReferee)
			{
				$referee_link=JoomleagueHelperRoute::getRefereeRoute($game->project_id,$matchReferee->id);
				echo HTMLHelper::link($referee_link,$matchReferee->firstname." ".$matchReferee->lastname);
				echo '<br />';
			}
			?>
		</td>
		<?php } ;?>

		<?php if ($this->config['show_playground'] == 1) { ?>
		<td>
			<?php
			echo HTMLHelper::link($playground_link,$game->pl_name);
			?>
		</td>
		<?php } ;?>

		<?php
		$score="";
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

		if (empty($game->cancel) || $game->cancel == 0) { ?>
		<td class="td_r_right"><?php echo $e1; ?></td>
		<td class="td_r_center">-</td>
		<td class="td_r_left"><?php echo $e2; ?></td>
		<?php
		} else { ?>
		<td class="td_r_left" valign="top" colspan="3"><?php echo $game->cancel_reason; ?></td>
		<?php
		}
		if ($this->config['show_thumbs_picture'] == 1) {
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
		   } ?>
		<td>
			<?php if(isset($team1) && isset($team2) && ($team1 == $team2)) {
				echo HTMLHelper::image('media/com_joomleague/jl_images/draw.png', 'draw.png',
					array('title' => JText::_('COM_JOOMLEAGUE_CLUBPLAN_MATCH_DRAW'))) . '&nbsp;';
			} else {
				if($team1 > $team2) {
					echo HTMLHelper::image("media/com_joomleague/jl_images/thumbs_up.png", "thumbs_up.png",
						array("title" => JText::_('COM_JOOMLEAGUE_CLUBPLAN_MATCH_WON'))) . "&nbsp;";
				} elseif($team2 > $team1) {
					echo HTMLHelper::image("media/com_joomleague/jl_images/thumbs_down.png", "thumbs_down.png",
						array("title" => JText::_('COM_JOOMLEAGUE_CLUBPLAN_MATCH_LOST'))) . "&nbsp;";
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
	$k=1 - $k;
}
?>
</table>
<br />
<!-- END: matches -->
