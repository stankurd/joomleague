<?php
/**
 * Joomleague
 *
 * @copyright	Copyright (C) 2006-2015 joomleague.at. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @link		http://www.joomleague.at
 */
use Joomla\CMS\Factory;

defined('_JEXEC') or die;

jimport('joomla.html.parameter.element.timezones');

/**
 * HTML View class
 */
class JoomleagueViewProject extends JLGView
{

	protected $form;
	protected $item;
	protected $state;

	public function display($tpl = null)
	{
		$app = Factory::getApplication();
		$jinput = $app->input;

		$this->form = $this->get('Form');
		$this->item = $this->get('Item');
		$this->state = $this->get('State');

		$isNew = ($this->form->getValue('id') < 1);
		if($isNew)
		{
			$this->form->setValue('is_utc_converted',null,1);
		}
		$edit = $jinput->get('edit');
		$copy = $jinput->get('copy');

		// add javascript
		$document = Factory::getDocument();
		$version = urlencode(JoomleagueHelper::getVersion());

		$this->edit = $edit;
		$this->copy = $copy;

		//$extended = $this->getExtended($this->item->extended, 'project', $this->item->id);
		//$this->extended = $extended;

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

		// Set toolbar items for the page
		if($this->copy)
		{
			$toolbarTitle = JText::_('COM_JOOMLEAGUE_ADMIN_PROJECT_COPY_PROJECT');
		}
		else
		{
			$toolbarTitle = $isNew ? JText::_('COM_JOOMLEAGUE_ADMIN_PROJECT_ADD_NEW') : JText::_('COM_JOOMLEAGUE_ADMIN_PROJECT_EDIT') . ': ' .
					 $this->form->getValue('name');
			JLToolBarHelper::divider();
		}
		JLToolBarHelper::title($toolbarTitle,'jl-ProjectSettings');

		if(!$this->copy)
		{
			JLToolBarHelper::apply('project.apply');
			JLToolBarHelper::save('project.save');
		}
		else
		{
			JLToolBarHelper::save('project.copysave');
		}
		JLToolBarHelper::divider();
		if((!$this->edit) || ($this->copy))
		{
			JLToolBarHelper::cancel('project.cancel');
		}
		else
		{
			JLToolBarHelper::cancel('project.cancel','COM_JOOMLEAGUE_GLOBAL_CLOSE');
		}
		JLToolBarHelper::help('screen.joomleague',true);
	}
}
