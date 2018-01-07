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
use Joomla\CMS\MVC\Model\BaseDatabaseModel;
use Joomla\CMS\HTML\HTMLHelper;

defined('_JEXEC') or die;

/**
 * HTML View class
 */
class JoomleagueViewTreetonode extends JLGView
{

	protected $form;

	protected $item;

	protected $state;

	public function display($tpl = null)
	{
		if($this->getLayout() == 'form')
		{
			$this->_displayForm($tpl);
			return;
		}
		
		parent::display($tpl);
	}

	function _displayForm($tpl)
	{
		$app = Factory::getApplication();
		$jinput = $app->input;
		
		$model = $this->getModel();
		$match = $model->getNodeMatch();
		
		$lists = array();
		
		$this->form = $this->get('Form');
		$this->item = $this->get('Item');
		$this->state = $this->get('State');
		
		$project_id = $app->getUserState('com_joomleagueproject');
		
		$mdlProject = BaseDatabaseModel::getInstance('project','JoomleagueModel');
		$project = $mdlProject->getItem($project_id);
		
		// $node = $this->get('data');
		// $total = $this->get('Total');
		// $pagination = $this->get('Pagination');
		// $projectws = $this->get('Data', 'project');
		// $model = $this->getModel('project');
		
		$mdlTreetonodes = BaseDatabaseModel::getInstance("Treetonodes","JoomleagueModel");
		$team_id[] = HTMLHelper::_('select.option','0',Text::_('COM_JOOMLEAGUE_GLOBAL_SELECT_TEAM'));
		if($projectteams = $mdlTreetonodes->getProjectTeamsOptions($model->_id))
		{
			$team_id = array_merge($team_id,$projectteams);
		}
		$lists['team'] = $team_id;
		unset($team_id);
		
		$this->user = Factory::getUser();
		$this->project = $project;
		$this->lists = $lists;
		// @todo fix!
		/* $this->division = $division; */
		/* $this->division_id = $division_id; */
		// $this->node = $node;
		$this->match = $match;
		// $this->pagination = $pagination;
		parent::display($tpl);
	}
}
