<?php
/**
 * Joomleague
 *
 * @copyright	Copyright (C) 2006-2016 joomleague.at. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @link		http://www.joomleague.at
 */

// Check to ensure this file is included in Joomla!
use Joomla\CMS\Factory;
use Joomla\CMS\Mail\Mail;

defined('_JEXEC') or die;

require_once JLG_PATH_SITE.'/models/project.php';

class JoomleagueModelPerson extends JoomleagueModelProject
{

	var $projectid		= 0;
	var $personid		= 0;
	var $teamplayerid	= 0;
	var $person			= null;
	var $teamplayer		= null;

	/**
	 * store person info specific to the project
	 * @var object
	 */
	protected $_inproject   = null;
	/**
	 * data array for player history
	 * @var array
	 */
	protected $_playerhistory = null;



	public function __construct($config = array())
	{
		parent::__construct($config);
		$app = Factory::getApplication();
		$this->projectid	= $app->input->getInt( 'p', 0 );
		$this->personid		= $app->input->getInt( 'pid', 0 );
		$this->teamplayerid	= $app->input->getInt( 'pt', 0 );
		$this->divisionid   = $app->input->getInt( 'division', 0 );
	}

	function getPerson()
	{
		if ( is_null( $this->person ) && $this->personid > 0)
		{
		    $db = Factory::getDbo();
		    $query = $db->getQuery(true);
			$query = '	SELECT p.*,  
						CASE WHEN CHAR_LENGTH( p.alias ) THEN CONCAT_WS( \':\', p.id, p.alias ) ELSE p.id END AS slug  
						FROM #__joomleague_person AS p 
						WHERE p.id = '. $db->Quote($this->personid)
					;
			$db->setQuery($query);
			$this->person = $db->loadObject();
		}
		return $this->person;
	}

	function &getReferee()
	{
		if ( is_null( $this->_inproject ) )
		{
		    $db = Factory::getDbo();
		    $query = $db->getQuery(true);
			$query = '	SELECT tp.*, pos.name AS position_name 
						FROM #__joomleague_project_referee AS tp 
						INNER JOIN #__joomleague_position AS pos ON pos.id = tp.project_position_id 
						WHERE tp.project_id = '. $db->Quote($this->projectid) .'
						AND tp.person_id = '. $db->Quote($this->personid)
					;
			$db->setQuery($query);
			$this->_inproject = $db->loadObject();
		}
		return $this->_inproject;
	}

	function getPositionEventTypes( $positionId = 0 )
	{
		$result = array();
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		$query = '	SELECT	pet.*,
					et.name,
					et.icon
	
					FROM #__joomleague_position_eventtype AS pet
					INNER JOIN #__joomleague_eventtype AS et ON et.id = pet.eventtype_id
					INNER JOIN #__joomleague_match_event AS me ON et.id = me.event_type_id
					WHERE me.project_id=' . $this->projectid;

		if ( $positionId > 0 )
		{
			$query .= ' AND pet.position_id = ' . (int)$positionId;
		}
		$query .= ' ORDER BY pet.ordering';

		$db->setQuery( $query );
		$result = $db->loadObjectList();

		if ( $result )
		{
			if ( $positionId )
			{
				return $result;
			}
			else
			{
				$posEvents = array();
				foreach ( $result as $r )
				{
					$posEvents[$r->position_id][] = $r;
				}
				return ( $posEvents );
			}
		}
		return array();
	}


	/**
	 * get person history across all projects, with team, season, position,... info
	 *
	 * @param int $person_id , linked to player_id from Person object
	 * @param int $order ordering for season and league, default is ASC ordering
	 * @param string $filter e.g. "s.name = 2007/2008", default empty string
	 * @return array of objects
	 */
	function getRefereeHistory($order = 'ASC')
	{
		$personid = $this->personid;
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		$query = ' SELECT	p.id AS person_id, '
				. ' tt.project_id, '
				. ' p.firstname AS fname, '
				. ' p.lastname AS lname, '
				. ' pj.name AS pname, '
				. ' s.name AS sname, '
				. ' pos.name AS position, '
				. ' COUNT(mr.id) AS matchesCount '
				. ' FROM #__joomleague_match_referee AS mr '
				. ' INNER JOIN #__joomleague_match AS m ON m.id = mr.match_id '
				. ' INNER JOIN #__joomleague_person AS p ON p.id = mr.project_referee_id '
				. ' INNER JOIN #__joomleague_project_team AS tt ON tt.id = m.projectteam1_id '
				. ' INNER JOIN #__joomleague_project AS pj ON pj.id = tt.project_id '
				. ' INNER JOIN #__joomleague_season AS s ON s.id = pj.season_id '
				. ' INNER JOIN #__joomleague_league AS l ON l.id = pj.league_id '
				. ' LEFT JOIN #__joomleague_position AS pos ON pos.id = mr.project_position_id '
				. ' WHERE p.id = ' . (int)$personid
				. ' GROUP BY (tt.project_id) '
				. ' ORDER BY s.ordering ASC, l.ordering ASC, pj.name ASC ';

		$db->setQuery( $query );
		$results = $db->loadObjectList();
		return $results;
	}

	function getContactID( $catid )
	{
		$person = $this->getPerson();
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		$query = '	SELECT	id
					FROM #__contact_details
					WHERE user_id = ' . $person->jl_user_id . '
					AND catid=' . $catid;

		$db->setQuery( $query );
		$contact_id = $db->loadResult();
		return $contact_id;
	}

	/**
	 * get all positions the player was assigned too in different projects
	 * @return unknown_type
	 */
	function getAllEvents()
	{
		$history = &$this->getPlayerHistory();
		$positionhistory = array();
		foreach($history as $h)
		{
			if (!in_array($h->position_id, $positionhistory))
				$positionhistory[] = $h->position_id;
		}
		if (!count($positionhistory)) {
			return array();
		}
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		$query = ' SELECT et.* '
				. ' FROM #__joomleague_eventtype AS et '
				. ' INNER JOIN #__joomleague_position_eventtype AS pet ON pet.eventtype_id = et.id '
				. ' WHERE published = 1 '
				. '   AND pet.position_id IN ('. implode(',', $positionhistory) .')'
				. ' ORDER BY et.ordering '
				;
				$db->setQuery( $query );
				$info = $db->loadObjectList();
		return $info;
	}

	/**
	 * get player events total, global or per project
	 * @param int $eventid
	 * @param int $projectid, all projects if null (default)
	 * @return array
	 */
	function getPlayerEvents($eventid, $projectid = null, $projectteamid = null)
	{
	    $db = Factory::getDbo();
	    $query = $db->getQuery(true);
		$query = ' SELECT	SUM(me.event_sum) as total '
				. ' FROM #__joomleague_match_event AS me '
				. ' INNER JOIN #__joomleague_team_player AS tp ON me.teamplayer_id = tp.id '
				. ' INNER JOIN #__joomleague_project_team AS pt ON pt.id = tp.projectteam_id '
				. ' WHERE me.event_type_id=' . $db->Quote((int) $eventid)
				. ' AND tp.person_id = ' . $db->Quote((int) $this->personid)
				;
				if ($projectteamid)
				{
					$query .= ' AND pt.id='.$db->Quote((int) $projectteamid);
				}
				if ($projectid)
				{
					$query .= ' AND pt.project_id=' . $db->Quote((int) $projectid);
				}
				$query .= ' GROUP BY tp.person_id';

				$db->setQuery($query);
				$result = $db->loadResult();
				return $result;
	}

	function getInOutStats( $project_id, $person_id )
	{
	    $db = Factory::getDbo();
	    $query = $db->getQuery(true);
		$query = ' SELECT	sum(IF(came_in=0,1,0)+max(came_in=1)) AS played, '
				. ' sum(mp.came_in) AS sub_in, '
				. ' sum(mp.out = 1) AS sub_out '
				. ' FROM #__joomleague_match_player AS mp '
				. ' INNER JOIN #__joomleague_match AS m ON mp.match_id = m.id '
				. ' INNER JOIN #__joomleague_team_player AS tp ON tp.id = mp.teamplayer_id '
				. ' INNER JOIN #__joomleague_project_team AS pt ON m.projectteam1_id = pt.id '
				. ' WHERE tp.person_id=' . $db->Quote((int)$person_id)
				. ' AND pt.project_id=' . $db->Quote((int)$project_id)
				. ' AND tp.published = 1 ';
		$db->setQuery( $query );
		$inoutstat = $db->loadObjectList();

		return $inoutstat;
	}

	function getPlayerChangedRecipients()
	{
	    $db = Factory::getDbo();
	    $query = $db->getQuery(true);
		$query = "	SELECT email
					FROM #__users
					WHERE usertype = 'Super Administrator'
					OR usertype = 'Administrator'";

		$db->setQuery( $query );

		return $db->loadColumn();
	}

	function sendMailTo($listOfRecipients, $subject, $message)
	{
		$app	= Factory::getApplication();
		$mailFrom = $app->get('mailfrom');
		$fromName = $app->get('fromname');
		Mail::sendMail( $mailFrom, $fromName, $listOfRecipients, $subject, $message );
	}

	function getAllowed($config_editOwnPlayer)
	{
		$user = Factory::getUser();
		$allowed=false;
		if(JoomleagueModelPerson::_isAdmin($user) || JoomleagueModelPerson::_isOwnPlayer($user, $config_editOwnPlayer)) {
			$allowed=true;
		}
		return $allowed;
	}

	function _isAdmin($user)
	{
		$allowed=false;
		if ($user->id > 0)
		{
			$project = $this->getProject();

			// Check if user is project admin or editor
			if ( $this->isUserProjectAdminOrEditor($user->id, $project))
			{
				$allowed = true;
			}

			// If not, then check if user has ACL rights
			if (!$allowed)
			{
				if (!$user->authorise('person.edit', 'com_joomleague')) {
					$allowed = false;
				} else {
					$allowed = true;
				}
			}
		}
		return $allowed;
	}

	function _isOwnPlayer($user,$config_editOwnPlayer)
	{
		if($user->id > 0)
		{
			$person=$this->getPerson();
			return $config_editOwnPlayer && $user->id == $person->user_id;
		}
		return false;
	}

	function isEditAllowed($config_editOwnPlayer,$config_editAllowed)
	{
		$allowed = false;
		$user = Factory::getUser();
		if($user->id > 0)
		{
			if(JoomleagueModelPerson::_isAdmin($user) || ($config_editAllowed && JoomleagueModelPerson::_isOwnPlayer($user,$config_editOwnPlayer))) {
				$allowed = true;
			}
			return $allowed;
		}
		return false;
	}

	function _getProjectTeamIds4UserId($userId)
	{
	    $db = Factory::getDbo();
	    $query = $db->getQuery(true);
		// team_player
		$query = '	SELECT tp.projectteam_id
					FROM #__joomleague_person AS pr
					INNER JOIN #__joomleague_team_player AS tp ON tp.person_id=pr.id
					LEFT JOIN #__contact_details AS cd ON cd.id=pr.contact_id
					WHERE cd.user_id='.$userId.'
					AND pr.published = 1
					AND tp.published = 1 ';
		$db->setQuery($query);
		$projectTeamIds = array();
		$projectTeamIds = $db->loadColumn();
		// team_staff
		$query='	SELECT ts.projectteam_id
				FROM #__joomleague_person pr
				INNER JOIN #__joomleague_team_staff AS ts ON ts.person_id=pr.id
				LEFT JOIN #__contact_details AS cd ON cd.id=pr.contact_id
				WHERE cd.user_id='.$userId.'
						AND pr.published = 1
						AND ts.published = 1';
		$db->setQuery($query);
		if(!empty($projectTeamIds)) {
			$projectTeamIds = array_merge($projectTeamIds, $db->loadColumn());
		} else {
			$projectTeamIds = $db->loadColumn();
		}
		return $projectTeamIds;
	}

	function isContactDataVisible($config_showContactDataOnlyTeamMembers)
	{
		$user = Factory::getUser();
		$result=true;
		// project admin and editor,see contact always
		if($config_showContactDataOnlyTeamMembers && !$this->isUserProjectAdminOrEditor($user->id,$this->getProject()))
		{
			$result=false;
			if($user->id > 0)
			{
				// get project_team id to user-id from team-player or team-staff
				$projectTeamIds= JoomleagueModelPerson::_getProjectTeamIds4UserId($user->id);
				$teamplayer=JoomleagueModelPlayer::getTeamPlayer();
				if(isset($teamplayer->projectteam_id)) {
					$result=in_array($teamplayer->projectteam_id, $projectTeamIds);
				}
			}
		}
		return $result;
	}

}
?>
