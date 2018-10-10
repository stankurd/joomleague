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
use Joomla\CMS\Factory;
use Joomla\CMS\Pagination\Pagination;
use Joomla\CMS\Language\Text;
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die;

require_once JLG_PATH_SITE.'/models/project.php';

/**
 * Model-Eventsranking
 */
class JoomleagueModelEventsRanking extends JoomleagueModelProject
{
	var $projectid=0;
	var $divisionid = 0;
	var $teamid = 0;
	var $eventid=0;
	var $matchid=0;
	var $limit=20;
	var $limitstart=0;

	public function __construct()
	{
		parent::__construct();
		
		$app = Factory::getApplication();
		$input = $app->input;
		
		$this->projectid=$input->get('p',0, 'INT');
		$this->divisionid = $input->get('division', 0, 'INT');
		$this->teamid = $input->get('tid', 0, 'INT');
		$this->setEventid($input->get('evid', '0', 'String'));
		$this->matchid = $input->get('mid',0, 'INT');
		$config = $this->getTemplateConfig($this->getName());
		$defaultLimit = $this->eventid != 0 ? $config['max_events'] : $config['count_events'];
		$this->limit=$input->get('limit',$defaultLimit, 'INT');
		$this->limitstart=$input->get('limitstart',0, 'INT');
		$this->setOrder($input->get('order','desc', 'String'));
	}

	function getDivision()
	{
		$division = null;
		if ($this->divisionid != 0)
		{
			$division = parent::getDivision($this->divisionid);
		}
		return $division;
	}

	function getTeamId()
	{
		return $this->teamid;
	}

	function getLimit()
	{
		return $this->limit;
	}

	function getLimitStart()
	{
		return $this->limitstart;
	}

	/**
	 * Method to get a pagination object for the events
	 *
	 * @access public
	 * @return integer
	 */
	function getPagination()
	{
		// Lets load the content if it doesn't already exist
		if (empty($this->_pagination))
		{
			jimport('joomla.html.pagination');
			$this->_pagination = new Pagination( $this->getTotal(), $this->getLimitStart(), $this->getLimit() );
		}
		return $this->_pagination;
	}

	function setEventid($evid)
	{
		// Allow for multiple statistics IDs, arranged in a single parameters (sid) as string
		// with "|" as separator
		$sidarr = explode("|", $evid);
		$this->eventid = array();
		foreach ($sidarr as $sid)
		{
			$this->eventid[] = (int)$sid;	// The cast gets rid of the slug
		}
		// In case 0 was (part of) the evid string, make sure all eventtypes are loaded)
		if (in_array(0, $this->eventid))
		{
			$this->eventid = 0;
		}
	}

	/**
	 * set order (asc or desc)
	 * @param string $order
	 * @return string order
	 */
	function setOrder($order)
	{
		if (strcasecmp($order, 'asc') === 0 || strcasecmp($order, 'desc') === 0) {
			$this->order = strtolower($order);
		}
		return $this->order;
	}

	/**
	 * @see JoomleagueModelProject::getEventTypes()
	 */
	function getEventTypes()
	{
	    $app = Factory::getApplication();
	    $db = Factory::getDbo();
	    $query = $db->getQuery(true);
	    $query
	           ->select('et.id as etid,me.event_type_id as id,et.*')
	           ->from('#__joomleague_eventtype as et')
	           ->innerJoin('#__joomleague_match_event as me ON et.id=me.event_type_id')
	           ->innerJoin('#__joomleague_match as m ON m.id=me.match_id')
	           ->innerJoin('#__joomleague_round as r ON m.round_id=r.id');
	           
	           if ($this->projectid > 0)
	           {
	               $query->where('r.project_id = ' .$this->projectid);
	           }
	           if ($this->eventid != 0)
	           {
	               if ($this->projectid > 0)
	               {
	                   $query .= " AND";
	               }
	               else
	               {
	                   $query .= " WHERE";
	               }
	               $query .= " me.event_type_id IN (".implode(",", $this->eventid).")";
	           }
	           $query .= " ORDER BY et.ordering";
		$db->setQuery($query);
		$result=$db->loadObjectList('etid');
		return $result;
	}

	function getTotal()
	{
	    $app = Factory::getApplication();
	    $db = Factory::getDbo();
	    $query = $db->getQuery(true);
		if (empty($this->_total))
		{
			$eventids = is_array($this->eventid) ? $this->eventid : array($this->eventid); 

			// Make sure the same restrictions are used here as in statistics/basic.php in getPlayersRanking()
			$query
			     ->select('COUNT(DISTINCT(teamplayer_id)) as count_player')
			     ->from('#__joomleague_match_event AS me')
			     ->innerJoin('#__joomleague_team_player AS tp ON tp.id=me.teamplayer_id')
			     ->innerJoin('#__joomleague_person pl ON tp.person_id=pl.id')
			     ->innerJoin('#__joomleague_project_team AS pt ON pt.id=tp.projectteam_id')
			     ->innerJoin('#__joomleague_team AS t ON t.id=pt.team_id')
			     ->where('me.event_type_id IN('.implode(',' ,$eventids).')')
			     ->where('pl.published = 1');
			     if ($this->projectid > 0)
			     {
			         $query->where('pt.project_id='.$this->projectid);
			     }
			     if ($this->divisionid > 0)
			     {
			         $query->where('pt.division_id='.$this->divisionid);
			     }
			     if ($this->teamid > 0)
			     {
			         $query->where('pt.team_id = '.$this->teamid);
			     }
			     if ($this->matchid > 0)
			     {
			         $query->where('me.match_id='.$this->matchid);
			     }
			     try{
			$db->setQuery($query);
			$this->_total = $db->loadResult();			    
			}
			catch (RuntimeException $e)
			{
			    $app->enqueueMessage(Text::_(__METHOD__.' '.' '.$e->getMessage()), 'error');
			    return false;
			}
			
		}
		return $this->_total;
			
	}

	function _getEventsRanking($eventtype_id, $order='desc', $limit=20, $limitstart=0)
	{
	    $app = Factory::getApplication();
	    $db = Factory::getDbo();
	    $query = $db->getQuery(true);
	    $query
	           ->select('SUM(me.event_sum) as p')
	           ->select('pl.firstname AS fname')
	           ->select('pl.nickname AS nname')
	           ->select('pl.lastname AS lname')
	           ->select('pl.lastname AS lname')
	           ->select('pl.country')
	           ->select('pl.id AS pid')
	           ->select('pl.picture')
	           ->select('tp.picture AS teamplayerpic')
	           ->select('t.id AS tid')
	           ->select('t.name AS tname')
	           ->from('#__joomleague_match_event AS me')
	           ->innerJoin('#__joomleague_team_player AS tp ON tp.id=me.teamplayer_id')
	           ->innerJoin('#__joomleague_person pl ON tp.person_id=pl.id')
	           ->innerJoin('#__joomleague_project_team AS pt ON pt.id=tp.projectteam_id')
	           ->innerJoin('#__joomleague_team AS t ON t.id=pt.team_id')
	           ->where('me.event_type_id='.$eventtype_id)
	           ->where('pl.published = 1');
	           if ($this->projectid > 0)
	           {
	               $query->where('pt.project_id='.$this->projectid);
	           }
	           if ($this->divisionid > 0)
	           {
	               $query->where('pt.division_id='.$this->divisionid);
	           }
	           if ($this->teamid > 0)
	           {
	               $query->where('pt.team_id='.$this->teamid);
	           }
	           if ($this->matchid > 0)
	           {
	               $query->where('me.match_id='.$this->matchid);
	           }
	           $query->group('me.teamplayer_id') 
	           ->order('p ' .$order. ', me.match_id');
		$db->setQuery($query, $this->getlimitStart(), $this->getlimit());
		$rows=$db->loadObjectList();

		// get ranks
		$previousval = 0;
		$currentrank = 1 + $limitstart;
		foreach ($rows as $k => $row) 
		{
			$rows[$k]->rank = ($row->p == $previousval) ? $currentrank : $k + 1 + $limitstart;
			$previousval = $row->p;
			$currentrank = $row->rank;
		}
		return $rows;
	}

	function getEventRankings($limit, $limitstart=0, $order=null)
	{
		$order = ($order ? $order : $this->order);
		$eventtypes=$this->getEventTypes();
		if (array_keys($eventtypes))
		{
			foreach (array_keys($eventtypes) AS $eventkey)
			{
				$eventrankings[$eventkey]=$this->_getEventsRanking($eventkey, $order, $limit, $limitstart);
			}
		}

		if (!isset ($eventrankings))
		{
			return null;
		}
		return $eventrankings;
	}
}
