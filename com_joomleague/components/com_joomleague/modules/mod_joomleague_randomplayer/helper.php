<?php
/**
 * Joomleague
 * @subpackage	Module-Randomplayer
 *
 * @copyright	Copyright (C) 2006-2015 joomleague.at. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @link		http://www.joomleague.at
 */
use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;

defined('_JEXEC') or die;


/**
 * Randomplayer Module helper
 */
class modJLGRandomplayerHelper
{

	/**
	 * Method to get the list
	 *
	 * @access public
	 * @return array
	 */
	public static function getData(&$params)
	{
		// catch variables
		$usedp 			= $params->get('projects'); // required
		$usedtid 		= $params->get('teams', '0'); // not required
		
		// convert to strings
		$projectstring	= (is_array($usedp)) ? implode(",", $usedp) : $usedp;
		$teamstring		= (is_array($usedtid)) ? implode(",", $usedtid) : $usedtid;
		
		// Project-teamids
		$db  = Factory::getDbo();
		$query = $db->getQuery(true);
		$query->select('id');
		$query->from('#__joomleague_project_team tt');
		$query->where('tt.project_id > 0');

		if($projectstring != "" && $projectstring > 0) {
			$query->where('tt.project_id IN ('. $projectstring .')');
		}
		if($teamstring != "" && $teamstring > 0) {
			$query->where('tt.team_id IN ('. $teamstring .')');
		}
		$db->setQuery( $query );
		$projectteamids = $db->loadColumn();
		
		// At this point we should have project-teamids //
		if (empty($projectteamids)) {
			return false;
		}
		
		$shuffleKeys = array_keys($projectteamids);
		shuffle($shuffleKeys);
		$projectidsarray = array();
		foreach($shuffleKeys as $key) {
    		$projectidsarray[$key] = $projectteamids[$key];
		}

		
		// Get person data that can be displayed
		foreach ($projectidsarray AS $projectteamid) {
			# at this point we do have a project-teamid
			
			// Retrieve persons related to a projectteam
			$db  = Factory::getDbo();
			$query = $db->getQuery(true);
			$query->select(array('pt.person_id', 'tt.project_id'));
			$query->from('#__joomleague_team_player AS pt');
			$query->join('INNER', '#__joomleague_project_team AS tt ON tt.id = pt.projectteam_id');
			$query->where('pt.projectteam_id = '.$projectteamid);
			$db->setQuery($query);
			$result = $db->loadObjectList();
			
			if ($result) {
				break;
			} else {
				continue;
			}
		}
		
		if (empty($result)) {
			return false;
		}
		
		# shuffle results
		$key = array_rand($result);
		$result = $result[$key];
				
		// Setting variables
		Factory :: getApplication()->input->set('p', $result->project_id); 		// projectid
		Factory :: getApplication()->input->set('pid', $result->person_id); 	// personid
		Factory :: getApplication()->input->set('pt', $projectteamid);          // project-team

		if (!class_exists('JoomleagueModelPlayer')) {
			require_once JLG_PATH_SITE.'/models/player.php';
		}

		// Player model + info
		$mdlPerson 	= new JoomleagueModelPlayer();
		$person 	= $mdlPerson->getPerson();
		$project	= $mdlPerson->getProject();
		$current_round = isset($project->current_round) ? $project->current_round : 0;
		$person_id	= isset($person->id) ? $person->id : 0;
		$player		= $mdlPerson->getTeamPlayerByRound($current_round, $person_id);
		$infoteam	= $mdlPerson->getTeaminfo($projectteamid);
		
		$data 	= array(
				'project'		=> $project,
				'player'		=> $person,
				'inprojectinfo'	=> !empty($player) ? $player[0] : array(),
				'infoteam'		=> $infoteam
		);
		

		return $data;
	}
}