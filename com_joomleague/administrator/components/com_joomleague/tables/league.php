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
use Joomla\CMS\Filter\OutputFilter;


defined('_JEXEC') or die;

/**
* League Table class
*/
class TableLeague extends JLTable 
{
	/**
	 * Constructor
	 *
	 * @param object Database connector object
	 */
	public function __construct(&$db)
	{
		parent :: __construct('#__joomleague_league', 'id', $db);
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
		
		// setting alias
		if (empty($this->alias))
		{
		    $this->alias = OutputFilter::stringURLSafe($this->name);
		}
		else {
		    $this->alias = OutputFilter::stringURLSafe($this->alias); // make sure the user didn't modify it to something illegal...
		}
		
		// check if name is unique
		$query = $db->getQuery(true);
		$query->select('name');
		$query->from('#__joomleague_league');
		$query->where('name =' . $db->Quote($this->name));
		if(! $isNew)
		{
			$query->where('id NOT LIKE' . $db->Quote($this->id));
		}
		$db->setQuery($query);
		$result = $db->loadColumn();
		
		if($result)
		{
			Factory::getApplication()->enqueueMessage('League already exists','warning');
			return false;
		}
		
		//should check name unicity
		return true;
	}
}
