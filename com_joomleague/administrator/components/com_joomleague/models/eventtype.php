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
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;

defined('_JEXEC') or die;


/**
 * Eventtype Model
 */
class JoomleagueModelEventtype extends JLGModelItem
{

	public $typeAlias = 'com_joomleague.eventtype';

	
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
					|| $user->authorise('core.delete','com_joomleague.eventtype.'.$id))
			{
				return true;
			} else {
				return false;
			}
		}
	}
	
	

	/**
	 * Method to remove a eventType
	 *
	 * @access public
	 * @return boolean on success
	 */
	public function delete(&$pks = array())
	{
		$return = array();
		if($pks)
		{
			$pksTodelete = array();
			$errorNotice = array();
			$db = $this->getDbo();
			foreach($pks as $pk)
			{
				$result = array();

				// first check that they are not used in any match events
				$query = $db->getQuery(true);
				$query->select('event_type_id');
				$query->from('#__joomleague_match_event');
				$query->where('event_type_id = '.$pk);
				$db->setQuery($query);
				if($db->loadObjectList())
				{
					$result[] = Text::_('COM_JOOMLEAGUE_ADMIN_EVENT_MODEL_ERROR_MATCHES_EXISTS');
				}
				// then check that they are not assigned to any positions
				$query = $db->getQuery(true);
				$query->select('eventtype_id');
				$query->from('#__joomleague_position_eventtype');
				$query->where('eventtype_id = '.$pk);
				$db->setQuery($query);
				if($db->loadObjectList())
				{
					$result[] = Text::_('COM_JOOMLEAGUE_ADMIN_EVENT_MODEL_ERROR_POSITION_EXISTS');
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
	 * Returns a Table object, always creating it
	 *
	 * @param	type The table type to instantiate
	 * @param	string A prefix for the table class name. Optional.
	 * @param	array Configuration array for model. Optional.
	 * @return Table database object
	 */
	public function getTable($type = 'Eventtype',$prefix = 'Table',$config = array())
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
		$form = $this->loadForm('com_joomleague.eventtype','eventtype',array('control' => 'jform','load_data' => $loadData));
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
		$data = $app->getUserState('com_joomleague.edit.eventtype.data',array());

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
				->from('#__joomleague_club');
	
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
		
		if(parent::save($data))
		{
			$pk = (int) $this->getState($this->getName().'.id');
			$item = $this->getItem($pk);

			return true;
		}

		return false;
	}


	/**
	 * Method to export one or more events
	 *
	 * @access public
	 * @return boolean on success
	 */
	function export($cid = array(),$table,$record_name)
	{
	    $app = Factory::getApplication();
		$result = false;

		if(count($cid))
		{
			$mdlJLXExports = BaseDatabaseModel::getInstance('jlxmlexport','JoomleagueModel');
			$cids = implode(',',$cid);

			// EventType
			$db = $this->getDbo();
			$query = $db->getQuery(true);
			$query->select('*');
			$query->from('#__joomleague_eventtype');
			$query->where('id IN ('.$cids.')');
			$db->setQuery($query);
			$exportData = $db->loadObjectList();

			// SportsType
			$SportsTypeArray = array();
			$x = 0;
			foreach($exportData as $event)
			{
				$SportsTypeArray[$x] = $event->sports_type_id;
			}
			$st_cids = implode(',',$SportsTypeArray);
			$query = $db->getQuery(true);
			$query->select('*');
			$query->from('#__joomleague_sports_type');
			$query->where('id IN ('.$st_cids.')');
			$db->setQuery($query);
			$exportDataSportsType = $db->loadObjectList();

			$output = "<?xml version=\"1.0\" encoding=\"utf-8\"?>\n";
			// Events
			$output .= "<events>\n";
			$output .= $mdlJLXExports->_addToXml($mdlJLXExports->_getJoomLeagueVersion());

			$record_name = 'SportsType';
			$tabVar = '  ';
			foreach($exportDataSportsType as $name=>$value)
			{
				$output .= "<record object=\"".JoomleagueHelper::stripInvalidXml($record_name)."\">\n";
				foreach($value as $name2=>$value2)
				{
					if(($name2 != 'checked_out') && ($name2 != 'checked_out_time'))
					{
						$output .= $tabVar.'<'.$name2.'><![CDATA['.JoomleagueHelper::stripInvalidXml(trim($value2)).']]></'.$name2.">\n";
					}
				}
				$output .= "</record>\n";
			}
			unset($name,$value);
			$record_name = 'EventType';
			foreach($exportData as $name=>$value)
			{
				$output .= "<record object=\"".JoomleagueHelper::stripInvalidXml($record_name)."\">\n";
				foreach($value as $name2=>$value2)
				{
					if(($name2 != 'checked_out') && ($name2 != 'checked_out_time'))
					{
						$output .= $tabVar.'<'.$name2.'><![CDATA[' . JoomleagueHelper::stripInvalidXml(trim($value2)).']]></'.$name2.">\n";
					}
				}
				$output .= "</record>\n";
			}
			unset($name,$value);
			// close events
			$output .= '</events>';

			$mdlJLXExports = BaseDatabaseModel::getInstance("jlxmlexport",'JoomleagueModel');
			$mdlJLXExports->downloadXml($output,$table,true);

			// close the application
			$app->close();
		}
		return true;
	}
}
