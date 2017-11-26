<?php
/**
 * @copyright	Copyright (C) 2005-2016 joomleague.at. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 * Joomla! is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */

// Check to ensure this file is included in Joomla!
use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;

defined('_JEXEC') or die;

/**
 * Model-Ajax
 */
class JoomleagueModelAjax extends BaseDatabaseModel
{
	function getProjectsOptions($season_id = 0, $league_id = 0, $ordering = 0)
	{
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		$query->select(array('p.id AS value','p.name AS text'));
		$query->from('#__joomleague_project AS p');
		
		// join season table
		$query->select('s.name AS season_name');
		$query->join('INNER','#__joomleague_season AS s on s.id = p.season_id');
		
		// join league table
		$query->select('l.name AS league_name');
		$query->join('INNER','#__joomleague_league AS l on l.id = p.league_id');
		
		$query->where('p.published = 1');

		if ($season_id) {
			$query->where('p.season_id = '. $season_id);
		}
		if ($league_id) {
			$query->where('p.league_id = '. $league_id);
		}
	
		switch ($ordering) 
		{
			
			case 1:
				$order = 'p.ordering DESC';				
			break;
			
			case 2:
				$order = array('s.ordering ASC','l.ordering ASC','p.ordering ASC');				
			break;
			
			case 3:
				$order = array('s.ordering DESC','l.ordering DESC','p.ordering DESC');				
			break;
			
			case 4:
				$order = 'p.name ASC';				
			break;
			
			case 5:
				$order = 'p.name DESC';				
			break;
			
			case 0:
			default:
				$order = 'p.ordering ASC';				
			break;
		}
		
		$query->order($order);
		$db->setQuery($query);
		$result = $db->loadObjectList();
		return $result;
	}
}