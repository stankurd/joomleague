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
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;
use Joomla\CMS\Uri\Uri;

defined('_JEXEC') or die;


/**
 * HTML View class
 */
class JoomleagueViewPositions extends JLGView
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
		$model = $this->getModel();
		$baseurl = Uri::root();
		
		$this->items = $this->get('Items');
		$this->pagination = $this->get('Pagination');
		$this->state = $this->get('State');

		$document = Factory::getDocument();
		$document->addScript($baseurl . 'administrator/components/com_joomleague/assets/js/multiselect.js');
		
		// state filter
		$lists['state'] = JoomleagueHelper::stateOptions($this->state->get('filter.state'));

		// build the html options for parent position
		$parent_id = array();
		$parent_id[] = HTMLHelper::_('select.option','',Text::_('COM_JOOMLEAGUE_ADMIN_POSITIONS_IS_P_POSITION'));
		if($res = $model->getParentsPositions())
		{
			foreach($res as $re)
			{
				$re->text = Text::_($re->text);
			}
			$parent_id = array_merge($parent_id,$res);
		}
		$lists['parent_id'] = $parent_id;
		unset($parent_id);

		// build the html select list for sportstypes
		$sportstypes[] = HTMLHelper::_('select.option','0',Text::_('COM_JOOMLEAGUE_ADMIN_POSITIONS_SPORTSTYPE_FILTER'),'id','name');
		$modelST = BaseDatabaseModel::getInstance('SportsTypes','JoomleagueModel');
		$allSportstypes = $modelST->getSportsTypes();
		$sportstypes = array_merge($sportstypes,$allSportstypes);

		$lists['sportstypes'] = HTMLHelper::_('select.genericList',$sportstypes,'filter_sportstype','class="input-medium" onChange="this.form.submit();"',
				'id','name',$this->state->get('filter.sportstype'));
		unset($sportstypes);

		$this->user = Factory::getUser();
		$this->config = Factory::getConfig();
		$this->lists = $lists;
		$this->request_url = $uri->toString();

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
		JLToolBarHelper::title(Text::_('COM_JOOMLEAGUE_ADMIN_POSITIONS_TITLE'),'jl-Positions');
		JLToolBarHelper::addNew('position.add');
		JLToolBarHelper::publishList('positions.publish');
		JLToolBarHelper::unpublishList('positions.unpublish');
		JLToolBarHelper::divider();
		JLToolBarHelper::apply('positions.saveshort');
		JLToolBarHelper::custom('positions.import','upload','upload',Text::_('COM_JOOMLEAGUE_GLOBAL_CSV_IMPORT'),false);
		JLToolBarHelper::archiveList('positions.export',Text::_('COM_JOOMLEAGUE_GLOBAL_XML_EXPORT'));
		JLToolBarHelper::deleteList('COM_JOOMLEAGUE_GLOBAL_CONFIRM_DELETE','positions.remove');
		JLToolBarHelper::divider();
		JLToolBarHelper::help('screen.joomleague',true);
	}
}
