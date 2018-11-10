<?php
/**
 * Joomleague
 * @subpackage	Module-Logo
 *
 * @copyright	Copyright (C) 2006-2015 joomleague.at. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @link		http://www.joomleague.at
 */
use Joomla\CMS\HTML\HTMLHelper;

defined('_JEXEC') or die;

/**
 * Logo Module helper
 */
class modJLGLogoHelper
{

	/**
	 * Method to get the list
	 *
	 * @access public
	 * @return array
	 */
	public static function getData(&$params)
	{
		// @todo check! // 23-07-2015
		// we are included model Teams but is it needed?
		if (!class_exists('JoomleagueModelTeams')) {
			require_once JLG_PATH_SITE.'/models/teams.php';
		}
		$model = new JoomleagueModelTeams();
		$model->setProjectId($params->get('p'));
		
		$project = $model->getProject();
		$division_id = $params->get('division_id');
		$division_id = explode(":", $division_id);
		$division_id = $division_id[0];
		$model->divisionid = $division_id;
		
		$data = array('project' => $project, 'teams' => $model->getTeams()); 
		
		return $data;

	}
	
	/**
	 * get img for team
	 * @param object teams row
	 * @param int type = 0 for club small logo, 1 for medium logo, 2 for big logo
	 * @return html string
	 */
	public static function getLogo($item, $type = 1)
	{
		if ($type == 0) // club small logo
		{
			$picture = $item->logo_small;
			if ( ( is_null( $item->logo_small ) ) || ( !file_exists( $picture ) ) )
			{
				$picture = JoomleagueHelper::getDefaultPlaceholder('clublogosmall'); 
			}
			echo HTMLHelper::image($picture, $item->team_name,'class="logo_small" title="View '.$item->team_name.'"');
				
		}
		else if ($type == 1) // club medium logo
		{
			$picture = $item->logo_middle;
			if ( ( is_null( $item->logo_middle ) ) || ( !file_exists( $picture ) ) )
			{
				$picture = JoomleagueHelper::getDefaultPlaceholder('clublogomedium'); 
			}
			echo HTMLHelper::image($picture, $item->team_name,'class="logo_middle" title="View '.$item->team_name.'"');

		}
		else if ($type == 2 ) // club big logo
		{
			$picture = $item->logo_big;
			if ( ( is_null( $item->logo_big ) ) || ( !file_exists( $picture ) ) )
			{
				$picture = JoomleagueHelper::getDefaultPlaceholder('clublogobig'); 
			}
			echo HTMLHelper::image($picture, $item->team_name,'class="logo_big" title="View '.$item->team_name.'"');
		}
		else if ($type == 3 ) // team logo
		{
			$picture = $item->team_picture;
			if ( ( is_null( $item->team_picture ) ) || ( !file_exists( $picture ) ) )
			{
				$picture = JoomleagueHelper::getDefaultPlaceholder('team');
			}
			echo HTMLHelper::image($picture, $item->team_name,'class="team_picture" title="View '.$item->team_name.'"');
		}
		else if ($type == 4 ) // projectteam logo
		{
			$picture = $item->projectteam_picture;
			if ( ( is_null( $item->projectteam_picture ) ) || ( !file_exists( $picture ) ) )
			{
				$picture = JoomleagueHelper::getDefaultPlaceholder('team'); 
			}
			echo HTMLHelper::image($picture, $item->team_name,'class="projecteam_picture" title="View '.$item->team_name.'"');
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
			case 'teamwww':
				return $item->team_www;
			case 'clubwww':
				return $item->club_www;
		}
	}
}