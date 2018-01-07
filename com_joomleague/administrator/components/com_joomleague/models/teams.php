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
 * Teams Model
 */
class JoomleagueModelTeams extends JLGModelList
{

	public function __construct($config = array())
	{
		if(empty($config['filter_fields']))
		{
			$config['filter_fields'] = array(
					'ordering','a.ordering',
					'a.name','c.name',
					'a.id','a.website',
					'a.middle_name','a.short_name'
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
		$app 	= Factory::getApplication();
		$input = $app->input;
		$option = $input->getCmd('option');
		$clubid = $input->get->getInt('clubid',false);
		
		$filter_order 		= $this->state->get('list.ordering','a.id');
		$filter_order_Dir	= $this->state->get('list.direction','desc');
		$search 	 = $this->getState('filter.search');

		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		$query->select('a.*');
		$query->from('#__joomleague_team AS a');

		// join club table
		$query->select(array('c.name as clubname','c.id AS club_id'));
		$query->join('LEFT','#__joomleague_club AS c ON c.id = a.club_id');

		// join user table
		$query->select('u.name AS editor');
		$query->join('LEFT','#__users AS u ON u.id = a.checked_out');

		// filter - search
		if (!empty($search))
		{
			$searchLength = mb_strlen($search);
			if($searchLength == 1 && preg_match('/[A-Z]/', $search) > 0)
			{
				$query->where('LOWER(a.name) LIKE '.$db->Quote($db->escape($search).'%'));
			} else {
				$query->where('LOWER(a.name) LIKE '.$db->Quote('%'.$db->escape($search).'%'));
			}
		}

		// filter - clubid
		if($clubid)
		{
			$query->where('c.id = '.$clubid);
		}

		// filter - order
		if($filter_order == 'a.ordering')
		{
			$query->order('a.ordering '.$filter_order_Dir);
		}
		else
		{
			$query->order(array($db->escape($filter_order.' '.$filter_order_Dir),'a.ordering '));
		}

		return $query;
	}


	/**
	 * Copy Team data
	 *
	 * @todo check if ordering should change
	 */
	public function copyTeams($cids)
	{
		$result = true;

		$db = Factory::getDbo();

		foreach($cids as $cid)
		{
			$query = $db->getQuery(true);
			$query->select('*');
			$query->from('#__joomleague_team');
			$query->where('id = '.$cid);
			$db->setQuery($query);
			if($object = $db->loadObject())
			{
				$newTeamName = Text::sprintf('!Copy of %1$s',$object->name);
				$query = $db->getQuery(true);
				$query->select('id');
				$query->from('#__joomleague_team');
				$query->where('name = '.$db->Quote($newTeamName));
				$db->setQuery($query);
				$found = $db->loadResult();
				if(!$found)
				{
					$object->name = $newTeamName;
					$object->ordering = (- 10);
					$teamArray = (array) $object;
					unset($teamArray['id']);

					$table = Table::getInstance('Team','Table');
					$result = $table->save($teamArray);

					if(!$result)
					{
						echo $this->getError();
					}
				}
			}
		}

		return $result;
	}
}
