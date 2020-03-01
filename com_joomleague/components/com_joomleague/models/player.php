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
use Joomla\CMS\Language\Text;

defined('_JEXEC') or die();

// include of person model
// this will include project-data+specific functions
//require_once('person.php');
require_once JLG_PATH_SITE.'/models/person.php';
//JLoader::register('JoomleagueModelPerson', JLG_PATH_SITE.'/models/person.php');

class JoomleagueModelPlayer extends JoomleagueModelPerson
{
	/**
	 * data array for player history
	 */
	protected $_playerhistory 	= null;
	protected $_teamplayers 	= null;
	protected $_inproject 		= null;
	
	/**
	 * Constructor
	 */
	public function __construct($config = array())
	{
		parent::__construct($config);

		$app 	= Factory::getApplication();
		$input = $app->input;

		$this->projectid	= $input->getInt('p',0);
		$this->personid		= $input->getInt('pid',0);
		$this->teamplayerid = $input->getInt('pt',0);
	}
	
	/**
	 * Get all teamplayers of the project where the person played in
	 * (in case the player was transferred team of the project within the season)
	 */
	public function getTeamPlayers()
	{
	    $db = Factory::getDbo();
	    $query = $db->getQuery(true);
		if (is_null($this->_teamplayers))
		{
		    $query
		          ->select('tp.*')
		          ->select('pt.project_id')
		          ->select('pt.team_id')
		          ->select('pos.name AS position_name')
		          ->select('ppos.position_id')
		          ->select('rinjuryfrom.round_date_first injury_date')
		          ->select('rinjuryto.round_date_last injury_end')
		          ->select('rinjuryfrom.name rinjury_from')
		          ->select('rinjuryto.name rinjury_to')
		          ->select('rsuspfrom.round_date_first suspension_date')
		          ->select('rsuspto.round_date_last suspension_end')
		          ->select('rsuspfrom.name rsusp_from')
		          ->select('rsuspto.name rsusp_to')
		          ->select('rawayfrom.round_date_first away_date')
		          ->select('rawayto.round_date_last away_end')
		          ->select('rawayfrom.name raway_from')
		          ->select('rawayto.name raway_to')
		          ->from('#__joomleague_team_player AS tp')
		          ->innerJoin('#__joomleague_project_team AS pt ON pt.id=tp.projectteam_id')
		          ->innerJoin('#__joomleague_project_position AS ppos ON ppos.id=tp.project_position_id')
		          ->innerJoin('#__joomleague_project AS p ON p.id=pt.project_id')
		          ->leftJoin('#__joomleague_position AS pos ON pos.id=ppos.position_id')
		          ->leftJoin('#__joomleague_round AS rinjuryfrom ON tp.injury_date=rinjuryfrom.id')
		          ->leftJoin('#__joomleague_round AS rinjuryto ON tp.injury_end=rinjuryto.id')
		          ->leftJoin('#__joomleague_round AS rsuspfrom ON tp.suspension_date=rsuspfrom.id')
		          ->leftJoin('#__joomleague_round AS rsuspto ON tp.suspension_end=rsuspto.id')
		          ->leftJoin('#__joomleague_round AS rawayfrom ON tp.away_date=rawayfrom.id')
		          ->leftJoin('#__joomleague_round AS rawayto ON tp.away_end=rawayto.id')
		          ->where('pt.project_id='.$db->quote($this->projectid))
		          ->where('tp.person_id='.$db->quote($this->personid))
		          ->where('p.published = 1');
			$db->setQuery($query);
			$this->_teamplayers = $db->loadObjectList('projectteam_id');
		}

		return $this->_teamplayers;
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
	public function getTeamPlayerByRound($round_id=0, $player_id=0)
	{
	    $app = Factory::getApplication();
	    $db = Factory::getDbo();
	    $query = $db->getQuery(true);
		if (is_null($this->_inproject))
		{
		    $query
		          ->select('tp.*')
		          ->select('tp.injury AS injury')
		          ->select('pt.team_id')
		          ->select('tp.suspension AS suspension')
		          ->select('tp.away AS away')
		          ->select('ppos.id As pposid')
		          ->select('ppos.position_id')
		          ->select('pos.id AS position_id')
		          ->select('pos.name AS position_name')
		          ->select('rinjuryfrom.round_date_first injury_date')
		          ->select('rinjuryto.round_date_last injury_end')
		          ->select('rinjuryfrom.name rinjury_from')
		          ->select('rinjuryto.name rinjury_to')
		          ->select('rsuspfrom.round_date_first suspension_date')
		          ->select('rsuspto.round_date_last suspension_end')
		          ->select('rsuspfrom.name rsusp_from')
		          ->select('rsuspto.name rsusp_to')
		          ->select('rawayfrom.round_date_first away_date')
		          ->select('rawayto.round_date_last away_end')
		          ->select('rawayfrom.name raway_from')
		          ->select('rawayto.name raway_to')
		          ->from('#__joomleague_team_player AS tp')
		          ->innerJoin('#__joomleague_person AS pr ON tp.person_id=pr.id')
		          ->innerJoin('#__joomleague_project_team AS pt ON pt.id=tp.projectteam_id')
		          ->innerJoin('#__joomleague_round AS r ON r.project_id=pt.project_id')
		          ->innerJoin('#__joomleague_project_position AS ppos ON ppos.id=tp.project_position_id')
		          ->innerJoin('#__joomleague_position AS pos ON pos.id=ppos.position_id')
		          
		          ->leftJoin('#__joomleague_round AS rinjuryfrom ON tp.injury_date=rinjuryfrom.id')
		          ->leftJoin('#__joomleague_round AS rinjuryto ON tp.injury_end=rinjuryto.id')
		          ->leftJoin('#__joomleague_round AS rsuspfrom ON tp.suspension_date=rsuspfrom.id')
		          ->leftJoin('#__joomleague_round AS rsuspto ON tp.suspension_end=rsuspto.id')
		          ->leftJoin('#__joomleague_round AS rawayfrom ON tp.away_date=rawayfrom.id')
		          ->leftJoin('#__joomleague_round AS rawayto ON tp.away_end=rawayto.id')
		          ->where('r.id='.$round_id)
				  ->where('pr.id='.$player_id)
				  ->where('pr.published = 1')
				  ->where('tp.published = 1')
				  ->order('tp.id DESC');
				  
				  try
				  { 
				      $db->setQuery($query);
				      $this->_inproject=$db->loadObjectList();
				  }
				  catch (RuntimeException $e)
				  {
				      $app->enqueueMessage(Text::_($e->getMessage()), 'error');
				      
				      return false;
				  }
		}
		return $this->_inproject;
	}

	/**
	 *  As a player can be transferred from one team to the next within a season, it is possible that
	 */
	public function getTeamPlayer_old()
	{
	    $db = Factory::getDbo();
	    $query = $db->getQuery(true);
		if (is_null($this->_inproject))
		{
		    $query
		          ->select('tp.*')
		          ->select('pt.project_id')
		          ->select('pt.team_id')
		          ->select('pos.name AS position_name')
		          ->select('ppos.position_id')
		          ->select('pt.notes AS ptnotes')
		          ->from('pt.notes AS ptnotes')
		          ->innerJoin('#__joomleague_project_team AS pt ON pt.id=tp.projectteam_id')
		          ->innerJoin('#__joomleague_project_position AS ppos ON ppos.id=tp.project_position_id')
		          ->innerJoin('#__joomleague_project AS p ON p.id=pt.project_id')
		          ->leftJoin('#__joomleague_position AS pos ON pos.id=ppos.position_id')
		          ->where('pt.project_id='.$db->Quote($this->projectid))
		          ->where('tp.person_id='.$db->Quote($this->personid))
		          ->where('p.published = 1');
			$db->setQuery($query);
			$this->_inproject=$db->loadObjectList();
		}
		return $this->_inproject;
	}
	
	/**
	 * getTeamStaff
	 */
	public function getTeamStaff()
	{
	    $db = Factory::getDbo();
	    $query = $db->getQuery(true);
		if (is_null($this->_inproject))
		{
		    $query
		          ->select('ts.*')
		          ->select('pos.name AS position_name')
		          ->select('pt.notes AS ptnotes')
		          ->from('#__joomleague_team_staff AS ts')
		          ->innerJoin('#__joomleague_project_team AS pt ON pt.id=ts.projectteam_id')
		          ->innerJoin('#__joomleague_project_position AS ppos ON ppos.id=ts.project_position_id')
		          ->innerJoin('#__joomleague_project AS p ON p.id=pt.project_id')
		          ->leftJoin('#__joomleague_position AS pos ON pos.id=ppos.position_id')
		          ->where('pt.project_id='.$db->Quote($this->projectid))
		          ->where('ts.person_id='.$db->Quote($this->personid))
		          ->where('p.published = 1');
			$db->setQuery($query);
			$this->_inproject=$db->loadObject();
		}
		return $this->_inproject;
	}
	
	/**
	 * getPositionEventTypes
	 */
	public function getPositionEventTypes($positionId=0)
	{
	    $db = Factory::getDbo();
	    $query = $db->getQuery(true);
		$result=array();
		// TODO: pj.id is not known in the query. Is this function called anywhere? If not, just remove it...
		$query
		      ->select('pet.*')
		      //->select('pj.id AS ppID')
		      ->select('et.name')
		      ->select('et.icon')
		      ->from('#__joomleague_position_eventtype AS pet')
		      ->innerJoin('#__joomleague_eventtype AS et ON et.id=pet.eventtype_id')
		      ->innerJoin('#__joomleague_match_event AS me ON et.id=me.event_type_id')
		      ->where('me.project_id='.$this->projectid);
		if ($positionId > 0)
		{
		    $query->where('pet.position_id='.(int)$positionId);
		}
		$query->order('pet.ordering');
		$db->setQuery($query);
		$result=$db->loadObjectList();
		if ($result)
		{
			if ($positionId) {
				return $result;
			} else {
				$posEvents=array();
				foreach ($result as $r)
				{
					//$posEvents[$r->position_id][]=$r;
					$posEvents[$r->ppID][]=$r;
				}
				return ($posEvents);
			}
		}
		return array();
	}

	/**
	 * get person history across all projects,with team,season,position,... info
	 *
	 * @param int $person_id,linked to player_id from Person object
	 * @param int $order ordering for season and league,default is ASC ordering
	 * @param string $filter e.g. "s.name=2007/2008",default empty string
	 * @return array of objects
	 */
	public function &getPlayerHistory($sportstype=0, $order='ASC')
	{
	    $db = Factory::getDbo();
	    $query = $db->getQuery(true);
		if (empty($this->_playerhistory))
		{
			$personid=$this->personid;
			$query
			     ->select('pr.id AS pid')
			     ->select('tp.person_id')
			     ->select('tp.id AS tpid')
			     ->select('pt.project_id')
			     ->select('pr.firstname')
			     ->select('pr.lastname')
			     ->select('p.name AS project_name')
			     ->select('s.name AS season_name')
			     ->select('t.name AS team_name')
			     ->select('pos.name AS position_name')
			     ->select('tp.project_position_id')
			     ->select('t.id AS team_id')
			     ->select('pt.id AS ptid')
			     ->select('pos.id AS posID')
			     ->select($this->constructSlug($db, 'team_slug', 't.alias', 't.id'))
			     ->select($this->constructSlug($db, 'project_slug', 'p.alias', 'p.id'))
			     ->from('#__joomleague_person AS pr')
			     ->innerJoin('#__joomleague_team_player AS tp ON tp.person_id=pr.id')
			     ->innerJoin('#__joomleague_project_team AS pt ON pt.id=tp.projectteam_id')
			     ->innerJoin('#__joomleague_team AS t ON t.id=pt.team_id')
			     ->innerJoin('#__joomleague_project AS p ON p.id=pt.project_id')
			     ->innerJoin('#__joomleague_season AS s ON s.id=p.season_id')
			     ->innerJoin('#__joomleague_league AS l ON l.id=p.league_id')
			     ->innerJoin('#__joomleague_project_position AS ppos ON ppos.id=tp.project_position_id')
			     ->innerJoin('#__joomleague_position AS pos ON pos.id=ppos.position_id')
			     ->where('pr.id='.$db->quote($this->personid))
			     ->where('p.published=1')
			     ->where('tp.published=1')
			     ->where('tp.published=1');
			if ($sportstype > 0)
			{
			    $query->where('p.sports_type_id = '.$db->Quote($sportstype));
			}
			$query->order('s.ordering ' . $order .',l.ordering ASC,p.name ASC');
			
			$db->setQuery($query);
			$this->_playerhistory=$db->loadObjectList();
		}
		return $this->_playerhistory;
	}
	
	/**
	 * new insert for staff
	 */
	public function &getPlayerHistoryStaff($sportstype=0, $order='ASC')
	{
	    $db = Factory::getDbo();
	    $query = $db->getQuery(true);
		if (empty($this->_playerhistorystaff))
		{
			$personid=$this->personid;
			$query
			     ->select('pr.id AS pid')
			     ->select('ts.person_id')
			     ->select('pt.project_id')
			     ->select('pr.firstname')
			     ->select('pr.lastname')
			     ->select('pr.lastname')
			     ->select('p.name AS project_name')
			     ->select('s.name AS season_name')
			     ->select('t.name AS team_name')
			     ->select('pos.name AS position_name')
			     ->select('ppos.position_id')
			     ->select('t.id AS team_id')
			     ->select('pt.id AS ptid')
			     ->select($this->constructSlug($db, 'team_slug', 't.alias', 't.id'))
			     ->select($this->constructSlug($db, 'project_slug', 'p.alias', 'p.id'))
			     ->from('#__joomleague_person AS pr')
			     ->innerJoin('#__joomleague_team_staff AS ts ON ts.person_id=pr.id')
			     ->innerJoin('#__joomleague_project_team AS pt ON pt.id=ts.projectteam_id')
			     ->innerJoin('#__joomleague_team AS t ON t.id=pt.team_id')
			     ->innerJoin('#__joomleague_project AS p ON p.id=pt.project_id')
			     ->innerJoin('#__joomleague_season AS s ON s.id=p.season_id')
			     ->innerJoin('#__joomleague_league AS l ON l.id=p.league_id')
			     ->innerJoin('#__joomleague_project_position AS ppos ON ppos.id=ts.project_position_id')
			     ->innerJoin('#__joomleague_position AS pos ON pos.id=ppos.position_id')
			     ->where('pr.id='.$db->quote($this->personid))
			     ->where('p.published=1')
			     ->where('p.published=1');
			if ($sportstype > 0)
			{
			    $query->where('p.sports_type_id = '.$db->quote($sportstype));
			}
			$query->order('s.ordering ' . $order .',l.ordering ASC,p.name ASC');
			$db->setQuery($query);
			$this->_playerhistorystaff=$db->loadObjectList();
		}
		return $this->_playerhistorystaff;
	}



	/**
	 * get all events associated to positions the player was assigned too in different projects
	 *
	 * @return array of event types row objects.
	 */
	public function getAllEvents($sportstype=0)
	{
		$history=&$this->getPlayerHistory($sportstype);
		$positionhistory=array();
		foreach($history as $h)
		{
			if (!in_array($h->posID,$positionhistory) && $h->posID!=null)
			{
				$positionhistory[]=$h->posID;
			}
		}
		if (!count($positionhistory))
		{
			return array();
		}
		$db = Factory::getDbo();
		$query = $db->getQuery(true);		
		$query
		      ->select('DISTINCT et.*')
		      ->from('#__joomleague_eventtype AS et')
		      ->innerJoin('#__joomleague_position_eventtype AS pet ON pet.eventtype_id=et.id')
		      ->innerJoin('#__joomleague_project_position AS ppos ON ppos.position_id=pet.position_id')
		      ->where('published=1')
		      ->where('pet.position_id IN ('. implode(',',$positionhistory) . ')')
		      ->order('et.ordering');
		$db->setQuery($query);
		$info=$db->loadObjectList();
		return $info;
	}

	/**
	 * getInOutStats
	 */
	public function getPlayerInOutStats($project_id, $projectteam_id, $teamplayer_id)
	{
	    $db = Factory::getDbo();
	    $quotedTpId = $db->Quote($teamplayer_id);
	    $quotedPtId = $db->Quote($projectteam_id);
	    $query  = ' SELECT COUNT(m.id) AS played, SUM(ios.started) AS started, SUM(ios.sub_in) AS sub_in, SUM(ios.sub_out) AS sub_out'
	        . ' FROM #__joomleague_match AS m'
	            . ' INNER JOIN '
	                . ' ('
	                    . '   SELECT mp.match_id,'
	                    . '   sum(mp.came_in=0 AND mp.teamplayer_id = '.$quotedTpId.') AS started,'
	                    . '   sum(mp.came_in=1 AND mp.teamplayer_id = '.$quotedTpId.') AS sub_in,'
	                    . '   sum((mp.teamplayer_id = '.$quotedTpId.' AND mp.out = 1) OR mp.in_for = '.$quotedTpId.') AS sub_out'
	                    . '   FROM #__joomleague_match_player AS mp'
	                    . '   INNER JOIN #__joomleague_team_player AS tp ON tp.id=mp.teamplayer_id'
	                    . '   INNER JOIN #__joomleague_match AS m ON m.id=mp.match_id'
	                    . '   INNER JOIN #__joomleague_round r ON r.id=m.round_id'
	                    . '   INNER JOIN #__joomleague_project AS p ON p.id=r.project_id'
	                    . '   INNER JOIN #__joomleague_project_team AS pt1 ON pt1.id=m.projectteam1_id'
	                    . '   INNER JOIN #__joomleague_team AS t1 ON t1.id=pt1.team_id'
	                    . '   INNER JOIN #__joomleague_project_team AS pt2 ON pt2.id=m.projectteam2_id'
	                    . '   INNER JOIN #__joomleague_team AS t2 ON t2.id=pt2.team_id'
	                    . '   WHERE ((mp.teamplayer_id = '.$quotedTpId.') OR (mp.in_for = '.$quotedTpId.'))'
	                    . '     AND p.id='.$this->_db->Quote($project_id)
	                    . '     AND (pt1.id='.$quotedPtId.' OR pt2.id='.$quotedPtId.')'
	                    . '     AND m.published = 1 AND p.published = 1'
	                    . '   GROUP BY mp.match_id'
	                    . ' ) AS ios ON ios.match_id=m.id';
		$db->setQuery($query);
		$inoutstat = $db->loadObject();

		return $inoutstat;
	}
	

	/**
	 * getRounds
	 * @see JoomleagueModelProject::getRounds()
	 */
	public function getRoundsPlayer($roundcodestart,$roundcodeend)
	{
	    $db = Factory::getDbo();
	    $query = $db->getQuery(true);
		$projectid=$this->projectid;
		$thisround=0;
		$query
		      ->select('id')
		      ->from('#__joomleague_round')
		      ->where('project_id='.(int)$projectid)
		      ->where('roundcode>='.(int)$roundcodestart)
              ->where('roundcode<='.(int)$roundcodeend)
              ->order('round_date_first');
		$db->setQuery($query);
		$rows=$db->loadColumn();
		$rounds=array();
		if (count($rows) > 0)
		{
			$startround = $this->getTable('Round','Table');
			$startround->load($rows[0]);
			$rounds[0]=$startround;
			$endround = $this->getTable('Round','Table');
			$endround->load(end($rows));
			$rounds[1]=$endround;
		}
		return $rounds;
	}


	/**
	 * getTimePlayed
	 */
	public function getTimePlayed($player_id,$game_regular_time,$match_id = NULL)
	{
	    $db = Factory::getDbo();
	    $query = $db->getQuery(true);
		$result = 0;
		// starting line up without subs in/out
		$query
		      ->select('count(match_id) as totalmatch')
		      ->from('#__joomleague_match_player')
		      ->where('teamplayer_id = '.$player_id)
		      ->where('came_in = 0');
		
		if ( $match_id )
		{
		    $query->where('match_id = '.$match_id);
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
		      ->from('#__joomleague_match_player')
		      ->where('teamplayer_id = '.$player_id)
		      ->where('came_in = 1 and in_for IS NOT NULL');
		if ( $match_id )
		{		    
			$query->where('match_id = '.$match_id);
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
		      ->from('#__joomleague_match_player')
		      ->where('in_for = '.$player_id)
		      ->where('came_in = 1');		
		if ( $match_id )
		{
		    $query->where('match_id = '.$match_id);
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
		      ->from('#__joomleague_eventtype')
		      ->where('suspension = 1');
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
		          ->where('teamplayer_id = '.$player_id)
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
	 * get stats for the player position (the person can be player of multiple teams in the project due to transfer(s))
	 * @return array
	 */
	public function getStats($current_round=0, $personid=0)
	{
		$stats = array();
		$players = $this->getTeamPlayerByRound($current_round, $personid);
		if (is_array($players))
		{
			foreach ($players as $player)
			{
				// Remark: we cannot use array_merge because numerical keys will result in duplicate entries
				// so we check if a key already exists in the output array before adding it.
				$projectStats = $this->getProjectStats(0,$player->position_id);
				if (is_array($projectStats))
				{
					foreach ($projectStats as $key=>$projectStat)
					{
						if (!array_key_exists($key, $stats))
						{
							$stats[$key] = $projectStat;
						}
					}
				}
			}
		}
		return $stats;
	}

	/**
	 * get all statistics types that are in use for the player in his/her whole career
	 * @return array
	 */
	public function getCareerStats($person_id, $sports_type_id)
	{
	    $db = Factory::getDbo();
	    $query = $db->getQuery(true);
		if (empty($this->_careerStats))
		{
			$db = Factory::getDbo();
			$query = $db->getQuery(true);

			$query->select(array('s.id','s.name','s.short','s.class','s.icon','s.calculated','s.params','s.baseparams'));
			$query->select(array('ppos.id AS pposid','ppos.position_id AS position_id'));
			$query->from('#__joomleague_person AS p');


			$query->where(array('p.id = '.$db->Quote($person_id)));

			if ($sports_type_id > 0)
			{
				$query->where('pos.sports_type_id = '.$db->Quote($sports_type_id));
			}

			$query->group('s.id, ppos.id, s.name, s.short, s.class, s.icon , s.calculated , s.params , s.baseparams');

			// join Teamplayer
			$query->join('INNER', '#__joomleague_team_player AS tp ON tp.person_id=p.id');

			// join project-position
			$query->join('INNER', '#__joomleague_project_position AS ppos ON tp.project_position_id=ppos.id');

			// join position
			$query->join('INNER', '#__joomleague_position AS pos ON ppos.position_id=pos.id');

			// join position-statistic
			$query->join('INNER', '#__joomleague_position_statistic AS ps ON ps.position_id=pos.id');

			// join statistic
			$query->join('INNER', '#__joomleague_statistic AS s ON ps.statistic_id=s.id');

			$db->setQuery($query);
			$this->_careerStats=$db->loadObjectList();
		}
		$stats=array();
		if (count($this->_careerStats) > 0)
		{
			foreach ($this->_careerStats as $k => $row)
			{
				$stat= JLGStatistic::getInstance($row->class);
				$stat->bind($row);
				$stat->set('position_id',$row->position_id);
				$stats[$row->id]=$stat;
			}
		}
		return $stats;
	}

	/**
	 * get player stats per game (one array entry per statistic ID)
	 * @return array
	 */
	public function getPlayerStatsByGame()
	{
		$teamplayers=$this->getTeamPlayers();
		$displaystats=array();
		if (count($teamplayers))
		{
			$project = $this->getProject();
			$project_id=$project->id;
			// Determine teamplayer id(s) of the player (plural if (s)he played in multiple teams of the project
			// and the position_id(s) where the player played
			$teamplayer_ids = array();
			$position_ids = array();
			foreach ($teamplayers as $teamplayer)
			{
				$teamplayer_ids[] = $teamplayer->id;
				if (!in_array($teamplayer->position_id, $position_ids))
				{
					$position_ids[] = $teamplayer->position_id;
				}
			}
			// For each position_id get the statistics types and merge the results (prevent duplicate statistics ids)
			// ($pos_stats is an array indexed by statistic_id)
			$pos_stats = array();
			foreach ($position_ids as $position_id)
			{
				$stats_for_position_id = $this->getProjectStats(0, $position_id);
				foreach ($stats_for_position_id as $id => $stat)
				{
					if (!array_key_exists($id, $pos_stats))
					{
						$pos_stats[$id] = $stat;
					}
				}
			}
			foreach ($pos_stats as $stat)
			{
				if(!empty($stat))
				{
					if ($stat->showInSingleMatchReports())
					{
						$stat->set('gamesstats',$stat->getPlayerStatsByGame($teamplayer_ids, $project_id));
						$displaystats[]=$stat;
					}
				}
			}
		}
		return $displaystats;
	}

	/**
	 * get player stats per project (one array entry per statistic ID)
	 * @return array
	 */
	public function getPlayerStatsByProject($sportstype=0, $current_round=0, $playerid)
	{
		$teamplayer = $this->getTeamPlayerByRound($current_round, $playerid);
		$result=array();
		if (is_array($teamplayer) && !empty($teamplayer))
		{
			// getTeamPlayer can return multiple teamplayers, because a player can be transferred from
			// one team to another inside a season, but they are all the same person so have same person_id.
			// So we get the player_id from the first array entry.
			$stats  = $this->getCareerStats($teamplayer[0]->person_id, $sportstype);
			$history= $this->getPlayerHistory($sportstype);
			if(count($history)>0)
			{
				foreach ($stats as $stat)
				{
					if(!empty($stat))
					{
						foreach ($history as $player)
						{
							$result[$stat->id][$player->project_id][$player->ptid]=$stat->getPlayerStatsByProject($player->person_id, $player->ptid, $player->project_id, $sportstype);
						}
						$result[$stat->id]['totals'] = $stat->getPlayerStatsByProject($player->person_id, 0, 0, $sportstype);
					}
				}
			}
		}
		return $result;
	}


	/**
	 * getGames
	 */
	public function getGames()
	{
	    $app = Factory::getApplication();
	    $option = Factory::getApplication()->input->getCmd('option');
	    $db = Factory::getDbo();
	    $query = $db->getQuery(true);
		$teamplayers=$this->getTeamPlayers();
		$games=array();
		//$app->enqueueMessage(Text::_(get_class($this).' '.__FUNCTION__.' teamplayers<br><pre>'.print_r($teamplayers,true).'</pre>'),'Error');
		if (count($teamplayers))
		{
			$quoted_tpids=array();
			foreach ($teamplayers as $teamplayer)
			{
				$quoted_tpids[]=$db->Quote($teamplayer->id);
			}
			$tpid_list = '('.implode(',', $quoted_tpids).')';
			$query  = ' SELECT m.*, ios.*'
					. ' FROM #__joomleague_match AS m'
					. ' INNER JOIN'
					. ' ('
					. ' 		SELECT mp.match_id, t1.id AS team1, t2.id AS team2, r.roundcode, r.project_id, tp.projectteam_id, p.timezone, '
					. ' 		COALESCE(sum(mp.came_in=0 AND mp.teamplayer_id IN ('.$tpid_list.')),0) AS started,'
					. ' 		COALESCE(sum(mp.came_in=1 AND mp.teamplayer_id IN ('.$tpid_list.')),0) AS sub_in,'
					. ' 		COALESCE(sum((mp.teamplayer_id IN ('.$tpid_list.') AND mp.out = 1) OR mp.in_for IN ('.$tpid_list.')),0) AS sub_out'
					. ' 		FROM #__joomleague_match_player AS mp'
					. ' 		INNER JOIN #__joomleague_team_player AS tp ON tp.id=mp.teamplayer_id'
					. ' 		INNER JOIN #__joomleague_match AS m ON m.id=mp.match_id'
					. ' 		INNER JOIN #__joomleague_round r ON r.id=m.round_id'
					. ' 		INNER JOIN #__joomleague_project AS p ON p.id=r.project_id'
					. ' 		INNER JOIN #__joomleague_project_team AS pt1 ON pt1.id=m.projectteam1_id'
					. ' 		INNER JOIN #__joomleague_team AS t1 ON t1.id=pt1.team_id'
					. ' 		INNER JOIN #__joomleague_project_team AS pt2 ON pt2.id=m.projectteam2_id'
					. ' 		INNER JOIN #__joomleague_team AS t2 ON t2.id=pt2.team_id'
					. ' 		WHERE ((mp.teamplayer_id IN ('.$tpid_list.')) OR (mp.in_for IN ('.$tpid_list.')))'
					. '           AND m.published = 1 AND p.published = 1'
					. ' 		GROUP BY mp.match_id, t1.id, t2.id, r.roundcode, r.project_id, tp.projectteam_id, p.timezone'
					. ' ) AS ios ON ios.match_id=m.id'
					. ' ORDER BY m.match_date';
		
    					try
    					{
    					   
                			$db->setQuery($query);
                			$games =  $db->loadObjectList();
    					}
    					catch (RuntimeException $e)
    					{
    					   $app->enqueueMessage(Text::_($e->getMessage()), 'error');
    					    
    					    return false;
    					}
					
			if ($games)
			{
				foreach ($games as $game)
				{
				    JoomleagueHelper::convertMatchDateToTimezone($game);
				}
		}
		}
		return $games;
	}


	/**
	 * getGamesEvents
	 */
	public function getGamesEvents()
	{
	    $db = Factory::getDbo();
	    $query = $db->getQuery(true);
		$teamplayers=$this->getTeamPlayers();
		$gameevents=array();
		if (count($teamplayers))
		{
			$quoted_tpids=array();
			foreach ($teamplayers as $teamplayer)
			{
				$quoted_tpids[]=$db->Quote($teamplayer->id);
			}			
			$query
			     ->select('SUM(me.event_sum) as value')
			     ->select('me.*')
			     ->select('me.match_id')
			     ->from('#__joomleague_match_event AS me')
			     ->where('me.teamplayer_id IN ('. implode(',', $quoted_tpids) .')')
			     ->group('me.match_id, me.event_type_id');
			$db->setQuery($query);
			$events=$db->loadObjectList();
			foreach ((array) $events as $ev)
			{
				if (isset($gameevents[$ev->match_id]))
				{
					$gameevents[$ev->match_id][$ev->event_type_id]=$ev->value;
				}
				else
				{
					$gameevents[$ev->match_id]=array($ev->event_type_id => $ev->value);
				}
			}
		}
		return $gameevents;
	}
}
