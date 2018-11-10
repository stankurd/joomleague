<?php 
use Joomla\CMS\Factory;
use Joomla\CMS\Filesystem\File;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;

defined('_JEXEC') or die;

jimport('joomla.filesystem.file');

require_once JPATH_COMPONENT.'/helpers/pagination.php';

/**
 * View-Results
 */
class JoomleagueViewResults extends JLGView
{
	var $model = null;
	var $overallconfig = null;
	var $config = null;
	var $project = null;
	var $lists = null;
	var $teams = null;
	var $showediticon = 0;
	var $division = null;
	var $matches = null;
	var $roundid = 0;
	var $roundcode = 0;
	var $rounds = null;
	var $favteams = null;
	var $asprojectevents = null;
	var $isAllowed = 0;
	var $pageTitle = null;

	public function display($tpl = null)
	{
		$model	= $this->getModel();
		$this->overallconfig = $model->getOverallConfig();
		$this->config = $model->getTemplateConfig($this->getName());
		$this->project = $model->getProject();
		$division_id = $model->getDivisionID();
		$matches = $model->getMatches();
		$mdlRound = BaseDatabaseModel::getInstance('Round', 'JoomleagueModel');
		$roundcode = $mdlRound->getRoundcode($model->roundid);
		$rounds = JoomleagueHelper::getRoundsOptions($this->project->id, 'ASC', true);

		//add js file // TODO is this really needed here?
		//HTMLHelper::_('behavior.framework');

		$this->lists=array();
		if (isset($this->project))
		{
			$this->teams = $model->getTeamsIndexedByPtid($division_id);
			$this->showediticon = $model->getShowEditIcon();
			$this->division = $model->getDivision();
			$this->matches = $matches;
			$this->roundid = $model->roundid;
			$this->roundcode = $roundcode;
			$rounds = $model->getRounds();
			$this->rounds = $rounds;
			$options = $this->getRoundSelectNavigation($rounds, $division_id);
			$this->matchdaysoptions = $options;
			$this->favteams = $model->getFavTeams(/*$project*/);
			$this->asprojectevents = $model->getProjectEvents();
			$this->model = $model;
			$this->isAllowed = $model->isAllowed();

			$this->lists['rounds'] = $rounds;

			if (!isset($this->config['switch_home_guest']))
			{
				$this->config['switch_home_guest'] = 0;
			}
			if (!isset($this->config['show_dnp_teams_icons']))
			{
				$this->config['show_dnp_teams_icons'] = 0;
			}
			if (!isset($this->config['show_results_ranking']))
			{
				$this->config['show_results_ranking'] = 0;
			}
			
		}
		$this->setPageTitle();
		$option = Factory::getApplication()->input->get('option');
		$this->setFeed($option);
		$this->setStyleSheet($option);

		parent::display($tpl);
	}

	function setPageTitle()
	{
		$titleInfo = JoomleagueHelper::createTitleInfo(Text::_('COM_JOOMLEAGUE_RESULTS_PAGE_TITLE'));
		if (!empty($this->project))
		{
			$titleInfo->projectName = $this->project->name;
			$titleInfo->leagueName = $this->project->league_name;
			$titleInfo->seasonName = $this->project->season_name;
		}
		if (!empty($this->division) && $this->division->id != 0)
		{
			$titleInfo->divisionName = $this->division->name;
		}
		$this->pageTitle=JoomleagueHelper::formatTitle($titleInfo, $this->config['page_title_format']);
		$document = Factory::getDocument();
		$document->setTitle($this->pageTitle);
	}

	function setFeed($option)
	{
		//build feed links
		$feed = 'index.php?option='.$option.'&view=results&p='.$this->project->id.'&format=feed';
		$rss = array('type' => 'application/rss+xml', 'title' => Text::_('COM_JOOMLEAGUE_RESULTS_RSSFEED'));

		// add the links
		$document = Factory::getDocument();
		$document->addHeadLink(Route::_($feed.'&type=rss'), 'alternate', 'rel', $rss);
	}

	function setStyleSheet($option)
	{
		$version 	= urlencode(JoomleagueHelper::getVersion());
		$css		= 'components/'.$option.'/assets/css/tabs.css?v='.$version;
		$document = Factory::getDocument();
		$document->addStyleSheet($css);
	}

	/**
	 * return html code for not playing teams
	 *
	 * @param array $games
	 * @param array $teams
	 * @param array $config
	 * @param array $favteams
	 * @param object $project
	 * @return string html
	 */
	public function showNotPlayingTeams(&$games, &$teams, &$config, &$favteams, &$project)
	{
		$output='';
		$playing_teams=array();
		foreach($games as $game)
		{
			$this->addPlayingTeams($playing_teams,$game->projectteam1_id,$game->projectteam2_id,$game->published);
		}
		$x=0;
		$not_playing=count($teams) - count($playing_teams);
		if ($not_playing > 0)
		{
			$output .= '<b>'.Text::sprintf('COM_JOOMLEAGUE_RESULTS_TEAMS_NOT_PLAYING',$not_playing).'</b> ';
			foreach ($teams AS $id => $team)
			{
				if (isset($team->projectteamid) && in_array($team->projectteamid,$playing_teams))
				{
					continue; //if team is playing, go to next
				}
				if ($x > 0)
				{
					$output .= ', ';
				}
				if ($config['show_logo_small'] > 0 && $config['show_dnp_teams_icons'])
				{
					$output .= $this->getTeamClubIcon($team, $config['show_logo_small']) . '&nbsp;';
				}
				$isFavTeam = in_array($team->id, $favteams);
				$output .= JoomleagueHelper::formatTeamName($team, 't'.$team->id, $config, $isFavTeam);
				$x++;
			}
		}
		return $output;
	}

	public function addPlayingTeams(&$playing_teams, $hometeam, $awayteam, $published = false)
	{
		if ($hometeam > 0 && !in_array($hometeam, $playing_teams) && $published)
		{
			$playing_teams[] = $hometeam;
		}
		if ($awayteam > 0 && !in_array($awayteam, $playing_teams) && $published)
		{
			$playing_teams[] = $awayteam;
		}
	}

	/**
	 * returns html <img> for club assigned to team
	 * @param object team
	 * @param int type=1 for club small image,or 2 for club country
	 * @param boolean $with_space
	 * @return string html
	 */
	public function getTeamClubIcon($team, $type = 1, $attribs = array())
	{
		$image = '';
		if (isset($team->name))
		{
			$title = $team->name;
			$attribs = array_merge(array('title' => $title, $attribs));
			if ($type == 1)
			{
				if (!empty($team->logo_small) && File::exists($team->logo_small))
				{
					$image=HTMLHelper::image($team->logo_small, $title, $attribs);
				}
				else
				{
					$image=HTMLHelper::image(JoomleagueHelper::getDefaultPlaceholder('clublogosmall'), $title, $attribs);
				}
			}
			elseif ($type == 2 && !empty($team->country))
			{
				$image = Countries::getCountryFlag($team->country);
				if (empty($image))
				{
					$image=HTMLHelper::image(JoomleagueHelper::getDefaultPlaceholder('icon'), $title, $attribs);
				}
			}
			else
			{
				$image='';
			}
		}
		return $image;
	}

	/**
	 * return an array of matches indexed by date
	 *
	 * @return array
	 */
	public function sortByDate()
	{
		$dates = array();
		foreach ((array) $this->matches as $m)
		{
			if (empty($m->match_date))
			{
				$matchDate = '0000-00-00';
			}
			else
			{
				$matchDate = JoomleagueHelper::getMatchDate($m);
			}
			$dates[$matchDate][] = $m;
		}
		return $dates;
	}

	/**
	 * formats the score according to settings
	 *
	 * @param object $game
	 * @param array $config
	 * @return string
	 */
	function formatScoreInline($game)
	{
		if ($this->config['switch_home_guest'])
		{
			$homeResult = $game->team2_result;
			$awayResult	= $game->team1_result;
			$homeResultOT = $game->team2_result_ot;
			$awayResultOT = $game->team1_result_ot;
			$homeResultSO = $game->team2_result_so;
			$awayResultSO = $game->team1_result_so;
			$homeResultDEC = $game->team2_result_decision;
			$awayResultDEC = $game->team1_result_decision;
		}
		else
		{
			$homeResult = $game->team1_result;
			$awayResult = $game->team2_result;
			$homeResultOT = $game->team1_result_ot;
			$awayResultOT = $game->team2_result_ot;
			$homeResultSO = $game->team1_result_so;
			$awayResultSO = $game->team2_result_so;
			$homeResultDEC = $game->team1_result_decision;
			$awayResultDEC = $game->team2_result_decision;
		}

		if (isset($homeResult) && isset($awayResult))
		{
			$result = $homeResult . '&nbsp;' . $this->overallconfig['seperator'] . '&nbsp;' . $awayResult;
		}
		else
		{
			$result = '_&nbsp;' . $this->overallconfig['seperator'] . '&nbsp;_';
		}
		if ($game->alt_decision)
		{
			$result = '<b style="color:red;">';
			$result .= $homeResultDEC . '&nbsp;' . $this->overallconfig['seperator'] . '&nbsp;' . $awayResultDEC;
			$result .= '</b>';
		}
		if (isset($homeResultSO) || isset($formatScoreawayResultSO))
		{
			$result .= $this->config['result_style'] == 1 ? '<br />' : ' ';
			$result .= '(' . Text::_('COM_JOOMLEAGUE_RESULTS_SHOOTOUT') . ' ';
			$result .= $homeResultSO . '&nbsp;' . $this->overallconfig['seperator'] . '&nbsp;' . $awayResultSO;
			$result .= ')';
		}
		else
		{
			if ($game->match_result_type == 2)
			{
				$result .= $this->config['result_style'] == 1 ? '<br />' : ' ';
				$result .= '(' . Text::_('COM_JOOMLEAGUE_RESULTS_SHOOTOUT');
				$result .= ')';
			}
		}
		if (isset($homeResultOT) || isset($awayResultOT))
		{
			$result .= $this->config['result_style'] == 1 ? '<br />' : ' ';
			$result .= '(' . Text::_('COM_JOOMLEAGUE_RESULTS_OVERTIME') . ' ';
			$result .= $homeResultOT . '&nbsp;' . $this->overallconfig['seperator'] . '&nbsp;' . $awayResultOT;
			$result .= ')';
		}
		else
		{
			if ($game->match_result_type == 1)
			{
				$result .= $this->config['result_style'] == 1 ? '<br />' : ' ';
				$result .= '(' . Text::_('COM_JOOMLEAGUE_RESULTS_OVERTIME');
				$result .= ')';
			}
		}

		return $result;
	}

	/**
	 * return match state html code
	 * @param $game
	 * @return string state
	 */
	function showMatchState($game)
	{
		return $game->cancel > 0 ? $game->cancel_reason : $this->formatScoreInline($game);
	}

	function showMatchRefereesAsTooltip($game)
	{
		if ($this->config['show_referee'])
		{
			if ($this->project->teams_as_referees)
			{
				$referees = $this->model->getMatchRefereeTeams($game->id);
			}
			else
			{
				$referees = $this->model->getMatchReferees($game->id);
			}

			if (!empty($referees))
			{
				$toolTipTitle	= Text::_('COM_JOOMLEAGUE_RESULTS_REF_TOOLTIP');
				$toolTipText	= '';

				foreach ($referees as $ref)
				{
					if ($this->project->teams_as_referees)
					{
						$toolTipText .= $ref->teamname . ' (' . $ref->position_name . ')' . '&lt;br /&gt;';
					}
					else
					{
						$toolTipText .= ($ref->firstname ? $ref->firstname.' '.$ref->lastname : $ref->lastname)
							. ' (' . $ref->position_name . ')' . '&lt;br /&gt;';
					}
				}

				?>
			<!-- Referee tooltip -->
			<span class='hasTip' title='<?php echo $toolTipTitle; ?> :: <?php echo $toolTipText; ?>'>
				<img src='<?php echo Uri::root(); ?>media/com_joomleague/jl_images/icon-16-Referees.png'
					alt='' title='' />
			</span>
				<?php
			}
			else
			{
				?>&nbsp;<?php
			}
		}
	}

	function showReportDecisionIcons($game)
	{
		if (($game->show_report && trim($game->summary) != '') || $game->alt_decision || $game->match_result_type > 0)
		{
			$report_link=JoomleagueHelperRoute::getMatchReportRoute($game->project_id, $game->id);
			if ($game->alt_decision)
			{
				$imgTitle = Text::_($game->decision_info);
				$img = 'media/com_joomleague/jl_images/court.gif';
			}
			else
			{
				$imgTitle = Text::_('Has match summary');
				$img = 'media/com_joomleague/jl_images/zoom.png';
			}
			$output = HTMLHelper::_(	'link', $report_link, HTMLHelper::image($img, $imgTitle, array('border' => 0,'title' => $imgTitle)),
				array('title' => $imgTitle));
		}
		else
		{
			$output = '&nbsp;';
		}
		return $output;
	}

	/**
	 * returns html for events in tabs
	 * @param object match
	 * @param array project events
	 * @param array match events
	 * @param aray match substitutions
	 * @param array $config
	 * @return string
	 */
	function showEventsContainerInResults($matchInfo, $projectEvents, $matchEvents, $substitutions = null, $config)
	{
		$output='';
		if ($this->config['use_tabs_events'])
		{
			// Make event tabs
			$iPanel = 1;
			$selector = 'events';
			$output .= HTMLHelper::_('bootstrap.startTabSet', $selector, array('active'=>'panel'.$iPanel)); 
	
			// Size of the event icons in the tabs (when used)
			$width = 20; $height = 20; $type = 4;
			// Never show event text or icon for each event list item (info already available in tab)
			$showEventInfo = 0;

			$cnt = 0;
			foreach ($projectEvents AS $event)
			{
				//display only tabs with events
				foreach ($matchEvents AS $me)
				{
					$cnt = 0;
					if ($me->event_type_id == $event->id)
					{
						$cnt++;
						break;
					}
				}
				// Skip this project event when there are no match events for it.
				if($cnt == 0)
				{
					continue;
				}

				if ($this->config['show_events_with_icons'] == 1)
				{
					// Event icon as thumbnail on the tab (a placeholder icon is used when the icon does not exist)
					$imgTitle = Text::_($event->name);
					$tab_content = JoomleagueHelper::getPictureThumb($event->icon, $imgTitle, $width, $height, $type);
				}
				else
				{
					$tab_content = Text::_($event->name);
				}
				$output .= HTMLHelper::_('bootstrap.addTab', $selector, 'panel'.$iPanel++, $tab_content);
				$output .= '<table class="matchreport" border="0">';
				$output .= '<tr>';

				// Home team events
				$output .= '<td class="list">';
				$output .= '<ul>';
				foreach ($matchEvents AS $me)
				{
					$output .= $this->_formatEventContainerInResults($me, $event, $matchInfo->projectteam1_id, $showEventInfo);
				}
				$output .= '</ul>';
				$output .= '</td>';

				// Away team events
				$output .= '<td class="list">';
				$output .= '<ul>';
				foreach ($matchEvents AS $me)
				{
					$output .= $this->_formatEventContainerInResults($me, $event, $matchInfo->projectteam2_id, $showEventInfo);
				}
				$output .= '</ul>';
				$output .= '</td>';
				$output .= '</tr>';
				$output .= '</table>';
				$output .= HTMLHelper::_('bootstrap.endTab');
			}

			if (!empty($substitutions))
			{
				if ($this->config['show_events_with_icons'] == 1)
				{
					// Event icon as thumbnail on the tab (a placeholder icon is used when the icon does not exist)
					$imgTitle = Text::_('COM_JOOMLEAGUE_IN_OUT');
					$pic_tab	= 'images/com_joomleague/database/events/'.$this->project->fs_sport_type_name.'/subst.png';
					$tab_content = JoomleagueHelper::getPictureThumb($pic_tab, $imgTitle, $width, $height, $type);
				}
				else
				{
					$tab_content = Text::_('COM_JOOMLEAGUE_IN_OUT');
				}

				$pic_time	= 'images/com_joomleague/database/events/'.$this->project->fs_sport_type_name.'/playtime.gif';
				$pic_out	= 'images/com_joomleague/database/events/'.$this->project->fs_sport_type_name.'/out.png';
				$pic_in		= 'images/com_joomleague/database/events/'.$this->project->fs_sport_type_name.'/in.png';
				$imgTime = HTMLHelper::image($pic_time, Text::_('COM_JOOMLEAGUE_MATCHREPORT_SUBSTITUTION_MINUTE'),
					array(' title' => Text::_('COM_JOOMLEAGUE_MATCHREPORT_SUBSTITUTION_MINUTE')));
				$imgOut  = HTMLHelper::image($pic_out, Text::_('COM_JOOMLEAGUE_MATCHREPORT_SUBSTITUTION_WENT_OUT'),
					array(' title' => Text::_('COM_JOOMLEAGUE_MATCHREPORT_SUBSTITUTION_WENT_OUT')));
				$imgIn   = HTMLHelper::image($pic_in, Text::_('COM_JOOMLEAGUE_MATCHREPORT_SUBSTITUTION_CAME_IN'),
					array(' title' => Text::_('COM_JOOMLEAGUE_MATCHREPORT_SUBSTITUTION_CAME_IN')));

				$output .= HTMLHelper::_('bootstrap.addTab', $selector, 'panel'.$iPanel++, $tab_content);
				$output .= '<table class="matchreport" border="0">';
				$output .= '<tr>';
				$output .= '<td class="list">';
				$output .= '<ul>';
				foreach ($substitutions AS $subs)
				{
					$output .= $this->_formatSubstitutionContainerInResults($subs, $matchInfo->projectteam1_id,
						$imgTime, $imgOut, $imgIn);
				}
				$output .= '</ul>';
				$output .= '</td>';
				$output .= '<td class="list">';
				$output .= '<ul>';
				foreach ($substitutions AS $subs)
				{
					$output .= $this->_formatSubstitutionContainerInResults($subs, $matchInfo->projectteam2_id,
						$imgTime, $imgOut, $imgIn);
				}
				$output .= '</ul>';
				$output .= '</td>';
				$output .= '</tr>';
				$output .= '</table>';
				$output .= HTMLHelper::_('bootstrap.endTab');
			}
			$output .= HTMLHelper::_('bootstrap.endTabSet');
		}
		else
		{
			$showEventInfo = $this->config['show_events_with_icons'] == 1 ? 1 : 2;
			$output .= '<table class="matchreport" border="0">';
			$output .= '<tr>';

			// Home team events
			$output .= '<td class="list-left">';
			$output .= '<ul>';
			foreach ((array) $matchEvents AS $me)
			{
				if ($me->ptid == $matchInfo->projectteam1_id)
				{
					$output .= $this->_formatEventContainerInResults($me, $projectEvents[$me->event_type_id],
						$matchInfo->projectteam1_id, $showEventInfo);
				}
			}
			$output .= '</ul>';
			$output .= '</td>';

			// Away team events
			$output .= '<td class="list-right">';
			$output .= '<ul>';
			foreach ($matchEvents AS $me)
			{
				if ($me->ptid == $matchInfo->projectteam2_id)
				{
					$output .= $this->_formatEventContainerInResults($me, $projectEvents[$me->event_type_id],
						$matchInfo->projectteam2_id, $showEventInfo);
			    }
			}
			$output .= '</ul>';
			$output .= '</td>';
			$output .= '</tr>';
			$output .= '</table>';
		}

		return $output;
	}

	function formatResult($team1, $team2, $game, $reportLink)
	{
		// check home and away team for favorite team
		$fav = (isset($team1->id) && in_array($team1->id,$this->favteams)) ||
				(isset($team2->id) && in_array($team2->id,$this->favteams));
		// 0 = no links
		// 1 = For all teams
		// 2 = For favorite team(s) only
		if($this->config['show_link_matchreport'] == 1 || ($this->config['show_link_matchreport'] == 2 && $fav))
		{
			$output = HTMLHelper::_(	'link', $reportLink,
				'<span class="score0">' . $this->showMatchState($game,$this->config) . '</span>',
				array('title' => Text::_('COM_JOOMLEAGUE_RESULTS_SHOW_MATCHREPORT')));
		}
		else
		{
			$output = $this->showMatchState($game, $this->config);
		}

		$search_empty_part_results = array(';', 'NULL');

		if($this->config['show_part_results'] &&
			(str_replace($search_empty_part_results, '', $game->team1_result_split) != '' &&
			str_replace($search_empty_part_results, '', $game->team2_result_split) != ''))
		{
			// show only one half time result for soccer and handball
			if ($this->project->sport_type_name == 'COM_JOOMLEAGUE_ST_SOCCER' ||
				$this->project->sport_type_name == 'COM_JOOMLEAGUE_ST_HANDBALL')
			{
				$output .= ' (' . strstr($game->team1_result_split, ';', true) .
					':' . strstr($game->team2_result_split, ';', true) . ')';
			}
			else
			{
				$output .= ' (' . implode(':',explode(';', $game->team1_result_split)) . ', ' .
								implode(':', explode(';', $game->team2_result_split)) . ')';
			}
		}
		return $output;
	}

	function _formatEventContainerInResults($matchEvent, $event, $projectTeamId, $showEventInfo)
	{
		// Meaning of $showEventInfo:
		// 0 : do not show event as text or as icon in a list item
		// 1 : show event as icon in a list item (before the time)
		// 2 : show event as text in a list item (after the time)
		$output='';
		if ($matchEvent->event_type_id == $event->id && $matchEvent->ptid == $projectTeamId)
		{
			$output .= '<li class="events">';
			if ($showEventInfo == 1)
			{
				// Size of the event icons in the tabs
				$width = 20; $height = 20; $type = 4;
				$imgTitle = Text::_($event->name);
				$icon = JoomleagueHelper::getPictureThumb($event->icon, $imgTitle, $width, $height, $type);
				$output .= $icon;
			}

			$eventMinute = str_pad($matchEvent->event_time, 2 ,'0', STR_PAD_LEFT);
			if ($this->config['show_event_minute'] == 1 && $matchEvent->event_time > 0)
			{
				$output .= '<b>' . $eventMinute . '\'</b> ';
			}

			if ($showEventInfo == 2)
			{
				$output .= Text::_($event->name) . ' ';
			}

			if (strlen($matchEvent->firstname1 . $matchEvent->lastname1) > 0)
			{
				$output .= JoomleagueHelper::formatName(null, $matchEvent->firstname1, $matchEvent->nickname1,
					$matchEvent->lastname1, $this->config['name_format']);
			}
			else
			{
				$output .= Text :: _('COM_JOOMLEAGUE_GLOBAL_UNKNOWN_PERSON');
			}

			// only show event sum and match notice when set to on in template cofig
			if($this->config['show_event_sum'] == 1 || $this->config['show_event_notice'] == 1)
			{
				if (($this->config['show_event_sum'] == 1 && $matchEvent->event_sum > 0) ||
					($this->config['show_event_notice'] == 1 && strlen($matchEvent->notice) > 0))
				{
					$output .= ' (';
					if ($this->config['show_event_sum'] == 1 && $matchEvent->event_sum > 0)
					{
						$output .= $matchEvent->event_sum;
					}
					if (($this->config['show_event_sum'] == 1 && $matchEvent->event_sum > 0) &&
						($this->config['show_event_notice'] == 1 && strlen($matchEvent->notice) > 0))
					{
						$output .= ' | ';
					}
					if ($this->config['show_event_notice'] == 1 && strlen($matchEvent->notice) > 0)
					{
						$output .= $matchEvent->notice;
					}
					$output .= ')';
				}
			}
			$output .= '</li>';
		}
		return $output;
	}

	function _formatSubstitutionContainerInResults($subs, $projectTeamId, $imgTime, $imgOut, $imgIn)
	{
		$output='';
		if ($subs->ptid == $projectTeamId)
		{
			$output .= '<li class="events">';
			// $output .= $imgTime;
			$output .= '&nbsp;' . $subs->in_out_time . '. ' . Text::_('COM_JOOMLEAGUE_MATCHREPORT_SUBSTITUTION_MINUTE');
			$output .= '<br />';

			$output .= $imgOut;
			$output .= '&nbsp;' . JoomleagueHelper::formatName(null, $subs->out_firstname, $subs->out_nickname,
					$subs->out_lastname, $this->config['name_format']);
			if ($subs->out_position != '')
			{
			  $output .= '&nbsp;(' . Text::_($subs->out_position) . ')';
			}
			$output .= '<br />';

			$output .= $imgIn;
			$output .= '&nbsp;' . JoomleagueHelper::formatName(null, $subs->firstname, $subs->nickname,
					$subs->lastname, $this->config['name_format']);
			if ($subs->in_position != '')
			{
			  $output  .= '&nbsp;(' . Text::_($subs->in_position) . ')';
			}

			$output .= '<br /><br />';
			$output .= '</li>';
		}
		return $output;
	}

	public function getRoundSelectNavigation($rounds, $division_id=0)
	{
		$currentUrl = JoomleagueHelperRoute::getResultsRoute($this->project->slug, $this->roundid, $division_id);
		$options = array();
		foreach ($rounds as $r)
		{
			$link = JoomleagueHelperRoute::getResultsRoute($this->project->slug, $r->id, $division_id);
			$options[] = HTMLHelper::_('select.option', $link, $r->roundcode);
		}
		return HTMLHelper::_('select.genericlist', $options, 'select-round', 'onchange="joomleague_changedoc(this);"',
			'value', 'text', $currentUrl);
	}

}
