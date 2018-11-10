<?php
/**
 * Joomleague
 * @subpackage	Module-TeamstatsRanking
 *
 * @copyright	Copyright (C) 2006-2015 joomleague.at. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @link		http://www.joomleague.at
 */
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

defined('_JEXEC') or die;


/**
 * TeamstatsRanking Module helper
 */
abstract class modJLGTeamStatHelper
{

	/**
	 * Method to get the list
	 *
	 * @access public
	 * @return array
	 */
	public static function getData(&$params)
	{
		$db = Factory::getDbo();

		if (!class_exists('JoomleagueModelProject')) {
			require_once JLG_PATH_SITE.'/models/project.php';
		}
		$model = new JoomleagueModelProject ();
		$model->setProjectId($params->get('p'));
		$stat_id		= (int)$params->get('sid');
				
		$project = $model->getProject();
		$stat = current(current($model->getProjectStats($stat_id)));
		if (!$stat) {
			echo 'Undefined stat';
		}
		
		$ranking = $stat->getTeamsRanking($project->id, $params->get('limit'), 0, $params->get('ranking_order', 'DESC'));
		if (empty($ranking)) {
			return false;
		}
		
		$ids = array();
		foreach ($ranking as $r)
		{
			$ids[] = $db->Quote($r->team_id);
		}
		$query = $db->getQuery(true);
		$query = ' SELECT t.*, c.logo_small '
				   . '  , CASE WHEN CHAR_LENGTH( t.alias ) THEN CONCAT_WS( \':\', t.id, t.alias ) ELSE t.id END AS team_slug '
				   . '  , CASE WHEN CHAR_LENGTH( c.alias ) THEN CONCAT_WS( \':\', c.id, c.alias ) ELSE c.id END AS club_slug '
		       . ' FROM #__joomleague_team AS t '
		       . ' LEFT JOIN #__joomleague_club AS c ON c.id = t.club_id '
		       . ' WHERE t.id IN ('.implode(',', $ids).')'
		       ;
		$db->setQuery($query);
		$teams = $db->loadObjectList('id');
		
		return array('project' => $project, 'ranking' => $ranking, 'teams' => $teams, 'stat' => $stat);
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

	public static function getStatIcon($stat)
	{
		if ($stat->icon == 'media/com_joomleague/event_icons/event.gif')
		{
			$txt = $stat->name;
		}
		else
		{
			$imgTitle=Text::_($stat->name);
			$imgTitle2=array(' title' => $imgTitle, ' alt' => $imgTitle);
			$txt=HTMLHelper::image($stat->icon, $imgTitle, $imgTitle2);
		}
		return $txt;
	}
}