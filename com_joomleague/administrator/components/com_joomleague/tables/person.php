<?php


/**
 * Joomleague
 *
 * @copyright	Copyright (C) 2006-2015 joomleague.at. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @link		http://www.joomleague.at
 */

// Check to ensure this file is included in Joomla!
use Joomla\CMS\Filter\OutputFilter;
use Joomla\CMS\Language\Text;

defined('_JEXEC') or die;

/**
 * Person Table class
 */
class TablePerson extends JLTable
{
	/**
	 * Constructor
	 *
	 * @param object Database connector object
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__joomleague_person', 'id', $db);
	}
	

	/**
	 * Overloaded check method to ensure data integrity
	 *
	 * @access public
	 * @return boolean True on success
	 */
	public function check()
	{
		if (empty($this->firstname) && empty($this->lastname))
		{
			$this->setError(Text::_('ERROR FIRSTNAME OR LASTNAME REQUIRED'));
			return false;
		}
		$parts = array(trim($this->firstname), trim($this->lastname));
		$alias = OutputFilter::stringURLSafe( implode( ' ', $parts ) );
	
		// setting alias
		if (empty($this->alias))
		{
			$this->alias = $alias;
		}
		else {
			$this->alias = OutputFilter::stringURLSafe($this->alias); // make sure the user didn't modify it to something illegal...
		}
		//should check name unicity
		return true;
	}
}
