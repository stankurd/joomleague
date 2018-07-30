<?php
/**
 * Joomleague
 *
 * @copyright	Copyright (C) 2006-2015 joomleague.at. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @link		http://www.joomleague.at
 */
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;
use Joomla\CMS\Factory;

defined('_JEXEC') or die;


/**
 * HTML View class
 */
class JoomleagueViewTreetos extends JLGView
{
	protected $items;
	protected $pagination;
	protected $state;

	public function display($tpl = null)
	{
		$app = Factory::getApplication();
		$input = $app->input;
		$uri = Uri::getInstance();
		$option = $input->getCmd('option');

		$project_id = $app->getUserState($option . 'project');
		$user = Factory::getUser();

		$this->items = $this->get('Items');
		$this->pagination = $this->get('Pagination');
		$this->state = $this->get('State');

		$model = $this->getModel();

		$mdlProject = BaseDatabaseModel::getInstance('project','JoomleagueModel');
		$project = $mdlProject->getItem($project_id);

		$division = $app->getUserStateFromRequest($this->context.'.division','division','','string');

		// build the html options for divisions
		$divisions[] = HTMLHelper::_('select.option','0',Text::_('COM_JOOMLEAGUE_GLOBAL_SELECT_DIVISION'));
		$mdlDivisions = BaseDatabaseModel::getInstance('divisions','JoomLeagueModel');
		if($res = $mdlDivisions->getDivisions($project_id))
		{
			$divisions = array_merge($divisions,$res);
		}
		$lists['divisions'] = $divisions;
		unset($divisions);

		$this->user = $user;
		$this->lists = $lists;
		$this->project = $project;
		$this->division = $division;
		$this->request_url = $uri->toString();

		$this->addToolbar();
		parent::display($tpl);
	}


	protected function addToolbar()
	{
		JLToolBarHelper::title(Text::_('COM_JOOMLEAGUE_ADMIN_TREETOS_TITLE'),'jl-Tree');
		ToolbarHelper::saveGroup(
		    [
		        ['apply', 'treetos.saveshort'],
		    ],
		    'btn-success'
		    );
		//JLToolBarHelper::apply('treetos.saveshort');
		JLToolBarHelper::publishList('treetos.publish');
		JLToolBarHelper::unpublishList('treetos.unpublish');
		JLToolBarHelper::divider();
		JLToolBarHelper::addNew('treetos.save');
		JLToolBarHelper::deleteList(Text::_('COM_JOOMLEAGUE_ADMIN_TREETOS_WARNING'),'treetos.remove');
		JLToolBarHelper::divider();
		JLToolBarHelper::help('screen.joomleague',true);
	}
}
