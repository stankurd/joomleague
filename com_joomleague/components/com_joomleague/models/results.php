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
use Joomla\CMS\HTML\HTMLHelper;

defined('_JEXEC') or die;

jimport('joomla.html.pane');
HTMLHelper::_('bootstrap.tooltip');

require_once JLG_PATH_SITE.'/models/project.php';

/**
 * Model-Results
 */
class JoomleagueModelResults extends JoomleagueModelProject
{
	var $projectid = 0;
	var $divisionid = 0;
	var $roundid = 0;
	var $rounds = array(0);
	var $mode = 0;
	var $order = 0;
	var $config = 0;
	var $project = null;
	var $matches = null;

	function __construct()
	{
		parent::__construct();
		
		$app = Factory::getApplication();
		$input = $app->input;
		
		$this->divisionid = $input->getInt('division', 0);
		$this->mode = $input->getInt('mode', 0);
		$this->order = $input->getInt('order', 0);
		$round = $input->getInt('r', 0);
		$this->roundid = $round > 0 ? $round : $this->getCurrentRound();
		$this->config = $this->getTemplateConfig('results');
	}

	function getDivisionID()
	{
		return $this->divisionid;
	}

	// TODO: this function is inherited from the project model, but its signature is different
	// (which is not allowed in PHP (gives warning)). Look into how this can be solved best.
	function getDivision($id = 0)
	{
		$division = null;
		if ($this->divisionid > 0)
		{
			$division = $this->getTable('Division','Table');
			$division->load($this->divisionid);
		}

		return $division;
	}

	/**
	 * get games
	 * @return array
	 */
	function getMatches()
	{
		if (is_null($this->matches))
		{
			$this->matches = $this->getResultsRows($this->roundid, $this->divisionid, $this->config);
			if (count($this->matches) > 0)
			{
				$allowed = $this->isAllowed();
				$user = Factory::getUser();
				foreach ($this->matches as $k => $match)
				{
					JoomleagueHelper::convertMatchDateToTimezone($match);
					if ($match->checked_out == 0 || $match->checked_out == $user->id)
					{
						if ($allowed || $this->isMatchAdmin($match->id))
						{
							$this->matches[$k]->allowed = true;
						}
					}
				}
			}
		}
		return $this->matches;
	}

	/**
	 * return array of games
	 * @param int round id,0 for current round
	 * @param int division id (0 for project)
	 * @return array
	 */
	function getResultsRows($round, $division)
	{
		$project = $this->getProject();
		if (!$round)
		{
			$round = $this->getCurrentRound();
		}
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		$query
			->select('m.*')
			->select($db->quoteName('p.timezone'))
			->select('DATE_FORMAT(' . $db->quoteName('m.time_present') . ', "%H:%i") AS time_present')
			->select($db->quoteName('playground.name', 'playground_name'))
			->select($db->quoteName('playground.short_name', 'playground_short_name'))
			->select($db->quoteName('pt1.project_id'))
			->select($db->quoteName('d1.name', 'divhome'))
			->select($db->quoteName('d2.name', 'divaway'))
			->select('CASE WHEN CHAR_LENGTH(' . $db->quoteName('t1.alias') . ')' .
						' AND CHAR_LENGTH(' . $db->quoteName('t2.alias') . ')' .
				' THEN CONCAT_WS(\':\',' . $db->quoteName('m.id') . ', CONCAT_WS("_",' . $db->quoteName('t1.alias') .
					', ' . $db->quoteName('t2.alias') . '))' .
				' ELSE ' . $db->quoteName('m.id') .  ' END AS slug')
			->from($db->quoteName('#__joomleague_match', 'm'))
			->join('INNER', $db->quoteName('#__joomleague_round', 'r') .
				' ON ' . $db->quoteName('r.id') . ' = ' . $db->quoteName('m.round_id'))
			->join('INNER', $db->quoteName('#__joomleague_project', 'p') .
				' ON ' . $db->quoteName('p.id') . ' = ' . $db->quoteName('r.project_id'))
			->join('INNER', $db->quoteName('#__joomleague_project_team', 'pt1') .
				' ON ' . $db->quoteName('pt1.id') . ' = ' . $db->quoteName('m.projectteam1_id'))
			->join('INNER', $db->quoteName('#__joomleague_project_team', 'pt2') .
				' ON ' . $db->quoteName('pt2.id') . ' = ' . $db->quoteName('m.projectteam2_id'))
			->join('INNER', $db->quoteName('#__joomleague_team', 't1') .
				' ON ' . $db->quoteName('t1.id') . ' = ' . $db->quoteName('pt1.team_id'))
			->join('INNER', $db->quoteName('#__joomleague_team', 't2') .
				' ON ' . $db->quoteName('t2.id') . ' = ' . $db->quoteName('pt2.team_id'))
			->join('LEFT', $db->quoteName('#__joomleague_division', 'd1') .
				' ON ' . $db->quoteName('d1.id') . ' = ' . $db->quoteName('pt1.division_id'))
			->join('LEFT', $db->quoteName('#__joomleague_division', 'd2') .
				' ON ' . $db->quoteName('d2.id') . ' = ' . $db->quoteName('pt2.division_id'))
			->join('LEFT', $db->quoteName('#__joomleague_playground', 'playground') .
				' ON ' . $db->quoteName('playground.id') . ' = ' . $db->quoteName('m.playground_id'))
			->where($db->quoteName('m.published') . ' = 1')
			->where($db->quoteName('r.id') . ' = ' . (int)$round)
			->where($db->quoteName('r.project_id') . ' = ' . (int)$project->id)
			->group($db->quoteName('m.id'))
			->order($db->quoteName('m.match_date') . ' ASC')
			->order($db->quoteName('m.match_number'));

		if ($division > 0)
		{
			$query
				->where($db->quoteName('d1.id') . ' = ' . (int)$division .
					' OR ' . $db->quoteName('d1.parent_id') . ' = ' . (int)$division .
					' OR ' . $db->quoteName('d2.id') . ' = ' . (int)$division .
					' OR ' . $db->quoteName('d2.parent_id') . ' = ' . (int)$division);
		}
		$db->setQuery($query);
		return $db->loadObjectList();
	}

	/**
	 * returns match referees
	 * @param int match id
	 * @return array
	 */
	function getMatchReferees($match_id)
	{
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		$query
			->select($db->quoteName('pref.id', 'person_id'))
			->select($db->quoteName('p.firstname'))
			->select($db->quoteName('p.lastname'))
			->select($db->quoteName('pos.name', 'position_name'))
			->from($db->quoteName('#__joomleague_match_referee', 'mr'))
			->join('INNER', $db->quoteName('#__joomleague_project_referee', 'pref') .
				' ON ' . $db->quoteName('pref.id') . ' = ' . $db->quoteName('mr.project_referee_id'))
			->join('INNER', $db->quoteName('#__joomleague_person', 'p') .
				' ON ' . $db->quoteName('p.id') . ' = ' . $db->quoteName('pref.person_id'))
			->join('INNER', $db->quoteName('#__joomleague_project_position', 'ppos') .
				' ON ' . $db->quoteName('ppos.id') . ' = ' . $db->quoteName('mr.project_position_id'))
			->join('INNER', $db->quoteName('#__joomleague_position', 'pos') .
				' ON ' . $db->quoteName('pos.id') . ' = ' . $db->quoteName('ppos.position_id'))
			->where($db->quoteName('mr.match_id') . ' = ' . (int)$match_id)
			->where($db->quoteName('p.published') . ' = 1')
			->order($db->quoteName('pos.name'))
			->order($db->quoteName('mr.ordering'));

		$db->setQuery($query);
		return $db->loadObjectList();
	}

	/**
	 * returns referees (as teamname) who ruled in specific match
	 *
	 * @param int $position_id
	 * @return array of players
	 */
	function getMatchRefereeTeams($match_id)
	{
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		$query
			->select($db->quoteName('mr.project_referee_id AS value'))
			->select($db->quoteName('t.name AS teamname'))
			->select($db->quoteName('pos.name AS position_name'))
			->from($db->quoteName('#__joomleague_match_referee AS mr'))
			->join('INNER', $db->quoteName('#__joomleague_project_team AS pt') .
				' ON ' . $db->quoteName('pt.id') . ' = ' . $db->quoteName('mr.project_referee_id'))
			->join('INNER', $db->quoteName('#__joomleague_team AS t') .
				' ON ' . $db->quoteName('t.id') . ' = ' . $db->quoteName('pt.team_id'))
			->join('INNER', $db->quoteName('#__joomleague_position AS pos') .
				' ON ' . $db->quoteName('pos.id') . ' = ' . $db->quoteName('mr.project_position_id'))
			->where($db->quoteName('mr.match_id') . ' = ' . (int) $match_id)
			->order($db->quoteName('pos.name'))
			->order($db->quoteName('mr.ordering') . ' ASC');

		$db->setQuery($query);
		return $db->loadObjectList('value');
	}

	function isMatchAdmin($matchid)
	{
		$project_id = $this->getProject()->id;
		$result = (	Factory::getUser()->authorise('core.admin', 'com_joomleague.project.'.$project_id) || 
					Factory::getUser()->authorise('core.manage', 'com_joomleague.project.'.$project_id) || 
					Factory::getUser()->authorise('core.edit', 'com_joomleague.match.'.$matchid) ? true : false);
		return $result;
	}

	function isAllowed()
	{
		$allowed = false;
		$user = Factory::getUser();
		if ($user->id != 0)
		{
			$project = $this->getProject();
			// Check if user is project admin or editor. If not, then check if user has ACL rights.
			$allowed = $this->isUserProjectAdminOrEditor($user->id, $project) || $user->authorise('match.saveshort', 'com_joomleague');
		}
		return $allowed;
	}

	function getShowEditIcon()
	{
		return $this->isAllowed();
	}

	// TODO: think we can do with the method supplied by the project model
//	function getFavTeams(&$project)
//	{
//		$favteams=explode(',',$project->fav_team);
//		return $favteams;
//	}
}
