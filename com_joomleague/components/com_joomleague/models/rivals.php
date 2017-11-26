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

defined('_JEXEC') or die;

require_once JLG_PATH_SITE.'/models/project.php';

/**
 * Model-Rivals
 */
class JoomleagueModelRivals extends JoomleagueModelProject
{
	var $project = null;
	var $projectid = 0;
	var $teamid = 0;
	var $team = null;

	function __construct( )
	{
		parent::__construct( );
		$app = Factory::getApplication();
		$this->projectid = $app->input->getInt( "p", 0 );
		$this->teamid = $app->input->getInt( "tid", 0 );
		$this->getTeam();
	}

	function getTeam( )
	{
		if ( !isset( $this->team ) )
		{
			if ( $this->teamid > 0 )
			{
				$this->team = $this->getTable('Team', 'Table');
				$this->team->load( $this->teamid );
			}
		}
		return $this->team;
	}

	function getOpponents()
	{
	    $db = Factory::getDbo();
	    $query = $db->getQuery(true);
		$query = ' SELECT m.id ';
		$query .=	' , m.projectteam1_id ';
		$query .=	' , m.projectteam2_id ';
		$query .=	' , pt1.project_id ';
		$query .=	' , pt1.team_id AS team1_id ';
		$query .=	' , pt2.team_id AS team2_id ';
		$query .=	' , pt1.division_id AS division_id ';
		$query .=	' , m.team1_result ';
		$query .=	' , m.team2_result ';
		$query .=	' , m.alt_decision ';
		$query .=	' , m.team1_result_decision ';
		$query .=	' , m.team2_result_decision ';
		$query .=	' , t1.short_name AS short_name1 ';
		$query .=	' , t2.short_name AS short_name2 ';
		$query .=	' , t1.middle_name AS middle_name1 ';
		$query .=	' , t2.middle_name AS middle_name2 ';
		$query .=	' , t1.name AS name1 ';
		$query .=	' , t2.name AS name2 ';
		$query .=	' , t1.club_id AS club1_id ';
		$query .=	' , t2.club_id AS club2_id ';
		$query .=	' FROM #__joomleague_match AS m ';
		$query .=	' INNER JOIN #__joomleague_project_team AS pt1 ON pt1.id = m.projectteam1_id ';
		$query .=	' INNER JOIN #__joomleague_project_team AS pt2 ON pt2.id = m.projectteam2_id ';
		$query .=	' LEFT JOIN #__joomleague_project AS p ON p.id = pt1.project_id ';
		$query .=	' JOIN #__joomleague_team AS t1 ON pt1.team_id=t1.id ';
		$query .=	' JOIN #__joomleague_team AS t2 ON pt2.team_id=t2.id ';
		$query .=	' WHERE m.published = 1 AND p.published=1';
		$query .=	' AND (pt1.team_id = '. $this->_db->Quote($this->teamid) ;
		$query .=	' OR pt2.team_id = '. $this->_db->Quote($this->teamid) . ')' ;
		$query .=	' AND (m.team1_result IS NOT NULL OR m.alt_decision > 0)';
		$query .=	' AND (m.cancel IS NULL OR m.cancel = 0) ';
		$query .=	' ORDER BY m.id ';
		$query .=	';';
		$this->_db->setQuery($query);
		$matches = $this->_db->loadObjectList();

		$opo = array();
		foreach ($matches as $match)
		{
			if(!isset($opo[$match->team2_id])) {
				$opo[$match->team2_id] = array('match'=>0, 'name'=> '', 'g_for'=>0, 'g_aga'=>0, 'win'=>0, 'tie'=>0, 'los'=>0);
			}
			if(!isset($opo[$match->team1_id])) {
				$opo[$match->team1_id] = array('match'=>0, 'name'=> '', 'g_for'=>0, 'g_aga'=>0, 'win'=>0, 'tie'=>0, 'los'=>0);
			}
			if($match->team1_id == $this->teamid)
			{
				$opo[$match->team2_id]['projectteamid']	= $match->projectteam2_id;
				$opo[$match->team2_id]['project_id']	= $match->project_id;
				$opo[$match->team2_id]['division_id']	= $match->division_id;
				$opo[$match->team2_id]['match']			+=1;
				$opo[$match->team2_id]['id']			= $match->team2_id;
				$opo[$match->team2_id]['team_id']		= $match->team2_id;
				$opo[$match->team2_id]['club_id']		= $match->club2_id;
				$opo[$match->team2_id]['name']			= $match->name2;
				$opo[$match->team2_id]['short_name']	= $match->short_name2;
				$opo[$match->team2_id]['middle_name']	= $match->middle_name2;
				$opo[$match->team2_id]['g_for']			+= $match->team1_result;
				$opo[$match->team2_id]['g_aga']			+= $match->team2_result;
				if (!$match->alt_decision)
				{
					if ($match->team1_result > $match->team2_result)
					{
						$opo[$match->team2_id]['win'] += 1;
					}
					else if ($match->team1_result < $match->team2_result)
					{
						$opo[$match->team2_id]['los'] += 1;
					}
					else
					{
						$opo[$match->team2_id]['tie'] += 1;
					}
				}
				else
				{
					if (empty($match->team1_result_decision))
					{
						$opo[$match->team2_id]['forfeit'] += 1;
					}
					else
					{
						if ($match->team1_result_decision > $match->team2_result_decision)
						{
							$opo[$match->team2_id]['win'] += 1;
						}
						else if ($match->team1_result_decision < $match->team2_result_decision)
						{
							$opo[$match->team2_id]['los'] += 1;
						}
						else
						{
							$opo[$match->team2_id]['tie'] += 1;
						}
					}
				}
			}
			else
			{
			 	$opo[$match->team1_id]['projectteamid']	= $match->projectteam1_id;
 				$opo[$match->team1_id]['project_id']	= $match->project_id;
 				$opo[$match->team1_id]['division_id']	= $match->division_id;
 				$opo[$match->team1_id]['id']			= $match->team1_id;
 				$opo[$match->team1_id]['team_id']		= $match->team1_id;
 				$opo[$match->team1_id]['club_id']		= $match->club1_id;
 				$opo[$match->team1_id]['short_name']	= $match->short_name1;
 				$opo[$match->team1_id]['middle_name']	= $match->middle_name1;
 			
				$opo[$match->team1_id]['match'] +=1;
				$opo[$match->team1_id]['name'] = $match->name1;
				$opo[$match->team1_id]['g_for'] += $match->team2_result;
				$opo[$match->team1_id]['g_aga'] += $match->team1_result;
				if (!$match->alt_decision)
				{
					if ($match->team1_result > $match->team2_result)
					{
						$opo[$match->team1_id]['los'] += 1;
					}
					else if ($match->team1_result < $match->team2_result)
					{
						$opo[$match->team1_id]['win'] += 1;
					}
					else
					{
						$opo[$match->team1_id]['tie'] += 1;
					}
				}
				else
				{
					if (empty($match->team2_result_decision))
					{
						$opo[$match->team1_id]['forfeit'] += 1;
					}
					else
					{
						if ($match->team1_result_decision > $match->team2_result_decision)
						{
							$opo[$match->team1_id]['los'] += 1;
						}
						else if ($match->team1_result_decision < $match->team2_result_decision)
						{
							$opo[$match->team1_id]['win'] += 1;
						}
						else
						{
							$opo[$match->team1_id]['tie'] += 1;
						}
					}
				}
			}
		}
		function array_csort() {
			$i=0;
			$args = func_get_args();
			$marray = array_shift($args);
			$msortline = 'return(array_multisort(';
			foreach ($args as $arg) {
				$i++;
				if (is_string($arg)) {
					foreach ($marray as $row) {
						$sortarr[$i][] = $row[$arg];
					}
				}
				else {
					$sortarr[$i] = $arg;
				}
				$msortline .= '$sortarr['.$i.'],';
			}
			$msortline .= '$marray));';
			eval($msortline);
			return $marray;
		}
		$sorted = array();
		if(count($opo)) {
			$sorted = array_csort($opo,'match', SORT_DESC, 'win', SORT_DESC, 'g_for', SORT_DESC);
		}
		return $sorted;

	}
}
