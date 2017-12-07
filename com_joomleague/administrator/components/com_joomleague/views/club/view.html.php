<?php
/**
 * Joomleague
 *
 * @copyright	Copyright (C) 2006-2015 joomleague.at. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @link		http://www.joomleague.at
 */

// Check to ensure this file is included in Joomla!
use Joomla\CMS\Factory;

defined('_JEXEC') or die;

/**
 * HTML View class
 */
class JoomleagueViewClub extends JLGView
{
	protected $form;
	protected $item;
	protected $state;

	public function display($tpl = null)
	{
		$app = Factory::getApplication();
		$input = $app->input;
		
		$this->form = $this->get('Form');
		$this->item = $this->get('Item');
		$this->state = $this->get('State');
		
		$extended = $this->getExtended($this->item->extended, 'club');
		$this->extended = $extended;
		
		// Check for errors.
		if(count($errors = $this->get('Errors')))
		{
			$app->enqueueMessage(implode("\n",$errors));
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
		$app 	= Factory::getApplication();		
		$app->input->set('hidemainmenu',true);
		$user = Factory::getUser();
		$userId = $user->get('id');
		$isNew = ($this->item->id == 0);
		$checkedOut = !($this->item->checked_out == 0 || $this->item->checked_out == $userId);
		
		if($isNew)
		{
			JLToolBarHelper::title(JText::_('COM_JOOMLEAGUE_ADMIN_CLUB_ADD_NEW'),'jl-clubs');
		}
		else
		{
			JLToolBarHelper::title(JText::_('COM_JOOMLEAGUE_ADMIN_CLUB_EDIT').': '.$this->form->getValue('name'),'jl-clubs');
		}
		
		JLToolBarHelper::apply('club.apply');
		JLToolBarHelper::save('club.save');
		JLToolBarHelper::divider();
		JLToolBarHelper::cancel('club.cancel');
		JLToolBarHelper::help('screen.joomleague',true);		
	}	
}
