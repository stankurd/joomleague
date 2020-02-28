<?php
/**
 * @copyright	Copyright (C) 2006-2015 joomleague.at. All rights reserved.
 * @license		GNU/GPL,see LICENSE.php
 * Joomla! is free software. This version may have been modified pursuant
 * to the GNU General Public License,and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */

// Check to ensure this file is included in Joomla!
use Joomla\CMS\Factory;

defined('_JEXEC') or die;


require_once JPATH_COMPONENT.'/helpers/ranking.php';
require_once JLG_PATH_SITE.'/models/project.php';

/**
 * Model-Teaminfo
 */
class JoomleagueModelTeamInfo extends JoomleagueModelProject
{
	var $project = null;
	var $projectid = 0;
	var $projectteamid = 0;
	var $teamid = 0;
	var $team = null;
	var $club = null;

	function __construct()
	{
		parent::__construct();

		$app = Factory::getApplication();
		$input = $app->input;
		
		$this->projectid = $input->getInt('p', 0);
		$this->projectteamid = $input->getInt('ptid', 0);
		$this->teamid = $input->getInt('tid', 0);
	}

	/**
	 * get team info
	 * @return object
	 */
	function getTeamByProject()
	{
		if (is_null($this->team))
		{
			$db = Factory::getDbo();
			$query = $db->getQuery(true);
			$query
				->select('t.*')
				->select($db->quoteName('t.name', 'tname'))
				->select($db->quoteName('t.website', 'team_website'))
				->select('pt.*')
				->select($db->quoteName('pt.notes', 'notes'))
				->select($db->quoteName('pt.info', 'info'))
				->select($db->quoteName('pt.id', 'project_team_id'))
				->select($db->quoteName('t.extended', 'teamextended'))
				->select($db->quoteName('t.picture', 'team_picture'))
				->select($db->quoteName('pt.picture', 'projectteam_picture'))
				->select('c.*')
				->select($this->constructSlug($db, 'slug', 't.alias', 't.id'))
				->from($db->quoteName('#__joomleague_team', 't'))
				->join('LEFT', $db->quoteName('#__joomleague_club', 'c') .
					' ON ' . $db->quoteName('c.id') . ' = ' . $db->quoteName('t.club_id'))
				->join('INNER', $db->quoteName('#__joomleague_project_team', 'pt') .
					' ON ' . $db->quoteName('pt.team_id') . ' = ' . $db->quoteName('t.id'))
				->where($db->quoteName('pt.project_id') . ' = ' . (int)$this->projectid);

			if($this->projectteamid > 0) {
				$query->where($db->quoteName('pt.id') . ' = ' .  (int)$this->projectteamid);
			} else {
				$query->where($db->quoteName('t.id') . ' = ' .  (int)$this->teamid);
			}
			$db->setQuery($query);
			$this->team  = $db->loadObject();
		}
		return $this->team;
	}

	/**
	 * get club info
	 * @return object
	 */
	function getClub()
	{
		if (is_null($this->club))
		{
			$team = $this->getTeamByProject();
			if ($team->club_id > 0)
			{
				$db = Factory::getDbo();
				$query = $db->getQuery(true);
				$query
					->select('*')
					->select($this->constructSlug($db, 'slug', 'alias', 'id'))
					->from($db->quoteName('#__joomleague_club'))
					->where($db->quoteName('id') . ' = ' . (int)$team->club_id);
				$db->setQuery($query);
				$this->club  = $db->loadObject();
			}
		}
		return $this->club;
	}

	/**
	 * get history of team in differents projects
	 * @param object config
	 * @return array
	 */
	function getSeasons($config)
	{
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		$query
			->select($db->quoteName('pt.id', 'ptid'))
			->select($db->quoteName('pt.team_id'))
			->select($db->quoteName('pt.picture'))
			->select($db->quoteName('pt.info'))
			->select($db->quoteName('pt.project_id', 'projectid'))
			->select($db->quoteName('p.name', 'projectname'))
			->select($db->quoteName('pt.division_id'))
			->select($db->quoteName('s.name', 'season'))
			->select($db->quoteName('l.name', 'league'))
			->select($db->quoteName('t.extended', 'teamextended'))
			->select($this->constructSlug($db,'project_slug', 'p.alias', 'p.id'))
			->select($this->constructSlug($db, 'team_slug', 't.alias', 't.id'))
			->select($this->constructSlug($db, 'division_slug', 'd.alias', 'd.id'))
			->select($db->quoteName('d.name', 'division_name'))
			->select($db->quoteName('d.shortname', 'division_short_name'))
			->from($db->quoteName('#__joomleague_project_team', 'pt'))
			->join('INNER', $db->quoteName('#__joomleague_team', 't') .
				' ON ' . $db->quoteName('t.id') . ' = ' . $db->quoteName('pt.team_id'))
			->join('LEFT', $db->quoteName('#__joomleague_division', 'd') .
				' ON ' . $db->quoteName('d.id') . ' = ' . $db->quoteName('pt.division_id'))
			->join('INNER', $db->quoteName('#__joomleague_project', 'p') .
				' ON ' . $db->quoteName('p.id') . ' = ' . $db->quoteName('pt.project_id'))
			->join('INNER', $db->quoteName('#__joomleague_season', 's') .
				' ON ' . $db->quoteName('s.id') . ' = ' . $db->quoteName('p.season_id'))
			->join('INNER', $db->quoteName('#__joomleague_league', 'l') .
				' ON ' . $db->quoteName('l.id') . ' = ' . $db->quoteName('p.league_id'))
			->where($db->quoteName('p.published') . ' = 1');

		if($this->projectteamid > 0) {
			$query->where($db->quoteName('pt.id') . ' = ' . (int)$this->projectteamid);
		} else {
			$query->where($db->quoteName('t.id') . ' = ' . (int)$this->teamid);
		}
		$query->order($db->quoteName('s.name') . ($config['ordering_teams_seasons'] == '1' ? ' DESC' : ' ASC'));
	    $db->setQuery($query);
	    $seasons = $db->loadObjectList();

	    foreach ($seasons as $k => $season)
	    {
	    	$ranking = $this->getTeamRanking($season->projectid, $season->division_id);
			if(!empty($ranking)) {
		    	$seasons[$k]->rank       = $ranking['rank'];
		    	$seasons[$k]->leaguename = $this->getLeague($season->projectid);
		    	$seasons[$k]->games      = $ranking['games'];
		    	$seasons[$k]->points     = $ranking['points'];
		    	$seasons[$k]->series     = $ranking['series'];
		    	$seasons[$k]->goals      = $ranking['goals'];
		    	$seasons[$k]->playercnt  = $this->getPlayerCount($season->projectid, $season->ptid);
	    	} else {
	    		$seasons[$k]->rank       = 0;
	    		$seasons[$k]->leaguename = '';
	    		$seasons[$k]->games      = 0;
	    		$seasons[$k]->points     = 0;
	    		$seasons[$k]->series     = 0;
	    		$seasons[$k]->goals      = 0;
	    		$seasons[$k]->playercnt  = 0;
	    	}
		}
    	return $seasons;
	}

	/**
	 * get ranking of current team in a project
	 * @param int projectid
	 * @param int division_id
	 * @return array
	 */
	function getTeamRanking($projectid, $division_id)
	{
		$rank = array();
		$model = JLGModel::getInstance('Project', 'JoomleagueModel');
		$model->setProjectID($projectid);
		$project = $model->getProject();
		$ranking = JLGRanking::getInstance($project);
		$ranking->setProjectId($project->id);
		$this->ranking = $ranking->getRanking(0, $model->getCurrentRound(), $division_id);
		foreach ($this->ranking as $ptid => $value)
		{
			if ($value->getPtid() == $this->projectteamid)
			{
				$rank['rank']   = $value->rank;
				$rank['games']  = $value->cnt_matches;
				$rank['points'] = $value->getPoints();
				$rank['series'] = $value->cnt_won . "/" . $value->cnt_draw . "/" . $value->cnt_lost;
				$rank['goals']  = $value->sum_team1_result . ":" . $value->sum_team2_result;
				break;
			} 
			else if ($value->getTeamId() == $this->teamid)
			{
				$rank['rank']   = $value->rank;
				$rank['games']  = $value->cnt_matches;
				$rank['points'] = $value->getPoints();
				$rank['series'] = $value->cnt_won . "/" . $value->cnt_draw . "/" . $value->cnt_lost;
				$rank['goals']  = $value->sum_team1_result . ":" . $value->sum_team2_result;
				break;
			}
		}
		return $rank;
	}

	/**
	 * gets name of league associated to project
	 * @param int $projectid
	 * @return string
	 */
	function getLeague($projectid)
	{
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		$query
			->select($db->quoteName('l.name', 'league'))
			->from($db->quoteName('#__joomleague_project', 'p'))
			->join('INNER', $db->quoteName('#__joomleague_league', 'l') .
				' ON ' . $db->quoteName('l.id') . ' = ' . $db->quoteName('p.league_id'))
			->where($db->quoteName('p.id') . ' = ' . (int)$projectid);

	    $db->setQuery($query, 0, 1);
    	$league = $db->loadResult();
		return $league;
	}

	/**
	 * Get total number of players assigned to a team
	 * @param int projectid
	 * @param int projectteamid
	 * @return int
	 */
	function getPlayerCount($projectid, $projectteamid)
	{
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		$query
			->select('COUNT(*) AS playercnt')
			->from($db->quoteName('#__joomleague_person', 'ps'))
			->join('INNER', $db->quoteName('#__joomleague_team_player', 'tp') .
				' ON ' . $db->quoteName('tp.person_id') . ' = ' . $db->quoteName('ps.id'))
			->join('INNER', $db->quoteName('#__joomleague_project_team', 'pt') .
				' ON ' . $db->quoteName('pt.id') . ' = ' . $db->quoteName('tp.projectteam_id'))
			->where($db->quoteName('pt.project_id') . ' = ' . (int)$projectid)
			->where($db->quoteName('pt.id') . ' = ' . (int)$projectteamid)
			->where($db->quoteName('tp.published') . ' = 1')
			->where($db->quoteName('ps.published') . ' = 1');

	   $db->setQuery($query);
		$player = $db->loadResult();
		return $player;
	}
	
	
	function getProjectTeamId($teamid)
	{
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		$query
			->select($db->quoteName('id'))
			->from($db->quoteName('#__joomleague_project_team'))
			->where($db->quoteName('team_id') . ' = ' . (int)$teamid)
			->where($db->quoteName('project_id') . ' = ' . (int)$this->projectid);

		$db->setQuery($query);
		$result = $db->loadResult();
		return $result;
	}
	
	/**
	* Method to return a team trainingdata array
	* @param int projectid
	* @return	array
	*/
	function getTrainingData($projectid)
	{
		$projectTeamId = $this->projectteamid > 0 ? $this->projectteamid: $this->getProjectTeamId($this->teamid);
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		$query
			->select('*')
			->from($db->quoteName('#__joomleague_team_trainingdata'))
			->where($db->quoteName('project_id') . ' = ' . (int)$projectid)
			->where($db->quoteName('project_team_id') . ' = ' . (int)$projectTeamId)
			->order($db->quoteName('dayofweek') . ' ASC');

		$db->setQuery($query);
		$trainingData = $db->loadObjectList();
		return $trainingData;
	}
	function getLeagueRankOverviewDetail($seasonsranking) {
	    // Reference global application object
	    $app = Factory::getApplication();
	    // JInput object
	    $input = $app->input;
	    $option = $input->getCmd('option');
	    
	    $leaguesoverviewdetail = array();
	    
	    foreach ($seasonsranking as $season) {
	        $temp = new stdClass();
	        $temp->match = 0;
	        $temp->won = 0;
	        $temp->draw = 0;
	        $temp->loss = 0;
	        $temp->goalsfor = 0;
	        $temp->goalsagain = 0;
	        $leaguesoverviewdetail[$season->league] = $temp;
	    }
	    
	    
	    foreach ($seasonsranking as $season) {
	        $leaguesoverviewdetail[$season->league]->match += $season->games;
	        $teile = explode("/", $season->series);
	        $leaguesoverviewdetail[$season->league]->won += $teile[0];
	        
	        if (array_key_exists('1', $teile)) {
	            $leaguesoverviewdetail[$season->league]->draw += $teile[1];
	        }
	        if (array_key_exists('2', $teile)) {
	            $leaguesoverviewdetail[$season->league]->loss += $teile[2];
	        }
	        $teile = explode(":", $season->goals);
	        $leaguesoverviewdetail[$season->league]->goalsfor += $teile[0];
	        
	        if (array_key_exists('1', $teile)) {
	            $leaguesoverviewdetail[$season->league]->goalsagain += $teile[1];
	        }
	    }
	    
	    
	    return $leaguesoverviewdetail;
	}
	
	function getLeagueRankOverview($seasonsranking) {
	    // Reference global application object
	    $app = Factory::getApplication();
	    // JInput object
	    $input = $app->input;
	    $option = $input->getCmd('option');
	    
	    $leaguesoverview = array();
	    
	    foreach ($seasonsranking as $season) {
	        
	        if (isset($leaguesoverview[$season->league][(int) $season->rank])) {
	            $leaguesoverview[$season->league][(int) $season->rank] += 1;
	        } else {
	            $leaguesoverview[$season->league][(int) $season->rank] = 0;
	        }
	    }
	    
	    ksort($leaguesoverview);
	    
	    foreach ($leaguesoverview as $key => $value) {
	        ksort($leaguesoverview[$key]);
	    }
	    
	    return $leaguesoverview;
	}
	
	function hasEditPermission($task = null,$id = false,$view=false)
	{
		$edit = false;
		
		$user = Factory::getUser();
		if (!$user->get('guest'))
		{
			$userId = $user->get('id');
			$asset = 'com_joomleague.project_team.'.$id;
			
			if ($user->authorise('core.edit', $asset))
			{
				$edit = true;
			}
		}
		
		return $edit;
	}
}
