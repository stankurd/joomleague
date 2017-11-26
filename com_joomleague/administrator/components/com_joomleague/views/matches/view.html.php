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
use Joomla\CMS\Uri\Uri;
use Joomla\String\StringHelper;
use Joomla\Utilities\ArrayHelper;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;

defined('_JEXEC') or die;


/**
 * HTML View class
*/

class JoomleagueViewMatches extends JLGView
{
	public function display($tpl = null)
	{
		$app	= Factory::getApplication();
		$input = $app->input;
		$option = $input->get('option');
		$uri		= Uri::getInstance();
		$params		= ComponentHelper::getParams($option);

		$filter_state		= $app->getUserStateFromRequest($option.'mc_filter_state',	'filter_state', 	'', 'word');
		$filter_order		= $app->getUserStateFromRequest($option.'mc_filter_order',	'filter_order', 	'mc.match_number', 'cmd');
		$filter_order_Dir	= $app->getUserStateFromRequest($option.'mc_filter_order_Dir','filter_order_Dir', '', 'word');
		$search				= $app->getUserStateFromRequest($option.'mc_search', 'search',					'', 'string');
		$search_mode		= $app->getUserStateFromRequest($option.'mc_search_mode',		'search_mode',		'', 'string');
		$division			= $app->getUserStateFromRequest($option.'mc_division',		'division',			'',	'string');
		$project_id			= $app->getUserState( $option . 'project' );
		$search				= StringHelper::strtolower($search);
		
		$round_id = $input->get('rid',array(),'array');
		ArrayHelper::toInteger($round_id);
		if(empty($round_id))
		{
			$round_id = $app->getUserState($option.'round_id',false);
		}
		else
		{
			$round_id = $round_id[0];
			$app->setUserState('com_joomleagueround_id',$round_id);
		}
		$mdlRound = BaseDatabaseModel::getInstance('round','JoomleagueModel');
		$round = $mdlRound->getItem($round_id);

		$matches		= $this->get('Data');		
		$total			= $this->get('Total');
		$pagination		= $this->get('Pagination');
		$model			= $this->getModel();
		$projectteams	= $model->getProjectTeams();
		
		// state filter
		$lists['state'] = JoomleagueHelper::stateOptions($filter_state,true,true,false,false);

		// table ordering
		$lists['order_Dir']=$filter_order_Dir;
		$lists['order']=$filter_order;

		// search filter
		$lists['search']=$search;
		$lists['search_mode']=$search_mode;
				
		//build the html options for teams
		foreach ($matches as $row)
		{
			$teams[]=JHtml::_('select.option','0',JText::_('COM_JOOMLEAGUE_GLOBAL_SELECT_TEAM'));
			$divhomeid = 0;
			//apply the filter only if both teams are from the same division
			//teams are not from the same division in tournament mode with divisions
			if($row->divhomeid==$row->divawayid) {
				$divhomeid = $row->divhomeid;
			} else {
				$row->divhomeid =0;
				$row->divawayid =0;
			}
			if ($projectteams = $model->getProjectTeamsOptions($divhomeid)){
				$teams=array_merge($teams,$projectteams);
			}
			$lists['teams_'+$divhomeid] = $teams;
			unset($teams);
		}
		// build the html selectlist for rounds
		$mdlProject = BaseDatabaseModel::getInstance('project','JoomleagueModel');
		$project = $mdlProject->getItem($project_id);
				
		
		
		$ress = JoomleagueHelper::getRoundsOptions($project_id, 'ASC', true);
		$project_roundslist = array();
		foreach ($ress as $res)
		{
			$project_roundslist[]=JHtml::_('select.option', $res->id, $this->getRoundDescription($res));
		}
		$lists['project_rounds']=JHtml::_(	'select.genericList',$project_roundslist,'rid[]',
				'class="inputbox" ' .
				'onChange="document.getElementById(\'short_act\').value=\'rounds\';' .
				'document.roundForm.submit();" ',
				'value','text',$round->id);

		$lists['project_rounds2']=JHtml::_('select.genericList',$project_roundslist,'rid','class="inputbox" ','value','text',$round->id);

		//build the html selectlist for matches
		$overall_config=$mdlProject->getTemplateConfig('overall');
		if ((isset($overall_config['use_jl_substitution']) && $overall_config['use_jl_substitution']) ||
				(isset($overall_config['use_jl_events']) && $overall_config['use_jl_events']))
		{
			$match_list=array();
			$mdd[]=JHtml::_('select.option','0',JText::_('COM_JOOMLEAGUE_GLOBAL_SELECT_MATCH'));

			foreach ($matches as $row)
			{
				$mdd[]=JHtml::_('select.option','index3.php?option=com_joomleague&task=match.editEvents&cid[0]='.$row->id,$row->team1.'-'.$row->team2);
			}
			$RosterEventMessage=(isset($overall_config['use_jl_substitution']) && $overall_config['use_jl_substitution']) ? JText::_('COM_JOOMLEAGUE_ADMIN_MATCHES_LINEUP') : '';
			if (isset($overall_config['use_jl_events']) && $overall_config['use_jl_events'])
			{
				if (isset($overall_config['use_jl_events']) && $overall_config['use_jl_substitution']){
					$RosterEventMessage .= ' / ';
				}
				$RosterEventMessage .= JText::_('COM_JOOMLEAGUE_ADMIN_MATCHES_EVENTS');
			}
			$RosterEventMessage .= ($RosterEventMessage != '') ? ':' : '';
			$lists['RosterEventMessage']=$RosterEventMessage;

			$lists['round_matches']=JHtml::_(	'select.genericList',$mdd,'mdd',
					'id="mdd" class="inputbox" onchange="jl_load_new_match_events(this,\'eventscontainer\')"',
					'value','text','0');
		}

		//build the html options for extratime
		$match_result_type[]=JHtmlSelect::option('0',JText::_('COM_JOOMLEAGUE_ADMIN_MATCHES_RT'));
		$match_result_type[]=JHtmlSelect::option('1',JText::_('COM_JOOMLEAGUE_ADMIN_MATCHES_OT'));
		$match_result_type[]=JHtmlSelect::option('2',JText::_('COM_JOOMLEAGUE_ADMIN_MATCHES_SO'));
		$lists['match_result_type']=$match_result_type;
		unset($match_result_type);

		//build the html options for massadd create type
		$createTypes=array(	0 => JText::_('COM_JOOMLEAGUE_ADMIN_MATCHES_MASSADD'),
				1 => JText::_('COM_JOOMLEAGUE_ADMIN_MATCHES_MASSADD_1'),
				2 => JText::_('COM_JOOMLEAGUE_ADMIN_MATCHES_MASSADD_2')
		);
		$ctOptions=array();
		foreach($createTypes AS $key => $value){
			$ctOptions[]=JHtmlSelect::option($key,$value);
		}
		$lists['createTypes']=JHtmlSelect::genericlist($ctOptions,'ct[]','class="inputbox" onchange="javascript:displayTypeView();"','value','text',1,'ct');
		unset($createTypes);

		// build the html radio for adding into one round / all rounds
		$createYesNo=array(0 => JText::_('COM_JOOMLEAGUE_GLOBAL_NO'),1 => JText::_('COM_JOOMLEAGUE_GLOBAL_YES'));
		$ynOptions=array();
		foreach($createYesNo AS $key => $value){
			$ynOptions[]=JHtmlSelect::option($key,$value);
		}
		$lists['addToRound']=JHtmlSelect::radiolist($ynOptions,'addToRound','class="inputbox"','value','text',0);

		// build the html radio for auto publish new matches
		$lists['autoPublish']=JHtmlSelect::radiolist($ynOptions,'autoPublish','class="inputbox"','value','text',0);
		//build the html options for divisions
		$divisions[]=JHtmlSelect::option('0',JText::_('COM_JOOMLEAGUE_GLOBAL_SELECT_DIVISION'));
		$mdlDivisions = BaseDatabaseModel::getInstance("divisions", "JoomLeagueModel");
		if ($res = $mdlDivisions->getDivisions($project_id)){
			$divisions=array_merge($divisions,$res);
		}
		$lists['divisions']=$divisions;
		unset($divisions);
		
		$this->division=$division;
		$this->user=Factory::getUser();
		$this->lists=$lists;
		$this->items=$matches;
		$this->ress=$ress;
		$this->project=$project;
		$this->round=$round;
		$this->pagination=$pagination;
		$this->teams=$projectteams;
		$this->request_url=$uri->toString();
		$this->prefill=$params->get('use_prefilled_match_roster',0);
		
		$this->addToolbar();
		parent::display($tpl);
	}

	/**
	 * Add the page title and toolbar
	 */
	protected function addToolbar()
	{
		$app 	 = Factory::getApplication();
		$input	 = $app->input;
		$massadd = $input->getInt('massadd',0);

		// Set toolbar items for the page
		JLToolBarHelper::title(JText::_('COM_JOOMLEAGUE_ADMIN_MATCHES_TITLE'),'jl-Matchdays');

		if (!$massadd)
		{
			JLToolBarHelper::publishList('matches.publish');
			JLToolBarHelper::unpublishList('matches.unpublish');
			JLToolBarHelper::divider();

			JLToolBarHelper::apply('matches.saveshort');
			JLToolBarHelper::divider();

			JLToolBarHelper::custom('matches.massadd','new.png','new_f2.png','COM_JOOMLEAGUE_ADMIN_MATCHES_MASSADD_MATCHES',false);
			JLToolBarHelper::addNewX('match.addmatch','COM_JOOMLEAGUE_ADMIN_MATCHES_MASSADD_ADD_MATCH');
			JLToolBarHelper::deleteList(JText::_('COM_JOOMLEAGUE_ADMIN_MATCHES_MASSADD_WARNING'), 'matches.remove');
			JLToolBarHelper::divider();

			JLToolBarHelper::back('Back','index.php?option=com_joomleague&view=rounds');
		}
		else
		{
			JLToolBarHelper::custom('matches.cancelmassadd','cancel.png','cancel_f2.png','COM_JOOMLEAGUE_ADMIN_MATCHES_MASSADD_CANCEL_MATCHADD',false);
		}
		JLToolBarHelper::help('screen.joomleague',true);
	}
	
	private function getRoundDescription($round)
	{
		$first = new DateTime($round->round_date_first);
		$last = new DateTime($round->round_date_last);
		return $round->name.' ('.
				$first->format(JText::_('COM_JOOMLEAGUE_ADMIN_MATCHES_DATE_FORMAT')).' - '.
				$last->format(JText::_('COM_JOOMLEAGUE_ADMIN_MATCHES_DATE_FORMAT')).')';
	}
}
