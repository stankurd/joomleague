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

// Include library dependencies
jimport('joomla.filter.input');

/**
 * EventType Table class
 */
class TableEventtype extends JLTable
{
	/**
	 * Constructor
	 *
	 * @param object Database connector object
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__joomleague_eventtype', 'id', $db);
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
		if ( empty( $this->alias ) )
		{
			$this->alias = OutputFilter::stringURLSafe($this->name);
		}
		else {
		    $this->alias = OutputFilter::stringURLSafe($this->alias); // make sure the user didn't modify it to something illegal...
		}
		
		// check if EventType is unique by checking: name+parent+sports_type_id
		$query = $db->getQuery(true);
		$query->select('name');
		$query->from('#__joomleague_eventtype');
		$query->where('name =' . $db->Quote($this->name));
		$query->where('parent = ' . $db->Quote($this->parent));
		$query->where('sports_type_id = ' . $db->Quote($this->sports_type_id));
		if(! $isNew)
		{
			$query->where('id NOT LIKE' . $db->Quote($this->id));
		}
		$db->setQuery($query);
		$result = $db->loadColumn();
		
		if($result)
		{
			Factory::getApplication()->enqueueMessage('EventType already exists','warning');
			return false;
		}
		
		return true;
	}
}
