<?php 
/**
 * Joomleague
 *
 * @copyright	Copyright (C) 2006-2015 joomleague.at. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @link		http://www.joomleague.at
 */

defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\Registry\Registry;

$nbcols = 6;
$nbcols_header = 0;
$dates = $this->sortByDate();

if ($this->config['show_division']) { $nbcols++; }
if ($this->config['show_match_number']) { $nbcols++; }
if ($this->config['show_events']) { $nbcols++; }
if ($this->config['show_time']) { $nbcols++; }
if ($this->config['show_playground'] || $this->config['show_playground_alert']) { $nbcols = $nbcols + 2; }
if ($this->config['show_referee']) { $nbcols++; }
if ($this->config['result_style']==2) { $nbcols++; }
if ($this->config['show_attendance_column']) { $nbcols++; $nbcols_header++; }

if ($this->config['show_comments_count'] > 0)
{
	$nbcols++;
	$nbcols_header++;

	$plugin = JoomleagueFrontHelper::getCommentsIntegrationPlugin();
	$registryData = is_object($plugin) ? $plugin->params : '';
	$pluginParams = new Registry($registryData);
	$separate_comments = $pluginParams->get('separate_comments', 0);
}
?>

<table class='fixtures-results'>
	<?php
	foreach($dates as $date => $games)
	{
		?>
	<!-- DATE HEADER -->
	<tr class="sectiontableheader">
		<?php if (($this->config['show_attendance_column']) || ($this->config['show_comments_count'] > 0)): ?>
		<th colspan="<?php echo $nbcols-$nbcols_header; ?>">
            <?php 
            if ($date != '0000-00-00') {
               echo HTMLHelper::date($date, 
								Text::_('COM_JOOMLEAGUE_RESULTS_GAMES_DATE'),
								JoomleagueHelper::getTimezone($this->project, $this->overallconfig));
               if ($this->config['show_matchday_dateheader']) {
                  echo ' - ';
               }
            }
            if ($this->config['show_matchday_dateheader']) {
               echo Text::sprintf('COM_JOOMLEAGUE_RESULTS_GAMEDAY_NB',$this->roundcode); 
            } 
            ?>
		</th>

		<?php if ($this->config['show_attendance_column']): ?>
		<th class="right"><?php echo Text::_('COM_JOOMLEAGUE_RESULTS_ATTENDANCE'); ?></th>
		<?php
		endif;

		if ($this->config['show_comments_count'] > 0): ?>
		<th class="center"><?php echo Text::_('COM_JOOMLEAGUE_RESULTS_COMMENTS'); ?></th>
		<?php
		endif;

		else: ?>
		<th colspan="<?php echo $nbcols; ?>">
            <?php
            if ($date != '0000-00-00') {
               echo HTMLHelper::date($date,
								Text::_('COM_JOOMLEAGUE_RESULTS_GAMES_DATE'),
								JoomleagueHelper::getTimezone($this->project, $this->overallconfig));
               if ($this->config['show_matchday_dateheader']) {
                  echo ' - ';
               }
            }

            if ($this->config['show_matchday_dateheader']) {
               echo Text::sprintf('COM_JOOMLEAGUE_RESULTS_GAMEDAY_NB',$this->roundcode);
            }
            ?>
		</th>
		<?php endif; ?>
	</tr>
	<!-- DATE HEADER END-->
	<!-- GAMES -->
	<?php
	$k = 0;
	foreach($games as $game)
	{
		$this->game = $game;
		// TODO: can it really occur that one of the project team IDs is zero? Otherwise we can remove those checks.
		if ($game->published && $game->projectteam1_id != 0 && $game->projectteam2_id != 0)
		{
			$report_link =  isset($game->team1_result)
				? JoomleagueHelperRoute::getMatchReportRoute($this->project->slug, $game->slug)
				: JoomleagueHelperRoute::getNextMatchRoute($this->project->slug, $game->slug);

			$events	= $this->model->getMatchEvents($game->id);
			$subs	= $this->model->getMatchSubstitutions($game->id);

			$hasEvents = $this->config['show_events'] &&
				$this->config['use_tabs_events'] ? count($events) + count($subs) > 0 : count($events) > 0;
			//no subs are shown when not using tabs for displaying events so don't check for that

			if($this->config['switch_home_guest'])
			{
				$team1 = $this->teams[$game->projectteam2_id];
				$team2 = $this->teams[$game->projectteam1_id];
			}
			else
			{
				$team1 = $this->teams[$game->projectteam1_id];
				$team2 = $this->teams[$game->projectteam2_id];
			}
			$favStyle 	= '';
			$color		= '';
			$isFavTeam = in_array($team1->id, $this->favteams) || in_array($team2->id, $this->favteams);
			if ($isFavTeam && $this->project->fav_team_highlight_type == 1 && $this->config['highlight_fav'] == 1)
			{
				if(trim($this->project->fav_team_color) != "")
				{
					$color = trim($this->project->fav_team_color);
				}
				$format = "%s";
				$favStyle = ' style="';
				$favStyle .= $this->project->fav_team_text_bold != '' ? 'font-weight:bold;' : '';
				$favStyle .= trim($this->project->fav_team_text_color) != '' ? 'color:' . trim($this->project->fav_team_text_color) . ';' : '';
				$favStyle .= $color != '' ? 'background-color:' . $color . ';' : '';
				if ($favStyle != ' style="')
				{
				  $favStyle .= '"';
				}
				else {
				  $favStyle = '';
				}
			}
			?>

	<tr class="<?php echo ($k==0)? $this->config['style_class1'] : $this->config['style_class2']; ?>"<?php echo $favStyle; ?>>
		<?php if ($this->config['show_match_number']): ?>
		<td width="5" class="ko"><?php echo $game->match_number > 0 ? $game->match_number : "&nbsp;"; ?></td>
		<?php endif;?>

		<?php if ($this->config['show_events']): ?>
		<td width="20" class="ko">
			<?php
			if ($hasEvents)
			{
				$link = "javascript:void(0);";
				$img = HTMLHelper::image('media/com_joomleague/jl_images/events.png', 'events.png');
				$params = array("title"   => Text::_('COM_JOOMLEAGUE_TEAMPLAN_EVENTS'),
								"onclick" => 'switchMenu(\'info'.$game->id.'\');return false;');
				echo HTMLHelper::link($link,$img,$params);
			}
			else
			{
				echo "&nbsp;";
			} ?>
		</td>
		<?php endif; ?>

		<?php if ($this->config['show_division']): ?>
			<td width="5" class="ko" nowrap="nowrap">
			<?php
			echo JoomleagueHelperHtml::showDivisonRemark($this->project->id, $this->teams[$game->projectteam1_id],
				$this->teams[$game->projectteam2_id], $this->config); ?>
			</td>
		<?php endif; ?>

		<?php if ($this->config['show_time']): ?>
		<td width='5' class='ko'>
			<abbr title='' class='dtstart'>
				<?php echo JoomleagueHelperHtml::showMatchTime($game, $this->config, $this->overallconfig, $this->project); ?>
			</abbr>
		</td>
		<?php endif; ?>

		<?php if (($this->config['show_playground'] || $this->config['show_playground_alert'])): ?>
		<td><?php
			JoomleagueHelperHtml::showMatchPlayground($this->project->id, $this->teams, $match, $this->config); ?></td>
		<?php endif; ?>

		<?php
		//--------------------------------------------------------------------------------------------------------------
		if ($this->config['result_style'] == 0): ?>
		<td width='20'>
			<?php echo $this->getTeamClubIcon($team1, $this->config['show_logo_small'], array('class' => 'teamlogo')); ?>
		</td>
		<td>
			<?php
				$isFavTeam = in_array($team1->id, $this->favteams);
				echo JoomleagueHelper::formatTeamName($team1, 'g' . $game->id, $this->config, $isFavTeam);
			?>
		</td>

		<td width='20'>
			<?php echo $this->getTeamClubIcon($team2, $this->config['show_logo_small'], array('class' => 'teamlogo')); ?>
		</td>
		<td>
			<?php
				$isFavTeam = in_array($team2->id, $this->favteams);
				echo JoomleagueHelper::formatTeamName($team2, 'g' . $game->id, $this->config, $isFavTeam);
			?>
		</td>
		<td width='10' class='score'>
			<?php echo $this->formatResult($this->teams[$game->projectteam1_id], $this->teams[$game->projectteam2_id], $game, $report_link); ?>
		</td>
		<?php
		//--------------------------------------------------------------------------------------------------------------
		elseif ($this->config['result_style'] == 1): ?>
		<td class='right'>
			<?php
				$isFavTeam = in_array($team1->id, $this->favteams);
				echo JoomleagueHelper::formatTeamName($team1,'g'.$game->id,$this->config,$isFavTeam);
			?>
		</td>
		<td width='20'>
			<?php echo $this->getTeamClubIcon($team1, $this->config['show_logo_small'], array('class' => 'teamlogo')); ?>
		</td>
		<td width='5' class='score' nowrap='nowrap'>
			<?php
				echo '&nbsp;';
				echo $this->formatResult($this->teams[$game->projectteam1_id], $this->teams[$game->projectteam2_id], $game, $report_link);
				echo '&nbsp;';
			?>
		</td>

		<td width='20'>
			<?php echo $this->getTeamClubIcon($team2, $this->config['show_logo_small'], array('class' => 'teamlogo')); ?>
		</td>
		<td class='left'>
			<?php
				$isFavTeam = in_array($team2->id, $this->favteams);
				echo JoomleagueHelper::formatTeamName($team2,'g'.$game->id,$this->config,$isFavTeam);
			?>
		</td>
		<?php
		//--------------------------------------------------------------------------------------------------------------
		elseif ($this->config['result_style'] == 2): ?>
		<td class='right'>
			<?php
				$isFavTeam = in_array($team1->id, $this->favteams);
				echo JoomleagueHelper::formatTeamName($team1,'g'.$game->id,$this->config,$isFavTeam);
			?>
		</td>
		<td width='20'>
			<?php echo $this->getTeamClubIcon($team1, $this->config['show_logo_small'], array('class' => 'teamlogo')); ?>
		</td>
		<td width='5'>
		-
		</td>

		<td width='20'>
			<?php echo $this->getTeamClubIcon($team2, $this->config['show_logo_small'], array('class' => 'teamlogo')); ?>
		</td>
		<td class='left'>
			<?php
				$isFavTeam = in_array($team2->id, $this->favteams);
				echo JoomleagueHelper::formatTeamName($team2,'g'.$game->id,$this->config,$isFavTeam);
			?>
		</td>
		<td width='5' class='score' nowrap='nowrap'>
			<?php
				echo '&nbsp;';
				echo $this->formatResult($this->teams[$game->projectteam1_id], $this->teams[$game->projectteam2_id], $game, $report_link);
				echo '&nbsp;';
			?>
		</td>
		<?php endif; ?>

		<!-- show hammer if there is a alternative decision of the score -->
		<td width="20" class="ko"><?php $this->showReportDecisionIcons($game); ?></td>

		<?php if($this->config['show_referee']): ?>
		<td width="20" class="referees"><?php $this->showMatchRefereesAsTooltip($game); ?></td>
		<?php endif; ?>

		<?php if ($this->config['show_playground'] || $this->config['show_playground_alert']): ?>
		<td><?php JoomleagueHelperHtml::showMatchPlayground($this->project->id, $this->teams, $game, $this->config); ?></td>
		<?php endif; ?>

		<?php if ($this->config['show_attendance_column']): ?>
		<td class="right"><?php echo $game->crowd > 0? $game->crowd : '&nbsp;'; ?></td>
		<?php endif; ?>

		<?php if ($this->config['show_comments_count'] > 0): ?>
		<td class="center">
			<?php
			$joomleague_comments_object_group = $separate_comments
				? isset($game->team1_result) ? 'com_joomleague_matchreport' : 'com_joomleague_nextmatch'
				:'com_joomleague';

			$options 					= array();
			$options['object_id']		= (int) $game->id;
			$options['object_group']	= $joomleague_comments_object_group;
			$options['published']		= 1;
			
			$count = 0;
			$plugin = JoomleagueFrontHelper::getCommentsIntegrationPlugin();
			if (is_object($plugin))
			{
				$count = JCommentsModel::getCommentsCount($options);
			}
			if ($count == 1)
			{
				$imgTitle = $count . ' ' . Text::_('COM_JOOMLEAGUE_RESULTS_COMMENTS_COUNT_SINGULAR');
				$imgFilename = 'discuss_active.gif';
			}
			elseif ($count > 1)
			{
				$imgTitle = $count . ' ' . Text::_('COM_JOOMLEAGUE_RESULTS_COMMENTS_COUNT_PLURAL');
				$imgFilename = 'discuss_active.gif';
			}
			else
			{
				$imgTitle = Text::_('COM_JOOMLEAGUE_RESULTS_COMMENTS_COUNT_NOCOMMENT');
				$imgFilename = 'discuss.gif';
			}
			if ($this->config['show_comments_count'] == 1)
			{
				$href_text = HTMLHelper::image('media/com_joomleague/jl_images/' . $imgFilename, $imgTitle,
					array(' title' => $imgTitle,' border' => 0,' style' => 'vertical-align: middle'));
			}
			elseif ($this->config['show_comments_count'] == 2)
			{
				$href_text = '<span title="'. $imgTitle .'">('.$count.')</span>';
			}
			$link = isset($game->team1_result)
				? JoomleagueHelperRoute::getMatchReportRoute($this->project->slug, $game->slug).'#comments'
				: JoomleagueHelperRoute::getNextMatchRoute($this->project->slug, $game->slug).'#comments';
			echo HTMLHelper::link($link, $href_text);
		?>
		</td>
		<?php endif; ?>
	</tr>

	<?php if ($hasEvents): ?>
	<!-- show icon for editing events in edit mode -->
	<tr class="events <?php echo $k == 0 ? '' : 'alt'; ?>">
		<td colspan="<?php echo $nbcols; ?>">
			<div id="info<?php echo $game->id; ?>" style="display: none;">
				<table class='matchreport' border='0'>
					<tr>
						<td>
							<?php echo $this->showEventsContainerInResults($game, $this->projectevents, $events, $subs,
								$this->config); ?>
						</td>
					</tr>
				</table>
			</div>
		</td>
	</tr>
	<?php endif;

	$k = 1 - $k;
		}
	}
	?>
	<!-- GAMES END -->
	<?php
	}
	?>
</table>
<br />
