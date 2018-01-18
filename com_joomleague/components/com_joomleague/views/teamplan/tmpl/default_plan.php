<?php 
/**
 * Joomleague
 *
 * @copyright	Copyright (C) 2006-2015 joomleague.at. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @link		http://www.joomleague.at
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Uri\Uri;
use Joomla\Registry\Registry;

if ($this->config['show_comments_count'] == 1 || $this->config['show_comments_count'] == 2)
{
	$plugin = JoomleagueFrontHelper::getCommentsIntegrationPlugin();
	$pluginParams = new Registry(is_object($plugin) ? $plugin->params : '');
	$separate_comments = $pluginParams->get('separate_comments', 0);
}
?>
<a name="jl_top" id="jl_top"></a>
<?php
if (!empty($this->matches))
{
	$app = Factory::getApplication();
	$input = $app->input;
	$teamid = $input->getInt('tid');
	$nbcols = 0;

	if ($this->config['show_plan_layout'] == 'plan_sorted_by_matchnumber')
	{
		//sort matches by match_number
		if ($this->config['plan_order'] == 'ASC')
		{
			usort($this->matches, function($a, $b) {
				return strnatcasecmp($a->match_number, $b->match_number);
			});
		}
		else
		{
			usort($this->matches, function($a, $b) {
				return strnatcasecmp($b->match_number, $a->match_number);
			});
		}
	}

	?>
<table class='fixtures'>
	<thead>
	<tr class='sectiontableheader'>
		<?php if ($this->config['show_events']): ?>
		<th>&nbsp;</th>
		<?php $nbcols++; endif; ?>

		<?php if ($this->config['show_matchday']): ?>
		<th><?php echo Text::_('COM_JOOMLEAGUE_TEAMPLAN_MATCHDAY'); ?></th>
		<?php $nbcols++; endif; ?>

		<?php if ($this->config['show_matchname']): ?>
		<th><?php echo Text::_('COM_JOOMLEAGUE_TEAMPLAN_MATCHNAME'); ?></th>
		<?php $nbcols++; endif; ?>

		<?php if ($this->config['show_match_number']): ?>
		<th><?php echo '&nbsp;'.Text::_('NUM').'&nbsp;'; ?></th>
		<?php $nbcols++; endif; ?>

		<?php if ($this->project->project_type=='DIVISIONS_LEAGUE' && $this->config['show_division']): ?>
		<th><?php echo Text::_('COM_JOOMLEAGUE_TEAMPLAN_DIVISION'); ?></th>
		<?php $nbcols++; endif; ?>

		<?php if ($this->config['show_playground'] || $this->config['show_playground_alert']): ?>
		<th><?php echo Text::_('COM_JOOMLEAGUE_TEAMPLAN_PLAYGROUND'); ?></th>
		<?php $nbcols++; endif; ?>

		<?php if ($this->config['show_date']): ?>
		<th><?php echo Text::_('COM_JOOMLEAGUE_TEAMPLAN_EDIT_DATE'); ?></th>
		<?php $nbcols++; endif; ?>

		<?php if ($this->config['show_time']): ?>
		<th><?php echo Text::_('COM_JOOMLEAGUE_TEAMPLAN_EDIT_TIME'); ?></th>
		<?php $nbcols++; endif; ?>

		<?php if ($this->config['show_time_present']): ?>
		<th><?php echo Text::_('COM_JOOMLEAGUE_TEAMPLAN_TIME_PRESENT'); ?></th>
		<?php $nbcols++; endif; ?>

	<?php
	switch ($this->config['result_style'])
	{
		case 1 :
			// Show home team marker
			?>
			<th class="right">
			<?php
			if ($this->config['show_home_guest_team_marker'] && !$this->config['switch_home_guest'])
			{
				echo Text::_('COM_JOOMLEAGUE_TEAMPLAN_HOME_TEAM');
			}
			elseif ($this->config['show_home_guest_team_marker'] && $this->config['switch_home_guest'])
			{
				echo Text::_('COM_JOOMLEAGUE_TEAMPLAN_AWAY_TEAM');
			}
			$nbcols++;
			?>
			</th>

			<!--Create space for logo home team-->
			<?php if ($this->config['show_logo_small']): ?>
			<th class="right">&nbsp;</th>
			<?php $nbcols++; endif;?>

			<!--Create room for the score to be displayed-->
			<th><?php echo Text::_('COM_JOOMLEAGUE_TEAMPLAN_RESULT'); ?></th>
			<?php $nbcols++; ?>

			<!--Create space for logo guest team-->
			<?php if ($this->config['show_logo_small']): ?>
			<th class="left">&nbsp;</th>';
			<?php $nbcols++; endif; ?>

			<!--Show guest team marker-->
			<th class="left">
			<?php
			if ($this->config['show_home_guest_team_marker'] && !$this->config['switch_home_guest'])
			{
				echo Text::_('COM_JOOMLEAGUE_TEAMPLAN_AWAY_TEAM');
			}
			elseif ($this->config['show_home_guest_team_marker'] && $this->config['switch_home_guest'])
			{
				echo Text::_('COM_JOOMLEAGUE_TEAMPLAN_HOME_TEAM');
			}
			$nbcols++;
			?>
			</th>
			<?php
			break;

		default :
		case 0 :
			?>
			<!--Show home team marker-->
			<th class="right">
			<?php
			if ($this->config['show_home_guest_team_marker'] && !$this->config['switch_home_guest'])
			{
				echo Text::_('COM_JOOMLEAGUE_TEAMPLAN_HOME_TEAM');
			}
			elseif ($this->config['show_home_guest_team_marker'] && $this->config['switch_home_guest'])
			{
				echo Text::_('COM_JOOMLEAGUE_TEAMPLAN_AWAY_TEAM');
			}
			$nbcols++;
			?>
			</th>

			<!--Create space for logo home team-->
			<?php if ($this->config['show_logo_small']): ?>
			<th class="right">&nbsp;</th>
			<?php $nbcols++; endif; ?>

			<th>&nbsp;</th>
			<?php $nbcols++; ?>

			<!--Create space for logo guest team-->
			<?php if ($this->config['show_logo_small']): ?>
			<th class="left">&nbsp;</th>
			<?php $nbcols++; endif; ?>

			<th class="left">
			<!--Show guest team marker-->
			<?php
			if ($this->config['show_home_guest_team_marker'] && !$this->config['switch_home_guest'])
			{
				echo Text::_('COM_JOOMLEAGUE_TEAMPLAN_AWAY_TEAM');
			}
			elseif ($this->config['show_home_guest_team_marker'] && $this->config['switch_home_guest'])
			{
				echo Text::_('COM_JOOMLEAGUE_TEAMPLAN_HOME_TEAM');
			}
			$nbcols++;
			?>
			</th>

			<th class="center"><?php echo Text::_('COM_JOOMLEAGUE_TEAMPLAN_RESULT'); ?></th>
			<?php
			$nbcols++;
			break;
	}
	?>

		<?php if ($this->config['show_referee']): ?>
		<th><?php echo Text::_('COM_JOOMLEAGUE_TEAMPLAN_REFEREE'); ?></th>
		<?php $nbcols++; endif; ?>

		<?php if ($this->config['show_thumbs_picture'] & $teamid > 0): ?>
		<th>&nbsp;</th>
		<?php $nbcols++; endif; ?>

		<?php if ($this->config['show_matchreport_column']): ?>
		<th><?php echo Text::_('COM_JOOMLEAGUE_MATCHREPORT_PAGE_TITLE'); ?></th>
		<?php $nbcols++; endif; ?>

		<?php if ($this->config['show_attendance_column']): ?>
		<th><?php echo Text::_('COM_JOOMLEAGUE_TEAMPLAN_ATTENDANCE'); ?></th>
		<?php $nbcols++; endif; ?>

		<?php if (($this->config['show_comments_count'] == 1 || $this->config['show_comments_count'] == 2) &&
			class_exists('JCommentsModel')): ?>
		<th><?php echo Text::_('COM_JOOMLEAGUE_TEAMPLAN_COMMENTS'); ?></th>
		<?php $nbcols++; endif; ?>
	</tr>
	</thead>
	<tbody>
	<?php
	$k = 0;
	$counter = 1;
	$round_date = '';
	foreach($this->matches as $match)
	{
		$hometeam = $this->teams[$match->projectteam1_id];
		$home_projectteam_id = $hometeam->projectteamid;
		$guestteam = $this->teams[$match->projectteam2_id];
		$guest_projectteam_id = $guestteam->projectteamid;
		$home = $hometeam->name;
		$away = $guestteam->name;
		$homeclub = $hometeam->club_id;
		$awayclub = $guestteam->club_id;
		$class = $k == 0 ? $this->config['style_class1'] : $this->config['style_class2'];
		$highlight = $match->team1 == $this->favteams ? 'highlight' : $class;
		$favStyle = '';
		if ($this->config['highlight_fav'] == 1 && !$teamid) {
			$isFavTeam = in_array($hometeam->id, $this->favteams) || in_array($guestteam->id, $this->favteams);
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
	<tr class='<?php echo $highlight; ?>'<?php echo $favStyle; ?>>
		<?php
		// start events
		if ($this->config['show_events'])
		{
			?>
		<td width='5' class='ko'>
		<?php
 			$events = $this->model->getMatchEvents($match->id);
 			$subs = $this->model->getMatchSubstitutions($match->id);

			if ($this->config['use_tabs_events']) {
			    $hasEvents = (count($events) + count($subs)) > 0;
			} else {
			    //no subs are shown when not using tabs for displaying events so don't check for that
			    $hasEvents = count($events) > 0;
			}

			if ($hasEvents)
			{
				$link = 'javascript:void(0);';
				$img = HTMLHelper::image('media/com_joomleague/jl_images/events.png', 'events.png');
				$params = array('title'   => Text::_('COM_JOOMLEAGUE_TEAMPLAN_EVENTS'),
								'onclick' => 'switchMenu(\'info'.$match->id.'\');return false;');
				echo HTMLHelper::link($link,$img,$params);
			}
		?>
		</td>
		<?php
		}
		else
		{
			$hasEvents = false;
		}
		// end events
		?>

		<?php if ($this->config['show_matchday']): ?>
		<td width='5%'>
			<?php
			$link=JoomleagueHelperRoute::getResultsRoute($this->project->slug, $match->roundid, $match->division_id);
			echo HTMLHelper::link($link, $match->roundcode);
			?>
		</td>
		<?php endif; ?>
		
		<?php if ($this->config['show_matchname']): ?>
		<td width='5%'><?php echo $match->name; ?></td>
		<?php endif; ?>

		<?php if ($this->config['show_match_number']): ?>
		<td><?php if (empty($match->match_number)){$match->match_number='-';} echo $match->match_number; ?></td>
		<?php endif; ?>

		<?php if (($this->project->project_type=='DIVISIONS_LEAGUE') && ($this->config['show_division'])): ?>
		<td><?php echo JoomleagueHelperHtml::showDivisonRemark($this->project->id, $hometeam, $guestteam, $this->config); ?></td>
		<?php endif; ?>

		<?php if (($this->config['show_playground'] || $this->config['show_playground_alert'])): ?>
		<td><?php JoomleagueHelperHtml::showMatchPlayground($this->project->id, $this->teams, $match, $this->config); ?></td>
		<?php endif; ?>

		<?php
		/*
		 echo JoomleagueModelTeamPlan::showPlayground(	$hometeam,
		 $guestteam,
		 $match,
		 $this->config['show_playground_alert'],
		 $this->config['show_playground'],
		 $match->project_id);
		 */
		?>

		<?php if ($this->config['show_date']): ?>
		<td width='10%'><?php
			if ($match->match_date)
			{
				echo JoomleagueHelper::getMatchDate($match, Text::_('COM_JOOMLEAGUE_GLOBAL_CALENDAR_DATE'));
			}
			else
			{
				echo '&nbsp;';
			}
			?>
		</td>
		<?php endif; ?>

		<?php if ($this->config['show_time']): ?>
		<td width='10%'>
			<?php echo JoomleagueHelperHtml::showMatchTime(	$match, $this->config, $this->overallconfig, $this->project); ?>
		</td>
		<?php endif; ?>

		<?php if ($this->config['show_time_present']): ?>
		<td width='10%'><?php echo empty($match->time_present) ? '-' : $match->time_present; ?></td>
		<?php endif; ?>

		<?php
		// Define some variables which will be used
		$teamA = '';
		$teamB = '';
		$score = '';

		// Check if the home and guest team should be switched around
		if ($this->config['switch_home_guest'])
		{
			$class1 = 'left';
			$class2 = 'right';
		}
		else
		{
			$class1 = 'right';
			$class2 = 'left';
		}
		if ($this->config['show_teamplan_link'])
		{
			$homelink = JoomleagueHelperRoute::getTeamPlanRoute($this->project->slug, $hometeam->team_slug);
			$awaylink = JoomleagueHelperRoute::getTeamPlanRoute($this->project->slug, $guestteam->team_slug);
		}
		else
		{
			$homelink = null;
			$awaylink = null;
		}
		$isFavTeam = in_array($hometeam->id, $this->favteams);
		$home = JoomleagueHelper::formatTeamName($hometeam, 'g' . $match->id . 't' . $hometeam->id,
			$this->config, $isFavTeam, $homelink);

		$teamA .= '<td class="' . $class1 . '">' . $home . '</td>';

		// Check if the user wants to show the club logo or country flag
		switch ($this->config['show_logo_small'])
		{
			case 1 :
				$teamA .= '<td class="'.$class1.'"> ' . JoomleagueModelProject::getClubIconHtml($hometeam, 1) . '</td>';
				$teamB .= '<td class="'.$class2.'"> ' . JoomleagueModelProject::getClubIconHtml($guestteam, 1) . '</td>';
				break;

			case 2 :
				$teamA .= '<td class="'.$class1.'">' . Countries::getCountryFlag($hometeam->country) . '</td>';
				$teamB .= '<td class="'.$class2.'">' . Countries::getCountryFlag($guestteam->country) . '</td>';
				break;

			case 3:
				$teamA .= '<td class="'.$class1.'">';
				$teamA .= JoomleagueHelper::getPictureThumb($hometeam->picture, $hometeam->name,
									$this->config['team_picture_width'], $this->config['team_picture_height'], 1);
				$teamA .= '</td>';

				$teamB .= '<td class="'.$class2.'">';
				$teamB .= JoomleagueHelper::getPictureThumb($guestteam->picture, $guestteam->name,
									$this->config['team_picture_width'], $this->config['team_picture_height'], 1);
				$teamB .= '</td>';
				break;
		}

		$separator ='<td width="10">' . $this->config['seperator'] . '</td>';

		$isFavTeam = in_array($guestteam->id, $this->favteams);
		$away = JoomleagueHelper::formatTeamName($guestteam, 'g' . $match->id . 't' . $guestteam->id,
			$this->config, $isFavTeam, $awaylink);
		$teamB .= '<td class="' . $class2 . '">' . $away . '</td>';

		if (!$match->cancel)
		{
            // In case show_part_results is true, then first check if the part results are available;
            // 'No part results available' occurs when teamX_result_split ONLY consists of zero or more ';' or NULL
            // (zero for projects with a single playing period, one or more for projects with two or more playing periods)
            $team1_result_split_present = preg_match('/^;*$|NULL/', $match->team1_result_split) == 0;
            $team2_result_split_present = preg_match('/^;*$|NULL/', $match->team2_result_split) == 0;

			if ($this->config['switch_home_guest'])
			{
				if (isset($match->team1_result) && isset($match->team2_result))
				{
					$result = '<strong>' . $match->team2_result . '&nbsp;' . $this->config['seperator'] .
						'&nbsp;' . $match->team1_result . '</strong>';
				}
				else
				{
					$result = '_&nbsp;' . $this->config['seperator'] . '&nbsp;_';
				}

				$part_results_left = explode(';', $match->team2_result_split);
				$part_results_right = explode(';', $match->team1_result_split);

				$leftResultOT	= $match->team2_result_ot;
				$rightResultOT	= $match->team1_result_ot;
				$leftResultSO	= $match->team2_result_so;
				$rightResultSO	= $match->team1_result_so;
				$leftResultDEC	= $match->team2_result_decision;
				$rightResultDEC	= $match->team1_result_decision;
			}
			else
			{
				if (isset($match->team1_result) && isset($match->team2_result))
				{
					$result = '<strong>' . $match->team1_result . '&nbsp;' . $this->config['seperator'] .
						'&nbsp;' . $match->team2_result . '</strong>';
				}
				else
				{
					$result = '_&nbsp;' . $this->config['seperator'] . '&nbsp;_';
				}

				$part_results_left = explode(';', $match->team1_result_split);
				$part_results_right = explode(';', $match->team2_result_split);

				$rightResultOT	= $match->team2_result_ot;
				$leftResultOT	= $match->team1_result_ot;
				$rightResultSO	= $match->team2_result_so;
				$leftResultSO	= $match->team1_result_so;
				$rightResultDEC	= $match->team2_result_decision;
				$leftResultDEC	= $match->team1_result_decision;
			}

			$SOTresult = '';
			$SOTtolltip = '';

            switch ($match->match_result_type)
            {
                case 2 :
					$result .= $this->config['result_style'] == 1 ? '<br />' : ' ';
					$result .= '(' . Text::_('COM_JOOMLEAGUE_RESULTS_SHOOTOUT') . ')';
					if (isset($leftResultOT))
					{
						$OTresultS = $leftResultOT . '&nbsp;' . $this->config['seperator'] . '&nbsp;' . $rightResultOT;
						$SOTresult .= '<br /><span class="hasTip" title="' . Text::_('COM_JOOMLEAGUE_RESULTS_OVERTIME2') .
							'::' . $OTresultS . '" >' . $OTresultS . '</span>';
						$SOTtolltip = ' | ' . $OTresultS;
					}
					if (isset($leftResultSO))
					{
						$SOresultS = $leftResultSO . '&nbsp;' . $this->config['seperator'] . '&nbsp;' . $rightResultSO;
						$SOTresult .= '<br /><span class="hasTip" title="' . Text::_('COM_JOOMLEAGUE_RESULTS_SHOOTOUT2') .
							'::' . $SOresultS . '" >' . $SOresultS . '</span>';
						$SOTtolltip = ' | ' . $SOresultS;
					}
					break;

				case 1 :
					$result .= $this->config['result_style'] == 1 ? '<br />' : ' ';
					$result .= '('.Text::_('COM_JOOMLEAGUE_RESULTS_OVERTIME') . ')';
					if (isset($leftResultOT))
					{
						$OTresultS = $leftResultOT . '&nbsp;' . $this->config['seperator'] . '&nbsp;' . $rightResultOT;
						$SOTresult .= '<br /><span class="hasTip" title="' . Text::_('COM_JOOMLEAGUE_RESULTS_OVERTIME2') .
							'::' . $OTresultS . '" >' . $OTresultS . '</span>';
						$SOTtolltip = ' | ' . $OTresultS ;
					}
					break;
			}

			//Link
			$link = isset($match->team1_result) ?
				JoomleagueHelperRoute::getMatchReportRoute($this->project->slug,$match->id) :
				JoomleagueHelperRoute::getNextMatchRoute($this->project->slug,$match->id);

			$ResultsTooltipTitle = $result;

			if ($this->config['results_linkable'] == 1)
			{
				$result = HTMLHelper::link($link, $result);
			}

			$ResultsTooltipTp = '(';
			$PartResult = '';
			if ($team1_result_split_present && $team2_result_split_present)
			{
				//Part results
				if (!is_array($part_results_left))
				{
					$part_results_left = array($part_results_left);
				}
				if (!is_array($part_results_right))
				{
					$part_results_right = array($part_results_right);
				}

				for ($i = 0; $i < count($part_results_left); $i++)
				{
					if (isset($part_results_left[$i]))
					{
						$resultS = $part_results_left[$i] . '&nbsp;' . $this->config['seperator'] . '&nbsp;' . $part_results_right[$i];
						$whichPeriod = $i + 1;
						$PartResult .= '<br /><span class="hasTip" title="' . Text::sprintf('COM_JOOMLEAGUE_GLOBAL_NPART',  "$whichPeriod")
							. '::' . $resultS . '" >' . $resultS . '</span>';
						$ResultsTooltipTp .= $i != 0 ? ' | ' . $resultS : $resultS;
					}
				}
			}
			$ResultsTooltipTp .= $SOTtolltip . ')';

			if ($team1_result_split_present && $team2_result_split_present)
			{
				if ($this->config['show_part_results'])
				{
					$result .= $PartResult . $SOTresult;
				}
				else
				{
					//No need to show a tooltip if the parts are shown anyways
					$result = '<span class="hasTip" title="' .$ResultsTooltipTitle . '::' . $ResultsTooltipTp . '" >' . $result . '</span>';
				}
			}

			if ($match->alt_decision)
			{
				$result = '<b style="color:red;">';
				$result .= $leftResultDEC . '&nbsp;' . $this->config['seperator'] . '&nbsp;' . $rightResultDEC;
				$result .= '</b>';
			}

			$score = "<td align='center'>" . $result . '</td>';
		}
		else
		{
			$score = '<td>' . Text::_($match->cancel_reason) . '</td>';
		}

		switch ($this->config['result_style'])
		{
			case 1 :
				echo $this->config['switch_home_guest'] ? $teamB.$score.$teamA : $teamA.$score.$teamB;
				break;

			default;
			case 0 :
				echo $this->config['switch_home_guest'] ? $teamB.$separator.$teamA.$score : $teamA.$separator.$teamB.$score;
				break;
		}
		?>

		<?php
		if ($this->config['show_referee'])
		{
			?>
		<td><?php
		if (isset($match->referees) && (count($match->referees)>0))
		{
			if ($this->project->teams_as_referees)
			{
				$output = '';
				$toolTipTitle = Text::_('COM_JOOMLEAGUE_TEAMPLAN_REF_TOOLTIP');
				$toolTipText = '';

				for ($i = 0; $i < count($match->referees); $i++)
				{
					if ($match->referees[$i]->referee_name != '')
					{
						$output .= $match->referees[$i]->referee_name;
						$toolTipText .= $match->referees[$i]->referee_name . '&lt;br /&gt;';
					}
					else
					{
						$output .= '-';
						$toolTipText .= '-&lt;br /&gt;';
					}
				}
				if ($this->config['show_referee'] == 1)
				{
					echo $output;
				}
				elseif ($this->config['show_referee'] == 2)
				{
				?>
					<span class='hasTip' title='<?php echo $toolTipTitle; ?> :: <?php echo $toolTipText; ?>'>
						<img src='<?php echo Uri::root(); ?>media/com_joomleague/jl_images/icon-16-Referees.png' alt='' title='' />
					</span>
				<?php
				}
			}
			else
			{
				$output = '';
				$toolTipTitle = Text::_('COM_JOOMLEAGUE_TEAMPLAN_REF_TOOLTIP');
				$toolTipText = '';
				for ($i = 0; $i < count($match->referees); $i++)
				{
					if ($match->referees[$i]->referee_lastname != '' && $match->referees[$i]->referee_firstname)
					{
						$output .= '<span class="hasTip" title="' . Text::_('COM_JOOMLEAGUE_TEAMPLAN_REF_FUNCTION') .
							'::' . $match->referees[$i]->referee_position_name . '">';
						$ref = $match->referees[$i]->referee_lastname . ',' . $match->referees[$i]->referee_firstname;
						$toolTipText .= $ref . ' (' . $match->referees[$i]->referee_position_name . ')' . '&lt;br /&gt;';
						if ($this->config['show_referee_link'])
						{
							$link = JoomleagueHelperRoute::getRefereeRoute($this->project->slug, $match->referees[$i]->referee_id, 3);
							$ref = HTMLHelper::link($link, $ref);
						}
						$output .= $ref . '</span>';

						if (($i + 1) < count($match->referees))
						{
							$output .= ' - ';
						}
					}
					else
					{
						$output .= '-';
					}
				}

				if ($this->config['show_referee'] == 1)
				{
					echo $output;
				}
				elseif ($this->config['show_referee']==2)
				{
					?>
					<span class='hasTip' title='<?php echo $toolTipTitle; ?> :: <?php echo $toolTipText; ?>'>
						<img src='<?php echo Uri::root(); ?>media/com_joomleague/jl_images/icon-16-Referees.png' alt='' title='' />
					</span>
					<?php
				}
			}
		}
		else
		{
			echo '-';
		}
		?>
		</td>
		<?php
		}
		?>

		<?php if (($this->config['show_thumbs_picture']) & ($teamid>0)): ?>
		<td><?php echo JoomleagueHelperHtml::getThumbUpDownImg($match, $this->ptid); ?></td>
		<?php endif; ?>

		<?php if ($this->config['show_matchreport_column']): ?>
		<td>
		<?php
		if (!$match->cancel) {
			if (isset($match->team1_result))
			{
				$href_text = $this->config['show_matchreport_image']
					? HTMLHelper::image($this->config['matchreport_image'], Text::_('COM_JOOMLEAGUE_TEAMPLAN_VIEW_MATCHREPORT'))
					: Text::_('COM_JOOMLEAGUE_TEAMPLAN_VIEW_MATCHREPORT');
				$link = JoomleagueHelperRoute::getMatchReportRoute($this->project->slug, $match->id);
			}
			else
			{
				$href_text = $this->config['show_matchreport_image']
					? HTMLHelper::image($this->config['matchpreview_image'], Text::_('COM_JOOMLEAGUE_TEAMPLAN_VIEW_MATCHPREVIEW'))
					: Text::_('COM_JOOMLEAGUE_TEAMPLAN_VIEW_MATCHPREVIEW');
				$link = JoomleagueHelperRoute::getNextMatchRoute($this->project->slug, $match->id);
			}
			echo HTMLHelper::link($link, $href_text);
		}
		?>
		</td>
		<?php endif; ?>

		<?php if ($this->config['show_attendance_column']): ?>
		<td class="center"><?php if ($match->crowd == 0){$match->crowd='';} echo $match->crowd; ?></td>
		<?php endif; ?>

		<?php if (($this->config['show_comments_count'] == 1 || $this->config['show_comments_count'] == 2)
			&& class_exists('JCommentsModel')): ?>
		<td class="center">
			<?php
			if ($separate_comments) {
				// Comments integration trigger when separate_comments in plugin is set to yes/1
				if (isset($match->team1_result))
				{
					$joomleague_comments_object_group = 'com_joomleague_matchreport';
				}
				else {
					$joomleague_comments_object_group = 'com_joomleague_nextmatch';
				}
			}
			else {
				// Comments integration trigger when separate_comments in plugin is set to no/0
				$joomleague_comments_object_group = 'com_joomleague';
			}

			$options 					= array();
			$options['object_id']		= (int) $match->id;
			$options['object_group']	= $joomleague_comments_object_group;
			$options['published']		= 1;

			$count = JCommentsModel::getCommentsCount($options);

			if ($count == 1)
			{
				$imgTitle = $count . ' ' . Text::_('COM_JOOMLEAGUE_TEAMPLAN_COMMENTS_COUNT_SINGULAR');
				if ($this->config['show_comments_count'] == 1)
				{
					$href_text = HTMLHelper::image('media/com_joomleague/jl_images/discuss_active.gif', $imgTitle, array(' title' => $imgTitle,' border' => 0,' style' => 'vertical-align: middle'));
				}
				elseif ($this->config['show_comments_count'] == 2)
				{
					$href_text = '<span title="' . $imgTitle . '">(' . $count . ')</span>';
				}
				$link = isset($match->team1_result)
					? JoomleagueHelperRoute::getMatchReportRoute($this->project->slug, $match->id) . '#comments'
					: JoomleagueHelperRoute::getNextMatchRoute($this->project->slug, $match->id) . '#comments';
				echo HTMLHelper::link($link, $href_text);
			}
			elseif ($count > 1)
			{
				$imgTitle = $count . ' ' . Text::_('COM_JOOMLEAGUE_TEAMPLAN_COMMENTS_COUNT_PLURAL');
				if ($this->config['show_comments_count'] == 1)
				{
					$href_text = HTMLHelper::image('media/com_joomleague/jl_images/discuss_active.gif', $imgTitle, array(' title' => $imgTitle,' border' => 0,' style' => 'vertical-align: middle'));
				}
				elseif ($this->config['show_comments_count'] == 2)
				{
					$href_text = '<span title="'. $imgTitle .'">('.$count.')</span>';
				}
				$link = isset($match->team1_result)
					? JoomleagueHelperRoute::getMatchReportRoute($this->project->slug,$match->id).'#comments'
					: JoomleagueHelperRoute::getNextMatchRoute($this->project->slug,$match->id).'#comments';
				echo HTMLHelper::link($link, $href_text);
			}
			else
			{
				$imgTitle = Text::_('COM_JOOMLEAGUE_TEAMPLAN_COMMENTS_COUNT_NOCOMMENT');
				if ($this->config['show_comments_count'] == 1)
				{
					$href_text = HTMLHelper::image('media/com_joomleague/jl_images/discuss.gif', $imgTitle, array(' title' => $imgTitle,' border' => 0,' style' => 'vertical-align: middle'));
				}
				elseif ($this->config['show_comments_count'] == 2)
				{
					$href_text		= '<span title="'. $imgTitle .'">('.$count.')</span>';
				}
				$link = isset($match->team1_result)
					? JoomleagueHelperRoute::getMatchReportRoute($this->project->slug,$match->id).'#comments'
					: JoomleagueHelperRoute::getNextMatchRoute($this->project->slug,$match->id).'#comments';
				echo HTMLHelper::link($link, $href_text);
			}
		?>
		</td>
		<?php endif; ?>
	</tr>
	<?php if ($hasEvents): ?>
	<!-- Show icon for editing events in edit mode -->
	<tr class="events <?php echo ($k == 0) ? '' : 'alt'; ?>">
		<td colspan="<?php echo $nbcols; ?>">
			<div id="info<?php echo $match->id; ?>" style="display: none;">
				<table class="matchreport">
					<tr>
						<td>
							<?php echo $this->showEventsContainerInResults(
								$match, $this->projectevents, $events, $subs, $this->config);
							?>
						</td>
					</tr>
				</table>
			</div>
		</td>
	</tr>
	<?php endif; ?>

	<?php
	$k = 1 - $k;
	$counter++;
	}
	?>
	</tbody>
</table>
	<?php
}
else
{
	?>
<h3><?php echo Text::_('COM_JOOMLEAGUE_TEAMPLAN_NO_MATCHES'); ?></h3>
	<?php
}
?>
