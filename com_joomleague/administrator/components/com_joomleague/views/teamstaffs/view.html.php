<?php
/**
 * Joomleague
 *
 * @copyright	Copyright (C) 2006-2015 joomleague.at. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @link		http://www.joomleague.at
 * @author		Kurt Norgaz
 */
use Joomla\CMS\Factory;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;

defined('_JEXEC') or die;


/**
 * HTML View class
 */
class JoomleagueViewTeamStaffs extends JLGView
{
	protected $items;
	protected $pagination;
	protected $state;

	public function display($tpl = null)
	{
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
		$document = Factory::getDocument();
		$option = $jinput->getCmd('option');
		$uri = Uri::getInstance();
		$baseurl = Uri::root();

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
			$team_id = $app->getUserState($option.'team');
		}

		$project_team_id = $jinput->get('project_team_id');
		if($project_team_id)
		{
			$project_team_id = $project_team_id;
			$app->setUserState('com_joomleagueproject_team_id',$project_team_id);
		}
		else
		{
			$project_team_id = $app->getUserState($option.'project_team_id');
		}

		// css
		$document->addStyleSheet($baseurl.'administrator/components/com_joomleague/assets/css/Autocompleter.css');

		$this->items = $this->get('Items');
		$this->pagination = $this->get('Pagination');
		$this->state = $this->get('State');

		$mdlProject = BaseDatabaseModel::getInstance('project','JoomleagueModel');
		$project = $mdlProject->getItem($project_id);

		$mdlProjectteam = BaseDatabaseModel::getInstance('projectteam','JoomleagueModel');
		$projectteam = $mdlProjectteam->getItem($project_team_id);

		$model = $this->getModel();

		// build the html options for position
		$position_id[] = HTMLHelper::_('select.option','0',Text::_('COM_JOOMLEAGUE_GLOBAL_SELECT_FUNCTION'));
		if($res = $model->getPositions())
		{
			$position_id = array_merge($position_id,$res);
		}
		$lists['project_position_id'] = $position_id;
		unset($position_id);

		//$this->user = Factory::getUser();
		$this->lists = $lists;
		$this->project = $project;
		$this->projectteam = $projectteam;
		$this->request_url = $uri->toString();

		$this->filterForm = $this->get('FilterForm');
		$this->activeFilters = $this->get('ActiveFilters');

		$this->addToolbar();
		parent::display($tpl);
	}


	/**
	 *	Assign Teamstaff
	 */
	function _displayAssignPlayers($tpl=null)
	{
		$app 	= Factory::getApplication();
		$jinput = $app->input;
		$option = $jinput->getCmd('option');
		$params = ComponentHelper::getParams($option);
		$uri 	= Uri::getInstance();

		$project_id = $app->getUserState($option.'project');

		$mdlProject = BaseDatabaseModel::getInstance('project','JoomLeagueModel');
		$mdlPerson = BaseDatabaseModel::getInstance('persons','JoomLeagueModel');
		$mdlTeamstaffs  = BaseDatabaseModel::getInstance('teamstaffs','JoomLeagueModel');

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
		
		$this->filterForm = $this->get('FilterForm');
		$this->activeFilters = $this->get('ActiveFilters');
		ToolbarHelper::saveGroup(
		    [
		        ['apply', 'teamstaffs.saveassigned','COM_JOOMLEAGUE_ADMIN_PERSONS_SAVE_SELECTED'],
		    ],
		    'btn-success'
		    );
		JLToolbarHelper::back('COM_JOOMLEAGUE_ADMIN_PERSONS_BACK','index.php?option=com_joomleague&view=teamstaffs');
		JLToolbarHelper::title(Text::_('COM_JOOMLEAGUE_ADMIN_PERSONS_ASSIGN_STAFF'),'generic.png');
		JLToolbarHelper::help('screen.joomleague',true);

		parent::display($tpl);
	}


	/**
	 * Add the page title and toolbar.
	 */
	protected function addToolbar()
	{
		JLToolbarHelper::title(Text::_('COM_JOOMLEAGUE_ADMIN_TSTAFFS_TITLE'));
		JLToolBarHelper::publishList('teamstaffs.publish');
		JLToolBarHelper::unpublishList('teamstaffs.unpublish');
		ToolbarHelper::saveGroup(
		    [
		        ['apply', 'teamstaffs.saveshort','COM_JOOMLEAGUE_ADMIN_TSTAFFS_APPLY'],
		    ],
		    'btn-success'
		    );
		JLToolbarHelper::divider();
		JLToolBarHelper::custom('teamstaffs.assign','upload.png','upload_f2.png','COM_JOOMLEAGUE_ADMIN_TSTAFFS_ASSIGN',false);
		JLToolBarHelper::custom('teamstaffs.unassign','cancel.png','cancel_f2.png','COM_JOOMLEAGUE_ADMIN_TSTAFFS_UNASSIGN',false);
		JLToolbarHelper::divider();
		JLToolbarHelper::back('COM_JOOMLEAGUE_ADMIN_TSTAFFS_BACK','index.php?option=com_joomleague&view=projectteams');
		JLToolbarHelper::divider();
		JLToolbarHelper::help('screen.joomleague',true);
	}
}
