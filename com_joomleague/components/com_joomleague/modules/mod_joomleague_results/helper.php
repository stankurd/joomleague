<?php
/**
 * Joomleague
 * @subpackage	Module-Results
 *
 * @copyright	Copyright (C) 2006-2015 joomleague.at. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @link		http://www.joomleague.at
 */
use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;

defined('_JEXEC') or die;


/**
 * Ranking Module helper
 */
class modJLGResultsHelper
{

	/**
	 * Method to get the list
	 *
	 * @access public
	 * @return array
	 */
	public static function getData(&$params)
	{		
		if (!class_exists('JoomleagueModelResults')) {
			require_once JLG_PATH_SITE.'/models/results.php';
		}
		$model = new JoomleagueModelResults;
		$model->setProjectId($params->get('p'));
		
		$project = $model->getProject();
		switch ($params->get('round_selection', 0))
		{
			case 0: // latest
				$roundid = modJLGResultsHelper::getLatestRoundId($project->id);
				break;
			case 1: // next
				$roundid = modJLGResultsHelper::getNextRoundId($project->id);
				break;
			case 2: //manual
				$roundid = ((int) $params->get('r') ? (int) $params->get('r') : $model->getCurrentRound());
				break;
		}
		if (!$roundid) {
			$roundid = $model->getCurrentRound();
		}
		
		$model->set('divisionid',	(int) $params->get('division_id') );
		$model->set('roundid',	    $roundid );
		
		$round   = modJLGResultsHelper::getRound($project->id, $roundid);
		
		$matches = $model->getMatches();
		uasort($matches, array('modJLGResultsHelper', '_cmpDate'));
		$matches = array_slice($matches, 0, $params->get('limit', 10));
		
		$teams   = $model->getTeamsIndexedByPtid();		
		 
		return array(	'project' => $project, 
						'round' => $round, 
						'matches' => $matches, 
						'teams' => $teams, 
						'divisionid' => $params->get('division_id', 0));
	}
	
	public static function sortByDate($matches)
	{
		$sorted = array();
		foreach ($matches as $m)
		{
			$matchDate = JoomleagueHelper::getMatchDate($m);
			$sorted[$matchDate][] = $m;
		}
		return $sorted;
	}
	
	public static function _cmpDate($a, $b)
	{
		$res = 0;
		if ($a->match_date && $b->match_date)
		{
			$res = $a->match_date->toUnix() - $b->match_date->toUnix();
		}
				
		if ($res == 0)
		{
			$res = $a->match_number - $b->match_number;
		}
		
		if ($res == 0)
		{
			$res = $a->id - $b->id;
		}		
		
		return $res;
	}
	
	public static function getRound($project_id, $roundid)
	{
		$db = Factory::getDbo();
		$query =$db->getQuery(true);
		$query
		      ->select('*')
		      ->from('#__joomleague_round')
		      ->where('id = '. $db->Quote($roundid))
		      ->where('project_id = '. $db->Quote($project_id));
		$db->setQuery($query);
		$res = $db->loadObject();
		return $res;
	}
	
	/**
	 * get img for team
	 * @param object ranking row
	 * @param int type = 1 for club small logo, 2 for country
	 * @return html string
	 */
	public static function getLogo($item, $params)
	{
		$type = $params->get('show_logo');
		if ($type == 'country_flag' && !empty($item->country))
		{
			return Countries::getCountryFlag($item->country, 'class="teamcountry"');
		}
		
		//dynamic object property string
		$pic = $params->get('show_picture', 'team_picture');
		return JoomleagueHelper::getPictureThumb($item->$pic,
				$item->name,
				$params->get('team_picture_width'),
				$params->get('team_picture_height'),
				3);
	}

	public static function getTeamLink($item, $params, $project)
	{
		switch ($params->get('teamlink'))
		{
			case 'teaminfo':
				return JoomleagueHelperRoute::getTeamInfoRoute($project->slug, $item->team_slug);
			case 'roster':
				return JoomleagueHelperRoute::getPlayersRoute($project->slug, $item->team_slug);
			case 'teamplan':
				return JoomleagueHelperRoute::getTeamPlanRoute($project->slug, $item->team_slug);
			case 'clubinfo':
				return JoomleagueHelperRoute::getClubInfoRoute($project->slug, $item->club_slug);				
		}
	}
	
	public static function getLatestRoundId($project_id)
	{		
		$db = Factory::getDbo();
		$query =$db->getQuery(true);
		$query
		      ->select('r.id AS roundid')
		      ->select('r.round_date_first')
		      ->from('#__joomleague_round AS r')
		      ->where('project_id = '. $db->Quote($project_id))
		      ->where('DATEDIFF(CURDATE(), CASE WHEN r.round_date_last IS NOT NULL THEN DATE(r.round_date_last) ELSE DATE(r.round_date_first) END) >= 0')
		      ->order('r.round_date_first DESC');
		$db->setQuery($query);
		$res = $db->loadResult();
		return $res;
	}
	

	public static function getNextRoundId($project_id)
	{
		$db = Factory::getDbo();
		$query =$db->getQuery(true);
		$query
		      ->select('r.id AS roundid')
		      ->select('r.round_date_first')
		      ->from('#__joomleague_round AS r')
		      ->where('project_id = '. $db->Quote($project_id))
		      ->where('DATEDIFF(CURDATE(), DATE(r.round_date_first)) < 0')
		      ->order('r.round_date_first ASC');
		$db->setQuery($query);
		$res = $db->loadResult();
		return $res;		
	}
	
	public static function getScoreLink($game, $project)
	{
		if (isset($game->team1_result) || $game->alt_decision)	{
			return JoomleagueHelperRoute::getMatchReportRoute($project->slug, $game->id);
		}
		else {
			return JoomleagueHelperRoute::getNextMatchRoute($project->slug, $game->id);
		}
	}
}