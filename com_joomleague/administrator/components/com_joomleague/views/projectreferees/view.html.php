<?php
/**
 * Joomleague
 *
 * @copyright	Copyright (C) 2006-2015 joomleague.at. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @link		http://www.joomleague.at
 *
 * @todo
 * check JLGModel::getinstance and see if it should be applied to other views
 */
use Joomla\CMS\Factory;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;

defined('_JEXEC') or die;


/**
 * HTML View class
 */
class JoomleagueViewProjectReferees extends JLGView
{
	protected $items;
	protected $pagination;
	protected $state;

	public function display($tpl = null)
	{
		if($this->getLayout() == 'editlist')
		{
			$this->_displayEditlist($tpl);
			return;
		}

		if($this->getLayout() == 'default')
		{
			$this->_displayDefault($tpl);
			return;
		}

		if($this->getLayout() == 'assignplayers')
		{
			$this->_displayAssignPlayers($tpl);
			return;
		}

		parent::display($tpl);
	}


	function _displayEditlist($tpl)
	{
		$app = Factory::getApplication();
		$jinput = $app->input;
		$option = $jinput->getCmd('option');
		$project_id = $app->getUserState($option.'project');
		$uri = Uri::getInstance();

		$model = $this->getModel();

		$this->items = $this->get('Items');
		$this->pagination = $this->get('Pagination');
		$this->state = $this->get('State');

		$mdlProject = BaseDatabaseModel::getInstance('project','JoomleagueModel');
		$project 	= $mdlProject->getItem($project_id);

		// build the html select list for project assigned players
		$ress = array();
		$res1 = array();
		$notusedplayers = array();
		if($ress = $model->getProjectPlayers())
		{
			foreach($ress1 as $res1)
			{
				$used = 0;
				foreach($ress as $res)
				{
					if($res1->value == $res->value)
					{
						$used = 1;
					}
				}
				if($used == 0)
				{
					$notusedplayers[] = JHtml::_('select.option',$res1->value,
							JoomleagueHelper::formatName(null,$res1->firstname,$res1->nickname,$res1->lastname,0).' ('.$res1->notes.')');
				}
			}
		}
		else
		{
			foreach($ress1 as $res1)
			{
				$notusedplayers[] = JHtml::_('select.option',$res1->value,
						JoomleagueHelper::formatName(null,$res1->firstname,$res1->nickname,$res1->lastname,0).' ('.$res1->notes.')');
			}
		}

		// build the html select list for players
		if(count($notusedplayers) > 0)
		{
			$lists['players'] = JHtml::_('select.genericlist',$notusedplayers,'playerslist[]',
					' style="width:150px" class="inputbox" multiple="true" size="30"','value','text');
		}
		else
		{
			$lists['players'] = '<select name="playerslist[]" id="playerslist" style="width:150px" class="inputbox" multiple="true" size="10"></select>';
		}
		unset($res);
		unset($res1);
		unset($notusedplayers);

		$this->user = Factory::getUser();
		$this->lists = $lists;
		$this->projectplayer = $projectplayer;
		$this->project = $project;
		$this->request_url = $uri->toString();

		parent::display($tpl);
	}


	function _displayDefault($tpl)
	{
		$app 	= Factory::getApplication();
		$jinput = $app->input;
		$option = $jinput->getCmd('option');
		$user	= Factory::getUser();
		$uri 	= Uri::getInstance();

		$project_id = $app->getUserState($option.'project');
		$team_id 	= $app->getUserState($option.'team');

		$this->items = $this->get('Items');
		$this->pagination = $this->get('Pagination');
		$this->state = $this->get('State');

		$baseurl = Uri::root();
		$document = Factory::getDocument();
		$document->addStyleSheet($baseurl.'administrator/components/com_joomleague/assets/css/Autocompleter.css');

		$model = $this->getModel(); // model ProjectReferees

		$mdlProject = BaseDatabaseModel::getInstance('project','JoomleagueModel');
		$project = $mdlProject->getItem($project_id);

		// build the html options for position
		$position_id = array();
		$position_id[] = JHtml::_('select.option','0',JText::_('COM_JOOMLEAGUE_GLOBAL_SELECT_REF_FUNCTION'));
		if($res = $model->getRefereePositions())
		{
			$position_id = array_merge($position_id,$res);
		}
		$lists['project_position_id'] = $position_id;
		unset($position_id);

		$this->user = $user;
		$this->lists = $lists;
		$this->project = $project;
		$this->filterForm = $this->get('FilterForm');
		$this->activeFilters = $this->get('ActiveFilters');

		$this->addToolbar();
		parent::display($tpl);
	}


	/**
	 * Assign Players
	 */
	function _displayAssignPlayers($tpl = null)
	{
		$app 	= Factory::getApplication();
		$jinput = $app->input;
		$option = $jinput->getCmd('option');
		$params = ComponentHelper::getParams($option);
		$type 	= $jinput->getInt('type');
		$uri 	= Uri::getInstance();

		$project_id = $app->getUserState($option.'project');
		$project_team_id = $app->getUserState($option.'project_team_id');

		$mdlPerson	  = BaseDatabaseModel::getInstance('persons','JoomleagueModel');
		$mdlProject   = BaseDatabaseModel::getInstance('project','JoomLeagueModel');
		$mdlProjectreferees   = BaseDatabaseModel::getInstance('projectreferees','JoomLeagueModel');
		$mdlQuickAdd = JLGModel::getInstance('Quickadd','JoomleagueModel');

		$project_name = $mdlProject->getProjectName($project_id);
		$team_name = $mdlPerson->getProjectTeamName($project_team_id);

		$model = $this->getModel();
		$model->setState('filter.assign',true);
		
		$this->items = $this->get('Items');
		$this->pagination = $this->get('Pagination');
		$this->state = $this->get('State');
	
		$this->prjid = $project_id;
		$this->prj_name = $project_name;
		/* $this->team_id = $team_id; */
		$this->team_name = $team_name;
		$this->project_team_id = $project_team_id;
		$this->request_url = $uri->toString();
		$this->component_params = $params;
		
		$this->filterForm = $this->get('FilterForm');
		$this->activeFilters = $this->get('ActiveFilters');

		JLToolBarHelper::apply('projectreferee.saveassigned','COM_JOOMLEAGUE_ADMIN_PERSONS_SAVE_SELECTED');
		JLToolBarHelper::back('COM_JOOMLEAGUE_ADMIN_PERSONS_BACK','index.php?option=com_joomleague&view=projectreferees');
		JLToolBarHelper::title(JText::_('COM_JOOMLEAGUE_ADMIN_PERSONS_ASSIGN_REFEREES'),'jl-Referees');
		JLToolBarHelper::help('screen.joomleague',true);

		parent::display($tpl);
	}


	/**
	 * Add the page title and toolbar.
	 */
	protected function addToolbar()
	{
		JLToolBarHelper::title(JText::_('COM_JOOMLEAGUE_ADMIN_PREF_TITLE'),'jl-Referees');
		JLToolBarHelper::apply('projectreferees.saveshort','COM_JOOMLEAGUE_ADMIN_PREF_APPLY');
		JLToolBarHelper::custom('projectreferees.assign','upload.png','upload_f2.png','COM_JOOMLEAGUE_ADMIN_PREF_ASSIGN',false);
		JLToolBarHelper::custom('projectreferees.unassign','cancel.png','cancel_f2.png','COM_JOOMLEAGUE_ADMIN_PREF_UNASSIGN',false);
		JLToolBarHelper::divider();
		JLToolBarHelper::help('screen.joomleague',true);
	}
}
