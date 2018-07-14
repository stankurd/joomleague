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
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\Uri\Uri;

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
		$app = Factory::getApplication();
		$jinput = $app->input;
		$option = $jinput->getCmd('option');
		$user = Factory::getUser();
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
		 * $msg = Text::sprintf('DESCBEINGEDITTED', Text::_('The treeto'),
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
		$app = Factory::getApplication();
		$jinput = $app->input;
		$lists = array();
		$project_id = $app->getUserState('com_joomleagueproject');
		
		$mdlProject = BaseDatabaseModel::getInstance('project','JoomleagueModel');
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
		JLToolBarHelper::title(Text::_('COM_JOOMLEAGUE_ADMIN_TREETO_TITLE_GENERATE'));
		JLToolBarHelper::back('Back','index.php?option=com_joomleague&view=treetos');
		JLToolBarHelper::help('screen.joomleague',true);
	}

	protected function addToolbar()
	{
		JLToolBarHelper::title(Text::_('COM_JOOMLEAGUE_ADMIN_TREETO_TITLE'));
		ToolbarHelper::saveGroup(
		    [
		        ['apply', 'treeto.apply'],
		        ['save', 'treeto.save'],
		    ],
		    'btn-success'
		    );
		JLToolBarHelper::back('Back','index.php?option=com_joomleague&view=treetos');
		JLToolBarHelper::help('screen.joomleague',true);
	}

	protected function setDocument()
	{
		$document = Factory::getDocument();
		$version = urlencode(JoomleagueHelper::getVersion());
		$document->addScript(Uri::root() . $this->script);
	}
}
