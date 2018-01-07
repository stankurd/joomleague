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
 * Projectreferees Model
 */
class JoomleagueModelProjectReferees extends JLGModelList
{

	public function __construct($config = array())
	{
		if(empty($config['filter_fields']))
		{
			$config['filter_fields'] = array(
					'a.lastname','a.firstname',
					'a.nickname','a.birthday',
					'a.country','a.id',
					'pref.project_position_id'
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

		$db = $this->getDbo();
		$query = $db->getQuery(true);

		$query->select($this->getState('list.select','a.*','a.id AS projectteamid'));
		$query->from('#__joomleague_person AS a');

		if (!$assign) {
			$query->select(array('pref.*'));
			$query->join('LEFT','#__joomleague_project_referee AS pref on pref.person_id = a.id');

			$query->select(array('u.name AS editor'));
			$query->join('LEFT','#__users AS u ON u.id = pref.checked_out');
		}

		$query->where('a.published = 1');

		if (!$assign) {
			$query->where('pref.project_id =' . $project_id);
		}

		$search = $this->getState('filter.search');
		if(!empty($search))
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

		// Exclude
		if ($assign) {
			// Filter by excluded users
			//$excluded = $this->getState('filter.excluded');
			$excluded = $this->getExcluded();
			if (!empty($excluded)) {
				$query->where('a.id NOT IN ('.implode(',',$excluded).')');
			}
		}

		$filter_order = $this->state->get('list.ordering','a.lastname');
		$filter_order_Dir = $this->state->get('list.direction','desc');
		if($filter_order == 'a.lastname')
		{
			$query->order('a.lastname '.$filter_order_Dir);
		}
		else
		{
			$query->order(array($filter_order.' '.$filter_order_Dir,'a.lastname '));
		}

		return $query;
	}


	/**
	 *
	 */
	function getExcluded()
	{
		$app = Factory::getApplication();
		$input = $app->input;
		$option = $input->getCmd('option');
		$projectteam_id = $app->getUserState('com_joomleagueproject_team_id');
		$project_id = $app->getUserState($option.'project');

		$db = Factory::getDbo();
		$query = $db->getQuery(true);

		$query->select($this->getState('list.select','a.id AS person_id'));
		$query->from('#__joomleague_person AS a');
		$query->join('LEFT','#__joomleague_project_referee AS pr ON pr.person_id = a.id');

		$query->where(array('pr.project_id = '.$db->Quote($project_id),'pr.person_id = a.id'));
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
	function saveshort($cid,$data)
	{
		$result = true;
		$record = Table::getInstance('ProjectReferee','Table');
		for($x = 0;$x < count($cid);$x ++)
		{
			$record->id = $cid[$x];
			$record->project_position_id = $data['project_position_id' . $cid[$x]];
			$record->store();
			if(! $record->check())
			{
				$this->setError($record->getError());
				$result = false;
			}
			if(! $record->store())
			{
				$this->setError($record->getError());
				$result = false;
			}
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
		$app = Factory::getApplication();
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		$query = '	SELECT	id AS value,
				lastname,
				firstname,
				info,
				weight,
				height,
				picture,
				birthday,
				notes,
				nickname,
				knvbnr,
				country,
				phone,
				mobile,
				email
				FROM #__joomleague_person
				WHERE published = 1
				ORDER BY lastname ASC ';
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
		$db = Factory::getDbo();
		$query = $db->getQuery(true);		
		$input = $app->input;
		$option = $input->getCmd('option');
		$project_id = $app->getUserState($option . 'project');
		$query = '	SELECT	pp.id AS value,
				name AS text

				FROM #__joomleague_position AS p
				LEFT JOIN #__joomleague_project_position AS pp ON pp.position_id=p.id
				WHERE pp.project_id=' . $project_id . '
						ORDER BY ordering ';
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
	 * Method to return a positions array of referees (id,position)
	 *
	 * @access public
	 * @return array
	 */
	function getRefereePositions()
	{
		$app = Factory::getApplication();
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		$input = $app->input;
		$option = $input->getCmd('option');
		$project_id = $app->getUserState($option . 'project');
		$query = '	SELECT	ppos.id AS value,
				pos.name AS text
				FROM #__joomleague_position AS pos
				INNER JOIN #__joomleague_project_position AS ppos ON pos.id=ppos.position_id
				WHERE ppos.project_id=' . $db->Quote($project_id) . ' AND pos.persontype=3';
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
	 * add the specified persons to team
	 *
	 * @param
	 *        	array int person ids
	 * @return int number of row inserted
	 */
	function storeAssigned($cid,$project_id)
	{
		$app = Factory::getApplication();
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		if(! count($cid))
		{
			return 0;
		}
		$query = '	SELECT	pt.id
				FROM #__joomleague_person AS pt
				INNER JOIN #__joomleague_project_referee AS r ON r.person_id=pt.id
				WHERE r.project_id=' . $db->Quote($project_id) . '
						AND pt.published = 1';
		$db->setQuery($query);
		$current = $db->loadColumn();

		$added = 0;
		foreach($cid as $pid)
		{
			if((! isset($current)) || (! in_array($pid,$current)))
			{
				$new = Table::getInstance('ProjectReferee','Table');
				$new->person_id = $pid;
				$new->project_id = $project_id;
				$new->published = 1;

				if(! $new->check())
				{
					$this->setError($new->getError());
					continue;
				}
				// Get data from person
				$query = "SELECT picture FROM #__joomleague_person AS pl WHERE pl.id='$pid' AND pl.published = 1";
				$db->setQuery($query);
				$player = $db->loadObject();
				if($player)
				{
					$new->picture = $player->picture;
				}
				if(! $new->store())
				{
					$this->setError($new->getError());
					continue;
				}
				$added ++;
			}
		}
		return $added;
	}

	/**
	 * remove the specified projectreferees from project
	 *
	 * @param
	 *        	array projectreferee ids
	 * @return int number of row removed
	 */
	function unassign($cid)
	{
		$app = Factory::getApplication();
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		if(! count($cid))
		{
			// no referees were selected
			return false;
		}

		$removed = 0;
		for($x = 0;$x < count($cid);$x ++)
		{
			$query->delete('#__joomleague_project_referee');
			$query->where('id = ' . $cid[$x]);
			try
			{
				$db->setQuery($query);
				$db->execute();
			}
			catch (Exception $e)
			{
				$app->enqueueMessage(Text::_($e->getMessage()), 'error');
				continue;
			}
			$removed ++;
		}
		return $removed;
	}

	/**
	 * return count of projectreferees
	 *
	 * @param
	 *        	int project_id
	 * @return int
	 */
	function getProjectRefereesCount($project_id)
	{
		$app = Factory::getApplication();
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		$query = 'SELECT count(*) AS count
				FROM #__joomleague_project_referee AS pr
				JOIN #__joomleague_project AS p on p.id = pr.project_id
				WHERE p.id=' . $project_id;
		$db->setQuery($query);
		return $db->loadResult();
	}
}
