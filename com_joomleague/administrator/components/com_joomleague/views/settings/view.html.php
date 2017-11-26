<?php
/**
 * Joomleague
 *
 * @copyright	Copyright (C) 2006-2015 joomleague.at. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @link		http://www.joomleague.at
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die;
jimport('cms.html.bootstrap');
jimport('joomla.form.form');
use Joomla\Registry\Registry;

/**
 * HTML View class
 */
class JoomleagueViewSettings extends JLGView
{
	public function display($tpl = null)
	{
		$option = JFactory::getApplication()->input->get('option');
		$params = JComponentHelper::getParams($option);
		$xmlfile = JPATH_ADMINISTRATOR.'/components/'.$option.'/config.xml';
		
		$jRegistry = new Registry;
		$jRegistry->loadString($params->toString('ini'), 'ini');
		$form = JForm::getInstance($option, $xmlfile, array('control'=> 'params'), false, "/config");
		$form->bind($jRegistry);
		$this->form=$form;

		$this->addToolbar();		
		parent::display($tpl);
	}

	/**
	* Add the page title and toolbar
	*/
	protected function addToolbar()
	{
		//create the toolbar
		JToolBarHelper::title(JText::_('COM_JOOMLEAGUE_SETTINGS_TITLE'),'jl-ProjectSettings');
		JLToolBarHelper::apply('settings.apply');
		JLToolBarHelper::save('settings.save');
		JLToolBarHelper::cancel('settings.cancel');
		JToolBarHelper::spacer();
		JToolBarHelper::help('screen.joomleague',true);		
	}
}
