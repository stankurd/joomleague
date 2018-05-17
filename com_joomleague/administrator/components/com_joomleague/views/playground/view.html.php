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
use Joomla\CMS\Toolbar\ToolbarHelper;

defined('_JEXEC') or die;


/**
 * HTML View class
 */
class JoomleagueViewPlayground extends JLGView
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

		$extended = $this->getExtended($this->item->extended,'playground');
		$this->extended = $extended;

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
		$checkedOut = !($this->item->checked_out == 0 || $this->item->checked_out == $userId);

		// Set toolbar items for the page
		$text = $isNew ? Text::_('COM_JOOMLEAGUE_GLOBAL_NEW') : Text::_('COM_JOOMLEAGUE_GLOBAL_EDIT');

		if($isNew)
		{
			JLToolBarHelper::title(Text::_('COM_JOOMLEAGUE_ADMIN_PLAYGROUND_ADD_NEW'),'jl-playground');
			ToolbarHelper::saveGroup(
			    [
			        ['apply', 'playground.apply'],
			        ['save', 'playground.save'],
			    ],
			    'btn-success'
			    );
			//JLToolBarHelper::apply('playground.apply');
			//JLToolBarHelper::save('playground.save');
			JLToolBarHelper::divider();
			JLToolBarHelper::cancel('playground.cancel');
		}
		else
		{
			JLToolBarHelper::title(Text::_('COM_JOOMLEAGUE_ADMIN_PLAYGROUND_EDIT'),'jl-playground');
			ToolBarHelper::saveGroup(
			    [
			        ['apply', 'playground.apply'],
			        ['save', 'playground.save'],
			    ],
			    'btn-success'
			    );
			JLToolBarHelper::divider();
			JLToolBarHelper::cancel('playground.cancel','COM_JOOMLEAGUE_GLOBAL_CLOSE');
		}
		JLToolBarHelper::divider();
		JLToolBarHelper::help('screen.joomleague',true);
	}
}
