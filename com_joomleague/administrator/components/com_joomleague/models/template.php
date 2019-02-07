<?php
/**
 * Joomleague
 *
 * @copyright	Copyright (C) 2006-2015 joomleague.at. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @link		http://www.joomleague.at
 */

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Table\Table;

defined('_JEXEC') or die;

require_once JPATH_COMPONENT.'/models/item.php';

/**
 * Template Model
 *
 * @author	Marco Vaninetti <martizva@tiscali.it>
 */
class JoomleagueModelTemplate extends JoomleagueModelItem
{
	

	/**
	 * Method to load content template data
	 *
	 * @access	private
	 * @return	boolean	True on success
	 */
	function _loadData()
	{
		// Lets load the content if it doesn't already exist
		if (empty($this->_data))
		{
			$this->_data = parent::getItem($this->_id);
			return (boolean) $this->_data;
		}
		return true;
	}

	
	/**
	 * Method to initialise the template data
	 *
	 * @access	private
	 * @return	boolean	True on success
	 */
	function _initData()
	{
		// Lets load the content if it doesn't already exist
		if (empty($this->_data))
		{
			$template=new stdClass();
			$template->id					= 0;
			$template->title				= null;
			$template->func					= null;
			$template->params				= null;
			$template->project_id			= null;
			$template->checked_out			= 0;
			$template->checked_out_time		= 0;
			$template->modified				= null;
			$template->modified_by			= null;
			
			$this->_data					= $template;
			return (boolean) $this->_data;
		}
		return true;
	}

	
	/**
	 * Method to copy a template in current project
	 *
	 * @access	public
	 * @return	boolean True on success
	 */
	function import($templateid,$projectid)
	{
		$app = Factory::getApplication();
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		$row = $this->getTable();

		// load record to copy
		try
		{
		$row->load($templateid);
		}
		catch (Exception $e)
		{
			$app->enqueueMessage(Text::_($e->getMessage()), 'error');
			return false;
		}

		//copy to new element
		$row->id=null;
		$row->project_id=(int) $projectid;

		// Make sure the item is valid
		try {
		$row->check();
		}
		catch (Exception $e)
		{
			$app->enqueueMessage(Text::_($e->getMessage()), 'error');
			return false;
		}

		// Store the item to the database
		try {
			$row->store();
		}
		catch (Exception $e)
		{
			$app->enqueueMessage(Text::_($e->getMessage()), 'error');
			return false;
		}
		return true;
	}
	

	function getAllTemplatesList($project_id,$master_id)
	{
		$db = Factory::getDbo();		
		$query = $db->getQuery(true);
	    $db = Factory::getDbo();
	    $query = $db->getQuery(true);
	    $query->select('template');
	    $query='SELECT template FROM #__joomleague_template_config WHERE project_id='.$project_id;
	    $db->setQuery($query);
	    $current=$db->loadColumn();
	    $query="SELECT id as value, title as text
				FROM #__joomleague_template_config
				WHERE project_id=$master_id AND template NOT IN ('".implode("','",$current)."')
				ORDER BY title";
	    $db->setQuery($query);
	    $result1=$db->loadObjectList();
	    $query="SELECT id as value, title as text
				FROM #__joomleague_template_config
				WHERE project_id=$project_id
				ORDER BY title";
	    $db->setQuery($query);
	    $result2=$db->loadObjectList();
	    $return = array_merge($result2,$result1);  
	    return $return;
	}
	
	
	/**
	 * Returns a Table object, always creating it
	 *
	 * @param	type	The table type to instantiate
	 * @param	string	A prefix for the table class name. Optional.
	 * @param	array	Configuration array for model. Optional.
	 * @return	Table	A database object
	 */
	public function getTable($type = 'TemplateConfig', $prefix = 'Table', $config = array())
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
		$form = $this->loadForm('com_joomleague.'.$this->name, $this->name,array('load_data' => $loadData) );
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
