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
class JoomleagueViewRounds extends JLGView
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
		else if($this->getLayout() == 'populate')
		{
			$this->_displayPopulate($tpl);
			return;
		}
		parent::display($tpl);
	}


	function _displayDefault($tpl)
	{
		$app = JFactory::getApplication();
		$jinput = $app->input;
		$option = $jinput->getCmd('option');
		$uri = JUri::getInstance();
		$url = $uri->toString();
		$project_id = $app->getUserState($option.'project');
		$params = JComponentHelper::getParams($option);
		
		$this->items = $this->get('Items');
		$this->pagination = $this->get('Pagination');
		$this->state = $this->get('State');

		$model = $this->getModel();

		$mdlProject = JModelLegacy::getInstance('project','JoomleagueModel');
		$project 	= $mdlProject->getItem($project_id);

		$projectteams 	= $this->get('projectteams');
		$massadd 		= $jinput->get('massadd');

		// build the html options for divisions
		$divisions[] = JHtmlSelect::option('0',JText::_('COM_JOOMLEAGUE_GLOBAL_SELECT_DIVISION'));
		$mdlDivisions = JModelLegacy::getInstance('divisions','JoomLeagueModel');
		if($res = $mdlDivisions->getDivisions($project->id))
		{
			$divisions = array_merge($divisions,$res);
		}
		$lists['divisions'] = $divisions;

		// Mass Round Add options
		$massAddType = 0;
		$options = array();
		$options[] = JHtml::_('select.option',$massAddType ++,JText::_("Number of rounds with start date and interval"));

		// Add additional options to select
		$path = JPath::clean(JPATH_ROOT.'/media/com_joomleague/database/round_templates');
		if(JFolder::exists($path))
		{
			$files = JFolder::files($path,'\.csv',false);
			foreach($files as $file)
			{
				$filename = str_replace('_',' ',JFile::stripExt($file));
				$options[] = JHtml::_('select.option',$file,JText::sprintf('COM_JOOMLEAGUE_ADMIN_ROUNDS_MASSADD_TYPE',$filename));
			}
		}
		$lists['roundscheduling'] = JHtml::_('select.genericlist',$options,'mass_add_method',
				'style="width:500px;" onchange="updateAddRoundMethod(this)"','value','text');

		$filter_order = $app->getUserStateFromRequest($this->get('context').'.filter_order','filter_order','a.match_number','cmd');
		$filter_order_Dir = $app->getUserStateFromRequest($this->get('context').'.filter_order_Dir','filter_order_Dir','','word');
		
		// table ordering
		$lists['order_Dir'] = $filter_order_Dir;
		$lists['order'] = $filter_order;
		
		$this->massadd = $massadd;
		$this->countProjectTeams = count($projectteams);
		$this->lists = $lists;
		$this->project = $project;
		$this->request_url = $url;
		$this->params = $params;
		
		$populate = 0;
		$this->populate = $populate;

		JHtml::_('bootstrap.framework');
		$baseurl = JUri::root();
		$document = JFactory::getDocument();
		$document->addScript($baseurl.'media/com_joomleague/bootstrap-editable/js/bootstrap-editable.js');
		$document->addStyleSheet($baseurl.'media/com_joomleague/bootstrap-editable/css/bootstrap-editable.css');

		$this->addToolbar();
		parent::display($tpl);
	}


	function _displayPopulate($tpl)
	{
		jimport('joomla.filesystem.folder');
		jimport('joomla.filesystem.file');

		$document = JFactory::getDocument();
		$uri = JUri::getInstance();
		$url = $uri->toString();
		$model = $this->getModel();
		$app = JFactory::getApplication();
		$jinput = $app->input;

		$project_id = $app->getUserState('com_joomleagueproject');

		$mdlProject = JModelLegacy::getInstance('project','JoomleagueModel');
		$project = $mdlProject->getItem($project_id);

		$document->setTitle(JText::_('COM_JOOMLEAGUE_ADMIN_ROUNDS_POPULATE_TITLE'));

		$lists = array();
		// Populate options
		$iScheduleType = 0;
		$options = array();
		$options[] = JHtml::_('select.option',$iScheduleType ++,JText::_('COM_JOOMLEAGUE_ADMIN_ROUNDS_POPULATE_TYPE_SINGLE_ROUND_ROBIN'));
		$options[] = JHtml::_('select.option',$iScheduleType ++,JText::_('COM_JOOMLEAGUE_ADMIN_ROUNDS_POPULATE_TYPE_DOUBLE_ROUND_ROBIN'));
		$path = JPath::clean(JPATH_ROOT . '/media/com_joomleague/database/round_populate_templates');
		if(JFolder::exists($path))
		{
			$files = JFolder::files($path,'\.txt',false);
			foreach($files as $file)
			{
				$filename = strtoupper(JFile::stripExt($file));
				$options[] = JHtml::_('select.option',$file,JText::_('COM_JOOMLEAGUE_ADMIN_ROUNDS_POPULATE_TYPE_' . $filename));
			}
		}
		$lists['scheduling'] = JHtml::_('select.genericlist',$options,'scheduling','','value','text');

		$teams = $this->get('projectteams');
		$options = array();
		foreach ($teams as $t) {
			$options[] = JHtml::_('select.option', $t->projectteam_id, $t->text);
		}
		$lists['teamsorder'] = JHtml::_('select.genericlist', $options, 'teamsorder[]', 'multiple="multiple" size="20"');
		
		
		$this->project = $project;
		$this->request_url = $url;
		$this->lists = $lists;

		$this->addToolbar_Populate();
		parent::display($tpl);
	}


	/**
	 * Add the page title and toolbar.
	 */
	protected function addToolbar()
	{
		// Set toolbar items for the page
		JToolBarHelper::title(JText::_('COM_JOOMLEAGUE_ADMIN_ROUNDS_TITLE'),'jl-Matchdays');

		if(!$this->massadd)
		{
			JLToolBarHelper::addNew('rounds.quickAdd');
			JLToolBarHelper::apply('rounds.saveshort');
			JToolBarHelper::divider();
			JLToolBarHelper::custom('rounds.massadd','new.png','new_f2.png','COM_JOOMLEAGUE_ADMIN_ROUNDS_MASSADD_BUTTON',false);
			$teams = $this->get('projectteams');
			if($teams && count($teams) > 0)
			{
				JLToolBarHelper::addNew('rounds.populate','COM_JOOMLEAGUE_ADMIN_ROUNDS_POPULATE_BUTTON',false);
			}
			JToolBarHelper::divider();
			JLToolBarHelper::deleteList(JText::_('COM_JOOMLEAGUE_ADMIN_ROUNDS_DELETE_WARNING'),'rounds.deletematches',
					'COM_JOOMLEAGUE_ADMIN_ROUNDS_MASSDEL_BUTTON');
			JLToolBarHelper::deleteList(JText::_('COM_JOOMLEAGUE_ADMIN_ROUNDS_DELETE_WARNING'),'rounds.remove');
			JToolBarHelper::divider();
		}
		else
		{
			JLToolBarHelper::custom('rounds.cancel','cancel.png','cancel_f2.png','COM_JOOMLEAGUE_ADMIN_ROUNDS_MASSADD_CANCEL',false);
		}
		JToolBarHelper::help('screen.joomleague',true);
	}


	/**
	 * Add the page title and toolbar.
	 */
	protected function addToolbar_Populate()
	{
		JToolBarHelper::title(JText::_('COM_JOOMLEAGUE_ADMIN_ROUNDS_POPULATE_TITLE'));
		JLToolBarHelper::apply('rounds.startpopulate');
		JToolBarHelper::back();
	}
}
