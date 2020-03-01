<?php
/**
 * Joomleague
 *
 * @copyright	Copyright (C) 2006-2015 joomleague.at. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @link		http://www.joomleague.at
 */

use Joomla\CMS\Factory;
use Joomla\CMS\Table\Table;

defined('_JEXEC') or die;


/**
 * Team Model
 */
class JoomleagueModelTeam extends JLGModelItem
{

	public $typeAlias = 'com_joomleague.team';

	
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
					|| $user->authorise('core.delete','com_joomleague.team.'.$id))
			{
				return true;
			} else {
				return false;
			}
		}
	}
	
	
	/**
	 * Returns a Table object, always creating it
	 *
	 * @param	type The table type to instantiate
	 * @param	string A prefix for the table class name. Optional.
	 * @param	array Configuration array for model. Optional.
	 * @return Table database object
	 */
	public function getTable($type = 'Team',$prefix = 'Table',$config = array())
	{
		return Table::getInstance($type,$prefix,$config);
	}


	/**
	 * Method to get the record form.
	 *
	 * @param array $data		the form.
	 * @param boolean $loadData	the form is to load its own data (default case), false if not.
	 * @return mixed JForm object on success, false on failure
	 */
	public function getForm($data = array(),$loadData = true)
	{
		// Get the form.
		$form = $this->loadForm('com_joomleague.team','team',array('control' => 'jform','load_data' => $loadData));
		if(empty($form))
		{
			return false;
		}

		return $form;
	}


	/**
	 * Method to get the data that should be injected in the form.
	 *
	 * @return mixed data for the form.
	 */
	protected function loadFormData()
	{
		// Check the session for previously entered form data.
		$app = Factory::getApplication();
		$data = $app->getUserState('com_joomleague.edit.team.data',array());

		if(empty($data))
		{
			$data = $this->getItem();
		}

		return $data;
	}
	
	
	/**
	 * Method to get a single record.
	 *
	 * @param   integer  $pk  The id of the primary key.
	 *
	 * @return  mixed  Object on success, false on failure.
	 */
	public function getItem($pk = null)
	{
		if ($item = parent::getItem($pk))
		{
	
		}
	
		return $item;
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
			    $db = Factory::getDbo();
				$query = $db->getQuery(true)
				->select('MAX(ordering)')
				->from('#__joomleague_team');
	
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
	 * @param array $data	The form data.
	 *
	 * @return boolean True on success.
	 */
	public function save($data)
	{
		$app 	= Factory::getApplication();		
		$data['extended'] = $app->input->get('extended',array(),'array');
		
		if(parent::save($data))
		{
			$pk = (int) $this->getState($this->getName().'.id');
			$item = $this->getItem($pk);

			return true;
		}

		return false;
	}
}
