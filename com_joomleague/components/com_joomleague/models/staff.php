 <?php
/*
 * @package         Joomleague
 * @subpackage		Module-Matches
 * @lastedit		30.08.2016
 * @testenvironment	Joomla 3.6 & PHP 5.6
 *
 * @copyright	Copyright (C) 2006-2016 joomleague.at. All rights reserved.
 * @link		http://www.joomleague.at 
 * @license		GNU/GPL,see LICENSE.php
 * Joomla! is free software. This version may have been modified pursuant
 * to the GNU General Public License,and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;

defined('_JEXEC') or die;

require_once 'person.php';

/**
 * Model-Staff
 */
class JoomleagueModelStaff extends JoomleagueModelPerson
{
	var $teamStaff = null;
	var $presenceStats = null;
	var $staffStatTypes = null;
	var $staffStats = null;
	var $staffCareerStats = null;

	/**
	 * return the injury,suspension,away data from a staff member
	 *
	 * @param int $round_id   ID of the round
	 * @param int $person_id  ID of the person
	 * @return array
	 * @access public
	 *
	 */
	function getTeamStaffByRound($round_id=0, $person_id=0)
	{
		if (empty($this->teamStaff))
		{
			// Get a db connection.
			$db = Factory::getDbo();
 			// Create a new query object.
			$query = $db->getQuery(true);
			// Define Query
			$query
				->select('ts.*')
				->select($db->quoteName('pt.team_id'))
				->select($db->quoteName('pt.id', 'project_team_id'))
				->select($db->quoteName('ppos.id', 'pposid'))
				->select($db->quoteName('ppos.position_id'))
				->select($db->quoteName('pos.id', 'position_id'))
				->select($db->quoteName('pos.name', 'position_name'))
				->select($db->quoteName('rinjuryfrom.round_date_first', 'injury_date'))
				->select($db->quoteName('rinjuryto.round_date_last', 'injury_end'))
				->select($db->quoteName('rinjuryfrom.name', 'injury_from'))
				->select($db->quoteName('rinjuryto.name', 'injury_to'))
				->select($db->quoteName('rsuspfrom.round_date_first', 'suspension_date'))
				->select($db->quoteName('rsuspto.round_date_last', 'suspension_end'))
				->select($db->quoteName('rsuspfrom.name', 'suspension_from'))
				->select($db->quoteName('rsuspto.name', 'suspension_to'))
				->select($db->quoteName('rawayfrom.round_date_first', 'away_date'))
				->select($db->quoteName('rawayto.round_date_last', 'away_end'))
				->select($db->quoteName('rawayfrom.name', 'away_from'))
				->select($db->quoteName('rawayto.name', 'away_to'))
				->from($db->quoteName('#__joomleague_team_staff', 'ts'))
				->join('INNER', $db->quoteName('#__joomleague_person', 'pr') .
					' ON ' . $db->quoteName('pr.id') . ' = ' . $db->quoteName('ts.person_id'))
				->join('INNER', $db->quoteName('#__joomleague_project_team', 'pt') .
					' ON ' . $db->quoteName('pt.id') . ' = ' . $db->quoteName('ts.projectteam_id'))
				->join('INNER', $db->quoteName('#__joomleague_round', 'r') .
					' ON ' . $db->quoteName('r.project_id') . ' = ' . $db->quoteName('pt.project_id'))
				->join('INNER', $db->quoteName('#__joomleague_project_position', 'ppos') .
					' ON ' . $db->quoteName('ppos.id') . ' = ' . $db->quoteName('ts.project_position_id'))
				->join('INNER', $db->quoteName('#__joomleague_position', 'pos') .
					' ON ' . $db->quoteName('pos.id') . ' = ' . $db->quoteName('ppos.position_id'))
				->join('LEFT', $db->quoteName('#__joomleague_round', 'rinjuryfrom') .
					' ON ' . $db->quoteName('rinjuryfrom.id') . ' = ' . $db->quoteName('ts.injury_date'))
				->join('LEFT', $db->quoteName('#__joomleague_round', 'rinjuryto') .
					' ON ' . $db->quoteName('rinjuryto.id') . ' = ' . $db->quoteName('ts.injury_end'))
				->join('LEFT', $db->quoteName('#__joomleague_round', 'rsuspfrom') .
					' ON ' . $db->quoteName('rsuspfrom.id') . ' = ' . $db->quoteName('ts.suspension_date'))
				->join('LEFT', $db->quoteName('#__joomleague_round', 'rsuspto') .
					' ON ' . $db->quoteName('rsuspto.id') . ' = ' . $db->quoteName('ts.suspension_end'))
				->join('LEFT', $db->quoteName('#__joomleague_round', 'rawayfrom') .
					' ON ' . $db->quoteName('rawayfrom.id') . ' = ' . $db->quoteName('ts.away_date'))
				->join('LEFT', $db->quoteName('#__joomleague_round', 'rawayto') .
					' ON ' . $db->quoteName('rawayto.id') . ' = ' . $db->quoteName('ts.away_end'))
				->where($db->quoteName('r.id') . ' = ' . $db->quote((int)$round_id))
				->where($db->quoteName('pr.id') . ' = ' . $db->quote((int)$person_id))
				->where($db->quoteName('pr.published') . ' = 1')
				->where($db->quoteName('ts.published') . ' = 1')
				->order($db->quoteName('ts.id') . ' DESC');
			// Load Query
			$db->setQuery($query);
			// Load Result Row
			$this->teamStaff = $db->loadObject();

		}

		return $this->teamStaff;
	}
	
	/**
	 * get person history across all projects,with team,season,position,... info
	 *
	 * @param int $person_id,linked to player_id from Person object
	 * @param int $order ordering for season and league,default is ASC ordering
	 * @param string $filter e.g. "s.name=2007/2008",default empty string
	 * @return array of objects
	 */
	function getStaffCareer($order = 'ASC')
	{
		if (empty($this->refereeCareer))
		{
			$db = Factory::getDbo();
			$query = $db->getQuery(true);
			$query
				->select($db->quoteName('per.id', 'pid'))
				->select($db->quoteName('ts.person_id'))
				->select($db->quoteName('pt.project_id'))
				->select($db->quoteName('ts.id', 'teamstaff_id'))
				->select($db->quoteName('pos.name', 'position_name'))
				->select($db->quoteName('per.firstname'))
				->select($db->quoteName('per.lastname'))
				->select($db->quoteName('p.name', 'project_name'))
				->select($db->quoteName('s.name', 'season_name'))
				->select($this->constructSlug($db, 'person_slug', 'per.alias', 'per.id'))
				->select($this->constructSlug($db, 'project_slug', 'p.alias', 'p.id'))
				->from($db->quoteName('#__joomleague_person', 'per'))
				->join('INNER', $db->quoteName('#__joomleague_team_staff', 'ts') .
					' ON ' . $db->quoteName('ts.person_id') . ' = ' . $db->quoteName('per.id'))
				->join('INNER', $db->quoteName('#__joomleague_project_team', 'pt') .
					' ON ' . $db->quoteName('pt.id') . ' = ' . $db->quoteName('ts.projectteam_id'))
					->join('INNER', $db->quoteName('#__joomleague_project', 'p') .
					' ON ' . $db->quoteName('p.id') . ' = ' . $db->quoteName('pt.project_id'))
				->join('INNER', $db->quoteName('#__joomleague_season', 's') .
					' ON ' . $db->quoteName('s.id') . ' = ' . $db->quoteName('p.season_id'))
				->join('INNER', $db->quoteName('#__joomleague_league', 'l') .
					' ON ' . $db->quoteName('l.id') . ' = ' . $db->quoteName('p.league_id'))
				->join('LEFT', $db->quoteName('#__joomleague_project_position', 'ppos') .
					' ON ' . $db->quoteName('ppos.id') . ' = ' . $db->quoteName('ts.project_position_id'))
				->join('LEFT', $db->quoteName('#__joomleague_position', 'pos') .
					' ON ' . $db->quoteName('pos.id') . ' = ' . $db->quoteName('ppos.position_id'))
				->where($db->quoteName('ts.person_id') . ' = ' . (int)$this->personid)
				->where($db->quoteName('ts.published') . ' = 1')
				->where($db->quoteName('per.published') . ' = 1')
				->where($db->quoteName('p.sports_type_id') . ' = ' . (int)$this->getSportsType())
				->order($db->quoteName('s.ordering') . ' ' . $order)
				->order($db->quoteName('l.ordering') . ' ASC')
				->order($db->quoteName('p.name') . ' ASC');

			$db->setQuery($query);
			$this->staffCareer = $db->loadObjectList();

			if (!empty($this->staffCareer))
			{
				foreach ($this->staffCareer as $job)
				{
					$link = JoomleagueHelperRoute::getRefereeRoute($job->project_slug, $this->person->slug);
					$job->project_link = HTMLHelper::link($link, $job->project_name);
				}
			}
		}
		return $this->staffCareer;
	}
	

	function getPresenceStats($project_id, $projectteam_id, $person_id)
	{
		if (empty($this->presenceStats))
		{
			$db = Factory::getDbo();
			$query = $db->getQuery(true);
			$query
				->select('COUNT(' . $db->quoteName('ms.id') . ') AS present')
				->from($db->quoteName('#__joomleague_match_staff', 'ms'))
				->join('INNER', $db->quoteName('#__joomleague_match', 'm') .
					' ON ' . $db->quoteName('m.id') . ' = ' . $db->quoteName('ms.match_id'))
				->join('INNER', $db->quoteName('#__joomleague_team_staff', 'ts') .
					' ON ' . $db->quoteName('ts.id') . ' = ' . $db->quoteName('ms.team_staff_id'))
				->join('INNER', $db->quoteName('#__joomleague_project_team', 'pt') .
					' ON ' . $db->quoteName('pt.id') . ' = ' . $db->quoteName('ts.projectteam_id'))
				->where($db->quoteName('ts.person_id') . ' = ' . (int)$person_id)
				->where($db->quoteName('ts.projectteam_id') . ' = ' .(int)$projectteam_id)
				->where($db->quoteName('pt.project_id') . ' = ' . (int)$project_id);

			$db->setQuery($query, 0, 1);
			$this->presenceStats = $db->loadResult();
		}
		return $this->presenceStats;
	}

	/**
	 * get stats for the player position
	 * @return array
	 */
	function getStaffStatTypes($current_round = 0)
	{
		if (empty($this->staffStatTypes))
		{
			$staff = $this->getTeamStaffByRound($current_round);
			if (!isset($staff->position_id))
			{
				$staff->position_id = 0;
			}
			$this->staffStatTypes = $this->getProjectStats(0, $staff->position_id);
		}
		return $this->staffStatTypes;
	}

	/**
	 * get player stats
	 * @return array
	 */
	function getStaffStats($current_round = 0)
	{
		if (empty($this->staffStats))
		{
			$statTypes = $this->getStaffStatTypes($current_round);
			$staffCareer = $this->getStaffCareer();
			$this->staffStats = array();
			if (count($staffCareer) > 0 && count($statTypes) > 0)
			{
				foreach ($staffCareer as $player)
				{
					foreach ($statTypes as $stat)
					{
						if (!isset($stat) && $stat->position_id != null)
						{
							$this->staffStats[$stat->id][$player->project_id] =
								$stat->getStaffStats($player->person_id, $player->team_id, $player->project_id);
						}
					}
				}
			}
		}
		return $this->staffStats;
	}

	function getStaffCareerStats($current_round = 0)
	{
		if (empty($this->staffCareerStats))
		{
			$staff = $this->getTeamStaffByRound($current_round);
			$stats = $this->getProjectStats(0, $staff->position_id);
			$this->staffCareerStats = array();
			if (count($stats) > 0)
			{
				foreach ($stats as $stat)
				{
					if (!isset($stat))
					{
						$this->staffCareerStats[$stat->id] = $stat->getHistoryStaffStats($staff->person_id);
					}
				}
			}
		}
		return $this->staffCareerStats;
	}
}
