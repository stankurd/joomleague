<?php
/**
 * Joomleague
 *
 * @copyright	Copyright (C) 2006-2015 joomleague.at. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @link		http://www.joomleague.at
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die;
use Joomla\CMS\FACTORY;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;
use Joomla\CMS\Filter\OutputFilter;


/**
 * JLXMLExport Model
 *
 * @author	Zoltan Koteles
 * @author	Kurt Norgaz
 */
class JoomleagueModelJLXMLExport extends BaseDatabaseModel
{
	/**
	 * @var int
	 *
	 * @access private
	 */
	private $_project_id = 0;

	/**
	 * @var array
	 *
	 * @access private
	 */
	private $_project = array();

	/**
	 * @var array
	 *
	 * @access private
	 */
	private $_projectteam = array();

	/**
	 * @var array
	 *
	 * @access private
	 */
	private $_projectreferee = array();

	/**
	 * @var array
	 *
	 * @access private
	 */
	private $_projectposition = array();

	/**
	 * @var array
	 *
	 * @access private
	 */
	private $_team = array();

	/**
	 * @var array
	 *
	 * @access private
	 */
	private $_teamplayer = array();

	/**
	 * @var array
	 *
	 * @access private
	 */
	private $_teamstaff = array();

	/**
	 * @var array
	 *
	 * @access private
	 */
	private $_teamtrainingdata = array();

	/**
	 * @var array
	 *
	 * @access private
	 */
	private $_match = array();

	/**
	 * @var array
	 *
	 * @access private
	 */
	private $_club = array();

	/**
	 * @var array
	 *
	 * @access private
	 */
	private $_playground = array();

	/**
	 * @var array
	 *
	 * @access private
	 */
	private $_matchplayer = array();

	/**
	 * @var array
	 *
	 * @access private
	 */
	private $_matchstaff = array();

	/**
	 * @var array
	 *
	 * @access private
	 */
	private $_matchreferee = array();

	/**
	 * @var array
	 *
	 * @access private
	 */
	private $_person = array();

	/**
	 * @var array
	 *
	 * @access private
	 */
	private $_matchevent = array();

	/**
	 * @var array
	 *
	 * @access private
	 */
	private $_eventtype = array();

	/**
	 * @var array
	 *
	 * @access private
	 */
	private $_position = array();

	/**
	 * @var array
	 *
	 * @access private
	 */
	private $_parentposition = array();

	/**
	 * @var array
	 *
	 * @access private
	 */
	private $_matchstaffstatistic = array();

	/**
	 * @var array
	 *
	 * @access private
	 */
	private $_matchstatistic = array();

	/**
	 * @var array
	 *
	 * @access private
	 */
	private $_positionstatistic = array();

	/**
	 * @var array
	 *
	 * @access private
	 */
	private $_statistic = array();

	/**
	 * @var array
	 *
	 * @access private
	 */
	private $_treeto = array();

/**
	 * @var array
	 *
	 * @access private
	 */
	private $_treetonode = array();

/**
	 * @var array
	 *
	 * @access private
	 */
	private $_treetomatch = array();

	/**
	 * exportData
	 *
	 * Export the active project data to xml
	 *
	 * @access public
	 *
	 * @return null
	 */
	public function exportData()
	{
		$app	= Factory::getApplication();
		$option = $app->input->get('option');
		
		$this->_project_id = $app->getUserState($option.'project');
		if (empty($this->_project_id) || $this->_project_id == 0)
		{
			$app->enqueueMessage(Text::_('COM_JOOMLEAGUE_ADMIN_XML_EXPORT_MODEL_SELECT_PROJECT'), 'warning');
				
		}
		else
		{
			$output = '<?xml version="1.0" encoding="utf-8"?>' . "\n";

			// open the project
			$output .= "<project>\n";

			// get the version of JoomLeague
			$output .= $this->_addToXml($this->_getJoomLeagueVersion());

			// get the project datas
			$output .= $this->_addToXml($this->_getProjectData());

			// get sportstype data of project
			$output .= $this->_addToXml($this->_getSportsTypeData());

			// get league data of project
			$output .= $this->_addToXml($this->_getLeagueData());

			// get season data of project
			$output .= $this->_addToXml($this->_getSeasonData());

			// get the template data
			$output .= $this->_addToXml($this->_getTemplateData());

			// get divisions data
			$output .= $this->_addToXml($this->_getDivisionData());

			// get the projectteams data
			$output .= $this->_addToXml($this->_getprojectteamData());

			// get referee data of project
			$output .= $this->_addToXml($this->_getProjectRefereeData());

			// get position data of project
			$output .= $this->_addToXml($this->_getProjectPositionData());

			// get the teams data
			$output .= $this->_addToXml($this->_getTeamData());

			// get the clubs data
			$output .= $this->_addToXml($this->_getClubData());

			// get the rounds data
			$output .= $this->_addToXml($this->_getRoundData());

			// get the matches data
			$output .= $this->_addToXml($this->_getMatchData());

			// get the playground data
			$output .= $this->_addToXml($this->_getPlaygroundData());

			// get the team player data
			$output .= $this->_addToXml($this->_getTeamPlayerData());

			// get the team staff data
			$output .= $this->_addToXml($this->_getTeamStaffData());

			// get the team training data
			$output .= $this->_addToXml($this->_getTeamTrainingData());

			// get the match player data
			$output .= $this->_addToXml($this->_getMatchPlayerData());

			// get the match staff data
			$output .= $this->_addToXml($this->_getMatchStaffData());

			// get the match referee data
			$output .= $this->_addToXml($this->_getMatchRefereeData());

			// get the positions data
			$output .= $this->_addToXml($this->_getPositionData());

			// get the positions parent data
			$output .= $this->_addToXml($this->_getParentPositionData());

			// get ALL persons data for Export
			$output .= $this->_addToXml($this->_getPersonData());

			// get the match events data
			$output .= $this->_addToXml($this->_getMatchEvent());

			// get the event types data
			$output .= $this->_addToXml($this->_getEventType());

			// get the position eventtypes data
			$output .= $this->_addToXml($this->_getPositionEventType());

			// get the match_statistic data
			$output .= $this->_addToXml($this->_getMatchStatistic());

			// get the match_staff_statistic data
			$output .= $this->_addToXml($this->_getMatchStaffStatistic());

			// get the position_statistic data
			$output .= $this->_addToXml($this->_getPositionStatistic());

			// get the statistic data
			$output .= $this->_addToXml($this->_getStatistic());

			// get the treeto data
			$output .= $this->_addToXml($this->_getTreetoData());

			// get the treetonode data
			$output .= $this->_addToXml($this->_getTreetoNodeData());

			// get the treetomatch data
			$output .= $this->_addToXml($this->_getTreetoMatchData());

			// close the project
			$output .= '</project>';

			// download the generated xml
			$this->downloadXml($output,"");

			// close the application
			$app->close();
		}
	}

	/**
	 * downloadXml
	 *
	 * Pop-up the browser's download window with the generated xml file
	 *
	 * @param string $data generated xml data
	 *
	 * @return null
	 */
	function downloadXml($data, $table)
	{
		$app	= Factory::getApplication();
		$option = $app->input->get('option');
		jimport('joomla.filter.output');
		$filename = $this->_getIdFromData('name', $this->_project);
		if(empty($filename)) {
			$this->_project_id = $app->getUserState($option.'project');
			if (empty($this->_project_id) || $this->_project_id == 0)
			{
				 $app->enqueueMessage(Text::_('COM_JOOMLEAGUE_ADMIN_XML_EXPORT_MODEL_SELECT_PROJECT'), 'warning');
				$filename[0] = $table;
			}
			else {
				// get the project datas
				$this->_getProjectData();
				$filename = $this->_getIdFromData('name', $this->_project);
				$filename[0] = $filename[0]."-".$table;
			}
		}
		/**/
		header('Content-type: "text/xml"; charset="utf-8"');
		header("Content-Disposition: attachment; filename=\"" . OutputFilter::stringURLSafe($filename[0])."-".date("ymd-His"). ".jlg\"");
		header("Expires: " . gmdate("D, d M Y H:i:s", mktime(date("H") + 2, date("i"), date("s"), date("m"), date("d"), date("Y"))) . " GMT");
		header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
		header("Cache-Control: no-cache, must-revalidate");
		header("Pragma: no-cache");
		/**/
		echo $data;
	}
	
	/**
	 * Add data to the xml
	 *
	 * @param array $data data what we want to add in the xml
	 *
	 * @access public
	 *
	 * @return void
	 */
	public function _addToXml($data)
	{
		if (is_array($data) && count($data) > 0)
		{
			$object = $data[0]['object'];
			$output = '';
			foreach ($data as $name => $value)
			{
				$output .= "<record object=\"" . JoomleagueHelper::stripInvalidXml($object) . "\">\n";
				foreach ($value as $key => $data)
				{
					if (!is_null($data) && !(substr($key, 0, 1) == "_") && $key != "object")
					{
						$output .= "  <$key><![CDATA[" . JoomleagueHelper::stripInvalidXml(trim($data)) . "]]></$key>\n";
					}
				}
				$output .= "</record>\n";
			}
			return $output;
		}
		return false;
	}

	/**
	 * _getIdFromData
	 *
	 * Get only the ids array from the full array
	 *
	 * @param string $id	field name what we find in the array
	 * @param array  $array the array where we find the field
	 *
	 * @access private
	 *
	 * @return void
	 */
	private function _getIdFromData($id,$array)
	{
		if (is_array($array) && count($array) > 0)
		{
			$ids = array();
			foreach ($array as $key => $value)
			{
				if (array_key_exists($id, $value) && $value[$id] != '')
				{
					$ids[] = $value[$id];
				}
			}
			return $ids;
		}
		return false;
	}

	/**
	 * _getJoomLeagueVersion
	 *
	 * Get the version data and actual date, time and
	 * Joomla systemName from the joomleague_version table
	 *
	 * @access public
	 *
	 * @return array
	 */
	public function _getJoomLeagueVersion()
	{
		$db	= Factory::getDbo();
        $query = $db->getQuery(true);
		$exportRoutine='2012-08-09 21:00:00';
		$query = "SELECT CONCAT(major,'.',minor,'.',build,'.',revision) AS version 
					FROM #__joomleague_version ORDER BY date DESC LIMIT 1";
		$db->setQuery($query);
		$db->execute();
		if ($db->getNumRows() > 0)
		{
			$result = $db->loadAssocList();
			$result[0]['exportRoutine']=$exportRoutine;
			$result[0]['exportDate']=date('Y-m-d');
			$result[0]['exportTime']=date('H:i:s');
			$result[0]['exportSystem']=Factory::getConfig()->get('config.sitename');
			$result[0]['object']='JoomLeagueVersion';
			return $result;
		}
		return false;
	}

	/**
	 * _getProjectData
	 *
	 * Get the project data from the joomleague table
	 *
	 * @access private
	 *
	 * @return array
	 */
	private function _getProjectData()
	{
        $db = Factory::getDbo();
        $query = $db->getQuery(true);
		$query->select('*')
		->from('#__joomleague_project')
		->where('id = ' . $this->_project_id );
		$db->setQuery($query);
		$db->execute();
		if ($db->getNumRows() > 0)
		{
			$result = $db->loadAssocList();
			$result[0]['object'] = 'JoomLeague20';
			$this->_project = $result;
			return $result;
		}
		return false;
	}

	/**
	 * _getTemplateData
	 *
	 * Get the template data from the joomleague table
	 *
	 * @access private
	 *
	 * @return array
	 */
	private function _getTemplateData()
	{
		$db	= Factory::getDbo();
        $query = $db->getQuery(true);
		// this is the master template
		if ($this->_project[0]['master_template']==0)
		{
			$master_template_id = $this->_project_id;
		}
		else
		{
			$master_template_id = $this->_project[0]['master_template'];
		}

		$query->select('*')
		->from('#__joomleague_template_config')
		->where(' project_id=' . $master_template_id);
		$db->setQuery($query);
		$db->execute();
		if ($db->getNumRows() > 0)
		{
			$result = $db->loadAssocList();
			$result[0]['object'] = 'Template';

			return $result;
		}
		return false;
	}

	/**
	 * _getLeagueData
	 *
	 * Get the league data from the joomleague_league table
	 *
	 * @access private
	 *
	 * @return array
	 */
	private function _getLeagueData()
	{
		$db	= Factory::getDbo();
        $query = $db->getQuery(true);
		$query->select('*')
		->from ('#__joomleague_league') 
		->where ('id=' . $this->_project[0]['league_id']);
		$db->setQuery($query);
		$db->execute();
		if ($db->getNumRows() > 0)
		{
			$result = $db->loadAssocList();
			$result[0]['object'] = 'League';

			return $result;
		}
		return false;
	}

	/**
	 * _getSportsTypeData
	 *
	 * Get the sportstype data from the joomleague_sports_type table
	 *
	 * @access private
	 *
	 * @return array
	 */
	private function _getSportsTypeData()
	{
		$db	= Factory::getDbo();
        $query = $db->getQuery(true);
		$query->select('*')
		->from('#__joomleague_sports_type') 
		->where('id=' . $this->_project[0]['sports_type_id']);
		$db->setQuery($query);
		$db->execute();
		if ($db->getNumRows() > 0)
		{
			$result = $db->loadAssocList();
			$result[0]['object'] = 'SportsType';

			return $result;
		}
		return false;
	}

	/**
	 * _getSeasonData
	 *
	 * Get the season data from the joomleague_season table
	 *
	 * @access private
	 *
	 * @return array
	 */
	private function _getSeasonData()
	{
		$db	= Factory::getDbo();
        $query = $db->getQuery(true);
		$query->select('*')
		->from('#__joomleague_season') 
		->where('id=' . $this->_project[0]['season_id']);
		$db->setQuery($query);
		$db->execute();
		if ($db->getNumRows() > 0)
		{
			$result = $db->loadAssocList();
			$result[0]['object'] = 'Season';

			return $result;
		}
		return false;
	}

	/**
	 * _getDivisionData
	 *
	 * Get the division data from the joomleague_divisions table
	 *
	 * @access private
	 *
	 * @return array
	 */
	private function _getDivisionData()
	{
		$db	= Factory::getDbo();
        $query = $db->getQuery(true);
		$query->select('*')
		->from('#__joomleague_division') 
		->where('project_id=' . $this->_project_id);
		$db->setQuery($query);
		$db->execute();
		if ($db->getNumRows() > 0)
		{
			$result = $db->loadAssocList();
			$result[0]['object'] = 'LeagueDivision';

			return $result;
		}
		return false;
	}

	/**
	 * _getprojectteamData
	 *
	 * Get the projectteam data from the joomleague_team_joomleague table
	 *
	 * @access private
	 *
	 * @return array
	 */
	private function _getprojectteamData()
	{
		$db	= Factory::getDbo();
        $query = $db->getQuery(true);
		$query->select('*')
		->from('#__joomleague_project_team')
		->where('project_id=' . $this->_project_id);
		$db->setQuery($query);
		$db->execute();
		if ($db->getNumRows() > 0)
		{
			$result = $db->loadAssocList();
			$result[0]['object'] = 'ProjectTeam';
			$this->_projectteam = $result;
			return $result;
		}
		return false;
	}

	/**
	 * _getProjectPositionData
	 *
	 * Get the season data from the joomleague_season table
	 *
	 * @access private
	 *
	 * @return array
	 */
	private function _getProjectPositionData()
	{
		$db	= Factory::getDbo();
        $query = $db->getQuery(true);
		$query->select('*')
		->from('#__joomleague_project_position') 
		->where('project_id=' . $this->_project_id);
		$db->setQuery($query);
		$db->execute();
		if ($db->getNumRows() > 0)
		{
			$result = $db->loadAssocList();
			$result[0]['object'] = 'ProjectPosition';
			$this->_projectposition = $result;
			return $result;
		}
		return false;
	}

	/**
	 * _getProjectRefereeData
	 *
	 * Get the season data from the joomleague_season table
	 *
	 * @access private
	 *
	 * @return array
	 */
	private function _getProjectRefereeData()
	{
		$db	= Factory::getDbo();
        $query = $db->getQuery(true);
		$query->select('*')
		->from('#__joomleague_project_referee')
		->where('project_id=' . $this->_project_id);
		$db->setQuery($query);
		$db->execute();
		if ($db->getNumRows() > 0)
		{
			$result = $db->loadAssocList();
			$result[0]['object'] = 'ProjectReferee';
			$this->_projectreferee = $result;
			return $result;
		}
		return false;
	}

	/**
	 * _getTeamData
	 *
	 * Get the team data from the joomleague_teams table
	 *
	 * @access private
	 *
	 * @return void
	 */
	private function _getTeamData()
	{
		$db	= Factory::getDbo();
        $query = $db->getQuery(true);
		$team_ids = $this->_getIdFromData('team_id', $this->_projectteam);

		if (is_array($team_ids) && count($team_ids) > 0)
		{
			$ids = implode(",", array_unique($team_ids));

			$query->select('*')
			->from('#__joomleague_team') 
			->where(' id IN (' . $ids .')')
			->order('name');
			$db->setQuery($query);
			$db->execute();
			if ($db->getNumRows() > 0)
			{
				$result = $db->loadAssocList();
				$result[0]['object'] = 'JL_Team';
				$this->_team = $result;
				return $result;
			}
			return false;
		}
		return false;
	}

	/**
	 * _getClubData
	 *
	 * Get the club data from the joomleague_clubs table
	 *
	 * @access private
	 *
	 * @return void
	 */
	private function _getClubData()
	{
		$db	= Factory::getDbo();
        $query = $db->getQuery(true);
		$cIDs=array();

		$teamClub_ids = $this->_getIdFromData('club_id', $this->_team);
		if (is_array($teamClub_ids)){$cIDs=array_merge($cIDs,$teamClub_ids);}

		//$playgroundClub_ids = $this->_getIdFromData('club_id',$this->_teamstaff);
		//if (is_array($playgroundClub_ids)){$cIDs=array_merge($cIDs,$playgroundClub_ids);}

		if (is_array($cIDs) && count($cIDs) > 0)
		{
			$ids = implode(",", array_unique($cIDs));
			$query->select('*')
			->from('#__joomleague_club') 
			->where(' id IN (' . $ids .')')
			->order('name');
			$db->setQuery($query);
			$db->execute();
			if ($db->getNumRows() > 0)
			{
				$result = $db->loadAssocList();
				$result[0]['object'] = 'Club';
				$this->_club = $result;
				return $result;
			}
			return false;
		}
		return false;
	}

	/**
	 * _getRoundData
	 *
	 * Get the rounds data from the joomleague_rounds table
	 *
	 * @access private
	 *
	 * @return array
	 */
	private function _getRoundData()
	{
		$db	= Factory::getDbo();
        $query = $db->getQuery(true);
		$query->select('*')
		->from('#__joomleague_round') 
		->where('project_id=' . $this->_project_id)
		->order('id ASC');
		$db->setQuery($query);
		$db->execute();
		if ($db->getNumRows() > 0)
		{
			$result = $db->loadAssocList();
			$result[0]['object'] = 'Round';

			return $result;
		}
		return false;
	}

	/**
	 * _getMatchData
	 *
	 * Get the matches data from the joomleague_matches table
	 *
	 * @access private
	 *
	 * @return array
	 */
	private function _getMatchData()
	{
		$db	= Factory::getDbo();
        $query = $db->getQuery(true);
		$query->select('m.*') 
		->from('#__joomleague_match as m')
		->join('INNER', '#__joomleague_round as r ON r.id= m.round_id')
		->where('r.project_id=' . $this->_project_id);
		$db->setQuery($query);
		$db->execute();
		if ($db->getNumRows() > 0)
		{
			$result = $db->loadAssocList();
			$result[0]['object'] = 'Match';
			$this->_match = $result;
			return $result;
		}
		return false;
	}

	/**
	 * _getPlaygroundData
	 *
	 * Get the playgrounds data from the joomleague_playgrounds table
	 *
	 * @access private
	 *
	 * @return array
	 */
	private function _getPlaygroundData()
	{
		$db	= Factory::getDbo();
        $query = $db->getQuery(true);
		$pgIDs=array();
		$clubsPlayground_ids = $this->_getIdFromData('standard_playground',$this->_club);
		if (is_array($clubsPlayground_ids)){$pgIDs=array_merge($pgIDs,$clubsPlayground_ids);}

		$projectTeamsPlayground_ids = $this->_getIdFromData('standard_playground',$this->_projectteam);
		if (is_array($projectTeamsPlayground_ids)){$pgIDs=array_merge($pgIDs,$projectTeamsPlayground_ids);}

		$matchPlayground_ids = $this->_getIdFromData('playground_id',$this->_match);
		if (is_array($matchPlayground_ids)){$pgIDs=array_merge($pgIDs,$matchPlayground_ids);}

		if (is_array($pgIDs) && count($pgIDs) > 0)
		{
			$ids = implode(",",array_unique($pgIDs));
			$query->select('*')
			->from('#__joomleague_playground') 
			->where(' id IN (' . $ids .')');
			$db->setQuery($query);
			$db->execute();
			if ($db->getNumRows() > 0)
			{
				$result = $db->loadAssocList();
				$result[0]['object'] = 'Playground';
				$this->_playground = $result;
				return $result;
			}
			return false;
		}
		return false;
	}

	/**
	 * _getTeamPlayerData
	 *
	 * Get the match players data from the joomleague_match_player table
	 *
	 * @access private
	 *
	 * @return array
	 */
	private function _getTeamPlayerData()
	{
		$db	= Factory::getDbo();
        $query = $db->getQuery(true);
		$teamplayer_ids = $this->_getIdFromData('id', $this->_projectteam);

		if (is_array($teamplayer_ids) && count($teamplayer_ids) > 0)
		{
			$ids = implode(",", array_unique($teamplayer_ids));
			$query->select('*')
			->from('#__joomleague_team_player') 
			->where('projectteam_id IN (' . $ids .')');
			$db->setQuery($query);
			$db->execute();
			if ($db->getNumRows() > 0)
			{
				$result = $db->loadAssocList();
				$result[0]['object'] = 'TeamPlayer';
				$this->_teamplayer = $result;

				return $result;
			}
			return false;
		}
		return false;
	}

	/**
	 * _getTeamTrainingData
	 *
	 * Get the projectteams training data from the joomleague_team_trainingdata table
	 *
	 * @access private
	 *
	 * @return array
	 */
	private function _getTeamTrainingData()
	{
		$db	= Factory::getDbo();
        $query = $db->getQuery(true);
		$teamtraining_ids = $this->_getIdFromData('id',$this->_projectteam);

		if (is_array($teamtraining_ids) && count($teamtraining_ids) > 0)
		{
			$ids = implode(',',array_unique($teamtraining_ids));

			$query->select('*') 
			->from('#__joomleague_team_trainingdata') 
			->where('project_team_id IN (' . $ids .')');
			$db->setQuery($query);
			$db->execute();
			if ($db->getNumRows() > 0)
			{
				$result = $db->loadAssocList();
				$result[0]['object'] = 'TeamTraining';
				$this->_teamtrainingdata = $result;
				return $result;
			}
			return false;
		}
		return false;
	}

	/**
	 * _getTeamStaffData
	 *
	 * Get the match players data from the joomleague_match_player table
	 *
	 * @access private
	 *
	 * @return array
	 */
	private function _getTeamStaffData()
	{
		$db	= Factory::getDbo();
        $query = $db->getQuery(true);
		$teamstaff_ids = $this->_getIdFromData('id', $this->_projectteam);

		if (is_array($teamstaff_ids) && count($teamstaff_ids) > 0)
		{
			$ids = implode(",", array_unique($teamstaff_ids));

			$query->select('*') 
			->from('#__joomleague_team_staff')
			->where(' projectteam_id IN (' . $ids .')');
			$db->setQuery($query);
			$db->execute();
			if ($db->getNumRows() > 0)
			{
				$result = $db->loadAssocList();
				$result[0]['object'] = 'TeamStaff';
				$this->_teamstaff = $result;

				return $result;
			}
			return false;
		}
		return false;
	}

	/**
	 * _getMatchPlayerData
	 *
	 * Get the match players data from the joomleague_match_player table
	 *
	 * @access private
	 *
	 * @return array
	 */
	private function _getMatchPlayerData()
	{
		$db	= Factory::getDbo();
        $query = $db->getQuery(true);
		$match_ids = $this->_getIdFromData('id', $this->_match);

		if (is_array($match_ids) && count($match_ids) > 0)
		{
			$ids = implode(",", array_unique($match_ids));

			$query->select('*') 
			->from('#__joomleague_match_player') 
			->where('match_id IN (' . $ids .')');
			$db->setQuery($query);
			$db->execute();
			if ($db->getNumRows() > 0)
			{
				$result = $db->loadAssocList();
				$result[0]['object'] = 'MatchPlayer';
				$this->_matchplayer = $result;

				return $result;
			}
			return false;
		}
		return false;
	}

	/**
	 * _getMatchStaffData
	 *
	 * Get the match staffs data from the joomleague_match_staff table
	 *
	 * @access private
	 *
	 * @return array
	 */
	private function _getMatchStaffData()
	{
		$db	= Factory::getDbo();
        $query = $db->getQuery(true);
		$match_ids = $this->_getIdFromData('id', $this->_match);

		if (is_array($match_ids) && count($match_ids) > 0)
		{
			$ids = implode(",", array_unique($match_ids));

			$query->select('*') 
			->from('#__joomleague_match_staff') 
			->where('match_id IN (' . $ids .')');
			$db->setQuery($query);
			$db->execute();
			if ($db->getNumRows() > 0)
			{
				$result = $db->loadAssocList();
				$result[0]['object'] = 'MatchStaff';
				$this->_matchstaff = $result;

				return $result;
			}
			return false;
		}
		return false;
	}

	/**
	 * _getMatchRefereeData
	 *
	 * Get the match referees data from the joomleague_match_referee table
	 *
	 * @access private
	 *
	 * @return array
	 */
	private function _getMatchRefereeData()
	{
		$db	= Factory::getDbo();
        $query = $db->getQuery(true);
		$match_ids = $this->_getIdFromData('id', $this->_match);

		if (is_array($match_ids) && count($match_ids) > 0)
		{
			$ids = implode(",", array_unique($match_ids));

			$query->select('*') 
			->from('#__joomleague_match_referee') 
			->where(' match_id IN (' . $ids .')');
			$db->setQuery($query);
			$db->execute();
			if ($db->getNumRows() > 0)
			{
				$result = $db->loadAssocList();
				$result[0]['object'] = 'MatchReferee';
				$this->_matchreferee = $result;

				return $result;
			}
			return false;
		}
		return false;
	}

	/**
	 * _getPositionData
	 *
	 * Get the positions data from the joomleague_playgrounds table
	 *
	 * @access private
	 *
	 * @return array
	 */
	private function _getPositionData()
	{
		$db	= Factory::getDbo();
        $query = $db->getQuery(true);
		$position_ids = $this->_getIdFromData('position_id', $this->_projectposition);

		if (is_array($position_ids) && count($position_ids) > 0)
		{
			$ids = implode(",", array_unique($position_ids));

			$query->select('*') 
			->from('#__joomleague_position') 
			->where('id IN (' . $ids .')');
			$db->setQuery($query);
			$db->execute();
			if ($db->getNumRows() > 0)
			{
				$result = $db->loadAssocList();
				$result[0]['object'] = 'Position';
				$this->_position = $result;

				return $result;
			}
			return false;
		}
		return false;
	}

	/**
	 * _getParentPositionData
	 *
	 * Get the parent positions data from the joomleague_positions table
	 *
	 * @access private
	 *
	 * @return array
	 */
	private function _getParentPositionData()
	{
		$db	= Factory::getDbo();
        $query = $db->getQuery(true);
		$position_ids = $this->_getIdFromData('parent_id', $this->_position);

		if (is_array($position_ids) && count($position_ids) > 0)
		{
			$ids = implode(",", array_unique($position_ids));

			$query->select('*') 
			->from('#__joomleague_position')
			->where('id IN (' . $ids .')');
			$db->setQuery($query);
			$db->execute();
			if ($db->getNumRows() > 0)
			{
				$result = $db->loadAssocList();
				$result[0]['object'] = 'ParentPosition';
				$this->_parentposition = $result;

				return $result;
			}
			return false;
		}
		return false;
	}

	/**
	 * getPersonData
	 *
	 * Get the persons data from the joomleague_persons table
	 *
	 * @access private
	 *
	 * @return array
	 */
	private function _getPersonData()
	{
		$db	= Factory::getDbo();
        $query = $db->getQuery(true);
		$pgIDs=array();

		$teamPlayer_ids = $this->_getIdFromData('person_id',$this->_teamplayer);
		if (is_array($teamPlayer_ids)){$pgIDs=array_merge($pgIDs,$teamPlayer_ids);}

		$teamStaff_ids = $this->_getIdFromData('person_id',$this->_teamstaff);
		if (is_array($teamStaff_ids)){$pgIDs=array_merge($pgIDs,$teamStaff_ids);}

		$projectReferee_ids = $this->_getIdFromData('person_id',$this->_projectreferee);
		if (is_array($projectReferee_ids)){$pgIDs=array_merge($pgIDs,$projectReferee_ids);}

		if (is_array($pgIDs) && count($pgIDs) > 0)
		{
			$ids = implode(",",array_unique($pgIDs));
			$query->select('*')
			->from('#__joomleague_person') 
			->where('id IN (' . $ids .')');
			$db->setQuery($query);
			$db->execute();
			if ($db->getNumRows() > 0)
			{
				$result = $db->loadAssocList();
				$result[0]['object'] = 'Person';
				$this->_person = $result;

				return $result;
			}
			return false;
		}
		return false;
	}

	/**
	 * _getMatchEvent
	 *
	 * Get the match events data from the joomleague_match_events table
	 *
	 * @access private
	 *
	 * @return array
	 */
	private function _getMatchEvent()
	{
		$db	= Factory::getDbo();
        $query = $db->getQuery(true);
		$match_ids = $this->_getIdFromData('id', $this->_match);

		if (is_array($match_ids) && count($match_ids) > 0)
		{
			$ids = implode(",", array_unique($match_ids));

			$query->select('*') 
			->from('#__joomleague_match_event') 
			->where('match_id IN (' . $ids .')');
			$db->setQuery($query);
			$db->execute();
			if ($db->getNumRows() > 0)
			{
				$result = $db->loadAssocList();
				$result[0]['object'] = 'MatchEvent';
				$this->_matchevent = $result;
				return $result;
			}
			return false;
		}
		return false;
	}

	/**
	 * _getEventType
	 *
	 * Get the event types data from the joomleague_eventtypes table
	 *
	 * @access private
	 *
	 * @return array
	 */
	private function _getEventType()
	{
		$db	= Factory::getDbo();
        $query = $db->getQuery(true);
		$eventtype_ids = $this->_getIdFromData('event_type_id', $this->_matchevent);

		if (is_array($eventtype_ids) && count($eventtype_ids) > 0)
		{
			$ids = implode(",", array_unique($eventtype_ids));

			$query->select('*') 
			->from('#__joomleague_eventtype') 
			->where('id IN (' . $ids .')');
			$db->setQuery($query);
			$db->execute();
			if ($db->getNumRows() > 0)
			{
				$result = $db->loadAssocList();
				$result[0]['object'] = 'EventType';
				$this->_eventtype = $result;
				return $result;
			}
			return false;
		}
		return false;
	}

	/**
	 * _getPositionEventType
	 *
	 * Get the position event types data from the joomleague_position_eventtype table
	 *
	 * @access private
	 *
	 * @return array
	 */
	private function _getPositionEventType()
	{
		$db	= Factory::getDbo();
        $query = $db->getQuery(true);
		$eventtype_ids	= $this->_getIdFromData('id', $this->_eventtype);
		$position_ids	= $this->_getIdFromData('id', $this->_position);

		if (is_array($eventtype_ids) && count($eventtype_ids) > 0)
		{
			$event_ids		= implode(",", array_unique($eventtype_ids));
			$position_ids	= implode(",", array_unique($position_ids));

			$query->select('*') 
			->from('#__joomleague_position_eventtype') 
			->where('eventtype_id IN (' . $event_ids . ') AND position_id IN (' . $position_ids . ')');
			$db->setQuery($query);
			$db->execute();
			if ($db->getNumRows() > 0)
			{
				$result = $db->loadAssocList();
				$result[0]['object'] = 'PositionEventType';
				return $result;
			}
			return false;
		}
		return false;
	}

	/**
	 * _getPositionStatistic
	 *
	 * Get the statisctics data from the joomleague_position_statistic table
	 *
	 * @access private
	 *
	 * @return void
	 */
	private function _getPositionStatistic()
	{
		$db	= Factory::getDbo();
        $query = $db->getQuery(true);
		$position_ids = $this->_getIdFromData('id', $this->_position);

		if (is_array($position_ids) && count($position_ids) > 0)
		{
			$ids = implode(",", array_unique($position_ids));

			$query->select('*') 
			->from('#__joomleague_position_statistic') 
			->where('position_id IN (' . $ids . ')');
			$db->setQuery($query);
			$db->execute();
			if ($db->getNumRows() > 0)
			{
				$result = $db->loadAssocList();
				$result[0]['object'] = 'PositionStatistic';
				$this->_positionstatistic = $result;
				return $result;
			}
			return false;
		}
		return false;
	}

	/**
	 * _getMatchStatistic
	 *
	 * Get the statisctics data from the joomleague_match_statistic table
	 *
	 * @access private
	 *
	 * @return void
	 */
	private function _getMatchStatistic()
	{
		$db	= Factory::getDbo();
        $query = $db->getQuery(true);
		$match_ids = $this->_getIdFromData('id', $this->_match);

		if (is_array($match_ids) && count($match_ids) > 0)
		{
			$ids = implode(",", array_unique($match_ids));

			$query->select('*') 
			->from('#__joomleague_match_statistic') 
			->where('match_id IN (' . $ids . ')');
			$db->setQuery($query);
			$db->execute();
			if ($db->getNumRows() > 0)
			{
				$result = $db->loadAssocList();
				$result[0]['object'] = 'MatchStatistic';
				$this->_matchstatistic = $result;
				return $result;
			}
			return false;
		}
		return false;
	}

	/**
	 * _getMatchStaffStatistic
	 *
	 * Get the statisctics data from the joomleague_match_staff_statistic table
	 *
	 * @access private
	 *
	 * @return void
	 */
	private function _getMatchStaffStatistic()
	{
		$db	= Factory::getDbo();
        $query = $db->getQuery(true);
		$match_ids = $this->_getIdFromData('id', $this->_match);

		if (is_array($match_ids) && count($match_ids) > 0)
		{
			$ids = implode(",", array_unique($match_ids));

			$query->select('*')
			->from('#__joomleague_match_staff_statistic') 
			->where('match_id IN (' . $ids . ')');
			$db->setQuery($query);
			$db->execute();
			if ($db->getNumRows() > 0)
			{
				$result = $db->loadAssocList();
				$result[0]['object'] = 'MatchStaffStatistic';
				$this->_matchstaffstatistic = $result;
				return $result;
			}
			return false;
		}
		return false;
	}

	/**
	 * _getStatistic
	 *
	 * Get the statistic data from the joomleague_statistic table
	 *
	 * @access private
	 *
	 * @return void
	 */
	private function _getStatistic()
	{
		$db	= Factory::getDbo();
        $query = $db->getQuery(true);
		$sIDs=array();

		$matchstatistic_ids = $this->_getIdFromData('statistic_id',$this->_matchstatistic);	// Get all ids of match statistics assigned to the actual project
		if (is_array($matchstatistic_ids)){$sIDs=array_merge($sIDs,$matchstatistic_ids);}

		$matchstaffstatistic_ids = $this->_getIdFromData('statistic_id',$this->_matchstaffstatistic);	// Get all ids of match staff statistic assigned to the actual project
		if (is_array($matchstaffstatistic_ids)){$sIDs=array_merge($sIDs,$matchstaffstatistic_ids);}

		$positionstatistic_ids = $this->_getIdFromData('statistic_id',$this->_positionstatistic);	// Get all ids of position statistic assigned to the actual project
		if (is_array($positionstatistic_ids)){$sIDs=array_merge($sIDs,$positionstatistic_ids);}

		if (is_array($sIDs) && count($sIDs) > 0)
		{
			$ids = implode(",",array_unique($sIDs));
			$query->select('*') 
			->from('#__joomleague_statistic') 
			->where('id IN (' . $ids . ')');
			$db->setQuery($query);
			$db->execute();
			if ($db->getNumRows() > 0)
			{
				$result = $db->loadAssocList();
				$result[0]['object'] = 'Statistic';
				$this->_person = $result;

				return $result;
			}
			return false;
		}
		return false;
	}

	private function _getTreetoData()
	{
		$db	= Factory::getDbo();
        $query = $db->getQuery(true);
		$query->select('*') 
		->from('#__joomleague_treeto') 
		->where('project_id=' . $this->_project_id);
		$db->setQuery($query);
		$db->execute();
		if ($db->getNumRows() > 0)
		{
			$result = $db->loadAssocList();
			$result[0]['object'] = 'Treeto';
			$this->_treeto = $result;

				return $result;
		}
		return false;
	}

	private function _getTreetoNodeData()
	{
		$db	= Factory::getDbo();
        $query = $db->getQuery(true);
		$treeto_ids = $this->_getIdFromData('id', $this->_treeto);

		if (is_array($treeto_ids) && count($treeto_ids) > 0)
		{
			$ids = implode(",", array_unique($treeto_ids));

			$query->select('*') 
			->from('#__joomleague_treeto_node') 
			->where('treeto_id IN (' . $ids . ')');
			$db->setQuery($query);
			$db->execute();
			if ($db->getNumRows() > 0)
			{
				$result = $db->loadAssocList();
				$result[0]['object'] = 'TreetoNode';
				$this->_treetonode = $result;

				return $result;
			}
			return false;
		}
		return false;
	}

	private function _getTreetoMatchData()
	{
		$db	= Factory::getDbo();
        $query = $db->getQuery(true);
		$treetonode_ids = $this->_getIdFromData('id', $this->_treetonode);

		if (is_array($treetonode_ids) && count($treetonode_ids) > 0)
		{
			$ids = implode(",", array_unique($treetonode_ids));

			$query->select('*')
			->from('#__joomleague_treeto_match') 
			->where('node_id IN (' . $ids . ')');
			$db->setQuery($query);
			$db->execute();
			if ($db->getNumRows() > 0)
			{
				$result = $db->loadAssocList();
				$result[0]['object'] = 'TreetoMatch';
				$this->_treetomatch = $result;

				return $result;
			}
			return false;
		}
		return false;
	}
}
