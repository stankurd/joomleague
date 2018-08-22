<?php
/**
 * Joomleague
 *
 * @copyright	Copyright (C) 2006-2015 joomleague.at. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @link		http://www.joomleague.at
 * @author		Kurt Norgaz
 */
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;

defined('_JEXEC') or die;


/**
 * Persons Model
 */
class JoomleagueModelPersons extends JLGModelList
{

	public function __construct($config = array())
	{
		if(empty($config['filter_fields']))
		{
			$config['filter_fields'] = array(
					'a.firstname','a.nickname',
					'a.lastname','a.id',
					'a.ordering','a.birthday',
					'a.country','a.position_id'
			);
		}

		parent::__construct($config);
	}


	protected function populateState($ordering = null,$direction = null)
	{
		$app = Factory::getApplication();

		// Adjust the context to support modal layouts.
		if($layout = $app->input->get('layout'))
		{
			$this->context .= '.'.$layout;
		}

		$search = $this->getUserStateFromRequest($this->context.'.filter.search','filter_search');
		$this->setState('filter.search',$search);

		// List state information.
		parent::populateState('a.lastname','asc');
	}


	protected function getStoreId($id = '')
	{
		$id .= ':'.$this->getState('filter.search');

		return parent::getStoreId($id);
	}


	protected function getListQuery()
	{
		$app 	= Factory::getApplication();
		$input = $app->input;
		$option = $input->getCmd('option');

		$project_id = $app->getUserState($option.'project');
		$team_id = $app->getUserState($option.'team_id');
		$project_team_id = $app->getUserState($option.'project_team_id');
		$exludePerson = '';

		$db 	= $this->getDbo();
		$query	= $db->getQuery(true);

		// Select the required fields from the table.
		$query->select($this->getState('list.select','a.*'));
		$query->from('#__joomleague_person AS a');

		// Users
		$query->select('u.name AS editor');
		$query->join('LEFT','#__users AS u ON u.id = a.checked_out');
		
		// remove Ghost Player data
		$query->where('a.firstname NOT LIKE '.$db->Quote("!Unknown"));
		$query->where('a.lastname NOT LIKE '.$db->Quote("!Player"));

		// filter-search
		$search = $this->getState('filter.search');
		if (!empty($search))
		{
			$searchLength = mb_strlen($search);
			if($searchLength == 1 && preg_match('/[A-Z]/', $search) > 0)
			{
				$query->where(
						'(LOWER(a.lastname) LIKE '.$db->Quote($search.'%').'OR LOWER(a.firstname) LIKE '.$db->Quote($search.'%').
						'OR LOWER(a.nickname) LIKE '.$db->Quote($search.'%').')');
			} else {
				$query->where(
						'(LOWER(a.lastname) LIKE ' . $db->Quote('%' . $search . '%') . 'OR LOWER(a.firstname) LIKE ' . $db->Quote('%' . $search . '%') .
						'OR LOWER(a.nickname) LIKE ' . $db->Quote('%' . $search . '%') . ')');
			}
		}

		// Order
		$filter_order = $this->state->get('list.ordering','a.id');
		$filter_order_Dir = $this->state->get('list.direction','desc');
		if($filter_order == 'a.lastname')
		{
			$query->order('a.lastname '.$filter_order_Dir);
		}
		else
		{
			$query->order($filter_order.' '.$filter_order_Dir,'a.lastname');
		}

		return $query;
	}


	/**
	 * get person history across all projects, with team, season, position,...
	 * info
	 *
	 * @param int $person_id
	 * @param int $order		ordering for season and league
	 * @param string $filter	e.g. "s.name = 2007/2008", default empty string
	 * @return array of objects
	 */
	function jl_getPersonHistory($person_id,$order = 'ASC',$published = 1,$filter = "")
	{
		$app = Factory::getApplication();
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		if($published)
		{
			$filter .= " AND p.published = 1 ";
		}

		$query = "	SELECT	pt.id AS ptid,
							pt.person_id AS pid,
							pt.team_id, pt.project_id,
							t.name AS teamname,
							p.name AS pname,
							s.name AS sname,
							tt.id AS ttid,
							pos.name AS position
					FROM #__joomleague_team_player AS pt
					INNER JOIN #__joomleague_project AS p ON p.id = pt.project_id
					INNER JOIN #__joomleague_season AS s ON s.id = p.season_id
					INNER JOIN #__joomleague_league AS l ON l.id = p.league_id
					INNER JOIN #__joomleague_team AS t ON t.id = pt.team_id
					INNER JOIN #__joomleague_project_team AS tt ON pt.team_id = tt.team_id AND pt.project_id = tt.project_id
					INNER JOIN #__joomleague_position AS pos ON pos.id = pt.project_position_id
					WHERE person_id='" . $person_id . "' " . $filter . "
					GROUP BY pt.id	ORDER BY	s.ordering " . $order . ",
												l.ordering " . $order . ",
												p.name
									ASC";
		$db->setQuery($query);
		$result = $db->loadObjectList();
		return $result;
	}


	/**
	 * get person history across all projects, with team, season, position,...
	 * info
	 *
	 * @param int $person_id,	linked to person_id from person object
	 * @param int $order		ordering for season and league
	 * @param string $filter	e.g. "s.name = 2007/2008", default empty string
	 * @return array of objects
	 */
	function jl_getStaffHistory($person_id,$order = 'ASC',$published = 1,$filter = "")
	{
		$app = Factory::getApplication();
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		if($published)
		{
			$filter .= " AND p.published = 1 ";
		}

		$query = "	SELECT	ts.teamstaff_id AS tsid,
							ts.person_id AS pid,
							p.id AS project_id,
							t.name AS teamname,
							p.name AS pname,
							s.name AS sname,
							tt.id AS ttid,
							pos.name AS position
					FROM	#__joomleague_team_staff AS ts
					INNER JOIN #__joomleague_project_team AS tt ON tt.id = ts.projectteam_id
					INNER JOIN #__joomleague_project AS p ON p.id = tt.project_id
					INNER JOIN #__joomleague_season AS s ON s.id = p.season_id
					INNER JOIN #__joomleague_league AS l ON l.id = p.league_id
					INNER JOIN #__joomleague_team AS t ON t.id = tt.team_id
					INNER JOIN #__joomleague_position AS pos ON pos.id = ts.project_position_id
					WHERE person_id= '" . $person_id . "' " . $filter . "
					GROUP BY ts.teamstaff_id	ORDER BY	s.ordering " . $order . ",
											l.ordering " . $order . ",
											p.name
											ASC";

		$db->setQuery($query);
		try
		{
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
	 * return persons list from ids contained in var cid
	 *
	 * @return array
	 */
	function getPersonsToAssign()
	{
		$app = Factory::getApplication();
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		$cid = $app->input->getVar('cid');

		if(! count($cid))
		{
			return array();
		}

		$query->select(array(
				'pl.id',
				'pl.firstname',
				'pl.nickname',
				'pl.lastname'
		));
		$query->from('#__joomleague_person AS pl');
		$query->where('pl.id IN (' . implode(', ',$cid) . ')','pl.published = 1');
		$db->setQuery($query);
		return $db->loadObjectList();
	}


	/**
	 * return list of project teams for select options
	 *
	 * @return array
	 */
	function getProjectTeamList()
	{
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		$query->select(array(
				't.id AS value',
				't.name AS text'
		));
		$query->from('#__joomleague_team AS t');
		$query->join('INNER','#__joomleague_project_team AS tt ON tt.team_id = t.id');
		$query->where('tt.project_id = ' . $this->_project_id);
		$query->order('text ASC');
		$db->setQuery($query);
		return $db->loadObjectList();
	}


	/**
	 * get team name
	 *
	 * @return string
	 */
	function getTeamName($team_id)
	{
		if(!$team_id)
		{
			return '';
		}
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		$query->select('name');
		$query->from('#__joomleague_team');
		$query->where('id = ' . $team_id);
		$db->setQuery($query);
		return $db->loadResult();
	}


	/**
	 * get team name
	 *
	 * @return string
	 */
	function getProjectTeamName($project_team_id)
	{
		if(!$project_team_id)
		{
			return '';
		}
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		$query->select('t.name');
		$query->from('#__joomleague_team AS t');
		$query->join('INNER','#__joomleague_project_team AS pt ON t.id = pt.team_id');
		$query->where('pt.id = ' . $db->Quote($project_team_id));
		$db->setQuery($query);
		return $db->loadResult();
	}
}
