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
 * Positions Model
 */
class JoomleagueModelPositions extends JLGModelList
{

	public function __construct($config = array())
	{
		if(empty($config['filter_fields']))
		{
			$config['filter_fields'] = array(
					'ordering','a.ordering',
					'a.persontype','a.sports_type_id',
					'a.parent_id','a.name',
					'a.id'
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
		parent::populateState('a.name','desc');
		
		$value = $this->getUserStateFromRequest($this->context.'.filter.sportstype','filter_sportstype');
		$this->setState('filter.sportstype',$value);
		
		$value = $this->getUserStateFromRequest($this->context.'.filter.state','filter_state');
		$this->setState('filter.state',$value);
	}


	protected function getStoreId($id = '')
	{
		$id .= ':'.$this->getState('filter.search');
		$id .= ':'.$this->getState('filter.sportstype');
		$id .= ':'.$this->getState('filter.state');

		return parent::getStoreId($id);
	}

	protected function getListQuery()
	{
		// Create a new query object.
	    $db = Factory::getDbo();
		$query = $db->getQuery(true);
		$user = Factory::getUser();
		$app = Factory::getApplication();
		$input = $app->input;

		// Select the required fields from the table.
		$query->select($this->getState('list.select','a.*'));
		$query->from('#__joomleague_position AS a');

		// join SportsType table
		$query->select(array('st.name AS sportstype'));
		$query->join('LEFT','#__joomleague_sports_type AS st ON st.id = a.sports_type_id');

		// join Position table
		$query->select(array('pop.name AS parent_name'));
		$query->join('LEFT','#__joomleague_position AS pop ON pop.id = a.parent_id');

		// join Users table
		$query->select('u.name AS editor');
		$query->join('LEFT','#__users AS u ON u.id = a.checked_out');

		// counts
		$query->select('(SELECT COUNT(*) FROM #__joomleague_position_eventtype WHERE position_id = a.id) AS '.$db->QuoteName('countEvents'));
		$query->select('(SELECT COUNT(*) FROM #__joomleague_position_statistic WHERE position_id = a.id) AS '.$db->QuoteName('countStats'));

		// filter - sportstype
		$sportstype = $this->getState('filter.sportstype');
		if($sportstype > 0)
		{
			$query->where('a.sports_type_id = '.$db->Quote($sportstype));
		}

		// filter - search
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
			} elseif ($published === '') {
				$query->where('(a.published = 0 OR a.published = 1)');
			}
		}

		// filter - order
		$filter_order = $this->state->get('list.ordering','a.id');
		$filter_order_Dir = $this->state->get('list.direction','desc');
		if($filter_order == 'a.ordering')
		{
			$query->order(array('a.parent_id ASC','a.ordering '.$filter_order_Dir));
		}
		else
		{
			$query->order(array('a.parent_id ASC',$filter_order.' '.$filter_order_Dir,'a.ordering '));
		}

		return $query;
	}


	/**
	 * Method to return the positions array (id,name)
	 *
	 * @access public
	 * @return array
	 *
	 * @todo Fix
	 * it's possible to select same position as parent,
	 * but doing so will cause problems
	 */
	function getParentsPositions()
	{
		$app = Factory::getApplication();
		$input = $app->input;
		$option = $input->getCmd('option');
		$project_id = $app->getUserState($option.'project');

		// get positions already in project for parents list
		// support only 2 sublevel, so parent must not have parents themselves
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		$query->select(array('pos.id AS value','pos.name AS text'));
		$query->from('#__joomleague_position AS pos');
		$query->where('pos.parent_id = 0');
		$query->order('pos.ordering ASC');
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
	 * Method to update checked persons
	 *
	 * @access public
	 * @return boolean on success
	 */
	function storeshort($cid,$post)
	{
		$result = true;
		$db = Factory::getDbo();
		$app = Factory::getApplication();
		$input = $app->input;

		for($x = 0;$x < count($cid);$x ++)
		{
			if($post['parent_id'.$cid[$x]])
			{
				$parentId = $post['parent_id'.$cid[$x]];
			}
			else
			{
				$parentId = 0;
			}

			$query = $db->getQuery(true);
			$query->update('#__joomleague_position');
			$query->set(array('parent_id = '.$parentId,'checked_out = 0','checked_out_time = 0'));
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
}
