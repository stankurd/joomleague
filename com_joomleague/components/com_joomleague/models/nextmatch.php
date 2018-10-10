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

defined('_JEXEC') or die;

require_once JPATH_COMPONENT.'/helpers/ranking.php';
require_once JLG_PATH_SITE.'/models/project.php';

/**
 * Model-Nextmatch
 */
class JoomleagueModelNextMatch extends JoomleagueModelProject
{
	var $project = null;
	var $matchid = 0;
	var $teamid = 0;
	var $projectid = 0;
	var $divisionid = 0;
	var $ranking = null;
	var $teams = null;

	/**
	 * caching match data
	 * @var object
	 */
	var $_match = null;

	public function __construct()
	{
		parent::__construct();
		
		$app = Factory::getApplication();
		$input = $app->input;
		
		$this->projectid = $input->getInt("p",0);
		$this->divisionid = $input->getInt("division",0);
		$this->teamid = $input->getInt("tid",0);
		$this->matchid = $input->getInt("mid",0);
		$this->getSpecifiedMatch($this->projectid, $this->divisionid, $this->teamid, $this->matchid);
	}

	/**
	 * 
	 */
	function getSpecifiedMatch($projectId, $divisionId, $teamId, $matchId)
	{
		if (!$this->_match)
		{
		    $db = Factory::getDbo();
		    $query = $db->getQuery(true);
			$config = $this->getTemplateConfig($this->getName());
			$expiry_time = $config ? $config['expiry_time'] : 0;
			$query
			     ->select('m.*')
			     ->select('DATE_FORMAT(m.time_present, "%H:%i") time_present')
			     ->select('pt1.project_id')
			     ->select('r.roundcode')
			     ->select('p.timezone')
			     ->select('pt1.division_id')
			     ->from('#__joomleague_match AS m ')
			     ->innerJoin('#__joomleague_round AS r ON r.id = m.round_id')
			     ->innerJoin('#__joomleague_project_team AS pt1 ON pt1.id = m.projectteam1_id')
			     ->innerJoin('#__joomleague_project_team AS pt2 ON pt2.id = m.projectteam2_id')
			     ->innerJoin('#__joomleague_team AS t1 ON t1.id = pt1.team_id')
			     ->innerJoin('#__joomleague_team AS t2 ON t2.id = pt2.team_id')
			     ->innerJoin('#__joomleague_project AS p ON p.id = pt1.project_id')
			     ->where('DATE_ADD(m.match_date, INTERVAL '.$db->Quote($expiry_time).' MINUTE) >= NOW()')
			     ->where('m.cancel=0')
			     ->where('m.published = 1');
			if ($matchId)
			{
				$query->where('m.id = ' . $db->Quote($matchId));
			}
			else
			{
				$query->where('(team1_result is null  OR  team2_result is null)');
				if ($teamId)
				{
					$query->where('t1.id = '. $db->Quote($teamId).' OR ' . 't2.id = '. $db->Quote($teamId));
				}
				else
				{
					$query->where('(m.projectteam1_id > 0  OR  m.projectteam2_id > 0)');
				}
			}
			if ($projectId)
			{
				$query->where('p.id = '.$db->Quote($projectId));
			}
			if ($divisionId)
			{
				$query->where('pt1.division_id = '.$db->Quote($divisionId));
			}
			$query->order('m.match_date');
			$db->setQuery($query, 0, 1);
			$this->_match = $db->loadObject();
			if($this->_match)
			{
				JoomleagueHelper::convertMatchDateToTimezone($this->_match);
				$this->projectid = $this->_match->project_id;
				$this->matchid = $this->_match->id;
			}
		}
		
		return $this->_match;
	}

	/**
	 * get match info
	 * @return object
	 */
	function getMatch()
	{
		if (empty($this->_match))
		{
		    $db = Factory::getDbo();
		    $query = $db->getQuery(true);
		    $query
		          ->select('m.*')
		          ->select('DATE_FORMAT(m.time_present, "%H:%i") time_present')
		          ->select('pt1.project_id')
		          ->select('r.roundcode')
		          ->select('p.timezone')
		          ->from('#__joomleague_match AS m')
		          ->innerJoin('#__joomleague_project_team AS pt1 ON pt1.id = m.projectteam1_id')
		          ->innerJoin('#__joomleague_round AS r ON r.id = m.round_id')
		          ->innerJoin('#__joomleague_project AS p ON p.id = r.project_id')
		          ->where(' m.id = '. $db->Quote($this->matchid))
		          ->where('m.published = 1');
			$db->setQuery($query, 0, 1);
			$this->_match = $db->loadObject();
			if ($this->_match)
			{
				JoomleagueHelper::convertMatchDateToTimezone($this->_match);
			}
		}
		
		return $this->_match;
	}

	/**
	 * get match teams details
	 * @return array
	 */
	function getMatchTeams()
	{
		if (empty($this->teams))
		{
			$this->teams = array();

			$match = $this->getMatch();
			if ( is_null ( $match ) )
			{
				return null;
			}

			$team1 = $this->getTeaminfo($match->projectteam1_id);
			$team2 = $this->getTeaminfo($match->projectteam2_id);
			$this->teams[] = $team1;
			$this->teams[] = $team2;
		}
		return $this->teams;
	}

	function getReferees()
	{
		$match = $this->getMatch();
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		$query
		      ->select('p.firstname')
		      ->select('p.nickname')
		      ->select('p.lastname')
		      ->select('p.country')
		      ->select('pos.name AS position_name')
		      ->select('p.id as person_id')
		      ->from('#__joomleague_match_referee AS mr')
		      ->leftJoin('#__joomleague_project_referee AS pref ON mr.project_referee_id=pref.id')
		      ->innerJoin('#__joomleague_person AS p ON p.id = pref.person_id')
		      ->innerJoin('#__joomleague_project_position ppos ON ppos.id = mr.project_position_id')
		      ->innerJoin('#__joomleague_position AS pos ON pos.id = ppos.position_id')
		      ->where('mr.match_id = '. $db->Quote($match->id))
		      ->where('p.published = 1');
		$db->setQuery($query);
		return $db->loadObjectList();
	}

	function _getRanking()
	{
		if (empty($this->ranking))
		{
			$project = $this->getProject();
			$division = $this->divisionid;
			$ranking = JLGRanking::getInstance($project);
			$ranking->setProjectId( $project->id );
			$this->ranking = $ranking->getRanking(0, $this->getCurrentRound(), $division);
		}
		return $this->ranking;
	}

	function getHomeRanked()
	{
		$match = $this->getMatch();
		$rankings = $this->_getRanking();
		foreach ($rankings as $ptid => $team)
		{
			if ($ptid == $match->projectteam1_id) {
				return $team;
			}
		}
		return false;
	}

	function getAwayRanked()
	{
		$match = $this->getMatch();
		$rankings = $this->_getRanking();

		foreach ($rankings as $ptid => $team)
		{
			if ($ptid == $match->projectteam2_id) {
				return $team;
			}
		}
		return false;
	}

	function _getHighestHomeWin($teamid)
	{
		$match = $this->getMatch();
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		$query
		      ->select('t1.name AS hometeam')
		      ->select('t2.name AS awayteam')
		      ->select('team1_result AS homegoals')
		      ->select('team2_result AS awaygoals')
		      ->select('pt1.project_id AS pid')
		      ->select('m.id AS mid')
		      ->from('#__joomleague_match as m')
		      ->innerJoin('#__joomleague_project_team pt1 ON pt1.id = m.projectteam1_id')
		      ->innerJoin('#__joomleague_team t1 ON t1.id = pt1.team_id')
		      ->innerJoin('#__joomleague_project_team pt2 ON pt2.id = m.projectteam2_id')
		      ->innerJoin('#__joomleague_team t2 ON t2.id = pt2.team_id')
		      ->where('pt1.project_id = ' . $db->Quote($match->project_id))
		      ->where('m.published = 1')
		      ->where('m.alt_decision = 0')
		      ->where('t1.id = '. $db->Quote($teamid))
		      ->where('(team1_result - team2_result > 0)')
		      ->order('(team1_result - team2_result) DESC');
		$db->setQuery($query, 0, 1);
		return $db->loadObject();
	}

	function getHomeHighestHomeWin( )
	{
		$teams = $this->getMatchTeams();
		if ( is_null ( $teams ) )
		{
			return null;
		}
		return $this->_getHighestHomeWin( $teams[0]->team_id );
	}

	function getAwayHighestHomeWin( )
	{
		$teams = $this->getMatchTeams();
		if ( is_null ( $teams ) )
		{
			return null;
		}
		return $this->_getHighestHomeWin( $teams[1]->team_id );
	}

	function _getHighestHomeDef( $teamid )
	{
		$match = $this->getMatch();
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		$query
		      ->select('t1.name AS hometeam')
		      ->select('t2.name AS awayteam')
		      ->select('team1_result AS homegoals')
		      ->select('team2_result AS awaygoals')
		      ->select('pt1.project_id AS pid')
		      ->select('m.id AS mid')
		      ->from('#__joomleague_match as m')
		      ->innerJoin('#__joomleague_project_team pt1 ON pt1.id = m.projectteam1_id')
		      ->innerJoin('#__joomleague_team t1 ON t1.id = pt1.team_id')
		      ->innerJoin('#__joomleague_project_team pt2 ON pt2.id = m.projectteam2_id')
		      ->innerJoin('#__joomleague_team t2 ON t2.id = pt2.team_id')
		      ->where('pt1.project_id = ' . $db->Quote($match->project_id))
		      ->where('m.published = 1')
		      ->where('m.alt_decision = 0')
		      ->where('t1.id = '. $db->Quote($teamid))
		      ->where('(team1_result - team2_result < 0)')
		      ->order('(team1_result - team2_result) ASC');
		$db->setQuery($query, 0, 1);
		return $db->loadObject();
	}

	function getHomeHighestHomeDef()
	{
		$teams = $this->getMatchTeams();
		if ( is_null ( $teams ) )
		{
			return null;
		}
		return $this->_getHighestHomeDef( $teams[0]->team_id );
	}

	function getAwayHighestHomeDef()
	{
		$teams = $this->getMatchTeams();
		if ( is_null ( $teams ) )
		{
			return null;
		}
		return $this->_getHighestHomeDef( $teams[1]->team_id );
	}

	function _getHighestAwayWin( $teamid )
	{
		$match = $this->getMatch();
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		$query
		      ->select('t1.name AS hometeam')
		      ->select('t2.name AS awayteam')
		      ->select('team1_result AS homegoals')
		      ->select('team2_result AS awaygoals')
		      ->select('pt1.project_id AS pid')
		      ->select('m.id AS mid')
		      ->from('#__joomleague_match as m')
		      ->innerJoin('#__joomleague_project_team pt1 ON pt1.id = m.projectteam1_id')
		      ->innerJoin('#__joomleague_team t1 ON t1.id = pt1.team_id')
		      ->innerJoin('#__joomleague_project_team pt2 ON pt2.id = m.projectteam2_id')
		      ->innerJoin('#__joomleague_team t2 ON t2.id = pt2.team_id')
		      ->where('pt1.project_id = ' . $db->Quote($match->project_id))
		      ->where('m.published = 1')
		      ->where('m.alt_decision = 0')
		      ->where('t2.id = '. $db->Quote($teamid))
		      ->where('(team2_result - team1_result > 0)')
		      ->order('(team2_result - team1_result) DESC');
		$db->setQuery($query, 0, 1);
		return $db->loadObject();
	}

	function getHomeHighestAwayWin( )
	{
		$teams = $this->getMatchTeams();
		if ( is_null ( $teams ) )
		{
			return null;
		}
		return $this->_getHighestAwayWin( $teams[0]->team_id );
	}

	function getAwayHighestAwayWin( )
	{
		$teams = $this->getMatchTeams();
		if ( is_null ( $teams ) )
		{
			return null;
		}
		return $this->_getHighestAwayWin( $teams[1]->team_id );
	}

	function _getHighestAwayDef( $teamid )
	{
		$match = $this->getMatch();
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		$query
		      ->select('t1.name AS hometeam')
		      ->select('t2.name AS awayteam')
		      ->select('team1_result AS homegoals')
		      ->select('team2_result AS awaygoals')
		      ->select('pt1.project_id AS pid')
		      ->select('m.id AS mid')
		      ->from('#__joomleague_match as m')
		      ->innerJoin('#__joomleague_project_team pt1 ON pt1.id = m.projectteam1_id')
		      ->innerJoin('#__joomleague_team t1 ON t1.id = pt1.team_id')
		      ->innerJoin('#__joomleague_project_team pt2 ON pt2.id = m.projectteam2_id')
		      ->innerJoin('#__joomleague_team t2 ON t2.id = pt2.team_id')
		      ->where('pt1.project_id = ' . $db->Quote($match->project_id))
		      ->where('m.published = 1')
		      ->where('m.alt_decision = 0')
		      ->where('t2.id = '. $db->Quote($teamid))
		      ->where('(team1_result - team2_result > 0)')
		      ->order('(team2_result - team1_result) ASC');
		$db->setQuery($query, 0, 1);
		return $db->loadObject();
	}


	function getHomeHighestAwayDef()
	{
		$teams = $this->getMatchTeams();
		if ( is_null ( $teams ) )
		{
			return null;
		}
		return $this->_getHighestAwayDef( $teams[0]->team_id );
	}

	function getAwayHighestAwayDef()
	{
		$teams = $this->getMatchTeams();
		if ( is_null ( $teams ) )
		{
			return null;
		}
		return $this->_getHighestAwayDef( $teams[1]->team_id );
	}

	/**
	 * get all games in all projects for these 2 teams
	 * @return array
	 */
	function getGames( )
	{
		$result = array();
		$teams = $this->getMatchTeams();
		if ( is_null ( $teams ) )
		{
			return null;
		}
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		$query
		      ->select('m.*')
		      ->select('DATE_FORMAT(m.time_present, "%H:%i") time_present')
		      ->select('pt1.project_id')
		      ->select('p.timezone')
		      ->select('p.name AS project_name')
		      ->select('r.id AS roundid')
		      ->select('pt1.division_id')
		      ->select('r.roundcode AS roundcode')
		      ->select('r.name AS mname')
		      ->select('p.id AS prid')
		      ->from('#__joomleague_match as m')
		      ->innerJoin('#__joomleague_project_team pt1 ON pt1.id = m.projectteam1_id')
		      ->innerJoin('#__joomleague_project_team pt2 ON pt2.id = m.projectteam2_id')
		      ->innerJoin('#__joomleague_project AS p ON p.id = pt1.project_id')
		      ->innerJoin('#__joomleague_round r ON m.round_id=r.id')
		      ->where('((pt1.team_id = '. $teams[0]->team_id .' AND pt2.team_id = '.$teams[1]->team_id .') '
		      . ' OR (pt1.team_id = '.$teams[1]->team_id .' AND pt2.team_id = '.$teams[0]->team_id .'))')
		      ->where('p.published = 1')
		      ->where('m.published = 1')
		      ->where('m.team1_result IS NOT NULL AND m.team2_result IS NOT NULL')
		      ->group('m.id')
		      ->order('p.ordering, m.match_date ASC');
		$db->setQuery( $query );
		$result = $db->loadObjectList();
		if ($result)
		{
			foreach ($result as $game)
			{
				JoomleagueHelper::convertMatchDateToTimezone($game);
			}
		}

		return $result;
	}

	function getTeamsFromMatches( & $games )
	{
		$teams = Array();

		if ( !count( $games ) )
		{
			return $teams;
		}

		foreach ( $games as $m )
		{
			$projectTeamIds[] = $m->projectteam1_id;
			$projectTeamIds[] = $m->projectteam2_id;
		}
		$listProjectTeamId = implode( ",", array_unique( $projectTeamIds ) );
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		$query
		      ->select('t.id')
		      ->select('t.name')
		      ->select('pt.id as ptid')
		      ->from('#__joomleague_project_team AS pt')
		      ->innerJoin('#__joomleague_team AS t ON t.id = pt.team_id')
		      ->where('pt.id IN ('.$listProjectTeamId.')');
		$db->setQuery( $query );
		$result = $db->loadObjectList();

		foreach ( $result as $r )
		{
			$teams[$r->ptid] = $r;
		}

		return $teams;
	}

	function getPlayground( $pgid )
	{
	    $db = Factory::getDbo();
	    $query = $db->getQuery(true);
	    $query
	           ->select('*')
	           ->from('#__joomleague_playground')
	           ->where('id = '. $db->Quote($pgid));
		$db->setQuery($query, 0, 1);
		return $db->loadObject();
	}

	function getMatchText($match_id)
	{
	    $db = Factory::getDbo();
	    $query = $db->getQuery(true);
	    $query
	           ->select('m.*')
	           ->select('t1.name t1name')
	           ->select('t2.name t2name')
	           ->select('p.timezone')
	           ->from('#__joomleague_match AS m')
	           ->innerJoin('#__joomleague_project_team AS pt1 ON m.projectteam1_id = pt1.id')
	           ->innerJoin('#__joomleague_project_team AS pt2 ON m.projectteam2_id = pt2.id')
	           ->innerJoin('#__joomleague_team AS t1 ON pt1.team_id=t1.id')
	           ->innerJoin('#__joomleague_project AS p ON p.id=pt1.project_id')
	           ->where('m.id = ' . $match_id)
	           ->where('m.published = 1')
	           ->order('m.match_date, t1.short_name');
		$db->setQuery($query);
		$matchText = $db->loadObject();
		if ($matchText)
		{
			JoomleagueHelper::convertMatchDateToTimezone($matchText);
		}
		return $matchText;
	}
	
	/**
	 * Calculates chances between 2 team
	 * Code is from LMO, all credits go to the LMO developers
	 * @return array
	 */
	function getChances()
	{
		$home=$this->getHomeRanked();
		$away=$this->getAwayRanked();

		if ((($home->cnt_matches)>0) && (($away->cnt_matches)>0))
		{
			$won1=$home->cnt_won;		
			$won2=$away->cnt_won;
			$loss1=$home->cnt_lost;
			$loss2=$away->cnt_lost;
			$matches1=$home->cnt_matches;
			$matches2=$away->cnt_matches;
			$goalsfor1=$home->sum_team1_result;
			$goalsfor2=$away->sum_team1_result;
			$goalsagainst1=$home->sum_team2_result;
			$goalsagainst2=$away->sum_team2_result;
		
			$ax=(100*$won1/$matches1)+(100*$loss2/$matches2);
			$bx=(100*$won2/$matches2)+(100*$loss1/$matches1);
			$cx=($goalsfor1/$matches1)+($goalsagainst2/$matches2);
			$dx=($goalsfor2/$matches2)+($goalsagainst1/$matches1);
			$ex=$ax+$bx;
			$fx=$cx+$dx;
		
			if (isset($ex) && ($ex>0) && isset($fx) &&($fx>0)) 
			{	 
				$ax=round(10000*$ax/$ex);
				$bx=round(10000*$bx/$ex);
				$cx=round(10000*$cx/$fx);
				$dx=round(10000*$dx/$fx);
		
				$chg1=number_format((($ax+$cx)/200),2,",",".");
				$chg2=number_format((($bx+$dx)/200),2,",",".");
				$result=array($chg1,$chg2);

				return $result;
			}
		}	
	}
		
	/**
	* get Previous X games of each team
	*
	* @return array
	*/
	function getPreviousX()
	{
		if (!$this->_match) {
			return false;
		}
		$games = array();
		$games[$this->_match->projectteam1_id] = $this->_getTeamPreviousX($this->_match->roundcode, $this->_match->projectteam1_id);
		$games[$this->_match->projectteam2_id] = $this->_getTeamPreviousX($this->_match->roundcode, $this->_match->projectteam2_id);
		
		return $games;
	}
	
	/**
	* returns last X games
	*
	* @param int $current_roundcode
	* @param int $ptid project team id
	* @return array
	*/
	function _getTeamPreviousX($current_roundcode, $ptid)
	{
	    $db = Factory::getDbo();
	    $query = $db->getQuery(true);
		$config = $this->getTemplateConfig('nextmatch');
		$nblast = $config['nb_previous'];
		$query
		      ->select('m.*')
		      ->select('r.project_id')
		      ->select('r.id AS roundid')
		      ->select('r.roundcode')
		      ->select('p.timezone')
		      ->select('pt1.division_id')
		      ->from('#__joomleague_match AS m')
		      ->innerJoin('#__joomleague_round AS r ON r.id = m.round_id')
		      ->innerJoin('#__joomleague_project AS p ON p.id = r.project_id')
		      ->innerJoin('#__joomleague_project_team AS pt1 ON p.id = m.projectteam1_id')
		      ->where('(m.projectteam1_id = ' . $ptid . ' OR m.projectteam2_id = ' . $ptid.')')
		      ->where('r.roundcode < '.$current_roundcode)
		      ->where('m.published = 1')
		      ->group('m.id')
		      ->order('r.roundcode DESC ' . ' LIMIT 0, '.$nblast);
		$db->setQuery($query);
		$res = $db->loadObjectList();
		if ($res) {
			$res = array_reverse($res);
			foreach ($res as $game)
			{
				JoomleagueHelper::convertMatchDateToTimezone($game);
			}
		}
		
		return $res;
	}
}
