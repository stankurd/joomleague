<?php 
/**
 * Joomleague
 *
 * @copyright	Copyright (C) 2006-2016 joomleague.at. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @link		http://www.joomleague.at
 */
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;

defined('_JEXEC') or die();
 

class JoomleagueViewPlayer extends JLGView
{

	/**
	 * 
	 * @see JLGView::display()
	 */
	public function display($tpl=null)
	{
	    $app = Factory::getApplication();
		// Get a refrence of the page instance in joomla
		$document 	= Factory::getDocument();
		$model 		= $this->getModel();
		$config		= $model->getTemplateConfig($this->getName());

		$person		= $model->getPerson();
		$nickname 	= isset($person->nickname) ? $person->nickname : "";
		if(!empty($nickname)){
			$nickname="'".$nickname."'";
		}
		$this->isContactDataVisible = $model->isContactDataVisible($config['show_contact_team_member_only']);
		
		$project = $model->getProject();
		$this->project = $project;
		
		$this->overallconfig = $model->getOverallConfig();
		$this->config = $config;
		$this->person = $person;
		$this->nickname = $nickname;
		
		/*
		$this->assignRef('teamPlayers',$model->getTeamPlayers());

		// Select the teamplayer that is currently published (in case the player played in multiple teams in the project)
		$teamPlayer = null;
		if (count($this->teamPlayers))
		{
			$currentProjectTeamId=0;
			foreach ($this->teamPlayers as $teamPlayer)
			{
				if ($teamPlayer->published == 1)
				{
					$currentProjectTeamId=$teamPlayer->projectteam_id;
					break;
				}
			}
			if ($currentProjectTeamId)
			{
				$teamPlayer = $this->teamPlayers[$currentProjectTeamId];
			}
		}
		*/
		$sportstype = $config['show_plcareer_sportstype'] ? $model->getSportsType() : 0;
		$current_round = $project->current_round;
		$personid = $model->personid;
		$teamPlayer = $model->getTeamPlayerByRound($current_round, $personid);
		if ($teamPlayer) {
			$this->teamPlayer = $teamPlayer[0];
		} else {
			$this->teamPlayer = false;
		}
		$this->historyPlayer = $model->getPlayerHistory($sportstype, 'ASC');
		$this->historyPlayerStaff = $model->getPlayerHistoryStaff($sportstype, 'ASC');
		$this->AllEvents = $model->getAllEvents($sportstype);
		$this->showediticon = $model->getAllowed($config['edit_own_player']);
		$this->stats = $model->getProjectStats();

		// Get events and stats for current project
		if ($config['show_gameshistory'])
		{
			$this->games = $model->getGames();
			$this->teams = $model->getTeamsIndexedByPtid();
			$this->gamesevents = $model->getGamesEvents();
			$this->gamesstats = $model->getPlayerStatsByGame();
		}

		// Get events and stats for all projects where player played in (possibly restricted to sports type of current project)
		if ($config['show_career_stats'])
		{
			$this->stats = $model->getStats($current_round, $personid);
			$this->projectstats = $model->getPlayerStatsByProject($sportstype,$current_round, $personid);
		}

		$extended = $this->getExtended($person->extended, 'person');
		$this->extended = $extended;
		
		$name = !empty($person) ? JoomleagueHelper::formatName(null, $person->firstname, $person->nickname,  $person->lastname,  $this->config["name_format"]) : "";
		$this->playername = $name;
		
		// Set page title
		$titleInfo = JoomleagueHelper::createTitleInfo(Text::_('COM_JOOMLEAGUE_PLAYER_PAGE_TITLE'));
		$titleInfo->personName = $name;
		if (!empty($this->project))
		{
			$titleInfo->projectName = $this->project->name;
			$titleInfo->leagueName = $this->project->league_name;
			$titleInfo->seasonName = $this->project->season_name;
		}
		$division = $model->getDivision($app->input->getInt('division',0));
		if (!empty( $division ) && $division->id != 0)
		{
			$titleInfo->divisionName = $division->name;
		}
		$this->pagetitle = JoomleagueHelper::formatTitle($titleInfo, $this->config["page_title_format"]);
		$document->setTitle($this->pagetitle);
		
		parent::display($tpl);
	}
}
