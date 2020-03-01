<?php
use Joomla\CMS\Factory;
use Joomla\CMS\Filter\OutputFilter;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Table\Table;
use Joomla\Database\DatabaseDriver;

/**
* @copyright	Copyright (C) 2007-2012 JoomLeague.net. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* Joomla! is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/


// Check to ensure this file is included in Joomla!
defined( '_JEXEC' ) or die( 'Restricted access' );

// Include library dependencies
jimport( 'joomla.filter.input' );
JLoader::register('JLTable',JPATH_SITE.'/administrator/components/com_joomleague/tables/jltables.php');

/**
 * Prediction Game Table class 
 *
 * @author	Kurt Norgaz
 * @package	JoomLeague
 * @since	1.5.0a
 */
class TablePredictionGame extends JLTable
{
	/**
	 * Primary Key
	 *
	 * @var int
	 */
	var $id = null;

	var $name;

	/* alias for nice sef urls */
	var $alias;

	var $auto_approve;
	var $only_favteams;
	var $admin_tipp;
	var $master_template;
	var $sub_template_id;
	var $extension;
	var $notify_to;

	var $published;

	var $checked_out;
	var $checked_out_time;

	/**
	 * Constructor
	 *
	 * @param object Database connector object
	 * @since 1.0
	 */
	function __construct(DatabaseDriver $db)
	{
		parent::__construct( '#__joomleague_prediction_game', 'id', $db );
	}

	/**
	 * Overloaded check method to ensure data integrity
	 *
	 * @access public
	 * @return boolean True on success
	 * @since 1.0
	 */
	public function check()
	{
		if ( trim( $this->name ) == '' )
		{
			$this->setError( Text::_( 'CHECK FAILED - Empty name of prediction game' ) );
			return false;
		}

		$alias = OutputFilter::stringURLSafe( $this->name );
		if ( empty( $this->alias ) || $this->alias === $alias )
		{
			$this->alias = $alias;
		}

		return true;
	}
	public static function _isCheckedOut($with = 0, $against = null)
	{
		// Handle the non-static case.
		if (isset($this) && ($this instanceof Table) && is_null($against))
		{
			$against = $this->get('checked_out');
		}
	
		// The item is not checked out or is checked out by the same user.
		if (!$against || ($against == $with))
		{
			return false;
		}
	
		$db = Factory::getDbo();
		$db->setQuery('SELECT COUNT(userid)' . 
						' FROM ' . $db->quoteName('#__session') . 
						' WHERE ' . $db->quoteName('userid') . ' = ' . (int) $against);
		$checkedOut = (boolean) $db->loadResult();
	
		// If a session exists for the user then it is checked out.
		return $checkedOut;
	}
	

}
?>