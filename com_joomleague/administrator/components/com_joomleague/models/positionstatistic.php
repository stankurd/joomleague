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
 * PositionStatistic Model
 *
 * @author Marco Vaninetti <martizva@libero.it>
 */
class JoomleagueModelPositionstatistic extends JoomleagueModelItem
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
	 * Method to update position statistic
	 *
	 * @access	public
	 * @return	boolean	True on success
	 */
	function store($data)
	{
		$app = Factory::getApplication();
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
 		$result	= true;
		$peid	= (isset($data['position_statistic']) ? $data['position_statistic'] : array());
		ArrayHelper::toInteger( $peid );
		$peids = implode( ',', $peid );
		
		$query = ' DELETE	FROM #__joomleague_position_statistic '
		       . ' WHERE position_id = ' . $data['id']
		       ;
		if (count($peid)) {
			$query .= '   AND statistic_id NOT IN  (' . $peids . ')';
		}

		$db->setQuery( $query );
		if( !$db->execute() )
		{
			$this->setError( $db->getErrorMsg() );
			$result = false;
		}

		for ( $x = 0; $x < count($peid); $x++ )
		{
			$query = "UPDATE #__joomleague_position_statistic SET ordering='$x' WHERE position_id = '" . $data['id'] . "' AND statistic_id = '" . $peid[$x] . "'";
 			$db->setQuery( $query );
			if( !$db->execute() )
			{
				$this->setError( $db->getErrorMsg() );
				$result= false;
			}
		}
		for ( $x = 0; $x < count($peid); $x++ )
		{
			$query = "INSERT IGNORE INTO #__joomleague_position_statistic (position_id, statistic_id, ordering) VALUES ( '" . $data['id'] . "', '" . $peid[$x] . "','" . $x . "')";
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
	public function getTable($type = 'PositionStatistic', $prefix = 'Table', $config = array())
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
