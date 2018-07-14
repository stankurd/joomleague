<?php
/**
 * Joomleague
 *
 * @copyright	Copyright (C) 2006-2015 joomleague.at. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @link		http://www.joomleague.at
 */
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Toolbar\ToolbarHelper;

defined('_JEXEC') or die;


/**
 * HTML View class
 */
class JoomleagueViewLeagues extends JLGView
{
	protected $items;
	protected $pagination;
	protected $state;

	public function display($tpl = null)
	{
		$app 	= Factory::getApplication();
		$jinput = $app->input;

		$this->items = $this->get('Items');
		$this->pagination = $this->get('Pagination');
		$this->state = $this->get('State');

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
	    ToolbarHelper::title(Text::_('COM_JOOMLEAGUE_ADMIN_LEAGUES_TITLE'),'jl-leagues');
		ToolBarHelper::addNew('league.add');
		ToolBarHelper::custom('leagues.import','upload','upload','COM_JOOMLEAGUE_GLOBAL_CSV_IMPORT',false);
		ToolBarHelper::archiveList('leagues.export','COM_JOOMLEAGUE_GLOBAL_XML_EXPORT');
		ToolBarHelper::deleteList('COM_JOOMLEAGUE_GLOBAL_CONFIRM_DELETE','leagues.remove');
		ToolBarHelper::divider();
		ToolBarHelper::help('screen.joomleague',true);
	}
}
