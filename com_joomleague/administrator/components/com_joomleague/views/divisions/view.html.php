<?php
/**
 * Joomleague
 *
 * @copyright	Copyright (C) 2006-2015 joomleague.at. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @link		http://www.joomleague.at
 */
use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;
use Joomla\CMS\Uri\Uri;

defined('_JEXEC') or die;


/**
 * HTML View class
 */
class JoomleagueViewDivisions extends JLGView
{
	protected $items;
	protected $pagination;
	protected $state;

	public function display($tpl = null)
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
		$project = $mdlProject->getItem($project_id);

		// state filter
		$lists = array();
		$lists['state'] = JoomleagueHelper::stateOptions($this->state->get('filter.state'));

		$this->user = Factory::getUser();
		$this->lists = $lists;
		$this->project = $project;

		$this->filterForm = $this->get('FilterForm');
		$this->activeFilters = $this->get('ActiveFilters');

		$this->addToolbar();
		parent::display($tpl);
	}


	/**
	 * Add the page title and toolbar.
	 */
	protected function addToolbar()
	{
		JLToolBarHelper::title(JText::_('COM_JOOMLEAGUE_ADMIN_DIVS_TITLE'),'jl-divisions');
		JLToolBarHelper::addNew('division.add');
		JLToolBarHelper::deleteList(JText::_('COM_JOOMLEAGUE_ADMIN_DIVISIONS_DELETE_WARNING'),'divisions.remove');
		JLToolBarHelper::divider();
		JLToolBarHelper::help('screen.joomleague',true);
	}
}
