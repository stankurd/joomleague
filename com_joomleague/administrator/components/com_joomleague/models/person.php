<?php
/**
 * Joomleague
 *
 * @copyright	Copyright (C) 2006-2015 joomleague.at. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @link		http://www.joomleague.at
 * @author		Kurt Norgaz
 */
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Table\Table;

defined('_JEXEC') or die;


/**
 * Person Model
 */
class JoomleagueModelPerson extends JLGModelItem
{

	public $typeAlias = 'com_joomleague.person';

	/**
	 * Method to test whether a record can be deleted.
	 *
	 * @param object $record A record object.
	 *
	 * @return boolean True if allowed to delete the record. '
	 * Defaults to the permission for the component.
	 */
	protected function canDelete($record)
	{
		if(!empty($record->id))
		{
		$user = Factory::getUser();

			if($user->authorise('core.admin','com_joomleague') 
					|| $user->authorise('core.delete','com_joomleague') 
					|| $user->authorise('core.delete','com_joomleague.club.'.$id))
			{
				return true;
			} else {
				return false;
			}
		}
	}


	/**
	 * Method to remove a person
	 *
	 * @access public
	 * @return boolean on success
	 */
	public function delete(&$pks = array())
	{
		$app = Factory::getApplication();
		$db = Factory::getDbo();	
		$query = $db->getQuery(true);
		if($pks)
		{
			$cids = implode(',',$pks);

			// First select all subpersons of the selected ids
			$query->select('*') 
			->from('#__joomleague_person') 
			->where('id IN  (' .  $cids . ')');
			$db->setQuery($query);
			if($results = $db->loadObjectList())
			{
				foreach($results as $result)
				{
					// Now delete all match-persons assigned as player to
					// subpersons of the selected ids
					$query = "DELETE FROM #__joomleague_match_event
								WHERE teamplayer_id in (select id from #__joomleague_team_player where person_id = " . $result->id . ")
									OR teamplayer_id2 in (select id from #__joomleague_team_player where person_id = " . $result->id . ")";
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


					// Now delete all match-events assigned as referee to
					// subpersons of the selected ids
					$query = "DELETE FROM #__joomleague_match_referee
								WHERE project_referee_id in (select id from #__joomleague_project_referee where person_id = " . $result->id . ")";
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
	
					// Now delete all match-events assigned as player1 to
					// subpersons of the selected ids
					$query = "DELETE FROM #__joomleague_match_player
								WHERE teamplayer_id in (select id from #__joomleague_team_player where person_id = " . $result->id . ")";
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

					// Now delete all match-events assigned as player2 to
					// subpersons of the selected ids
					$query = "DELETE FROM #__joomleague_match_staff
								WHERE team_staff_id in (select id from #__joomleague_team_staff where person_id = " . $result->id . ")";
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

					$query = "DELETE FROM #__joomleague_match_statistic
								WHERE teamplayer_id in (select id from #__joomleague_team_player where person_id = " . $result->id . ")";
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

					$query = "DELETE FROM #__joomleague_match_staff_statistic
								WHERE team_staff_id in (select id from #__joomleague_team_staff where person_id = " . $result->id . ")";
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

					// Now delete all person assigned as referee in a project of
					// the selected ids
					$query = "DELETE FROM #__joomleague_project_referee
								WHERE person_id=" . $result->id;
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

					// Now delete all person assigned as player in a team of the
					// selected ids
					$query = "DELETE FROM #__joomleague_team_player
								WHERE person_id=" . $result->id;
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

					// Now delete all person assigned as staff in a team of the
					// selected ids
					$query = "DELETE FROM #__joomleague_team_staff
								WHERE person_id=" . $result->id;
					$db->setQuery($query);
					
				}
			}
			return parent::delete($pks);
		}
					try
					{
						$db->execute();
					}
					catch (Exception $e)
					{
						$app->enqueueMessage(Text::_($e->getMessage()), 'error');
						return false;
					}
		return true;
	}


	/**
	 * Returns a Table object, always creating it
	 *
	 * @param	type The table type to instantiate
	 * @param	string A prefix for the table class name. Optional.
	 * @param	array Configuration array for model. Optional.
	 * @return Table database object
	 */
	public function getTable($type = 'Person',$prefix = 'Table',$config = array())
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
		$form = $this->loadForm('com_joomleague.person','person',array('control' => 'jform','load_data' => $loadData));
		if(empty($form))
		{
			return false;
		}
		$input = Factory::getApplication()->input;

		if($this->getState('person.id'))
		{
			$pk = $this->getState('person.id');
			$item = $this->getItem($pk);
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
		$data = $app->getUserState('com_joomleague.edit.person.data',array());

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

		$assignPerson = $input->get('assignperson',false);
		$data['extended'] = $input->get('extended',array(),'array');
		
		if(parent::save($data))
		{
			$pk = (int) $this->getState($this->getName().'.id');
			$item = $this->getItem($pk);
			
			if ($assignPerson)
			{
				$projectTeam_id    = $input->getInt('team_id',false);
			
				if ($projectTeam_id) {
					$mdlTeamplayers = JLGModel::getInstance('teamplayers','JoomleagueModel');
					$mdlTeamplayers->storeassigned(array($pk), $projectTeam_id);
				}
			}
			
			return true;
		}

		return false;
	}

	
	/**
	 * Method to update checked persons
	 *
	 * @access public
	 * @return boolean on success
	 */
	function storeshort($cid,$post)
	{
		$app 	= Factory::getApplication();
		$db = Factory::getDbo();	
		$query = $db->getQuery(true);
		$inplaceEditing = $post['inplaceEditing'];
		
		$result = true;
		for($x = 0;$x < count($cid);$x ++)
		{
			$tblPerson = $this->getTable();
			$tblPerson->id = $cid[$x];
			if ($inplaceEditing == 0) {
				$tblPerson->firstname = $post['firstname'.$cid[$x]];
				$tblPerson->lastname = $post['lastname'.$cid[$x]];
				$tblPerson->nickname = $post['nickname'.$cid[$x]];
			}
			if (isset($post['birthday'.$cid[$x]])) {
				$tblPerson->birthday = $post['birthday'.$cid[$x]];
			}
			$tblPerson->country = $post['country'.$cid[$x]];
			if (isset($post['position'.$cid[$x]])) {
				$tblPerson->position_id = $post['position'.$cid[$x]];
			}
				try
				{
					$tblPerson->store();
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
	 * Method to update checked persons
	 *
	 * @access public
	 * @return boolean on success
	 */
	function storeshortAjax($name,$value,$pk)
	{
		$app 	= Factory::getApplication();
		$db = Factory::getDbo();	
		$query = $db->getQuery(true);
		$result = true;

		$tblPerson = $this->getTable();
		$tblPerson->id = $pk;
		$tblPerson->$name = $value;
				try
				{
					$tblPerson->store();
				}
				catch (Exception $e)
				{
						$app->enqueueMessage(Text::_($e->getMessage()), 'error');
						return false;
				}
		return $result;
	}


	/**
	 * Method to return a positions array (id,position + (sports_type_name))
	 *
	 * @access public
	 * @return array
	 */
	function getPositions()
	{
		$app 	= Factory::getApplication();
		$db = Factory::getDbo();	
		$query = $db->getQuery(true);
		$query->select('pos.id AS value,
							pos.name AS posName,
							s.name AS sName')
			->from('#__joomleague_position pos')
			->join('INNER','#__joomleague_sports_type AS s ON s.id=pos.sports_type_id')
			->where('pos.published=1')
			->order('pos.ordering,pos.name');
			try
				{
					$db->setQuery($query);
					$result = $db->loadObjectList();
				}
				catch (Exception $e)
				{
						$app->enqueueMessage(Text::_($e->getMessage()), 'error');
						return false;
				}
		
			foreach($result as $position)
			{
				$position->text = Text::_($position->posName) . ' (' . Text::_($position->sName) . ')';
			}
			return $result;
	}

	/**
	 * Method to update checked persons
	 *
	 * @access public
	 * @return boolean on success
	 *
	 */
	function assign($post)
	{
		$app 	= Factory::getApplication();
		$db = Factory::getDbo();	
		$query = $db->getQuery(true);
		$result = true;
		$query = "	INSERT IGNORE
					INTO #__joomleague_person
					(person_id,project_id,team_id,is_person,is_player)
					VALUES
					('" . $post['id'] . "','" . $post['project'] . "','" . $post['team'] . "','0','1')
					WHERE
					published = '1'";
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
		return $result;
	}
}
