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
class JoomleagueViewTreeto extends JLGView
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
		elseif($this->getLayout() == 'gennode')
		{
			$this->_displayGennode($tpl);
			return;
		}
		parent::display($tpl);
	}

	function _displayForm($tpl)
	{
		$app = JFactory::getApplication();
		$jinput = $app->input;
		$option = $jinput->getCmd('option');
		$user = JFactory::getUser();
		$model = $this->getModel();
		
		$this->form = $this->get('Form');
		$this->item = $this->get('Item');
		$this->state = $this->get('State');
		
		$script = $this->get('Script');
		$this->script = $script;
		
		// fail if checked out not by 'me'
		// @todo: fix!
		/*
		 * if ($model->isCheckedOut($user->get('id')))
		 * {
		 * $msg = JText::sprintf('DESCBEINGEDITTED', JText::_('The treeto'),
		 * $treeto->id);
		 * $app->redirect('index.php?option=' . $option, $msg);
		 * }
		 */
		
		$this->addToolBar();
		parent::display($tpl);
		
		$this->setDocument();
	}

	function _displayGennode($tpl)
	{
		$app = JFactory::getApplication();
		$jinput = $app->input;
		$lists = array();
		$project_id = $app->getUserState('com_joomleagueproject');
		
		$mdlProject = JModelLegacy::getInstance('project','JoomleagueModel');
		$project = $mdlProject->getItem($project_id);
		
		$itemId = $jinput->get('cid');
		$app->setUserState('com_joomleaguetreeto_id',$itemId);
		
		$model = $this->getModel();
		$this->item = $model->getItem($itemId);
		$this->form = $this->get('Form');
		// $this->item = $this->get('Item');
		$this->state = $this->get('State');
		
		$this->project = $project;
		$this->lists = $lists;
		
		$this->addToolBar_Gennode();
		parent::display($tpl);
	}

	protected function addToolBar_Gennode()
	{
		JToolBarHelper::title(JText::_('COM_JOOMLEAGUE_ADMIN_TREETO_TITLE_GENERATE'));
		JToolBarHelper::back('Back','index.php?option=com_joomleague&view=treetos');
		JToolBarHelper::help('screen.joomleague',true);
	}

	protected function addToolbar()
	{
		JToolBarHelper::title(JText::_('COM_JOOMLEAGUE_ADMIN_TREETO_TITLE'));
		JLToolBarHelper::save('treeto.save');
		JLToolBarHelper::apply('treeto.apply');
		JToolBarHelper::back('Back','index.php?option=com_joomleague&view=treetos');
		JToolBarHelper::help('screen.joomleague',true);
	}

	protected function setDocument()
	{
		$document = JFactory::getDocument();
		$version = urlencode(JoomleagueHelper::getVersion());
		$document->addScript(JUri::root() . $this->script);
	}
}
