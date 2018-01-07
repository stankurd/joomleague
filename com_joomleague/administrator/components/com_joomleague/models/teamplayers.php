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
 * TeamPlayers Model
 */
class JoomleagueModelTeamPlayers extends JLGModelList
{

	public function __construct($config = array())
	{
		if(empty($config['filter_fields']))
		{
			$config['filter_fields'] = array(
					'a.lastname','a.nickname',
					'a.firstname','a.birthday',
					'a.person_id','a.country',
					'a.id','tp.project_position_id',
					'tpid','tp.person_id'
			);
		}

		parent::__construct($config);
	}


	protected function populateState($ordering = null,$direction = null)
	{
		$app = Factory::getApplication();
		$option = $app->input->getCmd('option');

		// Adjust the context to support modal layouts.
		if($layout = $app->input->get('layout'))
		{
			$this->context .= '.'.$layout;
		}

		$value = $this->getUserStateFromRequest($this->context.'.filter.search','filter_search');
		$this->setState('filter.search',$value);

		$this->setState('filter.project_id', $app->getUserState($option.'project'));
		$this->setState('filter.projectteam_id', $app->getUserState($option.'project_team_id'));

		// List state information.
		parent::populateState('a.lastname','desc');
	}


	protected function getStoreId($id = '')
	{
		$id .= ':'.$this->getState('filter.search');
		$id .= ':'.$this->getState('filter.project_id');
		$id .= ':'.$this->getState('filter.projectteam_id');

		return parent::getStoreId($id);
	}


	protected function getListQuery()
	{
		$app = Factory::getApplication();
		$input = $app->input;
		$option = $input->getCmd('option');

		$project_id = $this->getState('filter.project_id');
		$projectteam_id = $this->getState('filter.projectteam_id');
		$assign = $this->getState('filter.assign',false);

		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		$query->select($this->getState('list.select','a.*,a.id AS person_id'));
		$query->from('#__joomleague_person AS a');

		if (!$assign) {
			// join team-player table
			$query->select('tp.*,tp.id AS tpid');
			$query->join('INNER','#__joomleague_team_player AS tp ON tp.person_id = a.id');

			// join users table
			$query->select('u.name AS editor');
			$query->join('LEFT','#__users AS u ON u.id = tp.checked_out');
		}

		$query->where('a.published = 1');

		// filter-search
		$search = $this->getState('filter.search');
		if (!empty($search))
		{
			$searchLength = mb_strlen($search);
			if($searchLength == 1 && preg_match('/[A-Z]/', $search) > 0)
			{
				$query->where(
						'(LOWER(a.lastname) LIKE '.$db->Quote($search.'%').' OR LOWER(a.firstname) LIKE '.$db->Quote($search.'%') .
						' OR LOWER(a.nickname) LIKE '.$db->Quote($search.'%').')');
			}
			else
			{
				$query->where(
						'(LOWER(a.lastname) LIKE '.$db->Quote('%'.$search.'%').' OR LOWER(a.firstname) LIKE '.$db->Quote(
								'%'.$search.'%').' OR LOWER(a.nickname) LIKE '.$db->Quote('%'.$search.'%').')');
			}
		}

		// Where
		if (!$assign) {
			$query->where('tp.projectteam_id = '.$projectteam_id);
		}

		// filter-state
		$filter_state = $this->getState('filter.state');
		if($filter_state && !$assign)
		{
			if($filter_state == 'P')
			{
				$query->where('tp.published = 1');
			}
			elseif($filter_state == 'U')
			{
				$query->where('tp.published = 0');
			}
		}

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
			$query->order($filter_order.' '.$filter_order_Dir,'.a.lastname ');
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
		$query->join('LEFT','#__joomleague_team_player AS tp ON tp.person_id = a.id');

		$query->where(array('tp.projectteam_id = '.$db->Quote($projectteam_id),'tp.person_id = a.id'));
		$db->setQuery($query);

		$result = $db->loadColumn(0);

		return $result;
	}


	/**
	 * Method to update checked project teams
	 *
	 * @access public
	 * @return boolean on success
	 */
	function storeshort($cid,$data)
	{
		$result = true;
		$app = Factory::getApplication();
		$db = Factory::getDbo();

		for($x = 0;$x < count($cid);$x ++)
		{
			$query = $db->getQuery(true);
			$query->update('#__joomleague_team_player');
			$query->set(array(
					'project_position_id = '.$data['project_position_id'.$cid[$x]],
					'checked_out = 0','checked_out_time = 0'
			));
			$query->where('id = '.$cid[$x]);
			try{
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
	 * Method to update checked project teams
	 *
	 * @access public
	 * @return boolean on success
	 */
	function storeshortAjax($name,$value,$pk)
	{
		
		$result = true;
		$app = Factory::getApplication();
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		$tblTeamplayer = Table::getInstance('TeamPlayer','Table');
		$tblTeamplayer->id = $pk;
		$tblTeamplayer->$name = $value;
		try
		{
			$tblTeamplayer->store();
		}
		catch (Exception $e)
		{
			$app->enqueueMessage(Text::_($e->getMessage()), 'error');
			return false;
		}

		return $result;
	}


	/**
	 * Method to return the players array (projectid,teamid)
	 *
	 * @access public
	 * @return array
	 */
	function getPersons()
	{
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		$query->select('id AS value,lastname,firstname,info,weight,height,picture,birthday,notes,nickname,knvbnr,country');
		$query->from('#__joomleague_person');
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

		$project_id = $app->getUserState($option . 'project');

		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		$query->select('id AS value, name AS text');
		$query->from('#__joomleague_division');
		$query->where('project_id = ' . $project_id);
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

		$project_id = $app->getUserState($option . 'project');

		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		$query->select('p.name AS text');
		$query->from('#__joomleague_position AS p');
		// Project-Position
		$query->select('pp.id AS value');
		$query->join('LEFT','#__joomleague_project_position AS pp ON pp.position_id=p.id');
		// Where
		$query->where('pp.project_id = ' . $project_id);
		$query->where('p.persontype = 1');
		$query->order('p.ordering');
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
		$query->select('t.id AS value,t.name AS text');
		$query->from('#__joomleague_team AS t');
		// Project-Team
		$query->join('INNER','#__joomleague_project_team AS tt ON tt.team_id = t.id');
		$query->where('tt.project_id = ' . $this->_project_id);

		$db->setQuery($query);
		return $db->loadObjectList();
	}

	/**
	 * add the specified persons to team
	 *
	 * @param
	 *        	array int player ids
	 * @param
	 *        	int team id
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
		$query->select('p.id');
		$query->from('#__joomleague_person AS p');
		// Team-Player
		$query->join('INNER','#__joomleague_team_player AS tp ON tp.person_id = p.id');
		$query->where('tp.projectteam_id = ' . $db->Quote($projectteam_id));
		$query->where('p.published = 1');
		$db->setQuery($query);

		$current = $db->loadColumn();
		$added = 0;

		foreach($cid as $pid)
		{
			if(!in_array($pid,$current))
			{

				$tblTeamplayer = Table::getInstance('TeamPlayer','Table');
				$tblTeamplayer->person_id = $pid;
				$tblTeamplayer->projectteam_id = $projectteam_id;
				$tblTeamplayer->published = 1;

				$tblProjectTeam = Table::getInstance('ProjectTeam','Table');
				$tblProjectTeam->load($projectteam_id);

				if(! $tblTeamplayer->check())
				{
					$this->setError($tblTeamplayer->getError());
					continue;
				}
				// Get data from player
				$query = $db->getQuery(true);
				$query->select('pl.picture, pl.position_id');
				$query->from('#__joomleague_person AS pl');
				$query->where('pl.id=' . $db->Quote($pid));
				$db->setQuery($query);
				$person = $db->loadObject();
				if($person)
				{
					$query = $db->getQuery(true);
					$query->select('id');
					$query->from('#__joomleague_project_position');
					$query->where('position_id = ' . $db->Quote($person->position_id));
					$query->where('project_id = ' . $db->Quote($tblProjectTeam->project_id));
					$db->setQuery($query);
					if($resPrjPosition = $db->loadObject())
					{
						$tblTeamplayer->project_position_id = $resPrjPosition->id;
					}

					$tblTeamplayer->picture = $person->picture;
					$tblTeamplayer->projectteam_id = $projectteam_id;
				}
				$query = $db->getQuery(true);
				$query->select('MAX(ordering) AS count');
				$query->from('#__joomleague_team_player');
				$db->setQuery($query);
				$tp = $db->loadObject();

				$tblTeamplayer->ordering = (int) $tp->count + 1;
				if(! $tblTeamplayer->store())
				{
					$this->setError($tblTeamplayer->getError());
					continue;
				}
				$added ++;
			}
		}

		return $added;
	}


	/**
	 * remove specified players from team
	 *
	 * @param $cids player ids
	 * @return int count of removed
	 */
	function remove($cids)
	{
		$count = 0;
		foreach($cids as $cid)
		{
			$object = $this->getTable('teamplayer');
			if($object->canDelete($cid) && $object->delete($cid))
			{
				$count ++;
			}
			else
			{
				$this->setError(Text::sprintf('COM_JOOMLEAGUE_ADMIN_TEAMSTAFFS_MODEL_ERROR_REMOVE_TEAMPLAYER',$object->getError()));
			}
		}
		return $count;
	}
}
