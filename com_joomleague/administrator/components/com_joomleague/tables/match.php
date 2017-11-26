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
 * Match Table class
 *
 * @author Marco Vaninetti <martizva@tiscali.it>
 */

class TableMatch extends JLTable
{
	/**
	 * Constructor
	 *
	 * @param object Database connector object
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__joomleague_match', 'id', $db);
	}
	

	/**
	 * Overloaded check method to ensure data integrity
	 *
	 * @access public
	 * @return boolean True on success
	 */
	public function check()
	{

		if (!is_numeric($this->team1_result_decision)) {
			$this->team1_result_decision = null;
		}
		if (!is_numeric($this->team2_result_decision)) {
			$this->team2_result_decision = null;
		}
		
		return true;
	}
	
	
	/**
	 * Redefined asset name, as we support action control
	 */
	protected function _getAssetName() {
		$k = $this->_tbl_key;
		return 'com_joomleague.match.'.(int) $this->$k;
	}

}
