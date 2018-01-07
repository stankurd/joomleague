<?php
/**
 * Joomleague
 *
 * @copyright	Copyright (C) 2006-2015 joomleague.at. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @link		http://www.joomleague.at
 */
defined('_JEXEC') or die;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\Utilities\ArrayHelper;
require_once JPATH_COMPONENT . '/models/item.php';
/**
 * Joomleague Model
 */
class JoomleagueModelJoomleague extends JoomleagueModelItem
{

	/**
	 * Method to load content project data
	 *
	 * @access private
	 * @return boolean on success
	 */
	function _loadData()
	{
		$app = Factory::getApplication();
		$input = $app->input;
		// Lets load the content if it doesn't already exist
		if(empty($this->_data))
		{
			$pid = $input->get('pid',array(0),'array');
			ArrayHelper::toInteger($pid);

			$db = Factory::getDbo();
			$query = $db->getQuery(true);
			$query->select('p.*');
			$query->from('#__joomleague_project AS p');
			$query->where('p.id = ' . $pid[0]);
			$db->setQuery($query);
			$this->_data = $db->loadObject();
			return (boolean) $this->_data;
		}
		return true;
	}

	/**
	 * Method to initialise the project data
	 *
	 * @access private
	 * @return boolean on success
	 */
	function _initData()
	{
		// Lets load the content if it doesn't already exist
		if(empty($this->_data))
		{
			$project = new stdClass();
			$project->id = 0;
			$project->league_id = 0;
			$project->season_id = 0;
			$project->name = null;
			$project->published = 0;
			$project->checked_out = 0;
			$project->checked_out_time = 0;
			$project->ordering = 0;
			$project->params = null;
			$this->_data = $project;

			return (boolean) $this->_data;
		}
		return true;
	}

	
	/**
	 * Method to return a project array (id, name)
	 *
	 * @access public
	 * @return array project
	 */
	function getProjects()
	{
		$app = Factory::getApplication();
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		$query->select(array('id','name'));
		$query->from('#__joomleague_project');
		$query->where('p.published = 1');
		$query->order('ordering, name ASC');
		
		try
	{
		$db->setQuery($query);
		$result = $db->loadObjectList();
	}
		catch (Exception $e)
	{
		$app->enqueueMessage(Text::_($e->getMessage()), 'error');
	}
		return $result;
	}


	/**
	 * Method to return the project teams array (id, name)
	 *
	 * @access public
	 * @return array
	 */
	function getProjectteams()
	{
		$app = Factory::getApplication();
		$input = $app->input;
		$option = $input->getCmd('option');
		$project_id = $app->getUserState($option . 'project');

		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		$query->select(array('t.name As text','t.notes'));
		$query->from('#__joomleague_team AS t');

		$query->select('pt.id AS value');
		$query->join('LEFT','#__joomleague_project_team AS pt ON pt.team_id = t.id');

		$query->where('pt.project_id = ' . $project_id);
		$query->order('t.name ASC');
		
		try
	{
		$db->setQuery($query);
		$result = $db->loadObjectList();
	}
		catch (Exception $e)
	{
		$app->enqueueMessage(Text::_($e->getMessage()), 'error');
	}
		return $result;
	}


	/**
	 * Method to return the project rounds array
	 *
	 * @access public
	 * @return array
	 *
	 * @todo
	 * check if function can be removed
	 */
	function getProjectRounds()
	{
		$app = Factory::getApplication();
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		$query->select(array('id','roundcode','name','round_date_first','round_date_last'));
		$query->from('#__joomleague_round');
		$query->where('project_id = ' . $this->_id);
		$query->order('roundcode,round_date_first');
		try
			{
			$db->setQuery($query);
			$result = $db->loadObjectList();
			}
		catch (Exception $e)
			{
			$app->enqueueMessage(Text::_($e->getMessage()), 'error');
			}
		return $result;
	}


	/**
	 * Method to return a season array (id, name)
	 *
	 * @access public
	 * @return array seasons
	 */
	function getSeasons()
	{
		$app = Factory::getApplication();
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		$query->select(array('id','name'));
		$query->from('#__joomleague_season');
		$query->order('name DESC');

		try
			{
			$db->setQuery($query);
			$result = $db->loadObjectList();
			}
		catch (Exception $e)
			{
			$app->enqueueMessage(Text::_($e->getMessage()), 'error');
			}
		return $result;
	}


	/**
	 * Method to return a project array (id, name)
	 *
	 * @access public
	 * @return array project
	 */
	function getProjectsBySportsType($sportstype_id,$season = null)
	{
		$app = Factory::getApplication();
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		$query->select(array('p.id','p.name'));
		$query->from('#__joomleague_project as p');
		$query->where('p.sports_type_id = ' . $sportstype_id);
		$query->where('p.published = 1');
		if($season)
		{
			$query->where('p.season_id = ' . $season);
		}
		$query->order('p.ordering, p.name ASC');
		
		try
			{
			$db->setQuery($query);
			$result = $db->loadObjectList();
			}
		catch (Exception $e)
			{
			$app->enqueueMessage(Text::_($e->getMessage()), 'error');
			}
		return $result;
	}


	/**
	 *
	 */
	function getVersion()
	{
		$app = Factory::getApplication();
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		$query->select('CONCAT(major,minor,build,revision) AS version');
		$query->from('#__joomleague_version');
		$query->order('date DESC');
		$db->setQuery($query,0,1);
		try
			{
			$result = $db->loadObjectList();
			}
		catch (Exception $e)
			{
			$app->enqueueMessage(Text::_($e->getMessage()), 'error');
			}
		return $result;
	}


	/**
	 *  Set current project-data
	 */
	function setCurrentProjectData($pid,$rid,$sid,$stid,$tid)
	{
		if (empty($pid) && empty($rid) && empty($sid) && empty($stid) && empty($tid)) {
			return false;
		}
		
		// @todo
		// implement at a later time
		return false;

		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		$query->update('#__joomleague_current');
		if ($pid) {
			$query->set('project_id = '.$pid);
		}
		if ($rid) {
			$query->set('round_id = '.$rid);
		}
		if ($sid) {
			$query->set('season_id = '.$sid);
		}
		if ($stid) {
			$query->set('sportstype_id = '.$stid);
		}
		if ($tid) {
			$query->set('projectteam_id = '.$tid);
		}
		$query->where('id = 1');
		$db->setQuery($query);
		$db->execute();

		return true;
	}


	/**
	 *  get current project-data
	 */
	function getCurrentProjectData()
	{
		// @todo
		// implement at a later date
		return false;
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		$query->select('*');
		$query->from('#__joomleague_current');
		$query->where('id = 1');
		$db->setQuery($query);
		if (!$result = $db->loadObject()) {
			return false;
		}

		return $result;

	}
}
