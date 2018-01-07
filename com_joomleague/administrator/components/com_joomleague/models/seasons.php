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
 * Seasons Model
 */
class JoomleagueModelSeasons extends JLGModelList
{

	public function __construct($config = array())
	{
		if(empty($config['filter_fields']))
		{
			$config['filter_fields'] = array(
					'ordering','a.ordering',
					'a.name','a.published',
					'published','a.id'
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

		$search = $this->getUserStateFromRequest($this->context.'.filter.search','filter_search');
		$this->setState('filter.search',$search);

		// List state information.
		parent::populateState('a.name','desc');
		
		$published = $this->getUserStateFromRequest('com_joomleague.s_filter_state','filter_state','');
		$this->setState('filter.state',$published);
	}


	protected function getStoreId($id = '')
	{
		$id .= ':'.$this->getState('filter.search');
		$id .= ':'.$this->getState('filter.published');

		return parent::getStoreId($id);
	}


	protected function getListQuery()
	{
		$app = Factory::getApplication();
		$input = $app->input;

		$filter_order = $this->state->get('list.ordering','a.id');
		$filter_order_Dir = $this->state->get('list.direction','desc');
		$filter_state = $this->getState('filter.state');
		$search = $this->getState('filter.search');
		
		$db = Factory::getDbo();
		$query = $db->getQuery(true);

		$query->select($this->getState('list.select','a.*'));
		$query->from('#__joomleague_season AS a');

		// join users table
		$query->select('u.name AS editor');
		$query->join('LEFT','#__users AS u ON u.id = a.checked_out');

		// filter - search
		if($search)
		{
			$query->where('LOWER(a.name) LIKE '.$db->Quote('%'.$search.'%'));
		}

		// filter - state
		if ($filter_state)
		{
			if ($filter_state == 'P')
			{
				$query->where('a.published = 1');
			}
			elseif ($filter_state == 'U' )
			{
				$query->where('a.published = 0');
			}
			elseif ($filter_state == 'A' )
			{
				$query->where('a.published = 2');
			}
			elseif ($filter_state == 'T' )
			{
				$query->where('a.published = -2');
			}
		}

		// filter - order
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


	/**
	 * Method to return a season array (id, name)
	 *
	 * @access public
	 * @return array seasons
	 */
	function getSeasons()
	{
		$app = Factory::getApplication();
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		$query->select(array('a.id','a.name'));
		$query->from('#__joomleague_season AS a');
		$query->where('a.published = 1');
		$query->order('a.name DESC');
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
