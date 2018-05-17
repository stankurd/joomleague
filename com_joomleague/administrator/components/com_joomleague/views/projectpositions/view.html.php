<?php
/**
 * Joomleague
 *
 * @copyright	Copyright (C) 2006-2015 joomleague.at. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @link		http://www.joomleague.at
 */
use Joomla\CMS\Factory;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;

defined('_JEXEC') or die;
HTMLHelper::_('jquery.framework');
HTMLHelper::_('behavior.core');

/**
 * HTML View class
 */
class JoomleagueViewProjectpositions extends JLGView
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

		parent::display($tpl);
	}


	function _displayDefault($tpl)
	{
		$app = Factory::getApplication();
		$input = $app->input;
		$option = $input->getCmd('option');
		$uri = Uri::getInstance();
		$project_id = $app->getUserState($option.'project');

		$this->items = $this->get('Items');
		$this->pagination = $this->get('Pagination');
		$this->state = $this->get('State');

		$mdlProject = BaseDatabaseModel::getInstance('project','JoomleagueModel');
		$project 	= $mdlProject->getItem($project_id);
		
		$filter_order = $app->getUserStateFromRequest($this->get('context').'.filter_order','filter_order','a.id','cmd');
		$filter_order_Dir = $app->getUserStateFromRequest($this->get('context').'.filter_order_Dir','filter_order_Dir','','word');
		
		// table ordering
		$lists['order_Dir'] = $filter_order_Dir;
		$lists['order'] = $filter_order;
		$this->lists = $lists;
		
		$this->project = $project;

		$this->addToolbar();
		parent::display($tpl);
	}


	function _displayEditlist($tpl)
	{
		$app = Factory::getApplication();
		$uri = Uri::getInstance();
		$model = $this->getModel();
		$project_id = $app->getUserState('com_joomleagueproject');

		$mdlProject = BaseDatabaseModel::getInstance('project','JoomleagueModel');
		$project = $mdlProject->getItem($project_id);

		$baseurl = Uri::root();
		$document = Factory::getDocument();
		$document->addScript($baseurl . 'administrator/components/com_joomleague/assets/js/multiselect.js');

		// build the html select list for project assigned positions
		$ress = array();
		$res1 = array();
		$notusedpositions = array();

		if($ress = $model->getProjectPositions())
		{ // select all already
		  // assigned positions to the
		  // project
			foreach($ress as $res)
			{
				$project_positionslist[] = HTMLHelper::_('select.option',$res->value,Text::_($res->text));
			}
			$lists['project_positions'] = HTMLHelper::_('select.genericlist',$project_positionslist,'project_positionslist[]',
					' style="width:250px; height:250px;" class="inputbox" multiple="true" size="' . max(15,count($ress)) . '"','value','text',false,
					'multiselect_to');
		}
		else
		{
			$lists['project_positions'] = '<select name="project_positionslist[]" id="multiselect_to" style="width:250px; height:250px;" class="inputbox" multiple="true" size="10"></select>';
		}

		if($ress1 = $model->getSubPositions($project->sports_type_id))
		{
			if($ress)
			{
				foreach($ress1 as $res1)
				{
					if(! in_array($res1,$ress))
					{
						$res1->text = Text::_($res1->text);
						$notusedpositions[] = $res1;
					}
				}
			}
			else
			{
				foreach($ress1 as $res1)
				{
					$res1->text = Text::_($res1->text);
					$notusedpositions[] = $res1;
				}
			}
		}
		else
		{
			$app->enqueueMessage(Text::_('COM_JOOMLEAGUE_ADMIN_P_POSITION_ASSIGN_POSITIONS_FIRST'),'notice');
		}

		// build the html select list for positions
		if(count($notusedpositions) > 0)
		{
			$lists['positions'] = HTMLHelper::_('select.genericlist',$notusedpositions,'positionslist[]',
					' style="width:250px; height:250px;" class="inputbox" multiple="true" size="' . min(15,count($notusedpositions)) . '"','value',
					'text',false,'multiselect');
		}
		else
		{
			$lists['positions'] = '<select name="positionslist[]" id="multiselect" style="width:250px; height:250px;" class="inputbox" multiple="true" size="10"></select>';
		}
		unset($ress);
		unset($ress1);
		unset($notusedpositions);

		$this->user = Factory::getUser();
		$this->lists = $lists;
		$this->project = $project;
		$this->request_url = $uri->toString();

		$this->addToolbar_Editlist();
		parent::display($tpl);
	}


	/**
	 * Add the page title and toolbar.
	 */
	protected function addToolbar()
	{
		JLToolBarHelper::title(Text::_('COM_JOOMLEAGUE_ADMIN_P_POSITION_TITLE'),'jl-Positions');
		JLToolBarHelper::custom('projectpositions.assign','upload.png','upload_f2.png','COM_JOOMLEAGUE_ADMIN_P_POSITION_BUTTON_UN_ASSIGN',false);
		JLToolBarHelper::divider();
		JLToolBarHelper::help('screen.joomleague',true);
	}


	/**
	 * Add the page title and toolbar.
	 */
	protected function addToolbar_Editlist()
	{
		JLToolBarHelper::title(Text::_('COM_JOOMLEAGUE_ADMIN_P_POSITION_EDIT_TITLE'),'jl-Positions');
		//JLToolBarHelper::save('projectpositions.save_positionslist');
		ToolbarHelper::saveGroup(
		    [
		        ['save', 'projectpositions.save_positionslist'],
		    ],
		    'btn-success'
		    );
		JLToolBarHelper::cancel('projectpositions.cancel','COM_JOOMLEAGUE_GLOBAL_CLOSE');
		JLToolBarHelper::help('screen.joomleague',true);
	}
}
