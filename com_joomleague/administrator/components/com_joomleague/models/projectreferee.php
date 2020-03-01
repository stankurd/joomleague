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
 * Projectreferee Model
 */
class JoomleagueModelProjectReferee extends JLGModelItem
{

	public $typeAlias = 'com_joomleague.projectreferee';


	/**
	 * Returns a Table object, always creating it
	 *
	 * @param	type The table type to instantiate
	 * @param	string A prefix for the table class name. Optional.
	 * @param	array Configuration array for model. Optional.
	 * @return Table database object
	 */
	public function getTable($type = 'Projectreferee',$prefix = 'Table',$config = array())
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
			$db = Factory::getDbo();
			$query = $db->getQuery(true);
			$query->select(array('p.firstname','p.lastname','p.nickname'));
			$query->from('#__joomleague_person AS p');

			$query->select('r.*');
			$query->join('INNER','#__joomleague_project_referee AS r ON r.person_id = p.id');

			$query->select('u.name AS editor');
			$query->join('LEFT','#__users AS u ON u.id=r.checked_out');

			$query->select('ass.rules');
			$query->join('LEFT','#__assets AS ass ON ass.id = r.asset_id');

			$query->where(array('r.id='.(int) $item->id,'p.published = 1'));

			$db->setQuery($query);
			$result = $db->loadObject();
			if($result)
			{
				$item->firstname = $result->firstname;
				$item->lastname = $result->lastname;
				$item->nickname = $result->nickname;
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
		$form = $this->loadForm('com_joomleague.projectreferee','project_referee',array('control' => 'jform','load_data' => $loadData));
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
		$data = $app->getUserState('com_joomleague.edit.projectreferee.data',array());

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

		$data['extended'] = $input->get('extended',array(),'array');
		$data['project_position_id'] = $input->get('project_position_id');
		$data['project_id'] = $input->get('project_id');
	
		if(parent::save($data))
		{
			$pk = (int) $this->getState($this->getName().'.id');
			$item = $this->getItem($pk);

			return true;
		}

		return false;
	}

	/**
	 * Method to return a positions array of referees (id,position)
	 *
	 * @access public
	 * @return array
	 */
	function getRefereePositions()
	{
		$app = Factory::getApplication();
		$input = $app->input;
		$option = $input->getCmd('option');
		$project_id = $app->getUserState($option.'project');

		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		$query->select('pos.name AS text');
		$query->from('#__joomleague_position AS pos');

		// Project-position
		$query->select('ppos.id AS value');
		$query->join('INNER','#__joomleague_project_position AS ppos ON pos.id=ppos.position_id');

		// where
		$query->where(array(
				'ppos.project_id = '.$db->Quote($project_id),
				'pos.persontype = 3'
		));
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
	 */
	function getProjectMatchdays()
	{
		$app = Factory::getApplication();
		$input = $app->input;
		$option = $input->getCmd('option');
		$project_id = $app->getUserState($option.'project');

		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		$query->select(array('roundcode AS value','name AS text'));
		$query->from('#__joomleague_round');
		$query->where('project_id = '.$project_id);
		$query->order('roundcode');try
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
	 * Method to return a persons record
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
		$query->where(array('id = ' . $db->Quote($id),'published = 1'));
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
	 * Method to assign teams of an existing project to a copied project
	 *
	 * @access	public
	 * @return	array
	 */
	// Needs to be adapted to work with persons ans not projectreferee
	function cpCopyProjectReferees($post)
	{
		$app = Factory::getApplication();
		$option = $app->input->get('option');
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		$old_id=(int)$post['old_id'];
		$project_id=(int)$post['id'];
		//copy ProjectReferees
		$query='SELECT * FROM #__joomleague_project_referee WHERE project_id='.$old_id;
		try 
		{
		$db->setQuery($query);
		if ($results=$db->loadAssocList())
		{
			foreach($results as $result)
			{
				$p_player = $this->getTable();
				$p_player->bind($result);
				$p_player->set('id',NULL);
				$p_player->set('project_id',$project_id);
	
			}
		}
		$p_player->store();		
		}
			catch (Exception $e)
			{
				$app->enqueueMessage(Text::_($e->getMessage()), 'error');
				return false;
			}
			return true;		
		}
	
}

