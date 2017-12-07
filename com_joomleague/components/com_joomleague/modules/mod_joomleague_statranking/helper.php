<?php
/**
 * Joomleague
 * @subpackage	Module-Statranking
 *
 * @copyright	Copyright (C) 2006-2015 joomleague.at. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @link		http://www.joomleague.at
 */
use Joomla\CMS\HTML\HTMLHelper;

defined('_JEXEC') or die;


/**
 * Statranking Module helper
 */
abstract class modJLGStatHelper
{

	/**
	 * Method to get the list
	 *
	 * @access public
	 * @return array
	 */
	public static function getData(&$params)
	{
		if (!class_exists('JoomleagueModelStatsranking')) {
			require_once JLG_PATH_SITE.'/models/statsranking.php';
		}
		$divisionid = explode(':', $params->get('division_id', 0));
		$divisionid = $divisionid[0];
		$model = JLGModel::getInstance('statsranking', 'JoomleagueModel');
		$model->setProjectId($params->get('p'));
		$model->teamid = (int)$params->get('tid', 0);
		$model->setStatid($params->get('sid'));
		$model->limit = $params->get('limit');
		$model->limitstart = 0;
		$model->divisionid = $divisionid;
		$project = $model->getProject();
		$stattypes = $model->getProjectUniqueStats();
		$stats = $model->getPlayersStats($params->get('ranking_order', 'DESC'));
		$teams = $model->getTeamsIndexedById();
		
		return array('project' => $project, 'ranking' => $stats, 'teams' => $teams, 'stattypes' => $stattypes);
	}
		
	/**
	 * get img for team
	 * @param object ranking row
	 * @param int type = 1 for club small logo, 2 for country
	 * @return html string
	 */
	public static function getLogo($item, $type = 1)
	{
		if ($type == 1) // club small logo
		{
			if (!empty($item->logo_small))
			{
				return HTMLHelper::image($item->logo_small, $item->short_name, 'class="teamlogo"');
			}
		}		
		else if ($type == 2 && !empty($item->country))
		{
			return Countries::getCountryFlag($item->country, 'class="teamcountry"');
		}
		else if ($type == 3) {
			if (!empty($item->team_picture))
			{
				return HTMLHelper::image($item->team_picture, $item->short_name, 'class="teamlogo"');
			}
		}
		else if ($type == 4) {
			if (!empty($item->projectteam_picture))
			{
				return HTMLHelper::image($item->projecteam_picture, $item->short_name, 'class="teamlogo"');
			}
		}
		return '';
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
	
	public static function printName($item, $team, $params, $project)
	{
				$name = JoomleagueHelper::formatName(null, $item->firstname, 
													$item->nickname, 
													$item->lastname, 
													$params->get("name_format"));
				if ($params->get('show_player_link')) 
				{
					return HTMLHelper::link(JoomleagueHelperRoute::getPlayerRoute($project->slug, $team->team_slug, $item->person_id), $name);	
				}
				else
				{
					echo $name;
				}
				

	}

	public static function getStatIcon($stat)
	{
		if ($stat->icon == 'media/com_joomleague/event_icons/event.gif')
		{
			$txt = JText::_($stat->name);
		}
		else
		{
			$imgTitle=JText::_($stat->name);
			$imgTitle2=array(' title' => $imgTitle, ' alt' => $imgTitle);
			$txt=HTMLHelper::image($stat->icon, $imgTitle, $imgTitle2);
		}
		return $txt;
	}
}