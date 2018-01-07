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
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Table\Table;
use Joomla\Utilities\ArrayHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;

/**
 * ProjectTeam Model
 */
class JoomleagueModelProjectteam extends JLGModelItem
{

	public $typeAlias = 'com_joomleague.projectteam';

	/**
	 * Returns a Table object, always creating it
	 *
	 * @param	type The table type to instantiate
	 * @param	string A prefix for the table class name. Optional.
	 * @param	array Configuration array for model. Optional.
	 * @return Table database object
	 */
	public function getTable($type = 'Projectteam',$prefix = 'Table',$config = array())
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

		if($item->id)
		{
			$db = factory::getDbo();
			$query = $db->getQuery(true);
			$query->select(array(
					't.name AS name'
			));
			$query->from('#__joomleague_team AS t');

			$query->join('LEFT','#__joomleague_project_team AS pt ON pt.team_id = t.id');
			$query->where('pt.id = ' . $db->quote($item->id));

			$db->setQuery($query);
			$db->execute();
			$result = $db->loadResult();

			if(empty($result))
			{
				$item->name = '(projectteam: ' . $item->id . ')';
			}
			else
			{
				$item->name = $result;
			}
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
		$form = $this->loadForm('com_joomleague.projectteam','project_team',array('control' => 'jform','load_data' => $loadData
		));
		if(empty($form))
		{
			return false;
		}
		
		if($this->getState('projectteam.id'))
		{
			$pk = $this->getState('projectteam.id');
			$item = $this->getItem($pk);
		} else {
			$params		=	ComponentHelper::getParams('com_joomleague');
			$ph_team	=	$params->get('ph_team','images/com_joomleague/database/placeholders/placeholder_450_2.png');
			$form->setFieldAttribute('picture', 'default',$ph_team);
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
		$app = factory::getApplication();
		$data = $app->getUserState('com_joomleague.edit.projectteam.data',array());

		if(empty($data))
		{
			$data = $this->getItem();
		}

		return $data;
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
		$app = factory::getApplication();
		$input = $app->input;

		$data['extended'] = $input->get('extended',array(),'array');
		$data['project_id'] = $input->get('project_id');
		
		if(parent::save($data))
		{
			$pk = (int) $this->getState($this->getName().'.id');
			$item = $this->getItem($pk);
			$model = BaseDatabaseModel::getInstance('projectteam','JoomleagueModel');

			if(isset($data['add_trainingData'])) // add new rows in tab-training
			{
				if($model->addNewTrainingData($pk,$item->project_id))
				{
					$msg = Text::_('COM_JOOMLEAGUE_ADMIN_P_TEAM_CTRL_TRAINING');
				}
				else
				{
					$msg = Text::_('COM_JOOMLEAGUE_ADMIN_P_TEAM_CTRL_ERROR_TRAINING') . $model->getError();
				}
			}

			$post = $input->post->getArray();
			if(isset($post['tdCount'])) // Existing Team Trainingdata
			{
				if($model->saveTrainingData($post))
				{
					$msg = Text::_('COM_JOOMLEAGUE_ADMIN_P_TEAM_CTRL_TRAINING_SAVED');
				}
				else
				{
					$msg = Text::_('COM_JOOMLEAGUE_ADMIN_P_TEAM_CTRL_TRAINING_ERROR_SAVE') . $model->getError();
				}

				if($model->checkAndDeleteTrainingData($post))
				{
					$msg .= ' - ' . Text::_('COM_JOOMLEAGUE_ADMIN_P_TEAM_CTRL_TRAINING_DELETED');
				}
				else
				{
					$msg = ' - ' . Text::_('COM_JOOMLEAGUE_ADMIN_P_TEAM_CTRL_TRAINING_ERROR_DELETED') . $model->getError();
				}
				$msg .= ' - ';
			}
			
			// clear cache
			$project_id = $app->getUserState('com_joomleagueproject');
			$cache = factory::getCache('joomleague.project'.$project_id);
			$cache->clean();

			return true;
		}

		return false;
	}


	/**
	 * Method to return a playgrounds array (id, name)
	 *
	 * @access public
	 * @return array
	 */
	function getPlaygrounds()
	{
		$db = factory::getDbo();
		$query = $db->getQuery(true);
		$query->select(array('id AS value, name AS text'));
		$query->from('#__joomleague_playground');
		$query->order('text ASC');
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
		return $result;
	}


	/**
	 * Method to return a divisions array (id, name)
	 *
	 * @access public
	 * @return array
	 */
	function getDivisions()
	{
		$app 	= factory::getApplication();
		$input = $app->input;
		$option = $input->getCmd('option');

		$project_id = $app->getUserState($option.'project');

		$db = factory::getDbo();
		$query = $db->getQuery(true);
		$query->select(array('id AS value','name As text'));
		$query->from('#__joomleague_division');
		$query->where('project_id = '.$project_id);
		$query->order('name ASC');
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
		return $result;
	}




	/**
	 * Method to return a team trainingdata array
	 *
	 * @access public
	 * @return array
	 */
	function getTrainingData($projectTeamID,$project_id=false)
	{
		$app 	= factory::getApplication();
		$input = $app->input;
		$option = $input->getCmd('option');

		if ($project_id) {
			// take the given project_id
		} else {
			$project_id = $app->getUserState($option.'project');
		}

		$db = factory::getDbo();
		$query = $db->getQuery(true);
		$query->select('*');
		$query->from('#__joomleague_team_trainingdata');
		$query->where(array('project_id = '.$project_id,'project_team_id = '.$projectTeamID));
		$query->order('dayofweek ASC');
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
		return $result;
	}


	function addNewTrainingData($projectTeamID,$projectID)
	{
		$app 	= factory::getApplication();
		$db = factory::getDbo();
		$query = $db->getQuery(true);
		$result = true;
		$query = "INSERT INTO #__joomleague_team_trainingdata (project_id,team_id,project_team_id) VALUES ('$projectID','0','$projectTeamID')";
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


	function saveTrainingData($post)
	{
		$result = true;
		$app = factory::getApplication();
		$db = factory::getDbo();
		$query = $db->getQuery(true);
		$input = $app->input;
		$tdids = $input->get('tdids',array(),'array');
		ArrayHelper::toInteger($tdids);

		foreach($tdids as $tdid)
		{
			$timeStr1 = preg_split('[:]',$post['time_start_' . $tdid]);
			$start = ($timeStr1[0] * 3600) + ($timeStr1[1] * 60);
			$timeStr2 = preg_split('[:]',$post['time_end_' . $tdid]);
			$end = ($timeStr2[0] * 3600) + ($timeStr2[1] * 60);

			$query = "	UPDATE	#__joomleague_team_trainingdata
						SET
								dayofweek='" . $post['dw_' . $tdid] . "',
								time_start='$start',
								time_end='$end',
								place='" . $post['place_' . $tdid] . "',
								notes='" . $app->input->getVar('notes_' . $tdid,'none','post','STRING','') . "'
						WHERE id='$tdid'";
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


	function checkAndDeleteTrainingData($post)
	{
		$result = true;
		$app 	= factory::getApplication();
		$input = $app->input;
		$tdids	= $input->get('tdids',array(),'array');
		ArrayHelper::toInteger($tdids);

		$db = factory::getDbo();

		foreach($tdids as $tdid)
		{
			if(isset($post['delete_' . $tdid]))
			{
				$query = $db->getQuery(true);
				$query->delete('#__joomleague_team_trainingdata');
				$query->where('id = '.$tdid);
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
		}
		return $result;
	}
	
	
	/**
	 * Method to assign teams of an existing project to a copied project
	 *
	 * @access	public
	 * @return	array
	 */
	function cpCopyTeams($post,$source_to_copy_division)
	{
		$app = Factory::getApplication();
		$db = factory::getDbo();
		$query = $db->getQuery(true);
		$mdlTeamPlayer = JLGModel::getInstance('teamplayer','JoomleagueModel');
		$mdlTeamStaff = JLGModel::getInstance('teamstaff','JoomleagueModel');
	
		$old_id = (int)$post['old_id'];
		$project_id = (int)$post['id'];
		
		// copy teams
		$query = 'SELECT * FROM #__joomleague_project_team WHERE project_id='.$old_id;
		$db->setQuery($query);
		$results = $db->loadAssocList();
		
		if ($results)
		{
			foreach($results as $result)
			{
				$p_team = $this->getTable();
				$p_team->bind($result);
				$p_team->set('id', NULL);
				$p_team->set('project_id', $project_id);
				$p_team->set('start_points', 0);
				$p_team->set('start_points', 0);
				$p_team->set('points_finally', 0);
				$p_team->set('neg_points_finally', 0);
				$p_team->set('matches_finally', 0);
				$p_team->set('won_finally', 0);
				$p_team->set('draws_finally', 0);
				$p_team->set('lost_finally', 0);
				$p_team->set('homegoals_finally', 0);
				$p_team->set('guestgoals_finally', 0);
				$p_team->set('diffgoals_finally', 0);
				$p_team->set('is_in_score', 1);
				$p_team->set('use_finally', 0);
	
				//divisions have to be copied first to get a new division id to replace it here
				if ($post['project_type'] == 'DIVISIONS_LEAGUE')
				{
					if($result['division_id'] != null && array_key_exists($result['division_id'],$source_to_copy_division))
					{
						$p_team->set('division_id',$source_to_copy_division[$result['division_id']]);
					}
				}
				try
				{
					$to_projectteam_id = $p_team->store();
				}
				catch(Exception $e)
				{
					$app->enqueueMessage(Text::_($e->getMessage()), 'error');
					return false;
				}
	
				$from_projectteam_id = $result['id'];
	
				//copy project team-players
				if ($mdlTeamPlayer->cpCopyPlayers($from_projectteam_id,$to_projectteam_id))
				{
					echo Text::sprintf('COM_JOOMLEAGUE_ADMIN_PROJECTTEAM_MODEL_TP_COPIED',$from_projectteam_id).'<br />';
				}
				else
				{
					echo Text::sprintf('COM_JOOMLEAGUE_ADMIN_PROJECTTEAM_MODEL_ERROR_TP_COPIED',$from_projectteam_id).'<br />'.$model->getError().'<br />';
				}
	
				//copy project team-staff
				if ($mdlTeamStaff->cpCopyTeamStaffs($from_projectteam_id,$to_projectteam_id))
				{
					echo Text::sprintf('COM_JOOMLEAGUE_ADMIN_PROJECTTEAM_MODEL_TS_COPIED',$from_projectteam_id).'<br />';
				}
				else
				{
					echo Text::sprintf('COM_JOOMLEAGUE_ADMIN_PROJECTTEAM_MODEL_ERROR_TS_COPIED',$from_projectteam_id).'<br />'.$model->getError().'<br />';
				}
	
				// copy project team trainingdata
				$query = 'SELECT * FROM #__joomleague_team_trainingdata WHERE project_team_id='.$from_projectteam_id;
				$db->setQuery($query);
				if ($results = $db->loadAssocList())
				{
					foreach($results as $result)
					{
						$tData = $this->getTable('TeamTrainingData');
						$tData->bind($result);
						$tData->set('id',NULL);
						$tData->set('project_team_id',$to_projectteam_id);
						if (!$tData->store())
						{
							echo Text::sprintf('COM_JOOMLEAGUE_ADMIN_PROJECTTEAM_MODEL_ERROR_TP_COPIED',$from_projectteam_id).'<br />'.$model->getError().'<br />';
						}
						else
						{
							echo Text::sprintf('COM_JOOMLEAGUE_ADMIN_PROJECTTEAM_MODEL_ERROR_TRAINING_COPIED',$from_projectteam_id).'<br />';
						}
					}
				}
			}
		}
		return true;
	}
	
}
