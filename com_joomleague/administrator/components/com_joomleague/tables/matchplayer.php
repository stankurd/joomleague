<?php
/**
 * Joomleague
 *
 * @copyright	Copyright (C) 2006-2015 joomleague.at. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @link		http://www.joomleague.at
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die;

// Include library dependencies
jimport( 'joomla.filter.input' );

/**
* MatchPlayer Table class
*/
class TableMatchPlayer extends JLTable
{
	/**
	 * Constructor
	 *
	 * @param object Database connector object
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__joomleague_match_player', 'id', $db);
	}

	
	/**
	 * Overloaded check method to ensure data integrity
	 *
	 * @access public
	 * @return boolean True on success
	 */
	public function check()
	{
		if (!($this->match_id && $this->teamplayer_id))
		{
			$this->setError(JText::_('CHECK FAILED'));
			return false;
		}
		return true;
	}
}
