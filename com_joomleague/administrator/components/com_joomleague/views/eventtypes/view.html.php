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
use Joomla\CMS\MVC\Model\BaseDatabaseModel;

defined('_JEXEC') or die;


/**
 * HTML View class
 */
class JoomleagueViewEventtypes extends JLGView
{
	protected $items;
	protected $pagination;
	protected $state;

	public function display($tpl = null)
	{
		$app = Factory::getApplication();
		$jinput = $app->input;
		$option = $jinput->getCmd('option');
		$app = Factory::getApplication();
		$uri = Uri::getInstance();

		$this->items = $this->get('Items');
		$this->pagination = $this->get('Pagination');
		$this->state = $this->get('State');

		$lists = array();
		$lists['state'] = JoomleagueHelper::stateOptions($this->state->get('filter.state'));

		// build the html select list for sportstypes
		$sportstypes = array();
		$sportstypes[] = JHtml::_('select.option','0',JText::_('COM_JOOMLEAGUE_ADMIN_EVENTTYPES_SPORTSTYPE_FILTER'),'id','name');

		$modelST = BaseDatabaseModel::getInstance('sportstypes','JoomleagueModel');
		$allSportstypes = $modelST->getSportsTypes();
		$sportstypes = array_merge($sportstypes,$allSportstypes);

		$lists['sportstypes'] = JHtml::_('select.genericList',$sportstypes,'filter_sportstype','class="input-medium" onChange="this.form.submit();"',
				'id','name',$this->state->get('filter.sportstype'));
		unset($sportstypes);

		$this->user = Factory::getUser();
		$this->config = Factory::getConfig();
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
		JLToolBarHelper::title(JText::_('COM_JOOMLEAGUE_ADMIN_EVENTTYPES_TITLE'),'jl-eventtypes');
		JLToolBarHelper::addNew('eventtype.add');
		JLToolBarHelper::publishList('eventtypes.publish');
		JLToolBarHelper::unpublishList('eventtypes.unpublish');
		JLToolBarHelper::divider();
		JLToolBarHelper::custom('eventtypes.import','upload','upload','COM_JOOMLEAGUE_GLOBAL_CSV_IMPORT',false);
		JLToolBarHelper::archiveList('eventtypes.export','COM_JOOMLEAGUE_GLOBAL_XML_EXPORT');
		JLToolBarHelper::deleteList('COM_JOOMLEAGUE_GLOBAL_CONFIRM_DELETE','eventtypes.remove');
		JLToolBarHelper::divider();
		JLToolBarHelper::help('screen.joomleague',true);
	}
}
