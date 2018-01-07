<?php
/**
 * Joomleague
 *
 * @copyright	Copyright (C) 2006-2015 joomleague.at. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @link		http://www.joomleague.at
 * @author 		Kurt Norgaz <kurtnorgaz@web.de>
 */
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Table\Table;

defined('_JEXEC') or die;


/**
 * TeamStaff Model
 */
class JoomleagueModelTeamStaff extends JLGModelItem
{

	public $typeAlias = 'com_joomleague.teamstaff';

	/**
	 * Returns a Table object, always creating it
	 *
	 * @param	type The table type to instantiate
	 * @param	string A prefix for the table class name. Optional.
	 * @param	array Configuration array for model. Optional.
	 * @return Table database object
	 */
	public function getTable($type = 'Teamstaff',$prefix = 'Table',$config = array())
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
		
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		$query->select(array('a.*'));
		$query->from('#__joomleague_person AS a');
		$query->join('LEFT','#__joomleague_team_staff AS ppl ON ppl.person_id = a.id');
		$query->where('ppl.id = '.$item->id,'a.published = 1');
		$db->setQuery($query);
		$data = $db->loadObject();
		
		$item->firstname = $data->firstname;
		$item->lastname = $data->lastname;
		$item->nickname = $data->nickname;
		$item->knvbnr = $data->knvbnr;
		$item->birthday = $data->birthday;
		$item->country = $data->country;
		$item->height = $data->height;
		$item->weight = $data->weight;

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
		$form = $this->loadForm('com_joomleague.teamstaff','team_staff',array('control' => 'jform','load_data' => $loadData));
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
		$data = $app->getUserState('com_joomleague.edit.teamstaff.data',array());

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
		$app = Factory::getApplication();
		$input = $app->input;
		
		$post = $input->post->getArray();
				
		$data['project_position_id'] = $input->get('project_position_id');
		$data['extended'] = $input->get('extended',array(),'array');
		$data['project_id'] = $input->get('project_id');
		
		$data['injury_date'] = $input->getString('injury_date');
		$data['injury_end'] = $input->getString('injury_end');
		$data['suspension_date'] = $input->getString('suspension_date');
		$data['suspension_end'] = $input->getString('suspension_end');
		$data['away_date'] = $input->getString('away_date');
		$data['away_end'] = $input->getString('away_end');
		/* $data['team'] = $input->get('team'); */
		
		if(parent::save($data))
		{
			$pk = (int) $this->getState($this->getName().'.id');
			$item = $this->getItem($pk);

			return true;
		}

		return false;
	}


	/**
	 * Method to return a positions array (id,position)
	 *
	 * @access public
	 * @return array
	 *
	 */
	function getProjectPositions()
	{
		$app = Factory::getApplication();
		$input = $app->input;
		$option = $input->getCmd('option');
		$project_id = $app->getUserState($option.'project');

		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		$query->select(array('ppos.id AS value, pos.name AS text'));
		$query->from('#__joomleague_position AS pos');
		$query->join('INNER','#__joomleague_project_position AS ppos ON pos.id=ppos.position_id');
		$query->where(array('ppos.project_id ='.$project_id,'pos.persontype = 2'));
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
			$position->text = Text::_($position->text);
		}
		return $result;
	}


	/**
	 * Method to return a matchdays array (id,position)
	 *
	 * @access public
	 * @return array
	 *
	 */
	function getProjectMatchdays()
	{
		$app = Factory::getApplication();
		$option = $app->input->getCmd('option');
		$project_id = $app->getUserState($option.'project');
		$db = Factory::getDbo();
		$query = $db->getQuery(true);

		$query->select(array('roundcode AS value','name AS text'));
		$query->from('#__joomleague_round');
		$query->where('project_id ='.$project_id);
		$query->order('roundcode');
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
	 * Method to assign teamstaff of an existing project to a copied project
	 *
	 * @access public
	 * @return array
	 */
	function cpCopyTeamStaffs($from_projectteam_id,$to_projectteam_id)
	{
		$app = Factory::getApplication();
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		$query->select('ts.*');
		$query->from('#__joomleague_team_staff AS ts');
		$query->join('INNER','#__joomleague_project_team AS pt ON pt.id = ts.projectteam_id');
		$query->where('pt.id = '.$from_projectteam_id);
		try
		{
			$db->setQuery($query);
			$results = $db->loadAssocList();
		
			if($results = $db->loadAssocList())
			{
				foreach($results as $result)
				{
					$p_teamstaff = $this->getTable();
					$p_teamstaff->bind($result);
					$p_teamstaff->set('id',NULL);
					$p_teamstaff->set('projectteam_id',$to_projectteam_id);
					$p_teamstaff->store();
			
				}
			}
		}
			catch (Exception $e)
			{
				$app->enqueueMessage(Text::_($e->getMessage()), 'error');
				return false;
			}
			return true;
	}


	/**
	 * Method to return the teams array (id,name)
	 *
	 * @access public
	 * @return array
	 */
	function getPerson($id)
	{
		$app = Factory::getApplication();
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		$query->select('*');
		$query->from('#__joomleague_person');
		$query->where(array('team_id = 0','id=' . $id));
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
}
