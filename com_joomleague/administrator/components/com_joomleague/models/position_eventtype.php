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
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Table\Table;
use Joomla\Utilities\ArrayHelper;

require_once JPATH_COMPONENT.'/models/item.php';

/**
 * Position_eventtype Model
 *
 * @author Marco Vaninetti <martizva@libero.it>
 */
class JoomleagueModelPosition_eventtype extends JoomleagueModelItem
{
	/**
	 * Method to load  data
	 *
	 * @access	private
	 * @return	boolean	True on success
	 */
	function _loadData()
	{
		return true;
	}

	/**
	 * Method to initialise data
	 *
	 * @access	private
	 * @return	boolean	True on success
	 */
	function _initData()
	{
		// Lets load the content if it doesn't already exist
		return true;
	}


	/**
	 * Method to update position events
	 *
	 * @access	public
	 * @return	boolean	True on success
	 *
	 */
	function store($data)
	{
		$app = Factory::getApplication();
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		$result	= true;
		$peid	= (isset($data['position_eventslist']) ? $data['position_eventslist'] : array());
		ArrayHelper::toInteger( $peid );
		$peids = implode( ',', $peid );
		$query = ' DELETE	FROM #__joomleague_position_eventtype '
		       . ' WHERE position_id = ' . $data['id']
		       ;
		if (count($peid)) {
			$query .= '   AND eventtype_id NOT IN  (' . $peids . ')';
		}
		try
		{
			$db->setQuery($query);
			$db->execute();
		}
		catch (Exception $e)
		{
			$app->enqueueMessage(Text::_($e->getMessage()), 'error');
			return false;
		}
		
		for ( $x = 0; $x < count( $peid ); $x++ )
		{
			$query = "UPDATE #__joomleague_position_eventtype SET ordering='$x' WHERE position_id = '" . $data['id'] . "' AND eventtype_id = '" . $peid[$x] . "'";
			try
			{
				$db->setQuery($query);
				$db->execute();
			}
			catch (Exception $e)
			{
				$app->enqueueMessage(Text::_($e->getMessage()), 'error');
				return false;
			}
	
		}
		for ( $x = 0; $x < count ($peid ); $x++ )
		{
			$query = "INSERT IGNORE INTO #__joomleague_position_eventtype (position_id, eventtype_id, ordering) VALUES ( '" . $data['id'] . "', '" . $peid[$x] . "','" . $x . "')";
			try
			{
				$db->setQuery($query);
				$db->execute();
			}
			catch (Exception $e)
			{
				$app->enqueueMessage(Text::_($e->getMessage()), 'error');
				return false;
			}
			
		}
		return $result;
	}
	
	/**
	 * Returns a Table object, always creating it
	 *
	 * @param	type	The table type to instantiate
	 * @param	string	A prefix for the table class name. Optional.
	 * @param	array	Configuration array for model. Optional.
	 * @return	Table	A database object
	 */
	public function getTable($type = 'PositionEventtype', $prefix = 'Table', $config = array())
	{
		return Table::getInstance($type, $prefix, $config);
	}
	
	/**
	 * Method to get the record form.
	 *
	 * @param	array	$data		Data for the form.
	 * @param	boolean	$loadData	True if the form is to load its own data (default case), false if not.
	 * @return	mixed	A JForm object on success, false on failure
	 */
	public function getForm($data = array(), $loadData = true)
	{
		// Get the form.
		$form = $this->loadForm('com_joomleague.'.$this->name, $this->name,
				array('load_data' => $loadData) );
		if (empty($form))
		{
			return false;
		}
		return $form;
	}
	
	/**
	 * Method to get the data that should be injected in the form.
	 *
	 * @return	mixed	The data for the form.
	 */
	protected function loadFormData()
	{
		// Check the session for previously entered form data.
		$data = Factory::getApplication()->getUserState('com_joomleague.edit.'.$this->name.'.data', array());
		if (empty($data))
		{
			$data = $this->getData();
		}
		return $data;
	}
}
