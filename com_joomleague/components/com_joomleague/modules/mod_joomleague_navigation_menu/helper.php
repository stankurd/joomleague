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
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

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
		$db = Factory::getDbo();
		
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
		$app = Factory::getApplication();
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		$options = array(HTMLHelper::_('select.option', 0, Text::_($this->getParam('seasons_text'))));
		$query
		      ->select('s.id AS value')
		      ->select('s.name AS text')
		      ->from('#__joomleague_season AS s')
		      ->order('s.name DESC');
		$db->setQuery($query);
		$res = $db->loadObjectList();
		if ($res) {
			$options = array_merge($options, $res);
		}
		return HTMLHelper::_('select.genericlist', $options, 's', 'class="jlnav-select"', 'value', 'text', $this->getSeasonId());
	}	
	
	/**
	 * returns the selector for division
	 * 
	 * @return string html select
	 */
	public function getDivisionSelect()
	{
		$app = Factory::getApplication();
		$db = Factory::getDbo();
		$query = $db->getQuery(true);		
		$project = $this->getProject();
		if(!is_object($project)) return false;
		if(!$this->_project_id && !($this->_project_id>0) && $project->project_type!='DIVISION_LEAGUE') {
			return false;
		}
		$options = array(HTMLHelper::_('select.option', 0, Text::_($this->getParam('divisions_text'))));
		$query
		      ->clear()
		      ->select('d.id AS value')
		      ->select('d.name AS text')
		      ->from('#__joomleague_division AS d')
		      ->where('d.project_id = ' .  $project->id . ($this->getParam("show_only_subdivisions", 0) ? ' AND parent_id > 0' : ''))
		      ->order('d.name');
		$db->setQuery($query);
		$res = $db->loadObjectList();
		if ($res) {
			$options = array_merge($options, $res);
		}
		return HTMLHelper::_('select.genericlist', $options, 'd', 'class="jlnav-division"', 'value', 'text', $this->getDivisionId());
	}
	
	/**
	 * returns the selector for league
	 * 
	 * @return string html select
	 */
	public function getLeagueSelect()
	{
		$app = Factory::getApplication();
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		$options = array(HTMLHelper::_('select.option', 0, Text::_($this->getParam('leagues_text'))));
		$query
		      ->select('id AS value')
		      ->select('name AS text')
		      ->from('#__joomleague_league AS l');
		$db->setQuery($query);
		$res = $db->loadObjectList();
		if ($res) {
			$options = array_merge($options, $res);
		}
		return HTMLHelper::_('select.genericlist', $options, 'l', 'class="jlnav-select"', 'value', 'text', $this->getLeagueId());
	}

	/**
	 * returns the selector for project
	 * 
	 * @return string html select
	 */
	public function getProjectSelect()
	{
		$app = Factory::getApplication();
		$db = Factory::getDbo();
		$options = array(HTMLHelper::_('select.option', 0, Text::_($this->getParam('text_project_dropdown'))));
		$query_base = $db->getQuery(true);
		$query_base
		          ->select('p.id AS value')
		          ->select('p.name AS text')
		          ->select('s.name AS season_name')
		          ->select('st.name as sports_type_name')
		          ->select('CONCAT_WS( \':\', p.id, p.alias ) AS project_slug')
		          ->from('#__joomleague_project AS p')
		          ->innerJoin('#__joomleague_season AS s on s.id = p.season_id')
		          ->innerJoin('#__joomleague_league AS l on l.id = p.league_id')
		          ->innerJoin('#__joomleague_sports_type AS st on st.id = p.sports_type_id')
		          ->where('p.published = 1');
		$query = $db->getQuery(true);		          
		$query = $query_base;
		if ($this->getParam('show_project_dropdown') == 'season' && $this->getProject()) 
		{
			$query->where('p.season_id = '. $this->getProject()->season_id);
			$query->where('p.league_id = '. $this->getProject()->league_id);
		}
		$query->group('s.name, p.name, p.id');
		
		switch ($this->getParam('project_ordering', 0)) 
		{
			case 0:
				$query->order('p.ordering ASC');				
			break;
			
			case 1:
				$query->order('p.ordering DESC');				
			break;
			
			case 2:
				$query->order('s.ordering ASC, l.ordering ASC, p.ordering ASC');				
			break;
			
			case 3:
				$query->order('s.ordering DESC, l.ordering DESC, p.ordering DESC');				
			break;
			
			case 4:
				$query->order('p.name ASC');				
			break;
			
			case 5:
				$query->order('p.name DESC');				
			break;

			case 6:
				$query->order('l.ordering ASC, p.ordering ASC, s.ordering ASC');
				break;

			case 7:
				$query->order('l.ordering DESC, p.ordering DESC, s.ordering DESC');
				break;			
		}
		$db->setQuery($query);
		$res = $db->loadObjectList();
		
		if ($res) 
		{
			switch ($this->getParam('project_include_season_name', 0))
			{
				case 2:
					foreach ($res as $p)
					{
						$stText = ($this->getParam('project_include_sports_type_name', 0) ==1) ? ' ('.Text::_($p->sports_type_name). ')': '';
						$options[] = HTMLHelper::_('select.option', $p->value, $p->text.' - '.$p->season_name . $stText);
					}
					break;
				case 1:
					foreach ($res as $p)
					{
						$stText = ($this->getParam('project_include_sports_type_name', 0) ==1) ? ' ('.Text::_($p->sports_type_name). ')': '';
						$options[] = HTMLHelper::_('select.option', $p->value, $p->season_name .' - '. $p->text. $stText);
					}
					break;
				case 0:
				default:
					foreach ($res as $p)
					{
						$stText = ($this->getParam('project_include_sports_type_name', 0) ==1) ? ' ('.Text::_($p->sports_type_name). ')': '';
						$options[] = HTMLHelper::_('select.option', $p->value, $p->text. $stText);
					}
				}
		}
		return HTMLHelper::_('select.genericlist', $options, 'p', 'class="jlnav-project"', 'value', 'text', $this->_project_id);		
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
		$options = array(HTMLHelper::_('select.option', 0, Text::_($this->getParam('text_teams_dropdown'))));
		$res = $this->getTeamsOptions();
		if ($res) 
		{
			$options = array_merge($options, $res);
		}
		return HTMLHelper::_('select.genericlist', $options, 'tid', 'class="jlnav-team"', 'value', 'text', $this->getTeamId());		
	}
	
	/**
	 * returns select for project teams
	 * 
	 * @return string html select
	 */
	protected function getTeamsOptions()
	{
		$app = Factory::getApplication();
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		if (empty($this->_teamoptions))
		{
			if (!$this->_project_id) {
				return false;
			}
			$query
			     ->select('t.id AS value')
			     ->select('t.name AS text')
			     ->from('#__joomleague_project_team AS pt')
			     ->innerJoin('#__joomleague_team AS t ON t.id = pt.team_id')
			     //->where('pt.project_id = '.intval($this->_project_id) . ($this->_division_id ? ' AND pt.division_id = '.intval($this->_division_id) : ''))
			     ->where('pt.project_id = '.intval($this->_project_id));
			     if ($this->_division_id) {
			    $query->where('pt.division_id = ' . intval($this->_division_id));
			     }
		        $query->order('t.name ASC');
		       try
		       {
			$db->setQuery($query);
			$res = $db->loadObjectList();
		       }
		       catch (RuntimeException $e)
		       {
		           $app->enqueueMessage(Text::_($e->getMessage()), 'warning');
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
		$app = Factory::getApplication();
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		if (!$this->_project)
		{
			if (!$this->_project_id) {
				return false;
			}
			$query
			     ->select('p.id')
			     ->select('p.name')
			     ->select('p.season_id')
			     ->select('p.league_id')
			     ->from('#__joomleague_project AS p')
			     ->where('id = ' . $this->_project_id);
			$db->setQuery($query);
			$this->_project = $db->loadObject();
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
				
			case "rankingalltime":
            $link = JoomleagueHelperRoute::getRankingAllTimeRoute( $this->_league_id, $this->getParam('show_alltimetable_points'), $this->_project_id );
 		         break;
				 
            case "rosteralltime":
				if (!$this->_team_id) {
					return false;
				}
				$link = JoomleagueHelperRoute::getPlayersRouteAllTime( $this->_project_id, $this->_team_id );
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