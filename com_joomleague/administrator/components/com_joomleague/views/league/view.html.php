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


/**
 * HTML View class
 */
class JoomleagueViewLeague extends JLGView
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

		$extended = $this->getExtended($this->item->extended,'league');
		$this->extended = $extended;

		// Check for errors.
		if(count($errors = $this->get('Errors')))
		{
			JError::raiseError(500,implode("\n",$errors));
			return false;
		}

		$this->addToolbar();
		parent::display($tpl);
	}


	/**
	 * Add the page title and toolbar
	 */
	protected function addToolbar()
	{
		Factory::getApplication()->input->set('hidemainmenu',true);
		$user = Factory::getUser();
		$userId = $user->get('id');
		$isNew = ($this->item->id == 0);
		$checkedOut = ! ($this->item->checked_out == 0 || $this->item->checked_out == $userId);

		// Set toolbar items for the page
		if($isNew)
		{
			JLToolBarHelper::title(JText::_('COM_JOOMLEAGUE_ADMIN_LEAGUE_ADD_NEW'),'jl-leagues');
			JLToolBarHelper::apply('league.apply');
			JLToolBarHelper::save('league.save');
			JLToolBarHelper::divider();
			JLToolBarHelper::cancel('league.cancel');
		}
		else
		{
			JLToolBarHelper::title(JText::_('COM_JOOMLEAGUE_ADMIN_LEAGUE_EDIT') . ': ' . $this->form->getValue('name'),'jl-leagues');
			JLToolBarHelper::apply('league.apply');
			JLToolBarHelper::save('league.save');
			JLToolBarHelper::divider();
			JLToolBarHelper::cancel('league.cancel','COM_JOOMLEAGUE_GLOBAL_CLOSE');
		}
		JLToolBarHelper::divider();
		JLToolBarHelper::help('screen.joomleague',true);
	}
}
