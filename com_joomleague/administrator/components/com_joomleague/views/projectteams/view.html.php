<?php
/**
 * Joomleague
 *
 * @copyright	Copyright (C) 2006-2015 joomleague.at. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @link		http://www.joomleague.at
 */
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Uri\Uri;
use Joomla\Utilities\ArrayHelper;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;

defined('_JEXEC') or die;


/**
 * HTML View class
 */
class JoomleagueViewProjectteams extends JLGView
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

		if($this->getLayout() == 'changeteams')
		{
			$this->_displayChangeTeams($tpl);
			return;
		}

		if($this->getLayout() == 'default')
		{
			$this->_displayDefault($tpl);
			return;
		}

		if($this->getLayout() == 'copy')
		{
			$this->_displayCopy($tpl);
			return;
		}

		parent::display($tpl);
	}


	/**
	 *
	 */
	function _displayChangeTeams($tpl)
	{
		$app = Factory::getApplication();
		$input = $app->input;
		$option = $input->getCmd('option');
		$project_id = $app->getUserState($option.'project');

		$this->items = $this->get('Items');
		$this->pagination = $this->get('Pagination');
		$this->state = $this->get('State');

		$model = $this->getModel(); // model Projectteams

		// build the html select list for all teams
		$all_Teams = array();
		$all_teams[] = HTMLHelper::_('select.option','0',JText::_('COM_JOOMLEAGUE_GLOBAL_SELECT_TEAM'));

		if($allTeams = $model->getAllTeams($project_id))
		{
			$all_teams = array_merge($all_teams,$allTeams);
		}
		$lists['all_teams'] = $all_teams;
		unset($all_teams);

		$this->lists = $lists;

		// Toolbar for ChangeTeams
		JLToolbarHelper::title(JText::_('COM_JOOMLEAGUE_ADMIN_PROJECTTEAMS_CHANGEASSIGN_TEAMS'),'');
		JLToolBarHelper::custom('projectteam.storechangeteams','move.png','move_f2.png','COM_JOOMLEAGUE_ADMIN_PROJECTTEAMS_BUTTON_STORE_CHANGE_TEAMS',false);
		JLToolbarHelper::back();

		parent::display($tpl);
	}


	/**
	 *
	 */
	function _displayEditlist($tpl)
	{
		$app = Factory::getApplication();
		$input = $app->input;
		$option = $input->getCmd('option');
		$project_id = $app->getUserState($option.'project');

		$db = Factory::getDbo();
		$uri = Uri::getInstance();
		$baseurl = Uri::root();

		$document = Factory::getDocument();
		$document->addScript($baseurl.'administrator/components/com_joomleague/assets/js/multiselect.js');

		$this->items = $this->get('Items');
		$this->pagination = $this->get('Pagination');
		$this->state = $this->get('State');

		$model = $this->getModel(); // model Projectteams

		$mdlProject = BaseDataBaseModel::getInstance('project','JoomleagueModel');
		$project 	= $mdlProject->getItem($project_id);

		// build the html select list for project assigned teams
		$ress = array(); // Teams assigned
		$res1 = array();
		$notusedteams = array();

		if($ress = $model->getProjectTeams($project_id)) // all assigned teams
		{
			$teamslist = array();
			foreach($ress as $res)
			{
				if(empty($res1->info))
				{
					$project_teamslist[] = JHtmlSelect::option($res->value,$res->text);
				}
				else
				{
					$project_teamslist[] = JHtmlSelect::option($res->value,$res->text.' ('.$res->info.')');
				}
			}
			$lists['project_teams'] = JHtmlSelect::genericlist($project_teamslist,'project_teamslist[]',
					' style="width:250px; height:300px;" class="inputbox" multiple="true" size="'.min(30,count($ress)).'"','value','text',false,
					'multiselect_to');
		}
		else
		{
			$lists['project_teams'] = '<select name="project_teamslist[]" id="multiselect_to" style="width:250px; height:300px;" class="inputbox" multiple="true" size="10"></select>';
		}

		if($ress1 = $model->getTeams()) // All available teams
		{
			if($ress = $model->getProjectTeams($project_id)) // all assigned teams
			{
				foreach($ress1 as $res1)
				{
					$used = 0;
					foreach($ress as $res) // we're checking all assigned teams
					{
						if($res1->value == $res->value)
						{
							$used = 1;
						}
					}

					if($used == 0 && ! empty($res1->info))
					{
						$notusedteams[] = JHtmlSelect::option($res1->value,$res1->text.' ('.$res1->info.')');
					}
					elseif($used == 0 && empty($res1->info))
					{
						$notusedteams[] = JHtmlSelect::option($res1->value,$res1->text);
					}
				}
			}
			else
			{
				foreach($ress1 as $res1)
				{
					if(empty($res1->info))
					{
						$notusedteams[] = JHtmlSelect::option($res1->value,$res1->text);
					}
					else
					{
						$notusedteams[] = JHtmlSelect::option($res1->value,$res1->text.' ('.$res1->info.')');
					}
				}
			}
		}
		else
		{
			JError::raiseWarning('ERROR_CODE','<br />'.JText::_('COM_JOOMLEAGUE_ADMIN_PROJECTTEAMS_ADD_TEAM').'<br /><br />');
		}

		// build the html select list for teams
		if(count($notusedteams) > 0)
		{
			$lists['teams'] = JHtmlSelect::genericlist($notusedteams,'teamslist[]',
					' style="width:250px; height:300px;" class="inputbox" multiple="true" size="'.min(30,count($notusedteams)).'"','value','text',
					false,'multiselect');
		}
		else
		{
			$lists['teams'] = '<select name="teamslist[]" id="multiselect" style="width:250px; height:300px;" class="inputbox" multiple="true" size="10"></select>';
		}

		unset($res);
		unset($res1);
		unset($notusedteams);

		$this->user = Factory::getUser();
		$this->lists = $lists;
		$this->project = $project;
		$this->request_url = $uri->toString();

		$this->addToolbar_Editlist();
		parent::display($tpl);
	}


	/**
	 *
	 */
	function _displayDefault($tpl)
	{
		$app = Factory::getApplication();
		$input = $app->input;
		$document = Factory::getDocument();
		$option = $input->getCmd('option');
		$project_id = $app->getUserState($option.'project');

		$db = Factory::getDbo();
		$uri = Uri::getInstance();

		$this->items = $this->get('Items');
		$this->pagination = $this->get('Pagination');
		$this->state = $this->get('State');

		$model = $this->getModel();

		// build the html options for divisions
		$divisions = array();
		$divisions[] = JHtmlSelect::option('0',JText::_('COM_JOOMLEAGUE_GLOBAL_SELECT_DIVISION'),'value','text');
		$mdlDivisions = BaseDataBaseModel::getInstance('divisions','JoomleagueModel');
		if($res = $mdlDivisions->getDivisions($project_id))
		{
			$divisions = array_merge($divisions,$res);
		}
		$lists['divisions'] = HTMLHelper::_('select.genericList',$divisions,'filter_division','class="input-medium" onChange="this.form.submit();"',
				'value','text',$this->state->get('filter.division'));
		$this->divisions = $divisions;
		unset($divisions);

		$mdlProject = BaseDatabaseModel::getInstance('project','JoomleagueModel');
		$project 	= $mdlProject->getItem($project_id);

		$this->lists = $lists;
		$this->project = $project;

		HTMLHelper::_('bootstrap.framework');
		$baseurl = Uri::root();
		$document->addStyleSheet($baseurl.'administrator/components/com_joomleague/assets/css/Autocompleter.css');
		$document->addScript($baseurl.'media/com_joomleague/bootstrap-editable/js/bootstrap-editable.js');
		$document->addStyleSheet($baseurl.'media/com_joomleague/bootstrap-editable/css/bootstrap-editable.css');
		
		$this->filterForm = $this->get('FilterForm');
		$this->activeFilters = $this->get('ActiveFilters');

		$this->addToolbar();
		parent::display($tpl);
	}


	/**
	 *
	 */
	function _displayCopy($tpl)
	{
		$document = Factory::getDocument();
		$app = Factory::getApplication();
		$input = $app->input;
		$option = $input->getCmd('option');
		$project_id = $app->getUserState($option.'project');
		$uri = Uri::getInstance();
		$ptids = $input->get('cid',array(),'array');
		ArrayHelper::toInteger($ptids);

		$model = $this->getModel();

		$lists = array();

		// build the html select list for all Projects
		$ignoreId = $project_id;
		$options = JoomleagueHelper::getProjects($ignoreId);

		$lists['projects'] = HTMLHelper::_('select.genericlist',$options,'dest','','id','name');
		$this->ptids = $ptids;
		$this->lists = $lists;
		$this->request_url = $uri->toString();

		$this->addToolbar_Copy();
		parent::display($tpl);
	}


	/**
	 * Add the page title and toolbar.
	 */
	protected function addToolbar()
	{
		JLToolbarHelper::title(JText::_('COM_JOOMLEAGUE_ADMIN_PROJECTTEAMS_TITLE'));
		JLToolBarHelper::apply('projectteams.saveshort');
		JLToolBarHelper::custom('projectteams.changeteams','move.png','move_f2.png','COM_JOOMLEAGUE_ADMIN_PROJECTTEAMS_BUTTON_CHANGE_TEAMS',false);
		JLToolBarHelper::custom('projectteams.editlist','upload.png','upload_f2.png','COM_JOOMLEAGUE_ADMIN_PROJECTTEAMS_BUTTON_ASSIGN',false);

		// @todo: fix function
		/*
		 * JLToolBarHelper::custom('projectteams.copy','copy','copy','COM_JOOMLEAGUE_GLOBAL_COPY', true);
		 */
		JLToolbarHelper::divider();
		JLToolbarHelper::help('screen.joomleague',true);
	}


	/**
	 * Add the page title and toolbar.
	 */
	protected function addToolbar_Editlist()
	{
		JLToolbarHelper::title(JText::_('COM_JOOMLEAGUE_ADMIN_PROJECTTEAMS_ASSIGN'));
		JLToolBarHelper::save('projectteams.save_teamslist');
		JLToolBarHelper::cancel('projectteams.cancel','COM_JOOMLEAGUE_GLOBAL_CLOSE');
		JLToolbarHelper::help('screen.joomleague',true);
	}


	/**
	 * Add the page title and toolbar.
	 */
	protected function addToolbar_Copy()
	{
		JLToolbarHelper::title(JText::_('COM_JOOMLEAGUE_ADMIN_PROJECTTEAMS_COPY_DEST'),'Teams');
		JLToolBarHelper::apply('projectteam.copy');
		JLToolbarHelper::back();
	}
}
