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
use Joomla\CMS\HTML\HTMLHelper;

defined('_JEXEC') or die;

require_once 'person.php';

/**
 * Model-Referee
 */
class JoomleagueModelReferee extends JoomleagueModelPerson
{
	var $projectReferee = null;
	var $refereeCareer = null;
	var $presenceStats = null;
	var $refereeGames = null;

	function getProjectReferee()
	{
		if (is_null($this->projectReferee))
		{
			$db = Factory::getDbo();
			$query = $db->getQuery(true);
			$query
				->select($db->quoteName('pr.id'))
				->select($db->quoteName('pr.notes'))
				->select($db->quoteName('pos.name', 'position_name'))
				->select($db->quoteName('pr.picture'))
				->from($db->quoteName('#__joomleague_project_referee', 'pr'))
				->join('INNER', $db->quoteName('#__joomleague_person', 'p') .
					' ON ' . $db->quoteName('p.id') . ' = ' . $db->quoteName('pr.person_id'))
				->join('LEFT', $db->quoteName('#__joomleague_project_position', 'ppos') .
					' ON ' . $db->quoteName('ppos.id') . ' = ' . $db->quoteName('pr.project_position_id'))
				->join('LEFT', $db->quoteName('#__joomleague_position', 'pos') .
					' ON ' . $db->quoteName('pos.id') . ' = ' . $db->quoteName('ppos.position_id'))
				->where($db->quoteName('pr.project_id') . ' = ' . (int)$this->projectid)
				->where($db->quoteName('p.published') . ' = 1')
				->where($db->quoteName('pr.person_id') . ' = ' . (int)$this->personid);

			$db->setQuery($query);
			$this->projectReferee = $db->loadObject();
		}
		return $this->projectReferee;
	}

	/**
	 * get person history across all projects,with team,season,position,... info
	 *
	 * @param int $person_id,linked to player_id from Person object
	 * @param int $order ordering for season and league,default is ASC ordering
	 * @param string $filter e.g. "s.name=2007/2008",default empty string
	 * @return array of objects
	 */
	function getRefereeCareer($order = 'ASC')
	{
		if (empty($this->refereeCareer))
		{
			$db = Factory::getDbo();
			$query = $db->getQuery(true);
			$query
				->select($db->quoteName('per.id', 'pid'))
				->select($db->quoteName('pr.person_id'))
				->select($db->quoteName('pr.project_id'))
				->select($db->quoteName('pr.id', 'projectreferee_id'))
				->select($db->quoteName('pos.name', 'position_name'))
				->select($db->quoteName('per.firstname'))
				->select($db->quoteName('per.lastname'))
				->select($db->quoteName('p.name', 'project_name'))
				->select($db->quoteName('s.name', 'season_name'))
				->select($this->constructSlug($db, 'person_slug', 'per.alias', 'per.id'))
				->select($this->constructSlug($db, 'project_slug', 'p.alias', 'p.id'))
				->from($db->quoteName('#__joomleague_person', 'per'))
				->join('INNER', $db->quoteName('#__joomleague_project_referee', 'pr') .
					' ON ' . $db->quoteName('pr.person_id') . ' = ' . $db->quoteName('per.id'))
				->join('INNER', $db->quoteName('#__joomleague_project', 'p') .
					' ON ' . $db->quoteName('p.id') . ' = ' . $db->quoteName('pr.project_id'))
				->join('INNER', $db->quoteName('#__joomleague_season', 's') .
					' ON ' . $db->quoteName('s.id') . ' = ' . $db->quoteName('p.season_id'))
				->join('INNER', $db->quoteName('#__joomleague_league', 'l') .
					' ON ' . $db->quoteName('l.id') . ' = ' . $db->quoteName('p.league_id'))
				->join('LEFT', $db->quoteName('#__joomleague_project_position', 'ppos') .
					' ON ' . $db->quoteName('ppos.id') . ' = ' . $db->quoteName('pr.project_position_id'))
				->join('LEFT', $db->quoteName('#__joomleague_position', 'pos') .
					' ON ' . $db->quoteName('pos.id') . ' = ' . $db->quoteName('ppos.position_id'))
				->where($db->quoteName('per.id') . ' = ' . (int)$this->personid)
				->where($db->quoteName('per.published') . ' = 1')
				->where($db->quoteName('p.sports_type_id') . ' = ' . (int)$this->getSportsType())
				->order($db->quoteName('s.ordering') . ' ' . $order)
				->order($db->quoteName('l.ordering') . 'ASC')
				->order($db->quoteName('p.name') . 'ASC');

			$db->setQuery($query);
			$this->refereeCareer = $db->loadObjectList();

			if (!empty($this->refereeCareer))
			{
				foreach ($this->refereeCareer as $job)
				{
					$link = JoomleagueHelperRoute::getRefereeRoute($job->project_slug, $this->person->slug);
					$job->project_link = HTMLHelper::link($link, $job->project_name);
				}
			}
		}
		return $this->refereeCareer;
	}

	function getPresenceStats($project_id, $person_id)
	{
		if (empty($this->presenceStats))
		{
			$db = Factory::getDbo();
			$query = $db->getQuery(true);
			$query
				->select('COUNT(' . $db->quoteName('mr.id') . ') AS present')
				->from($db->quoteName('#__joomleague_match_referee', 'mr'))
				->join('INNER', $db->quoteName('#__joomleague_match', 'm') .
					' ON ' . $db->quoteName('m.id') . ' = ' . $db->quoteName('mr.match_id'))
				->join('INNER', $db->quoteName('#__joomleague_project_referee', 'pr') .
					' ON ' . $db->quoteName('pr.id') . ' = ' . $db->quoteName('mr.project_referee_id'))
				->where($db->quoteName('pr.person_id') . ' = ' . (int)$person_id)
				->where($db->quoteName('pr.project_id') . ' = ' . (int)$project_id);

			$db->setQuery($query, 0, 1);
			$this->presenceStats = $db->loadResult();
		}
		return $this->presenceStats;
	}

	function getGames()
	{
		if (empty($this->refereeGames))
		{
			$db = Factory::getDbo();
			$query = $db->getQuery(true);
			$query
				->select('m.*')
				->select($db->quoteName('t1.id', 'team1'))
				->select($db->quoteName('t2.id', 'team2'))
				->select($db->quoteName('r.roundcode'))
				->select($db->quoteName('r.project_id'))
				->select($db->quoteName('p.timezone'))
				->from($db->quoteName('#__joomleague_match', 'm'))
				->join('INNER', $db->quoteName('#__joomleague_match_referee', 'mr') .
					' ON ' . $db->quoteName('mr.match_id') . ' = ' . $db->quoteName('m.id'))
				->join('INNER', $db->quoteName('#__joomleague_project_referee', 'pr') .
					' ON ' . $db->quoteName('pr.id') . ' = ' . $db->quoteName('mr.project_referee_id'))
				->join('INNER', $db->quoteName('#__joomleague_round', 'r') .
					' ON ' . $db->quoteName('r.id') . ' = ' . $db->quoteName('m.round_id'))
				->join('INNER', $db->quoteName('#__joomleague_project_team', 'pt1') .
					' ON ' . $db->quoteName('pt1.id') . ' = ' . $db->quoteName('m.projectteam1_id'))
				->join('INNER', $db->quoteName('#__joomleague_team', 't1') .
					' ON ' . $db->quoteName('t1.id') . ' = ' . $db->quoteName('pt1.team_id'))
				->join('INNER', $db->quoteName('#__joomleague_project_team', 'pt2') .
					' ON ' . $db->quoteName('pt2.id') . ' = ' . $db->quoteName('m.projectteam2_id'))
				->join('INNER', $db->quoteName('#__joomleague_team', 't2') .
					' ON ' . $db->quoteName('t2.id') . ' = ' . $db->quoteName('pt2.team_id'))
				->join('INNER', $db->quoteName('#__joomleague_project', 'p') .
					' ON ' . $db->quoteName('p.id') . ' = ' . $db->quoteName('r.project_id'))
				->where($db->quoteName('pr.person_id') . ' = ' . (int)$this->personid)
				->where($db->quoteName('r.project_id') . ' = ' . (int)$this->projectid)
				->where($db->quoteName('m.published') . ' = 1')
				->order($db->quoteName('m.match_date'));

			$db->setQuery($query);
			$this->refereeGames = $db->loadObjectList();
			if ($this->refereeGames)
			{
				foreach ($this->refereeGames as $game)
				{
					JoomleagueHelper::convertMatchDateToTimezone($game);
				}
			}
		}
		return $this->refereeGames;
	}
}
