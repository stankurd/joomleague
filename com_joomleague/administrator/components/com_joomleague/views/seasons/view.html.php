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
use Joomla\CMS\Language\Text;

defined('_JEXEC') or die;

/**
 * HTML View class
 */
class JoomleagueViewSeasons extends JLGView
{
	protected $items;
	protected $pagination;
	protected $state;
	
	public function display($tpl = null)
	{
		$app = Factory::getApplication();
		$jinput = $app->input;
		
		$this->items = $this->get('Items');
		$this->pagination = $this->get('Pagination');
		$this->state = $this->get('State');
		
		$this->filterForm = $this->get('FilterForm');
		$this->activeFilters = $this->get('ActiveFilters');
		
		$lists		= array();
		
		// state filter
		$filter_state	= $app->getUserStateFromRequest('com_joomleague.s_filter_state',		'filter_state',		'P',			'word');
		$lists['state'] = JoomleagueHelper::stateOptions($filter_state,true,true,true,true);

		$this->lists = $lists;
		
		$this->addToolbar();
		parent::display($tpl);
	}
	
	
	/**
	* Add the page title and toolbar.
	*/
	protected function addToolbar()
	{ 
		JLToolBarHelper::title(Text::_('COM_JOOMLEAGUE_ADMIN_SEASONS_TITLE'),'jl-seasons');
		JLToolBarHelper::addNew('season.add');
		JLToolBarHelper::divider();
		JLToolBarHelper::publishList('seasons.publish');
		JLToolBarHelper::unpublishList('seasons.unpublish');
		JLToolBarHelper::divider();
		JLToolBarHelper::custom('seasons.import','upload','upload','COM_JOOMLEAGUE_GLOBAL_CSV_IMPORT',false);
		JLToolBarHelper::archiveList('seasons.export','COM_JOOMLEAGUE_GLOBAL_XML_EXPORT');
		JLToolBarHelper::divider();
		JLToolBarHelper::archiveList('seasons.archive');
		JLToolBarHelper::trash('seasons.trash');
		JLToolBarHelper::deleteList('COM_JOOMLEAGUE_GLOBAL_CONFIRM_DELETE','seasons.remove');
		JLToolBarHelper::divider();
		JLToolBarHelper::help('screen.joomleague',true);
	}	
}
