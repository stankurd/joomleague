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
<<<<<<< HEAD
use Joomla\CMS\Filesystem\File;
use Joomla\CMS\Filesystem\Folder;
=======
>>>>>>> branch 'master' of https://github.com/stankurd/joomleague.git
use Joomla\CMS\Filesystem\Path;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;

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
		$app = Factory::getApplication();
		$jinput = $app->input;
		$option = $jinput->getCmd('option');
		$uri = Uri::getInstance();
		$url = $uri->toString();
		$project_id = $app->getUserState($option.'project');
		$params = ComponentHelper::getParams($option);
		
		$this->items = $this->get('Items');
		$this->pagination = $this->get('Pagination');
		$this->state = $this->get('State');

		$model = $this->getModel();

		$mdlProject = BaseDatabaseModel::getInstance('project','JoomleagueModel');
		$project 	= $mdlProject->getItem($project_id);

		$projectteams 	= $this->get('projectteams');
		$massadd 		= $jinput->get('massadd');

		// build the html options for divisions
		$divisions[] =  HTMLHelper::_('select.option','0',Text::_('COM_JOOMLEAGUE_GLOBAL_SELECT_DIVISION'));
		$mdlDivisions = BaseDatabaseModel::getInstance('divisions','JoomLeagueModel');
		if($res = $mdlDivisions->getDivisions($project->id))
		{
			$divisions = array_merge($divisions,$res);
		}
		$lists['divisions'] = $divisions;

		// Mass Round Add options
		$massAddType = 0;
		$options = array();
		$options[] = HTMLHelper::_('select.option',$massAddType ++,Text::_("Number of rounds with start date and interval"));

		// Add additional options to select
		$path = Path::clean(JPATH_ROOT.'/media/com_joomleague/database/round_templates');
<<<<<<< HEAD
		if(Folder::exists($path))
=======
		if(JFolder::exists($path))
>>>>>>> branch 'master' of https://github.com/stankurd/joomleague.git
		{
			$files = Folder::files($path,'\.csv',false);
			foreach($files as $file)
			{
				$filename = str_replace('_',' ',JFile::stripExt($file));
				$options[] = HTMLHelper::_('select.option',$file,Text::sprintf('COM_JOOMLEAGUE_ADMIN_ROUNDS_MASSADD_TYPE',$filename));
			}
		}
		$lists['roundscheduling'] = HTMLHelper::_('select.genericlist',$options,'mass_add_method',
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

		HTMLHelper::_('bootstrap.framework');
		$baseurl = Uri::root();
		$document = Factory::getDocument();
		$document->addScript($baseurl.'media/com_joomleague/bootstrap-editable/js/bootstrap-editable.js');
		$document->addStyleSheet($baseurl.'media/com_joomleague/bootstrap-editable/css/bootstrap-editable.css');

		$this->addToolbar();
		parent::display($tpl);
	}


	function _displayPopulate($tpl)
	{
		jimport('joomla.filesystem.folder');
		jimport('joomla.filesystem.file');

		$document = Factory::getDocument();
		$uri = Uri::getInstance();
		$url = $uri->toString();
		$model = $this->getModel();
		$app = Factory::getApplication();
		$jinput = $app->input;

		$project_id = $app->getUserState('com_joomleagueproject');

		$mdlProject = BaseDatabaseModel::getInstance('project','JoomleagueModel');
		$project = $mdlProject->getItem($project_id);

		$document->setTitle(Text::_('COM_JOOMLEAGUE_ADMIN_ROUNDS_POPULATE_TITLE'));

		$lists = array();
		// Populate options
		$iScheduleType = 0;
		$options = array();
		$options[] = HTMLHelper::_('select.option',$iScheduleType ++,Text::_('COM_JOOMLEAGUE_ADMIN_ROUNDS_POPULATE_TYPE_SINGLE_ROUND_ROBIN'));
		$options[] = HTMLHelper::_('select.option',$iScheduleType ++,Text::_('COM_JOOMLEAGUE_ADMIN_ROUNDS_POPULATE_TYPE_DOUBLE_ROUND_ROBIN'));
		$path = Path::clean(JPATH_ROOT . '/media/com_joomleague/database/round_populate_templates');
<<<<<<< HEAD
		if(Folder::exists($path))
=======
		if(JFolder::exists($path))
>>>>>>> branch 'master' of https://github.com/stankurd/joomleague.git
		{
			$files = Folder::files($path,'\.txt',false);
			foreach($files as $file)
			{
				$filename = strtoupper(File::stripExt($file));
				$options[] = HTMLHelper::_('select.option',$file,Text::_('COM_JOOMLEAGUE_ADMIN_ROUNDS_POPULATE_TYPE_' . $filename));
			}
		}
		$lists['scheduling'] = HTMLHelper::_('select.genericlist',$options,'scheduling','','value','text');

		$teams = $this->get('projectteams');
		$options = array();
		foreach ($teams as $t) {
			$options[] = HTMLHelper::_('select.option', $t->projectteam_id, $t->text);
		}
		$lists['teamsorder'] = HTMLHelper::_('select.genericlist', $options, 'teamsorder[]', 'multiple="multiple" size="20"');
		
		
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
		JLToolbarHelper::title(Text::_('COM_JOOMLEAGUE_ADMIN_ROUNDS_TITLE'),'jl-Matchdays');

		if(!$this->massadd)
		{
			JLToolBarHelper::addNew('rounds.quickAdd');
			ToolbarHelper::saveGroup(
			    [
			        ['apply', 'rounds.saveshort'],
			    ],
			    'btn-success'
			    );
<<<<<<< HEAD
=======
			//JLToolBarHelper::apply('rounds.saveshort');
>>>>>>> branch 'master' of https://github.com/stankurd/joomleague.git
			JLToolbarHelper::divider();
			JLToolBarHelper::custom('rounds.massadd','new.png','new_f2.png','COM_JOOMLEAGUE_ADMIN_ROUNDS_MASSADD_BUTTON',false);
			$teams = $this->get('projectteams');
			if($teams && count($teams) > 0)
			{
				JLToolBarHelper::addNew('rounds.populate','COM_JOOMLEAGUE_ADMIN_ROUNDS_POPULATE_BUTTON',false);
			}
			JLToolbarHelper::divider();
			JLToolBarHelper::deleteList(Text::_('COM_JOOMLEAGUE_ADMIN_ROUNDS_DELETE_WARNING'),'rounds.deletematches',
					'COM_JOOMLEAGUE_ADMIN_ROUNDS_MASSDEL_BUTTON');
			JLToolBarHelper::deleteList(Text::_('COM_JOOMLEAGUE_ADMIN_ROUNDS_DELETE_WARNING'),'rounds.remove');
			JLToolbarHelper::divider();
		}
		else
		{
			JLToolBarHelper::custom('rounds.cancel','cancel.png','cancel_f2.png','COM_JOOMLEAGUE_ADMIN_ROUNDS_MASSADD_CANCEL',false);
		}
		JLToolbarHelper::help('screen.joomleague',true);
	}


	/**
	 * Add the page title and toolbar.
	 */
	protected function addToolbar_Populate()
	{
		JLToolbarHelper::title(Text::_('COM_JOOMLEAGUE_ADMIN_ROUNDS_POPULATE_TITLE'));
<<<<<<< HEAD
		ToolbarHelper::saveGroup(
		    [
		        ['apply','rounds.startpopulate'],
=======
		//JLToolBarHelper::apply('rounds.startpopulate');
		ToolbarHelper::saveGroup(
		    [
		        ['apply', 'rounds.startpopulate'],
>>>>>>> branch 'master' of https://github.com/stankurd/joomleague.git
		    ],
		    'btn-success'
		    );
		JLToolbarHelper::back();
	}
}
