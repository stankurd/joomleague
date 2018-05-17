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
use Joomla\CMS\Toolbar\ToolbarHelper;

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
		 * $msg = Text::sprintf('DESCBEINGEDITTED',
		 * Text::_('COM_JOOMLEAGUE_ADMIN_DIVISION_THE_DIVISION'),
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
		$parents[] = HTMLHelper::_('select.option','0',Text::_('COM_JOOMLEAGUE_GLOBAL_SELECT_DIVISION'));
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

		$text = $isNew ? Text::_('COM_JOOMLEAGUE_GLOBAL_NEW') : Text::_('COM_JOOMLEAGUE_GLOBAL_EDIT') . ': ' . Text::_($this->project->name) . ' / ' .
				 $this->item->name;

		// Set toolbar items for the page
		ToolBarHelper::title($text,'jl-divisions');

		if($isNew)
		{
		    ToolbarHelper::saveGroup(
		        [
		            ['apply', 'division.apply'],
		            ['save', 'division.save'],
		        ],
		        'btn-success'
		        );
			ToolBarHelper::cancel('division.cancel');
		}
		else
		{
		    ToolbarHelper::saveGroup(
		        [
		            ['apply', 'division.apply'],
		            ['save', 'division.save'],
		        ],
		        'btn-success'
		        );
			ToolBarHelper::cancel('division.cancel','COM_JOOMLEAGUE_GLOBAL_CLOSE');
		}
		ToolBarHelper::help('screen.joomleague',true);
	}
}
