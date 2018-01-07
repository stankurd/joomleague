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

defined('_JEXEC') or die;

/**
 * HTML View class
 */
class JoomleagueViewEventtype extends JLGView
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
		$text = $isNew ? Text::_('COM_JOOMLEAGUE_GLOBAL_NEW') : Text::_('COM_JOOMLEAGUE_GLOBAL_EDIT');
		JLToolBarHelper::title((Text::_('COM_JOOMLEAGUE_ADMIN_EVENTTYPE_EVENT') . ': <span class="toolbarTitleType">[ ' . $text . ' ]</span>'),
				'jl-eventtypes');
		if($isNew)
		{
			JLToolBarHelper::apply('eventtype.apply');
			JLToolBarHelper::save('eventtype.save');
			JLToolBarHelper::divider();
			JLToolBarHelper::cancel('eventtype.cancel');
		}
		else
		{
			JLToolBarHelper::apply('eventtype.apply');
			JLToolBarHelper::save('eventtype.save');
			JLToolBarHelper::divider();
			JLToolBarHelper::cancel('eventtype.cancel','COM_JOOMLEAGUE_GLOBAL_CLOSE');
		}
		JLToolBarHelper::help('screen.joomleague',true);
	}
}
