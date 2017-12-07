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
use Joomla\CMS\MVC\Model\BaseDatabaseModel;

defined('_JEXEC') or die;


/**
 * HTML View class
 */
class JoomleagueViewDivision extends JLGView
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
		$option = $jinput->getCmd('option');
		$user = Factory::getUser();
		$model = $this->getModel();
		$project_id = $app->getUserState($option . 'project');

		$lists = array();
		// get the division

		$this->form = $this->get('Form');
		$this->item = $this->get('Item');
		$this->state = $this->get('State');

		$isNew = ($this->item->id < 1);

		// fail if checked out not by 'me'
		// @todo: fix
		/*
		 * if ($model->isCheckedOut($user->get('id')))
		 * {
		 * $msg = JText::sprintf('DESCBEINGEDITTED',
		 * JText::_('COM_JOOMLEAGUE_ADMIN_DIVISION_THE_DIVISION'),
		 * $division->name);
		 * $app->redirect('index.php?option=' . $option, $msg);
		 * }
		 *
		 * // Edit or Create?
		 * if (! $isNew)
		 * {
		 * $model->checkout($user->get('id'));
		 * }
		 * else
		 * {
		 * // initialise new record
		 * $division->order = 0;
		 * }
		 */

		$mdlProject = BaseDatabaseModel::getInstance('project','JoomleagueModel');
		$project = $mdlProject->getItem($project_id);

		// build the html select list for parent divisions
		$parents[] = HTMLHelper::_('select.option','0',JText::_('COM_JOOMLEAGUE_GLOBAL_SELECT_DIVISION'));
		if($res = $model->getParentsDivisions())
		{
			$parents = array_merge($parents,$res);
		}
		$lists['parents'] = HTMLHelper::_('select.genericlist',$parents,'parent_id','class="inputbox" size="1"','value','text',$this->item->parent_id);
		unset($parents);

		$this->project = $project;
		$this->lists = $lists;

		$this->addToolbar();
		parent::display($tpl);
	}


	/**
	 * Add the page title and toolbar.
	 */
	protected function addToolbar()
	{
		Factory::getApplication()->input->set('hidemainmenu',true);
		$user = Factory::getUser();
		$userId = $user->get('id');
		$isNew = ($this->item->id == 0);
		$checkedOut = ! ($this->item->checked_out == 0 || $this->item->checked_out == $userId);

		$text = $isNew ? JText::_('COM_JOOMLEAGUE_GLOBAL_NEW') : JText::_('COM_JOOMLEAGUE_GLOBAL_EDIT') . ': ' . JText::_($this->project->name) . ' / ' .
				 $this->item->name;

		// Set toolbar items for the page
		JLToolBarHelper::title($text,'jl-divisions');

		if($isNew)
		{
			JLToolBarHelper::apply('division.apply');
			JLToolBarHelper::save('division.save');
			JLToolBarHelper::cancel('division.cancel');
		}
		else
		{
			JLToolBarHelper::apply('division.apply');
			JLToolBarHelper::save('division.save');
			JLToolBarHelper::cancel('division.cancel','COM_JOOMLEAGUE_GLOBAL_CLOSE');
		}
		JLToolBarHelper::help('screen.joomleague',true);
	}
}
