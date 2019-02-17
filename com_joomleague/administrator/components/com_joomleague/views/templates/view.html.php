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
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\Uri\Uri;
use Joomla\String\StringHelper;


defined('_JEXEC') or die;


/**
 * HTML View class
 *
 * @author	Marco Vaninetti <martizva@tiscali.it>
 */
class JoomleagueViewTemplates extends JLGView
{
	public function display($tpl = null)
	{
		$app = Factory::getApplication();
		$option = $app->input->get('option');
		$document = Factory::getDocument();
		$uri = Uri::getInstance();
		$templates = $this->get('Data');
		$total = $this->get('Total');
		$pagination = $this->get('Pagination');
		$mdlProject = new JoomleagueModelProject();
		$project_id = $app->getUserState($option.'project');
		$project 	= $mdlProject->getItem($project_id);
		
		$model=$this->getModel();
		if ($project->master_template)
		{
			$model->set('_getALL',1);
			$allMasterTemplates=$this->get('MasterTemplatesList');
			$model->set('_getALL',0);
			$masterTemplates=$this->get('MasterTemplatesList');
			$importlist=array();
			$importlist[]=HTMLHelper::_('select.option',0,Text::_('COM_JOOMLEAGUE_ADMIN_TEMPLATES_SELECT_FROM_MASTER'));
			$importlist=array_merge($importlist,$masterTemplates);
			$lists['mastertemplates']=HTMLHelper::_('select.genericlist',$importlist,'templateid');
			$master=$this->get('MasterName');
			$this->master = $master;
			$templates=array_merge($templates,$allMasterTemplates);
		}

		$filter_state		= $app->getUserStateFromRequest($option.'tmpl_filter_state',		'filter_state',		'',				'word');
		$filter_order		= $app->getUserStateFromRequest($option.'tmpl_filter_order',		'filter_order',		'tmpl.template','cmd');
		$filter_order_Dir	= $app->getUserStateFromRequest($option.'tmpl_filter_order_Dir',	'filter_order_Dir',	'',				'word');
		$search				= $app->getUserStateFromRequest($option.'tmpl_search',			'search',			'',				'string');
		$search_mode		= $app->getUserStateFromRequest($option.'tmpl_search_mode',		'search_mode',		'',				'string');
		$search				= StringHelper::strtolower($search);

		// state filter
		$lists['state'] = JoomleagueHelper::stateOptions($filter_state,true,true,false,false);

		// table ordering
		$lists['order_Dir']=$filter_order_Dir;
		$lists['order']=$filter_order;

		// search filter
		$lists['search']=$search;
		$lists['search_mode']=$search_mode;

		$this->user=Factory::getUser();
		$this->lists=$lists;
		$this->templates=$templates;
		$this->project=$project;
		$this->pagination=$pagination;
		$this->request_url=$uri->toString();
		
		$this->addToolbar();			
		parent::display($tpl);
	}
	
	
	/**
	* Add the page title and toolbar.
	*/
	protected function addToolbar()
	{
		// Set toolbar items for the page
		JLToolbarHelper::title(Text::_('COM_JOOMLEAGUE_ADMIN_TEMPLATES_TITLE'),'jl-FrontendSettings');
		/*ToolbarHelper::saveGroup(
		    [
		        ['save', 'template.save'],
		    ],
		    'btn-success'
		    );*/
		JLToolBarHelper::save('template.save');
		if ($this->project->master_template)
		{
			JLToolBarHelper::deleteList('COM_JOOMLEAGUE_GLOBAL_CONFIRM_DELETE','template.remove');
		}
		else
		{
			JLToolBarHelper::custom('template.reset','remove','restore',Text::_('COM_JOOMLEAGUE_GLOBAL_RESET'));
		}
		JLToolBarHelper::divider();
		JLToolBarHelper::help('screen.joomleague',true);
	}	
}
