<?php
/**
 * Joomleague
 * @subpackage	Module-Eventsranking
 *
 * @copyright	Copyright (C) 2006-2015 joomleague.at. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @link		http://www.joomleague.at
 */
use Joomla\CMS\HTML\HTMLHelper;

defined('_JEXEC') or die;

/**
 * Eventsranking Module helper
 */
abstract class modJLGEventsrankingHelper
{

	/**
	 * Method to get the list
	 *
	 * @access public
	 * @return array
	 */
	public static function getData(&$params)
	{
		if (!class_exists('JoomleagueModelEventsRanking')) {
			require_once JLG_PATH_SITE.'/models/eventsranking.php';
		}
		$model = new JoomleagueModelEventsRanking();
		$model->projectid	= modJLGEventsrankingHelper::getId($params, 'p');
		$model->divisionid  = modJLGEventsrankingHelper::getId($params, 'divisionid');
		$model->teamid		= modJLGEventsrankingHelper::getId($params, 'tid');
		$model->setEventid($params->get('evid'));
		$model->matchid		= modJLGEventsrankingHelper::getId($params, 'mid');
		$model->limit		= $params->get('limit');
		$model->limitstart 	= 0;
		$order = $params->get('ranking_order', 'DESC');
		$model->setOrder($order);
		$project = $model->getProject();
		$eventtypes = $model->getEventTypes();
		$events	= $model->getEventRankings($model->limit, $model->limitstart, $order);
		$teams = $model->getTeamsIndexedById();

		return array('project' => $project, 'ranking' => $events, 'eventtypes' => $eventtypes, 'teams' => $teams, 'model' => $model);
	}

	/**
	 * get id from the module configuration parameters
	 * (the parameter can either be the id by itself or a complete slug).
	 * @param object configuration parameters for the module
	 * @param string name of the configuration parameter
	 * @return id string for the requested parameter (e.g. project id or statistics id)
	 */
	public static function getId($params, $paramName)
	{
		$id = $params->get($paramName);
		preg_match('/(?P<id>\d+):.*/', $id, $matches);
		if (array_key_exists('id', $matches))
		{
			$id = $matches['id'];
		}
		return $id;
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
		
		return '';
	}

	public static function getTeamLink($team, $params, $project)
	{
		switch ($params->get('teamlink'))
		{
			case 'teaminfo':
				return JoomleagueHelperRoute::getTeamInfoRoute($project->slug, $team->team_slug);
			case 'roster':
				return JoomleagueHelperRoute::getPlayersRoute($project->slug, $team->team_slug);
			case 'teamplan':
				return JoomleagueHelperRoute::getTeamPlanRoute($project->slug, $team->team_slug);
			case 'clubinfo':
				return JoomleagueHelperRoute::getClubInfoRoute($project->slug, $team->club_slug);
				
		}
	}
	
	public static function printName($item, $team, $params, $project)
	{
		$name = JoomleagueHelper::formatName(null, $item->fname, $item->nname, $item->lname, $params->get("name_format"));
		if ($params->get('show_player_link'))
		{
			return HTMLHelper::link(JoomleagueHelperRoute::getPlayerRoute($project->slug, $team->team_slug, $item->pid), $name);
		}
		else
		{
			echo $name;
		}
	}

	public static function getEventIcon($event)
	{
		if ($event->icon == 'media/com_joomleague/event_icons/event.gif')
		{
			$txt = $event->name;
		}
		else
		{
			$imgTitle=JText::_($event->name);
			$imgTitle2=array(' title' => $imgTitle, ' alt' => $imgTitle);
			$txt=HTMLHelper::image($event->icon, $imgTitle, $imgTitle2);
		}
		return $txt;
	}
}
