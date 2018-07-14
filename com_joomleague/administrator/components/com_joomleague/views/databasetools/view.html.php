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
use Joomla\CMS\Language\Text;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\Uri\Uri;

defined('_JEXEC') or die;


/**
 * HTML View class
 */
class JoomleagueViewDatabaseTools extends JLGView
{
	function display($tpl = null)
	{
		$db		= Factory::getDbo();
		$uri	= Uri::getInstance();

		$this->request_url = $uri->toString();

		$this->addToolbar();		
		parent::display($tpl);
	}
	/**
	* Add the page title and toolbar
	*/
	protected function addToolbar()
	{
		// Set toolbar items for the page
		ToolBarHelper::title(Text::_('COM_JOOMLEAGUE_ADMIN_DBTOOLS_TITLE'),'config.png');
		ToolBarHelper::back();
		ToolbarHelper::help( 'screen.joomleague', true );
	}	
	
}
