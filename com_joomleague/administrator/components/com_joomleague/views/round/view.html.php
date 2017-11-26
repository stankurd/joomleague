<?php
/**
 * Joomleague
 *
 * @copyright	Copyright (C) 2006-2015 joomleague.at. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @link		http://www.joomleague.at
 */
defined('_JEXEC') or die;


/**
 * HTML View class
 */
class JoomleagueViewRound extends JLGView
{
	protected $form;
	protected $item;
	protected $state;

	public function display($tpl = null)
	{
		$app = JFactory::getApplication();
		$jinput = $app->input;
		$option = $jinput->getCmd('option');
		$project_id = $app->getUserState($option . 'project');

		$db = JFactory::getDbo();
		$uri = JUri::getInstance();
		$user = JFactory::getUser();
		$model = $this->getModel();
		$lists = array();

		$this->form = $this->get('Form');
		$this->item = $this->get('Item');
		$this->state = $this->get('State');

		// get the round (matchday)
		// $isNew = ($this->item->id < 1);

		// fail if checked out not by 'me'
		// @todo: fix!
		/*
		 * if ($model->isCheckedOut($user->get('id')))
		 * {
		 * $msg = JText::sprintf('DESCBEINGEDITTED', JText::_('The matchday'),
		 * $round->name);
		 * $app->redirect('index.php?option=' . $option, $msg);
		 * }
		 *
		 * // Edit or Create?
		 * if (!$isNew)
		 * {
		 * $model->checkout($user->get('id'));
		 * }
		 * else
		 * {
		 * // initialise new record
		 * $round->order = 0;
		 * }
		 */

		$mdlProject = JModelLegacy::getInstance('project','JoomleagueModel');
		$project = $mdlProject->getItem($project_id);

		$this->project = $project;

		$this->addToolbar();
		parent::display($tpl);
	}


	/**
	 * Add the page title and toolbar.
	 */
	protected function addToolbar()
	{
		JFactory::getApplication()->input->set('hidemainmenu',true);
		$user = JFactory::getUser();
		$userId = $user->get('id');
		$isNew = ($this->item->id == 0);
		$checkedOut = ! ($this->item->checked_out == 0 || $this->item->checked_out == $userId);

		$text = $isNew ? JText::_('COM_JOOMLEAGUE_GLOBAL_NEW') : JText::_('COM_JOOMLEAGUE_GLOBAL_EDIT');
		JToolBarHelper::title(JText::_('COM_JOOMLEAGUE_ADMIN_ROUND_TITLE') . ': ' . $this->item->name,'jl-Matchdays');

		JLToolBarHelper::apply('round.apply');
		JLToolBarHelper::save('round.save');
		if($isNew)
		{
			JLToolBarHelper::cancel('round.cancel');
		}
		else
		{
			JLToolBarHelper::cancel('round.cancel','COM_JOOMLEAGUE_GLOBAL_CLOSE');
		}
		JToolBarHelper::help('screen.joomleague',true);
	}
}
