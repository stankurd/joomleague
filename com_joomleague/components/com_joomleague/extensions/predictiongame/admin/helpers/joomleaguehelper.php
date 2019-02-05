<?php
/**
 * Joomleague
 *
 * @copyright	Copyright (C) 2005-2016 joomleague.at. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @link		http://www.joomleague.at
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die;
use Joomla\CMS\Factory;
use Joomla\CMS\Access\Access;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Date\Date;
use Joomla\CMS\Filesystem\File;
use Joomla\CMS\Filesystem\Folder;
use Joomla\CMS\Filesystem\Path;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Log\Log;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;
use Joomla\CMS\Helper\ContentHelper; 
use Joomla\Utilities\ArrayHelper;
use Joomla\CMS\Object\CMSObject;
if( !defined('THUMBLIB_BASE_PATH') ) {
	require_once(JLG_PATH_SITE.'/assets/classes/PHPThumb/ThumbLib.inc.php');
}

jimport('joomla.filesystem.file');
jimport('joomla.filesystem.folder');

class JoomleagueHelper extends ContentHelper
{
	public static function addSubmenu($vName = 'admin') {
	    
	   // HTMLHelperSidebar::addEntry(
	/**		 
     * 
     * Add a menu on the sidebar of page
     */
	    $app = Factory::getApplication();
		$input = $app->input;
		$option = $input->getCmd('option');
		$lists = array();

		HTMLHelper::_('behavior.framework');
		HTMLHelper::_('behavior.core');
		$db = Factory::getDbo();
		$document = Factory::getDocument();
		$version = urlencode(JoomleagueHelper::getVersion());

		$document->addScript(Uri::base() . 'components/com_joomleague/assets/js/quickmenu.js?v=' . $version);

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
		$sportstypes[] = HTMLHelper::_('option','0',Text::_('COM_JOOMLEAGUE_GLOBAL_SELECT_SPORTSTYPE'),'id','name');
		$allSportstypes = array_merge($sportstypes,$allSportstypes);

		$lists['sportstypes'] = HTMLHelper::_('select.genericList',$allSportstypes,'stid[]','class="inputbox" style="width:100%"','id','name',
				$sporttype_id);

		if($sporttype_id)
		{
			// seasons
			$availableSeasons = $mdlJoomleague->getSeasons();
			$seasons = array();
			$seasons[] = HTMLHelper::_('option','0',Text::_('COM_JOOMLEAGUE_GLOBAL_SELECT_SEASON'),'id','name');
			
			if ($availableSeasons) {
				$allSeasons = array_merge($seasons, $availableSeasons);
			} else {
				$allSeasons = array_merge($seasons);
			}
			
			$lists['seasons'] = HTMLHelper::_('select.genericList',$allSeasons,'seasonnav','class="inputbox" style="width:100%"','id','name',$seasonid);

			// build the html select list for projects
			$projects = array();
			$projects[] = HTMLHelper::_('option','0',Text::_('COM_JOOMLEAGUE_GLOBAL_SELECT_PROJECT'),'id','name');

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
			$projectteams[] = HTMLHelper::_('option','0',Text::_('COM_JOOMLEAGUE_GLOBAL_SELECT_TEAM'),'value','text');

			if($res = $mdlJoomleague->getProjectteams())
			{
				$projectteams = array_merge($projectteams,$res);
			}

			$lDummy = 'class="inputbox" ';
			$lDummy .= 'style="width:100%"';
			$lists['projectteams'] = HTMLHelper::_('select.genericList',$projectteams,'tid[]','class="inputbox" style="width:100%"','value','text',
					$team_id);

			$round_id = $app->getUserState($option . 'round_id');
			$projectrounds[] = HTMLHelper::_('option','0',Text::_('COM_JOOMLEAGUE_GLOBAL_SELECT_ROUND'),'value','text');

			$mdlRound = BaseDatabaseModel::getInstance('Round','JoomleagueModel');
			$round = $mdlRound->getItem($project->current_round);

			$projectrounds[] = HTMLHelper::_('option',$round->id,Text::_('COM_JOOMLEAGUE_GLOBAL_SELECT_CURRENT_ROUND'),'value','text');
			if($ress = JoomleagueHelper::getRoundsOptions($project_id,'ASC',true))
			{
				foreach($ress as $res)
				{
				    $project_roundslist[] = HTMLHelper::_('option',$res->value,$res->text);
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

	
	}

	/**
	 * Method to return a project array (id,name)
	 *
	 * @access	public
	 * @return	array project
	 */
	public static function getProjects()
	{
	    $app = Factory:: getApplication();
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		$query->select(array('id','name'));
		$query->from('#__joomleague_project');
		$query->order(array('ordering','name ASC'));
		$db->setQuery($query);

		try
		{
			$result	=	$db->loadObjectList();
		}
		catch (RuntimeException $e)
		{
			$app->enqueueMessage(Text::_($e->getMessage()),'warning');
			return false;
		}

		return $result;
	}


	/**
	 * Method to return the project teams array (id,name)
	 *
	 * @access	public
	 * @return	array
	 */
	public static function getProjectteams($project_id)
	{
	    $app = Factory:: getApplication();
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		$query->select(array('t.name AS text','t.notes'));
		$query->from('#__joomleague_team AS t');

		// join project_team table
		$query->select('pt.id AS value');
		$query->join('LEFT', '#__joomleague_project_team AS pt ON pt.team_id=t.id');

		$query->where('pt.project_id = '.$project_id);
		$query->order('name ASC');
		$db->setQuery($query);

		try
		{
			$result	= $db->loadObjectList();
		}
		catch (RuntimeException $e)
		{
			$app->enqueueMessage(Text::_($e->getMessage()),'warning');
			return false;
		}

		return $result;
	}


	/**
	 * Method to return the project teams array (id,name)
	 *
	 * @access	public
	 * @return	array
	 */
	public static function getProjectteamsNew($project_id)
	{
	    $app = Factory:: getApplication();
		$db = Factory::getDbo();
		$query = $db->getQuery(true);

		$query->select(array('t.name AS text','t.notes'));
		$query->from('#__joomleague_team AS t');

		// join project_team table
		$query->select('pt.team_id AS value');
		$query->join('LEFT','#__joomleague_project_team AS pt ON pt.team_id = t.id');

		$query->where('pt.project_id='.(int) $project_id);
		$query->order('name ASC');
		$db->setQuery($query);

		try
		{
			$result	= $db->loadObjectList();
		}
		catch (RuntimeException $e)
		{
			$app->enqueueMessage(Text::_($e->getMessage()),'warning');
			return false;
		}

		return $result;
	}


	/**
	 * Return info of favorite team (of selected Project)
	 */
	public static function getProjectFavTeams($project_id)
	{
	    $app = Factory:: getApplication();
		$db = Factory::getDbo();
		$query = $db->getQuery(true);

		$query->select(array('fav_team','fav_team_color','fav_team_text_color','fav_team_highlight_type','fav_team_text_bold'));
		$query->from('#__joomleague_project');

		$query->where('id='.(int) $project_id);
		$db->setQuery($query);

		try
		{
			$result	= $db->loadObject();
		}
		catch (RuntimeException $e)
		{
			$app->enqueueMessage(Text::_($e->getMessage()),'warning');
			return false;
		}

		return $result;
	}


	/**
	 * Method to return a SportsType name
	 *
	 * @access	public
	 * @return	array project
	 */
	public static function getSportsTypeName($sportsType)
	{
	    $app = Factory:: getApplication();
		$db = Factory::getDbo();
		$query = $db->getQuery(true);

		$query->select('name');
		$query->from('#__joomleague_sports_type');
		$query->where('id='.(int) $sportsType);
		$db->setQuery($query);

		try
		{
			$result	= $db->loadResult();
		}
		catch (RuntimeException $e)
		{
			$app->enqueueMessage(Text::_($e->getMessage()),'warning');
			return false;
		}

		$lang 		= Factory::getLanguage();
		$lang->load('com_joomleague_sport_types', JPATH_ADMINISTRATOR);

		return Text::_($result);
	}


	/**
	 * Method to return a sportsTypees array (id,name)
	 *
	 * @access	public
	 * @return	array seasons
	 */
	public static function getSportsTypes()
	{
	    $app = Factory:: getApplication();
		$db = Factory::getDbo();
		$query = $db->getQuery(true);

		$query->select(array('id','name'));
		$query->from('#__joomleague_sports_type');
		$query->order('name ASC');
		$db->setQuery($query);

		try
		{
			$result	= $db->loadObjectList();
		}
		catch (RuntimeException $e)
		{
		    $app->enqueueMessage(Text::_($e->getMessage()),'warning');
			return false;
		}

		$lang 		= Factory::getLanguage();
		$lang->load('com_joomleague_sport_types', JPATH_ADMINISTRATOR);

		$sportsType = array();
		foreach ($result as $sportstype){
			$sportstype->name = Text::_($sportstype->name);
			$sportsType[] = $sportstype;
		}
		return $sportsType;
	}


	/**
	 * Method to return a SportsType name
	 *
	 * @access	public
	 * @return	array project
	 */
	public static function getPosPersonTypeName($personType)
	{
		switch ($personType)
		{
			case 1:
				$result =	Text::_('COM_JOOMLEAGUE_F_PLAYERS');
				break;
			case 2:
				$result =	Text::_('COM_JOOMLEAGUE_F_TEAM_STAFF');
				break;
			case 3:
				$result =	Text::_('COM_JOOMLEAGUE_F_REFEREES');
				break;
			case 4:
				$result =	Text::_('COM_JOOMLEAGUE_F_CLUB_STAFF');
				break;
		}
		return $result;
	}


	/**
	 * return name of extension assigned to current project.
	 * @param int project_id
	 * @return string or false
	 */
	public static function getExtension($project_id=0)
	{
	    $app = Factory:: getApplication();
		if (!$project_id)
		{
			$app	= Factory::getApplication();
		    $option = $app->input->get('option');
			$project_id = $app->getUserState($option.'project',0);
		}
		if (!$project_id)
		{
			return false;
		}

		$db = Factory::getDbo();
		$query = $db->getQuery(true);

		$query->select('extension');
		$query->from('#__joomleague_project');
		$query->where('id='. $db->Quote((int)$project_id));
		$db->setQuery($query);

		try
		{
			$result	= $db->loadResult();
		}
		catch (RuntimeException $e)
		{
			$app->enqueueMessage(Text::_($e->getMessage()),'warning');
			return false;
		}

		if (empty($result)) {
			$result = false;
		}

		return $result;
	}


	/**
	 * Return Extensions of selected project
	 */
	public static function getExtensions($project_id)
	{
	    $app = Factory:: getApplication();
		$arrExtensions 		= array();
		$excludeExtension	= array();

		if ($project_id) {
			$db = Factory::getDbo();
			$query = $db->getQuery(true);
			$query->select('extension');
			$query->from('#__joomleague_project');
			$query->where('id='. $db->Quote($project_id));
			$db->setQuery($query);

			try
			{
				$result	= $db->loadObject();
			}
			catch (RuntimeException $e)
			{
				$app->enqueueMessage(Text::_($e->getMessage()),'warning');
				return false;
			}

			if(!empty($result)) {
				$excludeExtension = explode(",", $result->extension);
			}
		}

		if(Folder::exists(JPATH_SITE.'/components/com_joomleague/extensions')) {
			$folderExtensions  = Folder::folders(JPATH_SITE.'/components/com_joomleague/extensions',
					'.', false, false, $excludeExtension);
			if($folderExtensions !== false) {
				foreach ($folderExtensions as $ext)
				{
					$arrExtensions[] = $ext;
				}
			}
		}

		return $arrExtensions;
	}


	/**
	 * returns number of years between 2 dates
	 *
	 * @param string $birthday date in YYYY-mm-dd format
	 * @param string $current_date date in YYYY-mm-dd format,default to today
	 * @return int age
	 */
	public static function getAge($date, $seconddate)
	{
		if (($date != "0000-00-00") &&
			(preg_match('/([0-9]{4})-([0-9]{2})-([0-9]{2})/',$date,$regs) ) &&
			($seconddate == "0000-00-00"))
		{
			$intAge=date('Y') - $regs[1];
			if($regs[2] > date('m'))
			{
				$intAge--;
			}
			else
			{
				if($regs[2] == date('m'))
				{
					if($regs[3] > date('d')) $intAge--;
				}
			}
			return $intAge;
		}

		if (($date != "0000-00-00") &&
			(preg_match('/([0-9]{4})-([0-9]{2})-([0-9]{2})/',$date,$regs)) &&
			($seconddate != "0000-00-00") &&
			(preg_match('/([0-9]{4})-([0-9]{2})-([0-9]{2})/',$seconddate,$regs2)))
		{
			$intAge=$regs2[1] - $regs[1];
			if($regs[2] > $regs2[2])
			{
				$intAge--;
			}
			else
			{
				if($regs[2] == $regs2[2])
				{
					if($regs[3] > $regs2[3] ) $intAge--;
				}
			}
			return $intAge;
		}

		return '-';
	}
	

	/**
	 * returns the default placeholder
	 *
	 * @param string $type ,default is player
	 * @return string placeholder (path)
	 */
	public static function getDefaultPlaceholder($type="player")
	{

		$params		 	=	ComponentHelper::getParams('com_joomleague');
		$ph_player		=	$params->get('ph_player','images/com_joomleague/database/placeholders/placeholder_150_2.png');
		$ph_logo_big	=	$params->get('ph_logo_big','images/com_joomleague/database/placeholders/placeholder_150.png');
		$ph_logo_medium	=	$params->get('ph_logo_medium','images/com_joomleague/database/placeholders/placeholder_50.png');
		$ph_logo_small	=	$params->get('ph_logo_small','images/com_joomleague/database/placeholders/placeholder_small.gif');
		$ph_icon		=	$params->get('ph_icon',0);
		$ph_team		=	$params->get('ph_team','images/com_joomleague/database/placeholders/placeholder_450_2.png');
		$ph_playground	=	$params->get('ph_playground','images/com_joomleague/database/placeholders/placeholder_450_2.png');
		$ph_flag_small	=	$params->get('ph_flag_small',0);
		$ph_flag_big	=	$params->get('ph_flag_big',0);

		// setup the different placeholders
		switch ($type)
		{
			case "player": // player
				return $ph_player;
				break;
			case "clublogobig": // club logo big
				return $ph_logo_big;
				break;
			case "clublogomedium": // club logo medium
				return $ph_logo_medium;
				break;
			case "clublogosmall": // club logo small
				return $ph_logo_small;
				break;
			case "icon": // icon
				return $ph_icon;
				break;
			case "team": // team picture
				return $ph_team;
				break;
			case "playground": // playground picture
				return $ph_playground;
				break;
			case "flag_small": // small flag icon
				return $ph_flag_small;
				break;
			case "flag_big": // big flag icon
				return $ph_flag_big;
				break;
			default:
				$picture = null;
				break;
		}
	}


	/**
	 * static method which return a <img> tag with the given picture
	 * @param string $picture
	 * @param string $alttext
	 * @param int $width=40, if set to 0 the original picture width will be used
	 * @param int $height=40, if set to 0 the original picture height will be used
	 * @param int $type=0, 0=player, 1=club logo big, 2=club logo medium, 3=club logo small, 4=icon, 5=team, 6=small flag, 7=big flag
	 * @return string
	 */
	public static function getPictureThumb($picture, $alttext, $width=40, $height=40, $type=0)
	{
		$ret = "";
		$picturepath 	= 	Path::clean(JPATH_SITE.'/'.str_replace(JPATH_SITE.'/', '', $picture));
		$params		 	=	ComponentHelper::getParams('com_joomleague');
		$ph_player		=	$params->get('ph_player',0);
		$ph_logo_big	=	$params->get('ph_logo_big',0);
		$ph_logo_medium	=	$params->get('ph_logo_medium',0);
		$ph_logo_small	=	$params->get('ph_logo_small',0);
		$ph_icon		=	$params->get('ph_icon',0);
		$ph_team		=	$params->get('ph_team',0);
		$ph_flag_small	=	$params->get('ph_flag_small',0);
		$ph_flag_big	=	$params->get('ph_flag_big',0);

		if (!file_exists($picturepath) || $picturepath == JPATH_SITE.'/')
		{
			//setup the different placeholders
			switch ($type)
			{
				case 0: // player
					$picture = $ph_player;
					break;
				case 1: // club logo big
					$picture = $ph_logo_big;
					break;
				case 2: // club logo medium
					$picture = $ph_logo_medium;
					break;
				case 3: // club logo small
					$picture = $ph_logo_small;
					break;
				case 4: // icon
					$picture = $ph_icon;
					break;
				case 5: // team picture
					$picture = $ph_team;
					break;
				case 6: // small flag picture
					$picture = $ph_flag_small;
					break;
				case 7: // big flag picture
					$picture = $ph_flag_big;
					break;
				default:
					$picture = null;
					break;
			}
		}

		if (!empty($picture) && is_file(Path::clean(JPATH_SITE.'/'.str_replace(JPATH_SITE.'/', '', $picture))))
		{
			$params = ComponentHelper::getParams('com_joomleague');
			$format = "JPG"; // PNG is not working in IE8
			$format = $params->get('thumbformat', 'PNG');
			$bUseThumbLib = $params->get('usethumblib', false);
			$useThumbCache = $params->get('usethumbnailcache', false);
			// Set vars to check if thumbnailcreation is needed
			list($source_width, $source_height) = getimagesize(Path::clean(JPATH_SITE.'/'.str_replace(JPATH_SITE.'/', '', $picture)));
			$needthumb=1;

			// Check if thumbnailcreation with phpThumb is really needed
			if ($height==$source_height && $width==$source_width)
			{
				$needthumb=0;
			}
			elseif ($height==0 && $width==$source_width)
			{
				$needthumb=0;
			}
			elseif ($height==$source_height && $width==0)
			{
				$needthumb=0;
			}
			elseif ($height==0 && $width==0)
			{
				$needthumb=0;
			}


// End Check
			if($bUseThumbLib && $needthumb==1 && $useThumbCache==0 && file_exists($picturepath)) {
				try {
					$thumb=PhpThumbFactory::create($picturepath);
					$thumb->setFormat($format);
					//height and width set, resize it with the thumblib
					if($height>0 && $width>0) {
						$thumb->resize ($width, $height);
						$pic=$thumb->getImageAsString();
						$ret .= '<img src="data:image/'.$format.';base64,'. base64_encode($pic);
						$ret .='" alt="'.$alttext.'" title="'.$alttext.'"/>';
					}
					//height==0 and width set, let the browser resize it
					if($height==0 && $width>0) {
						$thumb->setMaxWidth($width);
						$pic=$thumb->getImageAsString();
						$ret .= '<img src="data:image/'.$format.';base64,'. base64_encode($pic);
						$ret .='" style="width:'.$width.'px;" alt="'.$alttext.'" title="'.$alttext.'"/>';
					}
					//width==0 and height set, let the browser resize it
					if($height>0 && $width==0) {
						$thumb->setMaxHeight($height);
						$pic=$thumb->getImageAsString();
						$ret .= '<img src="data:image/'.$format.';base64,'. base64_encode($pic);
						$ret .='" style="height:'.$height.'px;" alt="'.$alttext.'" title="'.$alttext.'"/>';
					}
					//width==0 and height==0, use original picture size
					if($height==0 && $width==0) {
						$thumb->setMaxHeight($height);
						$pic=$thumb->getImageAsString();
						$ret .= '<img src="data:image/'.$format.';base64,'. base64_encode($pic);
						$ret .='" alt="'.$alttext.'" title="'.$alttext.'"/>';
					}
				} catch (Exception $e) {
					$ret = '';
				}
			} elseif($useThumbCache==0){
				$picturepath = $picture;
				$picture = Uri::root(true).'/'.str_replace(JPATH_SITE.'/', "", $picture);
				$title = $alttext;
				//height and width set, let the browser resize it
				$bUseHighslide = $params->get('use_highslide', false);
				// no highslide if the source picture has exact the same size as the parameters width/height
				// e.g placeholders or correct sized images
				if(function_exists('getimagesize') && File::exists($picturepath) && $width>0 && $height>0 ) {
					list($iWidth, $iHeight, $type, $attr) = getimagesize($picturepath);
					$bUseHighslide = ($width!=$iWidth && $iHeight!=$height) ? true : false;
				}
				$arrNoHighSlidePicTypes = array(3,4,6,7,99);
				if($bUseHighslide && !in_array($type, $arrNoHighSlidePicTypes)) {
					$title .= ' (' . Text::_('COM_JOOMLEAGUE_GLOBAL_CLICK_TO_ENLARGE') . ')';
					$ret .= '<a onclick="return hs.expand(this)" href="'.$picture.'" class="highslide">';
				}
				$ret .= '<img';
				$ret .= ' ';
				if($height>0 && $width>0) {
					$ret .= ' src="'.$picture.'"';
					$ret .= ' style="width:'.$width.'px;height:'.$height.'px;"';
					$ret .= ' alt="'.$alttext.'" title="'.$title.'"';
				}
				// height==0 and width set, let the browser resize it
				if($height==0 && $width>0) {
					$ret .= ' src="'.$picture.'"';
					$ret .= ' style="width:'.$width.'px;" alt="'.$alttext.'" title="'.$title.'"';
				}
				// width==0 and height set, let the browser resize it
				if($height>0 && $width==0) {
					$ret .= ' src="'.$picture.'"';
					$ret .= ' style="height:'.$height.'px;" alt="'.$alttext.'" title="'.$title.'"';
				}
				// width==0 and height==0, use original picture size
				if($height==0 && $width==0) {
					$ret .= ' src="'.$picture.'"';
					$ret .= ' alt="'.$alttext.'" title="'.$title.'"';
				}
				$ret .= '/>';
				if($bUseHighslide && !in_array($type, $arrNoHighSlidePicTypes)) {
					$ret .= '</a>';
				}
			}

// Use phpThumb to create cached images and check if the source-file really exists
			$picturepath 	= 	Path::clean(JPATH_SITE.'/'.str_replace(JPATH_SITE.'/', '', $picture));
			if($bUseThumbLib && $useThumbCache==1 && file_exists($picturepath))
			{
				$thumb_cache=PhpThumbFactory::create($picturepath);
				$thumb_cache->setFormat($format);
				if ($needthumb==1)
				{
// check if the cache-directory exitst if not create one
					$image_path_parts = pathinfo($picture);
					$image_cache_path=PATH::clean(JPATH_SITE.'/cache/joomleague/'.$image_path_parts[dirname]);
					if (!file_exists($image_cache_path))
					{
						mkdir($image_cache_path, 0750, true);
					}
// check if there is a chached actual image if not, create one
					$image_timestamp=date("mdY_His", filectime($picturepath));
					$cached_thumb=PATH::clean(JPATH_SITE.'/cache/joomleague/'.$image_path_parts[dirname].'/'.$image_timestamp.'_'.$height.'_'.$width.'_'.$image_path_parts[filename].'.'.$format);
					$web_cached_thumb=Uri::root(true).'/'.str_replace(JPATH_SITE.'/', "", $cached_thumb);

					if (!file_exists($cached_thumb))
					{
// Check if there is are older files. If Yes, delete them.
					$matches = glob(PATH::clean(JPATH_SITE.'/cache/joomleague/'.$image_path_parts[dirname].'/'.'*_'.$height.'_'.$width.'_'.$image_path_parts[filename].'*'));
					foreach ($matches as $delete_matches) {
						unlink($delete_matches);
					}
					//height and width set
					if($height>0 && $width>0) {
						$thumb_cache->adaptiveResize($width, $height)->save($cached_thumb, $format);
					}
					//height==0 and width set
					if($height==0 && $width>0) {
						$thumb_cache->resize($width,0)->save($cached_thumb, $format);
					}
					//width==0 and height set
					if($height>0 && $width==0) {
						$thumb_cache->resize(0,$height)->save($cached_thumb, $format);
					}
					//width==0 and height==0, do nothing
					if($height==0 && $width==0) {
						$web_cached_thumb=Uri::root(true).'/'.str_replace(JPATH_SITE.'/', "", $picture);
					}
					}
				}
				else
				{
					$web_cached_thumb=Uri::root(true).'/'.str_replace(JPATH_SITE.'/', "", $picture);
				}
// If windows Server is used, replace backslashes with slashes befor return.
				$web_cached_thumb=str_replace('\\', '/', $web_cached_thumb);

// return cached or uncached (if not necessary) images
				$title = $alttext;
				$bUseHighslide = $params->get('use_highslide', false);
				// no highslide if the source picture has exact the same size as the parameters width/height
				// e.g placeholders or correct sized images
				if(function_exists('getimagesize') && File::exists($picturepath) && $width>0 && $height>0 ) {
					list($iWidth, $iHeight, $type, $attr) = getimagesize($picturepath);
					$bUseHighslide = ($width!=$iWidth && $iHeight!=$height) ? true : false;
				}
				$arrNoHighSlidePicTypes = array(3,4,6,7,99);
				if($bUseHighslide && !in_array($type, $arrNoHighSlidePicTypes)) {
					$title .= ' (' . Text::_('COM_JOOMLEAGUE_GLOBAL_CLICK_TO_ENLARGE') . ')';
					$ret .= '<a onclick="return hs.expand(this)" href="'.$picture.'" class="highslide">';
				}
				$ret .= '<img ';
				$ret .= ' ';
				if($height>0 && $width>0) {
					$ret .= ' src="'.$web_cached_thumb.'"';
					$ret .= ' style="width:'.$width.'px;height='.$height.'px;"';
					$ret .= ' alt="'.$alttext.'" title="'.$title.'"';
				}
				//height==0 and width set, let the browser resize it
				if($height==0 && $width>0) {
					$ret .= ' src="'.$web_cached_thumb.'"';
					$ret .= ' style="width:'.$width.'px;" alt="'.$alttext.'" title="'.$title.'"';
				}
				//width==0 and height set, let the browser resize it
				if($height>0 && $width==0) {
					$ret .= ' src="'.$web_cached_thumb.'"';
					$ret .= ' style="height:'.$height.'px;" alt="'.$alttext.'" title="'.$title.'"';
				}
				//width==0 and height==0, use original picture size
				if($height==0 && $width==0) {
					$ret .= ' src="'.$web_cached_thumb;
					$ret .='" alt="'.$alttext.'" title="'.$title.'"';
				}
				$ret .= '/>';
				if($bUseHighslide && !in_array($type, $arrNoHighSlidePicTypes)) {
					$ret .= '</a>';
				}

			}

		}

		return $ret;
	}


	/**
	 * static method which extends template path for given view names
	 * Can be used by views to search for extensions that implement parts of common views
	 * and add their path to the template search path.
	 * (e.g. 'projectheading', 'backbutton', 'footer')
	 * @param array(string) $viewnames, names of views for which templates need to be loaded,
	 *                      so that extensions are used when available
	 * @param JLGView       $view to which the template paths should be added
	 */
	public static function addTemplatePaths($templatesToLoad, &$view)
	{
		$app 	= Factory::getApplication();
		$input = $app->input;

		$extensions = JoomleagueHelper::getExtensions($input->getInt('p'));
		foreach ($templatesToLoad as $template)
		{
			$view->addTemplatePath(JPATH_COMPONENT.'/views/'.$template.'/tmpl');
			if (is_array($extensions) && count($extensions) > 0)
			{
				foreach ($extensions as $e => $extension)
				{
					$extension_views = JPATH_COMPONENT_SITE.'/extensions/'.$extension.'/views';
					$tmpl_path = $extension_views.'/'.$template.'/tmpl';
					if (Folder::exists($tmpl_path))
					{
						$view->addTemplatePath($tmpl_path);
					}
				}
			}
		}
	}

	public function getTimezone()
	{
	    $timezone = $this->getParam('timezone', Factory::getApplication()->get('offset', 'GMT'));
	    
	    return new DateTimeZone($timezone);
	}
	/**
	 * Convert the UTC timestamp of a match (stored as UTC in the database) to:
	 * - the timezone of the Joomla user if that is set
	 * - to the project timezone as set in the project otherwise (so also for guest users,
	 *   aka visitors that have not logged in).
	 *
	 * @param match $match Typically obtained from a DB-query and contains the match_date and timezone (of the project)
	 */
	public static function convertMatchDateToTimezone(&$match)
	{
		if ($match->match_date > 0)
		{
			$app = Factory::getApplication();
			if ($app-> isClient('administrator'))
			{
				$project_id = $app->getUserState('com_joomleagueproject');
				$mdlProject = BaseDatabaseModel::getInstance('project','JoomleagueModel');
				$project 	= $mdlProject->getItem($project_id);
				$timezone 	= $project->timezone;
				if (is_null($timezone)) {
					$timezone = 'UTC';
				}
			}
			else
			{
				// Otherwise use user timezone for display, and if not set use the project timezone
				$user = Factory::getUser();
	 			$timezone = $user->getParam('timezone', $match->timezone);
			}

	 		$matchDate = new Date($match->match_date, 'UTC');
	 		$matchDate->setTimezone(new DateTimeZone($timezone));

	 		$match->match_date = $matchDate;
	 		$match->timezone = $timezone;
		} else {
			$match->match_date = null;
		}
	}


	/**
	 * getMatchDate
	 */
	public static function getMatchDate($match, $format = 'Y-m-d')
	{
		return $match->match_date ? $match->match_date->format($format, true) : "0000-00-00";
	}


	/**
	 * getMatchTime
	 */
	public static function getMatchTime($match, $format = 'H:i')
	{
		return $match->match_date ? $match->match_date->format($format, true) : "00:00";
	}


	/**
	 * getMatchStartTimestamp
	 */
	public static function getMatchStartTimestamp($match, $format = 'Y-m-d H:i')
	{
		return $match->match_date ? $match->match_date->format($format, true) : "0000-00-00 00:00";
	}


	/**
	 * getMatchEndTimestamp
	 */
	public static function getMatchEndTimestamp($match, $totalMatchDuration, $format = 'Y-m-d H:i')
	{
		$endTimestamp = "0000-00-00 00:00";
		if ($match->match_date)
		{
			$start = new DateTime(self::getMatchStartTimestamp($match));
			$end = $start->add(new DateInterval('PT'.$totalMatchDuration.'M'));
			$endTimestamp = $end->format($format);
		}
		return $endTimestamp;
	}


	/**
	 * getMatchTimezone
	 */
	public static function getMatchTimezone($match)
	{
		return $match->timezone;
	}


	/**
	 * Method to convert a date from 0000-00-00 to 00-00-0000 or back
	 * return a date string
	 * $direction == 1 means from convert from 0000-00-00 to 00-00-0000
	 * $direction != 1 means from convert from 00-00-0000 to 0000-00-00
	 * call by JoomleagueHelper::convertDate($date) inside the script
	 *
	 * When no "-" are given in $date two short date formats (DDMMYYYY and DDMMYY) are supported
	 * for example "31122011" or "311211" for 31 december 2011
	 *
	 * @access	public
	 * @return	array
	 */
	public static function convertDate($DummyDate,$direction=1)
	{
		$result = '';
		if(!strpos($DummyDate,"-")!==false)
		{
			// for example 31122011 is used for 31 december 2011
			if (strlen($DummyDate) == 8 )
			{
				$result  = substr($DummyDate,4,4);
				$result .= '-';
				$result .= substr($DummyDate,2,2);
				$result .= '-';
				$result .= substr($DummyDate,0,2);
			}
			// for example 311211 is used for 31 december 2011
			elseif (strlen($DummyDate) == 6 )
			{
				$result  = substr(date("Y"),0,2);
				$result .= substr($DummyDate,4,2);
				$result .= '-';
				$result .= substr($DummyDate,2,2);
				$result .= '-';
				$result .= substr($DummyDate,0,2);
			}
		}
		else
		{

			if ($direction == 1)
			{
				$result  = substr($DummyDate,8);
				$result .= '-';
				$result .= substr($DummyDate,5,2);
				$result .= '-';
				$result .= substr($DummyDate,0,4);
			}
			else
			{
				$result  = substr($DummyDate,6,4);
				$result .= '-';
				$result .= substr($DummyDate,3,2);
				$result .= '-';
				$result .= substr($DummyDate,0,2);
			}
		}

		return $result;
	}


	/**
	 * showTeamIcons
	 */
	public static function showTeamIcons(&$team,&$config)
	{
		if(!isset($team->projectteamid)) {
			return "";
		}
		$projectteamid = $team->projectteamid;
		$teamname      = $team->name;
		$teamid        = $team->team_id;
		$teamSlug      = (isset($team->team_slug) ? $team->team_slug : $teamid);
		$clubSlug      = (isset($team->club_slug) ? $team->club_slug : $team->club_id);
		$division_slug = (isset($team->division_slug) ? $team->division_slug : $team->division_id);
		$projectSlug   = (isset($team->project_slug) ? $team->project_slug : $team->project_id);
		$output        = '';

		if ($config['show_team_link'])
		{
			$link =JoomleagueHelperRoute::getPlayersRoute($projectSlug,$teamSlug);
			$title=Text::_('COM_JOOMLEAGUE_TEAMICONS_ROSTER_LINK').'&nbsp;'.$teamname;
			$picture = 'media/com_joomleague/jl_images/team_icon.png';
			$desc = self::getPictureThumb($picture, $title, 0, 0, 4);
			$output .= HTMLHelper::link($link,$desc);
		}

		if (((!isset($team_plan)) || ($teamid!=$team_plan->id)) && ($config['show_plan_link']))
		{
			$link =JoomleagueHelperRoute::getTeamPlanRoute($projectSlug,$teamSlug,$division_slug);
			$title=Text::_('COM_JOOMLEAGUE_TEAMICONS_TEAMPLAN_LINK').'&nbsp;'.$teamname;
			$picture = 'media/com_joomleague/jl_images/calendar_icon.gif';
			$desc = self::getPictureThumb($picture, $title, 0, 0, 4);
			$output .= HTMLHelper::link($link,$desc);
		}

		if ($config['show_curve_link'])
		{
			$link =JoomleagueHelperRoute::getCurveRoute($projectSlug,$teamSlug,0,$division_slug);
			$title=Text::_('COM_JOOMLEAGUE_TEAMICONS_CURVE_LINK').'&nbsp;'.$teamname;
			$picture = 'media/com_joomleague/jl_images/curve_icon.gif';
			$desc = self::getPictureThumb($picture, $title, 0, 0, 4);
			$output .= HTMLHelper::link($link,$desc);
		}

		if ($config['show_teaminfo_link'])
		{
			$link =JoomleagueHelperRoute::getTeamInfoRoute($projectSlug,$teamid);
			$title=Text::_('COM_JOOMLEAGUE_TEAMICONS_TEAMINFO_LINK').'&nbsp;'.$teamname;
			$picture = 'media/com_joomleague/jl_images/teaminfo_icon.png';
			$desc = self::getPictureThumb($picture, $title, 0, 0, 4);
			$output .= HTMLHelper::link($link,$desc);
		}

		if ($config['show_club_link'])
		{
			$link =JoomleagueHelperRoute::getClubInfoRoute($projectSlug,$clubSlug);
			$title=Text::_('COM_JOOMLEAGUE_TEAMICONS_CLUBINFO_LINK').'&nbsp;'.$teamname;
			$picture = 'media/com_joomleague/jl_images/mail.gif';
			$desc = self::getPictureThumb($picture, $title, 0, 0, 4);
			$output .= HTMLHelper::link($link,$desc);
		}

		if ($config['show_teamstats_link'])
		{
			$link =JoomleagueHelperRoute::getTeamStatsRoute($projectSlug,$teamSlug);
			$title=Text::_('COM_JOOMLEAGUE_TEAMICONS_TEAMSTATS_LINK').'&nbsp;'.$teamname;
			$picture = 'media/com_joomleague/jl_images/teamstats_icon.png';
			$desc = self::getPictureThumb($picture, $title, 0, 0, 4);
			$output .= HTMLHelper::link($link,$desc);
		}

		if ($config['show_clubplan_link'])
		{
			$link =JoomleagueHelperRoute::getClubPlanRoute($projectSlug,$clubSlug);
			$title=Text::_('COM_JOOMLEAGUE_TEAMICONS_CLUBPLAN_LINK').'&nbsp;'.$teamname;
			$picture = 'media/com_joomleague/jl_images/clubplan_icon.png';
			$desc = self::getPictureThumb($picture, $title, 0, 0, 4);
			$output .= HTMLHelper::link($link,$desc);
		}

		if ($config['show_rivals_link'])
		{
			$link =JoomleagueHelperRoute::getRivalsRoute($projectSlug,$teamSlug);
			$title=Text::_('COM_JOOMLEAGUE_TEAMICONS_RIVALS_LINK').'&nbsp;'.$teamname;
			$picture = 'media/com_joomleague/jl_images/rivals.png';
			$desc = self::getPictureThumb($picture, $title, 0, 0, 4);
			$output .= HTMLHelper::link($link,$desc);
		}

		return $output;
	}


	/**
	 * formatTeamName
	 */
	public static function formatTeamName($team, $containerprefix, &$config, $isfav=0, $link=null)
	{
		$output			= '';
		$desc			= '';

		if ((isset($config['results_below'])) && ($config['results_below']) && ($config['show_logo_small']))
		{
			$js_func		= 'visibleMenu';
			$style_append	= 'visibility:hidden';
			$container		= 'span';
		}
		else
		{
			$js_func		= 'switchMenu';
			$style_append	= 'display:none';
			$container		= 'div';
		}

		$showIcons=	(
				($config['show_info_link']==2) && ($isfav)
		) ||
		(
				($config['show_info_link']==1) &&
				(
						$config['show_club_link'] ||
						$config['show_team_link'] ||
						$config['show_curve_link'] ||
						$config['show_plan_link'] ||
						$config['show_teaminfo_link'] ||
						$config['show_teamstats_link'] ||
						$config['show_clubplan_link'] ||
						$config['show_rivals_link']
				)
		);
		$containerId = $containerprefix.'t'.$team->id.'p'.$team->project_id;
		if ($showIcons)
		{
			$onclick	= $js_func.'(\''.$containerId.'\');return false;';
			$params		= array('onclick' => $onclick);
		}

		$style = 'padding:2px;';
		if ($config['highlight_fav'] && $isfav)
		{
			$favs = self::getProjectFavTeams($team->project_id);
			$style .= ($favs->fav_team_text_bold != '') ? 'font-weight:bold;' : '';
			$style .= (trim($favs->fav_team_text_color) != '') ? 'color:'.trim($favs->fav_team_text_color).';' : '';
			$style .= (trim($favs->fav_team_color) != '') ? 'background-color:'.trim($favs->fav_team_color).';' : '';
		}

		$desc .= '<span style="'.$style.'">';

		$formattedTeamName = "";
		if ($config['team_name_format']== 0)
		{
			$formattedTeamName = $team->short_name;
		}
		else if ($config['team_name_format']== 1)
		{
			$formattedTeamName = $team->middle_name;
		}
		if (empty($formattedTeamName))
		{
			$formattedTeamName = $team->name;
		}

		if (($config['team_name_format']== 0) && (!empty($team->short_name)))
		{
			$desc .=  '<acronym title="'.$team->name.'">'.$team->short_name.'</acronym>';
		}
		else
		{
			$desc .= $formattedTeamName;
		}

		$desc .=  '</span>';

		if ($showIcons)
		{
			$output .= HTMLHelper::link('javascript:void(0);',$desc,$params);
			$output .= '<'.$container.' id="'.$containerId.'" style="'.$style_append.';">';
			$output .= self::showTeamIcons ($team,$config);
			$output .= '</'.$container.'>';
		}
		else
		{
			$output = $desc;
		}

		if ($link != null)
		{
			$output = HTMLHelper::link($link, $output);
		}

		return $output;
	}


	/**
	 *	showClubIcon
	 */
	public static function showClubIcon(&$team,$type=1,$with_space=0)
	{
		if (($type==1) && (isset($team->country)))
		{
			if ($team->logo_small!='')
			{
				echo HTMLHelper::image($team->logo_small,'');
				if ($with_space==1){
					echo ' style="padding:1px;"';
				}
			}
			else
			{
				echo '&nbsp;';
			}
		}
		elseif (($type==2) && (isset($team->country)))
		{
			echo Countries::getCountryFlag($team->country);
		}
	}


	/**
	 * Removes invalid XML
	 *
	 * @access public
	 * @param string $value
	 * @return string
	 */
	public static function stripInvalidXml($value)
	{
		$ret='';
		$current='';
		if (is_null($value)){
			return $ret;
		}

		$length=strlen($value);
		for ($i=0; $i < $length; $i++)
		{
			$current=ord($value{$i});
			if (($current == 0x9) ||
					($current == 0xA) ||
					($current == 0xD) ||
					(($current >= 0x20) && ($current <= 0xD7FF)) ||
					(($current >= 0xE000) && ($current <= 0xFFFD)) ||
					(($current >= 0x10000) && ($current <= 0x10FFFF)))
			{
				$ret .= chr($current);
			}
			else
			{
				$ret .= ' ';
			}
		}
		return $ret;
	}


	/**
	 * return Version
	 */
	public static function getVersion()
	{
	    $app = Factory:: getApplication();
		$db = Factory::getDbo();
		$query = $db->getQuery(true);

		$query->select('CONCAT(major,".",minor,".",build,".",revision) AS version');
		$query->from('#__joomleague_version');
		$query->order('date DESC');
		$db->setQuery($query,0,1);

		try
		{
			$result	= $db->loadResult();
		}
		catch (RuntimeException $e)
		{
			$app->enqueueMessage(Text::_($e->getMessage()),'warning');
			return false;
		}

		return $result;
	}


	/**
	 * returns formatName
	 *
	 * @param prefix
	 * @param firstName
	 * @param nickName
	 * @param lastName
	 * @param format
	 */
	public static function formatName($prefix, $firstName, $nickName, $lastName, $format)
	{
		$name = array();
		if ($prefix)
		{
			$name[] = $prefix;
		}
		switch ($format)
		{
			case 0: // Firstname 'Nickname' Lastname
				if ($firstName != "") {
					$name[] = $firstName;
				}
				if ($nickName != "") {
					$name[] = "'" . $nickName . "'";
				}
				if ($lastName != "") {
					$name[] = $lastName;
				}
				break;
			case 1: // Lastname, 'Nickname' Firstname
				if ($lastName != "") {
					$name[] = $lastName . ",";
				}
				if ($nickName != "") {
					$name[] = "'" . $nickName . "'";
				}
				if ($firstName != "") {
					$name[] = $firstName;
				}
				break;
			case 2: // Lastname, Firstname 'Nickname'
				if ($lastName != "") {
					$name[] = $lastName . ",";
				}
				if ($firstName != "") {
					$name[] = $firstName;
				}
				if ($nickName != "") {
					$name[] = "'" . $nickName . "'";
				}
				break;
			case 3: // Firstname Lastname
				if ($firstName != "") {
					$name[] = $firstName;
				}
				if ($lastName != "") {
					$name[] = $lastName;
				}
				break;
			case 4: // Lastname, Firstname
				if ($lastName != "") {
					$name[] = $lastName . ",";
				}
				if ($firstName != "") {
					$name[] = $firstName;
				}
				break;
			case 5: // 'Nickname' - Firstname Lastname
				if ($nickName != "") {
					$name[] = "'" . $nickName . "' - ";
				}
				if ($firstName != "") {
					$name[] = $firstName;
				}
				if ($lastName != "") {
					$name[] = $lastName;
				}
				break;
			case 6: // 'Nickname' - Lastname, Firstname
				if ($nickName != "") {
					$name[] = "'" . $nickName . "' - ";
				}
				if ($lastName != "") {
					$name[] = $lastName . ",";
				}
				if ($firstName != "") {
					$name[] = $firstName;
				}
				break;
			case 7: // Firstname Lastname (Nickname)
				if ($firstName != "") {
					$name[] = $firstName;
				}
				if ($lastName != "") {
					$name[] = $lastName ;
				}
				if ($nickName != "") {
					$name[] = "(" . $nickName . ")";
				}
				break;
			case 8: // F. Lastname
				if ($firstName != "") {
					$name[] = $firstName[0] . ".";
				}
				if ($lastName != "") {
					$name[] = $lastName;
				}
				break;
			case 9: // Lastname, F.
				if ($lastName != "") {
					$name[] = $lastName.",";
				}
				if ($firstName != "") {
					$name[] = $firstName[0] . ".";
				}
				break;
			case 10: // Lastname
				if ($lastName != "") {
					$name[] = $lastName;
				}
				break;
			case 11: // Firstname 'Nickname' L.
				if ($firstName != "") {
					$name[] = $firstName;
				}
				if ($nickName != "") {
					$name[] = "'" . $nickName . "'";
				}
				if ($lastName != "") {
					$name[] = $lastName[0]. ".";
				}
				break;
			case 12: // Nickname
				if ($nickName != "") {
					$name[] = $nickName;
				}
				break;
			case 13: // Firstname L.
				if ($firstName != "") {
					$name[] = $firstName;
				}
				if ($lastName != "") {
					$name[] = $lastName[0]. ".";
				}
				break;
			case 14: // Lastname Firstname
				if ($lastName != "") {
					$name[] = $lastName;
				}
				if ($firstName != "") {
					$name[] = $firstName;
				}
				break;
			case 15: // Lastname newline Firstname
				if ($lastName != "") {
					$name[] = $lastName;
					$name[] = '<br \>';
				}
				if ($firstName != "") {
					$name[] = $firstName;
				}
				break;
			case 16: // Firstname newline Lastname
				if ($lastName != "") {
					$name[] = $lastName;
					$name[] = '<br \>';
				}
				if ($firstName != "") {
					$name[] = $firstName;
				}
				break;
			case 17: // Lastname Firstname Nickname
				if ($lastName != "") {
					$name[] = $lastName;
				}
				if ($firstName != "") {
					$name[] = $firstName;
				}
				if ($nickName != "") {
					$name[] = $nickName;
				}
				break;
			case 18: // Lastname F.
				if ($lastName != "") {
					$name[] = $lastName;
				}
				if ($firstName != "") {
					$name[] = mb_substr($firstName,0,1).".";
				}
				break;

		}

		return implode(" ", $name);
	}


	/**
	 * returns titleInfo
	 *
	 * @param prefix string Text that must be placed at the start of the title.
	 */
	public static function createTitleInfo($prefix)
	{
		return (object)array(
			"prefix" => $prefix,
			"clubName" => null,
			"team1Name" => null,
			"team2Name" => null,
			"roundName" => null,
			"personName" => null,
			"playgroundName" => null,
			"projectName" => null,
			"divisionName" => null,
			"leagueName" => null,
			"seasonName" => null
		);
	}


	/**
	 * returns formatName
	 *
	 * @param titleInfo (info on prefix, teams (optional), project, division (optional), league and season)
	 * @param format
	 */
	public static function formatTitle($titleInfo, $format)
	{
		$name = array();

		if (!empty($titleInfo->personName)) {
			$name[] = $titleInfo->personName;
		}

		if (!empty($titleInfo->playgroundName)) {
			$name[] = $titleInfo->playgroundName;
		}

		if (!empty($titleInfo->team1Name)) {
			if (!empty($titleInfo->team2Name)) {
				$name[] = $titleInfo->team1Name." - ".$titleInfo->team2Name;
			} else {
				$name[] = $titleInfo->team1Name;
			}
		}

		if (!empty($titleInfo->clubName)) {
			$name[] = $titleInfo->clubName;
		}

		if (!empty($titleInfo->roundName)) {
			$name[] = $titleInfo->roundName;
		}

		$projectDivisionName = !empty($titleInfo->projectName) ? $titleInfo->projectName : "";
		if (!empty($titleInfo->divisionName)) $projectDivisionName .= " - ".$titleInfo->divisionName;

		switch ($format)
		{
			case 0: // Projectname
				if (!empty($projectDivisionName)) {
					$name[] = $projectDivisionName;
				}
				break;
			case 1: // Project and league name
				if (!empty($projectDivisionName)) {
					$name[] = $projectDivisionName;
				}
				if (!empty($titleInfo->leagueName)) {
					$name[] = $titleInfo->leagueName;
				}
				break;
			case 2: // Project, league and season name
				if (!empty($projectDivisionName)) {
					$name[] = $projectDivisionName;
				}
				if (!empty($titleInfo->leagueName)) {
					$name[] = $titleInfo->leagueName;
				}
				if (!empty($titleInfo->seasonName)) {
					$name[] = $titleInfo->seasonName;
				}
				break;
			case 3: // Project and season name
				if (!empty($projectDivisionName)) {
					$name[] = $projectDivisionName;
				}
				if (!empty($titleInfo->seasonName)) {
					$name[] = $titleInfo->seasonName;
				}
				break;
			case 4: // League name
				if (!empty($titleInfo->leagueName)) {
					$name[] = $titleInfo->leagueName;
				}
				break;
			case 5: // League and season name
				if (!empty($titleInfo->leagueName)) {
					$name[] = $titleInfo->leagueName;
				}
				if (!empty($titleInfo->seasonName)) {
					$name[] = $titleInfo->seasonName;
				}
				break;
			case 6: // Season name
				if (!empty($titleInfo->seasonName)) {
					$name[] = $titleInfo->seasonName;
				}
				break;
			case 7: // None
				break;
		}

		$result = $titleInfo->prefix . ": " . implode(" | ", $name);

		return $result;
	}


	/**
	 * Creates the print button
	 *
	 * @param string $print_link
	 * @param array $config
	 */
	public static function printbutton($print_link, &$config)
	{
		if ($config['show_print_button'] == 1)
		{
			HTMLHelper::_('behavior.tooltip');
			$app = Factory::getApplication();
			$input = $app->input;

			$status = 'status=no,toolbar=no,scrollbars=yes,titlebar=no,menubar=no,resizable=yes,width=800,height=600,directories=no,location=no';
			// checks template image directory for image, if non found default are loaded
			if ($config['show_icons'] == 1) {
				$image = HTMLHelper::image('media/com_joomleague/jl_images/printButton.png',Text::_('JGLOBAL_PRINT'));
			} else {
				$image = Text::_('JGLOBAL_PRINT');
			}
			if ($input->getInt('pop')) {
				// button in popup
				$output = '<a href="javascript: void(0)" onclick="window.print();return false;">'.$image.'</a>';
			} else {
				// button in view
				$overlib = Text::_('COM_JOOMLEAGUE_GLOBAL_PRINT_TIP');
				$text = Text::_('COM_JOOMLEAGUE_GLOBAL_PRINT');
				$print_urlparams = "tmpl=component&print=1";

				if(is_null($print_link)) {
					$output	= '<a href="javascript: void(0)" class="editlinktip hasTip" onclick="window.open(window.location.href + (window.location.href.indexOf(\'?\') != -1 ? \'&amp;\' : \'?\' ) + \''.$print_urlparams.'\',\'win2\',\''.$status.'\'); return false;" rel="nofollow" title="'.$text.'::'.$overlib.'">'.$image.'</a>';
				} else {
					$output	= '<a href="'. Route::_($print_link) .'" class="editlinktip hasTip" onclick="window.open(window.location.href + (window.location.href.indexOf(\'?\') != -1 ? \'&amp;\' : \'?\' ) +  \''.$print_urlparams.'\',\'win2\',\''.$status.'\'); return false;" rel="nofollow" title="'.$text.'::'.$overlib.'">'.$image.'</a>';
				}
			}
			return $output;
		}
		return '';
	}


	/**
	 * return project rounds as array of objects(roundid as value, name as text)
	 *
	 * @param string $ordering
	 * @return array
	 */
	public static function getRoundsOptions($project_id, $ordering='ASC', $required = false)
	{
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		$query = ' SELECT id as value '
				. '      , CASE LENGTH(name) when 0 then CONCAT('.$db->Quote(Text::_('COM_JOOMLEAGUE_GLOBAL_MATCHDAY_NAME')). ', " ", id)	else name END as text '
				. '      , id, name, round_date_first, round_date_last, roundcode '
				. ' FROM #__joomleague_round '
				. ' WHERE project_id= ' .$project_id
				. ' ORDER BY roundcode '.$ordering;

		$db->setQuery($query);
		if(!$required) {
			$mitems = array(HTMLHelper::_('select.option', '', Text::_('COM_JOOMLEAGUE_GLOBAL_SELECT')));
			try
			{
				$items	= $db->loadObjectList();
			}
			catch (RuntimeException $e)
			{
			    Factory::getApplication()->enqueueMessage(Text::_($e->getMessage()), 'warning');
				return false;
			}
			if(!empty($items)) {
				return array_merge($mitems, $items);
			} else {
				return $mitems;
			}
		} else {

			try
			{
				$items	= $db->loadObjectList();
			}
			catch (RuntimeException $e)
			{
			    Factory::getApplication()->enqueueMessage(Text::_($e->getMessage()), 'warning');
				return false;
			}

			return $items;
		}
	}


	/**
	 * removeBOM
	 */
	public static function removeBOM($str) {
		$bom = pack ( "CCC", 0xef, 0xbb, 0xbf );
		if (0 == strncmp ( $str, $bom, 3 )) {
			//BOM detected - str is UTF-8
			$str = substr ( $str, 3 );
		}
		return $str;
	}


	/**
	 * getTimeZone
	 */
	public static function getTimezone($project, $overallconfig)
	{
		if($project) {
			return $project->timezone;
		} else {
			return $overallconfig['time_zone'];
		}
	}


	/**
	 * Override for normal grid.state, this as it wasn't possible to add a class to it.
	 *
	 * @param string $filter_state
	 * @param string $published
	 * @param string $unpublished
	 * @param string $archived
	 * @param string $trashed
	 * @return mixed
	 */
	public static function stateOptions($filter_state='*', $published='Published', $unpublished='Unpublished', $archived=NULL, $trashed=NULL )
	{
		$state[] = HTMLHelper::_('select.option','','- '.Text::_('Select State').' -');
		if ($published) {
			$state[] = HTMLHelper::_('select.option','P',Text::_('JPUBLISHED'));
		}
		if ($unpublished) {
			$state[] = HTMLHelper::_('select.option','U',Text::_('JUNPUBLISHED'));
		}
		if ($archived) {
			$state[] = HTMLHelper::_('select.option','A',Text::_('JARCHIVED'));
		}
		if ($trashed) {
			$state[] = HTMLHelper::_('select.option','T',Text::_('JTRASHED'));
		}

		return HTMLHelper::_('select.genericlist',   $state, 'filter_state', 'class="input-medium" size="1" onchange="submitform();"', 'value', 'text', $filter_state);
	}


	/**
	 *	return default name format
	 */
	public static function defaultNameFormat($type = false)
	{
		$app 	= Factory::getApplication();
		$input = $app->input;
		$params = ComponentHelper::getParams('com_joomleague');
		$default_name_format = $params->get('name_format');

		return $default_name_format;
	}


	/**
	 * get Content between characters
	 * @author	Bit Repository
	 * @link	http://www.bitrepository.com/extract-content-between-two-delimiters-with-php.html
	 */
	public static function getContentBetweenDelimiters($content,$delimiter1,$delimiter2)
	{
		$pos = stripos($content,$delimiter1);
		$str = substr($content, $pos);
		$str_two = substr($str, strlen($delimiter1));
		$second_pos = stripos($str_two,$delimiter2);
		$str_three = substr($str_two, 0, $second_pos);
		$content = (int)trim($str_three); // remove whitespaces

		return $content;
	}
	
	public static function createObjectArray($object)
	{
		$result = array();

		if ($object === null)
		{
			return $result;
		}

		foreach ($object as $name => $value)
		{
			$result[$name] = $value;

			if (is_object($value))
			{
				foreach ($value as $subName => $subValue)
				{
					$result[$subName] = $subValue;
				}
			}
		}

		return $result;
	}

	public static function decodeFields($jsonString)
	{
		$object = json_decode($jsonString);

		if (is_object($object))
		{
			foreach ($object as $name => $value)
			{
				if ($subObject = json_decode($value))
				{
					$object->$name = $subObject;
				}
			}
		}

		return $object;
	}
	public static function getActions()
	{
	    $user	= Factory::getUser();
	    $result	= new CMSObject;
	    
	    $assetName = 'com_joomleague';
	    $level = 'component';
	    
	    //$actions = Access::getActionsFromFile('com_joomleague', $level);
	    $actions = Access::getActionsFromFile(
	        JPATH_ADMINISTRATOR . '/components/' . $component . '/access.xml', '/access/section[@name="component"]/'
	        );
	    
	    if ($actions === false)
	    {
	        Log::add(
	            Text::sprintf('JLIB_ERROR_COMPONENTS_ACL_CONFIGURATION_FILE_MISSING_OR_IMPROPERLY_STRUCTURED', $component), Log::ERROR, 'jerror'
	            );
	        
	        return $result;
	    }
	    foreach ($actions as $action)
	    {
	        $result->set($action->name,	$user->authorise($action->name, $assetName));
	    }
	    
	    return $result;
	}
}
