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
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;

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
		    $query
        		    ->select($db->quoteName('p.id'))
        		    ->select($db->quoteName('p.contact_id'))
        		    ->select($db->quoteName('p.firstname'))
        		    ->select($db->quoteName('p.lastname'))
        		    ->select($db->quoteName('p.nickname'))
        		    ->select($db->quoteName('p.alias'))
        		    ->select($db->quoteName('p.country'))
        		    ->select($db->quoteName('p.knvbnr'))
        		    ->select($db->quoteName('p.birthday'))
        		    ->select($db->quoteName('p.deathday'))
        		    ->select($db->quoteName('p.height'))
        		    ->select($db->quoteName('p.weight'))
        		    ->select($db->quoteName('p.picture'))
        		    ->select($db->quoteName('p.show_pic'))
        		    ->select($db->quoteName('p.show_persdata'))
           		    ->select($db->quoteName('p.show_teamdata'))
        		    ->select($db->quoteName('p.show_on_frontend'))
        		    ->select($db->quoteName('p.info'))
        		    ->select($db->quoteName('p.notes'))
        		    ->select($db->quoteName('p.phone'))
        		    ->select($db->quoteName('p.mobile'))
        		    ->select($db->quoteName('p.email'))
        		    ->select($db->quoteName('p.website'))
        		    ->select($db->quoteName('p.address'))
        		    ->select($db->quoteName('p.zipcode'))
        		    ->select($db->quoteName('p.location'))
        		    ->select($db->quoteName('p.state'))
        		    ->select($db->quoteName('p.address_country'))
        		    ->select($db->quoteName('p.extended'))
        		    ->select($db->quoteName('p.position_id'))
        		    ->select($db->quoteName('p.published'))
        		    ->select($db->quoteName('p.ordering'))
        		    ->select($db->quoteName('p.checked_out'))
        		    ->select($db->quoteName('p.checked_out_time'))
        		    ->select($db->quoteName('p.modified'))
        		    ->select($db->quoteName('p.modified_by'))
           		    ->select($this->constructSlug($db, 'slug', 'p.alias', 'p.id'))		    
        		    ->from($db->quoteName('#__joomleague_person', 'p'))
        		    ->where($db->quoteName('p.id') . ' = ' . $db->quote($this->personid));
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
		    $query
		          ->select($db->quoteName('tp.id'))
		          ->select($db->quoteName('tp.project_id'))
		          ->select($db->quoteName('tp.person_id'))
		          ->select($db->quoteName('tp.project_position_id'))
		          ->select($db->quoteName('tp.notes'))
		          ->select($db->quoteName('tp.picture'))
		          ->select($db->quoteName('tp.published'))
		          ->select($db->quoteName('tp.extended'))
		          ->select($db->quoteName('tp.ordering'))
		          ->select($db->quoteName('tp.checked_out'))
		          ->select($db->quoteName('tp.checked_out_time'))
		          ->select($db->quoteName('tp.modified'))
		          ->select($db->quoteName('tp.modified_by'))
		          ->select($db->quoteName('tp.asset_id'))
		          ->select($db->quoteName('tp.alias'))
		          ->select($db->quoteName('pos.name' , 'position_name'))
		          ->from($db->quoteName('#__joomleague_project_referee' , 'tp'))
		          ->join('INNER', $db->quoteName('#__joomleague_project_referee', 'tp'))
		          ->where($db->quoteName('tp.project_id') . ' = ' . $db->quote($this->projectid))
		          ->where($db->quoteName('tp.person_id') . ' = ' . $db->quote($this->personid));
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
		$query
		      ->select($db->quoteName('pet.id'))
		      ->select($db->quoteName('pet.position_id'))
		      ->select($db->quoteName('pet.eventtype_id'))
		      ->select($db->quoteName('pet.ordering'))
		      ->select($db->quoteName('pet.checked_out'))
		      ->select($db->quoteName('pet.checked_out_time'))
		      ->select($db->quoteName('pet.modified'))
		      ->select($db->quoteName('pet.modified_by'))
		      ->select($db->quoteName('et.name'))
		      ->select($db->quoteName('et.icon'))
		      ->from($db->quoteName('#__joomleague_position_eventtype' , 'pet'))
		      ->innerJoin($db->quoteName('#__joomleague_eventtype AS et ON et.id = pet.eventtype_id'))
		      ->innerJoin($db->quoteName('#__joomleague_match_event AS me ON et.id = me.event_type_id'))
		      ->where($db->quoteName('me.project_id=' . $this->projectid));
		if ( $positionId > 0 )
		{
		    $query->where($db->quoteName('pet.position_id = ' . (int)$positionId));
		}
		$query->order('pet.ordering');

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
		$query
		      ->select($db->quoteName('p.id' , 'person_id'))
		      ->select($db->quoteName('tt.project_id')
		      ->select($db->quoteName('p.firstname' , 'fname'))
		      ->select($db->quoteName('p.lastname' , 'lname'))
		      ->select($db->quoteName('pj.name' , 'pname'))
		      ->select($db->quoteName('s.name' , 'sname'))
		      ->select($db->quoteName('pos.name' , 'position'))
		      ->select('COUNT(' . $db->quoteName('mr.id') . ') AS matchesCount'))
		      ->from($db->quoteName('#__joomleague_match_referee', 'mr'))   
		      ->join('INNER', $db->quoteName('#__joomleague_match', 'm') .
		          ' ON ' . $db->quoteName('m.id') . ' = ' . $db->quoteName('mr.match_id'))
		      ->join('INNER', $db->quoteName('#__joomleague_person' , 'p') . 
		          ' ON ' . $db->quoteName('p.id') . ' = ' . $db->quoteName('mr.project_referee_id'))
		      ->join('INNER', $db->quoteName('#__joomleague_project_team' , 'tt') .
		          ' ON ' . $db->quoteName('tt.id') . ' = ' . $db->quoteName('m.projectteam1_id'))
		      ->join('INNER', $db->quoteName('#__joomleague_project' , 'pj') .
		          ' ON ' .$db->quoteName('pj.id') . ' = ' . $db->quoteName('tt.project_id'))
		      ->join('INNER', $db->quoteName('#__joomleague_project' , 'pj') .
		          ' ON ' . $db->quoteName('pj.id') . ' = ' . $db->quoteName('tt.project_id'))
		      ->join('INNER', $db->quoteName('#__joomleague_league' , 'l') .
		          ' ON ' . $db->quoteName('l.id') . ' = ' . $db->quoteName('pj.league_id'))
		      ->join('LEFT', $db->quoteName('#__joomleague_position' , 'pos') .
		          ' ON ' . $db->quoteName('pos.id') . ' = ' . $db->quoteName('mr.project_position_id')
		          ->where($db->quoteName('p.id'). ' = ' . (int)$personid))
		       ->group($db->quoteName('tt.project_id'))
		       ->order($db->quoteName('s.ordering ASC, l.ordering ASC, pj.name ASC'));
		       
		$db->setQuery( $query );
		$results = $db->loadObjectList();
		return $results;
	}

	function getContactID( $catid )
	{
		$person = $this->getPerson();
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		$query
		      ->select('id')
		      ->from('#__contact_details')
		      ->where('user_id = ' . $person->jl_user_id)
		      ->where('catid=' . $catid);
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
		$query
		      ->select('et.id')
		      ->select('et.name')
		      ->select('et.alias')
		      ->select('et.icon')
		      ->select('et.parent')
		      ->select('et.splitt')
		      ->select('et.direction')
		      ->select('et.double')
		      ->select('et.suspension')
		      ->select('et.sports_type_id')
		      ->select('et.published')
		      ->select('et.ordering')
		      ->select('et.checked_out')
		      ->select('et.checked_out_time')
		      ->select('et.modified')
		      ->select('et.modified_by')		      
		      ->from('#__joomleague_eventtype AS et')
		      ->innerJoin('#__joomleague_position_eventtype AS pet ON pet.eventtype_id = et.id')
		      ->where('published = 1')
		      ->where('pet.position_id IN ('. implode(',', $positionhistory) .')')
		      ->order('et.ordering');
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
	    $query
	           ->select('SUM(me.event_sum) as total')
	           ->from('#__joomleague_match_event AS me')
	           ->innerJoin('#__joomleague_team_player AS tp ON me.teamplayer_id = tp.id')
	           ->innerJoin('#__joomleague_project_team AS pt ON pt.id = tp.projectteam_id')
	           ->where('me.event_type_id=' . $db->Quote((int) $eventid))
	           ->where('tp.person_id = ' . $db->Quote((int) $this->personid));
				if ($projectteamid)
				{
				    $query->where('pt.id='.$db->quote((int) $projectteamid));
				}
				if ($projectid)
				{
				    $query->where('pt.project_id=' . $db->quote((int) $projectid));
				}
				$query->group('tp.person_id');

				$db->setQuery($query);
				$result = $db->loadResult();
				return $result;
	}

	function getInOutStats( $project_id, $person_id )
	{
	    $db = Factory::getDbo();
	    $query = $db->getQuery(true);
	    $query
	           ->select('sum(IF(came_in=0,1,0)+max(came_in=1)) AS played')
	           ->select('sum(mp.came_in) AS sub_in')
	           ->select('sum(mp.out = 1) AS sub_out')
	           ->innerJoin('#__joomleague_match AS m ON mp.match_id = m.id')
	           ->innerJoin('#__joomleague_team_player AS tp ON tp.id = mp.teamplayer_id')
	           ->innerJoin('#__joomleague_project_team AS pt ON m.projectteam1_id = pt.id')
	           ->where('tp.person_id=' . $db->quote((int)$person_id))
	           ->where('pt.project_id=' . $db->quote((int)$project_id))
	           ->where('tp.published = 1');
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
	    $app = Factory::getApplication();
	    $db = Factory::getDbo();
	    $query = $db->getQuery(true);
		// team_player
		$query
        	  ->select($db->quoteName('tp.projectteam_id'))
		      ->from($db->quoteName('#__joomleague_person' , 'pr'))
		      ->innerJoin($db->quoteName('#__joomleague_team_player' , 'tp') . ' ON ' .$db->quoteName('tp.person_id') . ' = ' . $db->quoteName('pr.id'))
		      ->leftJoin($db->quoteName('#__contact_details' , 'cd') . ' ON ' . $db->quoteName('cd.id') . ' = ' . $db->quote('pr.contact_id'))
		      ->where($db->quoteName('cd.user_id') . ' = ' . $db->quote($userId))
		      ->andwhere($db->quoteName('pr.published') . '= 1')
		      ->andwhere($db->quoteName('tp.published') . '= 1');
		      try {
		    $db->setQuery($query);
		    $projectTeamIds = array();
		    $projectTeamIds = $db->loadColumn();
		} catch (RuntimeException $e) {
		    $app->enqueueMessage(Text::_($e->getMessage()), 'error');
	  
		}
		
		// team_staff
		$query = $db->getQuery(true);
		$query
		      ->select($db->quoteName('ts.projectteam_id'))
		      ->from($db->quoteName('#__joomleague_person' , 'pr'))
		      ->innerJoin($db->quoteName('#__joomleague_team_staff' , 'ts') . ' ON ' . $db->quoteName('ts.person_id') . ' = ' .$db->quoteName('pr.id'))
		      ->leftJoin($db->quoteName('#__contact_details' , 'cd') . ' ON ' . $db->quoteName('cd.id') . ' = ' . $db->quote('pr.contact_id'))
		      ->where($db->quoteName('cd.user_id') . ' = ' .$db->quote($userId))
		      ->andwhere($db->quoteName('pr.published') . '= 1') 
		      ->andwhere($db->quoteName('ts.published') . '= 1');
		 try {
		$db->setQuery($query);
		if(!empty($projectTeamIds)) {
			$projectTeamIds = array_merge($projectTeamIds, $db->loadColumn());
		} else {
			$projectTeamIds = $db->loadColumn();
		}
        }
        catch (RuntimeException $e) {
            $app->enqueueMessage(Text::_($e->getMessage()), 'error');
            
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
				$teamplayer=JoomleagueModelPlayer::getTeamPlayers();
				if(isset($teamplayer->projectteam_id)) {
					$result=in_array($teamplayer->projectteam_id, $projectTeamIds);
				}
			}
		}
		return $result;
	}

}
?>
