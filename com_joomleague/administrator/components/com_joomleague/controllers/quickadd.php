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
 * Quickadd Controller
 */
class JoomleagueControllerQuickadd extends JLGControllerAdmin
{

	public function __construct()
	{
		parent::__construct();
		
		// Register Extra tasks
		$this->registerTask('searchplayer','searchPlayer');
		$this->registerTask('searchstaff','searchStaff');
		$this->registerTask('searchreferee','searchReferee');
		$this->registerTask('searchteam','searchTeam');
		$this->registerTask('addplayer','addPlayer');
		$this->registerTask('addstaff','addstaff');
		$this->registerTask('addreferee','addReferee');
		$this->registerTask('addteam','addTeam');
	}

	/**
	 * searchPlayer
	 */
	public function searchPlayer()
	{
		header('Content-Type: application/json');
		
		$app = Factory::getApplication();
		$input = $app->input;
		$option = $input->getCmd('option');
		
		$model = JLGModel::getInstance('Quickadd','JoomleagueModel');
		$query = $input->getString('q');
		$projectteam_id = $input->getInt('projectteam_id');
		$results = $model->getNotAssignedPlayers($query,$projectteam_id);
		$response = array(
				"totalCount" => count($results),
				"rows" => array()
		);
		
		$names = array();
		foreach($results as $row)
		{
			$name = JoomleagueHelper::formatName(null,$row->firstname,$row->nickname,$row->lastname,0) . " [" . $row->id . "]";
			$names[] = $name;
			$response["rows"][] = array(
					"id" => $row->id,
					"name" => $name
			);
		}
		
		$suggestions = $names;
		
		// Send the response.
		echo '{ "suggestions": ' . json_encode($suggestions) . ' }';
		$app->close();
	}

	/**
	 * searchReferee
	 */
	public function searchReferee()
	{
		header('Content-Type: application/json');
		
		$app = Factory::getApplication();
		$input = $app->input;
		$option = $input->getCmd('option');
		$query = $input->getString('q');
		$projectid = $app->getUserState($option . "project");
		
		$model = JLGModel::getInstance('Quickadd','JoomleagueModel');
		$results = $model->getNotAssignedReferees($query,$projectid);
		$response = array(
				"totalCount" => count($results),
				"rows" => array()
		);
		
		$names = array();
		foreach($results as $row)
		{
			$name = JoomleagueHelper::formatName(null,$row->firstname,$row->nickname,$row->lastname,0) . " [" . $row->id . "]";
			$names[] = $name;
			$response["rows"][] = array(
					"id" => $row->id,
					"name" => $name
			);
		}
		
		$suggestions = $names;
		
		// Send the response.
		echo '{ "suggestions": ' . json_encode($suggestions) . ' }';
		$app->close();
	}

	/**
	 * searchStaff
	 */
	public function searchStaff()
	{
		header('Content-Type: application/json');
		
		$app = Factory::getApplication();
		$input = $app->input;
		$option = $input->getCmd('option');
		
		$model = JLGModel::getInstance('Quickadd','JoomleagueModel');
		$query = $input->get->getString('q');
		$projectteam_id = $input->getInt("projectteam_id");
		$results = $model->getNotAssignedStaff($query,$projectteam_id);
		$response = array(
				"totalCount" => count($results),
				"rows" => array()
		);
		
		$names = array();
		foreach($results as $row)
		{
			$name = JoomleagueHelper::formatName(null,$row->firstname,$row->nickname,$row->lastname,0) . " [" . $row->id . "]";
			$names[] = $name;
			$response["rows"][] = array(
					"id" => $row->id,
					"name" => $name
			);
		}
		
		$suggestions = $names;
		
		// Send the response.
		echo '{ "suggestions": ' . json_encode($suggestions) . ' }';
		$app->close();
	}

	/**
	 * SearchTeam
	 */
	public function searchTeam()
	{
		header('Content-Type: application/json');
		
		$app = Factory::getApplication();
		$input = $app->input;
		$option = $input->getCmd('option');
		
		$model = JLGModel::getInstance('Quickadd','JoomleagueModel');
		$query = $input->getString('q');
		$projectid = $app->getUserState($option . "project");
		$results = $model->getNotAssignedTeams($query,$projectid);
		
		$response = array(
				"totalCount" => count($results),
				"rows" => array()
		);
		
		$names = array();
		foreach($results as $row)
		{
			$names[] = $row->name . " [" . $row->id . "]";
			$name = $row->name;
			$name .= " (" . $row->info . ")";
			$name .= " (" . $row->id . ")";
			
			$response["rows"][] = array(
					"id" => $row->id,
					"name" => $name
			);
		}
		
		$suggestions = $names;
		
		// Send the response.
		echo '{ "suggestions": ' . json_encode($suggestions) . ' }';
		Factory::getApplication()->close();
	}

	/**
	 * addPlayer
	 * @todo: move some code to model
	 */
	public function addPlayer()
	{
		$app = Factory::getApplication();
		$input = $app->input;
		$searchText = $input->getString('p');
		$projectteam_id = $input->getInt('projectteam_id');
		
		if(empty($searchText))
		{
			$app->enqueueMessage(Text::_('Fill in a valid person'),'warning');
			$this->setRedirect("index.php?option=com_joomleague&view=teamplayers&project_team_id=" . $projectteam_id);
			return;
		}
		
		$personId = false;
		
		// Retrieve person-id
		$personId = JoomleagueHelper::getContentBetweenDelimiters($searchText,'[',']');
		
		// It is possible that a text is passed without the brackets
		// in that case no id will be there so let's check if the name exists
		if($personId == false)
		{
			$db = Factory::getDbo();
			$query = $db->getQuery(true);
			$query->select('id');
			$query->from('#__joomleague_person');
			$query->where('CONCAT(firstname, " ",lastname) LIKE ' . $db->quote('%' . $searchText . '%'));
			$db->setQuery($query);
			$result = $db->loadObjectList();
				
			if(count($result) > 1)
			{
				$app->enqueueMessage(Text::_('Multiple persons were found<br>no person was added'),'warning');
				$this->setRedirect("index.php?option=com_joomleague&view=teamplayers&project_team_id=" . $projectteam_id);
				return;
			}
				
			if(empty($result))
			{
				$app->enqueueMessage(Text::_('Person with that name was not found'),'warning');
				$this->setRedirect("index.php?option=com_joomleague&view=teamplayers&project_team_id=" . $projectteam_id);
				return;
			}
		}
		
		if(!is_int($personId) ? (ctype_digit($personId)) : true)
		{
			// take $personid
		}
		else
		{
			$personId = false;
		}
		
		if(empty($personId))
		{
			$app->enqueueMessage(Text::_('No person was found'),'warning');
			$this->setRedirect("index.php?option=com_joomleague&view=teamplayers&project_team_id=" . $projectteam_id);
			return;
		}
		
		
		// check if person is already attached to the project as a player
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		$query->select('person_id');
		$query->from('#__joomleague_team_player');
		$query->where('projectteam_id = ' . $db->quote($projectteam_id));
		$query->where('person_id =' . $db->quote($personId));
		$db->setQuery($query);
		$result = $db->loadObjectList();
		
		if($result)
		{
			$app->enqueueMessage(Text::_('Person already attached as player'),'warning');
			$this->setRedirect("index.php?option=com_joomleague&view=teamplayers&project_team_id=" . $projectteam_id);
			return;
		}
		
		if(!$result)
		{
			$tblTeamplayer = Table::getInstance('TeamPlayer','Table');
			$tblTeamplayer->person_id = $personId;
			$tblTeamplayer->projectteam_id = $projectteam_id;
				
			$tblProjectTeam = Table::getInstance('ProjectTeam','Table');
			$tblProjectTeam->load($projectteam_id);
			if(!$tblTeamplayer->check())
			{
				$this->setError($tblTeamplayer->getError());
			}
			// Get data from person
			$query = $db->getQuery(true);
			$query->select('pl.picture, pl.position_id');
			$query->from('#__joomleague_person AS pl');
			$query->where('pl.id=' . $db->Quote($personId));
			$query->where('pl.published = 1');
			$db->setQuery($query);
			$person = $db->loadObject();
			if($person)
			{
				$query = $db->getQuery(true);
				$query->select('id');
				$query->from('#__joomleague_project_position');
				$query->where('position_id = ' . $db->Quote($person->position_id));
				$query->where('project_id  = ' . $db->Quote($tblProjectTeam->project_id));
				$db->setQuery($query);
				if($resPrjPosition = $db->loadObject())
				{
					$tblTeamplayer->project_position_id = $resPrjPosition->id;
				}
				$tblTeamplayer->picture = $person->picture;
				$tblTeamplayer->projectteam_id = $projectteam_id;
			}
			$query = $db->getQuery(true);
			$query->select('MAX(ordering) AS count');
			$query->from('#__joomleague_team_player');
			$db->setQuery($query);
			$ts = $db->loadObject();
			$tblTeamplayer->ordering = (int) $ts->count + 1;
			if(! $tblTeamplayer->store())
			{
				$this->setError($tblTeamplayer->getError());
			}
		}
		$app->enqueueMessage(Text::_('COM_JOOMLEAGUE_ADMIN_QUICKADD_CTRL_PERSON_ASSIGNED'));
		$this->setRedirect("index.php?option=com_joomleague&view=teamplayers&project_team_id=" . $projectteam_id);
	}

	/**
	 * addStaff
	 * @todo: move some code to model
	 */
	public function addStaff()
	{
		$app = Factory::getApplication();
		$input = $app->input;
		$db = Factory::getDbo();
		$searchText = $input->getString('p');
		$projectteam_id = $input->getInt('projectteam_id',0);
		
		if(empty($searchText))
		{
			$app->enqueueMessage(Text::_('Fill in a valid person'),'warning');
			$this->setRedirect("index.php?option=com_joomleague&view=teamstaffs&project_team_id=" . $projectteam_id);
			return;
		}
		
		$personId = false;
		
		// Retrieve person-id
		$personId = JoomleagueHelper::getContentBetweenDelimiters($searchText,'[',']');
		
		// It is possible that a text is passed without the brackets
		// in that case no id will be there so let's check if the name exists
		if($personId == false)
		{
			$db = Factory::getDbo();
			$query = $db->getQuery(true);
			$query->select('id');
			$query->from('#__joomleague_person');
			$query->where('CONCAT(firstname, " ",lastname) LIKE ' . $db->quote('%' . $searchText . '%'));
			$db->setQuery($query);
			$result = $db->loadObjectList();
			
			if(count($result) > 1)
			{
				$app->enqueueMessage(Text::_('Multiple persons were found<br>no person was added'),'warning');
				$this->setRedirect("index.php?option=com_joomleague&view=teamstaffs&project_team_id=" . $projectteam_id);
				return;
			}
			
			if(empty($result))
			{
				$app->enqueueMessage(Text::_('Person with that name was not found'),'warning');
				$this->setRedirect("index.php?option=com_joomleague&view=teamstaffs&project_team_id=" . $projectteam_id);
				return;
			}
		}
		
		if(! is_int($personId) ? (ctype_digit($personId)) : true)
		{
			// $personid
		}
		else
		{
			$personId = false;
		}
		
		if(empty($personId))
		{
			$app->enqueueMessage(Text::_('No person was found'),'warning');
			$this->setRedirect("index.php?option=com_joomleague&view=teamstaffs&project_team_id=" . $projectteam_id);
			return;
		}
		
		// check if person is already attached to the project as a staff-member
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		$query->select('person_id');
		$query->from('#__joomleague_team_staff');
		$query->where('projectteam_id = ' . $db->quote($projectteam_id));
		$query->where('person_id =' . $db->quote($personId));
		$db->setQuery($query);
		$result = $db->loadObjectList();
		
		if($result)
		{
			$app->enqueueMessage(Text::_('Person already attached as staff-member'),'warning');
			$this->setRedirect("index.php?option=com_joomleague&view=teamstaffs&project_team_id=" . $projectteam_id);

			return;
		}
		
		if(! $result)
		{
			$tblTeamstaff = Table::getInstance('TeamStaff','Table');
			$tblTeamstaff->person_id = $personId;
			$tblTeamstaff->projectteam_id = $projectteam_id;
			
			$tblProjectTeam = Table::getInstance('ProjectTeam','Table');
			$tblProjectTeam->load($projectteam_id);
			if(! $tblTeamstaff->check())
			{
				$this->setError($tblTeamstaff->getError());
			}
			// Get data from person
			$query = $db->getQuery(true);
			$query->select('pl.picture, pl.position_id');
			$query->from('#__joomleague_person AS pl');
			$query->where('pl.id=' . $db->Quote($personId));
			$query->where('pl.published = 1');
			$db->setQuery($query);
			$person = $db->loadObject();
			if($person)
			{
				$query = $db->getQuery(true);
				$query->select('id');
				$query->from('#__joomleague_project_position');
				$query->where('position_id = ' . $db->Quote($person->position_id));
				$query->where('project_id  = ' . $db->Quote($tblProjectTeam->project_id));
				$db->setQuery($query);
				if($resPrjPosition = $db->loadObject())
				{
					$tblTeamstaff->project_position_id = $resPrjPosition->id;
				}
				$tblTeamstaff->picture = $person->picture;
				$tblTeamstaff->projectteam_id = $projectteam_id;
			}
			$query = $db->getQuery(true);
			$query->select('MAX(ordering) AS count');
			$query->from('#__joomleague_team_staff');
			$db->setQuery($query);
			$ts = $db->loadObject();
			$tblTeamstaff->ordering = (int) $ts->count + 1;
			if(! $tblTeamstaff->store())
			{
				$this->setError($tblTeamstaff->getError());
			}
		}
		$app->enqueueMessage(Text::_('COM_JOOMLEAGUE_ADMIN_QUICKADD_CTRL_PERSON_ASSIGNED'));
		$this->setRedirect("index.php?option=com_joomleague&view=teamstaffs&project_team_id=" . $projectteam_id);
	}

	/**
	 * Function to add Referees
	 */
	public function addReferee()
	{
		$app = Factory::getApplication();
		$input = $app->input;
		$option = $input->getCmd('option');
		$db = Factory::getDbo();
		$searchText = $input->getString('p');
		$project_id = $app->getUserState($option . "project");
		
		if(empty($searchText))
		{
			$app->enqueueMessage(Text::_('Fill in a valid Referee'),'warning');
			$this->setRedirect("index.php?option=com_joomleague&view=projectreferees&projectid=" . $project_id);
			return;
		}
		
		$text = false;
		
		// Retrieve person-id
		if(($pos = strpos($searchText,"[")) !== FALSE)
		{
			$text = substr($searchText,$pos + 1);
			$text = str_replace(']','',$text);
		}
		
		// It is possible that a text is passed without the brackets
		// in that case no id will be there so let's check if the name exists
		if($text == false)
		{
			$db = Factory::getDbo();
			$query = $db->getQuery(true);
			$query->select('id');
			$query->from('#__joomleague_person');
			$query->where('CONCAT(firstname, " ",lastname) LIKE ' . $db->quote('%' . $searchText . '%'));
			$db->setQuery($query);
			$result = $db->loadObjectList();
			
			if(count($result) > 1)
			{
				$app->enqueueMessage(Text::_('Multiple persons were found<br>no person was added'),'warning');
				$this->setRedirect("index.php?option=com_joomleague&view=projectreferees&projectid=" . $project_id);
				return;
			}
			
			if(empty($result))
			{
				$app->enqueueMessage(Text::_('Person with that name was not found'),'warning');
				$this->setRedirect("index.php?option=com_joomleague&view=projectreferees&projectid=" . $project_id);
				return;
			}
		}
		
		if(! is_int($text) ? (ctype_digit($text)) : true)
		{
			$personId = $text;
		}
		else
		{
			$personId = false;
		}
		
		if(empty($personId))
		{
			$app->enqueueMessage(Text::_('No person was found'),'warning');
			$this->setRedirect("index.php?option=com_joomleague&view=projectreferees&projectid=" . $project_id);
			return;
		}
		
		// check if person is already attached to the project as a referee
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		$query->select('person_id');
		$query->from('#__joomleague_project_referee');
		$query->where('project_id = ' . $db->quote($project_id));
		$query->where('person_id =' . $db->quote($personId));
		$db->setQuery($query);
		$result = $db->loadObjectList();
		
		if($result)
		{
			$app->enqueueMessage(Text::_('Referee already attached'),'warning');
			$this->setRedirect("index.php?option=com_joomleague&view=projectreferees&projectid=" . $project_id);
			return;
		}
		
		if(! $result)
		{
			$tblProjectReferee = Table::getInstance('ProjectReferee','Table');
			$tblProjectReferee->person_id = $personId;
			$tblProjectReferee->project_id = $project_id;
			
			if(! $tblProjectReferee->check())
			{
				$this->setError($tblProjectReferee->getError());
			}
			
			// Get data from person
			$query = $db->getQuery(true);
			$query->select(array(
					'pl.picture',
					'pl.position_id'
			));
			$query->from('#__joomleague_person AS pl');
			$query->where('pl.id = ' . $db->Quote($personId));
			$query->where('pl.published = 1');
			$db->setQuery($query);
			$person = $db->loadObject();
			
			if(empty($person))
			{
				$app->enqueueMessage(Text::_('Fill in a valid Referee'),'warning');
				$this->setRedirect("index.php?option=com_joomleague&view=projectreferees&projectid=" . $project_id);
				return;
			}
			
			if($person)
			{
				$query = $db->getQuery(true);
				$query->select('id');
				$query->from('#__joomleague_project_position');
				$query->where('position_id = ' . $db->Quote($person->position_id));
				$query->where('project_id = ' . $db->Quote($project_id));
				$db->setQuery($query);
				$result = $db->loadObject();
				
				if($result)
				{
					$tblProjectReferee->project_position_id = $result->id;
				}
				
				$tblProjectReferee->picture = $person->picture;
				$tblProjectReferee->project_id = $project_id;
			}
			
			$query = $db->getQuery(true);
			$query->select('max(ordering) AS count');
			$query->from('#__joomleague_project_referee');
			$db->setQuery($query);
			$pref = $db->loadObject();
			
			$tblProjectReferee->ordering = (int) $pref->count + 1;
			
			if(! $tblProjectReferee->store())
			{
				$this->setError($tblProjectReferee->getError());
			}
		}
		$msg = Text::_('COM_JOOMLEAGUE_ADMIN_QUICKADD_CTRL_PERSON_ASSIGNED');
		$this->setRedirect("index.php?option=com_joomleague&view=projectreferees&projectid=" . $project_id,$msg);
	}

	public function addTeam()
	{
		$app = Factory::getApplication();
		$input = $app->input;
		$db = Factory::getDbo();
		
		// catch variables
		$option = $input->getCmd('option');
		$searchText = $input->getString("p","");
		$project_id = $app->getUserState($option . "project");
		
		if(empty($searchText))
		{
			$app->enqueueMessage(Text::_('Fill in a teamname'),'warning');
			$this->setRedirect("index.php?option=com_joomleague&view=projectteams&projectid=" . $project_id);
			return;
		}
		
		$text = false;
		
		// Retrieve Team-id
		if(($pos = strpos($searchText,"[")) !== FALSE)
		{
			$text = substr($searchText,$pos + 1);
			$text = str_replace(']','',$text);
		}
		
		// It is possible that a text is passed without the brackets
		// in that case no id will be there so let's check if the name exists
		if($text == false)
		{
			$db = Factory::getDbo();
			$query = $db->getQuery(true);
			$query->select('id');
			$query->from('#__joomleague_team');
			$query->where('name = ' . $db->quote($searchText));
			$db->setQuery($query);
			$result = $db->loadObjectList();
			
			if(count($result) > 1)
			{
				$app->enqueueMessage(Text::_('Multiple teams with that name were found<br>no team is added'),'warning');
				$this->setRedirect("index.php?option=com_joomleague&view=projectteams&projectid=" . $project_id);
				return;
			}
			
			if(empty($result))
			{
				$app->enqueueMessage(Text::_('Team with that name was not found'),'warning');
				$this->setRedirect("index.php?option=com_joomleague&view=projectteams&projectid=" . $project_id);
				return;
			}
		}
		
		if(! is_int($text) ? (ctype_digit($text)) : true)
		{
			$teamId = $text;
		}
		else
		{
			$teamId = false;
		}
		
		if(empty($teamId) || $teamId == null)
		{
			$app->enqueueMessage(Text::_('Team does not exist.<br>No team was added'),'warning');
			$this->setRedirect("index.php?option=com_joomleague&view=projectteams&projectid=" . $project_id);
			return;
		}
		
		// At this point we do have a project+teamid
		// -> check if team already belongs to project
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		$query->select('id');
		$query->from('#__joomleague_project_team');
		$query->where('project_id = ' . $project_id);
		$query->where('team_id = ' . $teamId);
		$db->setQuery($query);
		$result = $db->loadResult();
		
		if($result)
		{
			// the team was already added so don't add it twice
			$app->enqueueMessage(Text::_('Team already exists<br>No team was added'),'warning');
			$this->setRedirect("index.php?option=com_joomleague&view=projectteams&projectid=" . $project_id);
			return;
		}
		
		// Add team to projectteam
		$new = Table::getInstance('ProjectTeam','Table');
		$new->team_id = $teamId;
		$new->project_id = $project_id;
		
		// Set ordering to the last item if not set
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		$query->select('MAX(ordering)');
		$query->from('#__joomleague_project_team');
		$query->where('project_id = ' . $project_id);
		$db->setQuery($query);
		$max = $db->loadResult();
		$new->ordering = $max + 1;
		
		if(! $new->check())
		{
			$this->setError($new->getError());
		}
		
		// Get data from team
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		$query->select('t.picture');
		$query->from('#__joomleague_team AS t');
		$query->where('t.id = ' . $teamId);
		$db->setQuery($query);
		$team = $db->loadObject();
		
		if($team)
		{
			$new->picture = $team->picture;
		}
		if(! $new->store())
		{
			$this->setError($new->getError());
		}
		
		// @todo fix!
		//$errors = $this->getErrors();
		if($errors)
		{
			 $app->enqueueMessage($errors,'error'); 
		}
		
		$msg = Text::_('COM_JOOMLEAGUE_ADMIN_QUICKADD_CTRL_TEAM_ASSIGNED');
		$this->setRedirect("index.php?option=com_joomleague&view=projectteams&projectid=" . $project_id,$msg);
	}
}
