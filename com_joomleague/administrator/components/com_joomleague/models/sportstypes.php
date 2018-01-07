<?php
/**
 * Joomleague
 *
 * @copyright	Copyright (C) 2006-2015 joomleague.at. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @link		http://www.joomleague.at
 */

// Check to ensure this file is included in Joomla!
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;

defined('_JEXEC') or die;

/**
 * Sportstypes Model
 */
class JoomleagueModelSportsTypes extends JLGModelList
{

	public function __construct($config = array())
	{
		if(empty($config['filter_fields']))
		{
			$config['filter_fields'] = array(
				'ordering','a.ordering',
				'a.name','a.id'
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

		$filter_order = $this->state->get('list.ordering','a.id');
		$filter_order_Dir = $this->state->get('list.direction','desc');
		$search = $this->getState('filter.search');

		// Select the required fields from the table.
		$db = Factory::getDbo();
		$query = $db->getQuery(true);

		$query->select($this->getState('list.select','a.*'));
		$query->from('#__joomleague_sports_type AS a');

		// join users table
		$query->select('u.name AS editor');
		$query->join('LEFT','#__users AS u ON u.id = a.checked_out');

		// filter-search
		if(!empty($search))
		{
			if (stripos($search, 'id:') === 0) {
				$query->where('a.id = '.(int) substr($search, 3));
			} else {
				$searchLength = mb_strlen($search);
				if($searchLength == 1 && preg_match('/[A-Z]/', $search) > 0)
				{
					$query->where('LOWER(a.name) LIKE '.$db->Quote($db->escape($search).'%'));
				} else {
					$query->where('LOWER(a.name) LIKE '.$db->Quote('%'.$db->escape($search).'%'));
				}
			}
		}

		// filter - order
		if($filter_order == 'a.ordering')
		{
			$query->order(array($db->escape('a.ordering '.$filter_order_Dir)));
		}
		else
		{
			$query->order(array($db->escape($filter_order.' '.$filter_order_Dir),'a.ordering'));
		}

		return $query;
	}

	/**
	 * Method to return a sportsTypes array (id,name)
	 *
	 * @access public
	 * @return array
	 */
	public static function getSportsTypes()
	{
		$app = Factory::getApplication();
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		$query->select(array('id','name'));
		$query->from('#__joomleague_sports_type');
		$query->order('name ASC');
		$db->setQuery($query);
		/**if($db->getErrorNum())
		{
		    return array();
		}*/
		try {
		    $sportTypes = $db->loadObjectList();
		} catch (Exception $e) {
		    $app->enqueueMessage(Text::_($e->getMessage()), 'error');
		    return array();
		}		
		//return $sportTypes;


		// include language-file for SportTypes
		$lang = Factory::getLanguage();
		$lang->load('com_joomleague_sport_types',JPATH_ADMINISTRATOR);

		foreach($sportTypes as $sportstype)
		{
			$sportstype->name = Text::_($sportstype->name);
		}
		return $sportTypes;
	
}
}