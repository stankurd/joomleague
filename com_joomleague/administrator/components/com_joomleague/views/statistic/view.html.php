<?php
/**
 * Joomleague
 *
 * @copyright	Copyright (C) 2006-2015 joomleague.at. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @link		http://www.joomleague.at
 */
use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;

defined('_JEXEC') or die;

require_once JPATH_COMPONENT_ADMINISTRATOR.'/statistics/base.php';

/**
 * HTML View class
 */
class JoomleagueViewStatistic extends JLGView
{
	protected $form;
	protected $item;
	protected $state;

	public function display($tpl = null)
	{
		$app = Factory::getApplication();
		$jinput = $app->input;
		$baseurl = Uri::root();
		$document = Factory::getDocument();
		$document->addScript($baseurl . 'administrator/components/com_joomleague/assets/js/statistic.js');
		
		$this->form = $this->get('Form');
		$this->item = $this->get('Item');
		$this->state = $this->get('State');

		$class = $this->form->getValue('class');
		if (!empty($class))
		{
			/**
			 * statistic class parameters
			 */
			$class = JLGStatistic::getInstance($class);
			$this->calculated = $class->getCalculated();
		}

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
		$this->isNew = $isNew;
		$checkedOut = !($this->item->checked_out == 0 || $this->item->checked_out == $userId);

		// Set toolbar items for the page
		$text = $isNew ? JText::_('COM_JOOMLEAGUE_GLOBAL_NEW') : JText::_('COM_JOOMLEAGUE_GLOBAL_EDIT').': '.JText::_($this->form->getValue('name'));
		JLToolBarHelper::title(JText::_('COM_JOOMLEAGUE_ADMIN_STAT_TITLE').': <span class="toolbarTitleType">[ '.$text.']</span>','jl-statistics');
		if($isNew)
		{
			JLToolBarHelper::apply('statistic.apply');
			JLToolBarHelper::save('statistic.save');
			JLToolBarHelper::divider();
			JLToolBarHelper::cancel('statistic.cancel');
		}
		else
		{
			JLToolBarHelper::apply('statistic.apply');
			JLToolBarHelper::save('statistic.save');
			JLToolBarHelper::divider();
			JLToolBarHelper::cancel('statistic.cancel','COM_JOOMLEAGUE_GLOBAL_CLOSE');
		}
		JLToolBarHelper::help('screen.joomleague',true);

		$this->isNew = $isNew;
	}
}
