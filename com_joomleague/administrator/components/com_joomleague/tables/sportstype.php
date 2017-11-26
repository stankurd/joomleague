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

defined('_JEXEC') or die;

// Include library dependencies
jimport('joomla.filter.input');

/**
* Sportstype Table class
*/
class TableSportsType extends JLTable
{
	/**
	 * Constructor
	 *
	 * @param object Database connector object
	 */
	public function __construct(&$db) {
		parent::__construct('#__joomleague_sports_type', 'id', $db);
	}

	
	/**
	 * Overloaded check method to ensure data integrity
	 *
	 * @access public
	 * @return boolean True on success
	 */
	public function check()
	{
		$db = Factory::getDbo();
		$isNew = ($this->id == 0);

		// check if name is unique
		$query = $db->getQuery(true);
		$query->select('name');
		$query->from('#__joomleague_sports_type');
		$query->where('name ='.$db->Quote($this->name));
		if(!$isNew)
		{
			$query->where('id NOT LIKE'.$db->Quote($this->id));
		}
		$db->setQuery($query);
		$result = $db->loadColumn();

		if($result)
		{
			$this->setError('Sportstype already exists');
			return false;
		}
		
		return true;
	}
}
