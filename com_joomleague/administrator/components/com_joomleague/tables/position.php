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
use Joomla\CMS\Language\Text;


defined('_JEXEC') or die;

// Include library dependencies
jimport('joomla.filter.input');

/**
* Position Table class
*/
class TablePosition extends JLTable
{
	/**
	 * Constructor
	 *
	 * @param object Database connector object
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__joomleague_position', 'id', $db);
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
		
		if (empty($this->name)) {
			$this->setError(Text::_('ERROR NAME REQUIRED'));
			return false;
		}
		// setting alias
		if (empty($this->alias))
		{
		    $this->alias = OutputFilter::stringURLSafe($this->name);
		}
		else {
		    $this->alias = OutputFilter::stringURLSafe($this->alias); // make sure the user didn't modify it to something illegal...
		}
		
		// check if Position is unique by checking:
		// name+parent_id+sports_type_id+persontype
		$query = $db->getQuery(true);
		$query->select('name');
		$query->from('#__joomleague_position');
		$query->where('name =' . $db->Quote($this->name));
		$query->where('parent_id = ' . $db->Quote($this->parent_id));
		$query->where('sports_type_id = ' . $db->Quote($this->sports_type_id));
		$query->where('persontype = ' . $db->Quote($this->persontype));
		if(!$isNew)
		{
			$query->where('id NOT LIKE' . $db->Quote($this->id));
		}
		$db->setQuery($query);
		$result = $db->loadColumn();
		
		if($result)
		{
			Factory::getApplication()->enqueueMessage('Position already exists','warning');
			return false;
		}
		
		return true;
	}
}
