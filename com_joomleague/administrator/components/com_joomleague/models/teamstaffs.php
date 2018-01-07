<?php
/**
 * Joomleague
 *
 * @copyright	Copyright (C) 2006-2015 joomleague.at. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @link		http://www.joomleague.at
 */
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Table\Table;

defined('_JEXEC') or die;


/**
 * TeamStaffs Model
 */
class JoomleagueModelTeamStaffs extends JLGModelList
{

	public function __construct($config = array())
	{
		if(empty($config['filter_fields']))
		{
			$config['filter_fields'] = array(
					'a.lastname','a.firstname',
					'a.nickname','a.project_position_id',
					'a.id','a.birthday',
					'a.country'
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

		$value = $this->getUserStateFromRequest($this->context.'.filter.search','filter_search');
		$this->setState('filter.search',$value);

		// List state information.
		parent::populateState('a.lastname','desc');

	}


	protected function getStoreId($id = '')
	{
		$id .= ':'.$this->getState('filter.search');

		return parent::getStoreId($id);
	}


	protected function getListQuery()
	{
		$app = Factory::getApplication();
		$input = $app->input;
		$option = $input->getCmd('option');
		$project_id = $app->getUserState($option.'project');
		$projectteam_id = $app->getUserState($option.'project_team_id');
		$assign = $this->getState('filter.assign',false);

		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		$query->select($this->getState('list.select','a.*,a.id AS person_id'));
		$query->from('#__joomleague_person AS a');

		if (!$assign) {
			// join team-player table
			$query->select('ts.*,ts.id AS tsid');
			$query->join('INNER','#__joomleague_team_staff AS ts ON ts.person_id = a.id');

			// join users table
			$query->select('u.name AS editor');
			$query->join('LEFT','#__users AS u ON u.id = ts.checked_out');
		}

		$query->where('a.published = 1');

		$search_mode = 'matchfirst';

		// Where
		if (!$assign) {
			$query->where('ts.projectteam_id = '.$projectteam_id);
		}

		// filter-search
		$search = $this->getState('filter.search');
		if (!empty($search))
		{
			$searchLength = mb_strlen($search);
			if($searchLength == 1 && preg_match('/[A-Z]/', $search) > 0)
			{
				$query->where('LOWER(lastname) LIKE '.$db->Quote($search.'%'));
			} else {
				$query->where('LOWER(a.lastname) LIKE '.$db->Quote('%'.$search.'%'));
			}
		}

		// filter-state
		$filter_state = $this->getState('filter.state');
		if($filter_state && !$assign)
		{
			if($filter_state == 'P')
			{
				$query->where('ts.published = 1');
			}
			elseif($filter_state == 'U')
			{
				$query->where('ts.published = 0');
			}
		}

		// Exclude
		if ($assign) {
			// Filter by excluded users
			//$excluded = $this->getState('filter.excluded');
			$excluded = $this->getExcluded();
			if (!empty($excluded)) {
				$query->where('a.id NOT IN ('.implode(',',$excluded).')');
			}
		}

		// Orderby
		$filter_order = $this->state->get('list.ordering','a.lastname');
		$filter_order_Dir = $this->state->get('list.direction','desc');
		if($filter_order == 'a.lastname')
		{
			$query->order('a.lastname '.$filter_order_Dir);
		}
		else
		{
			$query->order($filter_order.' '.$filter_order_Dir,'a.lastname ');
		}

		return $query;
	}


	/**
	 *
	 */
	function getExcluded()
	{
		$app = Factory::getApplication();
		$projectteam_id = $app->getUserState('com_joomleagueproject_team_id');

		$db = Factory::getDbo();
		$query = $db->getQuery(true);

		$query->select($this->getState('list.select','a.id AS person_id'));
		$query->from('#__joomleague_person AS a');
		$query->join('LEFT','#__joomleague_team_staff AS ts ON ts.person_id = a.id');

		$query->where(array('ts.projectteam_id = '.$db->Quote($projectteam_id),'ts.person_id = a.id'));
		$db->setQuery($query);

		$result = $db->loadColumn(0);

		return $result;
	}


	/**
	 * Method to update checked project teams
	 *
	 * @access public
	 * @return boolean on success
	 *
	 */
	function storeshort($cid,$data)
	{
		$result = true;
		$app = Factory::getApplication();
		$db = Factory::getDbo();
		for($x = 0;$x < count($cid);$x ++)
		{
			$query = $db->getQuery(true);
			$query->update('#__joomleague_team_staff');
			$query->set(array(
					'project_position_id = '.$data['project_position_id'.$cid[$x]],
					'checked_out = 0',
					'checked_out_time = 0'
			));
			$query->where('id = '.$cid[$x]);
			try 
			{
			$db->setQuery($query);
			$db->execute();
			}
			catch (Exception $e)
			{
				$app->enqueueMessage(Text::_($e->getMessage()), 'error');
				return false;
			}
		}
		return $result;
	}


	/**
	 * Method to return the teams array (id,name)
	 *
	 * @access public
	 * @return array
	 */
	function getPersons()
	{
		$app = Factory::getApplication();
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		$query->select('id AS value,lastname,nickname,firstname,info,team_id,weight,height,picture,birthday,position_id,notes,nickname,knvbnr,nation');
		$query->from('#__joomleague_person');
		$query->where('team_id = 0');
		$query->where('published = 1');
		$query->order('firstname ASC');
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
	 * Method to return a divisions array (id,name)
	 *
	 * @access public
	 * @return array
	 */
	function getDivisions()
	{
		$app = Factory::getApplication();
		$input = $app->input;
		$option = $input->getCmd('option');
		$project_id = $app->getUserState($option.'project');

		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		$query->select('id AS value, name AS text');
		$query->from('#__joomleague_division');
		$query->where('project_id = '.$project_id);
		$query->order('name ASC');
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
	 * Method to return a positions array (id,position)
	 *
	 * @access public
	 * @return array
	 */
	function getPositions()
	{
		$app = Factory::getApplication();
		$input = $app->input;
		$option = $input->getCmd('option');
		$project_id = $app->getUserState($option.'project');

		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		$query->select('pos.name AS text');
		$query->from('#__joomleague_position AS pos');

		// Project-Position
		$query->select('ppos.id AS value');
		$query->join('INNER','#__joomleague_project_position AS ppos ON ppos.position_id=pos.id');

		$query->where('ppos.project_id = '.$project_id);
		$query->where('pos.persontype = 2');
		$query->order('pos.ordering');
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
		
		foreach($result as $position)
		{
			$position->text = Text::_($position->text);
		}
		return $result;
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
		$query->select('t.id AS value, t.name AS text');
		$query->from('#__joomleague_team AS t');
		// Project-Team
		$query->join('INNER','#__joomleague_project_team AS tt ON tt.team_id=t.id');
		$query->where('tt.project_id = '.$this->_project_id);
		$db->setQuery($query);
		return $db->loadObjectList();
	}


	/**
	 * add the specified persons to team
	 *
	 * @param	array int teamstaff ids
	 * @param	int team id
	 * @return int number of row inserted
	 */
	function storeAssigned($cid,$projectteam_id)
	{
		if(!count($cid) || ! $projectteam_id)
		{
			return 0;
		}

		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		$query->select('pt.id');
		$query->from('#__joomleague_person AS pt');

		// Team-Staff
		$query->join('INNER','#__joomleague_team_staff AS r ON r.person_id = pt.id');

		// Where
		$query->where('r.projectteam_id=' . $projectteam_id);
		$query->where('pt.published = 1');

		$db->setQuery($query);
		$current = $db->loadColumn();

		$added = 0;
		foreach($cid as $pid)
		{
			if(!in_array($pid,$current))
			{
				$tblTeamstaff = Table::getInstance('TeamStaff','Table');
				$tblTeamstaff->person_id = $pid;
				$tblTeamstaff->projectteam_id = $projectteam_id;
				$tblTeamstaff->published = 1;

				$tblProjectTeam = Table::getInstance('ProjectTeam','Table');
				$tblProjectTeam->load($projectteam_id);

				if(!$tblTeamstaff->check())
				{
					$this->setError($tblTeamstaff->getError());
					continue;
				}
				// Get data from person
				$db = Factory::getDbo();
				$query = $db->getQuery(true);
				$query->select('pl.picture, pl.position_id');
				$query->from('#__joomleague_person AS pl');
				// Where
				$query->where('pl.id = '.$db->Quote($pid));
				$query->where('pl.published = 1');
				$db->setQuery($query);
				$person = $db->loadObject();
				if($person)
				{
					$db = Factory::getDbo();
					$query = $db->getQuery(true);
					$query->select('id');
					$query->from('#__joomleague_project_position');
					$query->where('position_id = '.$db->Quote($person->position_id));
					$query->where('project_id = '.$db->Quote($tblProjectTeam->project_id));
					$db->setQuery($query);
					if($resPrjPosition = $db->loadObject())
					{
						$tblTeamstaff->project_position_id = $resPrjPosition->id;
					}

					$tblTeamstaff->picture = $person->picture;
					$tblTeamstaff->projectteam_id = $projectteam_id;
				}
				if(! $tblTeamstaff->store())
				{
					$this->setError($tblTeamstaff->getError());
					continue;
				}
				$added ++;
			}
		}
		return $added;
	}


	/**
	 * remove staffs from team
	 *
	 * @param $cids staff ids
	 * @return int count of staffs removed
	 */
	function remove($cids)
	{
		$count = 0;
		foreach($cids as $cid)
		{
			$object = $this->getTable('teamstaff');
			if($object->canDelete($cid) && $object->delete($cid))
			{
				$count ++;
			}
			else
			{
				$this->setError(Text::sprintf('COM_JOOMLEAGUE_ADMIN_TEAMSTAFFS_MODEL_ERROR_REMOVE_STAFF',$object->getError()));
			}
		}
		return $count;
	}
}
