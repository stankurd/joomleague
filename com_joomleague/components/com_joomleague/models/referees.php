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

require_once JLG_PATH_SITE.'/models/project.php';

/**
 * Model-Referees
 */
class JoomleagueModelReferees extends JoomleagueModelProject
{
	var $referees = null;

	function getReferees()
	{
		if (empty($this->referees))
		{
			$db = Factory::getDbo();
			$subQuery = $db->getQuery(true);
			$subQuery
				->select('COUNT(*)')
				->from($db->quoteName('#__joomleague_match', 'm'))
				->join('INNER', $db->quoteName('#__joomleague_round', 'r') .
					' ON ' . $db->quoteName('r.id') . ' = ' . $db->quoteName('m.round_id'))
				->join('LEFT', $db->quoteName('#__joomleague_project_team', 'pt1') .
					' ON ' . $db->quoteName('pt1.id') . ' = ' . $db->quoteName('m.projectteam1_id'))
				->join('LEFT', $db->quoteName('#__joomleague_project_team', 'pt2') .
					' ON ' . $db->quoteName('pt2.id') . ' = ' . $db->quoteName('m.projectteam2_id'))
				->join('INNER', $db->quoteName('#__joomleague_match_referee', 'mr') .
					' ON ' . $db->quoteName('mr.match_id') . ' = ' . $db->quoteName('m.id'))
				->where('(' . $db->quoteName('pt1.project_id') . ' = ' . $db->quoteName('pr.project_id') .
					' OR ' . $db->quoteName('pt2.project_id') . ' = ' . $db->quoteName('pr.project_id') . ')')
				->where($db->quoteName('mr.project_referee_id') . ' = ' . $db->quoteName('pr.id'));

			$query = $db->getQuery(true);
			$query
				->select('p.*')
				->select($db->quoteName('pr.id', 'prid'))
				->select($db->quoteName('ppos.position_id'))
				->select($db->quoteName('p.id', 'pid'))
				->select($db->quoteName('pos.name', 'position'))
				->select($db->quoteName('pr.notes', 'description'))
				->select($db->quoteName('pos.parent_id'))
				->select($this->constructSlug($db, 'slug', 'p.alias', 'p.id'))
				->select('(' . $subQuery . ') AS countGames')
				->from($db->quoteName('#__joomleague_project_referee', 'pr'))
				->join('INNER', $db->quoteName('#__joomleague_person', 'p') .
					' ON ' . $db->quoteName('p.id') . ' = ' . $db->quoteName('pr.person_id'))
				->join('INNER', $db->quoteName('#__joomleague_project_position', 'ppos') .
					' ON ' . $db->quoteName('ppos.id') . ' = ' . $db->quoteName('pr.project_position_id'))
				->join('INNER', $db->quoteName('#__joomleague_position', 'pos') .
					' ON ' . $db->quoteName('pos.id') . ' = ' . $db->quoteName('ppos.position_id'))
				->where($db->quoteName('pr.project_id') . ' = ' . (int)$this->projectid)
				->where($db->quoteName('p.published') . ' = 1')
				->order($db->quoteName('pos.ordering'))
				->order($db->quoteName('pos.id'));

			$db->setQuery($query);
			$this->referees = $db->loadObjectList();
		}
		return $this->referees;
	}

	function getPositionEventTypes($positionId = 0)
	{
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		$query
			->select('pet.*')
			->select($db->quoteName('et.name'))
			->select($db->quoteName('et.icon'))
			->from($db->quoteName('#__joomleague_position_eventtype', 'pet'))
			->join('INNER', $db->quoteName('#__joomleague_eventtype', 'et') .
				' ON ' . $db->quoteName('et.id') . ' = ' . $db->quoteName('pet.eventtype_id'))
			->join('INNER', $db->quoteName('#__joomleague_project_position', 'ppos') .
				' ON ' . $db->quoteName('ppos.position_id') . ' = ' . $db->quoteName('pet.position_id'))
			->where($db->quoteName('ppos.project_id') . ' = ' . (int)$this->projectid);

		if ($positionId > 0)
		{
			$query
				->where($db->quoteName('pet.position_id') . ' = ' . (int)$positionId);
		}

		$query
			->order($db->quoteName('et.ordering'));

		$db->setQuery($query);
		$result = $db->loadObjectList();
		if ($result)
		{
			if ($positionId)
			{
				return $result;
			}
			else
			{
				$posEvents = array();
				foreach ($result as $r)
				{
					$posEvents[$r->position_id][] = $r;
				}
				return $posEvents;
			}
		}
		return array();
	}
}
