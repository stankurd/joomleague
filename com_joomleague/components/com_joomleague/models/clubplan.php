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

defined('_JEXEC') or die;

require_once JLG_PATH_SITE.'/models/project.php';

/**
 * ClubPlan model
 */
class JoomleagueModelClubPlan extends JoomleagueModelProject
{
	var $clubid = 0;
	var $project_id = 0;
	var $club = null;
	var $startdate = null;
	var $enddate = null;
	var $awaymatches = null;
	var $homematches = null;
	
	public function __construct()
	{
		parent::__construct();
		$app = Factory::getApplication();
		$jinput = $app->input;
		$this->clubid = $jinput->getInt('cid', 0);
		$this->project_id = $jinput->getInt('p', 0);
		$this->setStartDate($jinput->get('startdate', $this->startdate, 'request', 'string'));
		$this->setEndDate($jinput->get('enddate', $this->enddate, 'request', 'string'));
	}

	/**
	 * getClub
	 */
	function getClub()
	{
		if (is_null($this->club))
		{
			if ($this->clubid > 0)
			{
				$db = Factory::getDbo();
				$query = $db->getQuery(true);
				$query
					->select('*')
					->from($db->quoteName('#__joomleague_club'))
					->where($db->quoteName('id') . ' = ' . (int)$this->clubid);

				$db->setQuery($query);
				$this->club = $db->loadObject();

//				$this->club = $this->getTable('Club','Table');
//				$this->club->load($this->clubid);
			}
		}
		return $this->club;
	}

	/**
	 * @see JoomleagueModelProject::getTeams()
	 */
	function getTeams($division = 0)
	{
		$teams = array();
		if ($this->clubid > 0)
		{
			$db = Factory::getDbo();
			$query = $db->getQuery(true);
			$query->select(array('id','name AS team_name','short_name AS team_shortcut','info AS team_description'));
			$query->from($db->quoteName('#__joomleague_team'));
			$query->where($db->quoteName('club_id') . ' = '. (int)$this->clubid);

			$db->setQuery($query);
			$teams = $db->loadObjectList();
		}
		return $teams;
	}

	/**
	 * getStartDate
	 */
	function getStartDate()
	{
		$config = $this->getTemplateConfig('clubplan');
		if (empty($this->startdate))
		{
			$dayz = $config['days_before'];
			$prevweek = mktime(0, 0, 0, date('m'), date('d') - $dayz, date('y'));
			$this->startdate = date('Y-m-d', $prevweek);
		}
		if($config['use_project_start_date'] == '1')
		{
			$project = $this->getProject();
			$this->startdate = $project->start_date;
		}
		return $this->startdate;
	}

	/**
	 * getEndDate
	 */
	function getEndDate()
	{
		if (empty($this->enddate))
		{
			$config = $this->getTemplateConfig('clubplan');
			$dayz = $config['days_after'];
			//$dayz=6;
			$nextweek = mktime(0, 0, 0, date('m'), date('d') + $dayz, date('y'));
			$this->enddate = date('Y-m-d', $nextweek);
		}
		return $this->enddate;
	}

	/**
	 * setStartDate
	 */
	function setStartDate($date)
	{
		// should be in proper sql format
		if (strtotime($date))
		{
			$this->startdate = strftime('%Y-%m-%d',strtotime($date));
		}
		else
		{
			$this->startdate = null;
		}
	}

	/**
	 * setEndDate
	 */
	function setEndDate($date)
	{
		// should be in proper sql format
		if (strtotime($date))
		{
			$this->enddate = strftime('%Y-%m-%d',strtotime($date));
		}
		else
		{
			$this->enddate = null;
		}
	}

	/**
	 * Get the matches in a date range, where the matches are ordered by date as specified
	 * @param $startdate      Oldest match date to be included in the query
	 * @param $enddate        Newest match date to be included in the query
	 * @param $allHomeOrAway  1: get home matches, 2: get away matches, any other number gets all matches
	 * @param $orderBy        Ordering (ASC or DESC)
	 * @return mixed          List of match objects
	 */
	function getMatches($startdate, $enddate, $allHomeOrAway, $orderBy)
	{
		$db = Factory::getDbo();
		$query = $db->getQuery(true);

		$query
			->select('m.*')
			->select($db->quoteName('m.id','match_id'))
			->select('DATE_FORMAT(' . $db->quoteName('m.time_present') . ', "%H:%i") AS time_present')
			->select($db->quoteName('p.name', 'project_name'))
			->select($db->quoteName('p.timezone'))
			->select($db->quoteName('p.id', 'project_id'))
			->select($db->quoteName('r.id', 'roundid'))
			->select($db->quoteName('r.roundcode'))
			->select($db->quoteName('r.name', 'roundname'))
			->select($db->quoteName('t1.id', 'team1_id'))
			->select($db->quoteName('t2.id', 'team2_id'))
			->select($db->quoteName('t1.name', 'tname1'))
			->select($db->quoteName('t2.name', 'tname2'))
			->select($db->quoteName('t1.short_name', 'tname1_short'))
			->select($db->quoteName('t2.short_name', 'tname2_short'))
			->select($db->quoteName('t1.middle_name', 'tname1_middle'))
			->select($db->quoteName('t2.middle_name', 'tname2_middle'))
			->select($db->quoteName('t1.club_id', 'club1_id'))
			->select($db->quoteName('t2.club_id', 'club2_id'))
			->select($db->quoteName('p.id', 'prid'))
			->select($db->quoteName('l.name', 'l_name'))
			->select($db->quoteName('playground.name', 'pl_name'))
			->select($db->quoteName('c1.country', 'home_country'))
			->select($db->quoteName('c1.logo_small', 'home_logo_small'))
			->select($db->quoteName('c1.logo_middle', 'home_logo_middle'))
			->select($db->quoteName('c1.logo_big', 'home_logo_big'))
			->select($db->quoteName('c2.country', 'away_country'))
			->select($db->quoteName('c2.logo_small', 'away_logo_small'))
			->select($db->quoteName('c2.logo_middle', 'away_logo_middle'))
			->select($db->quoteName('c2.logo_big', 'away_logo_big'))
			->select($db->quoteName('tj1.division_id'))
			->select($db->quoteName('t1.club_id', 't1club_id'))
			->select($db->quoteName('t2.club_id', 't2club_id'))
			->select($db->quoteName('d.name', 'division_name'))
			->select($db->quoteName('d.shortname', 'division_shortname'))
			->select($db->quoteName('d.parent_id', 'parent_division_id'))
			->select($this->constructSlug($db, 'project_slug', 'p.alias', 'p.id'))
			->select($this->constructSlug($db, 'division_slug', 'd.alias', 'd.id'))
			->select($this->constructSlug($db, 'club1_slug', 'c1.alias', 'c1.id'))
			->select($this->constructSlug($db, 'club2_slug', 'c2.alias', 'c2.id'))
			->select($this->constructSlug($db, 'team1_slug', 't1.alias', 't1.id'))
			->select($this->constructSlug($db, 'team2_slug', 't2.alias', 't2.id'))
			->from($db->quoteName('#__joomleague_match', 'm'))
			->join('INNER', $db->quoteName('#__joomleague_project_team', 'tj1') .
				' ON ' . $db->quoteName('tj1.id') . ' = ' . $db->quoteName('m.projectteam1_id'))
			->join('INNER', $db->quoteName('#__joomleague_project_team', 'tj2') .
				' ON ' . $db->quoteName('tj2.id') . ' = ' . $db->quoteName('m.projectteam2_id'))
			->join('INNER', $db->quoteName('#__joomleague_team', 't1') .
				' ON ' . $db->quoteName('t1.id') . ' = ' . $db->quoteName('tj1.team_id'))
			->join('INNER', $db->quoteName('#__joomleague_team', 't2') .
				' ON ' . $db->quoteName('t2.id') . ' = ' . $db->quoteName('tj2.team_id'))
			->join('INNER', $db->quoteName('#__joomleague_project', 'p') .
				' ON ' . $db->quoteName('p.id') . ' = ' . $db->quoteName('tj1.project_id'))
			->join('INNER', $db->quoteName('#__joomleague_league', 'l') .
				' ON ' . $db->quoteName('l.id') . ' = ' . $db->quoteName('p.league_id'))
			->join('INNER', $db->quoteName('#__joomleague_club', 'c1') .
				' ON ' . $db->quoteName('c1.id') . ' = ' . $db->quoteName('t1.club_id'))
			->join('INNER', $db->quoteName('#__joomleague_round', 'r') .
				' ON ' . $db->quoteName('r.id') . ' = ' . $db->quoteName('m.round_id'))
			->join('INNER', $db->quoteName('#__joomleague_club', 'c2') .
				' ON ' . $db->quoteName('c2.id') . ' = ' . $db->quoteName('t2.club_id'))
			->join('LEFT', $db->quoteName('#__joomleague_playground', 'playground') .
				' ON ' . $db->quoteName('playground.id') . ' = ' . $db->quoteName('m.playground_id'))
			->join('LEFT', $db->quoteName('#__joomleague_division', 'd') .
				' ON ' . $db->quoteName('d.id') . ' = ' . $db->quoteName('tj1.division_id'))
			->where($db->quoteName('m.published') . ' = 1')
			->where($db->quoteName('p.published') . ' = 1')
			->where('(' . $db->quoteName('m.match_date') . ' BETWEEN ' . $db->quote($startdate) . ' AND ' . $db->quote($enddate) . ')');
		if ($this->project_id > 0)
		{
			$query->where($db->quoteName('p.id') . ' = ' . (int)$this->project_id);
		}
		if ($this->clubid > 0)
		{
			switch ($allHomeOrAway)
			{
				case 1:  // HOME
					$query->where($db->quoteName('t1.club_id') . ' = ' . (int)$this->clubid);
					break;
				case 2:  // AWAY
					$query->where($db->quoteName('t2.club_id') . ' = ' . (int)$this->clubid);
					break;
				default: // ALL
					$query->where('(' . $db->quoteName('t1.club_id') . ' = ' . (int)$this->clubid .
						' OR ' . $db->quoteName('t2.club_id') . ' = ' . (int)$this->clubid . ')');
					break;
			}
		}
		$query->order($db->quoteName('m.match_date') . ' ' . $orderBy);
		$db->setQuery($query);
		$matches = $db->loadObjectList();
		if ($matches)
		{
			foreach ($matches as $match)
			{
				JoomleagueHelper::convertMatchDateToTimezone($match);
			}
		}
		return $matches;
	}

	/**
	 * getAllMatches
	 */
	function getAllMatches($orderBy = 'ASC')
	{
		$matches = null;
		$teams = $this->getTeams();
		if (!empty($teams))
		{
			$matches = $this->getMatches($this->getStartDate(), $this->getEndDate(), 0, $orderBy);
			$this->allmatches = $matches;
		}
		return $matches;
	}

	/**
	 * getHomeMatches
	 */
	function getHomeMatches($orderBy = 'ASC')
	{
		$matches = null;
		$teams = $this->getTeams();
		if (!empty($teams))
		{
			$matches = $this->getMatches($this->getStartDate(), $this->getEndDate(), 1, $orderBy);
			$this->homematches = $matches;
		}
		return $matches;
	}

	/**
	 * getAwayMatches
	 */
	function getAwayMatches($orderBy = 'ASC')
	{
		$matches = null;
		$teams = $this->getTeams();
		if (!empty($teams))
		{
			$matches = $this->getMatches($this->getStartDate(), $this->getEndDate(), 2, $orderBy);
			$this->awaymatches = $matches;
		}
		return $matches;
	}

	/**
	 * getMatchReferees
	 */
	function getMatchReferees($matchId)
	{
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		$query
			->select($db->quoteName('p.id'))
			->select($db->quoteName('p.firstname'))
			->select($db->quoteName('p.lastname'))
			->select($db->quoteName('mp.project_position_id'))
			->select($this->constructSlug($db, 'person_slug', 'p.alias', 'p.id'))
			->from($db->quoteName('#__joomleague_match_referee', 'mp'))
			->join('LEFT', $db->quoteName('#__joomleague_project_referee', 'pref') .
				' ON ' . $db->quoteName('mp.project_referee_id') . ' = ' . $db->quoteName('pref.id'))
			->join('INNER', $db->quoteName('#__joomleague_person', 'p') .
				' ON ' . $db->quoteName('pref.person_id') . ' = ' . $db->quoteName('p.id'))
			->where($db->quoteName('mp.match_id') . ' = ' . (int)$matchId)
			->where($db->quoteName('p.published') . ' = 1');

		$db->setQuery($query);
		return $db->loadObjectList();
	}
}
