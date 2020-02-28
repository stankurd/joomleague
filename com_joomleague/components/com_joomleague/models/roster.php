<?php
/**
 * @copyright	Copyright (C) 2006-2015 joomleague.at. All rights reserved.
 * @license		GNU/GPL,see LICENSE.php
 * Joomla! is free software. This version may have been modified pursuant
 * to the GNU General Public License,and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */

// Check to ensure this file is included in Joomla!
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;

defined('_JEXEC') or die;

require_once JLG_PATH_SITE.'/models/project.php';

/**
 * Model-Roster
 */
class JoomleagueModelRoster extends JoomleagueModelProject
{
	var $projectid=0;
	var $projectteamid=0;
	var $projectteam=null;
	var $team=null;
	

	/**
	 * caching for team in out stats
	 * @var array
	 */
	var $_teaminout=null;

	/**
	 * caching players
	 * @var array
	 */
	var $_players=null;

	public function __construct()
	{
		parent::__construct();
		
		$app = Factory::getApplication();
		$input = $app->input;

		$this->projectid=$input->getInt('p',0);
		$this->teamid=$input->getInt('tid',0);
		$this->projectteamid=$input->getInt('ttid',0);
		$this->getProjectTeam();
	}

	/**
	 * returns project team info
	 * @return object
	 */
	function getProjectTeam()
	{
		if (is_null($this->projectteam))
		{
			if ($this->projectteamid)
			{
				$this->projectteam = $this->getTeaminfo($this->projectteamid);
			}
			else
			{
				if (!$this->teamid)
				{
					$this->setError(Text::_('Missing team id'));
					return false;
				}
				if (!$this->projectid)
				{
					$this->setError(Text::_('Missing project id'));
					return false;
				}
				$db = Factory::getDbo();
				$query = $db->getQuery(true);
				$query
				    ->select($db->quoteName('pt.id'))
				    ->from($db->quoteName('#__joomleague_project_team' , 'pt'))
				    ->where($db->quoteName('pt.team_id') . ' = ' .$db->quote($this->teamid))
				    ->where($db->quoteName('pt.project_id') . ' = ' .$db->quote($this->projectid));    
				$db->setQuery($query);
				$this->projectteamid = $db->loadObject()->id;
				$this->projectteam = $this->getTeaminfo($this->projectteamid);
			}
			if ($this->projectteam)
			{
				$this->projectid=$this->projectteam->project_id; // if only ttid was set
				$this->teamid=$this->projectteam->team_id; // if only ttid was set
			}
		}
		return $this->projectteam;
	}

	function getTeam()
	{
		if (is_null($this->team))
		{
			if (!$this->teamid)
			{
				$this->setError(Text::_('Missing team id'));
				return false;
			}
			if (!$this->projectid)
			{
				$this->setError(Text::_('Missing project id'));
				return false;
			}
			$db = Factory::getDbo();
			$query = $db->getQuery(true);
			$query
			     ->select($db->quoteName('t.id'))
			     ->select($db->quoteName('t.club_id'))
			     ->select($db->quoteName('t.name'))
			     ->select($db->quoteName('t.short_name'))
			     ->select($db->quoteName('t.middle_name'))
			     ->select($db->quoteName('t.alias'))
			     ->select($db->quoteName('t.website'))
			     ->select($db->quoteName('t.info'))
			     ->select($db->quoteName('t.notes'))
			     ->select($db->quoteName('t.picture'))
			     ->select($db->quoteName('t.extended'))
			     ->select($db->quoteName('t.ordering'))
			     ->select($this->constructSlug($db, 'slug', 't.alias', 't.id'))
			     ->from($db->quoteName('#__joomleague_team' , 't'))
			     ->where($db->quoteName('t.id')  . ' = ' .$db->quote($this->teamid));
			$db->setQuery($query);
			$this->team=$db->loadObject();
		}
		return $this->team;
	}

	/**
	 * return team players by positions
	 * @return array
	 */
	function getTeamPlayers()
	{
		$projectteam = $this->getprojectteam();
		if (empty($this->_players))
		{
		    $db = Factory::getDbo();
		    $query = $db->getQuery(true);
		    $query
		   	->select($db->quoteName('pr.firstname'))
		   	->select($db->quoteName('pr.nickname'))
		   	->select($db->quoteName('pr.lastname'))
		   	->select($db->quoteName('pr.country'))
		   	->select($db->quoteName('pr.birthday'))
		   	->select($db->quoteName('pr.deathday'))
		   	->select($db->quoteName('tp.id' , 'playerid'))
		    ->select($db->quoteName('pr.id' , 'pid'))
		    ->select($db->quoteName('pr.picture' , 'ppic'))
		    ->select($db->quoteName('tp.jerseynumber' , 'position_number'))
		    ->select($db->quoteName('tp.notes' , 'description'))
		    ->select($db->quoteName('tp.injury' , 'injury'))
		    ->select($db->quoteName('tp.suspension' , 'suspension'))
		    ->select($db->quoteName('pt.team_id'))
		    ->select($db->quoteName('tp.away' , 'away'))
		    ->select($db->quoteName('tp.picture'))
		    ->select($db->quoteName('pos.name' , 'position'))
		    ->select($db->quoteName('ppos.position_id'))
		    ->select($db->quoteName('ppos.id' , 'pposid'))								
			->select($this->constructSlug($db, 'slug', 'pr.alias', 'pr.id'))
			->from($db->quoteName('#__joomleague_team_player' , 'tp'))
			->join('INNER', $db->quoteName('#__joomleague_project_team' , 'pt')
			    . ' ON ' . $db->quoteName('pt.id') . ' = ' . $db->quoteName('tp.projectteam_id'))
			->join('INNER', $db->quoteName('#__joomleague_person' , 'pr') . ' ON ' . $db->quoteName('tp.person_id') . ' = ' . $db->quoteName('pr.id'))
			->join('INNER', $db->quoteName('#__joomleague_project_position' , 'ppos') . ' ON ' . $db->quoteName('ppos.id') . ' = ' . $db->quoteName('tp.project_position_id'))
			->join('INNER', $db->quoteName('#__joomleague_position' , 'pos') . ' ON ' . $db->quoteName('pos.id') . ' = ' . $db->quoteName('ppos.position_id'))
		    ->where($db->quoteName('tp.projectteam_id') . ' = ' . $db->quote($this->projectteamid))
			->where($db->quoteName('pr.published') . ' = ' . ('1'))
			->where($db->quoteName('tp.published'). ' = ' . ('1'))
			->order("pos.ordering, ppos.position_id, tp.jerseynumber, pr.lastname, pr.firstname");
			 
			$db->setQuery($query);
			$this->_players=$db->loadObjectList();
		}
		$bypos=array();
		foreach ($this->_players as $player)
		{
			if (isset($bypos[$player->position_id]))
			{
				$bypos[$player->position_id][]=$player;
			}
			else
			{
				$bypos[$player->position_id]= array($player);
			}
		}
		return $bypos;
	}

	function getStaffList()
	{
		$projectteam = $this->getprojectteam();
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		$query
    		->select($db->quoteName('pr.firstname'))
    		->select($db->quoteName('pr.nickname'))
    		->select($db->quoteName('pr.lastname'))
    		->select($db->quoteName('pr.country'))
    		->select($db->quoteName('pr.birthday'))
    		->select($db->quoteName('pr.deathday'))
    		->select($db->quoteName('ts.id' , 'ptid'))
    		->select($db->quoteName('pr.id' , 'pid'))
    		->select($db->quoteName('ppos.position_id'))
    		->select($db->quoteName('ppos.id' , 'pposid'))
    		->select($db->quoteName('pr.id' , 'pid'))
    		->select($db->quoteName('pr.picture' , 'ppic'))
    		->select($db->quoteName('pos.name' , 'position'))
    		->select($db->quoteName('ts.picture'))
    		->select($db->quoteName('ts.notes' , 'description'))
    		->select($db->quoteName('ts.injury' , 'injury'))
    		->select($db->quoteName('ts.suspension' , 'suspension'))
    		->select($db->quoteName('ts.away' , 'away'))
    		->select($db->quoteName('pos.parent_id'))
    		->select($db->quoteName('posparent.name' , 'parentname'))
    		->select($this->constructSlug($db, 'slug', 'pr.alias', 'pr.id'))
    		->from($db->quoteName('#__joomleague_team_staff' , 'ts'))
    		->join('INNER', $db->quoteName('#__joomleague_person' , 'pr') . ' ON ' . $db->quoteName('ts.person_id') . ' = ' . $db->quoteName('pr.id'))
    		->join('INNER', $db->quoteName('#__joomleague_project_position' , 'ppos')
    		    . ' ON ' . $db->quoteName('ppos.id') . ' = ' . $db->quoteName('ts.project_position_id'))
    	    ->join('INNER', $db->quoteName('#__joomleague_position' , 'pos') . ' ON ' . $db->quoteName('pos.id') . ' = ' . $db->quoteName('ppos.position_id'))		    		
    	    ->join('LEFT', $db->quoteName('#__joomleague_position' , 'posparent') . ' ON ' . $db->quoteName('pos.parent_id') . ' = ' . $db->quoteName('posparent.id'))
    	    ->where($db->quoteName('ts.projectteam_id') . ' = ' . $db->quote($this->projectteamid))
    	    ->where($db->quoteName('pr.published') . ' = ' . ('1'))
    	    ->where($db->quoteName('ts.published'). ' = ' . ('1'))
    	    ->order($db->quoteName('pos.parent_id') . ' , ' . $db->quoteName('pos.ordering'));
    	    
		$db->setQuery($query);
		$stafflist=$db->loadObjectList();
		return $stafflist;
	}

	function getPositionEventTypes($positionId=0)
	{
		$result=array();
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		$query
		->select($db->quoteName('pet.eventtype_id'))
		->select($db->quoteName('pet.position_id'))
		->select($db->quoteName('pet.ordering'))		
		->select($db->quoteName('ppos.id' , 'pposid'))
		->select($db->quoteName('ppos.position_id'))
		->select($db->quoteName('et.name' , 'name'))
		->select($db->quoteName('et.icon' , 'icon'))
		->select($db->quoteName('et.ordering'))
		->from($db->quoteName('#__joomleague_position_eventtype' , 'pet'))
		->innerJoin($db->quoteName('#__joomleague_eventtype' , 'et') . ' ON ' . $db->quoteName('et.id') . ' = ' . $db->quoteName('eventtype_id'))
		->innerJoin($db->quoteName('#__joomleague_project_position' , 'ppos') . ' ON ' . $db->quoteName('ppos.position_id') . ' = ' . $db->quoteName('pet.position_id'))
		->where($db->quoteName('ppos.project_id') . ' = ' . $db->quote($this->projectid))
		->where($db->quoteName('et.published') . ' = ' . ('1'));
		if ($positionId > 0)
		{
			$query->where($db->quoteName('pet.position_id') . ' = ' . (int)$positionId);
			
		}
		$query->order($db->quoteName('pet.ordering') . ' , ' . $db->quoteName('et.ordering'));
		$db->setQuery($query);
		$result=$db->loadObjectList();
		if ($result)
		{
			if ($positionId)
			{
				return $result;
			}
			else
			{
				$posEvents=array();
				foreach ($result as $r)
				{
					$posEvents[$r->position_id][]=$r;
				}
				return ($posEvents);
			}
		}
		return array();
	}

	function getPlayerEventStats()
	{
		$playerstats=array();
		$projectPositionEventTypes = $this->getProjectPositionEventTypes();
		foreach ($projectPositionEventTypes as $projectPositionEventType)
		{
			$projectPositionId = $projectPositionEventType->project_position_id;
			$eventTypeId = $projectPositionEventType->event_type_id;
			if (!array_key_exists($projectPositionId, $playerstats))
			{
				$playerstats[$projectPositionId] = array();
			}
			$playerstats[$projectPositionId][$eventTypeId] = $this->getPositionEventStat($this->projectteamid, $projectPositionId, $eventTypeId);
		}
		return $playerstats;
	}

	function getProjectPositionEventTypes()
	{
	    $db = Factory::getDbo();
	    $query = $db->getQuery(true);
	    $query
	    ->select($db->quoteName('ppos.id' , 'project_position_id'))
	    ->select($db->quoteName('pet.eventtype_id' , 'event_type_id'))
	    ->from($db->quoteName('#__joomleague_project_position' , 'ppos'))
	    ->innerJoin($db->quoteName('#__joomleague_position' , 'pos') . ' ON ' . $db->quoteName('pos.id') . '=' . $db->quoteName('ppos.position_id'))
	    ->innerJoin($db->quoteName('#__joomleague_position_eventtype' , 'pet') . ' ON ' .$db->quoteName('pet.position_id') . '=' .$db->quoteName('pos.id'))
	    ->where($db->quoteName('ppos.project_id') . '=' .$db->Quote($this->projectid));
		$db->setQuery($query);
		$result=$db->loadObjectList();
		return $result;
	}

	function getPositionEventStat($projectTeamId, $projectPositionId, $eventTypeId)
	{
	    $db = Factory::getDbo();
	    $query = $db->getQuery(true);
		
		$query	= ' FROM       #__joomleague_team_player        AS tp'
				. ' INNER JOIN #__joomleague_project_position   AS ppos ON ppos.id=tp.project_position_id'
				. ' INNER JOIN #__joomleague_position           AS pos ON pos.id=ppos.position_id'
				. ' INNER JOIN #__joomleague_person             AS per ON tp.person_id=per.id'
				. ' INNER JOIN #__joomleague_position_eventtype AS pet ON pet.position_id=pos.id'
				. ' INNER JOIN #__joomleague_eventtype          AS et ON et.id=pet.eventtype_id'
				. ' LEFT  JOIN #__joomleague_match_event        AS me ON me.event_type_id=et.id'
				. '                                             AND me.projectteam_id=tp.projectteam_id AND me.teamplayer_id=tp.id'
				. ' WHERE tp.projectteam_id = '.$db->Quote($projectTeamId)
				. '   AND tp.project_position_id = '.$db->Quote($projectPositionId)
				. '   AND et.id = '.$db->Quote($eventTypeId)
				;
	    $db->setQuery('SELECT tp.person_id, COALESCE(sum(me.event_sum),0) AS value'.$query.' GROUP BY tp.person_id');
		$result=$db->loadObjectList('person_id');

		$db->setQuery('SELECT COALESCE(sum(me.event_sum),0) AS value'.$query);
		$result["totals"] = $db->loadObject();
		$result["totals"]->person_id = 0;
		return $result;
	}

	function getInOutStats($player_id)
	{
		$teaminout=$this->_getTeamInOutStats();
		if (isset($teaminout[$player_id])) {
			return $teaminout[$player_id];
		}
		else {
			return null;
		}
	}

	function _getTeamInOutStats()
	{
	    $db = Factory::getDbo();
	    $query = $db->getQuery(true);
		$projectteam=$this->getprojectteam();
		if (empty($this->_teaminout))
		{
			$projectteam_id = $db->quote($this->projectteamid);
			$query
			     ->select('tp1.id AS tp_id1')
			     ->select('tp1.person_id AS person_id1')
			     ->select('tp2.id AS tp_id2')
			     ->select('tp2.person_id AS person_id2')
			     ->select('m.id AS mid')
			     ->select('mp.came_in')
			     ->select('mp.out')
			     ->select('mp.in_for')
			     ->from('#__joomleague_match AS m')
			     ->innerJoin('#__joomleague_round r ON m.round_id=r.id')
			     ->innerJoin('#__joomleague_project AS p ON p.id=r.project_id')
			     ->innerJoin('#__joomleague_match_player AS mp ON mp.match_id=m.id')
			     ->innerJoin('#__joomleague_team_player AS tp1 ON tp1.id=mp.teamplayer_id')
			     ->leftJoin('#__joomleague_team_player AS tp2 ON tp2.id = mp.in_for')
			     ->where('tp1.projectteam_id = '.$projectteam_id)
			     ->where('m.published = 1')
			     ->where('p.published = 1');
			$db->setQuery($query);
			$rows = $db->loadObjectList();
			$played = array();
			$this->_teaminout = array();
			foreach ($rows AS $row)
			{
				// Handle the first player tp1 (is always there, either as the one that is in the starting line-up,
				// is coming in, or is going out (while no-one comes in))
				if (!array_key_exists($row->person_id1, $this->_teaminout))
				{
					$this->_teaminout[$row->person_id1] = new stdclass;
					$this->_teaminout[$row->person_id1]->played = 0;
					$this->_teaminout[$row->person_id1]->started = 0;
					$this->_teaminout[$row->person_id1]->sub_in = 0;
					$this->_teaminout[$row->person_id1]->sub_out = 0;
				}
				if (!array_key_exists($row->person_id1, $played))
				{
					$played[$row->person_id1] = array();
				}
				if (!array_key_exists($row->mid, $played[$row->person_id1]))
				{
					$played[$row->person_id1][$row->mid] = 0;
				}
				$played[$row->person_id1][$row->mid] |= ($row->came_in == 0) || ($row->came_in == 1);
				$this->_teaminout[$row->person_id1]->started += ($row->came_in == 0);
				$this->_teaminout[$row->person_id1]->sub_in  += ($row->came_in == 1);
				$this->_teaminout[$row->person_id1]->sub_out += ($row->out == 1);

				// Handle the second player tp2 (only applicable when one goes out AND another comes in; tp2 is the player that goes out)
				if (isset($row->person_id2))
				{
					// As the rows can appear in any order it can happen that the player goes out
					// before the row where the player came in is processed. Therefore we need to
					// check if the entry already exists in the array, and create it when necessary.
					if (!array_key_exists($row->person_id2, $this->_teaminout))
					{
						$this->_teaminout[$row->person_id2] = new stdclass;
						$this->_teaminout[$row->person_id2]->played = 0;
						$this->_teaminout[$row->person_id2]->started = 0;
						$this->_teaminout[$row->person_id2]->sub_in = 0;
						$this->_teaminout[$row->person_id2]->sub_out = 0;
					}
					if (!array_key_exists($row->person_id2, $played))
					{
						$played[$row->person_id2] = array();
					}
					$played[$row->person_id2][$row->mid] = 1;
					$this->_teaminout[$row->person_id2]->sub_out++;
				}
			}
			foreach ($played AS $k => $v)
			{
				foreach ($v as $m)
				{
					$this->_teaminout[$k]->played += $m;
				}
			}
		}

		return $this->_teaminout;
	}

	function getTimePlayed($player_id,$game_regular_time,$match_id = NULL)
	{
	    $db = Factory::getDbo();
		$query = $db->getQuery(true);
		$result = 0;
		// starting line up without subs in/out
		$query
		      ->select('count(match_id) as totalmatch')
		      ->from($db->quoteName('#__joomleague_match_player'))
		      ->where($db->quoteName('teamplayer_id') . ' = ' .$db->quote($player_id))
		      ->where($db->quoteName('came_in') . ' = ' . '0');
		if ( $match_id )
		{
		    $query->where($db->quoteName('match_id') . '=' .$db->quote($match_id));
		}
		$db->setQuery($query);
		$totalresult = $db->loadObject();

		if ( $totalresult )
		{
			$result += $totalresult->totalmatch * $game_regular_time;
		}

		// subs in
		$query = $db->getQuery(true);
		$query
		      ->select('count(match_id) as totalmatch')
		      ->select('SUM(in_out_time) as totalin')
		      ->from($db->quoteName('#__joomleague_match_player'))
		      ->where($db->quoteName('teamplayer_id') . '=' .$db->quote($player_id))		      
		      ->where($db->quoteName('came_in') . ' = ' . '1')
		      ->where($db->quoteName('in_for') . 'IS NOT NULL');
		if ( $match_id )
		{
		    $query->where($db->quoteName('match_id') . '=' .$db->quote($match_id));
		}
		$db->setQuery($query);
		$cameinresult = $db->loadObject();

		if ( $cameinresult )
		{
			$result += ( $cameinresult->totalmatch * $game_regular_time ) - ( $cameinresult->totalin );
		}

		// subs out
		$query = $db->getQuery(true);
		$query
		      ->select('count(match_id) as totalmatch')
		      ->select('SUM(in_out_time) as totalout')
		      ->from($db->quoteName('#__joomleague_match_player'))
		      ->where($db->quoteName('in_for') . '=' .$db->quote($player_id))
		      ->where($db->quoteName('came_in') . '=' . '1');
		
		if ( $match_id )
		{
		    $query->where($db->quoteName('match_id') . '=' .$db->quote($match_id));
		}
		$db->setQuery($query);
		$cameoutresult = $db->loadObject();

		if ( $cameoutresult )
		{
			$result += ( $cameoutresult->totalout ) - ( $cameoutresult->totalmatch * $game_regular_time );
		}

		// get all events which leads to a suspension (e.g. red card)
		$query = $db->getQuery(true);
		$query
		      ->select('id')
		      ->from($db->quoteName('#__joomleague_eventtype'))
		      ->where($db->quoteName('suspension') . ' = ' . ('1'));
		$db->setQuery($query);
		$suspension_events = $db->loadColumn();
		$suspension_events = implode(',',$suspension_events);

		// find matches where the player was suspended because of e.g. a red card
		if (!empty($suspension_events))
		{
		    $query = $db->getQuery(true);
		    $query
		          ->select('*')
		          ->from('#__joomleague_match_event')
		          ->where('teamplayer_id' . ' = ' .$player_id)
		          ->where('event_type_id in ('.$suspension_events.')');
			if ( $match_id )
			{
			$query->where('match_id = '.$match_id);
			}
			$db->setQuery($query);
			$cardsresult = $db->loadObjectList();
			foreach ( $cardsresult as $row )
			{
			    $result -= ( $game_regular_time - $row->event_time );
			}
		}

		return $result;
	}

	/**
	 * return the injury,suspension,away data from a player
	 *
	 * @param int $round_id
	 * @param int $player_id
	 *
	 * @access public
	 *
	 * @return object
	 */
	function getTeamPlayer($round_id,$player_id)
	{
	    $db = Factory::getDbo();
	    $query = $db->getQuery(true);
	    $query
	    ->select($db->quoteName('tp.id'))
	    ->select($db->quoteName('tp.projectteam_id'))
	    ->select($db->quoteName('tp.person_id'))
	    ->select($db->quoteName('tp.project_position_id'))
	    ->select($db->quoteName('tp.active'))
	    ->select($db->quoteName('tp.jerseynumber'))
	    ->select($db->quoteName('tp.notes'))
	    ->select($db->quoteName('tp.picture'))
	    ->select($db->quoteName('tp.extended'))
	    ->select($db->quoteName('tp.injury'))
	    ->select($db->quoteName('tp.injury_date'))
	    ->select($db->quoteName('tp.injury_end'))
	    ->select($db->quoteName('tp.injury_detail'))
	    ->select($db->quoteName('tp.injury_date_start'))
	    ->select($db->quoteName('tp.injury_date_end'))
	    ->select($db->quoteName('tp.suspension'))
	    ->select($db->quoteName('tp.suspension_date'))
	    ->select($db->quoteName('tp.suspension_end'))
	    ->select($db->quoteName('tp.suspension_detail'))
	    ->select($db->quoteName('tp.susp_date_start'))
	    ->select($db->quoteName('tp.susp_date_end'))
	    ->select($db->quoteName('tp.away'))
	    ->select($db->quoteName('tp.away_date'))
	    ->select($db->quoteName('tp.away_end'))
	    ->select($db->quoteName('tp.away_detail'))
	    ->select($db->quoteName('tp.away_date_start'))
	    ->select($db->quoteName('tp.away_date_end'))
	    ->select($db->quoteName('tp.published'))
	    ->select($db->quoteName('tp.ordering'))
	    ->select($db->quoteName('tp.position_id'))
	    ->select($db->quoteName('tp.alias'))
	    ->select($db->quoteName('pt.picture'))
	    ->select($db->quoteName('ppos.id' , 'pposid'))
	    ->select($db->quoteName('pos.id' , 'position_id'))
	    ->select($db->quoteName('rinjuryfrom.round_date_first' , 'injury_date'))
	    ->select($db->quoteName('rinjuryfrom.round_date_first' , 'injury_date'))
	    ->select($db->quoteName('rinjuryfrom.name' , 'rinjury_from'))
	    ->select($db->quoteName('rinjuryto.name' , 'rinjury_to'))
	    ->select($db->quoteName('rsuspfrom.round_date_first' , 'suspension_date'))
	    ->select($db->quoteName('rsuspto.round_date_last' , 'suspension_end'))
	    ->select($db->quoteName('rsuspfrom.name' , 'rsusp_from'))
	    ->select($db->quoteName('rsuspto.name' , 'rsusp_to'))
	    ->select($db->quoteName('rawayfrom.round_date_first' , 'away_date'))
	    ->select($db->quoteName('rawayto.round_date_last' , 'away_end'))
	    ->select($db->quoteName('rawayfrom.name' , 'raway_from'))
	    ->select($db->quoteName('rawayfrom.name' , 'raway_from'))
	    ->from($db->quoteName('#__joomleague_team_player' , 'tp'))
	    ->innerJoin($db->quoteName('#__joomleague_person' , 'pr') . ' ON ' .$db->quoteName('tp.person_id') . ' = ' .$db->quoteName('pr.id'))
	    ->innerJoin($db->quoteName('#__joomleague_project_team' , 'pt') . ' ON ' .$db->quoteName('pt.id') . ' = ' . $db->quoteName('tp.projectteam_id'))
	    ->innerJoin($db->quoteName('#__joomleague_round' , 'r') . ' ON ' .$db->quoteName('r.project_id') . ' = ' .$db->quoteName('pt.project_id'))
	    ->innerJoin($db->quoteName('#__joomleague_project_position' , 'ppos') . ' ON ' .$db->quoteName('ppos.id') . ' = ' .$db->quoteName('tp.project_position_id'))
	    ->innerJoin($db->quoteName('#__joomleague_position' , 'pos') . ' ON ' .$db->quoteName('pos.id') . ' = ' .$db->quoteName('ppos.position_id'))
	    
	    ->leftJoin($db->quoteName('#__joomleague_round' , 'rinjuryfrom') . ' ON ' .$db->quoteName('tp.injury_date') . ' = ' .$db->quoteName('rinjuryfrom.id'))
	    ->leftJoin($db->quoteName('#__joomleague_round' , 'rinjuryto') . ' ON ' .$db->quoteName('tp.injury_end') . ' = ' .$db->quoteName('rinjuryto.id'))
	    ->leftJoin($db->quoteName('#__joomleague_round' , 'rsuspfrom') . ' ON ' .$db->quoteName('tp.suspension_date') . ' = ' .$db->quoteName('rsuspfrom.id'))
	    ->leftJoin($db->quoteName('#__joomleague_round' , 'rsuspto') . ' ON ' .$db->quoteName('tp.suspension_end') . ' = ' .$db->quoteName('rsuspto.id'))
	    ->leftJoin($db->quoteName('#__joomleague_round' , 'rawayfrom') . ' ON ' . $db->quoteName('tp.away_date') . ' = ' .$db->quoteName('rawayfrom.id'))
	    ->leftJoin($db->quoteName('#__joomleague_round' , 'rawayto') . ' ON ' .$db->quoteName('tp.away_end') . ' = ' .$db->quoteName('rawayto.id'))
	    ->where($db->quoteName('r.id') . ' = ' .$db->quote('$round_id'))
	    ->where($db->quoteName('tp.id') . '=' .$db->quote('$player_id'))
	    ->where($db->quoteName('pr.published') . ' = ' . ('1'))
	    ->where($db->quoteName('tp.published') . ' = ' . ('1'));
	    
		$db->setQuery($query);
		$rows=$db->loadObjectList();
		return $rows;
	}

	function getRosterStats()
	{
		$stats=$this->getProjectStats();
		$projectteam=$this->getprojectteam();
		$result=array();
		foreach ($stats as $pos => $pos_stats)
		{
			foreach ($pos_stats as $k => $stat)
			{
				$result[$pos][$stat->id]=$stat->getRosterStats($projectteam->team_id,$projectteam->project_id, $pos);
			}
		}
		return $result;
	}
}
