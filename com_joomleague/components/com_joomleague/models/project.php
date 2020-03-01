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
use Joomla\CMS\Form\Form;
use Joomla\CMS\Table\Table;
use Joomla\Registry\Registry;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;
use Joomla\CMS\HTML\HTMLHelper;

defined('_JEXEC') or die;

require_once JLG_PATH_ADMIN.'/models/jlgitem.php';
require_once JLG_PATH_ADMIN.'/models/jlglist.php';
require_once JLG_PATH_ADMIN.'/models/rounds.php';

/**
 * Model-Project
 */
class JoomleagueModelProject extends BaseDatabaseModel
{
	var $_project = null;
	var $projectid = 0;
	/**
	 * project league country
	 * @var string
	 */
	var $country = null;
	/**
	 * data array for teams
	 * @var array
	 */
	var $_teams = null;

	/**
	 * data array for matches
	 * @var array
	 */
	var $_matches = null;

	/**
	 * data array for rounds
	 * @var array
	 */
	var $_rounds = null;

	/**
	 * data project stats
	 * @var array
	 */
	var $_stats = null;

	/**
	 * data project positions
	 * @var array
	 */
	var $_positions = null;

	/**
	 * cache for project divisions
	 *
	 * @var array
	 */
	var $_divisions = null;

	/**
	 * caching for current round
	 * @var object
	 */
	var $_current_round;

	public function __construct()
	{
		$app 	= Factory::getApplication();
		$this->projectid=$app->input->getInt('p',0);
		parent::__construct();
	}

	function getProject()
	{
		if (is_null($this->_project) && $this->projectid > 0)
		{
		    $db = Factory::getDbo();
		    $query = $db->getQuery(true);
			//fs_sport_type_name = sport_type folder name
			$query
			     ->select('p.*')
			     ->select('l.country')
			     ->select('st.id AS sport_type_id')
			     ->select('st.name AS sport_type_name')
			     ->select('LOWER(SUBSTR(st.name, CHAR_LENGTH( "COM_JOOMLEAGUE_ST_")+1)) AS fs_sport_type_name')
				 //->select('CONCAT_WS( \':\', p.id, p.alias ) AS slug')
				 ->select($this->constructSlug($db, 'slug', 'p.alias', 'p.id'))
				 ->select($this->constructSlug($db, 'league_slug', 'l.alias', 'l.id'))
				 ->select($this->constructSlug($db, 'season_slug', 's.alias', 's.id'))
				 ->select('l.name AS league_name')
			     ->select('s.name AS season_name')
			     ->from('#__joomleague_project AS p')
			     ->innerJoin('#__joomleague_sports_type AS st ON p.sports_type_id = st.id')
			     ->leftJoin('#__joomleague_league AS l ON p.league_id = l.id')
			     ->leftJoin('#__joomleague_season AS s ON p.season_id = s.id')
			     ->where('p.id='. $db->Quote($this->projectid));
			$db->setQuery($query,0,1);
			$this->_project = $db->loadObject();
		}
		return $this->_project;
	}

	function setProjectID($id=0)
	{
		$this->projectid=$id;
		$this->_project=null;
	}

	function getSportsType()
	{
		if (!$project = $this->getProject())
		{
			$this->setError(0, Text::_('COM_JOOMLEAGUE_ERROR_PROJECTMODEL_PROJECT_IS_REQUIRED'));
			return false;
		}

		return $project->sports_type_id;
	}

	/**
	 * returns project current round id
	 *
	 * @return int
	 */
	function getCurrentRound()
	{
		$round = $this->increaseRound();
		return ($round ? $round->id : 0);
	}

	/**
	 * returns project current round code
	 *
	 * @return int
	 */
	function getCurrentRoundNumber()
	{
		$round = $this->increaseRound();
		return ($round ? $round->roundcode : 0);
	}

	/**
	 * method to update and return the project current round
	 * @return object
	 */
	function increaseRound()
	{
	    $db = Factory::getDbo();
	    $query = $db->getQuery(true);
		if (!$this->_current_round)
		{
			if (!$project = $this->getProject()) {
				$this->setError(0, Text::_('COM_JOOMLEAGUE_ERROR_PROJECTMODEL_PROJECT_IS_REQUIRED'));
				return false;
			}

			$current_date=strftime("%Y-%m-%d %H:%M:%S");

			// determine current round according to project settings
			switch ($project->current_round_auto)
			{
				case 0 :	 // manual mode
				    $query = $db->getQuery(true);
				    $query
				            ->select('r.id')
				            ->select('r.roundcode')
				            ->from('#__joomleague_round AS r')
				            ->where('r.id ='.$project->current_round);
					break;

				case 1 :	 // get current round from round_date_first
				    $query = $db->getQuery(true);
				    $query
				            ->select('r.id')
				            ->select('r.roundcode')
				            ->from('#__joomleague_round AS r')
				            ->where('r.project_id='.$project->id)
				            ->where("(r.round_date_first - INTERVAL ".($project->auto_time)." MINUTE < '".$current_date."')")
				            ->order('r.round_date_first DESC LIMIT 1');
					break;

				case 2 : // get current round from round_date_last
				    $query = $db->getQuery(true);
				    $query
        				    ->select('r.id')
        				    ->select('r.roundcode')
        				    ->from('#__joomleague_round AS r')
        				    ->where('r.project_id='.$project->id)
        				    ->where("(r.round_date_last + INTERVAL ".($project->auto_time)." MINUTE > '".$current_date."')")
        				    ->order('r.round_date_first ASC LIMIT 1');
					break;

				case 3 : // get current round from first game of the round
				    $query = $db->getQuery(true);
				    $query
        				    ->select('r.id')
        				    ->select('r.roundcode')
        				    ->from('#__joomleague_round AS r')
				            ->from('#__joomleague_match AS m')
				            ->where('r.project_id='.$project->id)
				            ->where('m.round_id=r.id')
				            ->where("(m.match_date - INTERVAL ".($project->auto_time)." MINUTE < '".$current_date."')")
				            ->order('m.match_date DESC LIMIT 1');
					break;

				case 4 : // get current round from last game of the round
				    $query = $db->getQuery(true);
				    $query
        				    ->select('r.id')
        				    ->select('r.roundcode')
        				    ->from('#__joomleague_round AS r')
        				    ->from('#__joomleague_match AS m')
        				    ->where('r.project_id='.$project->id)
        				    ->where('m.round_id=r.id')
        				    ->where("(m.match_date - INTERVAL ".($project->auto_time)." MINUTE < '".$current_date."')")
        				    ->order('m.match_date ASC LIMIT 1');
					break;
			}
			$db->setQuery($query);
			$result = $db->loadObject();

			// If result is empty, it probably means either this is not started, either this is over, depending on the mode.
			// Either way, do not change current value
			if (!$result)
			{
			    $query = $db->getQuery(true);
			    $query
			         ->select('r.id')
			         ->select('r.roundcode')
			         ->from('#__joomleague_round AS r')
			         ->where('r.project_id = '. $project->current_round);
				$db->setQuery($query);
				$result = $db->loadObject();

				if (!$result)
				{
					if ($project->current_round_auto == 2) {
					    // the current value is invalid... saison is over, just take the last round
					    $query = $db->getQuery(true);
					    $query
        					    ->select('r.id')
        					    ->select('r.roundcode')
        					    ->from('#__joomleague_round AS r')
        					    ->where('r.project_id = '. $project->id)
        					    ->order("r.roundcode DESC");
					    $db->setQuery($query);
					    $result = $db->loadObject();
					} else {
					    // the current value is invalid... just take the first round
					    $query = $db->getQuery(true);
					    $query
        					    ->select('r.id')
        					    ->select('r.roundcode')
        					    ->from('#__joomleague_round AS r')
        					    ->where('r.project_id = '. $project->id)
        					    ->order('r.roundcode ASC');
					    $db->setQuery($query);
					    $result = $db->loadObject();
					}

				}
			}

			// Update the database if determined current round is different from that in the database
			if ($result && ($project->current_round <> $result->id))
			{
			    $query = $db->getQuery(true);
			    $query
			         ->update('#__joomleague_project')
			         ->set('current_round = '.$result->id)
			         ->where('id = ' . $db->Quote($project->id));
				$db->setQuery($query);
				if (!$db->execute())
				{
				    Factory::getApplication()->enqueueMessage( Text::_('COM_JOOMLEAGUE_ERROR_CURRENT_ROUND_UPDATE_FAILED'));
				}
			}
			$this->_current_round = $result;
		}
		return $this->_current_round;
	}

	function getColors($configcolors='')
	{
		$s=substr($configcolors,0,-1);
		$s=str_replace(array('\r\n', '\n', '\r'), '', $s);

		$arr1=array();
		if(trim($s) != "")
		{
			$arr1=explode(";",$s);
		}

		$colors=array();

		$colors[0]["from"]="";
		$colors[0]["to"]="";
		$colors[0]["color"]="";
		$colors[0]["description"]="";

		for($i=0; $i < count($arr1); $i++)
		{
			$arr2=explode(",",$arr1[$i]);
			if(count($arr2) != 4)
			{
				break;
			}

			$colors[$i]["from"]=$arr2[0];
			$colors[$i]["to"]=$arr2[1];
			$colors[$i]["color"]=$arr2[2];
			$colors[$i]["description"]=$arr2[3];
		}
		return $colors;
	}

	function getDivisionsId($divLevel=0)
	{
	    $db = Factory::getDbo();
	    $query = $db->getQuery(true);
	    $query
	           ->select('id')
	           ->from('#__joomleague_division')
	           ->where('project_id='.$this->projectid);
		if ($divLevel==1)
		{
		    $query->where('(parent_id=0 OR parent_id IS NULL)');
		}
		else if ($divLevel==2)
		{
			$query->where('parent_id>0');
		}
		//$query->order('ordering');
		$query .= " ORDER BY ordering";
		$db->setQuery($query);
		$res = $db->loadColumn();
		if(count($res) == 0) {
			echo Text::_('COM_JOOMLEAGUE_RANKING_NO_SUBLEVEL_DIVISION_FOUND') . $divLevel;
		}
		return $res;
	}

	/**
	 * return an array of division id and it's subdivision ids
	 * @param int division id
	 * @return int
	 */
	function getDivisionTreeIds($divisionid)
	{
		if ($divisionid == 0) {
			return $this->getDivisionsId();
		}
		$divisions=$this->getDivisions();
		$res=array($divisionid);
		foreach ($divisions as $d)
		{
			if ($d->parent_id == $divisionid) {
				$res[]=$d->id;
			}
		}
		return $res;
	}

	function getDivision($id)
	{
		$divs=$this->getDivisions();
		if ($divs && isset($divs[$id])) {
			return $divs[$id];
		}
		$div = new stdClass();
		$div->id = 0;
		$div->name = '';
		return $div;
	}

	function getDivisions($divLevel=0)
	{
	    $db = Factory::getDbo();
	    $query = $db->getQuery(true);
		$project = $this->getProject();
		if ($project->project_type == 'DIVISIONS_LEAGUE')
		{
			if (empty($this->_divisions))
			{
			    $query
			         ->select('*')
			         ->from('#__joomleague_division')
			         ->where('project_id='.$this->projectid);
				$db->setQuery($query);
				$this->_divisions=$db->loadObjectList('id');
			}
			if ($divLevel)
			{
				$ids=$this->getDivisionsId($divLevel);
				$res=array();
				foreach ($this->_divisions as $d)
				{
					if (in_array($d->id,$ids)) {
						$res[]=$d;
					}
				}
				return $res;
			}
			return $this->_divisions;
		}
		return array();
	}

	/**
	 * return project rounds objects ordered by roundcode
	 *
	 * @param string ordering 'ASC or 'DESC'
	 * @return array
	 */
	function getRounds($ordering='ASC')
	{
	    $db = Factory::getDbo();
	    $query = $db->getQuery(true);
		if (empty($this->_rounds))
		{
		    $query
		          ->select('id,round_date_first,round_date_last')
		          ->select('CASE LENGTH(name) when 0 then roundcode else name END as name')
		          ->select('roundcode')
		          ->from('#__joomleague_round')
		          ->where('project_id='. $this->projectid)
		          ->order('roundcode '.$ordering);
			$db->setQuery($query);
			$this->_rounds=$db->loadObjectList();
		}
		if ($ordering == 'DESC') {
			return array_reverse($this->_rounds);
		}
		return $this->_rounds;
	}

	/**
	 * return project rounds as array of objects(roundid as value,name as text)
	 *
	 * @param string $ordering
	 * @return array
	 */
	function getRoundOptions($ordering='ASC')
	{
	    $db = Factory::getDbo();
	    $query = $db->getQuery(true);
	    $query
	           ->select('id as value')
	           ->select("(CASE LENGTH(name) when 0 then CONCAT('".Text::_('COM_JOOMLEAGUE_MATCHDAY_NAME'). "',' ', id) else name END as text")
	           ->from('#__joomleague_round')
	           ->where('project_id='.(int)$this->projectid)
	           ->order('roundcode' .$ordering);
		$db->setQuery($query);
		return $db->loadObjectList();
	}

	function getTeaminfo($projectteamid)
	{
	    $db = Factory::getDbo();
	    $query = $db->getQuery(true);
	    $query
	           ->select('t.*')
	           ->select('pt.division_id')
	           ->select('t.id as team_id')
	           ->select('pt.picture AS projectteam_picture')
	           ->select('pt.notes AS projectteam_notes')
	           ->select('t.picture as team_picture')
	           ->select('c.logo_small')
	           ->select('c.logo_middle')
	           ->select('c.logo_big')
	           ->select('c.country')
	           ->select('IF((ISNULL(pt.picture) OR (pt.picture="")), (IF((ISNULL(t.picture) OR (t.picture="")), c.logo_big , t.picture)) , pt.picture) as picture')
	           ->select('t.extended as teamextended')
	           ->select('pt.project_id AS project_id')
	           ->select('pt.id AS ptid')
	           ->from('#__joomleague_project_team AS pt')
	           ->innerJoin('#__joomleague_team AS t ON pt.team_id=t.id')
	           ->leftJoin('#__joomleague_club AS c ON t.club_id=c.id')
	           ->where('pt.id='. $db->Quote($projectteamid));
		$db->setQuery($query);
		return $db->loadObject();
	}

	public function _getTeams()
	{
	    $db = Factory::getDbo();
	    $query = $db->getQuery(true);
		if (empty($this->_teams))
		{
		    $query
		    ->select('tl.id AS projectteamid')
		          ->select('tl.division_id')
		          ->select('tl.standard_playground')
		          ->select('tl.start_points')
		          ->select('tl.points_finally')
		          ->select('tl.neg_points_finally')
		          ->select('tl.matches_finally')
		          ->select('tl.won_finally')
		          ->select('tl.draws_finally')
		          ->select('tl.lost_finally')
		          ->select('tl.homegoals_finally')
		          ->select('tl.guestgoals_finally')
		          ->select('tl.diffgoals_finally')
		          ->select('tl.info')
		          ->select('tl.reason')
		          ->select('tl.team_id')
		          ->select('tl.checked_out')
		          ->select('tl.checked_out_time')
		          ->select('tl.is_in_score')
		          ->select('tl.picture AS projectteam_picture')
		          ->select('t.picture as team_picture')
		          ->select('IF((ISNULL(tl.picture) OR (tl.picture="")),(IF((ISNULL(t.picture) OR (t.picture="")), c.logo_small , t.picture)) , t.picture) as picture')
		          ->select('tl.project_id')
		          ->select('t.id')
		          ->select('t.name')
		          ->select('t.short_name')
		          ->select('t.middle_name')
		          ->select('t.notes')
		          ->select('t.club_id')
		          ->select('c.email as club_email')
		          ->select('c.logo_small')
		          ->select('c.logo_middle')
		          ->select('c.logo_big')
		          ->select('c.country')
		          ->select('c.website')
		          ->select('d.name AS division_name')
		          ->select('d.picture AS division_picture')
		          ->select('d.shortname AS division_shortname')
		          ->select('d.parent_id AS parent_division_id')
		          ->select('plg.name AS playground_name')
		          ->select('plg.short_name AS playground_short_name')
		          ->select($this->constructSlug($db, 'project_slug', 'p.alias', 'p.id'))
		          ->select($this->constructSlug($db, 'team_slug', 't.alias', 't.id'))
		          ->select($this->constructSlug($db, 'division_slug', 'd.alias', 'd.id'))
		          ->select($this->constructSlug($db, 'club_slug', 'c.alias', 'c.id'))
		          ->select($this->constructSlug($db, 'projectteam_slug', 't.alias', 'tl.id'))
		          ->from('#__joomleague_project_team tl')
		          ->leftJoin('#__joomleague_team t ON tl.team_id=t.id')
		          ->leftJoin('#__joomleague_club c ON t.club_id=c.id')
		          ->leftJoin('#__joomleague_division d ON d.id=tl.division_id')
		          ->leftJoin('#__joomleague_playground plg ON plg.id=tl.standard_playground')
		          ->leftJoin('#__joomleague_project AS p ON p.id=tl.project_id')
		          ->where('tl.project_id='.(int)$this->projectid);
			$db->setQuery($query);
			$this->_teams=$db->loadObjectList();
		}
		return $this->_teams;
	}

	/**
	 * return teams of the project
	 *
	 * @param int $division
	 * @return array
	 */
	public function getTeams($division=0)
	{
		$teams=array();
		if ($division != 0)
		{
			$divids=$this->getDivisionTreeIds($division);
			foreach ((array)$this->_getTeams() as $t)
			{
				if (in_array($t->division_id,$divids))
				{
					$teams[]=$t;
				}
			}
		}
		else
		{
			$teams=$this->_getTeams();
		}

		return $teams;
	}

	/**
	 * return array of team ids
	 *
	 * @return array	 *
	 */
	public function getTeamIds($division=0)
	{
		$teams=array();
		foreach ((array)$this->_getTeams() as $t)
		{
			if (!$division || $t->division_id == $division) {
				$teams[]=$t->id;
			}
		}
		return $teams;
	}

	public function getTeamsIndexedById($division=0)
	{
		$result=$this->getTeams($division);
		$teams=array();
		if (count($result))
		{
			foreach($result as $r)
			{
				$teams[$r->id]=$r;
			}
		}

		return $teams;
	}

	public function getTeamsIndexedByPtid($division=0)
	{
		$result=$this->getTeams($division);
		$teams=array();

		if (count($result))
		{
			foreach($result as $r)
			{
				$teams[$r->projectteamid]=$r;
			}
		}
		return $teams;
	}

	public function getFavTeams()
	{
		$project = $this->getProject();
		if(!is_null($project))
		return explode(",",$project->fav_team);
		else
		return array();
	}

	public function getEventTypes($evid=0)
	{
	    $db = Factory::getDbo();
	    $query = $db->getQuery(true);
	    $query
	           ->select('et.id AS etid')
	           ->select('me.event_type_id AS id')
	           ->select('et.*')
	           ->from('#__joomleague_eventtype AS et')
	           ->leftJoin('#__joomleague_match_event AS me ON et.id=me.event_type_id');          
		if ($evid != 0)
		{
			if ($this->projectid > 0)
			{
				$query .= " AND";
			}
			else
			{
				$query .= " WHERE";
			}
			$query .= " me.event_type_id=".(int)$evid;
		}

		$db->setQuery($query);
		return $db->loadObjectList('etid');
	}

	public function getProjectTeamId($teamid)
	{
	    $db = Factory::getDbo();
	    $query = $db->getQuery(true);
	    $query
	           ->select('id')
	           ->from('#__joomleague_project_team')
	           ->where('team_id='.(int)$teamid)
	           ->where('project_id='.(int)$this->projectid);
		$db->setQuery($query);
		$result=$db->loadResult();

		return $result;
	}

	/**
	 * Method to return a playgrounds array (id,name)
	 *
	 * @access  public
	 * @return  array
	 */
	public function getPlaygrounds()
	{
	    $db = Factory::getDbo();
	    $query = $db->getQuery(true);
	    $query
	           ->select('id AS value')
	           ->select('name AS text')
	           ->from('#__joomleague_playground')
	           ->select('text ASC');
		$db->setQuery($query);
		try {
		    $result = $db->loadObjectList();
		} catch (RuntimeException $e) {
		    Factory::getApplication()->enqueueMessage(Text::_($e->getMessage()), 'error');
		    return false;
		}			
			return $result;
		}

	function getReferees()
	{
	    $db = Factory::getDbo();
	    $query = $db->getQuery(true);
		$project=$this->getProject();
		if ($project->teams_as_referees)
		{
		    $query
		          ->select('id AS value, name AS text')
		          ->from('#__joomleague_team')
		          ->order('name');
			$db->setQuery($query);
			$refs=$db->loadObjectList();
		}
		else
		{
		    $query
		          ->select('id AS value, firstname, lastname')
		          ->from('#__joomleague_project_referee')
		          ->order('lastname');
			$db->setQuery($query);
			$refs=$db->loadObjectList();
			foreach($refs as $ref)
			{
				$ref->text=$ref->lastname.",".$ref->firstname;
			}
		}
		return $refs;
	}

	public function getTemplateConfig($template)
	{
		$app 	= Factory::getApplication();
		//first load the default settings from the default <template>.xml file
		$paramsdata="";
		$arrStandardSettings=array();
		if(file_exists(JLG_PATH_SITE.'/settings/default/'.$template.'.xml')) {
			$strXmlFile = JLG_PATH_SITE.'/settings/default/'.$template.'.xml';
			$form = Form::getInstance($template, $strXmlFile);
			$fieldsets = $form->getFieldsets();
			foreach ($fieldsets as $fieldset) {
				foreach($form->getFieldset($fieldset->name) as $field) {
					$arrStandardSettings[$field->name]=$field->value;
				}
			}
		}
		//second load the default settings from the default extensions <template>.xml file
		$extensions=JoomleagueHelper::getExtensions($app->input->getInt('p'));
		foreach ($extensions as $e => $extension) {
			$JLGPATH_EXTENSION= JPATH_COMPONENT_SITE.'/extensions/'.$extension;
			$paramsdata="";
			$strXmlFile=$JLGPATH_EXTENSION.'/settings/default/'.$template.'.xml';
			if(file_exists($JLGPATH_EXTENSION.'/settings/default/'.$template.'.xml')) {
				$form = Form::getInstance($template, $strXmlFile);
				$fieldsets = $form->getFieldsets();
				foreach ($fieldsets as $fieldset) {
					foreach($form->getFieldset($fieldset->name) as $field) {
						$arrStandardSettings[$field->name]=$field->value;
					}
				}
			}
		}

		if($this->projectid == 0) return $arrStandardSettings;
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		$query
		      ->select('t.params')
		      ->from('#__joomleague_template_config AS t')
		      ->innerJoin('#__joomleague_project AS p ON p.id=t.project_id')
		      ->where('t.template='.$db->Quote($template))
		      ->where('p.id='.$db->Quote($this->projectid));
		$db->setQuery($query);
		if (! $result=$db->loadResult())
		{
			$project=$this->getProject();
			if (!empty($project) && $project->master_template>0)
			{
			    $query = $db->getQuery(true);
			    $query
			         ->select('t.params')
			         ->from('#__joomleague_template_config AS t')
			         ->innerJoin('#__joomleague_project AS p ON p.id=t.project_id')
			         ->where('t.template='.$db->Quote($template))
			         ->where('p.id='.$db->Quote($project->master_template));
				$db->setQuery($query);
				if (! $result=$db->loadResult())
				{
				    Factory::getApplication()->enqueueMessage(Text::_('COM_JOOMLEAGUE_MASTER_TEMPLATE_MISSING')." ".$template , 'notice');
				    Factory::getApplication()->enqueueMessage(Text::_('COM_JOOMLEAGUE_MASTER_TEMPLATE_MISSING_PID'). $project->master_template , 'notice');
				    Factory::getApplication()->enqueueMessage(Text::_('COM_JOOMLEAGUE_TEMPLATE_MISSING_HINT') , 'notice');
					return $arrStandardSettings;
				}
			}
			else
			{
			    Factory::getApplication()->enqueueMessage(Text::_('setting not found').  'project ' . $this->projectid . 'notice');
				//there are no saved settings found, use the standard xml file default values
				return $arrStandardSettings;
			}
		}
		$jRegistry = new Registry;
		$jRegistry->loadString($result, 'ini');
		$configvalues = $jRegistry->toArray();

		//merge and overwrite standard settings with individual view settings
		$settings = array_merge($arrStandardSettings,$configvalues);

		return $settings;
	}

	public function getOverallConfig()
	{
		return $this->getTemplateConfig('overall');
	}

	function getMapConfig()
	{
		return $this->getTemplateConfig('map');
	}

  	/**
   	* @author diddipoeler
   	* 
   	* @return country from project-league
   	*/
   	public function getProjectCountry()
	{
	    $db = Factory::getDbo();
	    $query = $db->getQuery(true);
	    $query
	           ->select('l.country')
	           ->from('#__joomleague_league as l')
	           ->innerJoin('#__joomleague_project as pro' . ' ON '. ' pro.league_id = l.id')
	           ->where('pro.id = '. $db->Quote($this->projectid));
		  $db->setQuery( $query );
		  $this->country = $db->loadResult();
		  return $this->country;
  	}

	/**
	 * return events assigned to the project
	 * @param int position_id if specified,returns only events assigned to this position
	 * @return array
	 */
	public function getProjectEvents($position_id=0)
	{
	    $db = Factory::getDbo();
	    $query = $db->getQuery(true);
	    $query
	           ->select('et.id,et.name,et.icon')
	           ->from('#__joomleague_eventtype AS et')
	           ->innerJoin('#__joomleague_position_eventtype AS pet' . ' ON ' . ' pet.eventtype_id=et.id')
	           ->innerJoin('#__joomleague_project_position AS ppos' . ' ON ' . ' ppos.position_id=pet.position_id')
	           ->where('ppos.project_id='.$db->Quote($this->projectid));
		if ($position_id)
		{
		    $query->where('ppos.position_id='. $db->Quote($position_id));
		}
		$query->group('et.id, et.name, et.icon');
		$db->setQuery($query);
		$events=$db->loadObjectList('id');
		return $events;
	}

	/**
	 * returns stats assigned to positions assigned to project
	 * @param int statid 0 for all stats
	 * @param int positionid 0 for all positions
	 * @return array objects
	 */
	public function getProjectStats($statid=0,$positionid=0)
	{
	    $app = Factory::getApplication();
	    $db = Factory::getDbo();
	    $query = $db->getQuery(true);
		if (empty($this->_stats))
		{
			require_once JLG_PATH_ADMIN.'/statistics/base.php';
			$project = $this->getProject();
			$project_id=$project->id;
			$query
			     ->select('stat.id')
			     ->select('stat.name')
			     ->select('stat.short')
			     ->select('stat.class')
			     ->select('stat.icon')
			     ->select('stat.calculated')
			     ->select('ppos.id AS pposid')
			     ->select('ppos.position_id AS position_id')
			     ->select('stat.params')
			     ->select('stat.baseparams')
			     ->from('#__joomleague_statistic AS stat')
			     ->innerJoin('#__joomleague_position_statistic AS ps' . ' ON ' . ' ps.statistic_id=stat.id')
			     ->innerJoin('#__joomleague_project_position AS ppos' . ' ON ' . ' ppos.position_id=ps.position_id' . ' AND ' . ' ppos.project_id='.$project_id)
			     ->innerJoin('#__joomleague_position AS pos' . ' ON ' . ' pos.id=ps.position_id')
			     ->where('stat.published=1')
			     ->where('pos.published =1')
			     ->order('pos.ordering,ps.ordering');
			     try
			     {
			         $db->setQuery($query);
			         $this->_stats=$db->loadObjectList();
			     }
			     catch (RuntimeException $e)
			     {
			         $app->enqueueMessage(Text::_($e->getMessage()), 'error');
			         
			         return false;
			     }

		}
		// sort into positions
		$positions=$this->getProjectPositions();
		$stats=array();
		// init
		foreach ($positions as $pos)
		{
			$stats[$pos->id]=array();
		}
		if (count($this->_stats) > 0)
		{
			foreach ($this->_stats as $k => $row)
			{
				if (!$statid || $statid == $row->id || (is_array($statid) && in_array($row->id, $statid)))
				{
					$stat=JLGStatistic::getInstance($row->class);
					$stat->bind($row);
					$stat->set('position_id',$row->position_id);
					$stats[$row->position_id][$row->id]=$stat;
				}
			}
			if ($positionid)
			{
				return (isset($stats[$positionid]) ? $stats[$positionid] : array());
			}
			else
			{
				return $stats;
			}
		}
		else
		{
			return $stats;
		}
	}

	public function getProjectPositions()
	{
	    $db = Factory::getDbo();
	    $query = $db->getQuery(true);
		if (empty($this->_positions))
		{
		    $query
		          ->select('pos.id')
		          ->select('pos.persontype')
		          ->select('pos.name')
		          ->select('pos.ordering')
		          ->select('pos.published')
		          ->select('ppos.id AS pposid')
		          ->from('#__joomleague_project_position AS ppos')
		          ->innerJoin('#__joomleague_position AS pos ON ppos.position_id=pos.id')
		          ->where('ppos.project_id='.$db->Quote($this->projectid));
			$db->setQuery($query);
			$this->_positions=$db->loadObjectList('id');
		}
		return $this->_positions;
	}

	static function getClubIconHtml(&$team,$type=1,$with_space=0)
	{
		$small_club_icon=$team->logo_small;
		if ($type==1)
		{
			$params=array();
			$params['align']="top";
			$params['border']=0;
			if ($with_space==1)
			{
				$params['style']='padding:1px;';
			}
			if ($small_club_icon=='')
			{
				$small_club_icon = JoomleagueHelper::getDefaultPlaceholder("clublogosmall");
			}

			return HTMLHelper::image($small_club_icon,'',$params);
		}
		elseif (($type==2) && (isset($team->country)))
		{
			return Countries::getCountryFlag($team->country);
		}
	}

	/**
	 * Method to store the item
	 *
	 * @access	public
	 * @return	boolean	True on success
	 */
	public function store($data,$table='')
	{
	    $db = Factory::getDbo();
	    $query = $db->getQuery(true);
		if ($table=='')
		{
			$row = $this->getTable();
		}
		else
		{
			$row = Table::getInstance($table,'Table');
		}

		// Bind the form fields to the items table
		if (!$row->bind($data))
		{
			$this->setError(Text::_('Binding failed'));
			return false;
		}

		// Create the timestamp for the date
		$row->checked_out_time=gmdate('Y-m-d H:i:s');

		// if new item,order last,but only if an ordering exist
		if ((isset($row->id)) && (isset($row->ordering)))
		{
			if (!$row->id && $row->ordering != NULL)
			{
				$row->ordering=$row->getNextOrder();
			}
		}

		// Make sure the item is valid
		if (!$row->check())
		{
		    throw new RuntimeException($e->getMessage());
		    return false;
		}

		// Store the item to the database
		if (!$row->store())
		{
		    throw new RuntimeException($e->getMessage());
			return false;
		}
		return $row->id;
	}

	public static function isUserProjectAdminOrEditor($userId=0, $project)
	{
		$result=false;
		if($userId > 0)
		{
			$id = $project->id;
			//$result= ($userId==$project->admin || $userId==$project->editor);
			$result = (	Factory::getUser()->authorise('core.admin', 'com_joomleague.project.'.$id) ||
						Factory::getUser()->authorise('core.manage', 'com_joomleague.project.'.$id) ||
						Factory::getUser()->authorise('core.edit', 'com_joomleague.project.'.$id) ? true : false);
		}
		return $result;
	}

	/**
	 * returns match substitutions
	 * @param int match id
	 * @return array
	 */
	function getMatchSubstitutions($match_id)
	{
	    $db = Factory::getDbo();
	    $query = $db->getQuery(true);
	    $query
	           ->select('mp.in_out_time')
	           ->select('pt.team_id')
	           ->select('pt.id AS ptid')
	           ->select('p2.id AS out_ptid')
	           ->select('p.firstname')
	           ->select('p.firstname')
	           ->select('p.lastname')
	           ->select('pos.name AS in_position')
	           ->select('pos2.name AS out_position')
	           ->select('p2.firstname AS out_firstname')
	           ->select('p2.nickname AS out_nickname')
	           ->select('p2.lastname AS out_lastname')
	           ->from('#__joomleague_match_player AS mp')
	           ->leftJoin('#__joomleague_team_player AS tp' . ' ON ' . 'mp.teamplayer_id=tp.id' . ' AND ' . 'tp.published=1')
	           ->leftJoin('#__joomleague_project_team AS pt' . ' ON ' . 'tp.projectteam_id=pt.id')
	           ->leftJoin('#__joomleague_person AS p' . ' ON ' . 'tp.person_id=p.id')
	           ->leftJoin('#__joomleague_team_player AS tp2' . ' ON ' . 'mp.in_for=tp2.id AND tp2.published=1')
	           ->leftJoin('#__joomleague_person AS p2' . ' ON ' . 'tp2.person_id=p2.id')
	           ->leftJoin('#__joomleague_project_position AS ppos' . ' ON ' . 'ppos.id=mp.project_position_id')
	           ->leftJoin('#__joomleague_position AS pos' . ' ON ' . 'ppos.position_id=pos.id')
	           ->leftJoin('#__joomleague_match_player AS mp2' . ' ON ' . 'mp.match_id=mp2.match_id' . ' AND ' . 'mp.in_for=mp2.teamplayer_id')
	           ->leftJoin('#__joomleague_project_position AS ppos2' . ' ON ' . 'ppos2.id=mp2.project_position_id')
	           ->leftJoin('#__joomleague_position AS pos2' . ' ON ' . 'ppos2.position_id=pos2.id')
	           ->where('mp.match_id='.(int)$match_id)
	           ->where('mp.came_in=1')
	           ->where('p.published = 1')
	           ->where('p2.published = 1')
	           ->order('mp.in_out_time+0');
		$db->setQuery( $query );
		return $db->loadObjectList();
	}

	/**
	 * returns match events
	 * @param int match id
	 * @return array
	 */
	function getMatchEvents($match_id,$showcomments=0,$sortdesc=0)
	{
	    $db = Factory::getDbo();
	    $query = $db->getQuery(true);
		if ($showcomments == 1) {
		    $join = 'LEFT';
		    $addline = 'me.notes,';
		} else {
		    $join = 'INNER';
		    $addline = '';
		}
		$esort = '';
		if ($sortdesc == 1) {
		    $esort = 'DESC';
		}
		$query = ' 	SELECT 	me.event_type_id,
							me.id as event_id,
							me.event_time,
							me.notice,'
							. $addline .
							'pt.team_id AS team_id,
							et.name AS eventtype_name,
							t.name AS team_name,
							me.projectteam_id AS ptid,
							me.event_sum,
							p.id AS playerid,
							p.firstname AS firstname1,
							p.nickname AS nickname1,
							p.lastname AS lastname1,
							p.picture AS picture1,
							tp.picture AS tppicture1
					FROM #__joomleague_match_event AS me
					'.$join.' JOIN #__joomleague_eventtype AS et ON me.event_type_id = et.id
					'.$join.' JOIN #__joomleague_project_team AS pt ON me.projectteam_id = pt.id
					'.$join.' JOIN #__joomleague_team AS t ON pt.team_id = t.id
					LEFT JOIN #__joomleague_team_player AS tp ON tp.id = me.teamplayer_id
						  AND tp.published = 1
					LEFT JOIN #__joomleague_person AS p ON tp.person_id = p.id
						  AND p.published = 1
					WHERE me.match_id = ' . $match_id . '
					ORDER BY (me.event_time + 0)'. $esort .', me.event_type_id, me.id';

		$db->setQuery( $query );
		return $db->loadObjectList();
	}

	function hasEditPermission($task=null)
	{
		$allowed = false;
		$user = Factory::getUser();
		if($user->id > 0) {
			if(!is_null($task)) {
				if (!$user->authorise($task, 'com_joomleague')) {
					$allowed = false;
					error_log("no ACL permission for task " . $task);
				} else {
					$allowed = true;
				}
			}
			//if no ACL permission, check for overruling permission by project admin/editor (compatibility < 2.5)
			if(!$allowed) {
				// If not, then check if user is project admin or editor
				$project = $this->getProject();
				if($this->isUserProjectAdminOrEditor($user->id, $project))
				{
					$allowed = true;
				} else {
					error_log("no isUserProjectAdminOrEditor");
				}
			}
		}
		return $allowed;
	}

	function constructSlug($db, $slugName = 'slug', $aliasFieldName = 'alias', $idFieldName = 'id')
	{
	    $db = Factory::getDbo();
	    $query = $db->getQuery(true);
		return
			'CASE WHEN CHAR_LENGTH(' . $db->quoteName($aliasFieldName) . ')' .
			' THEN CONCAT_WS(\':\', ' . $db->quoteName($idFieldName) . ',' . $db->quoteName($aliasFieldName) . ')' .
			' ELSE ' . $db->quoteName($idFieldName) . ' END AS ' . $slugName;
	}

	function constructCombiSlug($db, $slugName, $alias1FieldName, $alias2FieldName, $idFieldName)
	{
	    $db = Factory::getDbo();
	    $query = $db->getQuery(true);
		return
			'CASE WHEN CHAR_LENGTH(' . $db->quoteName($alias1FieldName) . ')' .
				' AND CHAR_LENGTH(' . $db->quoteName($alias2FieldName) . ')'.
			' THEN CONCAT_WS(\':\', ' . $db->quoteName($idFieldName) . ', CONCAT_WS("_",' .
				$db->quoteName($alias1FieldName) . ', ' . $db->quoteName($alias2FieldName) . '))' .
			' ELSE ' . $db->quoteName($idFieldName) . ' END AS ' . $slugName;
	}
	/**
	 * Generate column expression for slug .
	 *
	 * @param   DatabaseQuery  $query  Current query instance.
	 * @param   string           $id     Column id name.
	 * @param   string           $alias  Column alias name.
	 *
	 * @return  string
	 *
	 * @since   4.0.0
	 */
	function getSlugColumn($query, $id, $alias)
	{
	    $db = Factory::getDbo();
	    $query = $db->getQuery(true);
	    return 'CASE WHEN '
	        . $query->charLength($alias, '!=', '0')
	        . ' THEN '
	            . $query->concatenate(array($query->castAsChar($id), $alias), ':')
	            . ' ELSE '
	                . $id . ' END';
	}
	
}
