<?php
/**
 * Joomleague
 *
 * @copyright	Copyright (C) 2006-2015 joomleague.at. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @link		http://www.joomleague.at
 */

// Check to ensure this file is included in Joomla!
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Object\CMSObject;

defined('_JEXEC') or die;

require_once JLG_PATH_ADMIN.'/helpers/jlparameter.php';
/**
 * base class for statistics handling
 *
 */
class JLGStatistic extends CMSObject {
	
	var $_name = 'default';
	
	var $_calculated = 0;
	
	var $_showinsinglematchreports = 1;
	
	/**
	 * JRegistry object for parameters
	 * @var object JRegistry
	 */
	var $_params = null;
	
	/**
	 * statistic
	 *
	 * @var int
	 */
	var $id = null;

	/**
	 * stat name
	 * @var string
	 */
	var $name;
	
	/**
	 * short form (abbreviation) of the name
	 * @var string
	 */
	var $short;
	/**
	 * icon path
	 * @var string
	 */
	var $icon;

	/**
	 * parameters for the stat (from xml)
	 * @var string
	 */
	var $baseparams;

	/**
	 * parameters for the stat (from xml)
	 * @var string
	 */
	var $params;
	
	function __construct()
	{
		
	}

	/**
	 * get an instance of class corresponding to type
	 * @param string class
	 * @return object
	 */
	public static function &getInstance($class)
	{
		$classname = 'JLGStatistic'. ucfirst($class);
		
		// check for statistic in extensions
		$extensions = JoomleagueHelper::getExtensions(0);
		foreach ($extensions as $type)
		{
			$file = JLG_PATH_SITE.'/extensions/'.$type.'/admin/statistics/'.$class.'.php';
			if (file_exists($file))
			{
				require_once($file);
				$stat = new $classname();
				return $stat;
			}
		}
	
		if (!class_exists($classname))
		{
			$file = JLG_PATH_ADMIN.'/statistics/'.$class.'.php';
			if (!file_exists($file)) {
				Factory::getApplication()->enqueueMessage(0, $class .': '. Text::_('STATISTIC CLASS NOT DEFINED')); 
			}
			require_once($file);
		}
		$stat = new $classname();
		return $stat;
	}
	
	/**
	 * Binds a named array/hash to this object
	 *
	 * Can be overloaded/supplemented by the child class
	 *
	 * @access	public
	 * @param	$from	mixed	An associative array or object
	 * @param	$ignore	mixed	An array or space separated list of fields not to bind
	 * @return	boolean
	 */
	function bind( $from, $ignore=array() )
	{
		$fromArray	= is_array( $from );
		$fromObject	= is_object( $from );

		if (!$fromArray && !$fromObject)
		{
			$this->setError( get_class( $this ).'::bind failed. Invalid from argument' );
			return false;
		}
		if (!is_array( $ignore )) {
			$ignore = explode( ' ', $ignore );
		}
		foreach ($this->getProperties() as $k => $v)
		{
			// internal attributes of an object are ignored
			if (!in_array( $k, $ignore ))
			{
				if ($fromArray && isset( $from[$k] )) {
					$this->$k = $from[$k];
				} else if ($fromObject && isset( $from->$k )) {
					$this->$k = $from->$k;
				}
			}
		}
		return true;
	}
	
	/**
	 * return Statistic params as JParameter objet
	 * @return object
	 */
	function getBaseParams()
	{
		$paramsdata = $this->baseparams;
		$paramsdefs = JLG_PATH_ADMIN.'/statistics/base.xml';
		return new JLParameter( $paramsdata, $paramsdefs );
	}
	
	/**
	 * return Statistic params as JParameter objet
	 * @return object
	 */
	function getClassParams()
	{
		// cannot use __FILE__ because extensions can have their own stats
		$rc = new ReflectionClass(get_class($this));
		$currentdir = dirname($rc->getFileName());
		$paramsdata = $this->params;
		$paramsdefs = $currentdir.'/'. $this->_name .'.xml';
		return new JLParameter( $paramsdata, $paramsdefs );
	}
	
	/**
	 * return path to xml config file associated to this statistic
	 * @return object
	 */
	public function getXmlPath()
	{
		// cannot use __FILE__ because extensions can have their own stats
		$rc = new ReflectionClass(get_class($this));
		$currentdir = dirname($rc->getFileName());
		return $currentdir.'/'. $this->_name .'.xml';
	}
	
	/**
	 * return Statistic params as JParameter objet
	 * @return object
	 */
	function &getParams()
	{
		if (empty($this->_params))
		{
			$this->_params = $this->getBaseParams();
			$this->_params->merge($this->getClassParams());
		}
		return $this->_params;
	}
	
	/**
	 * return a parameter value
	 * @param string name
	 * @param mixed default value
	 * @return mixed
	 */
	function getParam($name, $default = '')
	{
		$params = &$this->getParams();
		return $params->get($name, $default);
	}
	
	function getPrecision()
	{
		$params = &$this->getParams();
		return $params->get('precision', 2);
	}
	
	/**
	 * is the stat calculated
	 * in this case, no user input
	 * @return boolean
	 */
	function getCalculated()
	{
		return $this->_calculated;
	}
	
	/**
	 * show stat in single match views/reports?
	 * e.g.: stats 'per game' should not be displayed in match report...
	 * @return boolean
	 */
	function showInSingleMatchReports()
	{
		return $this->_showinsinglematchreports;
	}

	/**
	 * show stat in match report?
	 * setting in every statistic (backend) on which frontend views the stat should be shown
	 * @return boolean
	 */
	function showInMatchReport()
	{
		$params = $this->getParams();
		if(!is_array($params->get('statistic_views'))) {
			$statistic_views = explode(',', $params->get('statistic_views'));
		} else {
			$statistic_views = $params->get('statistic_views');
		}
		if (!count($statistic_views)) {
			Factory::getApplication()->enqueueMessage(0, Text::sprintf('STAT %s/%s WRONG CONFIGURATION', $this->_name, $this->id)); 
			return(array(0));
		}
				
		if ( in_array("matchreport", $statistic_views) || empty($statistic_views[0]) ) {
		    return 1;
		} else { 
		    return 0;
		} 
	}

	/**
	 * show stat in team roster?
	 * setting in every statistic (backend) on which frontend views the stat should be shown
	 * @return boolean
	 */
	function showInRoster()
	{
		$params = $this->getParams();
		if(!is_array($params->get('statistic_views'))) {
			$statistic_views = explode(',', $params->get('statistic_views'));
		} else {
			$statistic_views = $params->get('statistic_views');
		}
		if (!count($statistic_views)) {
		    Factory::getApplication()->enqueueMessage(0, Text::sprintf('STAT %s/%s WRONG CONFIGURATION', $this->_name, $this->id));
			return(array(0));
		}
				
		if ( in_array("roster", $statistic_views) || empty($statistic_views[0]) ) {
		    return 1;
		} else { 
		    return 0;
		} 
	}

	/**
	 * show stat in player page?
	 * setting in every statistic (backend) on which frontend views the stat should be shown
	 * @return boolean
	 */
	function showInPlayer()
	{
		$params = $this->getParams();
		if(!is_array($params->get('statistic_views'))) {
			$statistic_views = explode(',', $params->get('statistic_views'));
		} else {
			$statistic_views = $params->get('statistic_views');
		}
		if (!count($statistic_views)) {
		    Factory::getApplication()->enqueueMessage(0, Text::sprintf('STAT %s/%s WRONG CONFIGURATION', $this->_name, $this->id));
			return(array(0));
		}
				
		if ( in_array("player", $statistic_views) || empty($statistic_views[0]) ) {
		    return 1;
		} else { 
		    return 0;
		} 
	}
	
	/**
	 * return stat <img> html tag
	 * @return string
	 */
	function getImage()
	{
		if (!empty($this->icon))
		{
			$iconPath = $this->icon;
			if ( !strpos( " " . $iconPath, "/"	) )
			{
				$iconPath = "images/com_joomleague/database/statistics/" . $iconPath;
			}
			return HTMLHelper::image($iconPath, Text::_($this->name),	array( "title" => Text::_($this->name) ));
		}
		return '<span class="stat-alternate hasTip" title="'.Text::_($this->name).'">'.Text::_($this->short).'</span>';
	}

	/**
	 * return the stat value for specified player in a specific game
	 * @param object $gamemodel must provide methods getPlayersStats and getPlayersEvents
	 * @param int $teamplayer_id
	 * @return mixed stat value
	 */
	function getMatchPlayerStat(&$gamemodel, $teamplayer_id)
	{
    Factory::getApplication()->enqueueMessage(0, $this->_name .': '. Text::_('METHOD NOT IMPLEMENTED IN THIS STATISTIC INSTANCE'));
    return 0;
	}
	
	/**
	 * return all players stat for the game indexed by teamplyer_id
	 * @param int match_id
	 * @return value
	 */
	function getMatchPlayersStats($match_id)
	{
	Factory::getApplication()->enqueueMessage(0, $this->_name .': '. Text::_('METHOD NOT IMPLEMENTED IN THIS STATISTIC INSTANCE'));
	return 0;
	}

	/**
	 * return stat for all player games in the project
	 * @param int match_id
	 * @return value
	 */
	function getPlayerStatsByGame($teamplayer_id, $project_id)
	{
	    Factory::getApplication()->enqueueMessage(0, $this->_name .': '. Text::_('METHOD NOT IMPLEMENTED IN THIS STATISTIC INSTANCE'));
		return 0;
	}
	
	/**
	 * return player stats in project if project_id != 0, otherwise player stats in whole player's career
	 * @param int person_id
	 * @return float
	 */
	function getPlayerStatsByProject($person_id, $projectteam_id = 0, $project_id = 0, $sports_type_id = 0)
	{
	    Factory::getApplication()->enqueueMessage(0, $this->_name .': '. Text::_('METHOD NOT IMPLEMENTED IN THIS STATISTIC INSTANCE'));
		return 0;
	}
	
	/**
	 * Get players stats
	 * @param $team_id
	 * @param $project_id
	 * @param $position_id
	 * @return array
	 */
	function getRosterStats($team_id, $project_id, $position_id)
	{
	    Factory::getApplication()->enqueueMessage(0, $this->_name .': '. Text::_('METHOD NOT IMPLEMENTED IN THIS STATISTIC INSTANCE'));
	    return 0;
	}
	
	/**
	 * returns players ranking
	 * @param int project_id
	 * @param int division_id
	 * @param int limit
	 * @param in limitstart
	 * @return array
	 */
	function getPlayersRanking($project_id, $division_id, $team_id, $limit = 20, $limitstart = 0, $order = null)
	{
	    Factory::getApplication()->enqueueMessage(0, $this->_name .': '. Text::_('METHOD NOT IMPLEMENTED IN THIS STATISTIC INSTANCE'));
	    return array();
	}

	/**
	 * returns teams ranking
	 * @param int project_id
	 * @param int limit
	 * @param in limitstart
	 * @return array
	 */
	function getTeamsRanking($project_id, $limit = 20, $limitstart = 0, $order = null)
	{
	    Factory::getApplication()->enqueueMessage(0, $this->_name .': '. Text::_('METHOD NOT IMPLEMENTED IN THIS STATISTIC INSTANCE'));
		return array();
	}
	
	/**
	 * return the stat value for specified player in a specific game
	 * @param object $gamemodel must provide methods getPlayersStats and getPlayersEvents
	 * @param int $teamstaff_id
	 * @return mixed stat value
	 */
	function getMatchStaffStat(&$gamemodel, $team_staff_id)
	{		
        Factory::getApplication()->enqueueMessage(0,$this->_name .': '.  Text::_('METHOD NOT IMPLEMENTED IN THIS STATISTIC INSTANCE'));
	    return 0;
	}
	
	/**
	 * return staff stat in project
	 * @param int person_id
	 * @param int project_id
	 * @return float
	 */
	function getStaffStats($person_id, $team_id, $project_id)
	{
        Factory::getApplication()->enqueueMessage(0,$this->_name .': '.  Text::_('METHOD NOT IMPLEMENTED IN THIS STATISTIC INSTANCE'));
	    return 0;
	}

	/**
	 * return player stats in project
	 * @param int person_id
	 * @param int project_id
	 * @return float
	 */
	function getHistoryStaffStats($person_id)
	{
        Factory::getApplication()->enqueueMessage(0, $this->_name .': '. Text::_('METHOD NOT IMPLEMENTED IN THIS STATISTIC INSTANCE'));
	    return 0;
	}

	/**
	 * return the player stats per match for given statistics IDs, for given project,
	 * using weighting factors if given
	 * @param int teamplayer IDs
	 * @param array statistic IDs
	 * @param int project_id
	 * @param array factors to be used for each statistics type
	 * @return array of stdclass objects with statistics info
	 */
	protected function getPlayerStatsByGameForIds($teamplayer_ids, $project_id, $sids, $factors = NULL)
	{
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		
		$quoted_sids = array();
		foreach ($sids as $sid) {
			$quoted_sids[] = $db->Quote($sid);
		}		
		$quoted_tpids = array();
		foreach ($teamplayer_ids as $tpid) {
			$quoted_tpids[] = $db->Quote($tpid);
		}
		if (isset($factors))
		{
			$query = ' SELECT ms.value AS value, ms.match_id, ms.statistic_id';
		}
		else
		{
			$query = ' SELECT SUM(ms.value) AS value, ms.match_id';
		}
		$query .= ' FROM #__joomleague_match_statistic AS ms'
			. ' INNER JOIN #__joomleague_match AS m ON m.id = ms.match_id'
			. '                                    AND m.published = 1'
			. ' INNER JOIN #__joomleague_team_player AS tp ON ms.teamplayer_id=tp.id'
			. ' INNER JOIN #__joomleague_project_position AS ppos ON tp.project_position_id=ppos.id'
			. ' INNER JOIN #__joomleague_position AS pos ON ppos.position_id=pos.id'
			. ' INNER JOIN #__joomleague_position_statistic AS ps ON ps.position_id=pos.id'
			. '                                                  AND ps.statistic_id=ms.statistic_id'
			. ' INNER JOIN #__joomleague_project_team AS pt ON tp.projectteam_id=pt.id'
			. ' INNER JOIN #__joomleague_project AS p ON pt.project_id=p.id'
			. '                                       AND p.id=' . $db->Quote($project_id)
			. ' WHERE ms.teamplayer_id IN (' . implode(',', $quoted_tpids) .')'
			. ' AND p.published=1'
			. ' AND ms.statistic_id  IN ('. implode(',', $quoted_sids) .')'
			;

		if (isset($factors))
		{
			$db->setQuery($query);
			$stats = $db->loadObjectList();
			// Apply weighting using factors
			$res = array();
			foreach ($stats as $stat)
			{
				$key = array_search($stat->statistic_id, $sids);
				if ($key !== FALSE)
				{
					if (!isset($res[$stat->match_id])) {
						$res[$stat->match_id] = new stdclass;
						$res[$stat->match_id]->match_id = $stat->match_id;
						$res[$stat->match_id]->value = 0;
					}
					$res[$stat->match_id]->value += $factors[$key] * $stat->value;
				}
			}
		}
		else
		{
			$db->setQuery($query . ' GROUP BY ms.match_id');
			$res = $db->loadObjectList('match_id');
		}

		if (is_array($res) && count($res) > 0)
		{
			// Determine total for the whole project
			$total = new stdclass;
			$total->value = 0;
			foreach ($res as $match)
			{
				$total->value += $match->value;
			}
			$res['totals'] = $total;
		}

		return $res;
	}

	/**
	 * return the player stats per project for given statistics IDs, for given project,
	 * using weighting factors if given
	 * @param int person_id
	 * @param int projectteam_id
	 * @param int project_id
	 * @param int sports_type_id
	 * @param array statistic IDs
	 * @param array factors to be used for each statistics type
	 * @return float
	 */
	protected function getPlayerStatsByProjectForIds($person_id, $projectteam_id, $project_id, $sports_type_id, $sids, $factors = NULL)
	{
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		
		$quoted_sids = array();
		foreach ($sids as $sid) {
			$quoted_sids[] = $db->Quote($sid);
		}		

		if (isset($factors))
		{
			$query = ' SELECT ms.value AS value, ms.statistic_id ';
		}
		else
		{
			$query = ' SELECT SUM(ms.value) AS value ';
		}
		$query .= ' FROM #__joomleague_match_statistic AS ms'
			. ' INNER JOIN #__joomleague_match AS m ON m.id = ms.match_id'
			. '                                    AND m.published = 1'
			. ' INNER JOIN #__joomleague_team_player AS tp ON ms.teamplayer_id=tp.id'
			. ' INNER JOIN #__joomleague_project_position AS ppos ON tp.project_position_id=ppos.id'
			. ' INNER JOIN #__joomleague_position AS pos ON ppos.position_id=pos.id'
			. ' INNER JOIN #__joomleague_position_statistic AS ps ON ps.position_id=pos.id'
			. '                                                  AND ps.statistic_id=ms.statistic_id'
			. ' INNER JOIN #__joomleague_project_team AS pt ON tp.projectteam_id=pt.id'
			. ' INNER JOIN #__joomleague_project AS p ON pt.project_id=p.id'
			. ' WHERE tp.person_id = ' . $db->Quote($person_id)
			. ' AND ms.statistic_id  IN ('. implode(',', $quoted_sids) .')'
			;
		if ($projectteam_id)
		{
			$query .= ' AND pt.id=' . $db->Quote($projectteam_id);
		}
		if ($project_id)
		{
			$query .= ' AND p.id=' . $db->Quote($project_id);
		}
		if ($sports_type_id)
		{
			$query .= ' AND p.sports_type_id='.$db->Quote($sports_type_id);
		}
		if (isset($factors))
		{
			$db->setQuery($query);
			$stats = $db->loadObjectList();
			// Apply weighting using factors
			$res = 0;
			foreach ($stats as $stat)
			{
				$key = array_search($stat->statistic_id, $sids);
				if ($key !== FALSE)
				{
					$res += $factors[$key] * $stat->value;
				}
			}
		}
		else
		{
			$db->setQuery($query, 0, 1);
			$res = $db->loadResult();
			if (!isset($res))
			{
				$res = 0;
			}
		}
		return $res;
	}

	/**
	 * return the number of games the player played for given project
	 * @param int person_id
	 * @param int project_id, if 0 then the number of played games for the complete career are returned
	 * @return integer
	 */
	protected function getGamesPlayedByPlayer($person_id, $projectteam_id, $project_id, $sports_type_id)
	{
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		
		$query 	= ' SELECT COUNT(DISTINCT m.id)'
				. ' FROM #__joomleague_match AS m'
				. ' INNER JOIN #__joomleague_match_player AS mp ON mp.match_id=m.id'
				. ' INNER JOIN #__joomleague_team_player AS tp ON tp.id=mp.teamplayer_id'
				. ' INNER JOIN #__joomleague_project_team AS pt ON pt.id=tp.projectteam_id'
				. ' INNER JOIN #__joomleague_project AS p ON p.id=pt.project_id'
				. ' WHERE tp.person_id = '.$db->Quote($person_id);
		if ($projectteam_id)
		{
			$query .= ' AND pt.id=' . $db->Quote($projectteam_id);
		}
		if ($project_id)
		{
			$query .= ' AND p.id=' . $db->Quote($project_id);
		}
		if ($sports_type_id)
		{
			$query .= ' AND p.sports_type_id=' . $db->Quote($sports_type_id);
		}
		$query .= '   AND (mp.came_in=0 || mp.came_in=1)'
				. '   AND m.published=1';

		$db->setQuery($query);
		$res = $db->loadResult();
		return $res;
	}

	/**
	 * return the player stats for events per project for given statistics IDs, for given project,
	 * using weighting factors if given
	 * @param int person_id
	 * @param int projectteam_id
	 * @param int project_id
	 * @param int sports_type_id
	 * @param array statistic IDs
	 * @return float
	 */
	protected function getPlayerStatsByProjectForEvents($person_id, $projectteam_id, $project_id, $sports_type_id, $sids)
	{
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		
		$quoted_sids = array();
		foreach ($sids as $sid) {
			$quoted_sids[] = $db->Quote($sid);
		}		

		$query = ' SELECT SUM(me.event_sum) AS value, tp.person_id '
		       . ' FROM #__joomleague_team_player AS tp '
		       . ' INNER JOIN #__joomleague_project_team AS pt ON pt.id = tp.projectteam_id ';
		if ($sports_type_id)
		{
			$query .= ' INNER JOIN #__joomleague_project AS p ON p.id = pt.project_id '
					. '   AND p.sports_type_id = '. $db->Quote($sports_type_id);
		}
		$query.= ' LEFT JOIN #__joomleague_match_event AS me ON me.teamplayer_id = tp.id '
		       . '   AND me.event_type_id IN ('. implode(',', $quoted_sids) .')'
		       . ' LEFT JOIN #__joomleague_match AS m ON m.id = me.match_id '
		       . '   AND m.published = 1 '
		       . ' WHERE tp.person_id = '. $db->Quote($person_id)
		       ;

		if ($projectteam_id)
		{
		       $query .= '   AND pt.id = '. $db->Quote($projectteam_id);
		}

		if ($project_id)
		{
		       $query .= '   AND pt.project_id = '. $db->Quote($project_id);
		}
		$query .= ' GROUP BY tp.person_id';

		$db->setQuery($query);
		$res = $db->loadResult();
		if (!isset($res))
		{
			$res = 0;
		}
		return $res;
	}

	/**
	 * return the statistics for given team and project
	 * @param int team_id
	 * @param int statistics ids (array)
	 * @param int project_id
	 * @param int array with multiplication factors for each sids entry
	 * @return array of integers
	 */
	protected function getRosterStatsForIds($team_id, $project_id, $position_id, $sids, $factors = NULL)
	{
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		
		$quoted_sids = array();
		foreach ($sids as $sid) {
			$quoted_sids[] = $db->Quote($sid);
		}		

		if (isset($factors))
		{
			$query = ' SELECT ms.value AS value, tp.person_id, ms.statistic_id ';
		}
		else
		{
			$query = '';
		}
		$query .= ' FROM #__joomleague_team_player AS tp '
		       . ' INNER JOIN #__joomleague_person AS prs ON prs.id=tp.person_id '
		       . ' INNER JOIN #__joomleague_project_team AS pt ON pt.id = tp.projectteam_id '
		       . ' INNER JOIN #__joomleague_match_statistic AS ms ON ms.teamplayer_id = tp.id '
		       . '   AND ms.statistic_id IN ('. implode(',', $quoted_sids) .')'
		       . ' INNER JOIN #__joomleague_project_position AS ppos ON ppos.id = tp.project_position_id ' 
		       . ' INNER JOIN #__joomleague_position AS pos ON pos.id=ppos.position_id ' 
		       . ' INNER JOIN #__joomleague_match AS m ON m.id = ms.match_id '
		       . '   AND m.published = 1 '
		       . ' WHERE pt.team_id = '. $db->Quote($team_id)
		       . '   AND pt.project_id = '. $db->Quote($project_id)
		       . '   AND ppos.position_id = '. $db->Quote($position_id)
			;
		if (isset($factors))
		{
			$db->setQuery($query);
			$stats = $db->loadObjectList();
			// Apply weighting using factors
			$res = array();
			$res['totals'] = new stdclass;
			$res['totals']->value = 0;
			foreach ($stats as $stat)
			{
				$key = array_search($stat->statistic_id, $sids);
				if ($key !== FALSE)
				{
					if (!isset($res[$stat->person_id])) 
					{
						$res[$stat->person_id] = new stdclass;
						$res[$stat->person_id]->person_id = $stat->person_id;
						$res[$stat->person_id]->value = 0;
					}
					$contribution = $factors[$key] * $stat->value;
					$res[$stat->person_id]->value += $contribution;
					$res['totals']->value += $contribution;
				}
			}
		}
		else
		{
			// Determine the statistics per project team player
			$db->setQuery(' SELECT SUM(ms.value) AS value, tp.person_id ' . $query . ' GROUP BY tp.person_id ');
			$res = $db->loadObjectList('person_id');

			// Determine the total statistics for the position_id of the project team
			$db->setQuery(' SELECT SUM(ms.value) AS value ' . $query);
			$res['totals'] = new stdclass;
			$res['totals']->value = $db->loadResult();
			if (!isset($res['totals']->value))
			{
				$res['totals']->value = 0;
			}
		}
		return $res;
	}

	/**
	 * return the number of games the player played for given project
	 * @param int team_id
	 * @param int project_id
	 * @return array of integers
	 */
	protected function getGamesPlayedByProjectTeam($team_id, $project_id, $position_id)
	{
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		
		// Get the number of matches played per teamplayer of the projectteam.
		$query	= ' FROM #__joomleague_match AS m'
				. ' INNER JOIN #__joomleague_match_player AS mp ON mp.match_id=m.id'
				. ' INNER JOIN #__joomleague_team_player AS tp ON tp.id = mp.teamplayer_id'
				. ' INNER JOIN #__joomleague_project_team AS pt ON pt.id=tp.projectteam_id'
				. ' INNER JOIN #__joomleague_project AS p ON p.id=pt.project_id'
				. ' WHERE pt.team_id = '.$team_id
				. '   AND p.id = '.$project_id
				. '   AND m.published = 1'
				. '   AND p.published = 1'
				. '   AND tp.published = 1'
				. '   AND (mp.came_in = 0 || mp.came_in = 1)'
				;

		$db->setQuery(' SELECT tp.person_id, COUNT(DISTINCT m.id) AS value' . $query . ' GROUP BY tp.id');
		$res = $db->loadObjectList('person_id');

		// Determine the total statistics for the position_id of the project team
		$db->setQuery(' SELECT COUNT(DISTINCT m.id) AS value ' . $query);
		$res['totals'] = new stdclass;
		$res['totals']->value = $db->loadResult();
		if (!isset($res['totals']->value))
		{
			$res['totals']->value = 0;
		}

		return $res;
	}

	/**
	 * return the player stats for events per project for given statistics IDs, for given project,
	 * using weighting factors if given
	 * @param int person_id
	 * @param array statistic IDs
	 * @param int project_id, if 0 then the complete career statistics of the player are returned
	 * @return float
	 */
	protected function getRosterStatsForEvents($team_id, $project_id, $position_id, $sids)
	{
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		
		$quoted_sids = array();
		foreach ($sids as $sid) {
			$quoted_sids[] = $db->Quote($sid);
		}		

		// Determine the events for each project team player
		$query = ' FROM #__joomleague_team_player AS tp '
		       . ' INNER JOIN #__joomleague_person AS prs ON prs.id=tp.person_id '
		       . ' INNER JOIN #__joomleague_project_team AS pt ON pt.id = tp.projectteam_id '
		       . ' INNER JOIN #__joomleague_project_position AS ppos ON ppos.id = tp.project_position_id ' 
		       . ' INNER JOIN #__joomleague_position AS pos ON pos.id=ppos.position_id ' 
		       . ' LEFT JOIN #__joomleague_match_event AS es ON es.teamplayer_id = tp.id '
		       . '   AND es.event_type_id IN ('. implode(',', $quoted_sids) .')'
		       . ' LEFT JOIN #__joomleague_match AS m ON m.id = es.match_id '
		       . '   AND m.published = 1 '
		       . ' WHERE pt.team_id = '. $db->Quote($team_id)
		       . '   AND pt.project_id = '. $db->Quote($project_id)
		       . '   AND ppos.position_id = '. $db->Quote($position_id)
		       . '   AND tp.published = 1'
		       ;
		$db->setQuery(' SELECT SUM(es.event_sum) AS value, tp.person_id ' . $query . ' GROUP BY tp.id ');
		$res = $db->loadObjectList('person_id');
		
		// Determine the event totals for the position_id of the project team
		$db->setQuery(' SELECT SUM(es.event_sum) AS value ' . $query);
		$res['totals'] = new stdclass;
		$res['totals']->value = $db->loadResult();
		return $res;
	}

	/**
	 * return the number of games the player played for given project
	 * @param int person_id
	 * @param int project_id, if 0 then the number of played games for the complete career are returned
	 * @return integer
	 */
	protected function getGamesPlayedQuery($project_id, $division_id, $team_id)
	{
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		
		$query 	  = ' SELECT gp.person_id, gp.tpid, COUNT(gp.mid) AS played '
				  . ' FROM ( '
				  . '   SELECT m.id AS mid, tp.person_id, tp.id AS tpid'
				  . '   FROM #__joomleague_team_player AS tp'
				  . '   INNER JOIN #__joomleague_person AS p ON p.id=tp.person_id'
				  . '   INNER JOIN #__joomleague_project_team AS pt ON pt.id=tp.projectteam_id'
				  . '   INNER JOIN #__joomleague_match AS m ON m.projectteam1_id=pt.id OR m.projectteam2_id=pt.id'
				  . '   INNER JOIN #__joomleague_match_player AS mp ON mp.match_id = m.id AND mp.teamplayer_id=tp.id'
				  . '   WHERE pt.project_id=' . $db->Quote($project_id)
				  . '     AND p.published = 1'
				  . '     AND m.published = 1'
				  . '     AND (mp.came_in = 0 || mp.came_in = 1)';
		if ($division_id)
		{
			$query .= ' AND pt.division_id=' . $db->Quote($division_id);
		}
		if ($team_id)
		{
			$query .= ' AND pt.team_id=' . $db->Quote($team_id);
		}
		$query .= ' GROUP BY m.id, tp.id '
				. ' ) AS gp'
				. ' GROUP BY gp.tpid' ;

		return $query;
	}

	function formatZeroValue()
	{
		return number_format(0, $this->getPrecision());
	}
}