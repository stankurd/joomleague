<?php
/**
 * Joomleague
 *
 * @copyright	Copyright (C) 2006-2015 joomleague.at. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @link		http://www.joomleague.at
 */
defined('_JEXEC') or die;


/**
 * HTML View class
 */
class JoomleagueViewTeamplayers extends JLGView
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


	function _displayDefault($tpl)
	{
		$app = JFactory::getApplication();
		$jinput = $app->input;
		$option = $jinput->getCmd('option');
		$document = JFactory::getDocument();
		$baseurl = JUri::root();
		$uri = JUri::getInstance();

		$pid = $jinput->get('pid');
		if($pid)
		{
			$project_id = $pid;
		}
		else
		{
			$project_id = $app->getUserState($option.'project');
		}

		$team_id = $jinput->get('team_id');
		if($team_id)
		{
			$team_id = $team_id;
			$app->setUserState('com_joomleagueteam',$team_id);
		}
		else
		{
			$team_id = $app->getUserState($option . 'team');
		}

		$project_team_id = $jinput->get('project_team_id');
		if($project_team_id)
		{
			$project_team_id = $project_team_id;
			$app->setUserState('com_joomleagueproject_team_id',$project_team_id);
		}
		else
		{
			$project_team_id = $app->getUserState($option . 'project_team_id');
		}

		$this->items = $this->get('Items');
		$this->pagination = $this->get('Pagination');
		$this->state = $this->get('State');

		$mdlProject = JModelLegacy::getInstance('project','JoomleagueModel');
		$project = $mdlProject->getItem($project_id);

		$mdlProjectteam = JModelLegacy::getInstance('projectteam','JoomleagueModel');
		$projectteam = $mdlProjectteam->getItem($project_team_id);

		// build the html options for position
		$position_id[] = JHtml::_('select.option','0',JText::_('COM_JOOMLEAGUE_GLOBAL_SELECT_POSITION'));
		if($res = $this->get('positions'))
		{
			$position_id = array_merge($position_id,$res);
		}
		$lists['project_position_id'] = $position_id;
		unset($position_id);

		$mdlProject = JModelLegacy::getInstance('project','JoomleagueModel');
		$project = $mdlProject->getItem($project_id);

		$this->user = JFactory::getUser();
		$this->lists = $lists;
		$this->project = $project;
		$this->projectteam = $projectteam;

		JHtml::_('bootstrap.framework');

		$baseurl = JUri::root();
		$document->addStyleSheet($baseurl.'administrator/components/com_joomleague/assets/css/Autocompleter.css');
		$document->addScript($baseurl.'media/com_joomleague/bootstrap-editable/js/bootstrap-editable.js');
		$document->addStyleSheet($baseurl.'media/com_joomleague/bootstrap-editable/css/bootstrap-editable.css');

		$this->filterForm = $this->get('FilterForm');
		$this->activeFilters = $this->get('ActiveFilters');

		$this->addToolbar();
		parent::display($tpl);
	}


	/**
	 * Assign Teamplayers
	 */
	function _displayAssignPlayers($tpl = null)
	{
		$app 	= JFactory::getApplication();
		$jinput = $app->input;
		$option = $jinput->getCmd('option');
		$params = JComponentHelper::getParams($option);
		$uri 	= JUri::getInstance();

		$project_id = $app->getUserState($option.'project');

		$mdlProject 	= JModelLegacy::getInstance('project','JoomLeagueModel');
		$mdlPerson  	= JModelLegacy::getInstance('persons','JoomLeagueModel');
		$mdlTeamplayers = JModelLegacy::getInstance('teamplayers','JoomLeagueModel');

		$project_name = $mdlProject->getProjectName($project_id);
		$project_team_id = $app->getUserState($option.'project_team_id');
		$team_name = $mdlPerson->getProjectTeamName($project_team_id);

		$model = $this->getModel();
		$model->setState('filter.assign',true);
		
		$this->items = $this->get('Items');
		$this->pagination = $this->get('Pagination');
		$this->state = $this->get('State');
		
		$this->prj_name = $project_name;
		$this->team_name = $team_name;
		$this->project_team_id = $project_team_id;
		$this->request_url = $uri->toString();
		$this->component_params = $params;
		
		$this->filterForm = $this->get('FilterForm');
		$this->activeFilters = $this->get('ActiveFilters');
		
		// save icon should be replaced by the apply
		JLToolBarHelper::apply('teamplayers.saveassigned','COM_JOOMLEAGUE_ADMIN_PERSONS_SAVE_SELECTED');
		JToolBarHelper::back('COM_JOOMLEAGUE_ADMIN_PERSONS_BACK','index.php?option=com_joomleague&view=teamplayers');
		JToolBarHelper::title(JText::_('COM_JOOMLEAGUE_ADMIN_PERSONS_ASSIGN_PLAYERS'),'generic.png');
		JToolBarHelper::help('screen.joomleague',true);

		parent::display($tpl);
	}


	/**
	 * Add the page title and toolbar.
	 */
	protected function addToolbar()
	{
		JToolBarHelper::title(JText::_('COM_JOOMLEAGUE_ADMIN_TPLAYERS_TITLE'));
		JLToolBarHelper::publishList('teamplayers.publish');
		JLToolBarHelper::unpublishList('teamplayers.unpublish');
		JLToolBarHelper::apply('teamplayers.saveshort','COM_JOOMLEAGUE_ADMIN_TPLAYERS_APPLY');
		JToolBarHelper::divider();
		JLToolBarHelper::custom('teamplayers.assign','upload.png','upload_f2.png','COM_JOOMLEAGUE_ADMIN_TPLAYERS_ASSIGN',false);
		JLToolBarHelper::custom('teamplayers.unassign','cancel.png','cancel_f2.png','COM_JOOMLEAGUE_ADMIN_TPLAYERS_UNASSIGN',false);
		JToolBarHelper::divider();
		JToolBarHelper::back('COM_JOOMLEAGUE_ADMIN_TPLAYERS_BACK','index.php?option=com_joomleague&view=projectteams');
		JToolBarHelper::divider();
		JToolBarHelper::help('screen.joomleague',true);
	}
}
