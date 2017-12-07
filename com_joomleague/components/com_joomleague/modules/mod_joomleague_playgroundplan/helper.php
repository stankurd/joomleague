<?php
/**
 * Joomleague
 * @subpackage	Module-Playgroundplan
 *
 * @copyright	Copyright (C) 2006-2015 joomleague.at. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @link		http://www.joomleague.at
 */
use Joomla\CMS\Factory;

defined('_JEXEC') or die;

/**
 * Playgroundplan Module helper
 */
abstract class modJLGPlaygroundplanHelper
{

	/**
	 * Method to get the list
	 *
	 * @access public
	 * @return array
	 */
	public static function getData(&$params)
	{
		$usedp = $params->get('projects','0');
		$usedpid = $params->get('playground', '0');
		$projectstring = (is_array($usedp)) ? implode(",", $usedp) : $usedp;
		$playgroundstring = (is_array($usedpid)) ? implode(",", $usedpid) : $usedpid;
		$numberofmatches = $params->get('maxmatches','5');

		$db  = Factory::getDbo();
		$result = array();
		$query =$db->getQuery(true);
		
		$query = 'SELECT  m.match_date, DATE_FORMAT(m.time_present, "%H:%i") time_present,
                          p.name AS project_name, p.id AS project_id, p.timezone, 
				 tj1.team_id team1, tj2.team_id team2, lg.name AS league_name,
			     plcd.id AS club_playground_id, 
			     plcd.name AS club_playground_name,
			     pltd.id AS team_playground_id, 
			     pltd.name AS team_playground_name, 
			     pl.id AS playground_id, 
			     pl.name AS playground_name,
			     t1.name AS team1_name,
			     t2.name AS team2_name
                      FROM #__joomleague_match AS m
                      INNER JOIN #__joomleague_project_team tj1 ON tj1.id = m.projectteam1_id 
                      INNER JOIN #__joomleague_project_team tj2 ON tj2.id = m.projectteam2_id 
                      INNER JOIN #__joomleague_project AS p ON p.id=tj1.project_id
                      INNER JOIN #__joomleague_team t1 ON t1.id = tj1.team_id
		      INNER JOIN #__joomleague_team t2 ON t2.id = tj2.team_id
                      INNER JOIN #__joomleague_club c ON c.id = t1.club_id
		      INNER JOIN #__joomleague_league lg ON lg.id = p.league_id
		      LEFT JOIN #__joomleague_playground AS plcd ON c.standard_playground = plcd.id
		      LEFT JOIN #__joomleague_playground AS pltd ON tj1.standard_playground = pltd.id 
		      LEFT JOIN #__joomleague_playground AS pl ON m.playground_id = pl.id

                      WHERE (m.playground_id IN ('. $playgroundstring .')
                          OR (tj1.standard_playground IN ('. $playgroundstring .') AND m.playground_id IS NULL)
                          OR (c.standard_playground IN ('. $playgroundstring .') AND (m.playground_id IS NULL AND tj1.standard_playground IS NULL )))
                      AND m.match_date > NOW()
                      AND m.published = 1
                      AND p.published = 1';


		if ($projectstring != 0)
		{
			$query .= ' AND p.id IN ('. $projectstring .')';
		}

		$query .= " ORDER BY m.match_date ASC LIMIT ".$numberofmatches;

			
		$db->setQuery($query);
		$info=$db->loadObjectList();
		
		if ($info)
		{
			foreach ($info as $match)
			{
				JoomleagueHelper::convertMatchDateToTimezone($match);
			}
		}
		return $info;
	}

	public static function getTeams( $team1_id, $teamformat)
	{
	    $db  = Factory::getDbo();
	    $query =$db->getQuery(true);

		$query = "SELECT ". $teamformat. "
                 FROM #__joomleague_team
                 WHERE id=".(int)$team1_id;
		$db->setQuery( $query );
		$team_name = $db->loadResult();

		return $team_name;
	}

	public static function getTeamLogo($team_id)
	{
	    $db  = Factory::getDbo();
	    $query =$db->getQuery(true);
	    
		$query = "
            SELECT c.logo_small
            FROM #__joomleague_team t
            LEFT JOIN #__joomleague_club c ON c.id = t.club_id
            WHERE t.id = ".$team_id;

		$db->setQuery( $query );
		$club_logo = $db->loadResult();

		if ($club_logo == '') {
			$club_logo= JoomleagueHelper::getDefaultPlaceholder('clublogosmall');
		}
		return $club_logo;
	}
}