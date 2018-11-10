<?php
/**
 * Joomleague
 * @subpackage	Module-SportstypeStatistics
 *
 * @copyright	Copyright (C) 2006-2015 joomleague.at. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @link		http://www.joomleague.at
 */
defined('_JEXEC') or die;

/**
 * SportstypeStatistics Module helper
 */
abstract class modJLGSportsHelper
{

	/**
	 * Method to get the list
	 *
	 * @access public
	 * @return array
	 */
	public static function getData(&$params)
	{
		if (!class_exists('JoomleagueModelSportsType')) {
			require_once JLG_PATH_SITE.'/models/sportstype.php';
		}
		$model = new JoomleagueModelSportsType ();
		$model->setId($params->get('sportstypes'));
		
		return array(
			'sportstype' => $model->getData(),
			'projectscount' => $model->getProjectsCount(), 
			'leaguescount' => $model->getLeaguesCount(), 
			'seasonscount' => $model->getSeasonsCount(), 
			'projectteamscount' => $model->getProjectTeamsCount(),
			'projectteamsplayerscount' => $model->getProjectTeamsPlayersCount(),
			'projectdivisionscount' => $model->getProjectDivisionsCount(),
			'projectroundscount' => $model->getProjectRoundsCount(),
			'projectmatchescount' => $model->getProjectMatchesCount(),
			'projectmatcheseventscount' => $model->getProjectMatchesEventsCount(),
			'projectmatcheseventnames' => $model->getProjectMatchesEventNames(),
			'projectmatchesstatscount' => $model->getProjectMatchesStatsCount(),
		);
	}
}