<?php
/**
 * @copyright	Copyright (C) 2006-2013 JoomLeague.net. All rights reserved.
 * @license		GNU/GPL,see LICENSE.php
 * Joomla! is free software. This version may have been modified pursuant
 * to the GNU General Public License,and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */

// Check to ensure this file is included in Joomla!
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Table\Table;
use Joomla\CMS\Uri\Uri;
use Joomla\String\StringHelper;

defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.view');

/**
 * HTML View class for the Joomleague component
 *
 * @static
 * @package	JoomLeague
 * @since	0.1
 */
class JoomleagueViewpredictiongroups extends JLGView
{
	function display($tpl=null)
	{
		$app = Factory::getApplication();
		$option = $app->input->getCmd('option');
		$uri	= Uri::getInstance();
		
		$filter_order		= $app->getUserStateFromRequest($option.'s_filter_order',		'filter_order',		's.ordering',	'cmd');
		$filter_order_Dir	= $app->getUserStateFromRequest($option.'s_filter_order_Dir',	'filter_order_Dir',	'',				'word');
		$search			= $app->getUserStateFromRequest($option.'s_search',			'search',			'',				'string');
		$search			= StringHelper::strtolower($search);
		$table = Table::getInstance('predictiongroup', 'joomleagueTable');
		$this->table	= $table;
		$items = $this->get('Data');
		$total = $this->get('Total');
		$pagination = $this->get('Pagination');

		// table ordering
		$lists['order_Dir']=$filter_order_Dir;
		$lists['order']=$filter_order;

		// search filter
		$lists['search']=$search;

		$this->user = Factory::getUser();
		$this->lists = $lists;
		$this->items = $items;
		$this->pagination = $pagination;
		$this->request_url = $uri->toString();
		$this->addToolbar();
		parent::display($tpl);
	}
	
	/**
	* Add the page title and toolbar.
	*
	* @since	1.7
	*/
	protected function addToolbar()
	{ 
		// Set toolbar items for the page
		JToolBarHelper::title(Text::_('COM_JOOMLEAGUE_ADMIN_PREDICTIONGROUPS_TITLE'),'predictiongroups');
		
		JLToolBarHelper::addNew('predictiongroup.add');
		JLToolBarHelper::editList('predictiongroup.edit');
		JLToolBarHelper::custom('predictiongroup.import','upload','upload',Text::_('COM_JOOMLEAGUE_GLOBAL_CSV_IMPORT'),false);
		JLToolBarHelper::archiveList('predictiongroup.export',Text::_('COM_JOOMLEAGUE_GLOBAL_XML_EXPORT'));
		JLToolBarHelper::deleteList('', 'predictiongroups.remove');
		JToolBarHelper::divider();
		//JToolBarHelper::preferences(JRequest::getCmd('option'));
	}
}
?>