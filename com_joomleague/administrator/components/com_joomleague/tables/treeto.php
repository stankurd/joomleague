<?php
/**
 * Joomleague
 *
 * @copyright	Copyright (C) 2006-2015 joomleague.at. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @link		http://www.joomleague.at
 */

// Check to ensure this file is included in Joomla!
use Joomla\Registry\Registry;

defined('_JEXEC') or die;

// Include library dependencies
jimport('joomla.filter.input');

/**
* Treeto Table class
*/
class TableTreeto extends JLTable
{
	/**
	 * Constructor
	 *
	 * @param object Database connector object
	 */
	public function __construct(&$db) 
	{
		parent::__construct('#__joomleague_treeto', 'id', $db);
	}

	
	public function bind( $array, $ignore = '' )
	{
		if ( key_exists( 'params', $array ) && is_array( $array['params'] ) )
		{
			$registry = new Registry();
			$registry->loadArray( $array['params'] );
			$array['params'] = $registry->toString();
		}
		if ( key_exists( 'comp_params', $array ) && is_array( $array['comp_params'] ) )
		{
			$registry = new Registry();
			$registry->loadArray( $array['comp_params'] );
			$array['comp_params'] = $registry->toString();
		}
    	//print_r( $array );exit;
		return parent::bind( $array, $ignore );
	}
}
