<?php
/**
 * Joomleague
 * @subpackage	Module-NavigationMenu
 *
 * @copyright	Copyright (C) 2006-2015 joomleague.at. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @link		http://www.joomleague.at
 */
use Joomla\CMS\Factory;

defined('_JEXEC') or die;

class modJoomleagueNavigationMenuHelper {
	
	protected $_params;
	protected $_db;
	protected $_project_id;
	protected $_team_id;
	protected $_division_id=0;
	protected $_round_id=null;
	protected $_teamoptions;
	protected $_project;
	
	public function __construct($params)
	{
	    $app = Factory::getApplication();
		$this->_params = $params;
		$this->_db = Factory::getDbo();
		
		if ($app->input->getCmd('option') == 'com_joomleague') {
			$p = $app->input->getInt('p', $params->get('default_project_id'));
		}
		else {
			$p = $params->get('default_project_id');
		}
		$this->_project_id 		= intval($p);
		$this->_project 		= $this->getProject();
		$this->_round_id   		= $app->input->getInt('r');
		$this->_division_id   	= $app->input->getInt('division',0);
		$this->_team_id   		= $app->input->getInt('tid',0);
	}
	
	public function getSeasonId()
	{
		if ($this->getProject()) {
			return $this->getProject()->season_id;
		}
		else {
			return 0;
		}
	}
	
	public function getLeagueId()
	{
		if ($this->getProject()) {
			return $this->getProject()->league_id;
		}
		else {
			return 0;
		}
	}

	public function getDivisionId()
	{
		return $this->_division_id;
	}

	public function getTeamId()
	{
		return $this->_team_id;
	}
	
	/**
	 * returns the selector for season
	 *
	 * @return string html select
	 */
	public function getSeasonSelect()
	{
		$options = array(JHtml::_('select.option', 0, JText::_($this->getParam('seasons_text'))));
		$query = ' SELECT s.id AS value, s.name AS text '
				. ' FROM #__joomleague_season AS s '
				;
		$this->_db->setQuery($query);
		$res = $this->_db->loadObjectList();
		if ($res) {
			$options = array_merge($options, $res);
		}
		return JHtml::_('select.genericlist', $options, 's', 'class="jlnav-select"', 'value', 'text', $this->getSeasonId());
	}	
	
	/**
	 * returns the selector for division
	 * 
	 * @return string html select
	 */
	public function getDivisionSelect()
	{		
		$project = $this->getProject();
		if(!is_object($project)) return false;
		if(!$this->_project_id && !($this->_project_id>0) && $project->project_type!='DIVISION_LEAGUE') {
			return false;
		}
		$options = array(JHtml::_('select.option', 0, JText::_($this->getParam('divisions_text'))));
		$query = ' SELECT d.id AS value, d.name AS text ' 
		       . ' FROM #__joomleague_division AS d ' 
		       . ' WHERE d.project_id = ' .  $project->id 
		       . ($this->getParam("show_only_subdivisions", 0) ? ' AND parent_id > 0' : '') 
		       . ' ORDER BY d.name'
		       ;
		$this->_db->setQuery($query);
		$res = $this->_db->loadObjectList();
		if ($res) {
			$options = array_merge($options, $res);
		}
		return JHtml::_('select.genericlist', $options, 'd', 'class="jlnav-division"', 'value', 'text', $this->getDivisionId());
	}
	
	/**
	 * returns the selector for league
	 * 
	 * @return string html select
	 */
	public function getLeagueSelect()
	{		
		$options = array(JHtml::_('select.option', 0, JText::_($this->getParam('leagues_text'))));
		$query = ' SELECT id AS value, name AS text ' 
		       . ' FROM #__joomleague_league AS l ' 
		       ;
		$this->_db->setQuery($query);
		$res = $this->_db->loadObjectList();
		if ($res) {
			$options = array_merge($options, $res);
		}
		return JHtml::_('select.genericlist', $options, 'l', 'class="jlnav-select"', 'value', 'text', $this->getLeagueId());
	}

	/**
	 * returns the selector for project
	 * 
	 * @return string html select
	 */
	public function getProjectSelect()
	{
		$options = array(JHtml::_('select.option', 0, JText::_($this->getParam('text_project_dropdown'))));
		$query_base = ' SELECT p.id AS value, p.name AS text, s.name AS season_name, st.name as sports_type_name ' 
		       . ' FROM #__joomleague_project AS p ' 
		       . ' INNER JOIN #__joomleague_season AS s on s.id = p.season_id '
		       . ' INNER JOIN #__joomleague_league AS l on l.id = p.league_id '
		       . ' INNER JOIN #__joomleague_sports_type AS st on st.id = p.sports_type_id '
		       . ' WHERE p.published = 1 ';
		       
		$query = $query_base;
		if ($this->getParam('show_project_dropdown') == 'season' && $this->getProject()) 
		{
			$query .= ' AND p.season_id = '. $this->getProject()->season_id;
			$query .= ' AND p.league_id = '. $this->getProject()->league_id;
		}
		$query .= ' GROUP BY p.id ';
		
		switch ($this->getParam('project_ordering', 0)) 
		{
			case 0:
				$query .= ' ORDER BY p.ordering ASC';				
			break;
			
			case 1:
				$query .= ' ORDER BY p.ordering DESC';				
			break;
			
			case 2:
				$query .= ' ORDER BY s.ordering ASC, l.ordering ASC, p.ordering ASC';				
			break;
			
			case 3:
				$query .= ' ORDER BY s.ordering DESC, l.ordering DESC, p.ordering DESC';				
			break;
			
			case 4:
				$query .= ' ORDER BY p.name ASC';				
			break;
			
			case 5:
				$query .= ' ORDER BY p.name DESC';				
			break;

			case 6:
				$query .= ' ORDER BY l.ordering ASC, p.ordering ASC, s.ordering ASC';
				break;

			case 7:
				$query .= ' ORDER BY l.ordering DESC, p.ordering DESC, s.ordering DESC';
				break;			
		}
		$this->_db->setQuery($query);
		$res = $this->_db->loadObjectList();
		
		if ($res) 
		{
			switch ($this->getParam('project_include_season_name', 0))
			{
				case 2:
					foreach ($res as $p)
					{
						$stText = ($this->getParam('project_include_sports_type_name', 0) ==1) ? ' ('.JText::_($p->sports_type_name). ')': '';
						$options[] = JHtml::_('select.option', $p->value, $p->text.' - '.$p->season_name . $stText);
					}
					break;
				case 1:
					foreach ($res as $p)
					{
						$stText = ($this->getParam('project_include_sports_type_name', 0) ==1) ? ' ('.JText::_($p->sports_type_name). ')': '';
						$options[] = JHtml::_('select.option', $p->value, $p->season_name .' - '. $p->text. $stText);
					}
					break;
				case 0:
				default:
					foreach ($res as $p)
					{
						$stText = ($this->getParam('project_include_sports_type_name', 0) ==1) ? ' ('.JText::_($p->sports_type_name). ')': '';
						$options[] = JHtml::_('select.option', $p->value, $p->text. $stText);
					}
				}
		}
		return JHtml::_('select.genericlist', $options, 'p', 'class="jlnav-project"', 'value', 'text', $this->_project_id);		
	}

	/**
	 * returns the selector for teams
	 * 
	 * @return string html select
	 */
	public function getTeamSelect()
	{
		if (!$this->_project_id) {
			return false;
		}
		$options = array(JHtml::_('select.option', 0, JText::_($this->getParam('text_teams_dropdown'))));
		$res = $this->getTeamsOptions();
		if ($res) 
		{
			$options = array_merge($options, $res);
		}
		return JHtml::_('select.genericlist', $options, 'tid', 'class="jlnav-team"', 'value', 'text', $this->getTeamId());		
	}
	
	/**
	 * returns select for project teams
	 * 
	 * @return string html select
	 */
	protected function getTeamsOptions()
	{
		if (empty($this->_teamoptions))
		{
			if (!$this->_project_id) {
				return false;
			}
			$query = ' SELECT t.id AS value, t.name AS text ' 
		       . ' FROM #__joomleague_project_team AS pt ' 
		       . ' INNER JOIN #__joomleague_team AS t ON t.id = pt.team_id '
		       . ' WHERE pt.project_id = '.intval($this->_project_id)
		       . ($this->_division_id ? ' AND pt.division_id = '.intval($this->_division_id) : '')
		       . ' ORDER BY t.name ASC '
		       ;
			$this->_db->setQuery($query);
			$res = $this->_db->loadObjectList();

			if (!$res) {
				Jerror::raiseWarning(0, $this->_db->getErrorMsg());
			}
			$this->_teamoptions = $res;			
		}
		return $this->_teamoptions;
	}
	
	/**
	 * return info for current project
	 * 
	 * @return object
	 */
	public function getProject()
	{
		if (!$this->_project)
		{
			if (!$this->_project_id) {
				return false;
			}
			
			$query = ' SELECT p.id, p.name, p.season_id, p.league_id ' 
			       . ' FROM #__joomleague_project AS p ' 
			       . ' WHERE id = ' . $this->_project_id;
			$this->_db->setQuery($query);
			$this->_project = $this->_db->loadObject();
		}
		return $this->_project;
	}
	
	/**
	 * return link for specified view - allow seo consistency
	 * 
	 * @param string $view
	 * @return string url
	 */
	public function getLink($view)
	{
		if (!$this->_project_id) {
			return false;
		}
		switch ($view)
		{								
			case "calendar":
				$link = JoomleagueHelperRoute::getTeamPlanRoute( $this->_project_id, $this->_team_id, $this->_division_id );
				break;	
				
			case "curve":
				$link = JoomleagueHelperRoute::getCurveRoute( $this->_project_id, $this->_team_id, 0, $this->_division_id );
				break;
				
			case "eventsranking":				
				$link = JoomleagueHelperRoute::getEventsRankingRoute( $this->_project_id, $this->_division_id, $this->_team_id );
				break;

			case "matrix":
				$link = JoomleagueHelperRoute::getMatrixRoute( $this->_project_id, $this->_division_id );
				break;
				
			case "referees":
				$link = JoomleagueHelperRoute::getRefereesRoute( $this->_project_id );
				break;
				
			case "results":
				$link = JoomleagueHelperRoute::getResultsRoute( $this->_project_id, $this->_round_id, $this->_division_id );
				break;
				
			case "resultsmatrix":
				$link = JoomleagueHelperRoute::getResultsMatrixRoute( $this->_project_id, $this->_round_id, $this->_division_id  );
				break;

			case "resultsranking":
				$link = JoomleagueHelperRoute::getResultsRankingRoute( $this->_project_id, $this->_round_id, $this->_division_id  );
				break;
				
			case "resultsrankingmatrix":
				$link = JoomleagueHelperRoute::getResultsRankingMatrixRoute( $this->_project_id, $this->_round_id, $this->_division_id  );
				break;
				
			case "roster":
				if (!$this->_team_id) {
					return false;
				}
				$link = JoomleagueHelperRoute::getPlayersRoute( $this->_project_id, $this->_team_id, null, $this->_division_id );
				break;
				
			case "stats":
				$link = JoomleagueHelperRoute::getStatsRoute( $this->_project_id, $this->_division_id );
				break;
				
			case "statsranking":
				$link = JoomleagueHelperRoute::getStatsRankingRoute( $this->_project_id, $this->_division_id );
				break;
				
			case "teaminfo":
				if (!$this->_team_id) {
					return false;
				}
				$link = JoomleagueHelperRoute::getTeamInfoRoute( $this->_project_id, $this->_team_id );
				break;				
				
			case "teamplan":
				if (!$this->_team_id) {
					return false;
				}
				$link = JoomleagueHelperRoute::getTeamPlanRoute( $this->_project_id, $this->_team_id, $this->_division_id );
				break;		
				
			case "teamstats":
				if (!$this->_team_id) {
					return false;
				}
				$link = JoomleagueHelperRoute::getTeamStatsRoute( $this->_project_id, $this->_team_id );
				break;
				
			case "treetonode":
				$link = JoomleagueHelperRoute::getBracketsRoute( $this->_project_id );
				break;
				
			case "separator":
				return false;
								
			default:
			case "ranking":
				$link = JoomleagueHelperRoute::getRankingRoute( $this->_project_id, $this->_round_id,null,null,0,$this->_division_id );
		}
		return $link;
	}
	
	/**
	 * return param value or default if not found
	 * 
	 * @param string $name
	 * @param mixed $default
	 * @return mixed
	 */
	protected function getParam($name, $default = null)
	{
		return $this->_params->get($name, $default);
	}	
}