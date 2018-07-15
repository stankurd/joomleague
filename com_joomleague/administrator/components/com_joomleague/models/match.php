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
use Joomla\CMS\Object\CMSObject;

/**
 * Match Model
 */

class JoomleagueModelMatch extends JLGModelItem
{
	public $typeAlias = 'com_joomleague.match';

	const MATCH_ROSTER_STARTER			= 0;
	const MATCH_ROSTER_SUBSTITUTE_IN	= 1;
	const MATCH_ROSTER_SUBSTITUTE_OUT	= 2;
	const MATCH_ROSTER_RESERVE			= 3;

	/**
	 * Method to test whether a record can be deleted.
	 *
	 * @param   object  $record  A record object.
	 *
	 * @return  boolean  True if allowed to delete the record. Defaults to the permission for the component.
	 */
	protected function canDelete($record)
	{
		$app 		= Factory::getApplication();
		$input		= $app->input;
		$option 	= $input->getCmd('option');
		$project_id	= $app->getUserState($option.'project',0);
		
		
		if (!empty($record->id)) {
			$user = Factory::getUser();
			if (!$user->authorise('core.admin', 'com_joomleague') ||
				!$user->authorise('core.admin', 'com_joomleague.project.'.$project_id) ||
				!$user->authorise('core.delete', 'com_joomleague.match.'.(int) $record->id))
			{
				return false;
			}
			return true;
		}
	}
	

	/**
	 * Method to remove a matchday
	 *
	 * @access	public
	 * @return	boolean	True on success
	 */
	function delete(&$pks=array())
	{
		$return = array();
		$app = Factory::getApplication();
		$db = Factory::getDbo();
		
		if($pks)
		{
			$cids = implode(',',$pks);
			
			// match_statistic
			$query = $db->getQuery(true);
			$query->delete('#__joomleague_match_statistic');
			$query->where('match_id IN ('.$cids.')');
			try
			{
			$db->setQuery($query)->execute();
			}
			catch (RuntimeException $e)
			{
			$app->enqueueMessage(Text::_($e->getMessage()), 'error');
			}
			
			// match_staff_statistic
			$query = $db->getQuery(true);
			$query->delete('#__joomleague_match_staff_statistic');
			$query->where('match_id IN ('.$cids.')');
			try
			{
			$db->setQuery($query)->execute();
			}
			catch (RuntimeException $e)
			{
			$app->enqueueMessage(Text::_($e->getMessage()), 'error');
			}
			
			// match_staff
			$query = $db->getQuery(true);
			$query->delete('#__joomleague_match_staff');
			$query->where('match_id IN ('.$cids.')');
			try
			{
			$db->setQuery($query)->execute();
			}
			catch (RuntimeException $e)
			{
			$app->enqueueMessage(Text::_($e->getMessage()), 'error');
			}
			
			// match_event
			$query = $db->getQuery(true);
			$query->delete('#__joomleague_match_event');
			$query->where('match_id IN ('.$cids.')');
			try
			{
			$db->setQuery($query)->execute();
			}
			catch (RuntimeException $e)
			{
			$app->enqueueMessage(Text::_($e->getMessage()), 'error');
			}
	
			// match_referee
			$query = $db->getQuery(true);
			$query->delete('#__joomleague_match_referee');
			$query->where('match_id IN ('.$cids.')');
			try
			{
			$db->setQuery($query)->execute();
			}
			catch (RuntimeException $e)
			{
			$app->enqueueMessage(Text::_($e->getMessage()), 'error');
			}
	
			// match_player
			$query = $db->getQuery(true);
			$query->delete('#__joomleague_match_player');
			$query->where('match_id IN ('.$cids.')');
			try
			{
			$db->setQuery($query)->execute();
			}
			catch (RuntimeException $e)
			{
			$app->enqueueMessage(Text::_($e->getMessage()), 'error');
			}
			
			return parent::delete($pks);
		}
		return array();
	}
	
	
	/**
	 * Returns a Table object, always creating it
	 *
	 * @param	type	The table type to instantiate
	 * @param	string	A prefix for the table class name. Optional.
	 * @param	array	Configuration array for model. Optional.
	 * @return	Table	A database object
	 */
	public function getTable($type = 'Match', $prefix = 'Table', $config = array())
	{
		return Table::getInstance($type, $prefix, $config);
	}
	
	
	/**
	 * Method to get a single record.
	 *[
	 * @param integer $pk
	 *        	The id of the primary key.
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
		$query->select('m.*,p.timezone,CASE m.time_present	when NULL then NULL	else DATE_FORMAT(m.time_present, "%H:%i") END AS time_present,m.extended as matchextended');
        $query->select('t1.name AS hometeam, t1.id AS t1id');
        $query->select('t2.name as awayteam, t2.id AS t2id');
        $query->select('pt1.project_id');
		$query->from('#__joomleague_match AS m');
		$query->join('LEFT','#__joomleague_project_team AS pt1 ON pt1.id=m.projectteam1_id');
		$query->join('LEFT',' #__joomleague_team AS t1 ON t1.id=pt1.team_id');
		$query->join('LEFT',' #__joomleague_project_team AS pt2 ON pt2.id=m.projectteam2_id');
		$query->join('LEFT',' #__joomleague_team AS t2 ON t2.id=pt2.team_id');
		$query->join('LEFT',' #__joomleague_project AS p ON p.id=pt1.project_id');
		$query->where(' m.id = '.$item->id);
		$db->setQuery($query);
		$data = $db->loadObject();
		
		if ($data) {
			JoomleagueHelper::convertMatchDateToTimezone($data);
		}
		
		$item->match_date = $data->match_date;
		$item->timezone = $data->timezone;
		$item->awayteam = $data->awayteam;
		$item->hometeam = $data->hometeam;
		
		$item->t1id = $data->t1id;
		$item->t2id = $data->t2id;
		$item->project_id = $data->project_id;
		$item->matchextended = $data->matchextended;
		
		return $item;
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
		$form = $this->loadForm('com_joomleague.match','match',array('control' => 'jform','load_data' => $loadData));
		if(empty($form))
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
		$data = Factory::getApplication()->getUserState('com_joomleague.edit.match.data', array());
		if (empty($data))
		{
			$data = $this->getItem();
		}
			
		return $data;
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
        
        $data['team1_legs'] = $input->getString('team1_legs');
        $data['team2_legs'] = $input->getString('team2_legs');
		$data['team1_bonus'] = $input->getString('team1_bonus');
		$data['team2_bonus'] = $input->getString('team2_bonus');
		$data['match_result_detail'] = $input->getString('match_result_detail');
		
		$data['team1_result_decision'] = $input->getString('team1_result_decision');
		$data['team2_result_decision'] = $input->getString('team2_result_decision');
		$data['decision_info'] = $input->getString('decision_info');
		$data['team_won'] = $input->getString('team_won');
		
		$data['old_match_id'] = $input->getString('old_match_id');
		$data['new_match_id'] = $input->getString('new_match_id');
		$data['extended'] = $input->get('extended',array(),'array');
	
		if($data['new_match_id'] > 0) {
			$table = $this->getTable();
			if (!$table->load($data['new_match_id']))
			{
				$this->setError(Text::_('COM_JOOMLEAGUE_ADMIN_MATCH_MODEL_OLD_GAME_NOT_FOUND'));
				return false;
			}
			$newdata=array();
			$newdata['old_match_id'] = $data['id'];
			// Bind the form fields to the table row
			if (!$table->bind($newdata))
			{
				$this->setError(Text::_('COM_JOOMLEAGUE_ADMIN_MATCH_MODEL_BINDING_FAILED'));
				return false;
			}
			if (!$table->check())
			{
				$this->setError(Text::_('COM_JOOMLEAGUE_ADMIN_MATCH_MODEL_CHECK_FAILED'));
				return false;
			}
			if (!$table->store(true))
			{
				$this->setError(Text::_('COM_JOOMLEAGUE_ADMIN_MATCH_MODEL_STORE_FAILED'));
				return false;
			}
		}
		if ($data['old_match_id'] > 0) {
			$table = $this->getTable();
			if (!$table->load($data['old_match_id']))
			{
				$this->setError(Text::_('COM_JOOMLEAGUE_ADMIN_MATCH_MODEL_OLD_GAME_NOT_FOUND'));
				return false;
			}
			$newdata=array();
			$newdata['new_match_id'] = $data['id'];
			// Bind the form fields to the table row
			if (!$table->bind($newdata))
			{
				$this->setError(Text::_('COM_JOOMLEAGUE_ADMIN_MATCH_MODEL_BINDING_FAILED'));
				return false;
			}
			if (!$table->check())
			{
				$this->setError(Text::_('COM_JOOMLEAGUE_ADMIN_MATCH_MODEL_CHECK_FAILED'));
				return false;
			}
			if (!$table->store(true))
			{
				$this->setError(Text::_('COM_JOOMLEAGUE_ADMIN_MATCH_MODEL_STORE_FAILED'));
				return false;
			}
		}
		if(parent::save($data))
		{
			$pk = (int) $this->getState($this->getName().'.id');
			$item = $this->getItem($pk);
			
			$project_id = $app->getUserState('com_joomleagueproject',0);
			$cache = Factory::getCache('joomleague.project'.$project_id);
			$cache->clean();
		
			return true;
		}
		
		return false;	
	}
	
	
	
	// function save_array changed for date per match and period results
	// Gucky 2007/05/25
function save_array($cid = null,$post = null,$zusatz = false,$project_id)
	{
		$app 	= Factory::getApplication();
		$input = $app->input;
		$option = $input->getCmd('option');
		$db = $this->getDbo();
		$query = $db->getQuery(true);
		$datatable = '#__joomleague_match';
		$columns = $db->getTableColumns($datatable);

		$fields = array(
				'#__joomleague_match' => $columns
		);

		foreach($fields as $field)
		{
			$query = '';
			$data = null;
			$datafield = array_keys($field);
			if($zusatz)
			{
				$fieldzusatz = $cid;
			}
			foreach($datafield as $keys)
			{
				if(isset($post[$keys . $fieldzusatz]))
				{
					$result = $post[$keys . $fieldzusatz];
					if($keys == 'team1_result_split' || $keys == 'team2_result_split' || $keys == 'homeroster' || $keys == 'awayroster')
					{
						$result = trim(join(';',$result));
					}
					if($keys == 'alt_decision' && $post[$keys . $fieldzusatz] == 0)
					{
						$query .= ",team1_result_decision=NULL,team2_result_decision=NULL,decision_info='',team_won=0";
					}
					if($keys == 'team1_result_decision' && strtoupper($post[$keys . $fieldzusatz]) == 'X' && $post['alt_decision' . $fieldzusatz] == 1)
					{
						$result = '';
					}
					if($keys == 'team2_result_decision' && strtoupper($post[$keys . $fieldzusatz]) == 'X' && $post['alt_decision' . $fieldzusatz] == 1)
					{
						$result = '';
					}
					if(! is_numeric($result) || ($keys == 'match_number'))
					{
						$vorzeichen = "'";
					}
					else
					{
						$vorzeichen = '';
					}
					if(strstr(
							"crowd,formation1,formation2,homeroster,awayroster,show_report,team1_result,
								team1_bonus,team1_legs,team2_result,team2_bonus,team2_legs,
								team1_result_decision,team2_result_decision,team1_result_split,
								team2_result_split,team1_result_ot,team2_result_ot,
								team1_result_so,team2_result_so,team_won,",$keys . ',') && $result == '' && isset($post[$keys . $fieldzusatz]))
					{
						$result = 'NULL';
						$vorzeichen = '';
					}
					if($keys == 'crowd' && $post['crowd' . $fieldzusatz] == '')
					{
						$result = '0';
					}
					if($result != '' || $keys == 'summary' || $keys == 'match_result_detail')
					{
						if($query)
						{
							$query .= ',';
						}
						$query .= $keys . '=' . $vorzeichen . $result . $vorzeichen;
					}

					if($result == '' && $keys == 'time_present')
					{
						if($query)
						{
							$query .= ',';
						}
						$query .= $keys . '=null';
					}

					if($result == '' && $keys == 'match_number')
					{
						if($query)
						{
							$query .= ',';
						}
						$query .= $keys . '=null';
					}
				}
			}
		}
		$user = Factory::getUser();

		$db = Factory::getDbo();
		$query2 = $db->getQuery(true);
		$query2->update('#__joomleague_match');
		$query2->set(array(
				$query,
				'modified = NOW()',
				'modified_by = ' . $user->id
		));
		$query2->where('id = ' . $cid);
		$db->setQuery($query2)->execute();

		// FIXME: ^^^^^^^ remove the table update query part above ^^^^^^
		// lets handle joomla the asset_id
		$table = $this->getTable();

		if($table->load($cid))
		{
			$table->store(true);
		}
		return true;
	}
	

	/**
	 * Method to return a playground/venue array (id,text)
	*
	* @access	public
	* @return	array
	*/
	function getPlaygrounds()
	{
		$app = Factory::getApplication();
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		$query->select(array('id AS value','name AS text'));
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
	 * Method to return teams and match data
	*
	* @access	public
	* @return	array
	*/
	function getMatchTeams($projectteam_id= false,$match_id = false)
	{
		$app = Factory::getApplication();		
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		
		$query->select(array('mc.*'));
		$query->from('#__joomleague_match AS mc');
		
		// join project_team table (pteam1)
		$query->join('INNER', '#__joomleague_project_team AS pt1 ON pt1.id=mc.projectteam1_id');
		
		// join team table (team1)
		$query->select(array('t1.name AS team1'));
		$query->join('INNER', '#__joomleague_team AS t1 ON t1.id=pt1.team_id');
		
		// join project_team table (pteam2)
		$query->join('INNER', '#__joomleague_project_team AS pt2 ON pt2.id=mc.projectteam2_id');
		
		// join team table (team2)
		$query->select(array('t2.name AS team2'));
		$query->join('INNER', '#__joomleague_team AS t2 ON t2.id=pt2.team_id');
		
		// join user table
		$query->select(array('u.name AS editor'));
		$query->join('LEFT', '#__users u ON u.id=mc.checked_out');
		
		// filter - match_id
		$query->where('mc.id = '.$match_id);
		try{	
		$db->setQuery($query);
		$result = $db->loadObject();
		}
		catch (Exception $e)
		{
		$app->enqueueMessage(Text::_($e->getMessage()), 'error');
			return false;
		}	
		return	$result;
	}

	/**
	 * returns starters player id for the specified team
	 *
	 * @param int $team_id
	 * @param int $project_position_id
	 * @return array of player ids
	 */
	function getRoster($team_id, $project_position_id=0,$match_id)
	{
		$app = Factory::getApplication();
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		
		$query->select(array('mp.teamplayer_id AS value'));
		$query->from('#__joomleague_match_player AS mp');
		
		// join team_player table
		$query->select(array('tpl.jerseynumber'));
		$query->join('INNER','#__joomleague_team_player AS tpl ON tpl.id=mp.teamplayer_id');
		
		// join person table
		$query->select(array('pl.firstname','pl.nickname','pl.lastname'));
		$query->join('INNER','#__joomleague_person AS pl ON pl.id=tpl.person_id');
		
		// join project_position table
		$query->join('INNER','#__joomleague_project_position as ppos ON ppos.id = tpl.project_position_id');
		
		// join position table
		$query->select(array('pos.name AS positionname'));
		$query->join('INNER', '#__joomleague_position AS pos ON pos.id=ppos.position_id');
		
		// filter - match_id
		$query->where('mp.match_id = '.(int) $match_id);
		
		// filter - projectteam_id
		$query->where('tpl.projectteam_id = '.$team_id);
	
		// filter - came_in
		$query->where('mp.came_in='.self::MATCH_ROSTER_STARTER);
		
		// filter - published
		$query->where(array('pl.published = 1','tpl.published = 1'));
		
		// filter - project_position_id
		if ($project_position_id > 0)
		{
			$query->where('mp.project_position_id = '.$project_position_id);
		}
		$query->order('ppos.position_id, mp.ordering ASC');
		/* $query .= " ORDER BY ppos.position_id, mp.ordering ASC"; */
		try{
		$db->setQuery($query);
		$result = $db->loadObjectList('value');
		}
		catch (Exception $e)
		{
		$app->enqueueMessage(Text::_($e->getMessage()), 'error');
			return false;
		}	
		return $result;
	}


	/**
	 * returns players who played for the specified team
	 *
	 * @param int $team_id
	 * @param int $project_position_id
	 * @return array of players
	 */
	function getMatchPlayers($projectteam_id, $project_position_id=0,$match_id=false)
	{
		$app = Factory::getApplication();
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		
		$query->select(array('mp.teamplayer_id AS tpid','mp.project_position_id'));
		$query->from('#__joomleague_match_player AS mp');
		
		// join team_player table
		$query->select(array('tpl.projectteam_id'));
		$query->join('INNER','#__joomleague_team_player AS tpl ON tpl.id=mp.teamplayer_id');
		
		// join person table
		$query->select(array('pl.firstname','pl.nickname','pl.lastname'));
		$query->join('INNER','#__joomleague_person AS pl ON pl.id=tpl.person_id');
		
		// join project_position table
		$query->select(array('ppos.position_id','ppos.id AS pposid'));
		$query->join('INNER','#__joomleague_project_position as ppos ON ppos.id = mp.project_position_id');
		
		// filter - match_id
		$query->where('mp.match_id = '.(int) $match_id);
		
		// filter - published
		$query->where(array('pl.published = 1','tpl.published = 1'));
		
		// filter - projectteam_id
		$query->where('tpl.projectteam_id = '.$projectteam_id);
		
		// filter - project_position_id
		if ($project_position_id > 0)
		{
			$query->where('mp.project_position_id='.$project_position_id);
		}
		
		$query->order(array('mp.project_position_id','mp.ordering','tpl.jerseynumber','pl.lastname','pl.firstname ASC'));
		try{
		$db->setQuery($query);
		$result = $db->loadObjectList('tpid');
		}
		catch (Exception $e)
		{
		$app->enqueueMessage(Text::_($e->getMessage()), 'error');
			return false;
		}
		return $result;
	}

	/**
	 * returns players who played for the specified team
	 *
	 * @param int $team_id
	 * @param int $project_position_id
	 * @return array of players
	 */
	function getMatchStaffs($projectteam_id,$project_position_id=0,$match_id)
	{
		$app = Factory::getApplication();
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		
		$query->select(array('mp.team_staff_id','mp.project_position_id'));
		$query->from('#__joomleague_match_staff AS mp');
		
		// join team_staff table
		$query->select('tpl.projectteam_id');
		$query->join('INNER','#__joomleague_team_staff AS tpl ON tpl.id = mp.team_staff_id');
		
		// join person table
		$query->select(array('pl.firstname','pl.nickname','pl.lastname'));
		$query->join('INNER','#__joomleague_person AS pl ON pl.id = tpl.person_id');
		
		// join project_position table
		$query->select(array('ppos.position_id','ppos.id AS pposid'));
		$query->join('INNER','#__joomleague_project_position AS ppos ON ppos.id = mp.project_position_id');
		
		// filter - match_id
		$query->where('mp.match_id = '.(int) $match_id);
		
		// filter - published
		$query->where(array('pl.published = 1','tpl.published = 1'));
		
		// filter - projectteam_id
		$query->where('tpl.projectteam_id = '.$projectteam_id);
		
		// filter - project_position_id
		if ($project_position_id > 0)
		{
			$query->where('mp.project_position_id = '.$project_position_id);
		}
		
		$query->order(array('mp.project_position_id','mp.ordering','pl.lastname','pl.firstname ASC'));
	
		try{
		$db->setQuery($query);
		$result = $db->loadObjectList('team_staff_id');
		}
		catch (Exception $e)
		{
		$app->enqueueMessage(Text::_($e->getMessage()), 'error');
		}

		return $result;
		}

	/**
	 * returns starters referees id for the specified team
	 *
	 * @param int $project_position_id
	 * @return array of referee ids
	 */
	function getRefereeRoster($project_position_id=0,$match_id)
	{
		$app = Factory::getApplication();
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		$query->select('pref.id AS value,pr.firstname,pr.nickname,pr.lastname,pr.email');
		$query->from('#__joomleague_match_referee AS mr');
		$query->join('LEFT',' #__joomleague_project_referee AS pref ON mr.project_referee_id=pref.id AND pref.published = 1');
		$query->join('LEFT',' #__joomleague_person AS pr ON pref.person_id=pr.id AND pr.published = 1');
		$query->where('mr.match_id='.(int) $match_id);
		if ($project_position_id > 0)
		{
		$query->where('mr.project_position_id = '.$project_position_id);	
			//$query .= ' AND mr.project_position_id='.$project_position_id;
		}
		        if ( $project_referee_id )
		{
		$query->where('mr.project_referee_id = '.$project_referee_id);	
		}
		$query->order('mr.project_position_id, mr.ordering ASC');
		
		try{
		$db->setQuery($query);
		$result = $db->loadObjectList('value');
		}
		catch (Exception $e)
		{
		$app->enqueueMessage(Text::_($e->getMessage()), 'error');
		}

		return $result;
	}

	/**
	 * Method to return the projects referees array
	 *
	 * @access	public
	 * @return	array
	 */
	function getProjectReferees($already_sel=false, $project_id)
	{
		$app = Factory::getApplication();
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		
		$query->select(array('pl.firstname','pl.nickname','pl.lastname','pl.info'));
		$query->from('#__joomleague_person AS pl');
		
		// join project_referee table
		$query->select('pref.id AS value');
		$query->join('LEFT', '#__joomleague_project_referee AS pref ON pref.person_id = pl.id');
		
		// join project_position table
		$query->join('LEFT', '#__joomleague_project_position AS ppos ON ppos.id = pref.project_position_id');
		
		// join position table
		$query->select(array('pos.name AS positionname'));
		$query->join('LEFT', '#__joomleague_position AS pos ON pos.id = ppos.position_id');
		
		
		// filter - published
		$query->where(array('pref.published = 1','pl.published = 1'));
		
		// filter - project_id
		$query->where('pref.project_id = '.$project_id);
		
		if (is_array($already_sel) && count($already_sel) > 0)
		{
			$query->where('pref.id NOT IN ('.implode(',',$already_sel).')');
		}
		$query->order('pl.lastname ASC');
		try{
		$db->setQuery($query);		
		$result = $db->loadObjectList('value');
		}
		catch (Exception $e)
		{
		$app->enqueueMessage(Text::_($e->getMessage()), 'error');
		}
		return $result;
	}

	/**
	 * Returns the team players
	 * @param in project team id
	 * @param array teamplayer_id to exclude
	 * @return array
	 */
	function getTeamPlayers($projectteam_id,$filter=false,$default_name_dropdown_list_order=false)
	{
		$app = Factory::getApplication();
		$db 	= Factory::getDbo();
		$query	= $db->getQuery(true);
		
		$query->select(array('pl.firstname','pl.nickname','pl.lastname','pl.info'));
		$query->from('#__joomleague_person AS pl');
		
		// join team_player table
		$query->select(array('tpl.id AS value','tpl.projectteam_id','tpl.jerseynumber','tpl.ordering'));
		$query->join('INNER','#__joomleague_team_player AS tpl ON tpl.person_id=pl.id');
		
		// join project_position table
		$query->select(array('ppos.position_id','ppos.id AS pposid'));
		$query->join('INNER','#__joomleague_project_position AS ppos ON ppos.id=tpl.project_position_id');
		
		// join position table
		$query->select('pos.name AS positionname');
		$query->join('INNER','#__joomleague_position AS pos ON pos.id=ppos.position_id');
		
		// filters
		$query->where('tpl.projectteam_id = '.$db->Quote($projectteam_id));
		$query->where('pl.published = 1');
		$query->where('tpl.published = 1');
		$query->where(array('tpl.injury = 0','tpl.suspension = 0','tpl.away = 0'));
		
		if (is_array($filter) && count($filter) > 0)
		{
			$query->where('tpl.id NOT IN ('.implode(',',$filter).')');
		}
		if (isset($default_name_dropdown_list_order))
		{
			
			$order = array('pos.ordering','pl.lastname ASC');
			
			switch ($default_name_dropdown_list_order)
			{
				case 'lastname':
					$order = 'pl.lastname ASC';
					break;

				case 'firstname':
					$order = 'pl.firstname ASC';
					break;

				case 'position':
					$order = array('pos.ordering','pl.lastname ASC');
					break;
			}
		}
		else
		{
			$order = array('pos.ordering','pl.lastname ASC');
		}
		
		$query->order($order);

		$db->setQuery($query);
		$result = $db->loadObjectList();
		
		return $result;
	}

	/**
	 * Method to return the team players array
	 *
	 * @access	public
	 * @return	array
	 */
	function getGhostPlayer()
	{
		$ghost = new CMSObject();
		$ghost->set('value',0);
		$ghost->set('tpid',0);
		$ghost->set('firstname','');
		$ghost->set('nickname','');
		$ghost->set('lastname',Text::_('COM_JOOMLEAGUE_GLOBAL_UNKNOWN'));
		$ghost->set('info','');
		$ghost->set('positionname','');
		$ghost->set('project_position_id',0);
		return array($ghost);
	}

	function getGhostPlayerbb($projectteam_id)
	{
		$ghost = new CMSObject();
		$ghost->set('value',0);
		$ghost->set('tpid',0);
		$ghost->set('firstname','');
		$ghost->set('nickname','');
		$ghost->set('lastname',Text::_('COM_JOOMLEAGUE_GLOBAL_UNKNOWN'));
		$ghost->set('projectteam_id',$projectteam_id);
		$ghost->set('info','');
		$ghost->set('positionname','');
		$ghost->set('project_position_id',0);
		return array($ghost);
	}


	/**
	 * Returns the team players
	 * @param in project team id
	 * @param array teamplayer_id to exclude
	 * @return array
	 */
	function getTeamStaffs($projectteam_id,$filter=false,$default_name_dropdown_list_order=false)
	{
		$app = Factory::getApplication();
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		
		$query->select(array('pl.firstname','pl.nickname','pl.lastname','pl.info'));
		$query->from('#__joomleague_person AS pl');
		
		// join team_staff table
		$query->select(array('ts.id AS value','ts.projectteam_id'));
		$query->join('INNER','#__joomleague_team_staff AS ts ON ts.person_id=pl.id');
		
		// join project_position table
		$query->select('ppos.position_id');
		$query->join('INNER','#__joomleague_project_position AS ppos ON ppos.id=ts.project_position_id');
		
		// join position table
		$query->select('pos.name AS positionname');
		$query->join('INNER','#__joomleague_position AS pos ON pos.id=ppos.position_id');
		
		// filters
		$query->where('ts.projectteam_id = '.$db->Quote($projectteam_id));
		$query->where(array('pl.published = 1','ts.published = 1'));
		$query->where(array('ts.injury = 0','ts.suspension = 0','ts.away = 0'));
					
		if (is_array($filter) && count($filter) > 0)
		{
			$query->where('ts.id NOT IN ('.implode(',',$filter).')');
		}
		if (isset($default_name_dropdown_list_order))
		{
			switch ($default_name_dropdown_list_order)
			{
				case 'lastname':
					$order = 'pl.lastname ASC';
					break;

				case 'firstname':
					$order = 'pl.firstname ASC';
					break;

				case 'position':
					$order = array('pos.ordering','pl.lastname ASC');
					break;
			}
		}
		else
		{
			$order = 'pl.lastname ASC';
		}
		$query->order($order);
		
		$db->setQuery($query);
		$result = $db->loadObjectList();
		
		return $result;
	}

	
	/**
	 * Method to return the project positions array (id,name)
	 *
	 * @access	public
	 * @return	array
	 */
	function getProjectPositions($id=0)
	{
		$app 	= Factory::getApplication();
		$input = $app->input;
		$option = $input->get('option');
		$project_id = $app->getUserState($option.'project');
		
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		
		$query->select(array('pos.id AS value','pos.name AS text'));
		$query->from('#__joomleague_position AS pos');
		
		// join project_position table
		$query->select('ppos.id AS pposid');
		$query->join('INNER','#__joomleague_project_position AS ppos ON ppos.position_id = pos.id');
		
		// filter - project_position_id
		$query->where('ppos.project_id = '.$project_id);
		
		// filter - persontype
		$query->where('pos.persontype = 1');
		
		// filter - position_id
		if ($id > 0)
		{
			$query->where('ppos.position_id='.$id);
		}
		$query->order('pos.ordering');
		try{
		$db->setQuery($query);
		$result = $db->loadObjectList('value');
		}
		catch (Exception $e)
		{
		$app->enqueueMessage(Text::_($e->getMessage()), 'error');
		return false;		
		}
		return $result;
		}

	/**
	 * Method to return the project positions array (id,name)
	 *
	 * @access	public
	 * @return	array
	 */
	function getProjectPositionsOptions($id=0, $person_type=1)
	{
		$app 	= Factory::getApplication();
		$input = $app->input;
		$option = $input->get('option');
		$project_id = $app->getUserState($option.'project');
		$db		= Factory::getDbo();
		$query = $db->getQuery(true);
		
		$query->select(array('pos.name AS text','pos.id AS posid'));
		$query->from('#__joomleague_position AS pos');
		
		$query->select('ppos.id AS value');
		$query->join('INNER','#__joomleague_project_position AS ppos ON ppos.position_id = pos.id');
		
		// filter
		$query->where(array('ppos.project_id = '.$project_id,'pos.persontype = '.$person_type));

		if ($id > 0)
		{
			$query->where('ppos.position_id = '.$id);
		}
		$query->order('pos.ordering');
		try
		{
		$db->setQuery($query);
		$result = $db->loadObjectList('value');
		}
		catch (Exception $e)
		{
		$app->enqueueMessage(Text::_($e->getMessage()), 'error');
		return false;
		}
		return $result;
	}

	/**
	 * Method to return the project staff positions array (id,name)
	 *
	 * @access	public
	 * @return	array
	 */
	function getProjectStaffPositions($id=0)
	{
		$app 	= Factory::getApplication();
		$input = $app->input;
		$option = $input->get('option');
		$project_id = $app->getUserState($option.'project');
		$db		= Factory::getDbo();
		$query	= $db->getQuery(true);
		$query->select(array('pos.id AS value','pos.name AS text'));
		$query->from('#__joomleague_position AS pos');
		$query->select('ppos.id AS pposid');
		$query->join('INNER', '#__joomleague_project_position AS ppos ON ppos.position_id = pos.id');
		$query->where(array('ppos.project_id = '.$project_id,'pos.persontype = 2'));
		
		if ($id > 0)
		{
			$query->where('ppos.position_id = '.$id);
		}
		$query->order('pos.ordering');
		try{
		$db->setQuery($query);
		$result = $db->loadObjectList('value');
		}
		catch (Exception $e)
		{
		$app->enqueueMessage(Text::_($e->getMessage()), 'error');
		return array();
		}
		return $result;
	}

	
	/**
	 * Method to return the projects referee positions array (id,name)
	 *
	 * @access	public
	 * @return	array
	 */
	function getProjectRefereePositions($id=0)
	{
		$app 	= Factory::getApplication();
		$option = $app->input->get('option');
		$project_id = $app->getUserState($option.'project');
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		
		$query->select(array('pos.name AS text'));
		$query->from('#__joomleague_position AS pos');
		
		$query->select(array('ppos.id AS value','ppos.id AS pposid'));
		$query->join('LEFT','#__joomleague_project_position AS ppos ON ppos.position_id = pos.id');
		
		$query->where(array('ppos.project_id = '.$project_id,'pos.persontype = 3'));
		
		if ($id > 0)
		{
			$query->where('ppos.position_id = '.$id);
		}
		$query->order('pos.ordering');
		try{
			$db->setQuery($query);
			$result = $db->loadObjectList('value');
		}
		catch (Exception $e)
		{
			$app->enqueueMessage(Text::_($e->getMessage()), 'error');
		return array();
		}
		return $result;
	}

	
	/**
	 * Method to update starting lineup list
	 *
	 * @access	public
	 * @return	boolean	True on success
	 */
	function updateRoster($data)
	{
		$app 		= Factory::getApplication();
		$result		= true;
		$positions	= $data['positions'];
		$mid		= $data['mid'];
		$team_id	= $data['team_id'];
		$db			= Factory::getDbo();
		$query 		= $db->getQuery(true);

		// we first remove the records of starter for this team and this game then add them again from updated data.

		$query='	DELETE	mp
					FROM #__joomleague_match_player AS mp
					INNER JOIN #__joomleague_team_player AS tp ON tp.id = mp.teamplayer_id
					WHERE	came_in = '.self::MATCH_ROSTER_STARTER.' AND
							mp.match_id = '.$db->Quote($mid).' AND
							tp.projectteam_id = '.$db->Quote($team_id);	
		
		//$db->setQuery($query)->loadColumn();
		$db->setQuery($query);
		

		try{
			$db->execute();
		}
		catch (Exception $e)
		{
			$app->enqueueMessage(Text::_($e->getMessage()), 'error');
			$result = false;
		}
				
		foreach ($positions AS $project_position_id => $pos)
		{
			if (isset($data['position'.$project_position_id]))
			{
				foreach ($data['position'.$project_position_id] AS $ordering => $player_id)
				{
					$record = Table::getInstance('MatchPlayer','Table');
					$record->match_id			= $mid;
					$record->teamplayer_id		= $player_id;
					$record->project_position_id= $pos->pposid;
					$record->came_in			= self::MATCH_ROSTER_STARTER;
					$record->ordering			= $ordering;
					if (!$record->check())
					{
						$this->setError($record->getError());
						$result=false;
					}
					if (!$record->store())
					{
						$this->setError($record->getError());
						$result=false;
					}
				}
			}
		}
		
		return $result;
	}
	
	
	/**
	 * Method to update starting lineup list
	 *
	 * @access	public
	 * @return	boolean	True on success
	 */
	function updateStaff($data)
	{
		$app 		= Factory::getApplication();
		$result		= true;
		$positions	= $data['staffpositions'];
		$mid		= $data['mid'];
		$team_id	= $data['team_id'];
		$db			= Factory::getDbo();
		$query 		= $db->getQuery(true);

		// we first remove the records of starter for this team and this game,then add them again from updated data.
		
		$query='	DELETE mp
					FROM #__joomleague_match_staff AS mp
					INNER JOIN #__joomleague_team_staff AS tp ON tp.id=mp.team_staff_id
					WHERE	mp.match_id = '.$db->Quote($mid).' AND
							tp.projectteam_id = '.$db->Quote($team_id);
		
		$db->setQuery($query);
		
		try{
			$db->execute();
		}
		catch (Exception $e)
		{
			$app->enqueueMessage(Text::_($e->getMessage()), 'error');
			$result = false;
		}
		
		foreach ($positions AS $project_position_id => $pos)
		{
			if (isset($data['staffposition'.$project_position_id]))
			{
				foreach ($data['staffposition'.$project_position_id] AS $ordering => $player_id)
				{
					$record = Table::getInstance('MatchStaff','Table');
					$record->match_id=$mid;
					$record->team_staff_id=$player_id;
					$record->project_position_id=$pos->pposid;
					$record->ordering=$ordering;
					if (!$record->check())
					{
						$this->setError($record->getError());
						$result=false;
					}
					if (!$record->store())
					{
						$this->setError($record->getError());
						$result=false;
					}
				}
			}
		}
		
		return true;
	}

	
	/**
	 * Method to update starting lineup list
	 *
	 * @access	public
	 * @return	boolean	True on success
	 *
	 */
	function updateReferees($post)
	{
		$mid	= $post['mid'];
		$peid	= array();
		$result	= true;
		$positions 	= $post['positions'];
		$project_id	= $post['project'];
		$db		= Factory::getDbo();
		
		foreach ($positions AS $key=>$pos)
		{
			if (isset($post['position'.$key])) { $peid=array_merge((array) $post['position'.$key],$peid); }
		}
		if ($peid == null)
		{ 
			// Delete all referees assigned to this match
			$query = $db->getQuery(true);
			$query->delete('#__joomleague_match_referee');	
			$query->where('match_id = '.$post['mid']);
		}
		else
		{ 
			// Delete all referees which are not selected anymore from this match
			ArrayHelper::toInteger($peid);
			$peids = implode(',',$peid);
			
			$query = $db->getQuery(true);
			$query->delete('#__joomleague_match_referee');
			$query->where(array('match_id = '.$post['mid'],'project_referee_id NOT IN ('.$peids.')'));
		}
		$db->setQuery($query);	
			try{
			$db->execute();
			}
			catch (Exception $e)
			{
			$app->enqueueMessage(Text::_($e->getMessage()), 'error');
			$result = false;
			}
		foreach ($positions AS $key=>$pos)
		{
			if (isset($post['position'.$key]))
			{
				for ($x=0; $x < count($post['position'.$key]); $x++)
				{
					$project_referee_id = $post['position'.$key][$x];
					
					$query = $db->getQuery(true);
					$query->select('*');
					$query->from('#__joomleague_match_referee');
					$query->where(array('match_id = '.$mid,'project_referee_id = '.$project_referee_id));
					$db->setQuery($query);
					
					if ($result = $db->loadResult())
					{
						$query = $db->getQuery(true);
						$query->update('#__joomleague_match_referee');
						$query->set(array('project_position_id = '.$key,'ordering = '.$x));
						$query->where(array('id = '.$result,'match_id = '.$mid,'project_referee_id = '.$project_referee_id));
					}
					else
					{
						$query	= $db->getQuery(true);
						
						// Insert columns.
						$columns = array('match_id','project_referee_id','project_position_id','ordering');
						
						// Insert values.
						$values = array($mid,$project_referee_id,$key,$x);
						
						// Prepare the insert query.
						$query->insert($db->quoteName('#__joomleague_match_referee'))
						->columns($db->quoteName($columns))
						->values(implode(',', $values));
						
					}
									
					$db->setQuery($query);
				
						try{
							$db->execute();
							}
						catch (Exception $e)
							{
							$app->enqueueMessage(Text::_($e->getMessage()), 'error');
							$result = false;
							}
				}
			}
		}
		return true;
	}

	
	/**
	 * Method to return substitutions made by a team during a match
	 * if no team id is passed,all substitutions should be returned (to be done!!)
	 * @access	public
	 * @return	array of substitutions
	 */
	function getSubstitutions($tid=0,$match_id)
	{
		$app 	= Factory::getApplication();
		$input = $app->input;
		$option = $input->get('option');
		$project_id = $app->getUserState($option.'project');
		$db		= Factory::getDbo();
		
		$in_out	= array();
		
		$query = $db->getQuery(true);
		$query->select(array('mp.*'));
		$query->from('#__joomleague_match_player AS mp');
		
		// join team_player table (tp1)
		$query->select('tp1.id AS value');
		$query->join('LEFT','#__joomleague_team_player AS tp1 ON tp1.id = mp.teamplayer_id');
		
		// join person table (p1)
		$query->select(array('p1.firstname AS firstname','p1.nickname AS nickname','p1.lastname AS lastname'));
		$query->join('LEFT','#__joomleague_person AS p1 ON tp1.person_id = p1.id');
		
		// join team_player table (tp2)
		$query->join('LEFT','#__joomleague_team_player AS tp2 ON tp2.id = mp.in_for');
		
		// join person table (p2)
		$query->select(array('p2.firstname AS out_firstname','p2.nickname AS out_nickname','p2.lastname AS out_lastname'));
		$query->join('LEFT','#__joomleague_person AS p2 ON tp2.person_id = p2.id');
		
		// join project_position table
		$query->join('LEFT','#__joomleague_project_position AS ppos ON mp.project_position_id = ppos.id');
		
		// join position table
		$query->select('pos.name AS in_position');
		$query->join('LEFT','#__joomleague_position AS pos ON ppos.position_id = pos.id');
		
		// join project_position table
		$query->join('LEFT','#__joomleague_project_position AS ppos2 ON ppos2.id = tp1.project_position_id');
		
		// join position table (pos2)
		$query->select('pos2.name AS positionname');
		$query->join('LEFT','#__joomleague_position AS pos2 ON pos2.id = ppos2.position_id');
		
		// join project_position table (ppos3)
		$query->join('LEFT',' #__joomleague_project_position AS ppos3 ON ppos3.id = tp2.project_position_id');
		
		// join position table (pos3)
		$query->select('pos3.name AS positionname_out');
		$query->join('LEFT','#__joomleague_position AS pos3 ON pos3.id = ppos3.position_id');
		
		// filters
		$query->where(array('p1.published = 1','p2.published = 1'));
		$query->where(array('mp.match_id = '.(int) $match_id,'mp.came_in > 0','tp1.projectteam_id = '.$tid));
	
		$query->order('(mp.in_out_time+0)');
		$db->setQuery($query);
		$in_out[$tid] = $db->loadObjectList();
		
		return $in_out;
	}

	/**
	 * Save match details from modal window
	 *
	 * @param array $data
	 * @return boolean
	 */
	function savedetails($data)
	{
		if (!$data['id'])
		{
			$this->setError(Text::_('COM_JOOMLEAGUE_ADMIN_MATCH_MODEL_MATCH_ID_IS_NULL'));
			return false;
		}
		if($data["new_match_id"] >0) {
			$table = $this->getTable();
			if (!$table->load($data['new_match_id']))
			{
				$this->setError(Text::_('COM_JOOMLEAGUE_ADMIN_MATCH_MODEL_OLD_GAME_NOT_FOUND'));
				return false;
			}
			$newdata=array();
			$newdata["old_match_id"]=$data["id"];
			// Bind the form fields to the table row
			if (!$table->bind($newdata))
			{
				$this->setError(Text::_('COM_JOOMLEAGUE_ADMIN_MATCH_MODEL_BINDING_FAILED'));
				return false;
			}
			if (!$table->check())
			{
				$this->setError(Text::_('COM_JOOMLEAGUE_ADMIN_MATCH_MODEL_CHECK_FAILED'));
				return false;
			}
			if (!$table->store(true))
			{
				$this->setError(Text::_('COM_JOOMLEAGUE_ADMIN_MATCH_MODEL_STORE_FAILED'));
				return false;
			}
		}
		if($data["old_match_id"] >0) {
			$table = $this->getTable();
			if (!$table->load($data['old_match_id']))
			{
				$this->setError(Text::_('COM_JOOMLEAGUE_ADMIN_MATCH_MODEL_OLD_GAME_NOT_FOUND'));
				return false;
			}
			$newdata=array();
			$newdata["new_match_id"]=$data["id"];
			// Bind the form fields to the table row
			if (!$table->bind($newdata))
			{
				$this->setError(Text::_('COM_JOOMLEAGUE_ADMIN_MATCH_MODEL_BINDING_FAILED'));
				return false;
			}
			if (!$table->check())
			{
				$this->setError(Text::_('COM_JOOMLEAGUE_ADMIN_MATCH_MODEL_CHECK_FAILED'));
				return false;
			}
			if (!$table->store(true))
			{
				$this->setError(Text::_('COM_JOOMLEAGUE_ADMIN_MATCH_MODEL_STORE_FAILED'));
				return false;
			}
		}
		return parent::save($data);
	}

	/**
	 * save the submitted substitution
	 *
	 * @param array $data
	 * @return boolean
	 */
	function savesubstitution($data)
	{
		$app 	= Factory::getApplication();

		$newId = null;
		
		if (!($data['matchid']))
		{
			$this->setError("in: " . $data['in'].
							", out: " . $data['out'].
							", matchid: " . $data['matchid'].
							", project_position_id: " . $data['project_position_id']);
			return false;
		}
		$player_in				= (int) $data['in'];
		$player_out				= (int) $data['out'];
		$match_id				= (int) $data['matchid'];
		$in_out_time			= $data['in_out_time'];
		$project_position_id 	= $data['project_position_id'];
		$db						= Factory::getDbo();

		if ($project_position_id == 0 && $player_in>0)
		{
			// retrieve normal position of player getting in
			
			$query = $db->getQuery(true);
			$query->select('project_position_id');
			$query->from('#__joomleague_team_player AS tp');
			$query->where('tp.id = '.$db->Quote($player_in));
			
			$db->setQuery($query);
			$project_position_id = $db->loadResult();
		}
		
		if($player_in > 0) 
		{
			$in_player_record 						= Table::getInstance('MatchPlayer','Table');
			$in_player_record->match_id				= $match_id;
			$in_player_record->came_in				= self::MATCH_ROSTER_SUBSTITUTE_IN; //1 //1=came in, 2=went out
			$in_player_record->teamplayer_id		= $player_in;
			$in_player_record->in_for				= ($player_out>0) ? $player_out : 0;
			$in_player_record->in_out_time			= $in_out_time;
			$in_player_record->project_position_id	= $project_position_id;
			
			try
				{
				$in_player_record->store();
				}
			catch (Exception $e)
				{
				$app->enqueueMessage(Text::_($e->getMessage()), 'error');
				$result = false;			
				}
			$newId = $in_player_record->id;
		}
		
		if($player_out>0 && $player_in==0) 
		{
			$out_player_record 						= Table::getInstance('MatchPlayer','Table');
			$out_player_record->match_id			= $match_id;
			$out_player_record->came_in				= self::MATCH_ROSTER_SUBSTITUTE_OUT; //2; //0=starting lineup
			$out_player_record->teamplayer_id		= $player_out;
			$out_player_record->in_out_time			= $in_out_time;
			$out_player_record->project_position_id	= $project_position_id;
			$out_player_record->out					= 1;
			
			try
				{
				$out_player_record->store();
				}
			catch (Exception $e)
				{
				$app->enqueueMessage(Text::_($e->getMessage()), 'error');
				$result = false;			
				}
			$newId = $out_player_record->id;
		}
		return $newId;
	}

	
	/**
	 * delete specified subsitute
	 *
	 * @param int $substitution_id
	 * @return boolean
	 */
	function deleteSubstitution($substitution_id)
	{
		$app 	= Factory::getApplication();
		// the subsitute isn't getting in so we delete the substitution
		$db = Factory::getDbo();	
		$query = $db->getQuery(true);
		$query->delete('#__joomleague_match_player');
		$query->where('(id = '.$db->Quote($substitution_id).' OR id='.$db->Quote($substitution_id + 1).')');
		
		$db->setQuery($query);
		
		try{
		 $db->execute();
		}
			catch (Exception $e)
		{
		$app->enqueueMessage(Text::_($e->getMessage()), 'error');
		$result = false;
		}
		return true;
	}

	
	/**
	 * function to add a new comment
	 * Layout: Editevents
	 */
	function savecomment($data, $project_id)
	{
		$app 	= Factory::getApplication();
		$db = Factory::getDbo();	
		$query = $db->getQuery(true);
		$object = Table::getInstance('MatchEvent','Table');
		$object->bind($data);
		if (!$object->check())
		{
			$this->setError(Text::_('COM_JOOMLEAGUE_ADMIN_MATCH_MODEL_CHECK_FAILED'));
			return false;
		}
		
			try
				{
				$object->store();
				}
			catch (Exception $e)
				{
				$app->enqueueMessage(Text::_($e->getMessage()), 'error');
				$result = false;			
				}
		return $object->id;
	}
	
	
	/**
	 * SaveEvent
	 */
	function saveevent($data, $project_id)
	{
		$app 	= Factory::getApplication();
		$db = Factory::getDbo();	
		$query = $db->getQuery(true);
		$object = Table::getInstance('MatchEvent','Table');
		$object->bind($data);
		if (!$object->check())
		{
			$this->setError(Text::_('COM_JOOMLEAGUE_ADMIN_MATCH_MODEL_CHECK_FAILED'));
			return false;
		}	
			try
				{
				$object->store();
				}
			catch (Exception $e)
				{
				$app->enqueueMessage(Text::_($e->getMessage()), 'error');
				$result = false;			
				}
		return $object->id;
	}

	
	/**
	 * save event (basketball)
	 */
	function saveeventbb($data,$project_id,$match_id)
	{
		$app 	= Factory::getApplication();
		$db = Factory::getDbo();	
		$query = $db->getQuery(true);
		$object = JLTable::getInstance('MatchEvent','Table');
		$object->match_id=(int) $match_id;
		// home players
		for ($x=0; $x < $data['total_h_players'] ; $x++)
		{
			for($e=1; $e < $data['tehp']+1; $e++)
			{
				if ((isset($data['cid_h'.$x])) && (($data['event_sum_h_'.$x.'_'.$e] != "") || ($data['event_time_h_'.$x.'_'.$e] != "") || ($data['notice_h_'.$x.'_'.$e] != "")))
				{
					$object->id 			= $data['event_id_h_'.$x.'_'.$e];
					//$object->project_id 	= $data['project_id'];
					$object->teamplayer_id 	= $data['player_id_h_'.$x];
					$object->projectteam_id = $data['team_id_h_'.$x];
					$object->event_type_id 	= $data['event_type_id_h_'.$x.'_'.$e];
					$object->event_sum 		= ($data['event_sum_h_'.$x.'_'.$e]		== "") ? NULL : $data['event_sum_h_'.$x.'_'.$e] ;
					$object->event_time		= ($data['event_time_h_'.$x.'_'.$e]		== "") ? NULL : $data['event_time_h_'.$x.'_'.$e] ;
					$object->notice			= ($data['notice_h_'.$x.'_'.$e]			== "") ? NULL : $data['notice_h_'.$x.'_'.$e] ;
					$object->notes				= "";
					try
						{
						$object->store(true);
						}
					catch (Exception $e)
						{
						$app->enqueueMessage(Text::_($e->getMessage()), 'error');
						$result = false;			
						}
				}

				if (((isset($data['cid_h'.$x])) && ($data['event_id_h_'.$x.'_'.$e])) && (($data['event_sum_h_'.$x.'_'.$e] == "") && ($data['event_time_h_'.$x.'_'.$e] == "") && ($data['notice_h_'.$x.'_'.$e] == "")))
				{
					$this->deleteevent($data['event_id_h_'.$x.'_'.$e]);
				}
			}
		}
		// away players
		for ($x=0; $x < $data['total_a_players'] ; $x++)
		{
			for($e=1; $e < $data['teap']+1; $e++)
			{
				if ((isset($data['cid_a'.$x])) && (($data['event_sum_a_'.$x.'_'.$e] != "") || ($data['event_time_a_'.$x.'_'.$e] != "") || ($data['notice_a_'.$x.'_'.$e] != "")))
				{
					$object->id 			= $data['event_id_a_'.$x.'_'.$e];
					//$object->project_id 	= $data['project_id'];
					$object->teamplayer_id 	= $data['player_id_a_'.$x];
					$object->projectteam_id	= $data['team_id_a_'.$x];
					$object->event_type_id 	= $data['event_type_id_a_'.$x.'_'.$e];
					$object->event_sum 		= ($data['event_sum_a_'.$x.'_'.$e]		== "") ? NULL : $data['event_sum_a_'.$x.'_'.$e];
					$object->event_time		= ($data['event_time_a_'.$x.'_'.$e]		== "") ? NULL : $data['event_time_a_'.$x.'_'.$e];
					$object->notice			= ($data['notice_a_'.$x.'_'.$e]			== "") ? NULL : $data['notice_a_'.$x.'_'.$e] ;
					$object->notes			= "";
					try
						{
						$object->store(true);
						}
					catch (Exception $e)
						{
						$app->enqueueMessage(Text::_($e->getMessage()), 'error');
						$result = false;			
						}
				}

				if (((isset($data['cid_a'.$x])) && ($data['event_id_a_'.$x.'_'.$e])) && (($data['event_sum_a_'.$x.'_'.$e] == "") && ($data['event_time_a_'.$x.'_'.$e] == "") && ($data['notice_a_'.$x.'_'.$e] == "")))
				{
					$this->deleteevent($data['event_id_a_'.$x.'_'.$e]);

				}
			}
		}
		return true;
	}

	
	/**
	 * SaveStats
	 */
	function savestats($data)
	{
		$match_id = $data['match_id'];
		$db = Factory::getDbo();
		
		if (isset($data['cid']))
		{
			// save all checked rows
			foreach ($data['teamplayer_id'] as $idx => $tpid)
			{
				$teamplayer_id  = $data['teamplayer_id'][$idx];
				$projectteam_id = $data['projectteam_id'][$idx];
				
				// clear previous data
				$query = $db->getQuery(true);
				$query->delete('#__joomleague_match_statistic');
				$query->where(array('match_id = '.$db->Quote($match_id),'teamplayer_id = '.$db->Quote($teamplayer_id)));
				$db->setQuery($query);
				$res = $db->execute();
				
				foreach ($data as $key => $value)
				{
					if (preg_match('/^stat'.$teamplayer_id.'_([0-9]+)/',$key,$reg) && $value!="")
					{
						$statistic_id=$reg[1];
						$stat=Table::getInstance('MatchStatistic','Table');
						$stat->match_id       = $match_id;
						$stat->projectteam_id = $projectteam_id;
						$stat->teamplayer_id  = $teamplayer_id;
						$stat->statistic_id   = $statistic_id;
						$stat->value          = ($value=="") ? null : $value;
						if (!$stat->check())
						{
							echo "stat check failed!"; die();
						}
						if (!$stat->store())
						{
							echo "stat store failed!"; die();
						}
					}
				}
			}
		}
		
		// staff stats
		if (isset($data['staffcid']))
		{
			// save all checked rows
			foreach ($data['team_staff_id'] as $idx => $stid)
			{
				$team_staff_id = $data['team_staff_id'][$idx];
				$projectteam_id = $data['sprojectteam_id'][$idx];
				
				// clear previous data
				$query = $db->getQuery(true);
				$query->delete('#__joomleague_match_staff_statistic');
				$query->where(array('match_id = '. $db->Quote($match_id),'team_staff_id = '. $db->Quote($team_staff_id)));
				$db->setQuery($query);
				$res = $db->execute();
				
				foreach ($data as $key => $value)
				{
					if (ereg('^staffstat'.$team_staff_id.'_([0-9]+)',$key,$reg) && $value!="")
					{
						$statistic_id=$reg[1];
						$stat=Table::getInstance('MatchStaffStatistic','Table');
						$stat->match_id      = $match_id;
						$stat->projectteam_id= $projectteam_id;
						$stat->team_staff_id = $team_staff_id;
						$stat->statistic_id  = $statistic_id;
						$stat->value= ($value=="") ? null : $value;
						if (!$stat->check())
						{
							echo "stat check failed!"; die();
						}
						if (!$stat->store())
						{
							echo "stat store failed!"; die();
						}
					}
				}
			}
		}
		return true;
	}

	
	/**
	 * deleteEvent
	 */
	function deleteevent($event_id)
	{
		$object = Table::getInstance('MatchEvent','Table');
		if (!$object->delete($event_id))
		{
			$this->setError('COM_JOOMLEAGUE_ADMIN_MATCH_MODEL_DELETE_FAILED');
			return false;
		}
		return true;
	}
	
	
	/**
	 * Function to delete a comment
	 * Layout: editevents
	 */
	function deletecomment($comment_id)
	{
		$object = Table::getInstance('MatchEvent','Table');
		if (!$object->delete($comment_id))
		{
			$this->setError('COM_JOOMLEAGUE_ADMIN_MATCH_MODEL_DELETE_FAILED');
			return false;
		}
		return true;
	}
	

	/**
	 * get match events
	 *
	 * @return array
	 */
	function getMatchEvents($match_id=false)
	{
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		$query->select(array('me.*'));
		$query->from('#__joomleague_match_event AS me');

		// join team_player table (tp1)
		$query->join('LEFT','#__joomleague_team_player AS tp1 ON tp1.id=me.teamplayer_id');
		
		// join person table (t1)
		$query->join('LEFT','#__joomleague_person AS t1 ON t1.id=tp1.person_id');
		
		// join project_team table
		$query->join('LEFT','#__joomleague_project_team AS pt ON pt.id=me.projectteam_id');
		
		// join team table
		$query->select('t.name AS team');
		$query->join('LEFT','#__joomleague_team AS t ON t.id=pt.team_id');
		
		// join eventtype table
		$query->select('et.name AS event');
		$query->join('LEFT','#__joomleague_eventtype AS et ON et.id=me.event_type_id');
		
		// join team_player table (tp2)
		$query->join('LEFT','#__joomleague_team_player AS tp2 ON tp2.id=me.teamplayer_id2');
		
		// join person table (t2)
		$query->join('LEFT','#__joomleague_person AS t2 ON t2.id=tp2.person_id');
		
		$query->select('CONCAT(t1.firstname," \'",t1.nickname,"\' ",t1.lastname) AS player1');
		$query->select('CONCAT(t2.firstname," \'",t2.nickname,"\' ",t2.lastname) AS player2');
		
		$query->where(array('t1.published = 1'));
		
		//$query->where(array('t2.published = 1')); 
		 		
		$query->where('me.match_id = '.$db->Quote($match_id));
		$query->order(array('me.event_time DESC','me.id DESC'));
		
		$db->setQuery($query);
		$result = $db->loadObjectList();
		
		return $result;
	}

	
	/**
	 * getMatchStatsInput
	 */
	function getMatchStatsInput($match_id)
	{
		$match = $this->getItem($match_id);
		
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		
		$query->select('*');
		$query->from('#__joomleague_match_statistic');
		
		// filter - match_id
		$query->where('match_id = '.$db->Quote($match->id));
		
		$db->setQuery($query);
		$match_statistics = $db->loadObjectList();
		
		$stats = array($match->projectteam1_id => array(),$match->projectteam2_id => array());
		foreach ($match_statistics as $stat)
		{
			@$stats[$stat->projectteam_id][$stat->teamplayer_id][$stat->statistic_id]=$stat->value;
		}
		return $stats;
	}

	
	/**
	 * getMatchStaffStatsInput
	 */
	function getMatchStaffStatsInput($match_id)
	{
		$match = $this->getItem($match_id);
		
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		
		$query->select('*');
		$query->from('#__joomleague_match_staff_statistic');
		
		// filter - match_id
		$query->where('match_id = '.$db->Quote($match->id));
		
		$db->setQuery($query);
		$staff_statistics = $db->loadObjectList();
		$stats	= array($match->projectteam1_id => array(),$match->projectteam2_id => array());
		
		foreach ((array)$staff_statistics as $stat)
		{
			@$stats[$stat->projectteam_id][$stat->team_staff_id][$stat->statistic_id]=$stat->value;
		}
		
		return $stats;
	}

	
	/**
	 * getPlayerEventsbb
	 */
	function getPlayerEventsbb($teamplayer_id=0,$event_type_id=0,$match_id)
	{
		$ret	= array();
		$record = new stdClass();
		$record->id='';
		$record->event_sum=0;
        $record->event_time="";
        $record->notice="";
		$record->teamplayer_id=$teamplayer_id;
		$record->event_type_id=$event_type_id;
		$ret[0]=$record;
		
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		
		$query->select(array(
				'me.projectteam_id',
				'me.id',
				'me.match_id',
				'me.teamplayer_id',
				'me.event_type_id',
				'me.event_sum',
				'me.event_time',
				'me.notice'
		));
		$query->from('#__joomleague_match_event AS me');
		
		// filter - match_id
		$query->where('me.match_id='.(int) $match_id);
		
		// filter - teamplayer_id
		$query->where('me.teamplayer_id='.(int) $teamplayer_id);
		
		// filter - event_type_id
		$query->where('me.event_type_id=' .(int) $event_type_id);
	
		$query->order('me.teamplayer_id ASC');
		$db->setQuery($query);
		$result = $db->loadObjectList();
		
		if(count($result) > 0)
		{
			return $result;
		}
		return $ret;
	}
	
	
	/**
	 * getEventsOptions
	 */
	function getEventsOptions($project_id,$match_id=false)
	{
		$app 	= Factory::getApplication();
		$db = Factory::getDbo();	
		$query = $db->getQuery(true);
		$query='	SELECT DISTINCT	et.id AS value,
									et.name AS text,
									et.icon AS icon
					FROM #__joomleague_match AS m
					INNER JOIN #__joomleague_project_position AS ppos ON ppos.project_id='.$project_id.'
					INNER JOIN #__joomleague_position_eventtype AS pet ON pet.position_id=ppos.position_id
					INNER JOIN #__joomleague_eventtype AS et ON et.id=pet.eventtype_id
					WHERE m.id='.$db->Quote($match_id).'
                    AND et.published=1';
		$db->setQuery($query);
		$result=$db->loadObjectList();
		if(!$result) return null;
		foreach ($result as $event){$event->text=Text::_($event->text);}
		return $result;
	}
	

	/**
	 * Checkout disabled
	 * @access	public
	 * @see administrator/components/com_joomleague/models/JoomleagueModelItem#checkout($uid)
	 */
	function checkout($uid=null)
	{
		return true;
	}

	
	/**
	 *
	 * @param int $round_id
	 */
	function getRoundMatches($round_id)
	{
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		
		$query->select('*');
		$query->from('#__joomleague_match');
		
		// filter - round_id
		$query->where('round_id = '.$round_id);
		
		// order
		$query->order('match_number');
		
		$db->setQuery($query);
		$result = $db->loadObjectList();
		
		return $result;
	}

	
	/**
	 * getInputStats
	 */
	function getInputStats($match_id)
	{
		require_once JPATH_COMPONENT_ADMINISTRATOR.'/statistics/base.php';
		$match = $this->getItem($match_id);
		$project_id = $match->project_id;
		
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		$query->select(array('stat.id','stat.name','stat.short','stat.class','stat.icon','stat.calculated'));
		$query->from('#__joomleague_statistic AS stat');
		
		// join position_statistic table
		$query->join('INNER','#__joomleague_position_statistic AS ps ON ps.statistic_id=stat.id');
		// join project_position table
		$query->select(array('ppos.position_id AS posid'));
		$query->join('INNER','#__joomleague_project_position AS ppos ON ppos.position_id=ps.position_id');
		
		// filter - where
		$query->where('ppos.project_id = '.  $project_id);
		
		// filter - order
		$query->order(array('stat.ordering','ps.ordering'));
		
		$db->setQuery($query);
		$res = $db->loadObjectList();
		$stats = array();
		foreach ($res as $k => $row)
		{
			$stat = JLGStatistic::getInstance($row->class);
			$stat->bind($row);
			$stat->set('position_id',$row->posid);
			$stats[] = $stat;
		}
		return $stats;
	}

	/**
	 * @param int $project_id
	 * @param int $excludeMatchId
	 */
	function getMatchRelationsOptions($project_id,$excludeMatchId=0)
	{
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		
		$query->select(array('m.id AS value','m.match_date'));
		$query->from('#__joomleague_match AS m');
		
		$query->join('INNER','#__joomleague_project_team AS pt1 ON m.projectteam1_id=pt1.id');
		$query->join('INNER','#__joomleague_project_team AS pt2 ON m.projectteam2_id=pt2.id');
		
		$query->select(array('t1.name AS t1_name'));
		$query->join('INNER','#__joomleague_team AS t1 ON pt1.team_id = t1.id');
		
		$query->select(array('t2.name AS t2_name'));
		$query->join('INNER','#__joomleague_team AS t2 ON pt2.team_id = t2.id');
		
		$query->select(array('p.timezone'));
		$query->join('INNER','#__joomleague_project AS p ON p.id = pt1.project_id');
		
		// filter - project_id
		$query->where('pt1.project_id = '.$db->Quote($project_id));
		
		// filter - match_id
		$query->where('m.id NOT IN ('.$excludeMatchId.')');
	
		// filter - published
		$query->where('m.published = 1');
		
		$query->order(array('m.match_date DESC','t1.short_name'));
		$db->setQuery($query);
		$matches = $db->loadObjectList();
		if ($matches)
		{
			foreach ($matches as $match)
			{
				JoomleagueHelper::convertMatchDateToTimezone($match);
			}
		}
		return $matches;
	}

	
	/**
	 * getProjectRoundCodes
	 */
	function getProjectRoundCodes($project_id)
	{
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		
		$query->select(array('id','roundcode','round_date_first'));
		$query->from('#__joomleague_round');
		
		$query->where('project_id = '.$project_id);
		
		$query->order(array('roundcode','round_date_first ASC'));
		$db->setQuery($query);
		
		return $db->loadObjectList();
	}

	
	/**
	 * prefill the roster with the project team players
	 *
	 * @param int $projecteam_id
	 * @param int $bDeleteCurrrentRoster
	 * 
	 * @author And_One <andone@mfga.at>
	 * @return boolean
	 */
	function prefillMatchPlayersWithProjectteamPlayers($projectteam_id, $bDeleteCurrrentRoster,$match_id)
	{
		$app 	= Factory::getApplication();
		$db = Factory::getDbo();	
		$query = $db->getQuery(true);
		$result = false;
		if($bDeleteCurrrentRoster) {
			$query='DELETE FROM #__joomleague_match_player
					WHERE match_id='.$match_id.'
					AND came_in = '.self::MATCH_ROSTER_STARTER.'
					AND teamplayer_id in (SELECT id from #__joomleague_team_player where projectteam_id = '.$projectteam_id.')
					';
				try
					{
						$db->setQuery($query);
						$db->execute();
					}
				catch (Exception $e)
					{
						$app->enqueueMessage(Text::_($e->getMessage()), 'error');
						$result = false;			
					}
		}
		$roster = $this->getMatchPlayers($projectteam_id,false,$match_id);
		
		if (count($roster)==0)
		{
			$team_players = $this->getTeamPlayers($projectteam_id);
			if(count($team_players == 0)) {
				$this->setError(Text::_('COM_JOOMLEAGUE_ADMIN_MATCH_NO_PLAYERS_MATCH'));
			}
			foreach ($team_players AS $player)
			{
				$record = Table::getInstance('MatchPlayer','Table');
				$record->match_id			= $match_id;
				$record->teamplayer_id		= $player->value;
				$record->project_position_id= $player->pposid;
				$record->came_in			= self::MATCH_ROSTER_STARTER;
				$record->ordering			= $player->ordering;
				if (!$record->check())
				{
					$this->setError($record->getError());
					$result = false;
				}
				if (!$record->store())
				{
					$this->setError($record->getError());
					$result = false;
				} else {
					$result = true;
				}
			}
		} else {
			$result = false;
		}
		return $result;
	}

	/**
	*
	* prefill the roster with the last known match players
	*
	* @param int $projecteam_id
	* @param int $bDeleteCurrrentRoster
	* 
	* @author And_One <andone@mfga.at>
	* @return boolean
	*/

	function prefillMatchPlayersWithLastMatch($projectteam_id, $bDeleteCurrrentRoster,$match_id=false)
	{
		$app 	= Factory::getApplication();
		$db = Factory::getDbo();	
		$query = $db->getQuery(true);
		$result = true;
		if($bDeleteCurrrentRoster) {
			$query='DELETE FROM #__joomleague_match_player
					WHERE match_id='.$match_id.'
					AND came_in = '.self::MATCH_ROSTER_STARTER.'
					AND teamplayer_id in (SELECT id from #__joomleague_team_player where projectteam_id = '.$projectteam_id.')
					';
				try
					{
						$db->setQuery($query);
						$db->execute();
					}
				catch (Exception $e)
					{
						$app->enqueueMessage(Text::_($e->getMessage()), 'error');
						$result = false;			
					}
		}
		$roster = $this->getMatchPlayers($projectteam_id,false,$match_id);
		if (count($roster)==0)
		{
			$team_players = $this->getTeamPlayers($projectteam_id);
			if(count($team_players == 0)) {
				$this->setError(Text::_('COM_JOOMLEAGUE_ADMIN_MATCH_NO_PLAYERS_MATCH'));
			}
			$matchid = 0;
			$query='	SELECT	distinct(mp.match_id) AS match_id
						FROM #__joomleague_match_player AS mp
						INNER JOIN #__joomleague_match AS m ON m.id = mp.match_id
						INNER JOIN #__joomleague_round AS r ON r.id = m.round_id
						INNER JOIN #__joomleague_team_player AS tpl ON tpl.id=mp.teamplayer_id
						INNER JOIN #__joomleague_person AS pl ON pl.id=tpl.person_id
						INNER JOIN #__joomleague_project_position as ppos ON ppos.id = mp.project_position_id
						WHERE pl.published = 1 AND
						tpl.projectteam_id='.$projectteam_id;
			$query .= " ORDER BY mp.id desc LIMIT 1";
			$db->setQuery($query);
			$match_players = $db->loadObjectList();
			
			if ($result) {
			  $matchid = $match_players[0]->match_id;
			}

			if($matchid>0) {
				$query='SELECT mp.match_id, mp.teamplayer_id, mp.project_position_id, mp.ordering
						FROM #__joomleague_match_player AS mp
						WHERE	mp.match_id='.$matchid.'
						AND came_in = '.self::MATCH_ROSTER_STARTER.'
						';
				$db->setQuery($query);
				$match_players=$db->loadObjectList();
				foreach ($match_players as $k => $player)
				{
					$record = Table::getInstance('MatchPlayer','Table');
					$record->match_id			= $match_id;
					$record->teamplayer_id		= $player->teamplayer_id;
					$record->project_position_id= $player->project_position_id;
					//$record->came_in			= self::MATCH_ROSTER_STARTER;
					$record->ordering			= $player->ordering;
					if (!$record->check())
					{
						$this->setError($record->getError());
						$result = false;
					}
					if (!$record->store())
					{
						$this->setError($record->getError());
						$result = false;
					} else {
						$result = true;
					}
				}
			} else {
				$teams = $this->getMatchTeams($projectteam_id,$match_id);
				$teamname = ($projectteam_id == $teams->projectteam1_id) ? $teams->team1 : $teams->team2;
				$this->setError(Text::sprintf('COM_JOOMLEAGUE_ADMIN_MATCH_MODEL_NO_LAST_MATCH_ROSTER',$teamname));
				$result = false;
			}
		} else {
			$result = false;
		}
		return $result;
	}

}
