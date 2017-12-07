<?php
/**
 * Joomleague
 *
 * @copyright	Copyright (C) 2006-2015 joomleague.at. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @link		http://www.joomleague.at
 */
use Joomla\CMS\Factory;

defined('_JEXEC') or die;


/**
 * Eventtypes Model
 */
class JoomleagueModelEventtypes extends JLGModelList
{

	public function __construct($config = array())
	{
		if(empty($config['filter_fields']))
		{
			$config['filter_fields'] = array(
					'ordering','a.ordering',
					'a.name','a.id',
					'a.sports_type_id'
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
		$app = Factory::getApplication();
		$input = $app->input;

		// Query
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		$query->select($this->getState('list.select','a.*'));
		$query->from('#__joomleague_eventtype AS a');

		// join Sportstype table
		$query->select('st.name AS sportstype');
		$query->join('LEFT','#__joomleague_sports_type AS st ON st.id = a.sports_type_id');

		// join User table
		$query->select('u.name AS editor');
		$query->join('LEFT','#__users AS u ON u.id = a.checked_out');

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

		// filter - sportsType
		$sportstype = $this->getState('filter.sportstype');
		if($sportstype > 0)
		{
			$query->where('a.sports_type_id = '.$sportstype);
		}

		// filter - search
		$search = $this->getState('filter.search');
		if($search)
		{
			$query->where('LOWER(a.name) LIKE '.$db->Quote('%'.$search.'%'));
		}

		// filter - order
		$filter_order = $this->state->get('list.ordering','a.id');
		$filter_order_Dir = $this->state->get('list.direction','desc');
		if($filter_order == 'a.ordering')
		{
			$query->order('a.ordering '.$filter_order_Dir);
		}
		else
		{
			$query->order($filter_order.' '.$filter_order_Dir,'a.ordering');
		}

		return $query;
	}
}
