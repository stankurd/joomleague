<?php
/**
 * Joomleague
 *
 * @copyright	Copyright (C) 2006-2015 joomleague.at. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @link		http://www.joomleague.at
 */
use Joomla\CMS\Factory;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;

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
		$app = Factory::getApplication();
		$jinput = $app->input;
		$option = $jinput->getCmd('option');
		$document = Factory::getDocument();
		$baseurl = Uri::root();
		$uri = Uri::getInstance();

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

		$mdlProject = BaseDatabaseModel::getInstance('project','JoomleagueModel');
		$project = $mdlProject->getItem($project_id);

		$mdlProjectteam = BaseDatabaseModel::getInstance('projectteam','JoomleagueModel');
		$projectteam = $mdlProjectteam->getItem($project_team_id);

		// build the html options for position
		$position_id[] = HTMLHelper::_('select.option','0',Text::_('COM_JOOMLEAGUE_GLOBAL_SELECT_POSITION'));
		if($res = $this->get('positions'))
		{
			$position_id = array_merge($position_id,$res);
		}
		$lists['project_position_id'] = $position_id;
		unset($position_id);

		$mdlProject = BaseDatabaseModel::getInstance('project','JoomleagueModel');
		$project = $mdlProject->getItem($project_id);

		$this->user = Factory::getUser();
		$this->lists = $lists;
		$this->project = $project;
		$this->projectteam = $projectteam;

		HTMLHelper::_('bootstrap.framework');
		HTMLHelper::_('bootstrap.tooltip');
		
		$baseurl = Uri::root();
		$document->addStyleSheet($baseurl.'administrator/components/com_joomleague/assets/css/Autocompleter.css');
		$document->addScript($baseurl.'media/com_joomleague/bootstrap-editable/js/bootstrap-editable.js');
		$document->addStyleSheet($baseurl.'media/com_joomleague/bootstrap-editable/css/bootstrap-editable.css');
		//$document->addStyleSheet($baseurl.'media/com_joomleague/bootstrap-editable/css/bootstrap-extended.css');
		//$document->addScript($baseurl.'media/com_joomleague/bootstrap-editable/js/bootstrap-tooltip-extended.js');
		
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
		$app 	= Factory::getApplication();
		$jinput = $app->input;
		$option = $jinput->getCmd('option');
		$params = ComponentHelper::getParams($option);
		$uri 	= Uri::getInstance();

		$project_id = $app->getUserState($option.'project');

		$mdlProject 	= BaseDatabaseModel::getInstance('project','JoomLeagueModel');
		$mdlPerson  	= BaseDatabaseModel::getInstance('persons','JoomLeagueModel');
		$mdlTeamplayers = BaseDatabaseModel::getInstance('teamplayers','JoomLeagueModel');

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
		JLToolbarHelper::back('COM_JOOMLEAGUE_ADMIN_PERSONS_BACK','index.php?option=com_joomleague&view=teamplayers');
		JLToolbarHelper::title(Text::_('COM_JOOMLEAGUE_ADMIN_PERSONS_ASSIGN_PLAYERS'),'generic.png');
		JLToolbarHelper::help('screen.joomleague',true);

		parent::display($tpl);
	}


	/**
	 * Add the page title and toolbar.
	 */
	protected function addToolbar()
	{
		JLToolbarHelper::title(Text::_('COM_JOOMLEAGUE_ADMIN_TPLAYERS_TITLE'));
		JLToolBarHelper::publishList('teamplayers.publish');
		JLToolBarHelper::unpublishList('teamplayers.unpublish');
		JLToolBarHelper::apply('teamplayers.saveshort','COM_JOOMLEAGUE_ADMIN_TPLAYERS_APPLY');
		JLToolbarHelper::divider();
		JLToolBarHelper::custom('teamplayers.assign','upload.png','upload_f2.png','COM_JOOMLEAGUE_ADMIN_TPLAYERS_ASSIGN',false);
		JLToolBarHelper::custom('teamplayers.unassign','cancel.png','cancel_f2.png','COM_JOOMLEAGUE_ADMIN_TPLAYERS_UNASSIGN',false);
		JLToolbarHelper::divider();
		JLToolbarHelper::back('COM_JOOMLEAGUE_ADMIN_TPLAYERS_BACK','index.php?option=com_joomleague&view=projectteams');
		JLToolbarHelper::divider();
		JLToolbarHelper::help('screen.joomleague',true);
	}
}
