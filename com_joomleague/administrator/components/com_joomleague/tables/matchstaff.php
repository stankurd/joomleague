<?php
/**
 * Joomleague
 *
 * @copyright	Copyright (C) 2006-2015 joomleague.at. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @link		http://www.joomleague.at
 */

// Check to ensure this file is included in Joomla!
use Joomla\CMS\Language\Text;

defined('_JEXEC') or die;

// Include library dependencies
jimport( 'joomla.filter.input' );

/**
* MatchStaff Table class
*/
class TableMatchStaff extends JLTable
{
	/**
	 * Constructor
	 *
	 * @param object Database connector object
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__joomleague_match_staff', 'id', $db);
	}
	

	/**
	 * Overloaded check method to ensure data integrity
	 *
	 * @access public
	 * @return boolean True on success
	 */
	public function check()
	{
		if (!($this->match_id && $this->team_staff_id))
		{
			$this->setError(Text::_('CHECK FAILED'));
			return false;
		}
		return true;
	}
}
