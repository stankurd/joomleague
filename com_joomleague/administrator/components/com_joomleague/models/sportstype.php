<?php
/**
 * Joomleague
 *
 * @copyright	Copyright (C) 2006-2015 joomleague.at. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @link		http://www.joomleague.at
 */

// Check to ensure this file is included in Joomla!
use Joomla\CMS\Factory;
use Joomla\CMS\Filesystem\Path;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Table\Table;

defined('_JEXEC') or die;

jimport('joomla.filesystem.folder');

/**
 * SportsType Model
 *
 * @author	Julien Vonthron <julien.vonthron@gmail.com>
 */
class JoomleagueModelSportsType extends JLGModelItem
{
	public $typeAlias = 'com_joomleague.sportsType';

	/**
	 * Method to test whether a record can be deleted.
	 *
	 * @param object $record	A record object.
	 *
	 * @return boolean True 	if allowed to delete the record.
	 * Defaults to the	permission for the component.
	 */
	protected function canDelete($record)
	{
		if(!empty($record->id))
		{
		    $user = Factory::getUser();
		    
			if($user->authorise('core.admin','com_joomleague')
				|| $user->authorise('core.delete','com_joomleague')
				|| $user->authorise('core.delete','com_joomleague.sportstype.'.$id))
			{
				return true;
			} else {
				return false;
			}
		}
	}

	/**
	 * Method to remove a sportstype
	 *
	 * @access	public
	 * @return	boolean	True on success
	 */
	public function delete(&$pks=array())
	{
		$return = array();
		$total = count($pks);
		
		if ($pks)
		{
			$pksTodelete = array();
			$errorNotice = array();
			$db = Factory::getDbo();
			foreach($pks as $pk)
			{
				$result = array();
				
				// Check if at least 1 sportstype remains after deletion. TODO: why is that needed?
				$query = $db->getQuery(true);
				$query->select('COUNT(id)');
				$query->from('#__joomleague_sports_type');
				$db->setQuery($query);
				if ($db->loadResult() == $total)
				{
					$result[] = Text::_('COM_JOOMLEAGUE_ADMIN_SPORTTYPE_MODEL_ERROR_LAST_SPORTSTYPE');
				}
				
				// Check if there are still eventtypes for this sportstype
				$query = $db->getQuery(true);
				$query->select('id');
				$query->from('#__joomleague_eventtype');
				$query->where('sports_type_id = '.$pk);
				$db->setQuery($query);
				if ($db->loadResult())
				{
					$result[] = Text::_('COM_JOOMLEAGUE_ADMIN_SPORTTYPE_MODEL_ERROR_EVENT_EXISTS');
				}
				
				// Check if there are still positions for this sportstype
				$query = $db->getQuery(true);
				$query->select('id');
				$query->from('#__joomleague_position');
				$query->where('sports_type_id = '.$pk);
				$db->setQuery($query);
				if ($db->loadResult())
				{
					$result[] = Text::_('COM_JOOMLEAGUE_ADMIN_SPORTTYPE_MODEL_ERROR_POSITION_EXISTS');
				}
				
				// Check if there are still projects for this sportstype
				$query = $db->getQuery(true);
				$query->select('id');
				$query->from('#__joomleague_project');
				$query->where('sports_type_id = '.$pk);
				$db->setQuery($query);
				if ($db->loadResult())
				{
					$result[] = Text::_('COM_JOOMLEAGUE_ADMIN_SPORTTYPE_MODEL_ERROR_PROJECT_EXISTS');
				}
				
				if($result)
				{
					$pkInfo = array("id:".$pk);
					$result = array_merge($pkInfo,$result);
					$errorNotice[] = $result;
				}
				else
				{
					$pksTodelete[] = $pk;
				}
			}
			
			if($pksTodelete)
			{
				$return['removed'] = parent::delete($pksTodelete);
				$return['removedCount'] = count($pksTodelete);
			}
			else
			{
				$return['removed'] = false;
				$return['removedCount'] = false;
			}
			
			if($errorNotice)
			{
				$return['error'] = $errorNotice;
			}
			else
			{
				$return['error'] = false;
			}
			
			return $return;	
		}
			
		$return['removed'] = false;
		$return['error'] = false;
		$return['removedCount'] = false;
		
		return $return;
	}

	/**
	 * Method to add a new sportstype if not already exists
	 *
	 * @access	private
	 * @return	boolean	True on success
	 **/
	function addSportsType($newSportsTypeName)
	{
		$path = Path::clean(JPATH_ROOT.'/images/com_joomleague/database/events/'.JFolder::makesafe($newSportsTypeName));
		if(!JFolder::exists($path)) {
			JFolder::create($path);
		}
		//SportsType does NOT exist and has to be created
		$tblSportsType = $this->getTable();
		$tblSportsType->load(array('name'=>$newSportsTypeName));
		$tblSportsType->name = $newSportsTypeName;
		$tblSportsType->store();
		return $tblSportsType->id;
	}
	
	/**
	 * Returns a Table object, always creating it
	 *
	 * @param	type	The table type to instantiate
	 * @param	string	A prefix for the table class name. Optional.
	 * @param	array	Configuration array for model. Optional.
	 * @return	Table	A database object
	 */
	public function getTable($type = 'SportsType', $prefix = 'Table', $config = array())
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
			array('control' => 'jform', 'load_data' => $loadData));
		if (empty($form))
		{
			return false;
		}
		return $form;
	}
	
	/**
	 * Method to get the data that should be injected in the form.
	 *
	 * @return	mixed	The data for the form
	 */
	protected function loadFormData()
	{
		// Check the session for previously entered form data.
		$app = Factory::getApplication();
		$data = $app->getUserState('com_joomleague.edit.'.$this->name.'.data', array());
		if (empty($data))
		{
			$data = $this->getItem();
		}
		return $data;
	}
	
	
	/**
	 * Prepare and sanitise the table prior to saving.
	 *
	 * @param   Table  $table  A Table object.
	 *
	 * @return  void
	 */
	protected function prepareTable($table)
	{
		$date = Factory::getDate();
		$user = Factory::getUser();
	
		if (empty($table->id))
		{
			// Set ordering to the last item if not set
			if (empty($table->ordering))
			{
				$db = $this->getDbo();
				$query = $db->getQuery(true)
				->select('MAX(ordering)')
				->from('#__joomleague_sports_type');
	
				$db->setQuery($query);
				$max = $db->loadResult();
	
				$table->ordering = $max + 1;
			}
		}
		else
		{
			// Set the values
			$table->modified    = $date->toSql();
			$table->modified_by = $user->get('id');
		}
	}
	
	
	/**
	 * Method to save the form data.
	 *
	 * @param array $data The form data.
	 *
	 * @return boolean True on success.
	 */
	public function save($data)
	{
		$app = Factory::getApplication();
		$input = $app->input;
	
		if(parent::save($data))
		{
			$pk = (int) $this->getState($this->getName().'.id');
			$item = $this->getItem($pk);
		
			return true;
		}
	
		return false;
	}
}
