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
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Toolbar\ToolbarHelper;

defined('_JEXEC') or die;

/**
 * HTML View class
 */
class JoomleagueViewClubs extends JLGView
{
	protected $items;
	protected $pagination;
	protected $state;

	public function display($tpl = null)
	{
		$app 	= Factory::getApplication();
		$jinput = $app->input;
		$option = $jinput->getCmd('option');
		$params	= ComponentHelper::getParams($option);

		$this->items = $this->get('Items');
		$this->pagination = $this->get('Pagination');
		$this->state = $this->get('State');

		$this->config = Factory::getConfig();
		$this->component_params = $params;
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
		ToolbarHelper::title(Text::_('COM_JOOMLEAGUE_ADMIN_CLUBS_TITLE'),'jl-clubs');
		ToolBarHelper::addNew('club.add');
		ToolBarHelper::custom('clubs.import','upload','upload','COM_JOOMLEAGUE_GLOBAL_CSV_IMPORT',false);
		ToolBarHelper::archiveList('clubs.export','COM_JOOMLEAGUE_GLOBAL_XML_EXPORT');
		ToolBarHelper::deleteList('COM_JOOMLEAGUE_GLOBAL_CONFIRM_DELETE','clubs.remove');
		ToolBarHelper::divider();
		ToolBarHelper::help('screen.joomleague',true);
	}
}
