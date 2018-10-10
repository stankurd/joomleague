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
 * Model-Clubs
 */
class JoomleagueModelClubs extends JoomleagueModelProject
{
	var $projectid = 0;
	var $divisionid = 0;

	public function __construct()
	{
		parent::__construct();
		
		$app = Factory::getApplication();
		$input = $app->input;

		$this->projectid = $input->getInt('p',0);
		$this->divisionid = $input->getInt('division',0);
	}

	/**
	 * @see JoomleagueModelProject::getDivision()
	 */
	function getDivision($id=0)
	{
		$division = null;
		if ($this->divisionid != 0)
		{
			$division = parent::getDivision($this->divisionid);
		}
		return $division;
	}

	/**
	 * getClubs
	 */
	function getClubs($ordering = null)
	{
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		$query
			->select('c.*')
			->select($this->constructSlug($db, 'club_slug', 'c.alias', 'c.id'))
			->from($db->quoteName('#__joomleague_club', 'c'))
			->join('LEFT', $db->quoteName('#__joomleague_team', 't') .
				' ON ' . $db->quoteName('t.club_id') . ' = ' . $db->quoteName('c.id'))
			->join('LEFT', $db->quoteName('#__joomleague_project_team', 'pt') .
				' ON ' . $db->quoteName('pt.team_id') . ' = ' . $db->quoteName('t.id'));
		if ($this->projectid > 0)
		{
			$query->where($db->quoteName('pt.project_id') . ' = ' . (int)$this->projectid);
			if ($this->divisionid > 0)
			{
				$query->where($db->quoteName('pt.division_id') . ' = ' . (int)$this->divisionid);
			}
		}
		$query
			->group($db->quoteName('c.id'))
			->order($ordering ? $db->quoteName('$ordering') : $db->quoteName('c.name'));

		$db->setQuery($query);
		$clubs = $db->loadObjectList();
		if (!empty($clubs))
		{
			$this->addTeamsToClubs($clubs);
		}
		else
		{
		    throw new Exception($e->getMessage());
		    
		  //echo $db->getErrorMsg();
		}
		return $clubs;
	}

	private function addTeamsToClubs($clubs)
	{
		$db = Factory::getDbo();
		for ($index = 0; $index < count($clubs); $index++) {
			$query = $db->getQuery(true);
			$query
				->select('t.*')
				->select($db->quoteName('t.picture', 'team_picture'))
				->select($db->quoteName('pt.picture', 'projectteam_picture'))
				->select($this->constructSlug($db, 'team_slug', 't.alias', 't.id'))
				->from($db->quoteName('#__joomleague_team', 't'))
				->join('LEFT', $db->quoteName('#__joomleague_project_team', 'pt') .
					' ON '. $db->quoteName('pt.team_id') . ' = ' . $db->quoteName('t.id'))
				->where($db->quoteName('pt.project_id') . ' = ' . (int)$this->projectid)
				->where($db->quoteName('t.club_id') . ' = ' . (int)$clubs[$index]->id);
			if ( $this->divisionid != 0 )
			{
				$query->where($db->quoteName('pt.division_id') . ' = ' . (int)$this->divisionid);
			}
			$db->setQuery($query);
			$teams = $db->loadObjectList();
			if (empty($teams))
			{
			    throw new RuntimeException($e->getMessage());
			}
			$clubs[$index]->teams = $teams;
		}
	}
}
