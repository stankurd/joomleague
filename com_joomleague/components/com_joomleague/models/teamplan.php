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
use Joomla\CMS\Language\Text;

defined('_JEXEC') or die;

require_once JLG_PATH_SITE.'/models/project.php';
require_once 'results.php';

/**
 * Model-Teamplan
 */
class JoomleagueModelTeamPlan extends JoomleagueModelProject
{
	var $projectid=0;
	var $teamid=0;
	var $team=null;
	var $club=null;
	var $divisionid=0;
	var $joomleague=null;
	var $mode=0;

	function __construct()
	{
		parent::__construct();
		
		$app = Factory::getApplication();
		$input = $app->input;

		$this->projectid = $input->getInt('p',0);
		$this->teamid = $input->getInt('tid',0);
		$this->divisionid = $input->getInt('division',0);
		$this->mode = $input->getInt("mode",0);
	}

	function getDivisionID()
	{
		return $this->divisionid;
	}

	function getMode()
	{
		return $this->mode;
	}

	// TODO: this function is inherited from the project model, but its signature is different
	// (which is not allowed in PHP (gives warning)). Look into how this can be solved best.
	function getDivision($id = 0)
	{
		$division = null;
		if ($this->divisionid > 0)
		{
			$db = Factory::getDbo();
			$query = $db->getQuery(true);
			$query
				->select('*')
				->select($this->constructSlug($db))
				->from($db->quoteName('#__joomleague_division'))
				->where($db->quoteName('id') . ' = ' . (int)$this->divisionid);

			$db->setQuery($query, 0, 1);
			$division = $db->loadObject();
		}
		return $division;
	}

	// TODO: this function is inherited from the project model, but its signature is different
	// (which is not allowed in PHP (gives warning)). Look into how this can be solved best.
	function getProjectTeamId($teamId = 0)
	{
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		$query
			->select($db->quoteName('id'))
			->from($db->quoteName('#__joomleague_project_team'))
			->where($db->quoteName('team_id') . ' = ' . (int)$this->teamid)
			->where($db->quoteName('project_id') . ' = ' . (int)$this->projectid);

		$db->setQuery($query, 0, 1);
		if (!$result = $db->loadResult())
		{
			return 0;
		}
		return $result;
	}

	function getMatchesPerRound($config, $rounds)
	{
		$rm = array();
		$ordering = $config['plan_order'] ? $config['plan_order'] : 'DESC';
		foreach ($rounds as $round)
		{
			$matches = $this->_getResultsRows($round->roundcode, $this->teamid, $ordering, 0, 1, $config['show_referee']);
			$rm[$round->roundcode] = $matches;
		}
		return $rm;
	}

	function getMatches($config)
	{
		$ordering = $config['plan_order'] ? $config['plan_order'] : 'DESC';
		return $this->_getResultsPlan($this->teamid, $ordering, 0, 1, $config['show_referee']);
	}

	function getMatchesRefering($config)
	{
		$ordering = $config['plan_order'] ? $config['plan_order'] : 'DESC';
		return $this->_getResultsPlan(0, $ordering, $this->teamid, 1, $config['show_referee']);
	}

	function _getResultsPlan($team = 0, $ordering = 'ASC', $referee = 0, $getplayground = 0, $getreferee = 0)
	{
		$joomleague = $this->getProject();
		$db = Factory::getDbo();
		if ($this->divisionid > 0)
		{
			$query = $db->getQuery(true);
			$query
				->select($db->quoteName('id'))
				->from($db->quoteName('#__joomleague_division'))
				->where($db->quoteName('parent_id') . ' = ' . (int)$this->divisionid);

			$db->setquery($query);
			$div_for_teams = $db->loadColumn();
			$div_for_teams[] = $this->getDivision()->id;
		}

		$query = $db->getQuery(true);
		$query
			->select('m.*')
			->select('DATE_FORMAT(' . $db->quoteName('m.time_present') . ', "%H:%i") AS time_present')
			->select($db->quoteName('t1.id', 'team1'))
			->select($db->quoteName('t2.id', 'team2'))
			->select($db->quoteName('r.roundcode'))
			->select($db->quoteName('pt1.division_id'))
			->select($db->quoteName('r.id', 'roundid'))
			->select($db->quoteName('r.project_id'))
			->select($db->quoteName('r.name'))
			->select($db->quoteName('p.timezone'));
		if ($referee != 0) {
			$query
				->select($db->quoteName('p.name', 'project_name'));
		}
		if ($getplayground)
		{
			$query
				->select($db->quoteName('playground.name', 'playground_name'))
				->select($db->quoteName('playground.short_name', 'playground_short_name'));
		}
		$query
			->from($db->quoteName('#__joomleague_match', 'm'))
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
				' ON ' . $db->quoteName('p.id') . ' = ' . $db->quoteName('r.project_id'));
		if ($referee != 0) {
			$query
				->join('INNER', $db->quoteName('#__joomleague_match_referee', 'mref') .
					' ON ' . $db->quoteName('mref.match_id') . ' = ' . $db->quoteName('m.id'));
		}
		if ($getplayground)
		{
			$query
				->join('LEFT', $db->quoteName('#__joomleague_playground', 'playground') .
					' ON ' . $db->quoteName('playground.id') . ' = ' . $db->quoteName('m.playground_id'));
		}
		$query
			->where($db->quoteName('m.published') . ' = 1');


		// win matches
		if ($this->mode == 1)
		{
			$query
				->where('(('. $db->quoteName('t1.id') . ' = ' . (int)$this->teamid . ' AND ' .
							  $db->quoteName('m.team1_result') . ' > ' . $db->quoteName('m.team2_result') . ')' .
					' OR (' . $db->quoteName('t2.id') . ' = ' . (int)$this->teamid . ' AND ' .
							  $db->quoteName('m.team1_result') . ' < ' . $db->quoteName('m.team2_result') . '))');
		}
		
		//draw matches
		if ($this->mode == 2)
		{
			$query
				->where($db->quoteName('m.team1_result') . ' = ' . $db->quoteName('m.team2_result'));
		}
		//lost matches
		if ($this->mode == 3)
		{
			$query
				->where('((' . $db->quoteName('t1.id') . ' = ' . (int)$this->teamid . ' AND ' .
							   $db->quoteName('m.team1_result') . ' < ' . $db->quoteName('m.team2_result') . ')'
					.' OR (' . $db->quoteName('t2.id') . ' = ' . (int)$this->teamid . ' AND ' .
							   $db->quoteName('m.team1_result') . ' > ' . $db->quoteName('m.team2_result') . '))');
		}
		
		
	
		if ($this->divisionid > 0)
		{
			$query
				->where('(' . $db->quoteName('pt1.division_id') . ' IN (' . implode(',',$div_for_teams) . ')' .
					' OR ' . $db->quoteName('pt2.division_id') . ' IN (' . implode(',',$div_for_teams) . '))');
		}

		if ($referee != 0)
		{
			$query
				->where($db->quoteName('mref.project_referee_id') . ' = ' . (int)$referee)
				->where($db->quoteName('p.season_id') . ' = ' . (int)$joomleague->season_id);
		}
		else
		{
			$query
				->where($db->quoteName('r.project_id') . ' = ' . (int)$this->projectid);
		}

		if ($this->teamid != 0)
		{
			$query
				->where('(' . $db->quoteName('t1.id') . ' = ' . (int)$this->teamid .
					' OR ' . $db->quoteName('t2.id') . ' = ' . (int)$this->teamid . ')');
		}
		

		$query
			->group($db->quoteName('m.id'))
			->order($db->quoteName('r.roundcode') . ' ' . $ordering . ', ' .
				$db->quoteName('m.match_date') . ', ' . $db->quoteName('m.match_number'));

		$db->setQuery($query);
		$matches = $db->loadObjectList();

		if ($matches)
		{
			foreach ($matches as $match)
			{
				JoomleagueHelper::convertMatchDateToTimezone($match);
			}
		}

		if ($getreferee)
		{
			$this->_getRefereesByMatch($matches, $joomleague);
		}

		return $matches;
	}

	function _getResultsRows($roundcode = 0, $teamId = 0, $ordering = 'ASC', $unpublished = 0, $getplayground = 0, $getreferee = 0)
	{
		$joomleague = $this->getProject();
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		$query
			->select('matches.*')
			->select($db->quoteName('p.timezone'));
		if ($getplayground)
		{
			$query
				->select($db->quoteName('playground.name', 'playground_name'))
				->select($db->quoteName('playground.short_name', 'playground_short_name'));
		}

		$query
			->from($db->quoteName('#__joomleague_match', 'matches'))
			->join('INNER', $db->quoteName('#__joomleague_round', 'r') .
				' ON ' . $db->quoteName('r.id') . ' = ' . $db->quoteName('matches.round_id'))
			->join('INNER', $db->quoteName('#__joomleague_project', 'p') .
				' ON ' . $db->quoteName('p.id') . ' = ' . $db->quoteName('r.project_id'))
			->join('INNER', $db->quoteName('#__joomleague_project_team', 'pt1') .
				' ON ' . $db->quoteName('pt1.id') . ' = ' . $db->quoteName('matches.projectteam1_id'))
			->join('INNER', $db->quoteName('#__joomleague_team', 't1') .
				' ON ' . $db->quoteName('t1.id') . ' = ' . $db->quoteName('pt1.team_id'))
			->join('INNER', $db->quoteName('#__joomleague_project_team', 'pt2') .
				' ON ' . $db->quoteName('pt2.id') . ' = ' . $db->quoteName('matches.projectteam2_id'))
			->join('INNER', $db->quoteName('#__joomleague_team', 't2') .
				' ON ' . $db->quoteName('t2.id') . ' = ' . $db->quoteName('pt2.team_id'));
		if ($this->divisionid > 0)
		{
			$query
				->join('LEFT', $db->quoteName('#__joomleague_division', 'd1') .
					' ON ' . $db->quoteName('d1.id') . ' = ' . $db->quoteName('pt1.division_id'))
				->join('LEFT', $db->quoteName('#__joomleague_division', 'd2') .
					' ON ' . $db->quoteName('d2.id') . ' = ' . $db->quoteName('pt2.division_id'));
		}
		if ($getplayground)
		{
			$query
				->join('LEFT', $db->quoteName('#__joomleague_playground', 'playground') .
					' ON ' . $db->quoteName('playground.id') . ' = ' . $db->quoteName('matches.playground_id'));
		}

		$query
			->where($db->quoteName('r.project_id') . ' = ' . (int)$this->projectid)
			->where($db->quoteName('r.roundcode') . ' = ' . (int)$roundcode);

		if ($teamId)
		{
			$query
				->where('(' . $db->quoteName('t1.id') . ' = ' . (int)$teamId .
					' OR ' . $db->quoteName('t2.id') . ' = ' . (int)$teamId . ')');
		}
		if ($this->divisionid > 0)
		{
			$query
				->where('(' . $db->quoteName('d1.id') . ' = ' . (int)$this->divisionid .
					' OR ' . $db->quoteName('d1.parent_id') . ' = ' . (int)$this->divisionid .
					' OR ' . $db->quoteName('d2.id') . ' = ' . (int)$this->divisionid .
					' OR ' . $db->quoteName('d2.parent_id') . ' = ' . (int)$this->divisionid . ')');
		}

		if ($unpublished != 1)
		{
			$query->
				where($db->quoteName('matches.published') . ' = 1');
		}

		$query
			->group($db->quoteName('matches.id'))
			->order($db->quoteName('matches.match_date') . ' ' . $ordering . ', ' . $db->quoteName('matches.match_number'));

		$db->setQuery($query);
		$matches = $db->loadObjectList();
		if ($matches)
		{
			foreach ($matches as $match)
			{
				JoomleagueHelper::convertMatchDateToTimezone($match);
			}
		}

		if ($getreferee)
		{
			$this->_getRefereesByMatch($matches, $joomleague);
		}

		return $matches;
	}

	function _getRefereesByMatch($matches, $joomleague)
	{
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		for ($index=0; $index < count($matches); $index++) {
			$query = $db->getQuery(true);
			if ($joomleague->teams_as_referees)
			{
				$query
					->select($db->quoteName('ref.name', 'referee_name'))
					->from($db->quoteName('#__joomleague_team', 'ref'))
					->join('LEFT', $db->quoteName('#__joomleague_match_referee', 'link') .
						' ON ' . $db->quoteName('link.project_referee_id') . ' = ' . $db->quoteName('ref.id'))
					->where($db->quoteName('link.match_id') . ' = ' . (int)$matches[$index]->id)
					->order($db->quoteName('link.ordering'));
			}
			else
			{
				$query
					->select($db->quoteName('ref.firstname', 'referee_firstname'))
					->select($db->quoteName('ref.lastname', 'referee_lastname'))
					->select($db->quoteName('ref.id', 'referee_id'))
					->select($db->quoteName('ppos.position_id'))
					->select($db->quoteName('pos.name', 'referee_position_name'))
					->from($db->quoteName('#__joomleague_person', 'ref'))
					->join('LEFT', $db->quoteName('#__joomleague_project_referee', 'pref') .
						' ON ' . $db->quoteName('pref.person_id') . ' = ' . $db->quoteName('ref.id'))
					->join('LEFT', $db->quoteName('#__joomleague_match_referee', 'link') .
						' ON ' . $db->quoteName('link.project_referee_id') . ' = ' . $db->quoteName('pref.id'))
					->join('INNER', $db->quoteName('#__joomleague_project_position', 'ppos') .
						' ON ' . $db->quoteName('ppos.id') . ' = ' . $db->quoteName('link.project_position_id'))
					->join('INNER', $db->quoteName('#__joomleague_position', 'pos') .
						' ON ' . $db->quoteName('pos.id') . ' = ' . $db->quoteName('ppos.position_id'))
					->where($db->quoteName('ref.published') . ' = 1')
					->where($db->quoteName('link.match_id') . ' = ' . (int)$matches[$index]->id)
					->order($db->quoteName('link.ordering'));
			}

			$db->setQuery($query);
			try {
			    $referees = $db->loadObjectList();
			} catch (Exception $e) {
			    Factory::getApplication()->enqueueMessage(Text::_($e->getMessage()), 'error');
			    return false;
			}
			$matches[$index]->referees = $referees;
		}
		return $matches;
	}
}
