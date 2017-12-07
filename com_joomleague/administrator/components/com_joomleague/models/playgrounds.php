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
 * Playgrounds Model
 */
class JoomleagueModelPlaygrounds extends JLGModelList
{

	public function __construct($config = array())
	{
		if(empty($config['filter_fields']))
		{
			$config['filter_fields'] = array(
					'ordering','a.ordering',
					'a.name','a.short_name','club',
					'a.id','a.max_visitors'
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
		parent::populateState('a.name','desc');
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

		$db = Factory::getDbo();
		$query = $db->getQuery(true);

		// Select the required fields from the table.
		$query->select($this->getState('list.select','a.*'));
		$query->from('#__joomleague_playground AS a');

		// join club table
		$query->select('c.name As club');
		$query->join('LEFT','#__joomleague_club AS c ON c.id = a.club_id');

		// join users table
		$query->select('u.name AS editor');
		$query->join('LEFT','#__users AS u ON u.id = a.checked_out');

		// filter - search
		$search = $this->getState('filter.search');
		if(!empty($search))
		{
			$searchLength = mb_strlen($search);
			if($searchLength == 1 && preg_match('/[A-Z]/', $search) > 0)
			{
				$query->where('LOWER(a.name) LIKE '.$db->Quote($db->escape($search).'%'));
			} else {
				$query->where('LOWER(a.name) LIKE '.$db->Quote('%'.$db->escape($search).'%'));
			}
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
			$query->order(array($filter_order.' '.$filter_order_Dir,'a.ordering '));
		}

		return $query;
	}
}
