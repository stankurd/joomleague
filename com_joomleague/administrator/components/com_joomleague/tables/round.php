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

defined('_JEXEC') or die;

// Include library dependencies
jimport( 'joomla.filter.input' );

/**
 * Round Table class
 *
 * @author Marco Vaninetti <martizva@tiscali.it>
 */

class TableRound extends JLTable
{
	/**
	 * Constructor
	 *
	 * @param object Database connector object
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__joomleague_round', 'id', $db);
	}
	

	/**
	 * Overloaded check method to ensure data integrity
	 *
	 * @access public
	 * @return boolean True on success
	 */
	public function check()
	{
		// setting alias
		if ( empty( $this->alias ) )
		{
		    $this->alias = OutputFilter::stringURLSafe( $this->name );
		}
		else {
		    $this->alias = OutputFilter::stringURLSafe( $this->alias ); // make sure the user didn't modify it to something illegal...
		}
		//should check name unicity
		return true;
	}
}
