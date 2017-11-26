<?php
/**
 * @copyright	Copyright (C) 2006-2015 joomleague.at. All rights reserved.
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
 * Model-Teams
 */
class JoomleagueModelTeams extends JoomleagueModelProject
{
	var $projectid = 0;
	var $divisionid = 0;
	var $teamid = 0;
	var $team = null;
	var $club = null;

	function __construct( )
	{
		parent::__construct( );

		$app = Factory::getApplication();
		$input = $app->input;
		
		$this->projectid = $input->getInt('p', 0);
		$this->divisionid = $input->getInt('division', 0);
	}

	// TODO: this function is inherited from the project model, but its signature is different
	// (which is not allowed in PHP (gives warning)). Look into how this can be solved best.
	function getDivision($id = 0)
	{
		$division = null;
		if ($this->divisionid != 0)
		{
			$division = parent::getDivision($this->divisionid);
		}
		return $division;
	}

	// TODO: this function is inherited from the project model, but its signature is different
	// (which is not allowed in PHP (gives warning)). Look into how this can be solved best.
	function getTeams($division = 0)
	{
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		$query
			->select($db->quoteName('pt.id', 'projectteamid'))
			->select($db->quoteName('pt.team_id'))
			->select($db->quoteName('pt.picture', 'projectteam_picture'))
			->select($db->quoteName('pt.project_id'))
			->select($db->quoteName('t.id'))
			->select($db->quoteName('t.name', 'team_name'))
			->select($db->quoteName('t.short_name'))
			->select($db->quoteName('t.middle_name'))
			->select($db->quoteName('t.club_id'))
			->select($db->quoteName('t.website', 'team_www'))
			->select($db->quoteName('t.picture', 'team_picture'))
			->select($db->quoteName('c.name', 'club_name'))
			->select($db->quoteName('c.address', 'club_address'))
			->select($db->quoteName('c.zipcode', 'club_zipcode'))
			->select($db->quoteName('c.state', 'club_state'))
			->select($db->quoteName('c.location', 'club_location'))
			->select($db->quoteName('c.email', 'club_email'))
			->select($db->quoteName('c.logo_big'))
			->select($db->quoteName('c.logo_small'))
			->select($db->quoteName('c.logo_middle'))
			->select($db->quoteName('c.country', 'club_country'))
			->select($db->quoteName('c.website', 'club_www'))
			->select($this->constructSlug($db, 'team_slug', 't.alias', 't.id'))
			->select($this->constructSlug($db, 'club_slug', 'c.alias', 'c.id'))
			->from($db->quoteName('#__joomleague_project_team', 'pt'))
			->join('LEFT', $db->quoteName('#__joomleague_team', 't') .
				' ON ' . $db->quoteName('t.id') . ' = ' . $db->quoteName('pt.team_id'))
			->join('LEFT', $db->quoteName('#__joomleague_club', 'c') .
				' ON ' . $db->quoteName('c.id') . ' = ' . $db->quoteName('t.club_id'))
			->join('LEFT', $db->quoteName('#__joomleague_division', 'd') .
				' ON ' . $db->quoteName('d.id') . ' = ' . $db->quoteName('pt.division_id'))
			->join('LEFT', $db->quoteName('#__joomleague_playground', 'plg') .
				' ON ' . $db->quoteName('plg.id') . ' = ' . $db->quoteName('pt.standard_playground'))
			->where($db->quoteName('pt.project_id') . ' = ' . (int)$this->projectid);

		if ( $this->divisionid > 0 )
		{
			$query
				->where($db->quoteName('pt.division_id') . ' = ' . (int)$this->divisionid);
		}
		$query
			->order($db->quoteName('t.name'));

		$db->setQuery($query);
		if (!$teams = $db->loadObjectList() )
		{
			echo $db->getErrorMsg();
		}

		return $teams;
	}
}
