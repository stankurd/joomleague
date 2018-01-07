<?php
/**
 * Joomleague
 *
 * @copyright	Copyright (C) 2006-2015 joomleague.at. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @link		http://www.joomleague.at
 * @author		Marco Vaninetti <martizva@tiscali.it>
 * @author		other JL Team members
 */
use Joomla\CMS\Factory;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;
use Joomla\Utilities\ArrayHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;

jimport('joomla.html.pane');
HTMLHelper::_('jquery.framework');
//HTMLHelper::_('behavior.core');
require_once JPATH_COMPONENT . '/models/sportstypes.php';
require_once JPATH_COMPONENT . '/models/leagues.php';
defined('_JEXEC') or die;

/**
 * HTML View class
 */
class JoomleagueViewJoomleague extends JLGView
{

	public function display($tpl = null)
	{
		$app 	= Factory::getApplication();
		$input = $app->input;
		 // Add submenu
       // JoomleagueHelper::addSubmenu($this->getName());
		// hide main menu in result/match edit view
		$viewName = $input->getCmd('view');

		if($viewName == 'matches')
		{
			return;
		}

		if($this->getLayout() == 'default')
		{
			$this->_displayMenu($tpl);
			return;
		}

		if($this->getLayout() == 'panel')
		{
			$this->_displayPanel($tpl);
			return;
		}

		if($this->getLayout() == 'footer')
		{
			parent::display();
		}
		$this->addSidebar();

	}

	/**
	 * display control panel
	 */
	function _displayPanel($tpl)
	{
		// get the project
		// $project = $this->get('data');
		$app = Factory::getApplication();
		$input = $app->input;
		
		$mdlJoomleague  = BaseDatabaseModel::getInstance('Joomleague','JoomleagueModel');
		$project_id = $app->getUserState('com_joomleagueproject');

		/*
		$currentData 	= $mdlJoomleague->getCurrentProjectData();
		$project_id = $currentData->project_id;
		*/
		
		$mdlProject = BaseDatabaseModel::getInstance('project','JoomleagueModel');
		$project = $mdlProject->getItem($project_id);

		$iProjectDivisionsCount = 0;
		$mdlProjectDivisions = BaseDatabaseModel::getInstance("divisions","JoomleagueModel");
		$iProjectDivisionsCount = $mdlProjectDivisions->getProjectDivisionsCount($project->id);

		$iProjectPositionsCount = 0;
		$mdlProjectPositions = BaseDatabaseModel::getInstance("Projectpositions","JoomleagueModel");
		$iProjectPositionsCount = $mdlProjectPositions->getProjectPositionsCount($project->id);

		$iProjectRefereesCount = 0;
		$mdlProjectReferees = BaseDatabaseModel::getInstance("Projectreferees","JoomleagueModel");
		$iProjectRefereesCount = $mdlProjectReferees->getProjectRefereesCount($project->id);

		$iProjectTeamsCount = 0;
		$mdlProjecteams = BaseDatabaseModel::getInstance("Projectteams","JoomleagueModel");
		$iProjectTeamsCount = $mdlProjecteams->getProjectTeamsCount($project->id);

		$iRoundsCount = 0;
		$mdlRounds = BaseDatabaseModel::getInstance("Rounds","JoomleagueModel");
		$iRoundsCount = $mdlRounds->getRoundsCount($project->id);

		$this->project = $project;
		$this->count_projectdivisions = $iProjectDivisionsCount;
		$this->count_projectpositions = $iProjectPositionsCount;
		$this->count_projectreferees = $iProjectRefereesCount;
		$this->count_projectteams = $iProjectTeamsCount;
		$this->count_rounds = $iRoundsCount;

		parent::display($tpl);
	}
 /**
     * 
     * Add a menu on the sidebar of page
     */
    protected function addSidebar() {
		$this->sidebar = JHtmlSidebar::render();
    }
	/**
	 * display left Menu
	 */
	function _displayMenu($tpl = null)
	{
		$app = Factory::getApplication();
		$input = $app->input;
		$option = $input->getCmd('option');
		$lists = array();

		$db = Factory::getDbo();
		$baseurl = Uri::root();
		$document = Factory::getDocument();
		$version = urlencode(JoomleagueHelper::getVersion());
		$document->addScript($baseurl . 'administrator/components/com_joomleague/assets/js/quickmenu.js');
		
		$mdlProject = BaseDatabaseModel::getInstance('project','JoomleagueModel');
		$mdlJoomleague = BaseDatabaseModel::getInstance('Joomleague','JoomleagueModel');
		$params = ComponentHelper::getParams($option);

		// catch Projectid (primary)
		$project_id = $input->get('pid',array(),'array');
		ArrayHelper::toInteger($project_id);
		if(empty($project_id))
		{
			$project_id = $app->getUserState('com_joomleagueproject',false);
		}
		else
		{
			$project_id = $project_id[0];
		}

		// set Project from jinput to userState
		if($project_id)
		{
			$app->setUserState($option . 'project',$project_id);
			$project = $mdlProject->getItem($project_id);
		}
		else
		{
			$project = false;
		}
		
		// catch SportType
		$sporttype_id = $input->get('stid',array(),'array');
		ArrayHelper::toInteger($sporttype_id);
		
		if(empty($sporttype_id))
		{
			if($project_id)
			{
				$sporttype_id = $project->sports_type_id;
			}
			else
			{
				$sporttype_id = $app->getUserState('com_joomleaguesportstypes');
			}
		}
		else
		{
			$sporttype_id = $sporttype_id[0];
		}
				
		if($sporttype_id)
		{
			$app->setUserState($option.'sportstypes',$sporttype_id);
		}
		else
		{
			// do we have sportTypes
			$mdlSportsTypes = BaseDatabaseModel::getInstance('SportsTypes','JoomleagueModel');
			$availableSportstypes = $mdlSportsTypes->getSportsTypes();
			
			if ($availableSportstypes) {
				$defsportstype = $params->get('defsportstype');
				$defsportstype = (empty($defsportstype)) ? "1" : $params->get('defsportstype');
				$app->setUserState($option.'sportstypes',$defsportstype);
			}
		}

		// Retrieve Season
		$seasonid = $app->getUserState($option . 'seasonnav');

		// Use seasons in dropdown or not
		$use_seasons = $params->get('cfg_show_seasons_in_project_drop_down', 0);

		$mdlSportsTypes = BaseDatabaseModel::getinstance('Sportstypes','JoomleagueModel');
		$allSportstypes = $mdlSportsTypes->getSportsTypes();

		$sportstypes = array();
		$sportstypes[] = HTMLHelper::_('select.option','0',Text::_('COM_JOOMLEAGUE_GLOBAL_SELECT_SPORTSTYPE'),'id','name');
		$allSportstypes = array_merge($sportstypes,$allSportstypes);

		$lists['sportstypes'] = HTMLHelper::_('select.genericList',$allSportstypes,'stid[]','class="inputbox" style="width:100%"','id','name',
				$sporttype_id);

		if($sporttype_id)
		{
			// seasons
			$availableSeasons = $mdlJoomleague->getSeasons();
			$seasons = array();
			$seasons[] = HTMLHelper::_('select.option','0',Text::_('COM_JOOMLEAGUE_GLOBAL_SELECT_SEASON'),'id','name');
			
			if ($availableSeasons) {
				$allSeasons = array_merge($seasons, $availableSeasons);
			} else {
				$allSeasons = array_merge($seasons);
			}
			
			$lists['seasons'] = HTMLHelper::_('select.genericList',$allSeasons,'seasonnav','class="inputbox" style="width:100%"','id','name',$seasonid);

			// build the html select list for projects
			$projects = array();
			$projects[] = HTMLHelper::_('select.option','0',Text::_('COM_JOOMLEAGUE_GLOBAL_SELECT_PROJECT'),'id','name');

			// check if the season filter is set and select the needed projects
			if(! $use_seasons)
			{
				if($res = $mdlJoomleague->getProjectsBySportsType($sporttype_id,$seasonid))
				{
					$projects = array_merge($projects,$res);
				}
			}
			else
			{
				if($res = $mdlProject->getSeasonProjects($seasonid))
				{
					$projects = array_merge($projects,$res);
				}
			}

			$lists['projects'] = HTMLHelper::_('select.genericList',$projects,'pid[]','class="inputbox" style="width:100%"','id','name',$project_id);
		}

		// if a project is active we create the teams and rounds select lists
		if($project_id > 0)
		{
			$team_id = $input->getInt('ptid',0);
			if($team_id == 0)
			{
				$team_id = $app->getUserState($option . 'project_team_id');
			}
			$projectteams[] = HTMLHelper::_('select.option','0',Text::_('COM_JOOMLEAGUE_GLOBAL_SELECT_TEAM'),'value','text');

			if($res = $mdlJoomleague->getProjectteams())
			{
				$projectteams = array_merge($projectteams,$res);
			}

			$lDummy = 'class="inputbox" ';
			$lDummy .= 'style="width:100%"';
			$lists['projectteams'] = HTMLHelper::_('select.genericList',$projectteams,'tid[]','class="inputbox" style="width:100%"','value','text',
					$team_id);

			$round_id = $app->getUserState($option . 'round_id');
			$projectrounds[] = HTMLHelper::_('select.option','0',Text::_('COM_JOOMLEAGUE_GLOBAL_SELECT_ROUND'),'value','text');

			$mdlRound = BaseDatabaseModel::getInstance('Round','JoomleagueModel');
			$round = $mdlRound->getItem($project->current_round);

			$projectrounds[] = HTMLHelper::_('select.option',$round->id,Text::_('COM_JOOMLEAGUE_GLOBAL_SELECT_CURRENT_ROUND'),'value','text');
			if($ress = JoomleagueHelper::getRoundsOptions($project_id,'ASC',true))
			{
				foreach($ress as $res)
				{
					$project_roundslist[] = HTMLHelper::_('select.option',$res->value,$res->text);
				}
				$projectrounds = array_merge($projectrounds,$project_roundslist);
			}

			$lists['projectrounds'] = HTMLHelper::_('select.genericList',$projectrounds,'rid[]','class="inputbox" style="width:100%"','value','text',
					$round_id);
		}

		$imagePath = 'administrator/components/com_joomleague/assets/images/';
		$tabs = array();
		$pane = new stdClass();
		$pane->title = Text::_('COM_JOOMLEAGUE_D_MENU_GENERAL');
		$pane->name = 'General data';
		$pane->alert = false;
		$tabs[] = $pane;

		// DEFININING - ARRAY PART1

		$link = array();
		$label = array();
		$limage = array();

		$link1 = array();
		$label1 = array();
		$limage1 = array();

		// Project
		$link1[] = Route::_('index.php?option=com_joomleague&view=projects');
		$label1[] = Text::_('COM_JOOMLEAGUE_D_MENU_PROJECTS');
		$limage1[] = HTMLHelper::_('image',$imagePath . 'projects.png',Text::_('COM_JOOMLEAGUE_D_MENU_PROJECTS'));
		// SportsType
		$link1[] = Route::_('index.php?option=com_joomleague&view=sportstypes');
		$label1[] = Text::_('COM_JOOMLEAGUE_D_MENU_SPORTSTYPES');
		$limage1[] = HTMLHelper::_('image',$imagePath . 'sportstypes.png',Text::_('COM_JOOMLEAGUE_D_MENU_SPORTSTYPES'));
		// League
		$link1[] = Route::_('index.php?option=com_joomleague&view=leagues');
		$label1[] = Text::_('COM_JOOMLEAGUE_D_MENU_LEAGUES');
		$limage1[] = HTMLHelper::_('image',$imagePath . 'leagues.png',Text::_('COM_JOOMLEAGUE_D_MENU_LEAGUES'));
		// Season
		$link1[] = Route::_('index.php?option=com_joomleague&view=seasons');
		$label1[] = Text::_('COM_JOOMLEAGUE_D_MENU_SEASONS');
		$limage1[] = HTMLHelper::_('image',$imagePath . 'seasons.png',Text::_('COM_JOOMLEAGUE_D_MENU_SEASONS'));
		// Club
		$link1[] = Route::_('index.php?option=com_joomleague&view=clubs');
		$label1[] = Text::_('COM_JOOMLEAGUE_D_MENU_CLUBS');
		$limage1[] = HTMLHelper::_('image',$imagePath . 'clubs.png',Text::_('COM_JOOMLEAGUE_D_MENU_CLUBS'));
		// Team
		$link1[] = Route::_('index.php?option=com_joomleague&view=teams');
		$label1[] = Text::_('COM_JOOMLEAGUE_D_MENU_TEAMS');
		$limage1[] = HTMLHelper::_('image',$imagePath . 'icon-16-Teams.png',Text::_('COM_JOOMLEAGUE_D_MENU_TEAMS'));
		// Person
		$link1[] = Route::_('index.php?option=com_joomleague&view=persons');
		$label1[] = Text::_('COM_JOOMLEAGUE_D_MENU_PERSONS');
		$limage1[] = HTMLHelper::_('image',$imagePath . 'players.png',Text::_('COM_JOOMLEAGUE_D_MENU_PERSONS'));
		// EventType
		$link1[] = Route::_('index.php?option=com_joomleague&view=eventtypes');
		$label1[] = Text::_('COM_JOOMLEAGUE_D_MENU_EVENTTYPES');
		$limage1[] = HTMLHelper::_('image',$imagePath . 'events.png',Text::_('COM_JOOMLEAGUE_D_MENU_EVENTTYPES'));
		// Statistic
		$link1[] = Route::_('index.php?option=com_joomleague&view=statistics');
		$label1[] = Text::_('COM_JOOMLEAGUE_D_MENU_STATISTICS');
		$limage1[] = HTMLHelper::_('image',$imagePath . 'calc16.png',Text::_('COM_JOOMLEAGUE_D_MENU_STATISTICS'));
		// Position
		$link1[] = Route::_('index.php?option=com_joomleague&view=positions');
		$label1[] = Text::_('COM_JOOMLEAGUE_D_MENU_POSITIONS');
		$limage1[] = HTMLHelper::_('image',$imagePath . 'icon-16-Positions.png',Text::_('COM_JOOMLEAGUE_D_MENU_POSITIONS'));
		// Playground
		$link1[] = Route::_('index.php?option=com_joomleague&view=playgrounds');
		$label1[] = Text::_('COM_JOOMLEAGUE_D_MENU_VENUES');
		$limage1[] = HTMLHelper::_('image',$imagePath . 'playground.png',Text::_('COM_JOOMLEAGUE_D_MENU_VENUES'));

		// Asign to array
		$link[] = $link1;
		$label[] = $label1;
		$limage[] = $limage1;

		// DEFINING - ARRAY PART2 - PROJECT
		if($project)
		{
			$link2 = array();
			$label2 = array();
			$limage2 = array();

			$project_type = $project->project_type;

			if($project_type == 0) // No divisions
			{
				$pane = new stdClass();
				$pane->title = Text::_('COM_JOOMLEAGUE_P_MENU_PROJECT');
				$pane->name = 'PMenu';
				$pane->alert = false;
				$tabs[] = $pane;

				// Project
				$link2[] = Route::_('index.php?option=com_joomleague&task=project.edit&id=' . $project->id.'&return=cpanel');
				$label2[] = Text::_('COM_JOOMLEAGUE_P_MENU_PSETTINGS');
				$limage2[] = HTMLHelper::_('image',$imagePath . 'projects.png',Text::_('COM_JOOMLEAGUE_P_MENU_PSETTINGS'));
				// Template
				$link2[] = Route::_('index.php?option=com_joomleague&view=templates&task=template.display');
				$label2[] = Text::_('COM_JOOMLEAGUE_P_MENU_FES');
				$limage2[] = HTMLHelper::_('image',$imagePath . 'icon-16-FrontendSettings.png',Text::_('COM_JOOMLEAGUE_P_MENU_FES'));

				if((isset($project->project_type)) && ($project->project_type == 'DIVISIONS_LEAGUE'))
				{
					$link2[] = Route::_('index.php?option=com_joomleague&view=divisions');
					$label2[] = Text::_('COM_JOOMLEAGUE_P_MENU_DIVISIONS');
					$limage2[] = HTMLHelper::_('image',$imagePath . 'icon-16-Divisions.png',Text::_('COM_JOOMLEAGUE_P_MENU_DIVISIONS'));
				}
				if((isset($project->project_type)) && (($project->project_type == 'TOURNAMENT_MODE') || ($project->project_type == 'DIVISIONS_LEAGUE')))
				{
					$link2[] = Route::_('index.php?option=com_joomleague&view=treetos');
					$label2[] = Text::_('COM_JOOMLEAGUE_P_MENU_TREE');
					$limage2[] = HTMLHelper::_('image',$imagePath . 'icon-16-Tree.png',Text::_('COM_JOOMLEAGUE_P_MENU_TREE'));
				}
				// Project-Position
				$link2[] = Route::_('index.php?option=com_joomleague&view=projectpositions');
				$label2[] = Text::_('COM_JOOMLEAGUE_P_MENU_POSITIONS');
				$limage2[] = HTMLHelper::_('image',$imagePath . 'icon-16-Positions.png',Text::_('COM_JOOMLEAGUE_P_MENU_POSITIONS'));
				// Project-Referee
				$link2[] = Route::_('index.php?option=com_joomleague&view=projectreferees');
				$label2[] = Text::_('COM_JOOMLEAGUE_P_MENU_REFEREES');
				$limage2[] = HTMLHelper::_('image',$imagePath . 'icon-16-Referees.png',Text::_('COM_JOOMLEAGUE_P_MENU_REFEREES'));
				// Project-Team
				$link2[] = Route::_('index.php?option=com_joomleague&view=projectteams');
				$label2[] = Text::_('COM_JOOMLEAGUE_P_MENU_TEAMS');
				$limage2[] = HTMLHelper::_('image',$imagePath . 'icon-16-Teams.png',Text::_('COM_JOOMLEAGUE_P_MENU_TEAMS'));
				// Round
				$link2[] = Route::_('index.php?option=com_joomleague&view=rounds');
				$label2[] = Text::_('COM_JOOMLEAGUE_P_MENU_ROUNDS');
				$limage2[] = HTMLHelper::_('image',$imagePath . 'icon-16-Matchdays.png',Text::_('COM_JOOMLEAGUE_P_MENU_ROUNDS'));
				// JLXML-Export
				$link2[] = Route::_('index.php?option=com_joomleague&task=jlxmlexport.export');
				$label2[] = Text::_('COM_JOOMLEAGUE_P_MENU_XML_EXPORT');
				$limage2[] = HTMLHelper::_('image',$imagePath . 'icon-16-XMLExportData.png',Text::_('COM_JOOMLEAGUE_P_MENU_XML_EXPORT'));
			}
			// Assign to array
			$link[] = $link2;
			$label[] = $label2;
			$limage[] = $limage2;
		}

		// DEFININING - ARRAY PART3

		$link3 = array();
		$label3 = array();
		$limage3 = array();

		$pane = new stdClass();
		$pane->title = Text::_('COM_JOOMLEAGUE_M_MENU_MAINTENANCE');
		$pane->name = 'MMenu';
		$pane->alert = false;
		$tabs[] = $pane;

		if(Factory::getUser()->authorise('core.manage'))
		{
			// Settings
			$link3[] = Route::_('index.php?option=com_joomleague&task=settings.edit');
			$label3[] = Text::_('COM_JOOMLEAGUE_M_MENU_SETTINGS');
			$limage3[] = HTMLHelper::_('image',$imagePath.'settings.png',Text::_('COM_JOOMLEAGUE_M_MENU_SETTINGS'));
			// XML Import
			$link3[] = Route::_('index.php?option=com_joomleague&view=jlxmlimports');
			$label3[] = Text::_('COM_JOOMLEAGUE_M_MENU_XML_IMPORT');
			$limage3[] = HTMLHelper::_('image',$imagePath.'import.png',Text::_('COM_JOOMLEAGUE_M_MENU_XML_IMPORT'));
			// Updates
			$link3[] = Route::_('index.php?option=com_joomleague&view=updates');
			$label3[] = Text::_('COM_JOOMLEAGUE_M_MENU_UPDATES');
			$limage3[] = HTMLHelper::_('image',$imagePath.'update.png',Text::_('COM_JOOMLEAGUE_M_MENU_UPDATES'));
			// Tools
			$link3[] = Route::_('index.php?option=com_joomleague&view=tools');
			$label3[] = Text::_('Tools');
			$limage3[] = HTMLHelper::_('image',$imagePath.'repair.gif',Text::_('Tools'));
		}
		// Assign to array
		$link[] = $link3;
		$label[] = $label3;
		$limage[] = $limage3;

		// active pane selector (project)
		$view = $input->getCmd('view');
		if (preg_match("/^(project|league|season|sportstype|club|team|person|eventtype|statistic|position|playground)s?$/", $view))
		{
			// For General list and item views
			if ($view == 'project' && $input->getCmd('return') == 'cpanel' && $project)
			{
				// If editing a project was initiated from the Project menu, show the project menu
				$active = 1;
			}
			else
			{
				// For all other cases show the General menu
				$active = 0;
			}
		}
		else if (preg_match("/^(settings|updates|jlxmlimports|databasetools|tools|about)$/", $view))
		{
			// For Administration views (depending if $project is set)
			$active = $project ? 2 : 1;
		}
		else
		{
			// For Project views (depending if $project is set)
			$active = $input->getInt('active', $project ? 1 : 0);
		}

		$this->version = $version;
		$this->link = $link;
		$this->tabs = $tabs;
		$this->label = $label;
		$this->lists = $lists;
		$this->active = $active;
		$this->limage = $limage;
		$this->project = $project;
		$this->sports_type_id = $sporttype_id;
		/* $this->management = $management; */

		parent::display('admin');
	}
}
