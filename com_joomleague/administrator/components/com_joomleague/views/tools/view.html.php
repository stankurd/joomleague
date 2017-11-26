<?php
/**
 * Joomleague
*
* @copyright	Copyright (C) 2006-2015 joomleague.at. All rights reserved.
* @license		GNU General Public License version 2 or later; see LICENSE.txt
* @link			http://www.joomleague.at
*/
defined('_JEXEC') or die;


/**
 * HTML View class
 */
class JoomleagueViewTools extends JLGView
{

	public function display($tpl=null)
	{
		$app 		= JFactory::getApplication();
		$uri		= JUri::getInstance();
		
		$this->request_url = $uri->toString();
		
		$this->tables = $this->get('Tables');
		$this->addToolbar();
		parent::display($tpl);
	}

	/**
	 * Add the page title and toolbar.
	 */
	protected function addToolbar()
	{
		JToolBarHelper::title(JText::_('Tools'),'wrench');
		JToolbarHelper::custom('tools.back','back','back','Back',false);
		JToolbarHelper::custom('tools.truncate','trash','Truncate','Truncate',false);
	}
}
