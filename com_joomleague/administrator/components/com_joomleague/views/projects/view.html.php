<?php
/**
 * Joomleague
 *
 * @copyright	Copyright (C) 2006-2015 joomleague.at. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @link		http://www.joomleague.at
 */
use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;

defined('_JEXEC') or die;


/**
 * HTML View class
 */
class JoomleagueViewProjects extends JLGView
{

	protected $items;
	protected $pagination;
	protected $state;
	public $filterForm;
	public $activeFilters;

	public function display($tpl = null)
	{
		if($this->getLayout() == 'progressCopy')
		{
			$this->_displayProgressCopy($tpl);
			return;
		}

		if($this->getLayout() == 'progressDelete')
		{
			$this->_displayProgressDelete($tpl);
			return;
		}

		$app = Factory::getApplication();
		$jinput = $app->input;
		$option = $jinput->getCmd('option');
		$uri = Uri::getInstance();
		$user = Factory::getUser();
		$lists = array();

		// Get data from the model
		$this->items = $this->get('Items');
		$this->pagination = $this->get('Pagination');
		$this->state = $this->get('State');

		// state filter
		$lists['state'] = JoomleagueHelper::stateOptions($this->state->get('filter.state'));

		// build the html select list for leagues
		$leagues[] = HTMLHelper::_('select.option','0',Text::_('COM_JOOMLEAGUE_ADMIN_PROJECTS_LEAGUES_FILTER'),'id','name');
		$mdlLeagues = BaseDatabaseModel::getInstance('Leagues','JoomleagueModel');
		$allLeagues = $mdlLeagues->getLeagues();
		$leagues = array_merge($leagues,$allLeagues);
		$lists['leagues'] = HTMLHelper::_('select.genericList',$leagues,'filter_league','class="input-medium" onChange="this.form.submit();"','id','name',
				$this->state->get('filter.league'));
		unset($leagues);

		// build the html select list for sportstypes
		$sportstypes[] = HTMLHelper::_('select.option','0',Text::_('COM_JOOMLEAGUE_ADMIN_PROJECTS_SPORTSTYPE_FILTER'),'id','name');
		$mdlSportsTypes = BaseDatabaseModel::getInstance('SportsTypes','JoomleagueModel');
		$allSportstypes = $mdlSportsTypes->getSportsTypes();
		$sportstypes = array_merge($sportstypes,$allSportstypes);
		$lists['sportstypes'] = HTMLHelper::_('select.genericList',$sportstypes,'filter_sportstype','class="input-medium" onChange="this.form.submit();"',
				'id','name',$this->state->get('filter.sportstype'));
		unset($sportstypes);

		// build the html select list for seasons
		$seasons[] = HTMLHelper::_('select.option','0',Text::_('COM_JOOMLEAGUE_ADMIN_PROJECTS_SEASON_FILTER'),'id','name');
		if($res = $this->get('Seasons'))
		{
			$seasons = array_merge($seasons,$res);
		}
		$lists['seasons'] = HTMLHelper::_('select.genericList',$seasons,'filter_season','class="input-medium" onChange="this.form.submit();"','id','name',
				$this->state->get('filter.season'));
		unset($seasons);

		$this->lists = $lists;

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
		// Set toolbar items for the page
		JLToolBarHelper::title(Text::_('COM_JOOMLEAGUE_ADMIN_PROJECTS_TITLE'),'jl-ProjectSettings');
		JLToolBarHelper::addNew('project.add');
		JLToolBarHelper::custom('projects.copy','copy.png','copy_f2.png','COM_JOOMLEAGUE_GLOBAL_COPY',false);
		JLToolBarHelper::divider();
		JLToolBarHelper::publishList('projects.publish');
		JLToolBarHelper::unpublishList('projects.unpublish');
		JLToolBarHelper::divider();
		JLToolBarHelper::custom('projects.import','upload','upload','COM_JOOMLEAGUE_GLOBAL_CSV_IMPORT',false);
		JLToolBarHelper::archiveList('projects.export','COM_JOOMLEAGUE_GLOBAL_XML_EXPORT');
		JLToolBarHelper::divider();
		JLToolBarHelper::archiveList('projects.archive');
		JLToolBarHelper::trash('projects.trash');
		JLToolBarHelper::deleteList('COM_JOOMLEAGUE_ADMIN_PROJECTS_DELETE_WARNING','projects.remove');
		JLToolBarHelper::divider();
		JLToolbarHelper::help('screen.joomleague',true);
	}


	/**
	 *
	 */
	function _displayProgressCopy($tpl)
	{
		$app 	= Factory::getApplication();
		$jinput = $app->input;

		$ids 	= $app->getUserState('filter.projectCopy',false);
		if ($ids) {
			$mdlProject = $this->getModel();
			$mdlProject->copyProjectData($ids);

			// we're done with copy function so set it to false
			$app->setUserState('filter.projectCopy',false);
		}

		JLToolBarHelper::cancel('project.cancel','COM_JOOMLEAGUE_GLOBAL_CLOSE');

		parent::display($tpl);
	}



	/**
	 *
	 */
	function _displayProgressDelete($tpl)
	{
		$app 	= Factory::getApplication();
		$jinput = $app->input;

		$ids 	= $app->getUserState('filter.projectDelete',false);
		if ($ids) {
			$mdlProject = $this->getModel();
			$mdlProject->deleteProjectData($ids);

			// we're done with copy function so set it to false
			$app->setUserState('filter.projectDelete',false);
		}

		JLToolBarHelper::cancel('project.cancel','COM_JOOMLEAGUE_GLOBAL_CLOSE');

		parent::display($tpl);
	}
	protected function getSortFields()
	{
		if ($this->getLayout() == 'default')
		{
			return array(
				'ordering'       => Text::_('JGRID_HEADING_ORDERING'),
				'a.published'    => Text::_('JSTATUS'),
				//'a.title'        => Text::_('JGLOBAL_TITLE'),
				//'position'       => Text::_('COM_MODULES_HEADING_POSITION'),
				//'name'           => Text::_('COM_MODULES_HEADING_MODULE'),
				//'pages'          => Text::_('COM_MODULES_HEADING_PAGES'),
				//'a.access'       => Text::_('JGRID_HEADING_ACCESS'),
				//'language_title' => Text::_('JGRID_HEADING_LANGUAGE'),
				'a.id'           => Text::_('JGRID_HEADING_ID')
			);
		}

		return array(
			//'a.title'        => Text::_('JGLOBAL_TITLE'),
			//'position'       => Text::_('COM_MODULES_HEADING_POSITION'),
			//'name'           => Text::_('COM_MODULES_HEADING_MODULE'),
			//'pages'          => Text::_('COM_MODULES_HEADING_PAGES'),
			//'a.access'       => Text::_('JGRID_HEADING_ACCESS'),
			//'language_title' => Text::_('JGRID_HEADING_LANGUAGE'),
			'a.id'           => Text::_('JGRID_HEADING_ID')
		);
	}


}
