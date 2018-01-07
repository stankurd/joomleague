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

defined('_JEXEC') or die;


/**
 * Divisions Model
 */
class JoomleagueModelDivisions extends JLGModelList
{

	public function __construct($config = array())
	{
		if(empty($config['filter_fields']))
		{
			$config['filter_fields'] = array(
					'ordering','a.ordering',
					'a.shortname','a.name',
					'parent_name','a.id'
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
			$this->context .= '.' . $layout;
		}

		$value = $this->getUserStateFromRequest($this->context.'.filter.search','filter_search');
		$this->setState('filter.search',$value);

		// List state information.
		parent::populateState('a.name','desc');
		
		$value = $this->getUserStateFromRequest($this->context.'.filter.state','filter_state');
		$this->setState('filter.state',$value);
	}


	protected function getStoreId($id = '')
	{
		$id .= ':'.$this->getState('filter.search');
		$id .= ':'.$this->getState('filter.state');

		return parent::getStoreId($id);
	}


	protected function getListQuery()
	{
		$app = Factory::getApplication();
		$input = $app->input;
		$option = $input->getCmd('option');
		$project_id = $app->getUserState($option.'project');
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		$query->select('a.*');
		$query->from('#__joomleague_division AS a');

		// join division table
		$query->select('dvp.name AS parent_name');
		$query->join('LEFT','#__joomleague_division AS dvp ON dvp.id = a.parent_id');

		// join user table
		$query->select('u.name AS editor');
		$query->join('LEFT','#__users AS u ON u.id = a.checked_out');

		// filter - project_id
		$query->where('a.project_id = '.$project_id);
		$search = $this->getState('filter.search');
		if (!empty($search))
		{
			$query->where('LOWER(a.name) LIKE '.$db->Quote('%'.$search.'%'));
		}

		// filter - state
		$filter_state = $this->getState('filter.state');
		if($filter_state)
		{
			if($filter_state == 'P')
			{
				$query->where('a.published = 1');
			}
			elseif($filter_state == 'U')
			{
				$query->where('a.published = 0');
			}
		}

		// filter - order
		$filter_order = $this->state->get('list.ordering','a.name');
		$filter_order_Dir = $this->state->get('list.direction','desc');
		if($filter_order == 'a.ordering')
		{
			$query->order('a.ordering '.$filter_order_Dir);
		}
		else
		{
			$query->order($filter_order.' '.$filter_order_Dir,'a.ordering ');
		}

		return $query;
	}


	/**
	 * Method to return a divisions array (id, name)
	 *
	 * @param int $project_id
	 * @access public
	 * @return array
	 */
	function getDivisions($project_id)
	{
		$app = Factory::getApplication();
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		$query->select(array('id AS value','name AS text'));
		$query->from('#__joomleague_division');
		$query->where('project_id = ' . $project_id);
		$query->order('name ASC');
		$db->setQuery($query);
		
		try
		{
			$result = $db->loadObjectList("value");
		}
		catch (RuntimeException $e)
		{
			$app->enqueueMessage(Text::_($e->getMessage()), 'error');
			return array();
		}
		{
			return $result;
		}
	}


	/**
	 * return count of project divisions
	 *
	 * @param	int project_id
	 * @return	int
	 */
	function getProjectDivisionsCount($project_id)
	{
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		$query->select('COUNT(d.id) AS count');
		$query->from('#__joomleague_division AS d');
		$query->join('LEFT','#__joomleague_project AS p on p.id = d.project_id');
		$query->where('p.id = ' . $project_id);
		$db->setQuery($query);
		return $db->loadResult();
	}
}
