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
 * Model-Matchreport
 */
class JoomleagueModelMatchReport extends JoomleagueModelProject
{

	var $matchid=0;
	var $match=null;

	/**
	 * caching for players events. Used in stats calculations
	 * @var unknown_type
	 */
	var $_playersevents=null;

	/**
	 * caching for players basic stats. Used in stats calculations
	 * @var unknown_type
	 */
	var $_playersbasicstats=null;

	/**
	 * caching for staff basic stats. Used in stats calculations
	 * @var unknown_type
	 */
	var $_staffsbasicstats=null;

	function __construct()
	{
	    $app = Factory::getApplication();
		$this->matchid=$app->input->getInt('mid',0);
		parent::__construct();
	}

	// Functions (some specific for Matchreport) below to be replaced to project.php when recoded to general functions
	function &getMatch()
	{
	    $db = Factory::getDbo();
	    $query = $db->getQuery(true);
		if (is_null($this->match))
		{
		    $query
		          ->select('m.*,DATE_FORMAT(m.time_present,"%H:%i") time_present, r.project_id, p.timezone')
		          ->from('#__joomleague_match AS m')
		          ->innerJoin('#__joomleague_round AS r on r.id=m.round_id')
		          ->innerJoin('#__joomleague_project AS p on r.project_id=p.id')
		          ->where('m.id='. $db->Quote($this->matchid));
			$db->setQuery($query,0,1);
			$this->match=$db->loadObject();
			if ($this->match)
			{
				JoomleagueHelper::convertMatchDateToTimezone($this->match);
			}
		}
		return $this->match;
	}

	function &getProject()
	{
		if (empty($this->_project))
		{
			$match=$this->getMatch();
			$this->setProjectID($match->project_id);
			parent::getProject();
		}
		return $this->_project;
	}

	function getClubinfo($clubid)
	{
		$this->club = $this->getTable('Club','Table');
		$this->club->load($clubid);

		return $this->club;
	}

	function getRound()
	{
		$match=$this->getMatch();

		$round = $this->getTable('Round','Table');
		$round->load($match->round_id);

		//if no match title set then set the default one
		if(is_null($round->name) || empty($round->name))
		{
			$round->name=Text::sprintf('COM_JOOMLEAGUE_RESULTS_GAMEDAY_NB',$round->id);
		}

		return $round;
	}

	function getMatchPlayerPositions()
	{
	    $db = Factory::getDbo();
	    $query = $db->getQuery(true);
	    $query
	           ->select('pos.id, pos.name, ppos.position_id AS position_id, ppos.id as pposid')
	           ->from('#__joomleague_position AS pos')
	           ->innerJoin('#__joomleague_project_position AS ppos ON pos.id=ppos.position_id')
	           ->innerJoin('#__joomleague_match_player AS mp ON ppos.id=mp.project_position_id')
	           ->where('mp.match_id='.(int)$this->matchid)
	           ->group('ppos.id')
	           ->order('pos.ordering ASC');
		$db->setQuery($query);
		return $db->loadObjectList();
	}

	function getMatchStaffPositions()
	{
	    $db = Factory::getDbo();
	    $query = $db->getQuery(true);
	    $query
	           ->select('pos.id, pos.name, ppos.position_id AS position_id, ppos.id as pposid')
	           ->from('#__joomleague_position AS pos')
	           ->innerJoin('#__joomleague_project_position AS ppos ON pos.id=ppos.position_id')
	           ->innerJoin('#__joomleague_match_staff AS mp ON ppos.id=mp.project_position_id')
	           ->where(' mp.match_id='.(int)$this->matchid)
	           ->group('ppos.id')
	           ->order('pos.ordering ASC');
		$db->setQuery($query);
		return $db->loadObjectList();
	}

	function getMatchRefereePositions()
	{
	    $db = Factory::getDbo();
	    $query = $db->getQuery(true);
	    $query
	           ->select('pos.id, pos.name, ppos.position_id AS position_id, ppos.id as pposid')
	           ->from('#__joomleague_position AS pos')
	           ->innerJoin('#__joomleague_project_position AS ppos ON pos.id=ppos.position_id')
	           ->innerJoin('#__joomleague_match_referee AS mp ON ppos.id=mp.project_position_id')
	           ->where('mp.match_id='.(int)$this->matchid)
	           ->group('ppos.id')
	           ->order('pos.ordering ASC');
		$db->setQuery($query);
		return $db->loadObjectList();
	}

	function getMatchPlayers()
	{
	    $db = Factory::getDbo();
	    $query = $db->getQuery(true);
	    $query
	           ->select('pt.id')
	           ->select('tp.person_id')
	           ->select('p.firstname')
	           ->select('p.nickname')
	           ->select('p.lastname')
	           ->select('tp.jerseynumber')
	           ->select('ppos.position_id')
	           ->select('ppos.id AS pposid')
	           ->select('pt.team_id')
	           ->select('pt.id as ptid')
	           ->select('mp.teamplayer_id')
	           ->select('tp.picture')
	           ->select('p.picture AS ppic')
	           ->select($this->constructSlug($db, 'team_slug', 't.alias', 't.id'))
	           ->select($this->constructSlug($db, 'person_slug', 'p.alias', 'p.id'))
	           ->from('#__joomleague_match_player AS mp')
	           ->innerJoin('#__joomleague_team_player AS tp ON tp.id=mp.teamplayer_id')
	           ->innerJoin('#__joomleague_project_team AS pt ON pt.id=tp.projectteam_id')
	           ->innerJoin('#__joomleague_team AS t ON t.id=pt.team_id')
	           ->innerJoin('#__joomleague_person AS p ON tp.person_id=p.id')
	           ->leftJoin('#__joomleague_project_position AS ppos ON ppos.id=mp.project_position_id')
	           ->leftJoin('#__joomleague_position AS pos ON ppos.position_id=pos.id')
	           ->where('mp.match_id='.(int)$this->matchid)
	           ->where('mp.came_in=0')
	           ->where('p.published = 1')
	           ->order('mp.ordering, tp.jerseynumber, p.lastname');
		$db->setQuery($query);
		return $db->loadObjectList();
	}

	function getMatchStaff()
	{
	    $db = Factory::getDbo();
	    $query = $db->getQuery(true);
	    $query
	           ->select('p.id')
	           ->select('p.id AS person_id')
	           ->select('ms.team_staff_id')
	           ->select('p.firstname')
	           ->select('p.nickname')
	           ->select('p.lastname')
	           ->select('ppos.position_id')
	           ->select('ppos.id AS pposid')
	           ->select('pt.team_id')
	           ->select('pt.id as ptid')
	           ->select('tp.picture')
	           ->select($this->constructSlug($db, 'team_slug', 't.alias', 't.id'))
	           ->select($this->constructSlug($db, 'person_slug', 'p.alias', 'p.id'))
	           ->from('#__joomleague_match_staff AS ms')
	           ->innerJoin('#__joomleague_team_staff AS tp ON tp.id=ms.team_staff_id')
	           ->innerJoin('#__joomleague_project_team AS pt ON pt.id=tp.projectteam_id')
	           ->innerJoin('#__joomleague_person AS p ON tp.person_id=p.id')
	           ->innerJoin('#__joomleague_team AS t ON t.id=pt.team_id')
	           ->leftJoin('#__joomleague_project_position AS ppos ON ppos.id=ms.project_position_id')
	           ->leftJoin('#__joomleague_position AS pos ON ppos.position_id=pos.id')
	           ->where('ms.match_id='.(int)$this->matchid)
	           ->where('p.published = 1');
		       $db->setQuery($query);
		return $db->loadObjectList();
	}

	function getMatchReferees()
	{
	    $db = Factory::getDbo();
	    $query = $db->getQuery(true);
	    $query
	           ->select('p.id')
	           ->select('p.firstname')
	           ->select('p.nickname')
	           ->select('p.lastname')
	           ->select('ppos.position_id')
	           ->select('ppos.id AS pposid')
	           ->select('pos.name AS position_name')
	           ->select($this->constructSlug($db, 'person_slug', 'p.alias', 'p.id'))
	           ->from('#__joomleague_match_referee AS mr')
	           ->leftJoin('#__joomleague_project_referee AS pref ON mr.project_referee_id=pref.id')
	           ->innerJoin('#__joomleague_person AS p ON pref.person_id=p.id')
	           ->leftJoin('#__joomleague_project_position AS ppos ON ppos.id=mr.project_position_id')
	           ->leftJoin('#__joomleague_position AS pos ON ppos.position_id=pos.id')
	           ->where('mr.match_id='.(int)$this->matchid)
	           ->where('p.published = 1');
		$db->setQuery($query);
		return $db->loadObjectList();
	}

	function getSubstitutes()
	{
	    $db = Factory::getDbo();
	    $query = $db->getQuery(true);
	    $query
	           ->select('mp.in_out_time')
	           ->select('mp.teamplayer_id')
	           ->select('pt.team_id')
	           ->select('pt.id AS ptid')
	           ->select('tp.person_id')
	           ->select('tp.jerseynumber')
	           ->select('tp2.person_id AS out_person_id')
	           ->select('mp.in_for')
	           ->select('p2.id AS out_ptid')
	           ->select('p.firstname')
	           ->select('p.nickname')
	           ->select('p.lastname')
	           ->select('pos.name AS in_position')
	           ->select('pos2.name AS out_position')
	           ->select('p2.firstname AS out_firstname')
	           ->select('p2.nickname AS out_nickname')
	           ->select('p2.lastname AS out_lastname')
	           ->select('ppos.id AS pposid1')
	           ->select('ppos2.id AS pposid2')
	           ->select($this->constructSlug($db, 'team_slug', 't.alias', 't.id'))
	           ->select($this->constructSlug($db, 'person_slug', 'p.alias', 'p.id'))
	           ->from('#__joomleague_match_player AS mp')
	           ->leftJoin('#__joomleague_team_player AS tp ON mp.teamplayer_id=tp.id')
	           ->leftJoin('#__joomleague_project_team AS pt ON tp.projectteam_id=pt.id')
	           ->leftJoin('#__joomleague_person AS p ON tp.person_id=p.id AND p.published = 1')
	           ->leftJoin('#__joomleague_team_player AS tp2 ON mp.in_for=tp2.id')
	           ->leftJoin('#__joomleague_person AS p2 ON tp2.person_id=p2.id AND p2.published = 1')
	           ->leftJoin('#__joomleague_project_position AS ppos ON ppos.id=mp.project_position_id')
	           ->leftJoin('#__joomleague_position AS pos ON ppos.position_id=pos.id')
	           ->leftJoin('#__joomleague_match_player AS mp2 ON mp.match_id=mp2.match_id and mp.in_for=mp2.teamplayer_id')
	           ->leftJoin('#__joomleague_project_position AS ppos2 ON ppos2.id=mp2.project_position_id')
	           ->leftJoin('#__joomleague_position AS pos2 ON ppos2.position_id=pos2.id')
	           ->innerJoin('#__joomleague_team AS t ON t.id=pt.team_id')
	           ->where('mp.match_id = '.(int)$this->matchid)
	           ->where('mp.came_in > 0')
	           ->order('(mp.in_out_time+0)');
		$db->setQuery($query);
		$result=$db->loadObjectList();
		return $result;
	}

	function getEventTypes($evid = 0)
	{
	    $db = Factory::getDbo();
	    $query = $db->getQuery(true);
	    $query
	           ->select('et.id')
	           ->select('et.name')
	           ->select('et.icon')
	           ->from('#__joomleague_eventtype AS et')
	           ->innerJoin('#__joomleague_position_eventtype AS pet ON pet.eventtype_id=et.id')
	           ->leftJoin('#__joomleague_match_event AS me ON et.id=me.event_type_id')
	           ->where('me.match_id='.(int)$this->matchid)
	           ->order('pet.ordering');
		/*$db->setQuery($query);
		return $db->loadObjectList();
	}
*/
	           try{
	               $db->setQuery($query);
	               $result = $db->loadObjectList();
	               $result = array_unique($result, SORT_REGULAR );
	           }
	           catch (Exception $e)
	           {
	               $app->enqueueMessage(Text::_(__METHOD__.' '.__LINE__.' '.$e->getMessage()), 'error');
	               $result = false;
	           }
	           $db->disconnect();
	           return $result;
	}
	function getPlayground($pgid)
	{
		$this->playground = $this->getTable('Playground','Table');
		$this->playground->load($pgid);

		return $this->playground;
	}

	/**
	 * get match statistics as an array (projectteam_id => teamplayer_id => statistic_id)
	 * @return array
	 */
	function getMatchStats()
	{
		$match=$this->getMatch();
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		$query
		      ->select('*')
		      ->from('#__joomleague_match_statistic')
		      ->where('match_id='. $db->Quote($match->id));
		$db->setQuery($query);
		$res=$db->loadObjectList();

		$stats = array(	$match->projectteam1_id => array(),
						$match->projectteam2_id => array());
		if(count($stats)>0 && count($res)>0) {
			foreach ($res as $stat)
			{
				@$stats[$stat->projectteam_id][$stat->teamplayer_id][$stat->statistic_id]=$stat->value;
			}
		}
		return $stats;
	}

	/**
	 * get match statistics as array(teamplayer_id => array(statistic_id => value))
	 * @return array
	 */
	function getPlayersStats()
	{
		if (!($this->_playersbasicstats))
		{
			$match=$this->getMatch();
			$db = Factory::getDbo();
			$query = $db->getQuery(true);
			$query
			     ->select('*')
			     ->from('#__joomleague_match_statistic')
			     ->where('match_id='. $db->Quote($match->id));
			$db->setQuery($query);
			$res=$db->loadObjectList();

			$stats=array();
			if (count($res))
			{
				foreach ($res as $stat)
				{
					@$stats[$stat->teamplayer_id][$stat->statistic_id]=$stat->value;
				}
			}
			$this->_playersbasicstats=$stats;
		}

		return $this->_playersbasicstats;
	}

	/**
	 * get match statistics as array(teamplayer_id => array(event_type_id => value))
	 * @return array
	 */
	function getPlayersEvents()
	{
		if (!($this->_playersevents))
		{
			$match=$this->getMatch();
			$db = Factory::getDbo();
			$query = $db->getQuery(true);
			$query
			     ->select('*')
			     ->from('#__joomleague_match_event')
			     ->where('match_id='. $db->Quote($match->id));
			$db->setQuery($query);
			$res=$db->loadObjectList();

			$events=array();
			if (count($res))
			{
				foreach ($res as $event)
				{
					@$events[$event->teamplayer_id][$event->event_type_id] += $event->event_sum;
				}
			}
			$this->_playersevents=$events;
		}

		return $this->_playersevents;
	}

	/**
	 * get match statistics as an array (team_staff_id => statistic_id)
	 * @return array
	 */
	function getMatchStaffStats()
	{
		if (!($this->_staffsbasicstats))
		{
			$match=$this->getMatch();
			$db = Factory::getDbo();
			$query = $db->getQuery(true);
			$query
			     ->select('*')
			     ->from('#__joomleague_match_staff_statistic')
			     ->where('match_id='. $db->Quote($match->id));
			$db->setQuery($query);
			$res=$db->loadObjectList();

			$stats=array();
			if (count($res))
			{
				foreach ($res as $stat)
				{
					@$stats[$stat->team_staff_id][$stat->statistic_id]=$stat->value;
				}
			}
			$this->_staffsbasicstats=$stats;
		}
		return $this->_staffsbasicstats;
	}

	function getMatchText($match_id)
	{
	    $db = Factory::getDbo();
	    $query = $db->getQuery(true);
	    $query
	           ->select('m.*')
	           ->select('t1.name t1name')
	           ->select('t2.name t2name')
	           ->from('#__joomleague_match AS m')
	           ->innerJoin('#__joomleague_project_team AS pt1 ON m.projectteam1_id=pt1.id')
	           ->innerJoin('#__joomleague_project_team AS pt2 ON m.projectteam2_id=pt2.id')
	           ->innerJoin('#__joomleague_team AS t1 ON pt1.team_id=t1.id')
	           ->innerJoin('#__joomleague_team AS t2 ON pt2.team_id=t2.id')
	           ->where('m.id='.$match_id)
	           ->where('m.published=1')
	           ->order('m.match_date,t1.short_name');
		$db->setQuery($query);
		return $db->loadObject();
	}
}
