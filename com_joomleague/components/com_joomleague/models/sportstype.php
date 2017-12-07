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
		$this->_db = Factory::getDbo();
		$query = $this->_db->getQuery(true);
		$query = 'SELECT count(*) AS count FROM #__joomleague_sports_type AS st
				INNER JOIN #__joomleague_project AS p ON p.sports_type_id = st.id
				WHERE st.id='.(int) $this->_id;
		$this->_db->setQuery($query);
		if (!$this->_db->execute())
		{
			$this->setError($this->_db->getErrorMsg());
			return 0;
		}
		return $this->_db->loadObject()->count;
	}

	/**
	 * get count of related projectleagues for this sports_type
	 */
	public function getLeaguesCount() {
		$this->_db = Factory::getDbo();
		$query = $this->_db->getQuery(true);
		$query = 'SELECT count(*) AS count FROM #__joomleague_sports_type AS st
				INNER JOIN #__joomleague_project AS p ON p.sports_type_id = st.id
				INNER JOIN #__joomleague_league AS l ON l.id = p.league_id
				WHERE st.id='.(int) $this->_id;
		$this->_db->setQuery($query);
		if (!$this->_db->execute())
		{
			$this->setError($this->_db->getErrorMsg());
			return 0;
		}
		return $this->_db->loadObject()->count;
	}

	/**
	 * get count of related seasons for this sports_type
	 */
	public function getSeasonsCount() {
		$this->_db = Factory::getDbo();
		$query = $this->_db->getQuery(true);
		$query = 'SELECT count(*) AS count FROM #__joomleague_sports_type AS st
				INNER JOIN #__joomleague_project AS p ON p.sports_type_id = st.id
				INNER JOIN #__joomleague_season AS s ON s.id = p.season_id
				WHERE st.id='.(int) $this->_id;
		$this->_db->setQuery($query);
		if (!$this->_db->execute())
		{
			$this->setError($this->_db->getErrorMsg());
			return 0;
		}
		return $this->_db->loadObject()->count;
	}

	/**
	 * get count of related projectteams for this sports_type
	 */
	public function getProjectTeamsCount() {
		$this->_db = Factory::getDbo();
		$query = $this->_db->getQuery(true);
		$query = 'SELECT count(*) AS count FROM #__joomleague_sports_type AS st
				INNER JOIN #__joomleague_project AS p ON p.sports_type_id = st.id
				INNER JOIN #__joomleague_project_team AS ptt ON ptt.project_id = p.id
				WHERE st.id='.(int) $this->_id;
		$this->_db->setQuery($query);
		if (!$this->_db->execute())
		{
			$this->setError($this->_db->getErrorMsg());
			return 0;
		}
		return $this->_db->loadObject()->count;
	}

	/**
	 * get count of related projectteamsplayers for this sports_type
	 */
	public function getProjectTeamsPlayersCount() {
		$this->_db = Factory::getDbo();
		$query = $this->_db->getQuery(true);
		$query = 'SELECT count(*) AS count FROM #__joomleague_sports_type AS st
				INNER JOIN #__joomleague_project AS p ON p.sports_type_id = st.id
				INNER JOIN #__joomleague_project_team AS ptt ON ptt.project_id = p.id
				INNER JOIN #__joomleague_team_player AS ptp ON ptp.projectteam_id = ptt.id
				WHERE st.id='.(int) $this->_id;

		$this->_db->setQuery($query);
		if (!$this->_db->execute())
		{
			$this->setError($this->_db->getErrorMsg());
			return 0;
		}
		return $this->_db->loadObject()->count;
	}

	/**
	 * get count of related projectdivisions for this sports_type
	 */
	public function getProjectDivisionsCount() {
		$this->_db = Factory::getDbo();
		$query = $this->_db->getQuery(true);
		$query = 'SELECT count(*) AS count FROM #__joomleague_sports_type AS st
				INNER JOIN #__joomleague_project AS p ON p.sports_type_id = st.id
				INNER JOIN #__joomleague_division AS d ON d.project_id = p.id
				WHERE st.id='.(int) $this->_id;

		$this->_db->setQuery($query);
		if (!$this->_db->execute())
		{
			$this->setError($this->_db->getErrorMsg());
			return 0;
		}
		return $this->_db->loadObject()->count;
	}

	/**
	 * get count of related projectrounds for this sports_type
	 */
	public function getProjectRoundsCount() {
		$this->_db = Factory::getDbo();
		$query = $this->_db->getQuery(true);
		$query = 'SELECT count(*) AS count FROM #__joomleague_sports_type AS st
				INNER JOIN #__joomleague_project AS p ON p.sports_type_id = st.id
				INNER JOIN #__joomleague_round AS r ON r.project_id = p.id
				WHERE st.id='.(int) $this->_id;

		$this->_db->setQuery($query);
		if (!$this->_db->execute())
		{
			$this->setError($this->_db->getErrorMsg());
			return 0;
		}
		return $this->_db->loadObject()->count;
	}

	/**
	 * get count of related projectmatches for this sports_type
	 */
	public function getProjectMatchesCount() {
		$this->_db = Factory::getDbo();
		$query = $this->_db->getQuery(true);
		$query = 'SELECT count(*) AS count FROM #__joomleague_sports_type AS st
				INNER JOIN #__joomleague_project AS p ON p.sports_type_id = st.id
				INNER JOIN #__joomleague_round AS r ON r.project_id = p.id
				INNER JOIN #__joomleague_match AS m ON m.round_id = r.id
				WHERE st.id='.(int) $this->_id;

		$this->_db->setQuery($query);
		if (!$this->_db->execute())
		{
			$this->setError($this->_db->getErrorMsg());
			return 0;
		}
		return $this->_db->loadObject()->count;
	}

	/**
	 * get count of related projectmatchesevents for this sports_type
	 */
	public function getProjectMatchesEventsCount() {
		$this->_db = Factory::getDbo();
		$query = $this->_db->getQuery(true);
		$query = 'SELECT count(*) AS count FROM #__joomleague_sports_type AS st
				INNER JOIN #__joomleague_project AS p ON p.sports_type_id = st.id
				INNER JOIN #__joomleague_round AS r ON r.project_id = p.id
				INNER JOIN #__joomleague_match AS m ON m.round_id = r.id
				INNER JOIN #__joomleague_match_event AS me ON me.match_id = m.id
				WHERE st.id='.(int) $this->_id;

		$this->_db->setQuery($query);
		if (!$this->_db->execute())
		{
			$this->setError($this->_db->getErrorMsg());
			return 0;
		}
		return $this->_db->loadObject()->count;
	}
	
	/**
	 * get count of related projectmatcheseventnames for this sports_type
	 */
	public function getProjectMatchesEventNames()
	{
				$this->_db = Factory::getDbo();
				$query = $this->_db->getQuery(true)
				->select('count(me.id) as count, me.event_type_id, et.name, et.icon')
				->from('#__joomleague_match_event as me')
				->join('INNER' , '#__joomleague_match AS m ON me.match_id= . m.id')
				->join('INNER' , '#__joomleague_round AS r ON m.round_id = r.id')
				->join('INNER' , '#__joomleague_project AS p ON r.project_id = p.id')
				->join('INNER' , '#__joomleague_eventtype AS et ON me.event_type_id = et.id')
				->where('p.sports_type_id = '.(int) $this->_id)
				->group('me.event_type_id')
				->order('et.ordering');
		$this->_db->setQuery($query);
		if (!$result = $this->_db->loadObjectList())
		{
			//$this->setError($this->_db->getErrorMsg());
			return array();
		}
		return $result;
	}
	
	/**
	 * get count of related projectmatchesstats for this sports_type
	 */
	public function getProjectMatchesStatsCount() {
		$this->_db = Factory::getDbo();
		$query = $this->_db->getQuery(true);
		$query = 'SELECT count(*) AS count FROM #__joomleague_sports_type AS st
				INNER JOIN #__joomleague_project AS p ON p.sports_type_id = st.id
				INNER JOIN #__joomleague_round AS r ON r.project_id = p.id
				INNER JOIN #__joomleague_match AS m ON m.round_id = r.id
				INNER JOIN #__joomleague_match_statistic AS ms ON ms.match_id = m.id
				WHERE st.id='.(int) $this->_id;

		$this->_db->setQuery($query);
		if (!$this->_db->execute())
		{
			$this->setError($this->_db->getErrorMsg());
			return 0;
		}
		return $this->_db->loadObject()->count;
	}
}
