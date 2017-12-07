<?php
/**
 * Joomleague
 * @subpackage	Module-Playgroundplan
 *
 * @copyright	Copyright (C) 2006-2015 joomleague.at. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @link		http://www.joomleague.at
 */
use Joomla\CMS\HTML\HTMLHelper;

defined('_JEXEC') or die;

$teamformat = $params->get ( 'teamformat', 'name' );
$dateformat = $params->get ( 'dateformat' );
$timezone 	= $params->get ( 'time_zone' );
$timeformat = $params->get ( 'timeformat' );
$mode = $params->get ( 'mode', 0 );
$textdiv = "";
$n = 1;
 if ($mode == 0)
echo '<div id="modjlplaygroundplan' . $mode . '">';
 else if ($mode == 1);
//odjlplaygroundplan1
foreach ( $list as $match ) {
	if ($mode == 0) {
		$textdiv .= '<div class="qslidejl">';
	}
	if ($mode == 1) {
		$odd = $n & 1;
		$textdiv .= '<div id="jlplaygroundplanis' . $odd . '" class="jlplaygroundplantextdivlist">';
	}
	$n ++;
	
	if ($params->get ( 'show_playground_name', 0 )) {
		$textdiv .= '<div class="jlplplaneplname"> ';
		if ($match->playground_id != "") {
			$playgroundname = $match->playground_name;
			$playground_id = $match->playground_id;
		} else if ($match->team_playground_id != "") {
			$playgroundname = $match->team_playground_name;
			$playground_id = $match->team_playground_id;
		} elseif ($match->club_playground_id != "") {
			$playgroundname = $match->club_playground_name;
			$playground_id = $match->club_playground_id;
		}
		
		if ($params->get ( 'show_playground_link' )) {
			$link = JoomleagueHelperRoute::getPlaygroundRoute ( $match->project_id, $playground_id );
			$playgroundname = HTMLHelper::link ( $link, JText::sprintf ( '%1$s', $playgroundname ) );
		} else {
			$playgroundname = JText::sprintf ( '%1$s', $playgroundname );
		}
		$textdiv .= $playgroundname . '</div>';
	}
	$textdiv .= '<div class="jlplplanedate">';
	$textdiv .= JoomleagueHelper::getMatchDate($match, $dateformat);
	$textdiv .= " " . JText::_('MOD_JOOMLEAGUE_PLAYGROUNDPLAN_START_TIME')." ";
	$textdiv .= JoomleagueHelper::getMatchTime($match, $timeformat);
	$textdiv .= '</div>';
	if ($params->get ( 'show_project_name', 0 )) {
		$textdiv .= '<div class="jlplplaneleaguename">';
		$textdiv .= $match->project_name;
		$textdiv .= '</div>';
	}
	if ($params->get ( 'show_league_name', 0 )) {
		$textdiv .= '<div class="jlplplaneleaguename">';
		$textdiv .= $match->league_name;
		$textdiv .= '</div>';
	}
	$textdiv .= '<div>';
	$textdiv .= '<div class="jlplplanetname">';
	if ($params->get ( 'show_club_logo' )) {
		$team1logo = modJLGPlaygroundplanHelper::getTeamLogo ( $match->team1 );
		$textdiv .= '<p>' . HTMLHelper::image ( $team1logo, "" ) . '</p>';
	}
	$textdiv .= '<p>' . modJLGPlaygroundplanHelper::getTeams ( $match->team1, $teamformat ) . '</p>';
	$textdiv .= '</div>';
	$textdiv .= '<div class="jlplplanetnamesep"> - </div>';
	$textdiv .= '<div class="jlplplanetname">';
	if ($params->get ( 'show_club_logo' )) {
		$team2logo = modJLGPlaygroundplanHelper::getTeamLogo ( $match->team2 );
		$textdiv .= '<p>' . HTMLHelper::image ( $team2logo, "" ) . '</p>';
	}
	$textdiv .= '<p>' . modJLGPlaygroundplanHelper::getTeams ( $match->team2, $teamformat ) . '</p>';
	$textdiv .= '</div>';
	$textdiv .= '</div>';
	$textdiv .= '<div style="clear:both"></div>';
	$textdiv .= '</div>';
}

echo $textdiv;

echo '</div>';
