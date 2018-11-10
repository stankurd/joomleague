<?php
/**
 * Joomleague
 * @subpackage	Module-Calendar
 *
 * @copyright	Copyright (C) 2006-2015 joomleague.at. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @link		http://www.joomleague.at
 */
use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;

defined('_JEXEC') or die;

class JoomleagueConnector extends JLCalendar{
	//var $db = Factory::getDbo();
	var $xparams;
	var $prefix;
	var $params = null;
	
	
	public function __construct($options)
	{
		$this->params = $options;
	}
	

	function getEntries ( &$caldates, &$params, &$matches )
	{
		$m = array();
		$b = array();
		$this->xparams = $params;
		$this->prefix = $params->prefix;
		if($this->xparams->get('joomleague_use_favteams', 0) == 1)
		{
			$this->favteams = JoomleagueConnector::getFavs();
		}
		if ($this->xparams->get('jlmatches', 0) == 1)
		{
			$rows = JoomleagueConnector::getMatches($caldates);
			$m = JoomleagueConnector::formatMatches($rows, $matches);
		}
		
		if ($this->xparams->get('jlbirthdays', 1) == 1)
		{
			$birthdays = JoomleagueConnector::getBirthdays (  $caldates, $this->params, $this->matches  );
			$b = JoomleagueConnector::formatBirthdays($birthdays, $matches, $caldates);
		}
		return array_merge($m, $b);
	}

	function getFavs()
	{
	    $db = Factory::getDbo();
	    $query = $db->getQuery(true);
	    $query
	           ->select('id')
	           ->select('fav_team')
	           ->from('#__joomleague_project')
	           ->where("fav_team != ''");
		/*$query = "SELECT id, fav_team FROM #__joomleague_project
      where fav_team != '' ";*/

		$projectid		= $this->xparams->get('project_ids') ;

		if ($projectid)
		{
			$projectids = (is_array($projectid)) ? implode(",", $projectid) : $projectid;

			//$query .= " AND id IN(".$projectids.")";
			$query->where("id IN(".$projectids.")");
		}

		$query = ($this->prefix != '') ? str_replace('#__', $this->prefix, $query) : $query;
		$db->setQuery($query);
		$fav=$db->loadObjectList();


		// echo '<pre>';
		// print_r($fav);
		// echo '</pre>';
		//	exit(0);
		return $fav;
		//	return implode(',', $fav);
	}

	function getBirthdays ( $caldates, $ordering='ASC' )
	{
		$teamCondition = '';
		$clubCondition = '';
		$favCondition = '';
		$limitingcondition = '';
		$limitingconditions = array();

		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		
		$customteam = Factory::getApplication()->input->post->get('jlcteam',0,'default');
		$teamid		= $this->xparams->get('team_ids') ;


		if($customteam != 0)
		{
			$limitingconditions[] = "( m.projectteam1_id = ".$customteam." OR m.projectteam2_id = ".$customteam.")";
		}

		if ($teamid && $customteam == 0)
		{

			$teamids = (is_array($teamid)) ? implode(",", $teamid) : $teamid;
			if($teamids > 0) 	{

				$limitingconditions[] = "pt.team_id IN (".$teamids.")";

			}
		}



		if($this->xparams->get('joomleague_use_favteams', 0) == 1 && $customteam == 0)
		{
			foreach ($this->favteams as $projectfavs)
			{
				$favConds[] = "(pt.team_id IN (". $projectfavs->fav_team.") AND p.id =".$projectfavs->id.")";

			}

			$limitingconditions[] = implode(' OR ', $favConds);

		}


		// new insert for user select a club
		$clubid		= $this->xparams->get('club_ids') ;

		if ($clubid && $customteam == 0)
		{

			$clubids = (is_array($clubid)) ? implode(",", $clubid) : $clubid;
			if($clubids > 0) 	$limitingconditions[] = "team.club_id IN (".$clubids.")";

		}



		if (count($limitingconditions) > 0)
		{
			//$limitingcondition .=' AND (';
			//$limitingcondition .= implode(' OR ', $limitingconditions);
			//$limitingcondition .=')';
		    $query->where(" ( ".implode(' OR ', $limitingconditions)." ) ");
		}

		$query = $db->getQuery(true);
		$query
		      ->select('p.id')
		      ->select('p.firstname')
		      ->select('p.lastname')
		      ->select('p.picture')
		      ->select('p.country')
		      ->select("DATE_FORMAT(p.birthday, '%m-%d') AS month_day")
		      ->select("YEAR( CURRENT_DATE( ) ) as year")
		      ->select("DATE_FORMAT('".$caldates['start']."', '%Y') - YEAR( p.birthday ) AS age")
		      ->select("DATE_FORMAT(p.birthday,'%Y-%m-%d') AS date_of_birth")
		      ->select('pt.project_id as project_id')
		      ->select("'showPlayer' AS func_to_call")
		      ->select("'pid' AS id_to_append")
		      ->select('team.short_name')
		      ->select('team.id as teamid')
		      ->from('#__joomleague_person AS p')
		      ->innerJoin('#__joomleague_team_player AS tp ON (p.id = tp.person_id)')
		      ->innerJoin('#__joomleague_project_team AS pt ON (pt.id = tp.projectteam_id)')
		      ->innerJoin('#__joomleague_team AS team ON (team.id = pt.team_id )')
		      ->innerJoin('#__joomleague_club AS club ON (club.id = team.club_id )')
		      ->where('p.published = 1')
		      ->where('p.birthday != \'0000-00-00\'')
		      ->where("DATE_FORMAT(p.birthday, '%m') = DATE_FORMAT('".$caldates['start']."', '%m')");

		$projectid		= $this->xparams->get('project_ids') ;


		if ($projectid)
		{
			$projectids = (is_array($projectid)) ? implode(",", $projectid) : $projectid;
			if($projectids > 0) 	$query->where("(pt.project_id IN (".$projectids.") )");

		}

		//$query .= $limitingcondition;
		$query->group('p.id');
		$query->order('p.birthday');
		
		$query = ($this->prefix != '') ? str_replace('#__', $this->prefix, $query) : $query;
		$db->setQuery($query);
		//echo($db->getQuery());

		$players=$db->loadObjectList();

		return $players;
	}
	function formatBirthdays( $rows, &$matches, $dates )
	{
		$newrows = array();
		$year = substr($dates['start'], 0, 4);


		foreach ($rows AS $key => $row)
		{
			$newrows[$key]['type'] = 'jlb';
			$newrows[$key]['homepic'] = '';
			$newrows[$key]['awaypic'] = '';
			$newrows[$key]['date'] = $year.'-'.$row->month_day;
			$newrows[$key]['age'] = '('.$row->age.')';
			$newrows[$key]['headingtitle'] = $this->xparams->get('birthday_text', 'Birthday');
			$newrows[$key]['name'] = '';

			if ($row->picture != '' && file_exists(JPATH_BASE.'/'.$row->picture))
			{
				$linkit = 1;
				$newrows[$key]['name'] = '<img src="'.Uri::root(true).'/'.$row->picture.'" alt="Picture" style="height:40px; vertical-align:middle;margin:0 5px;" />';

				//echo $newrows[$key]['name'].'<br />';
			}
			$newrows[$key]['name'] .= parent::jl_utf8_convert ($row->firstname, 'iso-8859-1', 'utf-8').' ';
			$newrows[$key]['name'] .= parent::jl_utf8_convert ($row->lastname, 'iso-8859-1', 'utf-8').' - '.parent::jl_utf8_convert ($row->short_name, 'iso-8859-1', 'utf-8');
			//$newrows[$key]['name'] .= ' ('..')';
			$newrows[$key]['matchcode'] = 0;
			$newrows[$key]['project_id'] = $row->project_id;

			// new insert for link to player profile
			//$newrows[$key]['link'] = 'index.php?option=com_joomleague&view=player&p='.$row->project_id.'&pid='.$row->id;
			$newrows[$key]['link'] = JoomleagueHelperRoute::getPlayerRoute( $row->project_id, $row->teamid, $row->id);


			$matches[] = $newrows[$key];
		}
		return $newrows;
	}


	function formatMatches( $rows, &$matches )
	{
		$newrows = array();
		$teamnames = $this->xparams->get('team_names', 'short_name');
		$teams = JoomleagueConnector::getTeamsFromMatches( $rows );
		$teams[0] = new stdclass;
		$teams[0]->name = $teams[0]->$teamnames = $teams[0]->logo_small = $teams[0]->logo_middle = $teams[0]->logo_big =  '';

		foreach ($rows AS $key => $row) {
			$newrows[$key]['type'] = 'jlm';
			$newrows[$key]['homepic'] = JoomleagueConnector::buildImage($teams[$row->projectteam1_id]);
			$newrows[$key]['awaypic'] = JoomleagueConnector::buildImage($teams[$row->projectteam2_id]);

			$newrows[$key]['date'] = JoomleagueHelper::getMatchStartTimestamp($row);
			//$newrows[$key]['result'] = (!is_null($row->matchpart1_result)) ? $row->matchpart1_result . ':' . $row->matchpart2_result : '-:-';
			$newrows[$key]['result'] = (!is_null($row->team1_result)) ? $row->team1_result . ':' . $row->team2_result : '-:-';
			$newrows[$key]['headingtitle'] = parent::jl_utf8_convert ($row->name.'-'.$row->roundname, 'iso-8859-1', 'utf-8');
			$newrows[$key]['homename'] = JoomleagueConnector::formatTeamName($teams[$row->projectteam1_id]);
			$newrows[$key]['awayname'] = JoomleagueConnector::formatTeamName($teams[$row->projectteam2_id]);
			$newrows[$key]['matchcode'] = $row->matchcode;
			$newrows[$key]['project_id'] = $row->project_id;

			// insert matchdetaillinks
			$newrows[$key]['link'] = JoomleagueHelperRoute::getNextMatchRoute( $row->project_id, $row->matchcode);
			$matches[] = $newrows[$key];
			parent::addTeam($row->projectteam1_id, parent::jl_utf8_convert ($teams[$row->projectteam1_id]->name, 'iso-8859-1', 'utf-8'), $newrows[$key]['homepic']);
			parent::addTeam($row->projectteam2_id, parent::jl_utf8_convert ($teams[$row->projectteam2_id]->name, 'iso-8859-1', 'utf-8'), $newrows[$key]['awaypic']);

		}
		return $newrows;
	}

	function formatTeamName($team)
	{
		$teamnames = $this->xparams->get('team_names', 'short_name');
		switch ($teamnames)
		{
			case '-':
				return '';
				break;
			case 'short_name':
				$teamname = '<acronym title="'.parent::jl_utf8_convert ($team->name, 'iso-8859-1', 'utf-8').'">'
				.parent::jl_utf8_convert ($team->short_name, 'iso-8859-1', 'utf-8')
				.'</acronym>';
				break;
			default:
				if (!isset($team->$teamnames) OR (is_null($team->$teamnames) OR trim($team->$teamnames)=='')) {
					$teamname = parent::jl_utf8_convert ($team->name, 'iso-8859-1', 'utf-8');
				}
				else {
					$teamname = parent::jl_utf8_convert ($team->$teamnames, 'iso-8859-1', 'utf-8');
				}
				break;
		}
		return $teamname;
	}

	function buildImage($team)
	{
		$image = $this->xparams->get('team_logos', 'logo_small');
		if ($image == '-') { return ''; }
		$logo = '';

		if ($team->$image != '' && file_exists(JPATH_BASE.'/'.$team->$image))
		{
			$h = $this->xparams->get('logo_height', 20);
			$logo = '<img src="'.Uri::root(true).'/'.$team->$image.'" alt="'
			.parent::jl_utf8_convert ($team->short_name, 'iso-8859-1', 'utf-8').'" title="'
			.parent::jl_utf8_convert ($team->name, 'iso-8859-1', 'utf-8').'"';
			if ($h > 0) {
				$logo .= ' style="height:'.$h.'px;"';
			}
			$logo .= ' />';
		}
		return $logo;
	}

	function getMatches($caldates, $ordering='ASC')
	{
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		
		$teamCondition = '';
		$clubCondition = '';
		$favCondition = '';
		$limitingcondition = '';
		$limitingconditions = array();
		$favConds = array();

		$customteam = Factory::getApplication()->input->post->get('jlcteam',0,'default');

		$teamid		= $this->xparams->get('team_ids') ;

		if($customteam != 0)
		{
			$limitingconditions[] = "( m.projectteam1_id = ".$customteam." OR m.projectteam2_id = ".$customteam.")";
		}

		if ($teamid && $customteam == 0)
		{
			$teamids = (is_array($teamid)) ? implode(",", $teamid) : $teamid;
			if($teamids > 0)
			{
				$limitingconditions[] = "pt.team_id IN (".$teamids.")";
			}
		}

		$clubid		= $this->xparams->get('club_ids') ;

		if ($clubid && $customteam == 0)
		{
			$clubids = (is_array($clubid)) ? implode(",", $clubid) : $clubid;
			if($clubids > 0)
			{
				$limitingconditions[] = "team.club_id IN (".$clubids.")";
			}
		}

		if($this->xparams->get('joomleague_use_favteams', 0) == 1 && $customteam == 0)
		{
			foreach ($this->favteams as $projectfavs)
			{
				$favConds[] = "(pt.team_id IN (". $projectfavs->fav_team.") AND p.id =".$projectfavs->id.")";
			}
			if(!empty($favConds))
			{
				$limitingconditions[] = implode(' OR ', $favConds);
			}
		}

		if (count($limitingconditions) > 0)
		{
			//$limitingcondition .=' AND (';
			//$limitingcondition .= implode(' OR ', $limitingconditions);
			//$limitingcondition .=')';
		    $query->where(" ( ".implode(' OR ', $limitingconditions)." ) ");
		}

		$limit = (isset($caldates['limitstart'])&&isset($caldates['limitend'])) ? ' LIMIT '.$caldates['limitstart'].', '.$caldates['limitend'] :'';

        $query
                ->select('m.*,p.*')
                //->select('m.id,m.round_id,m.projectteam1_id,m.projectteam2_id,m.match_date,m.team1_result,m.team2_result,m.match_date as gamematchdate')
                //->select('p.timezone,p.name')
                ->select('match_date AS caldate')
                ->select('r.roundcode')
                ->select('r.name AS roundname')
                ->select('r.round_date_first')
                ->select('r.round_date_last')
                ->select('m.id as matchcode')
                ->select('p.id as project_id')
                ->from('#__joomleague_match m')
                ->innerJoin('#__joomleague_round r ON r.id = m.round_id')
                ->innerJoin('#__joomleague_project p ON p.id = r.project_id')
                ->innerJoin('#__joomleague_project_team pt ON (pt.id = m.projectteam1_id OR pt.id = m.projectteam2_id)')
                ->innerJoin('#__joomleague_team team ON team.id = pt.team_id')
                ->innerJoin('#__joomleague_club club ON club.id = team.club_id');
        /*        
		$query = "SELECT  m.*,p.*,
                      match_date AS caldate,
                      r.roundcode, r.name AS roundname, r.round_date_first, r.round_date_last,
                      m.id as matchcode, p.id as project_id
              FROM #__joomleague_match m
              inner JOIN #__joomleague_round r ON r.id = m.round_id
              inner JOIN #__joomleague_project p ON p.id = r.project_id
              inner JOIN #__joomleague_project_team pt ON (pt.id = m.projectteam1_id OR pt.id = m.projectteam2_id)
              inner JOIN #__joomleague_team team ON team.id = pt.team_id
              inner JOIN #__joomleague_club club ON club.id = team.club_id
               ";
*/
		$where = " WHERE m.published = 1
               AND p.published = 1 ";
		if (isset($caldates['start'])) $where .= " AND m.match_date >= '".$caldates['start']."'";
		if (isset($caldates['end'])) $where .= " AND m.match_date <= '".$caldates['end']."'";
		if (isset($caldates['matchcode'])) $where .= " AND r.matchcode = '".$caldates['matchcode']."'";
		$projectid		= $this->xparams->get('project_ids') ;

		if ($projectid)
		{
			$projectids = (is_array($projectid)) ? implode(",", $projectid) : $projectid;
			if($projectids > 0) 	$where .= " AND p.id IN (".$projectids.")";
		}

		if(isset($caldates['resultsonly']) && $caldates['resultsonly']== 1) $where .= " AND m.team1_result IS NOT NULL";

		$where .= $limitingcondition;

		$where .= " GROUP BY m.id";
		$where .=" ORDER BY m.match_date ".$ordering;

		$query = ($this->prefix != '') ? str_replace('#__', $this->prefix, $query) : $query;
		$db->setQuery($query.$where.$limit);
		$result = $db->loadObjectList();
		if ($result)
		{
			foreach ($result as $match)
			{
				JoomleagueHelper::convertMatchDateToTimezone($match);
			}
		}
		return $result;
	}

	function getTeamsFromMatches( &$games )
	{
		$db = Factory::getDbo();
		$query = $db->getQuery(true);

		if ( !count ($games) ) return Array();
		foreach ( $games as $m )
		{
			$teamsId[] = $m->projectteam1_id;
			$teamsId[] = $m->projectteam2_id;
		}

		$listTeamId = implode( ",", array_unique($teamsId) );
        $query
                ->select('tl.id AS teamtoolid')
                ->select('tl.division_id')
                ->select('tl.standard_playground')
                ->select('tl.start_points')
                ->select('tl.info')
                ->select('tl.team_id')
                ->select('tl.checked_out')
                ->select('tl.checked_out_time')
                ->select('tl.picture')
                ->select('tl.project_id')
                ->select('t.id')
                ->select('t.name')
                ->select('t.short_name')
                ->select('t.middle_name')
                ->select('t.info')
                ->select('t.club_id')
                ->select('c.logo_small')
                ->select('c.logo_middle')
                ->select('c.logo_big')
                ->select('c.country')
                ->select('p.name AS project_name')
                ->from('#__joomleague_team t')
                ->innerJoin('#__joomleague_project_team tl on tl.team_id = t.id')
                ->innerJoin('#__joomleague_project p on p.id = tl.project_id')
                ->leftJoin('#__joomleague_club c on t.club_id = c.id')
                ->where('tl.id IN ('.$listTeamId.')')
                ->where('tl.project_id = p.id');
        
        /*
		$query = "SELECT tl.id AS teamtoolid,
                         tl.division_id,
                         tl.standard_playground,
                         tl.start_points,
                     tl.info,
                     tl.team_id,
                     tl.checked_out,
                     tl.checked_out_time,
                     tl.picture,
                     tl.project_id,
                     t.id, t.name,
                     t.short_name,
                     t.middle_name,
                     t.info, t.club_id,
                     c.logo_small,
                     c.logo_middle,
                     c.logo_big,
                     c.country,
                     p.name AS project_name
                FROM #__joomleague_team t
                INNER JOIN #__joomleague_project_team tl on tl.team_id = t.id
                INNER JOIN #__joomleague_project p on p.id = tl.project_id
                LEFT JOIN #__joomleague_club c on t.club_id = c.id
                WHERE tl.id IN (".$listTeamId.") AND tl.project_id = p.id";*/
		$query = ($this->prefix != '') ? str_replace('#__', $this->prefix, $query) : $query;
		$db->setQuery($query);
		if ( !$result = $db->loadObjectList('teamtoolid') ) $result = Array();
		return $result;
	}

	function build_url( &$row )
	{

	}

	function getGlobalTeams ()
	{
		$teamnames = $this->xparams->get('team_names', 'short_name');
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		
		$query = "SELECT t.".$teamnames." AS name, t.id AS value
    FROM #__joomleague_teams t, #__joomleague p
    WHERE t.id IN(p.fav_team)";
		$db->setQuery($query);
		$result = $db->loadObjectList();
	}
}