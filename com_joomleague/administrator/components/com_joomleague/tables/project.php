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
use Joomla\Registry\Registry;

defined('_JEXEC') or die;

// Include library dependencies
jimport( 'joomla.filter.input' );

/**
* Project Table class
*/
class TableProject extends JLTable
{
	/**
	 * Constructor
	 *
	 * @param object Database connector object
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__joomleague_project', 'id', $db);
	}


	/**
	* Overloaded bind function
	*
	* @acces public
	* @param array $hash named array
	* @return null|string	null is operation was satisfactory, otherwise returns an error
	* @see JTable:bind
	*/
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
		return true;
	}


	/**
	 * Redefined asset name, as we support action control
	 */
	protected function _getAssetName() {
		$k = $this->_tbl_key;
		return 'com_joomleague.project.'.(int) $this->$k;
	}
}
