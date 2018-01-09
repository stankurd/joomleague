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
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;
use Joomla\CMS\Table\Table;
use Joomla\CMS\Uri\Uri;

defined('_JEXEC') or die;

jimport('joomla.filesystem.file');

HTMLHelper::_('jquery.framework');
HTMLHelper::_('behavior.formvalidator');
//HTMLHelper::_('behavior.core');
/**
 * HTML View class
 */
class JoomleagueViewMatch extends JLGView 
{
	
	protected $form;
	protected $item;
	protected $state;

	function display($tpl = null) {
		if ($this->getLayout () == 'form') {
			$this->_displayForm ( $tpl );
			return;
		} elseif ($this->getLayout () == 'editevents') {
			$this->_displayEditevents ( $tpl );
			return;
		} elseif ($this->getLayout () == 'editeventsbb') {
			$this->_displayEditeventsbb ( $tpl );
			return;
		} elseif ($this->getLayout () == 'editstats') {
			$this->_displayEditstats ( $tpl );
			return;
		} elseif ($this->getLayout () == 'editlineup') {
			$this->_displayEditlineup ( $tpl );
			return;
		} elseif ($this->getLayout () == 'editreferees') {
			$this->_displayEditReferees ( $tpl );
			return;
		}
		
		parent::display ( $tpl );
	}
	
	
	/**
	 * default form
	 */
	function _displayForm($tpl)
	{
		$app 	= Factory::getApplication ();
		$option = $app->input->get('option');
		$user	= Factory::getUser();
		$model	= $this->getModel();
		$lists	= array();
		HTMLHelper::_('jquery.framework');
		//HTMLHelper::_('behavior.core');
		// retrieve data
		$this->form = $this->get('Form');
		$this->item = $this->get('Item');
		$this->state = $this->get('State');
		
		$match = $this->item;
		$isNew = ($this->item->id == 0);
		
		// check for teams
		if ((!$match->projectteam1_id) && (!$match->projectteam2_id)) {
			JLToolBarHelper::title('ERROR');
			JLToolBarHelper::back('back');
			$app->enqueueMessage(Text::_('COM_JOOMLEAGUE_ADMIN_MATCH_NO_TEAMS').'<br /><br /> warning');
			return false;
		}
	
		// build the html select booleanlist for published
		$lists['published'] = HTMLHelper::_('select.booleanlist','published','class="inputbox"',$match->published);
	
		// get the home team standard playground
		$tblProjectHomeTeam = Table::getInstance ( 'ProjectTeam', 'Table' );
		$tblProjectHomeTeam->load ( $match->projectteam1_id );
		$standard_playground_id = (! empty ( $tblProjectHomeTeam->standard_playground ) && $tblProjectHomeTeam->standard_playground > 0) ? $tblProjectHomeTeam->standard_playground : null;
		$playground_id = (! empty ( $match->playground_id ) && ($match->playground_id > 0)) ? $match->playground_id : $standard_playground_id;
	
		// build the html select booleanlist for count match result
		// $lists ['count_result'] = HTMLHelper::_ ( 'select.booleanlist', 'count_result', 'class="inputbox"', $match->count_result );
	
		// build the html select booleanlist which team got the won
		$myoptions = array ();
		$myoptions [] = HTMLHelper::_ ( 'select.option', '0', Text::_ ( 'COM_JOOMLEAGUE_ADMIN_MATCHES_NO_TEAM' ) );
		$myoptions [] = HTMLHelper::_ ( 'select.option', '1', Text::_ ( 'COM_JOOMLEAGUE_ADMIN_MATCHES_HOME_TEAM' ) );
		$myoptions [] = HTMLHelper::_ ( 'select.option', '2', Text::_ ( 'COM_JOOMLEAGUE_ADMIN_MATCHES_AWAY_TEAM' ) );
		$myoptions [] = HTMLHelper::_ ( 'select.option', '3', Text::_ ( 'COM_JOOMLEAGUE_ADMIN_MATCHES_LOSS_BOTH_TEAMS' ) );
		$myoptions [] = HTMLHelper::_ ( 'select.option', '4', Text::_ ( 'COM_JOOMLEAGUE_ADMIN_MATCHES_WON_BOTH_TEAMS' ) );
		$lists ['team_won'] = HTMLHelper::_ ( 'select.genericlist', $myoptions, 'team_won', 'class="inputbox" size="1"', 'value', 'text', $match->team_won );
	
	
		$project_id = $app->getUserState($option.'project');
		$mdlProject = BaseDatabaseModel::getInstance('project','JoomleagueModel');
		$project 	= $mdlProject->getItem($project_id);
	
		$overall_config = $mdlProject->getTemplateConfig ( 'overall' );
		$table_config = $mdlProject->getTemplateConfig ( 'ranking' );
	
		$extended = $this->getExtended ( $match->extended, 'match' );
	
		// match relation tab
		$mdlMatch = BaseDatabaseModel::getInstance ( 'match', 'JoomleagueModel' );
		$oldmatches [] = HTMLHelper::_ ( 'select.option', '0', Text::_ ( 'COM_JOOMLEAGUE_ADMIN_MATCH_OLD_MATCH' ) );
		$res = array ();
		$new_match_id = ($match->new_match_id) ? $match->new_match_id : 0;
		if ($res = $mdlMatch->getMatchRelationsOptions ( $app->getUserState ( $option . 'project', 0 ), $match->id . "," . $new_match_id )) {
			foreach ( $res as $m ) {
				$m->text = '(' . JoomleagueHelper::getMatchStartTimestamp ( $m ) . ') - ' . $m->t1_name . ' - ' . $m->t2_name;
			}
			$oldmatches = array_merge ( $oldmatches, $res );
		}
		$lists ['old_match'] = HTMLHelper::_ ( 'select.genericlist', $oldmatches, 'old_match_id', 'class="inputbox" size="1"', 'value', 'text', $match->old_match_id );
	
		$newmatches [] = HTMLHelper::_ ( 'select.option', '0', Text::_ ( 'COM_JOOMLEAGUE_ADMIN_MATCH_NEW_MATCH' ) );
		$res = array ();
		$old_match_id = ($match->old_match_id) ? $match->old_match_id : 0;
		if ($res = $mdlMatch->getMatchRelationsOptions ( $app->getUserState ( $option . 'project', 0 ), $match->id . "," . $old_match_id )) {
			foreach ( $res as $m ) {
				$m->text = '(' . JoomleagueHelper::getMatchStartTimestamp ( $m ) . ') - ' . $m->t1_name . ' - ' . $m->t2_name;
			}
			$newmatches = array_merge ( $newmatches, $res );
		}
		$lists ['new_match'] = HTMLHelper::_ ( 'select.genericlist', $newmatches, 'new_match_id', 'class="inputbox" size="1"', 'value', 'text', $match->new_match_id );
	
		$this->overall_config=$overall_config;
		$this->table_config=$table_config;
		$this->project=$project;
		$this->lists=$lists;
		$this->item=$match;
	
		$this->extended=$extended;
		$form = $this->get('form');
		$form->setValue('playground_id', null, $playground_id);
		$this->form=$form;
	
		$this->addToolbarForm();
		parent::display($tpl);
	}
	
	
	function _displayEditReferees($tpl) 
	{
	    HTMLHelper::_('jquery.framework');
	    //HTMLHelper::_('behavior.core');
		$app = Factory::getApplication ();
		$option = $app->input->get('option');
		$project_id = $app->getUserState($option.'project');
		$input = $app->input;
		$match_id = $input->getInt('match_id');
		$team_id = $input->getInt('team_id');
		
		$params = ComponentHelper::getParams($option);
		$default_name_format = $params->get('name_format');

		// add the js script
		$baseurl = Uri::root();
		$document = Factory::getDocument();
		$document->addScript($baseurl . 'administrator/components/com_joomleague/assets/js/editreferees.js');
		
		$model = $this->getModel();
		
		$mdlMatch = BaseDatabaseModel::getInstance('match','JoomleagueModel');
		$match = $mdlMatch->getItem($match_id);
		
		$allreferees = array();
		$allreferees = $model->getRefereeRoster(false,$match_id);
		$inroster = array();
		$projectreferees = array();
		$projectreferees2 = array();

		if (isset($allreferees)) {
			foreach($allreferees as $referee) {
				$inroster [] = $referee->value;
			}
		}
		$projectreferees = $model->getProjectReferees($inroster,$project_id);

		if (count ( $projectreferees ) > 0) {
			foreach ( $projectreferees as $referee ) {
				$projectreferees2 [] = HTMLHelper::_ ( 'select.option', $referee->value, JoomleagueHelper::formatName ( null, $referee->firstname, $referee->nickname, $referee->lastname, $default_name_format ) . ' - (' . strtolower ( Text::_ ( $referee->positionname ) ) . ')' );
			}
		}
		$lists ['team_referees'] = HTMLHelper::_ ( 'select.genericlist', $projectreferees2, 'roster[]', 'style="font-size:12px;height:auto;min-width:15em;" ' . 'class="inputbox" multiple="true" size="' . max ( 10, count ( $projectreferees2 ) ) . '"', 'value', 'text' );

		$selectpositions [] = HTMLHelper::_ ( 'select.option', '0', Text::_ ( 'COM_JOOMLEAGUE_GLOBAL_SELECT_REF_FUNCTION' ) );
		if ($projectpositions = $model->getProjectPositionsOptions ( 0, 3 )) {
			$selectpositions = array_merge ( $selectpositions, $projectpositions );
		}
		$lists ['projectpositions'] = HTMLHelper::_ ( 'select.genericlist', $selectpositions, 'project_position_id', 'class="inputbox" size="1"', 'value', 'text' );

		$squad = array ();
		if (! $projectpositions) {
			JLToolBarHelper::title('ERROR');
			JLToolBarHelper::back('back');
			$msg = Text::_ ( 'COM_JOOMLEAGUE_ADMIN_MATCH_NO_REF_POS' );
			$app->enqueueMessage($msg, 'warning');
			return;
		}

		// generate selection list for each position
		foreach ( $projectpositions as $key => $pos ) {
			// get referees assigned to this position
			$squad [$key] = $model->getRefereeRoster($pos->value,$match_id);
		}
		if (count ( $squad ) > 0) {
			foreach ( $squad as $key => $referees ) {
				$temp [$key] = array ();
				if (isset ( $referees )) {
					foreach ( $referees as $referee ) {
						$temp [$key] [] = HTMLHelper::_ ( 'select.option', $referee->value, JoomleagueHelper::formatName ( null, $referee->firstname, $referee->nickname, $referee->lastname, $default_name_format ) );
					}
				}
				$lists ['team_referees' . $key] = HTMLHelper::_ ( 'select.genericlist', $temp [$key], 'position' . $key . '[]', 'id="testing" style="font-size:12px;height:auto;min-width:15em;" ' . 'class="inputbox position-starters" multiple="true" ', 'value', 'text' );
			}
		}
		$this->project_id=$project_id;
		$this->match=$match;
		$this->positions=$projectpositions;
		$this->lists=$lists;
		$this->team_id = $team_id;
		
		$this->addToolbarEditreferees();
		parent::display($tpl);
	}
	
	
	/**
	 * HTML-Class for the Editevents layout
	 */
	function _displayEditevents($tpl) 
	{
	    HTMLHelper::_('jquery.framework');
	    HTMLHelper::_('behavior.core');
		$app = Factory::getApplication();
		$option = $app->input->get('option');
		$project_id = $app->getUserState($option.'project');
		$input = $app->input;
		$match_id = $input->getInt('match_id');
	
		$team_id = $app->input->get('team_id','0');
		$params = ComponentHelper::getParams($option);
		$default_name_format = $params->get('name_format',14);
		$default_name_dropdown_list_order = $params->get('cfg_be_name_dropdown_list_order','lastname');

		// add the js script
		$baseurl = Uri::root();
		$document = Factory::getDocument();
		$document->addScript($baseurl . 'administrator/components/com_joomleague/assets/js/editevents.js');

		$model = $this->getModel(); // match-model
		$mdlMatch = BaseDatabaseModel::getInstance('match','JoomleagueModel');
		$matchData = $mdlMatch->getMatchTeams(false,$match_id);
		
		if (is_null($matchData)) {
			JLToolBarHelper::title('ERROR');
			JLToolBarHelper::back('back');
			$msg = Text::_('COM_JOOMLEAGUE_ADMIN_MATCH_NO_TEAM_MATCH');
			//JError::raiseWarning(440,'<br />'.Text::_('COM_JOOMLEAGUE_ADMIN_MATCH_NO_TEAM_MATCH').'<br /><br />');
			Factory::getApplication()->enqueueMessage($msg, 'warning');
			return false;
		}
		$teamname = ($team_id == $matchData->projectteam1_id) ? $matchData->team1 : $matchData->team2;
		//$this->_handlePreFillRoster($matchData, $model, $params, $matchData->projectteam1_id, $teamname);
		//$this->_handlePreFillRoster($matchData, $model, $params, $matchData->projectteam2_id, $teamname);

		$homeRoster = $mdlMatch->getTeamPlayers($matchData->projectteam1_id, false, $default_name_dropdown_list_order);
		if (count($homeRoster) == 0) {
		    $homeRoster = $mdlMatch->getGhostPlayer();
		}
		$awayRoster = $mdlMatch->getTeamPlayers($matchData->projectteam2_id, false, $default_name_dropdown_list_order);
		if (count($awayRoster) == 0) {
		    $awayRoster = $mdlMatch->getGhostPlayer();
		}
		$rosters = array (
				'home' => $homeRoster,
				'away' => $awayRoster
		);
		$matchevents = $model->getMatchEvents($match_id);
		
		$mdlProject = BaseDatabaseModel::getInstance('project','JoomleagueModel');
		$project = $mdlProject->getItem($project_id);

		$lists = array();

		// teams
		$teamlist = array();
		$teamlist [] = HTMLHelper::_('select.option',$matchData->projectteam1_id,$matchData->team1);
		$teamlist [] = HTMLHelper::_('select.option',$matchData->projectteam2_id,$matchData->team2);
		$lists['teams'] = HTMLHelper::_('select.genericlist',$teamlist,'team_id','class="span12 select-team"');

		// eventtypes
		$events = $model->getEventsOptions($project_id,$match_id);
		if (!$events) {
			JLToolBarHelper::title('ERROR');
			JLToolBarHelper::back('back');
			JError::raiseWarning(440,'<br />'.Text::_('COM_JOOMLEAGUE_ADMIN_MATCH_NO_EVENTS_POS').'<br /><br />');
			return false;
		}
		$eventlist = array ();
		$eventlist = array_merge($eventlist,$events);

		$lists['events'] = HTMLHelper::_('select.genericlist',$eventlist,'event_type_id','class="span12 select-event"');

		$this->overall_config=$mdlProject->getTemplateConfig('overall');
		$this->lists=$lists;
		$this->rosters=$rosters;
		$this->teams=$matchData;
		$this->matchevents=$matchevents;
		$this->default_name_format=$default_name_format;
		$this->default_name_dropdown_list_order=$default_name_dropdown_list_order;

		$this->addToolbarEditevents();
		parent::display($tpl);
	}
	
	
	/**
	 * HTML-Class for the Editeventsbb layout
	 */
	function _displayEditeventsbb($tpl) 
	{
		$app = Factory::getApplication ();
		$option = $app->input->get('option');
		$project_id = $app->getUserState($option.'project');
		$document = Factory::getDocument();
		$params = ComponentHelper::getParams($option);
		$input = $app->input;
		$match_id = $input->getInt('match_id');
		
		$default_name_format = $params->get('name_format',14);
		$default_name_dropdown_list_order = $params->get('cfg_be_name_dropdown_list_order','lastname');
		$tid = $app->input->get('team','0');

		$model = $this->getModel(); // match
		$teams = $model->getMatchTeams(false,$match_id);

		if (is_null($teams)) {
			JLToolBarHelper::title('ERROR');
			JLToolBarHelper::back('back');
			JError::raiseWarning(440,'<br />'.Text::_('COM_JOOMLEAGUE_ADMIN_MATCH_NO_TEAM_MATCH').'<br /><br />');
			return false;
		}
		
		// eventtypes
		$events = $model->getEventsOptions($project_id,$match_id);
		if (!$events) {
			$msg = '<br />'.Text::_('COM_JOOMLEAGUE_ADMIN_MATCH_NO_EVENTS_POS').'<br /><br />';
			$app->enqueueMessage($msg,'warning');
			$this->addToolbar_Editeventsbb(false);
			return false;
		}

		$homeRoster = $model->getTeamPlayers($teams->projectteam1_id, false, $default_name_dropdown_list_order);
		if (count($homeRoster) == 0) {
			$homeRoster = $model->getGhostPlayerbb($teams->projectteam1_id);
		}
		$awayRoster = $model->getTeamPlayers($teams->projectteam2_id, false, $default_name_dropdown_list_order);
		if (count($awayRoster) == 0) {
			$awayRoster = $model->getGhostPlayerbb($teams->projectteam2_id);
		}

		$this->homeRoster=$homeRoster;
		$this->awayRoster=$awayRoster;
		$this->teams=$teams;
		$this->events=$events;
		$this->default_name_format=$default_name_format;
		$this->default_name_dropdown_list_order=$default_name_dropdown_list_order;
		$this->match_id = $match_id;

		$this->addToolbar_Editeventsbb();
		parent::display($tpl);
	}
	
	
	/**
	 * Add the page title and toolbar
	 */
	protected function addToolbar_Editeventsbb($showSave = true) {
		
		// set toolbar items for the page
		JLToolBarHelper::title(Text::_('COM_JOOMLEAGUE_ADMIN_MATCH_EEBB_TITLE'),'jl-eventtypes');
		
		JLToolBarHelper::apply('match.saveeventbb');
		JLToolBarHelper::back('back', 'index.php?option=com_joomleague&view=matches');
	}
	
	
	function _displayEditstats($tpl) 
	{
		$app 	= Factory::getApplication();
		$input = $app->input;
		$option = $app->input->get('option');
		$project_id = $app->getUserState($option.'project');
		$document = Factory::getDocument();
		$params = ComponentHelper::getParams($option);
		$default_name_format = $params->get("name_format");
		$team_id	= $app->input->get('team_id','0' );
		$match_id	= $input->getInt('match_id');

		// add the js script
		$version = urlencode(JoomleagueHelper::getVersion());
		//$document->addScript(Uri::root().'/administrator/components/com_joomleague/assets/js/editmatchstats.js?v='.$version);

		$model = $this->getModel();
		
		$mdlMatch = BaseDatabaseModel::getInstance('match','JoomleagueModel');
		$match = $mdlMatch->getItem($match_id);
		
		$teams = $model->getMatchTeams(false,$match_id);

		if (is_null($teams)) {
			JLToolBarHelper::title('ERROR');
			JLToolBarHelper::back('back');
			JError::raiseWarning(440,'<br />'.Text::_('COM_JOOMLEAGUE_ADMIN_MATCH_NO_TEAM_MATCH').'<br /><br />');
			return false;
		}

		$positions = $this->get('ProjectPositions');
		$staffpositions = $this->get('ProjectStaffPositions');

		$homeRoster = $model->getMatchPlayers($teams->projectteam1_id,false,$match_id);
		if (count($homeRoster) == 0) {
			$homeRoster = $model->getGhostPlayerbb ( $teams->projectteam1_id );
		}
		$awayRoster = $model->getMatchPlayers($teams->projectteam2_id,false,$match_id);
		if (count($awayRoster) == 0) {
			$awayRoster = $model->getGhostPlayerbb($teams->projectteam2_id);
		}

		$homeStaff = $model->getMatchStaffs($teams->projectteam1_id,false,$match_id);
		$awayStaff = $model->getMatchStaffs($teams->projectteam2_id,false,$match_id);

		// stats
		$stats = $model->getInputStats($match_id);
		if (!$stats) {
			JLToolBarHelper::title('ERROR');
			JLToolBarHelper::back('back');
			JError::raiseWarning(440,'<br />'.Text::_('COM_JOOMLEAGUE_ADMIN_MATCH_NO_STATS_POS').'<br /><br />');
			return false;
		}
		$playerstats = $model->getMatchStatsInput($match_id);
		$staffstats = $model->getMatchStaffStatsInput($match_id);

		$this->homeRoster=$homeRoster;
		$this->awayRoster=$awayRoster;
		$this->homeStaff=$homeStaff;
		$this->awayStaff=$awayStaff;
		$this->teams=$teams;
		$this->stats=$stats;
		$this->playerstats=$playerstats;
		$this->staffstats=$staffstats;
		$this->match=$match;
		$this->positions=$positions;
		$this->staffpositions=$staffpositions;
		$this->default_name_format=$default_name_format;
		
		$this->addToolbarEditstats();
		parent::display($tpl);
	}
	
	
	/**
	 * HTML-Class for EditLineup layout
	 */
	function _displayEditlineup($tpl) 
	{
		$app = Factory::getApplication();
		$input = $app->input;
		$option = $input->getCmd('option');
		$match_id = $input->getInt('match_id');
		$project_id = $app->getUserState($option.'project');
		$document = Factory::getDocument ();
		$tid = $input->getInt('team_id','0');
		$params = ComponentHelper::getParams($option);
		$default_name_format = $params->get('name_format');
		$default_name_dropdown_list_order = $params->get('cfg_be_name_dropdown_list_order','lastname');

		// add the js script
		$version = urlencode(JoomleagueHelper::getVersion());
		//$document->addScript(Uri::root().'/administrator/components/com_joomleague/assets/js/editlineup.js');

		$model = $this->getModel();
		$matchData = $model->getMatchTeams(false,$match_id);
		
		if (is_null($matchData)) {
			JLToolBarHelper::title('ERROR');
			JLToolBarHelper::back('back');
			JError::raiseWarning(440,'<br />'.Text::_('COM_JOOMLEAGUE_ADMIN_MATCH_NO_TEAM_MATCH').'<br /><br />');
			return false;
		}
		$teamname = ($tid == $matchData->projectteam1_id) ? $matchData->team1 : $matchData->team2;
		$this->_handlePreFillRoster($matchData, $model, $params, $tid, $teamname,$match_id);

		// get starters
		$starters = $model->getRoster($tid ,false,$match_id);
		$starters_id = array_keys($starters);

		// get players not already assigned to starter
		$not_assigned = $model->getTeamPlayers($tid,$starters_id,$default_name_dropdown_list_order);
		if (!$not_assigned && !$starters_id) 
		{
			JLToolBarHelper::title('ERROR');
			JLToolBarHelper::back('back');
			JError::raiseWarning(440,'<br />'.Text::_('COM_JOOMLEAGUE_ADMIN_MATCH_NO_PLAYERS_MATCH').'<br /><br />');
			return false;
		}

		$projectpositions = $model->getProjectPositions();
		if (!$projectpositions) {
			JLToolBarHelper::title('ERROR');
			JLToolBarHelper::back('back');
			JError::raiseWarning(440,'<br />'.Text::_('COM_JOOMLEAGUE_ADMIN_MATCH_NO_POS').'<br /><br />');
			return false;
		}

		// build select list for not assigned players
		$not_assigned_options = array();
		foreach ((array) $not_assigned as $p)
		{
			if ($p->jerseynumber > 0) {
				$jerseynumber = '[' . $p->jerseynumber . '] ';
			} else {
				$jerseynumber = '';
			}
			switch ($default_name_dropdown_list_order) {
				case 'lastname' :
				case 'firstname' :
					$not_assigned_options [] = HTMLHelper::_('select.option', $p->value, $jerseynumber . JoomleagueHelper::formatName ( null, $p->firstname, $p->nickname, $p->lastname, $default_name_format ) );
					break;

				case 'position' :
					$not_assigned_options[] = HTMLHelper::_('select.option', $p->value, '(' . Text::_ ( $p->positionname ) . ') - '.$jerseynumber.JoomleagueHelper::formatName(null,$p->firstname,$p->nickname,$p->lastname,$default_name_format));
					break;
			}
		}
		$lists ['team_players'] = HTMLHelper::_('select.genericlist',$not_assigned_options,'roster[]','style="font-size:12px;height:auto;min-width:15em;" class="inputbox" multiple="true" size="18"','value','text');

		// build position select
		$selectpositions[] = HTMLHelper::_('select.option','0',Text::_('COM_JOOMLEAGUE_GLOBAL_SELECT_IN_POSITION'));
		$selectpositions = array_merge($selectpositions,$model->getProjectPositionsOptions(0,1));
		$lists ['projectpositions'] = HTMLHelper::_('select.genericlist',$selectpositions,'project_position_id','class="inputbox" size="1"','value','text',NULL,false,true);

		// build player select for substitutions

		// starters + came in (because of multiple substitutions possibility in amateur soccer clubs for example)
		$substitutions = $model->getSubstitutions($tid,$match_id);
		$starters = array_merge($starters,$substitutions[$tid]);

		// not assigned players + went out (because of multiple substitutions possibility in amateur soccer clubs for example)
		$not_assigned = array_merge($not_assigned, $substitutions [$tid]);

		// filter out duplicates $starters
		$new_starters = array ();
		$exclude = array (
				""
		);
		for($i = 0; $i <= count($starters ) - 1; $i ++) {
			if (! in_array(trim($starters [$i]->value), $exclude)) {
				$new_starters[] = $starters [$i];
				$exclude[] = trim($starters [$i]->value);
			}
		}
		// filter out duplicates $not_assigned
		$new_not_assigned = array();
		$exclude = array(
				""
		);
		for($i = 0; $i <= count ( $not_assigned ) - 1; $i ++) {
			if (array_key_exists ( 'came_in', $not_assigned [$i] ) && $not_assigned [$i]->came_in == 1) {
				if (! in_array ( trim ( $not_assigned [$i]->in_for ), $exclude )) {
					$new_not_assigned [] = $not_assigned [$i];
					$exclude [] = trim ( $not_assigned [$i]->in_for );
				}
			} elseif (! array_key_exists ( 'came_in', $not_assigned [$i] )) {
				if (! in_array ( trim ( $not_assigned [$i]->value ), $exclude )) {
					$new_not_assigned [] = $not_assigned [$i];
					$exclude [] = trim ( $not_assigned [$i]->value );
				}
			}
		}
	
		$playersoptions_subs_out = array ();
		$playersoptions_subs_out [] = HTMLHelper::_ ( 'select.option', '0', Text::_ ( 'COM_JOOMLEAGUE_GLOBAL_SELECT_PLAYER' ) );
		$i = 0;
		foreach ( ( array ) $new_starters as $player ) {
			switch ($default_name_dropdown_list_order) {
				case 'lastname' :
				case 'firstname' :
					if (array_key_exists ( 'came_in', $player )) {
						$i ++;
						if ($i == 1) {
							$playersoptions_subs_out[]=HTMLHelper::_('select.option','0',Text::_('COM_JOOMLEAGUE_ADMIN_MATCH_ELUSUBST_SELECT_PLAYER_ALREADY_IN'));
						}
					}
					$playersoptions_subs_out [] = HTMLHelper::_ ( 'select.option', $player->value, JoomleagueHelper::formatName ( null, $player->firstname, $player->nickname, $player->lastname, $default_name_format ) );
					break;

				case 'position' :
					if (array_key_exists ( 'came_in', $player )) {
						$i ++;
						if ($i == 1) {
							$playersoptions_subs_out[]=HTMLHelper::_('select.option','0',Text::_('COM_JOOMLEAGUE_ADMIN_MATCH_ELUSUBST_SELECT_PLAYER_ALREADY_IN'));
						}
					}
					$playersoptions_subs_out [] = HTMLHelper::_ ( 'select.option', $player->value, '(' . Text::_ ( $player->positionname ) . ') - ' . JoomleagueHelper::formatName ( null, $player->firstname, $player->nickname, $player->lastname, $default_name_format ) );
					break;
			}
		}

		$playersoptions_subs_in = array ();
		$playersoptions_subs_in [] = HTMLHelper::_ ( 'select.option', '0', Text::_ ( 'COM_JOOMLEAGUE_GLOBAL_SELECT_PLAYER' ) );
		$i = 0;
		foreach ( ( array ) $new_not_assigned as $player ) {
			switch ($default_name_dropdown_list_order) {
				case 'lastname' :
				case 'firstname' :
					if (array_key_exists ( 'came_in', $player ) && $player->came_in == 1 && $player->in_for > 0) {
						$i ++;
						if ($i == 1) {
							$playersoptions_subs_in[]=HTMLHelper::_('select.option','0',Text::_('COM_JOOMLEAGUE_ADMIN_MATCH_ELUSUBST_SELECT_PLAYER_ALREADY_OUT'));
						}
						$playersoptions_subs_in [] = HTMLHelper::_ ( 'select.option', $player->in_for, JoomleagueHelper::formatName ( null, $player->out_firstname, $player->out_nickname, $player->out_lastname, $default_name_format ) );
					} elseif (! array_key_exists ( 'came_in', $player )) {
						$playersoptions_subs_in [] = HTMLHelper::_ ( 'select.option', $player->value, JoomleagueHelper::formatName ( null, $player->firstname, $player->nickname, $player->lastname, $default_name_format ) );
					}
					break;

				case 'position' :
					if (array_key_exists ( 'came_in', $player ) && $player->came_in == 1 && $player->in_for > 0) {
						$i ++;
						if ($i == 1) {
							$playersoptions_subs_in[]=HTMLHelper::_('select.option','0',Text::_('COM_JOOMLEAGUE_ADMIN_MATCH_ELUSUBST_SELECT_PLAYER_ALREADY_OUT'));
						}
						$playersoptions_subs_in [] = HTMLHelper::_ ( 'select.option', $player->in_for, '(' . Text::_ ( $player->positionname_out ) . ') - ' . JoomleagueHelper::formatName ( null, $player->out_firstname, $player->out_nickname, $player->out_lastname, $default_name_format ) );
					} elseif (! array_key_exists ( 'came_in', $player )) {
						$playersoptions_subs_in [] = HTMLHelper::_ ( 'select.option', $player->value, '(' . Text::_ ( $player->positionname ) . ') - ' . JoomleagueHelper::formatName ( null, $player->firstname, $player->nickname, $player->lastname, $default_name_format ) );
					}
					break;
			}
		}
		// $lists['all_players']=HTMLHelper::_( 'select.genericlist',$playersoptions,'roster[]',
		// 'id="roster" style="font-size:12px;height:auto;min-width:15em;" class="inputbox" size="4"',
		// 'value','text');

		// generate selection list for each position
		$starters = array();
		foreach ($projectpositions as $position_id => $pos) {
			// get players assigned to this position
			$starters [$position_id] = $model->getRoster($tid, $pos->pposid,$match_id);
		}

		foreach ( $starters as $position_id => $players ) {
			$options = array ();
			foreach ( ( array ) $players as $p ) {
				if ($p->jerseynumber > 0) {
					$jerseynumber = '[' . $p->jerseynumber . '] ';
				} else {
					$jerseynumber = '';
				}
				$options [] = HTMLHelper::_ ( 'select.option', $p->value, $jerseynumber . JoomleagueHelper::formatName ( null, $p->firstname, $p->nickname, $p->lastname, $default_name_format ) );
			}

			$lists ['team_players' . $position_id] = HTMLHelper::_ ( 'select.genericlist', $options, 'position' . $position_id . '[]', 'style="font-size:12px;height:auto;min-width:15em;" size="4" class="inputbox position-starters" multiple="true" ', 'value', 'text' );
		}

		// staff positions //
		$staffpositions = $model->getProjectStaffPositions (); 
		
		// assigned staff
		$assigned = $model->getMatchStaffs($tid,false,$match_id);
		$assigned_id = array_keys($assigned);
		// not assigned staff
		$not_assigned = $model->getTeamStaffs ( $tid, $assigned_id, $default_name_dropdown_list_order );

		// build select list for not assigned
		$not_assigned_options = array ();
		foreach ( ( array ) $not_assigned as $p ) {

			switch ($default_name_dropdown_list_order) {
				case 'lastname' :
				case 'firstname' :
					$not_assigned_options [] = HTMLHelper::_ ( 'select.option', $p->value, JoomleagueHelper::formatName ( null, $p->firstname, $p->nickname, $p->lastname, $default_name_format ) );
					break;

				case 'position' :
					$not_assigned_options[] = HTMLHelper::_( 'select.option', $p->value,'('.Text::_($p->positionname).') - '.JoomleagueHelper::formatName(null,$p->firstname,$p->nickname,$p->lastname,$default_name_format ) );
					break;
			}
		}
		$lists['team_staffs'] = HTMLHelper::_('select.genericlist', $not_assigned_options, 'staff[]', 'style="font-size:12px;height:auto;min-width:15em;" size="18" class="inputbox" multiple="true" size="18"', 'value', 'text' );

		// generate selection list for each position
		$options = array ();
		foreach ( $staffpositions as $position_id => $pos ) {
			// get players assigned to this position
			$options = array ();
			foreach ( $assigned as $staff ) {
				if ($staff->project_position_id == $pos->pposid) {
					$options [] = HTMLHelper::_ ( 'select.option', $staff->team_staff_id, JoomleagueHelper::formatName(null,$staff->firstname,$staff->nickname,$staff->lastname,$default_name_format));
				}
			}
			$lists ['team_staffs' . $position_id] = HTMLHelper::_ ( 'select.genericlist', $options, 'staffposition' . $position_id . '[]', 'style="font-size:12px;height:auto;min-width:15em;" size="4" class="inputbox position-staff" multiple="true" ', 'value', 'text' );
		}

		$this->match=$matchData;
		$this->tid=$tid;
		$this->teamname=$teamname;
		$this->positions=$projectpositions;
		$this->staffpositions=$staffpositions;
		$this->substitutions=$substitutions [$tid];
		$this->playersoptions_subs_out=$playersoptions_subs_out;
		$this->playersoptions_subs_in=$playersoptions_subs_in;
		$this->lists=$lists;
		$this->default_name_format=$default_name_format;
		$this->default_name_dropdown_list_order=$default_name_dropdown_list_order;

		$this->addToolbarEditlineup();
		parent::display($tpl);
	}
	

	protected function _handlePreFillRoster(&$matchData, &$model, &$params, &$tid, &$teamname) 
	{
		$app = Factory::getApplication();
		$input = $app->input;
		$match_id = $input->getInt('match_id');
		
		$preFillSuccess = false;
		
		if ($params->get('use_prefilled_match_roster') > 0) 
		{
			$bDeleteCurrrentRoster = $params->get('on_prefill_delete_current_match_roster',0);
			$prefillType = $input->getInt('prefill',0);
			if ($prefillType == 0) {
				$prefillType = $params->get('use_prefilled_match_roster');
			}
			$projectteam_id = ($tid == $matchData->projectteam1_id) ? $matchData->projectteam1_id : $matchData->projectteam2_id;

			if ($prefillType == 2) {
				$preFillSuccess = false;
				if (!$model->prefillMatchPlayersWithProjectteamPlayers($projectteam_id,$bDeleteCurrrentRoster,$match_id)) {
					if ($model->getError() != '') {
						JLToolBarHelper::title('ERROR');
						JLToolBarHelper::back('back');
						JError::raiseWarning(440,'<br />'.$model->getError().'<br /><br />');
						return false;
					} else {
						$preFillSuccess = false;
					}
				} else {
					$preFillSuccess = true;
				}
			} elseif ($prefillType == 1) {
				if (!$model->prefillMatchPlayersWithLastMatch($projectteam_id,$bDeleteCurrrentRoster,$match_id)) {
					if ($model->getError() != '') {
						JLToolBarHelper::title('ERROR');
						JLToolBarHelper::back('back');
						JError::raiseWarning(440,'<br />'.$model->getError().'<br /><br />');
						return false;
					} else {
						$preFillSuccess = false;
					}
				} else {
					$preFillSuccess = true;
				}
			}
		}
		$this->preFillSuccess=$preFillSuccess;
	}
	
	
	/**
	 * Add the page title and toolbar
	 */
	protected function addToolbarForm()
	{
		$app 	= Factory::getApplication();
		$input = $app->input;
		
		JLToolBarHelper::title(Text::sprintf('COM_JOOMLEAGUE_ADMIN_MATCH_F_TITLE',$this->item->hometeam,$this->item->awayteam),'jl-Matchdays');
		JLToolBarHelper::apply('match.apply');
		JLToolBarHelper::save('match.save');
		JLToolBarHelper::divider();
		JLToolBarHelper::cancel('match.cancel');
	}
	
	

	/**
	 * Add the page title and toolbar
	 */
	protected function addToolbarEditevents()
	{
		$app 	= Factory::getApplication();
		$input = $app->input;
	
		JLToolBarHelper::title(Text::sprintf('COM_JOOMLEAGUE_ADMIN_MATCH_EE_TITLE', $this->teams->team1, $this->teams->team2),'jl-Matchdays');
		JLToolBarHelper::back('back','index.php?option=com_joomleague&view=matches');
	}
	
	
	/**
	 * Add the page title and toolbar
	 */
	protected function addToolbarEditeventsbb()
	{
		$app 	= Factory::getApplication();
		$input = $app->input;
	
		JLToolBarHelper::title(Text::_('Editeventsbb'),'jl-Matchdays');
		JLToolBarHelper::cancel('match.cancel');
	}
	
	
	/**
	 * Add the page title and toolbar
	 */
	protected function addToolbarEditlineup()
	{
		$app 	= Factory::getApplication();
		$input = $app->input;
	
		JLToolBarHelper::title(Text::sprintf('COM_JOOMLEAGUE_ADMIN_MATCH_ELU_TITLE',$this->teamname),'jl-Matchdays');
		JLToolBarHelper::apply('match.saveroster');
		JLToolBarHelper::save('match.saveroster2');
		JLToolBarHelper::cancel('match.cancel');
	}
	
	
	/**
	 * Add the page title and toolbar
	 */
	protected function addToolbarEditreferees()
	{
		$app 	= Factory::getApplication();
		$input = $app->input;
	
		JLToolBarHelper::title(Text::_('COM_JOOMLEAGUE_ADMIN_MATCH_ER_TITLE'),'jl-Matchdays');
		JLToolBarHelper::apply('match.saveReferees');
		JLToolBarHelper::save('match.saveReferees2');
		JLToolBarHelper::cancel('match.cancel');
	}
	
	
	/**
	 * Add the page title and toolbar
	 */
	protected function addToolbarEditstats()
	{
		$app 	= Factory::getApplication();
		$input = $app->input;
	
		JLToolBarHelper::title(Text::_('Stats'),'jl-Matchdays');
		JLToolBarHelper::apply('match.savestats');
		JLToolBarHelper::save('match.savestats2');
		JLToolBarHelper::cancel('match.cancel');
	}
}
