<?php
/**
 * Joomleague
 *
 * @copyright	Copyright (C) 2006-2015 joomleague.at. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @link		http://www.joomleague.at
 */
defined('_JEXEC') or die;
use Joomla\CMS\Factory;
use Joomla\CMS\Table\Table;
use Joomla\Utilities\ArrayHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;

/**
 * Position Model
 */
class JoomleagueModelPosition extends JLGModelItem
{

	public $typeAlias = 'com_joomleague.position';

	
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
					|| $user->authorise('core.delete','com_joomleague.position.'.$id))
			{
				return true;
			} else {
				return false;
			}
		}
	}
	
	
	/**
	 * Method to remove a position
	 *
	 * @access public
	 * @return boolean on success
	 */
	public function delete(&$pks = array())
	{
		$app = Factory::getApplication();
		$db = Factory::getDbo();
		$return = array();
		if($pks)
		{
			$pksTodelete = array();
			$errorNotice = array();
			foreach($pks as $pk)
			{
				$result = array();

				// Project-position table
				$query = $db->getQuery(true);
				$query
        				->select($db->quoteName('ppos.id'))
        				->from($db->quoteName('#__joomleague_project_position' , 'ppos'))
        				->leftJoin($db->quoteName('#__joomleague_position' , 'pos') . ' ON ' .$db->quoteName('pos.id') . ' = ' .$db->quoteName('ppos.position_id'))
        				->where($db->quoteName('pos.id') . ' = ' .$pk);
				$db->setQuery($query);
				if($db->loadResult())
				{
					$result[] = Text::_('COM_JOOMLEAGUE_ADMIN_POSITION_MODEL_ERROR_P_POSITION_EXISTS');
				}
				
				// Project-Referee table
				$query = $db->getQuery(true);
				$query
				        ->select($db->quoteName('pref.id'))
				        ->from($db->quoteName('#__joomleague_project_referee' , 'pref'))
				        ->leftJoin($db->quoteName('#__joomleague_project_position' , 'ppos') . ' ON ' .$db->quoteName('ppos.id') . ' = ' .$db->quoteName('pref.project_position_id'))
				        ->leftJoin($db->quoteName('#__joomleague_position' , 'pos') . ' ON ' .$db->quoteName('pos.id') . ' = ' .$db->quoteName('ppos.position_id'))
				        ->where($db->quoteName('pos.id') . ' = '  .$pk);
				$db->setQuery($query);
				if ($db->loadResult()) 
				{
					$result[] = Text::_('COM_JOOMLEAGUE_ADMIN_POSITION_MODEL_ERROR_P_REFEREE_EXISTS');
				}

				// Team-Player table
				$query = $db->getQuery(true);
				$query
				        ->select($db->quoteName('tp.id'))
				        ->from($db->quoteName('#__joomleague_team_player' , 'tp'))
				        ->leftJoin($db->quoteName('#__joomleague_project_position' , 'ppos'). ' ON ' .$db->quoteName('ppos.id') . ' = ' .$db->quoteName('tp.project_position_id'))
				        ->leftJoin($db->quoteName('#__joomleague_position' , 'pos') . ' ON ' .$db->quoteName('pos.id') . ' = ' .$db->quoteName('ppos.position_id'))
				        ->where($db->quoteName('pos.id') . ' = ' .$pk);
				$db->setQuery($query);
				if ($db->loadResult())
				{
					$result[] = Text::_('COM_JOOMLEAGUE_ADMIN_POSITION_MODEL_ERROR_PLAYER_EXISTS');
				}
				
				// Team-Staff table
				$query = $db->getQuery(true);
				$query
				        ->select($db->quoteName('ts.id'))
				        ->from($db->quoteName('#__joomleague_team_staff' , 'ts'))
				        ->innerJoin($db->quoteName('#__joomleague_project_position' , 'ppos') . ' ON ' .$db->quoteName('ppos.id') . ' = ' .$db->quoteName('ts.project_position_id'))
				        ->innerJoin($db->quoteName('#__joomleague_position' , 'pos') . ' ON ' .$db->quoteName('pos.id') . ' = ' .$db->quoteName('ppos.position_id'))
				        ->where($db->quoteName('pos.id') . ' = ' .$pk);
				$db->setQuery($query);
				if ($db->loadResult())
				{
					$result[] = Text::_('COM_JOOMLEAGUE_ADMIN_POSITION_MODEL_ERROR_STAFF_EXISTS');
				}
				
				// Person table
				$query = $db->getQuery(true);
				$query->select('id');
				$query->from('#__joomleague_person');
				$query->where('position_id = '.$pk);
				$db->setQuery($query);
				if ($db->loadResult())
				{
					$result[] = Text::_('COM_JOOMLEAGUE_ADMIN_POSITION_MODEL_ERROR_PERSON_EXISTS');
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

				
				// @todo: change
				// Add permission checking to delete the positions
				
				// Position-EventType table
				$query = $db->getQuery(true);
				$query->delete('#__joomleague_position_eventtype');
				$query->where('position_id IN ('.implode(',',$pksTodelete).')');
				try
				{
					$db->setQuery($query);
					$db->execute();
				}
				catch (RuntimeException $e)
					{
						$app->enqueueMessage(Text::_($e->getMessage()), 'error');
						return false;
					}
				
				// Position-Statistic table
				$query = $db->getQuery(true);
				$query->delete('#__joomleague_position_statistic');
				$query->where('position_id IN ('.implode(',',$pksTodelete).')');
				try
				{
					$db->setQuery($query);
					$db->execute();
				}
				catch (RuntimeException $e)
					{
						$app->enqueueMessage(Text::_($e->getMessage()), 'error');
						return false;
					}
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
	public function getTable($type = 'Position',$prefix = 'Table',$config = array())
	{
		return Table::getInstance($type,$prefix,$config);
	}


	/**
	 * Method to get a single record.
	 *
	 * @param integer $pk	The id of the primary key.
	 *
	 * @return mixed Object on success, false on failure.
	 */
	public function getItem($pk = null)
	{
		if($item = parent::getItem($pk))
		{
		}

		return $item;
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
		$form = $this->loadForm('com_joomleague.position','position',array('control' => 'jform','load_data' => $loadData));
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
		$data = $app->getUserState('com_joomleague.edit.position.data',array());

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
				->from('#__joomleague_position');
	
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
	 *
	 * @todo
	 * - alter error check for storing eventTypes/statistics
	 * maybe by adding try/catch
	 */
	public function save($data)
	{
		
		$app = Factory::getApplication();
		$input = $app->input;
		$isNew = ($data['id'] == 0);

		// left event-select
		$data['eventslist'] = $input->get('eventslist',array(),'array');
		ArrayHelper::toInteger($data['eventslist']);

		// right event-select
		$data['position_eventslist'] = $input->get('position_eventslist',array(),'array');
		ArrayHelper::toInteger($data['position_eventslist']);

		// left statistic-select
		$data['statistic'] = $input->get('statistic',array(),'array');
		ArrayHelper::toInteger($data['statistic']);

		// right statistic-select
		$data['position_statistic'] = $input->get('position_statistic',array(),'array');
		ArrayHelper::toInteger($data['position_statistic']);

		$data['parent_id'] = $input->getInt('parent_id');

		if(parent::save($data))
		{
			$pk = (int) $this->getState($this->getName() . '.id');
			$item = $this->getItem($pk);
			$db = Factory::getDbo();

			// UPDATING ASSIGNED EVENTTYPES //
			$posEtId = $data['position_eventslist'];
			$posEtIds = implode(',',$posEtId);

			if($isNew)
			{
				//
			}
			else
			{
				// when edit we do have existing data
				// so first delete all entries related to this position
				$query = $db->getQuery(true);
				$query->delete('#__joomleague_position_eventtype');
				$query->where('position_id = ' . $pk);
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
			}

			if($posEtId)
			{
				// insert data
				for($x = 0;$x < count($posEtId);$x ++)
				{
					$query = $db->getQuery(true);

					$columns = array(
							'position_id',
							'eventtype_id',
							'ordering'
					);
					$values = array(
							$pk,
							$posEtId[$x],
							$x
					);

					// Prepare the insert query.
					$query->insert($db->quoteName('#__joomleague_position_eventtype'))
						->columns($db->quoteName($columns))
						->values(implode(',',$values));

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
				}
			}

			// UPDATE ASSIGNED STATISTICS //
			$posStId = $data['position_statistic'];
			$posStIds = implode(',',$posStId);

			if($isNew)
			{
				//
			}
			else
			{
				$query = $db->getQuery(true);
				$query->delete('#__joomleague_position_statistic');
				$query->where('position_id = ' . $pk);
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
			}

			if($posStId)
			{
				// we do have an statistic-id so let's store it
				// input will be an array

				for($x = 0;$x < count($posStId);$x ++)
				{
					$query = $db->getQuery(true);

					$columns = array(
							'position_id',
							'statistic_id',
							'ordering'
					);
					$values = array(
							$pk,
							$posStId[$x],
							$x
					);

					// Prepare the insert query.
					$query->insert($db->quoteName('#__joomleague_position_statistic'))
						->columns($db->quoteName($columns))
						->values(implode(',',$values));

					// Set the query using our newly populated query object and
					// execute it.
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
				}
			}

			return true;
		}

		return false;
	}

	
	/**
	 * Method to return the query that will obtain all ordering versus positions
	 * (with sportstype between brackets)
	 * It can be used to fill a list box with value/text data.
	 *
	 * @access public
	 * @return string
	 */
	function getOrderingAndPositionsQuery()
	{
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		return 'SELECT pos.ordering AS value, concat(pos.name, " (", st.name, ")")  AS text
			FROM #__joomleague_position pos
			LEFT JOIN #__joomleague_sports_type st ON st.id = pos.sports_type_id
			ORDER BY pos.ordering';
	}

	
	/**
	 * Method to return a events array (id,name)
	 *
	 * @access public
	 * @return array
	 */
	function getEvents()
	{
	    $app = Factory::getApplication();
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		$query->select(array('evt.id AS value','evt.name AS eventtype'));
		$query->from('#__joomleague_eventtype AS evt');

		$query->select('st.name AS sportstype');
		$query->join('LEFT','#__joomleague_sports_type AS st ON st.id = evt.sports_type_id');

		$query->where('evt.published = 1');
		$query->order('evt.name ASC');
		try
		{
			$db->setQuery($query);
			$result = $db->loadObjectList();
		}
		catch (RuntimeException $e)
		{
			$app->enqueueMessage(Text::_($e->getMessage()), 'error');
			return false;
		}
		foreach($result as $position)
		{
			$position->text = Text::_($position->eventtype)." (".Text::_($position->sportstype).")";
		}

		return $result;
	}

	
	/**
	 * Method to return the position events array (id,name)
	 *
	 * @access public
	 * @return array
	 */
	function getEventsPosition($pk = false)
	{
	    $app = Factory::getApplication();
		$db = Factory::getDbo();
		$query = $db->getQuery(true);

		$query->select(array('p.id AS value','p.name AS eventtype'));
		$query->from('#__joomleague_eventtype AS p');

		// join Position-EventType table
		$query->join('LEFT','#__joomleague_position_eventtype AS pe ON pe.eventtype_id = p.id');

		// join SportsType table
		$query->select('st.name AS sportstype');
		$query->join('LEFT','#__joomleague_sports_type AS st ON st.id = p.sports_type_id');

		$query->where('pe.position_id = ' . $pk);
		$query->order('pe.ordering ASC ');
		try
		{
			$db->setQuery($query);
			$result = $db->loadObjectList();
		}
		catch (RuntimeException $e)
		{
			$app->enqueueMessage(Text::_($e->getMessage()), 'error');
			return false;
		}
		foreach($result as $event)
		{
			$event->text = Text::_($event->eventtype) . " (" . Text::_($event->sportstype) . ")";
		}

		return $result;
	}

	/**
	 * Method to return the assigned Stastistics
	 *
	 * @access public
	 * @return array
	 */
	function getPositionStatsOptions($pk = false)
	{
	    $app = Factory::getApplication();
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		$query->select(array('s.id AS value','s.name AS statsname'));
		$query->from('#__joomleague_statistic AS s');

		// join Position Statistic table
		$query->join('LEFT','#__joomleague_position_statistic AS ps ON ps.statistic_id = s.id');

		// join SportsType table
		$query->select('st.name AS sportstype');
		$query->join('LEFT','#__joomleague_sports_type AS st ON st.id = s.sports_type_id');

		$query->where('ps.position_id = ' . $pk);
		$query->order('ps.ordering ASC');
		try
			{
				$db->setQuery($query);
				$result = $db->loadObjectList();
			}
			catch (RuntimeException $e)
			{
				$app->enqueueMessage(Text::_($e->getMessage()), 'error');
				return false;
			}
		foreach($result as $stat)
		{
			$stat->text = Text::_($stat->statsname) . " (" . Text::_($stat->sportstype) . ")";
		}

		return $result;
	}

	/**
	 * Method to return the statics not yet assigned to position (value,text)
	 *
	 * @access public
	 * @return array
	 */
	function getAvailablePositionStatsOptions($pk = false)
	{
	    $app = Factory::getApplication();
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		$query->select(array('s.id AS value','s.name AS statsname'));
		$query->from('#__joomleague_statistic AS s');

		// join Position-statistic table
		$query->select('ps.position_id');
		$query->join('LEFT','#__joomleague_position_statistic AS ps ON ps.statistic_id = s.id');

		// join SportsType table
		$query->select('st.name AS sportstype');
		$query->join('LEFT','#__joomleague_sports_type AS st ON st.id = s.sports_type_id');

		if($pk > 0)
		{
			$query->where(array('IFNULL(ps.position_id,"0") NOT LIKE ' . $db->Quote($pk)));
		}

		$query->group('s.id, ps.position_id, s.name, st.name');
		$query->order('s.ordering ASC ');
		try
			{
				$db->setQuery($query);
				$result = $db->loadObjectList();
			}
			catch (RuntimeException $e)
			{
				$app->enqueueMessage(Text::_($e->getMessage()), 'error');
				return false;
			}
		
		foreach($result as $stat)
		{
			$stat->text = Text::_($stat->statsname) . " (" . Text::_($stat->sportstype) . ")";
		}

		return $result;
	}

	/**
	 * Method to return the positions array (id,name)
	 *
	 * @access public
	 * @return array
	 */
	function getParentsPositions($pk = false)
	{
		$app = Factory::getApplication();
		$input = $app->input;
		$option = $input->getCmd('option');

		$project_id = $app->getUserState($option . 'project');

		// get positionss already in project for parents list
		// support only 2 sublevel, so parent must not have parents themselves

		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		$query->select(array(
				'pos.id AS value',
				'pos.name AS text'
		));
		$query->from('#__joomleague_position AS pos');
		$query->where('pos.parent_id = 0');
		$query->order('pos.ordering ASC');
		try
			{
				$db->setQuery($query);
				$result = $db->loadObjectList();
			}
			catch (RuntimeException $e)
			{
				$app->enqueueMessage(Text::_($e->getMessage()), 'error');
				return false;
			}
		foreach($result as $position)
		{
			$position->text = Text::_($position->text);
		}
		return $result;
	}

	/**
	 * Method to export one or more positions
	 *
	 * @access public
	 * @return boolean on success
	 */
	function export($cid = array(),$table,$record_name)
	{
		$app = Factory::getApplication();
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		$result = false;
		if(count($cid))
		{
			$cids = implode(',',$cid);
			$query = "SELECT * FROM #__joomleague_position WHERE id IN ($cids)";
			$db->setQuery($query);
			$exportData = $db->loadObjectList();
			$SportsTypeArray = array();
			$x = 0;
			foreach($exportData as $position)
			{
				$SportsTypeArray[$x] = $position->sports_type_id;
			}
			$st_cids = implode(',',$SportsTypeArray);
			$query = "SELECT * FROM #__joomleague_sports_type WHERE id IN ($st_cids)";
			// echo $query;
			$db->setQuery($query);
			$exportDataSportsType = $db->loadObjectList();
			$output = "<?xml version=\"1.0\" encoding=\"utf-8\"?>\n";
			// open the positions
			$output .= "<positions>\n";
			$record_name = 'SportsType';
			// $tabVar=' ';
			$tabVar = '  ';
			foreach($exportDataSportsType as $name=>$value)
			{
				$output .= "<record object=\"" . JoomleagueHelper::stripInvalidXml($record_name) . "\">\n";
				foreach($value as $name2=>$value2)
				{
					if(($name2 != 'checked_out') && ($name2 != 'checked_out_time'))
					{
						$output .= $tabVar . '<' . $name2 . '><![CDATA[' . JoomleagueHelper::stripInvalidXml(trim($value2)) . ']]></' . $name2 . ">\n";
					}
				}
				$output .= "</record>\n";
			}
			unset($name,$value);
			$record_name_position = 'Position';
			$record_name_parent_position = 'ParentPosition';
			foreach($exportData as $name=>$value)
			{
				if($value->parent_id == 0)
				{
					$output .= "<record object=\"" . JoomleagueHelper::stripInvalidXml($record_name_parent_position) . "\">\n";
				}
				else
				{
					$output .= "<record object=\"" . JoomleagueHelper::stripInvalidXml($record_name_position) . "\">\n";
				}
				foreach($value as $name2=>$value2)
				{
					if(($name2 != 'checked_out') && ($name2 != 'checked_out_time'))
					{
						$output .= $tabVar . '<' . $name2 . '><![CDATA[' . JoomleagueHelper::stripInvalidXml(trim($value2)) . ']]></' . $name2 . ">\n";
						// echo "<pre>".$name2."#".$value2."<br /></pre>";
					}
				}
				$output .= "</record>\n";
			}
			unset($name,$value);
			// close positions
			$output .= '</positions>';

			$mdlJLXExports = BaseDatabaseModel::getInstance("jlxmlexport",'JoomleagueModel');
			$mdlJLXExports->downloadXml($output,$table,true);

			// close the application
			$app->close();
		}
		return true;
	}
}
