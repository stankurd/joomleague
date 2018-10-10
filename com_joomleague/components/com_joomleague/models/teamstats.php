<?php
/**
 * @copyright	Copyright (C) 2006-2014 joomleague.at. All rights reserved.
 * @license		GNU/GPL,see LICENSE.php
 * Joomla! is free software. This version may have been modified pursuant
 * to the GNU General Public License,and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */

// Check to ensure this file is included in Joomla!
use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;

defined('_JEXEC') or die;

require_once JLG_PATH_SITE.'/models/project.php';

/**
 * Model-Teamstats
 */
class JoomleagueModelTeamStats extends JoomleagueModelProject
{
	var $projectid = 0;
	var $teamid = 0;
	var $highest_home = null;
	var $highest_away = null;
	var $highestdef_home = null;
	var $highestdef_away = null;
	var $highestdraw_home = null;
	var $highestdraw_away = null;
	var $totalshome = null;
	var $totalsaway = null;
	var $matchdaytotals = null;
	var $totalrounds = null;
	var $attendanceranking = null;

	function __construct()
	{
		parent::__construct();

		$app = Factory::getApplication();
		$input = $app->input;

		$this->projectid = $input->getInt("p", 0);
		$this->teamid = $input->getInt("tid", 0);

		//preload the team;
		$this->getTeam();
	}

	function getTeam()
	{
		# it should be checked if any tid is given in the params of the url
		# if (is_null($this->team))
		if (!isset($this->team))
		{
			if ($this->teamid > 0)
			{
				$this->team = $this->getTable('Team', 'Table');
				$this->team->load($this->teamid);
			}
		}
		return $this->team;
	}

	private function getGenericQueryForHighest($db)
	{
	    $db = Factory::getDbo();
		$query = $db->getQuery(true);
		$query
			->select($db->quoteName('m.id', 'matchid'))
			->select($db->quoteName('t1.name', 'hometeam'))
			->select($db->quoteName('t2.name', 'guestteam'))
			->select($db->quoteName('team1_result', 'homegoals'))
			->select($db->quoteName('team2_result', 'guestgoals'))
			->select($db->quoteName('t1.id', 'team1_id'))
			->select($db->quoteName('t2.id', 'team2_id'))
			->from($db->quoteName('#__joomleague_match', 'm'))
			->join('INNER', $db->quoteName('#__joomleague_project_team', 'pt1') .
				' ON ' . $db->quoteName('pt1.id') . ' = ' . $db->quoteName('m.projectteam1_id'))
			->join('INNER', $db->quoteName('#__joomleague_team', 't1') .
				' ON ' . $db->quoteName('t1.id') . ' = ' . $db->quoteName('pt1.team_id'))
			->join('INNER', $db->quoteName('#__joomleague_project_team', 'pt2') .
				' ON ' . $db->quoteName('pt2.id') . ' = ' . $db->quoteName('m.projectteam2_id'))
			->join('INNER', $db->quoteName('#__joomleague_team', 't2') .
				' ON ' . $db->quoteName('t2.id') . ' = ' . $db->quoteName('pt2.team_id'))
			->where($db->quoteName('pt1.project_id') . ' = ' . (int)$this->projectid)
			->where($db->quoteName('published') . ' = 1')
			->where($db->quoteName('alt_decision') . ' = 0')
			->where('(' . $db->quoteName('m.cancel') . ' IS NULL OR ' . $db->quoteName('m.cancel') . ' = 0)');
		return $query;
	}

	function getHighestHome()
	{
		if (is_null($this->highest_home))
		{
			$db = Factory::getDbo();
			$query = $this->getGenericQueryForHighest($db);
			$query
				->where($db->quoteName('t1.id') . ' = ' . (int)$this->team->id)
				->where($db->quoteName('team1_result') . ' > ' . $db->quoteName('team2_result'))
				->order($db->quoteName('team1_result') . ' - ' . $db->quoteName('team2_result') . 'DESC');

            $db->setQuery($query, 0, 1);
            $this->highest_home = $db->loadObject();
        }
        return $this->highest_home;
    }

    function getHighestAway()
    {
    	if (is_null($this->highest_away))
    	{
			$db = Factory::getDbo();
			$query = $this->getGenericQueryForHighest($db);
			$query
				->where($db->quoteName('t2.id') . ' = ' . (int)$this->team->id)
				->where($db->quoteName('team2_result') . ' > ' . $db->quoteName('team1_result'))
				->order($db->quoteName('team2_result') . ' - ' . $db->quoteName('team1_result') . 'DESC');

			$db->setQuery($query, 0, 1);
    		$this->highest_away = $db->loadObject();
    	}
    	return $this->highest_away;
    }

    function getHighestDefHome()
    {
    	if (is_null($this->highestdef_home))
    	{
			$db = Factory::getDbo();
			$query = $this->getGenericQueryForHighest($db);
			$query
				->where($db->quoteName('t1.id') . ' = ' . (int)$this->team->id)
				->where($db->quoteName('team2_result') . ' > ' . $db->quoteName('team1_result'))
				->order($db->quoteName('team2_result') . ' - ' . $db->quoteName('team1_result') . 'DESC');

			$db->setQuery($query, 0, 1);
    		$this->highestdef_home = $db->loadObject();
    	}
    	return $this->highestdef_home;
    }

    function getHighestDefAway()
    {
    	if (is_null($this->highestdef_away))
    	{
			$db = Factory::getDbo();
			$query = $this->getGenericQueryForHighest($db);
			$query
				->where($db->quoteName('t2.id') . ' = ' . (int)$this->team->id)
				->where($db->quoteName('team1_result') . ' > ' . $db->quoteName('team2_result'))
				->order($db->quoteName('team1_result') . ' - ' . $db->quoteName('team2_result') . 'DESC');

			$db->setQuery($query, 0, 1);
    		$this->highestdef_away = $db->loadObject();
    	}
    	return $this->highestdef_away;
    }

    function getHighestDrawAway()
    {
    	if (is_null($this->highestdraw_away))
    	{
			$db = Factory::getDbo();
			$query = $this->getGenericQueryForHighest($db);
			$query
				->where($db->quoteName('t2.id') . ' = ' . (int)$this->team->id)
				->where($db->quoteName('team1_result') . ' = ' . $db->quoteName('team2_result'))
				->order($db->quoteName('team2_result') . 'DESC');

			$db->setQuery($query, 0, 1);
    		$this->highestdraw_away = $db->loadObject();
    	}
    	return $this->highestdraw_away;
    }
    
    function getHighestDrawHome()
    {
    	if (is_null($this->highestdraw_home))
    	{
			$db = Factory::getDbo();
			$query = $this->getGenericQueryForHighest($db);
			$query
				->where($db->quoteName('t1.id') . ' = ' . (int)$this->team->id)
				->where($db->quoteName('team2_result') . ' = ' . $db->quoteName('team1_result'))
				->order($db->quoteName('team1_result') . 'DESC');

			$db->setQuery($query, 0, 1);
    		$this->highestdraw_home = $db->loadObject();
    	}
    	return $this->highestdraw_home;
    }
    
    function getNoGoalsAgainst()
    {
    	if ((!isset($this->nogoals_against)) || is_null($this->nogoals_against))
    	{
			$db = Factory::getDbo();
			$query = $db->getQuery(true);
			$query
				->select('COUNT(' . $db->quoteName('round_id') . ') AS totalzero')
				->select('SUM(' . $db->quoteName('t1.id') . ' = ' . (int)$this->team->id .
					' AND ' . $db->quoteName('team2_result') . ' = 0) AS homezero')
				->select('SUM(' . $db->quoteName('t2.id') . ' = ' . (int)$this->team->id .
					' AND ' . $db->quoteName('team1_result') . ' = 0) AS awayzero')
				->from($db->quoteName('#__joomleague_match', 'm'))
				->join('INNER', $db->quoteName('#__joomleague_project_team', 'pt1') .
					' ON ' . $db->quoteName('pt1.id') . ' = ' . $db->quoteName('m.projectteam1_id'))
				->join('INNER', $db->quoteName('#__joomleague_team', 't1') .
					' ON ' . $db->quoteName('t1.id') . ' = ' . $db->quoteName('pt1.team_id'))
				->join('INNER', $db->quoteName('#__joomleague_project_team', 'pt2') .
					' ON ' . $db->quoteName('pt2.id') . ' = ' . $db->quoteName('m.projectteam2_id'))
				->join('INNER', $db->quoteName('#__joomleague_team', 't2') .
					' ON ' . $db->quoteName('t2.id') . ' = ' . $db->quoteName('pt2.team_id'))
				->where($db->quoteName('pt1.project_id') . ' = ' . (int)$this->projectid)
				->where($db->quoteName('published') . ' = 1')
				->where($db->quoteName('alt_decision') . ' = 0')
				->where('(' . $db->quoteName('m.cancel') . ' IS NULL OR ' . $db->quoteName('m.cancel') . ' = 0)')
				->where('((' . $db->quoteName('t1.id') . ' = ' . (int)$this->team->id .
						' AND ' . $db->quoteName('team2_result') . ' = 0)' .
					' OR (' . $db->quoteName('t2.id') . ' = ' . (int)$this->team->id .
						' AND ' . $db->quoteName('team1_result') . ' = 0))');

    		$db->setQuery($query);
    		$this->nogoals_against = $db->loadObject();
    	}
    	return $this->nogoals_against;
    }

	private function getGenericQueryForSeason($db, $home_result, $away_result, $projectteam)
	{
		$query = $db->getQuery(true);
		$query
			->select('COUNT(' . $db->quoteName('m.id') .  ') AS totalmatches')
			->select('COUNT(' . $db->quoteName($home_result) . ') AS playedmatches')
			->select('IFNULL(SUM(' . $db->quoteName($home_result) . '), 0) AS goalsfor')
			->select('IFNULL(SUM(' . $db->quoteName($away_result) . '), 0) AS goalsagainst')
			->select('IFNULL(SUM(' . $db->quoteName($home_result) . ' + ' . $db->quoteName($away_result) . '), 0) AS totalgoals')
			->select('IFNULL(SUM(IF(' . $db->quoteName($home_result) . ' = ' . $db->quoteName($away_result) . ', 1, 0)), 0) AS totaldraw')
			->select('IFNULL(SUM(IF(' . $db->quoteName($home_result) . ' < ' . $db->quoteName($away_result) . ', 1, 0)), 0) AS totalloss')
			->select('IFNULL(SUM(IF(' . $db->quoteName($home_result) . ' > ' . $db->quoteName($away_result) . ', 1, 0)), 0) AS totalwin')
			->select('COUNT(' . $db->quoteName('crowd') . ') AS attendedmatches')
			->select('SUM(' . $db->quoteName('crowd') . ') AS sumspectators')
			->from($db->quoteName('#__joomleague_match', 'm'))
			->join('INNER', $db->quoteName('#__joomleague_project_team', 'pt') .
				' ON ' . $db->quoteName('pt.id') . ' = ' . $db->quoteName('m.' . $projectteam))
			->where($db->quoteName('pt.project_id') . ' = ' . (int)$this->projectid)
			->where($db->quoteName('published') . ' = 1')
			->where($db->quoteName('pt.team_id') . ' = ' . (int)$this->team->id)
			->where('(' . $db->quoteName('m.cancel') . ' IS NULL OR ' . $db->quoteName('m.cancel') . ' = 0)');

		return $query;
	}

    function getSeasonTotalsHome()
    {
    	if (is_null($this->totalshome))
    	{
			$db = Factory::getDbo();
			$query = $this->getGenericQueryForSeason($db, 'team1_result', 'team2_result', 'projectteam1_id');

    		$db->setQuery($query, 0, 1);
    		$this->totalshome = $db->loadObject();
    	}
    	return $this->totalshome;
    }

    function getSeasonTotalsAway()
    {
    	if (is_null($this->totalsaway))
    	{
			$db = Factory::getDbo();
			$query = $db->getQuery(true);
			$query = $this->getGenericQueryForSeason($db, 'team2_result', 'team1_result', 'projectteam2_id');

    		$this->_db->setQuery($query, 0, 1);
    		$this->totalsaway = $this->_db->loadObject();
    	}
    	return $this->totalsaway;
    }

    /**
     * get data for chart
     * @return  
     */
		function getChartData()
		{
			$db = Factory::getDbo();
			$query = $db->getQuery(true);
			$query
				//->select($db->quoteName('r.id'))
				->select('SUM(CASE WHEN ' . $db->quoteName('pt1.team_id') . ' = ' . (int)$this->teamid .
					' THEN ' . $db->quoteName('m.team1_result') .
					' ELSE ' . $db->quoteName('m.team2_result') . ' END) AS goalsfor')
				->select('SUM(CASE WHEN ' . $db->quoteName('pt1.team_id') . ' = ' . (int)$this->teamid .
					' THEN ' . $db->quoteName('m.team2_result') .
					' ELSE ' . $db->quoteName('m.team1_result') . ' END) AS goalsagainst')
				->select($db->quoteName('r.roundcode'))
				->from($db->quoteName('#__joomleague_round', 'r'))
				->join('INNER', $db->quoteName('#__joomleague_match', 'm') .
					' ON ' . $db->quoteName('m.round_id') . ' = ' . $db->quoteName('r.id'))
				->join('INNER', $db->quoteName('#__joomleague_project_team', 'pt1') .
					' ON ' . $db->quoteName('pt1.id') . ' = ' . $db->quoteName('m.projectteam1_id'))
				->join('INNER', $db->quoteName('#__joomleague_project_team', 'pt2') .
					' ON ' . $db->quoteName('pt2.id') . ' = ' . $db->quoteName('m.projectteam2_id'))
				->where($db->quoteName('r.project_id') . ' = ' . (int)$this->projectid)
				->where('(' . $db->quoteName('pt1.team_id') . ' = ' . (int)$this->teamid .
					' OR ' . $db->quoteName('pt2.team_id') . ' = ' . (int)$this->teamid . ')')
				->where('(' . $db->quoteName('m.cancel') . ' IS NULL OR ' . $db->quoteName('m.cancel') . ' = 0)')
				->where($db->quoteName('team1_result') . ' IS NOT NULL')
				->group($db->quoteName('r.roundcode'));

    		$db->setQuery($query);
    		$this->matchdaytotals = $db->loadObjectList();
    		return $this->matchdaytotals;
    }
    
    function getMatchDayTotals()
    {
    	if (is_null($this->matchdaytotals))
    	{
			$db = Factory::getDbo();
			$query = $db->getQuery(true);
			$query
				//->select($db->quoteName('r.id'))
				->select('COUNT(' . $db->quoteName('m.round_id') . ') AS totalmatchespd')
				->select('COUNT(' . $db->quoteName('m.id') . ') AS playedmatchespd')
				->select('SUM(' . $db->quoteName('m.team1_result') . ') AS homegoalspd')
				->select('SUM(' . $db->quoteName('m.team2_result') . ') AS guestgoalspd')
				->select($db->quoteName('r.roundcode'))
				->from($db->quoteName('#__joomleague_round', 'r'))
				->join('INNER', $db->quoteName('#__joomleague_match', 'm') .
					' ON ' . $db->quoteName('m.round_id') . ' = ' . $db->quoteName('r.id'))
				->join('INNER', $db->quoteName('#__joomleague_project_team', 'pt1') .
					' ON ' . $db->quoteName('pt1.id') . ' = ' . $db->quoteName('m.projectteam1_id'))
				->join('INNER', $db->quoteName('#__joomleague_project_team', 'pt2') .
					' ON ' . $db->quoteName('pt2.id') . ' = ' . $db->quoteName('m.projectteam2_id'))
				->where($db->quoteName('r.project_id') . ' = ' . (int)$this->projectid)
				->where('(' . $db->quoteName('pt1.team_id') . ' = ' . (int)$this->teamid .
					' OR ' . $db->quoteName('pt2.team_id') . ' =' . (int)$this->teamid . ')')
				->where('(' . $db->quoteName('m.cancel') . ' IS NULL OR ' . $db->quoteName('m.cancel') . ' = 0)')
				->group($db->quoteName('r.roundcode'));

    		$db->setQuery($query);
    		$this->matchdaytotals = $db->loadObjectList();
    	}
    	return $this->matchdaytotals;
    }

    function getTotalRounds()
    {
        if (is_null($this->totalrounds))
        {
			$db = Factory::getDbo();
			$query = $db->getQuery(true);
			$query
				->select('COUNT(' . $db->quoteName('id') . ')')
				->from($db->quoteName('#__joomleague_round'))
				->where($db->quoteName('project_id') . ' = ' . (int)$this->projectid);

            $db->setQuery($query);
            $this->totalrounds = $db->loadResult();
        }
        return $this->totalrounds;
    }

    /**
     * return games attendance
     * @return unknown_type
     */
    function _getAttendance()
    {
    	if (is_null($this->attendanceranking))
    	{
			$db = Factory::getDbo();
			$query = $db->getQuery(true);
			$query
				->select($db->quoteName('m.crowd'))
				->from($db->quoteName('#__joomleague_match', 'm'))
				->join('INNER', $db->quoteName('#__joomleague_project_team', 'pt1') .
					' ON ' . $db->quoteName('pt1.id') . ' = ' . $db->quoteName('m.projectteam1_id'))
				->join('INNER', $db->quoteName('#__joomleague_team', 't1') .
					' ON ' . $db->quoteName('t1.id') . ' = ' . $db->quoteName('pt1.team_id'))
				->where($db->quoteName('pt1.team_id') . ' = ' . (int)$this->teamid)
				->where($db->quoteName('m.crowd') . ' > 0')
				->where($db->quoteName('m.published') . ' = 1');

    		$db->setQuery($query);
    		$this->attendanceranking = $db->loadColumn();
    	}
    	return $this->attendanceranking;
    }

	function getBestAttendance()
	{
		$attendance = $this->_getAttendance();
		return (count($attendance) > 0) ? max($attendance) : 0;
	}

	function getWorstAttendance()
	{
		$attendance = $this->_getAttendance();
		return (count($attendance) > 0) ? min($attendance) : 0;
	}

	function getTotalAttendance()
	{
		$attendance = $this->_getAttendance();
		return (count($attendance) > 0) ? array_sum($attendance) : 0;
	}
	
	function getAverageAttendance()
	{
		$attendance = $this->_getAttendance();
		return (count($attendance) > 0) ? round(array_sum($attendance) / count($attendance), 0) : 0;
	}

	function getChartURL()
	{
		$url = JoomleagueHelperRoute::getTeamStatsChartDataRoute($this->projectid, $this->teamid);
		$url = str_replace('&', '%26', $url);
		return $url;
	}

	function getLogo()
	{
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		$query
			->select($db->quoteName('logo_big'))
			->from($db->quoteName('#__joomleague_club', 'c'))
			->join('LEFT', $db->quoteName('#__joomleague_team', 't') .
				' ON ' . $db->quoteName('t.club_id') . ' = ' . $db->quoteName('c.id'))
			->where($db->quoteName('t.id') . ' = ' . (int)$this->teamid);

    	$db->setQuery($query);
    	$logo = Uri::root() . $db->loadResult();

		return $logo;
	}

	function getResults()
	{
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		$query
			->select($db->quoteName('m.id'))
			->select($db->quoteName('m.projectteam1_id'))
			->select($db->quoteName('m.projectteam2_id'))
			->select($db->quoteName('pt1.team_id', 'team1_id'))
			->select($db->quoteName('pt2.team_id', 'team2_id'))
			->select($db->quoteName('m.team1_result'))
			->select($db->quoteName('m.team2_result'))
			->select($db->quoteName('m.alt_decision'))
			->select($db->quoteName('m.team1_result_decision'))
			->select($db->quoteName('m.team2_result_decision'))
			->from($db->quoteName('#__joomleague_match', 'm'))
			->join('INNER', $db->quoteName('#__joomleague_project_team', 'pt1') .
				' ON ' . $db->quoteName('pt1.id') . ' = ' . $db->quoteName('m.projectteam1_id'))
			->join('INNER', $db->quoteName('#__joomleague_project_team', 'pt2') .
				' ON ' . $db->quoteName('pt2.id') . ' = ' . $db->quoteName('m.projectteam2_id'))
			->where($db->quoteName('m.published') . ' = 1')
			->where($db->quoteName('pt1.project_id') . ' = ' . (int)$this->projectid)
			->where('(' . $db->quoteName('pt1.team_id') . ' = ' . (int)$this->teamid .
				' OR ' . $db->quoteName('pt2.team_id') . ' = ' . (int)$this->teamid . ')')
			->where('(' . $db->quoteName('m.team1_result') . ' IS NOT NULL OR ' . $db->quoteName('m.alt_decision') . ' > 0)')
			->where('(' . $db->quoteName('m.cancel') . ' IS NULL OR ' . $db->quoteName('m.cancel') . ' = 0)');

		$db->setQuery($query);
		$matches = $db->loadObjectList();
		
		$results = array(	'win' => array(), 'tie' => array(), 'loss' => array(), 'forfeit' => array(),
							'home_wins' => 0, 'home_draws' => 0, 'home_losses' => 0, 
							'away_wins' => 0, 'away_draws' => 0, 'away_losses' => 0,);
		foreach ($matches as $match)
		{
			if (!$match->alt_decision)
			{
				if ($match->team1_id == $this->teamid)
				{
					// We are the home team
					if ($match->team1_result > $match->team2_result)
					{
						$results['win'][] = $match;
						$results['home_wins']++;
					}
					else if ($match->team1_result < $match->team2_result)
					{
						$results['loss'][] = $match;
						$results['home_losses']++;
					}
					else
					{
						$results['tie'][] = $match;
						$results['home_draws']++;
					}
				}
				else
				{
					// We are the away team
					if ($match->team1_result > $match->team2_result)
					{
						$results['loss'][] = $match;
						$results['away_losses']++;
					}
					else if ($match->team1_result < $match->team2_result)
					{
						$results['win'][] = $match;
						$results['away_wins']++;
					}
					else
					{
						$results['tie'][] = $match;
						$results['away_draws']++;
					}
				}
			}
			else
			{
				if ($match->team1_id == $this->teamid)
				{
					// We are the home team
					if (empty($match->team1_result_decision)) {
						$results['forfeit'][] = $match;
					}
					else if (empty($match->team2_result_decision)) {
						$results['win'][] = $match;
					}
					else {
						if ($match->team1_result_decision > $match->team2_result_decision) {
							$results['win'][] = $match;
							$results['home_wins']++;
						}
						else if ($match->team1_result_decision < $match->team2_result_decision) {
							$results['loss'][] = $match;
							$results['home_losses']++;
						}
						else {
							$results['tie'][] = $match;
							$results['home_draws']++;
						}
					}
				}
				else
				{
					// We are the away team
					if (empty($match->team2_result_decision)) {
						$results['forfeit'][] = $match;
					}
					else if (empty($match->team1_result_decision)) {
						$results['win'][] = $match;
					}
					else {
						if ($match->team1_result_decision > $match->team2_result_decision) {
							$results['loss'][] = $match;
							$results['away_losses']++;
						}
						else if ($match->team1_result_decision < $match->team2_result_decision) {
							$results['win'][] = $match;
							$results['away_wins']++;
						}
						else {
							$results['tie'][] = $match;
							$results['away_draws']++;
						}
					}
				}
			}
		}
		
		return $results;
	}
	
	function getStats()
	{
		$stats = $this->getProjectStats();
		
		// those are per positions, group them so that we have team globlas stats
		
		$teamstats = array();
		foreach ($stats as $pos => $pos_stats)
		{
			foreach ($pos_stats as $k => $stat) 
			{
				if ($stat->getParam('show_in_teamstats', 1))
				{
					if (!isset($teamstats[$k])) 
					{
						$teamstats[$k] = $stat;
						$teamstats[$k]->value = $stat->getRosterTotalStats($this->teamid, $this->projectid);
					}
				}
			}
		}
		
		return $teamstats;
	}
}
