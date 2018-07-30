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

// Include library dependencies
jimport('joomla.filter.input');

/**
* Team Table class
*/
class TableTeam extends JLTable
{
	/**
	 * Constructor
	 *
	 * @param object Database connector object
	 */
	public function __construct(&$db) {
		parent::__construct('#__joomleague_team', 'id', $db);
	}
	

	/**
	 * Overloaded check method to ensure data integrity
	 *
	 * @access public
	 * @return boolean True on success
	 */
	public function check()
	{
		if (empty($this->name)) {
			$this->setError(Text::_('NAME REQUIRED'));
			return false;
		}
		
		// add default middle size name
		if (empty($this->middle_name)) {
			$parts = explode(" ", $this->name);
			$this->middle_name = substr($parts[0], 0, 20);
		}
	
		// add default short size name
		if (empty($this->short_name)) {
			$parts = explode(" ", $this->name);
			$this->short_name = substr($parts[0], 0, 2);
		}
	
		// setting alias
		if (empty($this->alias))
		{
			$this->alias = OutputFilter::stringURLSafe($this->name);
		}
		else {
		    $this->alias = OutputFilter::stringURLSafe($this->alias);
		}
		
		return true;
	}
	
	
	/**
	 * Redefined asset name, as we support action control
	 */
	protected function _getAssetName() {
		$k = $this->_tbl_key;
		return 'com_joomleague.team.'.(int) $this->$k;
	}
}
