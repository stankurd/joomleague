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
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;

defined('_JEXEC') or die;


/**
 * HTML View class
 */
class JoomleagueViewStatistics extends JLGView
{
	protected $items;
	protected $pagination;
	protected $state;

	public function display($tpl = null)
	{
		$app = Factory::getApplication();
		$jinput = $app->input;
		$option = $jinput->getCmd('option');

		$document = Factory::getDocument();
		$user = Factory::getUser();
		$uri = Uri::getInstance();

		$this->items = $this->get('Items');
		$this->pagination = $this->get('Pagination');
		$this->state = $this->get('State');

		// state filter
		$lists['state'] = JoomleagueHelper::stateOptions($this->state->get('filter.state'));

		// build the html select list for sportstypes
		$sportstypes[] = HTMLHelper::_('select.option','0',Text::_('COM_JOOMLEAGUE_ADMIN_EVENTS_SPORTSTYPE_FILTER'),'id','name');

		$modelST = BaseDatabaseModel::getInstance('sportsTypes','JoomleagueModel');
		$allSportstypes = $modelST->getSportsTypes();
		$sportstypes = array_merge($sportstypes,$allSportstypes);
		$lists['sportstypes'] = HTMLHelper::_('select.genericList',$sportstypes,'filter_sportstype','class="input-medium" onChange="this.form.submit();"',
				'id','name',$this->state->get('filter.sportstype'));
		unset($sportstypes);

		$this->user = $user;
		$this->config = Factory::getConfig();
		$this->lists = $lists;

		$this->filterForm    = $this->get('FilterForm');
		$this->activeFilters = $this->get('ActiveFilters');

		$this->addToolbar();
		parent::display($tpl);
	}


	/**
	 * Add the page title and toolbar.
	 */
	protected function addToolbar()
	{
		JLToolBarHelper::title(Text::_('COM_JOOMLEAGUE_ADMIN_STATISTICS_TITLE'),'jl-statistics');
		JLToolBarHelper::addNew('statistic.add');
		JLToolBarHelper::publishList('statistics.publish');
		JLToolBarHelper::unpublishList('statistics.unpublish');
		JLToolBarHelper::divider();
		JLToolBarHelper::custom('statistics.import','upload','upload','COM_JOOMLEAGUE_GLOBAL_CSV_IMPORT',false);
		JLToolBarHelper::archiveList('statistics.export','COM_JOOMLEAGUE_GLOBAL_XML_EXPORT');
		JLToolBarHelper::deleteList(Text::_('COM_JOOMLEAGUE_ADMIN_STATISTICS_DELETE_WARNING'),'statistics.fulldelete','COM_JOOMLEAGUE_ADMIN_STATISTICS_FULL_DELETE');
		JLToolBarHelper::divider();

		JLToolBarHelper::help('screen.joomleague',true);
	}
}
