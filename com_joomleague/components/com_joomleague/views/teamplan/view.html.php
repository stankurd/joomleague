<?php 
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;

defined('_JEXEC') or die;

jimport('joomla.html.pane');
jimport('joomla.functions');
HTMLHelper::_('bootstrap.tooltip');

/**
 * View-Teamplan
 */
class JoomleagueViewTeamPlan extends JLGView
{
	public function display($tpl=null)
	{
		$model = $this->getModel();
		$this->project = $model->getProject();
		$this->overallconfig = $model->getOverallConfig();
		$config = $model->getTemplateConfig($this->getName());
		$this->config = array_merge($this->overallconfig, $config);
		if (isset($this->project))
		{
			$this->rounds = $model->getRounds($config['plan_order']);
			$this->teams = $model->getTeamsIndexedByPtid();
			$this->favteams = $model->getFavTeams();
			$this->division = $model->getDivision();
			$this->ptid = $model->getProjectTeamId();
			$rosterModel = BaseDatabaseModel::getInstance('Roster', 'JoomleagueModel');
			$this->projectteam = $rosterModel->getProjectTeam();
			$this->projectevents = $model->getProjectEvents();
			$this->matches = $model->getMatches($config);
			$this->matches_refering = $model->getMatchesRefering($config);
			$this->matchesperround = $model->getMatchesPerRound($config, $this->rounds);
			$this->model = $model;
		}
		$this->setPageTitle();

		parent::display($tpl);
	}

	private function setPageTitle()
	{
		$titleInfo = JoomleagueHelper::createTitleInfo(Text::_('COM_JOOMLEAGUE_TEAMPLAN_PAGE_TITLE'));
		$titleInfo->team1Name = !empty($this->ptid) ? $this->teams[$this->ptid]->name : Text::_("COM_JOOMLEAGUE_TEAMPLAN_ALL_TEAMS");
		if (!empty($this->project))
		{
			$titleInfo->projectName = $this->project->name;
			$titleInfo->leagueName = $this->project->league_name;
			$titleInfo->seasonName = $this->project->season_name;
		}
		if (!empty( $this->division ) && $this->division->id != 0)
		{
			$titleInfo->divisionName = $this->division->name;
		}
		$this->pagetitle=JoomleagueHelper::formatTitle($titleInfo, $this->config["page_title_format"]);
		$document = Factory::getDocument();
		$document->setTitle($this->pagetitle);
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
	function showEventsContainerInResults($matchInfo,$projectevents,$matchevents,$substitutions=null,$config)
	{
		$output = '';

		if ($this->config['use_tabs_events'])
		{
			// Make event tabs
			$iPanel = 1;
			$selector = 'teamplan';
			echo HTMLHelper::_('bootstrap.startTabSet', $selector, array('active'=>'details'));

			// Size of the event icons in the tabs (when used)
			$width = 20; $height = 20; $type = 4;
 			// Never show event text or icon for each event list item (info already available in tab)
			$showEventInfo = 0;

			$cnt = 0;
			foreach ($projectevents AS $event)
			{
				//display only tabs with events
				foreach ($matchevents AS $me)
				{
					$cnt = 0;
					if ($me->event_type_id == $event->id)
					{
						$cnt++;
						break;
					}
				}
				if ($cnt == 0)
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
				foreach ($matchevents AS $me)
				{
					$output .= self::_formatEventContainerInResults($me, $event, $matchInfo->projectteam1_id, $showEventInfo);
				}
				$output .= '</ul>';
				$output .= '</td>';

				// Away team events
				$output .= '<td class="list">';
				$output .= '<ul>';
				foreach ($matchevents AS $me)
				{
					$output .= self::_formatEventContainerInResults($me, $event, $matchInfo->projectteam2_id, $showEventInfo);
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
				$imgTime = HTMLHelper::image($pic_time,Text::_('COM_JOOMLEAGUE_MATCHREPORT_SUBSTITUTION_MINUTE'),
					array(' title' => Text::_('COM_JOOMLEAGUE_MATCHREPORT_SUBSTITUTION_MINUTE')));
				$imgOut  = HTMLHelper::image($pic_out,Text::_('COM_JOOMLEAGUE_MATCHREPORT_SUBSTITUTION_WENT_OUT'),
					array(' title' => Text::_('COM_JOOMLEAGUE_MATCHREPORT_SUBSTITUTION_WENT_OUT')));
				$imgIn   = HTMLHelper::image($pic_in,Text::_('COM_JOOMLEAGUE_MATCHREPORT_SUBSTITUTION_CAME_IN'),
					array(' title' => Text::_('COM_JOOMLEAGUE_MATCHREPORT_SUBSTITUTION_CAME_IN')));

				$output .= HTMLHelper::_('bootstrap.addTab', $selector, 'panel'.$iPanel++, $tab_content);
				$output .= '<table class="matchreport" border="0">';
				$output .= '<tr>';
				$output .= '<td class="list">';
				$output .= '<ul>';
				foreach ($substitutions AS $subs)
				{
					$output .= self::_formatSubstitutionContainerInResults($subs,$matchInfo->projectteam1_id,$imgTime,$imgOut,$imgIn);
				}
				$output .= '</ul>';
				$output .= '</td>';
				$output .= '<td class="list">';
				$output .= '<ul>';
				foreach ($substitutions AS $subs)
				{
					$output .= self::_formatSubstitutionContainerInResults($subs,$matchInfo->projectteam2_id,$imgTime,$imgOut,$imgIn);
				}
				$output .= '</ul>';
				$output .= '</td>';
				$output .= '</tr>';
				$output .= '</table>';
				$output .= HTMLHelper::_('bootstrap.endTab');
			}
			echo HTMLHelper::_('bootstrap.endTabSet');

		}
		else
		{
			$showEventInfo = ($this->config['show_events_with_icons'] == 1) ? 1 : 2;
			$output .= '<table class="matchreport" border="0">';
			$output .= '<tr>';

			// Home team events
			$output .= '<td class="list-left">';
			$output .= '<ul>';
			foreach ((array) $matchevents AS $me)
			{
				if ($me->ptid == $matchInfo->projectteam1_id)
				{
					$output .= self::_formatEventContainerInResults($me, $projectevents[$me->event_type_id],
						$matchInfo->projectteam1_id, $showEventInfo);
				}
			}
			$output .= '</ul>';
			$output .= '</td>';

			// Away team events
			$output .= '<td class="list-right">';
			$output .= '<ul>';
			foreach ($matchevents AS $me)
			{
				if ($me->ptid == $matchInfo->projectteam2_id)
				{
					$output .= self::_formatEventContainerInResults($me, $projectevents[$me->event_type_id],
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


	function _formatEventContainerInResults($matchevent, $event, $projectteamId, $showEventInfo)
	{
		// Meaning of $showEventInfo:
		// 0 : do not show event as text or as icon in a list item
		// 1 : show event as icon in a list item (before the time)
		// 2 : show event as text in a list item (after the time)
		$output = '';
		if ($matchevent->event_type_id == $event->id && $matchevent->ptid == $projectteamId)
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

			$event_minute = str_pad($matchevent->event_time, 2 ,'0', STR_PAD_LEFT);
			if ($this->config['show_event_minute'] == 1 && $matchevent->event_time > 0)
			{
				$output .= '<b>'.$event_minute.'\'</b> ';
			}

			if ($showEventInfo == 2)
			{
				$output .= Text::_($event->name).' ';
			}

			if (strlen($matchevent->firstname1.$matchevent->lastname1) > 0)
			{
				$output .= JoomleagueHelper::formatName(null, $matchevent->firstname1, $matchevent->nickname1,
					$matchevent->lastname1, $this->config['name_format']);
			}
			else
			{
				$output .= Text :: _('COM_JOOMLEAGUE_GLOBAL_UNKNOWN_PERSON');
			}

			// only show event sum and match notice when set to on in template cofig
			if($this->config['show_event_sum'] == 1 || $this->config['show_event_notice'] == 1)
			{
				if (($this->config['show_event_sum'] == 1 && $matchevent->event_sum > 0) ||
					($this->config['show_event_notice'] == 1 && strlen($matchevent->notice) > 0))
				{
					$output .= ' (';
						if ($this->config['show_event_sum'] == 1 && $matchevent->event_sum > 0)
						{
							$output .= $matchevent->event_sum;
						}
						if (($this->config['show_event_sum'] == 1 && $matchevent->event_sum > 0) &&
							($this->config['show_event_notice'] == 1 && strlen($matchevent->notice) > 0))
						{
							$output .= ' | ';
						}
						if ($this->config['show_event_notice'] == 1 && strlen($matchevent->notice) > 0)
						{
							$output .= $matchevent->notice;
						}
					$output .= ')';
				}
			}

			$output .= '</li>';
		}
		return $output;
	}

	function _formatSubstitutionContainerInResults($subs, $projectteamId, $imgTime, $imgOut, $imgIn)
	{
		$output='';
		if ($subs->ptid == $projectteamId)
		{
			$output .= '<li class="events">';
			// $output .= $imgTime;
			$output .= '&nbsp;'.$subs->in_out_time.'. '.Text::_('COM_JOOMLEAGUE_MATCHREPORT_SUBSTITUTION_MINUTE');
			$output .= '<br />';

			$output .= $imgOut;
			$output .= '&nbsp;'.JoomleagueHelper::formatName(null, $subs->out_firstname, $subs->out_nickname,
					$subs->out_lastname, $this->config["name_format"]);
			$output .= '&nbsp;('.Text :: _($subs->out_position).')';
			$output .= '<br />';

			$output .= $imgIn;
			$output .= '&nbsp;'.JoomleagueHelper::formatName(null, $subs->firstname, $subs->nickname,
					$subs->lastname, $this->config["name_format"]);
			$output .= '&nbsp;('.Text :: _($subs->in_position).')';
			$output .= '<br /><br />';
			$output .= '</li>';
		}
		return $output;
	}
}
