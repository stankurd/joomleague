<?php

use Joomla\CMS\Filter\OutputFilter;
use Joomla\CMS\Language\Text;
use Joomla\Registry\Registry;

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
jimport('joomla.filter.input');

/**
 * Statistic Table class
 */
class TableStatistic extends JLTable
{
	/**
	 * Constructor
	 *
	 * @param object Database connector object
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__joomleague_statistic', 'id', $db);
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
		
		if (empty($this->short)) {
			$this->short = strtoupper(substr($this->name, 0, 4));
		}
	
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
	 * extends bind to include class params (non-PHPdoc)
	 * @see administrator/components/com_joomleague/tables/JLTable#bind($array, $ignore)
	 */
	public function bind($array, $ignore = '')
	{
		if (key_exists( 'baseparams', $array ) && is_array( $array['baseparams'] ))
		{
			$registry = new Registry();
			$registry->loadArray($array['baseparams']);
			$array['baseparams'] = (string) $registry;
		}
		if (key_exists( 'params', $array ) && is_array( $array['params'] ))
		{
			$registry = new Registry();
			$registry->loadArray($array['params']);
			$array['params'] = (string) $registry;
		}
		return parent::bind($array, $ignore);
	}
}
