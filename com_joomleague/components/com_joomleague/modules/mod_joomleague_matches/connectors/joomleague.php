<?php
use Joomla\CMS\Factory;
use Joomla\CMS\Router\Route;
use Joomla\CMS\HTML\HTMLHelper;

/*
 * @package 			Joomleague
 * @subpackage		Module-Matches
 * @lastedit			26.08.2016
 * @testedversion	Joomla 3.6
 *
 * @copyright	Copyright (C) 2006-2016 joomleague.at. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @link		http://www.joomleague.at 
 */
defined('_JEXEC') or die;

class MatchesJoomleagueConnector extends modMatchesHelper {

	public function buildTeamLinks(& $obj, $nr) {
		if (($this->params->get('link_teams', 0) + $this->usedteamscheck($obj->team_id, $obj->project_id)) < 2)
		return false;

		$linktext = '';
		$urls = array ();
		$linkstructure = array (
				'club' => array (
					'view' => 'clubinfo',
					'cid' => $obj->club_id,
					'p' => $obj->project_id
			),
				'teaminfo' => array (
					'view' => 'teaminfo',
					'tid' => $obj->team_id,
					'p' => $obj->project_id
			),
				'roster' => array (
					'view' => 'roster',
					'tid' => $obj->team_id,
					'p' => $obj->project_id
			),
				'curve' => array (
					'view' => 'curve',
					'tid' => $obj->team_id,
					'p' => $obj->project_id
			),
				'plan' => array (
					'view' => 'teamplan',
					'tid' => $obj->team_id,
					'p' => $obj->project_id
			)
		);
		foreach ($linkstructure AS $linktype => $urlarray) {
			if ($this->params->get('link_team_' . $linktype, 1) == 1) {
				$linktext .= '<a href="' . Route :: _('index.php?option=com_joomleague' . $this->arrayToUri($urlarray) . $this->itemid) . '">' . $this->addteamicon('link_team_' . $linktype) . '</a>';
			}
		}
		if ($this->params->get('link_team_www', 1) == 1) {
			if (!empty ($obj->website))
			$linktext .= '<a href="' . trim($obj->website) . '">' . $this->addteamicon('link_team_www') . '</a>';
		}

		if (!empty ($linktext)) {
			$linktext = '<span class="jlmlTeamLinks" style="display:' . $this->params->get('team_link_status', 'none') . ';" id="jlmlTeamname' . $obj->id . 'mod' . $this->module_id . 'nr' . $nr . '_over">' .
			$linktext . '</span>';
		}
		return $linktext;
	}
	/**
	 *
	 * TODO: fix for timezone
	 */
	public function getDateString() {
		if ($this->params->get('use_offset_matches') == 1 && 0) {
			//return 'DATE_ADD(m.match_date, INTERVAL p.serveroffset HOUR)';
		} else
		return 'm.match_date';

	}

	/**
	 *
	 * TODO: fix for timezone
	 */
	public function getDateStringNoTime() {
		if ($this->params->get('use_offset_matches') == 1 && 0) {
			//return 'DATE(DATE_ADD(m.match_date, INTERVAL p.serveroffset HOUR))';
		} else {
			return 'DATE(m.match_date)';
		}
	}

	public function buildWhere() {
	    $db = Factory :: getDBO();
	    $query = $db->getQuery(true);
		$this->getUsedTeams();
		if ($this->id > 0) {
			$this->conditions[0] = "(m.id = '" . $this->id . "')";
		} else {
			$this->conditions[] = "p.published = 1"; //project
			$this->conditions[] = "m.published = 1"; //match
			$this->conditions[] = $this->getTimeLimit();
			$p = $this->params->get('project');
			if (!empty ($p)) {
				$projectstring = (is_array($p)) ? implode(",", $p) : $p;
				if($projectstring != '-1' && $projectstring != '') {
					$this->conditions[] = "(pt1.project_id IN (" . $projectstring . ") OR pt2.project_id IN (" . $projectstring . "))";
				}
			}
			$nu = $this->params->get('project_not_used');
			if (!empty ($nu)) {
				$notusedstring = (is_array($nu)) ? implode(",", $nu) : $nu;
				if($notusedstring != '-1' && $notusedstring != '') {
					$this->conditions[] = "(pt1.project_id NOT IN (" . $notusedstring . ") OR pt2.project_id NOT IN (" . $notusedstring . "))";
				}
			}
		}
	}
	public function buildOrder() {
	    $db = Factory :: getDBO();
	    $query = $db->getQuery(true);
		$limit = ($this->params->get('limit', 0) > 0) ? $this->params->get('limit', 0) : 1;

		if ($this->params->get('order_by_project') == 0) {
			if ($this->params->get('lastsortorder') == 'desc') {
				return " ORDER by match_date DESC LIMIT " . $limit;
			}
			else {
				return " ORDER by match_date LIMIT " . $limit;
			}
		}
		else {
			return  " ORDER by match_date_notime, p.ordering ASC LIMIT ". $limit;
		}
	}

	public function getMatches() {
	    $db = Factory :: getDBO();
	    $query = $db->getQuery(true);
	    $limit = ($this->params->get('limit', 0) > 0) ? $this->params->get('limit', 0) : 1;
		$query	= " SELECT m.*,m.id as match_id, t1.id team1_id, t2.id team2_id,"
				. " " . $this->getDateStringNoTime() . " AS match_date_notime,"
				. " " . $this->getDateString() . " AS match_date,"
				. " NOW() AS currenttime,"
				. " IF ("
				. "		m.match_result_type > 0,"
				. "     (p.game_regular_time+(p.game_parts * p.halftime)-p.halftime) + p.add_time,"
				. "     p.game_regular_time+(p.game_parts * p.halftime)-p.halftime"
				. " ) AS totaltime,"
				. " IF ("
				. "		(match_date > UTC_TIMESTAMP() AND m.team1_result IS NULL) AND"
				. "		((m.team1_result_split IS NULL) OR TRIM(REPLACE(m.team1_result_split, ';', '')) = ''),"
				. 	    $this->getDateString() . ","
				. "     'z'"
				. " ) AS upcoming,"
				. " IF ("
				. "		(m.team1_result IS NOT NULL OR " . $this->getDateString() . " < UTC_TIMESTAMP()),"
				.		$this->getDateString() . ","
				. "		0"
				. " ) AS alreadyplayed,"
				. " IF ("
				. "		("
				. "			("
				. "				DATE_ADD(" . $this->getDateString() . ","
				. "					INTERVAL IF ("
				. "						m.match_result_type > 0,"
				. "						(p.game_regular_time+(p.game_parts * p.halftime)-p.halftime) + p.add_time,"
				. "						p.game_regular_time+(p.game_parts * p.halftime)-p.halftime"
				. "					) MINUTE"
				. "				) > UTC_TIMESTAMP()"
				. "			) AND ("
				.			$this->getDateString() . " < UTC_TIMESTAMP()"
				. "			) AND (m.team1_result IS NULL)"
				. "		),"
				. 		$this->getDateString() . ","
				. "		'z'"
				. " ) AS actplaying,"
				. " IF ("
				. "		((m.team1_result IS NULL) AND (m.team1_result_split IS NOT NULL) AND"
				. "			TRIM(REPLACE(m.team1_result_split, ';', '') != '')),"
				. 		$this->getDateString() . ","
				.		"'z'"
				. " ) AS live,"
				. " DATE_SUB(" . $this->getDateString() . ", INTERVAL '90' MINUTE) AS meetingtime,"
				. " NOW() AS local_time,"
				. " r.*,"
				. " p.name AS pname,"
				. " p.current_round,"
				. " p.id AS project_id,"
				. " p.timezone,"
				. " p.game_parts,"
				. " p.ordering,"
				. " IF (mref.project_referee_id > 0, concat(person.lastname, ', ', person.firstname), '') AS refname,"
				. " pg.name AS pg_name, pt1.division_id, "
				. " pg.short_name AS pg_shortname"
				. " FROM     #__joomleague_match m"
	        	. " INNER JOIN #__joomleague_round r ON m.round_id = r.id"
				. " INNER JOIN #__joomleague_project_team pt1 ON pt1.id = m.projectteam1_id"
	         	. " INNER JOIN #__joomleague_team t1 ON t1.id = pt1.team_id"
	         	. " INNER JOIN #__joomleague_project_team pt2 ON pt2.id = m.projectteam2_id"
	         	. " INNER JOIN #__joomleague_team t2 ON t2.id = pt2.team_id"
	         	. " INNER JOIN #__joomleague_project p ON pt1.project_id = p.id"
         		. " LEFT JOIN #__joomleague_match_referee AS mref ON m.id = mref.match_id"
         		. " LEFT JOIN #__joomleague_project_referee AS pref ON pref.id = mref.project_referee_id"
         		. " LEFT JOIN #__joomleague_person AS person ON person.id = pref.person_id"
         		. "       AND person.published = 1"
         		. "       AND pref.published = 1"
         		. " LEFT JOIN #__joomleague_playground AS pg ON pg.id = m.playground_id"
	  			. "  WHERE ( ";
		$this->buildWhere();
		$query .= implode(' AND ', $this->conditions);
		$query .= " )"
				. " GROUP BY m.id, mref.project_referee_id ";
		$query .= $this->buildOrder();
		$matches = $this->getFromDB($query);
		if ($matches)
		{
			foreach ($matches as $match)
			{
				JoomleagueHelper::convertMatchDateToTimezone($match);
			}
		}
		return $this->formatMatches($matches);
	}

	public function formatHeading(& $row, $match) {
		$pview = $this->params->get('p_link_func', 'results');
		$rview = $this->params->get('r_link_func', 'results');
			$views = array (
				'results'		=> 'results',
				'resultsrank'	=> 'resultsranking',
				'ranking'		=> 'ranking'
			);
		$linkstructure = array (
				'project' => array (
					'view'		=> $views[$pview],
					'p'			=> $match->project_id,
					'division'	=> $match->division_id
				),
				'matchday' => array (
					'view'		=> $views[$rview],
					'r'			=> $match->round_id,
					'p'			=> $match->project_id,
					'division'	=> $match->division_id
			)
		);
		$heading = '';
		$heading2 = '';
		if ($this->params->get('show_project_title') != 0) {
			if ($this->params->get('link_project_title') != 0) {
				$heading = '<a href="' . Route :: _('index.php?option=com_joomleague' . $this->arrayToUri($linkstructure['project']) . $this->itemid) . '">';
			}
			$heading .= $this->jl_utf8_convert($match->pname, 'iso-8859-1', 'utf-8');
			if ($this->params->get('link_project_title') != 0) {
				$heading .= '</a>';
			}

			if ($this->params->get('show_matchday_title') != 0) {
				$heading2 .= ' - ';
			}
		}
		$row['pname'] = $heading;
		if ($this->params->get('show_matchday_title') != 0) {
			if ($this->params->get('link_matchday_title') != 0) {
				$heading2 .= '<a href="' . Route :: _('index.php?option=com_joomleague' . $this->arrayToUri($linkstructure['matchday']) . $this->itemid) . '">';
			}
			$heading2 .= $this->jl_utf8_convert($match->name, 'iso-8859-1', 'utf-8');
			if ($this->params->get('link_matchday_title') != 0) {
				$heading2 .= '</a>';
			}
			$row['rname'] = $heading2;
		}
		$row['heading'] = $heading . $heading2;
	}

	public function getTeamsFromMatches(& $matches) {
	    $db = Factory :: getDBO();
	    $query = $db->getQuery(true);
		if (!count($matches))
		return Array ();
		foreach ($matches as $m) {
			$cond[] = "(pt.id = '" . $m->projectteam1_id . "'
					    OR  pt.id = '" . $m->projectteam2_id . "')";
		}
		/*$query = "SELECT pt.id as ptid,
                         pt.division_id,
                         pt.standard_playground,
                         pt.start_points,
		                 pt.info,
                         pt.team_id,
                         pt.checked_out,
                         pt.checked_out_time,
                         pt.picture team_picture,
                         pt.project_id,
		                 t.id,
                         t.name,
                         t.short_name,
                         t.middle_name,
                         t.notes
                         t.club_id,
		                 pg1.id AS tt_pg_id,
                         pg1.name AS tt_pg_name,
                         pg1.short_name AS tt_pg_short_name,
		                 pg2.id AS club_pg_id,
                         pg2.name AS club_pg_name,
                         pg2.short_name AS club_pg_short_name,
		                 t.id team_id,
		                 c.logo_small club_small,
                         c.logo_middle club_middle,
                         c.logo_big club_big,
                         c.country,
		                 d.name AS division_name,
                         d.shortname AS division_shortname,
		                 p.name AS project_name,
                         c.website
					FROM #__joomleague_team t
					LEFT JOIN #__joomleague_project_team pt on pt.team_id = t.id
					LEFT JOIN #__joomleague_club c on t.club_id = c.id
		           	LEFT JOIN #__joomleague_division d on d.id = pt.division_id
		           	LEFT JOIN #__joomleague_project p on p.id = pt.project_id
		           	LEFT JOIN #__joomleague_playground pg1 ON pg1.id = pt.standard_playground
		           	LEFT JOIN #__joomleague_playground pg2 ON pg2.id = c.standard_playground
					WHERE (" . implode(' OR ', $cond) . ")";*/
		$query = $db->getQuery(true);
		$query
		      ->select('pt.id as ptid')
		      ->select('pt.division_id')
		      ->select('pt.standard_playground')
		      ->select('pt.start_points')
		      ->select('pt.info')
		      ->select('pt.team_id')
		      ->select('pt.checked_out')
		      ->select('pt.checked_out_time')
		      ->select('pt.picture team_picture')
		      ->select('pt.project_id')
		      ->select('t.id')
		      ->select('t.name')
		      ->select('t.short_name')
		      ->select('t.middle_name')
		      ->select('t.notes')
		      ->select('t.club_id')
		      ->select('pg1.id AS tt_pg_id')
		      ->select('pg1.name AS tt_pg_name')
		      ->select('pg1.short_name AS tt_pg_short_name')
		      ->select('t.id team_id')
		      ->select('c.logo_small club_small')
		      ->select('c.logo_middle club_middle')
		      ->select('c.logo_big club_big')
		      ->select('c.country')
		      ->select('d.name AS division_name')
		      ->select('d.shortname AS division_shortname')
		      ->select('p.name AS project_name')
		      ->select('c.website')
		      ->from('#__joomleague_team t')
		      ->leftJoin('#__joomleague_project_team pt on pt.team_id = t.id')
		      ->leftJoin('#__joomleague_club c on t.club_id = c.id')
		      ->leftJoin('#__joomleague_division d on d.id = pt.division_id')
		      ->leftJoin('#__joomleague_project p on p.id = pt.project_id')
		      ->leftJoin('#__joomleague_playground pg1 ON pg1.id = pt.standard_playground')
		      ->leftJoin('#__joomleague_playground pg2 ON pg2.id = c.standard_playground')
		      ->where('(' . implode(' OR ', $cond) . ')');
		$tempteams = $this->getFromDB($query, 'ptid');
		$teams = array ();
		foreach ((array) $tempteams AS $t) {
			$teams[$t->ptid] = $t;
		}
		return $teams;
	}

	public function getUsedTeams() {
	    $db = Factory :: getDBO();
	    $query = $db->getQuery(true);
		$customteams = array();
		$ajaxteam = Factory ::getApplication()->input->post->get('usedteam', 0, 'default');
		if ($ajaxteam > 0) {
			array_push($customteams, $ajaxteam);
		}
		$conditions = array ();
		if ($this->params->get('use_fav', 0) == 0) {
			$teams = $this->params->get('teams', '0');
			array_push($customteams, $teams);
		} else {
			$query = "SELECT id, fav_team FROM #__joomleague_project p WHERE fav_team != ''";
			$projectid = $this->params->get('project');

			$notused = $this->params->get('project_not_used');

			if (!empty ($projectid)) {
				$projectstring = (is_array($projectid)) ? implode(',', $projectid) : $projectid;
				if($projectstring != '-1' && $projectstring != '') {
					$query .= " AND id IN (" . $projectstring . ")";
				}
			}
			if (!empty ($notused)) {
				$notusedstring = (is_array($notused)) ? implode(',', $notused) : $notused;
				if($notusedstring != '-1' && $notusedstring != '') {
					$query .= " AND id NOT IN (" . $notusedstring . ")";
				}
			}
			if ($fav = $this->getFromDB($query, 'id', 'assc')) {
				foreach ((array) $fav AS $key => $team) {
					$fav_team = $this->arrStrToClean($team['fav_team']);
					$this->usedteams[$key] = explode(',', $fav_team);
					$cond = "(
								(pt1.team_id IN (" . $fav_team . ") OR pt2.team_id IN (" . $fav_team . ")) AND
			                    (pt1.project_id = " . $key . "  OR pt2.project_id = " . $key . ")
							)";
					$conditions[] = $cond;
				}
			}
		}

		// For teams without project_id
		if (!empty ($customteams) && $customteams[0] != '0' && $customteams[0] != '') {
			//$this->usedteams[0] = $customteams;
			//$other_teams = implode(',', $customteams);
			$other_teams = (is_array($teams)) ? implode(',', $teams) : $teams;
			if($other_teams != '') {
				$conditions[] = "(pt1.team_id IN (" . $other_teams . ") OR pt2.team_id IN (" . $other_teams . "))";
			}
		}
		if(count($conditions) ) {
			$this->conditions[] = "(" . implode(' OR ', $conditions) . ")";
		}
	}

	public function createMatchLinks(& $row, & $match) {
		$useicons = $this->iconpath;

		$row['reportlink'] = false;
		$row['statisticlink'] = false;
		$row['nextmatchlink'] = false;
		$linkstructure = array (
			'report' => array (
				'view' => 'matchreport',
				'p' => $match->project_id,
				'mid' => $match->match_id
			),
			'nextmatch' => array (
				'view' => 'nextmatch',
				'p' => $match->project_id,
				'tid' => 0,
				'mid' => $match->match_id
			),
			'statistic' => array (
				'view' => 'stats',
				'p' => $match->project_id
			),
		);
		if ($this->params->get('show_act_report_link', 0) == 1 && $match->show_report == 1) {
			$uri = 'index.php?option=com_joomleague' . $this->arrayToUri($linkstructure['report']) . $this->itemid;
			$row['reportlink'] = '<a href="' . Route :: _($uri) . '" title="' . $this->params->get('show_act_report_text') . '">';
			$row['reportlink'] .= ($useicons) ? HTMLHelper::_('image', $this->iconpath . 'report.png', $this->params->get('show_act_report_text'), array (
				'title' => $this->params->get('show_act_report_text'),
				'height' => '16',
				'width' => '16'
			)) : $this->params->get('show_act_report_text') . '<br />';
			$row['reportlink'] .= '</a>';
		}
		if ($this->params->get('show_statistic_link', 0) == 1 && ($match->team1_result || $match->team2_result)) {
			$uri = 'index.php?option=com_joomleague' . $this->arrayToUri($linkstructure['statistic']) . $this->itemid;
			$row['statisticlink'] = '<a href="' . Route :: _($uri) . '" title="' . $this->params->get('statistic_link_text') . '">';
			$row['statisticlink'] .= ($useicons) ? HTMLHelper:: _('image', $this->iconpath . 'history.png', $this->params->get('statistic_link_text'), array (
				'title' => $this->params->get('statistic_link_text'),
				'height' => '16',
				'width' => '16'
			)) : $this->params->get('statistic_link_text') . '<br />';
			$row['statisticlink'] .= '</a>';
		}
		if ($this->params->get('show_nextmatch_link', 0) == 1 && !($match->team1_result || $match->team2_result)) {
			$uri = 'index.php?option=com_joomleague' . $this->arrayToUri($linkstructure['nextmatch']) . $this->itemid;
			$row['nextmatchlink'] = '<a href="' . Route :: _($uri) . '" title="' . $this->params->get('statistic_link_text') . '">';
			$row['nextmatchlink'] .= ($useicons) ? HTMLHelper :: _('image', $this->iconpath . 'history.png', $this->params->get('nextmatch_link_text'), array (
				'title' => $this->params->get('nextmatch_link_text'),
				'height' => '16',
				'width' => '16'
			)) : $this->params->get('nextmatch_link_text') . '<br />';
			$row['nextmatchlink'] .= '</a>';
		}

	}

	public function createLocation(& $row, & $match, & $team) {

		$thisvenue = false;
		if ($team && $this->params->get('show_venue') != 0) {
			$location_id = 0;
			$usedvenuename = $this->params->get('venue_name');
			$venue = array (
				'id' => 0
			);

			if ($match->playground_id > 0) {
				$venue['id'] = $match->playground_id;
				$venue['name'] = $match->pg_name;
				$venue['short_name'] = $match->pg_shortname;
			}

			elseif ($team->tt_pg_id > 0) {
				$venue['id'] = $team->tt_pg_id;
				$venue['name'] = $team->tt_pg_name;
				$venue['short_name'] = $team->tt_pg_short_name;
			}
			elseif ($team->club_pg_id > 0) {
				$venue['id'] = $team->club_pg_id;
				$venue['name'] = $team->club_pg_name;
				$venue['short_name'] = $team->club_pg_short_name;
			}
			if ($venue['id'] > 0) {
				$venuename = ($usedvenuename == 'name') ? $this->jl_utf8_convert($venue[$usedvenuename], 'iso-8859-1', 'utf-8') : '<abbr title="' . $this->jl_utf8_convert($venue['name'], 'iso-8859-1', 'utf-8') . '">' . $this->jl_utf8_convert($venue[$usedvenuename], 'iso-8859-1', 'utf-8') . '</abbr>';
				$venuetext = '%s';
				$venuetip = $this->params->get('venue_text');
				if ($this->params->get('link_venue') == 1) {
					$linkstructure = array (
						'venue' => array (
							'view' => 'playground',
							'pgid' => $venue['id'],
							'p' => $match->project_id
						)
					);
					$venuelink = Route :: _('index.php?option=com_joomleague' . $this->arrayToUri($linkstructure['venue']) . $this->itemid);
					$venuetext = '<a href="' . $venuelink . '" title="'. $venue['name'] .'">'. $venue['name'] .'</a>';
					$thisvenue = sprintf($venuetext, $venuetip.' '.$venuename, $venuename);
				} else {
					$thisvenue = sprintf($venuetext, $venuename);
				}
				if ($this->iconpath) {
					$thisvenue = HTMLHelper :: _('image', $this->iconpath . 'house.png', $venuetip, array (
						'title' => $venuetip,
						'height' => '16',
						'width' => '16'
					)) . ' ' . $thisvenue;
				} else {
					$thisvenue = $venuetip . ' ' . $thisvenue;
				}
			}
		}
		$row['location'] = $thisvenue;
	}

	public function getDefaultLogos() {
		return array (
			"club_big" 		=> JoomleagueHelper::getDefaultPlaceholder('clublogobig'),
			"club_middle" 	=> JoomleagueHelper::getDefaultPlaceholder('clublogomedium'),
			"club_small" 	=> JoomleagueHelper::getDefaultPlaceholder('clublogosmall'),
			"team_picture" 	=> JoomleagueHelper::getDefaultPlaceholder('team'),
			"country" 		=> JoomleagueHelper::getDefaultPlaceholder('icon'),
		);
	}

	public function next_last(& $match) {
		$match->lasthome = $match->nexthome = $match->lastaway = $match->nextaway = false;
        if ($match->match_date)
        {
          $match_date_utc_object = DateTime::createFromFormat('Y-m-d H:i:s', $match->match_date, new DateTimeZone($match->timezone));
          $match_date_utc_object->setTimezone(new DateTimeZone('UTC'));
          $match_date_utc = $match_date_utc_object->format('Y-m-d H:i:s');
        }
		$db = Factory :: getDBO();
		$query = $db->getQuery(true);
		$query
		      ->select('m.id')
		      ->from('#__joomleague_match AS m')
		      ->leftJoin('#__joomleague_project_team pt1 ON pt1.id = m.projectteam1_id')
		      ->leftJoin('#__joomleague_project_team pt2 ON pt2.id = m.projectteam2_id')
		      ->leftJoin('#__joomleague_project AS p ON p.id = pt1.project_id')
		      ->where('(' .$this->getDateString() . ' < ' .$match_date_utc .')')
		      ->where('(m.projectteam1_id = ' . $match->projectteam1_id . ' OR m.projectteam2_id = ' . $match->projectteam1_id .')')
		      ->where('pt1.project_id = ' . $match->project_id )
		      ->order('m.match_date DESC LIMIT 1');
		if ($temp = $this->getFromDB($query)) {
			$match->lasthome = $temp[0]->id;
		}
		$query
		->select('m.id')
		->from('#__joomleague_match AS m')
		->leftJoin('#__joomleague_project_team pt1 ON pt1.id = m.projectteam1_id')
		->leftJoin('#__joomleague_project_team pt2 ON pt2.id = m.projectteam2_id')
		->leftJoin('#__joomleague_project AS p ON p.id = pt1.project_id')
		->where('(' .$this->getDateString() . ' > ' .$match_date_utc .')')
		->where('(m.projectteam1_id = ' . $match->projectteam1_id . ' OR m.projectteam2_id = ' . $match->projectteam1_id .')')
		->where('pt1.project_id = ' . $match->project_id )
		->order('m.match_date ASC LIMIT 1');
		if ($temp = $this->getFromDB($query)) {
			$match->nexthome = $temp[0]->id;
		}
		$query
		->select('m.id')
		->from('#__joomleague_match AS m')
		->leftJoin('#__joomleague_project_team pt1 ON pt1.id = m.projectteam1_id')
		->leftJoin('#__joomleague_project_team pt2 ON pt2.id = m.projectteam2_id')
		->leftJoin('#__joomleague_project AS p ON p.id = pt1.project_id')
		->where('(' .$this->getDateString() . ' < ' .$match_date_utc .')')
		->where('(m.projectteam1_id = ' . $match->projectteam2_id . ' OR m.projectteam2_id = ' . $match->projectteam2_id .')')
		->where('pt1.project_id = ' . $match->project_id )
		->order('m.match_date DESC LIMIT 1');
		if ($temp = $this->getFromDB($query)) {
			$match->lastaway = $temp[0]->id;
		}
		$query
		->select('m.id')
		->from('#__joomleague_match AS m')
		->leftJoin('#__joomleague_project_team pt1 ON pt1.id = m.projectteam1_id')
		->leftJoin('#__joomleague_project_team pt2 ON pt2.id = m.projectteam2_id')
		->leftJoin('#__joomleague_project AS p ON p.id = pt1.project_id')
		->where('(' .$this->getDateString() . ' > ' .$match_date_utc .')')
		->where('(m.projectteam1_id = ' . $match->projectteam2_id . ' OR m.projectteam2_id = ' . $match->projectteam2_id .')')
		->where('pt1.project_id = ' . $match->project_id )
		->order('m.match_date ASC LIMIT 1');
		if ($temp = $this->getFromDB($query)) {
			$match->nextaway = $temp[0]->id;
		}
	}

	public function next_last2(& $match) {
	    $db = Factory :: getDBO();
	    $query = $db->getQuery(true);
		$match->lasthome = $match->nexthome = $match->lastaway = $match->nextaway = false;
        if ($match->match_date)
        {
          $match_date_utc_object = DateTime::createFromFormat('Y-m-d H:i:s', $match->match_date, new DateTimeZone($match->timezone));
          $match_date_utc_object->setTimezone(new DateTimeZone('UTC'));
          $match_date_utc = $match_date_utc_object->format('Y-m-d H:i:s');
        }
		$p = $this->params->get('project');
		if (!empty ($p)) {
			$projectstring = (is_array($p)) ? implode(",", $p) : $p;
			//$this->conditions[] = "((pt1.project_id IN (" . $projectstring . "))";
		}
		$query = $db->getQuery(true);
		$query
		->select('m.id')
		->from('#__joomleague_match AS m')
		->leftJoin('#__joomleague_project_team pt1 ON pt1.id = m.projectteam1_id')
		->innerJoin('#__joomleague_team t1 ON t1.id = pt1.team_id')
		->leftJoin('#__joomleague_project_team pt2 ON pt2.id = m.projectteam2_id')
		->innerJoin('#__joomleague_team t2 ON t2.id = pt2.team_id')
		->leftJoin('#__joomleague_project AS p ON p.id = pt1.project_id')
		->where('(' .$this->getDateString() . ' < ' .$match_date_utc .')')
		->where('(t1.id = ' . $match->team1_id . ' OR t2.id = ' . $match->team1_id .')')
		->where('pt1.project_id IN (' . $projectstring . ')')
		->order('m.match_date DESC LIMIT 1');
		if ($temp = $this->getFromDB($query)) {
			$match->lasthome = $temp[0]->id;
		}
		$query = $db->getQuery(true);
		$query
		      ->select('m.id')
		      ->from('#__joomleague_match AS m')
		      ->leftJoin('#__joomleague_project_team pt1 ON pt1.id = m.projectteam1_id')
		      ->innerJoin('#__joomleague_team t1 ON t1.id = pt1.team_id')
		      ->leftJoin('#__joomleague_project_team pt2 ON pt2.id = m.projectteam2_id')
		      ->innerJoin('#__joomleague_team t2 ON t2.id = pt2.team_id')
		      ->leftJoin('#__joomleague_project AS p ON p.id = pt1.project_id')
		      ->where('(' .$this->getDateString() . ' > ' .$match_date_utc .')')
		      ->where('(t1.id = ' . $match->team1_id . ' OR t2.id = ' . $match->team1_id .')')
		      ->where('pt1.project_id IN (' . $projectstring . ')')
		      ->order('m.match_date ASC LIMIT 1');
		
		if ($temp = $this->getFromDB($query)) {
			$match->nexthome = $temp[0]->id;
		}
		$query = $db->getQuery(true);
		$query
		->select('m.id')
		->from('#__joomleague_match AS m')
		->leftJoin('#__joomleague_project_team pt1 ON pt1.id = m.projectteam1_id')
		->innerJoin('#__joomleague_team t1 ON t1.id = pt1.team_id')
		->leftJoin('#__joomleague_project_team pt2 ON pt2.id = m.projectteam2_id')
		->innerJoin('#__joomleague_team t2 ON t2.id = pt2.team_id')
		->leftJoin('#__joomleague_project AS p ON p.id = pt1.project_id')
		->where('(' .$this->getDateString() . ' < ' .$match_date_utc .')')
		->where('(t1.id = ' . $match->team2_id . ' OR t2.id = ' . $match->team2_id .')')
		->where('pt1.project_id IN (' . $projectstring . ')')
		->order('m.match_date DESC LIMIT 1');
		if ($temp = $this->getFromDB($query)) {
			$match->lastaway = $temp[0]->id;
		}
		$query = $db->getQuery(true);
		$query
		      ->select('m.id')
		      ->from('#__joomleague_match AS m')
		      ->leftJoin('#__joomleague_project_team pt1 ON pt1.id = m.projectteam1_id')
		      ->innerJoin('#__joomleague_team t1 ON t1.id = pt1.team_id')
		      ->leftJoin('#__joomleague_project_team pt2 ON pt2.id = m.projectteam2_id')
		      ->innerJoin('#__joomleague_team t2 ON t2.id = pt2.team_id')
		      ->leftJoin('#__joomleague_project AS p ON p.id = pt1.project_id')
		      ->where('(' .$this->getDateString() . ' > ' .$match_date_utc .')')
              ->where('(t1.id = ' . $match->team2_id . ' OR t2.id = ' . $match->team2_id .')')
		      ->where('pt1.project_id IN (' . $projectstring . ')')
		      ->order('m.match_date ASC LIMIT 1');
		if ($temp = $this->getFromDB($query)) {
			$match->nextaway = $temp[0]->id;
		}
	}
}