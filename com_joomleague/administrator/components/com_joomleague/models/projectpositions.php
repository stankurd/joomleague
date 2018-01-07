<?php
/**
 * Joomleague
 *
 * @copyright	Copyright (C) 2006-2015 joomleague.at. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @link		http://www.joomleague.at
 */
defined('_JEXEC') or die;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\Utilities\ArrayHelper;

/**
 * ProjectPositions Model
 */
class JoomleagueModelProjectpositions extends JLGModelList
{

	public function __construct($config = array())
	{
		if(empty($config['filter_fields']))
		{
			$config['filter_fields'] = array();
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
	
		// List state information.
		// parent::populateState('po.name','desc');
		
		$value = $this->getUserStateFromRequest($this->context.'.filter_order','filter_order','po.name','string');
		$this->setState('list.ordering',$value);
		
		$value = $this->getUserStateFromRequest($this->context.'.filter_order_Dir','filter_order_Dir','DESC','word');
		$this->setState('list.direction',$value);
		
	}


	protected function getStoreId($id = '')
	{
		return parent::getStoreId($id);
	}


	protected function getListQuery()
	{
		$app = Factory::getApplication();
		$input = $app->input;
		$option = $input->getCmd('option');
		$project_id = $app->getUserState($option.'project');

		// Query
		$db = $this->getDbo();
		$query = $db->getQuery(true);

		$query->select($this->getState('list.select','a.*,a.id AS projectpositionid'));
		$query->from('#__joomleague_project_position AS a');

		$query->select(array('po.name AS names','po.*'));
		$query->join('LEFT','#__joomleague_position AS po ON po.id = a.position_id');

		$query->select(array('pid.name AS parent_name'));
		$query->join('LEFT','#__joomleague_position AS pid ON pid.id = po.parent_id');

		$query->select('(SELECT COUNT(*) FROM #__joomleague_position_eventtype AS pe WHERE pe.position_id = po.id) AS '.$db->QuoteName('countEvents'));
		$query->select('(SELECT COUNT(*) FROM #__joomleague_position_statistic AS ps WHERE ps.position_id = po.id) AS '.$db->QuoteName('countStats'));

		// where
		$query->where('a.project_id = '.$project_id);

		// orderby
		$filter_order = $this->getState('list.ordering');
		$filter_order_Dir = $this->getState('list.direction');
		if($filter_order == 'po.name')
		{
			$query->order('po.parent_id,po.name '.$filter_order_Dir);
		}
		else
		{
			$query->order($filter_order.' '.$filter_order_Dir,'po.name');
		}

		return $query;
	}


	/**
	 * Method to update project positions list
	 *
	 * @access public
	 * @return boolean on success
	 */
	function store($data)
	{
		$app = Factory::getApplication();
		$result = true;
		$peid = (isset($data['project_positionslist']));
		$db = Factory::getDbo();
		if($peid == null)
		{
			$query = $db->getQuery(true);
			$query->delete('#__joomleague_project_position');
			$query->where('project_id = ' . $data['id']);
		}
		else
		{
			$pidArray = $data['project_positionslist'];
			ArrayHelper::toInteger($pidArray);
			$peids = implode(",",$pidArray);

			$query = $db->getQuery(true);
			$query->delete('#__joomleague_project_position');
			$query->where('project_id = ' . $data['id']);
			$query->where('position_id NOT IN (' . $peids . ')');
		}
		try
		{
			$db->setQuery($query);
			$db->execute();
		}
		catch (Exception $e)
		{
			$app->enqueueMessage(Text::_($e->getMessage()), 'error');
			return false;
			return $result;
		}

		if($peid)
		{
			for($x = 0;$x < count($data['project_positionslist']);$x ++)
			{
				$query = "INSERT IGNORE INTO #__joomleague_project_position (project_id,position_id) VALUES ('" . $data['id'] . "','" .
						 $data['project_positionslist'][$x] . "')";
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
		}

		return $result;
	}


	/**
	 * Method to return the positions which are subpositions and are equal to a
	 * sportstype array (id,name)
	 *
	 * @access public
	 * @return array
	 */
	function getSubPositions($sports_type_id = 1)
	{
		$app = Factory::getApplication();
		$sports_type_id = $app->getUserState('com_joomleaguesportstypes',false);

		if($sports_type_id)
		{
			$db = Factory::getDbo();
			$query = $db->getQuery(true);
			$query->select(array(
					'id AS value',
					'name AS text',
					'sports_type_id AS type',
					'parent_id AS parentID'
			));
			$query->from('#__joomleague_position');
			$query->where(array(
					'published = 1',
					'sports_type_id = ' . $sports_type_id
			));
			$query->order('parent_id ASC,name ASC ');
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
			
		}
		else
		{
			return false;
		}

		return $result;
	}


	/**
	 * Method to return the project positions array (id,name)
	 *
	 * @access public
	 * @return array
	 */
	function getProjectPositions()
	{
		$app = Factory::getApplication();
		$project_id = $app->getUserState('com_joomleagueproject');

		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		$query->select(array(
				'p.id AS value',
				'p.name AS text',
				'p.sports_type_id AS type',
				'p.parent_id AS parentID'
		));
		$query->from('#__joomleague_position AS p');

		// join Project-Position table
		$query->join('LEFT','#__joomleague_project_position AS pp ON pp.position_id = p.id');

		$query->where('pp.project_id = ' . $project_id);
		$query->order('p.parent_id ASC,p.name ASC ');
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
	 * return count of projectpositions
	 *
	 * @param	int project_id
	 * @return	int
	 */
	function getProjectPositionsCount($project_id)
	{
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		$query->select('COUNT(*) AS count');
		$query->from('#__joomleague_project_position AS pp');
		$query->join('LEFT', '#__joomleague_project AS p on p.id = pp.project_id');
		$query->where('p.id = '.$project_id);
		$db->setQuery($query);
		return $db->loadResult();
	}
}
