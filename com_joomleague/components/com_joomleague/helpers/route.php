<?php
/**
 * @copyright	Copyright (C) 2006-2014 joomleague.at. All rights reserved.
 * @license		GNU/GPL,see LICENSE.php
 * Joomla! is free software. This version may have been modified pursuant
 * to the GNU General Public License,and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */

// Check to ensure this file is included in Joomla!
use Joomla\CMS\Factory;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;

defined('_JEXEC') or die;

// Component Helper
jimport('joomla.application.component.helper');

/**
 * Route helper for joomleague
 *
  * @since 2.5
 */
abstract class JoomleagueHelperRoute
{
  
    public static function getJoomleagueRoute($view='',$parameter = array(), $task='')
    {
        $params = ComponentHelper::getParams('com_joomleague');
        $app = Factory::getApplication();
        $params = array("option" => $this->$option,
            "view" => $view );
        
        
        
        foreach( $parameter as $key => $value )
        {
            $params[$key] = $value;
        }
        
        switch ( $task )
        {
            //    case 'person.edit':
            //    $params["layout"] = 'edit';
            //    $params["view"] = 'person';
            //	$params["id"] = $params['pid'];
            //    break;
            
        }
        
        $query = $this->buildQuery( $params );
        $link = Route::_( 'index.php?' . $query, false );
        
        return $link;
    }
    public static function joomleagueBuildRoute(&$query)
    {
        $app = Factory::getApplication();
        $segments = array();
        
        if (isset($query['view'])) {
            $segments[] = $query['view'];
            unset($query['view']);
        }
        if (isset($query['p'])) {
            $segments[] = $query['p'];
            unset($query['p']);
        }
        if (isset($query['cid'])) {
            $segments[] = $query['cid'];
            unset($query['cid']);
        }
        
        return $segments;
    }
    
    /**
     * Generic method to preprocess a URL
     *
     * @param   array  $query  An associative array of URL arguments
     *
     * @return  array  The URL arguments to use to assemble the subsequent URL.
     *
     * @since   3.3
     */
   
	public static function getTeamInfoRoute( $projectid, $teamid )
	{
		$params = array(	"option" => "com_joomleague",
				"view" => "teaminfo",
				"p" => $projectid,
				"tid" => $teamid );

		$query = JoomleagueHelperRoute::buildQuery( $params );
		$link = Route::_( 'index.php?' . $query, false );


		return $link;
	}

	public static function getProjectTeamInfoRoute( $projectid, $projectteamid, $task=null )
	{
		$params = array(	"option" => "com_joomleague",
					"view" => "teaminfo",
					"p" => $projectid,
					"ptid" => $projectteamid );
		if ( ! is_null( $task ) ) {
			unset($params["p"]);
			unset($params["ptid"]);
			$params["pid[]"] 	= $projectid;
			$params["cid[]"] 	= $projectteamid;
			$params["task"]		= $task;
			$params["view"] 	= 'projectteam';
			$params["hidemainmenu"] = '1';
			$query = JoomleagueHelperRoute::buildQuery( $params );
			$link = "administrator/index.php?" . $query;
		} else {
			$query = JoomleagueHelperRoute::buildQuery( $params );
		    $link = Route::_( 'index.php?' . $query, false );

		}
		return $link;
	}

	public static function getRivalsRoute( $projectid, $teamid )
	{
		$params = array(	"option" => "com_joomleague",
					"view" => "rivals",
					"p" => $projectid,
					"tid" => $teamid );

		$query = JoomleagueHelperRoute::buildQuery( $params );
		$link = Route::_( 'index.php?' . $query, false );


		return $link;
	}

	public static function getClubInfoRoute( $projectid, $clubid, $task=null )
	{
		$params = array(	"option" => "com_joomleague",
					"view" => "clubinfo",
					"p" => $projectid,
					"cid" => $clubid );

		if ( ! is_null( $task ) ) {
			unset($params["p"]);
			unset($params["cid"]);
			unset($params["view"]);
			$params["pid[]"] 		= $projectid;
			$params["id"]	 	= $clubid;
			$params["task"] 		= $task;
			$params["hidemainmenu"]	= '1';
			$query = JoomleagueHelperRoute::buildQuery( $params );
			$link = "administrator/index.php?" . $query;
		} else {
			$query = JoomleagueHelperRoute::buildQuery( $params );
		    $link = Route::_( 'index.php?' . $query, false );

		}
		return $link;
	}

	public static function getClubPlanRoute( $projectid, $clubid, $task=null )
	{
		$params = array(	"option" => "com_joomleague",
					"view" => "clubplan",
					"p" => $projectid,
					"cid" => $clubid );

		if ( ! is_null( $task ) ) { $params["task"] = $task; }

		$query = JoomleagueHelperRoute::buildQuery( $params );
		$link = Route::_( 'index.php?' . $query, false );
		
		return $link;
	}

	public static function getPlaygroundRoute( $projectid, $playgroundid )
	{
		$params = array(	"option" => "com_joomleague",
					"view" => "playground",
					"p" => $projectid,
					"pgid" => $playgroundid );

		$query = JoomleagueHelperRoute::buildQuery( $params );
		$link = Route::_( 'index.php?' . $query, false );


		return $link;
	}

  public static function getRankingAllTimeRoute( $leagueid, $points, $projectid)
	{
		$params = array(	"option" => "com_joomleague",
					"view" => "rankingalltime",
					"p" => $projectid,
					"l" => $leagueid,
					"points" => $points );
		$query = JoomleagueHelperRoute::buildQuery( $params );
		$link = Route::_( 'index.php?' . $query, false );

		return $link;			
	}
	/**
	 *
	 * @param int $projectid
	 * @param int $round
	 * @param int $from
	 * @param int $to
	 * @param int $type
	 */
	public static function getRankingRoute( $projectid, $round=0, $from=0, $to=0, $type=0, $division=0 )
	{
		$params = array(	"option" => "com_joomleague",
					"view" => "ranking",
					"p" => $projectid );

		$params["type"] = $type;
		$params["r"] = $round;
		$params["from"] = $from;
		$params["to"] = $to;
		$params["division"] = $division;
		$query = JoomleagueHelperRoute::buildQuery( $params );
		$link = Route::_( 'index.php?' . $query, false );


		return $link;
	}

	public static function getResultsRoute($projectid, $roundid=0, $divisionid=0, $mode=0, $order=0, $task=null)
	{
		$params = array(	'option' => 'com_joomleague',
							'view' => 'results',
							'p' => $projectid );

		if ( ! is_null( $task ) ) {
			unset($params["p"]);
			$params["view"] 	= 'matches';
			$params["pid[]"] 	= $projectid;
			$params["rid[]"] 	= $roundid;
			$params["task"] 	= $task;
			$params['division'] = $divisionid;
			$params["hidemainmenu"] = '1';
			$query = JoomleagueHelperRoute::buildQuery( $params );
			$link = "administrator/index.php?" . $query;
		} else {
			$params['r'] 		= $roundid;
			$params['division'] = $divisionid;
			$params['mode'] 	= $mode;
			$params['order'] 	= $order;
			$query = JoomleagueHelperRoute::buildQuery( $params );
		    $link = Route::_( 'index.php?' . $query, false );

		}

		return $link;
	}

	public static function getMatrixRoute( $projectid, $division=0, $round=0 )
	{
		$params = array(	"option" => "com_joomleague",
					"view" => "matrix",
					"p" => $projectid );

		$params["division"] = $division;
		$params["r"] = $round;

		$query = JoomleagueHelperRoute::buildQuery( $params );
		$link = Route::_( 'index.php?' . $query, false );


		return $link;
	}

	public static function getResultsRankingRoute( $projectid, $round=0, $division=0 )
	{
		$params = array(	"option" => "com_joomleague",
					"view" => "resultsranking",
					"p" => $projectid );

		if ( ! is_null( $round ) ) { $params["r"] = $round; }
		$params["division"] = $division;

		$query = JoomleagueHelperRoute::buildQuery( $params );
		$link = Route::_( 'index.php?' . $query, false );


		return $link;
	}

	public static function getResultsMatrixRoute( $projectid, $round=0, $division=0 )
	{
		$params = array(	"option" => "com_joomleague",
					"view" => "resultsmatrix",
					"p" => $projectid );

		if ( ! is_null( $round ) ) { $params["r"] = $round; }
		$params["division"] = $division;

		$query = JoomleagueHelperRoute::buildQuery( $params );
		$link = Route::_( 'index.php?' . $query, false );


		return $link;
	}

	public static function getRankingMatrixRoute( $projectid, $round=0, $division=0 )
	{
		$params = array(	"option" => "com_joomleague",
					"view" => "rankingmatrix",
					"p" => $projectid );

		if ( ! is_null( $round ) ) { $params["r"] = $round; }
		$params["division"] = $division;

		$query = JoomleagueHelperRoute::buildQuery( $params );
		$link = Route::_( 'index.php?' . $query, false );


		return $link;
	}

	public static function getResultsRankingMatrixRoute( $projectid, $round=0, $division=0 )
	{
		$params = array(	"option" => "com_joomleague",
					"view" => "resultsrankingmatrix",
					"p" => $projectid );

		if ( ! is_null( $round ) ) { $params["r"] = $round; }
		$params["division"] = $division;

		$query = JoomleagueHelperRoute::buildQuery( $params );
		$link = Route::_( 'index.php?' . $query, false );


		return $link;
	}

	public static function getTeamPlanRoute( $projectid, $teamid, $division=0, $mode=null )
	{
		$params = array(	"option" => "com_joomleague",
					"view" => "teamplan",
					"p" => $projectid,
					"tid" => $teamid,
					"division" => $division );

		if ( ! is_null( $mode ) ) { $params["mode"] = $mode; }

		$query = JoomleagueHelperRoute::buildQuery( $params );
		$link = Route::_( 'index.php?' . $query, false );
		
		return $link;
	}

	public static function getMatchReportRoute( $projectid, $matchid = null )
	{
		$params = array(	"option" => "com_joomleague",
					"view" => "matchreport",
					"p" => $projectid );

		if ( ! is_null( $matchid ) ) { $params["mid"] = $matchid; }

		$query = JoomleagueHelperRoute::buildQuery( $params );
		$link = Route::_( 'index.php?' . $query, false );


		return $link;
	}

	/**
	 * return links to a team player
	 * @param int projectid
	 * @param int teamid
	 * @param int personid
	 * @param string task
	 * @return url
	 */
	public static function getPlayerRoute($projectid, $teamid, $personid, $task=null)
	{
		$params = array(	"option" => "com_joomleague",
					"view" => "player",
					"p" => $projectid,
					"tid" => $teamid,
					"pid" => $personid );

		if(!is_null($task)) {
			unset($params["p"]);
			unset($params["tid"]);
			$params["pid[]"]		= $projectid;
			$params["cid[]"]		= $personid;
			$params["view"]			= 'person';
			$params["task"]			= $task;
			$params["hidemainmenu"]	= 1;
			$query = JoomleagueHelperRoute::buildQuery( $params );
			$link = "administrator/index.php?" . $query;
		} else {
			$query = JoomleagueHelperRoute::buildQuery( $params );
		    $link = Route::_( 'index.php?' . $query, false );

		}

		return $link;
	}

	/**
	 * return links to a team staff
	 * @param int projectid
	 * @param int teamid
	 * @param int personid
	 * @return url
	 */
	public static function getStaffRoute( $projectid, $teamid, $personid, $task=null )
	{
		$params = array(	"option" => "com_joomleague",
					"view" => "staff",
					"p" => $projectid,
					"tid" => $teamid,
					"pid" => $personid );

		if(!is_null($task)) {
			unset($params["p"]);
			unset($params["tid"]);
			unset($params["pid"]);
			$params["pid[]"] 		= $projectid;
			$params["team"] 		= $teamid;
			$params["cid[]"] 		= $personid;
			$params["view"] 		= 'teamstaff';
			$params["task"] 		= $task;
			$params["hidemainmenu"] = 1;
			$query = JoomleagueHelperRoute::buildQuery( $params );
			$link = "administrator/index.php?" . $query;
		} else {
			$query = JoomleagueHelperRoute::buildQuery( $params );
		    $link = Route::_( 'index.php?' . $query, false );

		}

		return $link;
	}

	/**
	 * returns url to a person
	 * @param int project id
	 * @param int person id
	 * @param int Type: 1 for player, 2 for staff, 3 for referee
	 * @deprecated since 1.5
	 * @return url
	 */
	public static function getPersonRoute( $projectid, $personid, $showType )
	{
		$params = array(	"option" => "com_joomleague",
					"view" => "person",
					"p" => $projectid,
					"pid" => $personid,
					"pt" => $showType );

		$query = JoomleagueHelperRoute::buildQuery( $params );
		$link = Route::_( 'index.php?' . $query, false );


		return $link;
	}

	public static function getPlayersRoute( $projectid, $teamid, $task=null, $divisionid=0, $project_team_id='')
	{
		$params = array(	"option" => "com_joomleague",
					"view" => "roster",
					"p" => $projectid,
					"tid" => $teamid,
					"division" => $divisionid,
					 );

		if(!is_null($task)) {
			unset($params["p"]);
			unset($params["tid"]);
			unset($params["division"]);
			$params["pid[]"] 			= $projectid;
			$params["team_id"] 			= $teamid;
			$params["project_team_id"] 	= $project_team_id;
			$params["view"] 			= 'projectteams';
			$params["task"] 			= $task;
			$params["hidemainmenu"] 	= 1;
			$query = JoomleagueHelperRoute::buildQuery( $params );
			$link = "administrator/index.php?" . $query;
		} else {
			$query = JoomleagueHelperRoute::buildQuery( $params );
		    $link = Route::_( 'index.php?' . $query, false );

		}

		return $link;
	}
	public static function getPlayersRouteAllTime( $projectid, $teamid, $task=null )
	{
		$params = array(	"option" => "com_joomleague",
					"view" => "rosteralltime",
					"p" => $projectid,
					"tid" => $teamid );

		$query = JoomleagueHelperRoute::buildQuery( $params );
		$link = Route::_( 'index.php?' . $query, false );

		return $link;
	}


	public static function getDivisionsRoute( $projectid, $divisionid, $task=null )
	{
		$params = array(	"option" => "com_joomleague",
					"view" => "treeone",
					"p" => $projectid,
					"division" => $divisionid );

		$query = JoomleagueHelperRoute::buildQuery( $params );
		$link = Route::_( 'index.php?' . $query, false );


		return $link;
	}

	public static function getFavPlayersRoute( $projectid )
	{
		$params = array(	"option" => "com_joomleague",
					"view" => "players",
					"task" => "favplayers",
					"p" => $projectid );

		$query = JoomleagueHelperRoute::buildQuery( $params );
		$link = Route::_( 'index.php?' . $query, false );


		return $link;
	}

	public static function getRefereeRoute( $projectid, $personid, $task=null )
	{
		$params = array(	"option" => "com_joomleague",
					"view" => "referee",
					"p" => $projectid,
					"pid" => $personid );
		if ( ! is_null( $task ) ) {
			unset($params['pid']);
			unset($params['p']);
			$params["pid[]"]		= $projectid;
			$params["cid[]"]		= $personid;
			$params["task"]			= $task;
			$params["view"] 		= 'projectreferee';
			$params["hidemainmenu"]	= 1;
			$query = JoomleagueHelperRoute::buildQuery( $params );
			$link = "administrator/index.php?" . $query;
		} else {
			$query = JoomleagueHelperRoute::buildQuery( $params );
		    $link = Route::_( 'index.php?' . $query, false );

		}
		return $link;
	}

	public static function getRefereesRoute( $projectid )
	{
		$params = array(	"option" => "com_joomleague",
					"view" => "referees",
					"p" => $projectid );

		$query = JoomleagueHelperRoute::buildQuery( $params );
		$link = Route::_( 'index.php?' . $query, false );


		return $link;
	}

	public static function getEventsRankingRoute( $projectid, $divisionid=0, $teamid=0, $eventid=0, $matchid=0)
	{
		$params = array(	"option" => "com_joomleague",
					"view" => "eventsranking",
					"p" => $projectid );

		$params["division"] = $divisionid;
		$params["tid"] = $teamid;
		$params["evid"] = $eventid;
		$params["mid"] = $matchid;

		$query = JoomleagueHelperRoute::buildQuery( $params );
		$link = Route::_( 'index.php?' . $query, false );


		return $link;
	}

	public static function getCurveRoute($projectid, $teamid1=0, $teamid2=0, $division=0)
	{
		$params = array(	"option" => "com_joomleague",
					"view" => "curve",
					"p" => $projectid );

		$params["tid1"] = $teamid1;
		$params["tid2"] = $teamid2;
		$params["division"] = $division;

		$query = JoomleagueHelperRoute::buildQuery( $params );
		$link = Route::_( 'index.php?' . $query, false );


		return $link;
	}

	public static function getStatsChartDataRoute( $projectid, $division=0 )
	{
		$params = array(	"option" => "com_joomleague",
					"view" => "stats",
					"layout" => "chartdata",
					"p" => $projectid );

		$params["division"] = $division;

		$query = JoomleagueHelperRoute::buildQuery( $params );
		$link = Route::_( 'index.php?' . $query, false );


		return $link;
	}

	public static function getTeamStatsChartDataRoute( $projectid, $teamid )
	{
		$params = array(	"option" => "com_joomleague",
					"view" => "teamstats",
					"layout" => "chartdata",
					"p" => $projectid,
					"tid" => $teamid );

		$query = JoomleagueHelperRoute::buildQuery( $params );
		$link = Route::_( 'index.php?' . $query, false );


		return $link;
	}

	public static function getStatsRoute( $projectid, $division = 0 )
	{
		$params = array(	"option" => "com_joomleague",
					"view" => "stats",
					"p" => $projectid );

		$params["division"] = $division;

		$query = JoomleagueHelperRoute::buildQuery( $params );
		$link = Route::_( 'index.php?' . $query, false );


		return $link;
	}

	public static function getBracketsRoute( $projectid )
	{
		$params = array(	"option" => "com_joomleague",
					"view" => "treetonode",
					"p" => $projectid );

		$query = JoomleagueHelperRoute::buildQuery( $params );
		$link = Route::_( 'index.php?' . $query, false );


		return $link;
	}

	public static function getStatsRankingRoute( $projectid, $divisionid = null, $teamid = null, $statid = 0, $order = null )
	{
		$params = array(	"option" => "com_joomleague",
					"view" => "statsranking",
					"p" => $projectid );

		if ( isset( $divisionid ) ) { $params["division"] = $divisionid; }
		if ( isset( $teamid ) ) { $params["tid"] = $teamid; }
		if ($statid) { $params['sid'] = $statid; }
		if (strcasecmp($order, 'asc') === 0 || strcasecmp($order, 'desc') === 0) { $params['order'] = strtolower($order); }

		$query = JoomleagueHelperRoute::buildQuery( $params );
		$link = Route::_( 'index.php?' . $query, false );


		return $link;
	}

	public static function getClubsRoute( $projectid, $divisionid = null )
	{
		$params = array(	"option" => "com_joomleague",
					"view" => "clubs",
					"p" => $projectid );

		if ( isset( $divisionid ) ) { $params["division"] = $divisionid; }

		$query = JoomleagueHelperRoute::buildQuery( $params );
		$link = Route::_( 'index.php?' . $query, false );


		return $link;
	}

	public static function getTeamsRoute( $projectid, $divisionid = null )
	{
		$params = array(	"option" => "com_joomleague",
					"view" => "teams",
					"p" => $projectid );

		if ( isset( $divisionid ) ) { $params["division"] = $divisionid; }

		$query = JoomleagueHelperRoute::buildQuery( $params );
		$link = Route::_( 'index.php?' . $query, false );


		return $link;
	}

	public static function getTeamStatsRoute( $projectid, $teamid )
	{
		$params = array(	"option" => "com_joomleague",
					"view" => "teamstats",
					"p" => $projectid,
					"tid" => $teamid );

		$query = JoomleagueHelperRoute::buildQuery( $params );
		$link = Route::_( 'index.php?' . $query, false );
		
		return $link;
	}

	public static function getTeamStaffRoute( $projectid, $playerid, $showType )
	{
		$params = array(	"option" => "com_joomleague",
					"view" => "person",
					"p" => $projectid,
					"pid" => $playerid,
					"pt" => $showType );

		$query = JoomleagueHelperRoute::buildQuery( $params );
		$link = Route::_( 'index.php?' . $query, false );


		return $link;
	}

	public static function getNextMatchRoute( $projectid, $matchid )
	{
		$params = array(	"option" => "com_joomleague",
					"view" => "nextmatch",
					"p" => $projectid,
					"mid" => $matchid );

		$query = JoomleagueHelperRoute::buildQuery( $params );
		$link = Route::_( 'index.php?' . $query, false );


		return $link;
	}

	public static function getContactRoute( $contactid )
	{
		$params = array(	"option" => "com_contact",
					"view" => "contact",
					"id" => $contactid );

		$query = JoomleagueHelperRoute::buildQuery( $params );
		$link = Route::_( 'index.php?' . $query, false );


		return $link;
	}

	public static function getUserProfileRouteCBE( $u_id, $p_id, $pl_id )
	{
		// Old Route to Community Builder User Page with support for CBE-JoomLeague Tab
		// index.php?option=com_cbe&view=userProfile&user=JOOMLA_USER_ID&jlp=PROJECT_ID&jlpid=JOOMLEAGUE_PLAYER_ID
		$params = array(	"option" => "com_cbe",
					"view" => "userProfile",
					"user" => $u_id,
					"jlp" => $p_id,
					"jlpid" => $pl_id );

		$query = JoomleagueHelperRoute::buildQuery( $params );
		$link = Route::_( 'index.php?' . $query, false );


		return $link;
	}

	public static function getUserProfileRoute( $userid )
	{
		$params = array(	"option" => "com_comprofiler",
					"task" => "userProfile",
					"user" => $userid );

		$query = JoomleagueHelperRoute::buildQuery( $params );
		$link = Route::_( 'index.php?' . $query, false );


		return $link;
	}

	public static function getIcalRoute( $projectid, $teamid=null, $pgid=null )
	{
		$params = array(	"option" => "com_joomleague",
					"view" => "ical",
					"p" => $projectid );

		if ( !is_null( $pgid ) ) { $params["pgid"] = $pgid; }
		if ( !is_null( $teamid ) ) { $params["teamid"] = $teamid; }

		$query = JoomleagueHelperRoute::buildQuery( $params );
		$link = Route::_( 'index.php?' . $query, false );
		
		return $link;
	}

	public static function buildQuery($parts)
	{
		if ($item = JoomleagueHelperRoute::_findItem($parts))
		{
			$parts['Itemid'] = $item->id;
		}
		else {
			$params = ComponentHelper::getParams('com_joomleague');
			if ($params->get('default_itemid')) {
				$parts['Itemid'] = intval($params->get('default_itemid'));
			}
		}

		return Uri::buildQuery( $parts );
	}

	/**
	 * Determines the Itemid
	 *
	 * searches if a menuitem for this item exists
	 * if not the first match will be returned
	 *
	 * @param array The id and view
	 *
	 * @return int Itemid
	 */
	public static function _findItem($query)
	{
	    $app = Factory::getApplication();
		$component = ComponentHelper::getComponent('com_joomleague');
	    $menus		= Factory::getApplication()->getMenu();
		$items	= $menus->getItems('component', $component->id);
		$user 	=  Factory::getUser();
		$access = (int)$user->get('aid');
		{
		if (is_array($items))
		{
		    // If only the option and itemid are specified in the query, return that item.
		    if (!isset($query['view']) && !isset($query['id']) && !isset($query['catid']) && isset($query['Itemid'])) {
		        $itemid = (int) $query['Itemid'];
		    }
		    
		if (!$items) {
			foreach($items as $item)
			{
				if ((@$item->query['view'] == $query['view']) && ($item->published == 1) && ($item->access <= $access)) {

					switch ($query['view'])
					{
						case 'teaminfo':
						case 'roster':
						case 'teamplan':
						case 'teamstats':
							if ((int)@$item->query['p'] == (int) $query['p'] && (int)@$item->query['tid'] == (int) $query['tid']) {
								return $item;
							}
							break;
						case 'clubinfo':
						case 'clubplan':
							if ((int)@$item->query['p'] == (int) $query['p'] && (int)@$item->query['cid'] == (int) $query['cid']) {
								return $item;
							}
							break;
						case 'nextmatch':
							if ((int)@$item->query['p'] == (int) $query['p']
							&& (int)@$item->query['tid'] == (int) $query['tid']
							&& (int)@$item->query['mid'] == (int) $query['mid']) {
								return $item;
							}
							break;
						case 'playground':
							if ((int)@$item->query['p'] == (int) $query['p'] && (int)@$item->query['pgid'] == (int) $query['pgid']) {
								return $item;
							}
							break;
						case 'ranking':
						case 'results':
						case 'resultsranking':
						case 'matrix':
						case 'resultsmatrix':
						case 'stats':
							if ((int)@$item->query['p'] == (int) $query['p']) {
								return $item;
							}
							break;
						case 'statsranking':
							if ((int)@$item->query['p'] == (int) $query['p']) {
								return $item;
							}
							break;
						case 'player':
						case 'staff':
							if (    (int)@$item->query['p'] == (int) $query['p']
							&& (int)@$item->query['tid'] == (int) $query['tid']
							&& (int)@$item->query['pid'] == (int) $query['pid']) {
								return $item;
							}
							break;
						case 'referee':
							if (    (int)@$item->query['p'] == (int) $query['p']
							&& (int)@$item->query['pid'] == (int) $query['pid']) {
								return $item;
							}
							break;
						case 'tree':
							if ((int)@$item->query['p'] == (int) $query['p'] && (int)@$item->query['division'] == (int) $query['division']) {
								return $item;
							}
							break;
					}
				}
			}
		}

		return false;
	}
		}
	}}
