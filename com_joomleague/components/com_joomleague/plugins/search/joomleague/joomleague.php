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

use Joomla\CMS\Factory;
use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\CMS\Plugin\PluginHelper;

defined('_JEXEC') or die;

// Import library dependencies
jimport('joomla.plugin.plugin');

class plgSearchJoomleague extends CMSPlugin {

	public function plgSearchJoomleague(&$subject, $params) {
		parent::__construct($subject, $params);
		// load language file for frontend
		CMSPlugin::loadLanguage('plg_joomleague_search', JPATH_ADMINISTRATOR);
	}

	function onContentSearchAreas() {
		static $areas = array('joomleague' => 'JoomLeague');
		return $areas;
	}

	function onContentSearch($text, $phrase = '', $ordering = '', $areas = null) {
		$db 	= Factory::getDbo();
		require_once JPATH_SITE.'/components/com_joomleague/joomleague.core.php';

		// load plugin params info
		$plugin 				= PluginHelper::getPlugin('search', 'joomleague');
		$search_clubs 			= $this->params->def('search_clubs', 1);
		$search_teams 			= $this->params->def('search_teams', 1);
		$search_players 		= $this->params->def('search_players', 1);
		$search_playgrounds 	= $this->params->def('search_playgrounds', 1);
		$search_staffs		 	= $this->params->def('search_staffs', 1);
		$search_referees 		= $this->params->def('search_referees', 1);
		$search_projects 		= $this->params->def('search_projects', 1);
		$text = trim($text);
		if ($text == '') {
			return array();
		}

		$wheres = array();

		switch ($phrase) {

		case 'any':
		default:
			$words = explode(' ', $text);
			$wheres = array();
			$wheresteam = array();
			$whereperson = array();
			$whereplayground = array();
			$whereproject = array();

			if ($search_clubs) {
				foreach ($words as $word) {
					$word = $db->Quote('%' . $db->escape($word, true) . '%', false);
					$wheres2 = array();
					$wheres2[] = 'c.name LIKE ' . $word;
					$wheres2[] = 'c.alias LIKE ' . $word;
					$wheres2[] = 'c.location LIKE ' . $word;

					$wheres[] = implode(' OR ', $wheres2);
				}
			}

			if ($search_teams) {
				foreach ($words as $word) {
					$word = $db->Quote('%' . $db->escape($word, true) . '%', false);
					$wheres2 = array();
					$wheres2[] = 't.name LIKE ' . $word;
					$wheresteam[] = implode(' OR ', $wheres2);
				}
			}

			if ($search_players || $search_referees || $search_staffs) {
				foreach ($words as $word) {
					$word = $db->Quote('%' . $db->escape($word, true) . '%', false);
					$wheres2 = array();
					$wheres2[] = 'pe.firstname LIKE ' . $word;
					$wheres2[] = 'pe.lastname LIKE ' . $word;
					$wheres2[] = 'pe.nickname LIKE ' . $word;
					$whereperson[] = implode(' OR ', $wheres2);
				}
			}

			if ($search_playgrounds) {
				foreach ($words as $word) {
					$word = $db->Quote('%' . $db->escape($word, true) . '%', false);
					$wheres2 = array();
					$wheres2[] = 'pl.name LIKE ' . $word;
					$wheres2[] = 'pl.city LIKE ' . $word;

					$whereplayground[] = implode(' OR ', $wheres2);
				}
			}

			if ($search_projects) {
				foreach ($words as $word) {
					$word = $db->Quote('%' . $db->escape($word, true) . '%', false);
					$wheres2 = array();
					$wheres2[] = 'prj.name LIKE ' . $word;

					$whereproject[] = implode(' OR ', $wheres2);
				}
			}

			$where 				= '(' . implode(($phrase == 'all' ? ') AND (' : ') OR ('), $wheres) . ')';
			$whereteam 			= '(' . implode(($phrase == 'all' ? ') AND (' : ') OR ('), $wheresteam) . ')';
			$whereperson 		= '(' . implode(($phrase == 'all' ? ') AND (' : ') OR ('), $whereperson) . ')';
			$whereplayground 	= '(' . implode(($phrase == 'all' ? ') AND (' : ') OR ('), $whereplayground) . ')';
			$whereproject 		= '(' . implode(($phrase == 'all' ? ') AND (' : ') OR ('), $whereproject) . ')';
			break;

		}

		$rows = array();

		if ($search_clubs) {
			$query = "SELECT 'Club' as section, c.name AS title," . 
					 " c.founded AS created," . " c.country," . 
					 " c.logo_big AS picture," .
					 " CONCAT( ' Address: ',c.address,' ',c.zipcode,' ',c.location,' Phone: ',c.phone,' Fax: ',c.fax,' E-Mail: ',c.email) AS text," .
					 " pt.project_id AS project_id, c.id AS club_id, " . 
					 " '' AS href," . 
					 " '2' AS browsernav " .
					 " FROM #__joomleague_club AS c" .
					 " LEFT JOIN #__joomleague_team AS t ON c.id = t.club_id" .
					 " LEFT JOIN #__joomleague_project_team AS pt ON pt.team_id = t.id" .
					 " WHERE ( " . $where . " ) " . 
					 " GROUP BY c.name ORDER BY c.name";

			$db->setQuery($query);
			$list = $db->loadObjectList();
			for ($i = 0; $i < count($list); $i++) {
				$list[$i]->href = JoomLeagueHelperRoute::getClubInfoRoute($list[$i]->project_id, $list[$i]->club_id);
			}
			$rows[] = $list;
		}

		if ($search_teams) {
			$query = "SELECT 'Team' as section, t.name AS title," . 
					" t.checked_out_time AS created, t.notes AS text, t.id AS team_id, ".
	 				" CONCAT( ' Info: ',t.info , ' Notes: ', t.notes ) AS text," . 
	 				" pt.project_id AS project_id, " . " pt.picture AS picture, " .
	 				" '' AS href, c.country AS country, " . 
	 				" '2' AS browsernav" .
	 				" FROM #__joomleague_team AS t " .
					" LEFT JOIN #__joomleague_project_team AS pt ON pt.team_id = t.id" .
					" LEFT JOIN #__joomleague_club AS c ON t.club_id = c.id" .
					" WHERE ( " . $whereteam . " ) " .
					" GROUP BY t.name ORDER BY t.name";

			$db->setQuery($query);
			$list = $db->loadObjectList();
			for ($i = 0; $i < count($list); $i++) {
				$list[$i]->href = JoomLeagueHelperRoute::getTeamInfoRoute($list[$i]->project_id, $list[$i]->team_id);
			}
			$rows[] = $list;
		}

		if ($search_players) {

			$query = "SELECT 'Person' as section, REPLACE(CONCAT(pe.firstname, ' \'', pe.nickname, '\' ' , pe.lastname ),'\'\'','') AS title," . 
					" pe.birthday AS created," . " pe.country," . " pe.picture AS picture, " . 
					" CONCAT( ' Birthday:',pe.birthday , ' Notes:', pe.notes ) AS text," .
					" pt.project_id AS project_id, pt.team_id as team_id, pe.id as person_id, " . 
					" '' AS href," . " '2' AS browsernav" .
					" FROM #__joomleague_person AS pe" .
					" LEFT JOIN #__joomleague_team_player AS tp" .
					" ON tp.person_id = pe.id" .
					" LEFT JOIN #__joomleague_project_team AS pt ON pt.id = tp.projectteam_id" . 
					" WHERE ( " . $whereperson . " ) " . " AND pe.published = '1' " .
					" GROUP BY pe.lastname, pe.firstname, pe.nickname ".
					" ORDER BY pe.lastname,pe.firstname,pe.nickname";

			$db->setQuery($query);
			$list = $db->loadObjectList();
			for ($i = 0; $i < count($list); $i++) {
				$list[$i]->href = JoomLeagueHelperRoute::getPlayerRoute($list[$i]->project_id, $list[$i]->team_id, $list[$i]->person_id);
			}
			$rows[] = $list;

		}

		if ($search_staffs) {

			$query = "SELECT 'Staff' as section, REPLACE(CONCAT(pe.firstname, ' \'', pe.nickname, '\' ' , pe.lastname ),'\'\'','') AS title," . 
					" pe.birthday AS created," . " pe.country," . " pe.picture AS picture, " . 
					" CONCAT( ' Birthday:',pe.birthday , ' Notes:', pe.notes ) AS text," .
					" pt.project_id AS project_id, pt.team_id as team_id, pe.id as person_id, " . 
					" '' AS href," . " '2' AS browsernav" .
					" FROM #__joomleague_person AS pe" .
					" LEFT JOIN #__joomleague_team_staff AS ts" .
					" ON ts.person_id = pe.id " .
					" LEFT JOIN #__joomleague_project_team AS pt" . " ON pt.id = ts.projectteam_id" .
					" WHERE ( " . $whereperson . " ) " .
					" AND pe.published = '1' " .
					" GROUP BY pe.lastname, pe.firstname, pe.nickname ".
					" ORDER BY pe.lastname,pe.firstname,pe.nickname";

			$db->setQuery($query);
			$list = $db->loadObjectList();
			for ($i = 0; $i < count($list); $i++) {
				$list[$i]->href = JoomLeagueHelperRoute::getStaffRoute($list[$i]->project_id, $list[$i]->team_id, $list[$i]->person_id);
			}
			$rows[] = $list;

		}

		if ($search_referees) {

			$query = "SELECT 'Referee' as section, REPLACE(CONCAT(pe.firstname, ' \'', pe.nickname, '\' ' , pe.lastname ),'\'\'','') AS title," . " pe.birthday AS created," . " pe.country," . " pe.picture AS picture, " . " CONCAT( 'Birthday:', pe.birthday, ' Notes:', pe.notes ) AS text,".
					" pr.project_id AS project_id, pe.id as person_id, " . 
					" ''AS href," . " '2' AS browsernav" .
					" FROM #__joomleague_person AS pe" .
					" LEFT JOIN #__joomleague_project_referee AS pr" . " ON pr.person_id = pe.id".
					" WHERE ( " . $whereperson . " ) " .
					" AND pe.published = '1' " .
					" AND pr.published = '1' " .
					" GROUP BY pe.lastname, pe.firstname, pe.nickname ".
					" ORDER BY pe.lastname,pe.firstname,pe.nickname";

			$db->setQuery($query);
			$list = $db->loadObjectList();
			for ($i = 0; $i < count($list); $i++) {
				$list[$i]->href = JoomLeagueHelperRoute::getRefereeRoute($list[$i]->project_id, $list[$i]->team_id, $list[$i]->person_id);
			}
			$rows[] = $list;
		}

		if ($search_playgrounds) {

			$query = "SELECT 'Playground' as section, pl.name AS title," . " pl.checked_out_time AS created," . 
					" pl.country," . " pl.picture AS picture, " . " pl.notes AS text," . 
					" r.project_id AS project_id, pl.id as playground_id, " . 
					" '' AS href," . " '2' AS browsernav" .
					" FROM #__joomleague_playground AS pl" .
					" LEFT JOIN #__joomleague_club AS c" . " ON c.id = pl.club_id" .
					" LEFT JOIN #__joomleague_match AS m" . " ON m.playground_id = pl.id".
					" LEFT JOIN #__joomleague_round AS r" . " ON m.round_id = r.id" .
					" WHERE ( " . $whereplayground . " ) " . 
					" GROUP BY pl.name ORDER BY pl.name ";

			$db->setQuery($query);
			$list = $db->loadObjectList();
			for ($i = 0; $i < count($list); $i++) {
				$list[$i]->href = JoomLeagueHelperRoute::getPlaygroundRoute($list[$i]->project_id, $list[$i]->playgroundid);
			}
			$rows[] = $list;
		}

		if ($search_projects) {

			$query = "SELECT 'Project' as section, prj.name AS title," . " prj.checked_out_time AS created," . 
					" l.country," . " prj.picture AS picture, " .
					" prj.id AS project_id, " . 
					" '' AS href," . " '2' AS browsernav" .
					" FROM #__joomleague_project AS prj" .
					" LEFT JOIN #__joomleague_league AS l" . " ON l.id = prj.league_id" .
					" WHERE ( " . $whereproject . " ) " .
					" GROUP BY prj.name ";
					" ORDER BY prj.name ";

			$db->setQuery($query);
			$list = $db->loadObjectList();
			for ($i = 0; $i < count($list); $i++) {
				$list[$i]->href = JoomLeagueHelperRoute::getRankingRoute($list[$i]->project_id);
			}
			$rows[] = $list;
		}

		$results = array();

		if (count($rows)) {
			foreach ($rows as $row) {
				if ($row) {
					foreach ($row as $output) {
						if ($output->country) {
							$flag = Countries::getCountryFlag($output->country);
							$output->flag = $flag;
							$output->text = $flag . ' ' . $output->text;
						}
						if ($output->picture) {
							$output->text = '<p><img style="float: left;" src="' . $output->picture . '" alt="" width="50" height="" >' . $output->text . '</p>';
						}
					}
				}
			}

			foreach ($rows as $row) {
				$results = array_merge($results, (array) $row);
			}
		}
		return $results;
	}
}
?>