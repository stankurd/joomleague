<?php
/**
 * @copyright	Copyright (C) 2006-2014 joomleague.at. All rights reserved.
 * @license		GNU/GPL,see LICENSE.php
 * Joomla! is free software. This version may have been modified pursuant
 * to the GNU General Public License,and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */

// Check to ensure this file is included in Joomla!
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;

defined('_JEXEC') or die;

require_once 'item.php';

/**
 * Model-Sportstype
 *
 * @author	Julien Vonthron <julien.vonthron@gmail.com>
*/
class JoomleagueModelSportsType extends JoomleagueModelItem
{

	/**
	 * Method to load content sportstype data
	 *
	 * @access	private
	 * @return	boolean	True on success
	 */
	function _loadData()
	{
		// Lets load the content if it doesn't already exist
		if (empty($this->_data))
		{
			$db = Factory::getDbo();
			$query = $db->getQuery(true);
			$query->select('*');
			$query->from('#__joomleague_sports_type');
			$query->where('id = '.(int) $this->_id);
			$db->setQuery($query);
			$this->_data=$db->loadObject();
			return (boolean) $this->_data;
		}
		return true;
	}

	/**
	 * get count of related projects for this sports_type
	 */
	public function getProjectsCount() {
	    $app = Factory::getApplication();
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		$query
		      ->select('count(*) AS count')
		      ->from('#__joomleague_sports_type AS st')
		      ->innerJoin('#__joomleague_project AS p ON p.sports_type_id = st.id')
		      ->where('st.id='.(int) $this->_id);
		      try
		      {
		          $db->setQuery($query)->execute();
		      }
		      catch (RuntimeException $e)
		      {
		          $app->enqueueMessage(Text::_($e->getMessage()), 'error');
		          return 0;
		      }
		return $db->loadObject()->count;
	}

	/**
	 * get count of related projectleagues for this sports_type
	 */
	public function getLeaguesCount() {
	    $app = Factory::getApplication();
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		$query
		      ->select('count(*) AS count')
		      ->from('#__joomleague_sports_type AS st')
		      ->innerJoin('#__joomleague_project AS p ON p.sports_type_id = st.id')
		      ->innerJoin('#__joomleague_league AS l ON l.id = p.league_id')
		      ->where('st.id='.(int) $this->_id);
		      try
		      {
		          $db->setQuery($query)->execute();
		      }
		      catch (RuntimeException $e)
		      {
		          $app->enqueueMessage(Text::_($e->getMessage()), 'error');
		          return 0;
		      }
		      return $db->loadObject()->count;
	}
	/**
	 * get count of related seasons for this sports_type
	 */
	public function getSeasonsCount() {
	    $app = Factory::getApplication();
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		$query
		      ->select('count(*) AS count')
		      ->from('#__joomleague_sports_type AS st')
		      ->innerJoin('#__joomleague_project AS p ON p.sports_type_id = st.id')
		      ->innerJoin('#__joomleague_season AS s ON s.id = p.season_id')
		      ->where('st.id='.(int) $this->_id);
		      try
		      {
		          $db->setQuery($query)->execute();
		      }
		      catch (RuntimeException $e)
		      {
		          $app->enqueueMessage(Text::_($e->getMessage()), 'error');
		          return 0;
		      }
		      return $db->loadObject()->count;
	}

	/**
	 * get count of related projectteams for this sports_type
	 */
	public function getProjectTeamsCount() {
	    $app = Factory::getApplication();
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		$query
		      ->select('count(*) AS count')
		      ->from('#__joomleague_sports_type AS st')
		      ->innerJoin('#__joomleague_project AS p ON p.sports_type_id = st.id')
		      ->innerJoin('#__joomleague_project_team AS ptt ON ptt.project_id = p.id')
		      ->where('st.id='.(int) $this->_id);
		      try
		      {
		          $db->setQuery($query)->execute();
		      }
		      catch (RuntimeException $e)
		      {
		          $app->enqueueMessage(Text::_($e->getMessage()), 'error');
		          return 0;
		      }
		      return $db->loadObject()->count;
	}

	/**
	 * get count of related projectteamsplayers for this sports_type
	 */
	public function getProjectTeamsPlayersCount() {
	    $app = Factory::getApplication();
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		$query
		      ->select('count(*) AS count')
		      ->from('#__joomleague_sports_type AS st')
		      ->innerJoin('#__joomleague_project AS p ON p.sports_type_id = st.id')
		      ->innerJoin('#__joomleague_project_team AS ptt ON ptt.project_id = p.id')
		      ->innerJoin('#__joomleague_team_player AS ptp ON ptp.projectteam_id = ptt.id')
		      ->where('st.id='.(int) $this->_id);
		      try
		      {
		          $db->setQuery($query)->execute();
		      }
		      catch (RuntimeException $e)
		      {
		          $app->enqueueMessage(Text::_($e->getMessage()), 'error');
		          return 0;
		      }
		      return $db->loadObject()->count;
	}

	/**
	 * get count of related projectdivisions for this sports_type
	 */
	public function getProjectDivisionsCount() {
	    $app = Factory::getApplication();
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		$query
		      ->select('count(*) AS count')
		      ->from('#__joomleague_sports_type AS st')
		      ->innerJoin('#__joomleague_project AS p ON p.sports_type_id = st.id')
		      ->innerJoin('#__joomleague_division AS d ON d.project_id = p.id')
		      ->where('st.id='.(int) $this->_id);
		      try
		      {
		          $db->setQuery($query)->execute();
		      }
		      catch (RuntimeException $e)
		      {
		          $app->enqueueMessage(Text::_($e->getMessage()), 'error');
		          return 0;
		      }
		      return $db->loadObject()->count;
	}

	/**
	 * get count of related projectrounds for this sports_type
	 */
	public function getProjectRoundsCount() {
	    $app = Factory::getApplication();
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		$query
		      ->select('count(*) AS count')
		      ->from('#__joomleague_sports_type AS st')
		      ->innerJoin('#__joomleague_project AS p ON p.sports_type_id = st.id')
		      ->innerJoin('#__joomleague_round AS r ON r.project_id = p.id')
		      ->where('st.id='.(int) $this->_id);
		      try
		      {
		          $db->setQuery($query)->execute();
		      }
		      catch (RuntimeException $e)
		      {
		          $app->enqueueMessage(Text::_($e->getMessage()), 'error');
		          return 0;
		      }
		      return $db->loadObject()->count;
	}

	/**
	 * get count of related projectmatches for this sports_type
	 */
	public function getProjectMatchesCount() {
	    $app = Factory::getApplication();
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		$query
		      ->select('count(*) AS count')
		      ->from('#__joomleague_sports_type AS st')
		      ->innerJoin('#__joomleague_project AS p ON p.sports_type_id = st.id')
		      ->innerJoin('#__joomleague_round AS r ON r.project_id = p.id')
		      ->where('st.id='.(int) $this->_id);
		      try
		      {
		          $db->setQuery($query)->execute();
		      }
		      catch (RuntimeException $e)
		      {
		          $app->enqueueMessage(Text::_($e->getMessage()), 'error');
		          return 0;
		      }
		      return $db->loadObject()->count;
	}
	/**
	 * get count of related projectmatchesevents for this sports_type
	 */
	public function getProjectMatchesEventsCount() {
	    $app = Factory::getApplication();
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		$query
		      ->select('count(*) AS count')
		      ->from('#__joomleague_sports_type AS st')
		      ->innerJoin('#__joomleague_project AS p ON p.sports_type_id = st.id')
		      ->innerJoin('#__joomleague_round AS r ON r.project_id = p.id')
		      ->innerJoin('#__joomleague_match AS m ON m.round_id = r.id')
		      ->innerJoin('#__joomleague_match_event AS me ON me.match_id = m.id')
		      ->where('st.id='.(int) $this->_id);
		      try
		      {
		          $db->setQuery($query)->execute();
		      }
		      catch (RuntimeException $e)
		      {
		          $app->enqueueMessage(Text::_($e->getMessage()), 'error');
		          return 0;
		      }
		      return $db->loadObject()->count;
	}
	
	/**
	 * get count of related projectmatcheseventnames for this sports_type
	 */
	public function getProjectMatchesEventNames()
	{
	            $app = Factory::getApplication();
				$db = Factory::getDbo();
				$query = $db->getQuery(true)
        				->select('count(me.id) as count, me.event_type_id, et.name, et.icon')
        				->from('#__joomleague_match_event as me')
        				->join('INNER' , '#__joomleague_match AS m ON me.match_id= . m.id')
        				->join('INNER' , '#__joomleague_round AS r ON m.round_id = r.id')
        				->join('INNER' , '#__joomleague_project AS p ON r.project_id = p.id')
        				->join('INNER' , '#__joomleague_eventtype AS et ON me.event_type_id = et.id')
        				->where('p.sports_type_id = '.(int) $this->_id)
        				->group('me.event_type_id')
        				->order('et.ordering');
				try
				{
				    $db->setQuery($query)->execute();
				}
				catch (RuntimeException $e)
				{
				    $app->enqueueMessage(Text::_($e->getMessage()), 'error');
				    return 0;
				}
				return $db->loadObject()->count;
	}
	
	/**
	 * get count of related projectmatchesstats for this sports_type
	 */
	public function getProjectMatchesStatsCount() {
	    $app = Factory::getApplication();
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		$query
		      ->select('count(*) AS count')
		      ->from('#__joomleague_sports_type AS st')
		      ->innerJoin('#__joomleague_project AS p ON p.sports_type_id = st.id')
		      ->innerJoin('#__joomleague_round AS r ON r.project_id = p.id')
		      ->innerJoin('#__joomleague_match AS m ON m.round_id = r.id')
		      ->innerJoin('#__joomleague_match_statistic AS ms ON ms.match_id = m.id')
		      ->where('st.id='.(int) $this->_id);
		      try
		      {
		          $db->setQuery($query)->execute();
		      }
		      catch (RuntimeException $e)
		      {
		          $app->enqueueMessage(Text::_($e->getMessage()), 'error');
		          return 0;
		      }
		      return $db->loadObject()->count;
	}
}
