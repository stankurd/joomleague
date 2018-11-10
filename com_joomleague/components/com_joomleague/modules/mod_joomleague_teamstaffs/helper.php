<?php
/**
 * Joomleague
 * @subpackage	Module-Teamstaffs
 *
 * @copyright	Copyright (C) 2006-2015 joomleague.at. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @link		http://www.joomleague.at
 * 
 * @author		Wolfgang Pinitsch <andone@mfga.at>
 */
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;

defined('_JEXEC') or die;


/**
 * Teamstaffs Module helper
 */
abstract class modJLGTeamStaffsHelper
{

	/**
	 * Method to get the list
	 *
	 * @access public
	 * @return array
	 */
	public static function getData(&$params)
	{
		$p = $params->get('p');
		$p = explode(":", $p);
		$p = $p[0];
		$t = $params->get('team');
		$t = explode(":", $t);
		$t = $t[0];
		$db  = Factory::getDbo();
		$query = $db->getQuery(true);
		$query
		->select('tt.id AS id')
		->select('t.name AS team_name')
		->from('#__joomleague_project_team tt')
		->innerJoin('#__joomleague_team t ON t.id = tt.team_id')
		->where('tt.project_id = '. $p)
		->where('tt.team_id = '. $t);
		/*
		$query = "SELECT tt.id AS id, t.name AS team_name
					FROM #__joomleague_project_team tt
					INNER JOIN #__joomleague_team t ON t.id = tt.team_id
					WHERE tt.project_id = ". $p . "
					AND tt.team_id = ". $t;*/
		$query .= " LIMIT 1";
		$db->setQuery( $query );
		$result = $db->loadRow();
		$projectteamid = $result[0];
		$team_name     = $result[1];
		

		Factory::getApplication()->input->set( 'p', $p );
		Factory::getApplication()->input->set( 'tid', $t);
		Factory::getApplication()->input->set( 'ttid', $projectteamid);

		if (!class_exists('JoomleagueModelRoster')) {
			require_once JLG_PATH_SITE.'/models/roster.php';
		}
		$model = new JoomleagueModelRoster ();
		$model->setProjectId($p);
		$project = $model->getProject();
		$project->team_name = $team_name;
		$staffs = $model->getStaffList();
		$bypos=array();
		foreach ($staffs as $staff)
		{
			if (isset($bypos[$staff->position_id]))
			{
				$bypos[$staff->position_id][]=$staff;
			}
			else
			{
				$bypos[$staff->position_id]= array($staff);
			}
		}
		
		return array('project' => $project, 'staffs' => $bypos);
	}

	public static function getStaffLink($item, $params)
	{
		$flag = "";
		if ($params->get('show_flag')) {
			$flag = Countries::getCountryFlag($item->country) . "&nbsp;";
		}
		$text = "<i>".JoomleagueHelper::formatName(null, $item->firstname, 
													$item->nickname, 
													$item->lastname, 
													$params->get("name_format")) . "</i>";
		if ($params->get('show_staff_link'))
		{
			$link = JoomleagueHelperRoute::getStaffRoute($params->get('p'), 
															$params->get('team'), 
															$item->slug );
			echo $flag . HTMLHelper::link($link, $text);
		}
		else
		{
			echo '<i>' . JText::sprintf( '%1$s', $flag . $text) . '</i>';
		}

	}
}