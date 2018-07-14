<?php
/**
 * Joomleague
 *
 * @copyright	Copyright (C) 2006-2015 joomleague.at. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @link		http://www.joomleague.at
 */

// Check to ensure this file is included in Joomla!
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;

defined('_JEXEC') or die;

require_once JPATH_COMPONENT.'/models/list.php';

/**
 * Matches Model
 *
 * @author	Marco Vaninetti <martizva@tiscali.it>
 */

class JoomleagueModelMatches extends JoomleagueModelList
{
	var $_identifier = "matches";
	
	public function getData()
	{
		$data = parent::getData();
		if ($data)
		{
			foreach ($data as $match)
			{
 				JoomleagueHelper::convertMatchDateToTimezone($match);
			}
		}
		return $data;
	}
				
	function _buildQuery()
	{
		$app 	= Factory::getApplication();
		$db = Factory::getDbo();	
		$query = $db->getQuery(true);
		// Get the WHERE and ORDER BY clauses for the query
		$where		= $this->_buildContentWhere();
		$orderby	= $this->_buildContentOrderBy();

		$query = '	SELECT	mc.*, p.timezone,
						CASE mc.time_present 
						when "00:00:00" then NULL
						else DATE_FORMAT(mc.time_present, "%H:%i")
						END AS time_present, IFNULL(divhome.shortname, divhome.name) divhome, 
						divhome.id divhomeid,
						divaway.id divawayid,
						t1.name AS team1,
							t2.name AS team2,
							u.name AS editor, 
							(Select count(mp.id) 
							 FROM #__joomleague_match_player AS mp 
							 WHERE mp.match_id = mc.id
							   AND (came_in=0 OR came_in=1) 
							   AND mp.teamplayer_id in (
							     SELECT id 
							     FROM #__joomleague_team_player AS tp
							     WHERE tp.projectteam_id = mc.projectteam1_id
							   )
							 ) AS homeplayers_count, 
							(Select count(ms.id) 
							 FROM #__joomleague_match_staff AS ms
							 WHERE ms.match_id = mc.id
							   AND ms.team_staff_id in (
							     SELECT id 
							     FROM #__joomleague_team_staff AS ts
							     WHERE ts.projectteam_id = mc.projectteam1_id
							   )
							) AS homestaff_count, 
							(Select count(mp.id) 
							 FROM #__joomleague_match_player AS mp 
							 WHERE mp.match_id = mc.id
							   AND (came_in=0 OR came_in=1) 
							   AND mp.teamplayer_id in (
							     SELECT id 
							     FROM #__joomleague_team_player AS tp
							     WHERE tp.projectteam_id = mc.projectteam2_id
							   )
							 ) AS awayplayers_count, 
							(Select count(ms.id) 
							 FROM #__joomleague_match_staff AS ms
							 WHERE ms.match_id = mc.id
							   AND ms.team_staff_id in (
							     SELECT id 
							     FROM #__joomleague_team_staff AS ts
							     WHERE ts.projectteam_id = mc.projectteam2_id
							   )
							) AS awaystaff_count,
							(Select count(mr.id) 
							  FROM #__joomleague_match_referee AS mr 
							  WHERE mr.match_id = mc.id
							) AS referees_count 
					FROM #__joomleague_match AS mc
					LEFT JOIN #__users u ON u.id = mc.checked_out
					LEFT JOIN #__joomleague_project_team AS pthome ON pthome.id = mc.projectteam1_id
					LEFT JOIN #__joomleague_project_team AS ptaway ON ptaway.id = mc.projectteam2_id
					LEFT JOIN #__joomleague_team AS t1 ON t1.id = pthome.team_id
					LEFT JOIN #__joomleague_team AS t2 ON t2.id = ptaway.team_id
					LEFT JOIN #__joomleague_round AS r ON r.id = mc.round_id 
					LEFT JOIN #__joomleague_project AS p ON p.id=r.project_id
					LEFT JOIN #__joomleague_division AS divaway ON divaway.id = ptaway.division_id 
					LEFT JOIN #__joomleague_division AS divhome ON divhome.id = pthome.division_id ' .
		
		$where . $orderby;
		return $query;
	}

	function _buildContentOrderBy()
	{
		$app	= Factory::getApplication();
		$option = $app->input->get('option');
		$filter_order		= $app->getUserStateFromRequest($option . 'mc_filter_order', 'filter_order', 'mc.match_date', 'cmd');
		$filter_order_Dir	= $app->getUserStateFromRequest($option . 'mc_filter_order_Dir', 'filter_order_Dir', '', 'word');

		if ($filter_order == 'mc.match_number')
		{
			$orderby    = ' ORDER BY mc.match_number +0 '. $filter_order_Dir .', divhome.id, divaway.id ' ;
		}
		elseif ($filter_order == 'mc.match_date')
		{
			$orderby 	= ' ORDER BY mc.match_date '. $filter_order_Dir .', divhome.id, divaway.id ';
		}
		else
		{
			$orderby 	= ' ORDER BY ' . $filter_order . ' ' . $filter_order_Dir . ' , mc.match_date, divhome.id, divaway.id';
		}

		return $orderby;
	}

	function _buildContentWhere()
	{
		$app = Factory::getApplication();
		$db = Factory::getDbo();	
		$query = $db->getQuery(true);
		$option = $app->input->get('option');
		$project_id = $app->getUserState($option . 'project');
		$division = (int) $app->getUserStateFromRequest($option.'mc_division', 'division', 0);
		$round_id = $app->getUserState($option . 'round_id');

		if (!empty($round_id)) {
			$where=array();
			$where[] = ' mc.round_id = ' . $round_id;
			if ($division>0)
			{
				$where[]=' divhome.id = '.$db->Quote($division);
			}
			$where = ' WHERE '.implode(' AND ',$where);
		} else {
			$where = '';
		}
		return $where;
	}

	/**
	 * Method to return the project teams array (id, name)
	 *
	 * @access  public
	 * @return  array
	 */
	function getProjectTeams()
	{
		$app = Factory::getApplication();
		$db = Factory::getDbo();	
		$query = $db->getQuery(true);
		$option = $app->input->get('option');
		$project_id = $app->getUserState($option . 'project');

		$query->select('pt.id AS value,
							t.name AS text,
							t.short_name AS short_name,
							t.notes')
			->from('#__joomleague_team AS t')
			->join('LEFT','#__joomleague_project_team AS pt ON pt.team_id = t.id')
			->where('pt.project_id = ' . $project_id)
			->order('text ASC');
				try
					{
						$db->setQuery($query);
						$result = $db->loadObjectList();
					}
				catch (Exception $e)
					{
						$app->enqueueMessage(Text::_($e->getMessage()), 'error');
				return false;
					}
			return $result;
	}

	/**
	 * @param int iDivisionId
	 * return project teams as options
	 * @return unknown_type
	 */
	function getProjectTeamsOptions($iDivisionId=0)
	{
		$app = Factory::getApplication();
		$db = Factory::getDbo();	
		$query = $db->getQuery(true);
		$option = $app->input->get('option');
		$project_id = $app->getUserState($option . 'project');

		$query = ' SELECT	pt.id AS value, '
		. ' CASE WHEN CHAR_LENGTH(t.name) < 25 THEN t.name ELSE t.middle_name END AS text '
		. ' FROM #__joomleague_team AS t '
		. ' LEFT JOIN #__joomleague_project_team AS pt ON pt.team_id = t.id '
		. ' WHERE pt.project_id = ' . $project_id;
		if($iDivisionId>0)  {
			$query .=' AND pt.division_id = ' .$iDivisionId;
		}
		$query .= ' ORDER BY text ASC ';

				try
					{
						$db->setQuery($query);
						$result = $db->loadObjectList();
					}
				catch (Exception $e)
					{
						$app->enqueueMessage(Text::_($e->getMessage()), 'error');
						return false;
					}
			return $result;
	}

	function getMatchesByRound($roundId)
	{
		$app = Factory::getApplication();
		$db = Factory::getDbo();	
		$query = $db->getQuery(true);
		$query->select('*') 
		->from('#__joomleague_match') 
		->where('round_id='.$roundId);
				try
					{
						$db->setQuery($query);
						$result = $db->loadObjectList();
					}
				catch (Exception $e)
					{
						$app->enqueueMessage(Text::_($e->getMessage()), 'error');
						return false;
					}
		return $result;
	}
}
