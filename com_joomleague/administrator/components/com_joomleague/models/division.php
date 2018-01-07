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


/**
 * Division Model
 */
class JoomleagueModelDivision extends JLGModelItem
{

	public $typeAlias = 'com_joomleague.division';


	/**
	 * Method to remove a division
	 *
	 * @access public
	 * @return boolean on success
	 */
	function delete(&$pks = array())
	{
		$app = Factory::getApplication();
		if(count($pks))
		{
			$cids = implode(',',$pks);

			$db = Factory::getDbo();
			$query = $db->getQuery(true);
			$query->update('#__joomleague_project_team');
			$query->set('division_id = 0');
			$query->where('division_id IN ('.$cids.')');
			$db->setQuery($query);
		try
		{
			$db->execute();
		}
		catch (RuntimeException $e)
		{
			$app->enqueueMessage(Text::_($e->getMessage()), 'error');
			return false;
			}
			$query->update('#__joomleague_treeto');
			$query->set('division_id = 0');
			$query->where('division_id IN ('.$cids.')');
			$db->setQuery($query);
		try
		{
			$db->execute();
		}
		catch (RuntimeException $e)
		{
			$app->enqueueMessage(Text::_($e->getMessage()), 'error');
			return false;
			}
			return parent::delete($pks);
		}
		return true;
	}


	/**
	 * Returns a Table object, always creating it
	 *
	 * @param	type 	The table type to instantiate
	 * @param	string 	A prefix for the table class name. Optional.
	 * @param	array 	Configuration array for model. Optional.
	 * @return Table database object
	 */
	public function getTable($type = 'Division',$prefix = 'Table',$config = array())
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
		$form = $this->loadForm('com_joomleague.division','division',array('control' => 'jform','load_data' => $loadData));
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
		$data = $app->getUserState('com_joomleague.edit.division.data',array());

		if(empty($data))
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
				->from('#__joomleague_division');
	
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
		$app = Factory::getApplication();
		$input = $app->input;

		$data['parent_id'] = $input->getInt('parent_id');
		$data['project_id'] = $input->getInt('project_id');
			
		if(parent::save($data))
		{
			$pk = (int) $this->getState($this->getName().'.id');
			$item = $this->getItem($pk);

			return true;
		}

		return false;
	}


	/**
	 * Method to return the division events array (id, name)
	 *
	 * @access public
	 * @return array
	 */
	function getParentsDivisions()
	{
		$app = Factory::getApplication();
		$input = $app->input;
		$option = $input->getCmd('option');

		$project_id = $app->getUserState($option.'project');

		// get divisions already in project for parents list
		// support only 2 sublevel, so parent must not have parents themselves
		$db = $this->getDbo();
		$query = $db->getQuery(true);
		$query->select(array('dv.id AS value','dv.name AS text'));
		$query->from('#__joomleague_division AS dv');
		$query->where(array('dv.project_id = '.$project_id,'dv.parent_id = 0'));
		$query->order('dv.ordering ASC ');
		$db->setQuery($query);
		try
		{
			$result = $db->loadObjectList();
		}
		catch (RuntimeException $e)
		{
			$app->enqueueMessage(Text::_($e->getMessage()), 'error');

			return false;
		}
		return $result;
	}
}
