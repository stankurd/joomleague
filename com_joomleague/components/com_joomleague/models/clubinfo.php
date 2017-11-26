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
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\Utilities\ArrayHelper;
require_once JLG_PATH_SITE.'/models/project.php';

/**
 * Clubinfo model
 */
class JoomleagueModelClubInfo extends JoomleagueModelProject
{
	var $projectid = 0;
	var $clubid = 0;
	var $club = null;

	public function __construct()
	{
		parent::__construct();

		$app = Factory::getApplication();
		$input = $app->input;
		
		$this->projectid = $input->getInt('p',0);
		$this->clubid = $input->getInt('cid',0);
	}

	/**
	 * Get the club information from the database
	 */
	function getClub()
	{
		if (is_null($this->club))
		{
			if ($this->clubid > 0)
			{
				$db = Factory::getDbo();
				$query = $db->getQuery(true);
				$query
					->select('*')
					->from($db->quoteName('#__joomleague_club'))
					->where($db->quoteName('id') . ' = ' . $db->quote($this->clubid));
				$db->setQuery($query);
				$this->club = $db->loadObject();
			}
		}
		return $this->club;
	}

	/**
	 * Get the teams of a club and per team find the latest project it is involved in.
	 */
	function getTeamsByClubId()
	{
		$teams = array();
		if ($this->clubid > 0 )
		{
			$db = Factory::getDbo();
//			$subQuery = $db->getQuery(true);
//			$subQuery
//				->select('MAX(' . $db->quoteName('project_id') . ')')
//				->from($db->quoteName('#__joomleague_project_team'))
//				->join('INNER', $db->quoteName('#__joomleague_team', 't') .
//					' ON ' . $db->quoteName('t.id') . ' = ' . $db->quoteName('team_id'))
//				->join('RIGHT', $db->quoteName('#__joomleague_project', 'p') .
//					' ON ' . $db->quoteName('project_id') . ' = ' . $db->quoteName('p.id'))
//				->where('p.published = 1');

			$query = $db->getQuery(true);
			$query
				->select($db->quoteName('id'))
				->select($this->constructSlug($db))
				->select($db->quoteName('name', 'team_name'))
				->select($db->quoteName('short_name', 'team_shortcut'))
				->select($db->quoteName('info', 'team_description'))
				// TODO: find out how to best work with subqueries
//					->select('(' . $subQuery . ') as pid')
				->select('COALESCE((SELECT MAX(project_id)
							FROM #__joomleague_project_team
							RIGHT JOIN #__joomleague_project p on project_id=p.id
							WHERE team_id=t.id and p.published = 1), 0) as pid')
				->from($db->quoteName('#__joomleague_team', 't'))
				->where($db->quoteName('club_id') . ' = ' . (int) $this->clubid)
				->order($db->quoteName('t.ordering'));

			$db->setQuery($query);
			$teams = $db->loadObjectList();
		}
		return $teams;
	}

	/**
	 * Get IDs for the playgrounds that are used by the club and its teams in projects.
	 */
	function getPlaygroundIds()
	{
		$playgroundIds = array();

		$club = $this->getClub();
		if (!empty($club))
		{
			$db = Factory::getDbo();
			$query = $db->getQuery(true);
			$query
				->select('DISTINCT(pt.standard_playground)')
				->from($db->quoteName('#__joomleague_project_team', 'pt'))
				->join('INNER', $db->quoteName('#__joomleague_team', 't') .
					' ON ' . $db->quoteName('t.id') . ' = ' . $db->quoteName('team_id'))
				->join('INNER', $db->quoteName('#__joomleague_club', 'c') .
					' ON '. $db->quoteName('c.id') . ' = ' . $db->quoteName('t.club_id'))
				->where($db->quoteName('c.id') . ' = ' . $db->quote($club->id))
				->where($db->quoteName('pt.standard_playground') . ' > 0');
			if ($club->standard_playground > 0)
			{
				$playgroundIds[] = $club->standard_playground;
				$query->where($db->quoteName('pt.standard_playground') . ' != ' . $db->quote($club->standard_playground));
			}
			$db->setQuery($query);
			$projectTeamPlaygroundIds = $db->loadColumn();
			if (!empty($projectTeamPlaygroundIds))
			{
				$playgroundIds = array_merge($playgroundIds, $projectTeamPlaygroundIds);
			}
		}
		return $playgroundIds;
	}

	/**
	 * @see JoomleagueModelProject::getPlaygrounds()
	 */
	function getPlaygrounds()
	{
		$playgrounds = array();

		$playgroundIds = $this->getPlaygroundIds();
		if (!empty($playgroundIds))
		{
			$db = Factory::getDbo();
			$query = $db->getQuery(true);
			$query
				->select('name')
				->select($this->constructSlug($db))
				->from($db->quoteName('#__joomleague_playground'))
				->where($db->quoteName('id') . ' IN (' . implode(',', ArrayHelper::toInteger($playgroundIds)) .')');

			$db->setQuery($query);
			$playgrounds = $db->loadObjectList();
		}
		return $playgrounds;
	}

	/**
	 * 
	 */
	function getAddressString()
	{
		$club = $this->getClub();
		if ( !isset ( $club ) ) { return null; }

		$address_parts = array();
		if (!empty($club->address))
		{
			$address_parts[] = $club->address;
		}
		if (!empty($club->state))
		{
			$address_parts[] = $club->state;
		}
		if (!empty($club->location))
		{
			if (!empty($club->zipcode))
			{
				$address_parts[] = $club->zipcode. ' ' .$club->location;
			}
			else
			{
				$address_parts[] = $club->location;
			}
		}
		if (!empty($club->country))
		{
			$address_parts[] = Countries::getShortCountryName($club->country);
		}
		$address = implode(', ', $address_parts);
		return $address;
	}

	/**
	 * @see JoomleagueModelProject::hasEditPermission()
	 */
	function hasEditPermission($task = null,$id = false,$view=false)
	{
		$edit = false;
		
		$user = Factory::getUser();
		if (!$user->get('guest'))
		{
			$userId = $user->get('id');
			$asset = 'com_joomleague.club.'.$id;
			
			if ($user->authorise('core.edit', $asset))
			{
				$edit = true;
			}
		}
		
		return $edit;
	}
}
